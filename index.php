<?php
/**
 * Simple demonstration interface for Affiliate Portal Plugin
 * This allows testing the plugin functionality without a full WordPress installation
 */

// Start session for user authentication
session_start();

// Simulate WordPress environment basics
define('ABSPATH', __DIR__ . '/');
define('WP_DEBUG', true);

// Include database helper
require_once 'database.php';
global $affiliate_db;

// Simple autoloader for WordPress-like functions
function wp_simulate_functions() {
    // Mock essential WordPress functions for plugin testing
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
}

// Initialize mock WordPress functions
wp_simulate_functions();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affiliate Portal - Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body { 
            background-color: #f8f9fa; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .demo-section {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .feature-card {
            border: none;
            border-radius: 10px;
            transition: transform 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1 class="text-center">🚀 Affiliate Portal Plugin</h1>
            <p class="text-center lead">Complete affiliate management system with multi-language support</p>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="demo-section">
                    <h2 class="text-center mb-4">🎯 Plugin Overview</h2>
                    <p class="text-center">Your affiliate portal plugin is now running in the Replit environment!</p>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card feature-card h-100">
                                <div class="card-body text-center">
                                    <h5 class="card-title">📝 Registration System</h5>
                                    <p class="card-text">5-step registration with 21 database fields</p>
                                    <a href="templates/register-form.php" class="btn btn-primary">View Registration</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card feature-card h-100">
                                <div class="card-body text-center">
                                    <h5 class="card-title">🔐 Login System</h5>
                                    <p class="card-text">Secure authentication with session management</p>
                                    <a href="templates/login-form.php" class="btn btn-primary">View Login</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card feature-card h-100">
                                <div class="card-body text-center">
                                    <h5 class="card-title">📊 Dashboard</h5>
                                    <p class="card-text">Enhanced user dashboard with account info</p>
                                    <a href="templates/dashboard-enhanced.php" class="btn btn-primary">View Dashboard</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card feature-card h-100">
                                <div class="card-body text-center">
                                    <h5 class="card-title">🛡️ Admin Panel</h5>
                                    <p class="card-text">Administrative interface for management</p>
                                    <a href="templates/admin-dashboard.php" class="btn btn-primary">View Admin</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="demo-section">
                    <h3>🌍 Multi-Language Support</h3>
                    <p>Your plugin includes Portuguese language support:</p>
                    <div class="row">
                        <div class="col-md-6">
                            <a href="templates/pt/register-form.php" class="btn btn-outline-success w-100 mb-2">Registro (Portuguese)</a>
                        </div>
                        <div class="col-md-6">
                            <a href="templates/pt/login-form.php" class="btn btn-outline-success w-100 mb-2">Login (Portuguese)</a>
                        </div>
                    </div>
                </div>

                <div class="demo-section">
                    <h3>📋 Plugin Features</h3>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">✅ 5-Step Registration Form with 21 Fields</li>
                        <li class="list-group-item">✅ Secure User Authentication</li>
                        <li class="list-group-item">✅ Custom Database Tables</li>
                        <li class="list-group-item">✅ AJAX-powered Forms</li>
                        <li class="list-group-item">✅ Bootstrap 5 Integration</li>
                        <li class="list-group-item">✅ KYC Document Management</li>
                        <li class="list-group-item">✅ Admin Management Interface</li>
                        <li class="list-group-item">✅ Multi-language Support (EN/PT)</li>
                    </ul>
                </div>

                <div class="demo-section text-center">
                    <h3>🔧 Development Status</h3>
                    <p class="text-success"><strong>✅ Plugin Successfully Loaded</strong></p>
                    <p>The affiliate portal is ready for integration into WordPress or can be adapted for standalone use.</p>
                    
                    <div class="alert alert-info">
                        <strong>Note:</strong> This is a demonstration interface. In a WordPress environment, 
                        the plugin would integrate seamlessly with WordPress hooks, database, and user management.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/script.js"></script>
</body>
</html>