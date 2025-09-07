# Affiliate Portal WordPress Plugin

A complete affiliate user registration and management system for WordPress with multi-step forms, user authentication, and dashboard functionality.

## Features

- **5-Step Registration Form** with all 21 affiliate database fields
- **Secure User Authentication** with WordPress-standard password hashing
- **Responsive Dashboard** showing user account information
- **Custom Database Table** separate from WordPress users
- **AJAX-powered Forms** for smooth user experience
- **Bootstrap 5 Integration** for modern, responsive design
- **Shortcode System** for easy integration with any theme
- **Automatic Page Creation** for login, registration, and dashboard

## Installation

1. **Download the Plugin**
   - Compress the `affiliate-portal-plugin` folder into a ZIP file
   - Or download the ZIP file directly

2. **Install via WordPress Admin**
   - Go to `Plugins > Add New` in your WordPress admin
   - Click `Upload Plugin`
   - Select the ZIP file and click `Install Now`
   - Click `Activate Plugin`

3. **Automatic Setup**
   - The plugin automatically creates the database table upon activation
   - Three pages are created automatically:
     - Affiliate Login (`/affiliate-login/`)
     - Affiliate Registration (`/affiliate-register/`)
     - Affiliate Dashboard (`/affiliate-dashboard/`)

## Integration with Your Existing Site

### Method 1: Link to Auto-Created Pages
From your existing landing page login/register buttons, link to:
- Login Button: `/affiliate-login/`
- Register Button: `/affiliate-register/`

### Method 2: Use Shortcodes in Existing Pages
Add these shortcodes to any page or post:

```
[affiliate_login]     - Displays the login form
[affiliate_register]  - Displays the registration form
[affiliate_dashboard] - Displays the user dashboard
```

### Method 3: Custom Integration
Use the shortcodes in your theme template files:
```php
<?php echo do_shortcode('[affiliate_login]'); ?>
<?php echo do_shortcode('[affiliate_register]'); ?>
<?php echo do_shortcode('[affiliate_dashboard]'); ?>
```

## Database Fields

The plugin creates a custom table `wp_affiliate_users` with these 21 fields:

### Step 1 - Credentials
- `username` - Unique username
- `password` - Hashed password
- `security_que` - Security question
- `security_ans` - Security answer

### Step 2 - Personal Information
- `name_prefix` - Mr/Ms/Dr etc.
- `first_name` - First name
- `last_name` - Last name
- `dob` - Date of birth
- `type` - Individual/Business

### Step 3 - Contact Details
- `email` - Email address (unique)
- `company_name` - Company name
- `country_code` - Phone country code
- `mobile_number` - Mobile number

### Step 4 - Address Information
- `address_line1` - Address line 1
- `address_line2` - Address line 2
- `city` - City
- `country` - Country
- `zipcode` - ZIP/Postal code

### Step 5 - Business Information
- `chat_id_channel` - Chat/Channel ID
- `affiliate_type` - Type of affiliate
- `currency` - Preferred currency

## Customization

### Styling
Edit `assets/style.css` to customize the appearance. The plugin uses Bootstrap 5 classes with custom affiliate-prefixed classes for easy customization.

### Form Fields
Modify `templates/register-form.php` to add or remove form fields. Remember to update the database schema and AJAX handler accordingly.

### Logo
Replace the logo in the templates by editing the logo section in:
- `templates/login-form.php`
- `templates/register-form.php`
- `templates/dashboard.php`

## Security Features

- **Nonce Verification** for all AJAX requests
- **Data Sanitization** for all user inputs
- **WordPress Password Hashing** for secure password storage
- **SQL Injection Protection** using prepared statements
- **Session Management** for user authentication

## Theme Compatibility

This plugin is designed to work with any WordPress theme, including your existing Fota WP theme. The Bootstrap CSS is loaded only for plugin forms and won't conflict with your theme styles.

## Support & Customization

For additional customization or support:
1. Edit the template files in the `templates/` directory
2. Modify the CSS in `assets/style.css`
3. Update JavaScript functionality in `assets/script.js`

## File Structure

```
affiliate-portal-plugin/
├── affiliate-portal.php      # Main plugin file
├── README.md                 # This documentation
├── assets/
│   ├── style.css            # Plugin CSS styles
│   └── script.js            # Plugin JavaScript
└── templates/
    ├── login-form.php       # Login form template
    ├── register-form.php    # Registration form template
    └── dashboard.php        # Dashboard template
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

## License

GPL v2 or later# GemAffiliate
