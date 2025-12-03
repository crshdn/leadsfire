# LeadsFire Click Tracker - Installation Guide

This guide will walk you through installing LeadsFire Click Tracker on your server.

## Prerequisites

Before you begin, ensure your server meets these requirements:

### Server Requirements
- **Operating System**: Linux (Ubuntu 20.04+, Debian 10+, CentOS 8+, or similar)
- **Web Server**: Nginx (recommended) or Apache
- **PHP**: 8.0 or higher
- **Database**: MariaDB 10.5+ or MySQL 8.0+
- **Memory**: 2GB RAM minimum (4GB recommended)
- **Disk Space**: 10GB minimum

### Required PHP Extensions
- pdo
- pdo_mysql
- curl
- json
- mbstring
- openssl
- gd (optional, for image processing)

## Quick Install (5 Steps)

### Step 1: Download & Configure

```bash
# Clone the repository
git clone https://github.com/crshdn/leadsfire.git /var/www/leadsfire
cd /var/www/leadsfire

# Copy the example environment file
cp .env.example .env

# Edit the configuration
nano .env
```

**Required settings to configure in `.env`:**
- `APP_URL` - Your admin panel URL (e.g., `https://admin.yourdomain.com`)
- `APP_TIMEZONE` - Your timezone (e.g., `America/New_York`)
- `ALLOWED_IPS` - IP addresses allowed to access admin (comma-separated)

> **Note:** You don't need to set database credentials - the setup script will handle that!

### Step 2: Install PHP Dependencies

```bash
# Install Composer if not already installed
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install dependencies
composer install --no-dev --optimize-autoloader
```

### Step 3: Set Up Database

Run the database setup script. It will:
- Create the database
- Create the application user
- Generate a secure password
- Update your `.env` file automatically

```bash
./scripts/setup-database.sh
```

You'll be prompted for MySQL root credentials (used once, not stored).

### Step 4: Set File Permissions

```bash
# Run the permissions script
sudo ./scripts/set-permissions.sh
```

This sets secure permissions for all files and directories.

### Step 5: Configure Web Server & SSL

#### For Nginx:

Create the configuration file:
```bash
sudo nano /etc/nginx/sites-available/leadsfire
```

Add this configuration (adjust domain names):
```nginx
# Admin Panel
server {
    listen 80;
    server_name admin.yourdomain.com yourdomain.com www.yourdomain.com;
    
    root /var/www/leadsfire/public;
    index index.php;
    
    # Security - block sensitive files/directories
    location ~ ^/(config|src|storage|database|vendor)/ {
        deny all;
        return 404;
    }
    
    location ~ /\.(env|git|htaccess) {
        deny all;
    }
    
    location ~* \.(log|sql|md|lock|json|yml|yaml)$ {
        deny all;
    }
    
    # ACME challenge for SSL
    location ^~ /.well-known/acme-challenge/ {
        root /var/www/leadsfire/public;
    }
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}

# Tracking Domain
server {
    listen 80;
    server_name track.yourdomain.com;
    
    root /var/www/leadsfire/tracker;
    index index.php;
    
    location ~ ^/c/(.+)$ {
        try_files $uri /c/index.php?key=$1&$query_string;
    }
    
    location = /postback.php {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location / {
        deny all;
        return 404;
    }
}
```

Enable and test:
```bash
sudo ln -s /etc/nginx/sites-available/leadsfire /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

#### Get SSL Certificates:
```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d admin.yourdomain.com -d yourdomain.com -d track.yourdomain.com
```

### Step 6: Run the Web Installer

1. Open your browser: `https://admin.yourdomain.com/install.php`
2. Follow the wizard to:
   - Verify system requirements
   - Run database migrations
   - Create your admin account
3. Log in and start using LeadsFire!

---

## Post-Installation

### Verify Security

After installation, verify these security measures:

```bash
# Test that sensitive files are blocked
curl -I https://yourdomain.com/.env          # Should return 404
curl -I https://yourdomain.com/config/app.php # Should return 404
curl -I https://yourdomain.com/storage/logs/  # Should return 404
```

### Set Up Log Rotation (Recommended)

```bash
sudo nano /etc/logrotate.d/leadsfire
```

Add:
```
/var/www/leadsfire/storage/logs/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 640 www-data www-data
}
```

### Set Up Backups (Recommended)

```bash
# Add to crontab
crontab -e

# Daily database backup at 2 AM
0 2 * * * mysqldump -u leadsfire_ct_user -p'YOUR_PASSWORD' leadsfire_tracker | gzip > /var/backups/leadsfire/db_$(date +\%Y\%m\%d).sql.gz
```

---

## Importing CPVLab Data

If you have an existing CPVLab database backup:

1. Go to **Settings > Import**
2. Upload your CPVLab SQL backup file
3. Follow the import wizard

---

## Troubleshooting

### Permission Errors
```bash
sudo ./scripts/set-permissions.sh
```

### Database Connection Failed
```bash
# Test connection manually
mysql -u leadsfire_ct_user -p leadsfire_tracker

# Check credentials in .env
cat .env | grep DB_
```

### 500 Internal Server Error
```bash
# Check logs
tail -f /var/www/leadsfire/storage/logs/*.log
tail -f /var/log/nginx/error.log
tail -f /var/log/php8.2-fpm.log

# Enable debug mode temporarily
sed -i 's/APP_DEBUG=false/APP_DEBUG=true/' .env
```

### Blank Page
```bash
# Check PHP-FPM
sudo systemctl status php8.2-fpm

# Check PHP version
php -v

# Check storage is writable
ls -la storage/
```

---

## Security Recommendations

1. **IP Whitelist**: Set `ALLOWED_IPS` in `.env` to restrict admin access
2. **Strong Passwords**: Use unique, complex passwords
3. **Regular Updates**: Keep PHP, MariaDB, and dependencies updated
4. **Backups**: Set up automated database backups
5. **Firewall**: Configure UFW to restrict access
6. **HTTPS Only**: Ensure all traffic uses SSL

---

## Getting Help

- **GitHub Issues**: https://github.com/crshdn/leadsfire/issues
- **Documentation**: See README.md

---

Installation complete! 🎉 Access your admin panel at `https://admin.yourdomain.com`
