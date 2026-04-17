<?php
use App\Models\HopDong;

$hDongs = HopDong::with('phuLucs.hopDongPL')->take(5)->get();
foreach($hDongs as $hd){
    echo "NhanVienId: " . $hd->NhanVienId . " | Root Contract: " . $hd->id . " - TongLuong: " . $hd->TongLuong . "\n";
    foreach($hd->phuLucs as $pl){
        if ($pl->hopDongPL) {
            echo " -- Appendix HopDongPL: " . $pl->hopDongPL->id . " - TongLuong: " . $pl->hopDongPL->TongLuong . " - NgayBatDau: " . $pl->hopDongPL->NgayBatDau . "\n";
        }
    }
}
