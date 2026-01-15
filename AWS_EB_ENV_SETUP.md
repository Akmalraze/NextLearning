# Setting Environment Variables in AWS Elastic Beanstalk

## Why .env is Empty in Production

On AWS Elastic Beanstalk, environment variables are set via EB's environment configuration, NOT in the `.env` file. This is the correct approach for production.

## Solution: Set Environment Variables in Elastic Beanstalk

You have **3 options** to set environment variables:

---

## Option 1: AWS Console (Recommended - Easiest)

### Steps:

1. **Go to AWS Elastic Beanstalk Console**
   - Navigate to: https://console.aws.amazon.com/elasticbeanstalk
   - Select your application and environment

2. **Open Configuration**
   - Click on your environment name
   - Click **Configuration** in the left sidebar

3. **Edit Software Configuration**
   - Scroll down to **Software** section
   - Click **Edit**

4. **Add Environment Properties**
   - Scroll to **Environment properties** section
   - Click **Add environment property** for each variable:

   ```
   DB_CONNECTION = mysql
   DB_HOST = edufairuzullah-db.ctou2eooyy86.ap-southeast-2.rds.amazonaws.com
   DB_PORT = 3306
   DB_DATABASE = edufairuzullah-db
   DB_USERNAME = your_actual_username
   DB_PASSWORD = your_actual_password
   APP_ENV = production
   APP_DEBUG = false
   APP_KEY = your_app_key_here
   APP_URL = https://your-domain.com
   ```

5. **Apply Changes**
   - Click **Apply** at the bottom
   - Wait for the environment to update (takes 2-5 minutes)

6. **After Update, SSH and Rebuild Config Cache**
   ```bash
   ssh into your EB instance
   cd /var/app/current
   php artisan config:clear
   php artisan config:cache
   ```

---

## Option 2: EB CLI Command (Fastest)

If you have EB CLI installed locally:

```bash
# Set all database variables at once
eb setenv educloud-prod \
  DB_CONNECTION=mysql \
  DB_HOST=edufairuzullah-db.ctou2eooyy86.ap-southeast-2.rds.amazonaws.com \
  DB_PORT=3306 \
  DB_DATABASE=edufairuzullah-db \
  DB_USERNAME=admin \
  DB_PASSWORD=GUdWvdvzOELN90PKahqL \
  APP_ENV=production \
  APP_DEBUG=false

# This will automatically update your EB environment
# Then SSH and rebuild config cache:
eb ssh
cd /var/app/current
php artisan config:clear
php artisan config:cache
```

---

## Option 3: .ebextensions File (Automated on Deploy)

The file `.ebextensions/02-environment-variables.config` has been created, but **it's better to use Option 1 or 2** for sensitive values like passwords.

If you want to use .ebextensions, edit the file and uncomment/add your values, then deploy.

**⚠️ Warning:** Don't commit passwords to git! Use Option 1 or 2 for sensitive data.

---

## After Setting Environment Variables

### Step 1: Verify Variables are Set

SSH into your EB instance and check:

```bash
cd /var/app/current

# Check if environment variables are available
printenv | grep DB_

# You should see:
# DB_HOST=edufairuzullah-db.ctou2eooyy86.ap-southeast-2.rds.amazonaws.com
# DB_DATABASE=edufairuzullah-db
# etc.
```

### Step 2: Clear and Rebuild Config Cache

```bash
php artisan config:clear
php artisan config:cache
```

### Step 3: Test in Tinker

```bash
php artisan tinker
```

Then test:
```php
// These should work now
>>> config('database.connections.mysql.host')
=> "edufairuzullah-db.ctou2eooyy86.ap-southeast-2.rds.amazonaws.com"

>>> config('database.connections.mysql.database')
=> "edufairuzullah-db"

// Note: env() won't work after config:cache, use config() instead
>>> env('DB_HOST')  // This will be null after caching
>>> config('database.connections.mysql.host')  // Use this instead
```

---

## Important Notes

1. **After `config:cache`, `env()` doesn't work** - Use `config()` instead
2. **Always run `config:cache` after setting/updating environment variables**
3. **`.env` file should be empty or minimal on production** - EB provides the values
4. **Use `config('database.connections.mysql.host')` in your code** instead of `env('DB_HOST')` for production

---

## Troubleshooting

### If variables still show as empty:

1. **Check if variables are actually set in EB:**
   ```bash
   printenv | grep DB_
   ```

2. **If printenv shows them but Laravel doesn't:**
   - Make sure you ran `php artisan config:clear` first
   - Then `php artisan config:cache`

3. **If printenv doesn't show them:**
   - Go back to AWS Console and verify they're set
   - Make sure you clicked "Apply" and waited for the update to complete
   - Try setting them again via EB CLI: `eb setenv DB_HOST=...`

4. **Verify the config cache file:**
   ```bash
   cat bootstrap/cache/config.php | grep -A 5 "mysql"
   ```
   You should see your database values in the cached config.

---

## Quick Reference Commands

```bash
# Set environment variables (from local machine with EB CLI)
eb setenv DB_HOST=edufairuzullah-db.ctou2eooyy86.ap-southeast-2.rds.amazonaws.com DB_DATABASE=edufairuzullah-db

# SSH into EB instance
eb ssh

# On EB instance - clear and rebuild config
cd /var/app/current
php artisan config:clear
php artisan config:cache

# Test in tinker
php artisan tinker
# Then: config('database.connections.mysql.host')
```

## Verification Script

A verification script `verify-env.sh` has been created. Upload it to your EB instance and run:

```bash
cd /var/app/current
chmod +x verify-env.sh
bash verify-env.sh
```

This will check:
- Environment variables from system
- Laravel config values
- Config cache status
- Database connection

