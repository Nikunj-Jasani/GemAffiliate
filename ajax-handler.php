<?php
/**
 * AJAX Handler for Affiliate Portal Plugin
 * Handles all AJAX requests in standalone mode
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set up WordPress environment simulation first
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

// WordPress plugin functions  
if (!defined('AFFILIATE_PORTAL_URL')) {
    define('AFFILIATE_PORTAL_URL', '/');
}
if (!defined('AFFILIATE_PORTAL_PATH')) {
    define('AFFILIATE_PORTAL_PATH', __DIR__ . '/');
}
if (!defined('AFFILIATE_PORTAL_VERSION')) {
    define('AFFILIATE_PORTAL_VERSION', '1.3.0');
}

// WordPress activation/deactivation hooks
if (!function_exists('register_activation_hook')) {
    function register_activation_hook($file, $callback) {
        // Mock activation hook for standalone mode
    }
}

if (!function_exists('register_deactivation_hook')) {
    function register_deactivation_hook($file, $callback) {
        // Mock deactivation hook for standalone mode
    }
}

// WordPress action/hook system
if (!function_exists('add_action')) {
    function add_action($hook, $callback, $priority = 10, $args = 1) {
        // Mock action system
    }
}

if (!function_exists('add_shortcode')) {
    function add_shortcode($tag, $callback) {
        // Mock shortcode system
    }
}

if (!function_exists('flush_rewrite_rules')) {
    function flush_rewrite_rules() {
        // Mock flush rewrite rules
    }
}

// Include required files AFTER WordPress functions are defined
require_once 'database.php';
require_once 'affiliate-portal.php';

// WordPress AJAX response functions
if (!function_exists('wp_send_json_success')) {
    function wp_send_json_success($data = null) {
        header('Content-Type: application/json');
        echo json_encode(array('success' => true, 'data' => $data));
        exit;
    }
}

if (!function_exists('wp_send_json_error')) {
    function wp_send_json_error($data = null) {
        header('Content-Type: application/json');
        echo json_encode(array('success' => false, 'data' => $data));
        exit;
    }
}

// WordPress sanitization functions
if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($str) {
        return htmlspecialchars(strip_tags(trim($str)), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('sanitize_email')) {
    function sanitize_email($email) {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }
}

if (!function_exists('sanitize_textarea_field')) {
    function sanitize_textarea_field($str) {
        return htmlspecialchars(strip_tags($str), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('wp_verify_nonce')) {
    function wp_verify_nonce($nonce, $action = -1) {
        return true; // Simplified for development
    }
}

if (!function_exists('current_time')) {
    function current_time($type = 'mysql') {
        return date('Y-m-d H:i:s');
    }
}


// Initialize the plugin
$affiliate_portal = new AffiliatePortal();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = sanitize_text_field($_POST['action']);
    
    switch ($action) {
        case 'affiliate_register':
            $affiliate_portal->handle_registration();
            break;
            
        case 'affiliate_login':
            $affiliate_portal->handle_login();
            break;
            
        case 'affiliate_logout':
            $affiliate_portal->handle_logout();
            break;
            
        case 'affiliate_change_password':
            $affiliate_portal->handle_change_password();
            break;
            
        case 'get_countries':
            $affiliate_portal->get_countries();
            break;
            
        case 'get_states':
            $affiliate_portal->get_states();
            break;
            
        case 'affiliate_admin_login':
            $affiliate_portal->handle_admin_login();
            break;
            
        case 'affiliate_admin_logout':
            $affiliate_portal->handle_admin_logout();
            break;
            
        case 'affiliate_get_applications':
            $affiliate_portal->get_admin_applications();
            break;
            
        case 'affiliate_update_status':
            $affiliate_portal->update_application_status();
            break;
            
        case 'affiliate_get_registration_details':
            $affiliate_portal->handle_get_registration_details();
            break;
            
        case 'affiliate_submit_kyc':
            $affiliate_portal->handle_kyc_submission();
            break;
            
        case 'affiliate_save_kyc_draft':
            $affiliate_portal->handle_kyc_draft_save();
            break;
            
        case 'affiliate_get_kyc_applications':
            $affiliate_portal->get_kyc_applications();
            break;
            
        case 'affiliate_get_kyc_details':
            $affiliate_portal->get_kyc_details();
            break;
            
        case 'affiliate_update_kyc_status':
            $affiliate_portal->update_kyc_status();
            break;
            
        case 'affiliate_get_kyc_verification_details':
            $affiliate_portal->get_kyc_verification_details();
            break;
            
        case 'affiliate_approve_document':
            $affiliate_portal->handle_document_approval();
            break;
            
        case 'affiliate_reject_document':
            $affiliate_portal->handle_document_rejection();
            break;
            
        case 'affiliate_get_document_details':
            $affiliate_portal->get_document_details();
            break;
            
        case 'affiliate_upload_company_docs':
            $affiliate_portal->handle_company_documents();
            break;
            
        case 'load_company_documents_form':
            $affiliate_portal->load_company_documents_form();
            break;
            
        case 'affiliate_admin_update_email_config':
            $affiliate_portal->handle_admin_update_email_config();
            break;
            
        case 'affiliate_get_email_config':
            $affiliate_portal->get_email_config();
            break;
            
        default:
            wp_send_json_error('Invalid action: ' . $action);
            break;
    }
} else {
    wp_send_json_error('No action specified');
}
?>