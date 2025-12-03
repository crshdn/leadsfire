# Changelog

All notable changes to LeadsFire Click Tracker will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [0.0.1] - 2024-12-03

### 🎉 Initial Release - MVP Foundation

This is the first development release of LeadsFire Click Tracker, establishing the foundation for a CPVLab-compatible click tracking system.

### Added

#### Core Infrastructure
- Project structure with PSR-4 autoloading
- Composer dependency management
- Environment-based configuration (`.env`)
- Secure file permissions system
- Nginx configuration templates

#### Installation & Setup
- Web-based setup wizard (`install.php`)
- CLI database setup script (`scripts/setup-database.sh`)
- Automated permission configuration (`scripts/set-permissions.sh`)
- CPVLab database import compatibility
- Comprehensive error handling with user-friendly messages

#### Authentication & Security
- Secure login system with bcrypt password hashing
- PHP session management with secure cookie settings
- CSRF protection on all forms
- IP-based admin access restriction
- Rate limiting on login attempts (5 attempts, 15-minute lockout)
- Secure headers (X-Frame-Options, X-Content-Type-Options, etc.)

#### Dashboard
- Real-time statistics display
- Today's clicks, conversions, revenue overview
- ECharts integration for performance graphs
- Quick stats cards with trend indicators

#### Campaign Management
- Campaign list view with search and filtering
- Campaign status indicators (active/paused)
- Traffic source association display
- Basic campaign statistics (views, clicks, conversions, revenue)
- CPVLab schema compatibility (KeyCode, CreateDate, Inactive columns)

#### Traffic Sources
- Traffic source list view
- Card-based UI with parameter display
- SubID, Keyword, Cost parameter configuration
- Add/Edit modal (API endpoints pending)

#### Affiliate Networks
- Affiliate network list view
- Postback URL configuration display
- Add/Edit modal (API endpoints pending)

#### Offers
- Offer management view
- Payout display with currency formatting
- Affiliate network association
- URL preview with truncation
- CPVLab schema compatibility (OfferName, OfferUrl, PredefOfferID columns)

#### Landing Pages
- Landing page management view
- URL display with truncation
- CPVLab schema compatibility (LpName, LpUrl, PredefLpID columns)

#### Click Tracking
- Basic click tracking endpoint (`/c/{campaign_key}`)
- Click data capture (IP, User Agent, Referrer)
- GeoIP integration via ip-api.com
- Redirect handling

#### Conversion Tracking
- Postback endpoint (`/postback.php`)
- CPVLab placeholder support ({subid}, {payout}, {txid}, etc.)

#### UI/UX
- Dark glassmorphism theme with fire accent colors
- Responsive Bootstrap 5 layout
- Sidebar navigation
- Flash message system
- Loading states and animations
- Mobile-friendly design

#### Documentation
- README.md with project overview
- INSTALL.md with step-by-step setup guide
- SPEC.md with full technical specification
- GPL-3.0 open-source license

### Database

#### Supported Tables (CPVLab Compatible)
- `campaigns` - Campaign configuration
- `clicks` - Click tracking data
- `subids` - Keyword/SubID tracking
- `destinations` - Landing pages and offers per campaign
- `cpvsources` - Traffic sources
- `affiliatesources` - Affiliate networks
- `predeflps` - Landing page library
- `predefoffers` - Offer library
- `users` - User authentication
- `config` - System settings

### Security Notes
- `.env` file secured with 640 permissions
- Storage directories with 770 permissions
- Sensitive paths blocked in Nginx
- No hardcoded credentials
- Proxy IP forwarding support (X-Real-IP, X-Forwarded-For)

### Known Issues
- API endpoints for CRUD operations not yet implemented
- Campaign edit page not yet complete
- Stats calculation needs optimization
- Bot detection not yet implemented

### Technical Debt
- Need to add unit tests
- Need to implement proper API versioning
- Landing page/offer rotation logic needs completion
- Email notification system not yet connected

---

## Roadmap

### [0.0.2] - Planned
- Complete campaign CRUD operations
- Campaign edit page
- API endpoints for all entities
- Improved stats calculation

### [0.0.3] - Planned
- Full click tracking with all redirect types
- Bot detection implementation
- Landing page rotation (probabilistic)
- Offer rotation

### [0.1.0] - Planned
- Complete MVP feature set
- Full reporting with ECharts
- Data export (CSV)
- Email notifications

### [1.0.0] - Future
- Multi-user support
- ML/AI integration readiness
- Advanced reporting
- Campaign cloning
- Bulk operations

---

## Contributors

- LeadsFire Development Team

## License

This project is licensed under the GPL-3.0 License - see the [LICENSE](LICENSE) file for details.

