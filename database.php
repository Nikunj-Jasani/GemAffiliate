<?php
/**
 * Database configuration for standalone affiliate portal
 * Simulates WordPress database functionality for development
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

// Database configuration
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'affiliate_portal');
}
if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}
if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', '');
}

// WordPress-like database class simulation
class WPDB_Simulation {
    public $prefix = 'wp_';
    private $connection = null;
    public $last_error = '';
    
    public function __construct() {
        // For demo purposes, we'll use SQLite for simplicity
        try {
            $this->connection = new PDO('sqlite:' . __DIR__ . '/affiliate_portal.db');
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            $this->last_error = $e->getMessage();
            error_log('Database connection failed: ' . $e->getMessage());
        }
    }
    
    public function get_charset_collate() {
        return '';
    }
    
    public function prepare($query, ...$args) {
        if (empty($args)) {
            return $query;
        }
        
        // Simple prepare simulation
        $prepared = $query;
        foreach ($args as $arg) {
            if (is_string($arg)) {
                $arg = $this->connection ? $this->connection->quote($arg) : "'" . addslashes($arg) . "'";
            } elseif (is_int($arg)) {
                $arg = intval($arg);
            } elseif (is_null($arg)) {
                $arg = 'NULL';
            }
            $prepared = preg_replace('/(%s|%d|%f)/', $arg, $prepared, 1);
        }
        
        return $prepared;
    }
    
    public function query($query) {
        if (!$this->connection) {
            $this->last_error = 'No database connection';
            return false;
        }
        
        try {
            $result = $this->connection->query($query);
            return $result;
        } catch (PDOException $e) {
            $this->last_error = $e->getMessage();
            error_log('Database query failed: ' . $e->getMessage());
            return false;
        }
    }
    
    public function get_row($query) {
        if (!$this->connection) {
            return null;
        }
        
        try {
            $stmt = $this->connection->query($query);
            return $stmt ? $stmt->fetchObject() : null;
        } catch (PDOException $e) {
            $this->last_error = $e->getMessage();
            return null;
        }
    }
    
    public function get_results($query) {
        if (!$this->connection) {
            return [];
        }
        
        try {
            $stmt = $this->connection->query($query);
            return $stmt ? $stmt->fetchAll(PDO::FETCH_OBJ) : [];
        } catch (PDOException $e) {
            $this->last_error = $e->getMessage();
            return [];
        }
    }
    
    public function get_var($query) {
        if (!$this->connection) {
            return null;
        }
        
        try {
            $stmt = $this->connection->query($query);
            return $stmt ? $stmt->fetchColumn() : null;
        } catch (PDOException $e) {
            $this->last_error = $e->getMessage();
            return null;
        }
    }
    
    public function insert($table, $data, $format = null) {
        if (!$this->connection) {
            return false;
        }
        
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $query = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        try {
            $stmt = $this->connection->prepare($query);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            $this->last_error = $e->getMessage();
            return false;
        }
    }
    
    public function update($table, $data, $where, $data_format = null, $where_format = null) {
        if (!$this->connection) {
            return false;
        }
        
        $set_clause = [];
        foreach ($data as $column => $value) {
            $set_clause[] = "{$column} = :{$column}";
        }
        $set_clause = implode(', ', $set_clause);
        
        $where_clause = [];
        foreach ($where as $column => $value) {
            $where_clause[] = "{$column} = :where_{$column}";
            $data["where_{$column}"] = $value;
        }
        $where_clause = implode(' AND ', $where_clause);
        
        $query = "UPDATE {$table} SET {$set_clause} WHERE {$where_clause}";
        
        try {
            $stmt = $this->connection->prepare($query);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            $this->last_error = $e->getMessage();
            return false;
        }
    }
    
    public function delete($table, $where, $where_format = null) {
        if (!$this->connection) {
            return false;
        }
        
        $where_clause = [];
        $data = [];
        foreach ($where as $column => $value) {
            $where_clause[] = "{$column} = :{$column}";
            $data[$column] = $value;
        }
        $where_clause = implode(' AND ', $where_clause);
        
        $query = "DELETE FROM {$table} WHERE {$where_clause}";
        
        try {
            $stmt = $this->connection->prepare($query);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            $this->last_error = $e->getMessage();
            return false;
        }
    }
}

// Create global database object
$wpdb = new WPDB_Simulation();
$affiliate_db = $wpdb;

// WordPress-like functions for compatibility
if (!function_exists('dbDelta')) {
    function dbDelta($queries) {
        global $wpdb;
        
        if (is_string($queries)) {
            $queries = explode(';', $queries);
        }
        
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                $wpdb->query($query);
            }
        }
    }
}

if (!function_exists('current_time')) {
    function current_time($type = 'mysql') {
        if ($type === 'mysql') {
            return date('Y-m-d H:i:s');
        }
        return time();
    }
}

if (!function_exists('is_ssl')) {
    function is_ssl() {
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    }
}

if (!function_exists('admin_url')) {
    function admin_url($path = '') {
        // In standalone mode, route AJAX requests to our ajax-handler.php
        if ($path === 'admin-ajax.php') {
            return '/ajax-handler.php';
        }
        return '/' . ltrim($path, '/');
    }
}

if (!function_exists('get_permalink')) {
    function get_permalink($post = 0) {
        // Mock permalink function for standalone mode
        return '/';
    }
}

if (!function_exists('get_page_by_title')) {
    function get_page_by_title($title) {
        // Mock function to return a dummy page object
        return (object) array('ID' => 1);
    }
}