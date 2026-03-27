<?php

namespace App\Services;

use App\Models\SystemConfig;
use App\Models\NhanVien;
use App\Models\ChamCong;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HanetCheckinService
{
    protected $baseUrl = 'https://partner.hanet.ai/person/getCheckinByPlaceIdInDay';

    /**
     * Fetch checkins from Hanet for a specific date
     */
    public function fetchCheckins($date = null)
    {
        $date = $date ?? Carbon::today()->toDateString();
        $token = SystemConfig::getValue('hanet_access_token');
        $placeID = SystemConfig::getValue('hanet_place_id', '994544');

        if (!$token) {
            Log::error("HanetSync: No access token found in system_configs.");
            return ['success' => false, 'error' => 'Missing access token'];
        }

        try {
            $response = Http::asForm()->post($this->baseUrl, [
                'token' => $token,
                'placeID' => $placeID,
                'date' => $date,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['returnCode']) && $data['returnCode'] == 1) {
                    return ['success' => true, 'data' => $data['data']];
                }
                return ['success' => false, 'error' => $data['returnMessage'] ?? 'Unknown API error'];
            }

            return ['success' => false, 'error' => 'HTTP Error: ' . $response->status()];
        } catch (\Exception $e) {
            Log::error("HanetSync Error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Sync checkins to local attendance table
     */
    public function syncCheckinsToAttendance($date = null)
    {
        $date = $date ?? Carbon::today()->toDateString();
        $result = $this->fetchCheckins($date);

        if (!$result['success']) {
            return $result;
        }

        $checkins = $result['data'];
        if (empty($checkins)) {
            return ['success' => true, 'message' => 'No checkins found for ' . $date, 'count' => 0];
        }

        // Group checkins by aliasID (Employee Code)
        $grouped = collect($checkins)->groupBy('aliasID');

        $ca = \App\Models\DmCaLamViec::where('MaCa', 'HC')->first();

        $syncCount = 0;
        foreach ($grouped as $aliasID => $personCheckins) {
            // Find employee by Ma (aliasID)
            $nhanVien = NhanVien::where('Ma', $aliasID)->first();
            if (!$nhanVien) {
                Log::warning("HanetSync: No employee found for Ma/aliasID: " . $aliasID);
                continue;
            }

            // Sắp xếp theo thời gian tăng dần
            $sortedCheckins = $personCheckins->sortBy('checkinTime')->values();

            // Bản ghi đầu tiên trong ngày → giờ VÀO
            $firstCheckin = $sortedCheckins->first();
            $vao = Carbon::createFromTimestampMs($firstCheckin['checkinTime'])
                ->setTimezone(config('app.timezone', 'Asia/Ho_Chi_Minh'));

            $trangThai = 'dung_gio';
            $ra = null;

            if ($ca) {
                $dateStr = $vao->toDateString();
                $gioVao = Carbon::parse($dateStr . ' ' . $ca->GioVao);
                $gioRa = Carbon::parse($dateStr . ' ' . $ca->GioRa);

                // Bản ghi cuối cùng có thời gian TRƯỚC GioRa → giờ RA
                // (bỏ qua những lần quét sau GioRa vì chưa đến giờ checkout thực sự)
                $lastBeforeGioRa = $sortedCheckins
                    ->filter(function ($checkin) use ($gioRa) {
                        $t = Carbon::createFromTimestampMs($checkin['checkinTime'])
                            ->setTimezone(config('app.timezone', 'Asia/Ho_Chi_Minh'));
                        return $t->lessThanOrEqualTo($gioRa);
                    })
                    ->last();

                // Ra chỉ được set nếu có ít nhất 2 lần quét và bản ghi cuối != bản ghi đầu
                if ($lastBeforeGioRa && $lastBeforeGioRa['checkinTime'] !== $firstCheckin['checkinTime']) {
                    $ra = Carbon::createFromTimestampMs($lastBeforeGioRa['checkinTime'])
                        ->setTimezone(config('app.timezone', 'Asia/Ho_Chi_Minh'));
                }

                $lateDueToCheckin = $vao->greaterThan($gioVao);

                if ($ra) {
                    $leftEarly = $ra->lessThan($gioRa);
                    if ($lateDueToCheckin) {
                        $trangThai = 'tre';
                    } elseif ($leftEarly) {
                        $trangThai = 've_som';
                    } else {
                        $trangThai = 'dung_gio';
                    }
                } else {
                    $trangThai = $lateDueToCheckin ? 'tre' : 'dung_gio';
                }
            } else {
                // Không có ca → lấy bản ghi cuối cùng làm Ra (fallback cũ)
                $lastCheckin = $sortedCheckins->last();
                if ($lastCheckin['checkinTime'] !== $firstCheckin['checkinTime']) {
                    $ra = Carbon::createFromTimestampMs($lastCheckin['checkinTime'])
                        ->setTimezone(config('app.timezone', 'Asia/Ho_Chi_Minh'));
                }
            }

            // Update or Create ChamCong record
            ChamCong::updateOrCreate(
                [
                    'NhanVienId' => $nhanVien->id,
                    'Loai' => 0, // Administrative
                    'Vao' => $vao->toDateString() . ' ' . $vao->toTimeString(),
                ],
                [
                    'Vao' => $vao,
                    'Ra' => $ra,
                    'TrangThai' => $trangThai,
                    'Cong' => $ra ? 1.0 : 0.0,
                ]
            );

            $syncCount++;
        }

        return ['success' => true, 'count' => $syncCount];
    }
}
