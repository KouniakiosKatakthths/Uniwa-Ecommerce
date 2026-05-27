function info    { param($msg) Write-Host "[INFO] $msg" -ForegroundColor Green }
function warning { param($msg) Write-Host "[WARN] $msg" -ForegroundColor Yellow }
function err     { param($msg) Write-Host "[ERROR] $msg" -ForegroundColor Red; exit 1 }

# Check if is running as admin
if (-not ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
    err "Please run this script as Administrator (right-click PowerShell > Run as Administrator)"
    exit -1;
}

# Check if WSL is already installed
$wslInstalled = Get-Command wsl -ErrorAction SilentlyContinue
$wslDistros = if ($wslInstalled) { wsl --list --quiet 2>$null | Where-Object { $_ -match '\S' } } else { $null }
 
if (-not $wslInstalled -or -not $wslDistros) {
    if ($wslInstalled) {
        info "WSL is installed but no Linux distribution found. Installing Ubuntu..."
        wsl --install -d Ubuntu
    } else {
        info "Installing WSL with Ubuntu..."
        wsl --install
    }
 
    Write-Host ""
    Write-Host "======================================" -ForegroundColor Yellow
    Write-Host "  Restart required to finish WSL setup!" -ForegroundColor Yellow
    Write-Host "  After restart, open Ubuntu from the" -ForegroundColor Yellow
    Write-Host "  Start menu, set your username/password," -ForegroundColor Yellow
    Write-Host "  then run this script again." -ForegroundColor Yellow
    Write-Host "======================================" -ForegroundColor Yellow
    Write-Host ""
 
    $restart = Read-Host "Restart now? [y/N]"
    if ($restart -match "^[Yy]$") {
        Restart-Computer -Force
    }
    exit
}

warning "WSL already installed with distro(s): $($wslDistros -join ', ')"

# Check Ubuntu is set up (has a user)
$wslUser = wsl whoami 2>$null
if (-not $wslUser) {
    Write-Host ""
    Write-Host "======================================" -ForegroundColor Yellow
    Write-Host "  Ubuntu isn't set up yet!" -ForegroundColor Yellow
    Write-Host "  Open Ubuntu from the Start menu first" -ForegroundColor Yellow
    Write-Host "  and create your Linux username/password." -ForegroundColor Yellow
    Write-Host "  Then run this script again." -ForegroundColor Yellow
    Write-Host "======================================" -ForegroundColor Yellow
    Write-Host ""
    exit
}
 
info "WSL ready, logged in as: $wslUser"

# Find setup.sh and run it inside WSL
$scriptDir = Split-Path -Parent $PSCommandPath
$wslPath = ($scriptDir -replace '\\', '/') -replace '^([A-Za-z]):', '/mnt/$1'.ToLower()
$wslPath = $wslPath -replace '^/mnt/([A-Za-z])', { "/mnt/$($_.Groups[1].Value.ToLower())" }
 
# Convert Windows path to WSL path properly
$driveLetter = $scriptDir.Substring(0,1).ToLower()
$rest = $scriptDir.Substring(2) -replace '\\', '/'
$wslScriptDir = "/mnt/$driveLetter$rest"
 
info "Project path in WSL: $wslScriptDir"
 
if (-not (Test-Path "$scriptDir\setup.sh")) {
    err "setup.sh not found in $scriptDir"
}
 
info "Running setup.sh inside WSL..."
wsl bash -c "cd '$wslScriptDir' && chmod +x setup.sh && ./setup.sh"
 
if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host " Done! To start your app run:" -ForegroundColor Green
    Write-Host " wsl bash -c `"cd '$wslScriptDir' && php artisan serve`"" -ForegroundColor Yellow
} else {
    Write-Host ""
    err "setup.sh failed. Check the errors above."
}
