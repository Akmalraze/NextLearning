#!/bin/bash

echo "=========================================="
echo "Checking Environment Variable Access"
echo "=========================================="
echo ""

echo "1. Checking as ec2-user:"
echo "--------------------------------------------"
printenv | grep DB_ || echo "No DB_ variables found for ec2-user"
echo ""

echo "2. Checking as webapp user (web server):"
echo "--------------------------------------------"
sudo -u webapp printenv | grep DB_ || echo "No DB_ variables found for webapp user"
echo ""

echo "3. Checking PHP process environment:"
echo "--------------------------------------------"
# Check what PHP sees
sudo -u webapp php -r "
echo 'DB_HOST: ' . (getenv('DB_HOST') ?: 'NOT SET') . PHP_EOL;
echo 'DB_DATABASE: ' . (getenv('DB_DATABASE') ?: 'NOT SET') . PHP_EOL;
echo 'DB_USERNAME: ' . (getenv('DB_USERNAME') ?: 'NOT SET') . PHP_EOL;
"
echo ""

echo "4. Checking EB environment file location:"
echo "--------------------------------------------"
if [ -f "/opt/elasticbeanstalk/deployment/env" ]; then
    echo "Found: /opt/elasticbeanstalk/deployment/env"
    sudo cat /opt/elasticbeanstalk/deployment/env | grep -E "^DB_" | head -5
elif [ -f "/var/elasticbeanstalk/deployment/env" ]; then
    echo "Found: /var/elasticbeanstalk/deployment/env"
    sudo cat /var/elasticbeanstalk/deployment/env | grep -E "^DB_" | head -5
else
    echo "EB env file not found in common locations"
fi
echo ""

echo "5. Checking if we can source EB environment:"
echo "--------------------------------------------"
# Try to source the environment
if [ -f "/opt/elasticbeanstalk/support/envvars" ]; then
    echo "Sourcing /opt/elasticbeanstalk/support/envvars"
    source /opt/elasticbeanstalk/support/envvars 2>/dev/null
    printenv | grep DB_ || echo "Still not available after sourcing"
else
    echo "envvars file not found"
fi
echo ""

echo "=========================================="
echo "Solution: Run artisan commands as webapp user"
echo "=========================================="
echo ""
echo "Since variables are available to webapp user, run:"
echo "  sudo -u webapp php artisan config:clear"
echo "  sudo -u webapp php artisan config:cache"
echo ""

