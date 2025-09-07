<?php
/**
 * Plugin Name: Affiliate Portal
 * Plugin URI: https://example.com/affiliate-portal
 * Description: Complete affiliate user registration and management system with multi-step forms and dashboard. Integrates seamlessly with existing WordPress themes.
 * Version: 1.3.0
 * Author: Your Name
 * License: GPL v2 or later
 * Text Domain: affiliate-portal
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('AFFILIATE_PORTAL_URL', plugin_dir_url(__FILE__));
define('AFFILIATE_PORTAL_PATH', plugin_dir_path(__FILE__));
define('AFFILIATE_PORTAL_VERSION', '1.3.0');

class AffiliatePortal {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        // Load text domain for translations
        $this->load_textdomain();
        
        // REMOVED: Session initialization to prevent global session sharing
        // Using ONLY cookie-based authentication for proper user isolation
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Add custom CSS for color customization
        add_action('wp_head', array($this, 'custom_css'));
        
        // Register shortcodes
        add_shortcode('affiliate_login', array($this, 'login_shortcode'));
        add_shortcode('affiliate_register', array($this, 'register_shortcode'));
        add_shortcode('affiliate_dashboard', array($this, 'dashboard_shortcode'));
        add_shortcode('affiliate_admin_login', array($this, 'admin_login_shortcode'));
        add_shortcode('affiliate_admin_dashboard', array($this, 'admin_dashboard_shortcode'));
        
        // Register Portuguese shortcodes
        add_shortcode('affiliate_login_pt', array($this, 'login_shortcode_pt'));
        add_shortcode('affiliate_register_pt', array($this, 'register_shortcode_pt'));
        add_shortcode('affiliate_dashboard_pt', array($this, 'dashboard_shortcode_pt'));
        add_shortcode('affiliate_admin_login_pt', array($this, 'admin_login_shortcode_pt'));
        add_shortcode('affiliate_admin_dashboard_pt', array($this, 'admin_dashboard_shortcode_pt'));
        
        // AJAX handlers
        add_action('wp_ajax_affiliate_register', array($this, 'handle_registration'));
        add_action('wp_ajax_nopriv_affiliate_register', array($this, 'handle_registration'));
        
        // AJAX handlers for country/state data
        add_action('wp_ajax_get_countries', array($this, 'get_countries'));
        add_action('wp_ajax_nopriv_get_countries', array($this, 'get_countries'));
        add_action('wp_ajax_get_states', array($this, 'get_states'));
        add_action('wp_ajax_nopriv_get_states', array($this, 'get_states'));
        add_action('wp_ajax_affiliate_login', array($this, 'handle_login'));
        add_action('wp_ajax_nopriv_affiliate_login', array($this, 'handle_login'));
        add_action('wp_ajax_affiliate_logout', array($this, 'handle_logout'));
        add_action('wp_ajax_affiliate_change_password', array($this, 'handle_change_password'));
        add_action('wp_ajax_affiliate_upload_company_docs', array($this, 'handle_company_documents'));
        add_action('wp_ajax_load_company_documents_form', array($this, 'load_company_documents_form'));
        
        // Admin AJAX handlers
        add_action('wp_ajax_affiliate_admin_login', array($this, 'handle_admin_login'));
        add_action('wp_ajax_nopriv_affiliate_admin_login', array($this, 'handle_admin_login'));
        add_action('wp_ajax_affiliate_admin_logout', array($this, 'handle_admin_logout'));
        add_action('wp_ajax_nopriv_affiliate_admin_logout', array($this, 'handle_admin_logout'));
        add_action('wp_ajax_affiliate_get_applications', array($this, 'get_admin_applications'));
        add_action('wp_ajax_nopriv_affiliate_get_applications', array($this, 'get_admin_applications'));
        add_action('wp_ajax_affiliate_admin_update_email_config', array($this, 'handle_admin_update_email_config'));
        add_action('wp_ajax_nopriv_affiliate_admin_update_email_config', array($this, 'handle_admin_update_email_config'));
        add_action('wp_ajax_affiliate_get_email_config', array($this, 'get_email_config'));
        add_action('wp_ajax_nopriv_affiliate_get_email_config', array($this, 'get_email_config'));
        add_action('wp_ajax_affiliate_update_status', array($this, 'update_application_status'));
        add_action('wp_ajax_nopriv_affiliate_update_status', array($this, 'update_application_status'));
        add_action('wp_ajax_affiliate_get_registration_details', array($this, 'handle_get_registration_details'));
        add_action('wp_ajax_nopriv_affiliate_get_registration_details', array($this, 'handle_get_registration_details'));
        
        // KYC AJAX handlers
        add_action('wp_ajax_affiliate_submit_kyc', array($this, 'handle_kyc_submission'));
        add_action('wp_ajax_nopriv_affiliate_submit_kyc', array($this, 'handle_kyc_submission'));
        
        // KYC Admin handling
        add_action('wp_ajax_affiliate_get_kyc_applications', array($this, 'get_kyc_applications'));
        add_action('wp_ajax_nopriv_affiliate_get_kyc_applications', array($this, 'get_kyc_applications'));
        add_action('wp_ajax_affiliate_get_kyc_details', array($this, 'get_kyc_details'));
        add_action('wp_ajax_nopriv_affiliate_get_kyc_details', array($this, 'get_kyc_details'));
        add_action('wp_ajax_affiliate_update_kyc_status', array($this, 'update_kyc_status'));
        add_action('wp_ajax_nopriv_affiliate_update_kyc_status', array($this, 'update_kyc_status'));
        
        // Enhanced KYC verification system
        add_action('wp_ajax_affiliate_get_kyc_verification_details', array($this, 'get_kyc_verification_details'));
        add_action('wp_ajax_nopriv_affiliate_get_kyc_verification_details', array($this, 'get_kyc_verification_details'));
        
        // Document approval/rejection system
        add_action('wp_ajax_affiliate_approve_document', array($this, 'handle_document_approval'));
        add_action('wp_ajax_nopriv_affiliate_approve_document', array($this, 'handle_document_approval'));
        add_action('wp_ajax_affiliate_reject_document', array($this, 'handle_document_rejection'));
        add_action('wp_ajax_nopriv_affiliate_reject_document', array($this, 'handle_document_rejection'));
        add_action('wp_ajax_affiliate_get_document_details', array($this, 'get_document_details'));
        add_action('wp_ajax_nopriv_affiliate_get_document_details', array($this, 'get_document_details'));
        
        // Create pages on activation and version updates
        add_action('wp_loaded', array($this, 'maybe_update_pages'));
        
        // Ensure session table exists
        add_action('wp_loaded', array($this, 'ensure_session_table'));
        
        // One-time recreation of Portuguese pages if missing
        add_action('wp_loaded', array($this, 'check_portuguese_pages'));
        
        // Force recreation of Portuguese pages on every load during debugging
        // Hook to send email notifications after registration
        add_action('affiliate_user_registered', array($this, 'send_registration_notification'), 10, 2);
        
        // Register settings
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    public function init_session() {
        // Database session system - no PHP sessions needed
        error_log('Affiliate Portal: Using database session system for authentication');
    }
    
    public function activate() {
        // Create database table
        $this->create_database_table();
        
        // Create custom session table
        $this->create_session_table();
        
        // Force creation of pages
        $this->create_pages(true);
        
        // Create Portuguese pages
        $this->create_portuguese_pages(true);
        
        // Set plugin version option
        update_option('affiliate_portal_version', AFFILIATE_PORTAL_VERSION);
        
        // Set flag to ensure pages are created
        update_option('affiliate_portal_pages_created', 'yes');
        
        // Load text domain for translations
        $this->load_textdomain();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        error_log('Affiliate Portal: Plugin activated successfully with Portuguese support');
    }
    
    public function deactivate() {
        // Clean up if needed
        flush_rewrite_rules();
    }
    
    // Create custom session management table
    private function create_session_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'affiliate_sessions';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            session_id varchar(128) NOT NULL,
            user_id int(11) NOT NULL,
            username varchar(100) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            expires_at datetime NOT NULL,
            user_agent varchar(500) DEFAULT '',
            ip_address varchar(45) DEFAULT '',
            last_activity datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (session_id),
            KEY user_id (user_id),
            KEY expires_at (expires_at)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    // Generate secure session ID
    private function generate_session_id() {
        return 'aff_' . bin2hex(random_bytes(32)) . '_' . time();
    }
    
    // Create new session and return session ID
    private function create_session($user_data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'affiliate_sessions';
        
        // Clean up expired sessions first
        $this->cleanup_expired_sessions();
        
        // Generate unique session ID
        $session_id = $this->generate_session_id();
        
        // Set session expiry (12 hours from now)
        $expires_at = date('Y-m-d H:i:s', time() + (12 * 60 * 60));
        
        // Get user info for security tracking
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        
        // Insert session into database
        $result = $wpdb->insert(
            $table_name,
            array(
                'session_id' => $session_id,
                'user_id' => intval($user_data->id),
                'username' => $user_data->username,
                'expires_at' => $expires_at,
                'user_agent' => substr($user_agent, 0, 500),
                'ip_address' => $ip_address
            ),
            array('%s', '%d', '%s', '%s', '%s', '%s')
        );
        
        if ($result === false) {
            error_log('Failed to create affiliate session: ' . $wpdb->last_error);
            return false;
        }
        
        return $session_id;
    }
    
    // Validate session and return user data
    private function validate_session($session_id) {
        if (empty($session_id)) {
            error_log('Session validation failed: Empty session ID');
            return false;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'affiliate_sessions';
        
        try {
            error_log('Validating session ID: ' . substr($session_id, 0, 20) . '...');
            
            // Get session data with expiry check (only positive user_id for regular users)
            $session = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_name WHERE session_id = %s AND user_id > 0 AND expires_at > NOW()",
                $session_id
            ));
            
            error_log('Session query result: ' . ($session ? 'FOUND' : 'NOT FOUND') . ' for session: ' . substr($session_id, 0, 20) . '...');
            
            if (!$session) {
                // Check if session exists but is expired
                $expired_session = $wpdb->get_row($wpdb->prepare(
                    "SELECT expires_at FROM $table_name WHERE session_id = %s AND user_id > 0",
                    $session_id
                ));
                
                if ($expired_session) {
                    error_log('Session exists but expired. Expiry: ' . $expired_session->expires_at . ', Current: ' . current_time('mysql'));
                } else {
                    error_log('Session not found in database at all for ID: ' . substr($session_id, 0, 20) . '...');
                }
                
                return false;
            }
            
            error_log('Session validation SUCCESS - User ID: ' . $session->user_id . ' (type: ' . gettype($session->user_id) . '), Username: "' . $session->username . '" (type: ' . gettype($session->username) . '), Expires: ' . $session->expires_at);
            
            // Update last activity
            $wpdb->update(
                $table_name,
                array('last_activity' => current_time('mysql')),
                array('session_id' => $session_id),
                array('%s'),
                array('%s')
            );
            
            return array(
                'user_id' => intval($session->user_id),
                'username' => $session->username,
                'session_id' => $session->session_id,
                'created_at' => $session->created_at,
                'expires_at' => $session->expires_at
            );
        } catch (Exception $e) {
            error_log('Session validation error: ' . $e->getMessage());
            return false;
        }
    }
    
    // Clean up expired sessions
    private function cleanup_expired_sessions() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'affiliate_sessions';
        
        $wpdb->query("DELETE FROM $table_name WHERE expires_at < NOW()");
    }
    
    // Destroy specific session
    private function destroy_session($session_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'affiliate_sessions';
        
        $wpdb->delete(
            $table_name,
            array('session_id' => $session_id),
            array('%s')
        );
    }
    
    // Destroy all sessions for a user (logout from all devices)
    private function destroy_user_sessions($user_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'affiliate_sessions';
        
        $wpdb->delete(
            $table_name,
            array('user_id' => intval($user_id)),
            array('%d')
        );
    }
    
    // Admin session methods using the same session table with admin flag
    private function create_admin_session($admin_data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'affiliate_sessions';
        
        // Clean up expired sessions first
        $this->cleanup_expired_sessions();
        
        // Generate unique session ID for admin
        $session_id = 'admin_' . bin2hex(random_bytes(32)) . '_' . time();
        
        // Set session expiry (8 hours for admin sessions)
        $expires_at = date('Y-m-d H:i:s', time() + (8 * 60 * 60));
        
        // Get security info
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        
        // Insert admin session (using negative user_id to distinguish from regular users)
        $result = $wpdb->insert(
            $table_name,
            array(
                'session_id' => $session_id,
                'user_id' => -intval($admin_data->id), // Negative ID for admins
                'username' => 'admin_' . $admin_data->username,
                'expires_at' => $expires_at,
                'user_agent' => substr($user_agent, 0, 500),
                'ip_address' => $ip_address
            ),
            array('%s', '%d', '%s', '%s', '%s', '%s')
        );
        
        if ($result === false) {
            error_log('Failed to create admin session: ' . $wpdb->last_error);
            return false;
        }
        
        return $session_id;
    }
    
    // Validate admin session
    private function validate_admin_session($session_id) {
        if (empty($session_id) || strpos($session_id, 'admin_') !== 0) {
            return false;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'affiliate_sessions';
        
        // Get admin session data (negative user_id indicates admin)
        $session = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE session_id = %s AND user_id < 0 AND expires_at > NOW()",
            $session_id
        ));
        
        if (!$session) {
            return false;
        }
        
        // Update last activity
        $wpdb->update(
            $table_name,
            array('last_activity' => current_time('mysql')),
            array('session_id' => $session_id),
            array('%s'),
            array('%s')
        );
        
        return array(
            'admin_id' => abs(intval($session->user_id)), // Convert back to positive
            'username' => str_replace('admin_', '', $session->username),
            'session_id' => $session->session_id,
            'created_at' => $session->created_at,
            'expires_at' => $session->expires_at
        );
    }
    
    // Set admin session cookie
    private function set_admin_session_cookie($session_id, $admin_id, $username) {
        if (!headers_sent()) {
            $is_secure = is_ssl() || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
            $cookie_expiry = time() + (8 * 60 * 60); // 8 hours for admin
            
            // Secure HTTP-only cookie for admin session ID
            setcookie('affiliate_admin_session', $session_id, $cookie_expiry, '/', '', $is_secure, true);
            // Regular cookies for admin UI
            setcookie('affiliate_admin_id', $admin_id, $cookie_expiry, '/', '', false, false);
            setcookie('affiliate_admin_username', $username, $cookie_expiry, '/', '', false, false);
            
            return true;
        }
        return false;
    }
    
    // Clear admin session cookie
    private function clear_admin_session_cookie() {
        if (!headers_sent()) {
            $is_secure = is_ssl() || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
            setcookie('affiliate_admin_session', '', time() - 3600, '/', '', $is_secure, true);
            setcookie('affiliate_admin_id', '', time() - 3600, '/', '', false, false);
            setcookie('affiliate_admin_username', '', time() - 3600, '/', '', false, false);
        }
    }

    // Database session-based authentication - reliable and secure
    public function is_user_authenticated() {
        // Check for session ID in cookie
        $session_id = $_COOKIE['affiliate_session'] ?? '';
        
        if (!$session_id) {
            return false;
        }
        
        // Validate session against database
        $session_data = $this->validate_session($session_id);
        if (!$session_data) {
            // Clear invalid session cookie
            $this->clear_session_cookie();
            return false;
        }
        
        // Session is valid - user is authenticated
        return $session_data;
    }
    
    // Clear session cookie
    private function clear_session_cookie() {
        if (!headers_sent()) {
            $is_secure = is_ssl() || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
            setcookie('affiliate_session', '', time() - 3600, '/', '', $is_secure, true);
            setcookie('affiliate_user_id', '', time() - 3600, '/', '', false, false);
            setcookie('affiliate_username', '', time() - 3600, '/', '', false, false);
        }
    }
    
    // REMOVED: Old JWT auth cookie method - replaced by database session system
    
    // Get current user ID from database session authentication
    private function get_current_user_id() {
        $auth_data = $this->is_user_authenticated();
        return $auth_data ? $auth_data['user_id'] : null;
    }
    
    // Get current username from database session authentication  
    private function get_current_username() {
        $auth_data = $this->is_user_authenticated();
        return $auth_data ? $auth_data['username'] : null;
    }
    
    // Set session cookie for database session system
    private function set_session_cookie($session_id, $user_id, $username) {
        if (!headers_sent()) {
            $is_secure = is_ssl() || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
            $cookie_expiry = time() + (12 * 60 * 60); // 12 hours
            
            // Secure HTTP-only cookie for session ID
            $session_set = setcookie('affiliate_session', $session_id, $cookie_expiry, '/', '', $is_secure, true);
            // Regular cookies for user info (accessible to JavaScript for UI)
            $user_id_set = setcookie('affiliate_user_id', $user_id, $cookie_expiry, '/', '', false, false);
            $username_set = setcookie('affiliate_username', $username, $cookie_expiry, '/', '', false, false);
            
            // Enhanced logging for cookie setting
            error_log('Session cookies set - Session ID: ' . substr($session_id, 0, 20) . '..., User ID: ' . $user_id . ', Username: ' . $username . ', Secure: ' . ($is_secure ? 'YES' : 'NO'));
            error_log('Cookie results - Session: ' . ($session_set ? 'SUCCESS' : 'FAILED') . ', User ID: ' . ($user_id_set ? 'SUCCESS' : 'FAILED') . ', Username: ' . ($username_set ? 'SUCCESS' : 'FAILED'));
            
            return ($session_set && $user_id_set && $username_set);
        }
        error_log('Cannot set session cookies - headers already sent');
        return false;
    }
    
    // Consolidated KYC table creation method
    private function create_consolidated_kyc_table($kyc_table_name, $charset_collate) {
        global $wpdb;
        
        // Check if table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$kyc_table_name'") === $kyc_table_name;
        
        if (!$table_exists) {
            error_log('Affiliate Portal: Creating KYC table with comprehensive schema...');
            
            $kyc_sql = "CREATE TABLE IF NOT EXISTS $kyc_table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                user_id mediumint(9) NOT NULL,
                account_type varchar(20) NOT NULL DEFAULT 'individual',
                
                -- Individual fields
                full_name varchar(200) DEFAULT '',
                date_of_birth date NULL,
                email varchar(120) DEFAULT '',
                nationality varchar(100) DEFAULT '',
                mobile_number varchar(20) DEFAULT '',
                affiliate_type varchar(50) DEFAULT '',
                address_line1 varchar(255) DEFAULT '',
                address_line2 varchar(255) DEFAULT '',
                city varchar(100) DEFAULT '',
                country varchar(100) DEFAULT '',
                post_code varchar(20) DEFAULT '',
                
                -- Company contact fields
                business_contact_name varchar(200) DEFAULT '',
                job_title varchar(100) DEFAULT '',
                business_email varchar(120) DEFAULT '',
                business_telephone varchar(20) DEFAULT '',
                
                -- Company details
                full_company_name varchar(200) DEFAULT '',
                trading_name varchar(200) DEFAULT '',
                type_of_business varchar(100) DEFAULT '',
                company_registration_no varchar(100) DEFAULT '',
                company_email varchar(120) DEFAULT '',
                company_telephone varchar(20) DEFAULT '',
                company_address_line1 varchar(255) DEFAULT '',
                company_address_line2 varchar(255) DEFAULT '',
                company_city varchar(100) DEFAULT '',
                company_country varchar(100) DEFAULT '',
                company_post_code varchar(20) DEFAULT '',
                
                -- Affiliate sites
                affiliate_sites text,
                
                -- Document URLs with approval status
                identity_document_type varchar(50) DEFAULT '',
                identity_document_number varchar(100) DEFAULT '',
                identity_document_expiry date NULL,
                identity_document_url text,
                identity_document_status varchar(20) DEFAULT 'pending',
                identity_document_notes text,
                
                address_proof_type varchar(50) DEFAULT '',
                address_proof_url text,
                address_proof_status varchar(20) DEFAULT 'pending',
                address_proof_notes text,
                
                bank_statement_url text,
                bank_statement_status varchar(20) DEFAULT 'pending',
                bank_statement_notes text,
                
                selfie_url text,
                selfie_status varchar(20) DEFAULT 'pending',
                selfie_notes text,
                
                passport_url text,
                passport_status varchar(20) DEFAULT 'pending',
                passport_notes text,
                
                company_registration_certificate_url text,
                company_registration_certificate_status varchar(20) DEFAULT 'pending',
                company_registration_certificate_notes text,
                
                company_address_proof_url text,
                company_address_proof_status varchar(20) DEFAULT 'pending',
                company_address_proof_notes text,
                
                business_license_url text,
                business_license_status varchar(20) DEFAULT 'pending',
                business_license_notes text,
                
                directors_id_docs_url text,
                directors_id_docs_status varchar(20) DEFAULT 'pending',
                directors_id_docs_notes text,
                
                -- Company specific
                company_type varchar(100) DEFAULT '',
                registration_number varchar(100) DEFAULT '',
                tax_id varchar(100) DEFAULT '',
                incorporation_date date NULL,
                
                -- Directors and shareholders (JSON)
                list_of_directors text,
                list_of_shareholders text,
                
                -- Director documents (JSON - each director can have separate docs)
                director_documents text,
                
                -- Status and admin
                kyc_status varchar(50) DEFAULT 'draft',
                overall_status varchar(50) DEFAULT 'pending_review',
                admin_notes text,
                submitted_at datetime DEFAULT CURRENT_TIMESTAMP,
                reviewed_at datetime NULL,
                reviewed_by varchar(100) DEFAULT '',
                last_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                
                PRIMARY KEY (id),
                UNIQUE KEY user_id (user_id),
                KEY idx_account_type (account_type),
                KEY idx_kyc_status (kyc_status),
                KEY idx_overall_status (overall_status)
            ) $charset_collate;";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            $result = dbDelta($kyc_sql);
            
            // Verify creation
            $created = $wpdb->get_var("SHOW TABLES LIKE '$kyc_table_name'") === $kyc_table_name;
            error_log('Affiliate Portal: KYC table creation result: ' . ($created ? 'SUCCESS' : 'FAILED'));
            
            if ($wpdb->last_error) {
                error_log('Affiliate Portal: KYC table creation error: ' . $wpdb->last_error);
            }
            
            return $result;
        }
        
        error_log('Affiliate Portal: KYC table already exists');
        return ['KYC table already exists'];
    }
    
    // Ensure KYC table exists (called before KYC operations) - now uses consolidated method
    private function ensure_kyc_table_exists() {
        global $wpdb;
        $kyc_table_name = $wpdb->prefix . 'affiliate_kyc';
        $charset_collate = $wpdb->get_charset_collate();
        
        return $this->create_consolidated_kyc_table($kyc_table_name, $charset_collate);
    }
    
    // Admin authentication - using database sessions
    
    // Admin session cookies handled by set_admin_session_cookie method
    
    // Admin session cookies cleared by clear_admin_session_cookie method
    
    private function is_admin_authenticated() {
        // Check for admin session ID in cookie
        $session_id = $_COOKIE['affiliate_admin_session'] ?? '';
        
        if (!$session_id) {
            return false;
        }
        
        // Validate admin session against database
        $session_data = $this->validate_admin_session($session_id);
        if (!$session_data) {
            // Clear invalid session cookie
            $this->clear_admin_session_cookie();
            return false;
        }
        
        return $session_data;
    }

    // REMOVED: Session validation - using database session system
    
    // REMOVED: Force logout - handled by database session destruction
    
    // REMOVED: JWT token blacklist - not needed with database sessions
    
    // REMOVED: JWT token blacklist check - not needed with database sessions

    public function create_database_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'affiliate_users';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            username varchar(64) NOT NULL UNIQUE,
            password varchar(256) NOT NULL,
            security_que varchar(255),
            security_ans varchar(255),
            name_prefix varchar(10),
            first_name varchar(50),
            last_name varchar(50),
            dob date,
            type varchar(20),
            email varchar(120) NOT NULL UNIQUE,
            company_name varchar(100),
            country_code varchar(10),
            mobile_number varchar(20),
            address_line1 varchar(255),
            address_line2 varchar(255),
            city varchar(100),
            country varchar(100),
            state varchar(100),
            zipcode varchar(20),
            chat_id_channel varchar(100),
            affiliate_type varchar(50),
            currency varchar(10),
            status varchar(50) DEFAULT 'kyc pending',
            admin_remarks text DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY username (username),
            UNIQUE KEY email (email)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $result = dbDelta($sql);
        
        // Create affiliate admin table
        $admin_table_name = $wpdb->prefix . 'affiliate_admin';
        
        $admin_sql = "CREATE TABLE $admin_table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            username varchar(60) NOT NULL,
            password varchar(255) NOT NULL,
            email varchar(100) NOT NULL,
            full_name varchar(100) NOT NULL,
            role varchar(50) DEFAULT 'admin',
            status varchar(20) DEFAULT 'active',
            last_login datetime NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY username (username),
            UNIQUE KEY email (email)
        ) $charset_collate;";
        
        $admin_result = dbDelta($admin_sql);
        
        // Create email configuration table
        $email_config_table = $wpdb->prefix . 'affiliate_email_config';
        
        $email_config_sql = "CREATE TABLE $email_config_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            config_key varchar(100) NOT NULL,
            config_value text NOT NULL,
            description varchar(255) DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY config_key (config_key)
        ) $charset_collate;";
        
        $email_result = dbDelta($email_config_sql);
        
        // Create comprehensive KYC table using consolidated schema
        $kyc_table_name = $wpdb->prefix . 'affiliate_kyc';
        $kyc_result = $this->create_consolidated_kyc_table($kyc_table_name, $charset_collate);
        
        // Log KYC table creation result
        error_log('Affiliate Portal: KYC table creation result: ' . print_r($kyc_result, true));
        
        // Check if KYC table was created successfully
        $kyc_table_exists = $wpdb->get_var("SHOW TABLES LIKE '$kyc_table_name'") === $kyc_table_name;
        error_log('Affiliate Portal: KYC table exists after creation: ' . ($kyc_table_exists ? 'YES' : 'NO'));
        
        // If table creation failed, try alternative method
        if (!$kyc_table_exists) {
            error_log('Affiliate Portal: Attempting direct KYC table creation');
            $direct_result = $wpdb->query($kyc_sql);
            error_log('Affiliate Portal: Direct KYC table creation result: ' . ($direct_result !== false ? 'SUCCESS' : 'FAILED'));
            if ($wpdb->last_error) {
                error_log('Affiliate Portal: KYC table creation error: ' . $wpdb->last_error);
            }
        }
        
        // Ensure the KYC table has all necessary columns for our forms
        $this->update_kyc_table_columns($kyc_table_name);
        
        // Insert default email configuration
        $wpdb->replace(
            $email_config_table,
            array(
                'config_key' => 'notification_emails',
                'config_value' => 'admin@example.com',
                'description' => 'Comma-separated list of emails to notify on new registrations'
            )
        );
        
        $wpdb->replace(
            $email_config_table,
            array(
                'config_key' => 'from_email',
                'config_value' => 'noreply@' . parse_url(home_url(), PHP_URL_HOST),
                'description' => 'From email address for notifications'
            )
        );
        
        $wpdb->replace(
            $email_config_table,
            array(
                'config_key' => 'from_name',
                'config_value' => get_bloginfo('name') . ' Affiliate Portal',
                'description' => 'From name for notifications'
            )
        );
        
        // Create default master admin if none exists
        $existing_admin = $wpdb->get_var("SELECT COUNT(*) FROM $admin_table_name");
        if ($existing_admin == 0) {
            $password_hash = wp_hash_password('admin123');
            $result = $wpdb->insert(
                $admin_table_name,
                array(
                    'username' => 'masteradmin',
                    'password' => $password_hash,
                    'email' => 'admin@example.com',
                    'full_name' => 'Master Administrator',
                    'role' => 'master_admin'
                )
            );
            error_log('Affiliate Portal: Default admin created with password hash: ' . $password_hash);
        }
        
        // Log table creation result
        error_log('Affiliate Portal: Affiliate users table creation result: ' . print_r($result, true));
        error_log('Affiliate Portal: Admin table creation result: ' . print_r($admin_result, true));
        error_log('Affiliate Portal: Email config table creation result: ' . print_r($email_result, true));
        error_log('Affiliate Portal: KYC table creation result: ' . print_r($kyc_result, true));
        
        // Verify tables exist
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
        $admin_table_exists = $wpdb->get_var("SHOW TABLES LIKE '$admin_table_name'");
        $email_table_exists = $wpdb->get_var("SHOW TABLES LIKE '$email_config_table'");
        $kyc_table_exists = $wpdb->get_var("SHOW TABLES LIKE '$kyc_table_name'");
        
        if ($table_exists) {
            error_log('Affiliate Portal: Table ' . $table_name . ' created successfully');
        } else {
            error_log('Affiliate Portal: Failed to create table ' . $table_name);
        }
        
        if ($admin_table_exists) {
            error_log('Affiliate Portal: Admin table ' . $admin_table_name . ' created successfully');
        } else {
            error_log('Affiliate Portal: Failed to create admin table ' . $admin_table_name);
        }
        
        if ($email_table_exists) {
            error_log('Affiliate Portal: Email config table ' . $email_config_table . ' created successfully');
        } else {
            error_log('Affiliate Portal: Failed to create email config table ' . $email_config_table);
        }
        
        if ($kyc_table_exists) {
            error_log('Affiliate Portal: KYC table ' . $kyc_table_name . ' created successfully');
        } else {
            error_log('Affiliate Portal: Failed to create KYC table ' . $kyc_table_name);
        }
        

    }
    
    // Update KYC table to ensure it has all necessary columns
    private function update_kyc_table_columns($table_name) {
        global $wpdb;
        
        $columns_to_add = array(
            'full_name' => "ALTER TABLE $table_name ADD COLUMN IF NOT EXISTS full_name varchar(255)",
            'date_of_birth' => "ALTER TABLE $table_name ADD COLUMN IF NOT EXISTS date_of_birth date",
            'email' => "ALTER TABLE $table_name ADD COLUMN IF NOT EXISTS email varchar(255)",
            'nationality' => "ALTER TABLE $table_name ADD COLUMN IF NOT EXISTS nationality varchar(100)",
            'mobile_number' => "ALTER TABLE $table_name ADD COLUMN IF NOT EXISTS mobile_number varchar(50)",
            'affiliate_type' => "ALTER TABLE $table_name ADD COLUMN IF NOT EXISTS affiliate_type varchar(100)",
            'address_line1' => "ALTER TABLE $table_name ADD COLUMN IF NOT EXISTS address_line1 varchar(255)",
            'address_line2' => "ALTER TABLE $table_name ADD COLUMN IF NOT EXISTS address_line2 varchar(255)",
            'city' => "ALTER TABLE $table_name ADD COLUMN IF NOT EXISTS city varchar(100)",
            'country' => "ALTER TABLE $table_name ADD COLUMN IF NOT EXISTS country varchar(100)",
            'post_code' => "ALTER TABLE $table_name ADD COLUMN IF NOT EXISTS post_code varchar(20)",
            'identification_url' => "ALTER TABLE $table_name ADD COLUMN IF NOT EXISTS identification_url text",
            'affiliate_sites' => "ALTER TABLE $table_name ADD COLUMN IF NOT EXISTS affiliate_sites text",
            'company_name' => "ALTER TABLE $table_name ADD COLUMN IF NOT EXISTS company_name varchar(255)",
            'business_registration_number' => "ALTER TABLE $table_name ADD COLUMN IF NOT EXISTS business_registration_number varchar(100)",
            'business_contact_name' => "ALTER TABLE $table_name ADD COLUMN IF NOT EXISTS business_contact_name varchar(255)",
            'business_email' => "ALTER TABLE $table_name ADD COLUMN IF NOT EXISTS business_email varchar(255)",
            'business_phone' => "ALTER TABLE $table_name ADD COLUMN IF NOT EXISTS business_phone varchar(50)",
            'business_address' => "ALTER TABLE $table_name ADD COLUMN IF NOT EXISTS business_address text",
            'account_type' => "ALTER TABLE $table_name ADD COLUMN IF NOT EXISTS account_type varchar(20)",
            'created_at' => "ALTER TABLE $table_name ADD COLUMN IF NOT EXISTS created_at timestamp DEFAULT CURRENT_TIMESTAMP",
            'updated_at' => "ALTER TABLE $table_name ADD COLUMN IF NOT EXISTS updated_at timestamp DEFAULT CURRENT_TIMESTAMP"
        );
        
        foreach ($columns_to_add as $column_name => $sql) {
            $wpdb->query($sql);
            if ($wpdb->last_error) {
                error_log("Error adding column $column_name to KYC table: " . $wpdb->last_error);
            }
        }
    }
    
    public function maybe_update_pages() {
        $installed_version = get_option('affiliate_portal_version');
        $pages_created = get_option('affiliate_portal_pages_created');
        
        // Check if English pages exist
        $login_exists = get_page_by_title('Affiliate Login');
        $register_exists = get_page_by_title('Affiliate Registration');
        $dashboard_exists = get_page_by_title('Affiliate Dashboard');
        $admin_login_exists = get_page_by_title('Admin Login');
        $admin_dashboard_exists = get_page_by_title('Admin Dashboard');
        
        // Check if Portuguese pages exist
        $pt_login_exists = get_page_by_title('Login do Afiliado');
        $pt_register_exists = get_page_by_title('Registro de Afiliado');
        $pt_dashboard_exists = get_page_by_title('Painel do Afiliado');
        $pt_admin_login_exists = get_page_by_title('Login do Administrador');
        $pt_admin_dashboard_exists = get_page_by_title('Painel do Administrador');
        
        // If version doesn't match or pages don't exist, recreate them
        if ($installed_version !== AFFILIATE_PORTAL_VERSION || 
            $pages_created !== 'yes' || 
            !$login_exists || 
            !$register_exists || 
            !$dashboard_exists ||
            !$admin_login_exists ||
            !$admin_dashboard_exists ||
            !$pt_login_exists ||
            !$pt_register_exists ||
            !$pt_dashboard_exists ||
            !$pt_admin_login_exists ||
            !$pt_admin_dashboard_exists) {
            
            $this->create_pages(true); // Force recreation
            $this->create_portuguese_pages(true); // Force recreation of Portuguese pages
            update_option('affiliate_portal_version', AFFILIATE_PORTAL_VERSION);
            update_option('affiliate_portal_pages_created', 'yes');
            error_log('Affiliate Portal: Updated pages for version ' . AFFILIATE_PORTAL_VERSION);
        }
    }

    public function force_recreate_portuguese_pages() {
        // Force recreation of Portuguese pages to ensure they exist
        static $recreated = false;
        if (!$recreated) {
            $this->create_portuguese_pages(true);
            $recreated = true;
            error_log('Affiliate Portal: Force recreated Portuguese pages');
        }
    }
    
    public function create_pages($force_recreation = false) {
        // Create Login Page
        $login_page = get_page_by_title('Affiliate Login');
        if (!$login_page || $force_recreation) {
            if ($login_page && $force_recreation) {
                // Delete existing page to recreate with updated content
                wp_delete_post($login_page->ID, true);
            }
            wp_insert_post(array(
                'post_title' => 'Affiliate Login',
                'post_content' => '[affiliate_login]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => 'affiliate-login'
            ));
            error_log('Affiliate Portal: Login page created/updated');
        }
        
        // Create Registration Page
        $register_page = get_page_by_title('Affiliate Registration');
        if (!$register_page || $force_recreation) {
            if ($register_page && $force_recreation) {
                // Delete existing page to recreate with updated content
                wp_delete_post($register_page->ID, true);
            }
            wp_insert_post(array(
                'post_title' => 'Affiliate Registration',
                'post_content' => '[affiliate_register]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => 'affiliate-register'
            ));
            error_log('Affiliate Portal: Registration page created/updated');
        }
        
        // Create Dashboard Page
        $dashboard_page = get_page_by_title('Affiliate Dashboard');
        if (!$dashboard_page || $force_recreation) {
            if ($dashboard_page && $force_recreation) {
                // Delete existing page to recreate with updated content
                wp_delete_post($dashboard_page->ID, true);
            }
            wp_insert_post(array(
                'post_title' => 'Affiliate Dashboard',
                'post_content' => '[affiliate_dashboard]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => 'affiliate-dashboard'
            ));
            error_log('Affiliate Portal: Dashboard page created/updated');
        }
        
        // Create Admin Login Page
        $admin_login_page = get_page_by_title('Admin Login');
        if (!$admin_login_page || $force_recreation) {
            if ($admin_login_page && $force_recreation) {
                wp_delete_post($admin_login_page->ID, true);
            }
            wp_insert_post(array(
                'post_title' => 'Admin Login',
                'post_content' => '[affiliate_admin_login]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => 'admin-login'
            ));
            error_log('Affiliate Portal: Admin Login page created/updated');
        }
        
        // Create Admin Dashboard Page
        $admin_dashboard_page = get_page_by_title('Admin Dashboard');
        if (!$admin_dashboard_page || $force_recreation) {
            if ($admin_dashboard_page && $force_recreation) {
                wp_delete_post($admin_dashboard_page->ID, true);
            }
            wp_insert_post(array(
                'post_title' => 'Admin Dashboard',
                'post_content' => '[affiliate_admin_dashboard]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => 'admin-dashboard'
            ));
            error_log('Affiliate Portal: Admin Dashboard page created/updated');
        }
        
        // Clear any cached content
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        // Flush rewrite rules to ensure URLs work
        flush_rewrite_rules();
    }
    
    public function create_portuguese_pages($force_recreation = false) {
        // Create Portuguese Login Page
        $login_page = get_page_by_title('Login do Afiliado');
        if (!$login_page || $force_recreation) {
            if ($login_page && $force_recreation) {
                wp_delete_post($login_page->ID, true);
            }
            wp_insert_post(array(
                'post_title' => 'Login do Afiliado',
                'post_content' => '[affiliate_login_pt]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => 'afiliado-login'
            ));
            error_log('Affiliate Portal: Portuguese login page created/updated');
        }
        
        // Create Portuguese Registration Page
        $register_page = get_page_by_title('Registro de Afiliado');
        if (!$register_page || $force_recreation) {
            if ($register_page && $force_recreation) {
                wp_delete_post($register_page->ID, true);
            }
            wp_insert_post(array(
                'post_title' => 'Registro de Afiliado',
                'post_content' => '[affiliate_register_pt]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => 'afiliado-registro'
            ));
            error_log('Affiliate Portal: Portuguese registration page created/updated');
        }
        
        // Create Portuguese Dashboard Page
        $dashboard_page = get_page_by_title('Painel do Afiliado');
        if (!$dashboard_page || $force_recreation) {
            if ($dashboard_page && $force_recreation) {
                wp_delete_post($dashboard_page->ID, true);
            }
            wp_insert_post(array(
                'post_title' => 'Painel do Afiliado',
                'post_content' => '[affiliate_dashboard_pt]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => 'painel-afiliado'
            ));
            error_log('Affiliate Portal: Portuguese dashboard page created/updated');
        }
        
        // Create Portuguese Admin Login Page
        $admin_login_page = get_page_by_title('Login do Administrador');
        if (!$admin_login_page || $force_recreation) {
            if ($admin_login_page && $force_recreation) {
                wp_delete_post($admin_login_page->ID, true);
            }
            wp_insert_post(array(
                'post_title' => 'Login do Administrador',
                'post_content' => '[affiliate_admin_login_pt]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => 'admin-login-pt'
            ));
            error_log('Affiliate Portal: Portuguese admin login page created/updated');
        }
        
        // Create Portuguese Admin Dashboard Page
        $admin_dashboard_page = get_page_by_title('Painel do Administrador');
        if (!$admin_dashboard_page || $force_recreation) {
            if ($admin_dashboard_page && $force_recreation) {
                wp_delete_post($admin_dashboard_page->ID, true);
            }
            wp_insert_post(array(
                'post_title' => 'Painel do Administrador',
                'post_content' => '[affiliate_admin_dashboard_pt]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => 'painel-admin'
            ));
            error_log('Affiliate Portal: Portuguese admin dashboard page created/updated');
        }
        
        // Clear any cached content
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        // Flush rewrite rules to ensure URLs work
        flush_rewrite_rules();
    }
    
    public function ensure_session_table() {
        // Only run this check once per request to avoid performance issues
        static $checked = false;
        if ($checked) return;
        $checked = true;
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'affiliate_sessions';
        
        // Check if table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
        
        if (!$table_exists) {
            $this->create_session_table();
            error_log('Affiliate Portal: Created missing session table');
        }
        
        // Also ensure main user table exists for login to work
        $user_table = $wpdb->prefix . 'affiliate_users';
        $user_table_exists = $wpdb->get_var("SHOW TABLES LIKE '$user_table'") === $user_table;
        
        if (!$user_table_exists) {
            $this->create_database_table();
            error_log('Affiliate Portal: Created missing user table');
            
            // Create sample users for testing after creating the table
            $this->create_test_users();
        }
        
        // Also ensure admin table exists
        $admin_table = $wpdb->prefix . 'affiliate_admin';
        $admin_table_exists = $wpdb->get_var("SHOW TABLES LIKE '$admin_table'") === $admin_table;
        
        if (!$admin_table_exists) {
            $this->create_admin_table();
            error_log('Affiliate Portal: Created missing admin table');
        }
    }
    
    // Create admin table if missing
    private function create_admin_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'affiliate_admin';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            username varchar(100) NOT NULL,
            password varchar(255) NOT NULL,
            email varchar(100) DEFAULT NULL,
            role varchar(50) DEFAULT 'admin',
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            last_login datetime DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY username (username)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Create default admin user if none exists
        $existing_admin = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        if ($existing_admin == 0) {
            $wpdb->insert($table_name, array(
                'username' => 'admin',
                'password' => wp_hash_password('admin123'),
                'email' => 'admin@example.com',
                'role' => 'super_admin',
                'status' => 'active'
            ));
            error_log('Affiliate Portal: Created default admin user (admin/admin123)');
        }
    }
    
    // Create test users for reliable login testing
    private function create_test_users() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'affiliate_users';
        
        // Check if test users already exist
        $existing_users = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE username LIKE 'testuser%'");
        
        if ($existing_users == 0) {
            // Create 3 test users with different statuses
            $test_users = array(
                array(
                    'username' => 'testuser1',
                    'password' => wp_hash_password('password123'),
                    'email' => 'test1@example.com',
                    'first_name' => 'Test',
                    'last_name' => 'User One',
                    'status' => 'approved',
                    'affiliate_type' => 'individual'
                ),
                array(
                    'username' => 'testuser2',
                    'password' => wp_hash_password('password123'),
                    'email' => 'test2@example.com',
                    'first_name' => 'Test',
                    'last_name' => 'User Two',
                    'status' => 'approved',
                    'affiliate_type' => 'business'
                ),
                array(
                    'username' => 'testuser3',
                    'password' => wp_hash_password('password123'),
                    'email' => 'test3@example.com',
                    'first_name' => 'Test',
                    'last_name' => 'User Three',
                    'status' => 'pending',
                    'affiliate_type' => 'individual'
                )
            );
            
            foreach ($test_users as $user) {
                $wpdb->insert($table_name, $user);
            }
            
            error_log('Affiliate Portal: Created test users (testuser1, testuser2, testuser3) with password: password123');
        }
    }
    
    public function check_portuguese_pages() {
        // Only run this check once per session to avoid performance issues
        static $checked = false;
        if ($checked) return;
        $checked = true;
        
        // Check if Portuguese pages exist
        $pt_login = get_page_by_title('Login do Afiliado');
        $pt_register = get_page_by_title('Registro de Afiliado'); 
        $pt_dashboard = get_page_by_title('Painel do Afiliado');
        
        // If any are missing, recreate all Portuguese pages
        if (!$pt_login || !$pt_register || !$pt_dashboard) {
            error_log('Affiliate Portal: Portuguese pages missing, recreating...');
            $this->create_portuguese_pages(true);
        }
    }
    
    public function load_textdomain() {
        load_plugin_textdomain(
            'affiliate-portal',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages/'
        );
    }
    
    public function enqueue_scripts() {
        // Add favicon to head
        add_action('wp_head', function() {
            echo '<link rel="icon" type="image/png" sizes="16x16" href="' . AFFILIATE_PORTAL_URL . 'assets/favicon.png">';
        });
        
        // Enqueue Bootstrap CSS
        wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css', array(), '5.3.0');
        
        // Remove Font Awesome dependency - using inline SVG icons instead
        
        // Use original working styles temporarily
        wp_enqueue_style('affiliate-portal-style', AFFILIATE_PORTAL_URL . 'assets/style.css', array(), AFFILIATE_PORTAL_VERSION);
        
        // Enqueue appropriate script based on current page language
        $current_page = get_queried_object();
        $is_portuguese_page = false;
        
        if ($current_page && isset($current_page->post_name)) {
            $portuguese_pages = array('afiliado-login', 'afiliado-registro', 'painel-afiliado', 'admin-login-pt', 'painel-admin');
            $is_portuguese_page = in_array($current_page->post_name, $portuguese_pages);
        }
        
        if ($is_portuguese_page) {
            wp_enqueue_script('affiliate-portal-script', AFFILIATE_PORTAL_URL . 'assets/script-pt.js', array('jquery'), AFFILIATE_PORTAL_VERSION, true);
        } else {
            wp_enqueue_script('affiliate-portal-script', AFFILIATE_PORTAL_URL . 'assets/script.js', array('jquery'), AFFILIATE_PORTAL_VERSION, true);
        }
        
        // Load countries data from JSON file
        $countries_data = array();
        $json_file = AFFILIATE_PORTAL_PATH . 'assets/countries-states.json';
        if (file_exists($json_file)) {
            $json_content = file_get_contents($json_file);
            $countries_data = json_decode($json_content, true);
        }
        
        // Localize script for AJAX and countries data with language-specific redirects
        $login_redirect = $is_portuguese_page ? 
            get_permalink(get_page_by_path('painel-afiliado')) : 
            get_permalink(get_page_by_title('Affiliate Dashboard'));
        
        wp_localize_script('affiliate-portal-script', 'affiliatePortalAjax', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('affiliate_nonce'),
            'loginRedirect' => $login_redirect,
            'pluginUrl' => AFFILIATE_PORTAL_URL,
            'isPortuguese' => $is_portuguese_page,
            'countriesData' => $countries_data
        ));
    }
    
    public function custom_css() {
        // Get logo size setting only (removed color customization)
        $logo_size = get_option('affiliate_portal_logo_size', 150);
        
        echo '<style type="text/css">';
        
        // Fixed CSS variables (no customization)
        echo ':root {';
        echo '--affiliate-primary-color: #667eea;';
        echo '--affiliate-secondary-color: #764ba2;';
        echo '}';
        
        // Logo sizing
        echo '.affiliate-custom-logo { max-width: ' . intval($logo_size) . 'px !important; height: auto !important; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }';
        
        // Fixed styles without horizontal scroll
        echo '
        /* Reset and base styles to prevent horizontal scroll */
        body, html { 
            overflow-x: hidden !important; 
            max-width: 100vw !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .affiliate-portal-container { 
            position: relative !important;
            width: 100vw !important;
            max-width: 100vw !important; 
            overflow-x: hidden !important; 
            box-sizing: border-box !important;
            padding: 0 !important;
            margin: 0 !important;
            left: 50% !important;
            right: 50% !important;
            margin-left: -50vw !important;
            margin-right: -50vw !important;
        }
        
        /* WordPress Content Area Override */
        .entry-content .affiliate-portal-container,
        .post-content .affiliate-portal-container,
        .page-content .affiliate-portal-container,
        .content .affiliate-portal-container,
        article .affiliate-portal-container,
        main .affiliate-portal-container {
            position: relative !important;
            width: 100vw !important;
            max-width: 100vw !important;
            margin: 0 !important;
            padding: 0 !important;
            left: 50% !important;
            right: 50% !important;
            margin-left: -50vw !important;
            margin-right: -50vw !important;
        }
        .affiliate-auth-grid { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            min-height: 100vh; 
            width: 100vw !important;
            max-width: 100vw !important;
            overflow: hidden !important;
            margin: 0 !important;
            box-sizing: border-box !important;
            position: relative !important;
        }
        .affiliate-brand-column, .affiliate-form-column { 
            padding: 2rem; 
            box-sizing: border-box !important;
            overflow: hidden !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        .affiliate-brand-column { 
            background: linear-gradient(135deg, #667eea, #764ba2) !important; 
        }
        .affiliate-form-column {
            background: white;
        }
        
        /* Enhanced Dashboard Styles */
        .affiliate-dashboard-fullscreen {
            min-height: 100vh;
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            padding: 0;
            margin: 0;
            overflow-x: hidden !important;
            max-width: 100vw !important;
            width: 100% !important;
        }
        .affiliate-welcome-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 2rem;
            margin-bottom: 2rem;
            width: 100%;
            box-sizing: border-box;
        }
        .affiliate-welcome-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }
        .affiliate-status-section {
            max-width: 1200px;
            margin: 0 auto 2rem auto;
            padding: 0 2rem;
            box-sizing: border-box;
        }
        .affiliate-dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem 2rem 2rem;
            box-sizing: border-box;
        }
        .affiliate-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        /* Prefix and Account Type Button Styles */
        .affiliate-prefix-selector, .affiliate-account-type-selector {
            display: flex;
            gap: 0;
            margin-top: 0.5rem;
        }
        .affiliate-prefix-radio, .affiliate-type-radio {
            display: none;
        }
        .affiliate-prefix-btn {
            flex: 1;
            padding: 12px 16px;
            border: 2px solid #dee2e6;
            background: white;
            color: #495057;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            border-right: none;
        }
        .affiliate-prefix-btn:first-of-type {
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }
        .affiliate-prefix-btn:last-of-type {
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
            border-right: 2px solid #dee2e6;
        }
        .affiliate-prefix-radio:checked + .affiliate-prefix-btn {
            background: #667eea;
            color: white;
            border-color: #667eea;
            z-index: 1;
            position: relative;
        }
        .affiliate-prefix-btn:hover {
            background: #f8f9fa;
            border-color: #667eea;
        }
        .affiliate-prefix-radio:checked + .affiliate-prefix-btn:hover {
            background: #5a6fd8;
        }
        
        .affiliate-type-btn {
            flex: 1;
            padding: 16px 20px;
            border: 2px solid #dee2e6;
            background: white;
            color: #495057;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 8px;
            margin-right: 8px;
        }
        .affiliate-type-btn:last-of-type {
            margin-right: 0;
        }
        .affiliate-type-radio:checked + .affiliate-type-btn {
            background: #667eea;
            color: white;
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        .affiliate-type-btn:hover {
            background: #f8f9fa;
            border-color: #667eea;
            transform: translateY(-1px);
        }
        .affiliate-type-radio:checked + .affiliate-type-btn:hover {
            background: #5a6fd8;
        }
        
        /* Responsive Design - Complete Fix */
        @media (max-width: 768px) {
            .affiliate-prefix-selector, .affiliate-account-type-selector {
                flex-direction: column;
                gap: 8px;
            }
            .affiliate-prefix-btn, .affiliate-type-btn {
                margin-right: 0;
                border-radius: 8px !important;
                border-right: 2px solid #dee2e6 !important;
            }
            .affiliate-portal-container {
                padding: 0 !important;
                width: 100vw !important;
                position: relative !important;
                left: 50% !important;
                right: 50% !important;
                margin-left: -50vw !important;
                margin-right: -50vw !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                overflow-x: hidden !important;
            }
            .affiliate-auth-grid { 
                grid-template-columns: 1fr !important; 
                min-height: auto !important;
                width: 100% !important;
                max-width: 100% !important;
                overflow-x: hidden !important;
            }
            .affiliate-brand-column, .affiliate-form-column { 
                padding: 1.5rem !important;
                width: 100% !important;
                max-width: 100% !important;
                overflow-x: hidden !important;
            }
            .affiliate-brand-column { 
                min-height: 40vh; 
            }
            .affiliate-welcome-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            .affiliate-dashboard-grid {
                grid-template-columns: 1fr;
                padding: 0 1rem 2rem 1rem;
            }
        }
        @media (max-width: 480px) {
            .affiliate-brand-column, .affiliate-form-column { 
                padding: 1rem !important;
            }
            .affiliate-custom-logo {
                max-width: ' . min(intval($logo_size), 120) . 'px !important;
            }
        }
        ';
        echo '</style>';
    }
    
    public function login_shortcode($atts) {
        ob_start();
        include AFFILIATE_PORTAL_PATH . 'templates/login-form.php';
        return ob_get_clean();
    }
    
    public function register_shortcode($atts) {
        ob_start();
        include AFFILIATE_PORTAL_PATH . 'templates/register-form.php';
        return ob_get_clean();
    }
    
    public function dashboard_shortcode($atts) {
        // Enhanced session initialization with forced refresh
        // REMOVED: force_session_start() - using cookie-only authentication
        
        // Force cache-busting headers to prevent browser caching of user data
        if (!headers_sent()) {
            header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        }
        
        ob_start();
        
        // Check if user is logged in using cookie authentication only
        if (!$this->is_user_authenticated()) {
            echo '<div class="affiliate-alert affiliate-alert-warning">Please <a href="' . get_permalink(get_page_by_title('Affiliate Login')) . '">log in</a> to access the dashboard.</div>';
            return ob_get_clean();
        }
        
        // REMOVED: Session validation since we're using cookie-only authentication
        
        // Include the enhanced dashboard template
        if (file_exists(AFFILIATE_PORTAL_PATH . 'templates/dashboard-enhanced.php')) {
            include AFFILIATE_PORTAL_PATH . 'templates/dashboard-enhanced.php';
        } else {
            // Fallback to regular dashboard if enhanced doesn't exist
            include AFFILIATE_PORTAL_PATH . 'templates/dashboard.php';
        }
        
        return ob_get_clean();
    }
    
    public function is_user_logged_in() {
        return $this->is_user_authenticated();
    }
    
    public function force_session_start() {
        // CRITICAL SECURITY FIX: Completely removed session management
        // Sessions were causing global data sharing across all users
        // Using database session authentication for proper isolation
        
        // If any session exists from elsewhere, destroy it to prevent contamination
        if (session_id()) {
            session_destroy();
            error_log('SECURITY: Destroyed existing session to prevent user data mixing');
        }
        
        error_log('Affiliate Portal: Session management DISABLED - using cookie-only authentication');
    }
    
    public function is_user_logged_in_enhanced() {
        $this->force_session_start();
        return $this->is_user_authenticated();
    }
    
    public function get_current_affiliate_user() {
        $user_id = $this->get_current_user_id();
        if (!$user_id) {
            return null;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'affiliate_users';
        
        try {
            $user = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d",
                $user_id
            ));
            
            if (!$user) {
                error_log('Affiliate Portal: User ID ' . $user_id . ' not found in table ' . $table_name);
            }
            
            return $user;
        } catch (Exception $e) {
            error_log('Affiliate Portal Error: ' . $e->getMessage());
            return null;
        }
    }
    
    public function debug_table_info() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'affiliate_users';
        
        // Check if table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
        error_log('Affiliate Portal Debug: Table exists: ' . ($table_exists ? 'YES' : 'NO'));
        
        if ($table_exists) {
            // Get table structure
            $columns = $wpdb->get_results("DESCRIBE $table_name");
            error_log('Affiliate Portal Debug: Table structure: ' . print_r($columns, true));
            
            // Get row count
            $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
            error_log('Affiliate Portal Debug: Total users in table: ' . $count);
        }
    }
    
    public function add_admin_menu() {
        add_options_page(
            'Affiliate Portal Settings',
            'Affiliate Portal',
            'manage_options',
            'affiliate-portal-settings',
            array($this, 'admin_page')
        );
    }
    
    public function register_settings() {
        // Register settings for customization
        register_setting('affiliate_portal_settings', 'affiliate_portal_logo');
        register_setting('affiliate_portal_settings', 'affiliate_portal_primary_color');
        register_setting('affiliate_portal_settings', 'affiliate_portal_secondary_color');
        register_setting('affiliate_portal_settings', 'affiliate_portal_button_color');
        register_setting('affiliate_portal_settings', 'affiliate_portal_background_color');
        register_setting('affiliate_portal_settings', 'affiliate_portal_text_color');
    }
    
    public function admin_page() {
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'settings';
        
        echo '<div class="wrap">';
        echo '<h1>Affiliate Portal</h1>';
        
        // Tab navigation
        echo '<h2 class="nav-tab-wrapper">';
        echo '<a href="?page=affiliate-portal-settings&tab=settings" class="nav-tab ' . ($active_tab == 'settings' ? 'nav-tab-active' : '') . '">Settings</a>';
        echo '<a href="?page=affiliate-portal-settings&tab=debug" class="nav-tab ' . ($active_tab == 'debug' ? 'nav-tab-active' : '') . '">Debug</a>';
        echo '</h2>';
        
        if ($active_tab == 'settings') {
            $this->settings_page();
        } else {
            $this->debug_page();
        }
        
        echo '</div>';
    }
    
    public function handle_company_documents() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'affiliate_nonce')) {
            wp_die(json_encode(['success' => false, 'data' => 'Security check failed']));
        }
        
        $user_id = intval($_POST['user_id']);
        
        // Handle file uploads
        $uploaded_files = [];
        $upload_dir = wp_upload_dir();
        $affiliate_dir = $upload_dir['basedir'] . '/affiliate-documents/' . $user_id;
        
        if (!file_exists($affiliate_dir)) {
            wp_mkdir_p($affiliate_dir);
        }
        
        $required_files = ['business_license', 'tax_certificate', 'bank_statement', 'director_id'];
        $optional_files = ['trade_license', 'financial_statements', 'authorization_letter', 'memorandum_articles'];
        
        // Check required files
        foreach ($required_files as $field) {
            if (empty($_FILES[$field]['name'])) {
                wp_die(json_encode(['success' => false, 'data' => 'Required document missing: ' . ucfirst(str_replace('_', ' ', $field))]));
            }
        }
        
        // Process file uploads
        foreach (array_merge($required_files, $optional_files) as $field) {
            if (!empty($_FILES[$field]['name'])) {
                $file = $_FILES[$field];
                $filename = sanitize_file_name($field . '_' . time() . '_' . $file['name']);
                $filepath = $affiliate_dir . '/' . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    $uploaded_files[$field] = $filepath;
                }
            }
        }
        
        // Save document data
        $document_data = [
            'user_id' => $user_id,
            'files' => $uploaded_files,
            'business_description' => sanitize_textarea_field($_POST['business_description']),
            'expected_volume' => sanitize_text_field($_POST['expected_volume']),
            'additional_notes' => sanitize_textarea_field($_POST['additional_notes']),
            'uploaded_at' => current_time('mysql'),
            'status' => 'submitted'
        ];
        
        update_option('affiliate_company_docs_' . $user_id, $document_data);
        
        wp_die(json_encode(['success' => true, 'data' => 'Documents uploaded successfully']));
    }
    
    public function load_company_documents_form() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'affiliate_nonce')) {
            wp_die('Security check failed');
        }
        
        // Load and return the company documents form
        ob_start();
        include AFFILIATE_PORTAL_PATH . 'templates/company-documents.php';
        $form_html = ob_get_clean();
        
        echo $form_html;
        wp_die();
    }
    
    public function get_countries() {
        global $wpdb;
        
        $countries = $wpdb->get_results("SELECT id, name, code, phone_code, flag_emoji FROM countries ORDER BY name ASC");
        
        if ($countries) {
            wp_send_json_success($countries);
        } else {
            wp_send_json_error('Failed to load countries');
        }
    }
    
    public function get_states() {
        if (!isset($_POST['country_id']) || empty($_POST['country_id'])) {
            wp_send_json_error('Country ID is required');
            return;
        }
        
        global $wpdb;
        $country_id = intval($_POST['country_id']);
        
        $states = $wpdb->get_results($wpdb->prepare(
            "SELECT id, name, code FROM states WHERE country_id = %d ORDER BY name ASC",
            $country_id
        ));
        
        if ($states) {
            wp_send_json_success($states);
        } else {
            wp_send_json_success(array()); // Empty array for countries without states
        }
    }
    
    public function settings_page() {
        // Handle manual page creation
        if (isset($_POST['create_pages'])) {
            $this->create_pages(true);
            update_option('affiliate_portal_pages_created', 'yes');
            echo '<div class="notice notice-success"><p>Affiliate pages created/updated successfully!</p></div>';
        }
        
        if (isset($_POST['submit'])) {
            // Handle logo upload
            if (!empty($_FILES['affiliate_portal_logo']['name'])) {
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                $uploaded_file = $_FILES['affiliate_portal_logo'];
                $upload_overrides = array('test_form' => false);
                $movefile = wp_handle_upload($uploaded_file, $upload_overrides);
                
                if ($movefile && !isset($movefile['error'])) {
                    update_option('affiliate_portal_logo', $movefile['url']);
                    echo '<div class="notice notice-success"><p>Logo uploaded successfully!</p></div>';
                } else {
                    echo '<div class="notice notice-error"><p>Logo upload failed: ' . $movefile['error'] . '</p></div>';
                }
            }
            
            // Save logo size
            if (isset($_POST['affiliate_portal_logo_size'])) {
                update_option('affiliate_portal_logo_size', intval($_POST['affiliate_portal_logo_size']));
            }
            
            // Save brand text
            if (isset($_POST['affiliate_portal_brand_title'])) {
                update_option('affiliate_portal_brand_title', sanitize_text_field($_POST['affiliate_portal_brand_title']));
            }
            if (isset($_POST['affiliate_portal_brand_slogan'])) {
                update_option('affiliate_portal_brand_slogan', sanitize_text_field($_POST['affiliate_portal_brand_slogan']));
            }
            
            echo '<div class="notice notice-success"><p>Settings saved!</p></div>';
        }
        
        $logo_url = get_option('affiliate_portal_logo', '');
        $logo_size = get_option('affiliate_portal_logo_size', 150);
        $brand_title = get_option('affiliate_portal_brand_title', 'Affiliate Portal');
        $brand_slogan = get_option('affiliate_portal_brand_slogan', 'Join our affiliate program');
        
        ?>
        <form method="post" enctype="multipart/form-data">
            <?php settings_fields('affiliate_portal_settings'); ?>
            
            <h2>Branding Settings</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Custom Logo</th>
                    <td>
                        <?php if ($logo_url): ?>
                            <img src="<?php echo esc_url($logo_url); ?>" style="max-width: <?php echo esc_attr($logo_size); ?>px; height: auto; display: block; margin-bottom: 10px;">
                            <p>Current logo (<?php echo esc_html($logo_size); ?>px width)</p>
                        <?php endif; ?>
                        <input type="file" name="affiliate_portal_logo" accept="image/*">
                        <p class="description">Upload your company logo. Recommended formats: PNG, JPG, SVG</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Logo Size</th>
                    <td>
                        <input type="range" name="affiliate_portal_logo_size" min="80" max="300" value="<?php echo esc_attr($logo_size); ?>" 
                               oninput="this.nextElementSibling.value = this.value + 'px'">
                        <output><?php echo esc_html($logo_size); ?>px</output>
                        <p class="description">Adjust the logo size (80px - 300px width)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Brand Title</th>
                    <td>
                        <input type="text" name="affiliate_portal_brand_title" value="<?php echo esc_attr($brand_title); ?>" class="regular-text">
                        <p class="description">Main headline text displayed on login/register pages</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Brand Slogan</th>
                    <td>
                        <input type="text" name="affiliate_portal_brand_slogan" value="<?php echo esc_attr($brand_slogan); ?>" class="regular-text">
                        <p class="description">Subtitle text displayed below the main title</p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
        
        <hr style="margin: 40px 0;">
        <h2>Page Management</h2>
        <p>If the affiliate pages are not showing the latest changes or missing, click the button below to manually create/update them:</p>
        
        <?php
        // Check current page status
        $login_page = get_page_by_title('Affiliate Login');
        $register_page = get_page_by_title('Affiliate Registration');
        $dashboard_page = get_page_by_title('Affiliate Dashboard');
        ?>
        
        <div style="background: #f1f1f1; padding: 15px; border-left: 4px solid #0073aa; margin: 20px 0;">
            <p><strong>Current Page Status:</strong></p>
            <ul>
                <li>Login Page: <?php echo $login_page ? '✅ Created' : '❌ Missing'; ?></li>
                <li>Registration Page: <?php echo $register_page ? '✅ Created' : '❌ Missing'; ?></li>
                <li>Dashboard Page: <?php echo $dashboard_page ? '✅ Created' : '❌ Missing'; ?></li>
            </ul>
        </div>
        
        <form method="post" style="margin-top: 20px;">
            <input type="hidden" name="create_pages" value="1">
            <button type="submit" class="button button-primary" style="font-size: 14px; padding: 8px 20px;">
                🔄 Create/Update Affiliate Pages
            </button>
            <p class="description">This will create or recreate all affiliate portal pages with the latest styling and functionality.</p>
        </form>
        
        <style>
        input[type="range"] {
            width: 300px;
            margin-right: 10px;
        }
        output {
            display: inline-block;
            min-width: 50px;
            font-weight: bold;
            color: #0073aa;
        }
        </style>
        <?php
    }
    
    public function debug_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'affiliate_users';
        
        echo '<h2>Database Status</h2>';
        
        // Check table existence
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
        echo '<p><strong>Table Name:</strong> ' . $table_name . '</p>';
        echo '<p><strong>Table Exists:</strong> ' . ($table_exists ? 'YES' : 'NO') . '</p>';
        
        if ($table_exists) {
            $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
            echo '<p><strong>Total Users:</strong> ' . $count . '</p>';
            
            // Show recent users
            if ($count > 0) {
                echo '<h3>Recent Registrations</h3>';
                $users = $wpdb->get_results("SELECT id, username, email, first_name, last_name, created_at FROM $table_name ORDER BY created_at DESC LIMIT 5");
                echo '<table class="wp-list-table widefat fixed striped">';
                echo '<thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Name</th><th>Date</th></tr></thead>';
                echo '<tbody>';
                foreach ($users as $user) {
                    echo '<tr>';
                    echo '<td>' . $user->id . '</td>';
                    echo '<td>' . $user->username . '</td>';
                    echo '<td>' . $user->email . '</td>';
                    echo '<td>' . $user->first_name . ' ' . $user->last_name . '</td>';
                    echo '<td>' . $user->created_at . '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
            }
        } else {
            echo '<p style="color: red;"><strong>Table does not exist!</strong></p>';
            echo '<p><a href="' . admin_url('plugins.php') . '" class="button">Deactivate and Reactivate Plugin</a></p>';
        }
    }
    
    public function handle_registration() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'affiliate_nonce')) {
            wp_send_json_error('Security check failed');
            return;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'affiliate_users';
        
        // Debug: Log all POST data received
        error_log('Affiliate Portal: Registration POST data: ' . print_r($_POST, true));
        error_log('Affiliate Portal: Using table name: ' . $table_name);
        
        try {
            // Sanitize and validate input
            $username = sanitize_user($_POST['username']);
            $email = sanitize_email($_POST['email']);
            $password = $_POST['password'];
            
            // Enhanced validation for all required fields
            // Check both 'type' and 'account_type' to support both form versions
            $account_type = $_POST['type'] ?? $_POST['account_type'] ?? '';
            
            $required_fields = [
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'security_que' => $_POST['security_que'] ?? '',
                'security_ans' => $_POST['security_ans'] ?? '',
                'name_prefix' => $_POST['name_prefix'] ?? '',
                'first_name' => $_POST['first_name'] ?? '',
                'last_name' => $_POST['last_name'] ?? '',
                'dob' => $_POST['dob'] ?? '',
                'type' => $account_type,
                'country_code' => $_POST['country_code'] ?? '',
                'mobile_number' => $_POST['mobile_number'] ?? '',
                'address_line1' => $_POST['address_line1'] ?? '',
                'city' => $_POST['city'] ?? '',
                'country' => $_POST['country'] ?? '',
                'state' => $_POST['state'] ?? '',
                'zipcode' => $_POST['zipcode'] ?? '',
                'chat_id_channel' => $_POST['chat_id_channel'] ?? '',
                'affiliate_type' => $_POST['affiliate_type'] ?? '',
                'currency' => $_POST['currency'] ?? ''
            ];
            
            // Determine if this is Portuguese version based on referrer or language context
            $is_portuguese = false;
            if (isset($_SERVER['HTTP_REFERER'])) {
                $referrer = $_SERVER['HTTP_REFERER'];
                if (strpos($referrer, 'registro-afiliado') !== false || strpos($referrer, 'afiliado') !== false) {
                    $is_portuguese = true;
                }
            }
            
            // Field name mapping for localized error messages
            $field_labels = [];
            if ($is_portuguese) {
                $field_labels = [
                    'username' => 'Nome de usuário',
                    'email' => 'Email',
                    'password' => 'Senha',
                    'security_que' => 'Pergunta de segurança',
                    'security_ans' => 'Resposta de segurança',
                    'name_prefix' => 'Prefixo do nome',
                    'first_name' => 'Primeiro nome',
                    'last_name' => 'Sobrenome',
                    'dob' => 'Data de nascimento',
                    'type' => 'Tipo de conta',
                    'country_code' => 'Código do país',
                    'mobile_number' => 'Número do celular',
                    'address_line1' => 'Endereço 1',
                    'city' => 'Cidade',
                    'country' => 'País',
                    'state' => 'Estado',
                    'zipcode' => 'CEP',
                    'chat_id_channel' => 'Canal de chat ID',
                    'affiliate_type' => 'Tipo de afiliado',
                    'currency' => 'Moeda'
                ];
            } else {
                $field_labels = [
                    'username' => 'Username',
                    'email' => 'Email',
                    'password' => 'Password',
                    'security_que' => 'Security question',
                    'security_ans' => 'Security answer',
                    'name_prefix' => 'Name prefix',
                    'first_name' => 'First name',
                    'last_name' => 'Last name',
                    'dob' => 'Date of birth',
                    'type' => 'Account type',
                    'country_code' => 'Country code',
                    'mobile_number' => 'Mobile number',
                    'address_line1' => 'Address line 1',
                    'city' => 'City',
                    'country' => 'Country',
                    'state' => 'State',
                    'zipcode' => 'ZIP code',
                    'chat_id_channel' => 'Chat ID channel',
                    'affiliate_type' => 'Affiliate type',
                    'currency' => 'Currency'
                ];
            }
            
            $missing_fields = [];
            foreach ($required_fields as $field => $value) {
                if (empty(trim($value))) {
                    $missing_fields[] = $field_labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
                }
            }
            
            if (!empty($missing_fields)) {
                $error_message = $is_portuguese ? 
                    'Os seguintes campos são obrigatórios: ' . implode(', ', $missing_fields) :
                    'The following fields are required: ' . implode(', ', $missing_fields);
                wp_send_json_error($error_message);
                return;
            }
            
            // Check if username or email already exists
            $existing_user = $wpdb->get_row($wpdb->prepare(
                "SELECT id FROM $table_name WHERE username = %s OR email = %s",
                $username, $email
            ));
            
            // Debug: Log the check result
            error_log('Affiliate Portal: Checking existing user for username: ' . $username . ', email: ' . $email);
            error_log('Affiliate Portal: Existing user result: ' . ($existing_user ? 'Found' : 'Not found'));
            
            if ($existing_user) {
                $error_message = $is_portuguese ? 'Nome de usuário ou email já existe' : 'Username or email already exists';
                wp_send_json_error($error_message);
                return;
            }
            
            // Hash password
            $hashed_password = wp_hash_password($password);
            
            // Prepare data for insertion using correct form field names
            $data = array(
                'username' => $username,
                'password' => $hashed_password,
                'security_que' => isset($_POST['security_que']) ? sanitize_text_field($_POST['security_que']) : '',
                'security_ans' => isset($_POST['security_ans']) ? sanitize_text_field($_POST['security_ans']) : '',
                'name_prefix' => isset($_POST['name_prefix']) ? sanitize_text_field($_POST['name_prefix']) : '',
                'first_name' => isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '',
                'last_name' => isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '',
                'dob' => isset($_POST['dob']) ? sanitize_text_field($_POST['dob']) : null,
                'type' => isset($_POST['type']) ? sanitize_text_field($_POST['type']) : (isset($_POST['account_type']) ? sanitize_text_field($_POST['account_type']) : ''),
                'email' => $email,
                'company_name' => isset($_POST['company_name']) ? sanitize_text_field($_POST['company_name']) : '',
                'country_code' => isset($_POST['country_code']) ? preg_replace('/^\+/', '', sanitize_text_field($_POST['country_code'])) : '',
                'mobile_number' => isset($_POST['mobile_number']) ? sanitize_text_field($_POST['mobile_number']) : '',
                'address_line1' => isset($_POST['address_line1']) ? sanitize_text_field($_POST['address_line1']) : '',
                'address_line2' => isset($_POST['address_line2']) ? sanitize_text_field($_POST['address_line2']) : '',
                'city' => isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '',
                'country' => isset($_POST['country']) ? sanitize_text_field($_POST['country']) : '',
                'state' => isset($_POST['state']) ? sanitize_text_field($_POST['state']) : '',
                'zipcode' => isset($_POST['zipcode']) ? sanitize_text_field($_POST['zipcode']) : '',
                'chat_id_channel' => isset($_POST['chat_id_channel']) ? sanitize_text_field($_POST['chat_id_channel']) : '',
                'affiliate_type' => isset($_POST['affiliate_type']) ? sanitize_text_field($_POST['affiliate_type']) : '',
                'currency' => isset($_POST['currency']) ? sanitize_text_field($_POST['currency']) : ''
                // Note: status column will use default value 'awaiting approval' from database
            );
            
            // Debug: Log prepared data
            error_log('Affiliate Portal: Prepared data for insertion: ' . print_r($data, true));
            
            // Insert into database
            $result = $wpdb->insert($table_name, $data);
            
            if ($result !== false) {
                $user_id = $wpdb->insert_id;
                error_log('Affiliate Portal: User registered successfully with ID: ' . $user_id);
                
                // Verify the insertion by retrieving the user
                $inserted_user = $wpdb->get_row($wpdb->prepare(
                    "SELECT * FROM $table_name WHERE id = %d",
                    $user_id
                ));
                error_log('Affiliate Portal: Inserted user data: ' . print_r($inserted_user, true));
                
                // Send confirmation email to user
                $this->send_user_confirmation_email($data['email'], $data['first_name'], $data['last_name']);
                
                // Send notification to admins
                $this->send_registration_notification($user_id, $data);
                
                // Automatically log in the user after registration - COOKIE ONLY
                // REMOVED: All session management to prevent data sharing across users
                
                // Create database session for secure authentication
                $user_obj = (object) array_merge($data, array('id' => $user_id));
                $session_id = $this->create_session($user_obj);
                if ($session_id) {
                    $this->set_session_cookie($session_id, $user_id, $data['username']);
                }
                
                error_log('Registration Auto-Login - User ID: ' . $user_id);
                error_log('Registration Auto-Login - Username: ' . $data['username']);
                error_log('Registration Auto-Login - Cookie-only authentication (no sessions)');
                
                // Determine redirect URL to dashboard based on language context
                $redirect_url = get_permalink(get_page_by_title('Affiliate Dashboard'));
                $success_message = 'Registration successful! You are now logged in and redirected to your dashboard.';
                
                if ($is_portuguese) {
                    $pt_dashboard = get_page_by_title('Painel do Afiliado');
                    if ($pt_dashboard) {
                        $redirect_url = get_permalink($pt_dashboard);
                    }
                    $success_message = 'Registro realizado com sucesso! Você foi conectado e redirecionado para seu painel.';
                }
                
                wp_send_json_success(array(
                    'message' => $success_message,
                    'redirect' => $redirect_url
                ));
            } else {
                error_log('Affiliate Registration Error: Database insertion failed. Error: ' . $wpdb->last_error);
                error_log('Affiliate Registration Error: Last query: ' . $wpdb->last_query);
                wp_send_json_error('Registration failed: ' . $wpdb->last_error);
            }
            
        } catch (Exception $e) {
            error_log('Affiliate Registration Exception: ' . $e->getMessage());
            wp_send_json_error('Registration failed: ' . $e->getMessage());
        }
    }
    
    public function handle_login() {
        // Verify nonce with better error handling
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'affiliate_nonce')) {
            error_log('Nonce verification failed - nonce: ' . ($_POST['nonce'] ?? 'missing'));
            wp_send_json_error('Security verification failed. Please refresh the page and try again.');
            return;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'affiliate_users';
        
        try {
            $username = sanitize_user($_POST['username']);
            $password = $_POST['password'];
            
            if (empty($username) || empty($password)) {
                // Check if this is Portuguese context
                $is_portuguese = false;
                if (isset($_SERVER['HTTP_REFERER'])) {
                    $referrer = $_SERVER['HTTP_REFERER'];
                    if (strpos($referrer, 'afiliado-login') !== false || strpos($referrer, 'afiliado') !== false) {
                        $is_portuguese = true;
                    }
                }
                
                $error_message = $is_portuguese ? 'Por favor, preencha todos os campos' : 'Please fill in all fields';
                wp_send_json_error($error_message);
                return;
            }
            
            // Get user from database (check both username and email)
            $user = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_name WHERE username = %s OR email = %s",
                $username, $username
            ));
            
            if ($user && wp_check_password($password, $user->password)) {
                // CRITICAL FIX: Remove all server-side sessions to prevent user data mixing
                // No session_start() - use ONLY cookie-based authentication
                
                // Clear WordPress object cache for any cached user data
                if (function_exists('wp_cache_delete')) {
                    wp_cache_delete($user->id, 'affiliate_users');
                    wp_cache_delete($user->id, 'users');
                    wp_cache_delete('affiliate_user_' . $user->id, 'default');
                }
                
                // Clear any existing session data if session was started elsewhere
                if (session_id()) {
                    $_SESSION = array(); // Clear all session data
                    session_destroy(); // Destroy the session completely
                    error_log('SECURITY: Destroyed server session during login to prevent data mixing');
                }
                
                // Force refresh cache and prevent browser caching of user data
                if (!headers_sent()) {
                    header('Cache-Control: no-cache, no-store, must-revalidate');
                    header('Pragma: no-cache');
                    header('Expires: 0');
                }
                
                // CRITICAL FIX: Clear ALL existing sessions and cookies before setting new ones
                
                // First, destroy any existing sessions for ANY user that might be active
                global $wpdb;
                $sessions_table = $wpdb->prefix . 'affiliate_sessions';
                
                // Clear any existing session if user has one already
                $existing_session = $_COOKIE['affiliate_session'] ?? '';
                if ($existing_session) {
                    $wpdb->delete($sessions_table, array('session_id' => $existing_session), array('%s'));
                    error_log('Affiliate Portal: Cleared existing session during login: ' . substr($existing_session, 0, 20) . '...');
                }
                
                // Also clear any sessions for this specific user (prevent duplicate sessions)
                $existing_user_sessions = $wpdb->delete($sessions_table, array('user_id' => intval($user->id)), array('%d'));
                if ($existing_user_sessions > 0) {
                    error_log('Affiliate Portal: Cleared ' . $existing_user_sessions . ' existing sessions for user: ' . $user->username);
                }
                
                if (!headers_sent()) {
                    $is_secure = is_ssl() || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
                    
                    // Clear ALL possible old cookies completely
                    $cookies_to_clear = ['affiliate_session', 'affiliate_user_id', 'affiliate_username'];
                    $paths = ['/', '/wp-admin/', '/wp-content/', ''];
                    $domains = ['', $_SERVER['HTTP_HOST'] ?? ''];
                    
                    foreach ($cookies_to_clear as $cookie_name) {
                        foreach ($paths as $path) {
                            foreach ($domains as $domain) {
                                setcookie($cookie_name, '', time() - 3600, $path, $domain, false, false);
                                setcookie($cookie_name, '', time() - 3600, $path, $domain, $is_secure, true);
                            }
                        }
                    }
                    
                    // Also clear from current request
                    unset($_COOKIE['affiliate_session']);
                    unset($_COOKIE['affiliate_user_id']);
                    unset($_COOKIE['affiliate_username']);
                    
                    error_log('Affiliate Portal: Cleared all old cookies and sessions before new login');
                }
                
                // SECURITY: Use database session-based authentication
                $session_id = $this->create_session($user);
                
                if (!$session_id) {
                    wp_send_json_error('Failed to create session. Please try again.');
                    return;
                }
                
                $numeric_user_id = intval($user->id);
                if ($numeric_user_id <= 0) {
                    error_log('CRITICAL ERROR: Invalid user ID detected! User ID: ' . $user->id . ', Username: ' . $user->username);
                    wp_send_json_error('Invalid user ID. Please contact support.');
                    return;
                }
                
                // Set session cookies FIRST before any cache clearing
                $cookie_set = $this->set_session_cookie($session_id, $numeric_user_id, $user->username);
                
                if (!$cookie_set) {
                    wp_send_json_error('Failed to set session cookies. Please try again.');
                    return;
                }
                
                // CRITICAL: Clear cache AFTER setting session cookies to prevent interference
                wp_cache_flush();
                
                error_log('SECURITY: New session created for user ' . $user->username . ' (ID: ' . $numeric_user_id . ') with session ID: ' . substr($session_id, 0, 20) . '...');
                
                // REMOVED: $_SESSION['affiliate_user'] array to prevent cached user data issues
                
                // Update last login time in database
                $wpdb->update(
                    $table_name,
                    array('last_login' => current_time('mysql')),
                    array('id' => $user->id)
                );
                
                // SECURITY: No cookies set - session only authentication for better security
                
                // Log successful login with session info
                error_log('Affiliate Portal: User ' . $username . ' logged in successfully. Session ID: ' . session_id() . ', User ID: ' . $user->id);
                
                // Determine redirect URL based on current page language
                $redirect_url = get_permalink(get_page_by_title('Affiliate Dashboard'));
                
                // Check if user is on Portuguese login page to redirect to Portuguese dashboard
                if (isset($_POST['language']) && $_POST['language'] === 'pt') {
                    $pt_dashboard = get_page_by_title('Painel do Afiliado');
                    if ($pt_dashboard) {
                        $redirect_url = get_permalink($pt_dashboard);
                    }
                } else {
                    // Check referrer to determine language
                    $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
                    if (strpos($referrer, 'afiliado-login') !== false) {
                        $pt_dashboard = get_page_by_title('Painel do Afiliado');
                        if ($pt_dashboard) {
                            $redirect_url = get_permalink($pt_dashboard);
                        }
                    }
                }
                
                // Determine success message based on context
                $success_message = 'Login successful! Redirecting to dashboard...';
                if (isset($_SERVER['HTTP_REFERER'])) {
                    $referrer = $_SERVER['HTTP_REFERER'];
                    if (strpos($referrer, 'afiliado-login') !== false || strpos($referrer, 'afiliado') !== false) {
                        $success_message = 'Login realizado com sucesso! Redirecionando para o painel...';
                    }
                }
                
                wp_send_json_success(array(
                    'message' => $success_message,
                    'redirect' => $redirect_url
                ));
            } else {
                // Check if this is Portuguese context  
                $is_portuguese = false;
                if (isset($_SERVER['HTTP_REFERER'])) {
                    $referrer = $_SERVER['HTTP_REFERER'];
                    if (strpos($referrer, 'afiliado-login') !== false || strpos($referrer, 'afiliado') !== false) {
                        $is_portuguese = true;
                    }
                }
                
                $error_message = $is_portuguese ? 'Nome de usuário ou senha inválidos' : 'Invalid username or password';
                wp_send_json_error($error_message);
            }
            
        } catch (Exception $e) {
            error_log('Affiliate Login Error: ' . $e->getMessage());
            wp_send_json_error('Login failed. Please try again.');
        }
    }
    
    public function handle_logout() {
        // Log logout for security audit - get user info from cookies
        $user_id = $_COOKIE['affiliate_user_id'] ?? 'unknown';
        $username = $_COOKIE['affiliate_username'] ?? 'unknown';
        $session_id = $_COOKIE['affiliate_session'] ?? null;
        
        error_log('Affiliate Portal: User logout initiated - User ID: ' . $user_id . ', Username: ' . $username);
        
        // CRITICAL FIX: Destroy the database session completely
        if ($session_id) {
            $this->destroy_session($session_id);
            error_log('Affiliate Portal: Database session destroyed for session ID: ' . $session_id);
        }
        
        // CRITICAL FIX: Destroy ALL sessions for this user to prevent cross-contamination
        if ($user_id && is_numeric($user_id)) {
            global $wpdb;
            $sessions_table = $wpdb->prefix . 'affiliate_sessions';
            
            // Get all sessions for this user before deletion
            $existing_sessions = $wpdb->get_results($wpdb->prepare(
                "SELECT session_id FROM $sessions_table WHERE user_id = %d", 
                intval($user_id)
            ));
            
            // Delete all sessions for this user
            $deleted = $wpdb->delete($sessions_table, array('user_id' => intval($user_id)), array('%d'));
            
            if ($deleted > 0) {
                error_log('Affiliate Portal: Destroyed ' . $deleted . ' sessions for user ID: ' . $user_id);
                foreach ($existing_sessions as $session) {
                    error_log('Affiliate Portal: Destroyed session: ' . $session->session_id);
                }
            }
            
            // Additional cleanup - clear any stray sessions that might have same username
            if ($username && $username !== 'unknown') {
                $deleted_by_username = $wpdb->delete($sessions_table, array('username' => $username), array('%s'));
                if ($deleted_by_username > 0) {
                    error_log('Affiliate Portal: Destroyed ' . $deleted_by_username . ' additional sessions by username: ' . $username);
                }
            }
        }
        
        // CRITICAL: Clear all cached data to prevent session contamination
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        // Clear session cookies
        $this->clear_session_cookie();
        
        // Additional cookie clearing to ensure complete removal
        if (!headers_sent()) {
            // Clear with multiple domain/path combinations to ensure removal
            $domains = ['', $_SERVER['HTTP_HOST'] ?? ''];
            $paths = ['/', '/wp-admin/', '/wp-content/'];
            
            foreach ($domains as $domain) {
                foreach ($paths as $path) {
                    // Clear ALL affiliate cookies across domains and paths
                    setcookie('affiliate_session', '', time() - 3600, $path, $domain, false, true);
                    setcookie('affiliate_session', '', time() - 3600, $path, $domain, true, true); // Also secure version
                    setcookie('affiliate_user_id', '', time() - 3600, $path, $domain);
                    setcookie('affiliate_username', '', time() - 3600, $path, $domain);
                }
            }
            
            // Also try to unset cookies in current request
            unset($_COOKIE['affiliate_session']); // CRITICAL: Clear session cookie
            unset($_COOKIE['affiliate_user_id']);
            unset($_COOKIE['affiliate_username']);
            
            error_log('Affiliate Portal: Cleared session cookies - Session, User ID, Username');
            
            error_log('Affiliate Portal: All cookies cleared during logout');
        }
        
        // Clear WordPress object cache for this user to prevent cached data leakage
        if (function_exists('wp_cache_delete')) {
            wp_cache_delete($user_id, 'affiliate_users');
            wp_cache_delete($user_id, 'users');
            wp_cache_delete('affiliate_user_' . $user_id, 'default');
        }
        
        // Determine redirect based on referrer or language preference
        $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $redirect_url = '';
        
        // Check if the referrer contains Portuguese-specific pages or URLs
        if (strpos($referrer, 'painel-afiliado') !== false || 
            strpos($referrer, 'afiliado-') !== false || 
            strpos($referrer, '/afiliado') !== false) {
            // Portuguese context - redirect to Portuguese login
            $pt_login = get_page_by_title('Login do Afiliado');
            if ($pt_login) {
                $redirect_url = get_permalink($pt_login);
            } else {
                // Fallback: try to get Portuguese login page by slug
                $pt_login_by_slug = get_page_by_path('afiliado-login');
                if ($pt_login_by_slug) {
                    $redirect_url = get_permalink($pt_login_by_slug);
                }
            }
        } else {
            // English context - redirect to English login
            $en_login = get_page_by_title('Affiliate Login');
            if ($en_login) {
                $redirect_url = get_permalink($en_login);
            } else {
                // Fallback: try to get English login page by slug
                $en_login_by_slug = get_page_by_path('affiliate-login');
                if ($en_login_by_slug) {
                    $redirect_url = get_permalink($en_login_by_slug);
                }
            }
        }
        
        // Final fallback if no page found
        if (empty($redirect_url)) {
            $redirect_url = home_url('/affiliate-login/');
        }
        
        wp_send_json_success(array(
            'message' => 'Logged out successfully',
            'redirect' => $redirect_url
        ));
    }
    
    public function handle_change_password() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'affiliate_nonce')) {
            wp_send_json_error('Security check failed');
            return;
        }
        
        // Start session if not already started
        if (!session_id()) {
            session_start();
        }
        
        // Check if user is logged in
        $user_id = $this->get_current_user_id();
        if (!$user_id) {
            wp_send_json_error('Please log in to change password');
            return;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'affiliate_users';
        
        try {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            
            if (empty($current_password) || empty($new_password)) {
                wp_send_json_error('Please fill in all fields');
                return;
            }
            
            if (strlen($new_password) < 8) {
                wp_send_json_error('New password must be at least 8 characters long');
                return;
            }
            
            // Get current user
            $user = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d",
                $user_id
            ));
            
            if (!$user) {
                wp_send_json_error('User not found');
                return;
            }
            
            // Verify current password
            if (!wp_check_password($current_password, $user->password)) {
                wp_send_json_error('Current password is incorrect');
                return;
            }
            
            // Update password
            $new_password_hash = wp_hash_password($new_password);
            $updated = $wpdb->update(
                $table_name,
                array('password' => $new_password_hash),
                array('id' => $user->id)
            );
            
            if ($updated !== false) {
                wp_send_json_success('Password updated successfully');
            } else {
                wp_send_json_error('Failed to update password. Please try again.');
            }
            
        } catch (Exception $e) {
            error_log('Affiliate Change Password Error: ' . $e->getMessage());
            wp_send_json_error('An error occurred. Please try again.');
        }
    }
    
    // Admin shortcode functions
    public function admin_login_shortcode($atts) {
        ob_start();
        include AFFILIATE_PORTAL_PATH . 'templates/admin-login.php';
        return ob_get_clean();
    }
    
    public function admin_dashboard_shortcode($atts) {
        // REMOVED: Session initialization - using cookie-only authentication
        
        // Check if admin is authenticated using cookies
        if (!$this->is_admin_authenticated()) {
            return '<div class="affiliate-alert affiliate-alert-warning">Please <a href="' . get_permalink(get_page_by_title('Admin Login')) . '">log in</a> to access the admin dashboard.</div>';
        }
        
        ob_start();
        include AFFILIATE_PORTAL_PATH . 'templates/admin-dashboard.php';
        return ob_get_clean();
    }
    
    // Portuguese shortcode functions
    public function login_shortcode_pt($atts) {
        ob_start();
        include AFFILIATE_PORTAL_PATH . 'templates/pt/login-form.php';
        return ob_get_clean();
    }
    
    public function register_shortcode_pt($atts) {
        ob_start();
        include AFFILIATE_PORTAL_PATH . 'templates/pt/register-form.php';
        return ob_get_clean();
    }
    
    public function dashboard_shortcode_pt($atts) {
        // REMOVED: Session initialization - using cookie-only authentication
        
        // Force cache-busting headers to prevent browser caching of user data
        if (!headers_sent()) {
            header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        }
        
        ob_start();
        
        // Check if user is logged in using cookie authentication only
        if (!$this->is_user_authenticated()) {
            echo '<div class="affiliate-alert affiliate-alert-warning">Por favor <a href="' . get_permalink(get_page_by_path('afiliado-login')) . '">faça login</a> para acessar o painel.</div>';
            return ob_get_clean();
        }
        
        // REMOVED: Session validation - using pure cookie authentication
        
        // Include the Portuguese dashboard template
        include AFFILIATE_PORTAL_PATH . 'templates/pt/dashboard-enhanced.php';
        
        return ob_get_clean();
    }
    
    public function admin_login_shortcode_pt($atts) {
        ob_start();
        include AFFILIATE_PORTAL_PATH . 'templates/pt/admin-login.php';
        return ob_get_clean();
    }
    
    public function admin_dashboard_shortcode_pt($atts) {
        // REMOVED: Session initialization - using cookie-only authentication
        
        // Check if admin is authenticated using cookies
        if (!$this->is_admin_authenticated()) {
            return '<div class="affiliate-alert affiliate-alert-warning">Por favor <a href="' . get_permalink(get_page_by_path('admin-login-pt')) . '">faça login</a> para acessar o painel administrativo.</div>';
        }
        
        ob_start();
        include AFFILIATE_PORTAL_PATH . 'templates/pt/admin-dashboard.php';
        return ob_get_clean();
    }
    
    // Admin AJAX handlers
    public function handle_admin_login() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'affiliate_nonce')) {
            error_log('Admin nonce verification failed - nonce: ' . ($_POST['nonce'] ?? 'missing'));
            wp_send_json_error('Security verification failed. Please refresh the page and try again.');
            return;
        }
        
        global $wpdb;
        $admin_table = $wpdb->prefix . 'affiliate_admin';
        
        $username = sanitize_text_field($_POST['admin_username']);
        $password = $_POST['admin_password'];
        
        error_log('Admin login attempt for username: ' . $username);
        
        if (empty($username) || empty($password)) {
            wp_send_json_error('Please fill in all fields');
            return;
        }
        
        $admin = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $admin_table WHERE username = %s AND status = 'active'",
            $username
        ));
        
        if (!$admin) {
            error_log('Admin not found for username: ' . $username);
            wp_send_json_error('Invalid username or password');
            return;
        }
        
        error_log('Admin found, checking password...');
        error_log('Stored hash: ' . $admin->password);
        
        // Temporary simple password check for debugging
        if ($password === $admin->password || wp_check_password($password, $admin->password)) {
            error_log('Password verified successfully');
            
            // Create database session for admin authentication
            $admin_session_id = $this->create_admin_session($admin);
            
            if (!$admin_session_id) {
                wp_send_json_error('Failed to create admin session. Please try again.');
                return;
            }
            
            $this->set_admin_session_cookie($admin_session_id, $admin->id, $admin->username);
            
            error_log('Admin Login - Database session authentication');
            
            // Update last login
            $wpdb->update(
                $admin_table,
                array('last_login' => current_time('mysql')),
                array('id' => $admin->id)
            );
            
            error_log('Admin login successful, redirecting...');
            
            // Determine redirect URL with fallbacks
            $redirect_url = '';
            $admin_dashboard = get_page_by_title('Admin Dashboard');
            if ($admin_dashboard) {
                $redirect_url = get_permalink($admin_dashboard);
            } else {
                // Fallback: try to get admin dashboard page by slug
                $admin_dashboard_by_slug = get_page_by_path('admin-dashboard');
                if ($admin_dashboard_by_slug) {
                    $redirect_url = get_permalink($admin_dashboard_by_slug);
                } else {
                    $redirect_url = home_url('/admin-dashboard/');
                }
            }
            
            error_log('Admin redirect URL: ' . $redirect_url);
            
            wp_send_json_success(array(
                'message' => 'Login successful',
                'redirect' => $redirect_url
            ));
        } else {
            error_log('Password verification failed');
            wp_send_json_error('Invalid username or password');
        }
    }
    
    public function handle_admin_logout() {
        // Get admin session ID and destroy it
        $admin_session_id = $_COOKIE['affiliate_admin_session'] ?? null;
        
        if ($admin_session_id) {
            $this->destroy_session($admin_session_id);
        }
        
        // Clear admin session cookies
        $this->clear_admin_session_cookie();
        
        wp_send_json_success(array(
            'message' => 'Logged out successfully',
            'redirect' => get_permalink(get_page_by_title('Admin Login'))
        ));
    }
    
    public function get_admin_applications() {
        if (!$this->is_admin_logged_in()) {
            wp_send_json_error('Unauthorized access');
            return;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'affiliate_users';
        
        // Get pagination parameters
        $page = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
        $per_page = isset($_POST['per_page']) ? max(1, min(100, intval($_POST['per_page']))) : 25;
        $status_filter = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
        
        // Build WHERE clause
        $where_clause = '';
        $where_args = array();
        if (!empty($status_filter)) {
            $where_clause = " WHERE status = %s";
            $where_args[] = $status_filter;
        }
        
        // Get total count for pagination
        $total_query = "SELECT COUNT(*) FROM $table_name$where_clause";
        if (!empty($where_args)) {
            $total_records = $wpdb->get_var($wpdb->prepare($total_query, $where_args));
        } else {
            $total_records = $wpdb->get_var($total_query);
        }
        
        // Calculate pagination
        $total_pages = ceil($total_records / $per_page);
        $offset = ($page - 1) * $per_page;
        
        // Get paginated results
        $limit_clause = $wpdb->prepare(" LIMIT %d OFFSET %d", $per_page, $offset);
        $applications_query = "SELECT * FROM $table_name$where_clause ORDER BY created_at DESC$limit_clause";
        
        if (!empty($where_args)) {
            $applications = $wpdb->get_results($wpdb->prepare($applications_query, $where_args));
        } else {
            $applications = $wpdb->get_results($applications_query);
        }
        
        // Get statistics (always get full counts regardless of pagination)
        $stats = array(
            'pending' => $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'awaiting approval'"),
            'approved' => $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'approved'"),
            'rejected' => $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'rejected'")
        );
        
        // Prepare pagination info
        $pagination = array(
            'current_page' => $page,
            'per_page' => $per_page,
            'total_pages' => $total_pages,
            'total_records' => intval($total_records),
            'has_next' => $page < $total_pages,
            'has_prev' => $page > 1
        );
        
        wp_send_json_success(array(
            'applications' => $applications,
            'stats' => $stats,
            'pagination' => $pagination
        ));
    }
    
    public function handle_get_registration_details() {
        if (!wp_verify_nonce($_POST['nonce'], 'affiliate_nonce')) {
            wp_send_json_error('Security check failed');
            return;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'affiliate_users';
        
        $application_id = intval($_POST['application_id']);
        
        if (empty($application_id)) {
            wp_send_json_error('Missing application ID');
            return;
        }
        
        $user = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE id = %d",
            $application_id
        ));
        
        if (!$user) {
            wp_send_json_error('User not found');
            return;
        }
        
        wp_send_json_success($user);
    }
    
    public function handle_admin_update_status() {
        if (!$this->is_admin_logged_in()) {
            wp_send_json_error('Unauthorized access');
            return;
        }
        
        if (!wp_verify_nonce($_POST['nonce'], 'affiliate_nonce')) {
            wp_send_json_error('Security check failed');
            return;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'affiliate_users';
        
        $application_id = intval($_POST['application_id']);
        $new_status = sanitize_text_field($_POST['status']);
        $remarks = sanitize_textarea_field($_POST['remarks']);
        
        if (empty($application_id) || empty($new_status)) {
            wp_send_json_error('Missing required fields');
            return;
        }
        
        $result = $wpdb->update(
            $table_name,
            array(
                'status' => $new_status,
                'admin_remarks' => $remarks,
                'updated_at' => current_time('mysql')
            ),
            array('id' => $application_id)
        );
        
        if ($result !== false) {
            // Send status update notification to user
            $this->send_status_update_notification($application_id, $new_status, $remarks);
            wp_send_json_success('Status updated successfully');
        } else {
            wp_send_json_error('Failed to update status');
        }
    }
    
    public function handle_admin_update_email_config() {
        if (!$this->is_admin_logged_in()) {
            wp_send_json_error('Unauthorized access');
            return;
        }
        
        if (!wp_verify_nonce($_POST['nonce'], 'affiliate_nonce')) {
            wp_send_json_error('Security check failed');
            return;
        }
        
        global $wpdb;
        $email_config_table = $wpdb->prefix . 'affiliate_email_config';
        
        // Log the received data for debugging
        error_log('Affiliate Portal: Email config update - POST data: ' . print_r($_POST, true));
        
        $configs = array(
            'notification_emails' => sanitize_textarea_field($_POST['notification_emails'] ?? ''),
            'from_email' => sanitize_email($_POST['from_email'] ?? ''),
            'from_name' => sanitize_text_field($_POST['from_name'] ?? '')
        );
        
        error_log('Affiliate Portal: Email config update - Configs to save: ' . print_r($configs, true));
        
        $success = true;
        $failed_updates = array();
        
        foreach ($configs as $key => $value) {
            $result = $wpdb->replace(
                $email_config_table,
                array(
                    'config_key' => $key,
                    'config_value' => $value,
                    'updated_at' => current_time('mysql')
                )
            );
            
            if ($result === false) {
                $success = false;
                $failed_updates[] = $key;
                error_log('Affiliate Portal: Failed to update email config for key: ' . $key . ', Error: ' . $wpdb->last_error);
            } else {
                error_log('Affiliate Portal: Successfully updated email config for key: ' . $key);
            }
        }
        
        if ($success) {
            error_log('Affiliate Portal: All email configuration updated successfully');
            wp_send_json_success('Email configuration updated successfully');
        } else {
            error_log('Affiliate Portal: Failed to update email configuration. Failed keys: ' . implode(', ', $failed_updates));
            wp_send_json_error('Failed to update email configuration: ' . implode(', ', $failed_updates));
        }
    }
    
    // Email notification function
    public function send_registration_notification($user_id, $user_data) {
        global $wpdb;
        $email_config_table = $wpdb->prefix . 'affiliate_email_config';
        
        // Get email configuration
        $notification_emails = $wpdb->get_var($wpdb->prepare(
            "SELECT config_value FROM $email_config_table WHERE config_key = %s",
            'notification_emails'
        ));
        
        $from_email = $wpdb->get_var($wpdb->prepare(
            "SELECT config_value FROM $email_config_table WHERE config_key = %s",
            'from_email'
        ));
        
        $from_name = $wpdb->get_var($wpdb->prepare(
            "SELECT config_value FROM $email_config_table WHERE config_key = %s",
            'from_name'
        ));
        
        if (empty($notification_emails)) {
            return; // No emails configured
        }
        
        $to_emails = array_map('trim', explode(',', $notification_emails));
        
        $subject = 'New Affiliate Registration - ' . $user_data['first_name'] . ' ' . $user_data['last_name'];
        
        $message = "
        <h2>New Affiliate Registration</h2>
        <p>A new affiliate has registered and is pending KYC completion.</p>
        
        <h3>Registration Details:</h3>
        <ul>
            <li><strong>Name:</strong> {$user_data['name_prefix']} {$user_data['first_name']} {$user_data['last_name']}</li>
            <li><strong>Email:</strong> {$user_data['email']}</li>
            <li><strong>Company:</strong> {$user_data['company_name']}</li>
            <li><strong>Country:</strong> {$user_data['country']}</li>
            <li><strong>Affiliate Type:</strong> {$user_data['affiliate_type']}</li>
            <li><strong>Mobile:</strong> {$user_data['country_code']} {$user_data['mobile_number']}</li>
            <li><strong>Status:</strong> KYC Pending</li>
        </ul>
        
        <p>The user needs to complete KYC verification before approval can be processed.</p>
        <p>Please log in to the admin portal to review this application once KYC is submitted.</p>
        ";
        
        // Send welcome email to user with KYC instruction
        $this->send_welcome_email_with_kyc($user_data);
        
        $headers = array();
        if (!empty($from_email)) {
            $headers[] = 'From: ' . $from_name . ' <' . $from_email . '>';
        } else {
            // Use default sender if no custom email configured
            $headers[] = 'From: GEM AFFILIATE <support@gem-affiliate.com>';
        }
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        
        foreach ($to_emails as $email) {
            if (is_email($email)) {
                wp_mail($email, $subject, $message, $headers);
            }
        }
    }
    
    // Send welcome email with KYC instructions
    private function send_welcome_email_with_kyc($user_data) {
        // Detect language from data or use default
        $is_portuguese = false;
        if (isset($user_data['language']) && $user_data['language'] === 'pt') {
            $is_portuguese = true;
        }
        
        if ($is_portuguese) {
            $subject = 'Bem-vindo! Complete sua Verificação KYC - GEM AFFILIATE';
            $message = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff;'>
                <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center;'>
                    <h1 style='margin: 0; font-size: 28px;'>Bem-vindo ao GEM AFFILIATE!</h1>
                </div>
                
                <div style='padding: 30px;'>
                    <p>Prezado(a) {$user_data['first_name']} {$user_data['last_name']},</p>
                    
                    <p>Obrigado por se registrar conosco! Sua conta foi criada com sucesso.</p>
                    
                    <div style='background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 20px; margin: 20px 0;'>
                        <h3 style='color: #856404; margin: 0 0 15px 0;'>🔔 Ação Necessária: Complete sua Verificação KYC</h3>
                        <p style='margin: 0; color: #856404;'>Para ativar sua conta e começar a ganhar comissões, você precisa completar o processo de verificação KYC (Conheça Seu Cliente).</p>
                    </div>
                    
                    <h3>Próximos passos:</h3>
                    <ol style='line-height: 1.6;'>
                        <li>Faça login no seu painel de afiliado</li>
                        <li>Complete o formulário KYC com seus dados pessoais</li>
                        <li>Faça upload dos documentos necessários</li>
                        <li>Aguarde a análise da nossa equipe (3-5 dias úteis)</li>
                        <li>Receba a aprovação final e comece a ganhar!</li>
                    </ol>
                    
                    <p><strong>Status Atual:</strong> KYC Pendente</p>
                    <p><strong>Documentos Necessários:</strong></p>
                    <ul style='line-height: 1.6;'>
                        <li>Documento de identidade válido (RG, CNH ou Passaporte)</li>
                        <li>Comprovante de endereço (conta de luz, água ou telefone)</li>
                    </ul>
                    
                    <p>Se tiver dúvidas, entre em contato conosco em support@gem-affiliate.com</p>
                    
                    <p style='margin-top: 30px;'>
                        Atenciosamente,<br>
                        <strong>Equipe GEM AFFILIATE</strong>
                    </p>
                </div>
            </div>
            ";
        } else {
            $subject = 'Welcome! Complete Your KYC Verification - GEM AFFILIATE';
            $message = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff;'>
                <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center;'>
                    <h1 style='margin: 0; font-size: 28px;'>Welcome to GEM AFFILIATE!</h1>
                </div>
                
                <div style='padding: 30px;'>
                    <p>Dear {$user_data['first_name']} {$user_data['last_name']},</p>
                    
                    <p>Thank you for registering with us! Your account has been successfully created.</p>
                    
                    <div style='background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 20px; margin: 20px 0;'>
                        <h3 style='color: #856404; margin: 0 0 15px 0;'>🔔 Action Required: Complete Your KYC Verification</h3>
                        <p style='margin: 0; color: #856404;'>To activate your account and start earning commissions, you need to complete the KYC (Know Your Customer) verification process.</p>
                    </div>
                    
                    <h3>Next Steps:</h3>
                    <ol style='line-height: 1.6;'>
                        <li>Log in to your affiliate dashboard</li>
                        <li>Complete the KYC form with your personal information</li>
                        <li>Upload the required documents</li>
                        <li>Wait for our team's review (3-5 business days)</li>
                        <li>Receive final approval and start earning!</li>
                    </ol>
                    
                    <p><strong>Current Status:</strong> KYC Pending</p>
                    <p><strong>Required Documents:</strong></p>
                    <ul style='line-height: 1.6;'>
                        <li>Valid ID document (Passport, Driver's License, or National ID)</li>
                        <li>Proof of address (Utility bill, bank statement, or lease agreement)</li>
                    </ul>
                    
                    <p>If you have any questions, please contact us at support@gem-affiliate.com</p>
                    
                    <p style='margin-top: 30px;'>
                        Best regards,<br>
                        <strong>GEM AFFILIATE Team</strong>
                    </p>
                </div>
            </div>
            ";
        }
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: GEM AFFILIATE <support@gem-affiliate.com>'
        );
        
        wp_mail($user_data['email'], $subject, $message, $headers);
    }
    
    // Helper function to check if admin is logged in
    public function is_admin_logged_in() {
        // REMOVED: Session-based admin validation - using cookie-only authentication
        return $this->is_admin_authenticated();
    }
    
    // Additional AJAX handlers for admin dashboard
    public function get_applications() {
        if (!$this->is_admin_logged_in()) {
            wp_send_json_error('Unauthorized access');
            return;
        }
        
        global $wpdb;
        $users_table = $wpdb->prefix . 'affiliate_users';
        
        $status_filter = sanitize_text_field($_POST['status'] ?? '');
        
        $where_clause = '';
        if (!empty($status_filter)) {
            $where_clause = $wpdb->prepare(' WHERE status = %s', $status_filter);
        }
        
        $applications = $wpdb->get_results(
            "SELECT id, username, first_name, last_name, email, company_name, affiliate_type, country, status, created_at, admin_remarks FROM $users_table $where_clause ORDER BY created_at DESC"
        );
        
        // Get stats with correct status values
        $stats = array(
            'pending' => $wpdb->get_var("SELECT COUNT(*) FROM $users_table WHERE status IN ('pending', 'awaiting approval')"),
            'approved' => $wpdb->get_var("SELECT COUNT(*) FROM $users_table WHERE status = 'approved'"),
            'rejected' => $wpdb->get_var("SELECT COUNT(*) FROM $users_table WHERE status = 'rejected'")
        );
        
        wp_send_json_success(array(
            'applications' => $applications,
            'stats' => $stats
        ));
    }
    

    
    // Send confirmation email to user after registration
    public function send_user_confirmation_email($email, $first_name, $last_name) {
        $subject = 'Affiliate Application Received - Thank You for Registering';
        
        $message = "
        <h2>Thank You for Your Interest!</h2>
        <p>Dear {$first_name} {$last_name},</p>
        
        <p>Thank you for submitting your affiliate application. We have received your registration and our team is currently reviewing it.</p>
        
        <h3>What happens next?</h3>
        <ul>
            <li>Our team will review your application within 1-2 business days</li>
            <li>You will receive an email notification once your application status is updated</li>
            <li>If approved, you will receive login credentials and access to your affiliate dashboard</li>
        </ul>
        
        <p>If you have any questions in the meantime, please don't hesitate to contact our support team.</p>
        
        <p>Best regards,<br>
        The Affiliate Team</p>
        ";
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: GEM AFFILIATE <support@gem-affiliate.com>'
        );
        wp_mail($email, $subject, $message, $headers);
    }
    
    // Send status update notification to user
    public function send_status_update_notification($application_id, $new_status, $admin_remarks = '') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'affiliate_users';
        
        // Get user data
        $user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $application_id));
        
        if (!$user) {
            error_log('Affiliate Portal: User not found for status notification. ID: ' . $application_id);
            return false;
        }
        
        $user_name = trim($user->first_name . ' ' . $user->last_name);
        if (empty($user_name)) {
            $user_name = $user->username;
        }
        
        // Prepare email content based on status
        switch (strtolower($new_status)) {
            case 'approved':
                $subject = 'Congratulations! Your Affiliate Application Has Been Approved';
                $message = $this->get_approved_email_template($user_name, $admin_remarks);
                break;
                
            case 'rejected':
                $subject = 'Update on Your Affiliate Application';
                $message = $this->get_rejected_email_template($user_name, $admin_remarks);
                break;
                
            case 'awaiting approval':
                $subject = 'Your Affiliate Application Status Update';
                $message = $this->get_awaiting_approval_email_template($user_name, $admin_remarks);
                break;
                
            default:
                $subject = 'Update on Your Affiliate Application';
                $message = $this->get_general_status_email_template($user_name, $new_status, $admin_remarks);
                break;
        }
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: GEM AFFILIATE <support@gem-affiliate.com>'
        );
        
        $email_sent = wp_mail($user->email, $subject, $message, $headers);
        
        if ($email_sent) {
            error_log('Affiliate Portal: Status update email sent successfully to ' . $user->email);
        } else {
            error_log('Affiliate Portal: Failed to send status update email to ' . $user->email);
        }
        
        return $email_sent;
    }
    
    // Email template for approved applications
    private function get_approved_email_template($user_name, $admin_remarks) {
        $remarks_section = '';
        if (!empty($admin_remarks)) {
            $remarks_section = "
            <div style='background-color: #f8f9fa; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0;'>
                <h4 style='color: #28a745; margin: 0 0 10px 0;'>Additional Notes:</h4>
                <p style='margin: 0; color: #6c757d;'>" . nl2br(esc_html($admin_remarks)) . "</p>
            </div>";
        }
        
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff;'>
            <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center;'>
                <h1 style='margin: 0; font-size: 28px;'>Application Approved!</h1>
            </div>
            
            <div style='padding: 30px;'>
                <h2 style='color: #28a745; margin-top: 0;'>Congratulations, {$user_name}!</h2>
                
                <p style='font-size: 16px; line-height: 1.6; color: #333;'>
                    We are delighted to inform you that your affiliate application has been <strong>approved</strong>. 
                    Welcome to our affiliate program!
                </p>
                
                <h3 style='color: #495057; margin-top: 25px;'>What's Next?</h3>
                <ul style='color: #6c757d; line-height: 1.8;'>
                    <li>Our team will send you the portal access details on your registered email address</li>
                    <li>You will receive your unique affiliate links and marketing materials</li>
                    <li>Commission tracking will begin immediately for any referrals you generate</li>
                    <li>Our support team is available to help you get started</li>
                </ul>
                
                {$remarks_section}
                
                <div style='background-color: #e9ecef; padding: 20px; border-radius: 5px; margin: 25px 0;'>
                    <h4 style='color: #495057; margin: 0 0 10px 0;'>Portal Access:</h4>
                    <p style='margin: 0; color: #6c757d;'>
                        Our team will send you the detailed portal access information on your registered email address shortly. 
                        Please check your email for login credentials and instructions to get started.
                    </p>
                </div>
                
                <p style='color: #6c757d; margin-top: 25px;'>
                    If you have any questions or need assistance, please contact us at office@gemmagics.com
                </p>
                
                <p style='margin-top: 30px; color: #495057;'>
                    Best regards,<br>
                    <strong>The Affiliate Team</strong><br>
                    Email: office@gemmagics.com
                </p>
            </div>
        </div>";
    }
    
    // Email template for rejected applications
    private function get_rejected_email_template($user_name, $admin_remarks) {
        $remarks_section = '';
        if (!empty($admin_remarks)) {
            $remarks_section = "
            <div style='background-color: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;'>
                <h4 style='color: #856404; margin: 0 0 10px 0;'>Feedback from Our Team:</h4>
                <p style='margin: 0; color: #856404;'>" . nl2br(esc_html($admin_remarks)) . "</p>
            </div>";
        }
        
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff;'>
            <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center;'>
                <h1 style='margin: 0; font-size: 28px;'>Application Update</h1>
            </div>
            
            <div style='padding: 30px;'>
                <h2 style='color: #495057; margin-top: 0;'>Dear {$user_name},</h2>
                
                <p style='font-size: 16px; line-height: 1.6; color: #333;'>
                    Thank you for your interest in joining our affiliate program. After careful review, 
                    we regret to inform you that we are unable to approve your application at this time.
                </p>
                
                {$remarks_section}
                
                <div style='background-color: #e9ecef; padding: 20px; border-radius: 5px; margin: 25px 0;'>
                    <h4 style='color: #495057; margin: 0 0 10px 0;'>Future Opportunities:</h4>
                    <p style='margin: 0; color: #6c757d;'>
                        Please note that this decision does not prevent you from reapplying in the future. 
                        We encourage you to address any feedback provided and consider submitting a new application.
                    </p>
                </div>
                
                <p style='color: #6c757d; margin-top: 25px;'>
                    We appreciate your understanding and thank you for your interest in partnering with us. 
                    If you have any questions regarding this decision, please feel free to contact us at office@gemmagics.com
                </p>
                
                <p style='margin-top: 30px; color: #495057;'>
                    Best regards,<br>
                    <strong>The Affiliate Team</strong><br>
                    Email: office@gemmagics.com
                </p>
            </div>
        </div>";
    }
    
    // Email template for awaiting approval status
    private function get_awaiting_approval_email_template($user_name, $admin_remarks) {
        $remarks_section = '';
        if (!empty($admin_remarks)) {
            $remarks_section = "
            <div style='background-color: #d1ecf1; padding: 15px; border-left: 4px solid #17a2b8; margin: 20px 0;'>
                <h4 style='color: #0c5460; margin: 0 0 10px 0;'>Additional Information:</h4>
                <p style='margin: 0; color: #0c5460;'>" . nl2br(esc_html($admin_remarks)) . "</p>
            </div>";
        }
        
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff;'>
            <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center;'>
                <h1 style='margin: 0; font-size: 28px;'>Application Status Update</h1>
            </div>
            
            <div style='padding: 30px;'>
                <h2 style='color: #17a2b8; margin-top: 0;'>Dear {$user_name},</h2>
                
                <p style='font-size: 16px; line-height: 1.6; color: #333;'>
                    We wanted to provide you with an update regarding your affiliate application. 
                    Your application is currently <strong>awaiting approval</strong> and is being reviewed by our team.
                </p>
                
                {$remarks_section}
                
                <div style='background-color: #e9ecef; padding: 20px; border-radius: 5px; margin: 25px 0;'>
                    <h4 style='color: #495057; margin: 0 0 10px 0;'>What This Means:</h4>
                    <ul style='margin: 10px 0 0 20px; color: #6c757d; line-height: 1.6;'>
                        <li>Our team is actively reviewing your application</li>
                        <li>We may contact you if additional information is needed</li>
                        <li>You will receive another update once a final decision is made</li>
                        <li>Processing typically takes 1-3 business days</li>
                    </ul>
                </div>
                
                <p style='color: #6c757d; margin-top: 25px;'>
                    We appreciate your patience during the review process. If you have any questions 
                    or would like to provide additional information, please contact us at office@gemmagics.com
                </p>
                
                <p style='margin-top: 30px; color: #495057;'>
                    Best regards,<br>
                    <strong>The Affiliate Team</strong><br>
                    Email: office@gemmagics.com
                </p>
            </div>
        </div>";
    }
    
    // General email template for other status changes
    private function get_general_status_email_template($user_name, $status, $admin_remarks) {
        $status_display = ucwords(str_replace('_', ' ', $status));
        
        $remarks_section = '';
        if (!empty($admin_remarks)) {
            $remarks_section = "
            <div style='background-color: #f8f9fa; padding: 15px; border-left: 4px solid #6c757d; margin: 20px 0;'>
                <h4 style='color: #495057; margin: 0 0 10px 0;'>Additional Notes:</h4>
                <p style='margin: 0; color: #6c757d;'>" . nl2br(esc_html($admin_remarks)) . "</p>
            </div>";
        }
        
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff;'>
            <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center;'>
                <h1 style='margin: 0; font-size: 28px;'>Application Status Update</h1>
            </div>
            
            <div style='padding: 30px;'>
                <h2 style='color: #495057; margin-top: 0;'>Dear {$user_name},</h2>
                
                <p style='font-size: 16px; line-height: 1.6; color: #333;'>
                    We wanted to update you regarding your affiliate application status. 
                    Your application status has been updated to: <strong>{$status_display}</strong>
                </p>
                
                {$remarks_section}
                
                <div style='background-color: #e9ecef; padding: 20px; border-radius: 5px; margin: 25px 0;'>
                    <h4 style='color: #495057; margin: 0 0 10px 0;'>Need Assistance?</h4>
                    <p style='margin: 0; color: #6c757d;'>
                        If you have any questions about this status update or need clarification, 
                        please contact us at office@gemmagics.com
                    </p>
                </div>
                
                <p style='margin-top: 30px; color: #495057;'>
                    Best regards,<br>
                    <strong>The Affiliate Team</strong><br>
                    Email: office@gemmagics.com
                </p>
            </div>
        </div>";
    }
    

    
    public function get_email_config() {
        if (!$this->is_admin_logged_in()) {
            wp_send_json_error('Unauthorized access');
            return;
        }
        
        global $wpdb;
        $email_config_table = $wpdb->prefix . 'affiliate_email_config';
        
        $configs = $wpdb->get_results("SELECT config_key, config_value FROM $email_config_table");
        
        $result = array();
        foreach ($configs as $config) {
            $result[$config->config_key] = $config->config_value;
        }
        
        wp_send_json_success($result);
    }
    
    public function update_application_status() {
        if (!$this->is_admin_logged_in()) {
            wp_send_json_error('Unauthorized access');
            return;
        }
        
        global $wpdb;
        $users_table = $wpdb->prefix . 'affiliate_users';
        
        $application_id = intval($_POST['application_id']);
        $new_status = sanitize_text_field($_POST['status']);
        $admin_remarks = sanitize_textarea_field($_POST['remarks']);
        
        if (empty($application_id) || empty($new_status)) {
            wp_send_json_error('Missing required fields');
            return;
        }
        
        $result = $wpdb->update(
            $users_table,
            array(
                'status' => $new_status,
                'admin_remarks' => $admin_remarks,
                'updated_at' => current_time('mysql')
            ),
            array('id' => $application_id)
        );
        
        // Debug logging for admin status updates
        error_log('Admin Status Update - Application ID: ' . $application_id);
        error_log('Admin Status Update - New Status: ' . $new_status);
        error_log('Admin Status Update - Remarks: ' . $admin_remarks);
        error_log('Admin Status Update - Database Result: ' . ($result !== false ? 'SUCCESS' : 'FAILED'));
        error_log('Admin Status Update - Query: ' . $wpdb->last_query);
        if ($wpdb->last_error) {
            error_log('Admin Status Update - Database Error: ' . $wpdb->last_error);
        }
        
        // Clear any caches for this user to force fresh data
        wp_cache_delete($application_id, 'affiliate_users');
        wp_cache_flush();
        
        if ($result !== false) {
            // Send status update notification to user
            $this->send_status_update_notification($application_id, $new_status, $admin_remarks);
            wp_send_json_success('Application status updated successfully');
        } else {
            wp_send_json_error('Failed to update application status: ' . $wpdb->last_error);
        }
    }
    
    public function handle_kyc_submission() {
        if (!wp_verify_nonce($_POST['nonce'], 'affiliate_nonce')) {
            wp_send_json_error('Security check failed');
            return;
        }
        
        // Check if user is logged in
        $user_id = $this->get_current_user_id();
        if (!$user_id) {
            wp_send_json_error('User not logged in');
            return;
        }
        
        // Ensure KYC table exists before attempting to save data
        if (!$this->ensure_kyc_table_exists()) {
            wp_send_json_error('Database table not available. Please contact administrator.');
            return;
        }
        
        global $wpdb;
        $kyc_action = sanitize_text_field($_POST['kyc_action']);
        
        // Get account type to determine which fields to process
        $account_type = sanitize_text_field($_POST['account_type']);
        
        // Prepare data for the new KYC table structure
        $kyc_data = array(
            'user_id' => $user_id,
            'account_type' => $account_type,
            'kyc_status' => $kyc_action === 'draft' ? 'draft' : 'pending',
            'submitted_at' => current_time('mysql')
        );

        // Handle individual form fields
        $individual_fields = ['full_name', 'date_of_birth', 'email', 'nationality', 'mobile_number', 'affiliate_type', 
                             'address_line1', 'address_line2', 'city', 'country', 'post_code'];
        
        foreach ($individual_fields as $field) {
            if (isset($_POST[$field])) {
                if ($field === 'email') {
                    $kyc_data[$field] = sanitize_email($_POST[$field]);
                } else {
                    $kyc_data[$field] = sanitize_text_field($_POST[$field]);
                }
            }
        }
        
        // Handle company contact fields
        $company_contact_fields = ['business_contact_name', 'job_title', 'business_email', 'business_telephone'];
        
        foreach ($company_contact_fields as $field) {
            if (isset($_POST[$field])) {
                if ($field === 'business_email') {
                    $kyc_data[$field] = sanitize_email($_POST[$field]);
                } else {
                    $kyc_data[$field] = sanitize_text_field($_POST[$field]);
                }
            }
        }
        
        // Handle company detail fields  
        $company_fields = ['full_company_name', 'trading_name', 'company_type', 'type_of_business', 
                          'company_registration_no', 'company_email', 'company_telephone',
                          'company_address_line1', 'company_address_line2', 'company_city', 
                          'company_country', 'company_post_code'];
        
        foreach ($company_fields as $field) {
            if (isset($_POST[$field])) {
                if ($field === 'company_email') {
                    $kyc_data[$field] = sanitize_email($_POST[$field]);
                } else {
                    $kyc_data[$field] = sanitize_text_field($_POST[$field]);
                }
            }
        }
        
        // Handle affiliate sites
        if (isset($_POST['affiliate_sites'])) {
            $kyc_data['affiliate_sites'] = sanitize_textarea_field($_POST['affiliate_sites']);
        }
        
        // Handle directors list (JSON)
        if (isset($_POST['directors']) && is_array($_POST['directors'])) {
            $directors = [];
            foreach ($_POST['directors'] as $director) {
                if (isset($director['name']) && isset($director['position'])) {
                    $directors[] = [
                        'name' => sanitize_text_field($director['name']),
                        'position' => sanitize_text_field($director['position'])
                    ];
                }
            }
            $kyc_data['list_of_directors'] = json_encode($directors);
        }
        
        // Handle shareholders list (JSON)
        if (isset($_POST['shareholders']) && is_array($_POST['shareholders'])) {
            $shareholders = [];
            foreach ($_POST['shareholders'] as $shareholder) {
                if (isset($shareholder['name']) && isset($shareholder['percentage'])) {
                    $shareholders[] = [
                        'name' => sanitize_text_field($shareholder['name']),
                        'percentage' => floatval($shareholder['percentage'])
                    ];
                }
            }
            $kyc_data['list_of_shareholders'] = json_encode($shareholders);
        }
        if (isset($_POST['address_line1'])) {
            $kyc_data['address_line1'] = sanitize_text_field($_POST['address_line1']);
        }
        if (isset($_POST['address_line2'])) {
            $kyc_data['address_line2'] = sanitize_text_field($_POST['address_line2']);
        }
        if (isset($_POST['city'])) {
            $kyc_data['city'] = sanitize_text_field($_POST['city']);
        }
        if (isset($_POST['country'])) {
            $kyc_data['country'] = sanitize_text_field($_POST['country']);
        }
        if (isset($_POST['post_code'])) {
            $kyc_data['post_code'] = sanitize_text_field($_POST['post_code']);
        }
        if (isset($_POST['affiliate_sites'])) {
            $kyc_data['affiliate_sites'] = sanitize_textarea_field($_POST['affiliate_sites']);
        }
        
        // Additional advanced fields for more comprehensive KYC
        if (isset($_POST['identity_document_type'])) {
            $kyc_data['identity_document_type'] = sanitize_text_field($_POST['identity_document_type']);
        }
        if (isset($_POST['identity_document_number'])) {
            $kyc_data['identity_document_number'] = sanitize_text_field($_POST['identity_document_number']);
        }
        if (isset($_POST['identity_document_expiry'])) {
            $kyc_data['identity_document_expiry'] = sanitize_text_field($_POST['identity_document_expiry']);
        }
        if (isset($_POST['address_proof_type'])) {
            $kyc_data['address_proof_type'] = sanitize_text_field($_POST['address_proof_type']);
        }
        
        // Handle Individual-specific fields
        if (strtolower($account_type) === 'individual') {
            // Individual KYC fields can be added here if needed
        }
        
        // Handle Company-specific fields
        if (strtolower($account_type) === 'company') {
            // Basic company fields from forms
            if (isset($_POST['company_name'])) {
                $kyc_data['company_name'] = sanitize_text_field($_POST['company_name']);
            }
            if (isset($_POST['business_registration_number'])) {
                $kyc_data['business_registration_number'] = sanitize_text_field($_POST['business_registration_number']);
            }
            if (isset($_POST['business_contact_name'])) {
                $kyc_data['business_contact_name'] = sanitize_text_field($_POST['business_contact_name']);
            }
            if (isset($_POST['business_email'])) {
                $kyc_data['business_email'] = sanitize_email($_POST['business_email']);
            }
            if (isset($_POST['business_phone'])) {
                $kyc_data['business_phone'] = sanitize_text_field($_POST['business_phone']);
            }
            if (isset($_POST['business_address'])) {
                $kyc_data['business_address'] = sanitize_textarea_field($_POST['business_address']);
            }
            
            // Advanced company fields
            if (isset($_POST['company_type'])) {
                $kyc_data['company_type'] = sanitize_text_field($_POST['company_type']);
            }
            if (isset($_POST['registration_number'])) {
                $kyc_data['registration_number'] = sanitize_text_field($_POST['registration_number']);
            }
            if (isset($_POST['tax_id'])) {
                $kyc_data['tax_id'] = sanitize_text_field($_POST['tax_id']);
            }
            if (isset($_POST['incorporation_date'])) {
                $kyc_data['incorporation_date'] = sanitize_text_field($_POST['incorporation_date']);
            }
            
            // Handle directors list - stored as JSON
            if (isset($_POST['directors']) && is_array($_POST['directors'])) {
                $directors = array();
                foreach ($_POST['directors'] as $director) {
                    if (!empty($director['name']) && !empty($director['position'])) {
                        $directors[] = array(
                            'name' => sanitize_text_field($director['name']),
                            'position' => sanitize_text_field($director['position']),
                            'nationality' => isset($director['nationality']) ? sanitize_text_field($director['nationality']) : '',
                            'id_document' => isset($director['id_document']) ? sanitize_text_field($director['id_document']) : ''
                        );
                    }
                }
                $kyc_data['directors_list'] = json_encode($directors);
            }
            
            // Handle shareholdings list - stored as JSON
            if (isset($_POST['shareholders']) && is_array($_POST['shareholders'])) {
                $shareholders = array();
                foreach ($_POST['shareholders'] as $shareholder) {
                    if (!empty($shareholder['name']) && !empty($shareholder['percentage'])) {
                        $shareholders[] = array(
                            'name' => sanitize_text_field($shareholder['name']),
                            'percentage' => floatval($shareholder['percentage']),
                            'type' => isset($shareholder['type']) ? sanitize_text_field($shareholder['type']) : 'individual'
                        );
                    }
                }
                $kyc_data['shareholdings'] = json_encode($shareholders);
            }
        }
        
        // Handle document uploads
        $upload_dir = AFFILIATE_PORTAL_PATH . 'uploads/kyc-documents/';
        if (!file_exists($upload_dir)) {
            wp_mkdir_p($upload_dir);
        }
        
        // Process document uploads for new KYC table structure
        
        // Identity document upload (from our forms - name="identification")
        if (isset($_FILES['identification']) && $_FILES['identification']['error'] === UPLOAD_ERR_OK) {
            $identity_doc_url = $this->handle_file_upload($_FILES['identification'], $upload_dir, $user_id . '_identity_document');
            if ($identity_doc_url) {
                $kyc_data['identification_url'] = $identity_doc_url;
                $kyc_data['identity_document_url'] = $identity_doc_url; // Also store in the advanced field
            }
        }
        
        // Address proof upload (from our forms - name="address_proof")
        if (isset($_FILES['address_proof']) && $_FILES['address_proof']['error'] === UPLOAD_ERR_OK) {
            $address_proof_url = $this->handle_file_upload($_FILES['address_proof'], $upload_dir, $user_id . '_address_proof');
            if ($address_proof_url) {
                $kyc_data['address_proof_url'] = $address_proof_url;
            }
        }
        
        // Bank statement upload
        if (isset($_FILES['bank_statement']) && $_FILES['bank_statement']['error'] === UPLOAD_ERR_OK) {
            $bank_statement_url = $this->handle_file_upload($_FILES['bank_statement'], $upload_dir, $user_id . '_bank_statement');
            if ($bank_statement_url) {
                $kyc_data['bank_statement_url'] = $bank_statement_url;
            }
        }
        
        // Individual-specific document uploads
        if (strtolower($account_type) === 'individual') {
            // Selfie upload
            if (isset($_FILES['selfie']) && $_FILES['selfie']['error'] === UPLOAD_ERR_OK) {
                $selfie_url = $this->handle_file_upload($_FILES['selfie'], $upload_dir, $user_id . '_selfie');
                if ($selfie_url) {
                    $kyc_data['selfie_url'] = $selfie_url;
                }
            }
            
            // Passport upload
            if (isset($_FILES['passport']) && $_FILES['passport']['error'] === UPLOAD_ERR_OK) {
                $passport_url = $this->handle_file_upload($_FILES['passport'], $upload_dir, $user_id . '_passport');
                if ($passport_url) {
                    $kyc_data['passport_url'] = $passport_url;
                }
            }
        }
        
        // Company-specific document uploads
        if (strtolower($account_type) === 'company') {
            // Company registration certificate
            if (isset($_FILES['company_registration_certificate']) && $_FILES['company_registration_certificate']['error'] === UPLOAD_ERR_OK) {
                $company_reg_url = $this->handle_file_upload($_FILES['company_registration_certificate'], $upload_dir, $user_id . '_company_registration');
                if ($company_reg_url) {
                    $kyc_data['company_registration_certificate_url'] = $company_reg_url;
                }
            }
            
            // Business license upload
            if (isset($_FILES['business_license']) && $_FILES['business_license']['error'] === UPLOAD_ERR_OK) {
                $business_license_url = $this->handle_file_upload($_FILES['business_license'], $upload_dir, $user_id . '_business_license');
                if ($business_license_url) {
                    $kyc_data['business_license_url'] = $business_license_url;
                }
            }
        }
        
        $kyc_table = $wpdb->prefix . 'affiliate_kyc';
        
        // Check if KYC record already exists
        $existing_kyc = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $kyc_table WHERE user_id = %d",
            $user_id
        ));
        
        if ($existing_kyc) {
            // Update existing record
            $result = $wpdb->update(
                $kyc_table,
                $kyc_data,
                array('user_id' => $user_id)
            );
            
            error_log('KYC Update - User ID: ' . $user_id . ', Result: ' . ($result !== false ? 'SUCCESS' : 'FAILED'));
            if ($wpdb->last_error) {
                error_log('KYC Update Error: ' . $wpdb->last_error);
            }
        } else {
            // Insert new record
            $result = $wpdb->insert($kyc_table, $kyc_data);
            
            error_log('KYC Insert - User ID: ' . $user_id . ', Result: ' . ($result !== false ? 'SUCCESS' : 'FAILED'));
            if ($wpdb->last_error) {
                error_log('KYC Insert Error: ' . $wpdb->last_error);
            }
        }
        
        if ($result !== false) {
            // Update user status when KYC is submitted (not draft)
            if ($kyc_action !== 'draft') {
                $users_table = $wpdb->prefix . 'affiliate_users';
                $wpdb->update(
                    $users_table,
                    array('status' => 'awaiting approval'),
                    array('id' => $user_id)
                );
            }
            
            // Send success message based on language context
            $is_portuguese = false;
            if (isset($_SERVER['HTTP_REFERER'])) {
                $referrer = $_SERVER['HTTP_REFERER'];
                if (strpos($referrer, 'afiliado') !== false || strpos($referrer, 'pt') !== false) {
                    $is_portuguese = true;
                }
            }
            
            if ($kyc_action === 'draft') {
                $message = $is_portuguese ? 'Rascunho salvo com sucesso' : 'Draft saved successfully';
            } else {
                $message = $is_portuguese ? 'Documentos KYC enviados com sucesso' : 'KYC documents submitted successfully';
                
                // Send email notifications for submitted KYC
                $this->send_kyc_notifications($user_id, $kyc_data, $is_portuguese);
            }
            
            wp_send_json_success($message);
        } else {
            error_log('KYC submission failed: ' . $wpdb->last_error);
            error_log('KYC data attempted to save: ' . print_r($kyc_data, true));
            
            $is_portuguese = false;
            if (isset($_SERVER['HTTP_REFERER'])) {
                $referrer = $_SERVER['HTTP_REFERER'];
                if (strpos($referrer, 'afiliado') !== false || strpos($referrer, 'pt') !== false) {
                    $is_portuguese = true;
                }
            }
            
            $error_message = $is_portuguese ? 'Falha ao salvar dados KYC' : 'Failed to save KYC data';
            wp_send_json_error($error_message);
        }
    }
    
    private function handle_file_upload($file, $upload_dir, $file_prefix) {
        $allowed_types = array('pdf', 'jpg', 'jpeg', 'png');
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_extension, $allowed_types)) {
            return false;
        }
        
        if ($file['size'] > 10 * 1024 * 1024) { // 10MB limit
            return false;
        }
        
        $filename = $file_prefix . '_' . time() . '.' . $file_extension;
        $file_path = $upload_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            return plugin_dir_url(__FILE__) . 'uploads/kyc-documents/' . $filename;
        }
        
        return false;
    }
    
    private function send_kyc_notifications($user_id, $kyc_data, $is_portuguese = false) {
        global $wpdb;
        $users_table = $wpdb->prefix . 'affiliate_users';
        $user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $users_table WHERE id = %d", $user_id));
        
        if (!$user) {
            return;
        }
        
        // Send notification to user
        if ($is_portuguese) {
            $user_subject = 'Documentos KYC Recebidos - GEM AFFILIATE';
            $user_message = "
Prezado(a) {$user->first_name} {$user->last_name},

Recebemos com sucesso sua documentação KYC (Conheça Seu Cliente). Obrigado por enviar todas as informações necessárias.

Detalhes da Aplicação:
- Nome Completo: " . ($kyc_data['full_name'] ?? 'N/A') . "
- Email: " . ($kyc_data['email'] ?? $user->email) . "
- Nacionalidade: " . ($kyc_data['nationality'] ?? 'N/A') . "
- Tipo de Afiliado: " . ($kyc_data['affiliate_type'] ?? 'N/A') . "
- Data de Envio: " . current_time('j \d\e F \d\e Y \à\s H:i') . "

Próximos passos:
1. Nossa equipe de conformidade irá revisar sua documentação KYC
2. Podemos entrar em contato caso precisemos de informações adicionais
3. Você será notificado por email assim que a análise for concluída
4. O processamento normalmente leva de 3 a 5 dias úteis

Você pode verificar o status da sua aplicação fazendo login no seu painel de afiliado.

Se tiver dúvidas, entre em contato com nossa equipe de suporte em support@gem-affiliate.com.

Atenciosamente,
Equipe GEM AFFILIATE
";
        } else {
            $user_subject = 'KYC Documents Received - GEM AFFILIATE';
            $user_message = "
Dear {$user->first_name} {$user->last_name},

We have successfully received your KYC (Know Your Customer) documentation. Thank you for submitting all the required information.

Application Details:
- Full Name: " . ($kyc_data['full_name'] ?? 'N/A') . "
- Email: " . ($kyc_data['email'] ?? $user->email) . "
- Nationality: " . ($kyc_data['nationality'] ?? 'N/A') . "
- Affiliate Type: " . ($kyc_data['affiliate_type'] ?? 'N/A') . "
- Submission Date: " . current_time('F j, Y \a\t g:i A') . "

What happens next:
1. Our compliance team will review your KYC documentation
2. We may contact you if additional information is required
3. You will be notified via email once the review is complete
4. Processing typically takes 3-5 business days

You can check your application status by logging into your affiliate dashboard.

If you have any questions, please contact our support team at support@gem-affiliate.com.

Best regards,
GEM AFFILIATE Team
";
        }

        wp_mail($user->email, $user_subject, $user_message, array(
            'From: GEM AFFILIATE <support@gem-affiliate.com>',
            'Content-Type: text/plain; charset=UTF-8'
        ));
        
        // Send notification to admin
        $admin_subject = 'New KYC Application Submitted - ' . $user->username;
        $admin_message = "
A new KYC application has been submitted by affiliate user: {$user->username}

User Details:
- Name: {$kyc_data['full_name']}
- Email: {$kyc_data['email']}
- Username: {$user->username}
- Account Type: {$user->type}
- Registration Date: " . date('F j, Y', strtotime($user->created_at)) . "

KYC Information:
- Nationality: {$kyc_data['nationality']}
- Mobile: {$kyc_data['mobile_number']}
- Affiliate Type: {$kyc_data['affiliate_type']}
- Address: {$kyc_data['address_line1']}, {$kyc_data['city']}, {$kyc_data['country']} {$kyc_data['post_code']}

Affiliate Sites:
{$kyc_data['affiliate_sites']}

Documents Uploaded:
" . (isset($kyc_data['address_proof_url']) ? "- Address Proof: {$kyc_data['address_proof_url']}" : "- Address Proof: Not uploaded") . "
" . (isset($kyc_data['identification_url']) ? "- Identification: {$kyc_data['identification_url']}" : "- Identification: Not uploaded") . "

Please review this application in the admin dashboard.

Submitted on: " . current_time('F j, Y \a\t g:i A') . "
";

        // Get admin email addresses from config
        $email_config_table = $wpdb->prefix . 'affiliate_email_config';
        $admin_emails_config = $wpdb->get_var($wpdb->prepare(
            "SELECT config_value FROM $email_config_table WHERE config_key = %s",
            'admin_emails'
        ));
        
        $admin_emails = $admin_emails_config ? explode(',', $admin_emails_config) : array('admin@gem-affiliate.com');
        
        foreach ($admin_emails as $admin_email) {
            wp_mail(trim($admin_email), $admin_subject, $admin_message, array(
                'From: GEM AFFILIATE <support@gem-affiliate.com>',
                'Content-Type: text/plain; charset=UTF-8'
            ));
        }
    }
    
    // KYC Admin Functions
    public function get_kyc_applications() {
        // Check admin authentication
        if (!$this->is_admin_logged_in()) {
            wp_send_json_error('Access denied');
            return;
        }
        
        global $wpdb;
        $kyc_table = $wpdb->prefix . 'affiliate_kyc';
        $users_table = $wpdb->prefix . 'affiliate_users';
        
        $status_filter = isset($_POST['status_filter']) ? sanitize_text_field($_POST['status_filter']) : '';
        $per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : 25;
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $offset = ($page - 1) * $per_page;
        
        // Build WHERE clause
        $where_clause = "WHERE 1=1";
        $params = array();
        
        if (!empty($status_filter)) {
            $where_clause .= " AND k.kyc_status = %s";
            $params[] = $status_filter;
        }
        
        // Get total count
        $count_query = "
            SELECT COUNT(*) 
            FROM $kyc_table k 
            INNER JOIN $users_table u ON k.user_id = u.id 
            $where_clause
        ";
        
        $total = $wpdb->get_var($count_query ? $wpdb->prepare($count_query, $params) : $count_query);
        
        // Get applications
        $query = "
            SELECT 
                k.*,
                u.username,
                u.email,
                u.type as affiliate_type
            FROM $kyc_table k 
            INNER JOIN $users_table u ON k.user_id = u.id 
            $where_clause
            ORDER BY k.updated_at DESC, k.created_at DESC
            LIMIT %d OFFSET %d
        ";
        
        $params[] = $per_page;
        $params[] = $offset;
        
        $applications = $wpdb->get_results($wpdb->prepare($query, $params));
        
        // Calculate pagination info
        $total_pages = ceil($total / $per_page);
        $from = $offset + 1;
        $to = min($offset + $per_page, $total);
        
        wp_send_json_success(array(
            'applications' => $applications,
            'pagination' => array(
                'total' => $total,
                'total_pages' => $total_pages,
                'current_page' => $page,
                'per_page' => $per_page,
                'from' => $from,
                'to' => $to
            )
        ));
    }
    
    public function get_kyc_details() {
        // Check admin authentication
        if (!$this->is_admin_logged_in()) {
            wp_send_json_error('Access denied');
            return;
        }
        
        if (!isset($_POST['user_id'])) {
            wp_send_json_error('User ID is required');
            return;
        }
        
        global $wpdb;
        $kyc_table = $wpdb->prefix . 'affiliate_kyc';
        $users_table = $wpdb->prefix . 'affiliate_users';
        $user_id = intval($_POST['user_id']);
        
        // Get KYC details with user information
        $query = "
            SELECT 
                k.*,
                u.username,
                u.email,
                u.type as affiliate_type,
                u.created_at as registration_date
            FROM $kyc_table k 
            INNER JOIN $users_table u ON k.user_id = u.id 
            WHERE k.user_id = %d
        ";
        
        $kyc_data = $wpdb->get_row($wpdb->prepare($query, $user_id));
        
        if (!$kyc_data) {
            wp_send_json_error('KYC data not found');
            return;
        }
        
        wp_send_json_success($kyc_data);
    }
    
    public function update_kyc_status() {
        // Check admin authentication
        if (!$this->is_admin_logged_in()) {
            wp_send_json_error('Access denied');
            return;
        }
        
        if (!isset($_POST['user_id']) || !isset($_POST['kyc_status'])) {
            wp_send_json_error('User ID and KYC status are required');
            return;
        }
        
        global $wpdb;
        $kyc_table = $wpdb->prefix . 'affiliate_kyc';
        $users_table = $wpdb->prefix . 'affiliate_users';
        
        $user_id = intval($_POST['user_id']);
        $kyc_status = sanitize_text_field($_POST['kyc_status']);
        $admin_comments = isset($_POST['admin_comments']) ? sanitize_textarea_field($_POST['admin_comments']) : '';
        
        // Validate status
        $valid_statuses = array('pending', 'approved', 'rejected', 'incomplete', 'submitted');
        if (!in_array($kyc_status, $valid_statuses)) {
            wp_send_json_error('Invalid KYC status');
            return;
        }
        
        // Require comments for certain statuses
        if (in_array($kyc_status, array('rejected', 'incomplete')) && empty($admin_comments)) {
            wp_send_json_error('Admin comments are required when rejecting or marking as incomplete');
            return;
        }
        
        // Update KYC status
        $updated = $wpdb->update(
            $kyc_table,
            array(
                'kyc_status' => $kyc_status,
                'admin_comments' => $admin_comments,
                'updated_at' => current_time('mysql')
            ),
            array('user_id' => $user_id)
        );
        
        if ($updated === false) {
            wp_send_json_error('Failed to update KYC status');
            return;
        }
        
        // Get user information for email notification
        $user = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $users_table WHERE id = %d",
            $user_id
        ));
        
        if ($user) {
            // Send email notification to user
            $this->send_kyc_status_notification($user, $kyc_status, $admin_comments);
        }
        
        wp_send_json_success('KYC status updated successfully');
    }
    
    // Handle document approval
    public function handle_document_approval() {
        // Check admin authentication
        if (!$this->is_admin_logged_in()) {
            wp_send_json_error('Access denied');
            return;
        }
        
        if (!isset($_POST['user_id']) || !isset($_POST['document_type'])) {
            wp_send_json_error('User ID and document type are required');
            return;
        }
        
        global $wpdb;
        $kyc_table = $wpdb->prefix . 'affiliate_kyc';
        
        $user_id = intval($_POST['user_id']);
        $document_type = sanitize_text_field($_POST['document_type']);
        $notes = isset($_POST['notes']) ? sanitize_textarea_field($_POST['notes']) : '';
        
        // Valid document types
        $valid_documents = array(
            'identity_document', 'address_proof', 'bank_statement', 'selfie', 'passport',
            'company_registration_certificate', 'company_address_proof', 'business_license', 'directors_id_docs'
        );
        
        if (!in_array($document_type, $valid_documents)) {
            wp_send_json_error('Invalid document type');
            return;
        }
        
        // Update document status to approved
        $status_field = $document_type . '_status';
        $notes_field = $document_type . '_notes';
        
        $updated = $wpdb->update(
            $kyc_table,
            array(
                $status_field => 'approved',
                $notes_field => $notes,
                'reviewed_at' => current_time('mysql'),
                'reviewed_by' => $this->get_current_admin_username()
            ),
            array('user_id' => $user_id)
        );
        
        if ($updated === false) {
            wp_send_json_error('Failed to approve document: ' . $wpdb->last_error);
            return;
        }
        
        // Check if all documents are approved to update overall status
        $this->update_overall_kyc_status($user_id);
        
        wp_send_json_success('Document approved successfully');
    }
    
    // Handle document rejection
    public function handle_document_rejection() {
        // Check admin authentication
        if (!$this->is_admin_logged_in()) {
            wp_send_json_error('Access denied');
            return;
        }
        
        if (!isset($_POST['user_id']) || !isset($_POST['document_type']) || !isset($_POST['notes'])) {
            wp_send_json_error('User ID, document type, and rejection notes are required');
            return;
        }
        
        global $wpdb;
        $kyc_table = $wpdb->prefix . 'affiliate_kyc';
        
        $user_id = intval($_POST['user_id']);
        $document_type = sanitize_text_field($_POST['document_type']);
        $notes = sanitize_textarea_field($_POST['notes']);
        
        // Valid document types
        $valid_documents = array(
            'identity_document', 'address_proof', 'bank_statement', 'selfie', 'passport',
            'company_registration_certificate', 'company_address_proof', 'business_license', 'directors_id_docs'
        );
        
        if (!in_array($document_type, $valid_documents)) {
            wp_send_json_error('Invalid document type');
            return;
        }
        
        if (empty($notes)) {
            wp_send_json_error('Rejection notes are required');
            return;
        }
        
        // Update document status to rejected
        $status_field = $document_type . '_status';
        $notes_field = $document_type . '_notes';
        
        $updated = $wpdb->update(
            $kyc_table,
            array(
                $status_field => 'rejected',
                $notes_field => $notes,
                'reviewed_at' => current_time('mysql'),
                'reviewed_by' => $this->get_current_admin_username(),
                'overall_status' => 'needs_resubmission'
            ),
            array('user_id' => $user_id)
        );
        
        if ($updated === false) {
            wp_send_json_error('Failed to reject document: ' . $wpdb->last_error);
            return;
        }
        
        // Send notification to user about rejection
        $this->send_document_rejection_notification($user_id, $document_type, $notes);
        
        wp_send_json_success('Document rejected successfully');
    }
    
    // Get document details for admin review
    public function get_document_details() {
        // Check admin authentication
        if (!$this->is_admin_logged_in()) {
            wp_send_json_error('Access denied');
            return;
        }
        
        if (!isset($_POST['user_id'])) {
            wp_send_json_error('User ID is required');
            return;
        }
        
        global $wpdb;
        $kyc_table = $wpdb->prefix . 'affiliate_kyc';
        $users_table = $wpdb->prefix . 'affiliate_users';
        $user_id = intval($_POST['user_id']);
        
        // Get KYC details with user information
        $query = "
            SELECT 
                k.*,
                u.username,
                u.email,
                u.first_name,
                u.last_name
            FROM $kyc_table k 
            INNER JOIN $users_table u ON k.user_id = u.id 
            WHERE k.user_id = %d
        ";
        
        $kyc_data = $wpdb->get_row($wpdb->prepare($query, $user_id));
        
        if (!$kyc_data) {
            wp_send_json_error('User KYC data not found');
            return;
        }
        
        // Format document information for admin review
        $documents = array(
            'identity_document' => array(
                'url' => $kyc_data->identity_document_url,
                'status' => $kyc_data->identity_document_status,
                'notes' => $kyc_data->identity_document_notes,
                'type' => $kyc_data->identity_document_type,
                'number' => $kyc_data->identity_document_number,
                'expiry' => $kyc_data->identity_document_expiry
            ),
            'address_proof' => array(
                'url' => $kyc_data->address_proof_url,
                'status' => $kyc_data->address_proof_status,
                'notes' => $kyc_data->address_proof_notes,
                'type' => $kyc_data->address_proof_type
            ),
            'bank_statement' => array(
                'url' => $kyc_data->bank_statement_url,
                'status' => $kyc_data->bank_statement_status,
                'notes' => $kyc_data->bank_statement_notes
            ),
            'selfie' => array(
                'url' => $kyc_data->selfie_url,
                'status' => $kyc_data->selfie_status,
                'notes' => $kyc_data->selfie_notes
            ),
            'passport' => array(
                'url' => $kyc_data->passport_url,
                'status' => $kyc_data->passport_status,
                'notes' => $kyc_data->passport_notes
            )
        );
        
        // Add company documents if account type is company
        if ($kyc_data->account_type === 'company') {
            $documents['company_registration_certificate'] = array(
                'url' => $kyc_data->company_registration_certificate_url,
                'status' => $kyc_data->company_registration_certificate_status,
                'notes' => $kyc_data->company_registration_certificate_notes
            );
            $documents['company_address_proof'] = array(
                'url' => $kyc_data->company_address_proof_url,
                'status' => $kyc_data->company_address_proof_status,
                'notes' => $kyc_data->company_address_proof_notes
            );
            $documents['business_license'] = array(
                'url' => $kyc_data->business_license_url,
                'status' => $kyc_data->business_license_status,
                'notes' => $kyc_data->business_license_notes
            );
            $documents['directors_id_docs'] = array(
                'url' => $kyc_data->directors_id_docs_url,
                'status' => $kyc_data->directors_id_docs_status,
                'notes' => $kyc_data->directors_id_docs_notes
            );
        }
        
        wp_send_json_success(array(
            'user_data' => array(
                'username' => $kyc_data->username,
                'email' => $kyc_data->email,
                'full_name' => trim($kyc_data->first_name . ' ' . $kyc_data->last_name),
                'account_type' => $kyc_data->account_type,
                'overall_status' => $kyc_data->overall_status
            ),
            'documents' => $documents
        ));
    }
    
    // Update overall KYC status based on individual document statuses
    private function update_overall_kyc_status($user_id) {
        global $wpdb;
        $kyc_table = $wpdb->prefix . 'affiliate_kyc';
        
        $kyc_data = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $kyc_table WHERE user_id = %d",
            $user_id
        ));
        
        if (!$kyc_data) {
            return false;
        }
        
        // Define required documents based on account type
        $required_docs = array('identity_document', 'address_proof', 'bank_statement', 'selfie');
        
        if ($kyc_data->account_type === 'company') {
            $required_docs = array_merge($required_docs, array(
                'company_registration_certificate', 'business_license', 'directors_id_docs'
            ));
        }
        
        $all_approved = true;
        $has_rejected = false;
        
        foreach ($required_docs as $doc) {
            $status_field = $doc . '_status';
            $url_field = $doc . '_url';
            
            // Skip if document URL is empty (not uploaded)
            if (empty($kyc_data->$url_field)) {
                $all_approved = false;
                continue;
            }
            
            if ($kyc_data->$status_field === 'rejected') {
                $has_rejected = true;
                $all_approved = false;
            } elseif ($kyc_data->$status_field !== 'approved') {
                $all_approved = false;
            }
        }
        
        // Determine overall status
        $overall_status = 'pending_review';
        if ($has_rejected) {
            $overall_status = 'needs_resubmission';
        } elseif ($all_approved) {
            $overall_status = 'approved';
        }
        
        // Update overall status
        $wpdb->update(
            $kyc_table,
            array('overall_status' => $overall_status),
            array('user_id' => $user_id)
        );
        
        return $overall_status;
    }
    
    // Send notification to user about document rejection
    private function send_document_rejection_notification($user_id, $document_type, $notes) {
        global $wpdb;
        $users_table = $wpdb->prefix . 'affiliate_users';
        
        $user = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $users_table WHERE id = %d",
            $user_id
        ));
        
        if (!$user) {
            return false;
        }
        
        $document_names = array(
            'identity_document' => 'Identity Document',
            'address_proof' => 'Address Proof',
            'bank_statement' => 'Bank Statement',
            'selfie' => 'Selfie',
            'passport' => 'Passport',
            'company_registration_certificate' => 'Company Registration Certificate',
            'company_address_proof' => 'Company Address Proof',
            'business_license' => 'Business License',
            'directors_id_docs' => 'Directors ID Documents'
        );
        
        $document_name = isset($document_names[$document_type]) ? $document_names[$document_type] : $document_type;
        
        $subject = 'Document Rejection - Action Required';
        $message = "Dear {$user->first_name},\n\n";
        $message .= "Your {$document_name} has been rejected and requires resubmission.\n\n";
        $message .= "Reason for rejection:\n{$notes}\n\n";
        $message .= "Please log in to your dashboard and resubmit the corrected document.\n\n";
        $message .= "Thank you,\nAffiliate Portal Team";
        
        wp_mail($user->email, $subject, $message);
        
        return true;
    }
    
    // Get current admin username for logging
    private function get_current_admin_username() {
        $admin_data = $this->is_admin_authenticated();
        return $admin_data ? $admin_data['username'] : 'unknown';
    }
    
    // Get comprehensive KYC verification details for admin review
    public function get_kyc_verification_details() {
        // Check admin authentication
        if (!$this->is_admin_logged_in()) {
            wp_send_json_error('Access denied');
            return;
        }
        
        if (!isset($_POST['user_id'])) {
            wp_send_json_error('User ID is required');
            return;
        }
        
        global $wpdb;
        $user_id = intval($_POST['user_id']);
        
        // Get user data
        $users_table = $wpdb->prefix . 'affiliate_users';
        $kyc_table = $wpdb->prefix . 'affiliate_kyc';
        
        $user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $users_table WHERE id = %d", $user_id));
        $kyc_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $kyc_table WHERE user_id = %d", $user_id));
        
        if (!$user) {
            wp_send_json_error('User not found');
            return;
        }
        
        // Determine if user is individual or company
        $is_individual = strtolower($user->type ?? '') !== 'company';
        
        // Build comprehensive KYC verification HTML
        $html = $this->build_kyc_verification_html($user, $kyc_data, $is_individual);
        
        wp_send_json_success(array('html' => $html));
    }
    
    private function build_kyc_verification_html($user, $kyc_data, $is_individual) {
        $avatar_initial = strtoupper(substr($user->first_name ?? $user->username, 0, 1));
        $kyc_status = $kyc_data->kyc_status ?? 'not_submitted';
        $account_type = $is_individual ? 'Individual' : 'Company';
        
        $html = '<div class="kyc-verification-content">';
        
        // User Info Header
        $html .= '<div class="kyc-user-info">';
        $html .= '<div class="kyc-user-header">';
        $html .= '<div class="kyc-user-avatar">' . $avatar_initial . '</div>';
        $html .= '<div class="kyc-user-details">';
        $html .= '<h3>' . esc_html($user->first_name . ' ' . $user->last_name) . '</h3>';
        $html .= '<p>' . esc_html($user->email) . ' • ' . esc_html($account_type) . ' Account</p>';
        $html .= '</div>';
        $html .= '<div class="kyc-status-badge kyc-status-' . esc_attr($kyc_status) . '">';
        $html .= '<span>' . ucfirst(str_replace('_', ' ', $kyc_status)) . '</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        // KYC Sections
        $html .= '<div class="kyc-sections">';
        
        if ($is_individual) {
            $html .= $this->build_individual_kyc_section($user, $kyc_data);
        } else {
            $html .= $this->build_company_kyc_section($user, $kyc_data);
        }
        
        // Documents Section
        $html .= $this->build_documents_section($kyc_data);
        
        $html .= '</div>';
        
        // Admin Actions Section
        $html .= $this->build_admin_actions_section($user->id, $kyc_data);
        
        $html .= '</div>';
        
        return $html;
    }
    
    private function build_individual_kyc_section($user, $kyc_data) {
        $html = '<div class="kyc-section">';
        $html .= '<div class="kyc-section-header">';
        $html .= '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">';
        $html .= '<path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>';
        $html .= '</svg>';
        $html .= '<h4>Individual Information</h4>';
        $html .= '</div>';
        
        $html .= '<div class="kyc-form-grid">';
        $html .= $this->render_kyc_field('Full Name', $kyc_data->full_name ?? $user->first_name . ' ' . $user->last_name);
        $html .= $this->render_kyc_field('Date of Birth', $kyc_data->date_of_birth ?? $user->dob);
        $html .= $this->render_kyc_field('Email', $kyc_data->email ?? $user->email);
        $html .= $this->render_kyc_field('Mobile Number', $kyc_data->mobile_number ?? $user->mobile_number);
        $html .= $this->render_kyc_field('Nationality', $kyc_data->nationality ?? 'Not provided');
        $html .= $this->render_kyc_field('Affiliate Type', $kyc_data->affiliate_type ?? $user->affiliate_type);
        $html .= $this->render_kyc_field('Address Line 1', $kyc_data->address_line1 ?? $user->address_line1);
        $html .= $this->render_kyc_field('Address Line 2', $kyc_data->address_line2 ?? $user->address_line2);
        $html .= $this->render_kyc_field('City', $kyc_data->city ?? $user->city);
        $html .= $this->render_kyc_field('Country', $kyc_data->country ?? $user->country);
        $html .= $this->render_kyc_field('Post Code', $kyc_data->post_code ?? $user->zipcode);
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    private function build_company_kyc_section($user, $kyc_data) {
        $html = '<div class="kyc-section">';
        $html .= '<div class="kyc-section-header">';
        $html .= '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">';
        $html .= '<path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/>';
        $html .= '</svg>';
        $html .= '<h4>Company Information</h4>';
        $html .= '</div>';
        
        $html .= '<div class="kyc-form-grid">';
        $html .= $this->render_kyc_field('Company Name', $kyc_data->company_name ?? $user->company_name);
        $html .= $this->render_kyc_field('Business Registration Number', $kyc_data->business_registration_number ?? 'Not provided');
        $html .= $this->render_kyc_field('Business Contact Name', $kyc_data->business_contact_name ?? 'Not provided');
        $html .= $this->render_kyc_field('Business Email', $kyc_data->business_email ?? $user->email);
        $html .= $this->render_kyc_field('Business Phone', $kyc_data->business_phone ?? $user->mobile_number);
        $html .= $this->render_kyc_field('Business Address', $kyc_data->business_address ?? $user->address_line1);
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    private function build_documents_section($kyc_data) {
        $html = '<div class="kyc-section">';
        $html .= '<div class="kyc-section-header">';
        $html .= '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">';
        $html .= '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>';
        $html .= '<polyline points="14,2 14,8 20,8"/>';
        $html .= '</svg>';
        $html .= '<h4>Uploaded Documents</h4>';
        $html .= '</div>';
        
        $html .= '<div class="kyc-documents-grid">';
        
        // Address Proof
        if (!empty($kyc_data->address_proof_url)) {
            $html .= $this->render_document_card('Address Proof', $kyc_data->address_proof_url);
        }
        
        // Identity Document
        if (!empty($kyc_data->identity_document_url)) {
            $html .= $this->render_document_card('Identity Document', $kyc_data->identity_document_url);
        }
        
        // Bank Statement
        if (!empty($kyc_data->bank_statement_url)) {
            $html .= $this->render_document_card('Bank Statement', $kyc_data->bank_statement_url);
        }
        
        // Selfie
        if (!empty($kyc_data->selfie_url)) {
            $html .= $this->render_document_card('Selfie', $kyc_data->selfie_url);
        }
        
        // Passport
        if (!empty($kyc_data->passport_url)) {
            $html .= $this->render_document_card('Passport', $kyc_data->passport_url);
        }
        
        // Company Registration Certificate
        if (!empty($kyc_data->company_registration_certificate_url)) {
            $html .= $this->render_document_card('Company Registration Certificate', $kyc_data->company_registration_certificate_url);
        }
        
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    private function render_document_card($title, $url) {
        $is_image = preg_match('/\.(jpg|jpeg|png|gif)$/i', $url);
        
        $html = '<div class="kyc-document">';
        $html .= '<div class="kyc-document-header">';
        $html .= '<h5 class="kyc-document-title">' . esc_html($title) . '</h5>';
        $html .= '</div>';
        $html .= '<div class="kyc-document-content">';
        
        if ($is_image) {
            $html .= '<img src="' . esc_url($url) . '" alt="' . esc_attr($title) . '" class="kyc-document-preview">';
        } else {
            $html .= '<div style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 8px;">';
            $html .= '<svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor" style="color: #6c757d; margin-bottom: 10px;">';
            $html .= '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>';
            $html .= '<polyline points="14,2 14,8 20,8"/>';
            $html .= '</svg>';
            $html .= '<p style="margin: 0; color: #6c757d;">Document File</p>';
            $html .= '</div>';
        }
        
        $html .= '<div class="kyc-document-actions">';
        $html .= '<button type="button" class="kyc-doc-btn kyc-doc-btn-view" onclick="viewDocumentInModal(\'' . esc_url($url) . '\', \'' . esc_attr($title) . '\')">';
        $html .= '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">';
        $html .= '<path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>';
        $html .= '</svg>';
        $html .= 'View';
        $html .= '</button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    private function render_kyc_field($label, $value) {
        return '<div class="kyc-field">' .
               '<div class="kyc-field-label">' . esc_html($label) . '</div>' .
               '<div class="kyc-field-value">' . esc_html($value ?: 'Not provided') . '</div>' .
               '</div>';
    }
    
    private function build_admin_actions_section($user_id, $kyc_data) {
        $html = '<div class="kyc-admin-actions">';
        $html .= '<h4>Admin Actions</h4>';
        
        // Previous Comments Section
        if (!empty($kyc_data->admin_comments)) {
            $html .= '<div class="kyc-previous-comments">';
            $html .= '<h5>Previous Admin Comments</h5>';
            $html .= '<div class="kyc-comment-item">';
            $html .= '<div class="kyc-comment-meta">Previous Review</div>';
            $html .= '<div class="kyc-comment-text">' . esc_html($kyc_data->admin_comments) . '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }
        
        // Comment Section
        $html .= '<div class="kyc-comment-section">';
        $html .= '<label for="kycAdminComments">Admin Comments</label>';
        $html .= '<textarea id="kycAdminComments" class="kyc-comment-textarea" placeholder="Enter your comments for the user. This will be included in the email notification.">' . esc_textarea($kyc_data->admin_comments ?? '') . '</textarea>';
        $html .= '</div>';
        
        // Action Buttons
        $html .= '<div class="kyc-status-actions">';
        $html .= '<button type="button" class="kyc-action-btn kyc-action-btn-approve" onclick="approveKyc(' . $user_id . ')">';
        $html .= '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">';
        $html .= '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>';
        $html .= '</svg>';
        $html .= 'Approve KYC';
        $html .= '</button>';
        
        $html .= '<button type="button" class="kyc-action-btn kyc-action-btn-reject" onclick="rejectKyc(' . $user_id . ')">';
        $html .= '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">';
        $html .= '<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>';
        $html .= '</svg>';
        $html .= 'Reject KYC';
        $html .= '</button>';
        
        $html .= '<button type="button" class="kyc-action-btn kyc-action-btn-info" onclick="requestMoreInfo(' . $user_id . ')">';
        $html .= '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">';
        $html .= '<path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z"/>';
        $html .= '</svg>';
        $html .= 'Request More Info';
        $html .= '</button>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }

    private function send_kyc_status_notification($user, $status, $admin_comments) {
        $subject = 'KYC Application Status Update - GEM AFFILIATE';
        
        switch ($status) {
            case 'approved':
                $status_text = 'approved';
                $message = "
Congratulations! Your KYC (Know Your Customer) application has been approved.

Your account is now fully verified and you can access all affiliate features.

If you have any questions, please contact our support team.

Best regards,
GEM AFFILIATE Team
";
                break;
                
            case 'rejected':
                $status_text = 'rejected';
                $message = "
We regret to inform you that your KYC (Know Your Customer) application has been rejected.

Reason: {$admin_comments}

You may resubmit your KYC application with updated information and documents.

If you have any questions, please contact our support team.

Best regards,
GEM AFFILIATE Team
";
                break;
                
            case 'incomplete':
                $status_text = 'marked as incomplete';
                $message = "
Your KYC (Know Your Customer) application requires additional information.

Admin Comments: {$admin_comments}

Please log into your dashboard and update your KYC information accordingly.

If you have any questions, please contact our support team.

Best regards,
GEM AFFILIATE Team
";
                break;
                
            default:
                $status_text = $status;
                $message = "
Your KYC (Know Your Customer) application status has been updated to: {$status}

" . (!empty($admin_comments) ? "Admin Comments: {$admin_comments}\n\n" : "") . "
Please log into your dashboard for more information.

If you have any questions, please contact our support team.

Best regards,
GEM AFFILIATE Team
";
                break;
        }
        
        wp_mail($user->email, $subject, $message, array(
            'From: GEM AFFILIATE <support@gem-affiliate.com>',
            'Content-Type: text/plain; charset=UTF-8'
        ));
    }
}

// Initialize the plugin
global $affiliate_portal_instance;
$affiliate_portal_instance = new AffiliatePortal();
?>