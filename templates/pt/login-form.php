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
                <h1 class="affiliate-brand-title">Bem-vindo de Volta</h1>
                <p class="affiliate-brand-slogan">Acesse sua conta de afiliado</p>
                <div class="affiliate-brand-features">
                    <div class="affiliate-feature-item">
                        <svg class="affiliate-feature-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9,12L11,14L15,10M21,5V19A2,2 0 0,1 19,21H5A2,2 0 0,1 3,19V5A2,2 0 0,1 5,3H19A2,2 0 0,1 21,5Z"/>
                        </svg>
                        <span>Acesso Seguro</span>
                    </div>
                    <div class="affiliate-feature-item">
                        <svg class="affiliate-feature-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12,8A4,4 0 0,1 16,12A4,4 0 0,1 12,16A4,4 0 0,1 8,12A4,4 0 0,1 12,8M12,10A2,2 0 0,0 10,12A2,2 0 0,0 12,14A2,2 0 0,0 14,12A2,2 0 0,0 12,10Z"/>
                        </svg>
                        <span>Painel Personalizado</span>
                    </div>
                    <div class="affiliate-feature-item">
                        <svg class="affiliate-feature-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M13,9H11V7H13M13,17H11V11H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
                        </svg>
                        <span>Suporte 24/7</span>
                    </div>
                </div>
                

            </div>
        </div>
        
        <!-- Right Column - Login Form -->
        <div class="affiliate-form-column">
            <div class="affiliate-form-container">
                <div class="affiliate-form-header">
                    <h2>Entrar na Conta</h2>
                    <p>Entre com suas credenciais para acessar seu painel</p>
                </div>
                
                <form class="affiliate-form" id="affiliateLoginForm">
                    <div class="affiliate-form-group">
                        <label for="affiliate_username" class="affiliate-form-label">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                            </svg>
                            Nome de usuário ou Email
                        </label>
                        <input type="text" id="affiliate_username" name="username" class="affiliate-form-control" required>
                    </div>
                    
                    <div class="affiliate-form-group">
                        <label for="affiliate_password" class="affiliate-form-label">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/>
                            </svg>
                            Senha
                        </label>
                        <input type="password" id="affiliate_password" name="password" class="affiliate-form-control" required>
                    </div>
                    
                    <div class="affiliate-form-options">
                        <div class="affiliate-form-check">
                            <input type="checkbox" id="remember_me" name="remember_me" class="affiliate-form-check-input">
                            <label for="remember_me" class="affiliate-form-check-label">Lembrar-me</label>
                        </div>
                        <a href="#" class="affiliate-forgot-link">Esqueceu a senha?</a>
                    </div>
                    
                    <button type="submit" class="affiliate-btn affiliate-btn-primary affiliate-btn-auth">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M10,17V14H3V10H10V7L15,12L10,17M10,2H19A2,2 0 0,1 21,4V20A2,2 0 0,1 19,22H10A2,2 0 0,1 8,20V18H10V20H19V4H10V6H8V4A2,2 0 0,1 10,2Z"/>
                        </svg>
                        Entrar
                    </button>
                </form>
                
                <div class="affiliate-auth-footer">
                    <div class="affiliate-auth-links">
                        <p>Não tem uma conta? <a href="<?php echo home_url('/afiliado-registro/'); ?>" class="affiliate-auth-link">Registre-se aqui</a></p>
                    </div>
                    <div class="affiliate-home-redirect">
                        <a href="<?php echo home_url('/pt'); ?>" class="affiliate-btn affiliate-btn-outline affiliate-btn-sm">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z"/>
                            </svg>
                            Voltar ao Início
                        </a>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<script>
// Define affiliate_ajax object for AJAX calls
if (typeof affiliate_ajax === 'undefined') {
    var affiliate_ajax = {
        ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>',
        nonce: '<?php echo wp_create_nonce('affiliate_nonce'); ?>'
    };
}
</script>
<script src="<?php echo plugin_dir_url(__FILE__) . '../assets/script-pt.js'; ?>?v=<?php echo time(); ?>"></script>