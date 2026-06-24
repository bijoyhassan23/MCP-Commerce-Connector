<?php
/**
 * Admin Page for MCP Commerce Connector
 */

if (!defined('ABSPATH')) {
    exit;
}

$connector = MCP_Commerce_Connector::get_instance();
$logs = $connector->get_logs(50);
$tools = $connector->get_tools();
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div style="margin: 20px 0;">
        <p>Version: <strong><?php echo esc_html(MCP_COMMERCE_VERSION); ?></strong> | Protocol: <strong><?php echo esc_html(MCP_COMMERCE_PROTOCOL_VERSION); ?></strong></p>
    </div>

    <div class="mcp-tabs">
        <h2 class="nav-tab-wrapper">
            <a href="#mcp-overview" class="nav-tab nav-tab-active">Overview</a>
            <a href="#mcp-tools" class="nav-tab">Tools</a>
            <a href="#mcp-logs" class="nav-tab">Activity Log</a>
        </h2>

        <div id="mcp-overview" class="mcp-tab-content">
            <div class="postbox">
                <h2 class="hndle"><span>Plugin Information</span></h2>
                <div class="inside">
                    <p><strong>MCP Commerce Connector</strong> is a WordPress plugin that exposes an MCP (Model Context Protocol) server through a REST API endpoint.</p>
                    
                    <h3>Features:</h3>
                    <ul style="list-style: disc; margin-left: 20px;">
                        <li>MCP Server Endpoint</li>
                        <li>REST API Based Communication</li>
                        <li>WooCommerce Product Integration</li>
                        <li>WooCommerce Order Management</li>
                        <li>Activity Logging</li>
                        <li>Extensible Tool Architecture</li>
                    </ul>

                    <h3>REST API Endpoint:</h3>
                    <p><code><?php echo esc_html(rest_url('mcp/v1/server')); ?></code></p>

                    <h3>Supported MCP Methods:</h3>
                    <ul style="list-style: disc; margin-left: 20px;">
                        <li><code>initialize</code> - Initialize the MCP connection</li>
                        <li><code>tools/list</code> - List available tools</li>
                        <li><code>tools/call</code> - Execute a tool</li>
                    </ul>
                </div>
            </div>

            <div class="postbox">
                <h2 class="hndle"><span>Security Information</span></h2>
                <div class="inside">
                    <p>This plugin includes several security features:</p>
                    <ul style="list-style: disc; margin-left: 20px;">
                        <li>WordPress permission checks (require manage_options capability)</li>
                        <li>Input sanitization</li>
                        <li>JSON-RPC validation</li>
                        <li>Error handling</li>
                        <li>Protected logs directory</li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="mcp-tools" class="mcp-tab-content" style="display: none;">
            <div class="postbox">
                <h2 class="hndle"><span>Available Tools</span></h2>
                <div class="inside">
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th>Tool Name</th>
                                <th>Description</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tools as $tool_name => $tool) : ?>
                                <?php $definition = $tool->get_definition(); ?>
                                <tr>
                                    <td><code><?php echo esc_html($definition['name']); ?></code></td>
                                    <td><?php echo esc_html($definition['description']); ?></td>
                                    <td><span class="badge" style="background-color: #28a745; color: white; padding: 3px 8px; border-radius: 3px;">Available</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="mcp-logs" class="mcp-tab-content" style="display: none;">
            <div class="postbox">
                <h2 class="hndle"><span>Activity Log</span></h2>
                <div class="inside">
                    <?php if (empty($logs)) : ?>
                        <p>No activity logs yet.</p>
                    <?php else : ?>
                        <table class="widefat">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Level</th>
                                    <th>Message</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $log) : ?>
                                    <tr>
                                        <td><?php echo esc_html($log['timestamp']); ?></td>
                                        <td><span style="padding: 3px 8px; border-radius: 3px; background-color: #007cba; color: white;"><?php echo esc_html($log['level']); ?></span></td>
                                        <td><?php echo esc_html($log['message']); ?></td>
                                        <td><code><?php echo esc_html(json_encode($log['data'])); ?></code></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .mcp-tabs {
        margin-top: 20px;
    }

    .nav-tab-wrapper {
        border-bottom: 1px solid #ccc;
        margin: 0 0 20px 0;
        padding: 0;
    }

    .nav-tab {
        background: #f1f1f1;
        border: 1px solid #ccc;
        border-bottom: none;
        color: #0073aa;
        cursor: pointer;
        padding: 10px 15px;
        text-decoration: none;
        margin-right: 5px;
    }

    .nav-tab:hover {
        background: #e9e9e9;
    }

    .nav-tab-active {
        background: #fff;
        border-bottom: 1px solid #fff !important;
        color: #000;
        font-weight: bold;
    }

    .mcp-tab-content {
        display: none;
    }

    .postbox {
        background: #fff;
        border: 1px solid #ccc;
        margin-bottom: 20px;
        padding: 0;
    }

    .hndle {
        background: #f1f1f1;
        border-bottom: 1px solid #ccc;
        padding: 12px;
        margin: 0;
        font-size: 14px;
        font-weight: 600;
    }

    .inside {
        padding: 12px;
    }

    .inside table {
        width: 100%;
        border-collapse: collapse;
    }

    .inside table th,
    .inside table td {
        padding: 8px;
        border-bottom: 1px solid #eee;
        text-align: left;
    }

    .inside table th {
        background: #f9f9f9;
        font-weight: bold;
    }

    .inside code {
        background: #f5f5f5;
        padding: 2px 5px;
        border-radius: 3px;
        font-family: monospace;
    }

    .inside ul {
        margin: 15px 0;
    }

    .badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: 600;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.nav-tab');
        const contents = document.querySelectorAll('.mcp-tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all tabs and hide all contents
                tabs.forEach(t => t.classList.remove('nav-tab-active'));
                contents.forEach(c => c.style.display = 'none');
                
                // Add active class to clicked tab and show corresponding content
                this.classList.add('nav-tab-active');
                const target = this.getAttribute('href');
                document.querySelector(target).style.display = 'block';
            });
        });
    });
</script>
