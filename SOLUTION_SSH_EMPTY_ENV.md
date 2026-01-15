# Solution: Environment Variables Empty in SSH but Set in EB

## Problem
- `eb printenv` shows variables are set ✅
- `printenv | grep DB_` in SSH shows nothing ❌
- Config cache is empty ❌

## Root Cause
Environment variables in Elastic Beanstalk are available to the **webapp user** (web server process), not the **ec2-user** (SSH session). When you run `php artisan` as `ec2-user`, it doesn't have access to EB environment variables.

## Solution: Run Artisan Commands as webapp User

### Step 1: SSH into your server
```bash
eb ssh educloud-prod
cd /var/app/current
```

### Step 2: Run artisan commands as webapp user
```bash
# Clear config cache as webapp user
sudo -u webapp php artisan config:clear

# Cache config as webapp user (this will have access to environment variables)
sudo -u webapp php artisan config:cache
```

### Step 3: Verify it worked
```bash
# Test as webapp user
sudo -u webapp php artisan tinker --execute="
echo 'DB_HOST: ' . config('database.connections.mysql.host') . PHP_EOL;
echo 'DB_DATABASE: ' . config('database.connections.mysql.database') . PHP_EOL;
"
```

Or:
```bash
sudo -u webapp php artisan tinker
>>> config('database.connections.mysql.host')
>>> config('database.connections.mysql.database')
```

## Alternative: Verify Variables Are Available to webapp User

First, check if webapp user can see the variables:

```bash
sudo -u webapp printenv | grep DB_
```

If this shows the variables, then running artisan as webapp user will work.

## Why This Happens

1. **EB sets environment variables** for the application environment
2. **Web server (webapp user)** has access to these variables
3. **SSH session (ec2-user)** doesn't automatically inherit them
4. **PHP artisan commands** need to run as webapp user to access EB environment variables

## Complete Fix Commands

```bash
# SSH into server
eb ssh educloud-prod
cd /var/app/current

# Verify webapp user can see variables
sudo -u webapp printenv | grep DB_

# If variables show up, clear and cache config as webapp user
sudo -u webapp php artisan config:clear
sudo -u webapp php artisan config:cache

# Test
sudo -u webapp php artisan tinker --execute="echo config('database.connections.mysql.host');"
```

## Important Notes

- **Always run `php artisan` commands as `webapp` user** on EB: `sudo -u webapp php artisan ...`
- The webapp user is the one that runs your Laravel application
- Environment variables are injected into the webapp user's environment by EB
- After caching config as webapp user, the cached config will be used by your application

## If Variables Still Don't Show for webapp User

If `sudo -u webapp printenv | grep DB_` also shows nothing, then:

1. **Restart the environment:**
   ```bash
   # From local machine
   eb restart educloud-prod
   ```

2. **Or restart via AWS Console:**
   - Go to EB Console → Your Environment
   - Click "Environment actions" → "Restart app server(s)"

3. **Then try again:**
   ```bash
   sudo -u webapp printenv | grep DB_
   ```

