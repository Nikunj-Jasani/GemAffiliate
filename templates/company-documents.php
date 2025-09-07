<?php
// Company Documents Upload Form
$current_user = $GLOBALS['affiliate_portal']->get_current_user();

if (!$current_user) {
    echo '<div class="affiliate-alert affiliate-alert-warning">Please log in to access this page.</div>';
    return;
}

// Get user data from database
global $wpdb;
$table_name = $wpdb->prefix . 'affiliate_users';
$user_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE username = %s", $current_user->username));

if (!$user_data || $user_data->type !== 'company') {
    echo '<div class="affiliate-alert affiliate-alert-warning">This form is only available for company accounts.</div>';
    return;
}
?>

<div class="affiliate-document-upload-container">
    <div class="affiliate-document-header">
        <h2>Company Due Diligence Documents</h2>
        <p>Please upload the required documents to complete your company registration</p>
    </div>

    <form id="companyDocumentsForm" class="affiliate-document-form" enctype="multipart/form-data">
        <input type="hidden" name="action" value="affiliate_upload_company_docs">
        <input type="hidden" name="user_id" value="<?php echo esc_attr($user_data->id); ?>">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('affiliate_nonce'); ?>">

        <!-- Business Registration Documents -->
        <div class="affiliate-document-section">
            <h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px;">
                    <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                </svg>
                Business Registration Documents
            </h3>
            
            <div class="affiliate-form-group">
                <label for="business_license">Business License / Certificate of Incorporation *</label>
                <input type="file" id="business_license" name="business_license" required accept=".pdf,.jpg,.jpeg,.png">
                <small class="affiliate-form-help">Upload your official business registration document</small>
            </div>

            <div class="affiliate-form-group">
                <label for="tax_certificate">Tax Registration Certificate *</label>
                <input type="file" id="tax_certificate" name="tax_certificate" required accept=".pdf,.jpg,.jpeg,.png">
                <small class="affiliate-form-help">Tax ID or VAT registration certificate</small>
            </div>

            <div class="affiliate-form-group">
                <label for="trade_license">Trade License (if applicable)</label>
                <input type="file" id="trade_license" name="trade_license" accept=".pdf,.jpg,.jpeg,.png">
                <small class="affiliate-form-help">Required for certain business types</small>
            </div>
        </div>

        <!-- Financial Documents -->
        <div class="affiliate-document-section">
            <h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px;">
                    <path d="M7,15H9C9,16.08 10.37,17 12,17C13.63,17 15,16.08 15,15C15,13.9 13.96,13.5 11.76,12.97C9.64,12.44 7,11.78 7,9C7,7.21 8.47,5.69 10.5,5.18V3H13.5V5.18C15.53,5.69 17,7.21 17,9H15C15,7.92 13.63,7 12,7C10.37,7 9,7.92 9,9C9,10.1 10.04,10.5 12.24,11.03C14.36,11.56 17,12.22 17,15C17,16.79 15.53,18.31 13.5,18.82V21H10.5V18.82C8.47,18.31 7,16.79 7,15Z"/>
                </svg>
                Financial Documents
            </h3>

            <div class="affiliate-form-group">
                <label for="bank_statement">Bank Statement (Last 3 months) *</label>
                <input type="file" id="bank_statement" name="bank_statement" required accept=".pdf,.jpg,.jpeg,.png">
                <small class="affiliate-form-help">Recent bank statements showing business activity</small>
            </div>

            <div class="affiliate-form-group">
                <label for="financial_statements">Financial Statements (Last 2 years)</label>
                <input type="file" id="financial_statements" name="financial_statements" accept=".pdf,.jpg,.jpeg,.png">
                <small class="affiliate-form-help">Audited financial statements or tax returns</small>
            </div>
        </div>

        <!-- Identity & Authorization -->
        <div class="affiliate-document-section">
            <h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px;">
                    <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                </svg>
                Identity & Authorization Documents
            </h3>

            <div class="affiliate-form-group">
                <label for="director_id">Director/Owner ID Copy *</label>
                <input type="file" id="director_id" name="director_id" required accept=".pdf,.jpg,.jpeg,.png">
                <small class="affiliate-form-help">Government-issued ID of company director/owner</small>
            </div>

            <div class="affiliate-form-group">
                <label for="authorization_letter">Authorization Letter</label>
                <input type="file" id="authorization_letter" name="authorization_letter" accept=".pdf,.jpg,.jpeg,.png">
                <small class="affiliate-form-help">If submitting on behalf of another person</small>
            </div>

            <div class="affiliate-form-group">
                <label for="memorandum_articles">Memorandum & Articles of Association</label>
                <input type="file" id="memorandum_articles" name="memorandum_articles" accept=".pdf,.jpg,.jpeg,.png">
                <small class="affiliate-form-help">Company constitution documents</small>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="affiliate-document-section">
            <h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px;">
                    <path d="M13,9H11V7H13M13,17H11V11H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
                </svg>
                Additional Information
            </h3>

            <div class="affiliate-form-group">
                <label for="business_description">Business Description *</label>
                <textarea id="business_description" name="business_description" rows="4" required placeholder="Describe your business activities, products/services, target markets..."></textarea>
            </div>

            <div class="affiliate-form-group">
                <label for="expected_volume">Expected Monthly Transaction Volume</label>
                <select id="expected_volume" name="expected_volume">
                    <option value="">Select volume range</option>
                    <option value="0-10k">$0 - $10,000</option>
                    <option value="10k-50k">$10,000 - $50,000</option>
                    <option value="50k-100k">$50,000 - $100,000</option>
                    <option value="100k-500k">$100,000 - $500,000</option>
                    <option value="500k+">$500,000+</option>
                </select>
            </div>

            <div class="affiliate-form-group">
                <label for="additional_notes">Additional Notes</label>
                <textarea id="additional_notes" name="additional_notes" rows="3" placeholder="Any additional information you'd like to provide..."></textarea>
            </div>
        </div>

        <!-- Terms & Conditions -->
        <div class="affiliate-document-section">
            <div class="affiliate-form-group">
                <label class="affiliate-checkbox-label">
                    <input type="checkbox" id="terms_agreement" name="terms_agreement" required>
                    <span class="affiliate-checkmark"></span>
                    I confirm that all information provided is accurate and I agree to the terms and conditions *
                </label>
            </div>

            <div class="affiliate-form-group">
                <label class="affiliate-checkbox-label">
                    <input type="checkbox" id="document_authenticity" name="document_authenticity" required>
                    <span class="affiliate-checkmark"></span>
                    I certify that all uploaded documents are authentic and unmodified *
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="affiliate-form-actions">
            <button type="button" class="affiliate-btn affiliate-btn-secondary" onclick="history.back()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 5px;">
                    <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
                </svg>
                Back to Dashboard
            </button>
            <button type="submit" class="affiliate-btn affiliate-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 5px;">
                    <path d="M9,16.17L4.83,12L3.41,13.41L9,19L21,7L19.59,5.59L9,16.17Z"/>
                </svg>
                Submit Documents
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('companyDocumentsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<svg width="16" height="16" class="spinning" viewBox="0 0 24 24" fill="currentColor"><path d="M12,4V2A10,10 0 0,0 2,12H4A8,8 0 0,1 12,4Z"/></svg> Uploading...';
    submitBtn.disabled = true;
    
    // Create FormData object
    const formData = new FormData(this);
    
    // Submit via AJAX
    fetch(affiliate_ajax.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Documents uploaded successfully! Your submission is now under review.');
            window.location.href = '<?php echo get_permalink(get_page_by_title('Affiliate Dashboard')); ?>';
        } else {
            alert('Error: ' + (data.data || 'Upload failed. Please try again.'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Upload failed. Please check your connection and try again.');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});
</script>

<style>
.spinning {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>