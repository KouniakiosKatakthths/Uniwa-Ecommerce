# Colors
function info    { param($msg) Write-Host "[INFO] $msg" -ForegroundColor Green }
function warning { param($msg) Write-Host "[WARN] $msg" -ForegroundColor Yellow }
function error   { param($msg) Write-Host "[ERROR] $msg" -ForegroundColor Red; exit 1 }

# Helper: reload PATH so newly installed tools are found
function Refresh-Path {
    $env:Path = [System.Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path","User")
}

# Check OS
if ($env:OS -notmatch "Windows") {
    error "This script is for Windows only. Use setup.sh on Linux/Mac."
}

# Check if is running as admin
if (-not ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
    error "Please run this script as Administrator (right-click PowerShell > Run as Administrator)"
}

# Check if winget is avaliable
if (-not (Get-Command winget -ErrorAction SilentlyContinue)) {
    error "winget not found. Make sure you're on Windows 10 (updated) or Windows 11. Install it from the Microsoft Store: App Installer."
}
info "winget found: $(winget --version)"

# =============== INSTALL PHP =============== 
if (Get-Command php -ErrorAction SilentlyContinue) {
    warning "PHP already installed: $(php -v | Select-Object -First 1)"
} else {
    info "Installing PHP..."
    winget install PHP.PHP --silent --accept-package-agreements --accept-source-agreements
    Refresh-Path
    info "PHP installed: $(php -v | Select-Object -First 1)"
}
 
# =============== INSTALL COMPOSER =============== 
if (Get-Command composer -ErrorAction SilentlyContinue) {
    warning "Composer already installed: $(composer --version)"
} else {
    info "Installing Composer..."
    winget install Composer.Composer --silent --accept-package-agreements --accept-source-agreements
    Refresh-Path
    info "Composer installed: $(composer --version)"
}

# =============== INSTALL NODEJS AND NPM ===============  
if (Get-Command node -ErrorAction SilentlyContinue) {
    warning "Node.js already installed: $(node -v)"
} else {
    info "Installing Node.js..."
    winget install OpenJS.NodeJS.LTS --silent --accept-package-agreements --accept-source-agreements
    Refresh-Path
    info "Node.js installed: $(node -v), npm: $(npm -v)"
}


# =============== INSTALL MYSQL =============== 
if (Get-Command mysql -ErrorAction SilentlyContinue) {
    warning "MySQL already installed."
} else {
    info "Installing MySQL..."
    winget install Oracle.MySQL --silent --accept-package-agreements --accept-source-agreements
    Refresh-Path
    # Give MySQL service time to start
    Start-Sleep -Seconds 5
    info "MySQL installed."
}

# =============== INSTALL PROJECT DEPENDENCIES =============== 
if (Test-Path "composer.json") {
    info "Installing Laravel dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
} else {
    warning "No composer.json found. Skipping."
}

# =============== SETUP ENV FILE =============== 
if ((Test-Path ".env.example") -and (-not (Test-Path ".env"))) {
    info "Copying .env.example to .env..."
    Copy-Item ".env.example" ".env"
} elseif (Test-Path ".env") {
    warning ".env already exists, skipping."
}

# =============== CREATE MYSQL DATABASE AND USER =============== 
$RUN_MIGRATE = Read-Host "Run database setup? [y/N]"
if ($RUN_MIGRATE -match "^[Yy]$") {
    $MYSQL_ROOT_PASS = Read-Host "Enter MySQL root password [leave empty if none]"
    $DB_NAME = Read-Host "Enter database name [default: uniwa_ecommerce]"
    $DB_USER = Read-Host "Enter new DB username [default: uniwa_user]"
    $DB_PASS = Read-Host "Enter new DB user password"
    
    if (-not $DB_NAME) { $DB_NAME = "uniwa_ecommerce" }
    if (-not $DB_USER) { $DB_USER = "uniwa_user" }
    if (-not $DB_PASS) { error "DB user password cannot be empty." }

    $SQL = @"
CREATE DATABASE IF NOT EXISTS ``$DB_NAME``;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON ``$DB_NAME``.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
"@

    info "Creating database '$DB_NAME' and user '$DB_USER'..."
    try {
        if (-not $MYSQL_ROOT_PASS) {
            $SQL | mysql -u root
        } else {
            $SQL | mysql -u root -p"$MYSQL_ROOT_PASS"
        }
        info "Database and user created successfully."
    } catch {
        error "Failed to create database. Check your MySQL root password."
    }

    # Update .env with DB credentials
    (Get-Content .env) `
        -replace 'DB_DATABASE=.*', "DB_DATABASE=$DB_NAME" `
        -replace 'DB_USERNAME=.*', "DB_USERNAME=$DB_USER" `
        -replace 'DB_PASSWORD=.*', "DB_PASSWORD=$DB_PASS" |
        Set-Content .env
    
    info "Updated .env with dedicated user credentials."
}

# =============== INSTALL JS DEPENDENCIES =============== 
if (Test-Path "package.json") {
    info "Installing JS dependencies..."
    npm install
}

# =============== GENERATE APPLICATION KEY =============== 
info "Generating Laravel app key..."
php artisan key:generate

# =============== RUN MIGRATIONS =============== 
if (Test-Path "artisan") {
    $RUN_MIGRATE = Read-Host "Run database migrations? [y/N]"
    if ($RUN_MIGRATE -match "^[Yy]$") {
        info "Running migrations..."
        php artisan migrate --force
    }
}
 
Write-Host ""
Write-Host " Setup complete!" -ForegroundColor Green
Write-Host " Start the dev server with: " -NoNewline
Write-Host "php artisan serve" -ForegroundColor Yellow
