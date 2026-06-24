<?php
/**
 * Plugin Name: MCP Commerce Connector
 * Plugin URI: https://bijoydev.vercel.app/
 * Description: WordPress plugin that exposes an MCP server through a REST API endpoint for WooCommerce integration
 * Version: 1.0.0
 * Author: Bijoy
 * Author URI: https://bijoydev.vercel.app/
 * License: GPL2
 * Text Domain: mcp-commerce-connector
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('MCP_COMMERCE_VERSION', '1.0.0');
define('MCP_COMMERCE_PROTOCOL_VERSION', '2025-11-25');
define('MCP_COMMERCE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MCP_COMMERCE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MCP_COMMERCE_LOGS_DIR', MCP_COMMERCE_PLUGIN_DIR . 'logs/');

require_once MCP_COMMERCE_PLUGIN_DIR . 'includes/class-mcp-logger.php';
require_once MCP_COMMERCE_PLUGIN_DIR . 'includes/class-mcp-rest-server.php';
require_once MCP_COMMERCE_PLUGIN_DIR . 'includes/tools/class-mcp-product-tool.php';
require_once MCP_COMMERCE_PLUGIN_DIR . 'includes/tools/class-mcp-order-tool.php';

class MCP_Commerce_Connector {

    private static $instance = null;
    private $logger = null;
    private $rest_server = null;
    private $tools = array();

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->logger = MCP_Logger::get_instance();
        $this->init();
    }

    public function init() {
        add_action('rest_api_init', array($this, 'register_rest_routes'), 10);
        add_action('admin_menu', array($this, 'add_admin_menu'));
        $this->register_tools();
    }

    public function register_rest_routes() {
        $this->rest_server = new MCP_REST_Server($this);
    }

    public function register_tools() {
        $this->tools['get_products'] = new MCP_Product_Tool();
        $this->tools['get_orders'] = new MCP_Order_Tool();
    }

    public function get_tools() {
        return $this->tools;
    }

    public function execute_tool($tool_name, $arguments) {
        if (!isset($this->tools[$tool_name])) {
            return array(
                'error' => 'Tool not found',
                'tool' => $tool_name
            );
        }

        return $this->tools[$tool_name]->execute($arguments);
    }

    public function send_response($id, $result = null, $error = null) {
        $response = array(
            'jsonrpc' => '2.0',
            'id' => $id
        );

        if ($error) {
            $response['error'] = $error;
        } else {
            $response['result'] = $result;
        }

        return $response;
    }

    public function add_log($message, $level = 'info', $data = array()) {
        $this->logger->add_log($message, $level, $data);
    }

    public function get_logs($limit = 100) {
        return $this->logger->get_logs($limit);
    }

    public function add_admin_menu() {
        add_menu_page(
            'MCP Commerce Connector',
            'MCP Commerce',
            'manage_options',
            'mcp-commerce',
            array($this, 'admin_page'),
            'dashicons-api',
            30
        );
    }

    public function admin_page() {
        require_once MCP_COMMERCE_PLUGIN_DIR . 'admin/admin-page.php';
    }
}

// Initialize the plugin
add_action('plugins_loaded', array('MCP_Commerce_Connector', 'get_instance'));

// Register activation hook
register_activation_hook(__FILE__, function() {
    // Create logs directory if it doesn't exist
    if (!file_exists(MCP_COMMERCE_LOGS_DIR)) {
        wp_mkdir_p(MCP_COMMERCE_LOGS_DIR);
    }
    // Add .htaccess to protect logs
    $htaccess_file = MCP_COMMERCE_LOGS_DIR . '.htaccess';
    if (!file_exists($htaccess_file)) {
        file_put_contents($htaccess_file, "deny from all\n");
    }
});

// Register deactivation hook
register_deactivation_hook(__FILE__, function() {
    // Cleanup if needed
});
