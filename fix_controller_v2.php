<?php
$file = 'app/Http/Controllers/HopDongController.php';
$lines = file($file);
$to_delete = array_merge(range(268, 278), range(930, 940)); // 0-indexed: lines 269-279 and 931-941
$new_lines = [];
foreach($lines as $i => $line) {
    if(!in_array($i, $to_delete)) {
        $new_lines[] = $line;
    }
}
file_put_contents($file, implode('', $new_lines));
echo "Fix applied\n";
