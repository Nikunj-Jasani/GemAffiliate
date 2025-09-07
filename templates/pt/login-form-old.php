<?php
// Portuguese Login Form Template
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="affiliate-form-container">
    <div class="language-switcher">
        <a href="<?php echo esc_url(get_permalink(get_page_by_path('affiliate-login'))); ?>" 
           class="lang-btn <?php echo (get_locale() == 'en_US') ? 'active' : ''; ?>">English</a>
        <a href="<?php echo esc_url(get_permalink(get_page_by_path('afiliado-login'))); ?>" 
           class="lang-btn <?php echo (get_locale() == 'pt_BR') ? 'active' : ''; ?>">Português</a>
    </div>

    <div class="affiliate-form-header">
        <h2><?php _e('Login de Afiliado', 'affiliate-portal'); ?></h2>
        <p><?php _e('Acesse sua conta de afiliado', 'affiliate-portal'); ?></p>
    </div>
    
    <form id="affiliateLoginForm" method="post" class="affiliate-form">
        <div class="form-group">
            <label for="affiliate_username"><?php _e('Nome de usuário', 'affiliate-portal'); ?> *</label>
            <input type="text" 
                   id="affiliate_username" 
                   name="affiliate_username" 
                   required 
                   placeholder="<?php _e('Digite seu nome de usuário', 'affiliate-portal'); ?>">
        </div>
        
        <div class="form-group">
            <label for="affiliate_password"><?php _e('Senha', 'affiliate-portal'); ?> *</label>
            <input type="password" 
                   id="affiliate_password" 
                   name="affiliate_password" 
                   required 
                   placeholder="<?php _e('Digite sua senha', 'affiliate-portal'); ?>">
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10,17V14H3V10H10V7L15,12L10,17Z"/>
                </svg>
                <?php _e('Entrar', 'affiliate-portal'); ?>
            </button>
        </div>
        
        <div class="form-footer">
            <p><?php _e('Não tem uma conta?', 'affiliate-portal'); ?> 
               <a href="<?php echo esc_url(get_permalink(get_page_by_path('afiliado-registro'))); ?>">
                   <?php _e('Registrar agora', 'affiliate-portal'); ?>
               </a>
            </p>
        </div>
    </form>
</div>

<style>
.language-switcher {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
    gap: 10px;
}

.lang-btn {
    padding: 8px 16px;
    background: #f8f9fa;
    color: #6c757d;
    text-decoration: none;
    border-radius: 20px;
    font-size: 14px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.lang-btn:hover {
    background: #e9ecef;
    color: #495057;
    text-decoration: none;
}

.lang-btn.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: 2px solid #667eea;
}

.affiliate-form-header {
    text-align: center;
    margin-bottom: 30px;
}

.affiliate-form-header h2 {
    color: #2c3e50;
    margin-bottom: 10px;
    font-weight: 600;
}

.affiliate-form-header p {
    color: #6c757d;
    margin: 0;
}
</style>