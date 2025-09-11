// Enhanced Admin Dashboard JavaScript for KYC Verification System

// Main function to load applications with both app and KYC actions
function loadApplications() {
    const statusFilter = document.getElementById('status-filter')?.value || '';
    const perPage = document.getElementById('per-page-filter')?.value || '25';
    
    fetch(affiliate_ajax.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'affiliate_get_applications_with_kyc',
            nonce: affiliate_ajax.nonce,
            status_filter: statusFilter,
            per_page: perPage,
            page: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateApplicationsTable(data.data.applications);
            updatePagination(data.data.pagination);
            updateStats(data.data.stats);
        } else {
            console.error('Failed to load applications:', data.data);
            document.getElementById('applications-tbody').innerHTML = 
                '<tr><td colspan="10" class="text-center">Error loading applications</td></tr>';
        }
    })
    .catch(error => {
        console.error('Error loading applications:', error);
        document.getElementById('applications-tbody').innerHTML = 
            '<tr><td colspan="10" class="text-center">Error loading applications</td></tr>';
    });
}

// Update applications table with separate app and KYC actions
function updateApplicationsTable(applications) {
    const tbody = document.getElementById('applications-tbody');
    if (!tbody) return;
    
    if (!applications || applications.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" class="text-center">No applications found</td></tr>';
        return;
    }
    
    tbody.innerHTML = applications.map(app => {
        // Determine KYC status display
        const kycStatus = app.kyc_status || 'not_started';
        const kycStatusDisplay = getKycStatusDisplay(kycStatus);
        
        // Generate App Actions
        const appActions = generateAppActions(app);
        
        // Generate KYC Actions based on status
        const kycActions = generateKycActions(app, kycStatus);
        
        return `
            <tr>
                <td><strong>${app.first_name} ${app.last_name}</strong></td>
                <td>${app.email}</td>
                <td>${app.company_name || 'N/A'}</td>
                <td><span class="account-type-badge ${(app.type || '').toLowerCase()}">${app.type || 'Individual'}</span></td>
                <td>${app.country || 'N/A'}</td>
                <td><span class="status-badge ${app.status.replace(' ', '_')}">${app.status.charAt(0).toUpperCase() + app.status.slice(1)}</span></td>
                <td>${kycStatusDisplay}</td>
                <td>${app.created_at ? new Date(app.created_at).toLocaleDateString() : 'N/A'}</td>
                <td>${appActions}</td>
                <td>${kycActions}</td>
            </tr>
        `;
    }).join('');
}

// Generate KYC status display with proper styling
function getKycStatusDisplay(kycStatus) {
    const statusMap = {
        'not_started': 'Not Started',
        'draft': 'Draft', 
        'awaiting approval': 'Awaiting Approval',
        'approved': 'Approved',
        'rejected': 'Rejected'
    };
    
    const displayText = statusMap[kycStatus] || kycStatus;
    return `<span class="kyc-status-badge ${kycStatus.replace(' ', '_')}">${displayText}</span>`;
}

// Generate application actions (approve/reject account)
function generateAppActions(app) {
    if (app.status === 'awaiting approval') {
        return `
            <button class="action-btn view" onclick="showApplicationDetails(${app.id})" title="View Details">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                View
            </button>
            <button class="action-btn edit" onclick="showApplicationStatusModal(${app.id})" title="Update Status">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                </svg>
                Update
            </button>
        `;
    } else {
        return `
            <button class="action-btn view" onclick="showApplicationDetails(${app.id})" title="View Details">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                View
            </button>
        `;
    }
}

// Generate KYC actions based on status
function generateKycActions(app, kycStatus) {
    const userId = app.id;
    
    switch(kycStatus) {
        case 'not_started':
        case 'draft':
            return `
                <span class="kyc-status-text" style="color: #6c757d; font-style: italic;">
                    KYC Pending
                </span>
            `;
            
        case 'awaiting approval':
            return `
                <button class="action-btn verify" onclick="verifyKycApplication(${userId})" 
                        style="background: #28a745; color: white;" title="Verify KYC">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Verify KYC
                </button>
                <button class="action-btn view" onclick="reviewKycApplication(${userId})" title="Review Application">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    Review
                </button>
            `;
            
        case 'approved':
            return `
                <span class="kyc-status-text" style="color: #28a745; font-weight: 600;">
                    ✓ Verified
                </span>
                <button class="action-btn view" onclick="reviewKycApplication(${userId})" title="View KYC">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    View
                </button>
            `;
            
        case 'rejected':
            return `
                <span class="kyc-status-text" style="color: #dc3545; font-weight: 600;">
                    ✗ Rejected
                </span>
                <button class="action-btn view" onclick="reviewKycApplication(${userId})" title="Review KYC">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    Review
                </button>
                <button class="action-btn edit" onclick="updateKycStatus(${userId})" title="Update KYC">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                    </svg>
                    Update
                </button>
            `;
            
        default:
            return `
                <span class="kyc-status-text" style="color: #6c757d;">
                    N/A
                </span>
            `;
    }
}

// Enhanced KYC verification function
function verifyKycApplication(userId) {
    // First load the KYC data
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
            showKycVerificationModal(data.data);
        } else {
            alert('Failed to load KYC details: ' + data.data);
        }
    })
    .catch(error => {
        console.error('Error loading KYC details:', error);
        alert('Error loading KYC details');
    });
}

// Enhanced KYC verification modal with proper individual/company view
function showKycVerificationModal(kycData) {
    const modal = document.getElementById('kycReviewModal');
    const content = document.getElementById('kycReviewContent');
    
    // Determine if this is individual or company
    const isCompany = kycData.account_type && kycData.account_type.toLowerCase() === 'company';
    
    let kycDetailsHTML = '';
    
    if (isCompany) {
        // Company KYC view
        kycDetailsHTML = generateCompanyKycView(kycData);
    } else {
        // Individual KYC view  
        kycDetailsHTML = generateIndividualKycView(kycData);
    }
    
    content.innerHTML = `
        <div class="kyc-verification-header">
            <h3>${isCompany ? 'Company' : 'Individual'} KYC Verification</h3>
            <div class="kyc-user-info">
                <strong>User:</strong> ${kycData.first_name} ${kycData.last_name} (${kycData.email})
            </div>
        </div>
        
        ${kycDetailsHTML}
        
        <div class="kyc-verification-actions" style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <h4>Verification Decision</h4>
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <button onclick="approveKyc(${kycData.user_id})" class="affiliate-btn" 
                        style="background: #28a745; color: white; padding: 12px 24px; font-size: 16px;">
                    ✓ Approve KYC
                </button>
                <button onclick="rejectKyc(${kycData.user_id})" class="affiliate-btn" 
                        style="background: #dc3545; color: white; padding: 12px 24px; font-size: 16px;">
                    ✗ Reject KYC
                </button>
                <button onclick="requestKycInfo(${kycData.user_id})" class="affiliate-btn" 
                        style="background: #ffc107; color: #212529; padding: 12px 24px; font-size: 16px;">
                    📝 Request More Info
                </button>
                <button onclick="hideKycReviewModal()" class="affiliate-btn affiliate-btn-secondary" 
                        style="padding: 12px 24px; font-size: 16px;">
                    Cancel
                </button>
            </div>
        </div>
    `;
    
    modal.style.display = 'flex';
}

// Generate individual KYC view
function generateIndividualKycView(kycData) {
    return `
        <div class="kyc-review-sections">
            <!-- Personal Information -->
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
                        <div class="kyc-field-label">Nationality</div>
                        <div class="kyc-field-value">${kycData.nationality || 'N/A'}</div>
                    </div>
                    <div class="kyc-field">
                        <div class="kyc-field-label">Mobile Number</div>
                        <div class="kyc-field-value">${kycData.mobile_number || 'N/A'}</div>
                    </div>
                </div>
            </div>
            
            <!-- Address Information -->
            <div class="kyc-review-section">
                <h4>Address Information</h4>
                <div class="kyc-field-grid">
                    <div class="kyc-field">
                        <div class="kyc-field-label">Address</div>
                        <div class="kyc-field-value">${kycData.address_line1 || 'N/A'}</div>
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
            </div>
            
            <!-- Business Information -->
            <div class="kyc-review-section">
                <h4>Business Information</h4>
                <div class="kyc-field">
                    <div class="kyc-field-label">Affiliate Type</div>
                    <div class="kyc-field-value">${kycData.affiliate_type || 'N/A'}</div>
                </div>
                ${kycData.affiliate_sites ? `
                    <div class="kyc-field">
                        <div class="kyc-field-label">Affiliate Sites</div>
                        <div class="kyc-field-value" style="white-space: pre-line;">${kycData.affiliate_sites}</div>
                    </div>
                ` : ''}
            </div>
            
            <!-- Documents -->
            <div class="kyc-review-section">
                <h4>Uploaded Documents</h4>
                <div class="kyc-documents-grid">
                    ${generateDocumentCard('Address Proof', kycData.address_proof_url)}
                    ${generateDocumentCard('Identification', kycData.identification_url)}
                    ${generateDocumentCard('Selfie Verification', kycData.selfie_url)}
                </div>
            </div>
        </div>
    `;
}

// Generate company KYC view
function generateCompanyKycView(kycData) {
    return `
        <div class="kyc-review-sections">
            <!-- Company Information -->
            <div class="kyc-review-section">
                <h4>Company Information</h4>
                <div class="kyc-field-grid">
                    <div class="kyc-field">
                        <div class="kyc-field-label">Company Name</div>
                        <div class="kyc-field-value">${kycData.company_name || 'N/A'}</div>
                    </div>
                    <div class="kyc-field">
                        <div class="kyc-field-label">Registration Number</div>
                        <div class="kyc-field-value">${kycData.company_registration_number || 'N/A'}</div>
                    </div>
                    <div class="kyc-field">
                        <div class="kyc-field-label">Tax ID</div>
                        <div class="kyc-field-value">${kycData.tax_id || 'N/A'}</div>
                    </div>
                    <div class="kyc-field">
                        <div class="kyc-field-label">Business Type</div>
                        <div class="kyc-field-value">${kycData.business_type || 'N/A'}</div>
                    </div>
                    <div class="kyc-field">
                        <div class="kyc-field-label">Incorporation Date</div>
                        <div class="kyc-field-value">${kycData.incorporation_date || 'N/A'}</div>
                    </div>
                    <div class="kyc-field">
                        <div class="kyc-field-label">Country of Incorporation</div>
                        <div class="kyc-field-value">${kycData.country_of_incorporation || 'N/A'}</div>
                    </div>
                </div>
            </div>
            
            <!-- Company Address -->
            <div class="kyc-review-section">
                <h4>Company Address</h4>
                <div class="kyc-field-grid">
                    <div class="kyc-field">
                        <div class="kyc-field-label">Address</div>
                        <div class="kyc-field-value">${kycData.company_address || 'N/A'}</div>
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
            </div>
            
            <!-- Directors Information -->
            <div class="kyc-review-section">
                <h4>Directors Information</h4>
                <div class="kyc-field-grid">
                    <div class="kyc-field">
                        <div class="kyc-field-label">Director 1 Name</div>
                        <div class="kyc-field-value">${kycData.director1_name || 'N/A'}</div>
                    </div>
                    <div class="kyc-field">
                        <div class="kyc-field-label">Director 1 ID</div>
                        <div class="kyc-field-value">${kycData.director1_id_number || 'N/A'}</div>
                    </div>
                    ${kycData.director2_name ? `
                        <div class="kyc-field">
                            <div class="kyc-field-label">Director 2 Name</div>
                            <div class="kyc-field-value">${kycData.director2_name}</div>
                        </div>
                        <div class="kyc-field">
                            <div class="kyc-field-label">Director 2 ID</div>
                            <div class="kyc-field-value">${kycData.director2_id_number || 'N/A'}</div>
                        </div>
                    ` : ''}
                </div>
            </div>
            
            <!-- Business Information -->
            <div class="kyc-review-section">
                <h4>Business Information</h4>
                <div class="kyc-field">
                    <div class="kyc-field-label">Affiliate Type</div>
                    <div class="kyc-field-value">${kycData.affiliate_type || 'N/A'}</div>
                </div>
                <div class="kyc-field">
                    <div class="kyc-field-label">Contact Phone</div>
                    <div class="kyc-field-value">${kycData.contact_phone || 'N/A'}</div>
                </div>
                ${kycData.business_description ? `
                    <div class="kyc-field">
                        <div class="kyc-field-label">Business Description</div>
                        <div class="kyc-field-value" style="white-space: pre-line;">${kycData.business_description}</div>
                    </div>
                ` : ''}
                ${kycData.affiliate_sites ? `
                    <div class="kyc-field">
                        <div class="kyc-field-label">Affiliate Sites</div>
                        <div class="kyc-field-value" style="white-space: pre-line;">${kycData.affiliate_sites}</div>
                    </div>
                ` : ''}
            </div>
            
            <!-- Documents -->
            <div class="kyc-review-section">
                <h4>Uploaded Documents</h4>
                <div class="kyc-documents-grid">
                    ${generateDocumentCard('Certificate of Incorporation', kycData.incorporation_certificate_url)}
                    ${generateDocumentCard('Company Address Proof', kycData.company_address_proof_url)}
                    ${generateDocumentCard('Director 1 ID', kycData.director1_id_document_url)}
                    ${kycData.director2_id_document_url ? generateDocumentCard('Director 2 ID', kycData.director2_id_document_url) : ''}
                    ${kycData.additional_documents_url ? generateDocumentCard('Additional Documents', kycData.additional_documents_url) : ''}
                </div>
            </div>
        </div>
    `;
}

// Helper function to generate document cards
function generateDocumentCard(title, documentUrl) {
    if (!documentUrl) {
        return `
            <div class="document-card not-uploaded">
                <h5>${title}</h5>
                <span class="document-status">Not Uploaded</span>
            </div>
        `;
    }
    
    return `
        <div class="document-card">
            <h5>${title}</h5>
            <button onclick="viewDocument('${documentUrl}', '${title}')" class="affiliate-btn affiliate-btn-primary">
                View Document
            </button>
        </div>
    `;
}

// KYC approval with comments
function approveKyc(userId) {
    const comments = prompt('Enter approval comments (optional):') || 'KYC application approved';
    updateKycStatusWithComments(userId, 'approved', comments);
}

// KYC rejection with required comments
function rejectKyc(userId) {
    const reason = prompt('Enter rejection reason (required):');
    if (!reason || reason.trim() === '') {
        alert('Rejection reason is required');
        return;
    }
    updateKycStatusWithComments(userId, 'rejected', reason);
}

// Request more information with comments
function requestKycInfo(userId) {
    const info = prompt('What additional information is needed?');
    if (!info || info.trim() === '') {
        alert('Please specify what information is needed');
        return;
    }
    updateKycStatusWithComments(userId, 'rejected', 'Additional information required: ' + info);
}

// Update KYC status with comments
function updateKycStatusWithComments(userId, status, comments) {
    fetch(affiliate_ajax.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'affiliate_update_kyc_status',
            nonce: affiliate_ajax.nonce,
            user_id: userId,
            kyc_status: status,
            admin_comments: comments
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('KYC status updated successfully');
            hideKycReviewModal();
            loadApplications(); // Reload the main table
        } else {
            alert('Failed to update KYC status: ' + data.data);
        }
    })
    .catch(error => {
        console.error('Error updating KYC status:', error);
        alert('Error updating KYC status');
    });
}

// Update stats display
function updateStats(stats) {
    if (stats) {
        document.getElementById('pending-count').textContent = stats.pending || 0;
        document.getElementById('approved-count').textContent = stats.approved || 0;
        document.getElementById('rejected-count').textContent = stats.rejected || 0;
    }
}

// CSS Styles for enhanced KYC verification
const kycStyles = `
<style>
.kyc-verification-header {
    margin-bottom: 20px;
    padding: 15px;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    border-radius: 8px;
}

.kyc-user-info {
    margin-top: 10px;
    font-size: 14px;
    opacity: 0.9;
}

.kyc-review-sections {
    max-height: 60vh;
    overflow-y: auto;
    padding: 20px 0;
}

.kyc-field-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.kyc-field {
    padding: 12px;
    background: #f8f9fa;
    border-radius: 6px;
    border-left: 3px solid #007bff;
}

.kyc-field-label {
    font-size: 12px;
    font-weight: 600;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
}

.kyc-field-value {
    color: #333;
    font-size: 14px;
    word-wrap: break-word;
}

.kyc-documents-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.document-card {
    padding: 15px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    text-align: center;
    background: white;
}

.document-card.not-uploaded {
    background: #f8f9fa;
    border-style: dashed;
}

.document-card h5 {
    margin: 0 0 10px 0;
    font-size: 14px;
    color: #333;
}

.document-status {
    color: #6c757d;
    font-style: italic;
    font-size: 12px;
}

.action-btn.verify {
    background: #28a745;
    color: white;
}

.action-btn.verify:hover {
    background: #218838;
}

.kyc-status-text {
    font-size: 12px;
    font-weight: 500;
    display: block;
    text-align: center;
    padding: 4px 8px;
}
</style>
`;

// Insert styles when script loads
document.head.insertAdjacentHTML('beforeend', kycStyles);