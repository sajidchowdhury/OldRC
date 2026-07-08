<?php
include_once("auth.php");
include('function_query.php');
include('bangla-convert.php');

$ck1 = $conn_me->prepare("SELECT * FROM `{$_GET['purches_type']}` where `code` = '".$_GET['code']."' GROUP BY `code` ");
$ck1->execute();
$fetch = $ck1->fetch(PDO::FETCH_ASSOC);


$invoice_info = SETUP::LOCAL_PURCHES($fetch['id'],$_GET['purches_type']);

$company_info = SETUP::SETUP_COMPANY('Active');
$info_supplier_due = FIND::SUPPLIER_DUE($invoice_info['supplier_id']);
$obj = new BanglaNumberToWord();



?>
 <link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css"/>


 <style>
.col-print-1 {width:8%;  float:left;}
.col-print-2 {width:16%; float:left;}
.col-print-3 {width:25%; float:left;}
.col-print-4 {width:33%; float:left;}
.col-print-5 {width:42%; float:left;}
.col-print-6 {width:50%; float:left;}
.col-print-7 {width:58%; float:left;}
.col-print-8 {width:66%; float:left;}
.col-print-9 {width:75%; float:left;}
.col-print-10{width:83%; float:left;}
.col-print-11{width:92%; float:left;}
.col-print-12{width:100%; float:left;}



.watermark{
  background:url("img/logo.png") center center no-repeat !important;
  opacity: 0.1 !important;
  position: absolute !important;
  width: 100% !important;
  height: 100% !important;
}

@media only print {
    .watermark{
  background:url("img/logo.png") center center no-repeat;opacity:0.1 !important;
  opacity: 0.1 !important;
  position: absolute !important;
  width: 100% !important;
  height: 100% !important;
}
}



 </style>

 <div id="printableArea">
 <div class="watermark"></div>

 <?php print $company_info['header_content'];?>

 <div class="container">




<div class="row">

<div class="col-print-6">
সরবরাহকারী কোড :  <strong> <?php print $invoice_info['supplier_code'];?></strong><br>
সরবরাহকারী  নাম :  <strong> <?php print $invoice_info['supplier_name'];?></strong><br>
সরবরাহকারী ঠিকানা : <strong> <?php print $invoice_info['supplier_address'];?></strong><br>
সরবরাহকারী মোবাইল : <strong> <?php print $invoice_info['supplier_mobile'];?></strong>

</div>

<div class="col-print-6 text-right">
ক্রয়কারী :  <strong> <?php print $invoice_info['purchase_by'];?></strong><br>
চালান নাম্বার :  <strong> <?php print $invoice_info['invoice_no'];?></strong><br>
তারিখ : <strong> <?php print $invoice_info['invoice_date'];?></strong><br>

</div>



</div>


<br>




<div class="row">
<div class="col-print-12">


<div class="table-responsive-sm">
<table class="table table-striped">
<thead>
<tr>
<th class="center">ক্রমিক নং</th>
<th>পন্যের বিবরণ</th>
<th>পন্যের শ্রেণী</th>
<th>পরিমান</th>
<th class="right">দর</th>
  <th class="center">মোট মূল্য</th>
</tr>
</thead>
<tbody>
    <?php 

$total_price =0;
$sl =1;
$qry2 = $conn_me->prepare("SELECT *  FROM `{$_GET['purches_type']}` where `code` = '".$_GET['code']."'  ");
$qry2->execute();
$count =   $qry2->rowCount();
$fetch_list2 = $qry2->fetchAll(PDO::FETCH_ASSOC);
foreach($fetch_list2 AS $fetch2) { 
    if($_GET['purches_type'] == 'raw_local_purches' ){
        $product_data = SETUP::SETUP_RAW_MATERIAL($fetch2['product_id']);
    }else {
        $product_data = SETUP::SETUP_PRODUCT($fetch2['product_id']);

    }

    ?>


<tr>
<td class="center"><?php print $sl++;?></td>
<td class="left strong"><?php print $product_data['product_name'];?></td>
<td class="left strong"><?php print $product_data['category'];?></td>
<td class="left"><?php print $fetch2['quantity'];?></td>
<td class="left"><?php print $fetch2['purches_price'];?></td>
  <td class="center"><?php print $fetch2['purches_price']*$fetch2['quantity'];?></td>
</tr>
<?php 
$total_price += $fetch2['purches_price']*$fetch2['quantity'];

} $challan_cost =number_format((float)( $total_price+$fetch['transport_cost']+$fetch['vat_cost']), 2, '.', ''); ?>


<tr>
    <td colspan="4"></td>
    <td class="right"><strong>মোট</strong></td>
    <td class="center"><strong><?php print $total_price;?><strong></td>
</tr>
<tr>
<td colspan="4"></td>
    <td class="right"><strong>পরিবহন খরচ</strong></td>
    <td class="center"><strong><?php print $fetch['transport_cost'];?><strong></td>
</tr>

<tr>
<td colspan="4"></td>
    <td class="right"><strong>ভ্যাট খরচ</strong></td>
    <td class="center"><strong><?php print $fetch['vat_cost'];?><strong></td>
</tr>fetch

<tr>
<td colspan="4"></td>
    <td class="right"><strong>মোট চালান মূল্য</strong></td>
    <td class="center"><strong><?php print $challan_cost;?><strong></td>
</tr>


<tr>
<td colspan="4"></td>
    <td class="right"><strong>মোট বকেয়া</strong></td>
    <td class="center"><strong><?php print $info_supplier_due['supplier_due'];?><strong></td>
</tr>


<tr>
    <td colspan="4"><strong>কথায়: </strong><?php print $obj->numToWord($challan_cost);?> টাকা মাত্র</td>
    <td colspan="5"></td>
</tr>



</tbody>
</table>
</div>

</div>

</div>

</div>

</div>


<script type="text/javascript" >
    var printContents = document.getElementById('printableArea').innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    </script>