<?php
// Get current user data safely
global $affiliate_portal_instance;
if (!$affiliate_portal_instance) {
    $affiliate_portal_instance = new AffiliatePortal();
}

$current_user = $affiliate_portal_instance->get_current_affiliate_user();

if (!$current_user) {
    echo '<div class="affiliate-alert affiliate-alert-danger">Unable to load user data. Please try logging in again.</div>';
    return;
}
?>

<div class="affiliate-dashboard">
    <div class="affiliate-dashboard-nav">
        <div>
            <h2>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px;">
                    <path d="M3,13H11V3H3M3,21H11V15H3M13,21H21V11H13M13,3V9H21V3"/>
                </svg>
                Affiliate Dashboard
            </h2>
            <p class="mb-0">Welcome back, <?php echo esc_html($current_user->first_name . ' ' . $current_user->last_name); ?>!</p>
        </div>
        <div>
            <a href="<?php echo home_url('/?affiliate_logout=1'); ?>" class="affiliate-btn affiliate-btn-secondary" style="color: white; text-decoration: none;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 5px;">
                    <path d="M14.08,15.59L16.67,13H7V11H16.67L14.08,8.41L15.49,7L20.49,12L15.49,17L14.08,15.59M19,3A2,2 0 0,1 21,5V9.67L19,7.67V5H5V19H19V16.33L21,14.33V19A2,2 0 0,1 19,21H5C3.89,21 3,20.1 3,19V5C3,3.89 3.89,3 5,3H19Z"/>
                </svg>
                Logout
            </a>
        </div>
    </div>
    
    <div class="affiliate-dashboard-content">
        <!-- Account Information -->
        <div class="affiliate-info-card">
            <h3>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px;">
                    <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                </svg>
                Account Information
            </h3>
            <div class="affiliate-info-item">
                <span class="affiliate-info-label">Username:</span>
                <span class="affiliate-info-value"><?php echo esc_html($current_user->username); ?></span>
            </div>
            <div class="affiliate-info-item">
                <span class="affiliate-info-label">Email:</span>
                <span class="affiliate-info-value"><?php echo esc_html($current_user->email); ?></span>
            </div>
            <div class="affiliate-info-item">
                <span class="affiliate-info-label">Full Name:</span>
                <span class="affiliate-info-value"><?php echo esc_html(($current_user->name_prefix ? $current_user->name_prefix . ' ' : '') . $current_user->first_name . ' ' . $current_user->last_name); ?></span>
            </div>
            <?php if ($current_user->company_name): ?>
            <div class="affiliate-info-item">
                <span class="affiliate-info-label">Company:</span>
                <span class="affiliate-info-value"><?php echo esc_html($current_user->company_name); ?></span>
            </div>
            <?php endif; ?>
            <div class="affiliate-info-item">
                <span class="affiliate-info-label">Member Since:</span>
                <span class="affiliate-info-value"><?php echo date('F j, Y', strtotime($current_user->created_at)); ?></span>
            </div>
            <?php if ($current_user->last_login): ?>
            <div class="affiliate-info-item">
                <span class="affiliate-info-label">Last Login:</span>
                <span class="affiliate-info-value"><?php echo date('M j, Y g:i A', strtotime($current_user->last_login)); ?></span>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Business Information -->
        <div class="affiliate-info-card">
            <h3>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px;">
                    <path d="M10,2H14A2,2 0 0,1 16,4V6H20A2,2 0 0,1 22,8V19A2,2 0 0,1 20,21H4C2.89,21 2,20.1 2,19V8A2,2 0 0,1 4,6H8V4C8,2.89 8.89,2 10,2M14,6V4H10V6H14Z"/>
                </svg>
                Business Details
            </h3>
            <?php if ($current_user->affiliate_type): ?>
            <div class="affiliate-info-item">
                <span class="affiliate-info-label">Affiliate Type:</span>
                <span class="affiliate-info-value"><?php echo esc_html($current_user->affiliate_type); ?></span>
            </div>
            <?php endif; ?>
            <?php if ($current_user->account_type): ?>
            <div class="affiliate-info-item">
                <span class="affiliate-info-label">Account Type:</span>
                <span class="affiliate-info-value"><?php echo esc_html($current_user->account_type); ?></span>
            </div>
            <?php endif; ?>
            <?php if ($current_user->currency): ?>
            <div class="affiliate-info-item">
                <span class="affiliate-info-label">Preferred Currency:</span>
                <span class="affiliate-info-value"><?php echo esc_html($current_user->currency); ?></span>
            </div>
            <?php endif; ?>
            <?php if ($current_user->mobile_number): ?>
            <div class="affiliate-info-item">
                <span class="affiliate-info-label">Mobile:</span>
                <span class="affiliate-info-value"><?php echo esc_html(($current_user->country_code ? $current_user->country_code . ' ' : '') . $current_user->mobile_number); ?></span>
            </div>
            <?php endif; ?>
            <div class="affiliate-info-item">
                <span class="affiliate-info-label">Status:</span>
                <span class="affiliate-info-value">
                    <span class="badge bg-success" style="background-color: #28a745; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8em;">
                        <?php echo esc_html(ucfirst($current_user->status)); ?>
                    </span>
                </span>
            </div>
        </div>
    </div>
    
    <!-- Address Information (if available) -->
    <?php if ($current_user->address_line1 || $current_user->city || $current_user->country): ?>
    <div style="margin-top: 30px;">
        <div class="affiliate-info-card">
            <h3><i class="fas fa-map-marker-alt"></i> Address Information</h3>
            <?php if ($current_user->address_line1): ?>
            <div class="affiliate-info-item">
                <span class="affiliate-info-label">Address:</span>
                <span class="affiliate-info-value">
                    <?php echo esc_html($current_user->address_line1); ?>
                    <?php if ($current_user->address_line2): ?>
                        <br><?php echo esc_html($current_user->address_line2); ?>
                    <?php endif; ?>
                </span>
            </div>
            <?php endif; ?>
            <?php if ($current_user->city): ?>
            <div class="affiliate-info-item">
                <span class="affiliate-info-label">City:</span>
                <span class="affiliate-info-value"><?php echo esc_html($current_user->city); ?></span>
            </div>
            <?php endif; ?>
            <?php if ($current_user->country): ?>
            <div class="affiliate-info-item">
                <span class="affiliate-info-label">Country:</span>
                <span class="affiliate-info-value"><?php echo esc_html($current_user->country); ?></span>
            </div>
            <?php endif; ?>
            <?php if ($current_user->zipcode): ?>
            <div class="affiliate-info-item">
                <span class="affiliate-info-label">ZIP Code:</span>
                <span class="affiliate-info-value"><?php echo esc_html($current_user->zipcode); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- KYC Status Section -->
    <?php
    // Check KYC status for current user - only show if user data is available
    if ($current_user && isset($current_user->id)) {
        global $wpdb;
        $kyc_table = $wpdb->prefix . 'affiliate_kyc';
        $kyc_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $kyc_table WHERE user_id = %d", $current_user->id));
        
        $kyc_status = $kyc_data ? $kyc_data->kyc_status : 'not_started';
        $account_type = strtolower(trim($current_user->type ?? ''));
        $is_individual = $account_type !== 'company';
    ?>
    <div style="margin-top: 30px;">
        <div class="affiliate-info-card">
            <h3>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px;">
                    <path d="M9,12L11,14L15,10M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4Z"/>
                </svg>
                KYC Verification Status
            </h3>
            <div class="affiliate-info-item">
                <span class="affiliate-info-label">Account Type:</span>
                <span class="affiliate-info-value">
                    <span class="badge" style="background-color: <?php echo $is_individual ? '#17a2b8' : '#fd7e14'; ?>; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8em;">
                        <?php echo $is_individual ? 'Individual' : 'Company'; ?>
                    </span>
                </span>
            </div>
            <div class="affiliate-info-item">
                <span class="affiliate-info-label">KYC Status:</span>
                <span class="affiliate-info-value">
                    <?php
                    $status_colors = array(
                        'not_started' => '#6c757d',
                        'draft' => '#ffc107',
                        'awaiting approval' => '#007bff',
                        'approved' => '#28a745',
                        'rejected' => '#dc3545'
                    );
                    $status_color = isset($status_colors[$kyc_status]) ? $status_colors[$kyc_status] : '#6c757d';
                    ?>
                    <span class="badge" style="background-color: <?php echo $status_color; ?>; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8em;">
                        <?php echo esc_html(ucwords(str_replace('_', ' ', $kyc_status))); ?>
                    </span>
                </span>
            </div>
            
            <!-- Admin Comments Section -->
            <?php if (!empty($kyc_data->admin_comments)): ?>
            <div class="affiliate-info-item" style="grid-column: 1 / -1; margin-top: 15px;">
                <span class="affiliate-info-label">Admin Comments:</span>
                <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; padding: 12px; margin-top: 5px;">
                    <strong style="color: #856404;">Review Comments:</strong>
                    <p style="margin: 8px 0 0 0; color: #856404; white-space: pre-wrap;"><?php echo esc_html($kyc_data->admin_comments); ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($kyc_status === 'not_started' || $kyc_status === 'draft'): ?>
            <div style="margin-top: 15px;">
                <a href="<?php echo home_url('/affiliate-kyc/'); ?>" class="affiliate-btn affiliate-btn-primary" style="text-decoration: none; display: inline-block;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 5px;">
                        <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z"/>
                    </svg>
                    <?php echo $kyc_status === 'draft' ? 'Continue KYC Application' : 'Start KYC Verification'; ?>
                </a>
            </div>
            <?php elseif ($kyc_status === 'awaiting approval'): ?>
            <div style="margin-top: 15px; padding: 10px; background-color: #e7f3ff; border-left: 4px solid #007bff; border-radius: 4px;">
                <p style="margin: 0; color: #004085;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 5px; vertical-align: middle;">
                        <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M11,16.5L6.5,12L7.91,10.59L11,13.67L16.59,8.09L18,9.5L11,16.5Z"/>
                    </svg>
                    Your <?php echo $is_individual ? 'Individual' : 'Company'; ?> KYC application is under review. We'll notify you once it's processed.
                </p>
            </div>
            <?php elseif ($kyc_status === 'approved'): ?>
            <div style="margin-top: 15px; padding: 10px; background-color: #d4edda; border-left: 4px solid #28a745; border-radius: 4px;">
                <p style="margin: 0; color: #155724;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 5px; vertical-align: middle;">
                        <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M11,16.5L6.5,12L7.91,10.59L11,13.67L16.59,8.09L18,9.5L11,16.5Z"/>
                    </svg>
                    Congratulations! Your KYC verification has been approved. Your account is fully verified.
                </p>
            </div>
            <?php elseif ($kyc_status === 'rejected'): ?>
            <div style="margin-top: 15px; padding: 10px; background-color: #f8d7da; border-left: 4px solid #dc3545; border-radius: 4px;">
                <p style="margin: 0; color: #721c24;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 5px; vertical-align: middle;">
                        <path d="M12,2C17.53,2 22,6.47 22,12C22,17.53 17.53,22 12,22C6.47,22 2,17.53 2,12C2,6.47 6.47,2 12,2M15.59,7L12,10.59L8.41,7L7,8.41L10.59,12L7,15.59L8.41,17L12,13.41L15.59,17L17,15.59L13.41,12L17,8.41L15.59,7Z"/>
                    </svg>
                    Your KYC application was rejected. Please contact support for more information.
                </p>
                <?php if ($kyc_data && $kyc_data->admin_notes): ?>
                <p style="margin: 10px 0 0 0; font-size: 0.9em; color: #721c24;">
                    <strong>Admin Notes:</strong> <?php echo esc_html($kyc_data->admin_notes); ?>
                </p>
                <?php endif; ?>
                <div style="margin-top: 10px;">
                    <a href="<?php echo home_url('/affiliate-kyc/'); ?>" class="affiliate-btn affiliate-btn-primary" style="text-decoration: none; display: inline-block; font-size: 0.9em;">
                        Resubmit KYC Application
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php } // End KYC section if user data available ?>

    <!-- Quick Stats -->
    <div style="margin-top: 30px;">
        <div class="alert alert-info">
            <h4><i class="fas fa-info-circle"></i> Welcome to Your Affiliate Portal</h4>
            <p class="mb-0">Your account is set up and ready! In a full implementation, this dashboard would show your referral links, commission tracking, performance analytics, and marketing tools.</p>
        </div>
    </div>
</div>