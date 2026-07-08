<?php 
    $xml_customer_list = simplexml_load_file("xml_customerList.xml");


$permission_management2 = PERMISSION::pageCreatePermission($_SESSION['USER_TYPE'],'Final Data','final_sales','');

 

if($_GET['related_id'] == 'New' ){

    $get_employeeid = SETUP::ADMIN_SETUP($_SESSION['NEWERP_SESS_MEMBER_ID']);
    $brunch_name =  $get_employeeid['brunch_name'];
    $sales_person = $get_employeeid['hr_name'];
    $sales_person_id = $_SESSION['NEWERP_SESS_MEMBER_ID'];

    $dispatch_from_which_brunch = '';
    $customer_id = '';
    $sales_by ='';
    $total_vat = 0;
    $invoice_total =0;
    $discount = 0;
    $invoice_date = date('d-m-Y');
    $access = 'YES';
    $related_code = '';
    $invoice_payable = 0 ; 
    $narration = '' ; 
    $brunch_id = $_SESSION['USER_BRUNCH'] ; 
    $due = 0 ;
    $creadit_limit = 0 ;


}else{

    $info = SETUP::SETUP_FG_SALES_BY_ID($_GET['related_id']);

    $invoice_info = FIND::TOTAL_SALES_INVOICE_PRICE($_GET['related_id']);
    $invoice_total = $invoice_info['sub_total_without_discout'] ;
    $invoice_payable = $invoice_info['price'] ;

    $info_brunch = SETUP::SETUP_BRUNCH($info['fetch']['brunch_id']);    
    $info_sales_person = SETUP::ADMIN_SETUP($info['fetch']['sales_person']);    
    $related_code = $info['fetch']['code'];
    $brunch_name =  $info_brunch['brunch'];
    $sales_person = $info_sales_person['hr_name'];
    $sales_person_id = $info['fetch']['sales_person'];



    $invoice_date = date("d-m-Y", strtotime($info['fetch']['invoice_date']));
    
    $dispatch_from_which_brunch = $info['fetch']['dispatch_from_which_brunch'];
    $customer_id = $info['fetch']['customer_id'];
    $sales_by = $info['fetch']['sales_by'];
    $total_vat = $info['fetch']['total_vat_cost'];
    $discount = $info['fetch']['discount'];
    $brunch_id = $info['fetch']['brunch_id']; 

    $narration = $info['fetch']['narration'];
    if($info['fetch']['generate_challan'] == 'Done' || !empty($info['fetch']['dispatcher_id']) ){
        $access = 'NO';
    }else{
        $access = 'YES';
    }

       $info_due = FIND::getAllCustomerDues('Single-Customer-Wise',$customer_id,date("Y-m-d"),'');
       $due = $info_due[0]['customer_due'];
       $creadit_limit = $info['fetch']['creadit_limit'] ; 


}

if( $access == 'YES'){ ?>


<style>
  .salestable>thead>tr>th, .salestable>tbody>tr>th, .salestable>tfoot>tr>th, .salestable>thead>tr>td, .salestable>tbody>tr>td, .salestable>tfoot>tr>td {
    padding: 5px;
    line-height: 1.42857143;
    vertical-align: top;
    border-top: 1px solid #ddd;
}

.salestable2>thead>tr>th, .salestable2>tbody>tr>th, .salestable2>tfoot>tr>th, .salestable2>thead>tr>td, .salestable2>tbody>tr>td, .salestable2>tfoot>tr>td {
    padding: 1px;
    line-height: 1.42857143;
    vertical-align: top;
    border-top: 1px solid #ddd;
}


</style>
<!-- PAGE TITLE -->

                <!-- END PAGE TITLE -->                
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap"  >
          
                    <div class="row">
                        <div class="col-md-8" style="margin-bottom: 0px!important;">
                            
                            <!-- START DEFAULT DATATABLE -->
                            <div class="panel panel-default" style="margin: 0px;">
                                <div class="panel-heading">                                
                <ul class="breadcrumb">
                    <li><a href="#">Sales Module </a></li>                    
                    <li class="active"><?php print $_GET['page_identity'];?></li>
                </ul> <b id="mess_box"></b>
                                    <ul class="panel-controls">
                                    <li><select  onchange="invoiceOrQuat(this.value);" name="invice_or_quotation" id="invice_or_quotation" class="form-control select">
            <option  value="Invoice">Invoice</option>
            <option value="Preorder">Preorder</option>
            <option value="Quotation">Quotation</option>
        </select></li>                        
                                </div>
                                <div class="panel-body" style="padding: 0px;">
                                <div class="table-responsive">
                                <table class="table salestable" style="margin-bottom: 0px !important;">

                                <input type="hidden" id="transport_cost" value="">
    <input type="hidden" id="product_id" value="">
    <input type="hidden" id="pipe_line_stock" value="">
    <input type="hidden" id="related_id" value="<?php print $_GET['related_id'];?>">
    <input type="hidden" id="related_code" value="<?php print $related_code;?>">
    <input type="hidden" id="previous_discount" value="<?php print $discount;?>">
    <input type="hidden" id="previous_vat" value="<?php print $total_vat;?>">
    <input type="hidden" id="brunch_id" value="<?php print $brunch_id;?>">


         <input type="hidden" id="customer_due" value="<?php print $due ;?>">

                         <input type="hidden" id="draft_code" value="">
               


<thead>
<tr>
<td >My Branch</td>
<td colspan="2"><input type="text" READONLY value="<?php print $brunch_name;?>"  class="form-control text-danger">
</td>

<td >Warehouse Branch</td>
<td colspan="2">

<select class="form-control select" id="dispatch_from_which_brunch" name = "dispatch_from_which_brunch" onchange="WarehouseWiseProductStock()" >
<?php 
$qry = $conn_me->prepare("SELECT * FROM `setup_brunch` where status = 'Active' ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) { ?>
<option <?php if($dispatch_from_which_brunch == $fetch['id'] ){ ?> selected="selected" <?php }else{ } ?>  value="<?php print $fetch['id'];?>"><?php print $fetch['brunch'] ;?></option>
<?php } ?>
</select>
</td>

</tr>

<tr>
<td >Select Customer</td>
<td >
<select id="customer_id" name="customer_id"   required onchange="fatch_customer_info('SALES_ENTRY')"  class="form-control select" data-live-search="true">
<option value = "">Select One</option>
<?php 
foreach ( $xml_customer_list->ROW as  $value ) { ?>
<option  <?php if($customer_id == $value['id'] ){ ?> selected="selected" <?php }else{ } ?>  value="<?php echo $value['id']; ?>" data-content="<b style='color: red;'><?php echo $value['mobile']; ?></b> - <b style='color: black;'><?php echo $value['shop_name']; ?></b> - <b style='color: darkorange;'><?php echo $value['address']; ?></b>">
  </option>
<?php } ?>

</select>
</td>
<td >
<a  href="sales/Customer-Setup/New" target="_blink" class="btn btn-danger" > <span class="fa fa-plus-circle"> </span></a>
</td>
<td >Product List</td>
<td colspan="2">

<input type="text" tabindex="1" class="form-control text-danger" onClick="this.setSelectionRange(0, this.value.length)" name="product_name" id="product_name" value="" onkeyup="navigate(this.value,event)">
<p id="search-listing"></p>

</td>

</tr>
</thead>
<tbody>
<tr>
<td >Mobile</td>
<td  colspan="2">
<input type="text" READONLY name="mobile" id="mobile" value="" class="form-control text-danger">
</td>
<td >Warehouse List</td>

<td><button type="button" class="btn btn-warning block" id="#exampleModalCenter" onclick="modal_wihout_refresh('load_data_in_modal','product_id','Warehouse List');">Branch W.H</button>
</td>
<td><button type="button" class="btn btn-warning block" id="#exampleModalCenter" onclick="modal_wihout_refresh('load_data_in_modal','product_id','Central Warehouse List');">Central W.H</button>
</td>
</tr>
<tr>
<td >Address</td>
<td  colspan="2">
<input type="text" READONLY  value="" id="address" name="address" class="form-control text-danger">
</td>
<td >Stock</td>
<td  colspan="2" >
<input type="number" READONLY name="stock_in_pcs" id="stock_in_pcs" value="" class="form-control text-danger">

</td>
</tr>



<tr>
<td >Limit</td>
<td  colspan="2">
<input type="number" READONLY  value="<?php print $creadit_limit;?>" id="creadit_limit" name="creadit_limit" class="form-control text-danger">
</td>
<td ><a  id="#exampleModalCenter" onclick="pipline_modal('load_data_in_modal','product_id','dispatch_from_which_brunch');">Saleable</a></td>
<td  colspan="2" >
<input type="number"  readonly  name="saleable"   id="saleable" value="" class="form-control text-danger">

</td>
</tr>



<tr>
<td >Note</td>
<td  colspan="2"> 
<input type="text"   value="" id="note" name="note" class="form-control">
</td>

<td >Quantity</td>
<td colspan="2" >
<input type="number"  tabindex="2" name="quantity"  onkeyup="sale_calculator();" id="quantity" value="" class="form-control">
</td>

</tr>
<tr>
<td>Purchase Rate</td>
<td  colspan="2">
<div class="form-group">
<div class="col-md-12">
<div class="input-group">
<span class="input-group-addon">
<input type="checkbox" id="checkValue"  onclick="showPurchasePrice();"/>
</span>
<input type="text" readonly class="form-control text-danger" name="show_purchase_price" id="show_purchase_price" value="">
</div>
</div>
</div></td>

<td >Sales Rate</td>
<td  colspan="2">
<input type="number"  READONLY name="product_retaile_price"  id="product_retaile_price" value="0.00" class="form-control text-danger">
</td>
</tr>

<tr>
<td></td>
<td  colspan="2"></td>

<td >Recommended Price</td>
<td  colspan="2">
<input type="number" tabindex="3" onkeyup="sale_calculator();"  name="recommended_price" id="recommended_price" value="0.00" class="form-control">
</td>
</tr>


<tr>
<td ></td>
<td  colspan="2" ></td>
<td colspan="2"><input style="font-size:20px;" type="button" id="each_item_total" value="0.00" class="btn btn-info block"></td>

</tr>

<tr>

<?php if($_GET['related_id'] == 'New' ){ ?>

<td colspan="3"><input type="button" tabindex="4" name="saveDraft" id="saveDraft" onclick="saveDraft()" value="Save as Draft" class ="btn btn-danger pull-left" >

<?php } ?>
</td>
<td  colspan="3" >

<input type="button" tabindex="4" name="add_cart_sale" id="add_cart_sale" value="Add++" class ="btn btn-primary pull-right" >

</td>
</tr>
</tbody>
</table>
</div>           </div>
                            </div>
                            <!-- END DEFAULT DATATABLE -->
                         
                            <div class="panel panel-default">
                               
                                <div class="panel-body faq" id="refresh_cart">
                                    
                                <?php include('cart_sales_entry.php');?>
                                </div>
                            </div>
                            
                        </div>                        
                        
<div class="col-md-4" >
                        
                        <div class="panel panel-primary">
                            <div class="panel-body">
                               
                                <div class="push-up-10" >

                                <div class="table-responsive">

                                    <table class="table table-hover table-condensed">
                                   


                                        <tr>
                                            <th>Sales Person</th>
                                            <td>
                                                 <?php if($_SESSION['USER_TYPE'] == 'Admin' ) { ?>

                                            <select name="sales_person" class="form-control select" data-live-search="true" id="sales_person">
                                            <option value="">Select One</option>
                                            <?php 

                                            // SALES PERSON 
                                            $qry = $conn_me->prepare("SELECT A.`id`,B.`name` FROM `admin` A JOIN `setup_employee` B ON (`A`.`employee_id` = B.`id`)  
                                            WHERE A.hr_status = 'Active' AND A.brunch_id = '".$_SESSION['USER_BRUNCH']."' AND ( B.`designation` = '15' OR  B.`designation` = '12' OR   B.`designation` = '13'  OR  B.`designation` = '19' ) AND A.hr_status = 'Active' ");
                                            $qry->execute();
                                            $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
                                            foreach($fetch_list AS $fetch) { 

                                            ?>
                                            <option <?php if($sales_person_id == $fetch['id'] ){ ?> selected="selected" <?php }else{ } ?> value="<?php print $fetch['id'] ?>" ><?php print $fetch['name'];?></option>
                                            <?php } ?>
                                            </select>


                                               <?php   }else{
                                               
                                               
                                               ?>
                                                <input type="text" READONLY  value="<?php print $sales_person;?>"  class="form-control text-danger">
                                                
                                                <input type="hidden" id="sales_person" name="sales_person" value="<?php print $sales_person_id;?>">
                                                
                                                
                                                 <?php } ?>




                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Saved By</th>
                                            <td>
                                                <select name="sales_by" class="form-control select" data-live-search="true" id="sales_by">
                                                    <option value="">Select One</option>
                                                    <?php 


                                                    // SALES PERSON 
                                                     $qry = $conn_me->prepare("SELECT A.`id`,B.`name` FROM `admin` A JOIN `setup_employee` B ON (`A`.`employee_id` = B.`id`)  
                                                        WHERE A.hr_status = 'Active' AND A.brunch_id = '".$_SESSION['USER_BRUNCH']."' AND ( B.`designation` = '15' OR  B.`designation` = '12' OR   B.`designation` = '13'  OR  B.`designation` = '19' ) AND A.hr_status = 'Active' ");
                                                     $qry->execute();
                                                     $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
                                                      foreach($fetch_list AS $fetch) { 
                                                  
                                                        ?>
                                                        <option <?php if($sales_by == $fetch['id'] ){ ?> selected="selected" <?php }else{ } ?> value="<?php print $fetch['id'] ?>" ><?php print $fetch['name'];?></option>
                                                     <?php } ?>
                                                </select>
                                            </td>
                                        </tr>

                           

                                        
                                             <tr <?php if ($_SESSION['USER_TYPE'] == 'Admin' ){ ?> <?php }else{ ?> style="display: none;" <?php } ?> >


    <th>Date </th>
    <td >
    <input  type="text" value="<?php print $invoice_date;?>" id="invoice_date" name="invoice_date"  class="date form-control text-danger" >    
    </td>
    </tr>



                                        <tr>
                                            <th>Sub Total</th>
                                            <td><input type="number" READONLY  name="sub_total" id="sub_total" value="<?php print $cart_sub_total;?>" class="form-control text-info"></td>
                                        </tr>

                                        <tr style="display:none">
                                            <th>Transport  Type</th>
                                            <td><select class="select form-control" onchange="fatch_customer_info('SALES_ENTRY')" id="transport_cost_type">
                                                <option value="nogot_cost">Nogot</option>
                                                <option value="nogot_cost">Baki </option>
                                            </select></td>
                                        </tr>

                                    
                                        <tr  >
                                            <th>Transport  Cost <b id="estamated_cost"></b></th>
                                            <td><input type="number"  name="total_transport_cost"   onkeyup="sale_calculator();"   id="total_transport_cost" value="0.00" class="form-control"></td>
                                        </tr>
                                        <tr>
                                            <th>VAT </th>
                                            <td><input type="number"  onkeyup="sale_calculator();"  name="total_vat_cost"  id="total_vat_cost" value="<?php print  $total_vat ;?>" class="form-control"></td>
                                        </tr>

                                        <tr>
                                            <th> Invoice Price </th>
                                            <td><input type="number" READONLY  name="invoice_total" id="invoice_total" value="<?php print  $invoice_total ;?>" class="form-control text-info"></td>
                                        </tr>
                                      

                                   
                                        <tr>
                                            <th>Discount</th>
                                            <td><input type="number" onkeyup="sale_calculator();" name="discount" id="discount" value="<?php print  $discount ;?>" class="form-control"></td>
                                        </tr>

                                    <tr>
                                    <th> Invoice Payable </th>
                                    <td><input type="number" READONLY  name="invoice_payable" id="invoice_payable" value="<?php print  $invoice_payable ;?>" class="form-control text-info"></td>
                                    </tr>


                                      
                                        <tr>
                                            <th>Previous Due</th>
                                            <td><input type="number"   name="amount_due"  readonly id="amount_due" value="0.00" class="form-control text-info"></td>
                                        </tr>
                                     
                                        <tr>
                                            <th>Narration</th>
                                            <td><input type="text"   value="<?php print $narration ;?>" id="narration" name="narration" class="form-control">
</td>
                                        </tr>
                                    </table>
                                                      </div>
                                    <?php print $permission_management2['save_update_buton'];?>

                                    </div>                                       
                                </div>                                    
                            </div>
                        </div>
                            
                        
                        </div>
                    </div>
                                                            
                </div>
                <!-- END PAGE CONTENT WRAPPER -->     


      <!--IMPORTANT FOR PUSH NOTIFICATION -->     
<div id="load_push_mess"></div>

      <!--IMPORTANT FOR PUSH NOTIFICATION -->     

      <script>


document.getElementsByClassName("list-group-item")[0].classList.add("active");
window.onload = function() {
document.getElementById("product_name").focus();
};



</script>
<?php }else{

   print "Challan Already Generated or godown copy is ready";
}

?>





     