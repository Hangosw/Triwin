<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DmCaLamViec;
use App\Models\NhanVien;
use App\Models\ChamCong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HanetWebhookController extends Controller
{
    /**
     * Handle incoming checkin from Hanet Webhook
     */
    public function handle(Request $request)
    {
        Log::info('Hanet Webhook Received:', $request->all());

        $data = $request->all();

        // Xử lý 2 loại payload khác nhau của Hanet
        $action = $data['action'] ?? null;
        $dataType = $data['data_type'] ?? null;

        $aliasID = null;
        $checkinTime = null;
        $imageUrl = null;
        $personName = null;

        // Nếu là format Checkin (như test Postman)
        if ($action === 'checkin' && isset($data['data']) && is_array($data['data'])) {
            $aliasID = $data['data']['aliasID'] ?? null;
            $checkinTime = $data['data']['checkinTime'] ?? null;
            $personName = $data['data']['personName'] ?? null;
        } 
        // Nếu là format Log/Event chuẩn của Hanet bắt người lạ
        elseif ($dataType === 'log') {
            $aliasID = $data['aliasID'] ?? null;
            $checkinTime = $data['time'] ?? null;
            $imageUrl = $data['detected_image_url'] ?? null;
            $personName = $data['personName'] ?? null;
        }  
        else {
            return response()->json(['message' => 'Payload format not supported'], 422);
        }

        if (!$checkinTime) {
            return response()->json(['message' => 'Missing checkin time'], 400);
        }

        $nhanVien = null;
        if ($aliasID) {
            $nhanVien = NhanVien::where('Ma', $aliasID)->first();
        }

        // Fallback: Xử lý trường hợp có nhân viên nhưng Hanet không gửi rỗng aliasID
        if (!$nhanVien && $personName) {
            $nhanVien = NhanVien::where('Ten', $personName)->first();
            if ($nhanVien) {
                Log::info("HanetWebhook: Matched employee by name: {$personName} (ID: {$nhanVien->id})");
            }
        }

        $time = Carbon::createFromTimestampMs($checkinTime)->setTimezone(config('app.timezone', 'Asia/Ho_Chi_Minh'));

        if (!$nhanVien) {
            Log::warning("HanetWebhook: No employee found for Ma/Name: {$aliasID}/{$personName}. Logging as stranger.");
            ChamCong::create([
                'NhanVienId' => null,
                'Loai' => 0,
                'Vao' => $time,
                'TrangThai' => null,
                'Cong' => 0.0,
                'AnhChamCong' => $imageUrl,
            ]);
            return response()->json(['message' => 'Logged as stranger'], 200);
        }

        $ca = DmCaLamViec::where('MaCa', 'HC')->first();
        if (!$ca) {
            Log::warning("HanetWebhook: Ca HC not found");
            return response()->json(['message' => 'Shift not found'], 404);
        }

        $date = $time->toDateString();

        $gioVao = Carbon::parse($date . ' ' . $ca->GioVao);
        $gioRa = Carbon::parse($date . ' ' . $ca->GioRa);
        $trangThai = $this->tinhTrangThai($time, $gioVao, $gioRa);

        $chamCong = ChamCong::where('NhanVienId', $nhanVien->id)
            ->where('Loai', 0)
            ->whereDate('Vao', $date)
            ->first();

        if (!$chamCong) {
            // Lần quét đầu tiên → lưu giờ Vào
            // Chỉ cập nhật Ra nếu thời gian quét nằm trước GioRa của ca
            $isBeforeGioRa = $time->lessThanOrEqualTo($gioRa);
            ChamCong::create([
                'NhanVienId' => $nhanVien->id,
                'Loai' => 0,
                'Vao' => $time,
                'TrangThai' => $time->greaterThan($gioVao) ? 'tre' : 'dung_gio',
                'Cong' => 1.0,
                'AnhChamCong' => $imageUrl,
            ]);
        } else {
            $oldVao = Carbon::parse($chamCong->Vao);
            $oldRa = $chamCong->Ra ? Carbon::parse($chamCong->Ra) : null;
            $updateData = [];

            if ($time->lessThan($oldVao)) {
                // Quét sớm hơn VÀO cũ → cập nhật lại VÀO (giờ vào sớm nhất)
                $updateData['Vao'] = $time;

                // VÀO cũ trở thành RA nếu nó nằm TRƯỚC GioRa và chưa có RA
                if (!$oldRa && $oldVao->lessThanOrEqualTo($gioRa)) {
                    $updateData['Ra'] = $oldVao;
                }
            } else {
                // Quét trễ hơn VÀO cũ → chỉ cập nhật RA nếu thời gian TRƯỚC GioRa
                // (các lần quét sau GioRa là checkout thực, không phải quét thêm trong ca)
                if ($time->lessThanOrEqualTo($gioRa)) {
                    // Trong khoảng ca: KHÔNG cập nhật Ra (đây là lần quét giữa ca, không phải check-out)
                    // Ra chỉ được cập nhật khi thời gian > GioRa (check-out thực sự)
                    // Do đó: bỏ qua các lần quét nằm trong ca sau VÀO
                } else {
                    // Sau GioRa: đây là lần check-out thực sự → lưu Ra
                    if (!$oldRa || $time->greaterThan($oldRa)) {
                        $updateData['Ra'] = $time;
                    }
                }
            }

            if (!empty($updateData)) {
                $finalRa = isset($updateData['Ra']) ? $updateData['Ra'] : $oldRa;
                $finalVao = isset($updateData['Vao']) ? $updateData['Vao'] : $oldVao;

                $lateDueToCheckin = $finalVao->greaterThan($gioVao);

                if ($finalRa) {
                    $leftEarly = $finalRa->lessThan($gioRa);
                    if ($lateDueToCheckin) {
                        $updateData['TrangThai'] = 'tre';
                    } elseif ($leftEarly) {
                        $updateData['TrangThai'] = 've_som';
                    } else {
                        $updateData['TrangThai'] = 'dung_gio';
                    }
                } elseif (isset($updateData['Vao'])) {
                    $updateData['TrangThai'] = $updateData['Vao']->greaterThan($gioVao) ? 'tre' : 'dung_gio';
                }

                $chamCong->update($updateData);
            }
        }

        return response()->json(['message' => 'Checkin processed'], 200);
    }

    private function tinhTrangThai(Carbon $time, Carbon $gioVao, Carbon $gioRa): string
    {
        if ($time->greaterThan($gioRa)) {
            return 've_som'; // check-out sớm
        }

        if ($time->greaterThan($gioVao)) {
            return 'tre';
        }

        return 'dung_gio';
    }
}
