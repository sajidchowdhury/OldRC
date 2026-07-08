<?php
include_once("auth.php");
include('function_query.php');
include('bangla-convert.php');

if($_GET['status'] == 'Pending' ){
$invoice_info = SETUP::ACCOUNT_TRANSECTION_PENDING($_GET['id']);
}else{

$invoice_info = SETUP::ACCOUNT_TRANSECTION($_GET['id']);

}
$obj = new BanglaNumberToWord();

$poster_info = SETUP::ADMIN_SETUP($invoice_info['poster']);

if($invoice_info['transection_to'] == 'Supplier' ){

    $info_due = FIND::SUPPLIER_DUE($invoice_info['transection_to_id']);
    $due = $info_due['supplier_due'];
}else if ($invoice_info['transection_to'] == 'Customer' ){

    $info_due = FIND::getAllCustomerDues('Brunch-Wise-Single-Customer-Wise',$invoice_info['transection_to_id'],date('Y-m-d'),$invoice_info['brunch_id']);
    $due = $info_due[0]['customer_due'];
}else{
    $due = 'NONEED';
}

        $transection_date = date("d-m-Y", strtotime($invoice_info['transection_date']));

        
?>


<!DOCTYPE html>
<html>

<style>

    
</style>
<body id="printableArea">


<?php for ( $i=0;  $i < 2;  $i++) {  ?>

<table class="title">

<tr>
<td colspan="6">  <img src="challan_copy.svg" alt=""></td>
</tr>
<tr>
    <td colspan="6" style="text-align:center">

    <text id="address1" transform="matrix(1 0 0 1 180.7775 77.9423)" style="font-size:14px;"> MONEY RECEIPT : Print Date: <?php print date('d-m-Y');?>  </text>

    </td>
</tr>

<tr>
<td colspan="2" style="white-space: nowrap;line-height:10px;text-align:left;padding: 10px 10px;border : 1px solid; " > Invoice No <?php print  $invoice_info['invoice_no'];?> </td>
<td colspan="4"  style="white-space: nowrap;line-height:10px;text-align:left;padding: 10px 10px;border: 1px solid ">Date  <?php print  $transection_date;?></td>
</tr>


<tr><td colspan="2" style="white-space: nowrap;line-height:10px;text-align:left; padding: 10px 10px;border: 1px solid black">Name <?php print $invoice_info['only_name'];?></td>
<td colspan="4" style="white-space: nowrap;line-height:10px;text-align:left; padding: 10px 10px;border: 1px solid black">Mobile <?php print $invoice_info['only_mobile'];?></td>
</tr>

<tr>
<td  colspan="6" style="white-space: nowrap;line-height:10px;text-align:left; padding: 10px 10px;border: 1px solid black">Address <?php print $invoice_info['address'];?></td>
</tr>

<tr>
<td  colspan="6" style="white-space: nowrap;line-height:10px;text-align:center; padding: 10px 10px;border: 1px solid black"> <b>TRANSACTION DETAILS</b> </td>
</tr>

<tr>
<td  colspan="6" style="white-space: nowrap;line-height:10px;text-align:left; padding: 10px 10px;border: 1px solid black">Transaction By : <?php print $invoice_info['transection_by'];?></td>
</tr>


<tr>
<td  colspan="6" style="white-space: nowrap;line-height:10px;text-align:left; padding: 10px 10px;border: 1px solid black">Details :  <?php print $invoice_info['title2'] ;?></td>
</tr>

<?php if( $due == 'NONEED' ) {

}else{ ?>
<tr>
<td  colspan="6" style="white-space: nowrap;line-height:10px;text-align:left; padding: 10px 10px;border: 1px solid black">Previous Due :  <?php print $due ;?></td>
</tr>
<?php } ?>

<tr>
<td  colspan="6" style="white-space: nowrap;line-height:10px;text-align:left; padding: 10px 10px;border: 1px solid black">Receive Now :  <?php print $invoice_info['transection_now'] ;?></td>
</tr>


<?php if( $due == 'NONEED' ) {

}else{ ?>
<tr>
<td  colspan="6" style="white-space: nowrap;line-height:10px;text-align:left; padding: 10px 10px;border: 1px solid black">Total Due :  <?php print $due - $invoice_info['transection_now'] ;?></td>
</tr>
<?php } ?>

<tr>
<td  colspan="6" style="text-align:left; padding: 10px 10px;border: 1px solid black">Narration :  <?php print $invoice_info['note'] ;?></td>
</tr>

    <tr>
    <td style="center;width:10%;padding: 10px 10px;"></td>
    <td style="center;width:60%;padding: 10px 10px;"></td>
    <td style="center;padding: 10px 10px;width:7.5%;"></td>
    <td style="center;padding: 10px 10px;width:7.5%;"></td>
    <td style="center;padding: 10px 10px;width:7.5%;"></td>
    <td style="center;padding: 10px 10px;width:7.5%;"></td>
    </tr>

    <tr>
    <th colspan="1" style="text-align:center;white-space:nowrap;"><?php print $poster_info['hr_name'] ;?></th>
    <th colspan="4"></th>
    <th colspan="1" style="text-align:center"></th>
 </tr>
 
 <tr>
    <th colspan="1" style="text-align:center">.................................... </th>
    <th colspan="4"></th>
    <th colspan="1" style="text-align:center">.................................... </th>
 </tr>


 <tr>
    <th colspan="1" style="text-align:center"> Operator Signature </th>
    <th colspan="4"></th>
    <th colspan="1" style="text-align:center"> Receiver Signature</th>

 </tr>


</table>

<?php } ?>







</body>
</html>




