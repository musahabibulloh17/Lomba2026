# Script untuk mengaktifkan PostgreSQL Extension di PHP

Write-Host "==================================" -ForegroundColor Cyan
Write-Host "Aktivasi PostgreSQL Extension PHP" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
Write-Host ""

# Lokasi php.ini
$phpIniPath = "C:\laragon\bin\php\php-8.4.2-nts-Win32-vs17-x64\php.ini"

if (-not (Test-Path $phpIniPath)) {
    Write-Host "ERROR: php.ini tidak ditemukan di $phpIniPath" -ForegroundColor Red
    Write-Host "Cek lokasi php.ini Anda dengan command: php --ini" -ForegroundColor Yellow
    exit 1
}

Write-Host "File php.ini ditemukan: $phpIniPath" -ForegroundColor Green

# Backup php.ini
$timestamp = Get-Date -Format 'yyyyMMdd-HHmmss'
$backupPath = "$phpIniPath.backup-$timestamp"
Write-Host "Membuat backup: $backupPath" -ForegroundColor Yellow
Copy-Item $phpIniPath $backupPath

# Baca isi php.ini
$content = Get-Content $phpIniPath

# Flag untuk tracking perubahan
$changed = $false

# Uncomment extension pdo_pgsql
$content = $content -replace '^;extension=pdo_pgsql', 'extension=pdo_pgsql'
if ($content -match '^extension=pdo_pgsql') {
    Write-Host "pdo_pgsql extension diaktifkan" -ForegroundColor Green
    $changed = $true
}

# Uncomment extension pgsql
$content = $content -replace '^;extension=pgsql', 'extension=pgsql'
if ($content -match '^extension=pgsql') {
    Write-Host "pgsql extension diaktifkan" -ForegroundColor Green
    $changed = $true
}

if ($changed) {
    # Simpan perubahan
    $content | Set-Content $phpIniPath
    Write-Host ""
    Write-Host "php.ini berhasil diupdate!" -ForegroundColor Green
    Write-Host ""
    Write-Host "PENTING: Restart Laragon untuk menerapkan perubahan!" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Setelah restart, verifikasi dengan:" -ForegroundColor Cyan
    Write-Host "  php -m | Select-String pgsql" -ForegroundColor White
} else {
    Write-Host ""
    Write-Host "Extension mungkin sudah aktif atau tidak ditemukan." -ForegroundColor Yellow
    Write-Host "Silakan cek manual file php.ini" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Backup tersimpan di:" -ForegroundColor Cyan
Write-Host $backupPath -ForegroundColor White
Write-Host ""

