# Fix ELB Health Check Failure

## Problem
ELB health checks are failing, likely because:
1. Database connection is failing (empty config)
2. No dedicated health check endpoint
3. Application errors preventing responses

## Immediate Steps to Diagnose

### Step 1: Check Application Logs

SSH into your server and check the logs:

```bash
eb ssh educloud-prod
cd /var/app/current

# Check Laravel logs
tail -50 storage/logs/laravel.log

# Check for recent errors
grep -i "error\|exception\|failed" storage/logs/laravel.log | tail -20
```

### Step 2: Test Database Connection

```bash
sudo -u webapp php artisan tinker --execute="
try {
    DB::connection()->getPdo();
    echo 'Database connection: SUCCESS' . PHP_EOL;
} catch (Exception \$e) {
    echo 'Database connection: FAILED - ' . \$e->getMessage() . PHP_EOL;
}
"
```

### Step 3: Test Root Route

```bash
# Test if the application responds
curl -I http://localhost/
```

### Step 4: Check What ELB is Checking

In AWS Console:
1. Go to EC2 → Load Balancers
2. Find your load balancer
3. Check the health check settings:
   - **Path**: Usually `/` or `/health`
   - **Port**: Usually `80` or `443`
   - **Protocol**: HTTP or HTTPS

## Solution: Create Health Check Endpoint

I'll create a simple health check route that doesn't require database access.

