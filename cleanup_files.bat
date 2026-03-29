@echo off
echo Cleaning up unnecessary files from Eventize project...
echo.

cd /d "%~dp0"

echo Removing backup/old HTML files from public/...
del /F /Q "public\admin-clean.html" 2>nul
del /F /Q "public\admin-debug.html" 2>nul
del /F /Q "public\admin-modern.html" 2>nul
del /F /Q "public\admin-simple.html" 2>nul
del /F /Q "public\event-modern.html" 2>nul
del /F /Q "public\index-modern.html" 2>nul
del /F /Q "public\login-modern.html" 2>nul
del /F /Q "public\login-simple.html" 2>nul
del /F /Q "public\home-new.html" 2>nul
del /F /Q "public\home-backup.html" 2>nul

echo Removing test files from root...
del /F /Q "test_event.json" 2>nul
del /F /Q "test_mail.php" 2>nul
del /F /Q "test_reservation.json" 2>nul
del /F /Q "test_reservation_real.json" 2>nul

echo Removing utility scripts...
del /F /Q "check_dashboard.ps1" 2>nul
del /F /Q "check_dashboard.sh" 2>nul
del /F /Q "check_user.php" 2>nul
del /F /Q "create_admin_simple.php" 2>nul
del /F /Q "generate_token.php" 2>nul

echo Removing old documentation...
del /F /Q "HOW_TO_RUN.md" 2>nul
del /F /Q "COMPLETE_SOLUTION.md" 2>nul
del /F /Q "CHANGELOG.md" 2>nul

echo Removing supervisor logs...
del /F /Q "supervisord.log" 2>nul
del /F /Q "supervisord.pid" 2>nul

echo.
echo ✓ Cleanup complete!
echo.
echo Remaining essential files:
echo   - public/home.html
echo   - public/index.html
echo   - public/event.html
echo   - public/login.html
echo   - public/admin.html
echo   - README.md
echo   - Docker configuration files
echo   - Application source code
echo.
pause


