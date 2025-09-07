<?php
// Portuguese Admin Login Template
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="affiliate-admin-container">
    <div class="affiliate-form-container admin-login-form">
        <div class="affiliate-brand-header">
            <h1 class="affiliate-brand-title">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px;">
                    <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
                </svg>
                Portal do Administrador Principal
            </h1>
            <p class="affiliate-brand-slogan">Acesso Administrativo Seguro</p>
        </div>

        <form id="affiliateAdminLoginForm" class="affiliate-form" method="post">
            <div class="affiliate-form-group">
                <label for="admin_username" class="affiliate-form-label">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                    </svg>
                    Nome de usuário
                </label>
                <input type="text" id="admin_username" name="admin_username" class="affiliate-form-control" required>
            </div>

            <div class="affiliate-form-group">
                <label for="admin_password" class="affiliate-form-label">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/>
                    </svg>
                    Senha
                </label>
                <input type="password" id="admin_password" name="admin_password" class="affiliate-form-control" required>
            </div>

            <button type="submit" class="affiliate-btn affiliate-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10,17V14H3V10H10V7L15,12L10,17M10,2H19A2,2 0 0,1 21,4V20A2,2 0 0,1 19,22H10A2,2 0 0,1 8,20V18H10V20H19V4H10V6H8V4A2,2 0 0,1 10,2Z"/>
                </svg>
                Entrar no Portal Administrativo
            </button>
        </form>

    </div>
</div>

<style>
.affiliate-admin-container {
    max-width: 400px;
    margin: 0 auto;
    padding: 40px 20px;
}

.admin-login-form {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    padding: 40px;
    border: 1px solid #e1e5e9;
}

.affiliate-admin-info {
    margin-top: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #6c757d;
}

.affiliate-admin-info p {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Fix button expansion on admin login */
.admin-login-form .affiliate-btn {
    width: 100% !important;
    max-width: 100% !important;
    min-width: 200px !important;
    height: 48px !important;
    min-height: 48px !important;
    padding: 12px 24px !important;
    font-size: 1rem !important;
    box-sizing: border-box !important;
    text-align: center !important;
    justify-content: center !important;
    display: flex !important;
    align-items: center !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

/* Ensure button doesn't expand during validation */
.admin-login-form .affiliate-btn:hover,
.admin-login-form .affiliate-btn:focus,
.admin-login-form .affiliate-btn:active,
.admin-login-form .affiliate-btn:disabled,
.admin-login-form .affiliate-btn.loading {
    width: 100% !important;
    max-width: 100% !important;
    height: 48px !important;
    min-height: 48px !important;
    padding: 12px 24px !important;
    font-size: 1rem !important;
    text-align: center !important;
    justify-content: center !important;
    transform: none !important;
    box-sizing: border-box !important;
}

/* Admin form container width constraint */
.affiliate-admin-container {
    max-width: 400px !important;
    width: 100% !important;
    box-sizing: border-box !important;
}

.admin-login-form {
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
}

/* Form validation error handling */
.admin-login-form .affiliate-form-group {
    margin-bottom: 20px !important;
    box-sizing: border-box !important;
}

/* Ensure form inputs don't cause layout shifts */
.admin-login-form .affiliate-form-control {
    width: 100% !important;
    box-sizing: border-box !important;
}

/* Message container styling */
.affiliate-message {
    padding: 12px 16px;
    margin-bottom: 20px;
    border-radius: 8px;
    font-weight: 500;
    display: none;
}

.affiliate-message.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.affiliate-message.danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.affiliate-message.warning {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.affiliate-message.info {
    background-color: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}
</style>

<script>
// Include the affiliate AJAX object if not already included
if (typeof affiliate_ajax === 'undefined') {
    var affiliate_ajax = {
        ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>',
        nonce: '<?php echo wp_create_nonce('affiliate_nonce'); ?>'
    };
}

document.addEventListener('DOMContentLoaded', function() {
    initAdminFunctionality();
});

function initAdminFunctionality() {
    // Admin login form
    const adminLoginForm = document.getElementById('affiliateAdminLoginForm');
    if (adminLoginForm) {
        adminLoginForm.addEventListener('submit', handleAdminLogin);
    }
}

function handleAdminLogin(e) {
    e.preventDefault();
    
    const username = document.getElementById('admin_username').value;
    const password = document.getElementById('admin_password').value;
    
    if (!username || !password) {
        showMessage('Por favor, preencha todos os campos', 'danger');
        return;
    }
    
    const button = e.target.querySelector('button[type="submit"]');
    const hideLoading = showLoading(button);
    
    const formData = new FormData();
    formData.append('action', 'affiliate_admin_login');
    formData.append('admin_username', username);
    formData.append('admin_password', password);
    formData.append('nonce', affiliate_ajax.nonce);
    
    fetch(affiliate_ajax.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            showMessage('Login realizado com sucesso', 'success');
            setTimeout(() => {
                if (data.data.redirect) {
                    window.location.href = data.data.redirect;
                } else {
                    // Fallback redirect for Portuguese - try Portuguese admin dashboard
                    <?php 
                    $admin_dashboard = get_page_by_title('Painel do Administrador');
                    if ($admin_dashboard) {
                        $fallback_url = get_permalink($admin_dashboard);
                    } else {
                        $admin_dashboard_by_slug = get_page_by_path('painel-admin');
                        if ($admin_dashboard_by_slug) {
                            $fallback_url = get_permalink($admin_dashboard_by_slug);
                        } else {
                            // Fallback to English admin dashboard
                            $admin_dashboard_en = get_page_by_title('Admin Dashboard');
                            if ($admin_dashboard_en) {
                                $fallback_url = get_permalink($admin_dashboard_en);
                            } else {
                                $fallback_url = home_url('/admin-dashboard/');
                            }
                        }
                    }
                    ?>
                    window.location.href = '<?php echo esc_url($fallback_url); ?>';
                }
            }, 1000);
        } else {
            showMessage(data.data || 'Falha no login', 'danger');
        }
    })
    .catch(error => {
        hideLoading();
        showMessage('Ocorreu um erro. Por favor, tente novamente.', 'danger');
        console.error('Admin login error:', error);
    });
}

function showMessage(message, type = 'info') {
    // Remove existing message
    const existingMessage = document.querySelector('.affiliate-message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // Create new message element
    const messageDiv = document.createElement('div');
    messageDiv.className = `affiliate-message ${type}`;
    messageDiv.textContent = message;
    messageDiv.style.display = 'block';
    
    // Insert before the form
    const form = document.getElementById('affiliateAdminLoginForm');
    form.parentNode.insertBefore(messageDiv, form);
    
    // Auto-hide success and info messages after 5 seconds
    if (type === 'success' || type === 'info') {
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.remove();
            }
        }, 5000);
    }
}

function showLoading(button) {
    const originalText = button.innerHTML;
    button.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="animation: spin 1s linear infinite;"><path d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z"/></svg> Entrando...';
    button.disabled = true;
    
    return function() {
        button.innerHTML = originalText;
        button.disabled = false;
    };
}
</script>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>