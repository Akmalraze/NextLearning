# Fix Database Connection Error: "No such file or directory"

## Problem
`SQLSTATE[HY000] [2002] No such file or directory` means PHP can't connect to the database host.

## Common Causes

1. **Database host unreachable** - Network/security group issue
2. **Wrong host format** - Should be hostname, not socket path
3. **RDS security group** - Not allowing connections from EB instances
4. **Config cache has wrong values** - Empty or incorrect host

## Diagnostic Steps

Run these on your AWS server:

```bash
cd /var/app/staging

# 1. Check what config has for DB_HOST
sudo -u webapp php artisan tinker --execute="
echo 'DB_HOST from config: ' . config('database.connections.mysql.host') . PHP_EOL;
echo 'DB_DATABASE from config: ' . config('database.connections.mysql.database') . PHP_EOL;
echo 'DB_USERNAME from config: ' . config('database.connections.mysql.username') . PHP_EOL;
"

# 2. Check if hostname resolves
nslookup edufairuzullah-db.ctou2eooyy86.ap-southeast-2.rds.amazonaws.com

# 3. Test network connectivity (if telnet/nc available)
telnet edufairuzullah-db.ctou2eooyy86.ap-southeast-2.rds.amazonaws.com 3306
# Or
nc -zv edufairuzullah-db.ctou2eooyy86.ap-southeast-2.rds.amazonaws.com 3306

# 4. Check if environment variables are set for webapp
sudo -u webapp printenv | grep DB_
```

## Solutions

### Solution 1: Verify RDS Security Group

The RDS security group must allow inbound connections from your EB instances:

1. **Go to RDS Console:**
   - https://console.aws.amazon.com/rds
   - Select your database: `edufairuzullah-db`

2. **Check Security Group:**
   - Click on the database
   - Go to **Connectivity & security** tab
   - Note the **VPC security groups**

3. **Edit Security Group:**
   - Go to EC2 → Security Groups
   - Find the security group used by your RDS
   - Click **Edit inbound rules**
   - Add rule:
     - **Type**: MySQL/Aurora (3306)
     - **Source**: Select the security group of your EB environment
       - Or use: `sg-xxxxx` (your EB security group)
     - **Description**: Allow EB to connect to RDS

### Solution 2: Verify Config Cache Has Correct Values

```bash
cd /var/app/staging

# Clear and rebuild config
sudo -u webapp php artisan config:clear

# Verify environment variables are available
sudo -u webapp printenv | grep DB_

# If variables show up, rebuild config
sudo -u webapp php artisan config:cache

# Check cached config
php -r "
\$config = require 'bootstrap/cache/config.php';
echo 'Cached DB_HOST: ' . (\$config['database']['connections']['mysql']['host'] ?: 'EMPTY') . PHP_EOL;
"
```

### Solution 3: Test Connection with mysql client

```bash
# Install mysql client if not available
sudo yum install mysql -y

# Test connection
mysql -h edufairuzullah-db.ctou2eooyy86.ap-southeast-2.rds.amazonaws.com \
      -u admin \
      -p'GUdWvdvzOELN90PKahqL' \
      edufairuzullah-db \
      -e "SELECT 1;"
```

If this works, the issue is in Laravel config. If it fails, it's a network/security group issue.

### Solution 4: Check Database Config Format

Make sure your database config doesn't have socket path instead of hostname. Check:

```bash
cat config/database.php | grep -A 10 "mysql"
```

Should show:
```php
'host' => env('DB_HOST', '127.0.0.1'),
```

NOT:
```php
'unix_socket' => env('DB_HOST', ...),
```

## Quick Fix Commands

```bash
cd /var/app/staging

# 1. Verify environment variables
sudo -u webapp printenv | grep DB_

# 2. If empty, the variables aren't set - need to set them in EB
# 3. If they show up, rebuild config
sudo -u webapp php artisan config:clear
sudo -u webapp php artisan config:cache

# 4. Check cached values
php -r "\$c=require 'bootstrap/cache/config.php'; var_dump(\$c['database']['connections']['mysql']);"

# 5. Test DNS resolution
nslookup edufairuzullah-db.ctou2eooyy86.ap-southeast-2.rds.amazonaws.com

# 6. Test network connection
nc -zv edufairuzullah-db.ctou2eooyy86.ap-southeast-2.rds.amazonaws.com 3306
```

