<?php
/**
 * AJAX Handler for standalone affiliate portal
 */

// Start session and include necessary files
session_start();

// Simulate WordPress environment
define('ABSPATH', __DIR__ . '/');
require_once 'database.php';

// WordPress-like functions for standalone environment
if (!function_exists('wp_send_json_success')) {
    function wp_send_json_success($data = null) {
        wp_send_json(array(
            'success' => true,
            'data' => $data
        ));
    }
}

if (!function_exists('wp_send_json_error')) {
    function wp_send_json_error($data = null) {
        wp_send_json(array(
            'success' => false,
            'data' => $data
        ));
    }
}

if (!function_exists('wp_send_json')) {
    function wp_send_json($response) {
        echo json_encode($response);
        exit;
    }
}

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
        // Simple nonce verification for standalone mode
        return true; // For development, bypassing nonce verification
    }
}

if (!function_exists('wp_create_nonce')) {
    function wp_create_nonce($action = -1) {
        // Simple nonce creation for standalone mode
        return 'standalone_nonce_' . md5($action . time());
    }
}

if (!function_exists('check_ajax_referer')) {
    function check_ajax_referer($action = -1, $query_arg = '_wpnonce', $die = true) {
        // Simple referer check for standalone mode
        return true; // For development, bypassing referer check
    }
}

if (!function_exists('wp_mail')) {
    function wp_mail($to, $subject, $message, $headers = '', $attachments = array()) {
        // Simple mail function for standalone mode - just log emails
        error_log("EMAIL: To: {$to}, Subject: {$subject}");
        error_log("EMAIL: Message: " . substr($message, 0, 200) . '...');
        return true; // For development, simulating successful email sending
    }
}

if (!function_exists('is_email')) {
    function is_email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('esc_attr')) {
    function esc_attr($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_textarea')) {
    function esc_textarea($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

// Include the main plugin file
require_once 'affiliate-portal.php';

// Create affiliate portal instance
$affiliate_portal = new AffiliatePortal();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = sanitize_text_field($_POST['action']);
    
    // Set proper headers for JSON response
    header('Content-Type: application/json');
    
    // Handle different actions
    switch ($action) {
        case 'affiliate_submit_kyc':
            $affiliate_portal->handle_kyc_submission();
            break;
            
        case 'affiliate_register':
            $affiliate_portal->handle_registration();
            break;
            
        case 'affiliate_login':
            $affiliate_portal->handle_login();
            break;
            
        case 'affiliate_logout':
            $affiliate_portal->handle_logout();
            break;
            
        default:
            wp_send_json_error('Invalid action');
            break;
    }
} else {
    wp_send_json_error('Invalid request');
}