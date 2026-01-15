# Fix Empty Database Configuration on AWS

## ⚠️ IMPORTANT: Read AWS_EB_ENV_SETUP.md First!

**This file is for reference. For the complete solution, see `AWS_EB_ENV_SETUP.md`**

## Problem
Both `env('DB_HOST')` and `config('database.connections.mysql.host')` return empty/null because environment variables are not set in Elastic Beanstalk.

**Note:** On AWS EB, `.env` should be empty. Environment variables are set via EB configuration, not `.env` file.

## Solution: Check and Fix .env File on AWS

Run these commands on your AWS server:

### Step 1: Check if .env file exists
```bash
cd /var/app/current
ls -la .env
```

### Step 2: Check what's in the .env file
```bash
cat .env | grep DB_
```

### Step 3: If .env doesn't exist or is missing DB values, create/update it

**Option A: If .env doesn't exist, create it:**
```bash
# Check if .env.example exists
ls -la .env.example

# If it exists, copy it
cp .env.example .env

# Then edit it with your values
nano .env
```

**Option B: If .env exists but DB values are missing, add them:**
```bash
nano .env
```

### Step 4: Add/Update these lines in .env file:
```env
DB_CONNECTION=mysql
DB_HOST=edufairuzullah-db.ctou2eooyy86.ap-southeast-2.rds.amazonaws.com
DB_PORT=3306
DB_DATABASE=edufairuzullah-db
DB_USERNAME=your_username_here
DB_PASSWORD=your_password_here
```

**Important:**
- Replace `your_username_here` with your actual RDS username
- Replace `your_password_here` with your actual RDS password
- Make sure there are NO spaces around the `=` sign
- Make sure there are NO quotes around the values (unless the value itself contains spaces)

### Step 5: Save the file (in nano: Ctrl+X, then Y, then Enter)

### Step 6: Clear config cache and rebuild
```bash
php artisan config:clear
php artisan config:cache
```

### Step 7: Test in tinker
```bash
php artisan tinker
```

Then run:
```php
>>> env('DB_HOST')
>>> env('DB_DATABASE')
>>> config('database.connections.mysql.host')
>>> config('database.connections.mysql.database')
```

## Alternative: Use Elastic Beanstalk Environment Variables

If you prefer to use Elastic Beanstalk environment variables instead of .env file:

1. Go to AWS Elastic Beanstalk Console
2. Select your environment
3. Go to Configuration → Software
4. Add environment properties:
   - `DB_HOST` = `edufairuzullah-db.ctou2eooyy86.ap-southeast-2.rds.amazonaws.com`
   - `DB_DATABASE` = `edufairuzullah-db`
   - `DB_USERNAME` = your username
   - `DB_PASSWORD` = your password
   - `DB_CONNECTION` = `mysql`
   - `DB_PORT` = `3306`

5. Apply changes
6. Then run: `php artisan config:clear && php artisan config:cache`

