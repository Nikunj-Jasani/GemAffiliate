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
    
    <!-- Quick Stats -->
    <div style="margin-top: 30px;">
        <div class="alert alert-info">
            <h4><i class="fas fa-info-circle"></i> Welcome to Your Affiliate Portal</h4>
            <p class="mb-0">Your account is set up and ready! In a full implementation, this dashboard would show your referral links, commission tracking, performance analytics, and marketing tools.</p>
        </div>
    </div>
</div>