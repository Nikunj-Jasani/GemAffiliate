<div class="affiliate-portal-container">
    <div class="affiliate-auth-grid">
        <!-- Left Column - Company Branding -->
        <div class="affiliate-brand-column">
            <div class="affiliate-brand-content">
                <div class="affiliate-logo-container">
                    <?php 
                    $custom_logo = get_option('affiliate_portal_logo', '');
                    if ($custom_logo): ?>
                        <img src="<?php echo esc_url($custom_logo); ?>" alt="Company Logo" class="affiliate-custom-logo" style="max-width: 80px; height: auto;">
                    <?php else: ?>
                        <svg class="affiliate-logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="80" height="80">
                            <defs>
                                <linearGradient id="logoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#0d6efd;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#6610f2;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                            <circle cx="50" cy="50" r="45" fill="url(#logoGradient)" stroke="white" stroke-width="2"/>
                            <path d="M30 75 L50 25 L70 75 M37 60 L63 60" 
                                  stroke="white" 
                                  stroke-width="4" 
                                  stroke-linecap="round" 
                                  stroke-linejoin="round" 
                                  fill="none"/>
                            <circle cx="25" cy="25" r="2" fill="white" opacity="0.8"/>
                            <circle cx="75" cy="25" r="2" fill="white" opacity="0.8"/>
                            <circle cx="85" cy="65" r="1.5" fill="white" opacity="0.6"/>
                            <line x1="15" y1="40" x2="25" y2="35" stroke="white" stroke-width="1.5" opacity="0.7"/>
                            <line x1="75" y1="35" x2="85" y2="40" stroke="white" stroke-width="1.5" opacity="0.7"/>
                            <line x1="20" y1="70" x2="30" y2="65" stroke="white" stroke-width="1.5" opacity="0.7"/>
                        </svg>
                    <?php endif; ?>
                </div>
                <?php
                // Get custom brand settings
                $brand_title = get_option('affiliate_portal_brand_title', 'Welcome Back');
                $brand_slogan = get_option('affiliate_portal_brand_slogan', 'Your gateway to success in affiliate marketing');
                ?>
                <h1 class="affiliate-brand-title"><?php echo esc_html($brand_title); ?></h1>
                <p class="affiliate-brand-slogan"><?php echo esc_html($brand_slogan); ?></p>
                <div class="affiliate-brand-features">
                    <div class="affiliate-feature-item">
                        <svg class="affiliate-feature-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                        </svg>
                        <span>Real-time Analytics</span>
                    </div>
                    <div class="affiliate-feature-item">
                        <svg class="affiliate-feature-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,7C13.4,7 14.8,8.6 14.8,10V14H16V22H8V14H9.2V10C9.2,8.6 10.6,7 12,7M12,8.2C11.2,8.2 10.4,8.7 10.4,10V14H13.6V10C13.6,8.7 12.8,8.2 12,8.2Z"/>
                        </svg>
                        <span>Secure Platform</span>
                    </div>
                    <div class="affiliate-feature-item">
                        <svg class="affiliate-feature-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7,15H9C9,16.08 10.37,17 12,17C13.63,17 15,16.08 15,15C15,13.9 13.96,13.5 11.76,12.97C9.64,12.44 7,11.78 7,9C7,7.21 8.47,5.69 10.5,5.18V3H13.5V5.18C15.53,5.69 17,7.21 17,9H15C15,7.92 13.63,7 12,7C10.37,7 9,7.92 9,9C9,10.1 10.04,10.5 12.24,11.03C14.36,11.56 17,12.22 17,15C17,16.79 15.53,18.31 13.5,18.82V21H10.5V18.82C8.47,18.31 7,16.79 7,15Z"/>
                        </svg>
                        <span>High Commission Rates</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column - Login Form -->
        <div class="affiliate-form-column">
            <div class="affiliate-form-container">
                <div class="affiliate-form-header">
                    <h2>Sign In</h2>
                    <p>Enter your credentials to access your account</p>
                </div>
                
                <form class="affiliate-form" id="affiliateLoginForm">
                    <div class="affiliate-form-group">
                        <label for="affiliate_username" class="affiliate-form-label">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                            </svg>
                            Username or Email
                        </label>
                        <input type="text" id="affiliate_username" name="username" class="affiliate-form-control" required>
                    </div>
                    
                    <div class="affiliate-form-group">
                        <label for="affiliate_password" class="affiliate-form-label">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/>
                            </svg>
                            Password
                        </label>
                        <input type="password" id="affiliate_password" name="password" class="affiliate-form-control" required>
                    </div>
                    
                    <div class="affiliate-form-options">
                        <div class="affiliate-form-check">
                            <input type="checkbox" id="remember_me" name="remember_me" class="affiliate-form-check-input">
                            <label for="remember_me" class="affiliate-form-check-label">Remember me</label>
                        </div>
                        <a href="#" class="affiliate-forgot-link">Forgot password?</a>
                    </div>
                    
                    <button type="submit" class="affiliate-btn affiliate-btn-primary affiliate-btn-auth">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M10,17V14H3V10H10V7L15,12L10,17M10,2H19A2,2 0 0,1 21,4V20A2,2 0 0,1 19,22H10A2,2 0 0,1 8,20V18H10V20H19V4H10V6H8V4A2,2 0 0,1 10,2Z"/>
                        </svg>
                        Sign In
                    </button>
                </form>
                
                <div class="affiliate-auth-footer">
                    <div class="affiliate-auth-links">
                        <p>Don't have an account? <a href="<?php echo home_url('/affiliate-register/'); ?>" class="affiliate-auth-link">Sign up here</a></p>
                    </div>
                    <div class="affiliate-home-redirect">
                        <a href="<?php echo home_url('/'); ?>" class="affiliate-btn affiliate-btn-outline affiliate-btn-sm">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z"/>
                            </svg>
                            Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load script with proper PHP variables
window.affiliate_ajax = {
    ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>',
    nonce: '<?php echo wp_create_nonce('affiliate_nonce'); ?>'
};
</script>
<script src="<?php echo plugin_dir_url(__FILE__) . '../assets/script.js'; ?>?v=<?php echo time(); ?>"></script>