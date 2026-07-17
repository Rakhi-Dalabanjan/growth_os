@echo off
title GrowthOS Dev Server
echo ============================================
echo   GrowthOS - AI Social Media OS
echo   Starting development server...
echo ============================================
echo.

set PHP=C:\xampp\php\php.exe
set PROJECT=D:\office_files\projects\growth_os\growthos

echo [1/1] Starting PHP server at http://localhost:8000
echo      Press Ctrl+C to stop
echo.
%PHP% "%PROJECT%\artisan" serve --host=127.0.0.1 --port=8000
