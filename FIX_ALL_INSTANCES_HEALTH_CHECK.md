# Fix: All Instances Failing ELB Health Checks

## Critical Issue
All instances are failing health checks, which means:
- ELB can't route traffic to your application
- Application is likely down or not responding
- Database connection issues (we saw this)
- Health check endpoint not working

## Step-by-Step Solution

### Step 1: Fix Database Connection (CRITICAL)

The database connection is failing. This is likely causing the application to crash.

#### A. Check RDS Security Group

1. **Go to RDS Console:**
   - https://console.aws.amazon.com/rds
   - Select: `edufairuzullah-db`

2. **Get Security Group ID:**
   - Click on database
   - **Connectivity & security** tab
   - Note the **VPC security groups** (e.g., `sg-xxxxx`)

3. **Get EB Security Group:**
   - Go to EC2 → Instances
   - Find one of your EB instances
   - Click on it → **Security** tab
   - Note the **Security groups** (e.g., `sg-yyyyy`)

4. **Edit RDS Security Group:**
   - Go to EC2 → Security Groups
   - Find the RDS security group (from step 2)
   - Click **Edit inbound rules**
   - **Add rule:**
     - **Type**: MySQL/Aurora
     - **Port**: 3306
     - **Source**: Select the EB security group (from step 3)
     - **Description**: Allow EB instances to connect
   - **Save rules**

#### B. Verify Database Connection

SSH into an instance and test:

```bash
eb ssh educloud-prod
# Select instance #1 or #2
cd /var/app/current  # or /var/app/staging if current doesn't exist

# Test direct mysql connection
sudo yum install mysql -y
mysql -h edufairuzullah-db.ctou2eooyy86.ap-southeast-2.rds.amazonaws.com \
      -u admin \
      -p'GUdWvdvzOELN90PKahqL' \
      edufairuzullah-db \
      -e "SELECT 1;"
```

If this works, proceed. If not, check security group again.

### Step 2: Fix Config Cache on All Instances

```bash
cd /var/app/current  # or /var/app/staging

# Fix permissions
sudo chown -R webapp:webapp storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Verify environment variables
sudo -u webapp printenv | grep DB_

# If variables show up, rebuild config
sudo -u webapp php artisan config:clear
sudo -u webapp php artisan config:cache

# Test database connection
sudo -u webapp php artisan tinker --execute="
try {
    DB::connection()->getPdo();
    echo 'Database: OK' . PHP_EOL;
} catch (Exception \$e) {
    echo 'Database ERROR: ' . \$e->getMessage() . PHP_EOL;
}
"
```

### Step 3: Test Health Check Endpoint

```bash
# Test the health endpoint
curl -v http://localhost/health

# Should return: {"status":"ok","timestamp":"..."}
```

If this fails, the health route might not be deployed yet.

### Step 4: Check Application Logs

```bash
# Check for errors
tail -100 storage/logs/laravel.log | grep -i "error\|exception\|fatal"

# Check recent logs
tail -50 storage/logs/laravel.log
```

### Step 5: Update ELB Health Check Configuration

1. **Go to EC2 Console:**
   - https://console.aws.amazon.com/ec2
   - Click **Load Balancers** (left sidebar)
   - Find your load balancer (usually named after your EB environment)

2. **Edit Health Check:**
   - Click on the load balancer
   - Go to **Health checks** tab
   - Click **Edit**
   - Update settings:
     - **Path**: `/health` (or `/` if health endpoint not deployed)
     - **Port**: `80` (or `443` for HTTPS)
     - **Protocol**: HTTP (or HTTPS)
     - **Healthy threshold**: `2`
     - **Unhealthy threshold**: `3`
     - **Timeout**: `5` seconds
     - **Interval**: `30` seconds
   - **Save**

### Step 6: Deploy Health Check Endpoint

The health check route needs to be deployed. From your local machine:

```bash
# Make sure health route is committed
git status
git add routes/web.php
git commit -m "Add health check endpoint for ELB"
git push

# Deploy to EB
eb deploy educloud-prod
```

### Step 7: Restart Environment (If Needed)

After fixing everything:

```bash
# From local machine
eb restart educloud-prod
```

Or via AWS Console:
- Elastic Beanstalk → Your Environment
- **Environment actions** → **Restart app server(s)**

## Quick Fix Checklist

- [ ] RDS security group allows EB security group on port 3306
- [ ] Database connection works: `mysql -h ... -u admin -p ...`
- [ ] Environment variables are set in EB: `eb printenv | grep DB_`
- [ ] Config cache rebuilt on instances: `sudo -u webapp php artisan config:cache`
- [ ] Database connection works in Laravel: `DB::connection()->getPdo()`
- [ ] Health endpoint works: `curl http://localhost/health`
- [ ] ELB health check path is `/health` (or `/`)
- [ ] Health check route is deployed
- [ ] Application logs show no critical errors

## Most Critical: RDS Security Group

**This is likely the main issue.** The RDS database security group must allow inbound connections from your EB instances' security group.

## Emergency Workaround

If you need the app up immediately while fixing:

1. **Temporarily allow all traffic** (NOT RECOMMENDED for production):
   - RDS Security Group → Edit inbound rules
   - Add: MySQL/Aurora, Port 3306, Source: `0.0.0.0/0`
   - **Remove this after fixing properly!**

2. **Or use a simpler health check** that doesn't require database:
   - Update ELB health check to `/health`
   - Make sure `/health` route doesn't use database

## After Fixing

Monitor the environment:
- AWS Console → Elastic Beanstalk → Your Environment → Health
- Wait 2-5 minutes for health checks to pass
- Check Events tab for any errors

