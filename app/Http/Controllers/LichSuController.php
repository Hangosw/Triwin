<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LichSuController extends Controller
{
    public function index()
    {
        $logs = DB::table('lich_sus')
            ->leftJoin('nhan_viens', 'lich_sus.NhanVienId', '=', 'nhan_viens.id')
            ->select('lich_sus.*', 'nhan_viens.Ten as TenNhanVien')
            ->latest('lich_sus.Created_At')
            ->paginate(50);

        return view('lich-su.index', compact('logs'));
    }
}
