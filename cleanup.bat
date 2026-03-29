@echo off
setlocal enabledelayedexpansion

cd /d C:\Users\mhana\miniEvent1

echo === Deleting files from public/ directory ===

set "publicFiles=admin-clean.html admin-debug.html admin-modern.html admin-simple.html event-modern.html index-modern.html login-modern.html login-simple.html home-new.html home-backup.html"

for %%F in (%publicFiles%) do (
    if exist "public\%%F" (
        del /F /Q "public\%%F"
        echo ✓ Deleted: public\%%F
    )
)

echo.
echo === Deleting files from root directory ===

set "rootFiles=test_event.json test_mail.php test_reservation.json test_reservation_real.json check_dashboard.ps1 check_dashboard.sh check_user.php create_admin_simple.php generate_token.php HOW_TO_RUN.md COMPLETE_SOLUTION.md CHANGELOG.md supervisord.log supervisord.pid"

for %%F in (%rootFiles%) do (
    if exist "%%F" (
        del /F /Q "%%F"
        echo ✓ Deleted: %%F
    )
)

echo.
echo === Cleanup complete ===
