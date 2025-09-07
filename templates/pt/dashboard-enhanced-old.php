<?php
// Portuguese Dashboard Template
if (!defined('ABSPATH')) {
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['affiliate_user_id'])) {
    wp_redirect(get_permalink(get_page_by_path('afiliado-login')));
    exit;
}

global $wpdb;
$user_id = $_SESSION['affiliate_user_id'];
$user = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}affiliate_users WHERE id = %d", 
    $user_id
));

if (!$user) {
    wp_redirect(get_permalink(get_page_by_path('afiliado-login')));
    exit;
}
?>

<div class="affiliate-portal-container">
    <div class="affiliate-dashboard-wrapper">
        <!-- Dashboard Header -->
        <div class="affiliate-dashboard-header">
            <div class="affiliate-header-content">
                <h1 class="affiliate-dashboard-title">Painel do Afiliado</h1>
                <div class="affiliate-header-actions">
                    <button class="affiliate-btn affiliate-btn-outline affiliate-btn-sm" onclick="showChangePasswordModal()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/>
                        </svg>
                        Alterar Senha
                    </button>
                    <button class="affiliate-btn affiliate-btn-danger affiliate-btn-sm" onclick="affiliateLogout()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0 0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0 0,1 14,22H5A2,2 0 0,1 3,20V4A2,2 0 0,1 5,2H14Z"/>
                        </svg>
                        Sair
                    </button>
                </div>
            </div>
        </div>

    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="welcome-card">
            <h2><?php _e('Bem-vindo', 'affiliate-portal'); ?>, <?php echo esc_html($user->first_name); ?>!</h2>
            <p><?php _e('Gerencie sua conta de afiliado e acompanhe o status da sua aplicação.', 'affiliate-portal'); ?></p>
        </div>
        
        <div class="status-card">
            <h3><?php _e('Status da aplicação', 'affiliate-portal'); ?></h3>
            <div class="status-display">
                <span class="status-badge <?php echo esc_attr(strtolower(str_replace(' ', '-', $user->status))); ?>">
                    <?php 
                    $status_translations = array(
                        'Pending Approval' => __('Aguardando aprovação', 'affiliate-portal'),
                        'Approved' => __('Aprovado', 'affiliate-portal'),
                        'Rejected' => __('Rejeitado', 'affiliate-portal'),
                        'Under Review' => __('Em análise', 'affiliate-portal')
                    );
                    echo isset($status_translations[$user->status]) ? $status_translations[$user->status] : $user->status;
                    ?>
                </span>
            </div>
            <?php if (!empty($user->admin_remarks)): ?>
            <div class="admin-remarks">
                <h4><?php _e('Observações do administrador', 'affiliate-portal'); ?>:</h4>
                <p><?php echo esc_html($user->admin_remarks); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Content -->
    <div class="dashboard-content">
        <!-- Account Information -->
        <div class="info-section">
            <h3><?php _e('Informações da conta', 'affiliate-portal'); ?></h3>
            <div class="info-grid">
                <div class="info-item">
                    <label><?php _e('Nome de usuário', 'affiliate-portal'); ?>:</label>
                    <span><?php echo esc_html($user->username); ?></span>
                </div>
                <div class="info-item">
                    <label><?php _e('Data de registro', 'affiliate-portal'); ?>:</label>
                    <span><?php echo date_i18n(get_option('date_format'), strtotime($user->created_at)); ?></span>
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="info-section">
            <h3><?php _e('Informações pessoais', 'affiliate-portal'); ?></h3>
            <div class="info-grid">
                <div class="info-item">
                    <label><?php _e('Nome completo', 'affiliate-portal'); ?>:</label>
                    <span><?php echo esc_html(trim($user->name_prefix . ' ' . $user->first_name . ' ' . $user->last_name)); ?></span>
                </div>
                <div class="info-item">
                    <label><?php _e('Data de nascimento', 'affiliate-portal'); ?>:</label>
                    <span><?php echo $user->DOB ? date_i18n(get_option('date_format'), strtotime($user->DOB)) : __('Não informado', 'affiliate-portal'); ?></span>
                </div>
                <div class="info-item">
                    <label><?php _e('Tipo', 'affiliate-portal'); ?>:</label>
                    <span><?php 
                    $type_translations = array(
                        'Individual' => __('Individual', 'affiliate-portal'),
                        'Company' => __('Empresa', 'affiliate-portal')
                    );
                    echo isset($type_translations[$user->type]) ? $type_translations[$user->type] : $user->type;
                    ?></span>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="info-section">
            <h3><?php _e('Informações de contato', 'affiliate-portal'); ?></h3>
            <div class="info-grid">
                <div class="info-item">
                    <label><?php _e('Email', 'affiliate-portal'); ?>:</label>
                    <span><?php echo esc_html($user->email); ?></span>
                </div>
                <div class="info-item">
                    <label><?php _e('Número do celular', 'affiliate-portal'); ?>:</label>
                    <span><?php echo esc_html($user->country_code . ' ' . $user->mobile_number); ?></span>
                </div>
                <div class="info-item">
                    <label><?php _e('Nome da empresa', 'affiliate-portal'); ?>:</label>
                    <span><?php echo $user->company_name ? esc_html($user->company_name) : __('Não informado', 'affiliate-portal'); ?></span>
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="info-section">
            <h3><?php _e('Informações de endereço', 'affiliate-portal'); ?></h3>
            <div class="info-grid">
                <div class="info-item full-width">
                    <label><?php _e('Endereço', 'affiliate-portal'); ?>:</label>
                    <span>
                        <?php 
                        $address_parts = array_filter([
                            $user->address_line1,
                            $user->address_line2,
                            $user->city,
                            $user->state,
                            $user->country,
                            $user->zipcode
                        ]);
                        echo esc_html(implode(', ', $address_parts));
                        ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Business Information -->
        <div class="info-section">
            <h3><?php _e('Informações comerciais', 'affiliate-portal'); ?></h3>
            <div class="info-grid">
                <div class="info-item">
                    <label><?php _e('Tipo de afiliado', 'affiliate-portal'); ?>:</label>
                    <span><?php 
                    $affiliate_type_translations = array(
                        'Influencer' => __('Influenciador', 'affiliate-portal'),
                        'Content Creator' => __('Criador de conteúdo', 'affiliate-portal'),
                        'Media Buyer' => __('Comprador de mídia', 'affiliate-portal'),
                        'Email Marketer' => __('Profissional de email marketing', 'affiliate-portal'),
                        'Social Media Manager' => __('Gerente de mídias sociais', 'affiliate-portal'),
                        'PPC Specialist' => __('Especialista em PPC', 'affiliate-portal'),
                        'SEO Specialist' => __('Especialista em SEO', 'affiliate-portal'),
                        'Blogger' => __('Blogueiro', 'affiliate-portal'),
                        'YouTuber' => __('YouTuber', 'affiliate-portal'),
                        'Website Owner' => __('Proprietário de site', 'affiliate-portal'),
                        'App Developer' => __('Desenvolvedor de aplicativos', 'affiliate-portal'),
                        'Other' => __('Outro', 'affiliate-portal')
                    );
                    echo isset($affiliate_type_translations[$user->affiliate_type]) ? $affiliate_type_translations[$user->affiliate_type] : $user->affiliate_type;
                    ?></span>
                </div>
                <div class="info-item">
                    <label><?php _e('Moeda', 'affiliate-portal'); ?>:</label>
                    <span><?php echo esc_html($user->currency); ?></span>
                </div>
                <div class="info-item">
                    <label><?php _e('ID do chat/Canal', 'affiliate-portal'); ?>:</label>
                    <span><?php echo esc_html($user->chat_id_channel); ?></span>
                </div>
            </div>
        </div>

        <!-- KYC Section -->
        <div class="info-section">
            <h3><?php _e('Documentação KYC', 'affiliate-portal'); ?></h3>
            <div class="kyc-content">
                <p><?php _e('Por favor, baixe e preencha o formulário KYC, depois envie para office@gemmagics.com', 'affiliate-portal'); ?></p>
                <a href="<?php echo AFFILIATE_PORTAL_URL; ?>assets/KYC_Form.xlsx" 
                   class="btn btn-primary download-btn" 
                   download>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                    </svg>
                    <?php _e('Baixar formulário KYC', 'affiliate-portal'); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div id="changePasswordModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><?php _e('Alterar senha', 'affiliate-portal'); ?></h3>
            <span class="close" onclick="hideChangePasswordModal()">&times;</span>
        </div>
        <form id="changePasswordForm">
            <div class="form-group">
                <label for="current_password"><?php _e('Senha atual', 'affiliate-portal'); ?>:</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password"><?php _e('Nova senha', 'affiliate-portal'); ?>:</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password"><?php _e('Confirmar nova senha', 'affiliate-portal'); ?>:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="hideChangePasswordModal()">
                    <?php _e('Cancelar', 'affiliate-portal'); ?>
                </button>
                <button type="submit" class="btn btn-primary">
                    <?php _e('Alterar senha', 'affiliate-portal'); ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showChangePasswordModal() {
    document.getElementById('changePasswordModal').style.display = 'flex';
}

function hideChangePasswordModal() {
    document.getElementById('changePasswordModal').style.display = 'none';
    document.getElementById('changePasswordForm').reset();
}

function affiliateLogout() {
    if (confirm('<?php _e('Tem certeza que deseja sair?', 'affiliate-portal'); ?>')) {
        window.location.href = '<?php echo esc_url(get_permalink(get_page_by_path('afiliado-login'))); ?>?action=logout';
    }
}

// Handle change password form
document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (newPassword !== confirmPassword) {
        alert('<?php _e('As senhas não coincidem', 'affiliate-portal'); ?>');
        return;
    }
    
    // Handle password change via AJAX
    const formData = new FormData(this);
    formData.append('action', 'affiliate_change_password');
    formData.append('nonce', '<?php echo wp_create_nonce('affiliate_change_password'); ?>');
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('<?php _e('Senha alterada com sucesso', 'affiliate-portal'); ?>');
            hideChangePasswordModal();
        } else {
            alert(data.data || '<?php _e('Erro ao alterar senha', 'affiliate-portal'); ?>');
        }
    })
    .catch(error => {
        alert('<?php _e('Erro ao alterar senha', 'affiliate-portal'); ?>');
    });
});
</script>