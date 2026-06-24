# MCP Commerce Connector

**Status: 🚧 In Development**

## Overview

MCP Commerce Connector is a WordPress plugin that exposes an MCP (Model Context Protocol) server through a REST API endpoint.

The plugin acts as a bridge between AI clients and WooCommerce data, allowing external MCP clients to retrieve product and order information through standardized MCP tool calls.

## Key Features

- MCP Server Endpoint
- REST API Based Communication
- Tool Discovery Support
- Product Information Tool
- Order Information Tool
- WordPress Native Integration
- WooCommerce Support
- JSON-RPC Response Format
- Extensible Tool Architecture
- Activity Logging

---

# Plugin Architecture

## Core Class: `MCP_Commerce_Connector`

The main plugin class is responsible for plugin initialization, configuration management, service loading, and MCP request routing.

### Key Responsibilities

- Plugin bootstrap
- MCP endpoint registration
- Tool registration
- Request validation
- Response formatting
- Logging
- Configuration management

### Key Methods

- `get_instance()`
- `init()`
- `register_rest_routes()`
- `handle_mcp_request()`
- `get_tools()`
- `execute_tool()`
- `send_response()`
- `add_log()`
- `get_logs()`

---

# Service Classes

## 1. MCP_REST_Server

**File:** `includes/class-mcp-rest-server.php`

Handles all MCP requests and responses.

### Responsibilities

- Register MCP REST endpoint
- Process JSON-RPC requests
- Handle initialization requests
- Handle tool listing requests
- Handle tool execution requests

### Endpoint

```
POST /wp-json/mcp/v1/server
```

---

## 2. MCP_Product_Tool

**File:** `includes/tools/class-mcp-product-tool.php`

Provides product information to MCP clients.

### Tool Name

```json
get_products
```

### Responsibilities

- Retrieve WooCommerce products
- Search products
- Filter products
- Return product metadata

### Example Response

```json
{
  "products": [
    {
      "id": 123,
      "name": "Sample Product",
      "price": "99.99",
      "stock_status": "instock"
    }
  ]
}
```

---

## 3. MCP_Order_Tool

**File:** `includes/tools/class-mcp-order-tool.php`

Provides WooCommerce order information.

### Tool Name

```json
get_orders
```

### Responsibilities

- Retrieve orders
- Search orders
- Filter by status
- Return customer information
- Return order details

### Example Response

```json
{
  "orders": [
    {
      "id": 5001,
      "status": "processing",
      "total": "150.00"
    }
  ]
}
```

---

# MCP Protocol Support

## Initialize Request

### Request

```json
{
  "jsonrpc": "2.0",
  "id": 1,
  "method": "initialize"
}
```

### Response

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

---

## Tools List Request

### Request

```json
{
  "jsonrpc": "2.0",
  "id": 2,
  "method": "tools/list"
}
```

### Response

```json
{
  "tools": [
    {
      "name": "get_products",
      "description": "Retrieve WooCommerce products"
    },
    {
      "name": "get_orders",
      "description": "Retrieve WooCommerce orders"
    }
  ]
}
```

---

## Tool Call Request

### Product Tool

```json
{
  "jsonrpc": "2.0",
  "id": 3,
  "method": "tools/call",
  "params": {
    "name": "get_products",
    "arguments": {}
  }
}
```

### Order Tool

```json
{
  "jsonrpc": "2.0",
  "id": 4,
  "method": "tools/call",
  "params": {
    "name": "get_orders",
    "arguments": {}
  }
}
```

---

# REST API Endpoint

## Main MCP Endpoint

```
POST /wp-json/mcp/v1/server
```

### Supported Methods

- initialize
- tools/list
- tools/call

---

# File Structure

```text
mcp-commerce-connector.php
│
├── admin/
│   ├── admin-page.php
│   └── assets/
│       ├── css/
│       │   └── admin.css
│       └── js/
│           └── admin.js
│
├── includes/
│   ├── class-mcp-rest-server.php
│   ├── class-mcp-logger.php
│   │
│   └── tools/
│       ├── class-mcp-product-tool.php
│       └── class-mcp-order-tool.php
│
├── assets/
│   ├── css/
│   └── js/
│
├── logs/
│
└── README.md
```

---

# Available MCP Tools

| Tool Name | Description |
|------------|-------------|
| get_products | Retrieve WooCommerce products |
| get_orders | Retrieve WooCommerce orders |

---

# Security Features

- WordPress permission checks
- Input sanitization
- Request validation
- JSON-RPC validation
- Error handling
- Rate limiting (optional)

---

# Logging System

The plugin includes an activity logging system.

### Logged Events

- MCP initialization requests
- Tool listing requests
- Tool execution requests
- Errors and exceptions

---

# Technologies Used

- WordPress REST API
- WooCommerce
- PHP 8+
- JSON-RPC 2.0
- Model Context Protocol (MCP)

---

# Version

**Version:** 1.0.0

**Protocol Version:** 2025-11-25

**Status:** In Development