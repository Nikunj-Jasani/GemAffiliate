<?php
/**
 * Database simulation for WordPress wpdb functionality
 * This file creates a mock WordPress database environment for standalone operation
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

class WP_DB_Simulator {
    private $tables = [];
    public $prefix = 'wp_';
    public $last_error = '';
    public $last_query = '';
    
    public function __construct() {
        // Initialize with empty tables structure
        $this->tables = [
            'affiliate_users' => [],
            'affiliate_admin' => [],
            'affiliate_sessions' => [],
            'affiliate_kyc' => [],
            'affiliate_email_config' => []
        ];
        
        // Create sample user data for testing KYC workflow
        $this->tables['affiliate_users'][] = [
            'id' => 1,
            'username' => 'testuser',
            'password' => password_hash('test123', PASSWORD_DEFAULT),
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'type' => 'individual',
            'status' => 'awaiting approval',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->tables['affiliate_users'][] = [
            'id' => 2,
            'username' => 'companyuser',
            'password' => password_hash('company123', PASSWORD_DEFAULT),
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@company.com',
            'type' => 'company',
            'status' => 'awaiting approval',
            'company_name' => 'Tech Corp Ltd',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Create sample KYC data for testing
        $this->tables['affiliate_kyc'][] = [
            'id' => 1,
            'user_id' => 1,
            'account_type' => 'individual',
            'full_name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'mobile_number' => '+1234567890',
            'nationality' => 'US',
            'address_line1' => '123 Main Street',
            'city' => 'New York',
            'country' => 'United States',
            'post_code' => '10001',
            'kyc_status' => 'pending',
            'submitted_at' => date('Y-m-d H:i:s'),
            'identity_document_type' => 'passport',
            'identity_document_number' => 'US123456789'
        ];
        
        $this->tables['affiliate_kyc'][] = [
            'id' => 2,
            'user_id' => 2,
            'account_type' => 'company',
            'full_company_name' => 'Tech Corp Ltd',
            'business_contact_name' => 'Jane Smith',
            'business_email' => 'jane.smith@company.com',
            'company_registration_no' => 'TC123456',
            'kyc_status' => 'pending',
            'submitted_at' => date('Y-m-d H:i:s')
        ];
        
        // Create default admin user for testing
        $this->tables['affiliate_admin'][] = [
            'id' => 1,
            'username' => 'admin',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'email' => 'admin@example.com',
            'full_name' => 'System Administrator',
            'role' => 'admin',
            'status' => 'active',
            'last_login' => null,
            'created_at' => date('Y-m-d H:i:s')
        ];
    }
    
    public function get_charset_collate() {
        return 'DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';
    }
    
    public function prepare($query, ...$args) {
        // Simple parameter replacement for basic queries
        $this->last_query = $query;
        
        foreach ($args as $arg) {
            if (is_string($arg)) {
                $arg = "'" . addslashes($arg) . "'";
            } elseif (is_null($arg)) {
                $arg = 'NULL';
            }
            $query = preg_replace('/%[sd]/', $arg, $query, 1);
        }
        
        return $query;
    }
    
    public function get_row($query, $output = OBJECT) {
        $this->last_query = $query;
        
        // Parse table name from query
        if (preg_match('/FROM\s+`?(\w+)`?/i', $query, $matches)) {
            $table_name = str_replace($this->prefix, '', $matches[1]);
            
            if (isset($this->tables[$table_name])) {
                // Enhanced WHERE clause parsing for multiple conditions
                if (preg_match('/WHERE\s+(.+?)(?:\s+ORDER\s+BY|\s+LIMIT|\s*$)/i', $query, $where_matches)) {
                    $where_clause = $where_matches[1];
                    
                    foreach ($this->tables[$table_name] as $row) {
                        if ($this->evaluate_where_clause($row, $where_clause)) {
                            return (object) $row;
                        }
                    }
                }
                
                // Return first row if no WHERE clause
                if (!empty($this->tables[$table_name])) {
                    return (object) $this->tables[$table_name][0];
                }
            }
        }
        
        return null;
    }
    
    private function evaluate_where_clause($row, $where_clause) {
        // Handle multiple conditions with AND
        $conditions = preg_split('/\s+AND\s+/i', $where_clause);
        
        foreach ($conditions as $condition) {
            if (preg_match('/(\w+)\s*=\s*[\'"]?([^\'"]*)[\'"]?/i', trim($condition), $matches)) {
                $field = $matches[1];
                $value = $matches[2];
                
                if (!isset($row[$field]) || $row[$field] != $value) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    public function get_results($query, $output = OBJECT) {
        $this->last_query = $query;
        
        if (preg_match('/FROM\s+`?(\w+)`?/i', $query, $matches)) {
            $table_name = str_replace($this->prefix, '', $matches[1]);
            
            if (isset($this->tables[$table_name])) {
                $results = [];
                foreach ($this->tables[$table_name] as $row) {
                    $results[] = (object) $row;
                }
                return $results;
            }
        }
        
        return [];
    }
    
    public function get_var($query) {
        $this->last_query = $query;
        
        // Handle SHOW TABLES queries
        if (preg_match('/SHOW TABLES LIKE\s+[\'"](\w+)[\'"]/i', $query, $matches)) {
            $table_name = str_replace($this->prefix, '', $matches[1]);
            return isset($this->tables[$table_name]) ? $this->prefix . $table_name : null;
        }
        
        // Handle COUNT queries
        if (preg_match('/SELECT COUNT/i', $query)) {
            if (preg_match('/FROM\s+`?(\w+)`?/i', $query, $matches)) {
                $table_name = str_replace($this->prefix, '', $matches[1]);
                return isset($this->tables[$table_name]) ? count($this->tables[$table_name]) : 0;
            }
        }
        
        return null;
    }
    
    public function insert($table, $data, $format = null) {
        $table_name = str_replace($this->prefix, '', $table);
        
        if (!isset($this->tables[$table_name])) {
            $this->tables[$table_name] = [];
        }
        
        // Generate ID if not provided
        if (!isset($data['id'])) {
            $max_id = 0;
            foreach ($this->tables[$table_name] as $row) {
                if (isset($row['id']) && $row['id'] > $max_id) {
                    $max_id = $row['id'];
                }
            }
            $data['id'] = $max_id + 1;
        }
        
        // Add timestamps if not provided
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        
        $this->tables[$table_name][] = $data;
        return true;
    }
    
    public function update($table, $data, $where, $format = null, $where_format = null) {
        $table_name = str_replace($this->prefix, '', $table);
        
        if (!isset($this->tables[$table_name])) {
            return false;
        }
        
        $updated = false;
        foreach ($this->tables[$table_name] as &$row) {
            $match = true;
            foreach ($where as $field => $value) {
                if (!isset($row[$field]) || $row[$field] != $value) {
                    $match = false;
                    break;
                }
            }
            
            if ($match) {
                foreach ($data as $field => $value) {
                    $row[$field] = $value;
                }
                if (!isset($data['updated_at'])) {
                    $row['updated_at'] = date('Y-m-d H:i:s');
                }
                $updated = true;
            }
        }
        
        return $updated;
    }
    
    public function delete($table, $where, $where_format = null) {
        $table_name = str_replace($this->prefix, '', $table);
        
        if (!isset($this->tables[$table_name])) {
            return false;
        }
        
        $deleted = false;
        foreach ($this->tables[$table_name] as $key => $row) {
            $match = true;
            foreach ($where as $field => $value) {
                if (!isset($row[$field]) || $row[$field] != $value) {
                    $match = false;
                    break;
                }
            }
            
            if ($match) {
                unset($this->tables[$table_name][$key]);
                $deleted = true;
            }
        }
        
        // Re-index array
        $this->tables[$table_name] = array_values($this->tables[$table_name]);
        
        return $deleted;
    }
    
    public function query($sql) {
        $this->last_query = $sql;
        
        // Handle CREATE TABLE statements
        if (preg_match('/CREATE TABLE\s+(?:IF NOT EXISTS\s+)?`?(\w+)`?/i', $sql, $matches)) {
            $table_name = str_replace($this->prefix, '', $matches[1]);
            if (!isset($this->tables[$table_name])) {
                $this->tables[$table_name] = [];
            }
            return true;
        }
        
        // Handle DELETE statements
        if (preg_match('/DELETE FROM\s+`?(\w+)`?/i', $sql, $matches)) {
            $table_name = str_replace($this->prefix, '', $matches[1]);
            if (isset($this->tables[$table_name])) {
                $this->tables[$table_name] = [];
            }
            return true;
        }
        
        // Handle INSERT statements
        if (preg_match('/INSERT INTO\s+`?(\w+)`?/i', $sql, $matches)) {
            return true; // Simplified for basic functionality
        }
        
        return true;
    }
    
    public function flush() {
        // Simulate cache flush
        return true;
    }
}

// Create global wpdb instance
if (!isset($wpdb)) {
    $wpdb = new WP_DB_Simulator();
    $wpdb->prefix = 'wp_';
}

// Set global variable for affiliate portal
$affiliate_db = $wpdb;

// Required WordPress functions for database operations
if (!function_exists('dbDelta')) {
    function dbDelta($queries) {
        global $wpdb;
        
        if (is_string($queries)) {
            $queries = [$queries];
        }
        
        $results = [];
        foreach ($queries as $query) {
            $result = $wpdb->query($query);
            $results[] = $result ? 'Table created successfully' : 'Table creation failed';
        }
        
        return $results;
    }
}

// WordPress time functions
if (!function_exists('current_time')) {
    function current_time($type = 'mysql') {
        return date('Y-m-d H:i:s');
    }
}

// WordPress options functions
if (!function_exists('get_option')) {
    function get_option($option, $default = false) {
        return $default;
    }
}

if (!function_exists('update_option')) {
    function update_option($option, $value) {
        return true;
    }
}

// WordPress URL functions
if (!function_exists('admin_url')) {
    function admin_url($path = '') {
        if ($path === 'admin-ajax.php') {
            return '/ajax-handler.php';
        }
        return '/' . ltrim($path, '/');
    }
}

if (!function_exists('get_permalink')) {
    function get_permalink($post = 0) {
        return '/';
    }
}

if (!function_exists('get_page_by_title')) {
    function get_page_by_title($title) {
        return (object) array('ID' => 1);
    }
}

if (!function_exists('get_page_by_path')) {
    function get_page_by_path($path) {
        return (object) array('ID' => 1);
    }
}

// WordPress plugin functions
if (!function_exists('plugin_dir_url')) {
    function plugin_dir_url($file) {
        return '/assets/';
    }
}

if (!function_exists('plugin_dir_path')) {
    function plugin_dir_path($file) {
        return __DIR__ . '/';
    }
}

// WordPress action/hook system
if (!function_exists('add_action')) {
    function add_action($hook, $callback, $priority = 10, $args = 1) {
        // Mock action system
    }
}

if (!function_exists('add_shortcode')) {
    function add_shortcode($tag, $callback) {
        // Mock shortcode system
    }
}

if (!function_exists('do_shortcode')) {
    function do_shortcode($content) {
        return $content;
    }
}

if (!function_exists('wp_enqueue_scripts')) {
    function wp_enqueue_scripts() {
        // Mock script enqueue
    }
}

// WordPress file functions
if (!function_exists('wp_mkdir_p')) {
    function wp_mkdir_p($target) {
        if (!is_dir($target)) {
            return mkdir($target, 0755, true);
        }
        return true;
    }
}

// WordPress cache functions
if (!function_exists('wp_cache_delete')) {
    function wp_cache_delete($key, $group = '') {
        return true;
    }
}

if (!function_exists('wp_cache_flush')) {
    function wp_cache_flush() {
        return true;
    }
}

// WordPress SSL detection
if (!function_exists('is_ssl')) {
    function is_ssl() {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
            || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    }
}

// WordPress upgrade functions
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

if (!file_exists(ABSPATH . 'wp-admin/includes/upgrade.php')) {
    // Create mock upgrade.php
    $upgrade_dir = ABSPATH . 'wp-admin/includes';
    if (!is_dir($upgrade_dir)) {
        mkdir($upgrade_dir, 0755, true);
    }
    
    if (!file_exists($upgrade_dir . '/upgrade.php')) {
        file_put_contents($upgrade_dir . '/upgrade.php', '<?php
// Mock WordPress upgrade.php for standalone operation
if (!function_exists("dbDelta")) {
    function dbDelta($queries) {
        // Already defined in database.php
        return [];
    }
}
');
    }
}

error_log('Database simulation initialized successfully');
?>