<?php

namespace App\Http\Controllers;

use App\Models\NgachLuong;
use App\Models\HopDong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NgachLuongController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all salary scales with the count of unique employees having active contracts
        $ngachLuongs = NgachLuong::withCount(['dienBienLuongs' => function($query) {
            $query->whereExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('hop_dongs')
                  ->whereColumn('hop_dongs.Id', 'dien_bien_luongs.HopDongId')
                  ->where('hop_dongs.TrangThai', 1); // Only active contracts
            });
        }])->get();

        return view('ngach-luong.index', compact('ngachLuongs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'Ma' => 'required|string|unique:ngach_luongs,Ma',
            'Ten' => 'required|string',
            'Nhom' => 'required|string',
        ], [
            'Ma.required' => 'Mã ngạch không được để trống',
            'Ma.unique' => 'Mã ngạch đã tồn tại',
            'Ten.required' => 'Tên ngạch không được để trống',
            'Nhom.required' => 'Nhóm ngạch không được để trống',
        ]);

        try {
            NgachLuong::create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Thêm ngạch lương thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'Ma' => 'required|string|unique:ngach_luongs,Ma,' . $id . ',Id',
            'Ten' => 'required|string',
            'Nhom' => 'required|string',
        ], [
            'Ma.required' => 'Mã ngạch không được để trống',
            'Ma.unique' => 'Mã ngạch đã tồn tại',
            'Ten.required' => 'Tên ngạch không được để trống',
            'Nhom.required' => 'Nhóm ngạch không được để trống',
        ]);

        try {
            $ngachLuong = NgachLuong::findOrFail($id);
            $ngachLuong->update($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật ngạch lương thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $ngachLuong = NgachLuong::findOrFail($id);
            
            // Check if there are any dependent records
            if ($ngachLuong->bacLuongs()->count() > 0 || $ngachLuong->dienBienLuongs()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa ngạch lương này vì đã có bậc lương hoặc diễn biến lương liên quan.'
                ], 400);
            }

            $ngachLuong->delete();
            return response()->json([
                'success' => true,
                'message' => 'Xóa ngạch lương thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
