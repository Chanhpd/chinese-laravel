@echo off
REM Topic Image Downloader - Windows Batch Script
REM Chỉnh sửa các thông tin database bên dưới

echo ================================================================================
echo Topic Image Downloader
echo ================================================================================
echo.

REM Đọc thông tin từ .env file của Laravel
for /f "tokens=1,2 delims==" %%a in ('type ..\..\\.env ^| findstr /r "^DB_"') do (
    if "%%a"=="DB_HOST" set DB_HOST=%%b
    if "%%a"=="DB_PORT" set DB_PORT=%%b
    if "%%a"=="DB_DATABASE" set DB_DATABASE=%%b
    if "%%a"=="DB_USERNAME" set DB_USERNAME=%%b
    if "%%a"=="DB_PASSWORD" set DB_PASSWORD=%%b
)

REM Hiển thị thông tin kết nối (ẩn password)
echo Database Configuration:
echo   Host: %DB_HOST%
echo   Port: %DB_PORT%
echo   Database: %DB_DATABASE%
echo   Username: %DB_USERNAME%
echo   Password: ********
echo.

REM Kiểm tra Python
python --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Python is not installed or not in PATH
    echo Please install Python 3.7+ from https://www.python.org/
    pause
    exit /b 1
)

echo Checking Python dependencies...
python -c "import mysql.connector" >nul 2>&1
if errorlevel 1 (
    echo Installing mysql-connector-python...
    pip install mysql-connector-python
)

python -c "import requests" >nul 2>&1
if errorlevel 1 (
    echo Installing requests...
    pip install requests
)

echo.
echo Starting topic image downloader...
echo.

REM Chạy script Python
python topic_image_downloader.py ^
    --host %DB_HOST% ^
    --user %DB_USERNAME% ^
    --password %DB_PASSWORD% ^
    --database %DB_DATABASE% ^
    --port %DB_PORT% ^
    --output img_topic ^
    --threads 4 ^
    --progress topic_download_progress.json ^
    --save-interval 10

echo.
echo ================================================================================
echo Download completed!
echo.
echo Next steps:
echo 1. Check images in: img_topic\
echo 2. Copy images to Laravel public storage:
echo    mkdir public\storage\topics
echo    copy docs\TOPIC_CRAWL_IMAGE\img_topic\* public\storage\topics\
echo 3. Link storage if not already linked:
echo    php artisan storage:link
echo ================================================================================
echo.

pause
