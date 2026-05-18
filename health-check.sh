#!/bin/bash
# Health check script for Docker services

set -e

FAILED=0

echo "🔍 Checking Docker services health..."
echo ""

# Check MySQL
echo "Checking MySQL..."
if docker-compose exec mysql mysqladmin ping -uroot -p${MYSQL_ROOT_PASSWORD} &> /dev/null; then
    echo "✅ MySQL: Healthy"
else
    echo "❌ MySQL: Unhealthy"
    FAILED=$((FAILED + 1))
fi

# Check Redis
echo "Checking Redis..."
if docker-compose exec redis redis-cli ping &> /dev/null; then
    echo "✅ Redis: Healthy"
else
    echo "❌ Redis: Unhealthy"
    FAILED=$((FAILED + 1))
fi

# Check PHP-FPM
echo "Checking PHP-FPM..."
if docker-compose exec app php -v &> /dev/null; then
    echo "✅ PHP-FPM: Healthy"
else
    echo "❌ PHP-FPM: Unhealthy"
    FAILED=$((FAILED + 1))
fi

# Check Nginx
echo "Checking Nginx..."
if docker-compose exec web wget --quiet --tries=1 --spider http://localhost/health &> /dev/null; then
    echo "✅ Nginx: Healthy"
else
    echo "❌ Nginx: Unhealthy"
    FAILED=$((FAILED + 1))
fi

echo ""
echo "📊 Container Resource Usage:"
docker stats --no-stream

echo ""
if [ $FAILED -eq 0 ]; then
    echo "✅ All services are healthy!"
    exit 0
else
    echo "❌ Some services are unhealthy"
    exit 1
fi
