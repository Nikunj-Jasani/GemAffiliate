<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// REMOVED: All session-based authentication - using cookie-only authentication
// Admin authentication is now handled by the shortcode function

global $wpdb;

// Get admin data from cookies
$admin_id = $_COOKIE['affiliate_admin_id'] ?? null;
$admin_username = $_COOKIE['affiliate_admin_username'] ?? null;

// Validate admin from cookies
if (!$admin_id) {
    echo '<div class="affiliate-admin-login-required">
        <div class="login-required-container">
            <h2>Access Restricted</h2>
            <p>Please log in to access the admin dashboard.</p>
            <a href="/admin-login/" class="affiliate-btn affiliate-btn-primary">Login</a>
        </div>
    </div>';
    return;
}

// Get admin data from database to ensure it's current
$admin_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}affiliate_admin WHERE id = %d AND status = 'active'", $admin_id));

if (!$admin_data) {
    error_log('Affiliate Portal: Admin not found in database or inactive. Admin ID: ' . $admin_id);
    echo '<div class="affiliate-admin-login-required">
        <div class="login-required-container">
            <h2>Access Restricted</h2>
            <p>Admin account not found or inactive. Please log in again.</p>
            <a href="/admin-login/" class="affiliate-btn affiliate-btn-primary">Login</a>
        </div>
    </div>';
    return;
}
?>

<div class="affiliate-admin-dashboard">
    <div class="admin-header">
        <div class="admin-header-left">
            <h1 class="admin-title">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
                </svg>
                Master Admin Dashboard
            </h1>
            <p class="admin-subtitle">Welcome, <?php echo esc_html($admin_username ?? $admin_data->full_name); ?></p>
        </div>
        <div class="admin-header-right">
            <button type="button" class="affiliate-btn affiliate-btn-outline" onclick="showEmailConfig()" style="padding: 12px 24px !important; height: 48px !important; min-height: 48px !important; max-height: 48px !important; width: auto !important; min-width: auto !important; max-width: none !important; font-size: 1rem !important; box-sizing: border-box !important; white-space: nowrap !important;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z"/>
                </svg>
                Email Settings
            </button>
            <button type="button" class="affiliate-btn affiliate-btn-danger" onclick="adminLogout()" style="padding: 12px 24px !important; height: 48px !important; min-height: 48px !important; max-height: 48px !important; width: auto !important; min-width: auto !important; max-width: none !important; font-size: 1rem !important; box-sizing: border-box !important; white-space: nowrap !important;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0 0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0 0,1 14,22H5A2,2 0 0,1 3,20V4A2,2 0 0,1 5,2H14Z"/>
                </svg>
                Logout
            </button>
        </div>
    </div>

    <div class="admin-content">
        <!-- Statistics Cards -->
        <div class="admin-stats">
            <div class="stat-card pending">
                <div class="stat-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,17A1.5,1.5 0 0,1 10.5,15.5A1.5,1.5 0 0,1 12,14A1.5,1.5 0 0,1 13.5,15.5A1.5,1.5 0 0,1 12,17M12,10.5C10.07,10.5 8.5,8.93 8.5,7C8.5,5.07 10.07,3.5 12,3.5C13.93,3.5 15.5,5.07 15.5,7C15.5,8.93 13.93,10.5 12,10.5Z"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-number" id="pending-count">-</div>
                    <div class="stat-label">Pending Applications</div>
                </div>
            </div>
            
            <div class="stat-card approved">
                <div class="stat-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M11,16.5L18,9.5L16.59,8.09L11,13.67L7.41,10.09L6,11.5L11,16.5Z"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-number" id="approved-count">-</div>
                    <div class="stat-label">Approved</div>
                </div>
            </div>
            
            <div class="stat-card rejected">
                <div class="stat-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M14.5,9L13.09,7.59L12,8.67L10.91,7.59L9.5,9L10.59,10.09L9.5,11.17L10.91,12.59L12,11.5L13.09,12.59L14.5,11.17L13.41,10.09L14.5,9Z"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-number" id="rejected-count">-</div>
                    <div class="stat-label">Rejected</div>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="admin-tabs">
            <button class="tab-btn active" onclick="loadApplications()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14,2 14,8 20,8"/>
                </svg>
                Applications & KYC Management
            </button>
        </div>

        <!-- Applications Table -->
        <div class="admin-table-container tab-content" id="applications-tab">
            <div class="admin-table-header">
                <h3>Affiliate Applications</h3>
                <div class="admin-filters">
                    <select id="status-filter" class="affiliate-form-control" onchange="loadApplications()">
                        <option value="">All Statuses</option>
                        <option value="awaiting approval">Awaiting Approval</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                    <select id="per-page-filter" class="affiliate-form-control" onchange="loadApplications()">
                        <option value="10">10 per page</option>
                        <option value="25" selected>25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                    <button type="button" class="affiliate-btn affiliate-btn-primary" onclick="loadApplications()" style="padding: 12px 24px !important; height: 48px !important; min-height: 48px !important; max-height: 48px !important; width: auto !important; min-width: auto !important; max-width: none !important; font-size: 1rem !important; box-sizing: border-box !important; white-space: nowrap !important;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.65,6.35C16.2,4.9 14.21,4 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20C15.73,20 18.84,17.45 19.73,14H17.65C16.83,16.33 14.61,18 12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6C13.66,6 15.14,6.69 16.22,7.78L13,11H20V4L17.65,6.35Z"/>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
            
            <div class="admin-table-responsive">
                <table class="admin-table" id="applications-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Company</th>
                            <th>Type</th>
                            <th>Country</th>
                            <th>Status</th>
                            <th>KYC Status</th>
                            <th>Applied Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="applications-tbody">
                        <tr>
                            <td colspan="8" class="text-center">Loading applications...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Controls -->
            <div class="admin-pagination" id="pagination-container" style="display: none;">
                <div class="pagination-info">
                    <span id="pagination-info-text">Showing 1 to 25 of 100 entries</span>
                </div>
                <div class="pagination-controls">
                    <button class="pagination-btn" id="first-page" onclick="goToPage(1)" disabled>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18.41,16.59L13.82,12L18.41,7.41L17,6L11,12L17,18L18.41,16.59M6,6H8V18H6V6Z"/>
                        </svg>
                        First
                    </button>
                    <button class="pagination-btn" id="prev-page" onclick="goToPreviousPage()" disabled>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M15.41,16.59L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.59Z"/>
                        </svg>
                        Previous
                    </button>
                    <div class="pagination-numbers" id="pagination-numbers">
                        <!-- Page numbers will be inserted here -->
                    </div>
                    <button class="pagination-btn" id="next-page" onclick="goToNextPage()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M8.59,16.59L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.59Z"/>
                        </svg>
                        Next
                    </button>
                    <button class="pagination-btn" id="last-page" onclick="goToLastPage()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M5.59,7.41L10.18,12L5.59,16.59L7,18L13,12L7,6L5.59,7.41M16,6H18V18H16V6Z"/>
                        </svg>
                        Last
                    </button>
                </div>
            </div>
        </div>

        <!-- KYC Review Table -->
        <!-- KYC tab removed and integrated into main applications tab -->
        <div class="admin-table-container tab-content" id="kyc-tab" style="display: none;">
            <div class="admin-table-header">
                <h3>KYC Applications Review</h3>
                <div class="admin-filters">
                    <select id="kyc-status-filter" class="affiliate-form-control" onchange="loadKycApplications()">
                        <option value="">All KYC Statuses</option>
                        <option value="pending">Pending Review</option>
                        <option value="submitted">Submitted</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="draft">Draft</option>
                    </select>
                    <select id="kyc-per-page-filter" class="affiliate-form-control" onchange="loadKycApplications()">
                        <option value="10">10 per page</option>
                        <option value="25" selected>25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                    <button type="button" class="affiliate-btn affiliate-btn-primary" onclick="loadKycApplications()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.65,6.35C16.2,4.9 14.21,4 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20C15.73,20 18.84,17.45 19.73,14H17.65C16.83,16.33 14.61,18 12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6C13.66,6 15.14,6.69 16.22,7.78L13,11H20V4L17.65,6.35Z"/>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
            
            <div class="admin-table-responsive">
                <table class="admin-table" id="kyc-applications-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Nationality</th>
                            <th>Affiliate Type</th>
                            <th>KYC Status</th>
                            <th>Submitted Date</th>
                            <th>Documents</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="kyc-applications-tbody">
                        <tr>
                            <td colspan="9" class="text-center">Loading KYC applications...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- KYC Pagination Controls -->
            <div class="admin-pagination" id="kyc-pagination-container" style="display: none;">
                <div class="pagination-info">
                    <span id="kyc-pagination-info-text">Showing 1 to 25 of 100 entries</span>
                </div>
                <div class="pagination-controls">
                    <button class="pagination-btn" id="kyc-first-page" onclick="goToKycPage(1)" disabled>First</button>
                    <button class="pagination-btn" id="kyc-prev-page" onclick="goToPreviousKycPage()" disabled>Previous</button>
                    <div class="pagination-numbers" id="kyc-pagination-numbers"></div>
                    <button class="pagination-btn" id="kyc-next-page" onclick="goToNextKycPage()">Next</button>
                    <button class="pagination-btn" id="kyc-last-page" onclick="goToLastKycPage()">Last</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Email Configuration Modal -->
<div id="emailConfigModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Email Configuration</h3>
            <button type="button" class="modal-close" onclick="hideEmailConfig()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="emailConfigForm">
                <div class="affiliate-form-group">
                    <label for="notification_emails" class="affiliate-form-label">Notification Email Addresses *</label>
                    <textarea id="notification_emails" name="notification_emails" class="affiliate-form-control" rows="3" placeholder="admin@example.com, manager@example.com" required></textarea>
                    <small class="form-text">Enter comma-separated email addresses to receive new registration notifications</small>
                </div>
                
                <div class="affiliate-form-group">
                    <label for="from_email" class="affiliate-form-label">From Email Address *</label>
                    <input type="email" id="from_email" name="from_email" class="affiliate-form-control" placeholder="noreply@yoursite.com" required>
                </div>
                
                <div class="affiliate-form-group">
                    <label for="from_name" class="affiliate-form-label">From Name *</label>
                    <input type="text" id="from_name" name="from_name" class="affiliate-form-control" placeholder="Your Company Affiliate Portal" required>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="affiliate-btn affiliate-btn-secondary" onclick="hideEmailConfig()" style="padding: 12px 24px !important; height: 48px !important; min-height: 48px !important; max-height: 48px !important; width: auto !important; min-width: auto !important; max-width: none !important; font-size: 1rem !important; box-sizing: border-box !important; white-space: nowrap !important;">Cancel</button>
                    <button type="submit" class="affiliate-btn affiliate-btn-primary" style="padding: 12px 24px !important; height: 48px !important; min-height: 48px !important; max-height: 48px !important; width: auto !important; min-width: auto !important; max-width: none !important; font-size: 1rem !important; box-sizing: border-box !important; white-space: nowrap !important;">Save Configuration</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- KYC Verification Modal -->
<div id="kycVerificationModal" class="modal kyc-modal" style="display: none;">
    <div class="modal-content kyc-modal-content">
        <div class="modal-header">
            <h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                KYC Verification & Document Review
            </h3>
            <button type="button" class="modal-close" onclick="hideKycModal()">&times;</button>
        </div>
        <div class="modal-body kyc-modal-body">
            <div id="kycVerificationContent">Loading KYC details...</div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div id="statusUpdateModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Update Application Status</h3>
            <button type="button" class="modal-close" onclick="hideStatusModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="statusUpdateForm">
                <input type="hidden" id="update_application_id" name="application_id">
                
                <div class="affiliate-form-group">
                    <label for="new_status" class="affiliate-form-label">New Status</label>
                    <select id="new_status" name="status" class="affiliate-form-control" required>
                        <option value="">Select Status</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="awaiting approval">Awaiting Approval</option>
                    </select>
                </div>
                
                <div class="affiliate-form-group">
                    <label for="admin_remarks" class="affiliate-form-label">Admin Remarks</label>
                    <textarea id="admin_remarks" name="remarks" class="affiliate-form-control" rows="4" placeholder="Enter remarks for the applicant..."></textarea>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="affiliate-btn affiliate-btn-secondary" onclick="hideStatusModal()" style="padding: 12px 24px !important; height: 48px !important; min-height: 48px !important; max-height: 48px !important; width: auto !important; min-width: auto !important; max-width: none !important; font-size: 1rem !important; box-sizing: border-box !important; white-space: nowrap !important;">Cancel</button>
                    <button type="submit" class="affiliate-btn affiliate-btn-primary" style="padding: 12px 24px !important; height: 48px !important; min-height: 48px !important; max-height: 48px !important; width: auto !important; min-width: auto !important; max-width: none !important; font-size: 1rem !important; box-sizing: border-box !important; white-space: nowrap !important;">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- KYC Review Modal -->
<div id="kycReviewModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 1000px; width: 95%;">
        <div class="modal-header">
            <h3>KYC Application Review</h3>
            <button type="button" class="modal-close" onclick="hideKycReviewModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div id="kycReviewContent">
                <!-- KYC review content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- KYC Status Update Modal -->
<div id="kycStatusModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Update KYC Status</h3>
            <button type="button" class="modal-close" onclick="hideKycStatusModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="kycStatusUpdateForm">
                <input type="hidden" id="kyc_user_id" name="user_id">
                
                <div class="affiliate-form-group">
                    <label for="kyc_new_status" class="affiliate-form-label">KYC Status</label>
                    <select id="kyc_new_status" name="kyc_status" class="affiliate-form-control" required>
                        <option value="">Select Status</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="pending">Pending Review</option>
                        <option value="incomplete">Incomplete - Needs More Information</option>
                    </select>
                </div>
                
                <div class="affiliate-form-group">
                    <label for="kyc_admin_comments" class="affiliate-form-label">Admin Comments</label>
                    <textarea id="kyc_admin_comments" name="admin_comments" class="affiliate-form-control" rows="4" placeholder="Enter comments for the user (required if rejecting or requesting more information)..."></textarea>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="affiliate-btn affiliate-btn-secondary" onclick="hideKycStatusModal()">Cancel</button>
                    <button type="submit" class="affiliate-btn affiliate-btn-primary">Update KYC Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Document Viewer Modal -->
<div id="documentViewerModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 900px; width: 95%; max-height: 90vh;">
        <div class="modal-header">
            <h3>Document Viewer</h3>
            <button type="button" class="modal-close" onclick="hideDocumentViewer()">&times;</button>
        </div>
        <div class="modal-body" style="padding: 0; overflow: hidden;">
            <div id="documentViewerContent" style="height: 70vh; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                <!-- Document content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<style>
.affiliate-admin-login-required {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 60vh;
    padding: 40px 20px;
}

.login-required-container {
    text-align: center;
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    max-width: 400px;
    width: 100%;
}

.login-required-container h2 {
    color: #dc3545;
    margin-bottom: 16px;
    font-size: 24px;
}

.login-required-container p {
    color: #6c757d;
    margin-bottom: 24px;
    line-height: 1.5;
}
.affiliate-admin-dashboard {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e1e5e9;
}

.admin-title {
    margin: 0;
    display: flex;
    align-items: center;
    color: #2c3e50;
    font-size: 28px;
    font-weight: 600;
}

.admin-subtitle {
    margin: 5px 0 0 32px;
    color: #6c757d;
    font-size: 14px;
}

.admin-header-right {
    display: flex;
    gap: 10px;
}

.admin-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: #fff;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    border: 1px solid #e1e5e9;
    display: flex;
    align-items: center;
    gap: 16px;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-card.pending .stat-icon {
    background: #fff3cd;
    color: #856404;
}

.stat-card.approved .stat-icon {
    background: #d1e7dd;
    color: #0f5132;
}

.stat-card.rejected .stat-icon {
    background: #f8d7da;
    color: #842029;
}

.stat-number {
    font-size: 32px;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 14px;
    color: #6c757d;
    font-weight: 500;
}

.admin-table-container {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    border: 1px solid #e1e5e9;
    overflow: hidden;
}

.admin-table-header {
    padding: 20px;
    border-bottom: 1px solid #e1e5e9;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.admin-table-header h3 {
    margin: 0;
    color: #2c3e50;
    font-size: 18px;
    font-weight: 600;
}

.admin-filters {
    display: flex;
    gap: 10px;
    align-items: center;
}

.admin-filters select {
    width: 200px;
}

.admin-table-responsive {
    overflow-x: auto;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table th,
.admin-table td {
    padding: 12px 16px;
    text-align: left;
    border-bottom: 1px solid #e1e5e9;
}

.admin-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #495057;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.admin-table tr:hover {
    background: #f8f9fa;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge.awaiting {
    background: #fff3cd;
    color: #856404;
}

.status-badge.approved {
    background: #d1e7dd;
    color: #0f5132;
}

.status-badge.rejected {
    background: #f8d7da;
    color: #842029;
}

.action-btn {
    padding: 6px 10px;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    cursor: pointer;
    margin-right: 5px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.action-btn.edit {
    background: #e3f2fd;
    color: #1565c0;
}

.action-btn.edit:hover {
    background: #bbdefb;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: #fff;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow: auto;
}

.modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e1e5e9;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    color: #2c3e50;
    font-size: 18px;
    font-weight: 600;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #6c757d;
    padding: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-body {
    padding: 24px;
}

.modal-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 20px;
}

.form-text {
    color: #6c757d;
    font-size: 12px;
    margin-top: 4px;
}

/* Tab Navigation Styles */
.admin-tabs {
    display: flex;
    gap: 4px;
    margin-bottom: 20px;
    border-bottom: 2px solid #e1e5e9;
}

.tab-btn {
    padding: 12px 20px;
    border: none;
    background: transparent;
    color: #6c757d;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    border-radius: 8px 8px 0 0;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.tab-btn:hover {
    background: #f8f9fa;
    color: #495057;
}

.tab-btn.active {
    background: #fff;
    color: #0d6efd;
    border-bottom-color: #0d6efd;
}

.tab-content {
    display: block;
}

.tab-content.hidden {
    display: none;
}

/* KYC specific styles */
.kyc-status-badge {
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.kyc-status-badge.pending {
    background: #fff3cd;
    color: #856404;
}

.kyc-status-badge.submitted {
    background: #cff4fc;
    color: #055160;
}

.kyc-status-badge.approved {
    background: #d1e7dd;
    color: #0f5132;
}

.kyc-status-badge.rejected {
    background: #f8d7da;
    color: #842029;
}

.kyc-status-badge.draft {
    background: #e2e3e5;
    color: #383d41;
}

/* Account Type Badge Styles */
.account-type-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.account-type-badge.individual {
    background: #d1ecf1;
    color: #0c5460;
}

.account-type-badge.sole_trader,
.account-type-badge.limited_company,
.account-type-badge.llp,
.account-type-badge.partnership {
    background: #f8d7da;
    color: #721c24;
}

.account-type-badge.other {
    background: #e2e3e5;
    color: #383d41;
}

.kyc-documents-list {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.document-link {
    padding: 4px 8px;
    background: #e3f2fd;
    color: #1565c0;
    text-decoration: none;
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
}

.document-link:hover {
    background: #bbdefb;
}

.kyc-review-section {
    margin-bottom: 24px;
    padding: 20px;
    border: 1px solid #e1e5e9;
    border-radius: 8px;
    background: #f8f9fa;
}

.kyc-review-section h4 {
    margin: 0 0 15px 0;
    color: #2c3e50;
    font-size: 16px;
    font-weight: 600;
}

.kyc-field-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
}

.kyc-field {
    display: flex;
    flex-direction: column;
}

.kyc-field-label {
    font-weight: 600;
    color: #495057;
    font-size: 13px;
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.kyc-field-value {
    color: #2c3e50;
    font-size: 14px;
    padding: 8px 0;
    border-bottom: 1px solid #dee2e6;
}

.version-history {
    margin-top: 15px;
    padding: 10px;
    background: #fff;
    border-radius: 6px;
    border: 1px solid #dee2e6;
}

.version-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 5px 0;
    border-bottom: 1px solid #f8f9fa;
}

.version-item:last-child {
    border-bottom: none;
}

@media (max-width: 768px) {
    .admin-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .admin-table-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .admin-filters {
        width: 100%;
        justify-content: flex-start;
    }
    
    .admin-filters select {
        flex: 1;
        max-width: 200px;
    }
}

/* Additional styles for registration details modal */
.action-btn.view {
    background: #e8f5e8;
    color: #2e7d32;
}

.action-btn.view:hover {
    background: #c8e6c9;
}

.registration-detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.registration-detail-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.registration-detail-label {
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.registration-detail-value {
    color: #666;
    font-size: 14px;
    line-height: 1.4;
}

.admin-modal {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(5px);
}

.admin-modal-content {
    background-color: white;
    margin: 2% auto;
    padding: 0;
    border-radius: 12px;
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
}

.admin-modal-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    padding: 20px 30px;
    border-radius: 12px 12px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.admin-modal-header h2 {
    margin: 0;
    font-size: 1.5rem;
}

.admin-modal-close {
    color: white;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.3s;
    padding: 0;
    background: none;
    border: none;
}

.admin-modal-close:hover {
    color: #f0f0f0;
}

.admin-modal-body {
    padding: 30px;
}

/* Pagination Styles */
.admin-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-top: 1px solid #e1e5e9;
    background: #f8f9fa;
}

.pagination-info {
    color: #6c757d;
    font-size: 14px;
}

.pagination-controls {
    display: flex;
    align-items: center;
    gap: 5px;
}

.pagination-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 8px 12px;
    border: 1px solid #dee2e6;
    background: white;
    color: #495057;
    text-decoration: none;
    border-radius: 6px;
    font-size: 13px;
    cursor: pointer;
    transition: background-color 0.2s ease, border-color 0.2s ease;
    white-space: nowrap;
    min-width: fit-content;
    box-sizing: border-box;
}

.pagination-btn:hover:not(:disabled) {
    background: #e9ecef;
    border-color: #adb5bd;
}

.pagination-btn:disabled {
    background: #f8f9fa;
    color: #adb5bd;
    cursor: not-allowed;
    border-color: #dee2e6;
}

.pagination-numbers {
    display: flex;
    gap: 2px;
    margin: 0 10px;
}

.pagination-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 8px 12px;
    border: 1px solid #dee2e6;
    background: white;
    color: #495057;
    text-decoration: none;
    border-radius: 6px;
    font-size: 13px;
    cursor: pointer;
    transition: background-color 0.2s ease, border-color 0.2s ease;
    min-width: 36px;
    box-sizing: border-box;
    white-space: nowrap;
}

.pagination-number:hover {
    background: #e9ecef;
    border-color: #adb5bd;
}

.pagination-number.active {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.pagination-number.active:hover {
    background: #0056b3;
    border-color: #0056b3;
}

@media (max-width: 768px) {
    .admin-pagination {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .pagination-controls {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .pagination-btn {
        padding: 6px 8px;
        font-size: 12px;
    }
    
    .pagination-number {
        padding: 6px 8px;
        font-size: 12px;
        min-width: 30px;
    }
}
</style>

<!-- Registration Details Modal -->
<div id="registrationDetailsModal" class="admin-modal">
    <div class="admin-modal-content">
        <div class="admin-modal-header">
            <h2>Complete Registration Details</h2>
            <span class="admin-modal-close" onclick="hideRegistrationDetailsModal()">&times;</span>
        </div>
        <div class="admin-modal-body">
            <div id="registrationDetailsContent">
                <!-- Details will be loaded here -->
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

// Initialize the admin dashboard when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    loadApplications();
    loadEmailConfig();
});

// Tab switching functionality
function switchTab(tabName) {
    // Remove active class from all tabs
    const tabBtns = document.querySelectorAll('.tab-btn');
    tabBtns.forEach(btn => btn.classList.remove('active'));
    
    // Hide all tab content
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => content.style.display = 'none');
    
    // Show selected tab content
    const selectedTab = document.getElementById(tabName + '-tab');
    const selectedBtn = event.target.closest('.tab-btn');
    
    if (selectedTab && selectedBtn) {
        selectedTab.style.display = 'block';
        selectedBtn.classList.add('active');
        
        // Load data based on tab
        if (tabName === 'applications') {
            loadApplications();
        } else if (tabName === 'kyc') {
            loadKycApplications();
        }
    }
}

// KYC Applications loading
function loadKycApplications() {
    const statusFilter = document.getElementById('kyc-status-filter')?.value || '';
    const perPage = document.getElementById('kyc-per-page-filter')?.value || '25';
    
    fetch(affiliate_ajax.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'affiliate_get_kyc_applications',
            nonce: affiliate_ajax.nonce,
            status_filter: statusFilter,
            per_page: perPage,
            page: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateKycTable(data.data.applications);
            updateKycPagination(data.data.pagination);
        } else {
            console.error('Failed to load KYC applications:', data.data);
        }
    })
    .catch(error => {
        console.error('Error loading KYC applications:', error);
    });
}

// Update KYC applications table
function updateKycTable(applications) {
    const tbody = document.getElementById('kyc-applications-tbody');
    if (!tbody) return;
    
    if (!applications || applications.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center">No KYC applications found</td></tr>';
        return;
    }
    
    tbody.innerHTML = applications.map(app => `
        <tr>
            <td><strong>${app.username}</strong></td>
            <td>${app.full_name || 'N/A'}</td>
            <td>${app.email || 'N/A'}</td>
            <td>${app.nationality || 'N/A'}</td>
            <td>${app.affiliate_type || 'N/A'}</td>
            <td><span class="kyc-status-badge ${app.kyc_status}">${app.kyc_status.charAt(0).toUpperCase() + app.kyc_status.slice(1)}</span></td>
            <td>${app.submitted_at ? new Date(app.submitted_at).toLocaleDateString() : 'N/A'}</td>
            <td>
                <div class="kyc-documents-list">
                    ${app.address_proof_url ? `<span class="document-link" onclick="viewDocument('${app.address_proof_url}', 'Address Proof')">Address Proof</span>` : ''}
                    ${app.identification_url ? `<span class="document-link" onclick="viewDocument('${app.identification_url}', 'Identification')">ID Document</span>` : ''}
                    ${!app.address_proof_url && !app.identification_url ? 'No documents' : ''}
                </div>
            </td>
            <td>
                <button class="action-btn edit" onclick="reviewKycApplication(${app.user_id})">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    Review
                </button>
                <button class="action-btn edit" onclick="updateKycStatus(${app.user_id})">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                    </svg>
                    Update Status
                </button>
            </td>
        </tr>
    `).join('');
}

// Update KYC pagination
function updateKycPagination(pagination) {
    const container = document.getElementById('kyc-pagination-container');
    const infoText = document.getElementById('kyc-pagination-info-text');
    
    if (container && pagination) {
        container.style.display = pagination.total_pages > 1 ? 'flex' : 'none';
        if (infoText) {
            infoText.textContent = `Showing ${pagination.from} to ${pagination.to} of ${pagination.total} entries`;
        }
    }
}

// Review KYC Application
function reviewKycApplication(userId) {
    fetch(affiliate_ajax.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'affiliate_get_kyc_details',
            nonce: affiliate_ajax.nonce,
            user_id: userId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showKycReviewModal(data.data);
        } else {
            alert('Failed to load KYC details: ' + data.data);
        }
    })
    .catch(error => {
        console.error('Error loading KYC details:', error);
        alert('Error loading KYC details');
    });
}

// Show KYC Review Modal
function showKycReviewModal(kycData) {
    const modal = document.getElementById('kycReviewModal');
    const content = document.getElementById('kycReviewContent');
    
    if (!modal || !content) return;
    
    const accountType = kycData.account_type || 'individual';
    const isCompany = accountType.toLowerCase() !== 'individual';
    
    let contentHTML = `
        <div class="kyc-review-section">
            <h4>Account Information</h4>
            <div class="kyc-field">
                <div class="kyc-field-label">Account Type</div>
                <div class="kyc-field-value">
                    <span class="account-type-badge ${accountType.toLowerCase()}">${accountType.charAt(0).toUpperCase() + accountType.slice(1)}</span>
                </div>
            </div>
        </div>
        
        <div class="kyc-review-section">
            <h4>Personal Information</h4>
            <div class="kyc-field-grid">
                <div class="kyc-field">
                    <div class="kyc-field-label">Full Name</div>
                    <div class="kyc-field-value">${kycData.full_name || 'N/A'}</div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">Date of Birth</div>
                    <div class="kyc-field-value">${kycData.date_of_birth || 'N/A'}</div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">Email</div>
                    <div class="kyc-field-value">${kycData.email || 'N/A'}</div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">Nationality</div>
                    <div class="kyc-field-value">${kycData.nationality || 'N/A'}</div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">Mobile Number</div>
                    <div class="kyc-field-value">${kycData.mobile_number || 'N/A'}</div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">Affiliate Type</div>
                    <div class="kyc-field-value">${kycData.affiliate_type || 'N/A'}</div>
                </div>
            </div>
        </div>
        
        <div class="kyc-review-section">
            <h4>Address Information</h4>
            <div class="kyc-field-grid">
                <div class="kyc-field">
                    <div class="kyc-field-label">Address Line 1</div>
                    <div class="kyc-field-value">${kycData.address_line1 || 'N/A'}</div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">Address Line 2</div>
                    <div class="kyc-field-value">${kycData.address_line2 || 'N/A'}</div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">City</div>
                    <div class="kyc-field-value">${kycData.city || 'N/A'}</div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">Country</div>
                    <div class="kyc-field-value">${kycData.country || 'N/A'}</div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">Post Code</div>
                    <div class="kyc-field-value">${kycData.post_code || 'N/A'}</div>
                </div>
            </div>
        </div>`;
    
    // Add company-specific sections
    if (isCompany) {
        contentHTML += `
        <div class="kyc-review-section">
            <h4>Business Contact Information</h4>
            <div class="kyc-field-grid">
                <div class="kyc-field">
                    <div class="kyc-field-label">Business Contact Name</div>
                    <div class="kyc-field-value">${kycData.business_contact_name || 'N/A'}</div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">Job Title</div>
                    <div class="kyc-field-value">${kycData.job_title || 'N/A'}</div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">Business Email</div>
                    <div class="kyc-field-value">${kycData.business_email || 'N/A'}</div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">Business Telephone</div>
                    <div class="kyc-field-value">${kycData.business_telephone || 'N/A'}</div>
                </div>
            </div>
        </div>
        
        <div class="kyc-review-section">
            <h4>Company Information</h4>
            <div class="kyc-field-grid">
                <div class="kyc-field">
                    <div class="kyc-field-label">Full Company Name</div>
                    <div class="kyc-field-value">${kycData.full_company_name || 'N/A'}</div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">Trading Name</div>
                    <div class="kyc-field-value">${kycData.trading_name || 'N/A'}</div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">Company Type</div>
                    <div class="kyc-field-value">${kycData.company_type || 'N/A'}</div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">Type of Business</div>
                    <div class="kyc-field-value">${kycData.type_of_business || 'N/A'}</div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">Company Registration No.</div>
                    <div class="kyc-field-value">${kycData.company_registration_no || 'N/A'}</div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">Company Email</div>
                    <div class="kyc-field-value">${kycData.company_email || 'N/A'}</div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">Company Telephone</div>
                    <div class="kyc-field-value">${kycData.company_telephone || 'N/A'}</div>
                </div>
            </div>
        </div>
        
        <div class="kyc-review-section">
            <h4>Company Address</h4>
            <div class="kyc-field-grid">
                <div class="kyc-field">
                    <div class="kyc-field-label">Address Line 1</div>
                    <div class="kyc-field-value">${kycData.company_address_line1 || 'N/A'}</div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">Address Line 2</div>
                    <div class="kyc-field-value">${kycData.company_address_line2 || 'N/A'}</div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">City</div>
                    <div class="kyc-field-value">${kycData.company_city || 'N/A'}</div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">Country</div>
                    <div class="kyc-field-value">${kycData.company_country || 'N/A'}</div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">Post Code</div>
                    <div class="kyc-field-value">${kycData.company_post_code || 'N/A'}</div>
                </div>
            </div>
        </div>`;
        
        // Directors information
        if (kycData.list_of_directors) {
            try {
                const directors = JSON.parse(kycData.list_of_directors);
                if (directors && directors.length > 0) {
                    contentHTML += `
                    <div class="kyc-review-section">
                        <h4>Directors</h4>
                        <div class="directors-list">`;
                    directors.forEach((director, index) => {
                        contentHTML += `
                            <div class="kyc-field-grid" style="margin-bottom: 10px; padding: 10px; border: 1px solid #e1e5e9; border-radius: 6px;">
                                <div class="kyc-field">
                                    <div class="kyc-field-label">Director ${index + 1} Name</div>
                                    <div class="kyc-field-value">${director.name || 'N/A'}</div>
                                </div>
                                <div class="kyc-field">
                                    <div class="kyc-field-label">Position</div>
                                    <div class="kyc-field-value">${director.position || 'N/A'}</div>
                                </div>
                            </div>`;
                    });
                    contentHTML += `
                        </div>
                    </div>`;
                }
            } catch (e) {
                console.error('Error parsing directors list:', e);
            }
        }
        
        // Shareholders information
        if (kycData.list_of_shareholders) {
            try {
                const shareholders = JSON.parse(kycData.list_of_shareholders);
                if (shareholders && shareholders.length > 0) {
                    contentHTML += `
                    <div class="kyc-review-section">
                        <h4>Shareholders</h4>
                        <div class="shareholders-list">`;
                    shareholders.forEach((shareholder, index) => {
                        contentHTML += `
                            <div class="kyc-field-grid" style="margin-bottom: 10px; padding: 10px; border: 1px solid #e1e5e9; border-radius: 6px;">
                                <div class="kyc-field">
                                    <div class="kyc-field-label">Shareholder ${index + 1} Name</div>
                                    <div class="kyc-field-value">${shareholder.name || 'N/A'}</div>
                                </div>
                                <div class="kyc-field">
                                    <div class="kyc-field-label">Percentage</div>
                                    <div class="kyc-field-value">${shareholder.percentage || 'N/A'}%</div>
                                </div>
                            </div>`;
                    });
                    contentHTML += `
                        </div>
                    </div>`;
                }
            } catch (e) {
                console.error('Error parsing shareholders list:', e);
            }
        }
    }
    
    contentHTML += `
        <div class="kyc-review-section">
            <h4>Business Information</h4>
            <div class="kyc-field">
                <div class="kyc-field-label">Affiliate Sites/URLs</div>
                <div class="kyc-field-value" style="white-space: pre-line;">${kycData.affiliate_sites || 'N/A'}</div>
            </div>
        </div>
        
        <div class="kyc-review-section">
            <h4>Uploaded Documents</h4>
            <div class="kyc-field-grid">
                <div class="kyc-field">
                    <div class="kyc-field-label">Address Proof</div>
                    <div class="kyc-field-value">
                        ${kycData.address_proof_url ? 
                            `<span class="document-link" onclick="viewDocument('${kycData.address_proof_url}', 'Address Proof')">View Document</span>` : 
                            'Not uploaded'
                        }
                    </div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">Identification</div>
                    <div class="kyc-field-value">
                        ${kycData.identification_url ? 
                            `<span class="document-link" onclick="viewDocument('${kycData.identification_url}', 'Identification')">View Document</span>` : 
                            'Not uploaded'
                        }
                    </div>
                </div>`;
    
    // Add company documents if applicable
    if (isCompany) {
        contentHTML += `
                <div class="kyc-field">
                    <div class="kyc-field-label">Company Registration Certificate</div>
                    <div class="kyc-field-value">
                        ${kycData.company_registration_cert_url ? 
                            `<span class="document-link" onclick="viewDocument('${kycData.company_registration_cert_url}', 'Company Registration Certificate')">View Document</span>` : 
                            'Not uploaded'
                        }
                    </div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">Company Address Proof</div>
                    <div class="kyc-field-value">
                        ${kycData.company_address_proof_url ? 
                            `<span class="document-link" onclick="viewDocument('${kycData.company_address_proof_url}', 'Company Address Proof')">View Document</span>` : 
                            'Not uploaded'
                        }
                    </div>
                </div>`;
        
        // Directors' ID documents
        if (kycData.directors_id_docs_urls) {
            try {
                const directorsDocs = JSON.parse(kycData.directors_id_docs_urls);
                if (directorsDocs && directorsDocs.length > 0) {
                    contentHTML += `
                <div class="kyc-field">
                    <div class="kyc-field-label">Directors' ID Documents</div>
                    <div class="kyc-field-value">`;
                    directorsDocs.forEach((docUrl, index) => {
                        contentHTML += `
                        <span class="document-link" onclick="viewDocument('${docUrl}', 'Director ${index + 1} ID')">Director ${index + 1} ID</span><br>`;
                    });
                    contentHTML += `
                    </div>
                </div>`;
                }
            } catch (e) {
                console.error('Error parsing directors documents:', e);
            }
        }
    }
    
    contentHTML += `
            </div>
        </div>
        
        <div class="kyc-review-section">
            <h4>Status Information</h4>
            <div class="kyc-field-grid">
                <div class="kyc-field">
                    <div class="kyc-field-label">Current Status</div>
                    <div class="kyc-field-value">
                        <span class="kyc-status-badge ${kycData.kyc_status}">${kycData.kyc_status.charAt(0).toUpperCase() + kycData.kyc_status.slice(1)}</span>
                    </div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">Submitted Date</div>
                    <div class="kyc-field-value">${kycData.submitted_at ? new Date(kycData.submitted_at).toLocaleString() : 'Not submitted'}</div>
                </div>
            </div>
        </div>`;
    
    content.innerHTML = contentHTML;
    modal.style.display = 'flex';
}

// Hide KYC Review Modal
function hideKycReviewModal() {
    const modal = document.getElementById('kycReviewModal');
    if (modal) modal.style.display = 'none';
}

// Update KYC Status
function updateKycStatus(userId) {
    const modal = document.getElementById('kycStatusModal');
    const userIdInput = document.getElementById('kyc_user_id');
    
    if (modal && userIdInput) {
        userIdInput.value = userId;
        modal.style.display = 'flex';
    }
}

// Hide KYC Status Modal
function hideKycStatusModal() {
    const modal = document.getElementById('kycStatusModal');
    if (modal) modal.style.display = 'none';
}

// View Document
function viewDocument(url, title) {
    const modal = document.getElementById('documentViewerModal');
    const content = document.getElementById('documentViewerContent');
    const modalTitle = modal.querySelector('.modal-header h3');
    
    if (!modal || !content) return;
    
    modalTitle.textContent = `Document Viewer - ${title}`;
    
    const fileExtension = url.split('.').pop().toLowerCase();
    
    if (fileExtension === 'pdf') {
        content.innerHTML = `<iframe src="${url}" style="width: 100%; height: 100%; border: none;"></iframe>`;
    } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
        content.innerHTML = `<img src="${url}" style="max-width: 100%; max-height: 100%; object-fit: contain;" alt="${title}">`;
    } else {
        content.innerHTML = `
            <div style="text-align: center; padding: 40px;">
                <p>Cannot preview this file type. Click below to download:</p>
                <a href="${url}" target="_blank" class="affiliate-btn affiliate-btn-primary">Download ${title}</a>
            </div>
        `;
    }
    
    modal.style.display = 'flex';
}

// Hide Document Viewer
function hideDocumentViewer() {
    const modal = document.getElementById('documentViewerModal');
    if (modal) modal.style.display = 'none';
}

// Handle KYC Status Update Form
document.addEventListener('DOMContentLoaded', function() {
    const kycStatusForm = document.getElementById('kycStatusUpdateForm');
    if (kycStatusForm) {
        kycStatusForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(kycStatusForm);
            formData.append('action', 'affiliate_update_kyc_status');
            formData.append('nonce', affiliate_ajax.nonce);
            
            fetch(affiliate_ajax.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('KYC status updated successfully');
                    hideKycStatusModal();
                    loadKycApplications();
                } else {
                    alert('Failed to update KYC status: ' + data.data);
                }
            })
            .catch(error => {
                console.error('Error updating KYC status:', error);
                alert('Error updating KYC status');
            });
        });
    }
});

// KYC Verification Functions
function showKycVerification(userId) {
    const modal = document.getElementById('kycVerificationModal');
    const content = document.getElementById('kycVerificationContent');
    
    if (!modal || !content) return;
    
    content.innerHTML = 'Loading KYC details...';
    modal.style.display = 'flex';
    
    // Fetch KYC details
    const formData = new FormData();
    formData.append('action', 'affiliate_get_kyc_verification_details');
    formData.append('user_id', userId);
    formData.append('nonce', affiliate_ajax.nonce);
    
    fetch(affiliate_ajax.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            content.innerHTML = data.data.html;
        } else {
            content.innerHTML = '<div class="error">Failed to load KYC details: ' + data.data + '</div>';
        }
    })
    .catch(error => {
        console.error('Error loading KYC details:', error);
        content.innerHTML = '<div class="error">Error loading KYC details</div>';
    });
}

function hideKycModal() {
    const modal = document.getElementById('kycVerificationModal');
    if (modal) modal.style.display = 'none';
}

function updateKycStatus(userId, status, comments) {
    const formData = new FormData();
    formData.append('action', 'affiliate_update_kyc_status');
    formData.append('user_id', userId);
    formData.append('kyc_status', status);
    formData.append('admin_comments', comments);
    formData.append('nonce', affiliate_ajax.nonce);
    
    fetch(affiliate_ajax.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('KYC status updated successfully. Email notification sent to user.');
            hideKycModal();
            loadApplications(); // Refresh the main applications table
        } else {
            alert('Failed to update KYC status: ' + data.data);
        }
    })
    .catch(error => {
        console.error('Error updating KYC status:', error);
        alert('Error updating KYC status');
    });
}

function approveKyc(userId) {
    const comments = document.getElementById('kycAdminComments')?.value || '';
    if (confirm('Are you sure you want to approve this KYC application?')) {
        updateKycStatus(userId, 'approved', comments);
    }
}

function rejectKyc(userId) {
    const comments = document.getElementById('kycAdminComments')?.value || '';
    if (!comments.trim()) {
        alert('Please provide rejection comments for the user.');
        return;
    }
    if (confirm('Are you sure you want to reject this KYC application?')) {
        updateKycStatus(userId, 'rejected', comments);
    }
}

function requestMoreInfo(userId) {
    const comments = document.getElementById('kycAdminComments')?.value || '';
    if (!comments.trim()) {
        alert('Please provide comments explaining what additional information is needed.');
        return;
    }
    if (confirm('This will request additional information from the user.')) {
        updateKycStatus(userId, 'incomplete', comments);
    }
}

function viewDocumentInModal(url, title) {
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.style.display = 'flex';
    modal.innerHTML = `
        <div class="modal-content" style="max-width: 90%; max-height: 90%;">
            <div class="modal-header">
                <h3>${title}</h3>
                <button type="button" class="modal-close" onclick="this.closest('.modal').remove()">&times;</button>
            </div>
            <div class="modal-body" style="padding: 20px; text-align: center;">
                ${url.toLowerCase().endsWith('.pdf') 
                    ? `<iframe src="${url}" style="width: 100%; height: 70vh; border: none;"></iframe>`
                    : `<img src="${url}" style="max-width: 100%; max-height: 70vh; object-fit: contain;" alt="${title}">`
                }
                <div style="margin-top: 15px;">
                    <a href="${url}" target="_blank" class="affiliate-btn affiliate-btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                        </svg>
                        Open in New Tab
                    </a>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}
</script>

<style>
/* KYC Modal Styles */
.kyc-modal .modal-content {
    max-width: 1200px !important;
    width: 95% !important;
    max-height: 90vh;
    overflow-y: auto;
}

.kyc-modal-body {
    padding: 20px;
    max-height: 70vh;
    overflow-y: auto;
}

.kyc-user-info {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
    border: 2px solid #dee2e6;
}

.kyc-user-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
}

.kyc-user-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0d6efd, #6610f2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    font-weight: bold;
}

.kyc-user-details h3 {
    margin: 0;
    color: #2c3e50;
    font-size: 1.4rem;
}

.kyc-user-details p {
    margin: 5px 0 0 0;
    color: #6c757d;
    font-size: 0.95rem;
}

.kyc-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.kyc-status-pending { background: #fff3cd; color: #856404; border: 2px solid #ffc107; }
.kyc-status-approved { background: #d1edff; color: #0c5460; border: 2px solid #17a2b8; }
.kyc-status-rejected { background: #f8d7da; color: #721c24; border: 2px solid #dc3545; }
.kyc-status-incomplete { background: #fff3cd; color: #856404; border: 2px solid #fd7e14; }

.kyc-sections { display: grid; gap: 25px; }

.kyc-section {
    background: white;
    border-radius: 12px;
    padding: 25px;
    border: 2px solid #e9ecef;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.kyc-section-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f8f9fa;
}

.kyc-section-header h4 {
    margin: 0;
    color: #2c3e50;
    font-size: 1.2rem;
    font-weight: 600;
}

.kyc-form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.kyc-field {
    display: flex;
    flex-direction: column;
}

.kyc-field-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 5px;
    font-size: 0.9rem;
}

.kyc-field-value {
    padding: 10px 12px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    color: #2c3e50;
    font-size: 0.95rem;
}

.kyc-documents-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.kyc-document {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    overflow: hidden;
    background: white;
}

.kyc-document-header {
    background: #f8f9fa;
    padding: 15px 20px;
    border-bottom: 2px solid #e9ecef;
}

.kyc-document-title {
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
    font-size: 1rem;
}

.kyc-document-content { padding: 20px; }

.kyc-document-preview {
    width: 100%;
    max-height: 200px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 15px;
}

.kyc-document-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.kyc-doc-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
}

.kyc-doc-btn-view { background: #0d6efd; color: white; }
.kyc-doc-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); }

.kyc-admin-actions {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 25px;
    margin-top: 25px;
    border: 2px solid #dee2e6;
}

.kyc-admin-actions h4 {
    margin: 0 0 20px 0;
    color: #2c3e50;
    font-size: 1.2rem;
    font-weight: 600;
}

.kyc-comment-section { margin-bottom: 20px; }

.kyc-comment-section label {
    display: block;
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
}

.kyc-comment-textarea {
    width: 100%;
    min-height: 120px;
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 1rem;
    font-family: inherit;
    resize: vertical;
    box-sizing: border-box;
}

.kyc-comment-textarea:focus {
    outline: none;
    border-color: #0d6efd;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
}

.kyc-status-actions {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
}

.kyc-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    min-height: 48px;
}

.kyc-action-btn-approve { background: linear-gradient(135deg, #198754, #20c997); color: white; }
.kyc-action-btn-reject { background: linear-gradient(135deg, #dc3545, #fd7e14); color: white; }
.kyc-action-btn-info { background: linear-gradient(135deg, #fd7e14, #ffc107); color: white; }

.kyc-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

@media (max-width: 768px) {
    .kyc-modal .modal-content { width: 98% !important; margin: 10px; }
    .kyc-form-grid { grid-template-columns: 1fr; }
    .kyc-documents-grid { grid-template-columns: 1fr; }
    .kyc-status-actions { flex-direction: column; align-items: stretch; }
}
</style>