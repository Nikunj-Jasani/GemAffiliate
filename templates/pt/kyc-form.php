<?php
// KYC Form Component - Portuguese Version
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get current user data from cookies for prefilling
global $wpdb;
$user_id = $_COOKIE['affiliate_user_id'] ?? null;

// Validate user ID
if (!$user_id) {
    echo '<div class="affiliate-alert affiliate-alert-danger">Autenticação de usuário necessária. Por favor <a href="' . get_permalink(get_page_by_path('afiliado-login')) . '">faça login novamente</a>.</div>';
    return;
}
$table_name = $wpdb->prefix . 'affiliate_users';
$current_user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $user_id));

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

// Determine account type based on affiliate_type field for KYC
$is_individual = strtolower($current_user->affiliate_type) !== 'company';
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

.kyc-status-indicator {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    font-weight: 600;
}

.kyc-status-pending {
    background: #fff3cd;
    border: 1px solid #ffc107;
    color: #856404;
}

.kyc-status-draft {
    background: #e7f3ff;
    border: 1px solid #17a2b8;
    color: #0c5460;
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

.kyc-form-group.full-width {
    grid-column: 1 / -1;
}

.kyc-form-label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.kyc-form-label.required::after {
    content: ' *';
    color: #dc3545;
}

.kyc-form-input {
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
}

.kyc-form-input:focus {
    outline: none;
    border-color: #0d6efd;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
}

.kyc-form-textarea {
    min-height: 120px;
    resize: vertical;
}

.kyc-document-upload {
    border: 2px dashed #dee2e6;
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.kyc-document-upload:hover {
    border-color: #0d6efd;
    background: rgba(13, 110, 253, 0.02);
}

.kyc-document-upload.dragover {
    border-color: #0d6efd;
    background: rgba(13, 110, 253, 0.1);
}

.kyc-upload-icon {
    width: 48px;
    height: 48px;
    margin: 0 auto 15px;
    opacity: 0.6;
}

.kyc-upload-text {
    color: #6c757d;
    font-size: 1rem;
    margin-bottom: 10px;
}

.kyc-upload-subtext {
    color: #adb5bd;
    font-size: 0.85rem;
}

.kyc-uploaded-file {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: #e8f5e8;
    border-radius: 8px;
    margin-top: 10px;
}

.kyc-file-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.kyc-remove-file {
    background: none;
    border: none;
    color: #dc3545;
    cursor: pointer;
    font-size: 1.2rem;
    padding: 4px;
    border-radius: 4px;
    transition: background 0.3s ease;
}

.kyc-remove-file:hover {
    background: rgba(220, 53, 69, 0.1);
}

.kyc-form-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}

.kyc-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    font-size: 1rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    min-height: 48px;
    box-sizing: border-box;
}

.kyc-btn-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
    color: white;
}

.kyc-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(13, 110, 253, 0.3);
}

.kyc-btn-secondary {
    background: #6c757d;
    color: white;
}

.kyc-btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
}

.kyc-progress-indicator {
    text-align: center;
    margin-bottom: 25px;
}

.kyc-progress-text {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 15px;
}

.kyc-progress-bar {
    width: 100%;
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
}

.kyc-progress-fill {
    height: 100%;
    background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
    width: 33%;
    transition: width 0.3s ease;
}

@media (max-width: 768px) {
    .kyc-form-container {
        padding: 25px 20px;
        margin-top: 30px;
    }
    
    .kyc-form-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .kyc-form-actions {
        flex-direction: column;
    }
    
    .kyc-btn {
        width: 100%;
        justify-content: center;
    }
}

/* Conditional section visibility */
.individual-fields-section,
.company-fields-section {
    display: none;
}

.individual-fields-section.show,
.company-fields-section.show {
    display: grid;
}
</style>

<div class="kyc-form-container">
    <div class="kyc-form-header">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor" style="color: #0d6efd;">
            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h2>Complete sua Verificação KYC</h2>
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
                Rascunho Salvo - Continue sua aplicação KYC
            <?php else: ?>
                Verificação KYC Necessária - Por favor, complete o formulário abaixo
            <?php endif; ?>
        </span>
    </div>

    <!-- Progress Indicator -->
    <div class="kyc-progress-indicator">
        <div class="kyc-progress-text">Passo 1 de 3: Informações Pessoais e Documentos</div>
        <div class="kyc-progress-bar">
            <div class="kyc-progress-fill"></div>
        </div>
    </div>

    <!-- KYC Form -->
    <form id="kycForm" enctype="multipart/form-data">
        <input type="hidden" name="action" value="affiliate_submit_kyc">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('affiliate_nonce'); ?>">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <input type="hidden" name="account_type" value="<?php echo $is_individual ? 'Individual' : 'Company'; ?>">

        <!-- Personal Information Section (Individual Only) -->
        <div class="kyc-form-grid individual-fields-section <?php echo $is_individual ? 'show' : ''; ?>">
            <div class="kyc-form-group">
                <label class="kyc-form-label required" for="kyc_full_name">Nome Completo</label>
                <input type="text" id="kyc_full_name" name="full_name" class="kyc-form-input" 
                       value="<?php echo esc_attr($form_data->full_name ?? ''); ?>" required>
            </div>

            <div class="kyc-form-group">
                <label class="kyc-form-label required" for="kyc_date_of_birth">Data de Nascimento</label>
                <input type="date" id="kyc_date_of_birth" name="date_of_birth" class="kyc-form-input" 
                       value="<?php echo esc_attr($form_data->date_of_birth ?? ''); ?>" required>
            </div>

            <div class="kyc-form-group">
                <label class="kyc-form-label required" for="kyc_email">Endereço de Email</label>
                <input type="email" id="kyc_email" name="email" class="kyc-form-input" 
                       value="<?php echo esc_attr($form_data->email ?? ''); ?>" required>
            </div>

            <div class="kyc-form-group">
                <label class="kyc-form-label required" for="kyc_nationality">Nacionalidade</label>
                <input type="text" id="kyc_nationality" name="nationality" class="kyc-form-input" 
                       value="<?php echo esc_attr($form_data->nationality ?? ''); ?>" 
                       placeholder="ex: Brasileiro, Americano, Português" required>
            </div>

            <div class="kyc-form-group">
                <label class="kyc-form-label required" for="kyc_mobile_number">Número do Celular</label>
                <input type="tel" id="kyc_mobile_number" name="mobile_number" class="kyc-form-input" 
                       value="<?php echo esc_attr($form_data->mobile_number ?? ''); ?>" 
                       placeholder="+55 11 99999-9999" required>
            </div>

            <div class="kyc-form-group">
                <label class="kyc-form-label required" for="kyc_affiliate_type">Tipo de Afiliado</label>
                <select id="kyc_affiliate_type" name="affiliate_type" class="kyc-form-input" required>
                    <option value="">Selecione o Tipo de Afiliado</option>
                    <option value="Influenciador" <?php selected($form_data->affiliate_type ?? '', 'Influenciador'); ?>>Influenciador</option>
                    <option value="Criador de Conteúdo" <?php selected($form_data->affiliate_type ?? '', 'Criador de Conteúdo'); ?>>Criador de Conteúdo</option>
                    <option value="Blogger" <?php selected($form_data->affiliate_type ?? '', 'Blogger'); ?>>Blogger</option>
                    <option value="Proprietário de Site" <?php selected($form_data->affiliate_type ?? '', 'Proprietário de Site'); ?>>Proprietário de Site</option>
                    <option value="Marketeiro de Redes Sociais" <?php selected($form_data->affiliate_type ?? '', 'Marketeiro de Redes Sociais'); ?>>Marketeiro de Redes Sociais</option>
                    <option value="Marketeiro de Email" <?php selected($form_data->affiliate_type ?? '', 'Marketeiro de Email'); ?>>Marketeiro de Email</option>
                    <option value="Outro" <?php selected($form_data->affiliate_type ?? '', 'Outro'); ?>>Outro</option>
                </select>
            </div>
        </div>

        <!-- Address Information (Individual Only) -->
        <div class="kyc-form-grid individual-fields-section <?php echo $is_individual ? 'show' : ''; ?>">
            <div class="kyc-form-group">
                <label class="kyc-form-label required" for="kyc_address_line1">Endereço Linha 1</label>
                <input type="text" id="kyc_address_line1" name="address_line1" class="kyc-form-input" 
                       value="<?php echo esc_attr($form_data->address_line1 ?? ''); ?>" required>
            </div>

            <div class="kyc-form-group">
                <label class="kyc-form-label" for="kyc_address_line2">Endereço Linha 2</label>
                <input type="text" id="kyc_address_line2" name="address_line2" class="kyc-form-input" 
                       value="<?php echo esc_attr($form_data->address_line2 ?? ''); ?>">
            </div>

            <div class="kyc-form-group">
                <label class="kyc-form-label required" for="kyc_city">Cidade</label>
                <input type="text" id="kyc_city" name="city" class="kyc-form-input" 
                       value="<?php echo esc_attr($form_data->city ?? ''); ?>" required>
            </div>

            <div class="kyc-form-group">
                <label class="kyc-form-label required" for="kyc_country">País</label>
                <input type="text" id="kyc_country" name="country" class="kyc-form-input" 
                       value="<?php echo esc_attr($form_data->country ?? ''); ?>" required>
            </div>

            <div class="kyc-form-group">
                <label class="kyc-form-label required" for="kyc_post_code">CEP</label>
                <input type="text" id="kyc_post_code" name="post_code" class="kyc-form-input" 
                       value="<?php echo esc_attr($form_data->post_code ?? ''); ?>" required>
            </div>
        </div>

        <!-- Document Uploads (Individual Only) -->
        <div class="kyc-form-grid individual-fields-section <?php echo $is_individual ? 'show' : ''; ?>">
            <div class="kyc-form-group">
                <label class="kyc-form-label required">Comprovante de Endereço</label>
                <div class="kyc-document-upload" onclick="document.getElementById('address_proof').click()">
                    <svg class="kyc-upload-icon" viewBox="0 0 24 24" fill="currentColor" style="color: #6c757d;">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14,2 14,8 20,8"/>
                    </svg>
                    <div class="kyc-upload-text">Clique para fazer upload ou arraste e solte</div>
                    <div class="kyc-upload-subtext">PDF, JPG, PNG até 10MB</div>
                </div>
                <input type="file" id="address_proof" name="address_proof" style="display: none;" 
                       accept=".pdf,.jpg,.jpeg,.png" required>
                <div id="address_proof_preview"></div>
            </div>

            <div class="kyc-form-group">
                <label class="kyc-form-label required">Documento de Identificação</label>
                <div class="kyc-document-upload" onclick="document.getElementById('identification').click()">
                    <svg class="kyc-upload-icon" viewBox="0 0 24 24" fill="currentColor" style="color: #6c757d;">
                        <path d="M20 6L9 17l-5-5"/>
                    </svg>
                    <div class="kyc-upload-text">Clique para fazer upload ou arraste e solte</div>
                    <div class="kyc-upload-subtext">Passaporte, CNH, RG ou CPF</div>
                </div>
                <input type="file" id="identification" name="identification" style="display: none;" 
                       accept=".pdf,.jpg,.jpeg,.png" required>
                <div id="identification_preview"></div>
            </div>
        </div>

        <!-- Company Information Section (Company Only) -->
        <div class="company-fields-section <?php echo !$is_individual ? 'show' : ''; ?>">
            <div class="company-section-title" style="display: flex; align-items: center; gap: 12px; margin-bottom: 25px; padding: 15px; background: #f8f9fa; border-radius: 12px;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" style="color: #0d6efd;">
                    <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/>
                </svg>
                <span style="font-size: 1.2rem; font-weight: 600; color: #2c3e50;">Informações da Empresa</span>
            </div>
            
            <div class="kyc-form-grid">
                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="company_name">Nome da Empresa</label>
                    <input type="text" id="company_name" name="company_name" class="kyc-form-input" 
                           value="<?php echo esc_attr($form_data->company_name ?? ''); ?>" <?php echo !$is_individual ? 'required' : ''; ?>>
                </div>
                
                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="business_registration_number">CNPJ</label>
                    <input type="text" id="business_registration_number" name="business_registration_number" class="kyc-form-input" 
                           value="<?php echo esc_attr($form_data->business_registration_number ?? ''); ?>" <?php echo !$is_individual ? 'required' : ''; ?>
                           placeholder="00.000.000/0000-00">
                </div>
                
                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="business_contact_name">Nome do Contato Comercial</label>
                    <input type="text" id="business_contact_name" name="business_contact_name" class="kyc-form-input" 
                           value="<?php echo esc_attr($form_data->business_contact_name ?? ''); ?>" <?php echo !$is_individual ? 'required' : ''; ?>>
                </div>
                
                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="business_email">E-mail Comercial</label>
                    <input type="email" id="business_email" name="business_email" class="kyc-form-input" 
                           value="<?php echo esc_attr($form_data->business_email ?? ''); ?>" <?php echo !$is_individual ? 'required' : ''; ?>>
                </div>
                
                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="business_phone">Telefone Comercial</label>
                    <input type="tel" id="business_phone" name="business_phone" class="kyc-form-input" 
                           value="<?php echo esc_attr($form_data->business_phone ?? ''); ?>" <?php echo !$is_individual ? 'required' : ''; ?>>
                </div>
                
                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="business_address">Endereço Comercial Completo</label>
                    <textarea id="business_address" name="business_address" class="kyc-form-input" style="min-height: 80px;" 
                              <?php echo !$is_individual ? 'required' : ''; ?>><?php echo esc_textarea($form_data->business_address ?? ''); ?></textarea>
                </div>
            </div>
        </div>

        <!-- Affiliate Sites -->
        <div class="kyc-form-group full-width">
            <label class="kyc-form-label required" for="kyc_affiliate_sites">URLs de Sites/Redes Sociais de Afiliados</label>
            <textarea id="kyc_affiliate_sites" name="affiliate_sites" class="kyc-form-input kyc-form-textarea" 
                      placeholder="Digite cada URL de site ou rede social em uma nova linha:&#10;https://exemplo.com&#10;https://instagram.com/usuario&#10;https://youtube.com/channel/..."
                      required><?php echo esc_textarea($form_data->affiliate_sites ?? ''); ?></textarea>
            <small style="color: #6c757d; margin-top: 8px; display: block;">
                Liste todos os sites, perfis de redes sociais ou plataformas onde você planeja promover produtos afiliados. Cada URL deve estar em uma linha separada.
            </small>
        </div>

        <!-- Form Actions -->
        <div class="kyc-form-actions">
            <button type="button" id="saveDraftBtn" class="kyc-btn kyc-btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V7l-4-4z"/>
                    <polyline points="9,9 9,15 15,15 15,9"/>
                </svg>
                Salvar como Rascunho
            </button>
            <button type="submit" id="submitKycBtn" class="kyc-btn kyc-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22,4 12,14.01 9,11.01"/>
                </svg>
                Enviar Aplicação KYC
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const kycForm = document.getElementById('kycForm');
    const saveDraftBtn = document.getElementById('saveDraftBtn');
    const submitBtn = document.getElementById('submitKycBtn');

    // File upload handling
    setupFileUpload('address_proof');
    setupFileUpload('identification');

    // Save draft functionality
    saveDraftBtn.addEventListener('click', function() {
        submitForm('draft');
    });

    // Submit form functionality
    kycForm.addEventListener('submit', function(e) {
        e.preventDefault();
        submitForm('submitted');
    });

    function setupFileUpload(inputId) {
        const input = document.getElementById(inputId);
        const uploadArea = input.parentElement.querySelector('.kyc-document-upload');
        const preview = document.getElementById(inputId + '_preview');

        input.addEventListener('change', function() {
            handleFileUpload(this, preview);
        });

        // Drag and drop functionality
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                input.files = files;
                handleFileUpload(input, preview);
            }
        });
    }

    function handleFileUpload(input, preview) {
        const file = input.files[0];
        if (file) {
            const fileName = file.name;
            const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
            
            preview.innerHTML = `
                <div class="kyc-uploaded-file">
                    <div class="kyc-file-info">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="color: #28a745;">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span><strong>${fileName}</strong> (${fileSize})</span>
                    </div>
                    <button type="button" class="kyc-remove-file" onclick="removeFile('${input.id}')">
                        ×
                    </button>
                </div>
            `;
        }
    }

    window.removeFile = function(inputId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(inputId + '_preview');
        input.value = '';
        preview.innerHTML = '';
    };

    function submitForm(action) {
        const formData = new FormData(kycForm);
        formData.append('kyc_action', action);

        // Show loading state
        const originalText = action === 'draft' ? 'Salvar como Rascunho' : 'Enviar Aplicação KYC';
        const button = action === 'draft' ? saveDraftBtn : submitBtn;
        button.disabled = true;
        button.innerHTML = `
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="animation: spin 1s linear infinite;">
                <path d="M12 2v4m0 12v4m10-10h-4M6 12H2m15.09-5.09l-2.83 2.83M9.74 14.26L6.91 17.09M17.09 17.09l-2.83-2.83M14.26 9.74l2.83-2.83"/>
            </svg>
            ${action === 'draft' ? 'Salvando...' : 'Enviando...'}
        `;

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (action === 'draft') {
                    showNotification('Rascunho salvo com sucesso!', 'success');
                } else {
                    showNotification('Aplicação KYC enviada com sucesso! Você receberá um email de confirmação em breve.', 'success');
                    // Reload page to show updated status
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            } else {
                showNotification(data.data || 'Ocorreu um erro. Tente novamente.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Ocorreu um erro. Tente novamente.', 'error');
        })
        .finally(() => {
            // Reset button state
            button.disabled = false;
            button.innerHTML = originalText;
        });
    }

    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `kyc-notification kyc-notification-${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#d4edda' : '#f8d7da'};
            color: ${type === 'success' ? '#155724' : '#721c24'};
            border: 1px solid ${type === 'success' ? '#c3e6cb' : '#f5c6cb'};
            border-radius: 8px;
            padding: 15px 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            z-index: 10000;
            max-width: 400px;
            word-wrap: break-word;
        `;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
});

// Add spinning animation for loading state
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
</script>