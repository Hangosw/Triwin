<?php
// Read the blade file and check for </style> inside <script> blocks
$blade = file_get_contents(__DIR__ . '/../resources/views/salary/index.blade.php');

// Find all <script>...</script> blocks
preg_match_all('/<script\b[^>]*>(.*?)<\/script>/si', $blade, $matches);

foreach ($matches[1] as $i => $js) {
    echo "=== SCRIPT BLOCK $i ===\n";
    
    // Check for </style> which Blade might mishandle
    if (strpos($js, '</style>') !== false) {
        echo "!!! CONTAINS </style> !!!\n";
    }
    if (strpos($js, '</script') !== false) {
        echo "!!! CONTAINS </script> !!!\n";
    }
    
    // Find any @xxx patterns that Blade would try to process
    preg_match_all('/@(\w+)/', $js, $directives);
    if (!empty($directives[1])) {
        echo "Blade directives found: " . implode(', ', array_unique($directives[1])) . "\n";
    }
    
    echo "Length: " . strlen($js) . " chars\n";
    echo "First 300 chars: " . substr($js, 0, 300) . "\n\n";
}

// Now let's compile the blade and check output
echo "\n=== COMPILING BLADE ===\n";
$compiler = new class {
    public function compile($value) {
        // Simulate Blade @xxx directive processing
        // Find what @xxx patterns exist
        preg_match_all('/@(\w+)/', $value, $m);
        return array_unique($m[0]);
    }
};

$directives = $compiler->compile($blade);
echo "All @ patterns in file:\n";
foreach ($directives as $d) {
    echo "  $d\n";
}
