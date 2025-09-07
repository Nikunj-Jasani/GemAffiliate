<?php
// Start session if not already started
if (!session_id()) {
    session_start();
}

// Get current user data - use global $wpdb instead of instance
global $wpdb;

// Check comprehensive session data
$session_valid = isset($_SESSION['affiliate_user_id']) && 
                 isset($_SESSION['affiliate_logged_in']) && 
                 $_SESSION['affiliate_logged_in'] === true;

if (!$session_valid) {
    error_log('Affiliate Portal: Session validation failed. Session data: ' . print_r($_SESSION, true));
    echo '<div class="affiliate-alert affiliate-alert-danger">User session expired. Please <a href="' . get_permalink(get_page_by_title('Affiliate Login')) . '">log in again</a>.</div>';
    return;
}

// Get user from database
$user_id = $_SESSION['affiliate_user_id'];
$table_name = $wpdb->prefix . 'affiliate_users';
$current_user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $user_id));

if (!$current_user) {
    error_log('Affiliate Portal: User not found in database. User ID: ' . $user_id);
    echo '<div class="affiliate-alert affiliate-alert-danger">User not found. Please <a href="' . get_permalink(get_page_by_title('Affiliate Login')) . '">log in again</a>.</div>';
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
    case 'rejected':
    case 'denied':
        $status_class = 'affiliate-status-rejected';
        $status_text = 'Rejected';
        break;
    case 'suspended':
        $status_class = 'affiliate-status-suspended';
        $status_text = 'Suspended';
        break;
    case 'documents_required':
        $status_class = 'affiliate-status-documents-required';
        $status_text = 'Documents Required';
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
            $status_text = 'Documents Required';
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
                    <a href="#" class="affiliate-dropdown-item" onclick="openChangePasswordModal()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                            <path d="M9 12l2 2 4-4"/>
                        </svg>
                        Change Password
                    </a>
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
        <div class="affiliate-kyc-section" style="margin-top: 40px;">
            <div class="affiliate-summary-header">
                <h2>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    KYC Process
                </h2>
            </div>
            
            <div class="affiliate-kyc-content" style="background: rgba(255, 255, 255, 0.98); border-radius: 24px; padding: 40px; box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.2); position: relative; overflow: hidden;">
                <div style="display: flex; align-items: flex-start; gap: 30px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 300px;">
                        <h3 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.4rem; font-weight: 600;">Complete Your KYC Documentation</h3>
                        
                        <div style="background: #e8f5e8; padding: 20px; border-radius: 12px; border-left: 4px solid #28a745; margin-bottom: 20px;">
                            <h4 style="color: #155724; margin: 0 0 10px 0; font-size: 1.1rem;">📋 Required Steps:</h4>
                            <ol style="margin: 10px 0 0 20px; color: #155724; line-height: 1.6;">
                                <li><strong>Download</strong> the KYC Excel form using the button below</li>
                                <li><strong>Fill out</strong> all required fields in the Excel sheet completely</li>
                                <li><strong>Gather</strong> all supporting documents (ID copies, bank statements, etc.)</li>
                                <li><strong>Email</strong> the completed form and documents to: <strong>office@gemmagics.com</strong></li>
                            </ol>
                        </div>
                        
                        <div style="background: #fff3cd; padding: 20px; border-radius: 12px; border-left: 4px solid #ffc107; margin-bottom: 25px;">
                            <h4 style="color: #856404; margin: 0 0 10px 0; font-size: 1rem;">⚠️ Important Notes:</h4>
                            <ul style="margin: 10px 0 0 20px; color: #856404; line-height: 1.6;">
                                <li>Complete <strong>ALL</strong> sections of the KYC form</li>
                                <li>Ensure all supporting documents are <strong>clearly readable</strong></li>
                                <li>Submit within <strong>30 days</strong> of account approval</li>
                                <li>Incomplete submissions will be returned for completion</li>
                            </ul>
                        </div>
                        
                        <div style="background: #d1ecf1; padding: 20px; border-radius: 12px; border-left: 4px solid #17a2b8;">
                            <h4 style="color: #0c5460; margin: 0 0 10px 0; font-size: 1rem;">📧 Submission Email:</h4>
                            <p style="margin: 0; color: #0c5460; font-size: 1.1rem;">
                                Send completed KYC form and supporting documents to:<br>
                                <strong style="font-size: 1.2rem; color: #0056b3;">office@gemmagics.com</strong>
                            </p>
                        </div>
                    </div>
                    
                    <div style="flex: 0 0 auto; text-align: center;">
                        <div style="background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%); padding: 30px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);">
                            <div style="background: rgba(255, 255, 255, 0.2); border-radius: 50%; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="white">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14,2 14,8 20,8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                    <polyline points="10,9 9,9 8,9"/>
                                </svg>
                            </div>
                            <h4 style="color: white; margin: 0 0 15px 0; font-size: 1.2rem;">KYC Excel Form</h4>
                            <p style="color: rgba(255, 255, 255, 0.9); margin: 0 0 20px 0; font-size: 0.95rem;">Download the official KYC form and complete all required sections</p>
                            <a href="<?php echo plugin_dir_url(__FILE__) . '../assets/KYC_Form.xlsx'; ?>" 
                               download="KYC_Form.xlsx"
                               style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: white; color: #0d6efd; text-decoration: none; border-radius: 25px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);"
                               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(0, 0, 0, 0.3)';"
                               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 15px rgba(0, 0, 0, 0.2)';">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7,10 12,15 17,10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                Download KYC Form
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                    <div class="affiliate-detail-label">Chat ID/Channel</div>
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

<!-- Change Password Modal -->
<div id="changePasswordModal" class="affiliate-modal">
    <div class="affiliate-modal-content" style="max-width: 500px;">
        <div class="affiliate-modal-header">
            <h2>Change Password</h2>
            <span class="affiliate-close" onclick="closeChangePasswordModal()">&times;</span>
        </div>
        <div class="affiliate-modal-body">
            <form id="changePasswordForm">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Current Password</label>
                    <input type="password" id="currentPassword" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;" required>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">New Password</label>
                    <input type="password" id="newPassword" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;" required>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Confirm New Password</label>
                    <input type="password" id="confirmPassword" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;" required>
                </div>
                <button type="submit" class="affiliate-btn affiliate-btn-primary" style="width: 100%;">
                    Update Password
                </button>
            </form>
        </div>
    </div>
</div>

<script>
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

function openChangePasswordModal() {
    document.getElementById('changePasswordModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
    toggleProfileDropdown();
}

function closeChangePasswordModal() {
    document.getElementById('changePasswordModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    document.getElementById('changePasswordForm').reset();
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
            if (data.success) {
                window.location.href = data.data.redirect;
            } else {
                // Fallback logout
                window.location.href = '<?php echo esc_url(get_permalink(get_page_by_title('Affiliate Login'))); ?>';
            }
        })
        .catch(error => {
            // Fallback logout
            window.location.href = '<?php echo esc_url(get_permalink(get_page_by_title('Affiliate Login'))); ?>';
        });
    }
    toggleProfileDropdown();
}

// Handle change password form submission
document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (newPassword !== confirmPassword) {
        alert('New passwords do not match');
        return;
    }
    
    if (newPassword.length < 8) {
        alert('New password must be at least 8 characters long');
        return;
    }
    
    // Show loading state
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Updating...';
    submitBtn.disabled = true;
    
    const formData = new FormData();
    formData.append('action', 'affiliate_change_password');
    formData.append('current_password', currentPassword);
    formData.append('new_password', newPassword);
    formData.append('nonce', affiliate_ajax.nonce);
    
    fetch(affiliate_ajax.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Password updated successfully');
            closeChangePasswordModal();
        } else {
            alert(data.data || 'Failed to update password');
        }
    })
    .catch(error => {
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
});

// Close modals when clicking outside
window.onclick = function(event) {
    const detailsModal = document.getElementById('detailsModal');
    const changePasswordModal = document.getElementById('changePasswordModal');
    
    if (event.target === detailsModal) {
        closeDetailsModal();
    }
    if (event.target === changePasswordModal) {
        closeChangePasswordModal();
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
        closeChangePasswordModal();
    }
});
</script>