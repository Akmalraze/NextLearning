# Finding the Application Path on EB Instance

## Problem
`/var/app/current` doesn't exist on this instance.

## Solution: Find the Correct Path

Run these commands to find where your application is:

```bash
# Check common EB paths
ls -la /var/app/ 2>/dev/null
ls -la /var/www/ 2>/dev/null
ls -la /opt/elasticbeanstalk/ 2>/dev/null

# Check if there's a staging directory
ls -la /var/app/ondeck/ 2>/dev/null

# Find where Laravel might be
find /var -name "artisan" 2>/dev/null | head -5
find /opt -name "artisan" 2>/dev/null | head -5

# Check current working directory
pwd

# List what's in home directory
ls -la ~
```

## Common EB Paths

- `/var/app/current` - Current deployed version (most common)
- `/var/app/ondeck` - Staging area during deployment
- `/var/www/html` - Alternative location
- `/opt/elasticbeanstalk/deployment/app_source` - Source location

## If Application Path Not Found

This might be a new instance that hasn't been deployed to yet. Check:

1. **Is this instance part of your EB environment?**
   ```bash
   # Check instance metadata
   curl -s http://169.254.169.254/latest/meta-data/instance-id
   ```

2. **Check EB environment status:**
   - Go to AWS Console → Elastic Beanstalk → Your Environment
   - Check if deployment is in progress
   - Check instance health

3. **Try the other instance:**
   - When you run `eb ssh educloud-prod`, it should show you multiple instances
   - Try selecting instance #1 instead

