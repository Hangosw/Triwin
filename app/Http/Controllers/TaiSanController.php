<?php

namespace App\Http\Controllers;

use App\Models\TaiSan;
use App\Models\NhanVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class TaiSanController extends Controller
{
    public function index()
    {
        $nhanViens = NhanVien::where('TrangThai', 'dang_lam')->orderBy('Ten')->get();
        return view('tai-san.index', compact('nhanViens'));
    }

    public function data(Request $request)
    {
        $query = TaiSan::with('nhanVien');

        // Search
        if ($request->has('search') && $request->search['value']) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('ten_tai_san', 'like', "%{$searchValue}%")
                  ->orWhere('trang_thai', 'like', "%{$searchValue}%");
            });
        }

        $totalRecords = TaiSan::count();
        $filteredRecords = $query->count();

        // Sorting
        if ($request->has('order')) {
            $columnIndex = $request->order[0]['column'];
            $columnDir = $request->order[0]['dir'];
            $columns = [null, 'hinh_anh', 'ten_tai_san', 'ngay_nhap_kho', 'trang_thai', 'nhan_vien_id', 'id'];
            if (isset($columns[$columnIndex]) && $columns[$columnIndex] !== null) {
                $query->orderBy($columns[$columnIndex], $columnDir);
            }
        }

        // Pagination
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

    public function create()
    {
        $nhanViens = NhanVien::where('TrangThai', 'dang_lam')->get();
        return view('tai-san.create', compact('nhanViens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_tai_san' => 'required|string|max:255',
            'ngay_nhap_kho' => 'required|string', // Changed to string for flatpickr
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'trang_thai' => 'required|string',
            'nhan_vien_id' => 'nullable|exists:nhan_viens,id',
            'ngay_muon' => 'nullable|string', // Changed to string for flatpickr
        ]);

        $data = $request->all();

        if ($request->ngay_nhap_kho) {
            $data['ngay_nhap_kho'] = \Carbon\Carbon::createFromFormat('d/m/Y', $request->ngay_nhap_kho)->format('Y-m-d');
        }
        if ($request->ngay_muon) {
            $data['ngay_muon'] = \Carbon\Carbon::createFromFormat('d/m/Y', $request->ngay_muon)->format('Y-m-d');
        }

        if ($request->hasFile('hinh_anh')) {
            $file = $request->file('hinh_anh');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('TaiSan'), $filename);
            $data['hinh_anh'] = 'TaiSan/' . $filename;
        }

        TaiSan::create($data);

        return redirect()->route('tai-san.index')->with('success', 'Thêm tài sản thành công!');
    }

    public function edit($id)
    {
        $taiSan = TaiSan::findOrFail($id);
        $nhanViens = NhanVien::where('TrangThai', 'dang_lam')->get();
        return view('tai-san.edit', compact('taiSan', 'nhanViens'));
    }

    public function update(Request $request, $id)
    {
        $taiSan = TaiSan::findOrFail($id);

        $request->validate([
            'ten_tai_san' => 'required|string|max:255',
            'ngay_nhap_kho' => 'required|string',
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'trang_thai' => 'required|string',
            'nhan_vien_id' => 'nullable|exists:nhan_viens,id',
            'ngay_muon' => 'nullable|string',
        ]);

        $data = $request->all();

        if ($request->ngay_nhap_kho) {
            $data['ngay_nhap_kho'] = \Carbon\Carbon::createFromFormat('d/m/Y', $request->ngay_nhap_kho)->format('Y-m-d');
        }
        if ($request->ngay_muon) {
            $data['ngay_muon'] = \Carbon\Carbon::createFromFormat('d/m/Y', $request->ngay_muon)->format('Y-m-d');
        }

        if ($request->hasFile('hinh_anh')) {
            // Delete old image
            if ($taiSan->hinh_anh && \Illuminate\Support\Facades\File::exists(public_path($taiSan->hinh_anh))) {
                \Illuminate\Support\Facades\File::delete(public_path($taiSan->hinh_anh));
            }

            $file = $request->file('hinh_anh');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('TaiSan'), $filename);
            $data['hinh_anh'] = 'TaiSan/' . $filename;
        }

        // If status is not Borrowed, clear borrower info
        if ($request->trang_thai !== 'DangMuon') {
            $data['nhan_vien_id'] = null;
            $data['ngay_muon'] = null;
        }

        $taiSan->update($data);

        return redirect()->route('tai-san.index')->with('success', 'Cập nhật tài sản thành công!');
    }

    /**
     * Cấp phát tài sản cho nhân viên
     */
    public function capPhat(Request $request, $id)
    {
        $taiSan = TaiSan::findOrFail($id);
        
        $request->validate([
            'nhan_vien_id' => 'required|exists:nhan_viens,id',
            'ngay_muon' => 'required|string',
            'ghi_chu' => 'nullable|string',
        ]);

        $taiSan->update([
            'nhan_vien_id' => $request->nhan_vien_id,
            'ngay_muon' => \Carbon\Carbon::createFromFormat('d/m/Y', $request->ngay_muon)->format('Y-m-d'),
            'trang_thai' => 'DangMuon',
            'ghi_chu' => $request->ghi_chu ?? $taiSan->ghi_chu
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cấp phát tài sản thành công!'
        ]);
    }

    /**
     * Thu hồi tài sản
     */
    public function thuHoi($id)
    {
        $taiSan = TaiSan::findOrFail($id);

        $taiSan->update([
            'nhan_vien_id' => null,
            'ngay_muon' => null,
            'trang_thai' => 'SanSang'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thu hồi tài sản thành công!'
        ]);
    }

    public function destroy($id)
    {
        $taiSan = TaiSan::findOrFail($id);
        
        if ($taiSan->hinh_anh && File::exists(public_path($taiSan->hinh_anh))) {
            File::delete(public_path($taiSan->hinh_anh));
        }

        $taiSan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa tài sản thành công!'
        ]);
    }
}
