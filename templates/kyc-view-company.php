<?php
if (!defined('ABSPATH')) {
    exit;
}

// This template shows the submitted KYC data in view mode for Company accounts
$kyc_status = $kyc_data->kyc_status;
$admin_comments = $kyc_data->admin_comments ?? '';
$can_reupload = ($kyc_status === 'rejected' || !empty($admin_comments));

?>
<style>
.kyc-view-container {
    background: rgba(255, 255, 255, 0.98);
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    margin: 20px 0;
}

.kyc-view-header {
    text-align: center;
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 2px solid #f8f9fa;
}

.kyc-view-header h2 {
    color: #2c3e50;
    font-size: 2.2rem;
    font-weight: 700;
    margin: 0 0 15px 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
}

.kyc-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 1.1rem;
    margin: 20px 0;
}

.status-awaiting {
    background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
    color: white;
}

.status-approved {
    background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
    color: white;
}

.status-rejected {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
}

.kyc-info-section {
    background: white;
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 25px;
    border: 1px solid #e9ecef;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.kyc-info-section h3 {
    color: #2c3e50;
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0 0 25px 0;
    padding-bottom: 15px;
    border-bottom: 2px solid #f8f9fa;
    display: flex;
    align-items: center;
    gap: 10px;
}

.kyc-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.kyc-info-item {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    border-left: 4px solid #667eea;
    transition: all 0.3s ease;
}

.kyc-info-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.kyc-info-label {
    display: block;
    font-weight: 600;
    color: #495057;
    font-size: 0.9rem;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.kyc-info-value {
    display: block;
    color: #2c3e50;
    font-size: 1.1rem;
    font-weight: 500;
    word-wrap: break-word;
}

.document-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.document-card {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 15px;
    padding: 25px;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.document-card:hover {
    border-color: #667eea;
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(102, 126, 234, 0.15);
}

.document-card.uploaded {
    border-color: #28a745;
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
}

.document-card.not-uploaded {
    border-style: dashed;
    border-color: #6c757d;
    background: #f8f9fa;
}

.doc-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

.uploaded .doc-icon {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.document-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 24px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    margin-top: 15px;
}

.document-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    color: white;
    text-decoration: none;
}

@media (max-width: 768px) {
    .kyc-view-container {
        padding: 20px;
        margin: 10px;
    }
    
    .kyc-info-grid {
        grid-template-columns: 1fr;
    }
    
    .document-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="kyc-view-container">
    <div class="kyc-view-header">
        <h2>
            <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12,7V3H2V21H22V7H12M6,19H4V17H6V19M6,15H4V13H6V15M6,11H4V9H6V11M6,7H4V5H6V7M10,19H8V17H10V19M10,15H8V13H10V15M10,11H8V9H10V11M10,7H8V5H10V7M20,19H12V17H20V19M20,15H12V13H20V15M20,11H12V9H20V11Z"/>
            </svg>
            Company KYC Verification
        </h2>
        <div class="kyc-status-badge status-<?php echo str_replace(['awaiting approval', ' '], ['awaiting', '-'], $kyc_status); ?>">
            <?php 
            $status_icons = [
                'awaiting approval' => '⏳',
                'approved' => '✅', 
                'rejected' => '❌'
            ];
            echo ($status_icons[$kyc_status] ?? '📝') . ' ' . ucwords(str_replace('_', ' ', $kyc_status)); 
            ?>
        </div>
    </div>

    <!-- Status Information -->
    <div class="kyc-info-section">
        <div style="text-align: center; padding: 20px;">
            <p style="font-size: 1.2rem; margin: 0; color: #6c757d;">
                <?php 
                switch($kyc_status) {
                    case 'awaiting approval':
                        echo '⏳ Your company KYC application is under review. You will be notified once the review is complete.';
                        break;
                    case 'approved':
                        echo '🎉 Congratulations! Your company KYC application has been approved and you are fully verified.';
                        break;
                    case 'rejected':
                        echo '📝 Your company KYC application requires updates. Please review the comments below and resubmit.';
                        break;
                    default:
                        echo '📋 Your company KYC application details are shown below.';
                }
                ?>
            </p>
        </div>
    </div>

    <?php if (!empty($admin_comments)): ?>
    <!-- Admin Comments Section -->
    <div class="kyc-info-section">
        <h3>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2M6 9H18V11H6V9M6 12H16V14H6V12M6 6H18V8H6V6Z"/>
            </svg>
            Review Comments
        </h3>
        <div style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border: 2px solid #f39c12; border-radius: 15px; padding: 25px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                <div style="width: 40px; height: 40px; background: #f39c12; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">💬</div>
                <strong style="color: #856404; font-size: 1.1rem;">Admin Feedback:</strong>
            </div>
            <div style="background: white; padding: 20px; border-radius: 10px; border-left: 4px solid #f39c12;">
                <p style="margin: 0; white-space: pre-wrap; color: #2c3e50; line-height: 1.6;"><?php echo esc_html($admin_comments); ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Business Contact Person Section -->
    <div class="kyc-info-section">
        <h3>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M16 4C18.2 4 20 5.8 20 8S18.2 12 16 12 12 10.2 12 8 13.8 4 16 4M16 14C20.4 14 24 15.8 24 18V20H8V18C8 15.8 11.6 14 16 14M12.5 11.5C12.8 11.8 13.1 12.1 13.5 12.3C12.9 12.1 12.4 12 12 12C9.8 12 8 10.2 8 8S9.8 4 12 4C12.4 4 12.9 4.1 13.4 4.2C13.1 4.7 12.9 5.3 12.9 6H12C10.3 6 9 7.3 9 9S10.3 12 12 12H12.5V11.5Z"/>
            </svg>
            Business Contact Person
        </h3>
        <div class="kyc-info-grid">
            <div class="kyc-info-item">
                <span class="kyc-info-label">👤 Contact Name</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->business_contact_name ?? 'Not provided'); ?></span>
            </div>
            <div class="kyc-info-item">
                <span class="kyc-info-label">💼 Job Title</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->business_contact_job_title ?? 'Not provided'); ?></span>
            </div>
            <div class="kyc-info-item">
                <span class="kyc-info-label">📧 Email Address</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->business_contact_email ?? 'Not provided'); ?></span>
            </div>
            <div class="kyc-info-item">
                <span class="kyc-info-label">📱 Phone Number</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->business_contact_phone ?? 'Not provided'); ?></span>
            </div>
        </div>
    </div>

    <!-- Company Information Section -->
    <div class="kyc-info-section">
        <h3>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12,7V3H2V21H22V7H12M6,19H4V17H6V19M6,15H4V13H6V15M6,11H4V9H6V11M6,7H4V5H6V7M10,19H8V17H10V19M10,15H8V13H10V15M10,11H8V9H10V11M10,7H8V5H10V7M20,19H12V17H14V15H12V13H14V11H12V9H20V19M18,11H16V13H18V11M18,15H16V17H18V15Z"/>
            </svg>
            Company Information
        </h3>
        <div class="kyc-info-grid">
            <div class="kyc-info-item">
                <span class="kyc-info-label">🏢 Company Name</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->full_name ?? 'Not provided'); ?></span>
            </div>
            <div class="kyc-info-item">
                <span class="kyc-info-label">🏷️ Trading Name</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->company_trading_name ?? 'Not provided'); ?></span>
            </div>
            <div class="kyc-info-item">
                <span class="kyc-info-label">📋 Registration Number</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->registration_number ?? 'Not provided'); ?></span>
            </div>
            <div class="kyc-info-item">
                <span class="kyc-info-label">🏷️ Tax ID</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->tax_id ?? 'Not provided'); ?></span>
            </div>
            <div class="kyc-info-item">
                <span class="kyc-info-label">🏭 Business Type</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->company_type ?? 'Not provided'); ?></span>
            </div>
            <div class="kyc-info-item">
                <span class="kyc-info-label">📅 Incorporation Date</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->incorporation_date ?? 'Not provided'); ?></span>
            </div>
            <div class="kyc-info-item">
                <span class="kyc-info-label">🌍 Country of Incorporation</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->country ?? 'Not provided'); ?></span>
            </div>
            <div class="kyc-info-item">
                <span class="kyc-info-label">📧 Company Email</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->email ?? 'Not provided'); ?></span>
            </div>
            <div class="kyc-info-item">
                <span class="kyc-info-label">📱 Company Phone</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->mobile_number ?? 'Not provided'); ?></span>
            </div>
        </div>
    </div>

    <!-- Company Address Section -->
    <div class="kyc-info-section">
        <h3>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22S19,14.25 19,9A7,7 0 0,0 12,2Z"/>
            </svg>
            Company Address
        </h3>
        <div class="kyc-info-grid">
            <div class="kyc-info-item">
                <span class="kyc-info-label">🏠 Address Line 1</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->address_line1 ?? 'Not provided'); ?></span>
            </div>
            <?php if (!empty($kyc_data->address_line2)): ?>
            <div class="kyc-info-item">
                <span class="kyc-info-label">🏠 Address Line 2</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->address_line2); ?></span>
            </div>
            <?php endif; ?>
            <div class="kyc-info-item">
                <span class="kyc-info-label">🏙️ City</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->city ?? 'Not provided'); ?></span>
            </div>
            <div class="kyc-info-item">
                <span class="kyc-info-label">🌍 Country</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->country ?? 'Not provided'); ?></span>
            </div>
            <div class="kyc-info-item">
                <span class="kyc-info-label">📮 Post Code</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->post_code ?? 'Not provided'); ?></span>
            </div>
        </div>
    </div>

    <!-- Directors Information Section -->
    <div class="kyc-info-section">
        <h3>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M16 4C18.2 4 20 5.8 20 8S18.2 12 16 12 12 10.2 12 8 13.8 4 16 4M16 14C20.4 14 24 15.8 24 18V20H8V18C8 15.8 11.6 14 16 14M12.5 11.5C12.8 11.8 13.1 12.1 13.5 12.3C12.9 12.1 12.4 12 12 12C9.8 12 8 10.2 8 8S9.8 4 12 4C12.4 4 12.9 4.1 13.4 4.2C13.1 4.7 12.9 5.3 12.9 6H12C10.3 6 9 7.3 9 9S10.3 12 12 12H12.5V11.5Z"/>
            </svg>
            Directors & Shareholders Information
        </h3>
        <?php 
        $directors_list = json_decode($kyc_data->directors_list ?? '[]', true);
        if (!empty($directors_list) && is_array($directors_list)): 
        ?>
            <div style="margin-top: 20px;">
                <?php foreach ($directors_list as $index => $director): ?>
                    <div class="kyc-info-item" style="grid-column: 1 / -1; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-left: 4px solid #667eea; margin-bottom: 20px; padding: 25px; border-radius: 12px;">
                        <h4 style="margin: 0 0 20px 0; color: #2c3e50; font-size: 1.2rem; display: flex; align-items: center; gap: 8px;">
                            <span style="width: 30px; height: 30px; background: #667eea; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 14px;">👤</span>
                            Director <?php echo ($index + 1); ?>
                        </h4>
                        <div class="kyc-info-grid" style="margin-top: 0;">
                            <div class="kyc-info-item">
                                <span class="kyc-info-label">👤 Full Name</span>
                                <span class="kyc-info-value"><?php echo esc_html($director['name'] ?? 'Not provided'); ?></span>
                            </div>
                            <div class="kyc-info-item">
                                <span class="kyc-info-label">💼 Position/Title</span>
                                <span class="kyc-info-value"><?php echo esc_html($director['position'] ?? 'Not provided'); ?></span>
                            </div>
                            <div class="kyc-info-item">
                                <span class="kyc-info-label">📊 Ownership (%)</span>
                                <span class="kyc-info-value"><?php echo esc_html($director['ownership'] ?? 'Not provided'); ?>%</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 30px; color: #6c757d; font-style: italic;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor" style="margin-bottom: 15px; opacity: 0.5;">
                    <path d="M16 4C18.2 4 20 5.8 20 8S18.2 12 16 12 12 10.2 12 8 13.8 4 16 4M16 14C20.4 14 24 15.8 24 18V20H8V18C8 15.8 11.6 14 16 14Z"/>
                </svg>
                <p style="margin: 0; font-size: 1.1rem;">No directors information provided</p>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($kyc_data->shareholdings)): ?>
        <div class="kyc-info-item" style="margin-top: 30px; background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-left: 4px solid #2196f3; padding: 25px; border-radius: 12px;">
            <span class="kyc-info-label" style="display: flex; align-items: center; gap: 8px; font-size: 1.1rem; margin-bottom: 15px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3M19,19H5V5H19V19Z"/>
                </svg>
                📊 Shareholding Structure
            </span>
            <div style="background: white; padding: 20px; border-radius: 10px; border-left: 4px solid #2196f3; white-space: pre-wrap; font-family: 'Courier New', monospace; line-height: 1.6;">
                <?php echo esc_html($kyc_data->shareholdings); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Affiliate URLs Section -->
    <div class="kyc-info-section">
        <h3>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M16.36,14C16.44,13.34 16.5,12.68 16.5,12C16.5,11.32 16.44,10.66 16.36,10H19.74C19.9,10.64 20,11.31 20,12C20,12.69 19.9,13.36 19.74,14M14.59,19.56C15.19,18.45 15.65,17.25 15.97,16H18.92C17.96,17.65 16.43,18.93 14.59,19.56M14.34,14H9.66C9.56,13.34 9.5,12.68 9.5,12C9.5,11.32 9.56,10.65 9.66,10H14.34C14.43,10.65 14.5,11.32 14.5,12C14.5,12.68 14.43,13.34 14.34,14M12,19.96C11.17,18.76 10.5,17.43 10.09,16H13.91C13.5,17.43 12.83,18.76 12,19.96M8,8H5.08C6.03,6.34 7.57,5.06 9.4,4.44C8.8,5.55 8.35,6.75 8,8M5.08,16H8C8.35,17.25 8.8,18.45 9.4,19.56C7.57,18.93 6.03,17.65 5.08,16M4.26,14C4.1,13.36 4,12.69 4,12C4,11.31 4.1,10.64 4.26,10H7.64C7.56,10.66 7.5,11.32 7.5,12C7.5,12.68 7.56,13.34 7.64,14M12,4.03C12.83,5.23 13.5,6.57 13.91,8H10.09C10.5,6.57 11.17,5.23 12,4.03M18.92,8H15.97C15.65,6.75 15.19,5.55 14.59,4.44C16.43,5.07 17.96,6.34 18.92,8Z"/>
            </svg>
            Affiliate URLs
        </h3>
        <div class="kyc-info-grid">
            <div class="kyc-info-item" style="grid-column: 1 / -1; background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%); border-left: 4px solid #9c27b0; padding: 25px; border-radius: 12px;">
                <span class="kyc-info-label" style="display: flex; align-items: center; gap: 8px; font-size: 1.1rem; margin-bottom: 15px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M16.36,14C16.44,13.34 16.5,12.68 16.5,12C16.5,11.32 16.44,10.66 16.36,10H19.74C19.9,10.64 20,11.31 20,12C20,12.69 19.9,13.36 19.74,14M14.59,19.56C15.19,18.45 15.65,17.25 15.97,16H18.92C17.96,17.65 16.43,18.93 14.59,19.56M14.34,14H9.66C9.56,13.34 9.5,12.68 9.5,12C9.5,11.32 9.56,10.65 9.66,10H14.34C14.43,10.65 14.5,11.32 14.5,12C14.5,12.68 14.43,13.34 14.34,14M12,19.96C11.17,18.76 10.5,17.43 10.09,16H13.91C13.5,17.43 12.83,18.76 12,19.96M8,8H5.08C6.03,6.34 7.57,5.06 9.4,4.44C8.8,5.55 8.35,6.75 8,8M5.08,16H8C8.35,17.25 8.8,18.45 9.4,19.56C7.57,18.93 6.03,17.65 5.08,16M4.26,14C4.1,13.36 4,12.69 4,12C4,11.31 4.1,10.64 4.26,10H7.64C7.56,10.66 7.5,11.32 7.5,12C7.5,12.68 7.56,13.34 7.64,14M12,4.03C12.83,5.23 13.5,6.57 13.91,8H10.09C10.5,6.57 11.17,5.23 12,4.03M18.92,8H15.97C15.65,6.75 15.19,5.55 14.59,4.44C16.43,5.07 17.96,6.34 18.92,8Z"/>
                    </svg>
                    🌐 Website URLs for Affiliate Application
                </span>
                <?php if (!empty($kyc_data->affiliate_urls)): ?>
                    <div style="background: white; padding: 20px; border-radius: 10px; border-left: 4px solid #9c27b0; white-space: pre-wrap; font-family: 'Courier New', monospace; line-height: 1.6;">
                        <?php echo esc_html($kyc_data->affiliate_urls); ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; color: #6c757d; font-style: italic; padding: 20px;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor" style="opacity: 0.5; margin-bottom: 10px;">
                            <path d="M16.36,14C16.44,13.34 16.5,12.68 16.5,12C16.5,11.32 16.44,10.66 16.36,10H19.74C19.9,10.64 20,11.31 20,12C20,12.69 19.9,13.36 19.74,14Z"/>
                        </svg>
                        <p style="margin: 0;">No affiliate URLs provided</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Business Information Section -->
    <div class="kyc-info-section">
        <h3>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17,8C8,10 5.9,16.17 3.82,21.34L5.71,22L6.66,19.7C7.14,19.87 7.64,20 8,20C19,20 22,3 22,3C21,5 14,5.25 9,6.25C4,7.25 2,11.5 2,13.5C2,15.5 3.75,17.25 3.75,17.25C7,8 17,8 17,8Z"/>
            </svg>
            Business Information
        </h3>
        <div class="kyc-info-grid">
            <div class="kyc-info-item">
                <span class="kyc-info-label">💼 Affiliate Type</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->affiliate_type ?? 'Not provided'); ?></span>
            </div>
            <div class="kyc-info-item">
                <span class="kyc-info-label">📱 Contact Phone</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->mobile_number ?? 'Not provided'); ?></span>
            </div>
        </div>
        <?php if (!empty($kyc_data->business_description)): ?>
        <div class="kyc-info-item" style="grid-column: 1 / -1; margin-top: 25px; background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%); border-left: 4px solid #4caf50; padding: 25px; border-radius: 12px;">
            <span class="kyc-info-label" style="display: flex; align-items: center; gap: 8px; font-size: 1.1rem; margin-bottom: 15px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                </svg>
                📄 Business Description
            </span>
            <div style="background: white; padding: 20px; border-radius: 10px; border-left: 4px solid #4caf50; white-space: pre-wrap; line-height: 1.6;">
                <?php echo esc_html($kyc_data->business_description); ?>
            </div>
        </div>
        <?php endif; ?>
        <?php if (!empty($kyc_data->affiliate_sites)): ?>
        <div class="kyc-info-item" style="grid-column: 1 / -1; margin-top: 25px; background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%); border-left: 4px solid #ff9800; padding: 25px; border-radius: 12px;">
            <span class="kyc-info-label" style="display: flex; align-items: center; gap: 8px; font-size: 1.1rem; margin-bottom: 15px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16.36,14C16.44,13.34 16.5,12.68 16.5,12C16.5,11.32 16.44,10.66 16.36,10H19.74C19.9,10.64 20,11.31 20,12C20,12.69 19.9,13.36 19.74,14Z"/>
                </svg>
                🌐 Affiliate Sites
            </span>
            <div style="background: white; padding: 20px; border-radius: 10px; border-left: 4px solid #ff9800; white-space: pre-wrap; font-family: 'Courier New', monospace; line-height: 1.6;">
                <?php echo esc_html($kyc_data->affiliate_sites); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Documents Section -->
    <div class="kyc-info-section">
        <h3>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
            </svg>
            Uploaded Documents
        </h3>
        <div class="document-grid">
            <div class="document-card <?php echo !empty($kyc_data->company_registration_certificate_url) ? 'uploaded' : 'not-uploaded'; ?>">
                <div class="doc-icon">
                    <?php echo !empty($kyc_data->company_registration_certificate_url) ? '🏢' : '📄'; ?>
                </div>
                <h4 style="margin: 0 0 10px 0; color: #2c3e50; font-weight: 600;">Company Registration</h4>
                <p style="margin: 0 0 15px 0; color: #6c757d; font-size: 0.9rem;">Certificate of Incorporation</p>
                <?php if (!empty($kyc_data->company_registration_certificate_url)): ?>
                    <a href="<?php echo esc_url($kyc_data->company_registration_certificate_url); ?>" target="_blank" class="document-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 9a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5 5 5 0 0 1 5-5 5 5 0 0 1 5 5 5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5z"/>
                        </svg>
                        View Document
                    </a>
                <?php else: ?>
                    <span style="color: #6c757d; font-style: italic;">❌ Not uploaded</span>
                <?php endif; ?>
            </div>
            
            <div class="document-card <?php echo !empty($kyc_data->business_license_url) ? 'uploaded' : 'not-uploaded'; ?>">
                <div class="doc-icon">
                    <?php echo !empty($kyc_data->business_license_url) ? '📜' : '📄'; ?>
                </div>
                <h4 style="margin: 0 0 10px 0; color: #2c3e50; font-weight: 600;">Business License</h4>
                <p style="margin: 0 0 15px 0; color: #6c757d; font-size: 0.9rem;">Operating License Verification</p>
                <?php if (!empty($kyc_data->business_license_url)): ?>
                    <a href="<?php echo esc_url($kyc_data->business_license_url); ?>" target="_blank" class="document-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 9a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5 5 5 0 0 1 5-5 5 5 0 0 1 5 5 5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5z"/>
                        </svg>
                        View Document
                    </a>
                <?php else: ?>
                    <span style="color: #6c757d; font-style: italic;">❌ Not uploaded</span>
                <?php endif; ?>
            </div>
            
            <div class="document-card <?php echo !empty($kyc_data->address_proof_url) ? 'uploaded' : 'not-uploaded'; ?>">
                <div class="doc-icon">
                    <?php echo !empty($kyc_data->address_proof_url) ? '🏠' : '📄'; ?>
                </div>
                <h4 style="margin: 0 0 10px 0; color: #2c3e50; font-weight: 600;">Address Proof</h4>
                <p style="margin: 0 0 15px 0; color: #6c757d; font-size: 0.9rem;">Company Address Verification</p>
                <?php if (!empty($kyc_data->address_proof_url)): ?>
                    <a href="<?php echo esc_url($kyc_data->address_proof_url); ?>" target="_blank" class="document-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 9a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5 5 5 0 0 1 5-5 5 5 0 0 1 5 5 5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5z"/>
                        </svg>
                        View Document
                    </a>
                <?php else: ?>
                    <span style="color: #6c757d; font-style: italic;">❌ Not uploaded</span>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($kyc_data->bank_statement_url)): ?>
            <div class="document-card uploaded">
                <div class="doc-icon">🏦</div>
                <h4 style="margin: 0 0 10px 0; color: #2c3e50; font-weight: 600;">Bank Statement</h4>
                <p style="margin: 0 0 15px 0; color: #6c757d; font-size: 0.9rem;">Financial Verification</p>
                <a href="<?php echo esc_url($kyc_data->bank_statement_url); ?>" target="_blank" class="document-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 9a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5 5 5 0 0 1 5-5 5 5 0 0 1 5 5 5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5z"/>
                    </svg>
                    View Statement
                </a>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($kyc_data->additional_documents_url)): ?>
            <div class="document-card uploaded">
                <div class="doc-icon">📎</div>
                <h4 style="margin: 0 0 10px 0; color: #2c3e50; font-weight: 600;">Additional Documents</h4>
                <p style="margin: 0 0 15px 0; color: #6c757d; font-size: 0.9rem;">Supporting Documentation</p>
                <a href="<?php echo esc_url($kyc_data->additional_documents_url); ?>" target="_blank" class="document-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 9a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5 5 5 0 0 1 5-5 5 5 0 0 1 5 5 5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5z"/>
                    </svg>
                    View Documents
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($can_reupload): ?>
    <!-- Reupload Section -->
    <div class="kyc-info-section">
        <h3>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17,3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V7L17,3M19,19H5V5H16V10H19V19Z"/>
            </svg>
            Update Documents
        </h3>
        <div style="text-align: center; padding: 20px;">
            <p style="margin: 0 0 25px 0; color: #6c757d; line-height: 1.6;">You can update your documents based on the admin feedback above.</p>
            <a href="?reupload=1" style="display: inline-flex; align-items: center; gap: 10px; background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); color: white; padding: 15px 30px; border-radius: 25px; text-decoration: none; font-weight: 600; font-size: 1.1rem; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(243, 156, 18, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17,3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V7L17,3M19,19H5V5H16V10H19V19Z"/>
                </svg>
                Update KYC Application
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Submission Details -->
    <div class="kyc-info-section">
        <h3>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3M19,19H5V5H19V19M17,12H12V17H17V12M7,12V17H10V12H7M7,7V10H10V7H7M12,7V10H17V7H12Z"/>
            </svg>
            Application Timeline
        </h3>
        <div class="kyc-info-grid">
            <div class="kyc-info-item">
                <span class="kyc-info-label">📅 Submitted On</span>
                <span class="kyc-info-value"><?php echo $kyc_data->submitted_at ? date('F j, Y \a\t g:i A', strtotime($kyc_data->submitted_at)) : 'Not available'; ?></span>
            </div>
            <?php if (!empty($kyc_data->approved_at)): ?>
            <div class="kyc-info-item">
                <span class="kyc-info-label">✅ Approved On</span>
                <span class="kyc-info-value"><?php echo date('F j, Y \a\t g:i A', strtotime($kyc_data->approved_at)); ?></span>
            </div>
            <?php endif; ?>
            <div class="kyc-info-item">
                <span class="kyc-info-label">🆔 Application ID</span>
                <span class="kyc-info-value">#KYC-<?php echo str_pad($kyc_data->id ?? '0000', 4, '0', STR_PAD_LEFT); ?></span>
            </div>
        </div>
    </div>

    <!-- Back to Dashboard -->
    <div style="margin-top: 40px; text-align: center; padding: 30px;">
        <a href="<?php echo get_permalink(get_page_by_title('Affiliate Dashboard')); ?>" style="display: inline-flex; align-items: center; gap: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 30px; border-radius: 25px; text-decoration: none; font-weight: 600; font-size: 1.1rem; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(102, 126, 234, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
            </svg>
            Back to Dashboard
        </a>
    </div>
</div>


<script>
// Add smooth hover effects for document cards
document.querySelectorAll('.document-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
        this.style.transition = 'all 0.3s ease';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});

// Add hover effects for info items
document.querySelectorAll('.kyc-info-item').forEach(item => {
    item.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-2px)';
        this.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.1)';
        this.style.transition = 'all 0.3s ease';
    });
    
    item.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = 'none';
    });
});
</script>