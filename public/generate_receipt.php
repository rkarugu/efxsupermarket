<?php
// Simple standalone receipt generator
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Get the sale ID from the URL parameter
$id = isset($_GET['id']) ? base64_decode($_GET['id']) : null;

if (!$id) {
    die('No ID provided');
}

try {
    // Bootstrap Laravel to get access to the models
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );

    // Get the sale data
    $data = App\Model\WaPosCashSales::with([
        'items.item.pack_size',
        'payment',
        'user'
    ])->find($id);

    if (!$data) {
        die('Receipt not found');
    }

    // Get payment details
    $payments = [];
    if ($data->payment && count($data->payment) > 0) {
        foreach ($data->payment as $payment) {
            $paymentMethod = App\Model\PaymentMethod::find($payment->payment_method_id);
            $payments[] = [
                'title' => $paymentMethod ? $paymentMethod->title : 'Unknown',
                'amount' => $payment->amount
            ];
        }
    }

    // Get ESD details if available
    $esd_details = App\Model\WaEsdDetails::where('invoice_number', $data->sales_no)
        ->orderBy('id', 'desc')
        ->first();

    // Generate HTML for the receipt
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Invoice #' . $data->sales_no . '</title>
        <style>
            body {
                font-family: Arial, sans-serif;
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
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>RECEIPT</h2>
                <h3>Invoice #' . $data->sales_no . '</h3>
            </div>

            <div>
                <h4>Customer Details</h4>
                <p><strong>Name:</strong> ' . $data->customer . '</p>
                <p><strong>Phone:</strong> ' . $data->customer_phone_number . '</p>
            </div>

            <div>
                <h4>Invoice Details</h4>
                <p><strong>Date:</strong> ' . $data->date . '</p>
                <p><strong>Time:</strong> ' . $data->time . '</p>
                <p><strong>Cashier:</strong> ' . $data->user->name . '</p>
            </div>

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
    $vat = 0;
                
    foreach ($data->items as $item) {
        $html .= '<tr>
            <td>' . $item->item->title . '</td>
            <td>' . $item->qty . '</td>
            <td>' . number_format($item->selling_price, 2) . '</td>
            <td>' . number_format($item->total, 2) . '</td>
        </tr>';
        
        $subtotal += $item->total;
        $vat += $item->vat_amount;
    }
                
    $html .= '</tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-right">Subtotal:</th>
                        <th>' . number_format($subtotal, 2) . '</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-right">VAT:</th>
                        <th>' . number_format($vat, 2) . '</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-right">Total:</th>
                        <th>' . number_format($subtotal + $vat, 2) . '</th>
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
            <td>' . $payment['title'] . '</td>
            <td>' . number_format($payment['amount'], 2) . '</td>
        </tr>';
    }
                
    $html .= '</tbody>
            </table>';
            
    if ($esd_details) {
        $html .= '<div>
            <h4>ESD Details</h4>
            <p><strong>Status:</strong> ' . $esd_details->description . '</p>';
            
        if ($esd_details->verify_url) {
            $html .= '<p><strong>Verification URL:</strong> ' . $esd_details->verify_url . '</p>';
        }
        
        $html .= '</div>';
    }
            
    $html .= '<div style="text-align: center; margin-top: 30px;">
            <p>Thank you for your business!</p>
        </div>
    </div>
</body>
</html>';

    // Configure Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    
    // Create Dompdf instance
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    // Output the PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="receipt_' . $data->sales_no . '.pdf"');
    echo $dompdf->output();
    
} catch (Exception $e) {
    // Log the error
    error_log('PDF Receipt Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    
    // Display error
    echo '<h1>Error Generating Receipt</h1>';
    echo '<p>' . $e->getMessage() . '</p>';
}
