<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LichSuController extends Controller
{
    public function index()
    {
        $logs = DB::table('lich_sus')
            ->leftJoin('nguoi_dungs', 'lich_sus.NhanVienId', '=', 'nguoi_dungs.id')
            ->select('lich_sus.*', 'nguoi_dungs.Ten as TenNguoiDung', 'nguoi_dungs.TaiKhoan')
            ->orderByDesc('lich_sus.Id')
            ->get();

        return view('lich-su.index', compact('logs'));
    }
}
