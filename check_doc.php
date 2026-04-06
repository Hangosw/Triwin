<?php
require 'vendor/autoload.php';

$file = 'public/01-2024-HDLD_Non-Disclosure Agreement -VUONG BAO CHAU.doc';
if (!file_exists($file)) {
    die("File not found");
}

try {
    $phpWord = \PhpOffice\PhpWord\IOFactory::load($file);
    echo "Successfully loaded as " . get_class($phpWord);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
