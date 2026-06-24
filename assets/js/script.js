/**
 * MCP Commerce Connector Frontend JavaScript
 */

(function() {
    'use strict';

    // Initialize on document ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }

    function initialize() {
        // Application initialization code
    }

    // Utility functions
    window.MCP = window.MCP || {};

    /**
     * Make a request to the MCP server
     */
    window.MCP.request = function(method, params, callback) {
        var data = {
            jsonrpc: '2.0',
            method: method,
            params: params,
            id: Math.floor(Math.random() * 10000)
        };

        fetch('/wp-json/mcp/v1/server', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': window.mcpNonce || ''
            },
            body: JSON.stringify(data)
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (callback) {
                callback(null, data);
            }
        })
        .catch(function(error) {
            if (callback) {
                callback(error, null);
            }
        });
    };

    /**
     * Initialize MCP connection
     */
    window.MCP.initialize = function(callback) {
        window.MCP.request('initialize', {}, callback);
    };

    /**
     * Get list of available tools
     */
    window.MCP.listTools = function(callback) {
        window.MCP.request('tools/list', {}, callback);
    };

    /**
     * Call a tool
     */
    window.MCP.callTool = function(toolName, arguments, callback) {
        window.MCP.request('tools/call', {
            name: toolName,
            arguments: arguments
        }, callback);
    };

})();
