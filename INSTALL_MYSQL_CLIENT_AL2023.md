# Install MySQL Client on Amazon Linux 2023

## Problem
`mysql` package doesn't exist on Amazon Linux 2023.

## Solution

Amazon Linux 2023 uses MariaDB packages. Install the client:

```bash
# Install MariaDB client (includes mysql command)
sudo yum install mariadb105 -y

# Or if that doesn't work:
sudo yum install mariadb -y
```

## Alternative: Use PHP to Test Connection

If you can't install mysql client, test with PHP instead:

```bash
cd /var/app/staging

# Test database connection with PHP
sudo -u webapp php artisan tinker --execute="
try {
    \$pdo = DB::connection()->getPdo();
    echo 'Database connection: SUCCESS' . PHP_EOL;
    echo 'Database name: ' . DB::connection()->getDatabaseName() . PHP_EOL;
} catch (Exception \$e) {
    echo 'Database connection: FAILED' . PHP_EOL;
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"
```

## Check What's Available

```bash
# Search for mysql/mariadb packages
yum search mysql
yum search mariadb
```

