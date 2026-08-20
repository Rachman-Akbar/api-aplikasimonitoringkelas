# ================================================================
# LARAVEL SERVER STARTER - FIXED FOR ANDROID CONNECTION
# ================================================================

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Laravel Server Starter for Android" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Get WiFi IP Address
Write-Host "[1/6] Detecting WiFi IP Address..." -ForegroundColor Yellow
$ipAddress = (Get-NetIPAddress -AddressFamily IPv4 | Where-Object {
    $_.InterfaceAlias -like "*Wi-Fi*" -or $_.InterfaceAlias -like "*Wireless*"
} | Select-Object -First 1).IPAddress

if (-not $ipAddress) {
    # Fallback to any IPv4 address that's not loopback
    $ipAddress = (Get-NetIPAddress -AddressFamily IPv4 | Where-Object {
        $_.IPAddress -ne "127.0.0.1" -and $_.PrefixOrigin -eq "Dhcp"
    } | Select-Object -First 1).IPAddress
}

if (-not $ipAddress) {
    Write-Host "ERROR: Could not detect WiFi IP address!" -ForegroundColor Red
    Write-Host "Please check your WiFi connection." -ForegroundColor Red
    pause
    exit 1
}

Write-Host "   WiFi IP: $ipAddress" -ForegroundColor Green
Write-Host ""

# Check if running as Administrator
Write-Host "[2/6] Checking Administrator privileges..." -ForegroundColor Yellow
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if ($isAdmin) {
    Write-Host "   Running as Administrator" -ForegroundColor Green
    
    # Add Firewall Rule
    Write-Host "[3/6] Configuring Windows Firewall..." -ForegroundColor Yellow
    
    # Remove existing rule if exists
    Remove-NetFirewallRule -DisplayName "Laravel Server Port 8000" -ErrorAction SilentlyContinue
    
    # Add new rule
    New-NetFirewallRule -DisplayName "Laravel Server Port 8000" `
        -Direction Inbound `
        -Action Allow `
        -Protocol TCP `
        -LocalPort 8000 `
        -Profile Any `
        -ErrorAction SilentlyContinue | Out-Null
    
    Write-Host "   Firewall configured successfully" -ForegroundColor Green
} else {
    Write-Host "   NOT running as Administrator (Firewall may block connections)" -ForegroundColor Yellow
    Write-Host "   Tip: Right-click and 'Run as Administrator' for auto firewall setup" -ForegroundColor Yellow
}
Write-Host ""

# Clear Laravel cache
Write-Host "[4/6] Clearing Laravel cache..." -ForegroundColor Yellow
php artisan config:clear | Out-Null
php artisan cache:clear | Out-Null
php artisan route:clear | Out-Null
Write-Host "   Cache cleared" -ForegroundColor Green
Write-Host ""

# Update .env file
Write-Host "[5/6] Updating .env configuration..." -ForegroundColor Yellow
$envPath = ".env"
if (Test-Path $envPath) {
    $envContent = Get-Content $envPath -Raw
    $envContent = $envContent -replace "APP_URL=http://.*", "APP_URL=http://${ipAddress}:8000"
    Set-Content $envPath $envContent
    Write-Host "   APP_URL set to: http://${ipAddress}:8000" -ForegroundColor Green
} else {
    Write-Host "   WARNING: .env file not found!" -ForegroundColor Red
}
Write-Host ""

# Start Laravel Server
Write-Host "[6/6] Starting Laravel Server..." -ForegroundColor Yellow
Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "  SERVER INFORMATION" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host "  URL: http://${ipAddress}:8000" -ForegroundColor White
Write-Host "  API: http://${ipAddress}:8000/api/" -ForegroundColor White
Write-Host "  Health: http://${ipAddress}:8000/api/health" -ForegroundColor White
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "IMPORTANT: Update Android App Configuration:" -ForegroundColor Cyan
Write-Host "  File: ApiConfig.kt" -ForegroundColor Yellow
Write-Host "  Line: DEFAULT_BASE_URL = `"http://${ipAddress}:8000/api/`"" -ForegroundColor Yellow
Write-Host ""
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Red
Write-Host ""

# Start the server
php artisan serve --host=$ipAddress --port=8000
