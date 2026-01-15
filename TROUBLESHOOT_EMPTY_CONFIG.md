# Troubleshooting: Empty Config Values After Setting Environment Variables

## Problem
After running `eb setenv` and `php artisan config:cache`, config values are still empty.

## Step-by-Step Solution

### Step 1: Verify Environment Variables Are Actually Set

**On your AWS server, run:**
```bash
printenv | grep DB_
```

**Expected output:**
```
DB_CONNECTION=mysql
DB_DATABASE=edufairuzullah-db
DB_HOST=edufairuzullah-db.ctou2eooyy86.ap-southeast-2.rds.amazonaws.com
DB_PASSWORD=GUdWvdvzOELN90PKahqL
DB_PORT=3306
DB_USERNAME=admin
```

**If you see NOTHING or only some variables:**
- The `eb setenv` command may not have worked
- The environment needs to be restarted
- Variables need to be set via AWS Console instead

### Step 2: Check from Your Local Machine

**Run this from your local machine (where you have EB CLI):**
```bash
eb printenv educloud-prod
```

This will show ALL environment variables set in your EB environment.

**If variables are missing, set them again:**
```bash
eb setenv educloud-prod \
  DB_CONNECTION=mysql \
  DB_HOST=edufairuzullah-db.ctou2eooyy86.ap-southeast-2.rds.amazonaws.com \
  DB_PORT=3306 \
  DB_DATABASE=edufairuzullah-db \
  DB_USERNAME=admin \
  DB_PASSWORD=GUdWvdvzOELN90PKahqL \
  APP_ENV=production \
  APP_DEBUG=false
```

**Wait for the environment to update** (this takes 2-5 minutes). You'll see output like:
```
INFO: Environment update is starting.
...
INFO: Successfully deployed new configuration to environment.
```

### Step 3: Restart the Environment (If Variables Still Not Showing)

Sometimes EB needs a restart to pick up new environment variables:

**From your local machine:**
```bash
eb restart educloud-prod
```

Or via AWS Console:
1. Go to Elastic Beanstalk → Your Environment
2. Click "Environment actions" → "Restart app server(s)"

### Step 4: Verify Variables Are Available to PHP

**SSH into your server and run:**
```bash
cd /var/app/current

# Check if PHP can see the variables
php -r "echo 'DB_HOST: ' . (getenv('DB_HOST') ?: 'NOT SET') . PHP_EOL;"
php -r "echo 'DB_DATABASE: ' . (getenv('DB_DATABASE') ?: 'NOT SET') . PHP_EOL;"
```

**If PHP shows "NOT SET":**
- The environment variables aren't being passed to PHP
- Try setting them via AWS Console instead (see Step 5)

### Step 5: Set Variables via AWS Console (Most Reliable Method)

1. **Go to AWS Console:**
   - https://console.aws.amazon.com/elasticbeanstalk
   - Select your application → `educloud-prod` environment

2. **Open Configuration:**
   - Click **Configuration** in left sidebar
   - Scroll to **Software** section
   - Click **Edit**

3. **Add Environment Properties:**
   - Scroll to **Environment properties**
   - Click **Add environment property** for each:
     - `DB_CONNECTION` = `mysql`
     - `DB_HOST` = `edufairuzullah-db.ctou2eooyy86.ap-southeast-2.rds.amazonaws.com`
     - `DB_PORT` = `3306`
     - `DB_DATABASE` = `edufairuzullah-db`
     - `DB_USERNAME` = `admin`
     - `DB_PASSWORD` = `GUdWvdvzOELN90PKahqL`
     - `APP_ENV` = `production`
     - `APP_DEBUG` = `false`

4. **Apply Changes:**
   - Click **Apply** at bottom
   - **Wait for environment update to complete** (2-5 minutes)
   - You'll see a green checkmark when done

5. **SSH and Rebuild Config:**
   ```bash
   eb ssh educloud-prod
   cd /var/app/current
   
   # Verify variables are now available
   printenv | grep DB_
   
   # Clear and rebuild config
   php artisan config:clear
   php artisan config:cache
   
   # Test
   php artisan tinker
   >>> config('database.connections.mysql.host')
   >>> config('database.connections.mysql.database')
   ```

### Step 6: Use Diagnostic Script

I've created a diagnostic script. Upload it to your server and run:

```bash
cd /var/app/current
chmod +x diagnose-env.sh
bash diagnose-env.sh
```

This will show you exactly where the problem is.

## Common Issues and Solutions

### Issue 1: `eb setenv` command didn't work
**Solution:** Use AWS Console method (Step 5) - it's more reliable

### Issue 2: Variables set but PHP can't see them
**Solution:** 
- Restart the environment: `eb restart educloud-prod`
- Or restart app server via AWS Console

### Issue 3: Config cache was built before variables were set
**Solution:**
```bash
php artisan config:clear
# Wait a moment
php artisan config:cache
```

### Issue 4: Environment update is still in progress
**Solution:** Wait for the green checkmark in AWS Console before testing

## Quick Fix Commands (Run These in Order)

```bash
# 1. From local machine - verify variables are set
eb printenv educloud-prod | grep DB_

# 2. If missing, set them
eb setenv educloud-prod DB_HOST=edufairuzullah-db.ctou2eooyy86.ap-southeast-2.rds.amazonaws.com DB_DATABASE=edufairuzullah-db DB_USERNAME=admin DB_PASSWORD=GUdWvdvzOELN90PKahqL DB_CONNECTION=mysql DB_PORT=3306

# 3. Wait for update to complete, then restart
eb restart educloud-prod

# 4. SSH and verify
eb ssh educloud-prod
cd /var/app/current
printenv | grep DB_

# 5. If variables show up, rebuild config
php artisan config:clear
php artisan config:cache

# 6. Test
php artisan tinker
>>> config('database.connections.mysql.host')
```

## Most Reliable Solution

**Use AWS Console (Step 5)** - it's the most reliable method and you can visually verify all variables are set correctly.

