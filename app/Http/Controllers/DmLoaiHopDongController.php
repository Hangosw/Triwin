<?php

namespace App\Http\Controllers;

use App\Models\DmLoaiHopDong;
use Illuminate\Http\Request;
use App\Services\SystemLogService;
use Illuminate\Support\Facades\DB;

class DmLoaiHopDongController extends Controller
{
    public function index()
    {
        return view('contracts.loai-hop-dong.index');
    }

    public function data(Request $request)
    {
        $query = DmLoaiHopDong::query();

        if ($request->has('search') && $request->search['value']) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('MaLoai', 'like', "%{$searchValue}%")
                  ->orWhere('TenLoai', 'like', "%{$searchValue}%");
            });
        }

        $totalRecords = DmLoaiHopDong::count();
        $filteredRecords = $query->count();

        // Sorting
        if ($request->has('order')) {
            $columnIndex = $request->order[0]['column'];
            $columnDir = $request->order[0]['dir'];
            $columns = [
                1 => 'MaLoai',
                2 => 'TenLoai',
                3 => 'ThoiHanThang',
                4 => 'ThoiHanBaoTruoc',
                5 => 'CoDongBaoHiem',
                6 => 'TrangThai'
            ];
            if (isset($columns[$columnIndex])) {
                $query->orderBy($columns[$columnIndex], $columnDir);
            }
        }

        $start = $request->start ?? 0;
        $length = $request->length ?? 10;
        $data = $query->skip($start)->take($length)->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'MaLoai' => 'required|string|max:50|unique:dm_loai_hop_dongs,MaLoai',
            'TenLoai' => 'required|string|max:255',
            'ThoiHanThang' => 'nullable|integer|min:0',
            'ThoiHanBaoTruoc' => 'nullable|integer|min:0',
            'CoDongBaoHiem' => 'nullable',
            'TrangThai' => 'required|in:mo,khoa',
        ]);

        $validated['CoDongBaoHiem'] = $request->has('CoDongBaoHiem') ? 1 : 0;

        try {
            $item = DmLoaiHopDong::create($validated);
            SystemLogService::log('Tạo mới', 'DmLoaiHopDong', $item->id, "Thêm loại hợp đồng: {$item->TenLoai}");

            return response()->json(['success' => true, 'message' => 'Thêm loại hợp đồng thành công!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $item = DmLoaiHopDong::findOrFail($id);

        $validated = $request->validate([
            'MaLoai' => 'required|string|max:50|unique:dm_loai_hop_dongs,MaLoai,' . $id,
            'TenLoai' => 'required|string|max:255',
            'ThoiHanThang' => 'nullable|integer|min:0',
            'ThoiHanBaoTruoc' => 'nullable|integer|min:0',
            'CoDongBaoHiem' => 'nullable',
            'TrangThai' => 'required|in:mo,khoa',
        ]);

        $validated['CoDongBaoHiem'] = $request->has('CoDongBaoHiem') ? 1 : 0;

        try {
            $oldData = $item->toArray();
            $item->update($validated);
            SystemLogService::log('Cập nhật', 'DmLoaiHopDong', $item->id, "Cập nhật loại hợp đồng: {$item->TenLoai}", $oldData, $item->fresh()->toArray());

            return response()->json(['success' => true, 'message' => 'Cập nhật loại hợp đồng thành công!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

        // Nút xóa đã bị loại bỏ theo yêu cầu


    public function toggleStatus($id)
    {
        try {
            $item = DmLoaiHopDong::findOrFail($id);
            $oldStatus = $item->TrangThai;
            $newStatus = ($oldStatus === 'mo' ? 'khoa' : 'mo');
            
            $item->update(['TrangThai' => $newStatus]);
            
            SystemLogService::log('Cập nhật trạng thái', 'DmLoaiHopDong', $id, 
                "Thay đổi trạng thái loại hợp đồng: {$item->TenLoai} từ {$oldStatus} sang {$newStatus}");

            return response()->json([
                'success' => true, 
                'message' => 'Cập nhật trạng thái thành công!',
                'new_status' => $newStatus
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }
}
