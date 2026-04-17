<?php

$dir = __DIR__;
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($files as $file) {
    if ($file->isFile() && in_array($file->getExtension(), ['php'])) {
        $content = file_get_contents($file->getPathname());
        if (strpos($content, 'Vietnam Rubber Group') !== false) {
            $lines = explode("\n", $content);
            foreach ($lines as $ln => $line) {
                if (strpos($line, 'Vietnam Rubber Group') !== false) {
                    echo "{$file->getFilename()}:" . ($ln + 1) . ": " . trim($line) . "\n";
                }
            }
        }
    }
}
