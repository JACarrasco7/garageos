# GarageOS - Setup PostgreSQL nativo
$ErrorActionPreference = "Stop"

$pgPaths = @(
    "C:\laragon\bin\postgresql\16",
    "C:\Program Files\PostgreSQL\16",
    "C:\Program Files\PostgreSQL\15"
)

$pgPath = $null
foreach ($path in $pgPaths) {
    if (Test-Path "$path\bin\psql.exe") {
        $pgPath = $path
        break
    }
}

if (-not $pgPath) {
    Write-Host "[ERROR] PostgreSQL no encontrado" -ForegroundColor Red
    exit 1
}

$pgBin = "$pgPath\bin"
Write-Host "[INFO] PostgreSQL detectado en: $pgPath" -ForegroundColor Cyan

$currentPath = [Environment]::GetEnvironmentVariable("Path", "User")
if ($currentPath -notlike "*$pgBin*") {
    [Environment]::SetEnvironmentVariable("Path", "$currentPath;$pgBin", "User")
    Write-Host "[OK] Anadido al PATH" -ForegroundColor Green
}

$env:Path = "$env:Path;$pgBin"
$env:PGPASSWORD = "admin"

$checkDb = cmd /c "psql -U postgres -h localhost -tAc ""SELECT 1 FROM pg_database WHERE datname='garageos';"" 2>nul"
if ($checkDb -match "1") {
    Write-Host "[INFO] DB ya existe" -ForegroundColor Yellow
} else {
    cmd /c "psql -U postgres -h localhost -c ""CREATE DATABASE garageos;""" 2>$null
    Write-Host "[OK] DB creada" -ForegroundColor Green
}

$checkUser = cmd /c "psql -U postgres -h localhost -tAc ""SELECT 1 FROM pg_roles WHERE rolname='garageos';"" 2>nul"
if ($checkUser -match "1") {
    Write-Host "[INFO] Usuario ya existe" -ForegroundColor Yellow
} else {
    cmd /c "psql -U postgres -h localhost -c ""CREATE USER garageos WITH PASSWORD 'garageos';""" 2>$null
    cmd /c "psql -U postgres -h localhost -c ""GRANT ALL PRIVILEGES ON DATABASE garageos TO garageos;""" 2>$null
    cmd /c "psql -U postgres -h localhost -c ""ALTER USER garageos CREATEDB;"" 2>$null"
    Write-Host "[OK] Usuario creado" -ForegroundColor Green
}

$svc = Get-Service -Name "postgresql*" -ErrorAction SilentlyContinue | Select-Object -First 1
if ($svc) {
    Set-Service $svc.Name -StartupType Automatic
    Write-Host "[OK] Servicio auto-arranque" -ForegroundColor Green
}

Write-Host ""
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "PostgreSQL nativo listo!" -ForegroundColor Green
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Siguiente:" -ForegroundColor Yellow
Write-Host "  1. .env: DB_HOST=127.0.0.1 DB_PASSWORD=admin" -ForegroundColor White
Write-Host "  2. php artisan migrate" -ForegroundColor Gray
Write-Host "  3. docker-compose down" -ForegroundColor Gray
