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
    echo '<div class="affiliate-error">Por favor, faça login para acessar a verificação KYC.</div>';
    return;
}

// Get user ID from cookie authentication system
$user_id = $affiliate_portal->get_current_user_id();
if (!$user_id) {
    echo '<div class="affiliate-error">Sessão de usuário expirada. Por favor, faça login novamente.</div>';
    return;
}

// Get user data from database
$table_name = $wpdb->prefix . 'affiliate_users';
$current_user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $user_id));

if (!$current_user) {
    error_log("Portuguese Company KYC Form: User not found in database. User ID: " . $user_id);
    echo '<div class="affiliate-error">Não foi possível carregar os dados do usuário. Tente fazer logout e login novamente.</div>';
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
    $form_data->email = $current_user->email;
    $form_data->mobile_number = $current_user->mobile_number;
    $form_data->address_line1 = $current_user->address_line1;
    $form_data->address_line2 = $current_user->address_line2;
    $form_data->city = $current_user->city;
    $form_data->country = $current_user->country;
    $form_data->post_code = $current_user->zipcode;
    $form_data->full_name = $current_user->company_name; // Use company name for company applications
}

// Parse directors data for form pre-filling
if (!empty($form_data->directors_list)) {
    $directors_json = $form_data->directors_list;
    $form_data->directors_data_parsed = json_decode($directors_json, true);
    if (!$form_data->directors_data_parsed) {
        $form_data->directors_data_parsed = [];
    }
} else {
    $form_data->directors_data_parsed = [];
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

.account-type-badge.company {
    background: linear-gradient(135deg, #fff3e0 0%, #fce4ec 100%);
    color: #e65100;
    border: 1px solid #ffcc02;
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
    background: linear-gradient(135deg, #e65100 0%, #ff9800 100%);
    border-radius: 4px;
    transition: width 0.3s ease;
}

.kyc-form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.kyc-form-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 16px;
    padding: 30px;
    margin: 30px 0;
}

.kyc-section-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
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
    border-color: #e65100;
    background: rgba(255, 255, 255, 1);
    box-shadow: 0 0 0 3px rgba(230, 81, 0, 0.1);
}

.kyc-form-textarea {
    min-height: 100px;
    resize: vertical;
}

.kyc-directors-section {
    background: linear-gradient(135deg, #e8f5e8 0%, #f1f8e9 100%);
    border-radius: 16px;
    padding: 25px;
    margin: 20px 0;
}

.kyc-director-item {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 15px;
    border: 1px solid #e9ecef;
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
    border-color: #e65100;
    background: rgba(230, 81, 0, 0.05);
}

.kyc-submit-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 16px;
    padding: 30px;
    text-align: center;
    margin-top: 40px;
}

.kyc-submit-btn {
    background: linear-gradient(135deg, #e65100 0%, #ff9800 100%);
    color: white;
    border: none;
    padding: 16px 40px;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(230, 81, 0, 0.3);
}

.kyc-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(230, 81, 0, 0.4);
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

.add-director-btn {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.remove-director-btn {
    background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 0.8rem;
    cursor: pointer;
    float: right;
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
    
    .kyc-form-section, .kyc-submit-section {
        padding: 20px;
    }
}
</style>

<div class="kyc-form-container">
    <!-- Header -->
    <div class="kyc-form-header">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="#e65100">
            <path d="M12,7V3H2V21H22V7H12M6,19H4V17H6V19M6,15H4V13H6V15M6,11H4V9H6V11M6,7H4V5H6V7M10,19H8V17H10V19M10,15H8V13H10V15M10,11H8V9H10V11M10,7H8V5H10V7M20,19H12V17H14V15H12V13H14V11H12V9H20V19M18,11H16V13H18V11M18,15H16V17H18V15Z"/>
        </svg>
        <h2>Verificação KYC Empresarial</h2>
    </div>
    
    <!-- Account Type Display -->
    <div class="kyc-account-type-display">
        <div class="account-type-badge company">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12,7V3H2V21H22V7H12M6,19H4V17H6V19M6,15H4V13H6V15M6,11H4V9H6V11M6,7H4V5H6V7M10,19H8V17H10V19M10,15H8V13H10V15M10,11H8V9H10V11M10,7H8V5H10V7M20,19H12V17H14V15H12V13H14V11H12V9H20V19M18,11H16V13H18V11M18,15H16V17H18V15Z"/>
            </svg>
            <span>Tipo de Conta: <strong>Empresa</strong></span>
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
                Rascunho Salvo - Continue sua aplicação KYC Empresarial
            <?php else: ?>
                Verificação KYC Empresarial Necessária - Por favor, complete o formulário abaixo
            <?php endif; ?>
        </span>
    </div>

    <!-- Progress Indicator -->
    <div class="kyc-progress-indicator">
        <div class="kyc-progress-text">Aplicação KYC Empresarial</div>
        <div class="kyc-progress-bar">
            <div class="kyc-progress-fill"></div>
        </div>
    </div>

    <!-- KYC Form -->
    <form id="kycCompanyForm" enctype="multipart/form-data">
        <input type="hidden" name="action" value="affiliate_submit_kyc">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('affiliate_nonce'); ?>">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <input type="hidden" name="account_type" value="Company">

        <!-- Informações da Pessoa de Contato Comercial -->
        <div class="kyc-form-section">
            <h3 class="kyc-section-title">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm9 9h-6v13h-2v-6h-2v6H9V11H3V9h18v2z"/>
                </svg>
                Pessoa de Contato Comercial
            </h3>
            
            <div class="kyc-form-grid">
                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="kyc_business_contact_name">Nome do Contato Comercial</label>
                    <input type="text" id="kyc_business_contact_name" name="business_contact_name" class="kyc-form-input" 
                           value="<?php echo esc_attr($form_data->business_contact_name ?? ''); ?>" required>
                </div>

                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="kyc_business_contact_job_title">Cargo</label>
                    <input type="text" id="kyc_business_contact_job_title" name="business_contact_job_title" class="kyc-form-input" 
                           value="<?php echo esc_attr($form_data->business_contact_job_title ?? ''); ?>" required>
                </div>

                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="kyc_business_contact_email">Endereço de Email</label>
                    <input type="email" id="kyc_business_contact_email" name="business_contact_email" class="kyc-form-input" 
                           value="<?php echo esc_attr($form_data->business_contact_email ?? ''); ?>" required>
                </div>

                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="kyc_business_contact_phone">Telefone (com código do país)</label>
                    <input type="tel" id="kyc_business_contact_phone" name="business_contact_phone" class="kyc-form-input" 
                           value="<?php echo esc_attr($form_data->business_contact_phone ?? ''); ?>" 
                           placeholder="+55 11 98765-4321" required>
                </div>
            </div>
        </div>

        <!-- Company Information Section -->
        <div class="kyc-form-section">
            <h3 class="kyc-section-title">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12,7V3H2V21H22V7H12M6,19H4V17H6V19M6,15H4V13H6V15M6,11H4V9H6V11M6,7H4V5H6V7M10,19H8V17H10V19M10,15H8V13H10V15M10,11H8V9H10V11M10,7H8V5H10V7M20,19H12V17H14V15H12V13H14V11H12V9H20V19M18,11H16V13H18V11M18,15H16V17H18V15Z"/>
                </svg>
                Detalhes da Empresa
            </h3>
            
            <div class="kyc-form-grid">
                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="kyc_company_name">Razão Social da Empresa</label>
                    <input type="text" id="kyc_company_name" name="full_name" class="kyc-form-input" 
                           value="<?php echo esc_attr($form_data->full_name ?? ''); ?>" required>
                </div>

                <div class="kyc-form-group">
                    <label class="kyc-form-label" for="kyc_company_trading_name">Nome Fantasia da Empresa</label>
                    <input type="text" id="kyc_company_trading_name" name="company_trading_name" class="kyc-form-input" 
                           value="<?php echo esc_attr($form_data->company_trading_name ?? ''); ?>" 
                           placeholder="Nome fantasia ou comercial (se diferente da razão social)">
                </div>

                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="kyc_company_type">Tipo de Empresa</label>
                    <select id="kyc_company_type" name="company_type" class="kyc-form-input" required>
                        <option value="">Selecione o Tipo de Empresa</option>
                        <option value="LTDA" <?php selected($form_data->company_type ?? '', 'LTDA'); ?>>Limitada (LTDA)</option>
                        <option value="SA" <?php selected($form_data->company_type ?? '', 'SA'); ?>>Sociedade Anônima (SA)</option>
                        <option value="EIRELI" <?php selected($form_data->company_type ?? '', 'EIRELI'); ?>>EIRELI</option>
                        <option value="MEI" <?php selected($form_data->company_type ?? '', 'MEI'); ?>>Microempreendedor Individual (MEI)</option>
                        <option value="EI" <?php selected($form_data->company_type ?? '', 'EI'); ?>>Empresário Individual</option>
                        <option value="Other" <?php selected($form_data->company_type ?? '', 'Other'); ?>>Outro</option>
                    </select>
                </div>

                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="kyc_registration_number">CNPJ</label>
                    <input type="text" id="kyc_registration_number" name="registration_number" class="kyc-form-input" 
                           value="<?php echo esc_attr($form_data->registration_number ?? ''); ?>" 
                           placeholder="00.000.000/0000-00" required>
                </div>

                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="kyc_tax_id">Inscrição Estadual</label>
                    <input type="text" id="kyc_tax_id" name="tax_id" class="kyc-form-input" 
                           value="<?php echo esc_attr($form_data->tax_id ?? ''); ?>" required>
                </div>

                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="kyc_incorporation_date">Data de Abertura</label>
                    <input type="date" id="kyc_incorporation_date" name="incorporation_date" class="kyc-form-input" 
                           value="<?php echo esc_attr($form_data->incorporation_date ?? ''); ?>" required>
                </div>

                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="kyc_email">E-mail da Empresa</label>
                    <input type="email" id="kyc_email" name="email" class="kyc-form-input" 
                           value="<?php echo esc_attr($form_data->email ?? ''); ?>" required>
                </div>

                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="kyc_mobile_number">Telefone da Empresa</label>
                    <input type="tel" id="kyc_mobile_number" name="mobile_number" class="kyc-form-input" 
                           value="<?php echo esc_attr($form_data->mobile_number ?? ''); ?>" 
                           placeholder="+55 11 9999-9999" required>
                </div>
                
                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="kyc_business_description">Descrição do Negócio</label>
                    <textarea id="kyc_business_description" name="business_description" class="kyc-form-input kyc-form-textarea" 
                              placeholder="Descreva as principais atividades da sua empresa..." required><?php echo esc_textarea($form_data->business_description ?? ''); ?></textarea>
                </div>
            </div>
        </div>

        <!-- URLs de Afiliados -->
        <div class="kyc-form-section">
            <h3 class="kyc-section-title">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H6.9C3.06 7 0 10.06 0 13.9s3.06 6.9 6.9 6.9h4v-1.9H6.9c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9.1-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c3.84 0 6.9-3.06 6.9-6.9S20.94 7 17.1 7z"/>
                </svg>
                URLs de Afiliados
            </h3>
            
            <div class="kyc-form-group">
                <label class="kyc-form-label required" for="kyc_affiliate_urls">URLs dos Sites para Aplicação de Afiliado</label>
                <textarea id="kyc_affiliate_urls" name="affiliate_urls" class="kyc-form-input kyc-form-textarea" 
                          placeholder="Insira as URLs dos sites que você deseja usar para sua aplicação de afiliado. Cada URL em uma nova linha:&#10;https://exemplo1.com&#10;https://exemplo2.com&#10;https://subdominio.exemplo.com" required><?php echo esc_textarea($form_data->affiliate_urls ?? ''); ?></textarea>
                <small style="color: #666; font-size: 0.9em;">
                    Insira cada URL em uma linha separada. Inclua a URL completa com http:// ou https://
                </small>
            </div>
        </div>

        <!-- Address Information -->
        <div class="kyc-form-section">
            <h3 class="kyc-section-title">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                </svg>
                Endereço da Empresa
            </h3>
            
            <div class="kyc-form-grid">
                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="kyc_address_line1">Endereço Linha 1</label>
                    <input type="text" id="kyc_address_line1" name="address_line1" class="kyc-form-input" 
                           value="<?php echo esc_attr($form_data->address_line1 ?? ''); ?>" 
                           placeholder="Rua, número, bairro" required>
                </div>

                <div class="kyc-form-group">
                    <label class="kyc-form-label" for="kyc_address_line2">Endereço Linha 2</label>
                    <input type="text" id="kyc_address_line2" name="address_line2" class="kyc-form-input" 
                           value="<?php echo esc_attr($form_data->address_line2 ?? ''); ?>" 
                           placeholder="Complemento, sala, andar, etc.">
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
                           value="<?php echo esc_attr($form_data->post_code ?? ''); ?>" 
                           placeholder="00000-000" required>
                </div>
            </div>
        </div>

        <!-- Directors Information -->
        <div class="kyc-form-section">
            <h3 class="kyc-section-title">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A2.003 2.003 0 0 0 18.05 7c-.55 0-1.05.22-1.41.59L14.5 9.5l1.41 1.41L17.5 9.5V22h2.5zm-8.5 0v-7.5h2.5V22H11.5zM6 6c1.11 0 2-.89 2-2s-.89-2-2-2-2 .89-2 2 .89 2 2 2zm1.5 16v-6H10l-2.54-7.63A2.003 2.003 0 0 0 5.55 7c-.55 0-1.05.22-1.41.59L2 9.5l1.41 1.41L5 9.5V22H7.5z"/>
                </svg>
                Diretores e Sócios
            </h3>
            
            <div id="directorsContainer">
                <div class="kyc-director-item">
                    <button type="button" class="remove-director-btn" onclick="removeDirector(this)" style="display: none;">Remover</button>
                    <div class="kyc-form-grid">
                        <div class="kyc-form-group">
                            <label class="kyc-form-label required">Nome Completo do Diretor</label>
                            <input type="text" name="directors[0][name]" class="kyc-form-input" value="<?php echo esc_attr($form_data->directors_data_parsed[0]->name ?? ''); ?>" required>
                        </div>
                        <div class="kyc-form-group">
                            <label class="kyc-form-label required">Cargo/Função</label>
                            <input type="text" name="directors[0][position]" class="kyc-form-input" value="<?php echo esc_attr($form_data->directors_data_parsed[0]->position ?? ''); ?>" required>
                        </div>
                        <div class="kyc-form-group">
                            <label class="kyc-form-label required">Porcentagem de Participação</label>
                            <input type="number" name="directors[0][ownership]" class="kyc-form-input" min="0" max="100" step="0.01" value="<?php echo esc_attr($form_data->directors_data_parsed[0]->ownership ?? ''); ?>" required>
                        </div>
                        <div class="kyc-form-group">
                            <label class="kyc-form-label required">Documento do Diretor</label>
                            <input type="file" name="directors[0][id_document]" class="kyc-file-input" accept=".jpg,.jpeg,.png,.pdf" <?php echo empty($form_data->directors_data_parsed[0]->id_document_url ?? '') ? 'required' : ''; ?>>
                            <?php if (!empty($form_data->directors_data_parsed[0]->id_document_url ?? '')): ?>
                                <small style="color: #28a745;">Documento enviado: <a href="<?php echo esc_url($form_data->directors_data_parsed[0]->id_document_url); ?>" target="_blank">Visualizar</a></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="button" class="add-director-btn" onclick="addDirector()">+ Adicionar Outro Diretor</button>
            
            <div class="kyc-form-group" style="margin-top: 20px;">
                <label class="kyc-form-label" for="kyc_shareholdings">Estrutura Societária (Opcional)</label>
                <textarea id="kyc_shareholdings" name="shareholdings" class="kyc-form-input kyc-form-textarea" 
                          placeholder="Descreva a estrutura societária da sua empresa..."><?php echo esc_textarea($form_data->shareholdings ?? ''); ?></textarea>
            </div>
        </div>

        <!-- Documents Section -->
        <div class="kyc-form-section">
            <h3 class="kyc-section-title">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14,2 14,8 20,8"/>
                </svg>
                Documentos Obrigatórios da Empresa
            </h3>
            
            <div class="kyc-form-grid">
                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="kyc_company_registration">Contrato Social</label>
                    <input type="file" id="kyc_company_registration" name="company_registration_certificate" class="kyc-file-input" 
                           accept=".jpg,.jpeg,.png,.pdf" required>
                    <small style="color: #6c757d;">Envie o contrato social da sua empresa (JPG, PNG, PDF)</small>
                </div>

                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="kyc_business_license">Alvará de Funcionamento</label>
                    <input type="file" id="kyc_business_license" name="business_license" class="kyc-file-input" 
                           accept=".jpg,.jpeg,.png,.pdf" required>
                    <small style="color: #6c757d;">Envie o alvará de funcionamento ou licença comercial (JPG, PNG, PDF)</small>
                </div>

                <div class="kyc-form-group">
                    <label class="kyc-form-label required" for="kyc_address_proof">Comprovante de Endereço da Empresa</label>
                    <input type="file" id="kyc_address_proof" name="address_proof" class="kyc-file-input" 
                           accept=".jpg,.jpeg,.png,.pdf" required>
                    <small style="color: #6c757d;">Envie conta de luz, contrato de aluguel ou extrato bancário mostrando o endereço da empresa (JPG, PNG, PDF)</small>
                </div>

                <div class="kyc-form-group">
                    <label class="kyc-form-label" for="kyc_bank_statement">Extrato Bancário (Opcional)</label>
                    <input type="file" id="kyc_bank_statement" name="bank_statement" class="kyc-file-input" 
                           accept=".jpg,.jpeg,.png,.pdf">
                    <small style="color: #6c757d;">Envie extrato bancário recente da empresa para verificação (JPG, PNG, PDF)</small>
                </div>
            </div>
        </div>

        <!-- Submit Section -->
        <div class="kyc-submit-section">
            <div style="margin-bottom: 20px;">
                <button type="button" id="saveDraftBtn" class="kyc-save-draft-btn">Salvar como Rascunho</button>
                <button type="submit" id="submitKycBtn" class="kyc-submit-btn">Enviar para Verificação</button>
            </div>
            <p style="font-size: 0.9rem; color: #6c757d; margin: 0;">
                Ao enviar este formulário, você confirma que todas as informações fornecidas são precisas e concorda com nosso processo de verificação KYC Empresarial.
            </p>
        </div>
    </form>
</div>

<script>
let directorCount = 1;

function addDirector() {
    const container = document.getElementById('directorsContainer');
    const newDirector = document.createElement('div');
    newDirector.className = 'kyc-director-item';
    newDirector.innerHTML = `
        <button type="button" class="remove-director-btn" onclick="removeDirector(this)">Remover</button>
        <div class="kyc-form-grid">
            <div class="kyc-form-group">
                <label class="kyc-form-label required">Nome Completo do Diretor</label>
                <input type="text" name="directors[${directorCount}][name]" class="kyc-form-input" required>
            </div>
            <div class="kyc-form-group">
                <label class="kyc-form-label required">Cargo/Função</label>
                <input type="text" name="directors[${directorCount}][position]" class="kyc-form-input" required>
            </div>
            <div class="kyc-form-group">
                <label class="kyc-form-label required">Porcentagem de Participação</label>
                <input type="number" name="directors[${directorCount}][ownership]" class="kyc-form-input" min="0" max="100" step="0.01" required>
            </div>
            <div class="kyc-form-group">
                <label class="kyc-form-label required">Documento do Diretor</label>
                <input type="file" name="directors[${directorCount}][id_document]" class="kyc-file-input" accept=".jpg,.jpeg,.png,.pdf" required>
            </div>
        </div>
    `;
    
    container.appendChild(newDirector);
    directorCount++;
    
    // Show remove buttons if more than one director
    const removeButtons = container.querySelectorAll('.remove-director-btn');
    removeButtons.forEach(btn => btn.style.display = 'inline-block');
}

function removeDirector(button) {
    const container = document.getElementById('directorsContainer');
    if (container.children.length > 1) {
        button.parentElement.remove();
        
        // Hide remove buttons if only one director remains
        if (container.children.length === 1) {
            container.querySelector('.remove-director-btn').style.display = 'none';
        }
    }
}

// Load existing directors data if available
<?php if (!empty($form_data->directors_data_parsed)): ?>
const existingDirectors = <?php echo json_encode($form_data->directors_data_parsed); ?>;
if (existingDirectors && existingDirectors.length > 1) {
    loadAdditionalDirectors(existingDirectors);
}
<?php endif; ?>

function loadAdditionalDirectors(directorsData) {
    // Skip first director (already loaded), add the rest
    for (let i = 1; i < directorsData.length; i++) {
        const director = directorsData[i];
        const container = document.getElementById('directorsContainer');
        const newDirector = document.createElement('div');
        newDirector.className = 'kyc-director-item';
        newDirector.innerHTML = `
            <button type="button" class="remove-director-btn" onclick="removeDirector(this)">Remover</button>
            <div class="kyc-form-grid">
                <div class="kyc-form-group">
                    <label class="kyc-form-label required">Nome Completo do Diretor</label>
                    <input type="text" name="directors[${i}][name]" class="kyc-form-input" value="${director.name || ''}" required>
                </div>
                <div class="kyc-form-group">
                    <label class="kyc-form-label required">Cargo/Função</label>
                    <input type="text" name="directors[${i}][position]" class="kyc-form-input" value="${director.position || ''}" required>
                </div>
                <div class="kyc-form-group">
                    <label class="kyc-form-label required">Porcentagem de Participação</label>
                    <input type="number" name="directors[${i}][ownership]" class="kyc-form-input" min="0" max="100" step="0.01" value="${director.ownership || ''}" required>
                </div>
                <div class="kyc-form-group">
                    <label class="kyc-form-label required">Documento do Diretor</label>
                    <input type="file" name="directors[${i}][id_document]" class="kyc-file-input" accept=".jpg,.jpeg,.png,.pdf" ${director.id_document_url ? '' : 'required'}>
                    ${director.id_document_url ? `<small style="color: #28a745;">Documento enviado: <a href="${director.id_document_url}" target="_blank">Visualizar</a></small>` : ''}
                </div>
            </div>
        `;
        container.appendChild(newDirector);
        directorCount++;
    }
    
    // Show remove buttons if more than one director
    const removeButtons = container.querySelectorAll('.remove-director-btn');
    removeButtons.forEach(btn => btn.style.display = 'inline-block');
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('kycCompanyForm');
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
                alert('Rascunho salvo com sucesso!');
            } else {
                alert('Falha ao salvar rascunho: ' + data.data);
            }
        })
        .catch(error => {
            console.error('Erro ao salvar rascunho:', error);
            alert('Erro ao salvar rascunho');
        });
    });
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Enviando...';
        
        const formData = new FormData(form);
        
        fetch(affiliate_ajax.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Aplicação KYC Empresarial enviada com sucesso!');
                // Redirect to dashboard to show updated status
                const dashboardUrl = window.location.origin + window.location.pathname.replace(/\/afiliado-kyc.*/, '/afiliado-dashboard');
                window.location.href = dashboardUrl;
            } else {
                alert('Falha ao enviar KYC Empresarial: ' + data.data);
            }
        })
        .catch(error => {
            console.error('Erro ao enviar KYC Empresarial:', error);
            alert('Erro ao enviar aplicação KYC Empresarial');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Enviar para Verificação';
        });
    });
});
</script>