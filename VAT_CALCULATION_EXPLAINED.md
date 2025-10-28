# VAT Calculation - VAT Inclusive Pricing

## Overview

The supermarket POS system uses **VAT INCLUSIVE** pricing. This means all displayed prices already include the 16% VAT.

---

## ✅ Correct VAT Calculation (VAT Inclusive)

### Formula

When prices include VAT:

```
Price (Inc. VAT) = Net Amount × 1.16

Therefore:
Net Amount = Price / 1.16
VAT Amount = Price - Net Amount
VAT Amount = Price - (Price / 1.16)
VAT Amount = Price × (16/116)
VAT Amount = Price × 0.13793103...
```

### Example 1: Single Item

**Product:** Coca Cola  
**Price (Inc. VAT):** KES 116.00  
**Quantity:** 1

**Calculation:**
```
Total = 116.00 × 1 = KES 116.00
VAT = 116.00 × (16/116) = KES 16.00
Net Amount = 116.00 - 16.00 = KES 100.00

Verification:
Net Amount × 1.16 = 100.00 × 1.16 = 116.00 ✓
```

### Example 2: Multiple Items

**Product:** Bread  
**Price (Inc. VAT):** KES 58.00  
**Quantity:** 5

**Calculation:**
```
Total = 58.00 × 5 = KES 290.00
VAT = 290.00 × (16/116) = KES 40.00
Net Amount = 290.00 - 40.00 = KES 250.00

Verification:
Net Amount × 1.16 = 250.00 × 1.16 = 290.00 ✓
```

### Example 3: With Discount

**Product:** Sugar  
**Price (Inc. VAT):** KES 232.00  
**Quantity:** 2  
**Discount:** 10%

**Calculation:**
```
Subtotal = 232.00 × 2 = KES 464.00
Discount Amount = 464.00 × 10% = KES 46.40
Total After Discount = 464.00 - 46.40 = KES 417.60

VAT = 417.60 × (16/116) = KES 57.60
Net Amount = 417.60 - 57.60 = KES 360.00

Grand Total = KES 417.60 (already includes VAT)

Verification:
Net Amount × 1.16 = 360.00 × 1.16 = 417.60 ✓
```

---

## ❌ Wrong Calculation (VAT Exclusive)

**DO NOT use this approach:**

```
Total = Price × Quantity
VAT = Total × 0.16  ← WRONG for VAT inclusive prices
Grand Total = Total + VAT  ← This double-counts VAT!
```

**Why it's wrong:**
- The price already includes VAT
- Adding VAT on top inflates the total by 16%
- Customer would be overcharged

**Example of Wrong Calculation:**
```
Price: KES 116.00 (already includes VAT)
Total: 116.00
Wrong VAT: 116.00 × 0.16 = 18.56
Wrong Grand Total: 116.00 + 18.56 = 134.56  ← TOO HIGH!

Customer should pay: KES 116.00
Wrong system charges: KES 134.56
Overcharge: KES 18.56 (16%)
```

---

## 📊 Cart Summary Display

### Frontend Display

```
┌────────────────────────────────────┐
│ Cart Summary                       │
├────────────────────────────────────┤
│ Subtotal (Inc. VAT)    KES 464.00 │
│ Discount               -KES 46.40 │
│ VAT (16% Inclusive)     KES 57.60 │ ← Informational only
├────────────────────────────────────┤
│ Total Payable          KES 417.60 │
└────────────────────────────────────┘
```

**Key Points:**
- Subtotal shows "(Inc. VAT)" to clarify prices include VAT
- VAT line is informational - shows extracted VAT amount
- Total Payable = Subtotal - Discount (VAT already included)

---

## 💻 Code Implementation

### JavaScript (Frontend)

```javascript
// Calculate totals with VAT inclusive pricing
function updateCartSummary() {
    let subtotal = 0;
    let totalDiscount = 0;
    let totalVat = 0;

    cart.forEach(item => {
        const itemTotal = item.price * item.quantity; // Price is VAT inclusive
        const discountAmount = (itemTotal * item.discount) / 100;
        const totalAfterDiscount = itemTotal - discountAmount;
        
        // Extract VAT from VAT-inclusive price (16%)
        const vatAmount = totalAfterDiscount * (16 / 116);
        
        subtotal += itemTotal;
        totalDiscount += discountAmount;
        totalVat += vatAmount;
    });

    const grandTotal = subtotal - totalDiscount; // VAT already included
    
    // Display
    $('#subtotal').text('KES ' + subtotal.toFixed(2));
    $('#vat').text('KES ' + totalVat.toFixed(2));
    $('#grand-total').text('KES ' + grandTotal.toFixed(2));
}
```

### PHP (Backend)

```php
// Calculate totals (Prices are VAT INCLUSIVE at 16%)
$subtotal = 0;
$totalDiscount = 0;
$totalVat = 0;

foreach ($cart as $item) {
    $itemTotal = $item['price'] * $item['quantity']; // VAT inclusive
    $discountAmount = ($itemTotal * $item['discount']) / 100;
    $totalAfterDiscount = $itemTotal - $discountAmount;
    
    // Extract VAT from VAT-inclusive price (16%)
    $vatAmount = $totalAfterDiscount * (16 / 116);
    
    $subtotal += $itemTotal;
    $totalDiscount += $discountAmount;
    $totalVat += $vatAmount;
}

$grandTotal = $subtotal - $totalDiscount; // VAT already included
```

---

## 🧮 Quick Reference Table

| Price (Inc. VAT) | VAT Amount | Net Amount |
|------------------|------------|------------|
| KES 116.00 | KES 16.00 | KES 100.00 |
| KES 58.00 | KES 8.00 | KES 50.00 |
| KES 232.00 | KES 32.00 | KES 200.00 |
| KES 464.00 | KES 64.00 | KES 400.00 |
| KES 1,160.00 | KES 160.00 | KES 1,000.00 |

**Formula:** VAT = Price × (16/116) ≈ Price × 0.13793

---

## 📝 Database Storage

### wa_pos_cash_sales_items

```sql
-- Example record for KES 116.00 item (Inc. VAT)
selling_price: 116.00      -- VAT inclusive price
qty: 1
vat_percentage: 16
vat_amount: 16.00          -- Extracted VAT (116 × 16/116)
discount_percent: 0
discount_amount: 0.00
total: 116.00              -- Total includes VAT
```

### With Discount Example

```sql
-- Item: KES 232.00, Qty: 2, Discount: 10%
selling_price: 232.00      -- VAT inclusive
qty: 2
subtotal: 464.00           -- 232 × 2
discount_percent: 10
discount_amount: 46.40     -- 464 × 10%
total_after_discount: 417.60
vat_percentage: 16
vat_amount: 57.60          -- 417.60 × (16/116)
total: 417.60              -- Final total (VAT inclusive)
```

---

## 🔍 Verification Method

To verify VAT calculation is correct:

```javascript
// Test function
function verifyVAT(price, quantity, discount = 0) {
    const itemTotal = price * quantity;
    const discountAmount = (itemTotal * discount) / 100;
    const totalAfterDiscount = itemTotal - discountAmount;
    const vat = totalAfterDiscount * (16 / 116);
    const netAmount = totalAfterDiscount - vat;
    
    // Verify: Net Amount × 1.16 should equal Total After Discount
    const verification = netAmount * 1.16;
    const isCorrect = Math.abs(verification - totalAfterDiscount) < 0.01;
    
    console.log('Price:', price);
    console.log('Quantity:', quantity);
    console.log('Discount:', discount + '%');
    console.log('Total After Discount:', totalAfterDiscount.toFixed(2));
    console.log('VAT Amount:', vat.toFixed(2));
    console.log('Net Amount:', netAmount.toFixed(2));
    console.log('Verification:', verification.toFixed(2));
    console.log('Correct:', isCorrect ? '✓' : '✗');
    
    return isCorrect;
}

// Test cases
verifyVAT(116, 1);        // KES 116.00 × 1
verifyVAT(232, 2, 10);    // KES 232.00 × 2 with 10% discount
verifyVAT(58, 5);         // KES 58.00 × 5
```

---

## 📊 Reporting Implications

### Sales Reports

When generating sales reports, remember:

```sql
-- Total Sales (VAT Inclusive)
SELECT SUM(total) as total_sales,
       SUM(total * (16/116)) as total_vat,
       SUM(total * (100/116)) as total_net
FROM wa_pos_cash_sales_items;
```

### VAT Returns

For tax purposes:

```sql
-- VAT collected for period
SELECT 
    DATE(date) as sale_date,
    SUM(total) as gross_sales,
    SUM(total * (16/116)) as vat_collected,
    SUM(total * (100/116)) as net_sales
FROM wa_pos_cash_sales
JOIN wa_pos_cash_sales_items ON wa_pos_cash_sales.id = wa_pos_cash_sales_id
WHERE date BETWEEN '2025-10-01' AND '2025-10-31'
  AND status = 'Completed'
GROUP BY DATE(date);
```

---

## ✅ Summary

**Key Takeaways:**

1. ✅ All prices in the system are VAT INCLUSIVE (16%)
2. ✅ VAT is extracted using formula: `VAT = Total × (16/116)`
3. ✅ Grand Total = Subtotal - Discount (VAT already included)
4. ✅ Display shows "Inc. VAT" or "Inclusive" for clarity
5. ✅ Never add VAT on top of VAT-inclusive prices
6. ✅ Verification: `Net Amount × 1.16 = Total (Inc. VAT)`

---

**Updated:** October 27, 2025  
**System:** Supermarket POS v3.0  
**VAT Rate:** 16% (Inclusive)

