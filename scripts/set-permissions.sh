#!/bin/bash
# LeadsFire Click Tracker - Security Permissions Script
# Run this after installation to set correct file permissions
# Usage: sudo ./scripts/set-permissions.sh [WEB_USER]

set -e

# Configuration
WEB_USER="${1:-www-data}"
APP_DIR="$(cd "$(dirname "$0")/.." && pwd)"
OWNER="$(stat -c '%U' "$APP_DIR")"

echo "============================================"
echo "  LeadsFire Security Permissions Setup"
echo "============================================"
echo ""
echo "App Directory: $APP_DIR"
echo "Owner: $OWNER"
echo "Web User: $WEB_USER"
echo ""

# Check if running as root or with sudo
if [ "$EUID" -ne 0 ]; then
    echo "Error: This script must be run as root or with sudo"
    exit 1
fi

echo "Setting directory permissions..."

# Base directories - owner can read/write/execute, group can read/execute
find "$APP_DIR" -type d -exec chmod 755 {} \;

# Storage directories - web server needs write access
chown -R "$OWNER:$WEB_USER" "$APP_DIR/storage"
chmod 770 "$APP_DIR/storage"
chmod 770 "$APP_DIR/storage/logs"
chmod 770 "$APP_DIR/storage/cache"
chmod 770 "$APP_DIR/storage/sessions"
chmod 770 "$APP_DIR/storage/uploads"

echo "Setting file permissions..."

# All PHP files - readable by owner and group
find "$APP_DIR" -type f -name "*.php" -exec chmod 644 {} \;

# Config files - more restrictive
chmod 640 "$APP_DIR/config/"*.php 2>/dev/null || true
chown "$OWNER:$WEB_USER" "$APP_DIR/config/"*.php 2>/dev/null || true

# Environment file - most restrictive
if [ -f "$APP_DIR/.env" ]; then
    chmod 640 "$APP_DIR/.env"
    chown "$OWNER:$WEB_USER" "$APP_DIR/.env"
fi

if [ -f "$APP_DIR/.env.example" ]; then
    chmod 640 "$APP_DIR/.env.example"
    chown "$OWNER:$WEB_USER" "$APP_DIR/.env.example"
fi

# Spec file - owner only
if [ -f "$APP_DIR/SPEC.md" ]; then
    chmod 600 "$APP_DIR/SPEC.md"
fi

# .htaccess files - readable but not writable
find "$APP_DIR" -name ".htaccess" -exec chmod 644 {} \;

# Scripts - executable
chmod 755 "$APP_DIR/scripts/"*.sh 2>/dev/null || true

# Vendor directory
if [ -d "$APP_DIR/vendor" ]; then
    # Create .htaccess if it doesn't exist
    if [ ! -f "$APP_DIR/vendor/.htaccess" ]; then
        cat > "$APP_DIR/vendor/.htaccess" << 'EOF'
<IfModule mod_authz_core.c>
    Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
    Order deny,allow
    Deny from all
</IfModule>
EOF
    fi
    chmod 644 "$APP_DIR/vendor/.htaccess"
fi

echo ""
echo "============================================"
echo "  Permissions Set Successfully!"
echo "============================================"
echo ""
echo "Summary:"
echo "  - Storage directories: 770 ($OWNER:$WEB_USER)"
echo "  - Config files: 640 ($OWNER:$WEB_USER)"
echo "  - .env file: 640 ($OWNER:$WEB_USER)"
echo "  - SPEC.md: 600 ($OWNER only)"
echo "  - PHP files: 644"
echo "  - .htaccess files: 644"
echo ""
echo "Run 'composer security-check' to verify permissions."

