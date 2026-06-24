<?php
/**
 * MCP Product Tool Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class MCP_Product_Tool {
    public function get_definition() {
        return array(
            'name' => 'get_products',
            'description' => 'Retrieve WooCommerce products with optional filtering and search',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'limit' => [
                        'type' => 'integer',
                        'description' => 'Number of products to retrieve (default: 20)'
                    ],
                    'offset' => [
                        'type' => 'integer',
                        'description' => 'Offset for pagination (default: 0)'
                    ],
                    'search' => [
                        'type' => 'string',
                        'description' => 'Search term for product name or description'
                    ],
                    'category' => [
                        'type' => 'integer',
                        'description' => 'Filter by product category ID'
                    ],
                    'status' => [
                        'type' => 'string',
                        'description' => 'Filter by product status'
                    ],
                    'stock_status' => [
                        'type' => 'string',
                        'description' => 'Filter by stock status'
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
                'products' => array()
            );
        }

        $args = array(
            'status' => 'publish',
            'limit' => isset($arguments['limit']) ? intval($arguments['limit']) : 20,
            'offset' => isset($arguments['offset']) ? intval($arguments['offset']) : 0
        );

        // Search by product name
        if (!empty($arguments['search'])) {
            $args['s'] = sanitize_text_field($arguments['search']);
        }

        // Filter by category
        if (!empty($arguments['category'])) {
            $args['category'] = intval($arguments['category']);
        }

        // Filter by status
        if (!empty($arguments['status'])) {
            $args['status'] = sanitize_text_field($arguments['status']);
        }

        // Filter by stock status
        if (!empty($arguments['stock_status'])) {
            $args['stock_status'] = sanitize_text_field($arguments['stock_status']);
        }

        try {
            $products = wc_get_products($args);

            $formatted_products = array();
            foreach ($products as $product) {
                $formatted_products[] = $this->format_product($product);
            }

            return [
                "content" => [
                    [
                        "type" => "text",
                        "text" => "Retrieved " . count($formatted_products) . " products."
                    ]
                ],
                'structuredContent' => [
                    'products' => $formatted_products,
                    'count' => count($formatted_products)
                ]
            ];
        } catch (Exception $e) {
            return array(
                'error' => $e->getMessage(),
                'products' => array()
            );
        }
    }

    private function format_product($product) {
        $formatted = [
            'id' => $product->get_id(),
            'name' => $product->get_name(),
            'description' => wp_trim_words($product->get_description(), 20),
            'price' => $product->get_price(),
            'regular_price' => $product->get_regular_price(),
            'sale_price' => $product->get_sale_price(),
            'stock_status' => $product->get_stock_status(),
            'stock_quantity' => $product->get_stock_quantity(),
            'sku' => $product->get_sku(),
            'type' => $product->get_type(),
            'rating' => array(
                'average' => $product->get_average_rating(),
                'count' => $product->get_review_count()
            ),
            'categories' => $this->get_product_categories($product->get_id()),
            'tags' => $this->get_product_tags($product->get_id()),
            'images' => $this->get_product_images($product)
        ];

        // Include variations if product is variable
        if ($product->get_type() === 'variable') {
            $formatted['variations'] = $this->get_product_variations($product->get_id());
        }

        return $formatted;
    }

    private function get_product_categories($product_id) {
        $categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'all'));
        
        $formatted = array();
        foreach ($categories as $cat) {
            $formatted[] = array(
                'id' => $cat->term_id,
                'name' => $cat->name,
                'slug' => $cat->slug
            );
        }

        return $formatted;
    }

    private function get_product_tags($product_id) {
        $tags = wp_get_post_terms($product_id, 'product_tag', array('fields' => 'all'));
        
        $formatted = array();
        foreach ($tags as $tag) {
            $formatted[] = array(
                'id' => $tag->term_id,
                'name' => $tag->name,
                'slug' => $tag->slug
            );
        }

        return $formatted;
    }

    private function get_product_images($product) {
        $images = array();
        
        if ($product->get_image_id()) {
            $images[] = array(
                'id' => $product->get_image_id(),
                'url' => wp_get_attachment_url($product->get_image_id())
            );
        }

        $gallery_image_ids = $product->get_gallery_image_ids();
        if (!empty($gallery_image_ids)) {
            foreach ($gallery_image_ids as $image_id) {
                $images[] = array(
                    'id' => $image_id,
                    'url' => wp_get_attachment_url($image_id)
                );
            }
        }

        return $images;
    }

    private function get_product_variations($product_id) {
        $variations = array();
        $variable_product = wc_get_product($product_id);

        if ($variable_product && $variable_product->get_type() === 'variable') {
            $variation_ids = $variable_product->get_children();

            foreach ($variation_ids as $variation_id) {
                $variation = wc_get_product($variation_id);

                if ($variation) {
                    $variations[] = array(
                        'id' => $variation->get_id(),
                        'name' => $variation->get_name(),
                        'sku' => $variation->get_sku(),
                        'price' => $variation->get_price(),
                        'regular_price' => $variation->get_regular_price(),
                        'sale_price' => $variation->get_sale_price(),
                        'stock_status' => $variation->get_stock_status(),
                        'stock_quantity' => $variation->get_stock_quantity(),
                        'attributes' => $variation->get_attributes(),
                        'image' => $variation->get_image_id() ? array(
                            'id' => $variation->get_image_id(),
                            'url' => wp_get_attachment_url($variation->get_image_id())
                        ) : null
                    );
                }
            }
        }

        return $variations;
    }
}
