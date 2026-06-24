<?php
/**
 * MCP Order Tool Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class MCP_Order_Tool {
    public function get_definition() {
        return array(
            'name' => 'get_orders',
            'description' => 'Retrieve WooCommerce orders with optional filtering by status and date',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'limit' => [
                        'type' => 'integer',
                        'description' => 'Number of orders to retrieve (default: 20)'
                    ],
                    'offset' => [
                        'type' => 'integer',
                        'description' => 'Offset for pagination (default: 0)'
                    ],
                    'status' => [
                        'type' => 'string',
                        'description' => 'Filter by order status (e.g., pending, processing, completed)'
                    ],
                    'customer_id' => [
                        'type' => 'integer',
                        'description' => 'Filter by customer ID'
                    ],
                    'date_from' => [
                        'type' => 'string',
                        'format' => 'date-time',
                        'description' => 'Filter orders created after this date (ISO 8601 format)'
                    ],
                    'date_to' => [
                        'type' => 'string',
                        'format' => 'date-time',
                        'description' => 'Filter orders created before this date (ISO 8601 format)'
                    ],
                    'search' => [
                        'type' => 'string',
                        'description' => 'Search term for order number or customer name'
                    ]
                ],
                'required' => []
            ],
        );
    }

    public function execute($arguments) {
        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            return array(
                'error' => 'WooCommerce is not active',
                'orders' => array()
            );
        }

        $args = array(
            'limit' => isset($arguments['limit']) ? intval($arguments['limit']) : 20,
            'offset' => isset($arguments['offset']) ? intval($arguments['offset']) : 0
        );

        // Filter by status
        if (!empty($arguments['status'])) {
            $status = sanitize_text_field($arguments['status']);
            if (!in_array($status, array('pending', 'processing', 'on-hold', 'completed', 'cancelled', 'refunded', 'failed'), true)) {
                $status = 'any';
            }
            $args['status'] = $status;
        } else {
            $args['status'] = 'any';
        }

        // Filter by customer
        if (!empty($arguments['customer_id'])) {
            $args['customer_id'] = intval($arguments['customer_id']);
        }

        // Filter by date range
        if (!empty($arguments['date_from'])) {
            $args['date_created'] = '>=' . sanitize_text_field($arguments['date_from']);
        }

        if (!empty($arguments['date_to'])) {
            $args['date_created'] = '<=' . sanitize_text_field($arguments['date_to']);
        }

        // Search by order number
        if (!empty($arguments['search'])) {
            $args['s'] = sanitize_text_field($arguments['search']);
        }

        try {
            $orders = wc_get_orders($args);

            $formatted_orders = array();
            foreach ($orders as $order) {
                $formatted_orders[] = $this->format_order($order);
            }

            return [
                "content" => [
                    [
                        "type" => "text",
                        "text" => "Retrieved " . count($formatted_orders) . " orders."
                    ]
                ],
                'structuredContent' => [
                    'orders' => $formatted_orders,
                    'count' => count($formatted_orders)
                ]
            ];
        } catch (Exception $e) {
            return array(
                'error' => $e->getMessage(),
                'orders' => array()
            );
        }
    }

    private function format_order($order) {
        $order_id = $order->get_id();
        
        return array(
            'id' => $order_id,
            'order_number' => $order->get_order_number(),
            'status' => $order->get_status(),
            'date_created' => $order->get_date_created() ? $order->get_date_created()->date('c') : null,
            'date_modified' => $order->get_date_modified() ? $order->get_date_modified()->date('c') : null,
            'total' => $order->get_total(),
            'subtotal' => $order->get_subtotal(),
            'total_tax' => $order->get_total_tax(),
            'total_shipping' => $order->get_total_shipping(),
            'discount_total' => $order->get_discount_total(),
            'currency' => $order->get_currency(),
            'payment_method' => $order->get_payment_method(),
            'payment_method_title' => $order->get_payment_method_title(),
            'customer' => $this->get_customer_info($order),
            'billing' => $this->get_billing_address($order),
            'shipping' => $this->get_shipping_address($order),
            'items' => $this->get_order_items($order),
            'notes' => $order->get_customer_note()
        );
    }

    private function get_customer_info($order) {
        return array(
            'id' => $order->get_customer_id(),
            'first_name' => $order->get_billing_first_name(),
            'last_name' => $order->get_billing_last_name(),
            'email' => $order->get_billing_email(),
            'phone' => $order->get_billing_phone()
        );
    }

    private function get_billing_address($order) {
        return array(
            'first_name' => $order->get_billing_first_name(),
            'last_name' => $order->get_billing_last_name(),
            'company' => $order->get_billing_company(),
            'address_1' => $order->get_billing_address_1(),
            'address_2' => $order->get_billing_address_2(),
            'city' => $order->get_billing_city(),
            'state' => $order->get_billing_state(),
            'postcode' => $order->get_billing_postcode(),
            'country' => $order->get_billing_country(),
            'email' => $order->get_billing_email(),
            'phone' => $order->get_billing_phone()
        );
    }

    private function get_shipping_address($order) {
        return array(
            'first_name' => $order->get_shipping_first_name(),
            'last_name' => $order->get_shipping_last_name(),
            'company' => $order->get_shipping_company(),
            'address_1' => $order->get_shipping_address_1(),
            'address_2' => $order->get_shipping_address_2(),
            'city' => $order->get_shipping_city(),
            'state' => $order->get_shipping_state(),
            'postcode' => $order->get_shipping_postcode(),
            'country' => $order->get_shipping_country()
        );
    }

    private function get_order_items($order) {
        $items = array();

        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            
            $items[] = array(
                'id' => $item->get_id(),
                'product_id' => $item->get_product_id(),
                'product_name' => $item->get_name(),
                'quantity' => $item->get_quantity(),
                'price' => $item->get_subtotal(),
                'total' => $item->get_total(),
                'tax_total' => $item->get_total_tax(),
                'sku' => $product ? $product->get_sku() : '',
                'variation_id' => $item->get_variation_id()
            );
        }

        return $items;
    }
}
