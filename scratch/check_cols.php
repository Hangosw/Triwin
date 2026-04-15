<?php
$blade = file_get_contents(__DIR__ . '/../resources/views/salary/index.blade.php');

// Count TH in THEAD
if (preg_match('/<thead>(.*?)<\/thead>/s', $blade, $match)) {
    $thCount = substr_count($match[1], '<th');
    echo "TH count in THEAD: $thCount\n";
}

// Check salary-row TDs
if (preg_match('/<tr class="salary-row">(.*?)<\/tr>/s', $blade, $match)) {
    $tdCount = substr_count($match[1], '<td');
    echo "TD count in salary-row: $tdCount\n";
}

// Check group-header-row TDs
if (preg_match('/<tr class="group-header-row">(.*?)<\/tr>/s', $blade, $match)) {
    $tdCount = substr_count($match[1], '<td');
    echo "TD count in group-header-row: $tdCount\n";
    if (preg_match('/colspan="(\d+)"/', $match[1], $m)) {
        echo "Group header colspan: " . $m[1] . "\n";
    }
}
