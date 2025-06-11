<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Advertisement;
use DB;
use Session;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;

class PrintTestController extends Controller
{

public function printform(){
	$title = 'Print Testing ';
	return view('admin/print_class/create_test',compact('title'));
}
public function printTesting(Request $request)
	{
		
		try{
			
		$bill = [
		'bill_id' => '17872',
		'table_no' => 237,
		'datetime' => '16 Jul 20:03 PM',
		'delivery_mode' => 'EAT IN',
		'waiter' => [
		'name' => 'Catherine Wanjiru'
		],
		'products' => [
		['name' =>'Mbuzi', 'quantity'=>2, 'description'=>'Dry fry', 'amount' => 200],
		['name' =>'Tusker', 'quantity'=>20, 'description'=>'Cold', 'amount' => 5000]
		]
		];
		
		
		
		
		$ip = $request->get('ip');//"192.168.1.102"; // from DB
		$port = $request->get('port'); //9100; // from DB
		
		
		// initialize in order to print over the network
		$connector = new NetworkPrintConnector($ip, $port);
		
		// Initialize printer object
		$printer = new Printer($connector);
		
		
		// set justification center
		$printer -> setJustification(Printer::JUSTIFY_CENTER);
		
		// increase the height and width
		$printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH);
		$printer->text("The Tunnel Liqour\n");
		
		// return height and width to normal size
		$printer->selectPrintMode();
		
		// address
		$printer -> text("Mombasa Road, Nairobi, Kenya.\n");
		$printer -> text("020 2001114 MPESA TILL 156224\n");
		$printer -> text("PIN: P0518091591 VAT:-\n");
		$printer -> text("http://www.thetunnel.co.ke\n");
		$printer -> text("info@thetunnel.co.ke\n");
		
		
		$printer -> feed(1); // create a line
		
		// print bill id and its barcode
		$printer -> text("Bill ID: ".$bill['bill_id']."\n");
		$printer->setBarcodeHeight(90);
		$printer->barcode($bill['bill_id'], Printer::BARCODE_CODE39);
		
		// return justifiacation to the left
		$printer -> setJustification(Printer::JUSTIFY_LEFT);
		
		// enter waiter details and date
		$printer -> text("Waiter: ".$bill['waiter']['name']."\n");
		$printer -> text($bill['datetime']."\n");
		$printer -> text("________________________________________________\n"); // this line is printed according to printer size
		
		// display product details
		foreach ($bill['products'] as $key => $product) {
		$printer -> setJustification(Printer::JUSTIFY_LEFT);
		$printer -> text($product['quantity']." ".$product['name']." - ".$product['description']."\n");
		
		$printer -> setJustification(Printer::JUSTIFY_RIGHT);
		$printer -> text(number_format($product['amount'],2)."\n");
		
		}
		$printer -> text("________________________________________________\n");
		$printer -> setJustification(Printer::JUSTIFY_LEFT);
		
		
		
		$printer -> text("2% CTL    ");
		$printer -> setJustification(Printer::JUSTIFY_RIGHT);
		$printer -> text(number_format($product['amount']*.02,2)."\n");
		$printer -> setJustification(Printer::JUSTIFY_LEFT);
		
		$printer -> text("14% VAT    ");
		$printer -> setJustification(Printer::JUSTIFY_RIGHT);
		$printer -> text(number_format($product['amount']*.14,2)."\n");
		
		$printer -> selectPrintMode(Printer::MODE_EMPHASIZED);
		$printer -> text("Total:  ");
		$printer -> setJustification(Printer::JUSTIFY_RIGHT);
		$printer -> text(number_format($product['amount'],2)."\n");
		
		$printer -> feed(1);
		
		$printer -> cut();
		$printer -> close();
		} catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }

	}

     
}
