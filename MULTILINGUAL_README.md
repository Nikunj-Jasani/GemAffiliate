# Affiliate Portal - Multilingual WordPress Plugin

## Overview
Complete affiliate management WordPress plugin with full English and Portuguese support. Features comprehensive registration, dashboard, and admin management systems with language-specific pages and navigation.

## Supported Languages
- **English** (en_US) - Default
- **Portuguese (Brazil)** (pt_BR) - Complete translation

## Features

### Multilingual Support
- Separate page sets for each language
- Language-specific shortcodes
- Localized JavaScript and validation
- Country/state data with Portuguese translations
- Email notifications in appropriate language

### English Pages
- `/affiliate-login/` - User login
- `/affiliate-registration/` - Multi-step registration  
- `/affiliate-dashboard/` - User dashboard
- `/admin-login/` - Admin login
- `/admin-dashboard/` - Admin management panel

### Portuguese Pages  
- `/afiliado-login/` - Login do usuário
- `/afiliado-registro/` - Registro em múltiplas etapas
- `/painel-afiliado/` - Painel do usuário
- `/admin-login-pt/` - Login do administrador
- `/painel-admin/` - Painel de gerenciamento

## Language Switching
Each page includes a language switcher in the header, allowing users to switch between English and Portuguese versions seamlessly.

## Installation

1. Upload the `affiliate-portal-plugin` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Pages will be automatically created in both languages
4. Configure email settings through the admin dashboard

## Shortcodes

### English Shortcodes
- `[affiliate_login]` - Login form
- `[affiliate_register]` - Registration form  
- `[affiliate_dashboard]` - User dashboard
- `[affiliate_admin_login]` - Admin login
- `[affiliate_admin_dashboard]` - Admin dashboard

### Portuguese Shortcodes
- `[affiliate_login_pt]` - Formulário de login
- `[affiliate_register_pt]` - Formulário de registro
- `[affiliate_dashboard_pt]` - Painel do usuário
- `[affiliate_admin_login_pt]` - Login do admin
- `[affiliate_admin_dashboard_pt]` - Painel do admin

## Database Tables

The plugin creates the following tables:
- `wp_affiliate_users` - User registration data (21 fields)
- `wp_affiliate_admin` - Admin authentication
- `wp_affiliate_email_config` - Email notification settings

## Email Configuration

### Default Settings
- **From Email:** noreply@gem-affiliates.com
- **Contact Email:** office@gemmagics.com
- **Language:** Automatic based on user's registration page

### KYC Process
- Excel form download available in user dashboard
- Users submit completed forms to office@gemmagics.com
- Available in both English and Portuguese

## Country & State Coverage

Comprehensive coverage of 39 countries with 1,076 total administrative divisions:

### Americas
- Brazil (27 states) - Full Portuguese state names
- United States (50 states + DC)
- Canada (13 provinces/territories)
- Chile, Argentina, Mexico

### Europe  
- Portugal, Spain, France, Germany, Italy
- United Kingdom, Netherlands, Belgium
- Switzerland, Austria, Sweden, Norway
- Denmark, Finland, Poland, Czech Republic
- Hungary, Romania, Greece, Ireland

### Asia-Pacific
- Australia, New Zealand, Singapore
- Malaysia, Thailand, Philippines, Indonesia, Vietnam
- Japan, China, India, Israel, UAE, Saudi Arabia

### Africa
- South Africa, Egypt, Turkey

## Technical Implementation

### File Structure
```
affiliate-portal-plugin/
├── affiliate-portal.php          # Main plugin file
├── assets/
│   ├── script.js                 # English JavaScript
│   ├── script-pt.js              # Portuguese JavaScript  
│   ├── style.css                 # Shared styles
│   ├── countries-states.json     # Country/state data
│   └── KYC_Form.xlsx            # KYC documentation
├── templates/
│   ├── login-form.php            # English templates
│   ├── register-form.php
│   ├── dashboard-enhanced.php
│   ├── admin-login.php
│   ├── admin-dashboard.php
│   └── pt/                      # Portuguese templates
│       ├── login-form.php
│       ├── register-form.php
│       ├── dashboard-enhanced.php
│       ├── admin-login.php
│       └── admin-dashboard.php
└── languages/
    ├── affiliate-portal-pt_BR.po  # Translation source
    └── affiliate-portal-pt_BR.mo  # Compiled translations
```

### Language Detection
The plugin automatically detects language based on:
1. Current page slug (Portuguese pages start with "afiliado-" or "painel-")
2. Loads appropriate JavaScript file (script.js vs script-pt.js)
3. Uses correct template directory (templates/ vs templates/pt/)

### JavaScript Localization
- Portuguese script includes all UI text in Portuguese
- Form validation messages in Portuguese
- Country names translated to Portuguese
- Date/currency formatting for Brazilian locale

## Admin Features

### Application Management
- View all registrations with filtering
- Approve/reject applications with remarks
- Status change email notifications
- Export capabilities
- Pagination for large datasets

### Email Configuration
- Configure notification recipients
- Set from address and name
- Test email functionality
- Multi-recipient support

### Statistics Dashboard
- Total applications counter
- Status breakdown (pending/approved/rejected)
- Real-time updates
- Visual status indicators

## User Registration Process

### 5-Step Multi-Step Form
1. **Account Credentials** - Username, password, security question
2. **Personal Information** - Name, date of birth, type
3. **Contact Information** - Email, phone, company
4. **Address Information** - Full address with country/state
5. **Business Information** - Affiliate type, currency, chat ID

### Validation
- Client-side validation in appropriate language
- Server-side security validation
- Password strength requirements
- Email format verification
- Required field validation

## Security Features

### User Authentication
- WordPress-compatible password hashing
- Session management with fallbacks
- Secure cookie handling
- CSRF protection with nonces

### Admin Authentication  
- Separate admin user system
- Role-based access control
- Session timeout handling
- Login attempt logging

### Data Protection
- SQL injection prevention
- XSS protection
- Input sanitization
- File upload restrictions

## Browser Compatibility
- Chrome 80+
- Firefox 75+
- Safari 13+
- Edge 80+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Performance Optimization
- Lazy loading of country/state data
- Efficient AJAX pagination
- Minimized database queries
- Optimized asset loading
- CDN-ready structure

## Support & Documentation
- Comprehensive error logging
- Debug mode available
- Admin settings panel
- Built-in troubleshooting tools

## Version History
- v1.3.0 - Full Portuguese language support added
- v1.2.0 - Enhanced country/state coverage (39 countries)
- v1.1.0 - Admin dashboard and email notifications
- v1.0.0 - Initial release with basic functionality

## License
GPL v2 or later

## Contact
For support and customization:
- Email: office@gemmagics.com
- Plugin Portal: Affiliate Management System