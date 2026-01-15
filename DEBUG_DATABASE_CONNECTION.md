# Debug Database Connection: "No such file or directory"

## Current Error
`SQLSTATE[HY000] [2002] No such file or directory`

This error usually means:
1. PHP is trying to use a Unix socket instead of TCP
2. The hostname is empty or wrong
3. Network connectivity issue

## Diagnostic Steps

Run these commands to find the exact issue:

```bash
cd /var/app/staging

# 1. Check what config has
sudo -u webapp php artisan tinker --execute="
echo 'DB_HOST from config: [' . config('database.connections.mysql.host') . ']' . PHP_EOL;
echo 'DB_DATABASE from config: [' . config('database.connections.mysql.database') . ']' . PHP_EOL;
echo 'DB_USERNAME from config: [' . config('database.connections.mysql.username') . ']' . PHP_EOL;
echo 'DB_PORT from config: [' . config('database.connections.mysql.port') . ']' . PHP_EOL;
"

# 2. Check environment variables
sudo -u webapp printenv | grep DB_

# 3. Check if hostname resolves
nslookup edufairuzullah-db.ctou2eooyy86.ap-southeast-2.rds.amazonaws.com

# 4. Check cached config file directly
cat bootstrap/cache/config.php | grep -A 10 "'mysql'" | head -15
```

## Common Issues and Fixes

### Issue 1: Config Cache Has Empty Values

If config shows empty values, the cache was built before environment variables were available.

**Fix:**
```bash
# Clear and rebuild
sudo -u webapp php artisan config:clear
sudo -u webapp printenv | grep DB_  # Verify variables exist
sudo -u webapp php artisan config:cache

# Check again
sudo -u webapp php artisan tinker --execute="echo config('database.connections.mysql.host');"
```

### Issue 2: Environment Variables Not Available

If `printenv` shows nothing, variables aren't set in EB.

**Fix:**
- Set variables via AWS Console or `eb setenv`
- Restart environment: `eb restart educloud-prod`
- Then rebuild config cache

### Issue 3: Hostname Empty or Wrong

If config shows empty host, check the cached config file:

```bash
php -r "
\$config = require 'bootstrap/cache/config.php';
var_dump(\$config['database']['connections']['mysql']['host']);
"
```

If it's empty, rebuild config cache after verifying environment variables.

### Issue 4: Network/Security Group Issue

Even if config is correct, connection might fail due to security group.

**Test DNS resolution:**
```bash
nslookup edufairuzullah-db.ctou2eooyy86.ap-southeast-2.rds.amazonaws.com
```

**Test network connectivity:**
```bash
# If telnet available
telnet edufairuzullah-db.ctou2eooyy86.ap-southeast-2.rds.amazonaws.com 3306

# Or use nc (netcat)
nc -zv edufairuzullah-db.ctou2eooyy86.ap-southeast-2.rds.amazonaws.com 3306
```

If DNS resolution fails or connection times out, it's a security group issue.

## Quick Fix Script

Run this complete diagnostic:

```bash
cd /var/app/staging

echo "=== 1. Environment Variables ==="
sudo -u webapp printenv | grep DB_ || echo "NO DB VARIABLES FOUND"

echo ""
echo "=== 2. Config Values ==="
sudo -u webapp php artisan tinker --execute="
echo 'Host: [' . config('database.connections.mysql.host') . ']';
echo 'Database: [' . config('database.connections.mysql.database') . ']';
echo 'Username: [' . config('database.connections.mysql.username') . ']';
"

echo ""
echo "=== 3. DNS Resolution ==="
nslookup edufairuzullah-db.ctou2eooyy86.ap-southeast-2.rds.amazonaws.com || echo "DNS FAILED"

echo ""
echo "=== 4. Network Test ==="
timeout 5 bash -c "</dev/tcp/edufairuzullah-db.ctou2eooyy86.ap-southeast-2.rds.amazonaws.com/3306" && echo "Port 3306 is OPEN" || echo "Port 3306 is CLOSED or TIMEOUT"
```

