<?php
// Portuguese Registration Form Template
if (!defined('ABSPATH')) {
    exit;
}
?>

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
                <?php
                // Get custom brand settings with Portuguese translations
                $brand_title = 'Junte-se à Nossa Rede';
                $brand_slogan = 'Inicie sua jornada de afiliado conosco';
                ?>
                <h1 class="affiliate-brand-title"><?php echo esc_html($brand_title); ?></h1>
                <p class="affiliate-brand-slogan"><?php echo esc_html($brand_slogan); ?></p>
                <div class="affiliate-brand-features">
                    <div class="affiliate-feature-item">
                        <svg class="affiliate-feature-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M2.81 14.12L5.64 11.29C8.34 10.95 10.7 9.17 12.2 6.69C13.7 4.22 14.24 1.33 13.81 0.1L13.79 0C13.78 0 13.78 0 13.77 0C13.3 0 12.9 0.25 12.69 0.64L10.06 5.86C9.5 7 8.45 7.88 7.22 8.32L2.63 9.82C2.09 9.97 1.74 10.46 1.74 11C1.74 11.54 2.09 12.03 2.63 12.18L2.81 14.12Z"/>
                        </svg>
                        <span>Configuração Rápida</span>
                    </div>
                    <div class="affiliate-feature-item">
                        <svg class="affiliate-feature-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7,15H9C9,16.08 10.37,17 12,17C13.63,17 15,16.08 15,15C15,13.9 13.96,13.5 11.76,12.97C9.64,12.44 7,11.78 7,9C7,7.21 8.47,5.69 10.5,5.18V3H13.5V5.18C15.53,5.69 17,7.21 17,9H15C15,7.92 13.63,7 12,7C10.37,7 9,7.92 9,9C9,10.1 10.04,10.5 12.24,11.03C14.36,11.56 17,12.22 17,15C17,16.79 15.53,18.31 13.5,18.82V21H10.5V18.82C8.47,18.31 7,16.79 7,15Z"/>
                        </svg>
                        <span>Taxas Competitivas</span>
                    </div>
                    <div class="affiliate-feature-item">
                        <svg class="affiliate-feature-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.9,17.39C17.64,16.59 16.89,16 16,16H15V13A1,1 0 0,0 14,12H8V10H10A1,1 0 0,0 11,9V7H13A2,2 0 0,0 15,5V4.59C17.93,5.77 20,8.64 20,12C20,14.08 19.2,15.97 17.9,17.39M11,19.93C7.05,19.44 4,16.08 4,12C4,11.38 4.08,10.78 4.21,10.21L9,15V16A2,2 0 0,0 11,18M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
                        </svg>
                        <span>Alcance Global</span>
                    </div>
                </div>
                
                <!-- Language Switcher -->
                <div class="language-switcher">
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('affiliate-registration'))); ?>" 
                       class="lang-btn">English</a>
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('afiliado-registro'))); ?>" 
                       class="lang-btn active">Português</a>
                </div>
            </div>
        </div>
        
        <!-- Right Column - Registration Form -->
        <div class="affiliate-form-column">
            <div class="affiliate-form-container">
                <div class="affiliate-form-header">
                    <h2>Criar Conta</h2>
                    <p>Preencha suas informações para começar</p>
                </div>
    
                <!-- Step Indicator -->
                <div class="affiliate-step-indicator">
                    <div class="affiliate-step active" data-step="1">
                        <div class="affiliate-step-number">1</div>
                        <div class="affiliate-step-title">Credenciais</div>
                    </div>
                    <div class="affiliate-step" data-step="2">
                        <div class="affiliate-step-number">2</div>
                        <div class="affiliate-step-title">Pessoal</div>
                    </div>
                    <div class="affiliate-step" data-step="3">
                        <div class="affiliate-step-number">3</div>
                        <div class="affiliate-step-title">Contato</div>
                    </div>
                    <div class="affiliate-step" data-step="4">
                        <div class="affiliate-step-number">4</div>
                        <div class="affiliate-step-title">Endereço</div>
                    </div>
                    <div class="affiliate-step" data-step="5">
                        <div class="affiliate-step-number">5</div>
                        <div class="affiliate-step-title">Negócio</div>
                    </div>
                </div>
                
                <form id="affiliateRegistrationForm" method="post" class="affiliate-form">
                    <!-- Step 1: Account Credentials -->
                    <div class="affiliate-form-step active" data-step="1">
                        <h3>Passo 1: Credenciais da Conta</h3>
                        
                            <div class="affiliate-form-group">
                                <label for="username">Nome de usuário *</label>
                                <input type="text" 
                                       id="username" 
                                       name="username" 
                                       class="affiliate-form-control" 
                                       required 
                                       placeholder="Digite seu nome de usuário">
                            </div>
                            
                            <div class="affiliate-form-group">
                                <label for="password">Senha *</label>
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       class="affiliate-form-control" 
                                       required 
                                       placeholder="Digite sua senha">
                            </div>
                            
                            <div class="affiliate-form-group">
                                <label for="securityQue">Pergunta de segurança *</label>
                                <select id="securityQue" name="securityQue" class="affiliate-form-control" required>
                                    <option value="">Selecione uma pergunta</option>
                                    <option value="pet">Qual era o nome do seu primeiro animal de estimação?</option>
                                    <option value="mother">Qual era o nome de solteira da sua mãe?</option>
                                    <option value="city">Em que cidade você nasceu?</option>
                                    <option value="school">Qual era o nome da sua primeira escola?</option>
                                    <option value="movie">Qual é o seu filme favorito?</option>
                                </select>
                            </div>
                            
                            <div class="affiliate-form-group">
                                <label for="securityAns">Resposta de segurança *</label>
                                <input type="text" 
                                       id="securityAns" 
                                       name="securityAns" 
                                       class="affiliate-form-control" 
                                       required 
                                       placeholder="Digite sua resposta">
                            </div>
                        </div>
        
        <!-- Step 2: Personal Information -->
        <div class="form-step" data-step="2">
            <h3><?php _e('Passo 2: Informações pessoais', 'affiliate-portal'); ?></h3>
            
            <div class="form-group">
                <label for="namePrefix"><?php _e('Prefixo do nome', 'affiliate-portal'); ?></label>
                <select id="namePrefix" name="namePrefix">
                    <option value=""><?php _e('Selecione', 'affiliate-portal'); ?></option>
                    <option value="Mr."><?php _e('Sr.', 'affiliate-portal'); ?></option>
                    <option value="Mrs."><?php _e('Sra.', 'affiliate-portal'); ?></option>
                    <option value="Ms."><?php _e('Srta.', 'affiliate-portal'); ?></option>
                    <option value="Dr."><?php _e('Dr.', 'affiliate-portal'); ?></option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="firstName"><?php _e('Primeiro nome', 'affiliate-portal'); ?> *</label>
                <input type="text" 
                       id="firstName" 
                       name="firstName" 
                       required 
                       placeholder="<?php _e('Digite seu primeiro nome', 'affiliate-portal'); ?>">
            </div>
            
            <div class="form-group">
                <label for="lastName"><?php _e('Sobrenome', 'affiliate-portal'); ?> *</label>
                <input type="text" 
                       id="lastName" 
                       name="lastName" 
                       required 
                       placeholder="<?php _e('Digite seu sobrenome', 'affiliate-portal'); ?>">
            </div>
            
            <div class="form-group">
                <label for="DOB"><?php _e('Data de nascimento', 'affiliate-portal'); ?> *</label>
                <input type="date" 
                       id="DOB" 
                       name="DOB" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="type"><?php _e('Tipo', 'affiliate-portal'); ?> *</label>
                <select id="type" name="type" required>
                    <option value=""><?php _e('Selecione o tipo', 'affiliate-portal'); ?></option>
                    <option value="Individual"><?php _e('Individual', 'affiliate-portal'); ?></option>
                    <option value="Company"><?php _e('Empresa', 'affiliate-portal'); ?></option>
                </select>
            </div>
        </div>
        
        <!-- Step 3: Contact Information -->
        <div class="form-step" data-step="3">
            <h3><?php _e('Passo 3: Informações de contato', 'affiliate-portal'); ?></h3>
            
            <div class="form-group">
                <label for="email"><?php _e('Endereço de email', 'affiliate-portal'); ?> *</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       required 
                       placeholder="<?php _e('Digite seu endereço de email', 'affiliate-portal'); ?>">
            </div>
            
            <div class="form-group">
                <label for="companyName"><?php _e('Nome da empresa', 'affiliate-portal'); ?></label>
                <input type="text" 
                       id="companyName" 
                       name="companyName" 
                       placeholder="<?php _e('Digite o nome da empresa', 'affiliate-portal'); ?>">
            </div>
            
            <div class="form-group">
                <label for="countryCode"><?php _e('Código do país', 'affiliate-portal'); ?> *</label>
                <input type="text" 
                       id="countryCode" 
                       name="countryCode" 
                       required 
                       placeholder="<?php _e('Ex: +55', 'affiliate-portal'); ?>">
            </div>
            
            <div class="form-group">
                <label for="mobileNumber"><?php _e('Número do celular', 'affiliate-portal'); ?> *</label>
                <input type="tel" 
                       id="mobileNumber" 
                       name="mobileNumber" 
                       required 
                       placeholder="<?php _e('Digite seu número do celular', 'affiliate-portal'); ?>">
            </div>
        </div>
        
        <!-- Step 4: Address Information -->
        <div class="form-step" data-step="4">
            <h3><?php _e('Passo 4: Informações de endereço', 'affiliate-portal'); ?></h3>
            
            <div class="form-group">
                <label for="addressLine1"><?php _e('Endereço linha 1', 'affiliate-portal'); ?> *</label>
                <input type="text" 
                       id="addressLine1" 
                       name="addressLine1" 
                       required 
                       placeholder="<?php _e('Digite sua rua e número', 'affiliate-portal'); ?>">
            </div>
            
            <div class="form-group">
                <label for="addressLine2"><?php _e('Endereço linha 2', 'affiliate-portal'); ?></label>
                <input type="text" 
                       id="addressLine2" 
                       name="addressLine2" 
                       placeholder="<?php _e('Complemento, apartamento, etc.', 'affiliate-portal'); ?>">
            </div>
            
            <div class="form-group">
                <label for="city"><?php _e('Cidade', 'affiliate-portal'); ?> *</label>
                <input type="text" 
                       id="city" 
                       name="city" 
                       required 
                       placeholder="<?php _e('Digite sua cidade', 'affiliate-portal'); ?>">
            </div>
            
            <div class="form-group">
                <label for="country"><?php _e('País', 'affiliate-portal'); ?> *</label>
                <select id="country" name="country" required>
                    <option value=""><?php _e('Selecione seu país', 'affiliate-portal'); ?></option>
                    <option value="Brazil">Brasil</option>
                    <option value="United States">Estados Unidos</option>
                    <option value="Canada">Canadá</option>
                    <option value="Portugal">Portugal</option>
                    <option value="Spain">Espanha</option>
                    <!-- More countries will be loaded dynamically -->
                </select>
            </div>
            
            <div class="form-group">
                <label for="state"><?php _e('Estado/Província', 'affiliate-portal'); ?> *</label>
                <select id="state" name="state" required disabled>
                    <option value=""><?php _e('Selecione o país primeiro', 'affiliate-portal'); ?></option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="zipcode"><?php _e('CEP/Código postal', 'affiliate-portal'); ?> *</label>
                <input type="text" 
                       id="zipcode" 
                       name="zipcode" 
                       required 
                       placeholder="<?php _e('Digite seu CEP/código postal', 'affiliate-portal'); ?>">
            </div>
        </div>
        
        <!-- Step 5: Business Information -->
        <div class="form-step" data-step="5">
            <h3><?php _e('Passo 5: Informações comerciais', 'affiliate-portal'); ?></h3>
            
            <div class="form-group">
                <label for="chatIdChannel"><?php _e('ID do chat/Canal', 'affiliate-portal'); ?> *</label>
                <input type="text" 
                       id="chatIdChannel" 
                       name="chatIdChannel" 
                       required 
                       placeholder="<?php _e('Digite seu ID do Telegram/WhatsApp', 'affiliate-portal'); ?>">
            </div>
            
            <div class="form-group">
                <label for="affiliateType"><?php _e('Tipo de afiliado', 'affiliate-portal'); ?> *</label>
                <select id="affiliateType" name="affiliateType" required>
                    <option value=""><?php _e('Selecione o tipo de afiliado', 'affiliate-portal'); ?></option>
                    <option value="Influencer"><?php _e('Influenciador', 'affiliate-portal'); ?></option>
                    <option value="Content Creator"><?php _e('Criador de conteúdo', 'affiliate-portal'); ?></option>
                    <option value="Media Buyer"><?php _e('Comprador de mídia', 'affiliate-portal'); ?></option>
                    <option value="Email Marketer"><?php _e('Profissional de email marketing', 'affiliate-portal'); ?></option>
                    <option value="Social Media Manager"><?php _e('Gerente de mídias sociais', 'affiliate-portal'); ?></option>
                    <option value="PPC Specialist"><?php _e('Especialista em PPC', 'affiliate-portal'); ?></option>
                    <option value="SEO Specialist"><?php _e('Especialista em SEO', 'affiliate-portal'); ?></option>
                    <option value="Blogger"><?php _e('Blogueiro', 'affiliate-portal'); ?></option>
                    <option value="YouTuber"><?php _e('YouTuber', 'affiliate-portal'); ?></option>
                    <option value="Website Owner"><?php _e('Proprietário de site', 'affiliate-portal'); ?></option>
                    <option value="App Developer"><?php _e('Desenvolvedor de aplicativos', 'affiliate-portal'); ?></option>
                    <option value="Other"><?php _e('Outro', 'affiliate-portal'); ?></option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="currency"><?php _e('Moeda', 'affiliate-portal'); ?> *</label>
                <select id="currency" name="currency" required>
                    <option value=""><?php _e('Selecione a moeda', 'affiliate-portal'); ?></option>
                    <option value="BRL"><?php _e('BRL - Real brasileiro', 'affiliate-portal'); ?></option>
                    <option value="USD"><?php _e('USD - Dólar americano', 'affiliate-portal'); ?></option>
                    <option value="EUR"><?php _e('EUR - Euro', 'affiliate-portal'); ?></option>
                    <option value="GBP"><?php _e('GBP - Libra esterlina', 'affiliate-portal'); ?></option>
                </select>
            </div>
        </div>
        
        <!-- Form Navigation -->
        <div class="form-navigation">
            <button type="button" id="prevBtn" class="btn btn-secondary" style="display: none;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M14,7L9,12L14,17V7Z"/>
                </svg>
                <?php _e('Anterior', 'affiliate-portal'); ?>
            </button>
            
            <button type="button" id="nextBtn" class="btn btn-primary">
                <?php _e('Próximo', 'affiliate-portal'); ?>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10,17V14H17V10H10V7L15,12L10,17Z"/>
                </svg>
            </button>
            
            <button type="submit" id="submitBtn" class="btn btn-success" style="display: none;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                </svg>
                <?php _e('Finalizar registro', 'affiliate-portal'); ?>
            </button>
        </div>
        
        <div class="form-footer">
            <p><?php _e('Já tem uma conta?', 'affiliate-portal'); ?> 
               <a href="<?php echo esc_url(get_permalink(get_page_by_path('afiliado-login'))); ?>">
                   <?php _e('Voltar ao login', 'affiliate-portal'); ?>
               </a>
            </p>
        </div>
    </form>
</div>