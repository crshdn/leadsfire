#!/bin/bash
# LeadsFire Click Tracker - Database Setup Script
# This script creates the database and user for the application.
# Run this BEFORE visiting the web installer.
#
# Usage: ./scripts/setup-database.sh
#
# Requirements:
# - MySQL/MariaDB root access
# - .env file must exist (copy from .env.example first)

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Get script directory and app root
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
APP_DIR="$(dirname "$SCRIPT_DIR")"
ENV_FILE="$APP_DIR/.env"
INSTALLED_FLAG="$APP_DIR/storage/.installed"

echo -e "${BLUE}"
echo "============================================"
echo "  LeadsFire Click Tracker - Database Setup"
echo "============================================"
echo -e "${NC}"

# Check if already installed
if [ -f "$INSTALLED_FLAG" ]; then
    echo -e "${RED}Error: Application appears to be already installed.${NC}"
    echo "If you need to re-run setup, delete: $INSTALLED_FLAG"
    exit 1
fi

# Check if .env exists
if [ ! -f "$ENV_FILE" ]; then
    echo -e "${RED}Error: .env file not found.${NC}"
    echo "Please copy .env.example to .env first:"
    echo "  cp $APP_DIR/.env.example $APP_DIR/.env"
    exit 1
fi

# Read current values from .env
current_db_name=$(grep "^DB_DATABASE=" "$ENV_FILE" | cut -d'=' -f2)
current_db_user=$(grep "^DB_USERNAME=" "$ENV_FILE" | cut -d'=' -f2)
current_db_host=$(grep "^DB_HOST=" "$ENV_FILE" | cut -d'=' -f2)

echo -e "${YELLOW}Current .env database settings:${NC}"
echo "  Database: $current_db_name"
echo "  Username: $current_db_user"
echo "  Host: $current_db_host"
echo ""

# Ask for confirmation or custom values
read -p "Use these settings? [Y/n]: " use_current
use_current=${use_current:-Y}

if [[ ! "$use_current" =~ ^[Yy]$ ]]; then
    read -p "Database name [$current_db_name]: " db_name
    db_name=${db_name:-$current_db_name}
    
    read -p "Database user [$current_db_user]: " db_user
    db_user=${db_user:-$current_db_user}
else
    db_name="$current_db_name"
    db_user="$current_db_user"
fi

# Generate secure password (32 characters)
echo ""
echo -e "${BLUE}Generating secure database password...${NC}"
db_password=$(openssl rand -base64 32 | tr -dc 'a-zA-Z0-9' | head -c 32)
echo -e "${GREEN}✓ Password generated${NC}"

# Get MySQL root credentials
echo ""
echo -e "${YELLOW}MySQL/MariaDB root credentials required to create database and user.${NC}"
echo -e "${YELLOW}These credentials are used once and NOT stored anywhere.${NC}"
echo ""

read -p "MySQL root username [root]: " mysql_root_user
mysql_root_user=${mysql_root_user:-root}

# Read password securely (no echo)
read -s -p "MySQL root password: " mysql_root_pass
echo ""

# Test root connection
echo ""
echo -e "${BLUE}Testing MySQL connection...${NC}"
if ! mysql -u "$mysql_root_user" -p"$mysql_root_pass" -e "SELECT 1;" &>/dev/null; then
    echo -e "${RED}Error: Could not connect to MySQL with provided credentials.${NC}"
    exit 1
fi
echo -e "${GREEN}✓ MySQL connection successful${NC}"

# Check if database already exists
db_exists=$(mysql -u "$mysql_root_user" -p"$mysql_root_pass" -N -e "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME='$db_name';" 2>/dev/null)

if [ -n "$db_exists" ]; then
    echo -e "${YELLOW}Warning: Database '$db_name' already exists.${NC}"
    read -p "Drop and recreate? This will DELETE ALL DATA! [y/N]: " drop_db
    if [[ "$drop_db" =~ ^[Yy]$ ]]; then
        echo -e "${BLUE}Dropping existing database...${NC}"
        mysql -u "$mysql_root_user" -p"$mysql_root_pass" -e "DROP DATABASE \`$db_name\`;" 2>/dev/null
        echo -e "${GREEN}✓ Database dropped${NC}"
    else
        echo -e "${YELLOW}Keeping existing database. Will only update user password.${NC}"
    fi
fi

# Create database
echo -e "${BLUE}Creating database '$db_name'...${NC}"
mysql -u "$mysql_root_user" -p"$mysql_root_pass" -e "CREATE DATABASE IF NOT EXISTS \`$db_name\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
echo -e "${GREEN}✓ Database created${NC}"

# Check if user exists
user_exists=$(mysql -u "$mysql_root_user" -p"$mysql_root_pass" -N -e "SELECT User FROM mysql.user WHERE User='$db_user' AND Host='localhost';" 2>/dev/null)

if [ -n "$user_exists" ]; then
    echo -e "${BLUE}Updating existing user '$db_user'...${NC}"
    mysql -u "$mysql_root_user" -p"$mysql_root_pass" -e "ALTER USER '$db_user'@'localhost' IDENTIFIED BY '$db_password';" 2>/dev/null
else
    echo -e "${BLUE}Creating user '$db_user'...${NC}"
    mysql -u "$mysql_root_user" -p"$mysql_root_pass" -e "CREATE USER '$db_user'@'localhost' IDENTIFIED BY '$db_password';" 2>/dev/null
fi
echo -e "${GREEN}✓ User configured${NC}"

# Grant privileges
echo -e "${BLUE}Granting privileges...${NC}"
mysql -u "$mysql_root_user" -p"$mysql_root_pass" -e "GRANT ALL PRIVILEGES ON \`$db_name\`.* TO '$db_user'@'localhost'; FLUSH PRIVILEGES;" 2>/dev/null
echo -e "${GREEN}✓ Privileges granted${NC}"

# Update .env file
echo -e "${BLUE}Updating .env file...${NC}"
sed -i "s/^DB_DATABASE=.*/DB_DATABASE=$db_name/" "$ENV_FILE"
sed -i "s/^DB_USERNAME=.*/DB_USERNAME=$db_user/" "$ENV_FILE"
sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=$db_password/" "$ENV_FILE"
echo -e "${GREEN}✓ .env file updated${NC}"

# Test new connection
echo -e "${BLUE}Testing application database connection...${NC}"
if mysql -u "$db_user" -p"$db_password" -e "SELECT 1;" "$db_name" &>/dev/null; then
    echo -e "${GREEN}✓ Application can connect to database${NC}"
else
    echo -e "${RED}Error: Application cannot connect with new credentials.${NC}"
    exit 1
fi

# Clear sensitive variables from memory
unset mysql_root_pass
unset db_password

echo ""
echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}  Database Setup Complete!${NC}"
echo -e "${GREEN}============================================${NC}"
echo ""
echo -e "Database: ${BLUE}$db_name${NC}"
echo -e "Username: ${BLUE}$db_user${NC}"
echo -e "Password: ${BLUE}(saved to .env)${NC}"
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo "1. Visit your site to run the web installer:"
echo "   https://your-domain.com/install.php"
echo ""
echo "2. The installer will:"
echo "   - Run database migrations"
echo "   - Create your admin account"
echo "   - Configure initial settings"
echo ""

