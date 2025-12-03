# LeadsFire Click Tracker

A self-hosted, open-source click tracking and campaign management system designed for performance marketers. Built with ML-ready architecture for future AI-powered optimization.

![License](https://img.shields.io/badge/license-GPL--3.0-blue.svg)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-purple.svg)
![MariaDB](https://img.shields.io/badge/MariaDB-10.5%2B-orange.svg)

## 🔥 Features

### Core Features (MVP)
- **Campaign Management** - Create, edit, clone, and organize campaigns
- **Click Tracking** - High-performance click tracking with sub-second response times
- **Conversion Tracking** - Postback URL and pixel-based conversion tracking
- **Real-time Stats** - Live dashboard with key performance metrics
- **Traffic Source Integration** - Track and attribute traffic from any source
- **Landing Page & Offer Rotation** - Probabilistic and exact rotation algorithms
- **Bot Filtering** - Built-in bot detection to keep your stats clean
- **GeoIP Detection** - Automatic visitor location detection

### Security
- HTTPS enforcement
- CSRF protection
- SQL injection prevention
- XSS protection
- IP-based admin access restriction
- Bcrypt password hashing
- Rate limiting on login attempts

### Future Features (Roadmap)
- 🤖 **AI/ML Integration** - Google Cloud ML, TensorFlow for predictive analytics
- 💬 **AI Chatbot** - Natural language queries on your campaign data
- 📊 **Smart Rotation** - ML-powered LP/Offer optimization
- 🔍 **Anomaly Detection** - Automatic fraud and bot pattern detection
- 👥 **Multi-user Support** - Team collaboration features
- 🌍 **Multi-currency** - Support for multiple currencies

## 📋 Requirements

- **PHP** 8.0 or higher
- **MariaDB** 10.5+ or MySQL 8.0+
- **Nginx** or Apache
- **PHP Extensions**: PDO, pdo_mysql, curl, json, mbstring, openssl

### Recommended
- 2GB RAM minimum
- 10GB disk space
- SSL certificate for tracking domains

## 🚀 Quick Start

### 1. Clone the Repository

```bash
git clone https://github.com/crshdn/leadsfire.git
cd leadsfire
```

### 2. Install Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

### 3. Configure Web Server

Point your web server's document root to the `public/` directory.

**Nginx Example:**
```nginx
server {
    listen 443 ssl http2;
    server_name admin.yourdomain.com;
    root /var/www/leadsfire/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 4. Run Installation Wizard

Navigate to `https://admin.yourdomain.com/install.php` and follow the setup wizard.

## 📁 Directory Structure

```
leadsfire/
├── public/              # Web root (admin panel)
│   ├── index.php       # Main entry point
│   ├── install.php     # Installation wizard
│   └── assets/         # CSS, JS, images
├── tracker/             # Click tracking endpoints
│   └── c/              # Path-based tracking
├── src/                 # Application code
│   ├── Controllers/
│   ├── Models/
│   ├── Views/
│   ├── Services/
│   └── Helpers/
├── config/              # Configuration files
├── database/            # Schema and migrations
├── storage/             # Logs, cache, sessions
├── vendor/              # Composer dependencies
├── .env.example         # Environment template
└── composer.json
```

## ⚙️ Configuration

Copy `.env.example` to `.env` and configure your settings:

```bash
cp .env.example .env
chmod 600 .env
```

Key settings:
- `DB_*` - Database connection
- `APP_URL` - Your admin panel URL
- `ALLOWED_IPS` - Restrict admin access to specific IPs
- `MAIL_*` - Email notifications

## 🔗 Tracking URLs

**Click URL Format:**
```
https://tracking-domain.com/c/CAMPAIGN_KEY
```

**Postback URL Format:**
```
https://admin-domain.com/postback?subid={subid}&revenue={payout}
```

## 🛠️ Development

### Running Tests

```bash
./vendor/bin/phpunit
```

### Code Style

Follow PSR-12 coding standards.

## 📄 License

This project is licensed under the GPL-3.0 License - see the [LICENSE](LICENSE) file for details.

## 🤝 Contributing

Contributions are welcome! Please read our contributing guidelines before submitting PRs.

## 📞 Support

- **Issues**: [GitHub Issues](https://github.com/crshdn/leadsfire/issues)
- **Documentation**: Coming soon

---

Built with 🔥 by the LeadsFire team

