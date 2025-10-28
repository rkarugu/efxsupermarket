# Supermarket POS Ecosystem - Complete Implementation Guide

## 🚀 Overview

This comprehensive guide covers the complete supermarket POS ecosystem including stock movements, promotions, discounts, cash drops, and all integrated features.

**Version:** 4.0  
**Last Updated:** October 27, 2025  
**Author:** EFX Development Team

---

## 📋 Table of Contents

1. [Features Overview](#features-overview)
2. [Architecture](#architecture)
3. [Stock Movement System](#stock-movement-system)
4. [Promotions & Discounts](#promotions--discounts)
5. [Bin Location Validation](#bin-location-validation)
6. [Cash Drop System](#cash-drop-system)
7. [API Endpoints](#api-endpoints)
8. [Frontend Integration](#frontend-integration)
9. [Database Schema](#database-schema)
10. [Usage Guide](#usage-guide)
11. [Troubleshooting](#troubleshooting)

---

## 🎯 Features Overview

### ✅ Implemented Features

1. **Real-time Inventory Integration**
   - Live stock levels from `wa_stock_moves` table
   - Automatic stock deduction on sale completion
   - Stock validation before sale
   - Product images display ✨ NEW

2. **Visual Product Display** 🆕
   - Product images in POS grid
   - Automatic fallback to icons if image missing
   - Optimized image scaling and display
   - Error handling for missing images

3. **Comprehensive Promotion System**
   - Price Discount Promotions ("Was X, Now Y")
   - Buy X Get Y Free Promotions
   - Automatic promotion detection and application
   - Visual promotion indicators on products
   - Promotion expiry date handling

4. **Advanced Discount Management**
   - Item-level discount percentage
   - Manual discount override by cashier
   - Automatic promotion-based discounts
   - Discount tracking in stock moves

5. **Bin Location Validation** ⏸️ (Disabled - To be enabled later)
   - Items must have bin location before selling
   - Store-specific bin location checking
   - Prevents selling unallocated items
   - Improves inventory accuracy

6. **Cash Drop Functionality**
   - Real-time cashier balance tracking
   - Automatic drop limit alerts
   - Cash drop transaction recording
   - Drop history and reporting

7. **Completed Sales Management** 🆕
   - View all completed sales with filters
   - Receipt reprinting functionality
   - Date range filtering
   - Returns preparation (coming soon)

5. **Stock Movement Tracking**
   - Every sale creates stock movement records
   - Links to POS sale transactions
   - Tracks price, discount, and cost
   - Maintains stock audit trail

6. **Multi-Payment Support**
   - Cash payments with change calculation
   - M-Pesa STK Push integration
   - Card payments with reference
   - Split payments across multiple methods

7. **Modern UI/UX**
   - Split-screen layout (products + cart)
   - Touch-optimized interface
   - Keyboard shortcuts (F1-F4)
   - Real-time calculations
   - Visual feedback for promotions

---

## 🏗️ Architecture

### System Components

```
┌─────────────────────────────────────────────────────────────┐
│                     Supermarket POS                          │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Frontend (supermarket-pos.js)                              │
│  ├── Product Display & Search                               │
│  ├── Cart Management                                        │
│  ├── Payment Processing                                     │
│  └── Cash Drop Interface                                    │
│                                                              │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Backend (PosCashSalesController)                           │
│  ├── getSupermarketProducts() - Load products + promotions  │
│  ├── storeSupermarketSale() - Complete sale + stock moves   │
│  ├── storeCashDrop() - Record cash drops                    │
│  └── getCashierInfo() - Balance & drop limits               │
│                                                              │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Database Tables                                            │
│  ├── wa_inventory_items (Products)                          │
│  ├── item_promotions (Promotions)                           │
│  ├── wa_stock_moves (Stock Movements)                       │
│  ├── wa_pos_cash_sales (Sales)                              │
│  ├── wa_pos_cash_sales_items (Sale Line Items)              │
│  ├── wa_pos_cash_sales_payments (Payment Records)           │
│  └── cash_drop_transactions (Cash Drops)                    │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### Data Flow

```
Product Selection
    ↓
Cart Management
    ↓
Promotion Detection → Automatic Price/Discount Application
    ↓
Payment Entry
    ↓
Sale Validation
    ↓
┌───────────────────────────────────────┐
│  Transaction Processing (DB Begin)     │
│  ├── Create wa_pos_cash_sales         │
│  ├── Create wa_pos_cash_sales_items   │
│  ├── Create wa_stock_moves (-)        │
│  └── Create wa_pos_cash_sales_payments│
└───────────────────────────────────────┘
    ↓
Stock Update & Cash Balance Update
    ↓
Receipt Generation
```

---

## 📦 Stock Movement System

### How It Works

Every sale automatically creates negative stock movement entries to track inventory deductions.

### Stock Move Record Structure

```php
WaStockMove::create([
    'user_id' => $cashier_id,
    'wa_pos_cash_sales_id' => $sale_id,
    'restaurant_id' => $branch_id,
    'wa_location_and_store_id' => $store_id,
    'wa_inventory_item_id' => $product_id,
    'stock_id_code' => $product_code,
    'refrence' => 'POS Sale: CS20251027001',
    'qauntity' => -5,  // Negative for deduction
    'price' => 120.00,
    'discount_percent' => 10,
    'standard_cost' => 80.00,
    'selling_price' => 120.00,
    'document_no' => 'CS20251027001',
    'total_cost' => 600.00,
]);
```

### Stock Calculation

```sql
-- Current stock for an item at a specific location
SELECT SUM(qauntity) as current_stock
FROM wa_stock_moves
WHERE wa_inventory_item_id = :item_id
  AND wa_location_and_store_id = :location_id
```

### Features

- ✅ Automatic creation on every sale
- ✅ Links to original POS sale
- ✅ Tracks user who made the sale
- ✅ Records discount and pricing details
- ✅ Maintains cost information for margin analysis
- ✅ Supports audit trail and reporting

---

## 🎁 Promotions & Discounts

### Promotion Types

#### 1. Price Discount Promotion

**Database:** `PromotionMatrix::PD` = "Price Discount. X was N now N-1"

```php
// Example Promotion Record
ItemPromotion {
    inventory_item_id: 123,
    promotion_type_id: 1, // Price Discount
    current_price: 150.00,
    promotion_price: 120.00,
    discount_percentage: 20,
    from_date: '2024-10-01',
    to_date: '2024-10-31',
    status: 'active'
}
```

**How it works:**
- Product displays with strikethrough original price
- Promotion price automatically applied to cart
- Red "SALE" badge on product card
- Tracks original and promotion prices

#### 2. Buy X Get Y Free Promotion

**Database:** `PromotionMatrix::BSGY` = "Buy X Get Y Free"

```php
// Example Promotion Record
ItemPromotion {
    inventory_item_id: 456,
    promotion_type_id: 2, // Buy X Get Y
    sale_quantity: 3,  // Buy 3
    promotion_quantity: 1,  // Get 1 free
    promotion_item_id: 456,  // Same or different item
    from_date: '2024-10-01',
    to_date: '2024-10-31',
    status: 'active'
}
```

**How it works:**
- Customer buys required quantity
- Free item automatically added to cart
- Orange "PROMO" badge on product card
- Can give different item as free item

### Discount Hierarchy

1. **Promotion Price** (Highest Priority)
   - Applied automatically from `item_promotions` table
   - Cannot be removed by cashier
   - Shown as new price

2. **Manual Discount** (Cashier Override)
   - Applied as percentage on top of base price
   - Entered in cart item discount field
   - Requires permission in some setups

### Promotion Detection

```javascript
// Frontend automatically detects promotions
products.forEach(product => {
    if (product.has_promotion) {
        if (product.promotion.type === 'price_discount') {
            // Show strikethrough original price
            // Display promotion price
            // Add SALE badge
        } else if (product.promotion.type === 'buy_x_get_y') {
            // Calculate free items
            // Add PROMO badge
        }
    }
});
```

### Backend Promotion Logic

```php
// In PosCashSalesController::getSupermarketProducts()
->with(['promotions' => function($query) {
    $query->where('status', 'active')
          ->where('from_date', '<=', now())
          ->where(function($q) {
              $q->where('to_date', '>=', now())
                ->orWhereNull('to_date');
          });
}])
```

---

## 📍 Bin Location Validation

### ⏸️ Status: Implemented but Currently Disabled

This feature is ready but **commented out** to be enabled later in the project.

### Purpose

Ensures all items sold have assigned bin locations for proper inventory tracking and warehouse efficiency.

### How It Works

**1. Product Loading Filter**

Only items with bin locations assigned to the current store appear in POS:

```php
// In PosCashSalesController::getSupermarketProducts()
->with([
    'bin_locations' => function($query) use ($storeId) {
        $query->where('location_id', $storeId);
    },
])
->filter(function($item) {
    // Only include items with bin location assigned
    return $item->bin_locations && $item->bin_locations->count() > 0;
})
```

**2. Sale Processing Validation**

Double-check during sale to prevent any unallocated items:

```php
// In storeSupermarketSale()
foreach ($validated['cart'] as $cartItem) {
    $product = WaInventoryItem::with(['bin_locations'])->find($cartItem['id']);
    
    $hasBinLocation = $product->bin_locations()
        ->where('location_id', $storeId)
        ->exists();
    
    if (!$hasBinLocation) {
        throw new \Exception(
            "Item '{$product->title}' cannot be sold - no bin location assigned."
        );
    }
}
```

### Database Structure

**Table:** `wa_inventory_location_uom`

```sql
CREATE TABLE wa_inventory_location_uom (
    id INT PRIMARY KEY,
    inventory_id INT,  -- Links to wa_inventory_items
    location_id INT,   -- Links to wa_location_and_store (Store/Branch)
    uom_id INT,        -- Unit of Measure for this bin
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Benefits

1. ✅ **Prevents Invalid Sales** - Can't sell items not physically allocated
2. ✅ **Improves Picking** - Warehouse knows exact bin location
3. ✅ **Better Stock Control** - Location-based inventory management
4. ✅ **Data Integrity** - Ensures all sold items have proper tracking
5. ✅ **Store-Specific** - Items only sellable in stores where they're located

### User Experience

**Items WITH bin location:**
- ✅ Appear in POS product grid
- ✅ Can be added to cart
- ✅ Sale completes successfully

**Items WITHOUT bin location:**
- ❌ Hidden from POS
- ❌ Cannot be selected
- ❌ Error if somehow added to cart

### Error Messages

```javascript
// If validation fails:
"Error completing sale: Item 'New Product' cannot be sold - 
no bin location assigned."
```

**Action:** Contact inventory manager to assign bin location.

### Setting Up Bin Locations

**For new items to be sellable:**

1. Go to Inventory Items
2. Select the item
3. Assign bin location:
   - Store: Select store/branch
   - Bin/Location: Assign warehouse bin
   - UOM: Select unit of measure
4. Save
5. Item now appears in POS for that store

---

## 💰 Cash Drop System

### Purpose

Prevents cashiers from holding excessive cash by enforcing drop limits.

### Features

- **Real-time Balance Tracking**: System knows exact cash at hand
- **Drop Limit Enforcement**: Automatic alerts when limit reached
- **Visual Alerts**: Warning popup when approaching limit
- **Transaction Recording**: Full audit trail of all drops
- **Keyboard Shortcut**: F4 to open cash drop modal

### Drop Limit Logic

```php
// User model (assumed to have these attributes)
$dropLimit = $user->drop_limit; // e.g., 100,000 KES
$cashAtHand = $user->cashAtHand(); // Calculates from sales - drops

// Alert when approaching limit
if ($cashAtHand >= $dropLimit) {
    // Show mandatory drop alert
    // May block further sales depending on config
}
```

### Cash Drop Flow

```
1. Cashier makes sales throughout shift
2. System tracks total cash collected
3. When limit reached → Alert shown
4. Cashier presses F4 or clicks "Make Cash Drop"
5. Enters amount to drop (validated against cash at hand)
6. Optional notes
7. Submit → Records in cash_drop_transactions
8. Cash at hand reduced
9. Cashier can continue selling
```

### Cash Drop Record

```php
CashDropTransaction::create([
    'amount' => 50000.00,
    'cashier_balance' => 45000.00, // Remaining after drop
    'user_id' => $user->id,
    'cashier_id' => $user->id,
    'notes' => 'Mid-shift drop',
]);
```

### Reporting

Cash drops can be tracked for:
- Daily cashier performance
- Shift reconciliation
- Banking records
- Audit compliance

---

## 🔌 API Endpoints

### 1. Get Products with Promotions

**Endpoint:** `GET /admin/pos-cash-sales/supermarket/products`

**Response:**
```json
[
  {
    "id": 123,
    "name": "Coca Cola 500ml",
    "barcode": "5449000000996",
    "price": 60.00,
    "stock": 150,
    "category": "beverages",
    "vat": 16.0,
    "has_promotion": true,
    "promotion": {
      "type": "price_discount",
      "original_price": 80.00,
      "promotion_price": 60.00,
      "discount_amount": 20.00,
      "discount_percentage": 25.0
    }
  }
]
```

### 2. Store Sale

**Endpoint:** `POST /admin/pos-cash-sales/supermarket/store`

**Headers:** `X-CSRF-TOKEN: {token}`

**Request:**
```json
{
  "cart": [
    {
      "id": 123,
      "quantity": 5,
      "price": 60.00,
      "discount": 10
    }
  ],
  "customer": {
    "name": "John Doe",
    "phone": "0712345678"
  },
  "payments": [
    {
      "method": "Cash",
      "amount": 300.00,
      "reference": ""
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Sale completed successfully",
  "sale_id": 456,
  "sales_no": "CS20251027001",
  "total": 285.60,
  "change": 14.40
}
```

### 3. Record Cash Drop

**Endpoint:** `POST /admin/pos-cash-sales/supermarket/cash-drop`

**Headers:** `X-CSRF-TOKEN: {token}`

**Request:**
```json
{
  "amount": 50000.00,
  "notes": "Mid-shift drop"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Cash drop recorded successfully",
  "drop_id": 789,
  "new_balance": 45000.00
}
```

### 4. Get Cashier Info

**Endpoint:** `GET /admin/pos-cash-sales/supermarket/cashier-info`

**Response:**
```json
{
  "success": true,
  "cash_at_hand": 95000.00,
  "drop_limit": 100000.00,
  "total_drops_today": 150000.00,
  "unbanked_drops": 2,
  "needs_drop": false,
  "drop_percentage": 95.0
}
```

---

## 🖥️ Frontend Integration

### Initialization

```javascript
$(document).ready(function() {
    loadCashierInfo();    // Load drop limits and balance
    loadProducts();       // Load inventory with promotions
    setupEventListeners(); // Keyboard shortcuts, etc.
});
```

### Product Display

```javascript
// Products with promotions show special badges
const promotionBadge = product.has_promotion ? `
    <div style="background: #ff5722; color: #fff;">
        ${product.promotion.type === 'price_discount' ? 'SALE' : 'PROMO'}
    </div>
` : '';
```

### Cart Management

```javascript
// Automatically apply promotion prices
if (product.has_promotion && product.promotion.type === 'price_discount') {
    price = product.promotion.promotion_price;
}

cart.push({
    id: product.id,
    name: product.name,
    price: price, // Price is VAT INCLUSIVE (16%)
    quantity: 1,
    has_promotion: true,
    promotion: product.promotion
});

// VAT Calculation (Prices are VAT Inclusive)
// VAT Amount = Total × (16/116)
// Net Amount = Total - VAT Amount = Total × (100/116)
```

### Keyboard Shortcuts

- **F1** - Focus search box
- **F2** - Select customer
- **F3** - Proceed to payment (if cart has items)
- **F4** - Open cash drop modal

---

## 🗄️ Database Schema

### Key Tables

#### `wa_inventory_items`
```sql
id, title, stock_id_code, selling_price, 
wa_inventory_category_id, status, standard_cost
```

#### `item_promotions`
```sql
id, inventory_item_id, promotion_type_id, 
sale_quantity, promotion_quantity, promotion_item_id,
current_price, promotion_price, discount_percentage,
from_date, to_date, status
```

#### `wa_stock_moves`
```sql
id, user_id, wa_pos_cash_sales_id, restaurant_id,
wa_location_and_store_id, wa_inventory_item_id,
stock_id_code, refrence, qauntity, price,
discount_percent, standard_cost, selling_price,
document_no, total_cost
```

#### `wa_pos_cash_sales`
```sql
id, sales_no, date, time, user_id, attending_cashier,
customer, customer_phone_number, cash, change,
status, branch_id, is_tablet_sale
```

#### `wa_pos_cash_sales_items`
```sql
id, wa_pos_cash_sales_id, wa_inventory_item_id,
qty, selling_price, vat_percentage, vat_amount,
discount_percent, discount_amount, total,
standard_cost, is_dispatched, dispatched_by
```

#### `wa_pos_cash_sales_payments`
```sql
id, wa_pos_cash_sales_id, payment_method_id,
amount, payment_reference, cashier_id, branch_id,
remarks, transaction_type, reconciled, posted
```

#### `cash_drop_transactions`
```sql
id, amount, cashier_balance, user_id, cashier_id,
notes, bank_receipt_number, created_at
```

### Relationships

```
wa_inventory_items (1) ←→ (N) item_promotions
wa_inventory_items (1) ←→ (N) wa_stock_moves
wa_pos_cash_sales (1) ←→ (N) wa_pos_cash_sales_items
wa_pos_cash_sales (1) ←→ (N) wa_pos_cash_sales_payments
wa_pos_cash_sales (1) ←→ (N) wa_stock_moves
users (1) ←→ (N) cash_drop_transactions
```

---

## 📖 Usage Guide

### For Cashiers

#### Starting a Shift
1. Log in to the system
2. Navigate to POS → Supermarket POS
3. System loads products and checks your cash balance
4. If drop needed, handle it before selling

#### Making a Sale
1. **Search Product**: Type name or scan barcode
2. **Add to Cart**: Click product card
3. **Adjust Quantity**: Use +/- buttons or type directly
4. **Apply Discount**: Enter percentage in discount field (if authorized)
5. **Check Promotions**: Look for SALE/PROMO badges
6. **Add Customer** (optional): Click "Select Customer" or press F2
7. **Proceed to Payment**: Click checkout or press F3
8. **Enter Payment**: Fill in cash/M-Pesa/card amounts
9. **Complete Sale**: Click "Complete Sale"
10. **Give Change**: System calculates change automatically

#### Making a Cash Drop
1. Press **F4** or click cash drop alert
2. System shows current cash at hand
3. Enter amount to drop (must be ≤ cash at hand)
4. Add optional notes
5. Submit drop
6. Continue selling

#### End of Shift
1. Navigate to pending sales or reports
2. Ensure all drops are banked
3. Reconcile cash drawer
4. Log out

### For Managers

#### Setting Up Promotions
1. Navigate to Inventory → Promotions
2. Create new promotion
3. Select type (Price Discount or Buy X Get Y)
4. Set dates and status
5. Promotion automatically appears in POS

#### Monitoring Cash Drops
1. View drop reports by cashier/date
2. Check unbanked drops
3. Verify cash balances
4. Generate banking reports

#### Viewing Stock Movements
1. Navigate to Reports → Stock Movements
2. Filter by POS sales
3. See all transactions with full details
4. Audit trail for stock changes

---

## 🐛 Troubleshooting

### Products Not Loading

**Symptom:** Empty product grid

**Causes:**
1. No products with stock > 0
2. User not assigned to a store location
3. Database connection issue

**Solutions:**
```php
// Check user has store assigned
$user->wa_location_and_store_id; // Must not be null

// Check stock calculation
SELECT SUM(qauntity) FROM wa_stock_moves 
WHERE wa_inventory_item_id = X 
  AND wa_location_and_store_id = Y;
```

### Promotions Not Showing

**Symptom:** Product has promotion but no badge/price change

**Causes:**
1. Promotion status not 'active'
2. Dates outside valid range
3. Promotion type relationship missing

**Solutions:**
```sql
-- Check promotion
SELECT * FROM item_promotions
WHERE inventory_item_id = X
  AND status = 'active'
  AND from_date <= NOW()
  AND (to_date >= NOW() OR to_date IS NULL);

-- Check promotion type
SELECT * FROM promotion_types WHERE id = Y;
```

### Sale Not Completing

**Symptom:** Error on "Complete Sale" click

**Causes:**
1. CSRF token missing/invalid
2. Insufficient stock
3. Database constraint violation

**Solutions:**
```html
<!-- Ensure CSRF token in page head -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Check browser console for error -->
console.log('CSRF Token:', csrfToken);

<!-- Check server logs -->
tail -f storage/logs/laravel.log
```

### Cash Drop Not Recording

**Symptom:** Cash drop modal submits but doesn't save

**Causes:**
1. Amount exceeds cash at hand
2. User model missing cashAtHand() method
3. Database permissions

**Solutions:**
```php
// Verify user method exists
if (!method_exists($user, 'cashAtHand')) {
    // Add method to User model
}

// Check validation
$amount <= $user->cashAtHand();
```

### Stock Not Deducting

**Symptom:** Sale completes but stock unchanged

**Causes:**
1. Transaction rollback
2. Stock move creation failed
3. Location ID mismatch

**Solutions:**
```php
// Check logs for rollback
DB::commit(); // Should succeed

// Verify stock move created
SELECT * FROM wa_stock_moves
WHERE wa_pos_cash_sales_id = X;

// Check location consistency
$user->wa_location_and_store_id == $stockMove->wa_location_and_store_id
```

---

## 🔐 Security Considerations

### Permission Checks

```php
// Controller checks permissions
if (isset($permission[$pmodule . '___add']) || $permission == 'superadmin') {
    // Allow access
}
```

### CSRF Protection

All POST requests require CSRF token:
```javascript
headers: {
    'X-CSRF-TOKEN': csrfToken
}
```

### Input Validation

```php
$validated = $request->validate([
    'cart' => 'required|array|min:1',
    'cart.*.id' => 'required|integer|exists:wa_inventory_items,id',
    'cart.*.quantity' => 'required|numeric|min:0.01',
    // ... more rules
]);
```

### Transaction Safety

All sale operations wrapped in database transaction:
```php
DB::beginTransaction();
try {
    // Create sale, items, stock moves, payments
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    Log::error('Sale Error: ' . $e->getMessage());
}
```

---

## 📊 Reporting

### Available Reports

1. **Sales by Cashier**
   - Total sales per cashier
   - Discount amounts
   - Payment methods used

2. **Stock Movement Report**
   - All POS deductions
   - Links to sale numbers
   - Quantity and value tracking

3. **Promotion Performance**
   - Items sold on promotion
   - Promotion revenue impact
   - Popular promotions

4. **Cash Drop History**
   - Drop amounts by cashier
   - Drop frequency
   - Unbanked drops

### Sample Queries

```sql
-- Total sales today
SELECT SUM(total) FROM wa_pos_cash_sales
WHERE DATE(created_at) = CURDATE()
  AND status = 'Completed';

-- Stock deductions today
SELECT i.title, SUM(sm.qauntity) as qty_sold
FROM wa_stock_moves sm
JOIN wa_inventory_items i ON sm.wa_inventory_item_id = i.id
WHERE DATE(sm.created_at) = CURDATE()
  AND sm.qauntity < 0
GROUP BY i.id;

-- Promotion sales
SELECT p.*, COUNT(si.id) as times_sold
FROM item_promotions p
JOIN wa_pos_cash_sales_items si ON p.inventory_item_id = si.wa_inventory_item_id
WHERE p.status = 'active'
GROUP BY p.id;
```

---

## 🚀 Performance Optimization

### Database Indexes

Ensure these indexes exist:
```sql
-- Stock moves by item and location
CREATE INDEX idx_stock_moves_item_location 
ON wa_stock_moves(wa_inventory_item_id, wa_location_and_store_id);

-- Active promotions
CREATE INDEX idx_promotions_active 
ON item_promotions(status, from_date, to_date);

-- Sales by date
CREATE INDEX idx_sales_date 
ON wa_pos_cash_sales(date, status);
```

### Caching Strategy

```php
// Cache frequently accessed data
Cache::remember('pos_products_' . $storeId, 300, function() {
    return WaInventoryItem::with('promotions')->get();
});
```

### Query Optimization

```php
// Eager load relationships
->with(['category', 'promotions', 'stockMoves'])

// Use select to limit columns
->select(['id', 'title', 'selling_price', 'stock_id_code'])

// Add havingRaw for stock filtering
->havingRaw('stock > 0')
```

---

## 📞 Support

For issues or questions:
- **Technical Support:** support@efxsupermarket.com
- **Documentation:** https://docs.efxsupermarket.com
- **Bug Reports:** https://github.com/efx/supermarket-pos/issues

---

## 📝 Changelog

### Version 3.0 (October 27, 2025)
- ✅ Full promotion system integration
- ✅ Automatic stock movement tracking
- ✅ Cash drop functionality
- ✅ Enhanced UI with promotion indicators
- ✅ Multi-payment support
- ✅ Comprehensive API endpoints

### Version 2.0 (Previous)
- Basic inventory integration
- Simple product loading
- Mock data support

### Version 1.0 (Initial)
- Static UI prototype
- Mock products only

---

## 📄 License

Copyright © 2025 EFX Supermarket. All rights reserved.

---

**End of Documentation**

