# GarageOS Development Startup Script
# This script must be run as Administrator to update the Windows hosts file.

Write-Host "Starting GarageOS Development Environment..." -ForegroundColor Cyan

# 1. Copy Nginx Configuration to WSL
Write-Host "Configuring Nginx in WSL..." -ForegroundColor Yellow
wsl -d Ubuntu -u root cp /mnt/c/laragon/www/app_garage/nginx-app-garage.test.conf /etc/nginx/sites-available/app-garage.test

# 2. Enable the site in Nginx
Write-Host "Enabling Nginx site..." -ForegroundColor Yellow
wsl -d Ubuntu -u root ln -sf /etc/nginx/sites-available/app-garage.test /etc/nginx/sites-enabled/app-garage.test

# 3. Update Windows Hosts file
Write-Host "Updating Windows hosts file..." -ForegroundColor Yellow
$hostsFile = "C:\Windows\System32\drivers\etc\hosts"
$hostEntry = "127.0.0.1 app-garage.test"
$hostsContent = Get-Content $hostsFile
if ($hostsContent -notcontains $hostEntry) {
    Add-Content -Path $hostsFile -Value "`n127.0.0.1 app-garage.test"
    Write-Host "Added app-garage.test to hosts file." -ForegroundColor Green
} else {
    Write-Host "app-garage.test already exists in hosts file." -ForegroundColor Gray
}

# 4. Start Services in WSL
Write-Host "Starting services in WSL (Nginx and PHP-FPM)..." -ForegroundColor Yellow
wsl -d Ubuntu -u root service nginx restart
wsl -d Ubuntu -u root service php8.5-fpm restart

Write-Host ""
Write-Host "SUCCESS! Your environment is ready." -ForegroundColor Green
Write-Host "Access it at: http://app-garage.test" -ForegroundColor Cyan
Write-Host "To stop, run: wsl -d Ubuntu -u root service nginx stop && wsl -d Ubuntu -u root service php8.5-fpm stop" -ForegroundColor Gray
