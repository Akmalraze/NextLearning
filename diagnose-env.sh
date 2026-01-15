#!/bin/bash

echo "=========================================="
echo "Environment Variables Diagnostic"
echo "=========================================="
echo ""

echo "1. Checking system environment variables:"
echo "--------------------------------------------"
printenv | grep -E "^DB_|^APP_" | sort
echo ""

echo "2. Checking if variables are in EB environment:"
echo "--------------------------------------------"
# Try to get from EB metadata (if available)
if [ -f "/opt/elasticbeanstalk/deployment/env" ]; then
    echo "EB deployment env file found:"
    cat /opt/elasticbeanstalk/deployment/env | grep -E "DB_|APP_" | head -10
else
    echo "EB deployment env file not found at /opt/elasticbeanstalk/deployment/env"
fi
echo ""

echo "3. Checking PHP environment:"
echo "--------------------------------------------"
php -r "echo 'DB_HOST: ' . (getenv('DB_HOST') ?: 'NOT SET') . PHP_EOL;"
php -r "echo 'DB_DATABASE: ' . (getenv('DB_DATABASE') ?: 'NOT SET') . PHP_EOL;"
php -r "echo 'DB_USERNAME: ' . (getenv('DB_USERNAME') ?: 'NOT SET') . PHP_EOL;"
echo ""

echo "4. Checking Laravel .env file:"
echo "--------------------------------------------"
if [ -f ".env" ]; then
    echo ".env file exists:"
    cat .env | grep -E "^DB_|^APP_" | head -10
else
    echo ".env file does NOT exist (this is OK for EB)"
fi
echo ""

echo "5. Checking cached config file:"
echo "--------------------------------------------"
if [ -f "bootstrap/cache/config.php" ]; then
    echo "Config cache exists. Checking DB values:"
    php -r "
    \$config = require 'bootstrap/cache/config.php';
    if (isset(\$config['database']['connections']['mysql'])) {
        echo 'DB_HOST: ' . (\$config['database']['connections']['mysql']['host'] ?: 'EMPTY') . PHP_EOL;
        echo 'DB_DATABASE: ' . (\$config['database']['connections']['mysql']['database'] ?: 'EMPTY') . PHP_EOL;
        echo 'DB_USERNAME: ' . (\$config['database']['connections']['mysql']['username'] ?: 'EMPTY') . PHP_EOL;
    } else {
        echo 'MySQL config not found in cache' . PHP_EOL;
    }
    "
else
    echo "Config cache file does NOT exist"
fi
echo ""

echo "6. Checking EB environment configuration:"
echo "--------------------------------------------"
# Check if we can access EB metadata
if command -v eb &> /dev/null; then
    echo "EB CLI available. Run 'eb printenv' from your local machine to see all environment variables."
else
    echo "EB CLI not available on this instance"
fi
echo ""

echo "=========================================="
echo "Diagnostic Complete"
echo "=========================================="

