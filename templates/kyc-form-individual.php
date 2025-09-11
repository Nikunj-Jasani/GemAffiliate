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
    error_log("Individual KYC Form: User not found in database. User ID: " . $user_id);
    echo '<div class="affiliate-error">Unable to load user data. Please try logging out and logging in again.</div>';
    return;
}

// Check if KYC data already exists
$kyc_table = $wpdb->prefix . 'affiliate_kyc';
$existing_kyc = $wpdb->get_row($wpdb->prepare("SELECT * FROM $kyc_table WHERE user_id = %d", $user_id));

// Determine if this is a draft or new form
$is_draft = $existing_kyc && $existing_kyc->kyc_status === 'draft';
$form_data = $existing_kyc ?: new stdClass();

// Set default values from registration if no KYC data exists
if (!$existing_kyc && $current_user) {
    $form_data->full_name = ($current_user->name_prefix ? $current_user->name_prefix . ' ' : '') . 
                           $current_user->first_name . ' ' . $current_user->last_name;
    $form_data->date_of_birth = $current_user->dob;
    $form_data->email = $current_user->email;
    $form_data->mobile_number = $current_user->mobile_number;
    $form_data->affiliate_type = $current_user->affiliate_type;
    $form_data->address_line1 = $current_user->address_line1;
    $form_data->address_line2 = $current_user->address_line2;
    $form_data->city = $current_user->city;
    $form_data->country = $current_user->country;
    $form_data->post_code = $current_user->zipcode;
}
?>

<style>
.kyc-form-container {
    background: rgba(255, 255, 255, 0.98);
    border-radius: 24px;
    padding: 40px;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    margin-top: 40px;
}

.kyc-form-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f8f9fa;
}

.kyc-form-header h2 {
    color: #2c3e50;
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0;
    background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.kyc-account-type-display {
    margin-bottom: 25px;
}

.account-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.95rem;
}

.account-type-badge.individual {
    background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
    color: #1565c0;
    border: 1px solid #bbdefb;
}

.kyc-status-indicator {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    font-weight: 500;
}

.kyc-status-draft {
    background: linear-gradient(135deg, #fff3e0 0%, #fce4ec 100%);
    color: #f57c00;
    border: 1px solid #ffcc02;
}

.kyc-status-pending {
    background: linear-gradient(135deg, #e8f5e8 0%, #f1f8e9 100%);
    color: #388e3c;
    border: 1px solid #c8e6c9;
}

.kyc-progress-indicator {
    margin-bottom: 30px;
}

.kyc-progress-text {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
}

.kyc-progress-bar {
    width: 100%;
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.kyc-progress-fill {
    width: 33%;
    height: 100%;
    background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
    border-radius: 4px;
    transition: width 0.3s ease;
}

.kyc-form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.kyc-form-group {
    display: flex;
    flex-direction: column;
}

.kyc-form-label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.kyc-form-label.required::after {
    content: " *";
    color: #dc3545;
}

.kyc-form-input {
    padding: 14px 16px;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.8);
}

.kyc-form-input:focus {
    outline: none;
    border-color: #0d6efd;
    background: rgba(255, 255, 255, 1);
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
}

.kyc-documents-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 16px;
    padding: 30px;
    margin: 30px 0;
}

.kyc-documents-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.kyc-file-upload {
    position: relative;
    display: inline-block;
    width: 100%;
}

.kyc-file-input {
    width: 100%;
    padding: 16px;
    border: 2px dashed #6c757d;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.8);
    cursor: pointer;
    transition: all 0.3s ease;
}

.kyc-file-input:hover {
    border-color: #0d6efd;
    background: rgba(13, 110, 253, 0.05);
}

.kyc-submit-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 16px;
    padding: 30px;
    text-align: center;
    margin-top: 40px;
}

.kyc-submit-btn {
    background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
    color: white;
    border: none;
    padding: 16px 40px;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
}

.kyc-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(13, 110, 253, 0.4);
}

.kyc-save-draft-btn {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
    border: none;
    padding: 14px 30px;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-right: 15px;
}

.kyc-save-draft-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
}

@media (max-width: 768px) {
    .kyc-form-container {
        padding: 25px;
        margin-top: 20px;
        border-radius: 16px;
    }
    
    .kyc-form-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .kyc-documents-section, .kyc-submit-section {
        padding: 20px;
    }
}
</style>

<div class="kyc-form-container">
    <!-- Header -->
    <div class="kyc-form-header">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="#0d6efd">
            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
        </svg>
        <h2>Individual KYC Verification</h2>
    </div>
    
    <!-- Account Type Display -->
    <div class="kyc-account-type-display">
        <div class="account-type-badge individual">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>
            <span>Account Type: <strong>Individual</strong></span>
        </div>
    </div>

    <!-- Status Indicator -->
    <div class="kyc-status-indicator <?php echo $is_draft ? 'kyc-status-draft' : 'kyc-status-pending'; ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <?php if ($is_draft): ?>
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            <?php else: ?>
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
            <?php endif; ?>
        </svg>
        <span>
            <?php if ($is_draft): ?>
                Draft Saved - Continue your KYC application
            <?php else: ?>
                KYC Verification Required - Please complete the form below
            <?php endif; ?>
        </span>
    </div>

    <!-- Progress Indicator -->
    <div class="kyc-progress-indicator">
        <div class="kyc-progress-text">Individual KYC Application</div>
        <div class="kyc-progress-bar">
            <div class="kyc-progress-fill"></div>
        </div>
    </div>

    <!-- KYC Form -->
    <form id="kycIndividualForm" enctype="multipart/form-data">
        <input type="hidden" name="action" value="affiliate_submit_kyc">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('affiliate_nonce'); ?>">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <input type="hidden" name="account_type" value="Individual">

        <!-- Personal Information Section -->
        <div class="kyc-form-grid">
            <div class="kyc-form-group">
                <label class="kyc-form-label required" for="kyc_full_name">Full Name</label>
                <input type="text" id="kyc_full_name" name="full_name" class="kyc-form-input" 
                       value="<?php echo esc_attr($form_data->full_name ?? ''); ?>" required>
            </div>

            <div class="kyc-form-group">
                <label class="kyc-form-label required" for="kyc_date_of_birth">Date of Birth</label>
                <input type="date" id="kyc_date_of_birth" name="date_of_birth" class="kyc-form-input" 
                       value="<?php echo esc_attr($form_data->date_of_birth ?? ''); ?>" required>
            </div>

            <div class="kyc-form-group">
                <label class="kyc-form-label required" for="kyc_email">Email Address</label>
                <input type="email" id="kyc_email" name="email" class="kyc-form-input" 
                       value="<?php echo esc_attr($form_data->email ?? ''); ?>" required>
            </div>

            <div class="kyc-form-group">
                <label class="kyc-form-label required" for="kyc_nationality">Nationality</label>
                <input type="text" id="kyc_nationality" name="nationality" class="kyc-form-input" 
                       value="<?php echo esc_attr($form_data->nationality ?? ''); ?>" 
                       placeholder="e.g., American, British, Indian" required>
            </div>

            <div class="kyc-form-group">
                <label class="kyc-form-label required" for="kyc_mobile_number">Mobile Number</label>
                <input type="tel" id="kyc_mobile_number" name="mobile_number" class="kyc-form-input" 
                       value="<?php echo esc_attr($form_data->mobile_number ?? ''); ?>" 
                       placeholder="+1 234 567 8900" required>
            </div>

            <div class="kyc-form-group">
                <label class="kyc-form-label required" for="kyc_affiliate_type">Affiliate Type</label>
                <select id="kyc_affiliate_type" name="affiliate_type" class="kyc-form-input" required>
                    <option value="">Select Affiliate Type</option>
                    <option value="Influencer" <?php selected($form_data->affiliate_type ?? '', 'Influencer'); ?>>Influencer</option>
                    <option value="Content Creator" <?php selected($form_data->affiliate_type ?? '', 'Content Creator'); ?>>Content Creator</option>
                    <option value="Blogger" <?php selected($form_data->affiliate_type ?? '', 'Blogger'); ?>>Blogger</option>
                    <option value="Website Owner" <?php selected($form_data->affiliate_type ?? '', 'Website Owner'); ?>>Website Owner</option>
                    <option value="Social Media Marketer" <?php selected($form_data->affiliate_type ?? '', 'Social Media Marketer'); ?>>Social Media Marketer</option>
                    <option value="Email Marketer" <?php selected($form_data->affiliate_type ?? '', 'Email Marketer'); ?>>Email Marketer</option>
                    <option value="Other" <?php selected($form_data->affiliate_type ?? '', 'Other'); ?>>Other</option>
                </select>
            </div>
        </div>

        <!-- Affiliate URLs Section -->
        <div class="kyc-form-group" style="grid-column: 1 / -1;">
            <label class="kyc-form-label" for="kyc_affiliate_urls">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="vertical-align: middle; margin-right: 8px;">
                    <path d="M16.36,14C16.44,13.34 16.5,12.68 16.5,12C16.5,11.32 16.44,10.66 16.36,10H19.74C19.9,10.64 20,11.31 20,12C20,12.69 19.9,13.36 19.74,14M14.59,19.56C15.19,18.45 15.65,17.25 15.97,16H18.92C17.96,17.65 16.43,18.93 14.59,19.56M14.34,14H9.66C9.56,13.34 9.5,12.68 9.5,12C9.5,11.32 9.56,10.65 9.66,10H14.34C14.43,10.65 14.5,11.32 14.5,12C14.5,12.68 14.43,13.34 14.34,14M12,19.96C11.17,18.76 10.5,17.43 10.09,16H13.91C13.5,17.43 12.83,18.76 12,19.96M8,8H5.08C6.03,6.34 7.57,5.06 9.4,4.44C8.8,5.55 8.35,6.75 8,8M5.08,16H8C8.35,17.25 8.8,18.45 9.4,19.56C7.57,18.93 6.03,17.65 5.08,16M4.26,14C4.1,13.36 4,12.69 4,12C4,11.31 4.1,10.64 4.26,10H7.64C7.56,10.66 7.5,11.32 7.5,12C7.5,12.68 7.56,13.34 7.64,14M12,4.03C12.83,5.23 13.5,6.57 13.91,8H10.09C10.5,6.57 11.17,5.23 12,4.03M18.92,8H15.97C15.65,6.75 15.19,5.55 14.59,4.44C16.43,5.07 17.96,6.34 18.92,8Z"/>
                </svg>
                Website URLs for Affiliate Application
            </label>
            <textarea id="kyc_affiliate_urls" name="affiliate_urls" class="kyc-form-input" 
                      style="min-height: 120px; resize: vertical; font-family: 'Courier New', monospace;" 
                      placeholder="Enter your website URLs (one per line):&#10;https://example.com&#10;https://blog.example.com&#10;https://social-media-profile.com"><?php echo esc_textarea($form_data->affiliate_urls ?? ''); ?></textarea>
            <small style="color: #6c757d; font-size: 0.9rem; margin-top: 8px; display: block;">
                📝 List all websites, blogs, social media profiles, or platforms where you plan to promote affiliate content. This helps us understand your promotional channels.
            </small>
        </div>

        <!-- Address Information -->
        <div class="kyc-form-grid">
            <div class="kyc-form-group">
                <label class="kyc-form-label required" for="kyc_address_line1">Address Line 1</label>
                <input type="text" id="kyc_address_line1" name="address_line1" class="kyc-form-input" 
                       value="<?php echo esc_attr($form_data->address_line1 ?? ''); ?>" 
                       placeholder="Street address, P.O. box, company name, c/o" required>
            </div>

            <div class="kyc-form-group">
                <label class="kyc-form-label" for="kyc_address_line2">Address Line 2</label>
                <input type="text" id="kyc_address_line2" name="address_line2" class="kyc-form-input" 
                       value="<?php echo esc_attr($form_data->address_line2 ?? ''); ?>" 
                       placeholder="Apartment, suite, unit, building, floor, etc.">
            </div>

            <div class="kyc-form-group">
                <label class="kyc-form-label required" for="kyc_city">City</label>
                <input type="text" id="kyc_city" name="city" class="kyc-form-input" 
                       value="<?php echo esc_attr($form_data->city ?? ''); ?>" required>
            </div>

            <div class="kyc-form-group">
                <label class="kyc-form-label required" for="kyc_country">Country</label>
                <input type="text" id="kyc_country" name="country" class="kyc-form-input" 
                       value="<?php echo esc_attr($form_data->country ?? ''); ?>" required>
            </div>

            <div class="kyc-form-group">
                <label class="kyc-form-label required" for="kyc_post_code">Postal Code</label>
                <input type="text" id="kyc_post_code" name="post_code" class="kyc-form-input" 
                       value="<?php echo esc_attr($form_data->post_code ?? ''); ?>" required>
            </div>
        </div>

        <!-- Documents Section -->
        <div class="kyc-documents-section">
            <h3 class="kyc-documents-title">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14,2 14,8 20,8"/>
                </svg>
                Required Documents
            </h3>
            
            <div class="kyc-form-grid">
                <!-- Identity Document -->
                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="kyc_identity_document_type">Identity Document Type</label>
                    <select id="kyc_identity_document_type" name="identity_document_type" class="kyc-form-input" required>
                        <option value="">Select Document Type</option>
                        <option value="Passport" <?php selected($form_data->identity_document_type ?? '', 'Passport'); ?>>Passport</option>
                        <option value="National ID" <?php selected($form_data->identity_document_type ?? '', 'National ID'); ?>>National ID</option>
                        <option value="Driver's License" <?php selected($form_data->identity_document_type ?? '', 'Driver\'s License'); ?>>Driver's License</option>
                    </select>
                </div>

                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="kyc_identity_document_number">Document Number</label>
                    <input type="text" id="kyc_identity_document_number" name="identity_document_number" class="kyc-form-input" 
                           value="<?php echo esc_attr($form_data->identity_document_number ?? ''); ?>" required>
                </div>

                <div class="kyc-form-group">
                    <label class="kyc-form-label" for="kyc_identity_document_expiry">Document Expiry Date</label>
                    <input type="date" id="kyc_identity_document_expiry" name="identity_document_expiry" class="kyc-form-input" 
                           value="<?php echo esc_attr($form_data->identity_document_expiry ?? ''); ?>">
                </div>

                <!-- Document Uploads -->
                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="kyc_identity_document">Identity Document Upload</label>
                    <input type="file" id="kyc_identity_document" name="identity_document" class="kyc-file-input" 
                           accept=".jpg,.jpeg,.png,.pdf" required>
                    <small style="color: #6c757d;">Upload a clear photo or scan of your identity document (JPG, PNG, PDF)</small>
                </div>

                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="kyc_address_proof">Address Proof</label>
                    <select id="kyc_address_proof_type" name="address_proof_type" class="kyc-form-input" style="margin-bottom: 10px;" required>
                        <option value="">Select Address Proof Type</option>
                        <option value="Utility Bill" <?php selected($form_data->address_proof_type ?? '', 'Utility Bill'); ?>>Utility Bill</option>
                        <option value="Bank Statement" <?php selected($form_data->address_proof_type ?? '', 'Bank Statement'); ?>>Bank Statement</option>
                        <option value="Government Letter" <?php selected($form_data->address_proof_type ?? '', 'Government Letter'); ?>>Government Letter</option>
                        <option value="Lease Agreement" <?php selected($form_data->address_proof_type ?? '', 'Lease Agreement'); ?>>Lease Agreement</option>
                    </select>
                    <input type="file" id="kyc_address_proof" name="address_proof" class="kyc-file-input" 
                           accept=".jpg,.jpeg,.png,.pdf" required>
                    <small style="color: #6c757d;">Upload recent address proof not older than 3 months (JPG, PNG, PDF)</small>
                </div>

                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="kyc_selfie">Selfie Verification</label>
                    <input type="file" id="kyc_selfie" name="selfie" class="kyc-file-input" 
                           accept=".jpg,.jpeg,.png" required>
                    <small style="color: #6c757d;">Upload a clear selfie holding your identity document (JPG, PNG)</small>
                </div>
            </div>
        </div>

        <!-- Submit Section -->
        <div class="kyc-submit-section">
            <div style="margin-bottom: 20px;">
                <button type="button" id="saveDraftBtn" class="kyc-save-draft-btn">Save as Draft</button>
                <button type="submit" id="submitKycBtn" class="kyc-submit-btn">Submit for Verification</button>
            </div>
            <p style="font-size: 0.9rem; color: #6c757d; margin: 0;">
                By submitting this form, you confirm that all information provided is accurate and agree to our KYC verification process.
            </p>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('kycIndividualForm');
    const submitBtn = document.getElementById('submitKycBtn');
    const saveDraftBtn = document.getElementById('saveDraftBtn');
    
    // Save as draft functionality
    saveDraftBtn.addEventListener('click', function() {
        const formData = new FormData(form);
        formData.set('action', 'affiliate_save_kyc_draft');
        
        fetch(affiliate_ajax.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Draft saved successfully!');
            } else {
                alert('Failed to save draft: ' + data.data);
            }
        })
        .catch(error => {
            console.error('Error saving draft:', error);
            alert('Error saving draft');
        });
    });
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';
        
        const formData = new FormData(form);
        
        fetch(affiliate_ajax.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('KYC application submitted successfully!');
                // Redirect to dashboard to show updated status
                const dashboardUrl = window.location.origin + window.location.pathname.replace(/\/affiliate-kyc.*/, '/affiliate-dashboard');
                window.location.href = dashboardUrl;
            } else {
                alert('Failed to submit KYC: ' + data.data);
            }
        })
        .catch(error => {
            console.error('Error submitting KYC:', error);
            alert('Error submitting KYC application');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit for Verification';
        });
    });
});
</script>