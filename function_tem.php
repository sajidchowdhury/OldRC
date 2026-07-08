<?php
 
include_once('function_query.php'); 

$conn_me = Database::getInstance();


 if ($_POST['action'] == 'FG_Recipe_Load'){

    $content = '';


    $content .= '<div id="table-scroll">
        <div class="table-responsive">
            <table id="mytable" class="table" style="white-space:nowrap;">
                <thead>';
                $product_info = SETUP::SETUP_PRODUCT($_POST['PRODUCT_ID']);
                $content .= '<tr style="background-color:#941d63"><td style="color:white;text-align:center"  colspan="6">*** Recipe for '.$product_info['product_name'].' ***</td></tr>
                    <tr>

                        <th> Sl</th>
                        <th>Raw Material</th>
                        <th>Recipe Qty</th>
                        <th>Total Quantity</th>
                        <th>Stock</th>
                        <th>Demand</th>

                    </tr>
                </thead>
                <tbody>';

              

                        $count = 0;
                         $sl = 1;
                        $query = $conn_me->prepare("SELECT *  FROM `receip_fg`  where `product_id` = '".$_POST['PRODUCT_ID']."' ORDER BY `id` DESC");
                        $query->execute();
                        $fetch_query = $query->fetchAll(PDO::FETCH_ASSOC);
                        $rowCount =$query->rowCount();
                    

                        foreach($fetch_query AS $fetch){
                            $count = $count + 1;
                            $CART_DATA = SETUP::SETUP_FG_RECIPE($fetch['id']);

                            $recipe_qty = number_format(($fetch['quantity']),2);
                            $qty_muliply_by_batch = $fetch['quantity']*$_POST['batch_quantity'];
                            $stock = STOCK::RAW_ITEM_WISE_STOCK('',$fetch['raw_material_id'],'product_wise');



                        $content .= '<tr class="tr'. $count .'">
                            <td>'.$sl++.'<input type="hidden" name="product_id[]" id="product_id'.$count.'" data-srno="'.$count.'" class="form-control product_id" value="'.$fetch['product_id'].'"/></td>
                            <td>'.$CART_DATA['raw_material_name'].'<input type="hidden" name="material_id[]" id="material_id'.$count.'" data-srno="'.$count.'" class="form-control material_id" value="'.$fetch['raw_material_id'].'"/></td>
                            <td>'.$recipe_qty . ' ' . $CART_DATA['raw_material_unit'].' </td>
                            <td>'.$qty_muliply_by_batch. ' ' . $CART_DATA['raw_material_unit'].' </td>
                            <td>'.$stock['ITEM_STOCK']. ' ' . $CART_DATA['raw_material_unit'].' </td>
                            <td><input type="number" name="demand_qty[]" id="demand_qty'.$count.'" data-srno="'.$count.'" class="form-control demand_qty" value="'.$qty_muliply_by_batch.'"/>

                             </tr>';

                   
                             }
                             $content .= '<input type="hidden" name="total_item" id="total_item" value="'.$rowCount.'"/>';
          
                             $content .= '</tbody></table>
        </div>';

        $content .='</div>
<input type="hidden" name="count_cart" id="count_cart" value="">';
    
print $content ;

}else if ($_POST['action'] == 'Delete_FG_Movement'){



if($_POST['section'] == 'stock_out_balance_product' || $_POST['section'] == 'stock_in_balance_product'){


$qry = $conn_me->prepare("SELECT * FROM `balance_product` where  `id`  = '".$_POST['ID']."' ");
$qry->execute();
$fetch = $qry->fetch(PDO::FETCH_ASSOC);

$stock_in = $fetch['stock_in'];
$stock_out = $fetch['stock_out'];


$new_stock_id = $stock_in  - $_POST['in_amount'] ; 
$new_stock_out = $stock_out - $_POST['out_amount'] ; 

$query1 = $conn_me->prepare("UPDATE `balance_product` SET `stock_in` = '".$new_stock_id."',`stock_out` = '".$new_stock_out."'
 where   `id` = '".$_POST['ID']."'  ");
$query1->execute(); 

}


if($_POST['section'] == 'fg_opening_stock' ){
    $conn_me->beginTransaction();

try {
    $qry = $conn_me->prepare("SELECT * FROM fg_opening_stock WHERE id = ?");
    $qry->execute([$_POST['ID']]);
    $fetch = $qry->fetch(PDO::FETCH_ASSOC);

    if (!$fetch) throw new Exception('Opening stock not found' . $_POST['ID']);

    $qry2 = $conn_me->prepare("
        SELECT * FROM balance_product 
        WHERE product_id = ? AND warehouse_id = ? AND date = ?
    ");
    $qry2->execute([
        $fetch['product_id'],
        $fetch['warehouse_id'],
        $fetch['invoice_date']
    ]);
    $fetch2 = $qry2->fetch(PDO::FETCH_ASSOC);

    if (!$fetch2) throw new Exception('Balance product not found');

    $new_stock = $fetch2['stock_in'] - $_POST['in_amount'];
    if ($new_stock < 0) throw new Exception('Negative stock');

    $upd = $conn_me->prepare("UPDATE balance_product SET stock_in = ? WHERE id = ?");
    $upd->execute([$new_stock, $fetch2['id']]);

    $del = $conn_me->prepare("DELETE FROM fg_opening_stock WHERE id = ?");
    $del->execute([$_POST['ID']]);

    $conn_me->commit();

} catch (Exception $e) {
    $conn_me->rollBack();
    echo $e->getMessage();
}

}


if($_POST['section'] == 'fg_damage_store' ){
    $conn_me->beginTransaction();

try {
    $qry = $conn_me->prepare("SELECT * FROM fg_damage_store WHERE id = ?");
    $qry->execute([$_POST['ID']]);
    $fetch = $qry->fetch(PDO::FETCH_ASSOC);

    if (!$fetch) throw new Exception('Damage stock not found' . $_POST['ID']);

    $qry2 = $conn_me->prepare("
        SELECT * FROM balance_product 
        WHERE product_id = ? AND warehouse_id = ? AND date = ?
    ");
    $qry2->execute([
        $fetch['product_id'],
        $fetch['warehouse_id'],
        $fetch['invoice_date']
    ]);
    $fetch2 = $qry2->fetch(PDO::FETCH_ASSOC);

    if (!$fetch2) throw new Exception('Product not found');

    $new_stock = $fetch2['stock_out'] - $_POST['out_amount'];
    if ($new_stock < 0) throw new Exception('Negative stock');

    $upd = $conn_me->prepare("UPDATE balance_product SET stock_out = ? WHERE id = ?");
    $upd->execute([$new_stock, $fetch2['id']]);

    $del = $conn_me->prepare("DELETE FROM fg_damage_store WHERE id = ?");
    $del->execute([$_POST['ID']]);

    $conn_me->commit();

} catch (Exception $e) {
    $conn_me->rollBack();
    echo $e->getMessage();
}

}


if($_POST['section'] == 'Transfer_To_fg_warehouse_to_warehouse_transfer'  ||  $_POST['section'] == 'Transfer_From_fg_warehouse_to_warehouse_transfer'  ){
    $conn_me->beginTransaction();

try {


    $qry = $conn_me->prepare("SELECT * FROM fg_warehouse_to_warehouse_transfer WHERE id = ?");
    $qry->execute([$_POST['ID']]);
    $fetch = $qry->fetch(PDO::FETCH_ASSOC);
    if (!$fetch) throw new Exception(' stock not found' . $_POST['ID']);



    $qry2 = $conn_me->prepare("
        SELECT * FROM balance_product 
        WHERE product_id = ? AND warehouse_id = ? AND date = ?
    ");
    $qry2->execute([
        $fetch['product_id'],
        $fetch['TO_warehouse_id'],
        $fetch['invoice_date']
    ]);
    $fetch2 = $qry2->fetch(PDO::FETCH_ASSOC);

    if (!$fetch2) throw new Exception('Product not found');

    $new_stock = $fetch2['stock_in'] - $_POST['in_amount'];

    if ($new_stock < 0) throw new Exception('Negative stock');


    $upd = $conn_me->prepare("UPDATE balance_product SET stock_in = ? WHERE id = ?");
    $upd->execute([$new_stock, $fetch2['id']]);



    $qry2 = $conn_me->prepare("
    SELECT * FROM balance_product 
    WHERE product_id = ? AND warehouse_id = ? AND date = ?
    ");
    $qry2->execute([
    $fetch['product_id'],
    $fetch['FROM_warehouse_id'],
    $fetch['invoice_date']
    ]);
    $fetch2 = $qry2->fetch(PDO::FETCH_ASSOC);

    if (!$fetch2) throw new Exception('Product not found');


    $new_stock = $fetch2['stock_out'] - $_POST['in_amount'];

    if ($new_stock < 0) throw new Exception('Negative stock');




    $upd = $conn_me->prepare("UPDATE balance_product SET stock_out = ? WHERE id = ?");
    $upd->execute([$new_stock, $fetch2['id']]);



    $del = $conn_me->prepare("DELETE FROM fg_warehouse_to_warehouse_transfer WHERE id = ?");
    $del->execute([$_POST['ID']]);

    $conn_me->commit();   

} catch (Exception $e) {
    $conn_me->rollBack();
    echo $e->getMessage();
}

}




print 'success';

}else if ($_POST['action'] == 'convert_to_identified_customer'){




    $query1 = $conn_me->prepare("UPDATE `account_transection` SET `ledger_id` = '2' , `transection_to` = 'Customer' , `transection_head_id` = '66' , `data_inserted_from` = 'CUSTOMER-TRANSACTION'  , `transection_to_id` = '".$_POST['transection_to_id']."' where   `id` = '".$_POST['transection_id']."'  ");
    $query1->execute(); 


$qry = $conn_me->prepare("SELECT * FROM `account_transection` where  `id`  = '".$_POST['transection_id']."' ");
$qry->execute();
$fetch = $qry->fetch(PDO::FETCH_ASSOC);


QUICK_BALANCE::QUICK_OPENING_BALANCE('in_amount',$fetch['transection_by'],$fetch['transection_by_id'],-$fetch['in_amount'],$fetch['transection_date'],$fetch['brunch_id']);
QUICK_BALANCE::CUSTOMER_QUICK_DUE($fetch['transection_to_id'],$fetch['in_amount'],'receive_amount',$fetch['transection_date'],$fetch['brunch_id']);



$query2 = $conn_me->prepare("UPDATE `account_posting_pending` SET `data_confirmed` = 'YES' where   `id` = '".$_POST['posting_transection_id']."'  ");
$query2->execute(); 

if($query2){
    print "Done";
}else{
    print "Error";
}


}else if ($_POST['action'] == 'ledger_wise_accounts'){

    

    $content   = '<select name="transection_head_id" id="transection_head_id" class="form-control select"  data-live-search="true"><option value="">Select One</option>';

$qry = $conn_me->prepare("SELECT * FROM `setup_ac_head` where ( `account_type`  = '".$_POST['type']."' OR  `account_type`  = 'BOTH' )  AND  `ledger_id`  = '".$_POST['ledger_id']."' ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) {

 $content   .= ' <option value="'.$fetch['id'].'">'.$fetch['account_head'].'</option>';

} 
$content   .= '</select>';

print $content ; 



}else if ($_POST['action'] == 'find_recipe'){

    $qry = $conn_me->prepare("SELECT * FROM `{$_POST['WHICH']}` WHERE `{$_POST['FIELD']}` = '".$_POST['VALUE']."' ORDER BY `id` ASC");
    $qry->execute();
$count = $qry->rowCount();
if($count > 0 ){
$mess ="Recipe Alredy Setup";
$value= "1";
}else{
$mess =""; 
$value= "0";
}

print json_encode(array ('mess' => $mess,'value' => $value));




}else if ($_POST['action'] == 'get_pending_post'){

    

if($_POST['transection_type'] == 'BOTH' ){
$QUERY = "";
}else{
$QUERY = " A.`transection_type` = '".$_POST['transection_type']."'  AND ";
}
$content = '';

$content = '<div class="row mydivclass">
<div class="col-md-12">
<input type="button" class="btn btn-success pull-right" value="POST ALL" onclick="POST_DATA(\'All\');">

    <div class="panel panel-default">
        <div class="panel-body" id="load_table">
       <div class="table-responsive">
    <table class="table  table-hover table-condensed table-striped table-bordered datatable" style="white-space:nowrap;" id="myDatatable">
        <thead>';

$content .='<tr>
<td>Sl</td>
<td>Tr.Id</td>
<td>Tr. Date</td>
<td>Tr. Type</td>
<td>Led. Head</td>
<td>Acc. Head</td>
<td>Note</td>
<td>Receive</td>
<td>Payment</td>
<td>Post By</td>
<td>Action</td>';



$content .='</thead>
<tbody>
';
$sl=1;
$count = 0;
$total_in = 0 ;
$total_out = 0;
$query1 = $conn_me->prepare("SELECT A.*,B.`account_head`,C.`name`
FROM `account_posting_pending` A 
JOIN `setup_ac_head` B ON (A.`transection_head_id` = B.`id`)
JOIN `setup_ladger_head` C ON (A.`ledger_id` = C.`id`)

where A.`poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND $QUERY A.`data_inserted_from` = '".$_POST['data_inserted_from']."'  AND ( A.`posting_status` = 'Pending' OR A.`data_confirmed` = 'NO')  "); 
$query1->execute();
$count = $query1->rowCount();
    $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
    foreach($fetch_list1 AS $fetch1){ 
        $count = $count + 1;

        $poster_info = SETUP::ADMIN_SETUP($fetch1['poster']);

       if( $fetch1['transection_by']  == 'Bank'){

        $info = SETUP::BANK_SETUP($fetch1['transection_by_id']);
        $details =  $info['bank_name'] . ' ' .  $info['brunch_name'] .  $info['account_number'] ; 
       }else if ($fetch1['transection_by']  == 'Mobile Banking'){

        $info = SETUP::BANK_MOBILE_BANKING($fetch1['transection_by_id']);
        $details =  $inf['mobile_bank_name'] . ' ' .  $info['mobile_number']  ; 

       }else if ($fetch1['transection_by']  == 'Cash'){
        $details =  'Cash';
       }else{
        $details =  '';
       }


       $LINK = "Account/" . $_POST['link'] . "/New" ;


       if($fetch1['transection_to'] == 'Customer' ){
           
        $ledger = "Customer Transaction";
        $info_customer = SETUP::SETUP_CUSTOMER($fetch1['transection_to_id']);
        $ac_head =  '<b>Shop Name: </b>'.  $info_customer['shop_name'] . ' <br><b>Customer Name : </b>' .  $info_customer['customer_name'] . '<br><b>Mobile: </b>' .  $info_customer['mobile'];

       }else{

        $ledger = $fetch1['name'];
        $ac_head =$fetch1['account_head'];
       }

        $content .= ' <input value="'.$fetch1['id'].'"  type="text" style="display:none" name="tr_id" id="tr_id'.$count.'"  class="form-control tr_id"/>';

        $transection_date = date("d-m-Y", strtotime($fetch1['transection_date']));


    $content .='<tr>
    <td>'.$sl++.'</td>

    <td>'.$fetch1['invoice_no'].'</td>
    <td>'.$transection_date.'</td>
    <td>'.$details . '</td>
    <td>'.$ledger.'</td>
    <td>'.$ac_head.'</td>
    <td>'.$fetch1['note'].'</td>';



        $content .='<td>'.$fetch1['in_amount'].'</td>';
        $content .='<td>'.$fetch1['out_amount'].'</td>';

 
  
$content .='<td>'.$poster_info['hr_name'].'</td>';

$content .='<td>';



$content .='<a target="_BLINK" href="money_recipt.php?status=Pending&id='.$fetch1['id'].'" class="btn btn-warning"><i class="glyphicon glyphicon-print
"></i><a>';

if($fetch1['posting_status'] == 'Done' ){

    $content .='<button type="button" class="btn btn-danger btn-rounded btn-sm" id="#exampleModalCenter" onclick="modal_wihout_refresh(\'IDENTIFY-CUSTOMER\',\'tr_id'.$count.'\',\'Identify Customer\');"><i class="fa fa-info-circle"></i></button>';

}else{
    $content .='<input type="button" class="btn btn-danger" value="x" onclick="delete_cart_row(\''.$LINK.'\',\'account_posting_pending\',\''.$fetch1['id'].'\',\'Yes\');">
    <a href="Account/'.$_POST['link'].'/'.$fetch1['id'].'" class="btn btn-warning"><i class="fa fa-edit danger"></i><a>
    <input type="button" class="btn btn-success" value="P O S T" onclick="POST_DATA(\''.$fetch1['id'].'\');">';
}


$content .='</td>';

$total_in += $fetch1['in_amount'] ;
$total_out += $fetch1['out_amount'];    


}



    $content .=' </tr>';

    
  


$content .= '</tbody> 
<tfoot>
<tr>
    <th colspan="7" style="text-align:right"><b>Total</b></th>
    <th>' . $total_in . '</th>
    <th>' . $total_out . '</th>
    <th></th>   <th></th>
</tr>
</tfoot>

</table>
</div>
</div>      
</div>

</div>
</div>';

    print json_encode(array ('content' => $content));



}else if ($_POST['action'] == 'POST_DATA'){


$content1 =  CRUD::copyDataFromSetupAtoSetupB($_POST['ID'],$_POST['transaction_type'],$_POST['data_inserted_from']);
$content2 = FIND::PENDING_TRANSACTION_POST($_POST['transaction_type'],$_POST['data_inserted_from']);

print json_encode(array ('content1' => $content1,'content2' => $content2));

}else if ($_POST['action'] == 'ledger_wise_accounts'){


    $content   = '<select name="transection_head_id" id="transection_head_id" class="form-control select"  data-live-search="true"><option value="">Select One</option>';

$qry = $conn_me->prepare("SELECT * FROM `setup_ac_head` where ( `account_type`  = '".$_POST['type']."' OR  `account_type`  = 'BOTH' )  AND  `ledger_id`  = '".$_POST['ledger_id']."' ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) {

 $content   .= ' <option value="'.$fetch['id'].'">'.$fetch['account_head'].'</option>';

} 
$content   .= '</select>';

print $content ; 




}else if($_POST['action'] == 'find_purchase_invoice_by_no' ){
    

$content = '';

$content .= ' <div class="panel-body panel-body-table">
                            
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th width="33.33%">Invoice</th>
                <th width="33.33%">Supplier</th>
                <th width="33.33%">Action</th>
            </tr>
        </thead>
        <tbody>';


$invoice_no = $_POST['INVOICENO'];

if ($invoice_no === '') {
    print json_encode(['content' => '<div class="alert alert-warning">Please enter an invoice number.</div>']);
    exit;
}


$invoice_no = preg_quote($invoice_no, '/');

        $sl =1;
        $count1 = 0;

        $qry = $conn_me->prepare("
        SELECT A.*, DATE_FORMAT(A.`invoice_date`, '%d-%m-%Y') AS `invoicedate`, B.`supplier_name`, B.`mobile`
        FROM `fg_local_purches` A
        JOIN `setup_supplier` B ON A.`supplier_id` = B.`id`
        WHERE A.`invoice_no` REGEXP :inv_no OR A.`supplier_bill_no` REGEXP :inv_no
                GROUP BY A.`invoice_no`
        ");
        $qry->execute([ 'inv_no' => $invoice_no ]);
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);

            foreach($fetch_list AS $fetch) {
                $count1 = $count1+1;

            $content .= '<tr><input type="text" style="display:none" id="purchase_id_'.$count1.'" value="'.$fetch['code'].'" >

                <td>Invoice No. <strong>'.$fetch['invoice_no'].'</strong><br>Invoice Date. <strong>'.$fetch['invoicedate'].'</strong></td>
                <td>Name: ' . $fetch['supplier_name'].' <br>Mobile: '.$fetch['mobile'].'<br>Bill: '.$fetch['supplier_bill_no'].'</td>
                <td>';

$content .= '<button type="button" class="btn btn-info btn-rounded btn-sm" id="#exampleModalCenter" onclick="modal_wihout_refresh(\'load_data_in_modal\',\'purchase_id_'.$count1.'\',\'Return FG Local Purches\');"><i class="fa fa-eye"></i></button>';

                $content .= '</td>
            </tr>';
            }                                     
        
            $content .= '</tbody>
    </table>
</div>

</div>';


    print json_encode(array ('content' => $content));



}else if ($_POST['action'] == 'find_transection_due'){

    if($_POST['TYPE'] == 'Customer' ){


        if($_POST['SECTION'] == 'Brunch_Wise' ){
            $brunch_id  = $_SESSION['USER_BRUNCH'] ;
        }else{
            $brunch_id = 'All';
        }


$info_due = FIND::getAllCustomerDues('Brunch-Wise-Single-Customer-Wise',$_POST['ID'],date('Y-m-d'),$brunch_id);

$due = $info_due[0]['customer_due'];


    }else if($_POST['TYPE'] == 'Supplier' ){

        $info_due = FIND::SUPPLIER_DUE($_POST['ID']);
        $due = $info_due['supplier_due'];
    }else{
        $due = '0.00';
    }

print json_encode(array ('due' => $due));

}else if ($_POST['action'] == 'find_transection_to_details'){


    $content   ='';
    $content   .= '<tr>
    <th >'.$_POST['TYPE'].' List</th>
    <td colspan="4">
    <select class="form-control select" id="transection_to_id" name = "transection_to_id"  onchange="findTransectionDue(this.value,\''.$_POST['TYPE'].'\',\'Brunch_Wise\');"  data-live-search="true">
    <option value="">Select One</option>';

    if($_POST['TYPE'] == 'Supplier'){

        $qry = $conn_me->prepare("SELECT * FROM `setup_supplier` ORDER BY `id` ASC");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) { 
            $supplier_details = "$fetch[mobile] :: $fetch[supplier_name]";
            $content .= '<option ';
            if($_POST['transection_to_id'] == $fetch['id']){
                $content .= ' selected="selected"';
            }else{

            }
            $content .= ' value="'.$fetch['id'].'"><b style="font-color:blue">' .$supplier_details. '</b></option>';

        }

    }else if($_POST['TYPE'] == 'Employee'){

        $qry = $conn_me->prepare("SELECT * FROM `setup_employee`  ORDER BY `id` ASC");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) { 
            $employee_details = "$fetch[code] :: $fetch[name] ";
            $content .= '<option value="'.$fetch['id'].'"><b style="font-color:blue">' .$employee_details. '</b></option>';

        }
    }else{
        $content   .= '';
    }

    $content .='</select>

    </select>
</td>



</tr>';


    print json_encode(array ('content' => $content));


}else if ($_POST['action'] == 'create_pagination'){

    $content = '';
$qry = $conn_me->prepare("Call pagenationTable('".$_POST['VALUE']."')");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach($fetch_list AS $fetch){
    $content .= '<br>'. $fetch['product_name'] . '<br>';
} 

print $content;


}else if ($_POST['action'] == 'find_today_balance'){


$info_balance = QUICK_BALANCE::TODAY_BALANCE($_POST['transection_by'],$_POST['transection_by_id']);
print $info_balance['BALANCE'];


}else if ($_POST['action'] == 'find_transection_by_details'){




    $content   ='';
    $content2   ='';
    if($_POST['TYPE'] == 'Bank'){

        $content   .= '<tr>
        <th >Bank Details </th>
        <td colspan="4">
        <select class="form-control select" id="transection_by_id"  name = "transection_by_id"  data-live-search="true">
        <option value="">Select One</option>';
        $qry = $conn_me->prepare("SELECT * FROM `setup_bank` where `status`  = 'Active'   ORDER BY `id` ASC");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) { 
            $bank_details = "$fetch[account_number] :: $fetch[brunch_name] :: $fetch[bank_name]";
            $content .= '<option ';

            if($_POST['transection_by_id'] == $fetch['id']){
                $content .= ' selected="selected"';
            }else{

            }
            $content .= 'value="'.$fetch['id'].'"><b style="font-color:blue">qq' .$bank_details. '</b></option>';

        }
    $content .='</select>

            </select>
        </td>
       


   </tr>';

   
   $content2   .= '<tr>
   <th>Cheque/Transaction No</th>
   <td><input type="text" class="form-control" id="check_number" value="'.$_POST['check_number'].'" ></td>
  

   <th >Cheque/Transaction Date</th>
   <td><input type="date" class="form-control" id="check_date" value="'.$_POST['check_date'].'" ></td> 

</tr>';

    }else if ($_POST['TYPE'] == 'Mobile-Banking'){
 
        $content   .= '<tr>
        <th >Number  (Today Balance: <b style="color:red;" id="todaybalance"></b> /-)</th>

        <td colspan="4">
        <select class="form-control select" onchange="todayBalance(\'Mobile\',this.value);" id="transection_by_id" name = "transection_by_id"  data-live-search="true">
        <option value="">Select One</option>';
        $qry = $conn_me->prepare("SELECT * FROM `setup_mobile_banking` where `status`  = 'Active'   ORDER BY `id` ASC");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) { 
            $bank_details = "$fetch[mobile_number] :: $fetch[mobile_bank_name] ";
            $content .= '<option ';
            
            if($_POST['transection_by_id'] == $fetch['id']){
                $content .= ' selected="selected"';
            }else{

            }
            $content .=' value="'.$fetch['id'].'"><b style="font-color:blue">' .$bank_details. '</b></option>';

        }
    $content .='</select>

            </select>
        </td>
       
        <input type="hidden" class="form-control" id="check_number" value="" >
        <input type="hidden" class="form-control" id="check_date" value="" >
   </tr>';


    }else if($_POST['TYPE'] == 'Cash'){
        $content   .= '
        <input type="hidden" class="form-control" id="check_number" value="" >
        <input type="hidden" class="form-control" id="check_date" value="" >
        <input type="hidden" class="form-control" id="transection_by_id" value="" >';

    }else{
        $content   .= '';
    }


    print json_encode(array ('content' => $content,'content2' => $content2));


}else if ($_POST['action'] == 'ck_if_sub_ac'){
    $content   ='';
    if($_POST['TYPE'] == 'YES'){

        $content .= '<div class="form-group">
        <label class="col-md-3 col-xs-12 control-label">Parent Head</label>
        <div class="col-md-6 col-xs-12">    ';                                                                                       
        $content .= '<select name="parent_id" id="parent_id" class="form-control select"  data-live-search="true"><option value="">Select One</option>';
        $qry = $conn_me->prepare("SELECT * FROM `setup_ac_head` ");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) { 
            $content .= '<option value="'.$fetch['id'].'"><b style="font-color:blue">' .$fetch['account_head']. '</b></option>';
    
    
        }
        $content .='</select>
        </div>
    </div>';

    }else{
 $content .='<input type="hidden" name="parent_id" id="parent_id" value="0">';
    }
   



print $content;
    

}else if ($_POST['action'] == 'find_purchase_price'){

    $query = $conn_me->prepare("  SELECT AVG(`purches_price`) AS `price` from `raw_local_purches` where `product_id` = '".$_POST['product_id']."' ");
    $query->execute();
    $fetch_query = $query->fetch(PDO::FETCH_ASSOC);

    if(!empty($fetch_query['price'])){
        $price =  number_format((float)$fetch_query['price'], 2, '.', '') ;

    }else{
        $price = 0.00;
    }
  


    print json_encode(array ('price' => $price));



}else if ($_POST['action'] == 'find_stock'){

if($_POST['TYPE'] == 'RAW'){

    $stock = STOCK::RAW_ITEM_WISE_STOCK('',$_POST['ID'],'product_wise');

    $b = '<select id="from_warehouse_id" name="from_warehouse_id"   class="form-control select" data-live-search="true">';
    $a = FIND::WAREHOUSE_LIST('RAW',$_POST['ID'],'');
    $b .= $a['warehouse_list'];
    $b .= '</select>';

    $retail_price = '0.00';
    $wholesale_price = '0.00';
    $content = '';


        $ITEM_STOCK_CARTOON = 0.00;
    

}else if($_POST['TYPE'] == 'FG'){

    $pipelinestock = FIND::PIPE_LINE_WISE_STOCK($_POST['BRUNCH_ID'],$_POST['TYPE'],$_POST['ID']);


    $stock = STOCK::FG_ITEM_WISE_STOCK($_POST['BRUNCH_ID'],$_POST['ID'],'unique_brunch_wise');
    $price_info = SETUP::SETUP_PRODUCT($_POST['ID']);

    $b = '<select id="from_warehouse_id" name="from_warehouse_id"  tabindex="3"   class="form-control select" data-live-search="true">';
    $a = FIND::WAREHOUSE_LIST('FG',$_POST['ID'],'');
    $b .= $a['warehouse_list'];
    $b .= '</select>';
    $retail_price = $price_info['sales_rate'];
    $wholesale_price =  $price_info['wholesale_rate'];
    $content = $a['warehouse_content'];

    if($price_info['pcs_in_cartoon'] > 0){
            
        $ITEM_STOCK_CARTOON = $stock['ITEM_STOCK']/$price_info['pcs_in_cartoon'];
        }else{
        $ITEM_STOCK_CARTOON = 0.00;
        }


    $saleable = $stock['ITEM_STOCK'] - $pipelinestock['pipe_line_stock'];
     
    

     
}else{
    $retail_price = '0.00';
    $wholesale_price = '0.00';
    $content = '';

}


print json_encode(array 
(
    'stock_pcs' => "$stock[ITEM_STOCK]",
    'pipe_line_stock' => $pipelinestock['pipe_line_stock'],
    'stock_carton' => "$ITEM_STOCK_CARTOON",
    'saleable' => "$saleable",
    'item_stock' => "Pcs-$stock[ITEM_STOCK] Carton $ITEM_STOCK_CARTOON",
    'warehouse_list' => $b ,
    'retail_price' => $retail_price ,
    'wholesale_price' => $wholesale_price,
    'stock_list' => $content
));


}else if ($_POST['action'] == 'FINAL_RECIPE_WISE_PRINT'){



    $accepting_delivery_date = date("Y-m-d", strtotime($_POST['accepting_delivery_date']));  

    $query1 = $conn_me->prepare("UPDATE `raw_print` SET 
    `status` = 'Done',
    `note` = '".$_POST['note']."',
    `send_to` = '".$_POST['send_to']."',
    `accepting_delivery_date` = '".$accepting_delivery_date."',
    `supplier_or_factory_id` = '".$_POST['send_to_id']."'

    where   `code` = '".$_POST['invoice_code']."'  ");
    $query1->execute(); 

    $query1 = $conn_me->prepare("UPDATE `raw_print_item` SET `status` = 'Done' where   `demand_code` = '".$_POST['invoice_code']."'  ");
    $query1->execute(); 



    if ($query1) {

        print json_encode(array ('mess' => 'Update Success' , 'code' => $_POST['invoice_code']));
    } 
    else {
        print json_encode(array ('mess' => 'Update Failed' , 'code' => $_POST['invoice_code']));
    }

}else if ($_POST['action'] == 'search_product_wise'){

    $value = preg_quote($_POST['value'], '/');


    $contemt = ' <div class="panel-body"><ul class="list-group border-bottom" id="listofItems">';
        $placeStart=1;

        $qry = $conn_me->prepare("SELECT A.*, B.`category`, B.`image`  
FROM `setup_product` A 
JOIN `setup_category` B ON (A.`category_id` = B.`id`)
WHERE 
    A.`in_service` = 'checked' AND (
    A.`code` REGEXP :value OR 
    A.`product_name` REGEXP :value OR 
    CONVERT(B.`category` USING utf8) REGEXP :value
);
");
        $qry->bindValue(':value', $value, PDO::PARAM_STR);
        $qry->execute();



        if($qry->rowCount() > 0 ){

            $contemt .= '<li  class="list-group-item" style="text-align:center;display:none"><b class="text-muted fs-6">Search by product name , category name </b></li>';

        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) {
                $placeholder = $placeStart++;

            $contemt .= '<input type="hidden" id="get_product_id'.$placeholder.'" value="'.$fetch['id'].'">';
            $contemt .= '<input type="hidden" id="get_product_name'.$placeholder.'" value="P'.$fetch['code'].' '.$fetch['product_name'].'">';
            
            $contemt .= '<li id="itemID'.$placeholder.'" style="cursor: pointer;padding:3px;font-size:13px" class="list-group-item" data-placeholder="'.$placeholder.'">
            <a  onclick="putonsearchbar(\'get_product_id'.$placeholder.'\',\'get_product_name'.$placeholder.'\')">
             <b class="text-danger">'.$fetch['product_name'].'</b> <b class="text-info"> '.$fetch['category'].' </b> <b class="text-success"> P'.$fetch['code'].'</b>
             </a>
             </li>';

            
              
            
         
         
        }
        
    }else{
        $contemt .= '<li><a ><span class="fa fa-tag"></span> <b class="text-danger">No Data Found</b></a></li>';
    
    }
    
        $contemt .= '</ul>
        </div>';
    
        print $contemt ; 

}else if ($_POST['action'] == 'FINAL_RECIPE_WISE_SPRAY'){

    $accepting_delivery_date = date("Y-m-d", strtotime($_POST['accepting_delivery_date']));  

    $query1 = $conn_me->prepare("UPDATE `raw_spray` SET 
        `status` = 'Done',
        `note` = '".$_POST['note']."',
        `send_to` = '".$_POST['send_to']."',
        `accepting_delivery_date` = '".$accepting_delivery_date."',
        `supplier_or_factory_id` = '".$_POST['send_to_id']."'
    
     where   `code` = '".$_POST['invoice_code']."'  ");
    $query1->execute(); 

    $query1 = $conn_me->prepare("UPDATE `raw_spray_item` SET `status` = 'Done' where   `demand_code` = '".$_POST['invoice_code']."'  ");
    $query1->execute(); 

    if ($query1) {

        print json_encode(array ('mess' => 'Update Success' , 'code' => $_POST['invoice_code']));
    } 
    else {
        print json_encode(array ('mess' => 'Update Failed' , 'code' => $_POST['invoice_code']));
    }


}else if ($_POST['action'] == 'FINAL_RECIPE_WISE_MOLD'){
    
    $accepting_delivery_date = date("Y-m-d", strtotime($_POST['accepting_delivery_date']));  

    $query1 = $conn_me->prepare("UPDATE `raw_molding` SET 
        `status` = 'Done',
        `note` = '".$_POST['note']."',
        `send_to` = '".$_POST['molding_type']."',
        `accepting_delivery_date` = '".$accepting_delivery_date."',
        `supplier_or_factory_id` = '".$_POST['send_to_id']."'
    
     where   `code` = '".$_POST['invoice_code']."'  ");
    $query1->execute(); 

    $query1 = $conn_me->prepare("UPDATE `raw_molding_item` SET `status` = 'Done' where   `demand_code` = '".$_POST['invoice_code']."'  ");
    $query1->execute(); 



if ($query1) {

    print json_encode(array ('mess' => 'Update Success' , 'code' => $_POST['invoice_code']));
} 
else {
    print json_encode(array ('mess' => 'Update Failed' , 'code' => $_POST['invoice_code']));
}

}else if ($_POST['action'] == 'FINAL_RECIPE_WISE_DEMAND'){

    $query1 = $conn_me->prepare("UPDATE `raw_request_recipe_wise` SET `status` = 'Done' where   `code` = '".$_POST['invoice_code']."'  ");
    $query1->execute(); 

    $query1 = $conn_me->prepare("UPDATE `raw_request_recipe_wise_item` SET `status` = 'Done' where   `demand_code` = '".$_POST['invoice_code']."'  ");
    $query1->execute(); 



print "Final Success";



}else if ($_POST['action'] == 'PRINT_Recipe_Load'){

    $content = '';


    $content .= '<div id="table-scroll">
        <div class="table-responsive">
            <table id="mytable" class="table" style="white-space:nowrap;">
                <thead>';
                $product_info = SETUP::SETUP_RAW_MATERIAL($_POST['print_material_id']);
                $content .= '<tr style="background-color:#941d63"><td style="color:white;text-align:center"  colspan="6">*** Recipe for '.$product_info['product_name'].' ***</td></tr>
                    <tr>

                        <th> Sl</th>
                        <th>Raw Material</th>
                        <th>Recipe Qty</th>
                        <th>Total Quantity</th>
                        <th>Stock</th>
                        <th>Demand</th>

                    </tr>
                </thead>
                <tbody>';

              

                        $count = 0;
                         $sl = 1;
                        $query = $conn_me->prepare("SELECT *  FROM `receip_print`  where `print_material_id` = '".$_POST['print_material_id']."' ORDER BY `id` DESC");
                        $query->execute();
                        $fetch_query = $query->fetchAll(PDO::FETCH_ASSOC);
                        $rowCount =$query->rowCount();
                    

                        foreach($fetch_query AS $fetch){
                            $count = $count + 1;
                            $CART_DATA = SETUP::SETUP_PRINT_RECIPE($fetch['id']);

                            $recipe_qty = number_format(($fetch['quantity']),2);
                            $qty_muliply_by_batch = number_format(($fetch['quantity']*$_POST['batch_quantity']),2);
                            $stock = STOCK::RAW_ITEM_WISE_STOCK('',$fetch['raw_material_id'],'product_wise');


                        $content .= '<tr class="tr'. $count .'">
                            <td>'.$sl++.'<input type="hidden" name="print_material_id[]" id="print_material_id'.$count.'" data-srno="'.$count.'" class="form-control print_material_id" value="'.$fetch['print_material_id'].'"/></td>
                            <td>'.$CART_DATA['raw_material_name'].'<input type="hidden" name="material_id[]" id="material_id'.$count.'" data-srno="'.$count.'" class="form-control material_id" value="'.$fetch['raw_material_id'].'"/></td>
                            <td>'.$recipe_qty . ' ' . $CART_DATA['raw_material_unit'].' </td>
                            <td>'.$qty_muliply_by_batch. ' ' . $CART_DATA['raw_material_unit'].' </td>
                            <td>'.$stock['ITEM_STOCK']. ' ' . $CART_DATA['raw_material_unit'].' </td>
                            <td><input type="number" name="demand_qty[]" id="demand_qty'.$count.'" data-srno="'.$count.'" class="form-control demand_qty" value="'.$qty_muliply_by_batch.'"/>

                             </tr>';

                   
                             }
                             $content .= '<input type="hidden" name="total_item" id="total_item" value="'.$rowCount.'"/>';
          
                             $content .= '</tbody></table>
        </div>';

        $content .='</div>
<input type="hidden" name="count_cart" id="count_cart" value="">';
    
print $content ;


}else if ($_POST['action'] == 'SPREY_Recipe_Load'){


    $content = '';


    $content .= '<div id="table-scroll">
        <div class="table-responsive">
            <table id="mytable" class="table" style="white-space:nowrap;">
                <thead>';
                $product_info = SETUP::SETUP_RAW_MATERIAL($_POST['spray_material_id']);
                $content .= '<tr style="background-color:#941d63"><td style="color:white;text-align:center"  colspan="6">*** Recipe for '.$product_info['product_name'].' ***</td></tr>
                    <tr>

                        <th> Sl</th>
                        <th>Raw Material</th>
                        <th>Recipe Qty</th>
                        <th>Total Quantity</th>
                        <th>Stock</th>
                        <th>Demand</th>

                    </tr>
                </thead>
                <tbody>';

              

                        $count = 0;
                         $sl = 1;
                        $query = $conn_me->prepare("SELECT *  FROM `receip_spray`  where `spray_material_id` = '".$_POST['spray_material_id']."' ORDER BY `id` DESC");
                        $query->execute();
                        $fetch_query = $query->fetchAll(PDO::FETCH_ASSOC);
                        $rowCount =$query->rowCount();
                    

                        foreach($fetch_query AS $fetch){
                            $count = $count + 1;
                            $CART_DATA = SETUP::SETUP_SPRAY_RECIPE($fetch['id']);

                            $recipe_qty = number_format(($fetch['quantity']),2);
                            $qty_muliply_by_batch = number_format(($fetch['quantity']*$_POST['batch_quantity']),2);
                            $stock = STOCK::RAW_ITEM_WISE_STOCK('',$fetch['raw_material_id'],'product_wise');


                        $content .= '<tr class="tr'. $count .'">
                            <td>'.$sl++.'<input type="hidden" name="spray_material_id[]" id="spray_material_id'.$count.'" data-srno="'.$count.'" class="form-control spray_material_id" value="'.$fetch['spray_material_id'].'"/></td>
                            <td>'.$CART_DATA['raw_material_name'].'<input type="hidden" name="material_id[]" id="material_id'.$count.'" data-srno="'.$count.'" class="form-control material_id" value="'.$fetch['raw_material_id'].'"/></td>
                            <td>'.$recipe_qty . ' ' . $CART_DATA['raw_material_unit'].' </td>
                            <td>'.$qty_muliply_by_batch. ' ' . $CART_DATA['raw_material_unit'].' </td>
                            <td>'.$stock['ITEM_STOCK']. ' ' . $CART_DATA['raw_material_unit'].' </td>
                            <td><input type="number" name="demand_qty[]" id="demand_qty'.$count.'" data-srno="'.$count.'" class="form-control demand_qty" value="'.$qty_muliply_by_batch.'"/>

                             </tr>';

                   
                             }
                             $content .= '<input type="hidden" name="total_item" id="total_item" value="'.$rowCount.'"/>';
          
                             $content .= '</tbody></table>
        </div>';

        $content .='</div>
<input type="hidden" name="count_cart" id="count_cart" value="">';
    
print $content ;


}else if ($_POST['action'] == 'SUPPORTING_Recipe_Load'){


    $content = '';


    $content .= '<div id="table-scroll">
        <div class="table-responsive">
            <table id="mytable" class="table" style="white-space:nowrap;">
                <thead>';
                $product_info = SETUP::SETUP_RAW_MATERIAL($_POST['supporting_id']);
                $content .= '<tr style="background-color:#941d63"><td style="color:white;text-align:center"  colspan="6">*** Recipe for '.$product_info['product_name'].' ***</td></tr>
                    <tr>

                        <th> Sl</th>
                        <th>Raw Material</th>
                        <th>Recipe Qty</th>
                        <th>Total Quantity</th>
                        <th>Stock</th>
                        <th>Demand</th>

                    </tr>
                </thead>
                <tbody>';

              

                        $count = 0;
                         $sl = 1;
                        $query = $conn_me->prepare("SELECT *  FROM `receip_supporting_goods`  where `supporting_id` = '".$_POST['supporting_id']."' ORDER BY `id` DESC");
                        $query->execute();
                        $fetch_query = $query->fetchAll(PDO::FETCH_ASSOC);
                        $rowCount =$query->rowCount();
                    

                        foreach($fetch_query AS $fetch){
                            $count = $count + 1;
                            $CART_DATA = SETUP::SETUP_SUPPORTING_RECIPE($fetch['id']);

                            $recipe_qty = $fetch['quantity'];
                            $qty_muliply_by_batch = $fetch['quantity']*$_POST['batch_quantity'];
                            $stock = STOCK::RAW_ITEM_WISE_STOCK('',$fetch['raw_material_id'],'product_wise');


                        $content .= '<tr class="tr'. $count .'">
                            <td>'.$sl++.'<input type="hidden" name="supporting_id[]" id="supporting_id'.$count.'" data-srno="'.$count.'" class="form-control supporting_id" value="'.$fetch['supporting_id'].'"/></td>
                            <td>'.$CART_DATA['raw_material_name'].'<input type="hidden" name="material_id[]" id="material_id'.$count.'" data-srno="'.$count.'" class="form-control material_id" value="'.$fetch['raw_material_id'].'"/></td>
                            <td>'.$recipe_qty . ' ' . $CART_DATA['raw_material_unit'].' </td>
                            <td>'.$qty_muliply_by_batch. ' ' . $CART_DATA['raw_material_unit'].' </td>
                            <td>'.$stock['ITEM_STOCK']. ' ' . $CART_DATA['raw_material_unit'].' </td>
                            <td><input type="number" name="demand_qty[]" id="demand_qty'.$count.'" data-srno="'.$count.'" class="form-control demand_qty" value="'.$qty_muliply_by_batch.'"/>

                             </tr>';

                   
                             }
                             $content .= '<input type="hidden" name="total_item" id="total_item" value="'.$rowCount.'"/>';
          
                             $content .= '</tbody></table>
        </div>';

        $content .='</div>
<input type="hidden" name="count_cart" id="count_cart" value="">';
    
print $content ;

}else if ($_POST['action'] == 'SUPPLIER_WISE_PENDING_PRINT'){

    $product_info = FIND::SUPPLIER_WISE_PENDING_PRINT($_POST['ID']);
    
    print $product_info['pending'] ;

}else if ($_POST['action'] == 'SUPPLIER_WISE_PENDING_SPRAY'){
    $product_info = FIND::SUPPLIER_WISE_PENDING_SPRAY($_POST['ID']);
    print $product_info['pending'] ;
}else if ($_POST['action'] == 'SUPPLIER_WISE_PENDING_MOLDING'){
    $product_info = FIND::SUPPLIER_WISE_PENDING_MOLDING($_POST['ID']);
    print $product_info['pending'] ;
}else if ($_POST['action'] == 'SUPPORTING_MATERIAL_DEMAND_FROM_PRODUCTION'){
    $product_info = FIND::SUPPORTING_MATERIAL_DEMAND_FROM_PRODUCTION($_POST['ID']);
    print $product_info['demand'] ;
}else if ($_POST['action'] == 'find_raw_material_unit'){
    $product_info = SETUP::SETUP_RAW_MATERIAL($_POST['VALUE']);
    print $product_info['unit'] ;
}else if ($_POST['action'] == 'find_unit'){

    if($_POST['purches_type'] == 'fg_local_purches' ){
        $product_info = SETUP::SETUP_PRODUCT($_POST['VALUE']);
        print $product_info['unit'];
    }else{
        $product_info = SETUP::SETUP_RAW_MATERIAL($_POST['VALUE']);
        print $product_info['unit'];
    }


    
}else if ($_POST['action'] == 'give_main_menu_access_to_user'){

    
    $ck3 = $conn_me->prepare("SELECT *  FROM `menu_list` WHERE `section` = '".$_POST['section']."' ");
    $ck3->execute();
    $fetch_ck3 = $ck3->fetchAll(PDO::FETCH_ASSOC);
    foreach($fetch_ck3 AS $fetch_ck_run3){
      if($_POST['view_check'] == ''){
        $query = $conn_me->prepare("DELETE FROM `menu_permission` WHERE `employee_id` = '".$_POST['employee_id']."' AND `menu_id` = '".$fetch_ck_run3['id']."' "); 
        $query->execute();
    }else{
        $query1 = $conn_me->exec("INSERT INTO `menu_permission` 
        ( 
            `id` , `employee_id` , `menu_id`,`view_check`,`edit_check`,`date`, `time`, `poster`, `lastupdate`
        
        ) 
        VALUES
        (
            '0',
            '".$_POST['employee_id']."',
            '".$fetch_ck_run3['id']."',
            '".$_POST['view_check']."',
            '".$_POST['view_check']."',
            '" . date("Y-m-d") . "',
            '" . date("h:i:s a") . "',
            '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
            '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
        ) ");

       


        
      }


    }


    print "All Menu Permission Changed";

}else if ($_POST['action'] == 'give_single_menu_access_to_user'){



    if($_POST['view_check'] == ''){

        $query = $conn_me->prepare("DELETE FROM `menu_permission` WHERE `employee_id` = '".$_POST['employee_id']."' AND `menu_id` = '".$_POST['menu_id']."' "); 
        $query->execute();

    }else{


    $query1 = $conn_me->exec("INSERT INTO `menu_permission` 
    ( 
        `id` , `employee_id` , `menu_id`,`view_check`,`edit_check`,`date`, `time`, `poster`, `lastupdate`
    
    ) 
    VALUES
    (
        '0',
        '".$_POST['employee_id']."',
        '".$_POST['menu_id']."',
        '".$_POST['view_check']."',
        '".$_POST['view_check']."',
        '" . date("Y-m-d") . "',
        '" . date("h:i:s a") . "',
        '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
        '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
    ) ");



    }



    print "All Menu Permission Changed";

}else if($_POST['action'] == 'MAKE_ATTANDANCE_FROM_FROM_CSV' ){



    $qry = $conn_me->prepare("SELECT * FROM `check_in_check_out` where `status` = 'Pending'  GROUP BY `employee_id` ");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch) { 

      $info_emp = SETUP::SETUP_EMPLOYEEY($fetch['employee_id']);
      $take_att = FIND::TAKE_ATTANDANCE_FROM_MACHINE_DATA($fetch['employee_id'],$fetch['attendance_date']);


      $department_id = $info_emp['present_department'];
      $attandance_date =  $fetch['attendance_date'];
      $leave = $take_att['leave'];
      $late = $take_att['late'];
      $present = $take_att['present'];
      $absent = $take_att['absent'];

      
      $query = $conn_me->exec("INSERT INTO `take_attandance` 
      ( 
        `id`, `employee_id`,`real_attandance`, `department_id`, `present`, `late`, `absent`, `leave`, `attandance_date`, `note`, `year`, `date`, `time`, `poster`, `brunch_id`,`lastupdate`
      ) 
      VALUES
      (
          '0',
          '".$fetch['employee_id']."',
          '".$take_att['value']."',
          '".$department_id."',
          '".$present."',
          '".$late."',
          '".$absent."',
          '".$leave."',
          '".$attandance_date."',
          'From Machine',
          '" . date("Y") . "',
          '" . date("Y-m-d") . "',
          '" . date("h:i:s a") . "',
          '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
          '" . $_SESSION['USER_BRUNCH'] . "',
          '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'

          ) ");
   
      
    }



}else if($_POST['action'] == 'supplier_or_factory' ){


    $content = '<select id="send_to_id" name="send_to_id"   class="form-control select" data-live-search="true">
    <option value="">Select One</option> ';


if($_POST['VALUE'] == 'Supplier' ){



    $qry = $conn_me->prepare("SELECT * FROM `setup_supplier`  ");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch) { 
    $content .= '<option value="'.$fetch['id'].'"><b style="font-color:blue">' .$fetch['mobile']. '</b> - <b style="color:orange">' . $fetch['supplier_name'].'</b></option>';
   } 

}else if($_POST['VALUE'] == 'Factory'){
    $qry = $conn_me->prepare("SELECT * FROM `setup_factory`  ");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch) { 
    $content .= '<option value="'.$fetch['id'].'"><b style="font-color:blue">' .$fetch['mobile']. '</b> - <b style="color:orange">' . $fetch['factory_name'].'</b></option>';
   } 




}else{


}
$content .= '</select>';


print $content;



}else if($_POST['action'] == 'advance_status' ){

$info_emp = SETUP::SETUP_EMPLOYEEY($_POST['employee_id']);

$content = '<table class="table " style="background-color:antiquewhite;">';

$content .= '<tr>';
$content .= '<td>Employee Name</td>';
$content .= '<td>'.$info_emp['name'].'</td>';
$content .= '</tr>';

$content .= '<tr>';
$content .= '<td>Designation</td>';
$content .= '<td>'.$info_emp['designation_text'].'</td>';
$content .= '</tr>';

$content .= '<tr>';
$content .= '<td>Joining reference</td>';
$content .= '<td>'.$info_emp['referrer'].'</td>';
$content .= '</tr>';

$content .= '<tr>';
$content .= '<td>Joining date</td>';
$content .= '<td>'.$info_emp['join_d'].'</td>';
$content .= '</tr>';



$joinDate = new DateTime($info_emp['join_d']);

$today = new DateTime();
$interval = $today->diff($joinDate);
$days = $interval->days;
$totalMonths = ($interval->y * 12) + $interval->m;
$years = floor($totalMonths / 12);
$months = $totalMonths % 12;
$days = $interval->d;
$roundMonths = ($years * 12) + $months;
$roundedYears = ceil($roundMonths / 12);

$advance_limit = FIND::ADVANCE_LIMIT($roundedYears);


$content .= '<tr>';
$content .= '<td>Job age till today</td>';
$content .= '<td> '. $years.' years, '. $months.' months, and '.$days.' days.</td>';
$content .= '</tr>';

$content .= '<tr>';
$content .= '<td>Current Salary</td>';
$content .= '<td>'.$info_emp['present_salary'].'</td>';
$content .= '</tr>';

$content .= '<tr>';
$content .= '<td>Advance Limit</td>';
$content .= '<td>'.$advance_limit.'</td>';
$content .= '</tr>';

$content .= '<table>';
print $content;

}else if($_POST['action'] == 'find_leave_limit' ){


    $info_leave1 = STOCK::HOW_MANY_DAYS_LEAVE_LEFT($_POST['employee_id'],$_POST['leave_type_id'],$_POST['year'],$_POST['month'],'Type Wise Leave Yearly');


    print json_encode(array (
        'type_wise_leave_left' => $info_leave1['LEAVE_LEFT'] , 
        'total_leave_taken' => $info_leave1['LEAVE_TAKEN'])
    );


    }else if($_POST['action'] == 'employee_data_for_attendance' ){


        
		$attendance_date = date("Y-m-d", strtotime($_POST['attendance_date']));

 
        $content = '';
        $content .= '<table class="table table-bordered" style="padding-bottom:15px;" >
         <tr>
         <td style="text-align:center">Attendance of '.$_POST['attendance_date'].'</td>
         <td style="text-align:center"><input type="button" class="btn btn-danger" value="Save Data" onclick="takeAttandance()" id="take_attendance"></td>
         </tr>
    

         </table>';

        $content .= ' 
        
    <br>   
        <table class="table table-bordered " style="padding-bottom:15px;" id="ATable" >
        <thead>
        <th>Sl</th>
        <th>NAME</th>
        <th>ATTENDANCE</th>
        </thead>
        <tbody>';



        $count = 1;
        $qry = $conn_me->prepare("SELECT `id`,`code`,`name` FROM `setup_employee`  where hr_status = 'Active' ");
        $qry->execute();
        $count_item = $qry->rowCount();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        $sl = 1; 
        foreach ($fetch_list as $fetch) { 
           
$attandance_info = FIND::checkAttendanceStatus($fetch['id'],$attendance_date);
$leave_info = FIND::LEAVE($fetch['id'],$attendance_date);
$content .= '<tr>';

            $content .= '<input type="hidden" value="'.$fetch['id'].'" id="employee_id'.$count.'" >';

            $content .= '<td>'.$sl++.'</td>';
            $content .= '<td>'.$fetch['name'].'</td>';
            $content .= '<td>';
            

            $content .= ' <div class="form-group">


        
<label class="check"><input '.$leave_info['mess'].' type="radio" ' ; if( $attandance_info == 'P'){ $content .= 'Checked';}else{ $content .= ' '; }
$content .= '  value="' . $fetch['id'] . '" class="iradio" name="attandance'.$count.'" id="present'.$count.'" /> Present </label>


<label class="check"><input '.$leave_info['mess'].' type="radio" ' ; if( $attandance_info == 'Lt'){ $content .= 'Checked';}else{ $content .= ' '; }
$content .= '  value="' . $fetch['id'] . '" class="iradio" name="attandance'.$count.'" id="late'.$count.'" /> Late </label>

<label class="check"><input '.$leave_info['mess'].' type="radio" ' ; if( $attandance_info == 'A'){ $content .= 'Checked';}else{ $content .= ' '; }
$content .= '  value="' . $fetch['id'] . '" class="iradio" name="attandance'.$count.'" id="absent'.$count.'" /> Absent </label>

<label class="check"><input '.$leave_info['mess'].' type="radio" ' ; if( $attandance_info == 'L'){ $content .= 'Checked';}else{ $content .= ' '; }
$content .= '  value="' . $fetch['id'] . '" class="iradio" name="attandance'.$count.'" id="leave'.$count.'" /> Leave </label>

            
          </div></td>';

 
            $content .= '</tr>';
            $count = $count + 1;
        }

     $content .= '<input type="hidden" id="total_item" value="'.$count_item.'"> </tbody></table>';

     print json_encode(array ('content' => $content));

}else if($_POST['action'] == 'get_department_wise_value' ){

    $content = '';
    
    '<select id="employee_id" name="employee_id[]" data-live-search="true" class="select selectpicker" multiple="multiple" data-selected-text-format="count>2" data-all="false">';
    $qry = $conn_me->prepare("SELECT * FROM `setup_employee` where `present_department` = '".$_POST['VALUE']."' ");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch) { 
    $content .= '<option value="'.$fetch['id'].'"><b style="font-color:blue">' .$fetch['name']. '</b></option>';


    }
    $content .='</select>';
    print $content;

}else if($_POST['action'] == 'get_employee_data' ){

    $content = '<select name="get_employee_id[]" id="get_employee_id'.$_POST['count'].'" data-srno="'.$_POST['count'].'"  onchange="get_placement(this.value,\''.$_POST['count'].'\');" class="form-control select employee_id"  data-live-search="true"><option value="">Select One</option>';
    $qry = $conn_me->prepare("SELECT * FROM `setup_employee`  ");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch) { 
        $content .= '<option value="'.$fetch['id'].'"><b style="font-color:blue">' .$fetch['name']. '</b></option>';


    }
    $content .='</select>';
print $content;

}else if($_POST['action'] == 'find_type_wise_data' ){

    if($_POST['TYPE'] == 'raw_local_purches' ){

        $content = '<select id="material_id" name="material_id" onchange="find_unit(this.value,\'unittext\');" class="form-control select" data-live-search="true">
        <option value="">Select One</option> ';
  
  $filePath = 'xml_raw_material_list.xml';
  
  if (file_exists($filePath)) {
      $xml = simplexml_load_file($filePath);
  
      if ($xml !== false) {
          if (isset($xml->ROWDATA) && count($xml->ROWDATA->ROW) > 0) {
              // Process the XML data using foreach or other methods
              foreach ($xml->ROWDATA->ROW as $value) {
                  if ($value->product_category != 'Not Set') {
                      $content .= '<option value="'.$value->id.'">'.$value->product_category.' ::: '.$value->product_name.'</option>';
                  }
              }
          } else {
          }
      } else {
      }
  } else {
  }
  
  $content .= '</select>';
  

    }else if ($_POST['TYPE'] == 'fg_local_purches'){
        $xml_product_list = simplexml_load_file("xml_productList.xml");

        $content = '<select id="material_id" name="material_id" onchange="find_unit(this.value,\'unittext\');"  class="form-control select" data-live-search="true">
      <option value="">Select One</option> ';

      foreach ( $xml_product_list->ROW as  $value ) {

      $content .= '<option value="'.$value['id'].'"><b style="color:blue">' .$value['product_category'].' ::: '.$value['product_name'].'</b></option>';

     } 

$content .= '</select>';

    }else{

      $content = '';

    }
print $content ;

}else if($_POST['action'] == 'MESS_SEEN' ){

    $query1 = $conn_me->prepare("UPDATE `mess_box` 

    SET
    `seen` = '1',
    `seen_by`  = 	'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
    `seen_time` = '" . date("h:i:s a") . "',
    `seen_date` = '" . date("Y-m-d") . "'

    WHERE `id` = '".$_POST['ID']."' ");

    $query1->execute();

print 1;

}else if($_POST['action'] == 'check_pending_invoice' ){


$query = $conn_me->prepare("SELECT COUNT(*) as `data`  FROM `sales_invoice`  WHERE  `status` = 'Done' AND  `confirm_by_sales_manager` = 'Pending' ");
$query->execute();
$fetch_query = $query->fetch(PDO::FETCH_ASSOC);
$count = $query->rowCount();

if( $_SESSION['NEWERP_SESS_MEMBER_ID'] == '43'){

    if($count > 0 ){

        $show_notification = 'YES';
        $mess = "You have $fetch_query[data] invoice pending to confirm";
    
    }else{
        $show_notification = 'NO';
        $mess = "";
    }
}else{
    $show_notification = 'NO';
    $mess = "";
}

print json_encode(array ('show_notification' => $show_notification , 'mess' => $mess));

}else if($_POST['action'] == 'get_customer_info' ){



    $info_customer  = SETUP::SETUP_CUSTOMER($_POST['customer_id']);

    $info_due = FIND::getAllCustomerDues('Single-Customer-Wise',$_POST['customer_id'],date("Y-m-d"),'');
    $due = $info_due[0]['customer_due'];


    print json_encode(array ('due' => $due,'mobile' => $info_customer['mobile'] , 'address' => $info_customer['address'], 'transport_cost' => 0, 'creadit_limit' => $info_customer['creadit_limit'] ));

}else if($_POST['action'] == 'get_supplier_info' ){


$info_supplier  = SETUP::SETUP_SUPPLIER($_POST['SUPPLIERID']);
print json_encode(array ('mobile' => $info_supplier['mobile'] , 'address' => $info_supplier['address']));


}else if($_POST['action'] == 'BLOCK_SALE' ){


    $query1 = $conn_me->prepare("UPDATE `setup_product`
    SET
    `in_service` = '".$_POST['mess']."'

    WHERE `id` = '".$_POST['PRODUCTID']."' ");

    $query1->execute();

    if($_POST['mess'] == 'Block'){
        print "Block Done";
    }else{
        print "Unblock Done";
    }



}else if($_POST['action'] == 'GET_RELATED_ADMIN_NAME' ){
 

    $sales_return_due = FIND::SALES_RETURN_DUE($_POST['fromDate'],$_POST['toDate']);
    $top_sold_product = FIND::TOP_SOLD_PRODUCT($_POST['fromDate'],$_POST['toDate'],$_POST['productNumber']);
    $least_sold_product = FIND::LEAST_SOLD_PRODUCT($_POST['fromDate'],$_POST['toDate'],$_POST['productNumber']);

   

  print json_encode(array (

'invoice_amount1' =>  ABS($sales_return_due['invoice_amount1']),
'invoice_amount2' =>  ABS($sales_return_due['invoice_amount2']),
'invoice_amount3' =>  ABS($sales_return_due['invoice_amount3']),
'receive_amount1' =>  ABS($sales_return_due['receive_amount1']),
'receive_amount2' =>  ABS($sales_return_due['receive_amount2']),
'receive_amount3' =>  ABS($sales_return_due['receive_amount3']),
'return_amount1' =>   ABS($sales_return_due['return_amount1']),
'return_amount2' =>   ABS($sales_return_due['return_amount2']),
'return_amount3' =>   ABS($sales_return_due['return_amount3']),
'customer_due1' =>   ABS($sales_return_due['customer_due1']),
'customer_due2' =>   ABS($sales_return_due['customer_due2']),
'customer_due3' =>   ABS($sales_return_due['customer_due3']),
'top_sold' =>  $top_sold_product,
'least_sold' =>  $least_sold_product

));




}else if($_POST['action'] == 'BLOCK_USER' ){

    if($_POST['mess'] == 'Block'){
        $hr_status = 'Block';
        $query = $conn_me->prepare("DELETE FROM `webpush_member` WHERE `member_id` = '".$_POST['ADMINID']."' "); 
        $query->execute();
         
    }else{
        $hr_status = 'Active';
    }

    $query1 = $conn_me->prepare("UPDATE `admin` 

    SET
    `hr_status` = '".$hr_status."'

    WHERE `employee_id` = '".$_POST['ADMINID']."' ");

    $query1->execute();



    $query1 = $conn_me->prepare("UPDATE `setup_employee` 

    SET
    `hr_status` = '".$hr_status."'

    WHERE `id` = '".$_POST['ADMINID']."' ");

    $query1->execute();




    print "$_POST[mess] Done";


}else if($_POST['action'] == 'UPDATE_PRINT_STATUS' ){


    if($_POST['page_name'] == 'Godown Copy'){

    
        $query1 = $conn_me->prepare("UPDATE `sales_invoice` 

        SET
        `printed` = 'Yes'
    
        WHERE `code` = '".$_POST['code']."' ");
    
        $query1->execute();
    
    
   
    }

    print "1";

}else if($_POST['action'] == 'UPDATE_PREFIX' ){

    

    $query1 = $conn_me->prepare("UPDATE `invoice_prefix` 

    SET
    `prefix` = '".$_POST['prefix']."'

    WHERE `id` = '".$_POST['ID']."' ");

    $query1->execute();


    print "Change Success";




}else if($_POST['action'] == 'UPDATE_PRICE' ){


    $query1 = $conn_me->prepare("UPDATE `setup_product` 

    SET
    `sales_rate` = '".$_POST['price']."',
    `vat_percentage` = '".$_POST['vat_percentage']."',
    `discount` = '".$_POST['discount']."'



    WHERE `id` = '".$_POST['ID']."' ");

    $query1->execute();

    $query2 = $conn_me->exec("INSERT INTO `history_change_product_price_vat` 
    ( 
        `id`, `product_id`, `price`,`vat_percentage`, `discount`,`poster`, `date`
    
    ) 

    VALUES
    (
        '0',
        '".$_POST['ID']."',
        '".$_POST['price']."',
        '".$_POST['vat_percentage']."',
        '".$_POST['discount']."',
        '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
        '" . date("Y-m-d") . "'
    ) ");




    print "Change Success";


}else if($_POST['action'] == 'find_area_data' ){

    $selectedValues = implode(',', $_POST['selectedValues']);



   $content = '<select style="width:100%!imortant" ';
   
   if($_POST['TABLE'] == 'districts' ){
    $content .= ' id= "district_id" name="district_id[]"';
    $span = 'mess_box_district_id';
    }else if($_POST['TABLE'] == 'upazilas'){
        $content .= ' id= "upazilla_id" name="upazilla_id[]" ';
        $span = 'mess_box_upazilla_id';

    }else if($_POST['TABLE'] == 'unions'){
        $content .= ' id= "union_id" name="union_id[]" ';
        $span = 'mess_box_union_id';

    }else{
    $content .= '';
    $span = '';


    }

   $content .= ' data-live-search=true class="selectpicker" multiple data-selected-text-format="count>3" data-all="false" ';




        if($_POST['TABLE'] == 'districts' ){
        $content .= ' onchange="find_area_data(\'upazilas\',\'Upazila\',\'district_id\',this.value);" ';
        }else if($_POST['TABLE'] == 'upazilas'){
            $content .= ' onchange="find_area_data(\'unions\',\'Union\',\'upazilla_id\',this.value);" ';
        }else{
        $content .= ' ';

        }
    $content .= 'name="dist" id="dist" data-rel="chosen">
    <option value="">Select One</option>';
    $qry = $conn_me->prepare("SELECT * FROM `{$_POST['TABLE']}`  WHERE `{$_POST['FIELD']}`  IN ({$selectedValues}) ORDER BY `id` ASC");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
     foreach($fetch_list AS $fetch) { 
        $content .=  '<option value="'.$fetch['id'].'">'.$fetch['name'].'</option>'; 
     } 
     $content .= '</select><span class="help-block" id="'.$span.'"></span>';


     print  $content   ;



}else if($_POST['action'] == 'find_related_data' ){

    
   $content = '<select ';
   
   if($_POST['TABLE'] == 'districts' ){
    $content .= ' id= "district_id" ';
    $span = 'mess_box_district_id';
    }else if($_POST['TABLE'] == 'upazilas'){
        $content .= ' id= "upazilla_id" ';
        $span = 'mess_box_upazilla_id';

    }else if($_POST['TABLE'] == 'unions'){
        $content .= ' id= "union_id" ';
        $span = 'mess_box_union_id';

    }else{
    $content .= '';
    $span = '';


    }

   $content .= 'required class="select form-control" class="form-control" data-live-search="true"';




        if($_POST['TABLE'] == 'districts' ){
        $content .= ' onchange="find_related_data(\'upazilas\',\'Upazila\',\'district_id\',this.value);" ';
        }else if($_POST['TABLE'] == 'upazilas'){
            $content .= ' onchange="find_related_data(\'unions\',\'Union\',\'upazilla_id\',this.value);" ';
        }else{
        $content .= ' ';

        }
    $content .= 'name="dist" id="dist" data-rel="chosen">
    <option value="">Select One</option>';
    $qry = $conn_me->prepare("SELECT * FROM `{$_POST['TABLE']}`  WHERE `{$_POST['FIELD']}` = '".$_POST['VALUE']."' ORDER BY `id` ASC");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
     foreach($fetch_list AS $fetch) { 
        $content .=  '<option value="'.$fetch['id'].'">'.$fetch['name'].'</option>'; 
     } 
     $content .= '</select><span class="help-block" id="'.$span.'"></span>';


     print  $content   ;




    }else if($_POST['action'] == 'get_Each_Warehouse_Stock'){


        $stock = FIND::WAREHOUSE_WISE_STOCK($_POST['Type'],$_POST['material_id'],$_POST['warehouse_id']);

        print json_encode(array ('stock_pcs' => $stock['warhouse_wise_stock'],'stock_carton' => $stock['warhouse_wise_stock_in_carton']));

        
    }else if($_POST['action'] == 'delete_recipe_wise_demand_and_item'){

        if( $_SESSION['USER_TYPE'] == 'Admin'){
            $query = $conn_me->prepare("DELETE FROM `raw_request_recipe_wise` WHERE `id` = '".$_POST['ID']."' "); 
            $query->execute();

            $query = $conn_me->prepare("DELETE FROM `raw_request_recipe_wise_item` WHERE `raw_recipe_wise_request_id` = '".$_POST['ID']."' "); 
            $query->execute();


    
            print 'Item Deleted with recipe';
        }else{
            print 'Only Admin Can Delete A Data';
        }


        

    }else if($_POST['action'] == 'delete_spray_recipe_wise_demand_and_item'){

        

        if( $_SESSION['USER_TYPE'] == 'Admin'){
            $query = $conn_me->prepare("DELETE FROM `raw_spray` WHERE `id` = '".$_POST['ID']."' "); 
            $query->execute();

            $query = $conn_me->prepare("DELETE FROM `raw_spray_item` WHERE `raw_spray_id` = '".$_POST['ID']."' "); 
            $query->execute();


    
            print 'Item Deleted with recipe';
        }else{
            print 'Only Admin Can Delete A Data';
        }





    }else if($_POST['action'] == 'delete_print_recipe_wise_demand_and_item'){



        if( $_SESSION['USER_TYPE'] == 'Admin'){
            $query = $conn_me->prepare("DELETE FROM `raw_print` WHERE `id` = '".$_POST['ID']."' "); 
            $query->execute();

            $query = $conn_me->prepare("DELETE FROM `raw_print_item` WHERE `raw_print_id` = '".$_POST['ID']."' "); 
            $query->execute();


    
            print 'Item Deleted with recipe';
        }else{
            print 'Only Admin Can Delete A Data';
        }
    }else if($_POST['action'] == 'delete_Full_Demand'){


        $query = $conn_me->prepare("SELECT count(id) AS total_item FROM `demand_receive`  WHERE  `demand_id` = '".$_POST['id']."' ");
        $query->execute();
        $fetch2 = $query->fetch(PDO::FETCH_ASSOC);

        if( $fetch2['total_item'] > 0 ){
         print "some product of this Demand alredy approved ! Can not delete this item";
        }else{
        
            $query = $conn_me->prepare("DELETE FROM `demand_item` WHERE `demand_id` = '".$_POST['id']."' "); 
            $query->execute();

            $query = $conn_me->prepare("DELETE FROM `demand` WHERE `id` = '".$_POST['id']."' "); 
            $query->execute();



            print 'Item Deleted';

        }
 
        die();


}else if ($_POST['action'] == 'DONE_DEMAND'){


    $query1 = $conn_me->prepare("UPDATE `demand` SET `convert_to_invoice` = 'Done' WHERE `id` = '".$_POST['id']."'  ");
    $query1->execute(); 
    
    
    
    }else if($_POST['action'] == 'delete_demand'){


        $query = $conn_me->prepare("SELECT demand_id,product_id FROM `demand_item`  WHERE  `id` = '".$_POST['id']."' ");
        $query->execute();
        $fetch2 = $query->fetch(PDO::FETCH_ASSOC);

        $delivery_done  = DEMAND::TotalDelivery($fetch2['demand_id'],$fetch2['product_id']) ; 
        if( $delivery_done['total_item'] > 0 ){
         print "Demand alredy approved ! Can not delete this item";
        }else{
        
            $query = $conn_me->prepare("DELETE FROM `demand_item` WHERE `id` = '".$_POST['id']."' "); 
            $query->execute();

            print 'Item Deleted';

        }
 
        die();

    }else if($_POST['action'] == 'delete_mold_recipe_wise_demand_and_item'){

        if( $_SESSION['USER_TYPE'] == 'Admin'){
            $query = $conn_me->prepare("DELETE FROM `raw_molding` WHERE `id` = '".$_POST['ID']."' "); 
            $query->execute();

            $query = $conn_me->prepare("DELETE FROM `raw_molding_item` WHERE `raw_molding_id` = '".$_POST['ID']."' "); 
            $query->execute();


    
            print 'Item Deleted with recipe';
        }else{
            print 'Only Admin Can Delete A Data';
        }




    }else if($_POST['action'] == 'delete_receipe_table_row'){

        if( $_SESSION['USER_TYPE'] == 'Admin'){
            $query = $conn_me->prepare("DELETE FROM `{$_POST['TABLENAME']}` WHERE `id` = '".$_POST['ID']."' "); 
            $query->execute();
    
            print 'Row deleted';
        }else{
            print 'Only Admin Can Delete A Data';
        }


    }else if($_POST['action'] == 'delete_money_transfer'){



        $qry1 = $conn_me->prepare("SELECT * FROM `account_transection`  WHERE `id` = '".$_POST['id']."' ");
        $qry1->execute();
        $tr_to = $qry1->fetch(PDO::FETCH_ASSOC);


        QUICK_BALANCE::QUICK_OPENING_BALANCE('in_amount',$tr_to['transection_by'],$tr_to['transection_by_id'],-$tr_to['in_amount'],$tr_to['transection_date'],$tr_to['brunch_id']);



        $qry2 = $conn_me->prepare("SELECT * FROM `account_transection`  WHERE `id` = '".$tr_to['transection_id']."' ");
        $qry2->execute();
        $tr_from = $qry2->fetch(PDO::FETCH_ASSOC);


        QUICK_BALANCE::QUICK_OPENING_BALANCE('out_amount',$tr_from['transection_by'],$tr_from['transection_by_id'],-$tr_from['out_amount'],$tr_from['transection_date'],$tr_from['brunch_id']);



        $query1 = $conn_me->prepare("DELETE FROM `account_transection` WHERE `id` = '".$tr_to['id']."' "); 
        $query1->execute();

        $query2 = $conn_me->prepare("DELETE FROM `account_transection` WHERE `id` = '".$tr_from['id']."' "); 
        $query2->execute();




        print 'Transaction Deleted';




    }else if($_POST['action'] == 'delete_total_transection'){

        $ck1 = $conn_me->prepare("SELECT *  FROM `account_transection` where `id` = '".$_POST['id']."'");
        $ck1->execute();
        $info_tr = $ck1->fetch(PDO::FETCH_ASSOC);


        if($info_tr['transection_type'] == 'INCOME' ){

            QUICK_BALANCE::QUICK_OPENING_BALANCE('in_amount',$info_tr['transection_by'],$info_tr['transection_by_id'],-$info_tr['in_amount'],$info_tr['transection_date'],$info_tr['brunch_id']);
            

            if($info_tr['transection_to'] == 'Customer'){

                QUICK_BALANCE::CUSTOMER_QUICK_DUE($info_tr['transection_to_id'],-$info_tr['in_amount'],'receive_amount',$info_tr['transection_date'],$info_tr['brunch_id']);

                    $query = $conn_me->prepare("UPDATE `sales_invoice` SET
                    `call_it_a_day` = 'NO',
                    `transection_id` = NULL
                    WHERE `transection_id` = '".$info_tr['id']."'");
                    $query->execute();

            }else{



            }
        }else if ($info_tr['transection_type'] == 'EXPENSE'){


            QUICK_BALANCE::QUICK_OPENING_BALANCE('out_amount',$info_tr['transection_by'],$info_tr['transection_by_id'],-$info_tr['out_amount'],$info_tr['transection_date'],$info_tr['brunch_id']);


            if($info_tr['transection_to'] == 'Supplier'){

                QUICK_BALANCE::SUPPLIER_QUICK_DUE($info_tr['transection_to_id'],-$info_tr['out_amount'],0.00,0.00,0.00,'payment_amount',$info_tr['transection_date']);
               
            }else{

            }


        }else{

        }


        $query1 = $conn_me->prepare("DELETE FROM `account_transection` WHERE `id` = '".$info_tr['id']."' "); 
        $query1->execute();


        print 'Invoice Deleted';

        
    }else if($_POST['action'] == 'delete_preorder_invoice'){


        $query1 = $conn_me->prepare("DELETE FROM `preorder_invoice` WHERE `id` = '".$_POST['id']."' "); 
        $query1->execute();


        $query2 = $conn_me->prepare("DELETE FROM `pre_order_invoice_item` WHERE `preorder_invoice_id` = '".$_POST['id']."' "); 
        $query2->execute();

        print 'Preorder Invoice Deleted'; 


    }else if($_POST['action'] == 'delete_return_invoice'){


        $query1 = $conn_me->prepare("DELETE FROM `sales_return_invoice` WHERE `id` = '".$_POST['id']."' "); 
        $query1->execute();


        $query2 = $conn_me->prepare("DELETE FROM `sales_return_invoice_item` WHERE `return_invoice_id` = '".$_POST['id']."' "); 
        $query2->execute();

        print 'Return Invoice Deleted'; 


    }else if($_POST['action'] == 'delete_total_invoice'){

            
        $info_invoice = SETUP::SETUP_SALES_INVOICE($_POST['CODE']);


        QUICK_BALANCE::CUSTOMER_QUICK_DUE($info_invoice['customer_id'],-$info_invoice['total_invoice_price'],'invoice_amount',$info_invoice['invoice_date'],$info_invoice['brunch_id']);


        $query1 = $conn_me->prepare("DELETE FROM `sales_invoice` WHERE `id` = '".$info_invoice['id']."' "); 
        $query1->execute();


        $query2 = $conn_me->prepare("DELETE FROM `sales_invoice_item` WHERE `sales_invoice_id` = '".$info_invoice['id']."' "); 
        $query2->execute();

        print 'Invoice Deleted'; 

    }else if($_POST['action'] == 'delete_sales_table_row'){
        
        
        
        

if($_POST['related_id'] == 'New' ) {

 $query = $conn_me->prepare("DELETE FROM `sales_invoice_item` WHERE `id` = '".$_POST['ID']."' and status = 'Pending' "); 
    $query->execute();
    if($query){
    print 'Row deleted';
    }

}else{

        $ck1 = $conn_me->prepare("SELECT `dispatcher_id`,`generate_challan`,`customer_id`,`invoice_date`,`brunch_id`  FROM `sales_invoice` where `id` = '".$_POST['SALES_ID']."'");
        $ck1->execute();
        $info_sales = $ck1->fetch(PDO::FETCH_ASSOC);


if(!empty($info_sales['dispatcher_id'])){


        print 'can not deleted item of this invoice';

    
}else{

    $ck2 = $conn_me->prepare("SELECT `sales_quantity`,`sales_rate`,`status`  FROM `sales_invoice_item` where `id` = '".$_POST['ID']."'");
    $ck2->execute();
    $info_salesItem = $ck2->fetch(PDO::FETCH_ASSOC);

    if  ($info_salesItem['status'] == 'Done') {
    $price = $info_salesItem['sales_quantity']*$info_salesItem['sales_rate'];
    QUICK_BALANCE::CUSTOMER_QUICK_DUE($info_sales['customer_id'],-$price,'invoice_amount',$info_sales['invoice_date'],$info_sales['brunch_id']);
    }

    $query = $conn_me->prepare("DELETE FROM `sales_invoice_item` WHERE `id` = '".$_POST['ID']."' "); 
    $query->execute();
    if($query){
    print 'Row deleted';
    }


}
 

}


    }else if($_POST['action'] == 'delete_table_row'){

            $query = $conn_me->prepare("DELETE FROM `{$_POST['TABLENAME']}` WHERE `id` = '".$_POST['ID']."' "); 
            $query->execute();
    
            print 'Row deleted';
 

    }else if($_POST['action'] == 'transfer_type'){
        
        
        
        
        $content = '<table class="table">';

        if($_POST['type'] == 'Cash_To_Mobile'){

            $content   .= '<input type="hidden" class="form-control" id="transection_by_from" value="Cash" >';
            $content   .= '<input type="hidden" class="form-control" id="transection_by_to" value="Mobile-Banking" >';

            $content   .= '<tr>';
            $content   .= '<th>From</th>';
            $content   .= '<th><input type="text" id="tr_from" value="Cash" class="form-control text-success" readonly></th>';
            $content   .= '</tr>';
            $content   .= '<tr>';
            $content   .= '<th>To Mobile</th>';
            $content   .= '<td>
            <select class="form-control select" id="tr_to" name = "tr_to"  data-live-search="true">
    <option value="">Select One</option>';
    $qry = $conn_me->prepare("SELECT * FROM `setup_mobile_banking` where `status`  = 'Active'   ORDER BY `id` ASC");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch) { 
        $bank_details = "$fetch[mobile_number] :: $fetch[mobile_bank_name] ";
        $content .= '<option  value="'.$fetch['id'].'">' .$bank_details. '</option>';
    }
$content .='</select></td>';
$content   .= '</tr>';

}else if ($_POST['type'] == 'Cash_To_Cash'){


    $content   .= '<input type="hidden" class="form-control" id="transection_by_from" value="Cash" >';
    $content   .= '<input type="hidden" class="form-control" id="transection_by_to" value="Cash" >';

    $content   .= '<tr>';
    $content   .= '<th>From</th>';
    $content   .= '<th><input type="text" id="tr_from" value="Cash" class="form-control text-success" readonly></th>';
    $content   .= '</tr>';

    $content   .= '<tr>';
    $content   .= '<th>To</th>';
    $content   .= '<th><input type="text" id="tr_to" value="Cash" class="form-control text-success" readonly></th>';
    $content   .= '</tr>';




        }else if ($_POST['type'] == 'Cash_To_Bank'){

            $content   .= '<input type="hidden" class="form-control" id="transection_by_from" value="Cash" >';
            $content   .= '<input type="hidden" class="form-control" id="transection_by_to" value="Bank" >';


            $content   .= '<tr>';
            $content   .= '<th>From</th>';
            $content   .= '<th><input type="text" id="tr_from" value="Cash" class="form-control text-success" readonly></th>';
            $content   .= '</tr>';
            $content   .= '<tr>';
            $content   .= '<th>To Bank</th>';
            $content   .= '<td>
            <select class="form-control select" id="tr_to" name = "tr_to"  data-live-search="true">
    <option value="">Select One</option>';
    $qry = $conn_me->prepare("SELECT * FROM `setup_bank` where `status`  = 'Active'   ORDER BY `id` ASC");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch) { 
        $bank_details = "$fetch[account_number] :: $fetch[brunch_name] :: $fetch[bank_name]";
        $content .= '<option  value="'.$fetch['id'].'">' .$bank_details. '</option>';
    }
$content .='</select></td>';
$content   .= '</tr>';
           

}else if ($_POST['type'] == 'Bank_To_Mobile'){

    $content   .= '<input type="hidden" class="form-control" id="transection_by_from" value="Bank" >';
    $content   .= '<input type="hidden" class="form-control" id="transection_by_to" value="Mobile-Banking" >';


    $content   .= '<tr>';
    $content   .= '<th>From Bank</th>';
    $content   .= '<td>
    <select class="form-control select" id="tr_from" name = "tr_from"  data-live-search="true">
<option value="">Select One</option>';
$qry = $conn_me->prepare("SELECT * FROM `setup_bank` where `status`  = 'Active'   ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) { 
$bank_details = "$fetch[account_number] :: $fetch[brunch_name] :: $fetch[bank_name]";
$content .= '<option  value="'.$fetch['id'].'">' .$bank_details. '</option>';
}
$content .='</select></td>';
$content   .= '</tr>';
        

$content   .= '<tr>';
$content   .= '<th>To Mobile</th>';
$content   .= '<td>
<select class="form-control select" id="tr_to" name = "tr_to"  data-live-search="true">
<option value="">Select One</option>';
$qry = $conn_me->prepare("SELECT * FROM `setup_mobile_banking` where `status`  = 'Active'   ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) { 
$bank_details = "$fetch[mobile_number] :: $fetch[mobile_bank_name] ";
$content .= '<option  value="'.$fetch['id'].'">' .$bank_details. '</option>';
}
$content .='</select></td>';
$content   .= '</tr>';


}else if ($_POST['type'] == 'Bank_To_Cash'){


    $content   .= '<input type="hidden" class="form-control" id="transection_by_from" value="Bank" >';
    $content   .= '<input type="hidden" class="form-control" id="transection_by_to" value="Cash" >';


    $content   .= '<tr>';
    $content   .= '<th>From Bank</th>';
    $content   .= '<td>
    <select class="form-control select" id="tr_from" name = "tr_from"  data-live-search="true">
<option value="">Select One</option>';
$qry = $conn_me->prepare("SELECT * FROM `setup_bank` where `status`  = 'Active'   ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) { 
$bank_details = "$fetch[account_number] :: $fetch[brunch_name] :: $fetch[bank_name]";
$content .= '<option  value="'.$fetch['id'].'">' .$bank_details. '</option>';
}
$content .='</select></td>';
$content   .= '</tr>';

$content   .= '<tr>';
$content   .= '<th>To</th>';
$content   .= '<th><input type="text" id="tr_to" name = "tr_to" value="Cash" class="form-control text-success" readonly></th>';
$content   .= '</tr>';


}else if ($_POST['type'] == 'Mobile_To_Cash'){

    $content   .= '<input type="hidden" class="form-control" id="transection_by_from" value="Mobile-Banking" >';
    $content   .= '<input type="hidden" class="form-control" id="transection_by_to" value="Cash" >';


    $content   .= '<tr>';
    $content   .= '<th>To Mobile</th>';
    $content   .= '<td>
    <select class="form-control select" id="tr_from" name = "tr_from"  data-live-search="true">
    <option value="">Select One</option>';
    $qry = $conn_me->prepare("SELECT * FROM `setup_mobile_banking` where `status`  = 'Active'   ORDER BY `id` ASC");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch) { 
    $bank_details = "$fetch[mobile_number] :: $fetch[mobile_bank_name] ";
    $content .= '<option  value="'.$fetch['id'].'">' .$bank_details. '</option>';
    }
    $content .='</select></td>';
    $content   .= '</tr>';
    

    $content   .= '<tr>';
$content   .= '<th>To</th>';
$content   .= '<th><input type="text" value="Cash" id="tr_to" class="form-control text-success" readonly></th>';
$content   .= '</tr>';


}else if ($_POST['type'] == 'Mobile_To_Bank'){

    $content   .= '<input type="hidden" class="form-control" id="transection_by_from" value="Mobile-Banking" >';
    $content   .= '<input type="hidden" class="form-control" id="transection_by_to" value="Bank" >';



    $content   .= '<tr>';
$content   .= '<th>From Mobile</th>';
$content   .= '<td>
<select class="form-control select" id="tr_from" name = "tr_from"  data-live-search="true">
<option value="">Select One</option>';
$qry = $conn_me->prepare("SELECT * FROM `setup_mobile_banking` where `status`  = 'Active'   ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) { 
$bank_details = "$fetch[mobile_number] :: $fetch[mobile_bank_name] ";
$content .= '<option  value="'.$fetch['id'].'">' .$bank_details. '</option>';
}
$content .='</select></td>';
$content   .= '</tr>';


$content   .= '<tr>';
$content   .= '<th>To Bank</th>';
$content   .= '<td>
<select class="form-control select" id="tr_to" name = "tr_to"  data-live-search="true">
<option value="">Select One</option>';
$qry = $conn_me->prepare("SELECT * FROM `setup_bank` where `status`  = 'Active'   ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) { 
$bank_details = "$fetch[account_number] :: $fetch[brunch_name] :: $fetch[bank_name]";
$content .= '<option  value="'.$fetch['id'].'">' .$bank_details. '</option>';
}
$content .='</select></td>';
$content   .= '</tr>';
    





}else{

$content   = '<input type="hidden" value="nothing" id="tr_from"><input type="hidden" id="tr_to" value="nothing">';
$content   .= '<input type="hidden" class="form-control" id="transection_by_to" value="" >';
$content   .= '<input type="hidden" class="form-control" id="transection_by_from" value="" >';

        }


        print json_encode(array ('content' => $content));


        
    }else if($_POST['action'] == 'find_invoice_by_invoice_no'){

        $content = '';
       
        $content .= ' <div class="panel-body panel-body-table">
                                    
        <div class="table-responsive">
            <table id="ATable" class="table table-bordered table-striped datatable" style="background-color:white;">
                <thead>
                    <tr>
                        <th width="33.33%">Invoice</th>
                        <th width="33.33%">Customer</th>
                        <th width="33.33%">Action</th>
                    </tr>
                </thead>
                <tbody>';
                $invoice_no = preg_quote($_POST['INVOICENO'], '/');

                $sl =1;
                $count1 = 0;
                $qry = $conn_me->prepare("SELECT A.* , date_format(A.`invoice_date`, '%d-%m-%Y') AS `invoice_date`,B.customer_name  
                FROM `sales_invoice` A 
                join `setup_customer` B ON (`A`.`customer_id` = B.`id`)
                where `warehouse_dispatch` = 'Done' AND (
                A.`invoice_no` REGEXP '".$invoice_no."' OR 
                date_format(A.`invoice_date`, '%d-%m-%Y') REGEXP '".$invoice_no."' OR 
                B.`customer_name` REGEXP '".$invoice_no."' OR
                B.`shop_name` REGEXP '".$invoice_no."' OR
                B.`mobile` REGEXP '".$invoice_no."' 
                ) ");
                $qry->execute();
                $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
                if ($qry->rowCount() > 0)
                {
                    foreach($fetch_list AS $fetch) {
                  
                        $count1 = $count1+1;
                        $info_customer = SETUP::SETUP_CUSTOMER($fetch['customer_id']);
                        $info_status = FIND::SALES_INVOICE_STATUS($fetch['id']);

                               $content .= '<tr><input type="text" style="display:none" id="search_invoice_'.$count1.'" value="'.$fetch['code'].'" >

                        <td>Invoice No. <strong>'.$fetch['invoice_no'].'</strong><br>Invoice Date. <strong>'.$fetch['invoice_date'].'</strong></td>
                        <td>Name: ' . $info_customer['shop_name'].' <br>Mobile: '.$info_customer['mobile']. ' <br>Address: '.$info_customer['address'].'</td>
                        <td>';

                        if($_POST['TYPE'] == 'Sales Invoice' ){ 
                            $content .= $info_status['status'] . ' <a href="invoice_copy.php?code='.$fetch['code'].'" target="_BLINK"><span class="fa fa-file-text"></span></a>';
                        }else{ 
                            $content .= '<button type="button" class="btn btn-info btn-rounded btn-sm" id="#exampleModalCenter" onclick="modal_wihout_refresh(\'load_data_in_modal\',\'search_invoice_'.$count1.'\',\''.$_POST['TYPE'].'\');"><i class="fa fa-eye"></i></button>';
                        }
                      

                
                        $content .= '
                        </td>
                    </tr>';
                    }                                     
                }
                    $content .= '</tbody>
            </table>
        </div>
        
    </div>';
   

        print json_encode(array ('content' => $content));



    }else if($_POST['action'] == 'approve_single_pending_data'){

        $query = $conn_me->prepare("UPDATE `{$_POST['TABLE']}` 

		SET
		`{$_POST['FIELD1']}` = 'Done'


		WHERE `id` = '".$_POST['ID']."'");

		$query->execute();

        if($query){
            
         print 'Invoice Transfer to Assembling';
        }

    }else if($_POST['action'] == 'search_task'){

     

        if($_POST['WHERE'] == 'search_colection' ){
            $flow_chart = MANUFACTUR_PRODUCT::MATERIAL_COLLECTION($_POST['WHAT'],$_POST['WHERE']);

            print $flow_chart['raw_material_collection'];
        }else if($_POST['WHERE'] == 'search_requsation'){
            $flow_chart = MANUFACTUR_PRODUCT::REQUISITION($_POST['WHAT'],$_POST['WHERE']);

            print $flow_chart['requisition'];
        }else if($_POST['WHERE'] == 'search_assembling'){
            $flow_chart = MANUFACTUR_PRODUCT::ASSEMBLING($_POST['WHAT'],$_POST['WHERE']);

            print $flow_chart['assembling'];

        }else{
            print '';
        }
    
        
        
    }else if($_POST['action'] == 'pursh_to_sale_report'){


 $query1 = $conn_me->prepare("UPDATE `sales_invoice` SET `generate_challan` = 'Done' WHERE `id` = '".$_POST['related_id']."'");
 		$query1->execute();

	exit();
    
        
    }else if($_POST['action'] == 'final_recipe_wise_dispatch_from_warehouse'){


        $query1 = $conn_me->prepare("UPDATE `raw_request_recipe_wise` 

		SET
        `warehouse_full_dispatch_date` = '" . date("Y-m-d") . "',
		`warehouse_dispatch` = 'Done',
		`warehouse_dispatch_by` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "'


		WHERE `code` = '".$_POST['CODE']."'");

		$query1->execute();

        $query2 = $conn_me->prepare("UPDATE `raw_request_recipe_wise_item` 

		SET
        `warehouse_dispatch_date` = '" . date("Y-m-d") . "',
		`warehouse_dispatch` = 'Done',
		`warehouse_dispatch_by` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "'


		WHERE `demand_code` = '".$_POST['CODE']."'");

		$query2->execute();

    }else if($_POST['action'] == 'Module_wise_report'){

        $content = '';

        
        if($_POST['MODULE'] == 'All' ){
        $QUEY = "";
        }else{
        $QUEY = " `section` = '".$_POST['MODULE']."' AND ";
        }


        $qry = $conn_me->prepare("SELECT  *   FROM `menu_list` where  $QUEY `type` = 'Report'  GROUP BY `section` ");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);

            foreach($fetch_list AS $fetch) {

                $content .= '<div class="col-md-12">';  

                $qry2 = $conn_me->prepare("SELECT *   FROM `menu_list` where  `section` = '".$fetch['section']."' AND `type` = 'Report' ORDER BY `sort`");
                $qry2->execute();
                $fetch_list2 = $qry2->fetchAll(PDO::FETCH_ASSOC);
                foreach($fetch_list2 AS $fetch2) {

                $content .= '<div class="col-sm-2">    
                                    
                <a href="'.$fetch2['menu_link'].'" class="tile" style="background-color:'.$fetch2['status'].'">
                '.$fetch2['icon'].'
                    <p>'.$fetch2['menu'].'</p>                            
                    <div class="informer informer-danger dir-tr"></div>
                </a>                        
            </div>';
            }
            $content .= '</div>';
            }

            print   $content;

        

    }else if($_POST['action'] == 'Search_Report'){
        
        
        

        $invoice_no = isset($_POST['search_report']) ? $_POST['search_report'] : '';

        if (empty($invoice_no)) {
            // Handle the case where the input is empty
            print 'No report found';
        } else {
            $invoice_no = preg_quote($invoice_no, '/');
        
            // Use parameterized query to avoid SQL injection
            $qry = $conn_me->prepare("SELECT * FROM `menu_list` WHERE `menu` REGEXP :invoice_no AND `type` = 'Report'");
            $qry->bindParam(':invoice_no', $invoice_no, PDO::PARAM_STR);
            $qry->execute();
            $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        
            if ($qry->rowCount() > 0) {
                foreach ($fetch_list as $fetch) {
                    $content = '<div class="col-md-2">    
                                    <a href="' . $fetch['menu_link'] . '" class="tile" style="background-color:' . $fetch['status'] . '">
                                        ' . $fetch['icon'] . '
                                        <p>' . $fetch['menu'] . '</p>                            
                                        <div class="informer informer-danger dir-tr"></div>
                                    </a>                        
                                </div>';
                    print $content;
                }
            } else {
                print 'No report found';
            }
        }
        

    }else if($_POST['action'] == 'send_for_fitting'){

        $query = $conn_me->prepare("UPDATE `raw_request_recipe_wise` 

		SET
		`send_for_fitting` = '".$_POST['STATUS']."'
		WHERE `code` = '".$_POST['CODE']."'");

		$query->execute();

        
        $query = $conn_me->prepare("UPDATE `raw_request_recipe_wise_item` 

		SET
		`send_for_fitting` = '".$_POST['STATUS']."'
		WHERE `demand_code` = '".$_POST['CODE']."'");

		$query->execute();


        if($query){
print 'Data Approve Success';
        }

    }else if($_POST['action'] == 'approve_pending_data'){


        $query = $conn_me->prepare("UPDATE `{$_POST['TABLE']}` 

		SET
        `{$_POST['FIELD1']}` = '" . date("Y-m-d") . "',
		`{$_POST['FIELD2']}` = 'Done',
		`{$_POST['FIELD3']}` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "'


		WHERE `code` = '".$_POST['CODE']."'");

		$query->execute();

        if($query){
print 'Data Approve Success';
        }


}else if($_POST['action'] == 'activity_report'){


print 'No data' ;



}else if($_POST['action'] == 'fatch_employee_details'){


$content = '';

$employe_info = SETUP::SETUP_EMPLOYEEY($_POST['ID']);

$content .='<div class="form-group">
<label class="col-md-3 col-xs-12 control-label">User ID/ Employee ID</label>
<div class="col-md-6 col-xs-12">                                            
<input id="username" name="username" style="color:red;" class="form-control" type="text"  value="'.$employe_info['employee_prefix'].$employe_info['code'].'">
</div>
</div>';

$content .='<div class="form-group">
<label class="col-md-3 col-xs-12 control-label">Emial</label>
<div class="col-md-6 col-xs-12">                                            
<input style="color:red;" class="form-control" type="text" readonly value="'.$employe_info['email'].'">
</div>
</div>';

$content .='<div class="form-group">
<label class="col-md-3 col-xs-12 control-label">Phone</label>
<div class="col-md-6 col-xs-12">                                            
<input style="color:red;" class="form-control" type="text" readonly value="'.$employe_info['mob_no'].'">
</div>
</div>';



print $content ;
}else{


}
