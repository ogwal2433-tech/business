@echo off
cd /d "%~dp0"
echo Starting SmartBiz Desktop...
echo Make sure your Laravel dev server is running: php artisan serve
echo.
node_modules\.bin\electron.cmd .
