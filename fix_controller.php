<?php
$file = 'app/Http/Controllers/HopDongController.php';
$content = file_get_contents($file);

// Improved regex with more flexible whitespace matching
$patternTao = "/'LuongCoBan' => \(float\) str_replace\(\['\.', ','\], '', \$request->luong_co_ban\),\s+'PhuCapChucVu'.*?'phu_cap_ngoai_bhxh' => \$phu_cap_ngoai_bhxh,/s";
$replacementTao = "'LuongCoBan' => (float) str_replace(['.', ','], '', \$request->luong_co_ban),";
$content = preg_replace($patternTao, $replacementTao, $content);

$patternCapNhat = "/'LuongCoBan' => \(float\) str_replace\(\['\.', ','\], '', \$request->luong_co_ban\),\s+'PhuCapChucVu'.*?'phu_cap_ngoai_bhxh' => \$phu_cap_ngoai_bhxh,/s";
$replacementCapNhat = "'LuongCoBan' => (float) str_replace(['.', ','], '', \$request->luong_co_ban),";
$content = preg_replace($patternCapNhat, $replacementCapNhat, $content);

file_put_contents($file, $content);
echo "HopDongController fixed successfully.\n";
