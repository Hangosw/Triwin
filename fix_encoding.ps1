$files = @(
    "c:\Users\huy hoang dz\Desktop\Triwin\resources\views\overtime\self.blade.php",
    "c:\Users\huy hoang dz\Desktop\Triwin\resources\views\overtime\index.blade.php",
    "c:\Users\huy hoang dz\Desktop\Triwin\resources\views\nghi-phep\index.blade.php",
    "c:\Users\huy hoang dz\Desktop\Triwin\resources\views\nghi-phep\self.blade.php"
)

foreach ($f in $files) {
    if (Test-Path $f) {
        try {
            # Read using Windows-1258 (Vietnamese)
            $content = Get-Content -Path $f -Encoding String -Raw
            # Set-Content with UTF8 encoding usually uses UTF8 with BOM in older PS, 
            # but [System.IO.File]::WriteAllText is better for no-BOM if needed.
            # However, standard UTF8 is usually fine for Laravel.
            [System.IO.File]::WriteAllText($f, $content, [System.Text.Encoding]::UTF8)
            Write-Host "Converted $f"
        } catch {
            Write-Warning "Failed to convert $f : $_"
        }
    }
}
