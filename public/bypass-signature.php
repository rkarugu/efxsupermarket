<?php
/**
 * Direct Receipt Access with Signature Bypass
 * This standalone script bypasses any signature validation
 */

// Include the autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Set up error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get the sale ID from the request
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    die("Error: No sale ID provided. Please specify an ID parameter.");
}

try {
    // Create a new DomPDF instance with options
    $options = new \Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');
    
    $dompdf = new \Dompdf\Dompdf($options);
    
    // Connect to the database
    $db = new PDO('mysql:host=localhost;dbname=efficentrix;charset=utf8mb4', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch the sale data
    $stmt = $db->prepare("
        SELECT * FROM wa_pos_cash_sales 
        WHERE id = :id OR sales_no = :sales_no
    ");
    $stmt->execute([
        ':id' => $id,
        ':sales_no' => $id
    ]);
    
    $sale = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$sale) {
        die("Error: Sale not found with ID: $id");
    }
    
    // Fetch customer data
    $customer = null;
    if (!empty($sale['customer_id'])) {
        $stmt = $db->prepare("SELECT * FROM wa_customers WHERE id = :id");
        $stmt->execute([':id' => $sale['customer_id']]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Fetch sale items
    $stmt = $db->prepare("
        SELECT i.*, p.title as product_title, p.code as product_code 
        FROM wa_pos_cash_sales_items i
        LEFT JOIN wa_inventory_items p ON i.inventory_item_id = p.id
        WHERE i.pos_cash_sale_id = :sale_id
    ");
    $stmt->execute([':sale_id' => $sale['id']]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch payment data
    $stmt = $db->prepare("
        SELECT p.*, m.title as method_title
        FROM wa_pos_cash_sales_payments p
        LEFT JOIN payment_methods m ON p.payment_method_id = m.id
        WHERE p.pos_cash_sale_id = :sale_id
    ");
    $stmt->execute([':sale_id' => $sale['id']]);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch user data
    $user = null;
    if (!empty($sale['user_id'])) {
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $sale['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Generate HTML for the receipt
    $html = '<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #' . $sale['sales_no'] . '</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .invoice-details {
            margin-bottom: 20px;
        }
        .customer-details, .invoice-info {
            width: 48%;
            float: left;
        }
        .invoice-info {
            float: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
        }
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>RECEIPT</h2>
            <h3>Invoice #' . $sale['sales_no'] . '</h3>
        </div>

        <div class="invoice-details clearfix">
            <div class="customer-details">
                <h4>Customer Details</h4>
                <p><strong>Name:</strong> ' . $sale['customer'] . '</p>
                <p><strong>Phone:</strong> ' . $sale['customer_phone_number'] . '</p>';
                
    if (!empty($sale['customer_pin'])) {
        $html .= '<p><strong>PIN:</strong> ' . $sale['customer_pin'] . '</p>';
    }
    
    $html .= '</div>
            <div class="invoice-info">
                <h4>Invoice Details</h4>
                <p><strong>Date:</strong> ' . $sale['date'] . '</p>
                <p><strong>Time:</strong> ' . $sale['time'] . '</p>
                <p><strong>Cashier:</strong> ' . ($user ? $user['name'] : 'Unknown') . '</p>
            </div>
        </div>

        <div class="clearfix"></div>

        <h4>Items</h4>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>';
    
    $subtotal = 0;
    $vat_total = 0;
    
    foreach ($items as $item) {
        $html .= '<tr>
                <td>' . $item['product_title'] . '</td>
                <td>' . $item['qty'] . '</td>
                <td>' . number_format($item['selling_price'], 2) . '</td>
                <td>' . number_format($item['total'], 2) . '</td>
            </tr>';
        
        $subtotal += $item['total'];
        $vat_total += isset($item['vat_amount']) ? $item['vat_amount'] : 0;
    }
    
    $html .= '</tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right">Subtotal:</th>
                    <th>' . number_format($subtotal, 2) . '</th>
                </tr>
                <tr>
                    <th colspan="3" class="text-right">VAT:</th>
                    <th>' . number_format($vat_total, 2) . '</th>
                </tr>
                <tr>
                    <th colspan="3" class="text-right">Total:</th>
                    <th>' . number_format($subtotal + $vat_total, 2) . '</th>
                </tr>
            </tfoot>
        </table>

        <h4>Payment Details</h4>
        <table>
            <thead>
                <tr>
                    <th>Payment Method</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($payments as $payment) {
        $html .= '<tr>
                <td>' . ($payment['method_title'] ?? 'Unknown') . '</td>
                <td>' . number_format($payment['amount'], 2) . '</td>
            </tr>';
    }
    
    $html .= '</tbody>
        </table>

        <div class="footer">
            <p>Thank you for your business!</p>
            <p><strong>Note:</strong> This is an unsigned receipt generated for internal use.</p>
        </div>
    </div>
</body>
</html>';

    // Load the HTML into DomPDF
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    // Output the generated PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="receipt_' . $sale['sales_no'] . '.pdf"');
    echo $dompdf->output();
    
} catch (Exception $e) {
    // Log error
    error_log('PDF Generation Error: ' . $e->getMessage());
    
    // Display error
    echo '<h1>Error</h1>';
    echo '<p>' . $e->getMessage() . '</p>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
