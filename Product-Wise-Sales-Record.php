<?php
include_once("auth.php");
include('function_query.php');
$conn_me = Database::getInstance();

?>
 <link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css"/>

 <?php


$date_from = date("Y-m-d", strtotime($_GET['date_from']));
$date_to = date("Y-m-d", strtotime($_GET['date_to']));
$product_id = $_GET['product_id'];

$report = '<div class="row">
<div class="col-md-12">

    <div class="panel panel-default">
        <div class="panel-body" id="load_table">
       
        <table class="table datatable" id="MSalary">
        <thead>
        <th>Sl</th>
        <th>Invoice No</th>
        <th>Invoice Date</th>
        <th>Product Name</th>
        <th>Category</th>
        <th>Sales By</th>
        <th>Brunch</th>
        <th>Warehouse Branch</th>
        <th>Qty</th>
        <th>Rate</th>


        </thead>
            <tbody>'; 
$sl =1;





$total_sold =0;
$qry = $conn_me->prepare("SELECT
A.code,
A.sales_quantity,
A.sales_rate,
B.invoice_no,
B.invoice_date,
C.product_name,
I.category,
F.unit,
H.name AS SalesPerson,
D.brunch AS sales_brunch,
E.brunch AS dispatcher_brunch
FROM
sales_invoice_item A JOIN
        sales_invoice B ON A.sales_invoice_id = B.id
      JOIN
        setup_product C ON A.product_id = C.id
      JOIN
        setup_brunch D ON A.brunch_id = D.id
      JOIN
        setup_brunch E ON B.dispatch_from_which_brunch = E.id
      JOIN
        setup_unit F ON C.unit_id = F.id
      JOIN
        admin G ON A.sales_person = G.id
      JOIN
        setup_employee H ON G.employee_id = H.id
       JOIN
        setup_category I ON C.category_id = I.id
      INNER JOIN
        (SELECT DISTINCT product_id
         FROM sales_invoice_item
         WHERE product_id =  '".$_GET['product_id']."' ) AS subquery ON A.product_id = subquery.product_id
      WHERE
        A.sales_manager_confirm_date BETWEEN '".$date_from."' AND '".$date_to."'
        AND B.generate_challan = 'Done' 
      ORDER BY
        A.id ASC
");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);

    foreach($fetch_list AS $fetch) {

        
                $report .= '<tr>
                <td>'.$sl++.'</td>
                <td><a target="_BLINK" href="invoice_copy.php?code='.$fetch['code'].'" >'.$fetch['invoice_no'].'</a></td>
                <td>'.date("Y/m/d", strtotime($fetch['invoice_date'])) .'</td>
                <td>'.$fetch['product_name'].'</td>
                <td>'.$fetch['category'].'</td>
                <td>'.$fetch['SalesPerson'].'</td>
                <td>'.$fetch['sales_brunch'].'</td>
                <td>'.$fetch['dispatcher_brunch'].'</td>
                <td>'.$fetch['sales_quantity']. ' ' .   $fetch['unit'] .'</td>
                <td>'.$fetch['sales_rate'].'</td>

               </tr>';

               $total_sold += $fetch['sales_quantity'];

      
           

         
        
        
    
       

    }

    $report .= '<tfoot>
<tr>
<th colspan="8" style="text-align:right"><b>Total</b></th>
<th colspan="2">' . $total_sold . '</th>
</tr>
</tfoot>';

                
$report .= '</tbody> 
      </table>
      </div>
            
    </div>

</div>
</div>';



print $report; 