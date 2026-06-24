/**
 * Admin JavaScript for MCP Commerce Connector
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        initializeTabs();
        initializeRefreshButton();
        initializeAutoRefresh();
    });

    /**
     * Initialize tab switching functionality
     */
    function initializeTabs() {
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();

            var tabTarget = $(this).attr('href');

            // Remove active class from all tabs and hide all contents
            $('.nav-tab').removeClass('nav-tab-active');
            $('.mcp-tab-content').hide();

            // Add active class to clicked tab and show corresponding content
            $(this).addClass('nav-tab-active');
            $(tabTarget).show();
        });
    }

    /**
     * Initialize refresh button
     */
    function initializeRefreshButton() {
        $(document).on('click', '.mcp-refresh-btn', function() {
            var $btn = $(this);
            var originalText = $btn.text();

            $btn.prop('disabled', true).text('Refreshing...');

            setTimeout(function() {
                location.reload();
            }, 500);
        });
    }

    /**
     * Initialize auto-refresh for logs
     */
    function initializeAutoRefresh() {
        var autoRefreshCheckbox = $('.mcp-auto-refresh');

        if (autoRefreshCheckbox.length) {
            autoRefreshCheckbox.on('change', function() {
                if ($(this).is(':checked')) {
                    startAutoRefresh();
                } else {
                    stopAutoRefresh();
                }
            });
        }
    }

    /**
     * Start auto-refresh of logs
     */
    function startAutoRefresh() {
        window.mcpAutoRefreshInterval = setInterval(function() {
            $('.mcp-logs-container').load(window.location.href + ' .mcp-logs-container');
        }, 5000);
    }

    /**
     * Stop auto-refresh of logs
     */
    function stopAutoRefresh() {
        if (window.mcpAutoRefreshInterval) {
            clearInterval(window.mcpAutoRefreshInterval);
        }
    }

})(jQuery);
