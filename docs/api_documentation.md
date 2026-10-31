# Smart Garments POS — API Documentation

> **Base URL:** `http://10.10.10.209/smart-garments-pos/api` 
> **Content-Type:** `application/json`
> **Auth:** Laravel Sanctum — pass token as `Authorization: Bearer <token>` header on protected routes.

---

## Table of Contents

1. [Authentication](#1-authentication)
   - [Login](#11-login)
   - [Register](#12-register)
   - [Logout](#13-logout)
2. [Profile](#2-profile)
   - [Get Profile](#21-get-profile)
   - [Update Profile](#22-update-profile)
3. [Categories](#3-categories)
   - [List All Categories](#31-list-all-categories)
4. [Products](#4-products)
   - [List Products](#41-list-products)
   - [Get Single Product](#42-get-single-product)
5. [Orders](#5-orders)
   - [Place Order](#51-place-order)
   - [Get My Orders](#52-get-my-orders)
   - [Get Single Order](#53-get-single-order)
6. [Coupons](#6-coupons)
   - [Validate Coupon](#61-validate-coupon)
7. [Website Settings](#7-website-settings)
   - [Get Settings](#71-get-settings)
8. [Offers](#8-offers)
   - [List Offers](#81-list-offers)
   - [Get Single Offer](#82-get-single-offer)
   - [Get Offer Products](#83-get-offer-products)

---

## 1. Authentication

### 1.1 Login

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/api/login` |
| **Auth Required** | ❌ No |

#### Request Body

```json
{
  "phone": "01XXXXXXXXX",
  "password": "yourpassword"
}
```

| Field | Type | Required | Rules |
|---|---|---|---|
| `phone` | string | ✅ Yes | max 15 characters |
| `password` | string | ✅ Yes | min 6, max 16 characters |

#### Response — Success `200`

```json
{
  "success": true,
  "access_token": "1|abc123...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "John Doe",
    "phone": "01XXXXXXXXX",
    "email": "john@example.com",
    "role_id": 0,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

#### Response — User Not Found `404`

```json
{
  "message": "User not found"
}
```

#### Response — Wrong Password `401`

```json
{
  "message": "Invalid credentials"
}
```

---

### 1.2 Register

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/api/register` |
| **Auth Required** | ❌ No |

#### Request Body

```json
{
  "name": "John Doe",
  "phone": "01XXXXXXXXX",
  "email": "john@example.com",
  "password": "yourpassword",
  "password_confirmation": "yourpassword"
}
```

| Field | Type | Required | Rules |
|---|---|---|---|
| `name` | string | ✅ Yes | max 255 characters |
| `phone` | string | ✅ Yes | max 15, must be unique |
| `email` | string | ❌ No | valid email, max 150, must be unique |
| `password` | string | ✅ Yes | min 6, max 16 characters |
| `password_confirmation` | string | ✅ Yes | must match `password` |

#### Response — Success `200`

```json
{
  "success": true,
  "message": "Registration successful",
  "access_token": "1|abc123...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "John Doe",
    "phone": "01XXXXXXXXX",
    "email": "john@example.com",
    "role_id": 0,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

#### Response — Validation Error `422`

```json
{
  "message": "The phone has already been taken.",
  "errors": {
    "phone": ["The phone has already been taken."]
  }
}
```

---

### 1.3 Logout

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/api/logout` |
| **Auth Required** | ✅ Yes (Bearer Token) |

#### Response — Success `200`

```json
{
  "message": "User successfully logged out"
}
```

---

## 2. Profile

### 2.1 Get Profile

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/api/profile` |
| **Auth Required** | ✅ Yes (Bearer Token) |

#### Response — Success `200`

```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "username": "johndoe",
    "phone": "01XXXXXXXXX",
    "email": "john@example.com",
    "role_id": 0,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

---

### 2.2 Update Profile

| | |
|---|---|
| **Method** | `PUT` |
| **Endpoint** | `/api/profile` |
| **Auth Required** | ✅ Yes (Bearer Token) |

#### Request Body

All fields are optional. Only send the fields you want to update.

```json
{
  "name": "John Doe",
  "username": "johndoe",
  "email": "john@example.com",
  "phone": "01XXXXXXXXX",
  "current_password": "oldpassword",
  "password": "newpassword",
  "password_confirmation": "newpassword"
}
```

| Field | Type | Required | Rules |
|---|---|---|---|
| `name` | string | ❌ No | max 150 characters |
| `username` | string | ❌ No | max 50 characters, must be unique |
| `email` | string | ❌ No | valid email, max 191, must be unique |
| `phone` | string | ❌ No | max 50 characters, must be unique |
| `current_password` | string | ❌ No | required if changing password, min 6 |
| `password` | string | ❌ No | new password, min 6 characters |
| `password_confirmation` | string | ❌ No | must match `password` |

> **Note:** If you want to change the password, you must provide `current_password` along with `password` and `password_confirmation`.

#### Response — Success `200`

```json
{
  "success": true,
  "message": "Profile updated successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "username": "johndoe",
    "phone": "01XXXXXXXXX",
    "email": "john@example.com",
    "role_id": 0,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

#### Response — Wrong Current Password `422`

```json
{
  "success": false,
  "message": "Current password does not match"
}
```

#### Response — Validation Error `422`

```json
{
  "success": false,
  "errors": {
    "email": ["The email has already been taken."]
  }
}
```

---

## 3. Categories

### 3.1 List All Categories

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/api/categories` |
| **Auth Required** | ❌ No |

#### Response — Success `200`

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "T-Shirts",
      "slug": "t-shirts",
      "image": "categories/tshirts.png",
      "is_active": 1,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  ]
}
```

#### Response — No Categories `404`

```json
{
  "success": false,
  "message": "Categories not found"
}
```

---

## 4. Products

### 4.1 List Products

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/api/products` |
| **Auth Required** | ❌ No |

#### Query Parameters

| Param | Type | Required | Description |
|---|---|---|---|
| `category` | string | ❌ No | Filter by category **slug** (e.g. `t-shirts`) |
| `search` | string | ❌ No | Search products by name |

**Example:** `/api/products?category=t-shirts&search=polo`

#### Response — Success `200`

Returns a **paginated** list (8 items per page). Products may include offer information.

```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Polo T-Shirt",
        "slug": "polo-t-shirt",
        "price": 500,
        "mrp": 600,
        "offer_price": 450,
        "thumbnail_image": "products/polo.jpg",
        "is_active": 1,
        "parent_id": null,
        "category_id": 1,
        "category": {
          "id": 1,
          "name": "T-Shirts",
          "slug": "t-shirts"
        },
        "freeProducts": [
          {
            "id": 5,
            "name": "Free Socks",
            "thumbnail_image": "products/socks.jpg",
            "price": 0
          }
        ]
      }
    ],
    "first_page_url": "http://yourdomain.com/api/products?page=1",
    "last_page": 5,
    "last_page_url": "http://yourdomain.com/api/products?page=5",
    "next_page_url": "http://yourdomain.com/api/products?page=2",
    "per_page": 8,
    "total": 40
  }
}
```

> **Offer fields explained:**
> - `offer_price` — discounted price (present if product has a **discount** offer)
> - `freeProducts` — array of free gift products (present if product has a **gift** offer)

---

### 4.2 Get Single Product

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/api/products/{slug}` |
| **Auth Required** | ❌ No |

#### URL Parameter

| Param | Type | Description |
|---|---|---|
| `slug` | string | Product slug (e.g. `polo-t-shirt`) |

#### Response — Success `200`

```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Polo T-Shirt",
    "slug": "polo-t-shirt",
    "price": 500,
    "mrp": 600,
    "offer_price": 450,
    "thumbnail_image": "products/polo.jpg",
    "is_active": 1,
    "parent_id": null,
    "category": {
      "id": 1,
      "name": "T-Shirts",
      "slug": "t-shirts"
    },
    "specifications": [
      {
        "id": 1,
        "key": "Material",
        "value": "100% Cotton"
      }
    ],
    "children": [
      {
        "id": 2,
        "name": "Polo T-Shirt - Red / M",
        "price": 500,
        "mrp": 600,
        "offer_price": 450,
        "size": { "id": 1, "name": "M" },
        "color": { "id": 1, "name": "Red", "code": "#FF0000" },
        "freeProducts": []
      }
    ]
  }
}
```

> **Note:** `children` are the variants of the product (different sizes/colors). Each child can also have `offer_price` or `freeProducts` if an offer applies.

#### Response — Not Found `404`

```json
{
  "success": false,
  "message": "Product not found"
}
```

---

## 5. Orders

### 5.1 Place Order

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/api/orders` |
| **Auth Required** | ❌ No (guest checkout supported — but logged in users get the order linked to their account) |

#### Request Body

```json
{
  "name": "John Doe",
  "phone": "01XXXXXXXXX",
  "shipping_address": "123 Main Street, Dhaka",
  "city": "Dhaka",
  "postal_code": "1212",
  "note": "Please deliver before 5 PM",
  "coupon_id": 2,
  "items": [
    {
      "id": 5,
      "qty": 2
    },
    {
      "id": 8,
      "qty": 1
    }
  ]
}
```

| Field | Type | Required | Rules |
|---|---|---|---|
| `name` | string | ✅ Yes | Customer's name |
| `phone` | string | ✅ Yes | Customer's phone |
| `shipping_address` | string | ✅ Yes | Full delivery address |
| `city` | string | ✅ Yes | Delivery city |
| `postal_code` | string | ❌ No | Postal / ZIP code |
| `note` | string | ❌ No | Order note / delivery instruction |
| `coupon_id` | integer | ❌ No | ID of the coupon to apply (must exist in coupons table) |
| `items` | array | ✅ Yes | Array of ordered items |
| `items[].id` | integer | ✅ Yes | Product ID (use the **variant/child** product ID if applicable) |
| `items[].qty` | integer | ✅ Yes | Quantity (min 1) |

> **Payment Method:** All orders are placed as **Cash on Delivery (COD)** by default with `payment_status: pending`.

#### Response — Success `200`

```json
{
  "success": true,
  "order": {
    "id": 101,
    "invoice_no": "SMG-20240101-ABCDEF",
    "customer_id": 1,
    "branch_id": 2,
    "warehouse_id": 3,
    "subtotal": 1000,
    "discount": 50,
    "coupon_discount": 100,
    "shipping_charge": 60,
    "total": 910,
    "due": 910,
    "coupon_code": "SAVE100",
    "coupon_id": 2,
    "sale_type": "e-commerce",
    "name": "John Doe",
    "phone": "01XXXXXXXXX",
    "shipping_address": "123 Main Street, Dhaka",
    "city": "Dhaka",
    "postal_code": "1212",
    "sale_note": "Please deliver before 5 PM",
    "status": "pending",
    "payment_status": "pending",
    "payment_method": "COD",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

#### Response — Product Not Found `404`

```json
{
  "success": false,
  "message": "Product not found"
}
```

#### Response — Server Error `500`

```json
{
  "success": false,
  "message": "Order not created"
}
```

---

### 5.2 Get My Orders

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/api/user/orders` |
| **Auth Required** | ✅ Yes (Bearer Token) |

Returns all orders placed by the currently authenticated user, newest first.

#### Response — Success `200`

```json
{
  "success": true,
  "data": [
    {
      "id": 101,
      "invoice_no": "SMG-20240101-ABCDEF",
      "subtotal": 1000,
      "discount": 50,
      "coupon_discount": 100,
      "shipping_charge": 60,
      "total": 910,
      "due": 910,
      "status": "pending",
      "payment_status": "pending",
      "payment_method": "COD",
      "created_at": "2024-01-01T00:00:00.000000Z",
      "items": [
        {
          "id": 1,
          "sale_id": 101,
          "product_id": 5,
          "name": "Polo T-Shirt",
          "image": "products/polo.jpg",
          "quantity": 2,
          "unit_price": 500,
          "line_total": 1000,
          "product": { }
        }
      ]
    }
  ]
}
```

---

### 5.3 Get Single Order

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/api/user/orders/{invoice_no}` |
| **Auth Required** | ✅ Yes (Bearer Token) |

#### URL Parameter

| Param | Type | Description |
|---|---|---|
| `invoice_no` | string | The invoice number (e.g. `SMG-20240101-ABCDEF`) |

#### Response — Success `200`

```json
{
  "success": true,
  "data": {
    "id": 101,
    "invoice_no": "SMG-20240101-ABCDEF",
    "subtotal": 1000,
    "discount": 50,
    "coupon_discount": 100,
    "shipping_charge": 60,
    "total": 910,
    "due": 910,
    "status": "pending",
    "payment_status": "pending",
    "payment_method": "COD",
    "name": "John Doe",
    "phone": "01XXXXXXXXX",
    "shipping_address": "123 Main Street, Dhaka",
    "city": "Dhaka",
    "postal_code": "1212",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "items": [
      {
        "id": 1,
        "sale_id": 101,
        "product_id": 5,
        "name": "Polo T-Shirt",
        "image": "products/polo.jpg",
        "quantity": 2,
        "unit_price": 500,
        "line_total": 1000,
        "product": {
          "id": 5,
          "name": "Polo T-Shirt",
          "color": { "id": 1, "name": "Red" },
          "size": { "id": 1, "name": "M" }
        }
      }
    ]
  }
}
```

#### Response — Not Found `404`

```json
{
  "success": false,
  "message": "Order not found"
}
```

---

## 6. Coupons

### 6.1 Validate Coupon

| | |
|---|---|
| **Method** | `POST` |
| **Endpoint** | `/api/get-coupon` |
| **Auth Required** | ❌ No |

Use this endpoint to validate a coupon code before placing an order. If the coupon is valid, use the returned `data.id` as `coupon_id` in the Place Order request.

#### Request Body

```json
{
  "code": "SAVE100"
}
```

| Field | Type | Required | Rules |
|---|---|---|---|
| `code` | string | ✅ Yes | The coupon code to validate |

#### Response — Coupon Found `200`

```json
{
  "status": true,
  "message": "Coupon found",
  "data": {
    "id": 2,
    "code": "SAVE100",
    "discount": 100,
    "discount_type": "fixed",
    "min_purchase": 500,
    "max_discount": 200,
    "is_active": 1,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

> **`discount_type` values:**
> - `fixed` — a fixed amount discount (e.g. ৳100 off)
> - `percentage` — a percentage off the subtotal (e.g. 10% off, capped at `max_discount`)

#### Response — Coupon Not Found `200`

```json
{
  "status": false,
  "message": "Coupon not found"
}
```

---

## 7. Website Settings

### 7.1 Get Settings

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/api/website-settings` |
| **Auth Required** | ❌ No |

Returns global website/store configuration including shipping charge, logo, contact info, etc.

#### Response — Success `200`

```json
{
  "success": true,
  "data": {
    "id": 1,
    "site_name": "Smart Garments",
    "logo": "settings/logo.png",
    "favicon": "settings/favicon.png",
    "phone": "01XXXXXXXXX",
    "email": "info@smartgarments.com",
    "address": "Dhaka, Bangladesh",
    "shipping_charge": 60,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

---

## 8. Offers

### 8.1 List Offers

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/api/offers` |
| **Auth Required** | ❌ No |

#### Query Parameters

| Param | Type | Required | Description |
|---|---|---|---|
| `category` | string | ❌ No | Filter by offer **slug** |

#### Response — Success `200`

```json
{
  "success": true,
  "message": "Offers retrieved successfully",
  "data": [
    {
      "id": 1,
      "title": "Buy 1 Get 1 Free",
      "slug": "buy-1-get-1-free",
      "offer_type": "gift",
      "is_active": 1,
      "banner": "offers/buy1get1.jpg",
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  ]
}
```

> **`offer_type` values:**
> - `gift` — buy product X, get product Y free
> - `bundle` — bundle of products
> - `shipping` — free/reduced shipping on certain products
> - `discount` — percentage or fixed price discount on products

---

### 8.2 Get Single Offer

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/api/offers/{slug}` |
| **Auth Required** | ❌ No |

> ⚠️ **Note:** The `show` method is referenced in the route but not yet implemented in the `OfferController`. This endpoint may return an error until implemented.

#### URL Parameter

| Param | Type | Description |
|---|---|---|
| `slug` | string | Offer slug (e.g. `buy-1-get-1-free`) |

---

### 8.3 Get Offer Products

| | |
|---|---|
| **Method** | `GET` |
| **Endpoint** | `/api/offers/{slug}/products` |
| **Auth Required** | ❌ No |

Returns all products associated with the given offer. The product list differs depending on the `offer_type`.

#### URL Parameter

| Param | Type | Description |
|---|---|---|
| `slug` | string | Offer slug (e.g. `buy-1-get-1-free`) |

#### Response — Success `200`

```json
{
  "success": true,
  "message": "Products retrieved successfully",
  "data": [
    {
      "id": 5,
      "name": "Polo T-Shirt",
      "slug": "polo-t-shirt",
      "price": 500,
      "mrp": 600,
      "thumbnail_image": "products/polo.jpg",
      "is_active": 1,
      "category": {
        "id": 1,
        "name": "T-Shirts",
        "slug": "t-shirts"
      }
    }
  ]
}
```

---

## General Error Responses

| HTTP Status | Meaning |
|---|---|
| `200` | OK — Request succeeded |
| `401` | Unauthorized — Invalid credentials or missing/expired token |
| `404` | Not Found — Resource does not exist |
| `422` | Unprocessable Entity — Validation failed |
| `500` | Server Error — Something went wrong on the server |

### Validation Error Format (422)

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": [
      "The field_name is required."
    ]
  }
}
```

---

## Authentication Flow (Quick Guide for Frontend)

```
1. Register  →  POST /api/register  →  get access_token
2. Login     →  POST /api/login     →  get access_token
3. Use token →  Add header: Authorization: Bearer <access_token>
4. Logout    →  POST /api/logout    →  token is revoked
```

---

*Generated from source: `core/routes/api.php` & `App\Http\Controllers\api\*`*
