<?php
if (!defined('ABSPATH')) {
    exit;
}

// This template shows the submitted KYC data in view mode for Company accounts (Portuguese)
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

.status-incomplete {
    background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
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
            Verificação KYC Empresarial
        </h2>
        <div class="kyc-status-badge status-<?php echo str_replace(['awaiting approval', ' '], ['awaiting', '-'], $kyc_status); ?>">
            <?php 
            $status_icons = [
                'awaiting approval' => '⏳',
                'approved' => '✅', 
                'rejected' => '❌',
                'incomplete' => '⚠️'
            ];
            $status_text = [
                'awaiting approval' => 'Aguardando Aprovação',
                'approved' => 'Aprovado',
                'rejected' => 'Rejeitado',
                'incomplete' => 'Documentos Adicionais'
            ];
            echo ($status_icons[$kyc_status] ?? '📝') . ' ' . ($status_text[$kyc_status] ?? ucwords(str_replace('_', ' ', $kyc_status))); 
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
                        echo '⏳ Sua aplicação KYC empresarial está em análise. Você será notificado quando a análise for concluída.';
                        break;
                    case 'approved':
                        echo '🎉 Parabéns! Sua aplicação KYC empresarial foi aprovada e você está totalmente verificado.';
                        break;
                    case 'rejected':
                        echo '📝 Sua aplicação KYC empresarial foi rejeitada. Revise os comentários abaixo e reenvie.';
                        break;
                    case 'incomplete':
                        echo '📋 Sua aplicação KYC empresarial requer documentos adicionais. Revise os requisitos abaixo.';
                        break;
                    default:
                        echo '📋 Os detalhes da sua aplicação KYC empresarial estão mostrados abaixo.';
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
            Comentários da Análise
        </h3>
        <div style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border: 2px solid #f39c12; border-radius: 15px; padding: 25px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                <div style="width: 40px; height: 40px; background: #f39c12; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">💬</div>
                <strong style="color: #856404; font-size: 1.1rem;">Feedback do Administrador:</strong>
            </div>
            <div style="background: white; padding: 20px; border-radius: 10px; border-left: 4px solid #f39c12;">
                <p style="margin: 0; white-space: pre-wrap; color: #2c3e50; line-height: 1.6;"><?php echo esc_html($admin_comments); ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Company Information Section -->
    <div class="kyc-info-section">
        <h3>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12,7V3H2V21H22V7H12M6,19H4V17H6V19M6,15H4V13H6V15M6,11H4V9H6V11M6,7H4V5H6V7M10,19H8V17H10V19M10,15H8V13H10V15M10,11H8V9H10V11M10,7H8V5H10V7M20,19H12V17H20V19M20,15H12V13H20V15M20,11H12V9H20V11Z"/>
            </svg>
            Informações da Empresa
        </h3>
        <div class="kyc-info-grid">
            <div class="kyc-info-item">
                <span class="kyc-info-label">🏢 Nome da Empresa</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->full_name ?? 'Não fornecido'); ?></span>
            </div>
            <div class="kyc-info-item">
                <span class="kyc-info-label">📋 Número de Registro</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->registration_number ?? 'Não fornecido'); ?></span>
            </div>
            <div class="kyc-info-item">
                <span class="kyc-info-label">🏷️ Tipo de Empresa</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->company_type ?? 'Não fornecido'); ?></span>
            </div>
            <div class="kyc-info-item">
                <span class="kyc-info-label">📅 Data de Constituição</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->incorporation_date ?? 'Não fornecido'); ?></span>
            </div>
            <div class="kyc-info-item">
                <span class="kyc-info-label">💰 Capital Social</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->share_capital ?? 'Não fornecido'); ?></span>
            </div>
            <div class="kyc-info-item">
                <span class="kyc-info-label">📧 Email da Empresa</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->email ?? 'Não fornecido'); ?></span>
            </div>
            <div class="kyc-info-item">
                <span class="kyc-info-label">📱 Telefone da Empresa</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->mobile_number ?? 'Não fornecido'); ?></span>
            </div>
        </div>
    </div>

    <!-- Business Activities Section -->
    <div class="kyc-info-section">
        <h3>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M16,6L18.29,8.29L13.41,13.17L9.41,9.17L2,16.59L3.41,18L9.41,12L13.41,16L19.71,9.71L22,12V6H16Z"/>
            </svg>
            Atividades Empresariais
        </h3>
        <div class="kyc-info-grid">
            <div class="kyc-info-item">
                <span class="kyc-info-label">🎯 Tipo de Afiliado</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->affiliate_type ?? 'Não fornecido'); ?></span>
            </div>
            <?php if (!empty($kyc_data->expected_monthly_volume)): ?>
            <div class="kyc-info-item">
                <span class="kyc-info-label">💰 Volume Mensal Esperado</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->expected_monthly_volume); ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($kyc_data->business_nature)): ?>
            <div class="kyc-info-item">
                <span class="kyc-info-label">🏭 Natureza do Negócio</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->business_nature); ?></span>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($kyc_data->affiliate_urls)): ?>
        <!-- Affiliate URLs Section -->
        <div class="kyc-info-item" style="margin-top: 25px; background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%); border-left: 4px solid #9c27b0; padding: 25px; border-radius: 12px;">
            <span class="kyc-info-label" style="display: flex; align-items: center; gap: 8px; font-size: 1.1rem; margin-bottom: 15px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10.59,13.41C11,13.8 11,14.4 10.59,14.81C10.2,15.2 9.6,15.2 9.19,14.81L7.77,13.39L7.77,13.39C6.6,12.22 6.6,10.35 7.77,9.18L10.59,6.36C11.76,5.19 13.63,5.19 14.8,6.36L16.22,7.78C17.39,8.95 17.39,10.82 16.22,12L14.1,14.1C13.71,14.5 13.08,14.5 12.69,14.1C12.3,13.71 12.3,13.08 12.69,12.69L14.81,10.57C15.22,10.16 15.22,9.56 14.81,9.15L13.39,7.73C12.98,7.32 12.38,7.32 11.97,7.73L9.15,10.55C8.74,10.96 8.74,11.56 9.15,11.97L10.59,13.41Z"/>
                </svg>
                🔗 URLs de Afiliado
            </span>
            <div style="background: white; border-radius: 8px; padding: 20px; border-left: 4px solid #9c27b0;">
                <div style="white-space: pre-wrap; color: #2c3e50; line-height: 1.8; font-family: monospace; font-size: 0.95rem;"><?php echo esc_html($kyc_data->affiliate_urls); ?></div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($kyc_data->previous_affiliate_experience)): ?>
        <!-- Previous Experience Section -->
        <div class="kyc-info-item" style="margin-top: 25px; background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-left: 4px solid #2196f3; padding: 25px; border-radius: 12px;">
            <span class="kyc-info-label" style="display: flex; align-items: center; gap: 8px; font-size: 1.1rem; margin-bottom: 15px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12,2A3,3 0 0,1 15,5V11A3,3 0 0,1 12,14A3,3 0 0,1 9,11V5A3,3 0 0,1 12,2M19,11C19,14.53 16.39,17.44 13,17.93V21H11V17.93C7.61,17.44 5,14.53 5,11H7A5,5 0 0,0 12,16A5,5 0 0,0 17,11H19Z"/>
                </svg>
                💼 Experiência Anterior
            </span>
            <div style="background: white; border-radius: 8px; padding: 20px; border-left: 4px solid #2196f3;">
                <div style="white-space: pre-wrap; color: #2c3e50; line-height: 1.6;"><?php echo esc_html($kyc_data->previous_affiliate_experience); ?></div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Address Information Section -->
    <div class="kyc-info-section">
        <h3>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22S19,14.25 19,9A7,7 0 0,0 12,2Z"/>
            </svg>
            Endereço da Empresa
        </h3>
        <div class="kyc-info-grid">
            <div class="kyc-info-item">
                <span class="kyc-info-label">🏠 Endereço Linha 1</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->address_line1 ?? 'Não fornecido'); ?></span>
            </div>
            <?php if (!empty($kyc_data->address_line2)): ?>
            <div class="kyc-info-item">
                <span class="kyc-info-label">🏠 Endereço Linha 2</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->address_line2); ?></span>
            </div>
            <?php endif; ?>
            <div class="kyc-info-item">
                <span class="kyc-info-label">🏙️ Cidade</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->city ?? 'Não fornecido'); ?></span>
            </div>
            <div class="kyc-info-item">
                <span class="kyc-info-label">🌍 País</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->country ?? 'Não fornecido'); ?></span>
            </div>
            <div class="kyc-info-item">
                <span class="kyc-info-label">📮 CEP</span>
                <span class="kyc-info-value"><?php echo esc_html($kyc_data->post_code ?? 'Não fornecido'); ?></span>
            </div>
        </div>
    </div>

    <!-- Uploaded Documents Section -->
    <div class="kyc-info-section">
        <h3>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M6,2A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2H6M6,4H13V9H18V20H6V4M8,12V14H16V12H8M8,16V18H13V16H8Z"/>
            </svg>
            Documentos Enviados
        </h3>
        <div class="document-grid">
            <!-- Certificate of Incorporation -->
            <?php if (!empty($kyc_data->certificate_of_incorporation_url)): ?>
            <div class="document-card uploaded">
                <div class="doc-icon">📜</div>
                <h4 style="margin: 0 0 10px 0; color: #2c3e50; font-weight: 600;">Certidão de Constituição</h4>
                <p style="margin: 0 0 15px 0; color: #6c757d; font-size: 0.9rem;">Documento Oficial de Constituição</p>
                <a href="<?php echo esc_url($kyc_data->certificate_of_incorporation_url); ?>" target="_blank" class="document-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 9a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5 5 5 0 0 1 5-5 5 5 0 0 1 5 5 5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5z"/>
                    </svg>
                    Ver Documento
                </a>
            </div>
            <?php endif; ?>

            <!-- Memorandum and Articles -->
            <?php if (!empty($kyc_data->memorandum_articles_url)): ?>
            <div class="document-card uploaded">
                <div class="doc-icon">📋</div>
                <h4 style="margin: 0 0 10px 0; color: #2c3e50; font-weight: 600;">Estatuto Social</h4>
                <p style="margin: 0 0 15px 0; color: #6c757d; font-size: 0.9rem;">Memorando e Estatutos</p>
                <a href="<?php echo esc_url($kyc_data->memorandum_articles_url); ?>" target="_blank" class="document-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 9a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5 5 5 0 0 1 5-5 5 5 0 0 1 5 5 5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5z"/>
                    </svg>
                    Ver Documento
                </a>
            </div>
            <?php endif; ?>

            <!-- Directors List -->
            <?php if (!empty($kyc_data->directors_list_url)): ?>
            <div class="document-card uploaded">
                <div class="doc-icon">👥</div>
                <h4 style="margin: 0 0 10px 0; color: #2c3e50; font-weight: 600;">Lista de Diretores</h4>
                <p style="margin: 0 0 15px 0; color: #6c757d; font-size: 0.9rem;">Registro de Diretores</p>
                <a href="<?php echo esc_url($kyc_data->directors_list_url); ?>" target="_blank" class="document-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 9a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5 5 5 0 0 1 5-5 5 5 0 0 1 5 5 5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5z"/>
                    </svg>
                    Ver Documento
                </a>
            </div>
            <?php endif; ?>

            <!-- Shareholding Structure -->
            <?php if (!empty($kyc_data->shareholding_structure_url)): ?>
            <div class="document-card uploaded">
                <div class="doc-icon">📊</div>
                <h4 style="margin: 0 0 10px 0; color: #2c3e50; font-weight: 600;">Estrutura Acionária</h4>
                <p style="margin: 0 0 15px 0; color: #6c757d; font-size: 0.9rem;">Composição dos Acionistas</p>
                <a href="<?php echo esc_url($kyc_data->shareholding_structure_url); ?>" target="_blank" class="document-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 9a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5 5 5 0 0 1 5-5 5 5 0 0 1 5 5 5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5z"/>
                    </svg>
                    Ver Documento
                </a>
            </div>
            <?php endif; ?>

            <!-- Company Proof of Address -->
            <?php if (!empty($kyc_data->company_proof_of_address_url)): ?>
            <div class="document-card uploaded">
                <div class="doc-icon">🏢</div>
                <h4 style="margin: 0 0 10px 0; color: #2c3e50; font-weight: 600;">Comprovante de Endereço</h4>
                <p style="margin: 0 0 15px 0; color: #6c757d; font-size: 0.9rem;">Endereço da Empresa</p>
                <a href="<?php echo esc_url($kyc_data->company_proof_of_address_url); ?>" target="_blank" class="document-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 9a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5 5 5 0 0 1 5-5 5 5 0 0 1 5 5 5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5z"/>
                    </svg>
                    Ver Documento
                </a>
            </div>
            <?php endif; ?>

            <!-- Financial Statements -->
            <?php if (!empty($kyc_data->financial_statements_url)): ?>
            <div class="document-card uploaded">
                <div class="doc-icon">💼</div>
                <h4 style="margin: 0 0 10px 0; color: #2c3e50; font-weight: 600;">Demonstrações Financeiras</h4>
                <p style="margin: 0 0 15px 0; color: #6c757d; font-size: 0.9rem;">Relatórios Financeiros</p>
                <a href="<?php echo esc_url($kyc_data->financial_statements_url); ?>" target="_blank" class="document-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 9a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5 5 5 0 0 1 5-5 5 5 0 0 1 5 5 5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5z"/>
                    </svg>
                    Ver Documento
                </a>
            </div>
            <?php endif; ?>

            <!-- Additional Documents -->
            <?php if (!empty($kyc_data->additional_documents_url)): ?>
            <div class="document-card uploaded">
                <div class="doc-icon">📎</div>
                <h4 style="margin: 0 0 10px 0; color: #2c3e50; font-weight: 600;">Documentos Adicionais</h4>
                <p style="margin: 0 0 15px 0; color: #6c757d; font-size: 0.9rem;">Documentação de Apoio</p>
                <a href="<?php echo esc_url($kyc_data->additional_documents_url); ?>" target="_blank" class="document-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 9a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5 5 5 0 0 1 5-5 5 5 0 0 1 5 5 5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5z"/>
                    </svg>
                    Ver Documentos
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($can_reupload): ?>
    <!-- Re-upload Section -->
    <div class="kyc-info-section" style="text-align: center; background: linear-gradient(135deg, #fff9c4 0%, #fff3cd 100%); border: 2px solid #f0ad4e;">
        <h3 style="color: #8a6d3b;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M9,16V10H5L12,3L19,10H15V16H9M5,20V18H19V20H5Z"/>
            </svg>
            Atualizar Documentos
        </h3>
        <p style="margin-bottom: 20px; color: #8a6d3b;">Você pode reenviar seus documentos KYC empresariais com as informações atualizadas.</p>
        <button class="document-link" onclick="location.reload();" style="background: linear-gradient(135deg, #f0ad4e 0%, #eea236 100%);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17.65,6.35C16.2,4.9 14.21,4 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20C15.73,20 18.84,17.45 19.73,14H17.65C16.83,16.33 14.61,18 12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6C13.66,6 15.14,6.69 16.22,7.78L13,11H20V4L17.65,6.35Z"/>
            </svg>
            Reenviar Documentos KYC
        </button>
    </div>
    <?php endif; ?>
</div>