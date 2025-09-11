<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

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

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
.admin-dashboard {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 20px 0;
}

.dashboard-header {
    background: white;
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.stat-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    margin-bottom: 20px;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.stat-card .stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    font-size: 24px;
    color: white;
}

.stat-card.pending .stat-icon { background: linear-gradient(135deg, #ffc107, #ff8c00); }
.stat-card.approved .stat-icon { background: linear-gradient(135deg, #28a745, #20c997); }
.stat-card.rejected .stat-icon { background: linear-gradient(135deg, #dc3545, #e83e8c); }

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: #2c3e50;
    margin-bottom: 5px;
}

.stat-label {
    color: #6c757d;
    font-weight: 500;
}

.applications-container {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.application-card {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.application-card:hover {
    border-color: #667eea;
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.1);
}

.status-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    text-transform: uppercase;
}

.status-badge.pending { background: #fff3cd; color: #856404; }
.status-badge.approved { background: #d4edda; color: #155724; }
.status-badge.rejected { background: #f8d7da; color: #721c24; }
.status-badge.awaiting_approval { background: #cce7ff; color: #004085; }

.action-btn {
    margin: 0 2px;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 0.875rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-view { background: #17a2b8; color: white; }
.btn-approve { background: #28a745; color: white; }
.btn-reject { background: #dc3545; color: white; }
.btn-update { background: #007bff; color: white; }

.btn-view:hover { background: #138496; }
.btn-approve:hover { background: #218838; }
.btn-reject:hover { background: #c82333; }
.btn-update:hover { background: #0056b3; }

.search-section {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
}

.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
    border-bottom: none;
}

@media (max-width: 768px) {
    .dashboard-header {
        padding: 20px;
    }
    
    .applications-container {
        padding: 20px;
    }
    
    .stat-card {
        padding: 20px;
    }
    
    .application-card {
        padding: 15px;
    }
}

/* Custom Modal Styles */
.custom-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    box-sizing: border-box;
}

.custom-modal-container {
    width: 90%;
    max-width: 900px;
    max-height: 85vh;
    height: 85vh;
    background: white;
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    animation: modalSlideIn 0.3s ease-out;
    display: flex;
    flex-direction: column;
}

.custom-modal-content {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.custom-modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
}

.custom-modal-title {
    font-size: 1.3rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
}

.custom-modal-close {
    background: none;
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.custom-modal-close:hover {
    background: rgba(255, 255, 255, 0.2);
}

.custom-modal-body {
    flex: 1;
    overflow-y: scroll !important;
    overflow-x: hidden;
    padding: 20px;
    background: #f8fafc;
    min-height: 0;
    max-height: calc(85vh - 120px);
    scrollbar-width: thick;
    scrollbar-color: #667eea #f1f5f9;
}

/* Custom scrollbar for Webkit browsers */
.custom-modal-body::-webkit-scrollbar {
    width: 8px;
}

.custom-modal-body::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.custom-modal-body::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
    border: 1px solid #f1f5f9;
}

.custom-modal-body::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
}

.custom-modal-footer {
    padding: 15px 20px;
    background: white;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    flex-shrink: 0;
}

/* Custom card and form styles */
.custom-info-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 15px;
}

.custom-info-card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 15px;
    font-weight: 600;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
}

.custom-info-card-body {
    padding: 15px;
}

.custom-info-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 6px 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.85rem;
}

.custom-info-item:last-child {
    border-bottom: none;
}

.custom-info-label {
    font-weight: 600;
    color: #374151;
    min-width: 110px;
    flex-shrink: 0;
    font-size: 0.85rem;
}

.custom-info-value {
    color: #6b7280;
    text-align: right;
    flex: 1;
    margin-left: 15px;
    font-size: 0.85rem;
}

.custom-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    font-size: 0.9rem;
}

.custom-btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.custom-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.custom-btn-secondary {
    background: #64748b;
    color: white;
}

.custom-btn-secondary:hover {
    background: #475569;
}

.custom-btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.custom-btn-success:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.form-group-compact {
    margin-bottom: 15px;
}

.form-label-compact {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
    font-size: 0.9rem;
}

.form-control-compact {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.9rem;
    transition: border-color 0.2s;
}

.form-control-compact:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: scale(0.95) translateY(-20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .custom-modal-container {
        width: 95%;
        max-width: 95%;
    }
    
    .custom-info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }
    
    .custom-info-label {
        min-width: auto;
    }
    
    .custom-info-value {
        text-align: left;
        margin-left: 0;
    }
}

.custom-info-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    overflow: hidden;
}

.custom-info-card-header {
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: white;
    padding: 20px;
    font-weight: bold;
    font-size: 1.2rem;
}

.custom-info-card-body {
    padding: 25px;
}

.custom-info-item {
    margin-bottom: 15px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}

.custom-info-label {
    font-weight: bold;
    color: #333;
    margin-right: 10px;
}

.custom-info-value {
    color: #666;
}

.custom-btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
}

.custom-btn-primary {
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: white;
}

.custom-btn-secondary {
    background: #6c757d;
    color: white;
}

.custom-btn-success {
    background: linear-gradient(45deg, #28a745, #20c997);
    color: white;
}

.custom-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
}
</style>

<div class="admin-dashboard">
    <div class="container-fluid">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0">
                        <i class="fas fa-shield-alt text-primary me-3"></i>
                        Master Admin Dashboard
                    </h1>
                    <p class="text-muted mb-0 mt-2">Welcome, <?php echo esc_html($admin_username ?? $admin_data->full_name); ?></p>
                </div>
                <div class="col-md-4 text-end">
                    <button type="button" class="btn btn-outline-primary me-2" onclick="showEmailConfig()">
                        <i class="fas fa-envelope me-2"></i>Email Settings
                    </button>
                    <button type="button" class="btn btn-danger" onclick="adminLogout()">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card pending">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-number" id="pending-count">-</div>
                    <div class="stat-label">Pending Applications</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card approved">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-number" id="approved-count">-</div>
                    <div class="stat-label">Approved</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card rejected">
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-number" id="rejected-count">-</div>
                    <div class="stat-label">Rejected</div>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="search-section">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="search-input" placeholder="Search by name, email, or company..." oninput="performSearch()">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="status-filter" onchange="loadAdminApplications()">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="awaiting approval">Awaiting Approval</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="per-page-filter" onchange="loadAdminApplications()">
                        <option value="10">10 per page</option>
                        <option value="25" selected>25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-primary w-100" onclick="loadAdminApplications()">
                        <i class="fas fa-sync-alt me-2"></i>Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- Applications Table -->
        <div class="applications-container">
            <h4 class="mb-4">
                <i class="fas fa-users me-2"></i>
                Applications <span class="badge bg-primary" id="total-count">0</span>
            </h4>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Company</th>
                            <th>Type</th>
                            <th>Country</th>
                            <th>Status</th>
                            <th>Applied Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="applications-tbody">
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-3 text-muted mb-0">Loading applications...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav id="pagination-container" class="mt-4" style="display: none;">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="text-muted mb-0" id="pagination-info">Showing entries</p>
                    </div>
                    <div class="col-md-6">
                        <ul class="pagination justify-content-end mb-0" id="pagination-controls">
                            <!-- Pagination buttons will be inserted here -->
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</div>

<!-- Custom Application Details Modal will be created dynamically -->

<!-- Custom Status Update Modal will be created dynamically -->

<!-- Email Configuration Modal -->
<div class="modal fade" id="emailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h4 class="modal-title">
                    <i class="fas fa-envelope me-3"></i>Email Configuration
                </h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="email-config-form">
                    <div class="mb-3">
                        <label for="notification_emails" class="form-label">Notification Emails</label>
                        <textarea class="form-control" id="notification_emails" rows="3" placeholder="Enter email addresses separated by commas"></textarea>
                        <div class="form-text">Emails that will receive notifications about new registrations</div>
                    </div>
                    <div class="mb-3">
                        <label for="from_email" class="form-label">From Email</label>
                        <input type="email" class="form-control" id="from_email" placeholder="noreply@yoursite.com">
                    </div>
                    <div class="mb-3">
                        <label for="from_name" class="form-label">From Name</label>
                        <input type="text" class="form-control" id="from_name" placeholder="Your Site Name">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-success" onclick="updateEmailConfig()">
                    <i class="fas fa-save me-2"></i>Save Settings
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Global variables
window.adminDashboard = {
    currentPage: 1,
    currentStatus: '',
    searchTerm: '',
    perPage: 25,
    applications: [],
    totalPages: 0,
    totalRecords: 0
};

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    // Set up AJAX configuration
    window.affiliate_ajax = {
        ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>',
        nonce: '<?php echo wp_create_nonce('affiliate_nonce'); ?>'
    };
    
    // Load initial data with a small delay to ensure DOM is ready
    setTimeout(function() {
        console.log('Starting initial data load...');
        loadAdminApplications();
        loadEmailConfig();
    }, 100);
});

// Load applications with proper error handling
function loadAdminApplications() {
    const statusFilter = document.getElementById('status-filter').value;
    const perPage = document.getElementById('per-page-filter').value;
    const searchTerm = document.getElementById('search-input').value;
    
    // Update global state
    window.adminDashboard.currentStatus = statusFilter;
    window.adminDashboard.perPage = parseInt(perPage);
    window.adminDashboard.searchTerm = searchTerm;
    window.adminDashboard.currentPage = 1;
    
    // Show loading
    document.getElementById('applications-tbody').innerHTML = `
        <tr>
            <td colspan="8" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted mb-0">Loading applications...</p>
            </td>
        </tr>
    `;
    
    // Prepare request data with correct parameter names for existing backend
    const requestData = {
        action: 'affiliate_get_applications',
        nonce: window.affiliate_ajax.nonce,
        page: window.adminDashboard.currentPage,
        per_page: window.adminDashboard.perPage,
        status: statusFilter, // Backend expects 'status' parameter
        search: searchTerm
    };
    
    console.log('Loading applications with data:', requestData);
    
    fetch(window.affiliate_ajax.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(requestData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Applications response:', data);
        
        if (data.success) {
            window.adminDashboard.applications = data.data.applications || [];
            window.adminDashboard.totalPages = data.data.pagination ? data.data.pagination.total_pages : 1;
            window.adminDashboard.totalRecords = data.data.pagination ? data.data.pagination.total_records : 0;
            
            updateApplicationsGrid(window.adminDashboard.applications);
            updateAdminPagination(data.data.pagination);
            updateStatistics(data.data.stats);
        } else {
            console.error('Failed to load applications:', data.data);
            document.getElementById('applications-tbody').innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <i class="fas fa-exclamation-triangle text-warning fa-2x mb-3"></i>
                        <h6>Failed to load applications</h6>
                        <p class="text-muted">${data.data || 'Unknown error occurred'}</p>
                        <button class="btn btn-sm btn-primary" onclick="loadAdminApplications()">
                            <i class="fas fa-retry me-2"></i>Try Again
                        </button>
                    </td>
                </tr>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading applications:', error);
        document.getElementById('applications-tbody').innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <i class="fas fa-exclamation-triangle text-danger fa-2x mb-3"></i>
                    <h6>Connection Error</h6>
                    <p class="text-muted">Unable to connect to server. Please check your connection.</p>
                    <button class="btn btn-sm btn-primary" onclick="loadAdminApplications()">
                        <i class="fas fa-retry me-2"></i>Try Again
                    </button>
                </td>
            </tr>
        `;
    });
}

// Update applications table
function updateApplicationsGrid(applications) {
    const tbody = document.getElementById('applications-tbody');
    
    if (!applications || applications.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <i class="fas fa-inbox text-muted fa-2x mb-3"></i>
                    <p class="text-muted mb-0">No applications found matching your criteria.</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = applications.map(app => `
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <i class="fas fa-user text-primary me-2"></i>
                    <strong>${app.first_name} ${app.last_name}</strong>
                </div>
            </td>
            <td>${app.email}</td>
            <td>${app.company_name || 'N/A'}</td>
            <td>${app.affiliate_type || 'N/A'}</td>
            <td>${app.country || 'N/A'}</td>
            <td>
                <span class="badge ${getStatusBadgeClass(app.status)}">${app.status}</span>
            </td>
            <td>${new Date(app.created_at).toLocaleDateString()}</td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="viewApplication(${app.id})" title="View complete application details">
                        <i class="fas fa-file-text me-1"></i>View Application
                    </button>
                    ${app.status.toLowerCase() !== 'kyc pending' ? `
                    <button class="btn btn-sm btn-outline-success" onclick="viewKYCDetails(${app.id})" title="View and verify KYC documents">
                        <i class="fas fa-shield-check me-1"></i>Verify KYC
                    </button>
                    ` : ''}
                </div>
            </td>
        </tr>
    `).join('');
    
    // Update total count
    document.getElementById('total-count').textContent = window.adminDashboard.totalRecords;
}

// Helper function to get status badge class
function getStatusBadgeClass(status) {
    switch(status.toLowerCase()) {
        case 'approved': return 'bg-success';
        case 'rejected': return 'bg-danger';
        case 'awaiting approval': return 'bg-warning';
        case 'kyc pending': return 'bg-info';
        case 'additional document required': return 'bg-warning';
        default: return 'bg-secondary';
    }
}

// Helper function to get status display name
function getStatusDisplayName(status) {
    switch(status.toLowerCase()) {
        case 'kyc pending': return 'KYC Pending';
        case 'awaiting approval': return 'Awaiting Approval';
        case 'approved': return 'Approved';
        case 'rejected': return 'Rejected';
        case 'additional document required': return 'Addition Info. Required';
        default: return status.charAt(0).toUpperCase() + status.slice(1);
    }
}

// Update pagination
function updateAdminPagination(pagination) {
    const container = document.getElementById('pagination-container');
    const controls = document.getElementById('pagination-controls');
    const info = document.getElementById('pagination-info');
    
    if (!pagination || pagination.total_pages <= 1) {
        container.style.display = 'none';
        return;
    }
    
    container.style.display = 'block';
    
    // Update info
    const start = ((pagination.current_page - 1) * pagination.per_page) + 1;
    const end = Math.min(pagination.current_page * pagination.per_page, pagination.total_records);
    info.textContent = `Showing ${start} to ${end} of ${pagination.total_records} entries`;
    
    // Generate pagination buttons
    let buttonsHTML = '';
    
    // Previous button
    buttonsHTML += `
        <li class="page-item ${!pagination.has_prev ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="goToPage(${pagination.current_page - 1})" tabindex="-1">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>
    `;
    
    // Page numbers
    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(pagination.total_pages, pagination.current_page + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        buttonsHTML += `
            <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="goToPage(${i})">${i}</a>
            </li>
        `;
    }
    
    // Next button
    buttonsHTML += `
        <li class="page-item ${!pagination.has_next ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="goToPage(${pagination.current_page + 1})">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    `;
    
    controls.innerHTML = buttonsHTML;
}

// Go to specific page
function goToPage(page) {
    if (page < 1 || page > window.adminDashboard.totalPages) return;
    
    window.adminDashboard.currentPage = page;
    
    const requestData = {
        action: 'affiliate_get_applications',
        nonce: window.affiliate_ajax.nonce,
        page: page,
        per_page: window.adminDashboard.perPage,
        status: window.adminDashboard.currentStatus,
        search: window.adminDashboard.searchTerm
    };
    
    fetch(window.affiliate_ajax.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(requestData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateApplicationsGrid(data.data.applications);
            updateAdminPagination(data.data.pagination);
        }
    })
    .catch(error => {
        console.error('Error loading page:', error);
    });
}

// View KYC Details function
function viewKYCDetails(userId) {
    fetch(window.affiliate_ajax.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'affiliate_get_kyc_details',
            nonce: window.affiliate_ajax.nonce,
            user_id: userId
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('KYC details response:', data);
        if (data.success) {
            // Pass the data as userData to the modal
            showKYCModal(data.data);
        } else {
            alert('Failed to load KYC details: ' + (data.data || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error loading KYC details:', error);
        alert('Error loading KYC details');
    });
}

// Show KYC Modal with details and verification options
function showKYCModal(userData) {
    const modalHTML = `
        <div class="custom-modal-overlay" id="kycModal" onclick="closeModalOnOverlay(event)">
            <div class="custom-modal-container">
                <div class="custom-modal-content">
                    <div class="custom-modal-header">
                        <h2 class="custom-modal-title">
                            <i class="fas fa-shield-check me-3"></i>KYC Verification - ${userData.full_name || 'N/A'}
                        </h2>
                        <button type="button" class="custom-modal-close" onclick="closeKYCModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="custom-modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-6 mb-4">
                                    <div class="custom-info-card">
                                        <div class="custom-info-card-header">
                                            <i class="fas fa-user-circle me-3"></i>Personal Information
                                        </div>
                                        <div class="custom-info-card-body">
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Full Name:</span>
                                                <span class="custom-info-value">${userData.full_name || 'N/A'}</span>
                                            </div>
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Email:</span>
                                                <span class="custom-info-value">${userData.email || 'N/A'}</span>
                                            </div>
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Date of Birth:</span>
                                                <span class="custom-info-value">${userData.date_of_birth || 'N/A'}</span>
                                            </div>
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Nationality:</span>
                                                <span class="custom-info-value">${userData.nationality || 'N/A'}</span>
                                            </div>
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Mobile:</span>
                                                <span class="custom-info-value">${userData.mobile_number || 'N/A'}</span>
                                            </div>
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Account Type:</span>
                                                <span class="custom-info-value">${userData.account_type || 'Individual'}</span>
                                            </div>
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Affiliate Type:</span>
                                                <span class="custom-info-value">${userData.affiliate_type || 'N/A'}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="custom-info-card">
                                        <div class="custom-info-card-header">
                                            <i class="fas fa-home me-3"></i>Address Information
                                        </div>
                                        <div class="custom-info-card-body">
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Address 1:</span>
                                                <span class="custom-info-value">${userData.address_line1 || 'N/A'}</span>
                                            </div>
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Address 2:</span>
                                                <span class="custom-info-value">${userData.address_line2 || 'N/A'}</span>
                                            </div>
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">City:</span>
                                                <span class="custom-info-value">${userData.city || 'N/A'}</span>
                                            </div>
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Country:</span>
                                                <span class="custom-info-value">${userData.country || 'N/A'}</span>
                                            </div>
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Post Code:</span>
                                                <span class="custom-info-value">${userData.post_code || 'N/A'}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            ${userData.account_type && userData.account_type.toLowerCase() === 'company' ? `
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                                        <div class="card-header text-white" style="background: linear-gradient(45deg, #27ae60, #2ecc71); border-radius: 15px 15px 0 0; padding: 20px;">
                                            <h4 class="mb-0" style="font-size: 1.4rem; font-weight: bold;"><i class="fas fa-building me-3"></i>🏢 Company Info</h4>
                                        </div>
                                        <div class="card-body" style="padding: 25px; background: #fff;">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div style="font-size: 1.1rem; line-height: 2.2;">
                                                        <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                                                            <strong style="color: #27ae60;">🏢 Trading Name:</strong> <span style="margin-left: 10px; color: #333;">${userData.company_trading_name || 'N/A'}</span>
                                                        </div>
                                                        <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                                                            <strong style="color: #3498db;">🏷️ Company Type:</strong> <span style="margin-left: 10px; color: #333;">${userData.company_type || 'N/A'}</span>
                                                        </div>
                                                        <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                                                            <strong style="color: #e74c3c;">📋 Registration:</strong> <span style="margin-left: 10px; color: #333;">${userData.registration_number || 'N/A'}</span>
                                                        </div>
                                                        <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                                                            <strong style="color: #f39c12;">💰 Tax ID:</strong> <span style="margin-left: 10px; color: #333;">${userData.tax_id || 'N/A'}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div style="font-size: 1.1rem; line-height: 2.2;">
                                                        <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                                                            <strong style="color: #9b59b6;">📅 Incorporation:</strong> <span style="margin-left: 10px; color: #333;">${userData.incorporation_date || 'N/A'}</span>
                                                        </div>
                                                        <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                                                            <strong style="color: #e67e22;">👤 Contact Name:</strong> <span style="margin-left: 10px; color: #333;">${userData.business_contact_name || 'N/A'}</span>
                                                        </div>
                                                        <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                                                            <strong style="color: #1abc9c;">📧 Contact Email:</strong> <span style="margin-left: 10px; color: #333;">${userData.business_contact_email || 'N/A'}</span>
                                                        </div>
                                                        <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                                                            <strong style="color: #34495e;">📞 Contact Phone:</strong> <span style="margin-left: 10px; color: #333;">${userData.business_contact_phone || 'N/A'}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ` : ''}
                            
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                                        <div class="card-header text-white" style="background: linear-gradient(45deg, #f39c12, #e67e22); border-radius: 15px 15px 0 0; padding: 20px;">
                                            <h4 class="mb-0" style="font-size: 1.4rem; font-weight: bold;"><i class="fas fa-file-alt me-3"></i>📄 Uploaded Documents</h4>
                                        </div>
                                        <div class="card-body" style="padding: 25px; background: #fff;">
                                            <div class="row">
                                                ${userData.identity_document_url ? `
                                                <div class="col-md-4 mb-3">
                                                    <div class="card" style="border: 3px solid #ff6b6b; border-radius: 15px; box-shadow: 0 8px 25px rgba(255,107,107,0.3);">
                                                        <div class="card-body text-center" style="padding: 25px;">
                                                            <div style="background: linear-gradient(45deg, #ff6b6b, #ee5a52); color: white; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 2rem;">
                                                                🆔
                                                            </div>
                                                            <h5 style="color: #ff6b6b; font-weight: bold; margin-bottom: 15px;">Identity Document</h5>
                                                            <a href="${userData.identity_document_url}" target="_blank" class="btn btn-lg" style="background: linear-gradient(45deg, #ff6b6b, #ee5a52); color: white; border: none; border-radius: 25px; padding: 10px 20px; font-weight: bold;">
                                                                <i class="fas fa-eye me-2"></i>View Document
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                ` : ''}
                                                ${userData.address_proof_url ? `
                                                <div class="col-md-4 mb-3">
                                                    <div class="card" style="border: 3px solid #4ecdc4; border-radius: 15px; box-shadow: 0 8px 25px rgba(78,205,196,0.3);">
                                                        <div class="card-body text-center" style="padding: 25px;">
                                                            <div style="background: linear-gradient(45deg, #4ecdc4, #44a08d); color: white; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 2rem;">
                                                                🏠
                                                            </div>
                                                            <h5 style="color: #4ecdc4; font-weight: bold; margin-bottom: 15px;">Address Proof</h5>
                                                            <a href="${userData.address_proof_url}" target="_blank" class="btn btn-lg" style="background: linear-gradient(45deg, #4ecdc4, #44a08d); color: white; border: none; border-radius: 25px; padding: 10px 20px; font-weight: bold;">
                                                                <i class="fas fa-eye me-2"></i>View Document
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                ` : ''}
                                                ${userData.bank_statement_url ? `
                                                <div class="col-md-4 mb-3">
                                                    <div class="card" style="border: 3px solid #27ae60; border-radius: 15px; box-shadow: 0 8px 25px rgba(39,174,96,0.3);">
                                                        <div class="card-body text-center" style="padding: 25px;">
                                                            <div style="background: linear-gradient(45deg, #27ae60, #2ecc71); color: white; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 2rem;">
                                                                🏛️
                                                            </div>
                                                            <h5 style="color: #27ae60; font-weight: bold; margin-bottom: 15px;">Bank Statement</h5>
                                                            <a href="${userData.bank_statement_url}" target="_blank" class="btn btn-lg" style="background: linear-gradient(45deg, #27ae60, #2ecc71); color: white; border: none; border-radius: 25px; padding: 10px 20px; font-weight: bold;">
                                                                <i class="fas fa-eye me-2"></i>View Document
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                ` : ''}
                                                ${!userData.identity_document_url && !userData.address_proof_url && !userData.bank_statement_url ? `
                                                <div class="col-12">
                                                    <div class="alert" style="background: linear-gradient(45deg, #3498db, #2980b9); color: white; border: none; border-radius: 15px; padding: 20px; font-size: 1.1rem; text-align: center;">
                                                        <i class="fas fa-info-circle me-3" style="font-size: 1.5rem;"></i>
                                                        📄 No documents have been uploaded yet.
                                                    </div>
                                                </div>
                                                ` : ''}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="custom-info-card">
                                        <div class="custom-info-card-header">
                                            <i class="fas fa-cog me-3"></i>Status Management
                                        </div>
                                        <div class="custom-info-card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <h5>Current Status</h5>
                                                        <div class="custom-info-item">
                                                            <strong>${getStatusDisplayName(userData.status || 'kyc pending')}</strong>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="kyc-status-update" class="custom-info-label">Update Status</label>
                                                        <select class="form-select" id="kyc-status-update" style="margin-top: 10px;">
                                                            <option value="">Select Status</option>
                                                            <option value="approved" ${userData.status === 'approved' ? 'selected' : ''}>Approved</option>
                                                            <option value="rejected" ${userData.status === 'rejected' ? 'selected' : ''}>Rejected</option>
                                                            <option value="additional document required" ${userData.status === 'additional document required' ? 'selected' : ''}>Additional Document Required</option>
                                                            <option value="awaiting approval" ${userData.status === 'awaiting approval' ? 'selected' : ''}>Awaiting Approval</option>
                                                            <option value="kyc pending" ${userData.status === 'kyc pending' ? 'selected' : ''}>KYC Pending</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="admin-comments-kyc" class="custom-info-label">Admin Comments</label>
                                                        <textarea class="form-control" id="admin-comments-kyc" rows="4" placeholder="Add your comments about this application..." style="margin-top: 10px;">${userData.admin_remarks || ''}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="custom-modal-footer">
                        <button type="button" class="custom-btn custom-btn-secondary" onclick="closeKYCModal()">
                            <i class="fas fa-times me-2"></i>Close
                        </button>
                        <button type="button" class="custom-btn custom-btn-success" onclick="updateKYCStatus(${userData.user_id})">
                            <i class="fas fa-save me-2"></i>Update Status
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if present
    const existingModal = document.getElementById('kycModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Show modal (no Bootstrap needed)
    const modal = document.getElementById('kycModal');
    modal.style.display = 'flex';
    
    // Force scrollbar visibility and proper height constraints
    const modalBody = modal.querySelector('.custom-modal-body');
    if (modalBody) {
        // Force scrollbar to be always visible
        modalBody.style.overflowY = 'scroll';
        modalBody.style.overflowX = 'hidden';
        modalBody.style.height = 'calc(85vh - 140px)';
        modalBody.style.maxHeight = 'calc(85vh - 140px)';
        modalBody.style.minHeight = '300px';
        
        // Add aggressive scrollbar styling that forces visibility
        const scrollbarStyle = document.createElement('style');
        scrollbarStyle.textContent = `
            .custom-modal-body {
                scrollbar-width: thick !important;
                scrollbar-color: #667eea #f1f5f9 !important;
            }
            .custom-modal-body::-webkit-scrollbar {
                width: 16px !important;
                background-color: #f1f5f9 !important;
                display: block !important;
            }
            .custom-modal-body::-webkit-scrollbar-track {
                background: #f1f5f9 !important;
                border-radius: 10px !important;
            }
            .custom-modal-body::-webkit-scrollbar-thumb {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                border-radius: 10px !important;
                border: 2px solid #f1f5f9 !important;
                min-height: 30px !important;
            }
            .custom-modal-body::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%) !important;
            }
            .custom-modal-body::-webkit-scrollbar-corner {
                background: #f1f5f9 !important;
            }
            #applicationModal .custom-modal-body,
            #kycModal .custom-modal-body {
                overflow-y: scroll !important;
                height: calc(85vh - 140px) !important;
                max-height: calc(85vh - 140px) !important;
            }
        `;
        document.head.appendChild(scrollbarStyle);
        
        // Force a reflow to ensure scrollbars appear
        modalBody.scrollTop = 0;
        modalBody.scrollTop = 1;
        modalBody.scrollTop = 0;
    }
}

// Close KYC Modal
function closeKYCModal() {
    const modal = document.getElementById('kycModal');
    if (modal) {
        modal.remove();
    }
}

// Close modal when clicking on overlay (but not on modal content)
function closeModalOnOverlay(event) {
    if (event.target === event.currentTarget) {
        closeKYCModal();
    }
}

// Update KYC Status function (renamed to avoid conflict)
function updateKYCStatus(userId) {
    console.log('updateApplicationStatus called with userId:', userId);
    
    // Add a longer delay and more robust element detection
    setTimeout(() => {
        console.log('Attempting to find status elements...');
        
        // Try multiple approaches to find the dropdown
        let statusElement = document.getElementById('kyc-status-update');
        
        if (!statusElement) {
            console.log('Primary ID not found, trying modal-specific search');
            const modal = document.getElementById('kycModal');
            if (modal) {
                // Try multiple selectors
                statusElement = modal.querySelector('#kyc-status-update') || 
                               modal.querySelector('select[id="kyc-status-update"]') ||
                               modal.querySelector('select.form-select') ||
                               modal.querySelector('select');
                
                console.log('Modal found:', !!modal);
                console.log('Status element found via modal:', !!statusElement);
                
                if (statusElement) {
                    console.log('Status element ID:', statusElement.id);
                    console.log('Status element class:', statusElement.className);
                }
            }
        } else {
            console.log('Primary element found with ID:', statusElement.id);
        }
        
        const commentsElement = document.getElementById('admin-comments-kyc') ||
                               document.querySelector('#kycModal textarea') ||
                               document.querySelector('textarea[id="admin-comments-kyc"]');
        
        console.log('Comments element found:', !!commentsElement);
        
        if (!statusElement) {
            console.error('All attempts to find status dropdown failed');
            console.log('Available elements in modal:');
            const modal = document.getElementById('kycModal');
            if (modal) {
                const allSelects = modal.querySelectorAll('select');
                const allInputs = modal.querySelectorAll('input, select, textarea');
                console.log('Found selects:', allSelects.length);
                console.log('All form elements:', allInputs.length);
                
                allInputs.forEach((el, i) => {
                    console.log(`Element ${i}: ${el.tagName} - ID: ${el.id} - Class: ${el.className}`);
                });
            }
            alert('Status dropdown not found. Please try again.');
            return;
        }
        
        updateKYCStatusWithElements(userId, statusElement, commentsElement);
    }, 300); // Increased delay
}

// Separate function to handle the actual KYC update logic
function updateKYCStatusWithElements(userId, statusElement, commentsElement) {
    
    const newStatus = statusElement.value;
    const adminComments = commentsElement ? commentsElement.value : '';
    
    if (!newStatus || newStatus === '') {
        alert('Please select a status to update.');
        return;
    }
    
    // Show loading state
    const modal = document.getElementById('kycModal');
    const updateBtn = modal ? modal.querySelector('.custom-btn-success') : document.querySelector('.custom-btn-success');
    if (updateBtn) {
        updateBtn.disabled = true;
        updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
    }
    
    fetch(window.affiliate_ajax.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'affiliate_update_kyc_status',
            nonce: window.affiliate_ajax.nonce,
            user_id: userId,
            application_status: newStatus,
            admin_comments: adminComments
        })
    })
    .then(response => response.json())
    .then(data => {
        // Reset button state
        if (updateBtn) {
            updateBtn.disabled = false;
            updateBtn.innerHTML = '<i class="fas fa-save me-2"></i>Update Status';
        }
        
        if (data.success) {
            closeKYCModal();
            loadAdminApplications(); // Reload applications
            alert('KYC status updated successfully');
        } else {
            alert('Failed to update KYC status: ' + (data.data || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error updating KYC status:', error);
        
        // Reset button state on error
        if (updateBtn) {
            updateBtn.disabled = false;
            updateBtn.innerHTML = '<i class="fas fa-save me-2"></i>Update Status';
        }
        
        alert('Error updating KYC status');
    });
}

// Update statistics
function updateStatistics(stats) {
    if (stats) {
        document.getElementById('pending-count').textContent = stats.pending || 0;
        document.getElementById('approved-count').textContent = stats.approved || 0;
        document.getElementById('rejected-count').textContent = stats.rejected || 0;
    }
}

// Search functionality
function performSearch() {
    clearTimeout(window.searchTimeout);
    window.searchTimeout = setTimeout(() => {
        loadAdminApplications();
    }, 500);
}

// View application details
function viewApplication(applicationId) {
    fetch(window.affiliate_ajax.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'affiliate_get_registration_details',
            nonce: window.affiliate_ajax.nonce,
            application_id: applicationId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const app = data.data;
            showApplicationModal(app);
        } else {
            alert('Failed to load application details: ' + (data.data || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error loading application details:', error);
        alert('Error loading application details');
    });
}

// Show Application Modal with custom styling
function showApplicationModal(app) {
    const modalHTML = `
        <div class="custom-modal-overlay" id="applicationModal" onclick="closeModalOnOverlay(event)">
            <div class="custom-modal-container">
                <div class="custom-modal-content">
                    <div class="custom-modal-header">
                        <h2 class="custom-modal-title">
                            <i class="fas fa-user-circle me-2"></i>${app.first_name} ${app.last_name}
                        </h2>
                        <button type="button" class="custom-modal-close" onclick="closeApplicationModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="custom-modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <div class="custom-info-card">
                                        <div class="custom-info-card-header">
                                            <i class="fas fa-user me-2"></i>Personal
                                        </div>
                                        <div class="custom-info-card-body">
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Name:</span>
                                                <span class="custom-info-value">${app.name_prefix || ''} ${app.first_name} ${app.last_name}</span>
                                            </div>
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Email:</span>
                                                <span class="custom-info-value">${app.email}</span>
                                            </div>
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">DOB:</span>
                                                <span class="custom-info-value">${app.date_of_birth || 'N/A'}</span>
                                            </div>
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Type:</span>
                                                <span class="custom-info-value">${app.type || 'N/A'}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <div class="custom-info-card">
                                        <div class="custom-info-card-header">
                                            <i class="fas fa-building me-2"></i>Business
                                        </div>
                                        <div class="custom-info-card-body">
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Company:</span>
                                                <span class="custom-info-value">${app.company_name || 'N/A'}</span>
                                            </div>
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Country:</span>
                                                <span class="custom-info-value">${app.country || 'N/A'}</span>
                                            </div>
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Mobile:</span>
                                                <span class="custom-info-value">${app.country_code || ''} ${app.mobile_number || 'N/A'}</span>
                                            </div>
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Type:</span>
                                                <span class="custom-info-value">${app.affiliate_type || 'N/A'}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <div class="custom-info-card">
                                        <div class="custom-info-card-header">
                                            <i class="fas fa-info-circle me-2"></i>Status
                                        </div>
                                        <div class="custom-info-card-body">
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Status:</span>
                                                <span class="custom-info-value"><strong>${getStatusDisplayName(app.status || 'pending')}</strong></span>
                                            </div>
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Date:</span>
                                                <span class="custom-info-value">${app.registration_date || 'N/A'}</span>
                                            </div>
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Currency:</span>
                                                <span class="custom-info-value">${app.currency || 'N/A'}</span>
                                            </div>
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Chat ID:</span>
                                                <span class="custom-info-value">${app.chat_id_channel || 'N/A'}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <div class="custom-info-card">
                                        <div class="custom-info-card-header">
                                            <i class="fas fa-map-marker-alt me-2"></i>Address
                                        </div>
                                        <div class="custom-info-card-body">
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Address 1:</span>
                                                <span class="custom-info-value">${app.address_line1 || 'N/A'}</span>
                                            </div>
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Address 2:</span>
                                                <span class="custom-info-value">${app.address_line2 || 'N/A'}</span>
                                            </div>
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">City:</span>
                                                <span class="custom-info-value">${app.city || 'N/A'}</span>
                                            </div>
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">ZIP:</span>
                                                <span class="custom-info-value">${app.zipcode || 'N/A'}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <div class="custom-info-card">
                                        <div class="custom-info-card-header">
                                            <i class="fas fa-comment-alt me-2"></i>Remarks
                                        </div>
                                        <div class="custom-info-card-body">
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Security Q:</span>
                                                <span class="custom-info-value">${app.security_que || 'N/A'}</span>
                                            </div>
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Username:</span>
                                                <span class="custom-info-value">${app.username || 'N/A'}</span>
                                            </div>
                                            ${app.admin_remarks ? `
                                            <div class="custom-info-item">
                                                <span class="custom-info-label">Admin Notes:</span>
                                                <span class="custom-info-value">${app.admin_remarks}</span>
                                            </div>
                                            ` : '<div class="custom-info-item"><span class="custom-info-label">Admin Notes:</span><span class="custom-info-value">None</span></div>'}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="custom-modal-footer">
                        <button type="button" class="custom-btn custom-btn-secondary" onclick="closeApplicationModal()">
                            <i class="fas fa-times me-2"></i>Close
                        </button>
                        <button type="button" class="custom-btn custom-btn-primary" onclick="showStatusUpdateModal(${app.id}, '${app.status}', '${app.admin_remarks || ''}')">
                            <i class="fas fa-edit me-2"></i>Update Status
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if present
    const existingModal = document.getElementById('applicationModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Show modal
    const modal = document.getElementById('applicationModal');
    modal.style.display = 'flex';
    
    // Force scrollbar visibility and proper height constraints for application modal
    const modalBody = modal.querySelector('.custom-modal-body');
    if (modalBody) {
        // Force scrollbar to be always visible
        modalBody.style.overflowY = 'scroll';
        modalBody.style.overflowX = 'hidden';
        modalBody.style.height = 'calc(85vh - 140px)';
        modalBody.style.maxHeight = 'calc(85vh - 140px)';
        modalBody.style.minHeight = '300px';
        
        // Force a reflow to ensure scrollbars appear
        modalBody.scrollTop = 0;
        modalBody.scrollTop = 1;
        modalBody.scrollTop = 0;
    }
}

// Close Application Modal
function closeApplicationModal() {
    const modal = document.getElementById('applicationModal');
    if (modal) {
        modal.remove();
    }
}

// Show Status Update Modal
function showStatusUpdateModal(applicationId, currentStatus, currentRemarks) {
    const modalHTML = `
        <div class="custom-modal-overlay" id="statusModal" onclick="closeModalOnOverlay(event)">
            <div class="custom-modal-container" style="max-width: 600px;">
                <div class="custom-modal-content">
                    <div class="custom-modal-header">
                        <h2 class="custom-modal-title">
                            <i class="fas fa-edit me-2"></i>Update Status
                        </h2>
                        <button type="button" class="custom-modal-close" onclick="closeStatusModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="custom-modal-body">
                        <div class="custom-info-card">
                            <div class="custom-info-card-header">
                                <i class="fas fa-cog me-2"></i>Status Management
                            </div>
                            <div class="custom-info-card-body">
                                <div class="form-group-compact">
                                    <label for="status-update-select" class="form-label-compact">Status</label>
                                    <select class="form-control-compact" id="status-update-select">
                                        <option value="">Select Status</option>
                                        <option value="kyc pending" ${currentStatus === 'kyc pending' ? 'selected' : ''}>KYC Pending</option>
                                        <option value="awaiting approval" ${currentStatus === 'awaiting approval' ? 'selected' : ''}>Awaiting Approval</option>
                                        <option value="approved" ${currentStatus === 'approved' ? 'selected' : ''}>Approved</option>
                                        <option value="rejected" ${currentStatus === 'rejected' ? 'selected' : ''}>Rejected</option>
                                        <option value="additional document required" ${currentStatus === 'additional document required' ? 'selected' : ''}>Additional Document Required</option>
                                    </select>
                                </div>
                                <div class="form-group-compact">
                                    <label for="admin-remarks-textarea" class="form-label-compact">Admin Remarks</label>
                                    <textarea class="form-control-compact" id="admin-remarks-textarea" rows="3" placeholder="Add your comments about this application...">${currentRemarks || ''}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="custom-modal-footer">
                        <button type="button" class="custom-btn custom-btn-secondary" onclick="closeStatusModal()">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="button" class="custom-btn custom-btn-success" onclick="updateApplicationStatus(${applicationId})">
                            <i class="fas fa-save me-2"></i>Update Status
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if present
    const existingModal = document.getElementById('statusModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Show modal
    document.getElementById('statusModal').style.display = 'flex';
}

// Close Status Modal
function closeStatusModal() {
    const modal = document.getElementById('statusModal');
    if (modal) {
        modal.remove();
    }
}

// Close modal when clicking on overlay (not on modal content)
function closeModalOnOverlay(event) {
    if (event.target.classList.contains('custom-modal-overlay')) {
        if (event.target.id === 'applicationModal') {
            closeApplicationModal();
        } else if (event.target.id === 'statusModal') {
            closeStatusModal();
        }
    }
}

// Update Application Status (for regular status updates, not KYC)
function updateApplicationStatus(applicationId) {
    const statusElement = document.getElementById('status-update-select');
    const remarksElement = document.getElementById('admin-remarks-textarea');
    
    if (!statusElement) {
        alert('Status dropdown not found. Please try again.');
        return;
    }
    
    const newStatus = statusElement.value;
    const adminRemarks = remarksElement ? remarksElement.value : '';
    
    if (!newStatus || newStatus === '') {
        alert('Please select a status to update.');
        return;
    }
    
    fetch(window.affiliate_ajax.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'affiliate_update_application_status',
            nonce: window.affiliate_ajax.nonce,
            application_id: applicationId,
            status: newStatus,
            admin_remarks: adminRemarks
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeStatusModal();
            closeApplicationModal(); // Close the application modal too
            loadAdminApplications(); // Reload applications
            alert('Application status updated successfully');
        } else {
            alert('Failed to update status: ' + (data.data || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error updating status:', error);
        alert('Error updating status');
    });
}

// Email configuration functions
function showEmailConfig() {
    loadEmailConfig();
    new bootstrap.Modal(document.getElementById('emailModal')).show();
}

function loadEmailConfig() {
    fetch(window.affiliate_ajax.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'affiliate_get_email_config',
            nonce: window.affiliate_ajax.nonce
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const config = data.data;
            document.getElementById('notification_emails').value = config.notification_emails || '';
            document.getElementById('from_email').value = config.from_email || '';
            document.getElementById('from_name').value = config.from_name || '';
        }
    })
    .catch(error => {
        console.error('Error loading email config:', error);
    });
}

function updateEmailConfig() {
    const notificationEmails = document.getElementById('notification_emails').value;
    const fromEmail = document.getElementById('from_email').value;
    const fromName = document.getElementById('from_name').value;
    
    fetch(window.affiliate_ajax.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'affiliate_admin_update_email_config',
            nonce: window.affiliate_ajax.nonce,
            notification_emails: notificationEmails,
            from_email: fromEmail,
            from_name: fromName
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('emailModal')).hide();
            alert('Email configuration updated successfully');
        } else {
            alert('Failed to update email configuration: ' + (data.data || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error updating email config:', error);
        alert('Error updating email configuration');
    });
}

// Admin logout
function adminLogout() {
    if (confirm('Are you sure you want to logout?')) {
        fetch(window.affiliate_ajax.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'affiliate_admin_logout',
                nonce: window.affiliate_ajax.nonce
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.data.redirect || '/admin-login/';
            } else {
                alert('Logout failed: ' + (data.data || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error during logout:', error);
            // Force redirect even if request fails
            window.location.href = '/admin-login/';
        });
    }
}

// Prevent Bootstrap modal backdrop click from closing modals with forms
document.addEventListener('DOMContentLoaded', function() {
    const modals = ['statusModal', 'emailModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('hide.bs.modal', function(e) {
                if (e.target === this && e.relatedTarget && e.relatedTarget.classList.contains('modal-backdrop')) {
                    e.preventDefault();
                }
            });
        }
    });
});
</script>