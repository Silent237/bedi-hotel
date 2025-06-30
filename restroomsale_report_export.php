<?php
session_start();
require 'scripts/settings.php';
require 'vendor/autoload.php'; // Adjust path as needed for PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;



// Prepare your SQL query based on session or default
if(isset($_SESSION['sale_date_from'])){
	$sql = 'select invoice_sale_restaurant.sno, cust_name,company_name , concerned_person, department, id_2 as tin, taxable_amount, tot_vat, tot_sat, total_amount, tot_disc, other_discount, grand_total, timestamp, quantity, type, invoice_type, invoice_no, agent_id, mode_of_payments.mode_of_payment as mode_of_payment, storeid, service_charge_amount, service_charge_tax_amount, service_charge_total from invoice_sale_restaurant left join customer on customer.sno = invoice_sale_restaurant.supplier_id left join mode_of_payments on mode_of_payments.sno = invoice_sale_restaurant.mode_of_payment where timestamp>="'.$_SESSION['sale_date_from'].'" and timestamp<="'.$_SESSION['sale_date_to'].'"  and storeid like "room%" ';
	//echo $sql;

	$sql_sum = 'select sum(taxable_amount) as taxable_amount, sum(tot_vat) as tot_vat, sum(tot_sat) as tot_sat, sum(total_amount) as total_amount, sum(grand_total) as grand_total, if(other_discount="", sum(tot_disc), sum(other_discount)) as total_discount, sum(quantity) as quantity from invoice_sale_restaurant left join customer on customer.sno = invoice_sale_restaurant.supplier_id where timestamp>="'.$_SESSION['sale_date_from'].'" and timestamp<="'.$_SESSION['sale_date_to'].'"  and storeid like "room%" ';
	//echo $sql_sum;
	$filter_summary = ' where timestamp>="'.$_SESSION['sale_date_from'].'" and timestamp<="'.$_SESSION['sale_date_to'].'"';
	if(isset($_SESSION['sale_supplier_sno'])){
		if($_SESSION['sale_supplier_sno']!=''){
			$sql .= ' and supplier_id="'.$_SESSION['sale_supplier_sno'].'" ';
			$sql_sum .= ' and supplier_id="'.$_SESSION['sale_supplier_sno'].'" ';
			$filter_summary .= ' and supplier_id="'.$_SESSION['sale_supplier_sno'].'" ';
		}
		if($_SESSION['sale_quantity']!=''){
			$sql .= ' and quantity '.$_SESSION['sale_qty_symbol'].$_SESSION['sale_quantity'];
			$sql_sum .= ' and quantity '.$_SESSION['sale_qty_symbol'].$_SESSION['sale_quantity'];
			$filter_summary .= ' and quantity '.$_SESSION['sale_qty_symbol'].$_SESSION['sale_quantity'];
		}
		if($_SESSION['sale_amount']!=''){
			$sql .= ' and grand_total '.$_SESSION['sale_amount_symbol'].$_SESSION['sale_amount'];
			$sql_sum .= ' and grand_total '.$_SESSION['sale_amount_symbol'].$_SESSION['sale_amount'];
			$filter_summary .= ' and grand_total '.$_SESSION['sale_amount_symbol'].$_SESSION['sale_amount'];
		}
		
		if($_SESSION['sale_invoice_type']!='all'){
			switch($_SESSION['sale_invoice_type']){
				case 'tax_gst':{
					$sql .= ' and id_2!=""';
					$sql_sum .= ' and id_2!=""';
					$filter_summary .= ' and id_2!=""';
					
					break;
				}
				case 'tax_wo_gst':{
					$sql .= ' and id_2=""';
					$sql_sum .= ' and id_2=""';
					$filter_summary .= ' and id_2=""';
					
					break;
				}
				default:{
					$sql .= ' and invoice_type="'.$_SESSION['sale_invoice_type'].'"';
					$sql_sum .= ' and invoice_type="'.$_SESSION['sale_invoice_type'].'"';
					$filter_summary .= ' and invoice_type="'.$_SESSION['sale_invoice_type'].'"';
					break;
				}
			}
		}
		if($_SESSION['sale_invoice_no']!=''){
			$sql .= ' and invoice_no="'.$_SESSION['sale_invoice_no'].'" ';
			$sql_sum .= ' and invoice_no="'.$_SESSION['sale_invoice_no'].'" ';
			$filter_summary .= ' and invoice_no="'.$_SESSION['sale_invoice_no'].'" ';
		}
		if($_SESSION['sale_mop']!='all'){
			if($_SESSION['sale_mop']=='all_nocharge'){
				$sql .= ' and invoice_sale_restaurant.mode_of_payment!="nocharge"';
				$sql_sum .= ' and mode_of_payment!="nocharge"';
				$filter_summary .= ' and invoice_sale_restaurant.mode_of_payment!="nocharge"';
			}
			else{
				$sql .= ' and invoice_sale_restaurant.mode_of_payment="'.$_SESSION['sale_mop'].'"';
				$sql_sum .= ' and mode_of_payment="'.$_SESSION['sale_mop'].'"';
				$filter_summary .= ' and invoice_sale_restaurant.mode_of_payment="'.$_SESSION['sale_mop'].'"';
			}	
		}
	}
}
	$sql .= ' order by timestamp desc, abs(substr(invoice_no,2)) desc';

$result_data = mysqli_query($db, $sql);

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set header row
$headers = [
    "S.No.",
    "Company Name / Guest Name",
    "GSTIN",
    "Item Total",
    "Service Charge",
    "Taxable Amount",
    "SGST",
    "CGST",
    "Invoice Amount",
    "Discount",
    "Amount Payable",
    "Sale Date",
    "Unit",
    "Mode of Payment",
    "Table",
    "Invoice No."
];
$sheet->fromArray($headers, NULL, 'A1');

$rowIndex = 2; // Starting row for data
$i = 1; // Serial number

while($row = mysqli_fetch_assoc($result_data)) {
    $row['service_charge_amount'] = ($row['service_charge_amount']==''?0:$row['service_charge_amount']);
    $row['service_charge_tax_amount'] = $row['service_charge_tax_amount']==''?0:$row['service_charge_tax_amount'];
    $row['service_charge_vat'] = round($row['service_charge_tax_amount']/2,2);
    $row['service_charge_sat'] = round($row['service_charge_tax_amount']/2,2);

    $row['tot_vat'] = (float)$row['service_charge_vat']+(float)$row['tot_vat'];
    $row['tot_sat'] = (float)$row['service_charge_sat']+(float)$row['tot_sat'];
    $row['grand_total'] = round($row['grand_total'],2);

    // Prepare company/guest name
    $companyGuest = $row['company_name'];
    if($row['concerned_person']!=''){
        $companyGuest .= "\nGuest: ".$row['concerned_person'];
    }
    if($row['department']!=''){
        $companyGuest .= "\nDept: ".$row['department'];
    }

    // Mode of payment display fix
    $mode_of_payment = strtoupper($row['mode_of_payment']);
    if($mode_of_payment == 'BANK_TRANSFER') {
        $mode_of_payment = 'BANK TRANSFER';
    }

    // Table / Room name
    if(strpos($row['storeid'], "room") === false){
        // You may need a helper function to get table name here
        $table = 'T-'.get_table($row['storeid']);
    } else {
        $room_id = str_replace("room_", "", $row['storeid']);
        $sqlRoom = "SELECT room_name FROM room_master WHERE sno = ".$room_id;
        $room_result = mysqli_query($db, $sqlRoom);
        $room_data = mysqli_fetch_assoc($room_result);
        $table = 'R-'.$room_data['room_name'];
    }

    $data = [
        $i,
        $companyGuest,
        $row['tin'],
        $row['taxable_amount'],
        $row['service_charge_amount'],
        $row['taxable_amount'] + $row['service_charge_amount'],
        $row['tot_vat'],
        $row['tot_sat'],
        $row['total_amount'] + $row['service_charge_total'],
        $row['other_discount'] != 0 ? $row['other_discount'] : $row['tot_disc'],
        $row['grand_total'],
        date("d-m-Y", strtotime($row['timestamp'])),
        $row['quantity'],
        $mode_of_payment,
        $table,
        $row['invoice_no']
    ];

    // Write to sheet
    $sheet->fromArray($data, NULL, 'A' . $rowIndex);
    $rowIndex++;
    $i++;
}

// Set headers to download file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="sales_report.xlsx"');
header('Cache-Control: max-age=0');

// Write file to output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;

