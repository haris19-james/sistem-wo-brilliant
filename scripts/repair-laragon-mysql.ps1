# Repair Laragon MySQL 8.4.3 — missing lib/ folder causes "connection refused" on port 3306
# Run in PowerShell:  .\scripts\repair-laragon-mysql.ps1

$ErrorActionPreference = 'Stop'

$mysqlRoot = 'C:\laragon\bin\mysql\mysql-8.4.3-winx64'
$libPath   = Join-Path $mysqlRoot 'lib'
$pluginDll = Join-Path $libPath 'plugin\component_reference_cache.dll'

Write-Host '=== Brilliant WO — Laragon MySQL repair ===' -ForegroundColor Cyan
Write-Host "Target: $mysqlRoot`n"

if (Test-Path $pluginDll) {
    Write-Host 'lib\plugin sudah ada. Coba start MySQL dari Laragon (Start All).' -ForegroundColor Green
    exit 0
}

Write-Host 'MASALAH: folder lib\ hilang dari instalasi MySQL Laragon.' -ForegroundColor Yellow
Write-Host 'Akibatnya mysqld crash / port 3306 tidak aktif -> Laravel error 2002.`n'

$zipUrl  = 'https://cdn.mysql.com/Downloads/MySQL-8.4/mysql-8.4.3-winx64.zip'
$zipFile = Join-Path $env:TEMP 'mysql-8.4.3-winx64.zip'
$extract = Join-Path $env:TEMP 'mysql-8.4.3-extract'

Write-Host 'Langkah perbaikan:' -ForegroundColor White
Write-Host "1. Unduh: $zipUrl"
Write-Host "2. Simpan sebagai: $zipFile"
Write-Host '3. Jalankan ulang script ini (akan extract & copy lib\ otomatis)'
Write-Host '   ATAU extract manual, lalu copy folder lib\ ke:'
Write-Host "   $mysqlRoot`n"

if (-not (Test-Path $zipFile)) {
    $answer = Read-Host 'Unduh sekarang dari internet? (y/n)'
    if ($answer -eq 'y') {
        Write-Host 'Mengunduh... (±300 MB, tunggu beberapa menit)'
        Invoke-WebRequest -Uri $zipUrl -OutFile $zipFile -UseBasicParsing
    } else {
        exit 1
    }
}

if (-not (Test-Path (Join-Path $extract 'mysql-8.4.3-winx64\lib'))) {
    Write-Host 'Extract archive...'
    if (Test-Path $extract) { Remove-Item $extract -Recurse -Force }
    Expand-Archive -Path $zipFile -DestinationPath $extract -Force
}

$sourceLib = Join-Path $extract 'mysql-8.4.3-winx64\lib'
if (-not (Test-Path $sourceLib)) {
    Write-Error "Folder lib tidak ditemukan di archive. Periksa isi $extract"
}

Write-Host "Menyalin lib\ ke $mysqlRoot ..."
Copy-Item -Path $sourceLib -Destination $libPath -Recurse -Force

if (Test-Path $pluginDll) {
    Write-Host '`nBerhasil! Sekarang:' -ForegroundColor Green
    Write-Host '1. Buka Laragon -> Stop All -> Start All'
    Write-Host '2. Pastikan MySQL hijau (running)'
    Write-Host '3. Di folder project: php artisan migrate'
} else {
    Write-Error 'Copy gagal — plugin DLL masih tidak ditemukan.'
}
