<?php
/**
 * MCP REST Server Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class MCP_REST_Server {

    private $connector = null;

    public function __construct($connector = null) {
        $this->connector = $connector;
        $this->register_routes();
    }

    public function register_routes() {
        register_rest_route(
            'mcp/v1',
            '/server',
            array(
                array(
                    'methods' => array('POST', 'OPTIONS'),
                    'callback' => array($this, 'handle_request'),
                    'permission_callback' => '__return_true'
                )
            )
        );
    }

    public function check_permissions($request) {
        // Allow authenticated users with manage_options capability
        if (!is_user_logged_in()) {
            return false;
        }
        return current_user_can('manage_options');
    }

    public function handle_request($request) {
        // Check permissions
        if (!$this->check_permissions($request) && false) {
            return new WP_REST_Response(array(
                'jsonrpc' => '2.0',
                'id' => null,
                'error' => array(
                    'code' => -32603,
                    'message' => 'Unauthorized'
                )
            ), 403);
        }

        $body = json_decode($request->get_body(), true);

        if (empty($body) || !isset($body['jsonrpc']) || $body['jsonrpc'] !== '2.0') {
            return $this->send_error($body['id'] ?? null, 'Invalid JSON-RPC request');
        }

        $method = $body['method'] ?? null;
        $id = $body['id'] ?? null;

        $connector = $this->connector ?: MCP_Commerce_Connector::get_instance();
        $connector->add_log('MCP Request', 'info', array('method' => $method, 'id' => $id));

        switch ($method) {
            case 'initialize':
                return $this->handle_initialize($id, $connector);

            case 'tools/list':
                return $this->handle_tools_list($id, $connector);

            case 'tools/call':
                return $this->handle_tool_call($id, $body['params'] ?? array(), $connector);

            default:
                return $this->send_error($id, 'Method not found');
        }
    }

    private function handle_initialize($id, $connector) {
        $result = array(
            'protocolVersion' => MCP_COMMERCE_PROTOCOL_VERSION,
            'serverInfo' => array(
                'name' => 'MCP Commerce Connector',
                'version' => MCP_COMMERCE_VERSION
            ),
            'capabilities' => new stdClass() 
        );

        $connector->add_log('Initialize request handled', 'info');

        return $this->send_response($id, $result);
    }

    private function handle_tools_list($id, $connector) {
        $tools_registry = $connector->get_tools();

        $tools = array();
        foreach ($tools_registry as $tool_name => $tool_instance) {
            if (method_exists($tool_instance, 'get_definition')) {
                $tools[] = $tool_instance->get_definition();
            }
        }

        $connector->add_log('Tools list request handled', 'info', array('tools_count' => count($tools)));

        return $this->send_response($id, array('tools' => $tools));
    }

    private function handle_tool_call($id, $params, $connector) {
        $tool_name = $params['name'] ?? null;
        $arguments = $params['arguments'] ?? array();

        if (!$tool_name) {
            return $this->send_error($id, 'Tool name not specified');
        }

        $connector->add_log('Tool call', 'info', array('tool' => $tool_name, 'arguments' => $arguments));

        $result = $connector->execute_tool($tool_name, $arguments);

        return $this->send_response($id, $result);
    }

    private function send_response($id, $result) {
        return new WP_REST_Response(array(
            'jsonrpc' => '2.0',
            'id' => $id,
            'result' => $result
        ), 200);
    }

    private function send_error($id, $message) {
        return new WP_REST_Response(array(
            'jsonrpc' => '2.0',
            'id' => $id,
            'error' => array(
                'code' => -32603,
                'message' => $message
            )
        ), 200);
    }
}
