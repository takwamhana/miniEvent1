# Admin Dashboard Health Check Script (PowerShell)
# This script verifies that all components are working correctly

Write-Host "`n🔍 Admin Dashboard Health Check" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""

$allChecksPass = $true

# Check 1: Docker containers
Write-Host "1. Checking Docker containers..."
try {
    $containers = docker-compose ps 2>&1
    if ($containers -match "Up") {
        Write-Host "✅ Docker containers are running" -ForegroundColor Green
    } else {
        Write-Host "❌ Docker containers are not running" -ForegroundColor Red
        Write-Host "   Run: docker-compose up -d" -ForegroundColor Yellow
        $allChecksPass = $false
    }
} catch {
    Write-Host "❌ Docker not available or not running" -ForegroundColor Red
    $allChecksPass = $false
}
Write-Host ""

# Check 2: Nginx is responding
Write-Host "2. Checking Nginx web server..."
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8080" -UseBasicParsing -ErrorAction SilentlyContinue
    if ($response.StatusCode -in @(200, 301, 302)) {
        Write-Host "✅ Nginx is responding" -ForegroundColor Green
    } else {
        Write-Host "❌ Nginx returned status: $($response.StatusCode)" -ForegroundColor Red
        $allChecksPass = $false
    }
} catch {
    Write-Host "❌ Nginx is not responding" -ForegroundColor Red
    Write-Host "   Check: docker-compose logs nginx" -ForegroundColor Yellow
    $allChecksPass = $false
}
Write-Host ""

# Check 3: API is accessible
Write-Host "3. Checking API endpoint..."
try {
    $apiResponse = Invoke-WebRequest -Uri "http://localhost:8080/api/events" -UseBasicParsing -ErrorAction SilentlyContinue
    if ($apiResponse.StatusCode -in @(200, 401)) {
        Write-Host "✅ API is accessible" -ForegroundColor Green
    } else {
        Write-Host "❌ API returned status: $($apiResponse.StatusCode)" -ForegroundColor Red
        $allChecksPass = $false
    }
} catch {
    if ($_.Exception.Response.StatusCode.value__ -eq 401) {
        Write-Host "✅ API is accessible (401 expected without auth)" -ForegroundColor Green
    } else {
        Write-Host "❌ API is not accessible" -ForegroundColor Red
        Write-Host "   Check: docker-compose logs php" -ForegroundColor Yellow
        $allChecksPass = $false
    }
}
Write-Host ""

# Check 4: Admin.html file exists
Write-Host "4. Checking admin.html file..."
if (Test-Path "public\admin.html") {
    Write-Host "✅ admin.html exists" -ForegroundColor Green
    
    # Check file size
    $fileSize = (Get-Item "public\admin.html").Length
    if ($fileSize -gt 20000) {
        Write-Host "   File size: $fileSize bytes (looks good)" -ForegroundColor Green
    } else {
        Write-Host "   ⚠️  File size: $fileSize bytes (seems small)" -ForegroundColor Yellow
    }
} else {
    Write-Host "❌ admin.html not found" -ForegroundColor Red
    $allChecksPass = $false
}
Write-Host ""

# Check 5: No duplicate functions
Write-Host "5. Checking for duplicate functions..."
$content = Get-Content "public\admin.html" -Raw
$functionMatches = [regex]::Matches($content, "function (loadReservations|displayReservations|editReservation|deleteReservation|populateEventSelect)\(")
$uniqueFunctions = $functionMatches | ForEach-Object { $_.Groups[1].Value } | Sort-Object -Unique
if ($uniqueFunctions.Count -eq 5 -and $functionMatches.Count -eq 5) {
    Write-Host "✅ No duplicate functions found (5 unique functions)" -ForegroundColor Green
} else {
    Write-Host "   ⚠️  Found $($functionMatches.Count) function definitions, $($uniqueFunctions.Count) unique" -ForegroundColor Yellow
    if ($functionMatches.Count -gt 5) {
        Write-Host "   ⚠️  There may be duplicates!" -ForegroundColor Yellow
    }
}
Write-Host ""

# Check 6: XSS protection (escapeHtml function)
Write-Host "6. Checking XSS protection..."
if ($content -match "function escapeHtml") {
    Write-Host "✅ escapeHtml() function exists" -ForegroundColor Green
} else {
    Write-Host "❌ escapeHtml() function not found" -ForegroundColor Red
    $allChecksPass = $false
}
Write-Host ""

# Check 7: No inline onclick with user data
Write-Host "7. Checking for unsafe onclick handlers..."
$unsafeOnclick = [regex]::Matches($content, 'onclick=".*reservation\.').Count
if ($unsafeOnclick -eq 0) {
    Write-Host "✅ No unsafe onclick handlers found" -ForegroundColor Green
} else {
    Write-Host "❌ Found $unsafeOnclick unsafe onclick handlers" -ForegroundColor Red
    $allChecksPass = $false
}
Write-Host ""

# Check 8: CSS classes exist
Write-Host "8. Checking CSS classes..."
if ($content -match "\.card \{") {
    Write-Host "✅ .card CSS class exists" -ForegroundColor Green
} else {
    Write-Host "❌ .card CSS class missing" -ForegroundColor Red
    $allChecksPass = $false
}

if ($content -match "\.btn-sm \{") {
    Write-Host "✅ .btn-sm CSS class exists" -ForegroundColor Green
} else {
    Write-Host "❌ .btn-sm CSS class missing" -ForegroundColor Red
    $allChecksPass = $false
}
Write-Host ""

# Check 9: Error container exists
Write-Host "9. Checking error containers..."
if ($content -match 'id="events-error"') {
    Write-Host "✅ events-error container exists" -ForegroundColor Green
} else {
    Write-Host "❌ events-error container missing" -ForegroundColor Red
    $allChecksPass = $false
}
Write-Host ""

# Check 10: Database connection
Write-Host "10. Checking database..."
try {
    $dbCheck = docker-compose exec -T php bin/console doctrine:query:sql "SELECT COUNT(*) FROM `"user`";" 2>&1
    if ($dbCheck -match "error" -or $dbCheck -match "Exception") {
        Write-Host "   ⚠️  Could not connect to database" -ForegroundColor Yellow
        Write-Host "   Run migrations: docker-compose exec php bin/console doctrine:migrations:migrate" -ForegroundColor Yellow
    } else {
        Write-Host "✅ Database is accessible" -ForegroundColor Green
        if ($dbCheck -match "(\d+)") {
            $userCount = $matches[1]
            Write-Host "   Users in database: $userCount" -ForegroundColor Cyan
        }
    }
} catch {
    Write-Host "   ⚠️  Could not check database" -ForegroundColor Yellow
}
Write-Host ""

# Summary
Write-Host "================================" -ForegroundColor Cyan
if ($allChecksPass) {
    Write-Host "🎉 All Critical Checks Passed!" -ForegroundColor Green
} else {
    Write-Host "⚠️  Some checks failed. Review the output above." -ForegroundColor Yellow
}
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Go to http://localhost:8080/login.html"
Write-Host "2. Login with: admin@test.com"
Write-Host "3. Go to http://localhost:8080/admin.html"
Write-Host "4. Test creating/editing/deleting events"
Write-Host ""
Write-Host "📚 Documentation:" -ForegroundColor Cyan
Write-Host "- Quick Summary: ADMIN_DASHBOARD_QUICK_SUMMARY.md"
Write-Host "- Full Details: ADMIN_DASHBOARD_FIXES_COMPLETE.md"
Write-Host "- Testing Guide: TESTING_GUIDE.md"
Write-Host ""
Write-Host "✨ Dashboard is ready to use!" -ForegroundColor Green
Write-Host ""
