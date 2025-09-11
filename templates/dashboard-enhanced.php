<?php
// CRITICAL: Prevent ALL caching of dashboard data
if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0, s-maxage=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Expires: Thu, 01 Jan 1970 00:00:00 GMT'); // Unix epoch (always expired)
    header('X-Accel-Expires: 0'); // Nginx cache prevention
}

// Get current user data - use global $wpdb instead of instance
global $wpdb;

// Database session authentication
// Get the plugin instance to access authentication methods
global $affiliate_portal_instance;
if (!$affiliate_portal_instance) {
    $affiliate_portal_instance = new AffiliatePortal();
}

if (!$affiliate_portal_instance || !class_exists('AffiliatePortal')) {
    echo '<div class="affiliate-alert affiliate-alert-danger">System error. Please <a href="' . get_permalink(get_page_by_title('Affiliate Login')) . '">log in again</a>.</div>';
    return;
}

// Database session authentication - reliable and secure
$session_id = $_COOKIE['affiliate_session'] ?? '';
$cookie_user_id = $_COOKIE['affiliate_user_id'] ?? '';
$cookie_username = $_COOKIE['affiliate_username'] ?? '';

error_log('Dashboard authentication check - Session ID present: ' . ($session_id ? 'YES' : 'NO') . ', Cookie User ID: ' . $cookie_user_id . ', Cookie Username: ' . $cookie_username);

$auth_data = $affiliate_portal_instance->is_user_authenticated();

if (!$auth_data) {
    error_log('Dashboard authentication FAILED - Session validation returned false. Session ID: ' . $session_id);
    
    // Clear session cookies
    if (!headers_sent()) {
        $is_secure = is_ssl() || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        setcookie('affiliate_session', '', time() - 3600, '/', '', $is_secure, true);
        setcookie('affiliate_user_id', '', time() - 3600, '/', '', false, false);
        setcookie('affiliate_username', '', time() - 3600, '/', '', false, false);
    }
    
    echo '<div class="affiliate-alert affiliate-alert-danger">Authentication failed. Please <a href="' . get_permalink(get_page_by_title('Affiliate Login')) . '">log in again</a>.</div>';
    return;
} else {
    error_log('Dashboard authentication SUCCESS - User ID: ' . ($auth_data['user_id'] ?? 'missing') . ', Username: ' . ($auth_data['username'] ?? 'missing'));
}

// Extract authenticated user data from database session
$user_id = $auth_data['user_id'] ?? null;
$username = $auth_data['username'] ?? null;

if (!$user_id || !$username) {
    error_log('CRITICAL: Invalid session data - missing user info');
    echo '<div class="affiliate-alert affiliate-alert-danger">Invalid session data. Please <a href="' . get_permalink(get_page_by_title('Affiliate Login')) . '">log in again</a>.</div>';
    return;
}


// Enhanced logging for debugging authentication issues
error_log('Dashboard auth validation - User ID: "' . $user_id . '" (type: ' . gettype($user_id) . '), Username: "' . $username . '" (type: ' . gettype($username) . ')');

// REMOVED: Test user validation - was causing false positives
// Only validate that we have proper numeric user ID and valid username

// CRITICAL: Final validation of session user ID
if (!is_numeric($user_id) || intval($user_id) <= 0) {
    error_log('CRITICAL: Invalid user ID from session! User ID: "' . $user_id . '" (type: ' . gettype($user_id) . '), Username: "' . $username . '"');
    echo '<div class="affiliate-alert affiliate-alert-danger">Invalid authenticated session. Please <a href="' . get_permalink(get_page_by_title('Affiliate Login')) . '">log in again</a>.</div>';
    return;
}

// CRITICAL: Final user ID conversion and validation
$user_id = intval($user_id);
$table_name = $wpdb->prefix . 'affiliate_users';


// AGGRESSIVE cache clearing moved to right before database query to prevent interference

// CRITICAL: Force browser to never cache this page and always fetch fresh content
if (!headers_sent()) {
    header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0, private');
    header('Pragma: no-cache');
    header('Expires: -1'); // Force expiry in past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('ETag: "' . md5($user_id . time() . uniqid()) . '"'); // Unique ETag
    
    // Additional headers to prevent caching
    header('Vary: *'); // Prevent proxy caching
    header('X-Cache-Control: no-cache'); // Additional cache prevention
}


// SECURITY FIX: Use database session data instead of cookies
// Validate user_id from database session
if (!is_numeric($user_id) || $user_id <= 0) {
    error_log('CRITICAL: Invalid user_id from session: ' . $user_id);
    echo '<div class="affiliate-alert affiliate-alert-danger">Invalid session. Please <a href="' . get_permalink(get_page_by_title('Affiliate Login')) . '">log in again</a>.</div>';
    return;
}

// CRITICAL FIX: Force complete cache flush and get absolutely fresh user data
if (function_exists('wp_cache_flush')) {
    wp_cache_flush(); // Clear ALL WordPress cache
}

// Clear specific object cache entries that might contain user data
if (function_exists('wp_cache_delete')) {
    for ($i = 1; $i <= 20; $i++) { // Clear potential cached users
        wp_cache_delete($i, 'users');
        wp_cache_delete($i, 'affiliate_users');
        wp_cache_delete('user_' . $i, 'default');
        wp_cache_delete('affiliate_user_' . $i, 'default');
    }
}

// Force MySQL to not use query cache and get fresh data
$wpdb->flush();
$wpdb->query("SET SESSION query_cache_type = OFF");

// CRITICAL: Get fresh user data with FORCED database query (no cache)
error_log('DASHBOARD: Fetching user data for ID: ' . $user_id . ', Username: ' . $username);
$current_user = $wpdb->get_row($wpdb->prepare("
    SELECT SQL_NO_CACHE * FROM $table_name WHERE id = %d AND username = %s AND id > 0 LIMIT 1
", $user_id, $username), OBJECT);

error_log('DASHBOARD: Database returned user: ' . ($current_user ? 'ID=' . $current_user->id . ', Username=' . $current_user->username : 'NULL'));

// Verify correct user data returned with database session validation
if ($current_user && (intval($current_user->id) !== $user_id || $current_user->username !== $username)) {
    error_log('CRITICAL: User data mismatch! Expected ID: ' . $user_id . ', Username: ' . $username . ' | Got ID: ' . ($current_user->id ?? 'null') . ', Username: ' . ($current_user->username ?? 'null'));
    
    // Clear session from database and cookies
    $session_id = $_COOKIE['affiliate_session'] ?? '';
    if ($session_id) {
        $affiliate_portal_instance->destroy_session($session_id);
    }
    $affiliate_portal_instance->clear_session_cookie();
    
    echo '<div class="affiliate-alert affiliate-alert-danger">Session validation failed. Please <a href="' . get_permalink(get_page_by_title('Affiliate Login')) . '">log in again</a>.</div>';
    return;
}


// CRITICAL: Double-check that we got the correct user data
if ($current_user && intval($current_user->id) !== $user_id) {
    error_log('CRITICAL: Database returned wrong user! Expected: ' . $user_id . ', Got: ' . $current_user->id);
    echo '<div class="affiliate-alert affiliate-alert-danger">Data integrity error. Please <a href="' . get_permalink(get_page_by_title('Affiliate Login')) . '">log in again</a>.</div>';
    return;
}

// Additional user data integrity check
if ($current_user && $current_user->username !== $username) {
    error_log('CRITICAL: Username mismatch in session! Session username: ' . $username . ', DB username: ' . $current_user->username);
    
    // Destroy corrupted session
    $session_id = $_COOKIE['affiliate_session'] ?? '';
    if ($session_id) {
        $affiliate_portal_instance->destroy_session($session_id);
    }
    $affiliate_portal_instance->clear_session_cookie();
    
    echo '<div class="affiliate-alert affiliate-alert-danger">Session data corrupted. Please <a href="' . get_permalink(get_page_by_title('Affiliate Login')) . '">log in again</a>.</div>';
    return;
}

if (!$current_user) {
    error_log('CRITICAL: User not found in database. User ID: ' . $user_id . ' - FORCING COMPLETE LOGOUT');
    
    // EMERGENCY: Force complete cookie clearing
    if (!headers_sent()) {
        $is_secure = is_ssl() || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        // REMOVED: JWT auth token clearing
        setcookie('affiliate_user_id', '', time() - 3600, '/', '', false, false);
        setcookie('affiliate_username', '', time() - 3600, '/', '', false, false);
        
        // Clear any other potential cookies
        setcookie('wp-settings-1', '', time() - 3600, '/');
        setcookie('wp-settings-time-1', '', time() - 3600, '/');
    }
    
    echo '<div class="affiliate-alert affiliate-alert-danger">
        <h4>Authentication Error</h4>
        <p>Your user account could not be found or has been removed. All authentication cookies have been cleared.</p>
        <p><strong>Please <a href="' . get_permalink(get_page_by_title('Affiliate Login')) . '">log in again</a> with valid credentials.</strong></p>
    </div>';
    return;
}

// Get actual status and remarks from database
$actual_status = isset($current_user->status) ? $current_user->status : 'pending';
$admin_remarks = isset($current_user->admin_remarks) ? $current_user->admin_remarks : '';
$status_class = 'affiliate-status-pending-review';
$status_text = 'Pending Review';

// Map database status to display values
switch (strtolower($actual_status)) {
    case 'approved':
    case 'active':
        $status_class = 'affiliate-status-approved';
        $status_text = 'Approved';
        break;
    case 'pending':
        $status_class = 'affiliate-status-pending';
        $status_text = 'Pending Review';
        break;
    case 'kyc pending':
        $status_class = 'affiliate-status-pending';
        $status_text = 'KYC Pending';
        break;
    case 'rejected':
    case 'denied':
        $status_class = 'affiliate-status-rejected';
        $status_text = 'Rejected';
        break;
    case 'suspended':
        $status_class = 'affiliate-status-suspended';
        $status_text = 'Suspended';
        break;
    case 'additional document required':
        $status_class = 'affiliate-status-documents-required';
        $status_text = 'Addition Info. Required';
        break;
    case 'awaiting approval':
        $status_class = 'affiliate-status-awaiting-approval';
        $status_text = 'Awaiting Approval';
        break;
    default:
        // Fallback based on account type if status is not set
        if ($current_user->type === 'Individual') {
            $status_class = 'affiliate-status-pending';
            $status_text = 'Pending Review';
        } elseif ($current_user->type === 'Company') {
            $status_class = 'affiliate-status-documents-required';
            $status_text = 'Addition Info. Required';
        }
}

// Get logo URL from WordPress settings
$logo_url = get_option('affiliate_portal_logo', '');
?>

<style>
/* Full-screen dashboard styles */
.affiliate-dashboard-fullscreen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: #f8f9fa;
    z-index: 9999;
    overflow-y: auto;
    padding: 0;
    margin: 0;
}

.affiliate-dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 40px;
    background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.affiliate-header-left .affiliate-logo-placeholder,
.affiliate-header-left .affiliate-logo {
    width: 100px;
    height: 50px;
    background: transparent;
    border-radius: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 18px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    letter-spacing: 1px;
}

.affiliate-user-profile {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 25px;
    padding: 10px 20px;
    cursor: pointer;
    color: white;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.affiliate-user-profile:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-1px);
}

.affiliate-profile-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border-radius: 12px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    min-width: 200px;
    z-index: 99999;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    margin-top: 10px;
    overflow: hidden;
}

.affiliate-profile-dropdown.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.affiliate-dropdown-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px 20px;
    color: #333;
    text-decoration: none;
    transition: all 0.2s ease;
    border-bottom: 1px solid #f0f0f0;
}

.affiliate-dropdown-item:last-child {
    border-bottom: none;
}

.affiliate-dropdown-item:hover {
    background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
    color: white;
}

.affiliate-dashboard-content {
    padding: 40px;
    max-width: 1400px;
    margin: 0 auto;
    background: white;
    min-height: calc(100vh - 80px);
    display: block;
}

.affiliate-welcome-section {
    text-align: center;
    margin-bottom: 40px;
    padding: 30px 0;
}

.affiliate-welcome-section h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 10px;
    color: #2c3e50;
    background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.affiliate-welcome-subtitle {
    font-size: 1.1rem;
    color: #6c757d;
    font-weight: 500;
}

.affiliate-registration-summary {
    background: rgba(255, 255, 255, 0.98);
    border-radius: 24px;
    padding: 40px;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    position: relative;
    overflow: hidden;
    width: 100%;
    display: block;
    clear: both;
}

.affiliate-registration-summary::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #0d6efd 0%, #6610f2 100%);
}

.affiliate-summary-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.affiliate-summary-header h2 {
    color: #333;
    font-size: 1.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 12px;
}

.affiliate-summary-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    border-radius: 16px;
    overflow: hidden;
    background: white;
}

.affiliate-summary-table th {
    background: #f8f9fa;
    color: #495057;
    padding: 18px 16px;
    text-align: center;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    border: 1px solid #dee2e6;
    border-bottom: 2px solid #dee2e6;
    position: relative;
    white-space: nowrap;
}

.affiliate-summary-table th:first-child {
    border-top-left-radius: 16px;
}

.affiliate-summary-table th:last-child {
    border-top-right-radius: 16px;
}

.affiliate-summary-table td {
    padding: 24px 16px;
    border: none;
    color: #2c3e50;
    vertical-align: middle;
    font-size: 0.95rem;
    font-weight: 500;
    border-bottom: 1px solid #eef2f7;
    white-space: nowrap;
}

.affiliate-summary-table tbody tr {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: white;
}

.affiliate-summary-table tbody tr:hover {
    background: #f1f3f4;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.affiliate-summary-table tbody tr:last-child td {
    border-bottom: none;
}

.affiliate-summary-table tbody tr:last-child td:first-child {
    border-bottom-left-radius: 16px;
}

.affiliate-summary-table tbody tr:last-child td:last-child {
    border-bottom-right-radius: 16px;
}

.affiliate-action-buttons {
    display: flex;
    gap: 10px;
}

.affiliate-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 24px;
    font-size: 1rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
    min-height: 48px;
    height: 48px;
    box-sizing: border-box;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    position: relative;
}

.affiliate-btn-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
    color: white;
}

.affiliate-btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(13, 110, 253, 0.3);
}

.affiliate-status-badge {
    padding: 10px 18px;
    border-radius: 25px;
    font-weight: 700;
    font-size: 0.85rem;
    text-align: center;
    display: inline-block;
    min-width: 130px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border: 2px solid transparent;
}

/* Status colors */
.affiliate-status-pending {
    background-color: #ffc107;
    color: #000;
}

.affiliate-status-pending-review {
    background-color: #007bff;
    color: white;
}

.affiliate-status-documents-required {
    background-color: #dc3545;
    color: white;
}

/* Modal styles */
.affiliate-modal {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(5px);
}

.affiliate-modal-content {
    background-color: white;
    margin: 2% auto;
    padding: 0;
    border-radius: 15px;
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
}

.affiliate-modal-header {
    background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
    color: white;
    padding: 20px 30px;
    border-radius: 15px 15px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.affiliate-modal-header h2 {
    margin: 0;
    font-size: 1.5rem;
}

.affiliate-close {
    color: white;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.3s;
}

.affiliate-close:hover {
    color: #f0f0f0;
}

.affiliate-modal-body {
    padding: 30px;
}

.affiliate-detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.affiliate-detail-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}

.affiliate-detail-label {
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.affiliate-detail-value {
    color: #666;
}

/* Table container for mobile scrolling */
.affiliate-table-container {
    overflow-x: auto;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
}

/* Responsive design */
@media (max-width: 1200px) {
    .affiliate-summary-table {
        font-size: 0.85rem;
    }
    
    .affiliate-summary-table th,
    .affiliate-summary-table td {
        padding: 16px 12px;
    }
}

@media (max-width: 968px) {
    .affiliate-dashboard-content {
        padding: 30px 20px;
    }
    
    .affiliate-summary-table {
        overflow-x: auto;
        display: block;
        white-space: nowrap;
    }
    
    .affiliate-summary-table thead,
    .affiliate-summary-table tbody,
    .affiliate-summary-table tr {
        display: table;
        width: 100%;
        table-layout: fixed;
    }
    
    .affiliate-summary-table {
        min-width: 900px;
    }
    
    .affiliate-summary-table th {
        font-size: 0.75rem;
        padding: 18px 12px;
        letter-spacing: 1px;
    }
}

@media (max-width: 768px) {
    .affiliate-dashboard-header {
        padding: 15px 20px;
        flex-direction: column;
        gap: 15px;
    }
    
    .affiliate-dashboard-content {
        padding: 20px 15px;
    }
    
    .affiliate-welcome-section {
        padding: 20px 25px;
        margin: 0 -5px 30px;
    }
    
    .affiliate-welcome-section h1 {
        font-size: 2.2rem;
    }
    
    .affiliate-registration-summary {
        padding: 20px;
        margin: 0 -5px;
    }
    
    .affiliate-summary-table {
        border-radius: 12px;
        min-width: 800px;
    }
    
    .affiliate-summary-table th,
    .affiliate-summary-table td {
        padding: 14px 8px;
        font-size: 0.8rem;
    }
    
    .affiliate-btn {
        padding: 10px 16px;
        font-size: 0.8rem;
    }
    
    .affiliate-status-badge {
        min-width: 100px;
        padding: 8px 12px;
        font-size: 0.75rem;
    }
}

@media (max-width: 480px) {
    .affiliate-welcome-section h1 {
        font-size: 1.8rem;
    }
    
    .affiliate-dashboard-content {
        padding: 15px 10px;
    }
    
    .affiliate-registration-summary {
        padding: 15px;
        border-radius: 12px;
    }
    
    .affiliate-summary-table {
        min-width: 700px;
        border-radius: 10px;
    }
}
</style>

<div class="affiliate-dashboard-fullscreen">
    <!-- Header -->
    <div class="affiliate-dashboard-header">
        <div class="affiliate-header-left">
            <?php if ($logo_url): ?>
                <div class="affiliate-logo">
                    <img src="<?php echo esc_url($logo_url); ?>" alt="Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                </div>
            <?php else: ?>
                <div class="affiliate-logo-placeholder">
                    <span>LOGO</span>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="affiliate-header-right">
            <div class="affiliate-user-menu" style="position: relative;">
                <div class="affiliate-user-profile" onclick="toggleProfileDropdown()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <circle cx="12" cy="8" r="3"/>
                        <path d="M12 14c-4 0-8 2-8 6v2h16v-2c0-4-4-6-8-6z"/>
                    </svg>
                    <span><?php echo esc_html($current_user->first_name . ' ' . $current_user->last_name); ?></span>
                    <svg class="affiliate-dropdown-arrow" width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7 10l5 5 5-5z"/>
                    </svg>
                </div>
                <div class="affiliate-profile-dropdown">
                    <a href="#" class="affiliate-dropdown-item" onclick="logout()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16,17 21,12 16,7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="affiliate-dashboard-content">
        <!-- Welcome Section -->
        <div class="affiliate-welcome-section">
            <h1>Welcome, <?php echo esc_html($current_user->first_name . ' ' . $current_user->last_name); ?>!</h1>
            <p class="affiliate-welcome-subtitle">Your affiliate registration overview</p>
        </div>

        <!-- Registration Summary -->
        <div class="affiliate-registration-summary">
            <div class="affiliate-summary-header">
                <h2>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14,2 14,8 20,8"/>
                    </svg>
                    Registration Summary
                </h2>
            </div>

            <div class="affiliate-table-container">
                <table class="affiliate-summary-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Account Type</th>
                        <th>Mobile</th>
                        <th>Registration Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong><?php echo esc_html($current_user->username); ?></strong></td>
                        <td><?php echo esc_html(($current_user->name_prefix ? $current_user->name_prefix . ' ' : '') . $current_user->first_name . ' ' . $current_user->last_name); ?></td>
                        <td><?php echo esc_html($current_user->email); ?></td>
                        <td><?php echo esc_html($current_user->type); ?></td>
                        <td><?php 
                            $mobile_display = $current_user->mobile_number;
                            if ($current_user->country_code && !str_starts_with($mobile_display, '+')) {
                                $mobile_display = '+' . $current_user->country_code . ' ' . $mobile_display;
                            }
                            echo esc_html($mobile_display); 
                        ?></td>
                        <td><?php echo esc_html(date('M j, Y', strtotime($current_user->created_at))); ?></td>
                        <td>
                            <span class="affiliate-status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                            <?php if (!empty($admin_remarks) && strtolower($actual_status) !== 'awaiting approval'): ?>
                                <div class="admin-remarks-preview" style="margin-top: 8px; font-size: 0.8rem; color: #6c757d; font-style: italic;">
                                    Admin Note: <?php echo esc_html(wp_trim_words($admin_remarks, 8, '...')); ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="affiliate-action-buttons">
                                <button class="affiliate-btn affiliate-btn-primary" onclick="openDetailsModal()">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    View Details
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
                </table>
            </div>
        </div>
        <!-- KYC Process Section -->
        <?php
        // Check user's application status to determine what to show
        $user_status = $current_user->status ?? 'kyc pending';
        
        // Show KYC form if status is pending or if additional information is required
        if (in_array($user_status, ['kyc pending', 'additional document required'])) {
            include AFFILIATE_PORTAL_PATH . 'templates/kyc-form.php';
        } else {
            // Show status message for completed/submitted KYC
            ?>
            <div class="affiliate-kyc-section" style="margin-top: 40px;">
                <div class="affiliate-summary-header">
                    <h2>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        KYC Verification Status
                    </h2>
                </div>
                
                <div class="kyc-status-container" style="background: rgba(255, 255, 255, 0.98); border-radius: 24px; padding: 40px; box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1); margin-top: 20px;">
                    <?php if ($user_status === 'awaiting approval'): ?>
                        <div style="text-align: center; color: #17a2b8;">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="currentColor" style="margin-bottom: 20px;">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            <h3 style="color: #17a2b8; margin-bottom: 15px;">KYC Application Under Review</h3>
                            <p style="color: #6c757d; font-size: 1.1rem; line-height: 1.6;">
                                Your KYC application has been submitted successfully and is currently under review. 
                                Our compliance team will process your application within 3-5 business days.
                            </p>
                            <p style="color: #6c757d; margin-top: 20px;">
                                You will receive an email notification once the review is complete.
                            </p>
                        </div>
                    <?php elseif ($user_status === 'approved'): ?>
                        <div style="text-align: center; color: #28a745;">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="currentColor" style="margin-bottom: 20px;">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 style="color: #28a745; margin-bottom: 15px;">KYC Verified ✓</h3>
                            <p style="color: #6c757d; font-size: 1.1rem; line-height: 1.6;">
                                Congratulations! Your KYC verification has been completed and approved. 
                                Your account is now fully verified and you can access all affiliate features.
                            </p>
                        </div>
                    <?php elseif ($user_status === 'rejected'): ?>
                        <div style="text-align: center; color: #dc3545;">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="currentColor" style="margin-bottom: 20px;">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                            </svg>
                            <h3 style="color: #dc3545; margin-bottom: 15px;">KYC Application Rejected</h3>
                            <p style="color: #6c757d; font-size: 1.1rem; line-height: 1.6;">
                                Your KYC application has been rejected. Please review the admin notes below and resubmit with updated information.
                            </p>
                            <?php if (!empty($admin_remarks)): ?>
                                <div style="background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; padding: 15px; margin-top: 20px; text-align: left;">
                                    <strong style="color: #721c24;">Admin Notes:</strong><br>
                                    <span style="color: #721c24;"><?php echo esc_html($admin_remarks); ?></span>
                                </div>
                            <?php endif; ?>
                            <div style="margin-top: 20px;">
                                <button class="affiliate-btn affiliate-btn-primary" onclick="location.reload();" style="padding: 12px 24px; font-size: 1rem;">
                                    Resubmit KYC Application
                                </button>
                            </div>
                        </div>
                    <?php elseif ($user_status === 'additional document required'): ?>
                        <div style="text-align: center; color: #ffc107;">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="currentColor" style="margin-bottom: 20px;">
                                <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                            </svg>
                            <h3 style="color: #ffc107; margin-bottom: 15px;">Additional Information Required</h3>
                            <p style="color: #6c757d; font-size: 1.1rem; line-height: 1.6;">
                                Your KYC application requires additional documents or information. Please review the admin notes below and upload the required documents.
                            </p>
                            <?php if (!empty($admin_remarks)): ?>
                                <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 15px; margin-top: 20px; text-align: left;">
                                    <strong style="color: #856404;">Admin Requirements:</strong><br>
                                    <span style="color: #856404;"><?php echo nl2br(esc_html($admin_remarks)); ?></span>
                                </div>
                            <?php endif; ?>
                            <div style="margin-top: 20px;">
                                <button class="affiliate-btn affiliate-btn-warning" onclick="scrollToKYCForm();" style="padding: 12px 24px; font-size: 1rem; background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%); border: none; color: white;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px;">
                                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                                    </svg>
                                    Update Documents & Details
                                </button>
                                <p style="font-size: 0.9rem; color: #6c757d; margin-top: 10px;">
                                    Please scroll down to complete the required updates below.
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }
        ?>

    </div>
</div>

<!-- Registration Details Modal -->
<div id="detailsModal" class="affiliate-modal">
    <div class="affiliate-modal-content">
        <div class="affiliate-modal-header">
            <h2>Complete Registration Details</h2>
            <span class="affiliate-close" onclick="closeDetailsModal()">&times;</span>
        </div>
        <div class="affiliate-modal-body">
            <div class="affiliate-detail-grid">
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Username</div>
                    <div class="affiliate-detail-value"><?php echo esc_html($current_user->username); ?></div>
                </div>
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Full Name</div>
                    <div class="affiliate-detail-value"><?php echo esc_html(($current_user->name_prefix ? $current_user->name_prefix . ' ' : '') . $current_user->first_name . ' ' . $current_user->last_name); ?></div>
                </div>
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Email Address</div>
                    <div class="affiliate-detail-value"><?php echo esc_html($current_user->email); ?></div>
                </div>
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Account Type</div>
                    <div class="affiliate-detail-value"><?php echo esc_html($current_user->type); ?></div>
                </div>
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Date of Birth</div>
                    <div class="affiliate-detail-value"><?php echo esc_html($current_user->dob ?: 'Not provided'); ?></div>
                </div>
                <?php if ($current_user->company_name): ?>
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Company Name</div>
                    <div class="affiliate-detail-value"><?php echo esc_html($current_user->company_name); ?></div>
                </div>
                <?php endif; ?>
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Mobile Number</div>
                    <div class="affiliate-detail-value"><?php 
                        $mobile_display = $current_user->mobile_number;
                        if ($current_user->country_code && !str_starts_with($mobile_display, '+')) {
                            $mobile_display = '+' . $current_user->country_code . ' ' . $mobile_display;
                        }
                        echo esc_html($mobile_display); 
                    ?></div>
                </div>
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Address</div>
                    <div class="affiliate-detail-value"><?php echo esc_html($current_user->address_line1 . ($current_user->address_line2 ? ', ' . $current_user->address_line2 : '') . ', ' . $current_user->city . ', ' . $current_user->state . ', ' . $current_user->country . ' ' . $current_user->zipcode); ?></div>
                </div>
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Affiliate Type</div>
                    <div class="affiliate-detail-value"><?php echo esc_html($current_user->affiliate_type); ?></div>
                </div>
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Preferred Currency</div>
                    <div class="affiliate-detail-value"><?php echo esc_html($current_user->currency); ?></div>
                </div>
                <?php if ($current_user->chat_id_channel): ?>
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Telegram/Teams</div>
                    <div class="affiliate-detail-value"><?php echo esc_html($current_user->chat_id_channel); ?></div>
                </div>
                <?php endif; ?>
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Registration Date</div>
                    <div class="affiliate-detail-value"><?php echo esc_html(date('F j, Y \a\t g:i A', strtotime($current_user->created_at))); ?></div>
                </div>
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Status</div>
                    <div class="affiliate-detail-value"><span class="affiliate-status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span></div>
                </div>
                <?php if (!empty($admin_remarks) && strtolower($actual_status) !== 'awaiting approval'): ?>
                <div class="affiliate-detail-item" style="grid-column: 1 / -1; border-left: 4px solid #17a2b8; background: #e7f3ff;">
                    <div class="affiliate-detail-label" style="color: #0c5460; font-weight: 700;">Admin Remarks</div>
                    <div class="affiliate-detail-value" style="color: #0c5460; font-style: italic; line-height: 1.5;"><?php echo nl2br(esc_html($admin_remarks)); ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<script>
// Define affiliate_ajax object for AJAX calls
if (typeof affiliate_ajax === 'undefined') {
    var affiliate_ajax = {
        ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>',
        nonce: '<?php echo wp_create_nonce('affiliate_nonce'); ?>'
    };
}

function toggleProfileDropdown() {
    const dropdown = document.querySelector('.affiliate-profile-dropdown');
    dropdown.classList.toggle('show');
}

function openDetailsModal() {
    document.getElementById('detailsModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeDetailsModal() {
    document.getElementById('detailsModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}


function logout() {
    if (confirm('Are you sure you want to logout?')) {
        const formData = new FormData();
        formData.append('action', 'affiliate_logout');
        formData.append('nonce', affiliate_ajax.nonce);
        
        fetch(affiliate_ajax.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.redirect) {
                window.location.href = data.data.redirect;
            } else {
                // Fallback logout - try multiple methods to find login page
                <?php 
                $fallback_url = '';
                $login_page = get_page_by_title('Affiliate Login');
                if ($login_page) {
                    $fallback_url = get_permalink($login_page);
                } else {
                    $login_page_by_slug = get_page_by_path('affiliate-login');
                    if ($login_page_by_slug) {
                        $fallback_url = get_permalink($login_page_by_slug);
                    } else {
                        $fallback_url = home_url('/affiliate-login/');
                    }
                }
                ?>
                window.location.href = '<?php echo esc_url($fallback_url); ?>';
            }
        })
        .catch(error => {
            // Fallback logout on error
            <?php 
            $fallback_url = '';
            $login_page = get_page_by_title('Affiliate Login');
            if ($login_page) {
                $fallback_url = get_permalink($login_page);
            } else {
                $login_page_by_slug = get_page_by_path('affiliate-login');
                if ($login_page_by_slug) {
                    $fallback_url = get_permalink($login_page_by_slug);
                } else {
                    $fallback_url = home_url('/affiliate-login/');
                }
            }
            ?>
            window.location.href = '<?php echo esc_url($fallback_url); ?>';
        });
    }
    toggleProfileDropdown();
}


// Close modals when clicking outside
window.onclick = function(event) {
    const detailsModal = document.getElementById('detailsModal');
    
    if (event.target === detailsModal) {
        closeDetailsModal();
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.querySelector('.affiliate-profile-dropdown');
    const profile = document.querySelector('.affiliate-user-profile');
    
    if (!event.target.closest('.affiliate-user-menu')) {
        dropdown.classList.remove('show');
    }
});

// Escape key to close modals
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDetailsModal();
    }
});

// CRITICAL: Force browser cache clearing on page load
document.addEventListener('DOMContentLoaded', function() {
    // Clear all browser storage to prevent cached user data
    try {
        if (typeof Storage !== "undefined") {
            localStorage.clear();
            sessionStorage.clear();
        }
        
        // Force page refresh if we detect cached data issues
        const currentUrl = window.location.href;
        if (!currentUrl.includes('cb=') && performance.navigation.type !== 1) {
            // Add cache-busting parameter and reload
            const separator = currentUrl.includes('?') ? '&' : '?';
            window.location.href = currentUrl + separator + 'cb=' + Date.now();
        }
    } catch (e) {
        console.log('Cache clearing error (non-critical):', e);
    }
});

// Add CSS for spinning animation
const spinCSS = `
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
`;
const style = document.createElement('style');
style.textContent = spinCSS;
document.head.appendChild(style);

// Function to scroll to KYC form for additional documents
function scrollToKYCForm() {
    const kycSection = document.querySelector('.affiliate-kyc-section');
    if (kycSection) {
        kycSection.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
        
        // Highlight the section briefly
        kycSection.style.transition = 'all 0.3s ease';
        kycSection.style.boxShadow = '0 0 20px rgba(255, 193, 7, 0.5)';
        kycSection.style.border = '2px solid #ffc107';
        
        setTimeout(() => {
            kycSection.style.boxShadow = '';
            kycSection.style.border = '';
        }, 2000);
    }
}

// Function to handle KYC resubmission
function resubmitKYCApplication() {
    if (!confirm('Are you sure you want to resubmit your KYC application? This will change your status to "Awaiting Approval" and notify the admin team.')) {
        return;
    }
    
    // Show loading state
    const submitBtn = document.querySelector('[onclick="resubmitKYCApplication()"]');
    if (submitBtn) {
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px; animation: spin 1s linear infinite;"><path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25"/><path d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z"/></svg>Resubmitting...';
        submitBtn.disabled = true;
    }
    
    // Make AJAX request to update status
    fetch(affiliate_ajax.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'affiliate_resubmit_kyc',
            nonce: affiliate_ajax.nonce,
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('KYC application resubmitted successfully! Your status has been updated to "Awaiting Approval".');
            location.reload(); // Refresh to show updated status
        } else {
            alert('Error resubmitting application: ' + (data.data || 'Unknown error'));
            // Restore button
            if (submitBtn) {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error occurred while resubmitting application.');
        // Restore button
        if (submitBtn) {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });
}
</script>