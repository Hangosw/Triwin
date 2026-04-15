<?php
$blade = file_get_contents(__DIR__ . '/../resources/views/salary/index.blade.php');

// Count TH in header row
preg_match('/<thead>\s*<tr>(.*?)<\/tr>\s*<\/thead>/su', $blade, $mHeaderRow);
$ths = [0 => []];
if (isset($mHeaderRow[1])) {
    preg_match_all('/<th\b[^>]*>(.*?)<\/th>/siu', $mHeaderRow[1], $ths);
}
echo "Header TH count: " . count($ths[0]) . "\n";
foreach($ths[1] as $idx => $th) {
    echo "  TH $idx: " . trim(strip_tags($th)) . "\n";
}

// Check salary-row TDs
$tds = [0 => []];
if (preg_match('/<tr class="salary-row">\s*(.*?)\s*<\/tr>/su', $blade, $match)) {
    preg_match_all('/<td\b[^>]*>(.*?)<\/td>/siu', $match[1], $tds);
    echo "TD count in salary-row: " . count($tds[0]) . "\n";
}

// Check group-header-row TDs
$gtds = [0 => []];
if (preg_match('/<tr class="group-header-row">\s*(.*?)\s*<\/tr>/su', $blade, $match)) {
    preg_match_all('/<td\b[^>]*>(.*?)<\/td>/siu', $match[1], $gtds);
    echo "TD count in group-header-row: " . count($gtds[0]) . "\n";
    if (isset($gtds[0][0]) && preg_match('/colspan="(\d+)"/', $gtds[0][0], $mC)) {
        echo "Group header colspan: " . $mC[1] . "\n";
    }
}
