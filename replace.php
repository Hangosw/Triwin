<?php

$dirs = [
    __DIR__ . '/app',
    __DIR__ . '/resources/views'
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($files as $file) {
        if ($file->isFile() && in_array($file->getExtension(), ['php'])) {
            $content = file_get_contents($file->getPathname());
            
            if (strpos($content, 'Vietnam Rubber Group') !== false) {
                // If the file is a blade view
                if (strpos($file->getFilename(), '.blade.php') !== false) {
                    // special case for @section('title', ... )
                    if (preg_match('/@section\(\'title\',\s*\'[^\']*Vietnam Rubber Group[^\']*\'\)/', $content)) {
                        // We will replace Vietnam Rubber Group with the PHP evaluation
                        // for example: @section('title', 'Admin - Vietnam Rubber Group')
                        // becomes:  @section('title', 'Admin - ' . \App\Models\SystemConfig::getValue('company_name'))
                        $content = preg_replace_callback('/@section\(\'title\',\s*\'([^\']*?)Vietnam Rubber Group([^\']*?)\'\)/', function ($m) {
                            $prefix = $m[1] !== '' ? "'" . $m[1] . "' . " : "";
                            $suffix = $m[2] !== '' ? " . '" . $m[2] . "'" : "";
                            
                            $val = "\\App\\Models\\SystemConfig::getValue('company_name')";
                            
                            $str = trim($prefix . $val . $suffix, " .");
                            return "@section('title', $str)";
                        }, $content);
                    }
                    
                    // case for <title> tags
                    if (strpos($content, '<title>') !== false) {
                        $content = preg_replace('/<title>(.*?)Vietnam Rubber Group(.*?)<\/title>/', '<title>$1{{ \App\Models\SystemConfig::getValue(\'company_name\') }}$2</title>', $content);
                    }
                    
                    // general case for blade (might replace some valid text as well)
                    $content = str_replace('Vietnam Rubber Group', '{{ \App\Models\SystemConfig::getValue(\'company_name\') }}', $content);
                } else {
                    // In pure PHP files (like mailers, exports)
                    // if it's inside a double quoted string, e.g. "Vietnam Rubber Group"
                    $content = preg_replace('/"([^"]*?)Vietnam Rubber Group([^"]*?)"/', '"$1" . \App\Models\SystemConfig::getValue(\'company_name\') . "$2"', $content);
                    
                    // if it's inside a single quoted string, e.g. 'Vietnam Rubber Group'
                    $content = preg_replace('/\'([^\']*?)Vietnam Rubber Group([^\']*?)\'/', "'$1' . \App\Models\SystemConfig::getValue('company_name') . '$2'", $content);
                    
                    // Cleanup empty strings concatenation
                    $content = str_replace('"" . ', '', $content);
                    $content = str_replace('. ""', '', $content);
                    $content = str_replace("'' . ", '', $content);
                    $content = str_replace(". ''", '', $content);
                }
                
                file_put_contents($file->getPathname(), $content);
                echo "Replaced in: " . $file->getPathname() . "\n";
            }
        }
    }
}
