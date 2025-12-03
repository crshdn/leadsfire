# LeadsFire Click Tracker - Technical Specification

## Project Overview

A self-hosted, open-source click tracking and campaign management system, designed as an improved clone of CPVLab Pro. This system will support importing existing CPVLab database backups and provide full campaign management, click tracking, and conversion tracking capabilities.

**This is an open-source project designed to be easily installed on any server.**

### Your Installation
- **Primary Domain:** admin.qote.us (admin panel)  
- **Static Site:** qote.us (separate static website)  
- **Server:** Same server as current installation

---

## Technology Stack

| Component | Technology | Version |
|-----------|------------|---------|
| **Backend** | PHP | 8.2+ |
| **Frontend** | Bootstrap 5 + Vanilla JS | Latest |
| **Database** | MariaDB | 10.11+ |
| **Web Server** | Nginx | Any modern |
| **Charts** | ECharts | Latest |
| **Email** | Mailgun API (configurable) | - |

---

## Open Source Requirements

### Easy Installation
- Single setup wizard handles all configuration
- No hardcoded values - everything configurable
- Works on any domain
- Clear installation documentation
- Minimal server requirements

### Setup Wizard Configures:
1. Database connection (host, name, user, password)
2. Admin account (username, password, email)
3. Application URL/domain
4. Timezone selection
5. IP whitelist for admin access
6. Email notification settings (optional)
7. SSL certificate setup guidance

### Distributable Package
- Self-contained PHP application
- Composer for dependency management
- Database migrations/schema creation
- Sample .env.example file
- Installation guide (INSTALL.md)
- README.md with features and requirements

---

## Database Configuration

- **Database Name:** User-configured during setup
- **Character Set:** utf8mb4
- **Collation:** utf8mb4_general_ci
- **Default for your install:** `click_tracker`

---

## Database Structure

The system will use the existing CPVLab database schema (126 tables). Key tables for MVP:

### Core Campaign Tables
- `campaigns` - Main campaign configuration
- `campaignlevels` - Multi-level landing page funnels
- `campaignoptions` - Offer option groups
- `campaigntypes` - Campaign types (LPS, DLLP, DLLPO, MO)
- `campaigngroups` - Campaign organization
- `campaignstokens` - Token configuration per campaign

### Tracking Tables
- `clicks` - Main click tracking (IsDup flag for duplicates)
- `clicksextra`, `clicksips`, `clickslp` - Extended click data
- `subids` - Keyword/SubID tracking
- `destinations` - Landing pages and offers per campaign

### Supporting Tables
- `cpvsources` - Traffic sources with token templates
- `affiliatesources` - Affiliate networks for postback configuration
- `predeflps` - Predefined landing pages library
- `predefoffers` - Predefined offers library
- `customdomains` - Tracking domains
- `users` - User authentication
- `config` - System configuration key/value pairs

---

## Authentication & Security

### Authentication
- **Method:** Simple username/password
- **Users:** Single user (with architecture to support multi-user later)
- **Sessions:** PHP sessions with secure cookie settings
- **Session Timeout:** 24 hours (86400 seconds) - configurable
- **Password Storage:** bcrypt hashing
- **First Run:** Setup wizard to create initial admin account

### Security Requirements
- HTTPS enforced on all endpoints
- CSRF protection on all forms
- SQL injection prevention via prepared statements
- XSS prevention via output escaping
- Rate limiting on login attempts
- Secure file permissions (600 for sensitive files like .env)

### IP Restriction
- Configurable during setup and via config file
- **Your IPs:** 23.17.197.227, 10.8.0.2 (VPN)
- **Management:** Via application config file (easy to update)

### API Authentication (Future)
- Architecture will support adding API keys/JWT later
- No API authentication in MVP

---

## Domain & URL Structure

### Admin Panel
- **URL:** `https://[your-domain]/` or `https://admin.[your-domain]/`
- **Access:** IP restricted + username/password authentication

### Tracking URLs
- **Format:** `https://[tracking-domain]/c/[CAMPAIGN_KEY]`
- **Example:** `https://click.example.com/c/abc123xyz`

---

## UI Configuration

### Theme
- **Style:** Dark, glassy, modern
- **Primary Color:** Black/dark background
- **Accent Colors:** To be determined (clean and modern)

### Date/Time Format
- **Format:** US Format (MM/DD/YYYY 12:00 AM/PM)
- **Timezone:** User-configured during setup

### Default View
- **Login Landing Page:** Dashboard with overall stats

### Responsiveness
- Desktop-first design
- Mobile should work but not critical priority

---

## MVP Features

### Phase 1: Core Infrastructure
1. Setup wizard for first-run configuration
2. Database schema creation/migration
3. User authentication system
4. Base application structure
5. Database import from CPVLab backup (UI feature)

### Phase 2: Campaign Management

#### Campaign List Page
- Summary stats cards (Today/Yesterday/This Week/This Month)
- Filters (Campaign Type, Groups, Traffic Sources, Date Range)
- Column selector
- Search functionality
- Bulk actions:
  - Delete multiple campaigns
  - Activate/Deactivate multiple campaigns
  - Change group for multiple campaigns
  - Export selected campaigns
- Quick action icons (stats, edit, clone, delete)

#### Campaign Edit Page
- General Settings section (including Failure Page URL)
- Tracking Settings section (Data Options, Redirect Type, Lead Capture)
- Macros & Tokens section
- Landing Pages section (multi-level, share percentage, rotation)
- Offers section (multi-option, share percentage, rotation)
- Campaign Notes section
- Links & Pixels section

#### Campaign Cloning
- Clone all settings
- Clone landing pages and offers
- Clone token configurations
- Clone notes
- Reset stats to zero

#### Campaign Types (All in MVP)
- **LPS** - Landing Page Sequence
- **DLLP** - Direct Link Landing Page  
- **DLLPO** - Direct Link Landing Page with Options
- **MO** - Multiple Offers

### Phase 3: Click Tracking

#### Redirect Types
- Direct Redirect (send)
- Double Meta Refresh (hide)
- Redirect Loop (hide)

#### Data Capture
- IP Address (with EU/California privacy options)
- User Agent
- Referrer
- GeoIP (Country, Region, City) - via ip-api.com (pluggable architecture)
- ISP detection
- Device/Browser/OS detection
- **Direct Traffic:** Log everything (including visits without tracking params)

#### Bot Filtering
- Bot detection and blocking (MVP feature)
- Configurable bot rules
- Option to capture cost as $0 for bot traffic
- **Prefetch Requests:** Ignore (cleaner stats)

#### Click Deduplication (CPVLab Compatible)
- Configurable via `SecondsIgnoreDuplicateClicks` setting
- Default: 0 (disabled)
- IsDup flag stored in clicks table

#### Performance Requirements
- Handle burst traffic efficiently
- Target: 10k+ clicks per day
- Low latency redirects (<50ms processing)

### Phase 4: Conversion Tracking
- S2S Postback URLs
- Single conversion event per click
- Revenue tracking
- **Attribution Window:** 30 days

### Phase 5: Reporting & Stats
- Basic stats (views, clicks, conversions, revenue, cost, profit, ROI)
- Drill-down by date
- Drill-down by traffic source/keyword
- Graphic performance charts (ECharts)
- Target Performance table
- Ad Performance table
- **Caching Strategy:** Hybrid (real-time for today, cached for historical)

---

## Campaign Alerts (MVP - Basic)

### Configurable Per Campaign
- EPC (Earnings Per Click) threshold alerts
- CVR (Conversion Rate) threshold alerts
- Basic alert system for MVP, expandable later

### Delivery
- Email notifications (configurable provider)
- In-app notifications

---

## Landing Page & Offer Rotation

### Rotation Algorithms (All Available as Options)
1. **Exact Rotation (Method 1):** Get the exact share% defined in campaign (slower)
2. **Probabilistic Rotation (Method 2):** Follow the share% in a probabilistic way (faster) - Default
3. **Adaptive (Future):** Adjusts based on performance

---

## Cookie Configuration

| Setting | Value |
|---------|-------|
| Cookie Timeout | 30 days (2592000 seconds) |
| SameSite | None (for cross-domain tracking) |
| Secure | true |
| Root Domain | true |

---

## Postback URL Placeholders (CPVLab Compatible)

| Placeholder | Description |
|-------------|-------------|
| `{subid}` or `{!subid!}` | Click/SubID |
| `{campaignid}` | Campaign ID |
| `{payout}` | Revenue amount |
| `{revenue}` | Revenue amount (alias) |
| `{txid}` or `{transaction_id}` | Transaction ID |
| `{status}` | Conversion status |
| `{aff_sub}` | Affiliate SubID |
| `{tscode}` | Traffic source code |
| `{your_offer_url}` | Base offer URL |
| Custom fields 1-5 | Network-specific fields |

---

## Supporting Features (MVP)

### Traffic Sources Management
- Add/Edit/Delete traffic sources
- Bidding type (CPV, CPC, CPM)
- Timezone configuration
- Currency support (USD)
- Token configuration (Keyword, Cost, External ID, Ad, Custom tokens)
- Postback URL templates

### Offer Sources (Affiliate Networks)
- Add/Edit/Delete offer sources
- Offer URL templates with placeholders
- SubID configuration
- Revenue/Status/Transaction placeholders
- Custom field placeholders (5 fields)
- Postback URL configuration

### Landing Pages Library
- Add/Edit/Delete predefined landing pages
- Name, URL, Notes
- Group organization
- Active/Inactive toggle
- Bulk import via CSV

### Offers Library
- Add/Edit/Delete predefined offers
- Name, URL, Payout, Notes
- Source/Network association
- Group organization
- Active/Inactive toggle
- Bulk import via CSV

### Custom Domains
- Add/Edit/Delete tracking domains
- SSL status verification
- User assignment (for future multi-user)
- Domain testing functionality

### Data Export (MVP)
- Export clicks to CSV
- Export campaigns to CSV
- Export stats/reports to CSV

### Notifications (MVP)
- Campaign alerts (EPC, CVR thresholds)
- Conversion notifications
- System errors
- **Delivery:** Email (configurable) + In-app notifications

---

## Features NOT in MVP (Future)

- AI Smart Rotation (Google Cloud ML)
- Landing Page Protection
- Backup/Restore from UI
- Multi-currency support
- API authentication
- Multi-user support

---

## 🔥 CORE FUTURE FEATURE: AI/ML Integration

**This is the heart of the application and its key differentiator.**

### Google Cloud ML Integration (Post-MVP)
The architecture MUST support future integration with:

#### TensorFlow / Vertex AI
- **Predictive Analytics:** Predict conversion probability for traffic segments
- **Smart Rotation:** ML-powered LP/Offer rotation based on predicted performance
- **Anomaly Detection:** Detect fraud, bot patterns, unusual traffic
- **Audience Segmentation:** Auto-cluster visitors by behavior patterns

#### Data Pipeline Architecture
- All click/conversion data stored in ML-ready format
- Export capabilities to BigQuery/Cloud Storage
- Real-time data streaming support (future)
- Feature engineering columns in database schema

#### AI Chatbot Integration
- Train on campaign performance data
- Natural language queries: "What's my best performing offer this week?"
- Recommendations: "Which traffic source should I scale?"
- Anomaly alerts: "Campaign X has unusual drop in CVR"
- Integration points: OpenAI API, Google Dialogflow, or custom LLM

### Architecture Considerations for ML-Ready Design
1. **Normalized data schema** - Clean, consistent data for training
2. **Timestamp precision** - Millisecond timestamps for time-series analysis
3. **Feature columns** - Pre-calculated metrics stored for ML input
4. **Data export API** - Easy extraction to ML pipelines
5. **Webhook system** - Real-time event streaming capability
6. **Modular service layer** - Easy to plug in ML services later

---

## GeoIP Architecture

**Primary Provider:** ip-api.com (no API key required)
- Endpoint: `http://ip-api.com/json/{ip}`
- Returns: Country, Region, City, ISP, Timezone, etc.

**Pluggable Design:** Architecture will support adding additional providers:
- MaxMind GeoIP2
- IP2Location
- ipinfo.io
- Custom providers

---

## Logging Configuration

- **Default Level:** Verbose (all events logged)
- **Configurable via:** `.env` file (`LOG_LEVEL=verbose|standard|minimal`)
- **Log Location:** `storage/logs/`

---

## Error Handling

### Failed Redirects
- Each campaign has a configurable Failure Page URL
- If no failure page configured, show generic error
- Log all failures for debugging

---

## Data Retention

- **Click Data:** 2 years retention
- **Archival Strategy:** Automatic deletion after 2 years
- **Backup:** Not in MVP, will add later (manual + scheduled + restore)

---

## File Structure

```
leadsfire-click-tracker/
├── public/                  # Web root
│   ├── index.php           # Entry point
│   ├── install.php         # Installation/setup wizard
│   ├── assets/             # CSS, JS, images
│   │   ├── css/
│   │   ├── js/
│   │   └── img/
│   └── .htaccess
├── tracker/                 # Tracking endpoints (can be same or different domain)
│   ├── c/                  # Path-based tracking
│   │   └── index.php       # Click handler
│   ├── base.php            # Legacy compatibility
│   ├── base2.php           # Redirect to next level
│   ├── base3.php           # Redirect to offer
│   ├── adclick.php         # Postback/pixel handler
│   └── adsub.php           # Subscriber tracking
├── src/                     # Application code
│   ├── Controllers/
│   ├── Models/
│   ├── Views/
│   ├── Services/
│   │   ├── ClickTracker/
│   │   ├── GeoIP/
│   │   ├── BotDetection/
│   │   ├── Email/
│   │   └── DeviceDetection/
│   ├── Helpers/
│   └── Installer/          # Setup wizard logic
├── config/                  # Configuration files
│   ├── database.php
│   ├── app.php
│   └── routes.php
├── database/                # Database files
│   ├── schema.sql          # Full schema for fresh install
│   └── migrations/         # Version migrations
├── storage/                 # Logs, cache, uploads (writable)
│   ├── logs/
│   ├── cache/
│   ├── sessions/
│   └── uploads/
├── vendor/                  # Composer dependencies
├── .env.example            # Sample environment file
├── .env                    # Environment variables (created during setup)
├── composer.json
├── README.md               # Project overview and features
├── INSTALL.md              # Installation instructions
├── LICENSE                 # Open source license
└── CHANGELOG.md            # Version history
```

---

## Environment Variables (.env.example)

```env
# Application
APP_NAME="LeadsFire Click Tracker"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_TIMEZONE=America/New_York

# Database
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=click_tracker
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Session
SESSION_LIFETIME=86400

# Logging
LOG_LEVEL=verbose

# Email (Optional - Mailgun)
MAIL_DRIVER=mailgun
MAILGUN_DOMAIN=
MAILGUN_API_KEY=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="LeadsFire Click Tracker"
MAIL_TO_ADDRESS=

# Email (Optional - SMTP)
# MAIL_DRIVER=smtp
# SMTP_HOST=
# SMTP_PORT=587
# SMTP_USERNAME=
# SMTP_PASSWORD=
# SMTP_ENCRYPTION=tls

# GeoIP
GEOIP_PROVIDER=ip-api

# Security
ALLOWED_IPS=127.0.0.1,::1
```

---

## Server Requirements

### Minimum Requirements
- PHP 8.0+
- MariaDB 10.5+ or MySQL 5.7+
- Nginx or Apache
- 512MB RAM minimum
- 1GB disk space

### PHP Extensions Required
- PDO
- PDO_MySQL
- cURL
- JSON
- mbstring
- OpenSSL

### Recommended
- PHP 8.2+
- 2GB+ RAM for high traffic
- SSD storage
- PHP OPcache enabled

---

## Import Feature

### Supported Import
- Full CPVLab database backup (SQL format)
- All campaigns with configurations
- All click data
- All supporting data (traffic sources, offers, landing pages, etc.)

### Import Process
1. Upload SQL backup file via admin UI
2. Validate schema compatibility
3. Import in transaction
4. Verify data integrity
5. Report import results

---

## Confirmed Answers - All Rounds

| Question | Answer |
|----------|--------|
| AI Smart Rotation | Not for MVP. Will add later with Google Cloud ML. |
| Bot Filtering | Yes, include in MVP |
| Import Timing | UI feature (import anytime via admin panel) |
| GeoIP Provider | ip-api.com with pluggable architecture |
| Campaign Types | All 4 types in MVP |
| URL Structure | Path-based: `https://domain.com/c/CAMPAIGN_KEY` |
| SubID Format | Keep CPVLab format for compatibility |
| Postback Placeholders | Match CPVLab placeholders exactly |
| Rotation Algorithm | All options available (Exact, Probabilistic, Adaptive) |
| Click Deduplication | Match CPVLab (configurable seconds, default 0) |
| Conversion Attribution | 30 days |
| Currency | USD only |
| Stats Caching | Hybrid (real-time today, cached historical) |
| Failed Redirects | Campaign-specific failure page |
| Logging Level | Verbose (configurable via .env) |
| Admin Panel Access | IP restriction (configurable) |
| Mobile Responsiveness | Desktop-first, mobile should work but not critical |
| Data Export | Yes (clicks, campaigns, reports to CSV) |
| Backup/Restore | Not in MVP, add later |
| Notifications | Email + In-app |
| Email Service | Mailgun (configurable, credentials via .env) |
| IP Whitelist | Configurable via config file |
| Session Timeout | 24 hours |
| Date Format | US Format (MM/DD/YYYY 12:00 AM/PM) |
| Timezone | Configurable during setup |
| Default View | Dashboard with overall stats |
| Campaign Cloning | Clone everything except stats |
| Bulk Actions | All (delete, activate/deactivate, change group, export) |
| Alert Thresholds | Configurable per campaign (EPC, CVR focus for MVP) |
| Chart Library | ECharts |
| Database Name | User-configured (default: click_tracker) |
| First Run | Setup wizard |
| Cookie Config | Keep CPVLab defaults (30 days, SameSite=None, Secure) |
| Direct Traffic | Log everything |
| LP Protection | Not in MVP, add later |
| Prefetch Requests | Ignore (cleaner stats) |
| PHP Version | 8.2+ |
| MariaDB Version | 10.11+ |
| Database User | Create new user during setup |
| App Name | LeadsFire Click Tracker |
| Theme | Dark, glassy, modern |
| Distribution | Open source, easy install on any server |

---

## Interview Questions - Round 5 (Final Clarifications)

### Q1: Open Source License
What license should this project use?
- **MIT** (most permissive, allows commercial use)
- **GPL v3** (copyleft, derivatives must also be open source)
- **Apache 2.0** (permissive with patent protection)
- Other?

**Your Answer:** _[Please specify license]_
GPL v3
---

### Q2: GitHub Repository
Should I set this up as a GitHub repository from the start?
- Yes, create public repo now
- Yes, but private for now (make public later)
- No, just local development for now

**Your Answer:** _[Please specify]_
yes private for now
---

### Q3: Email Provider Flexibility
For the setup wizard, which email options should be available?
- Mailgun only
- Mailgun + SMTP
- Mailgun + SMTP + SendGrid
- All common options (Mailgun, SMTP, SendGrid, AWS SES, etc.)

**Your Answer:** _[Please specify]_
all common options
---

### Q4: Dark Theme Inspiration
For the dark/glassy theme, any specific inspiration or examples?
- Similar to CPVLab screenshots (dark purple)
- More like Discord/Slack dark mode
- Glassmorphism style (frosted glass effects)
- Just clean dark (like GitHub dark mode)
- Other reference?

**Your Answer:** _[Please specify or provide reference]_
Glassmorphism style with orange/fire color - dark oranges like fire
---

### Q5: Tracking Domain Setup
For the tracking domains (separate from admin), should the setup wizard:
- Guide user to set up in nginx manually (with instructions)
- Auto-detect if admin and tracker are same domain
- Support both same-domain and separate-domain setups

**Your Answer:** _[Please specify]_
- Support both same-domain and separate-domain setups

---

### Q6: Ready to Begin?
I think we have enough detail now. Any final additions before I start coding?

**Your Answer:** Ready to code. Key consideration: Architecture must support future Google Cloud ML/TensorFlow integration for predictive analytics, smart rotation, and AI chatbot trained on campaign data. This will be the heart of the application.

---

## Development Phases

### Phase 1: Foundation (Week 1)
- [ ] Project structure setup
- [ ] Composer dependencies
- [ ] Database schema from CPVLab
- [ ] Setup wizard (install.php)
- [ ] Basic authentication
- [ ] Dark theme UI framework

### Phase 2: Campaign Management (Week 2)
- [ ] Campaign list page
- [ ] Campaign edit page
- [ ] Campaign CRUD operations
- [ ] Landing pages & offers management
- [ ] Traffic sources management

### Phase 3: Click Tracking (Week 3)
- [ ] Click tracking endpoints
- [ ] Redirect logic (all types)
- [ ] GeoIP integration
- [ ] Bot detection
- [ ] Data capture

### Phase 4: Conversions & Stats (Week 4)
- [ ] Postback handling
- [ ] Stats calculation
- [ ] Dashboard
- [ ] Reports with ECharts
- [ ] Data export

### Phase 5: Polish & Documentation (Week 5)
- [ ] Notifications system
- [ ] CPVLab import feature
- [ ] Testing
- [ ] Documentation (README, INSTALL)
- [ ] Final polish

---

## Changelog

| Date | Version | Changes |
|------|---------|---------|
| 2024-12-03 | 0.1 | Initial specification draft |
| 2024-12-03 | 0.2 | Added Round 1 answers, Round 2 questions |
| 2024-12-03 | 0.3 | Added Round 2 answers, CPVLab config analysis, Round 3 questions |
| 2024-12-03 | 0.4 | Added Round 3 answers, Round 4 questions |
| 2024-12-03 | 0.5 | Added Round 4 answers, open-source requirements, Round 5 (final) |
