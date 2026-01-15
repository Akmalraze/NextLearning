#!/bin/bash

# Script to verify environment variables are set correctly on AWS EB
# Run this on your EB instance: bash verify-env.sh

echo "=========================================="
echo "Environment Variables Verification"
echo "=========================================="
echo ""

echo "1. Checking environment variables from system:"
echo "--------------------------------------------"
printenv | grep -E "^DB_|^APP_" | sort
echo ""

echo "2. Checking Laravel config (after cache):"
echo "--------------------------------------------"
php artisan tinker --execute="
echo 'DB_HOST: ' . config('database.connections.mysql.host') . PHP_EOL;
echo 'DB_DATABASE: ' . config('database.connections.mysql.database') . PHP_EOL;
echo 'DB_USERNAME: ' . config('database.connections.mysql.username') . PHP_EOL;
echo 'DB_CONNECTION: ' . config('database.default') . PHP_EOL;
"
echo ""

echo "3. Checking if config cache exists:"
echo "--------------------------------------------"
if [ -f "bootstrap/cache/config.php" ]; then
    echo "✓ Config cache file exists"
    echo "  File size: $(du -h bootstrap/cache/config.php | cut -f1)"
else
    echo "✗ Config cache file NOT found"
    echo "  Run: php artisan config:cache"
fi
echo ""

echo "4. Testing database connection:"
echo "--------------------------------------------"
php artisan tinker --execute="
try {
    DB::connection()->getPdo();
    echo '✓ Database connection successful!' . PHP_EOL;
} catch (Exception \$e) {
    echo '✗ Database connection failed: ' . \$e->getMessage() . PHP_EOL;
}
"
echo ""

echo "=========================================="
echo "Verification Complete"
echo "=========================================="

