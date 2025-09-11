<?php
if (!defined('ABSPATH')) {
    exit;
}

// Updated authentication check using cookie-based system
global $wpdb;

// Get the affiliate portal instance for authentication checks
$affiliate_portal = new AffiliatePortal();

// Check if user is authenticated using the same method as dashboard
if (!$affiliate_portal->is_user_authenticated()) {
    echo '<div class="affiliate-error">Please log in to access KYC verification.</div>';
    return;
}

// Get user ID from cookie authentication system
$user_id = $affiliate_portal->get_current_user_id();
if (!$user_id) {
    echo '<div class="affiliate-error">User session expired. Please log in again.</div>';
    return;
}

// Get user data from database
$table_name = $wpdb->prefix . 'affiliate_users';
$current_user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $user_id));

if (!$current_user) {
    error_log("KYC Form: User not found in database. User ID: " . $user_id);
    echo '<div class="affiliate-error">Unable to load user data. Please try logging out and logging in again.</div>';
    return;
}

// Check existing KYC data and status
$kyc_table = $wpdb->prefix . 'affiliate_kyc';
$kyc_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $kyc_table WHERE user_id = %d", $user_id));

// Determine account type based on the TYPE field from registration
$account_type_lower = strtolower(trim($current_user->type ?? ''));
$is_individual = $account_type_lower !== 'company';

// Debug logging
error_log('KYC Form Router - User ID: ' . $user_id);
error_log('KYC Form Router - Account Type: "' . ($current_user->type ?? 'NULL') . '"');
error_log('KYC Form Router - Is Individual: ' . ($is_individual ? 'YES' : 'NO'));

// Determine what to show based on KYC status and main user status
$kyc_status = $kyc_data ? $kyc_data->kyc_status : 'not_started';
$main_user_status = $current_user->status ?? 'pending';

// Check if admin has requested additional documents via main status
$admin_requested_additional = ($main_user_status === 'additional document required');

$show_view_mode = in_array($kyc_status, ['awaiting approval', 'approved']) && !$admin_requested_additional;
$show_edit_mode = in_array($kyc_status, ['not_started', 'draft', 'rejected', 'incomplete', 'additional document required']) || $admin_requested_additional;

// Debug logging to track form display decisions
error_log('KYC Form Display - User ID: ' . $user_id);
error_log('KYC Form Display - KYC Status from DB: "' . $kyc_status . '"');
error_log('KYC Form Display - Main User Status: "' . $main_user_status . '"');
error_log('KYC Form Display - Admin Requested Additional: ' . ($admin_requested_additional ? 'YES' : 'NO'));
error_log('KYC Form Display - Show View Mode: ' . ($show_view_mode ? 'YES' : 'NO'));
error_log('KYC Form Display - Show Edit Mode: ' . ($show_edit_mode ? 'YES' : 'NO'));
error_log('KYC Form Display - Has KYC Data: ' . ($kyc_data ? 'YES' : 'NO'));

// Check if reupload is requested
if (isset($_GET['reupload']) && $_GET['reupload'] === '1') {
    $show_edit_mode = true;
    $show_view_mode = false;
}

if ($show_view_mode) {
    // Show view mode for submitted/approved applications
    if ($is_individual) {
        include plugin_dir_path(__FILE__) . 'kyc-view-individual.php';
    } else {
        include plugin_dir_path(__FILE__) . 'kyc-view-company.php';
    }
} elseif ($show_edit_mode) {
    // Show edit mode for new, draft, or rejected applications
    if ($is_individual) {
        include plugin_dir_path(__FILE__) . 'kyc-form-individual.php';
    } else {
        include plugin_dir_path(__FILE__) . 'kyc-form-company.php';
    }
} else {
    echo '<div class="affiliate-error">Unknown KYC status. Please contact support.</div>';
}
?>