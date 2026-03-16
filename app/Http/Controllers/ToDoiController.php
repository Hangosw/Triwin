<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DmToDoi;
use App\Models\DmPhongBan;
use App\Models\NhanVien;
use App\Models\ToDoiLanhDao;
use Illuminate\Support\Facades\DB;

class ToDoiController extends Controller
{
    public function index()
    {
        return view('todoi.index');
    }

    public function DataToDoi(Request $request)
    {
        $query = \App\Models\DmToDoi::with(['phongBan', 'lanhDaoHienTai.nhanVien']);

        // Server-side processing
        $totalRecords = clone $query;
        $totalRecords = $totalRecords->count();

        // Search
        if ($request->has('search') && $request->search['value']) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('Ma', 'like', "%{$searchValue}%")
                    ->orWhere('Ten', 'like', "%{$searchValue}%")
                    ->orWhereHas('phongBan', function ($sq) use ($searchValue) {
                        $sq->where('Ten', 'like', "%{$searchValue}%");
                    });
            });
        }

        $filteredRecords = clone $query;
        $filteredRecords = $filteredRecords->count();

        // Sorting
        if ($request->has('order') && count($request->order)) {
            $columnIndex = $request->order[0]['column'];
            $columnDir = $request->order[0]['dir'];
            $columns = ['Ma', 'Ten', 'PhongBanId', 'GhiChu'];

            // Default to 'id' if sorting by an undefined column (like STT or Actions)
            $orderBy = isset($columns[$columnIndex]) ? $columns[$columnIndex] : 'id';

            if ($orderBy === 'PhongBanId') {
                // If sorting by department name, we need to join the tables
                $query->join('dm_phong_bans', 'dm_to_dois.PhongBanId', '=', 'dm_phong_bans.id')
                    ->orderBy('dm_phong_bans.Ten', $columnDir)
                    ->select('dm_to_dois.*'); // Ensure we only select to_doi columns to avoid ID conflicts
            } else {
                $query->orderBy($orderBy, $columnDir);
            }
        } else {
            // Default sort by ID descending
            $query->orderBy('id', 'desc');
        }

        // Pagination
        $start = $request->start ?? 0;
        $length = $request->length ?? 10;

        $data = $query->skip($start)->take($length)->get();

        // Transform data for DataTables
        $data->transform(function ($item) {
            $truongTo = $item->truongTo;
            $truongToName = $truongTo ? $truongTo->Ten : '<span class="text-gray italic">Chưa xác định</span>';

            return [
                'id' => $item->id,
                'Ma' => '<a href="' . route('to-doi.detail', $item->id) . '" class="text-blue-600 hover:text-blue-800">' . $item->Ma . '</a>',
                'Ten' => $item->Ten,
                'PhongBan' => $item->phongBan ? $item->phongBan->Ten : '',
                'TruongTo' => $truongToName,
                'GhiChu' => $item->GhiChu ?? '',
            ];
        });

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    public function create()
    {
        $phongBans = DmPhongBan::all();
        return view('todoi.create', compact('phongBans'));
    }



    public function getNhanViens($phongBanId)
    {
        $nhanViens = \App\Models\NhanVien::whereHas('ttCongViec', function ($query) use ($phongBanId) {
            $query->where('PhongBanId', $phongBanId)
                ->where('LoaiNhanVien', 0); // 0 là Công nhân
        })->with('ttCongViec.chucVu')->get();

        $result = $nhanViens->map(function ($nv) {
            return [
                'id' => $nv->id,
                'Ma' => $nv->Ma,
                'Ten' => $nv->Ten,
                'ChucVu' => $nv->chucVu?->Ten ?? 'Chưa rõ CV'
            ];
        });

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $request->validate([
            'Ma' => 'required|string|max:50|unique:dm_to_dois,Ma',
            'Ten' => 'required|string|max:255',
            'PhongBanId' => 'required|exists:dm_phong_bans,id',
            'TruongToId' => 'nullable|exists:nhan_viens,id',
            'GhiChu' => 'nullable|string'
        ], [
            'Ma.required' => 'Mã tổ đội không được để trống.',
            'Ma.unique' => 'Mã tổ đội đã tồn tại.',
            'Ten.required' => 'Tên tổ đội không được để trống.',
            'PhongBanId.required' => 'Vui lòng chọn phòng ban.'
        ]);

        DB::beginTransaction();
        try {
            $todoi = DmToDoi::create([
                'Ma' => $request->Ma,
                'Ten' => $request->Ten,
                'PhongBanId' => $request->PhongBanId,
                'GhiChu' => $request->GhiChu
            ]);

            if ($request->filled('TruongToId')) {
                ToDoiLanhDao::create([
                    'ToDoiId' => $todoi->id,
                    'NhanVienId' => $request->TruongToId,
                    'VaiTro' => 1, // 1 for Tổ trưởng
                    'NgayBatDau' => now()
                ]);
            }

            if ($request->filled('ThanhVienIds') && is_array($request->ThanhVienIds)) {
                $thanhVienIds = array_filter($request->ThanhVienIds);
                if (!empty($thanhVienIds)) {
                    \App\Models\TtNhanVienCongViec::whereIn('NhanVienId', $thanhVienIds)
                        ->update(['ToDoiId' => $todoi->id]);
                }
            }

            DB::commit();
            return redirect()->route('to-doi.danh-sach')->with('success', 'Thêm mới Tổ Đội và phân bổ nhân sự thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $todoi = DmToDoi::with(['phongBan.donVi', 'lanhDaoHienTai.nhanVien.ttCongViec.chucVu'])->findOrFail($id);

        // Members of the team
        $thanhViens = \App\Models\NhanVien::whereHas('ttCongViec', function ($q) use ($id) {
            $q->where('ToDoiId', $id);
        })->with('ttCongViec.chucVu')->get();

        $phongBanId = $todoi->PhongBanId;
        $nhanVienKhacs = \App\Models\NhanVien::whereHas('ttCongViec', function ($q) use ($id, $phongBanId) {
            if ($phongBanId) {
                $q->where('PhongBanId', $phongBanId);
            }
            $q->where(function ($sq) use ($id) {
                $sq->whereNull('ToDoiId')->orWhere('ToDoiId', '!=', $id);
            });
        })->with('ttCongViec.chucVu')->get();

        $truongTo = $todoi->truongTo; // using the accessor from DmToDoi model

        return view('todoi.detail', compact('todoi', 'thanhViens', 'truongTo', 'nhanVienKhacs'));
    }

    public function addMember(Request $request, $id)
    {
        $request->validate([
            'NhanVienIds' => 'required|array',
            'NhanVienIds.*' => 'exists:nhan_viens,id'
        ], [
            'NhanVienIds.required' => 'Vui lòng chọn ít nhất một nhân viên.'
        ]);

        try {
            if (!empty($request->NhanVienIds)) {
                \App\Models\TtNhanVienCongViec::whereIn('NhanVienId', $request->NhanVienIds)
                    ->update(['ToDoiId' => $id]);
            }

            return back()->with('success', 'Thêm thành viên vào tổ đội thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function changeLeader(Request $request, $id)
    {
        $request->validate([
            'NewLeaderId' => 'required|exists:nhan_viens,id'
        ], [
            'NewLeaderId.required' => 'Vui lòng chọn tổ trưởng mới.'
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // End date for current leader
            \App\Models\ToDoiLanhDao::where('ToDoiId', $id)
                ->whereNull('NgayKetThuc')
                ->where('VaiTro', 1)
                ->update(['NgayKetThuc' => now()]);

            // Assign new leader
            \App\Models\ToDoiLanhDao::create([
                'ToDoiId' => $id,
                'NhanVienId' => $request->NewLeaderId,
                'VaiTro' => 1,
                'NgayBatDau' => now()
            ]);

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', 'Thay đổi tổ trưởng thành công!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
