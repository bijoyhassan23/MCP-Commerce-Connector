# MCP Commerce Connector

**Status: ✅ Ready**

MCP Commerce Connector is a WordPress plugin that exposes an MCP (Model Context Protocol) server through a REST API endpoint. The plugin acts as a bridge between AI clients and WooCommerce data, allowing external MCP clients to retrieve product and order information through standardized MCP tool calls.

## Installation

1. Copy the `mcp-commerce-connector` folder to `/wp-content/plugins/`
2. Activate the plugin from the WordPress admin dashboard
3. The MCP endpoint will be available at `/wp-json/mcp/v1/server`

## Configuration

No configuration required. The plugin works out of the box with WooCommerce.

## REST API Endpoint

### URL
```
POST /wp-json/mcp/v1/server
```

### Authentication

Requires WordPress admin authentication (user must have `manage_options` capability).

## Supported MCP Methods

### 1. Initialize
Initialize the MCP connection.

**Request:**
```json
{
  "jsonrpc": "2.0",
  "id": 1,
  "method": "initialize"
}
```

**Response:**
```json
{
  "jsonrpc": "2.0",
  "id": 1,
  "result": {
    "protocolVersion": "2025-11-25",
    "serverInfo": {
      "name": "MCP Commerce Connector",
      "version": "1.0.0"
    },
    "capabilities": {}
  }
}
```

### 2. Tools List
Get the list of available tools.

**Request:**
```json
{
  "jsonrpc": "2.0",
  "id": 2,
  "method": "tools/list"
}
```

**Response:**
```json
{
  "jsonrpc": "2.0",
  "id": 2,
  "result": {
    "tools": [
      {
        "name": "get_products",
        "description": "Retrieve WooCommerce products with optional filtering and search"
      },
      {
        "name": "get_orders",
        "description": "Retrieve WooCommerce orders with optional filtering by status and date"
      }
    ]
  }
}
```

### 3. Tool Call
Execute a tool.

**Request:**
```json
{
  "jsonrpc": "2.0",
  "id": 3,
  "method": "tools/call",
  "params": {
    "name": "get_products",
    "arguments": {
      "limit": 10,
      "offset": 0
    }
  }
}
```

## Available Tools

### get_products
Retrieve WooCommerce products.

**Parameters:**
- `limit` (integer): Number of products to retrieve (default: 20)
- `offset` (integer): Offset for pagination (default: 0)
- `search` (string): Search term for product name
- `category` (integer): Filter by category ID
- `status` (string): Filter by status (default: publish)
- `stock_status` (string): Filter by stock status (instock, outofstock, onbackorder)

**Example Response:**
```json
{
  "products": [
    {
      "id": 123,
      "name": "Sample Product",
      "description": "Product description...",
      "price": "99.99",
      "regular_price": "119.99",
      "sale_price": "99.99",
      "stock_status": "instock",
      "stock_quantity": 100,
      "sku": "PROD123",
      "type": "simple",
      "rating": {
        "average": 4.5,
        "count": 25
      },
      "categories": [
        {
          "id": 1,
          "name": "Electronics",
          "slug": "electronics"
        }
      ],
      "tags": [
        {
          "id": 5,
          "name": "Popular",
          "slug": "popular"
        }
      ],
      "images": [
        {
          "id": 456,
          "url": "https://example.com/product.jpg"
        }
      ]
    }
  ],
  "count": 1
}
```

### get_orders
Retrieve WooCommerce orders.

**Parameters:**
- `limit` (integer): Number of orders to retrieve (default: 20)
- `offset` (integer): Offset for pagination (default: 0)
- `status` (string): Filter by status (pending, processing, on-hold, completed, cancelled, refunded, failed, any)
- `customer_id` (integer): Filter by customer ID
- `date_from` (string): Filter orders from date (YYYY-MM-DD format)
- `date_to` (string): Filter orders to date (YYYY-MM-DD format)
- `search` (string): Search by order number

**Example Response:**
```json
{
  "orders": [
    {
      "id": 5001,
      "order_number": "5001",
      "status": "processing",
      "date_created": "2025-06-23T10:30:00Z",
      "date_modified": "2025-06-23T11:00:00Z",
      "total": "150.00",
      "subtotal": "140.00",
      "total_tax": "10.00",
      "total_shipping": "5.00",
      "discount_total": "5.00",
      "currency": "USD",
      "payment_method": "credit_card",
      "payment_method_title": "Credit Card",
      "customer": {
        "id": 1,
        "first_name": "John",
        "last_name": "Doe",
        "email": "john@example.com",
        "phone": "+1234567890"
      },
      "billing": {
        "first_name": "John",
        "last_name": "Doe",
        "company": "",
        "address_1": "123 Main St",
        "address_2": "",
        "city": "New York",
        "state": "NY",
        "postcode": "10001",
        "country": "US",
        "email": "john@example.com",
        "phone": "+1234567890"
      },
      "shipping": {
        "first_name": "John",
        "last_name": "Doe",
        "company": "",
        "address_1": "123 Main St",
        "address_2": "",
        "city": "New York",
        "state": "NY",
        "postcode": "10001",
        "country": "US"
      },
      "items": [
        {
          "id": 1,
          "product_id": 123,
          "product_name": "Sample Product",
          "quantity": 1,
          "price": "99.99",
          "total": "99.99",
          "tax_total": "10.00",
          "sku": "PROD123",
          "variation_id": 0
        }
      ],
      "notes": "Customer special request"
    }
  ],
  "count": 1
}
```

## Plugin Features

- ✅ MCP Server Endpoint
- ✅ REST API Based Communication
- ✅ Tool Discovery Support
- ✅ Product Information Tool
- ✅ Order Information Tool
- ✅ WordPress Native Integration
- ✅ WooCommerce Support
- ✅ JSON-RPC Response Format
- ✅ Extensible Tool Architecture
- ✅ Activity Logging
- ✅ Admin Dashboard

## Security

- WordPress permission checks (requires `manage_options`)
- Input sanitization
- JSON-RPC validation
- Error handling
- Protected logs directory
- Rate limiting ready

## Requirements

- WordPress 5.0+
- PHP 7.2+
- WooCommerce 3.0+

## Version

**Current Version:** 1.0.0

**MCP Protocol Version:** 2025-11-25

## License

GPL2 - See LICENSE file for details

## Support

For issues and support, please contact the plugin developer.

---

**Status:** ✅ In Production
