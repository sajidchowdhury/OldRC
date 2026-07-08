<?php
include_once("auth.php");
include('function_query.php');
$conn_me = Database::getInstance();

?>
 <link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css"/>







<?php if($_GET['copy'] == 'Print-Copy' ){


$qry = $conn_me->prepare("SELECT A.code,A.id,
A.notes,A.`invoice_no`,A.`invoice_date`,B.`brunch` AS DEMAND_FROM ,C.`brunch` AS DEMAND_TO,
F.`name` as `poster_name`

FROM `demand` A  

JOIN `setup_brunch` B ON (A.`demand_created_from` = B.`id`)
JOIN `setup_brunch` C ON (A.`demand_created_to` = C.`id`)
JOIN `admin` E ON (A.`poster` = E.`id`)
JOIN `setup_employee` F ON (E.`employee_id` = F.`id`)

WHERE
 A.id = '".$_GET['demand_id']."' 
 ORDER BY A.`id` ASC");
$qry->execute();
$fetch = $qry->fetch(PDO::FETCH_ASSOC);


$report = ' <div id="printableArea">
<div class="row">

<div class="panel-heading hidden-print">
  <div class="btn-group pull-right">
      <button onclick="printNow()" class="btn btn-danger" ><i class="fa fa-print"></i> Print</button>

  </div>                                    
                                  
</div>
<div class="row">
<div class="col-md-12"><div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
<tr><th style="text-align:center;font-size:18px;color:red">DEMAND RECORD PRINT COPY</th></tr>
<tr><th style="text-align:center;font-size:18px">Invoice No. '.$fetch['invoice_no'].' | Demand Date. '.$fetch['invoice_date'].' </th></tr> 
<tr><th style="text-align:center;font-size:18px">Demand From  '.$fetch['DEMAND_FROM'].' >>  Demand To  '.$fetch['DEMAND_TO'].'</th></tr> 
<tr><th style="text-align:center;font-size:18px">Notes: '.$fetch['notes'].'</th></tr> ';

$report .= '</table></div>';


$report .= '';


$report .= '<div class="row">
<div class="col-md-12">

    <div class="panel panel-default">
        <div class="panel-body" id="load_table">
        <div class="table-responsive">
        <table class="table table-hover table-condensed table-striped table-bordered"  id="MSalary2">
<thead>
<tr>
<th>Sl</th> 

<th>Product Name</th> 
<th>CTN</th> 
<th>Pcs</th> 
<th>Approved Quantity</th> 
<th>Warehouse Name</th> 
<th>Price</th> 
<th>Total</th> 

</tr>
</thead>
<tbody>'; 

$sl =1;

$total_of_total_qty = 0.00;
$total_of_total_invoice_price = 0.00;
$total_of_total_receive = 0.00;
$total_of_total_due = 0.00;

          
        

          $total_qty =0;
          $actul_total_price = 0;
          $qry2 = $conn_me->prepare("SELECT
          A.id,
          A.demand_id,
          A.product_id,
          A.quantity,
          B.product_name,
          B.pcs_in_cartoon,
          B.sales_rate
        FROM
        demand_item A
        JOIN
        setup_product B ON A.product_id = B.id
        WHERE
          A.demand_id = '".$fetch['id']."';
        ");
          $qry2->execute();
          $count =   $qry2->rowCount();
          $fetch_list2 = $qry2->fetchAll(PDO::FETCH_ASSOC);
          foreach($fetch_list2 AS $fetch2) {
            $count_sl = $sl++;
            if($count_sl == 1 ){ $rowspan= "rowspan='.$count.'"; } else { $rowspan= '' ; }

            $delivery_done  = DEMAND::TotalDelivery($fetch2['demand_id'],$fetch2['product_id']) ; 
             
            $total = $delivery_done['total_item']*$delivery_done['productprice'] ; 

  
            $in_carton = (float)($fetch2['quantity'] / $fetch2['pcs_in_cartoon']);
            $in_carton = ($in_carton < 1) ? 0 : $in_carton;



                $report .= '<tr>
                <td>'.$count_sl.'</td> 
     
                <td>'.$fetch2['product_name'].'</td> 
                <td >'.$in_carton.'</td> 
                <td >'.$fetch2['quantity'].'</td> 
                <td > </td> 

                <td></td> 
                <td >'.$fetch2['sales_rate'].'</td> 
                <td></td>';
               
                $report .= '</tr>';


          }
         
    
  

    $report .= '</tbody></table></div>';   

                
$report .= '
      </div>
            
    </div>

</div>
</div></div>
</div>
</div>


';


}else if ($_GET['copy'] == 'Approved-Copy'){


  $qry = $conn_me->prepare("SELECT A.code,A.id,
  A.notes,A.`invoice_no`,A.`invoice_date`,B.`brunch` AS DEMAND_FROM ,C.`brunch` AS DEMAND_TO,
  F.`name` as `poster_name`
  
  FROM `demand` A  
  
  JOIN `setup_brunch` B ON (A.`demand_created_from` = B.`id`)
  JOIN `setup_brunch` C ON (A.`demand_created_to` = C.`id`)
  JOIN `admin` E ON (A.`poster` = E.`id`)
  JOIN `setup_employee` F ON (E.`employee_id` = F.`id`)
  
  WHERE
   A.id = '".$_GET['demand_id']."' 
   ORDER BY A.`id` ASC");
  $qry->execute();
  $fetch = $qry->fetch(PDO::FETCH_ASSOC);
  
  
  $report = ' <div id="printableArea">
  <div class="row">
  
  <div class="panel-heading hidden-print">
    <div class="btn-group pull-right">
        <button onclick="printNow()" class="btn btn-danger" ><i class="fa fa-print"></i> Print</button>
  
    </div>                                    
                                    
  </div>
  <div class="row">
  <div class="col-md-12"><div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
  <tr><th style="text-align:center;font-size:18px;color:red">DEMAND RECORD APPROVED COPY</th></tr>
  <tr><th style="text-align:center;font-size:18px">Invoice No. '.$fetch['invoice_no'].' | Demand Date. '.$fetch['invoice_date'].' </th></tr> 
  <tr><th style="text-align:center;font-size:18px">Demand From  '.$fetch['DEMAND_FROM'].' >>  Demand To  '.$fetch['DEMAND_TO'].'</th></tr> 
  <tr><th style="text-align:center;font-size:18px">Notes: '.$fetch['notes'].'</th></tr> ';
  
  $report .= '</table></div>';
  
  
  $report .= '';
  
  
  $report .= '<div class="row">
  <div class="col-md-12">
  
      <div class="panel panel-default">
          <div class="panel-body" id="load_table">
          <div class="table-responsive">
          <table class="table table-hover table-condensed table-striped table-bordered"  id="MSalary2">
  <thead>
  <tr>
  <th>Sl</th> 
  <th>Date</th> 

  <th>Product Name</th> 
  <th>Dispatch Warehouse Name</th> 
  <th>Receive Warehouse Name</th> 
  <th>Approved Quantity</th> 
  <th>Price</th> 
  <th>Total</th> 
  
  </tr>
  </thead>
  <tbody>'; 
  
  $sl =1;
  
  $total_of_total_qty = 0.00;
  $total_of_total_invoice_price = 0.00;
  $total_of_total_receive = 0.00;
  $total_of_total_due = 0.00;
  
             $all_total = 0 ;
          
  
            $total_qty =0;
            $actul_total_price = 0;
            $qry2 = $conn_me->prepare("SELECT
            A.id,
            A.invoice_date,
            A.price,
            A.demand_id,
            A.product_id,
            A.quantity,
            B.product_name,
            B.pcs_in_cartoon,
            C.name as receive_w,
            D.name as dis_w
            
          FROM
          demand_receive A
          JOIN
          setup_product B ON A.product_id = B.id
          JOIN
          setup_warehouse C ON A.received_warehouse = C.id

          JOIN
          setup_warehouse D ON A.dispatch_from_warehouse = D.id
          
          WHERE
            A.demand_id = '".$fetch['id']."';
          ");
            $qry2->execute();
            $count =   $qry2->rowCount();
            $fetch_list2 = $qry2->fetchAll(PDO::FETCH_ASSOC);
            foreach($fetch_list2 AS $fetch2) {
              $count_sl = $sl++;
              if($count_sl == 1 ){ $rowspan= "rowspan='.$count.'"; } else { $rowspan= '' ; }
  
               
              $total = $fetch2['quantity']*$fetch2['price'] ; 
  
            
  
  
                  $report .= '<tr>
                  <td>'.$count_sl.'</td> 
                  <td>'.$fetch2['invoice_date'].'</td> 
                  <td>'.$fetch2['product_name'].'</td> 

                  <td > '.$fetch2['dis_w'].' </td> 
                  <td > '.$fetch2['receive_w'].' </td> 

                  <td > '.$fetch2['quantity'].' </td> 
                  <td > '.$fetch2['price'].' </td> 

                  <td >'.$total.'</td> 

                  ';
                 
                  $report .= '</tr>';
                  
                  $total_qty += $fetch2['quantity'] ;  
                  $all_total += $total ; 
  
  
            }
           
      
    
  
      $report .= '<tfoot><tr><th colspan="5" style="text-align:right">Total</th><th>'.$total_qty.'</th><th></th><th>'.$all_total.'</th><th></th></tr></tfoot></tbody></table></div>';   
  
                  
  $report .= '
        </div>
              
      </div>
  
  </div>
  </div></div>
  </div>
  </div>
  
  
  ';
  

}else{



}



print $report;

?>

<script type="text/javascript" >
  function printNow(){

    var printContents = document.getElementById('printableArea').innerHTML;
  var originalContents = document.body.innerHTML;
  document.body.innerHTML = printContents;
  window.print();
  document.body.innerHTML = originalContents;
  }

    </script>
    