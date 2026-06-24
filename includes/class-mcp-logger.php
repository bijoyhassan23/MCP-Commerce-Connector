<?php
/**
 * MCP Logger Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class MCP_Logger {

    private static $instance = null;
    private $log_file = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->log_file = MCP_COMMERCE_LOGS_DIR . 'mcp-activity.log';
    }

    public function add_log($message, $level = 'info', $data = array()) {
        $timestamp = current_time('mysql');
        $log_entry = array(
            'timestamp' => $timestamp,
            'level' => strtoupper($level),
            'message' => $message,
            'data' => $data
        );

        $log_line = json_encode($log_entry) . "\n";

        // Ensure directory exists
        if (!file_exists(MCP_COMMERCE_LOGS_DIR)) {
            wp_mkdir_p(MCP_COMMERCE_LOGS_DIR);
        }

        // Append to log file
        error_log($log_line, 3, $this->log_file);
    }

    public function get_logs($limit = 100) {
        if (!file_exists($this->log_file)) {
            return array();
        }

        $logs = array();
        $file = fopen($this->log_file, 'r');

        if ($file) {
            $lines = file($this->log_file);
            $lines = array_reverse($lines);
            
            foreach ($lines as $line) {
                if (empty(trim($line))) {
                    continue;
                }

                $entry = json_decode(trim($line), true);
                if ($entry) {
                    $logs[] = $entry;
                }

                if (count($logs) >= $limit) {
                    break;
                }
            }

            fclose($file);
        }

        return array_reverse($logs);
    }

    public function clear_logs() {
        if (file_exists($this->log_file)) {
            unlink($this->log_file);
        }
    }

    public function get_log_file_size() {
        if (file_exists($this->log_file)) {
            return filesize($this->log_file);
        }
        return 0;
    }
}
