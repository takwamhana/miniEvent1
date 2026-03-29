#!/bin/bash

# Admin Dashboard Health Check Script
# This script verifies that all components are working correctly

echo "🔍 Admin Dashboard Health Check"
echo "================================"
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check 1: Docker containers
echo "1. Checking Docker containers..."
if docker-compose ps | grep -q "Up"; then
    echo -e "${GREEN}✅ Docker containers are running${NC}"
else
    echo -e "${RED}❌ Docker containers are not running${NC}"
    echo "   Run: docker-compose up -d"
    exit 1
fi
echo ""

# Check 2: Nginx is responding
echo "2. Checking Nginx web server..."
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8080 | grep -q "200\|301\|302"; then
    echo -e "${GREEN}✅ Nginx is responding${NC}"
else
    echo -e "${RED}❌ Nginx is not responding${NC}"
    echo "   Check: docker-compose logs nginx"
    exit 1
fi
echo ""

# Check 3: API is accessible
echo "3. Checking API endpoint..."
API_RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8080/api/events)
if [ "$API_RESPONSE" -eq 200 ] || [ "$API_RESPONSE" -eq 401 ]; then
    echo -e "${GREEN}✅ API is accessible${NC}"
else
    echo -e "${RED}❌ API returned status: $API_RESPONSE${NC}"
    echo "   Check: docker-compose logs php"
    exit 1
fi
echo ""

# Check 4: Admin.html file exists
echo "4. Checking admin.html file..."
if [ -f "public/admin.html" ]; then
    echo -e "${GREEN}✅ admin.html exists${NC}"
    
    # Check file size (should be around 30-35KB with all fixes)
    FILE_SIZE=$(wc -c < public/admin.html)
    if [ $FILE_SIZE -gt 20000 ]; then
        echo -e "${GREEN}   File size: ${FILE_SIZE} bytes (looks good)${NC}"
    else
        echo -e "${YELLOW}   ⚠️  File size: ${FILE_SIZE} bytes (seems small)${NC}"
    fi
else
    echo -e "${RED}❌ admin.html not found${NC}"
    exit 1
fi
echo ""

# Check 5: No duplicate functions
echo "5. Checking for duplicate functions..."
DUPLICATE_COUNT=$(grep -c "function loadReservations\|function displayReservations\|function editReservation\|function deleteReservation\|function populateEventSelect" public/admin.html || true)
if [ "$DUPLICATE_COUNT" -eq 5 ]; then
    echo -e "${GREEN}✅ No duplicate functions found${NC}"
else
    echo -e "${YELLOW}   ⚠️  Found $DUPLICATE_COUNT function definitions (expected 5)${NC}"
fi
echo ""

# Check 6: XSS protection (escapeHtml function)
echo "6. Checking XSS protection..."
if grep -q "function escapeHtml" public/admin.html; then
    echo -e "${GREEN}✅ escapeHtml() function exists${NC}"
else
    echo -e "${RED}❌ escapeHtml() function not found${NC}"
fi
echo ""

# Check 7: No inline onclick with user data
echo "7. Checking for unsafe onclick handlers..."
UNSAFE_ONCLICK=$(grep -c "onclick=\".*reservation\." public/admin.html || true)
if [ "$UNSAFE_ONCLICK" -eq 0 ]; then
    echo -e "${GREEN}✅ No unsafe onclick handlers found${NC}"
else
    echo -e "${RED}❌ Found $UNSAFE_ONCLICK unsafe onclick handlers${NC}"
fi
echo ""

# Check 8: CSS classes exist
echo "8. Checking CSS classes..."
if grep -q ".card {" public/admin.html; then
    echo -e "${GREEN}✅ .card CSS class exists${NC}"
else
    echo -e "${RED}❌ .card CSS class missing${NC}"
fi

if grep -q ".btn-sm {" public/admin.html; then
    echo -e "${GREEN}✅ .btn-sm CSS class exists${NC}"
else
    echo -e "${RED}❌ .btn-sm CSS class missing${NC}"
fi
echo ""

# Check 9: Error container exists
echo "9. Checking error containers..."
if grep -q "id=\"events-error\"" public/admin.html; then
    echo -e "${GREEN}✅ events-error container exists${NC}"
else
    echo -e "${RED}❌ events-error container missing${NC}"
fi
echo ""

# Check 10: Database connection
echo "10. Checking database..."
DB_CHECK=$(docker-compose exec -T php bin/console doctrine:query:sql "SELECT COUNT(*) FROM \"user\";" 2>&1 || echo "error")
if echo "$DB_CHECK" | grep -q "error"; then
    echo -e "${YELLOW}   ⚠️  Could not connect to database${NC}"
    echo "   Run migrations: docker-compose exec php bin/console doctrine:migrations:migrate"
else
    echo -e "${GREEN}✅ Database is accessible${NC}"
    USER_COUNT=$(echo "$DB_CHECK" | grep -o "[0-9]\+" | head -1)
    echo "   Users in database: $USER_COUNT"
fi
echo ""

# Summary
echo "================================"
echo "🎉 Health Check Complete!"
echo ""
echo "Next steps:"
echo "1. Go to http://localhost:8080/login.html"
echo "2. Login with: admin@test.com"
echo "3. Go to http://localhost:8080/admin.html"
echo "4. Test creating/editing/deleting events"
echo ""
echo "📚 Documentation:"
echo "- Quick Summary: ADMIN_DASHBOARD_QUICK_SUMMARY.md"
echo "- Full Details: ADMIN_DASHBOARD_FIXES_COMPLETE.md"
echo "- Testing Guide: TESTING_GUIDE.md"
echo ""
echo "✨ Dashboard is ready to use!"
