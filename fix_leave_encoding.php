<?php
$path = 'c:\Users\huy hoang dz\Desktop\Triwin\resources\views\leave\self.blade.php';
$content = file_get_contents($path);
// Ensure it's treated as UTF-8
file_put_contents($path, $content);
echo "File rewritten to ensure UTF-8.";
