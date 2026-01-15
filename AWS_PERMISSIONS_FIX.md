# AWS Permissions Fix Guide

## Immediate Fix (Run on AWS Server)

SSH into your AWS server and run these commands:

```bash
cd /var/app/current

# Fix ownership (webapp is the default Elastic Beanstalk user)
sudo chown -R webapp:webapp storage
sudo chown -R webapp:webapp bootstrap/cache

# Fix permissions
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache

# Create directories if they don't exist
sudo mkdir -p storage/framework/sessions
sudo mkdir -p storage/framework/views
sudo mkdir -p storage/framework/cache
sudo mkdir -p storage/logs
sudo mkdir -p bootstrap/cache

# Set permissions again after creating
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache
sudo chown -R webapp:webapp storage
sudo chown -R webapp:webapp bootstrap/cache

# Now try config:cache again
php artisan config:cache
```

## Automated Solution

The `.ebextensions/01-fix-permissions.config` file has been created. This will automatically fix permissions on every deployment.

After deploying this file, the permissions will be set automatically during deployment.

## Verify Permissions

After running the commands, verify with:

```bash
ls -la storage/
ls -la bootstrap/cache/
```

You should see `webapp` as the owner.

