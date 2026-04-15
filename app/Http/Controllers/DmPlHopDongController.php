<?php

namespace App\Http\Controllers;

use App\Models\DmPlHopDong;
use Illuminate\Http\Request;
use App\Services\SystemLogService;
use Illuminate\Support\Facades\DB;

class DmPlHopDongController extends Controller
{
    public function index()
    {
        return view('contracts.dm_phu_luc.index');
    }

    public function data(Request $request)
    {
        $query = DmPlHopDong::query();

        if ($request->has('search') && $request->search['value']) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('keyvalue', 'like', "%{$searchValue}%")
                  ->orWhere('noi_dung', 'like', "%{$searchValue}%");
            });
        }

        $totalRecords = DmPlHopDong::count();
        $filteredRecords = $query->count();

        // Sorting
        if ($request->has('order')) {
            $columnIndex = $request->order[0]['column'];
            $columnDir = $request->order[0]['dir'];
            $columns = [
                1 => 'keyvalue',
                2 => 'noi_dung',
                3 => 'is_bhxh',
                4 => 'TrangThai'
            ];
            if (isset($columns[$columnIndex])) {
                $col = $columns[$columnIndex];
                if ($col === 'keyvalue') {
                    $query->orderByRaw("LENGTH($col) $columnDir")->orderBy($col, $columnDir);
                } else {
                    $query->orderBy($col, $columnDir);
                }
            }
        } else {
            $query->orderByRaw("LENGTH(keyvalue) asc")->orderBy('keyvalue', 'asc');
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
            'keyvalue' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'TrangThai' => 'required|in:mo,khoa',
            'is_bhxh' => 'nullable',
        ]);

        $validated['is_bhxh'] = $request->has('is_bhxh') ? 1 : 0;

        try {
            $item = DmPlHopDong::create($validated);
            SystemLogService::log('Tạo mới', 'DmPlHopDong', $item->id, "Thêm danh mục phụ lục: {$item->keyvalue}");

            return response()->json(['success' => true, 'message' => 'Thêm danh mục phụ lục thành công!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $item = DmPlHopDong::findOrFail($id);

        $validated = $request->validate([
            'keyvalue' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'TrangThai' => 'required|in:mo,khoa',
            'is_bhxh' => 'nullable',
        ]);

        $validated['is_bhxh'] = $request->has('is_bhxh') ? 1 : 0;

        try {
            $oldData = $item->toArray();
            $item->update($validated);
            SystemLogService::log('Cập nhật', 'DmPlHopDong', $item->id, "Cập nhật danh mục phụ lục: {$item->keyvalue}", $oldData, $item->fresh()->toArray());

            return response()->json(['success' => true, 'message' => 'Cập nhật danh mục phụ lục thành công!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }
}
