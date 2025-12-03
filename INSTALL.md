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
- intl (optional, for internationalization)

## Installation Steps

### Step 1: Download the Application

```bash
# Clone from GitHub
git clone https://github.com/crshdn/leadsfire.git /var/www/leadsfire

# Or download and extract
wget https://github.com/crshdn/leadsfire/archive/main.zip
unzip main.zip -d /var/www/
mv /var/www/leadsfire-main /var/www/leadsfire
```

### Step 2: Set File Permissions

```bash
cd /var/www/leadsfire

# Set ownership (replace www-data with your web server user)
sudo chown -R www-data:www-data .

# Set directory permissions
sudo find . -type d -exec chmod 755 {} \;

# Set file permissions
sudo find . -type f -exec chmod 644 {} \;

# Make storage writable
sudo chmod -R 775 storage/

# Secure sensitive files
sudo chmod 600 .env 2>/dev/null || true
sudo chmod 600 SPEC.md 2>/dev/null || true
```

### Step 3: Install PHP Dependencies

```bash
# Install Composer if not already installed
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install dependencies
cd /var/www/leadsfire
composer install --no-dev --optimize-autoloader
```

### Step 4: Create Database

```bash
# Log into MySQL/MariaDB
mysql -u root -p

# Create database and user
CREATE DATABASE leadsfire_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE USER 'leadsfire_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON leadsfire_tracker.* TO 'leadsfire_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 5: Configure Nginx

Create a new Nginx configuration file:

```bash
sudo nano /etc/nginx/sites-available/leadsfire
```

Add the following configuration:

```nginx
# Admin Panel
server {
    listen 443 ssl http2;
    server_name admin.yourdomain.com;
    
    root /var/www/leadsfire/public;
    index index.php;
    
    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/admin.yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/admin.yourdomain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256;
    ssl_prefer_server_ciphers off;
    
    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
    
    location ~ /\.(env|git|htaccess) {
        deny all;
    }
    
    location ~* \.(log|sql|md)$ {
        deny all;
    }
}

# Redirect HTTP to HTTPS
server {
    listen 80;
    server_name admin.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

# Tracking Domain
server {
    listen 443 ssl http2;
    server_name track.yourdomain.com;
    
    root /var/www/leadsfire/tracker;
    index index.php;
    
    ssl_certificate /etc/letsencrypt/live/track.yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/track.yourdomain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    
    location /c/ {
        try_files $uri $uri/ /c/index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/leadsfire /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Step 6: Obtain SSL Certificates

```bash
# Install Certbot if not already installed
sudo apt install certbot python3-certbot-nginx

# Obtain certificates
sudo certbot --nginx -d admin.yourdomain.com -d track.yourdomain.com
```

### Step 7: Run the Installation Wizard

1. Open your browser and navigate to: `https://admin.yourdomain.com/install.php`
2. Follow the on-screen instructions:
   - Verify system requirements
   - Enter database credentials
   - Create admin account
   - Configure application settings
3. Complete the installation

### Step 8: Secure the Installation

After installation, remove or restrict access to the install file:

```bash
# Option 1: Remove install file
rm /var/www/leadsfire/public/install.php

# Option 2: Restrict access in Nginx
# Add to your server block:
location = /install.php {
    deny all;
}
```

## Post-Installation

### Configure Cron Jobs (Optional)

For scheduled tasks like stats caching and cleanup:

```bash
crontab -e
```

Add:
```
# LeadsFire Click Tracker - Hourly stats cache
0 * * * * php /var/www/leadsfire/artisan stats:cache > /dev/null 2>&1

# LeadsFire Click Tracker - Daily cleanup (old clicks)
0 3 * * * php /var/www/leadsfire/artisan cleanup:clicks > /dev/null 2>&1
```

### Set Up Log Rotation

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
    create 644 www-data www-data
}
```

## Importing CPVLab Data

If you have an existing CPVLab database backup:

1. Go to Settings > Import
2. Upload your CPVLab SQL backup file
3. Follow the import wizard

## Troubleshooting

### Permission Errors
```bash
sudo chown -R www-data:www-data /var/www/leadsfire
sudo chmod -R 775 /var/www/leadsfire/storage
```

### Database Connection Failed
- Verify credentials in `.env`
- Check MySQL/MariaDB is running: `sudo systemctl status mysql`
- Test connection: `mysql -u leadsfire_user -p leadsfire_tracker`

### 500 Internal Server Error
- Check PHP error log: `tail -f /var/log/php8.2-fpm.log`
- Check Nginx error log: `tail -f /var/log/nginx/error.log`
- Enable debug mode temporarily in `.env`: `APP_DEBUG=true`

### Blank Page
- Check PHP-FPM is running: `sudo systemctl status php8.2-fpm`
- Verify PHP version: `php -v`
- Check file permissions

## Security Recommendations

1. **IP Whitelist**: Restrict admin access to specific IPs in `.env`
2. **Strong Passwords**: Use unique, complex passwords
3. **Regular Updates**: Keep PHP, MariaDB, and dependencies updated
4. **Backups**: Set up automated database backups
5. **Firewall**: Configure UFW or iptables to restrict access
6. **Monitoring**: Set up server monitoring and alerts

## Getting Help

- **GitHub Issues**: https://github.com/crshdn/leadsfire/issues
- **Documentation**: See README.md

---

Installation complete! 🎉 Access your admin panel at `https://admin.yourdomain.com`

