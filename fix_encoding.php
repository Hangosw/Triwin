<?php
$files = [
    'c:\Users\huy hoang dz\Desktop\Triwin\resources\views\overtime\self.blade.php',
    'c:\Users\huy hoang dz\Desktop\Triwin\resources\views\overtime\index.blade.php',
    'c:\Users\huy hoang dz\Desktop\Triwin\resources\views\nghi-phep\index.blade.php',
    'c:\Users\huy hoang dz\Desktop\Triwin\resources\views\nghi-phep\self.blade.php',
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "Processing $file...\n";
        $content = file_get_contents($file);
        
        // Remove null bytes
        $content = str_replace("\0", "", $content);
        
        // Map common broken sequences back to Vietnamese if possible
        // But better to just try a clean conversion
        $converted = mb_convert_encoding($content, 'UTF-8', 'Windows-1258, UTF-16, UTF-16BE, UTF-16LE, Windows-1252, ISO-8859-1');
        
        // If conversion looks like it failed or didn't change anything, try to just force UTF-8
        if (empty($converted) || $converted === $content) {
             // Maybe it's already UTF-8 but some chars are messed up?
             // No, usually if it's messed up it's one of the above.
        }
        
        file_put_contents($file, $converted ?: $content);
        echo "Done $file.\n";
    }
}
