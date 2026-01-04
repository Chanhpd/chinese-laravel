# Topic Image Downloader - PowerShell Script
# Automatically reads database config from Laravel .env file

Write-Host "================================================================================" -ForegroundColor Cyan
Write-Host "Topic Image Downloader" -ForegroundColor Cyan
Write-Host "================================================================================" -ForegroundColor Cyan
Write-Host ""

# Get script directory and Laravel root
$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$LaravelRoot = Join-Path $ScriptDir "..\..\"
$EnvFile = Join-Path $LaravelRoot ".env"

# Check if .env exists
if (-not (Test-Path $EnvFile)) {
    Write-Host "ERROR: .env file not found at $EnvFile" -ForegroundColor Red
    Write-Host "Please make sure you're running this from the correct directory" -ForegroundColor Red
    exit 1
}

# Read .env file
Write-Host "Reading database configuration from .env file..." -ForegroundColor Yellow
$EnvContent = Get-Content $EnvFile
$DbConfig = @{}

foreach ($line in $EnvContent) {
    if ($line -match '^DB_HOST=(.+)$') { $DbConfig['host'] = $matches[1] }
    if ($line -match '^DB_PORT=(.+)$') { $DbConfig['port'] = $matches[1] }
    if ($line -match '^DB_DATABASE=(.+)$') { $DbConfig['database'] = $matches[1] }
    if ($line -match '^DB_USERNAME=(.+)$') { $DbConfig['username'] = $matches[1] }
    if ($line -match '^DB_PASSWORD=(.+)$') { $DbConfig['password'] = $matches[1] }
}

# Display configuration (hide password)
Write-Host ""
Write-Host "Database Configuration:" -ForegroundColor Green
Write-Host "  Host:     $($DbConfig['host'])" -ForegroundColor White
Write-Host "  Port:     $($DbConfig['port'])" -ForegroundColor White
Write-Host "  Database: $($DbConfig['database'])" -ForegroundColor White
Write-Host "  Username: $($DbConfig['username'])" -ForegroundColor White
Write-Host "  Password: ********" -ForegroundColor White
Write-Host ""

# Check Python
Write-Host "Checking Python installation..." -ForegroundColor Yellow
try {
    $pythonVersion = python --version 2>&1
    Write-Host "  $pythonVersion" -ForegroundColor Green
} catch {
    Write-Host "ERROR: Python is not installed or not in PATH" -ForegroundColor Red
    Write-Host "Please install Python 3.7+ from https://www.python.org/" -ForegroundColor Red
    exit 1
}

# Check and install dependencies
Write-Host ""
Write-Host "Checking Python dependencies..." -ForegroundColor Yellow

$dependencies = @('mysql-connector-python', 'requests')
foreach ($dep in $dependencies) {
    $checkImport = $dep -replace '-', '.'
    if ($dep -eq 'mysql-connector-python') { $checkImport = 'mysql.connector' }
    
    $installed = python -c "import $checkImport" 2>&1
    if ($LASTEXITCODE -ne 0) {
        Write-Host "  Installing $dep..." -ForegroundColor Yellow
        pip install $dep
    } else {
        Write-Host "  $dep is already installed" -ForegroundColor Green
    }
}

# Run the downloader
Write-Host ""
Write-Host "================================================================================" -ForegroundColor Cyan
Write-Host "Starting topic image downloader..." -ForegroundColor Cyan
Write-Host "================================================================================" -ForegroundColor Cyan
Write-Host ""

$outputFolder = "img_topic"
$progressFile = "topic_download_progress.json"
$threads = 4
$saveInterval = 10

# Build command arguments
$pythonArgs = @(
    "topic_image_downloader.py",
    "--host", $DbConfig['host'],
    "--user", $DbConfig['username'],
    "--password", $DbConfig['password'],
    "--database", $DbConfig['database'],
    "--port", $DbConfig['port'],
    "--output", $outputFolder,
    "--threads", $threads,
    "--progress", $progressFile,
    "--save-interval", $saveInterval
)

# Run Python script
& python $pythonArgs

# Check result
if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "================================================================================" -ForegroundColor Green
    Write-Host "Download completed successfully!" -ForegroundColor Green
    Write-Host "================================================================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "Next steps:" -ForegroundColor Yellow
    Write-Host "1. Check images in: $outputFolder\" -ForegroundColor White
    Write-Host "2. Copy images to Laravel public storage:" -ForegroundColor White
    Write-Host "   mkdir public\storage\topics -Force" -ForegroundColor Cyan
    Write-Host "   copy $ScriptDir\$outputFolder\* public\storage\topics\" -ForegroundColor Cyan
    Write-Host "3. Link storage if not already linked:" -ForegroundColor White
    Write-Host "   php artisan storage:link" -ForegroundColor Cyan
    Write-Host ""
    
    # Ask if user wants to copy files now
    $response = Read-Host "Do you want to copy images to public/storage/topics now? (y/n)"
    if ($response -eq 'y' -or $response -eq 'Y') {
        $publicTopicsDir = Join-Path $LaravelRoot "public\storage\topics"
        New-Item -ItemType Directory -Force -Path $publicTopicsDir | Out-Null
        
        $imageFiles = Join-Path $ScriptDir "$outputFolder\*"
        Copy-Item $imageFiles $publicTopicsDir -Force
        
        Write-Host "Images copied successfully!" -ForegroundColor Green
        
        # Check if storage is linked
        $storageLink = Join-Path $LaravelRoot "public\storage"
        if (-not (Test-Path $storageLink)) {
            Write-Host ""
            Write-Host "Storage is not linked. Linking now..." -ForegroundColor Yellow
            Push-Location $LaravelRoot
            php artisan storage:link
            Pop-Location
        }
    }
} else {
    Write-Host ""
    Write-Host "================================================================================" -ForegroundColor Red
    Write-Host "Download failed or was interrupted" -ForegroundColor Red
    Write-Host "================================================================================" -ForegroundColor Red
    Write-Host ""
    Write-Host "Progress has been saved. You can run the script again to resume." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
