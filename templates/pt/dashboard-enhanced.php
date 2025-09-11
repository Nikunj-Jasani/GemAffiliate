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
    echo '<div class="affiliate-alert affiliate-alert-danger">Erro do sistema. Por favor <a href="' . get_permalink(get_page_by_path('afiliado-login')) . '">faça login novamente</a>.</div>';
    return;
}

// Database session authentication - reliable and secure
$auth_data = $affiliate_portal_instance->is_user_authenticated();

if (!$auth_data) {
    // Clear session cookies
    if (!headers_sent()) {
        $is_secure = is_ssl() || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        setcookie('affiliate_session', '', time() - 3600, '/', '', $is_secure, true);
        setcookie('affiliate_user_id', '', time() - 3600, '/', '', false, false);
        setcookie('affiliate_username', '', time() - 3600, '/', '', false, false);
    }
    
    echo '<div class="affiliate-alert affiliate-alert-danger">Falha na autenticação. Por favor <a href="' . get_permalink(get_page_by_path('afiliado-login')) . '">faça login novamente</a>.</div>';
    return;
}

// Extract authenticated user data from database session
$user_id = $auth_data['user_id'] ?? null;
$username = $auth_data['username'] ?? null;

if (!$user_id || !$username) {
    error_log('CRITICAL PT: Invalid session data - missing user info');
    echo '<div class="affiliate-alert affiliate-alert-danger">Dados de sessão inválidos. Por favor <a href="' . get_permalink(get_page_by_path('afiliado-login')) . '">faça login novamente</a>.</div>';
    return;
}


// Enhanced logging for debugging authentication issues
error_log('Dashboard PT auth validation - User ID: "' . $user_id . '" (type: ' . gettype($user_id) . '), Username: "' . $username . '" (type: ' . gettype($username) . ')');

// REMOVED: Test user validation - was causing false positives
// Only validate that we have proper numeric user ID and valid username

// CRITICAL: Final validation of session user ID
if (!is_numeric($user_id) || intval($user_id) <= 0) {
    error_log('CRITICAL PT: Invalid user ID from session! User ID: "' . $user_id . '" (type: ' . gettype($user_id) . '), Username: "' . $username . '"');
    echo '<div class="affiliate-alert affiliate-alert-danger">Sessão autenticada inválida. Por favor <a href="' . get_permalink(get_page_by_path('afiliado-login')) . '">faça login novamente</a>.</div>';
    return;
}

// CRITICAL: Final user ID conversion and validation  
$user_id = intval($user_id);
$table_name = $wpdb->prefix . 'affiliate_users';

// CRITICAL FIX: Complete cache clearing to prevent user data contamination
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
}

if (function_exists('wp_cache_delete')) {
    for ($i = 1; $i <= 100; $i++) {
        wp_cache_delete($i, 'users');
        wp_cache_delete($i, 'affiliate_users');
        wp_cache_delete('user_' . $i, 'default');
        wp_cache_delete('affiliate_user_' . $i, 'default');
    }
}

// Clear WordPress transients
delete_transient('affiliate_user_data');
delete_transient('affiliate_current_user');
for ($i = 1; $i <= 100; $i++) {
    delete_transient('affiliate_user_' . $i);
}

// Disable MySQL query cache for fresh data
$wpdb->query("SET SESSION query_cache_type = OFF");

// Force browser cache refresh with no-cache headers
if (!headers_sent()) {
    header('Cache-Control: no-cache, no-store, must-revalidate, private');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('ETag: "' . md5($user_id . time()) . '"');
}

// ENHANCED DEBUG: Log cookie auth state and user ID
error_log('Portuguese Dashboard: Current User ID from cookie: ' . $user_id);
error_log('Portuguese Dashboard: Username from cookie: ' . ($_COOKIE['affiliate_username'] ?? 'NOT_SET'));
error_log('Portuguese Dashboard: Auth token present: ' . ($auth_token ? 'YES' : 'NO'));

// Get fresh user data with session validation (match both ID and username)
$wpdb->flush();
$current_user = $wpdb->get_row($wpdb->prepare("
    SELECT SQL_NO_CACHE * FROM $table_name WHERE id = %d AND username = %s AND id > 0 LIMIT 1
", $user_id, $username), OBJECT);

// Verify correct user data returned
if ($current_user && intval($current_user->id) !== $user_id) {
    if (!headers_sent()) {
        $is_secure = is_ssl() || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        // REMOVED: JWT auth token clearing
        setcookie('affiliate_user_id', '', time() - 3600, '/', '', false, false);
        setcookie('affiliate_username', '', time() - 3600, '/', '', false, false);
    }
    echo '<div class="affiliate-alert affiliate-alert-danger">Erro de integridade de dados. Por favor <a href="' . get_permalink(get_page_by_path('afiliado-login')) . '">faça login novamente</a>.</div>';
    return;
}

// Verify user data integrity
if ($current_user && $cookie_username && $cookie_username !== $current_user->username) {
    echo '<div class="affiliate-alert affiliate-alert-danger">Incompatibilidade de sessão detectada. Por favor <a href="' . get_permalink(get_page_by_path('afiliado-login')) . '">faça login novamente</a>.</div>';
    return;
}

// CRITICAL: Double-check that we got the correct user data
if ($current_user && intval($current_user->id) !== $user_id) {
    error_log('CRITICAL PT: Database returned wrong user! Expected: ' . $user_id . ', Got: ' . $current_user->id);
    echo '<div class="affiliate-alert affiliate-alert-danger">Erro de integridade de dados. Por favor <a href="' . get_permalink(get_page_by_path('afiliado-login')) . '">faça login novamente</a>.</div>';
    return;
}

// Log user data fetch for debugging with more detail
if ($current_user) {
    error_log('Portuguese Dashboard: Fetched user - ID: ' . $user_id . ', Username: ' . $current_user->username . ', Email: ' . $current_user->email . ', Status: ' . $current_user->status);
} else {
    error_log('Portuguese Dashboard: NO USER FOUND for ID: ' . $user_id);
}

if (!$current_user) {
    error_log('Affiliate Portal: User not found in database. User ID: ' . $user_id);
    echo '<div class="affiliate-alert affiliate-alert-danger">Usuário não encontrado. Por favor <a href="' . get_permalink(get_page_by_path('afiliado-login')) . '">faça login novamente</a>.</div>';
    return;
}

// Get actual status and remarks from database
$actual_status = isset($current_user->status) ? $current_user->status : 'pending';
$admin_remarks = isset($current_user->admin_remarks) ? $current_user->admin_remarks : '';
$status_class = 'affiliate-status-pending-review';
$status_text = 'Aguardando Análise';

// Map database status to display values
switch (strtolower($actual_status)) {
    case 'approved':
    case 'active':
        $status_class = 'affiliate-status-approved';
        $status_text = 'Aprovado';
        break;
    case 'pending':
        $status_class = 'affiliate-status-pending';
        $status_text = 'Aguardando Análise';
        break;
    case 'kyc pending':
        $status_class = 'affiliate-status-pending';
        $status_text = 'KYC Pendente';
        break;
    case 'rejected':
    case 'denied':
        $status_class = 'affiliate-status-rejected';
        $status_text = 'Rejeitado';
        break;
    case 'suspended':
        $status_class = 'affiliate-status-suspended';
        $status_text = 'Suspenso';
        break;
    case 'additional document required':
        $status_class = 'affiliate-status-documents-required';
        $status_text = 'Info. Adicional Necessária';
        break;
    case 'awaiting approval':
        $status_class = 'affiliate-status-awaiting-approval';
        $status_text = 'Aguardando Aprovação';
        break;
    default:
        // Fallback based on account type if status is not set
        if ($current_user->type === 'Individual') {
            $status_class = 'affiliate-status-pending';
            $status_text = 'Aguardando Análise';
        } elseif ($current_user->type === 'Company') {
            $status_class = 'affiliate-status-documents-required';
            $status_text = 'Info. Adicional Necessária';
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
                        Sair
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="affiliate-dashboard-content">
        <!-- Welcome Section -->
        <div class="affiliate-welcome-section">
            <h1>Bem-vindo, <?php echo esc_html($current_user->first_name . ' ' . $current_user->last_name); ?>!</h1>
            <p class="affiliate-welcome-subtitle">Resumo do seu registro de afiliado</p>
        </div>

        <!-- Registration Summary -->
        <div class="affiliate-registration-summary">
            <div class="affiliate-summary-header">
                <h2>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14,2 14,8 20,8"/>
                    </svg>
                    Resumo do Registro
                </h2>
            </div>

            <div class="affiliate-table-container">
                <table class="affiliate-summary-table">
                <thead>
                    <tr>
                        <th>Nome de Usuário</th>
                        <th>Nome Completo</th>
                        <th>Email</th>
                        <th>Tipo de Conta</th>
                        <th>Celular</th>
                        <th>Data de Registro</th>
                        <th>Status</th>
                        <th>Ações</th>
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
                                    Nota do Administrador: <?php echo esc_html(wp_trim_words($admin_remarks, 8, '...')); ?>
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
                                    Ver Detalhes
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
            include AFFILIATE_PORTAL_PATH . 'templates/pt/kyc-form.php';
        } else {
            // Show status message for completed/submitted KYC
            ?>
            <div class="affiliate-kyc-section" style="margin-top: 40px;">
                <div class="affiliate-summary-header">
                    <h2>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Status de Verificação KYC
                    </h2>
                </div>
                
                <div class="kyc-status-container" style="background: rgba(255, 255, 255, 0.98); border-radius: 24px; padding: 40px; box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1); margin-top: 20px;">
                    <?php if ($user_status === 'awaiting approval'): ?>
                        <div style="text-align: center; color: #17a2b8;">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="currentColor" style="margin-bottom: 20px;">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            <h3 style="color: #17a2b8; margin-bottom: 15px;">Aplicação KYC em Análise</h3>
                            <p style="color: #6c757d; font-size: 1.1rem; line-height: 1.6;">
                                Sua aplicação KYC foi enviada com sucesso e está atualmente em análise. 
                                Nossa equipe de conformidade processará sua aplicação em 3-5 dias úteis.
                            </p>
                            <p style="color: #6c757d; margin-top: 20px;">
                                Você receberá uma notificação por email assim que a análise for concluída.
                            </p>
                        </div>
                    <?php elseif ($user_status === 'approved'): ?>
                        <div style="text-align: center; color: #28a745;">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="currentColor" style="margin-bottom: 20px;">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 style="color: #28a745; margin-bottom: 15px;">KYC Verificado ✓</h3>
                            <p style="color: #6c757d; font-size: 1.1rem; line-height: 1.6;">
                                Parabéns! Sua verificação KYC foi concluída e aprovada. 
                                Sua conta agora está totalmente verificada e você pode acessar todos os recursos de afiliado.
                            </p>
                        </div>
                    <?php elseif ($user_status === 'rejected'): ?>
                        <div style="text-align: center; color: #dc3545;">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="currentColor" style="margin-bottom: 20px;">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                            </svg>
                            <h3 style="color: #dc3545; margin-bottom: 15px;">Aplicação KYC Rejeitada</h3>
                            <p style="color: #6c757d; font-size: 1.1rem; line-height: 1.6;">
                                Sua aplicação KYC foi rejeitada. Por favor, revise as notas do administrador abaixo e reenvie com informações atualizadas.
                            </p>
                            <?php if (!empty($admin_remarks)): ?>
                                <div style="background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; padding: 15px; margin-top: 20px; text-align: left;">
                                    <strong style="color: #721c24;">Notas do Administrador:</strong><br>
                                    <span style="color: #721c24;"><?php echo esc_html($admin_remarks); ?></span>
                                </div>
                            <?php endif; ?>
                            <div style="margin-top: 20px;">
                                <button class="affiliate-btn affiliate-btn-primary" onclick="resubmitKYCApplication(this);" style="padding: 12px 24px; font-size: 1rem;">
                                    Reenviar Aplicação KYC
                                </button>
                            </div>
                        </div>
                    <?php elseif ($user_status === 'additional document required'): ?>
                        <div style="text-align: center; color: #ffc107;">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="currentColor" style="margin-bottom: 20px;">
                                <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                            </svg>
                            <h3 style="color: #ffc107; margin-bottom: 15px;">Informação Adicional Necessária</h3>
                            <p style="color: #6c757d; font-size: 1.1rem; line-height: 1.6;">
                                Sua aplicação KYC requer documentos ou informações adicionais. Por favor, revise os requisitos do administrador abaixo e envie os documentos necessários.
                            </p>
                            <?php if (!empty($admin_remarks)): ?>
                                <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 15px; margin-top: 20px; text-align: left;">
                                    <strong style="color: #856404;">Requisitos do Administrador:</strong><br>
                                    <span style="color: #856404;"><?php echo nl2br(esc_html($admin_remarks)); ?></span>
                                </div>
                            <?php endif; ?>
                            <div style="margin-top: 20px;">
                                <button class="affiliate-btn affiliate-btn-primary" onclick="scrollToKYCForm(); resubmitKYCApplication(this);" style="padding: 12px 24px; font-size: 1rem;">
                                    Enviar Documentos Adicionais
                                </button>
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
            <h2>Detalhes Completos do Registro</h2>
            <span class="affiliate-close" onclick="closeDetailsModal()">&times;</span>
        </div>
        <div class="affiliate-modal-body">
            <div class="affiliate-detail-grid">
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Nome de Usuário</div>
                    <div class="affiliate-detail-value"><?php echo esc_html($current_user->username); ?></div>
                </div>
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Nome Completo</div>
                    <div class="affiliate-detail-value"><?php echo esc_html(($current_user->name_prefix ? $current_user->name_prefix . ' ' : '') . $current_user->first_name . ' ' . $current_user->last_name); ?></div>
                </div>
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Endereço de Email</div>
                    <div class="affiliate-detail-value"><?php echo esc_html($current_user->email); ?></div>
                </div>
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Tipo de Conta</div>
                    <div class="affiliate-detail-value"><?php echo esc_html($current_user->type); ?></div>
                </div>
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Data de Nascimento</div>
                    <div class="affiliate-detail-value"><?php echo esc_html($current_user->dob ?: 'Não informado'); ?></div>
                </div>
                <?php if ($current_user->company_name): ?>
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Nome da Empresa</div>
                    <div class="affiliate-detail-value"><?php echo esc_html($current_user->company_name); ?></div>
                </div>
                <?php endif; ?>
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Número de Celular</div>
                    <div class="affiliate-detail-value"><?php 
                        $mobile_display = $current_user->mobile_number;
                        if ($current_user->country_code && !str_starts_with($mobile_display, '+')) {
                            $mobile_display = '+' . $current_user->country_code . ' ' . $mobile_display;
                        }
                        echo esc_html($mobile_display); 
                    ?></div>
                </div>
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Endereço</div>
                    <div class="affiliate-detail-value"><?php echo esc_html($current_user->address_line1 . ($current_user->address_line2 ? ', ' . $current_user->address_line2 : '') . ', ' . $current_user->city . ', ' . $current_user->state . ', ' . $current_user->country . ' ' . $current_user->zipcode); ?></div>
                </div>
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Tipo de Afiliado</div>
                    <div class="affiliate-detail-value"><?php echo esc_html($current_user->affiliate_type); ?></div>
                </div>
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Moeda Preferida</div>
                    <div class="affiliate-detail-value"><?php echo esc_html($current_user->currency); ?></div>
                </div>
                <?php if ($current_user->chat_id_channel): ?>
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Telegram/Teams</div>
                    <div class="affiliate-detail-value"><?php echo esc_html($current_user->chat_id_channel); ?></div>
                </div>
                <?php endif; ?>
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Data de Registro</div>
                    <div class="affiliate-detail-value"><?php echo esc_html(date('F j, Y \a\t g:i A', strtotime($current_user->created_at))); ?></div>
                </div>
                <div class="affiliate-detail-item">
                    <div class="affiliate-detail-label">Status</div>
                    <div class="affiliate-detail-value"><span class="affiliate-status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span></div>
                </div>
                <?php if (!empty($admin_remarks) && strtolower($actual_status) !== 'awaiting approval'): ?>
                <div class="affiliate-detail-item" style="grid-column: 1 / -1; border-left: 4px solid #17a2b8; background: #e7f3ff;">
                    <div class="affiliate-detail-label" style="color: #0c5460; font-weight: 700;">Observações do Administrador</div>
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
    if (confirm('Tem certeza de que deseja sair?')) {
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
                // Fallback logout - try multiple methods to find Portuguese login page
                <?php 
                $fallback_url = '';
                $login_page = get_page_by_title('Login do Afiliado');
                if ($login_page) {
                    $fallback_url = get_permalink($login_page);
                } else {
                    $login_page_by_slug = get_page_by_path('afiliado-login');
                    if ($login_page_by_slug) {
                        $fallback_url = get_permalink($login_page_by_slug);
                    } else {
                        $fallback_url = home_url('/afiliado-login/');
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
            $login_page = get_page_by_title('Login do Afiliado');
            if ($login_page) {
                $fallback_url = get_permalink($login_page);
            } else {
                $login_page_by_slug = get_page_by_path('afiliado-login');
                if ($login_page_by_slug) {
                    $fallback_url = get_permalink($login_page_by_slug);
                } else {
                    $fallback_url = home_url('/afiliado-login/');
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
    }
}

// Function to resubmit KYC application
function resubmitKYCApplication(button) {
    // Store original button content
    const originalText = button.innerHTML;
    const originalColor = button.style.backgroundColor;
    
    // Show spinning animation
    button.innerHTML = '<div style="display: inline-block; width: 16px; height: 16px; border: 2px solid #ffffff; border-top: 2px solid transparent; border-radius: 50%; animation: spin 1s linear infinite; margin-right: 8px;"></div>Reenviando...';
    button.disabled = true;
    button.style.backgroundColor = '#6c757d';
    button.style.cursor = 'not-allowed';
    
    // Prepare AJAX request data
    const formData = new FormData();
    formData.append('action', 'affiliate_resubmit_kyc');
    formData.append('nonce', '<?php echo wp_create_nonce('affiliate_nonce'); ?>');
    
    // Send AJAX request
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            button.innerHTML = '✓ Reenviado com Sucesso!';
            button.style.backgroundColor = '#28a745';
            button.style.cursor = 'pointer';
            
            // Show success alert
            alert(data.data || 'Aplicação KYC reenviada com sucesso! Status alterado para "Aguardando Aprovação".');
            
            // Reload page after short delay to show updated status
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            // Show error message
            button.innerHTML = '✗ Erro no Reenvio';
            button.style.backgroundColor = '#dc3545';
            button.style.cursor = 'pointer';
            
            // Show error alert
            alert(data.data || 'Falha ao reenviar aplicação KYC. Tente novamente.');
            
            // Reset button after delay
            setTimeout(() => {
                button.innerHTML = originalText;
                button.style.backgroundColor = originalColor;
                button.disabled = false;
                button.style.cursor = 'pointer';
            }, 3000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Show error message
        button.innerHTML = '✗ Erro de Conexão';
        button.style.backgroundColor = '#dc3545';
        button.style.cursor = 'pointer';
        
        // Show error alert
        alert('Erro de conexão. Por favor, verifique sua conexão com a internet e tente novamente.');
        
        // Reset button after delay
        setTimeout(() => {
            button.innerHTML = originalText;
            button.style.backgroundColor = originalColor;
            button.disabled = false;
            button.style.cursor = 'pointer';
        }, 3000);
    });
}
</script>