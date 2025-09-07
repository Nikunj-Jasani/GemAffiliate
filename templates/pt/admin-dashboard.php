<?php
// Portuguese Admin Dashboard Template
if (!defined('ABSPATH')) {
    exit;
}

// REMOVED: Session-based authentication - now using cookie-only authentication
// Admin authentication is handled by the shortcode function

// Get admin data from cookies
global $wpdb;
$admin_id = $_COOKIE['affiliate_admin_id'] ?? null;
$admin_username = $_COOKIE['affiliate_admin_username'] ?? null;

// Validate admin from cookies
if (!$admin_id) {
    echo '<div class="affiliate-alert affiliate-alert-warning">Por favor <a href="' . get_permalink(get_page_by_path('admin-login-pt')) . '">faça login</a> para acessar o painel administrativo.</div>';
    return;
}

// Get admin data from database
$admin_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}affiliate_admin WHERE id = %d AND status = 'active'", $admin_id));

if (!$admin_data) {
    echo '<div class="affiliate-alert affiliate-alert-warning">Conta de administrador não encontrada. Por favor <a href="' . get_permalink(get_page_by_path('admin-login-pt')) . '">faça login novamente</a>.</div>';
    return;
}
?>

<div class="affiliate-admin-container">


    <!-- Admin Header -->
    <div class="admin-header">
        <h1><?php _e('Painel do Administrador', 'affiliate-portal'); ?></h1>
        <div class="header-actions">
            <span class="admin-welcome"><?php _e('Bem-vindo', 'affiliate-portal'); ?>, 
                <?php echo esc_html($admin_username ?? $admin_data->username); ?>!</span>
            <a href="#" onclick="adminLogout()" class="btn btn-danger">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0 0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0 0,1 14,22H5A2,2 0 0,1 3,20V4A2,2 0 0,1 5,2H14Z"/>
                </svg>
                <?php _e('Sair', 'affiliate-portal'); ?>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon pending">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22C6.47,22 2,17.5 2,12A10,10 0 0,1 12,2M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z"/>
                </svg>
            </div>
            <div class="stat-content">
                <h3 id="pendingCount">-</h3>
                <p><?php _e('Aplicações pendentes', 'affiliate-portal'); ?></p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon approved">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M11,16.5L18,9.5L16.59,8.09L11,13.67L7.41,10.09L6,11.5L11,16.5Z"/>
                </svg>
            </div>
            <div class="stat-content">
                <h3 id="approvedCount">-</h3>
                <p><?php _e('Aplicações aprovadas', 'affiliate-portal'); ?></p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon rejected">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M15.09,9L12,12.09L8.91,9L8,9.91L11.09,13L8,16.09L8.91,17L12,13.91L15.09,17L16,16.09L12.91,13L16,9.91L15.09,9Z"/>
                </svg>
            </div>
            <div class="stat-content">
                <h3 id="rejectedCount">-</h3>
                <p><?php _e('Aplicações rejeitadas', 'affiliate-portal'); ?></p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon total">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16,17V19H2V17S2,13 9,13 16,17 16,17M12.5,7.5A3.5,3.5 0 0,1 9,11A3.5,3.5 0 0,1 5.5,7.5A3.5,3.5 0 0,1 9,4A3.5,3.5 0 0,1 12.5,7.5M15.94,13A5.32,5.32 0 0,1 18,17V19H22V17S22,13.37 15.94,13M15,4A3.39,3.39 0 0,1 16.5,7.5A3.39,3.39 0 0,1 15,11A3.39,3.39 0 0,1 13.5,7.5A3.39,3.39 0 0,1 15,4Z"/>
                </svg>
            </div>
            <div class="stat-content">
                <h3 id="totalCount">-</h3>
                <p><?php _e('Total de aplicações', 'affiliate-portal'); ?></p>
            </div>
        </div>
    </div>

    <!-- Control Panel -->
    <div class="admin-controls">
        <div class="control-section">
            <h3><?php _e('Filtros', 'affiliate-portal'); ?></h3>
            <div class="filter-controls">
                <select id="statusFilter" class="form-control">
                    <option value=""><?php _e('Todos os status', 'affiliate-portal'); ?></option>
                    <option value="awaiting approval"><?php _e('Aguardando aprovação', 'affiliate-portal'); ?></option>
                    <option value="approved"><?php _e('Aprovado', 'affiliate-portal'); ?></option>
                    <option value="rejected"><?php _e('Rejeitado', 'affiliate-portal'); ?></option>
                </select>
                
                <input type="text" id="searchFilter" class="form-control" 
                       placeholder="<?php _e('Buscar por nome ou email...', 'affiliate-portal'); ?>">
                
                <button onclick="loadApplications()" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z"/>
                    </svg>
                    <?php _e('Filtrar', 'affiliate-portal'); ?>
                </button>
            </div>
        </div>
        
        <div class="control-section">
            <h3><?php _e('Configuração de email', 'affiliate-portal'); ?></h3>
            <button onclick="showEmailConfigModal()" class="btn btn-outline">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z"/>
                </svg>
                <?php _e('Configurar notificações', 'affiliate-portal'); ?>
            </button>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="applications-section">
        <h3><?php _e('Aplicações de afiliados', 'affiliate-portal'); ?></h3>
        <div class="table-container">
            <table class="applications-table">
                <thead>
                    <tr>
                        <th><?php _e('ID', 'affiliate-portal'); ?></th>
                        <th><?php _e('Nome', 'affiliate-portal'); ?></th>
                        <th><?php _e('Email', 'affiliate-portal'); ?></th>
                        <th><?php _e('Tipo', 'affiliate-portal'); ?></th>
                        <th><?php _e('Status', 'affiliate-portal'); ?></th>
                        <th><?php _e('Data de registro', 'affiliate-portal'); ?></th>
                        <th><?php _e('Ações', 'affiliate-portal'); ?></th>
                    </tr>
                </thead>
                <tbody id="applicationsTableBody">
                    <tr>
                        <td colspan="7" class="loading-cell">
                            <div class="loading-spinner">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12,4V2A10,10 0 0,0 2,12H4A8,8 0 0,1 12,4Z">
                                        <animateTransform attributeName="transform" attributeType="XML" type="rotate" from="0 12 12" to="360 12 12" dur="1s" repeatCount="indefinite"/>
                                    </path>
                                </svg>
                                <?php _e('Carregando aplicações...', 'affiliate-portal'); ?>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div id="paginationContainer" class="pagination-container"></div>
    </div>
</div>

<!-- Application Details Modal -->
<div id="applicationModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><?php _e('Detalhes da aplicação', 'affiliate-portal'); ?></h3>
            <span class="close" onclick="hideApplicationModal()">&times;</span>
        </div>
        <div id="applicationDetails" class="modal-body">
            <!-- Application details will be loaded here -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="hideApplicationModal()">
                <?php _e('Fechar', 'affiliate-portal'); ?>
            </button>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><?php _e('Atualizar status da aplicação', 'affiliate-portal'); ?></h3>
            <span class="close" onclick="hideStatusModal()">&times;</span>
        </div>
        <form id="statusUpdateForm">
            <div class="modal-body">
                <input type="hidden" id="applicationId" name="application_id">
                
                <div class="form-group">
                    <label for="newStatus"><?php _e('Novo status', 'affiliate-portal'); ?>:</label>
                    <select id="newStatus" name="status" class="form-control" required>
                        <option value="awaiting approval"><?php _e('Aguardando aprovação', 'affiliate-portal'); ?></option>
                        <option value="approved"><?php _e('Aprovado', 'affiliate-portal'); ?></option>
                        <option value="rejected"><?php _e('Rejeitado', 'affiliate-portal'); ?></option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="adminRemarks"><?php _e('Observações do administrador', 'affiliate-portal'); ?>:</label>
                    <textarea id="adminRemarks" name="remarks" class="form-control" rows="4" 
                              placeholder="<?php _e('Adicione observações ou comentários...', 'affiliate-portal'); ?>"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideStatusModal()">
                    <?php _e('Cancelar', 'affiliate-portal'); ?>
                </button>
                <button type="submit" class="btn btn-primary">
                    <?php _e('Atualizar status', 'affiliate-portal'); ?>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Email Configuration Modal -->
<div id="emailConfigModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><?php _e('Configuração de email', 'affiliate-portal'); ?></h3>
            <span class="close" onclick="hideEmailConfigModal()">&times;</span>
        </div>
        <form id="emailConfigForm">
            <div class="modal-body">
                <div class="form-group">
                    <label for="notificationEmails"><?php _e('Emails para notificação', 'affiliate-portal'); ?>:</label>
                    <textarea id="notificationEmails" name="notification_emails" class="form-control" rows="3" 
                              placeholder="<?php _e('Digite os emails separados por vírgula', 'affiliate-portal'); ?>"></textarea>
                    <small class="form-text"><?php _e('Estes emails receberão notificações quando novos afiliados se registrarem', 'affiliate-portal'); ?></small>
                </div>
                
                <div class="form-group">
                    <label for="fromEmail"><?php _e('Email remetente', 'affiliate-portal'); ?>:</label>
                    <input type="email" id="fromEmail" name="from_email" class="form-control" 
                           placeholder="noreply@gem-affiliates.com">
                </div>
                
                <div class="form-group">
                    <label for="fromName"><?php _e('Nome do remetente', 'affiliate-portal'); ?>:</label>
                    <input type="text" id="fromName" name="from_name" class="form-control" 
                           placeholder="Portal de Afiliados">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideEmailConfigModal()">
                    <?php _e('Cancelar', 'affiliate-portal'); ?>
                </button>
                <button type="submit" class="btn btn-primary">
                    <?php _e('Salvar configuração', 'affiliate-portal'); ?>
                </button>
            </div>
        </form>
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

let currentPage = 1;
let currentFilter = '';
let currentSearch = '';

// Load applications on page load
document.addEventListener('DOMContentLoaded', function() {
    loadApplications();
    loadEmailConfig();
});

function adminLogout() {
    if (confirm('<?php _e('Tem certeza que deseja sair?', 'affiliate-portal'); ?>')) {
        window.location.href = '<?php echo esc_url(get_permalink(get_page_by_path('admin-login-pt'))); ?>?action=logout';
    }
}

function loadApplications(page = 1) {
    currentPage = page;
    currentFilter = document.getElementById('statusFilter').value;
    currentSearch = document.getElementById('searchFilter').value;
    
    const formData = new FormData();
    formData.append('action', 'affiliate_get_applications');
    formData.append('nonce', '<?php echo wp_create_nonce('affiliate_nonce'); ?>');
    formData.append('page', page);
    formData.append('status_filter', currentFilter);
    formData.append('search', currentSearch);
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateApplicationsTable(data.data.applications);
            updateStats(data.data.stats);
            updatePagination(data.data.pagination);
        } else {
            console.error('Failed to load applications:', data.data);
        }
    })
    .catch(error => {
        console.error('Error loading applications:', error);
    });
}

function updateApplicationsTable(applications) {
    const tbody = document.getElementById('applicationsTableBody');
    tbody.innerHTML = '';
    
    if (applications.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="no-data"><?php _e('Nenhuma aplicação encontrada', 'affiliate-portal'); ?></td></tr>';
        return;
    }
    
    applications.forEach(app => {
        const statusClass = app.status.toLowerCase().replace(' ', '-');
        const row = `
            <tr>
                <td>${app.id}</td>
                <td>${app.first_name} ${app.last_name}</td>
                <td>${app.email}</td>
                <td>${app.affiliate_type}</td>
                <td><span class="status-badge ${statusClass}">${translateStatus(app.status)}</span></td>
                <td>${formatDate(app.created_at)}</td>
                <td>
                    <button onclick="viewApplication(${app.id})" class="btn btn-sm btn-outline">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/>
                        </svg>
                        <?php _e('Ver', 'affiliate-portal'); ?>
                    </button>
                    <button onclick="editStatus(${app.id}, '${app.status}', '${app.admin_remarks || ''}')" class="btn btn-sm btn-primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z"/>
                        </svg>
                        <?php _e('Editar', 'affiliate-portal'); ?>
                    </button>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}

function translateStatus(status) {
    const translations = {
        'awaiting approval': '<?php _e('Aguardando aprovação', 'affiliate-portal'); ?>',
        'approved': '<?php _e('Aprovado', 'affiliate-portal'); ?>',
        'rejected': '<?php _e('Rejeitado', 'affiliate-portal'); ?>'
    };
    return translations[status] || status;
}

function updateStats(stats) {
    document.getElementById('pendingCount').textContent = stats.pending || 0;
    document.getElementById('approvedCount').textContent = stats.approved || 0;
    document.getElementById('rejectedCount').textContent = stats.rejected || 0;
    document.getElementById('totalCount').textContent = (parseInt(stats.pending || 0) + parseInt(stats.approved || 0) + parseInt(stats.rejected || 0));
}

function updatePagination(pagination) {
    const container = document.getElementById('paginationContainer');
    
    if (pagination.total_pages <= 1) {
        container.innerHTML = '';
        return;
    }
    
    let paginationHTML = '<div class="pagination">';
    
    // Previous button
    if (pagination.has_prev) {
        paginationHTML += `<button onclick="loadApplications(${pagination.current_page - 1})" class="page-btn">← <?php _e('Anterior', 'affiliate-portal'); ?></button>`;
    }
    
    // Page numbers
    for (let i = 1; i <= pagination.total_pages; i++) {
        const activeClass = i === pagination.current_page ? 'active' : '';
        paginationHTML += `<button onclick="loadApplications(${i})" class="page-btn ${activeClass}">${i}</button>`;
    }
    
    // Next button
    if (pagination.has_next) {
        paginationHTML += `<button onclick="loadApplications(${pagination.current_page + 1})" class="page-btn"><?php _e('Próximo', 'affiliate-portal'); ?> →</button>`;
    }
    
    paginationHTML += '</div>';
    container.innerHTML = paginationHTML;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('pt-BR');
}

// Modal functions and other JavaScript would continue here...
// (Similar to the English version but with Portuguese translations)
</script>