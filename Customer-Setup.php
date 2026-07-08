<?php

include('xml_customer_list.php');
$xml_customer_list = simplexml_load_file("xml_customerList.xml");



$CODE = SETUP::SETUP_CODE('setup_customer');

    if($_GET['related_id'] == 'New'){
      

$customer_code = $CODE['only_code'];
$customer_name = '';
$address = '';
$mobile =  '';
$customer_type =  '';
$email =  '';
$district_id =  '';
$creadit_limit  =  '50000';
$division_id   =  '';
$related_id = 'new_id';
$upazila_id = '';
$union_id = '';
$shop_name = '';
$QUERY1= '';
$QUERY2= '';
$QUERY3= '';
$check_value = 1;
$in_service = '';
$sales_person_id = '';


}else{

$DATA  = SETUP::SETUP_CUSTOMER($_GET['related_id']);

$customer_name = $DATA['customer_name'];
$address =  $DATA['address'];
$mobile =  $DATA['mobile'];
$customer_type =  $DATA['customer_type'];
$email =  $DATA['email'];
$related_id = $DATA['id'];
$creadit_limit =$DATA['creadit_limit'];
$division_id =$DATA['division_id'];
$district_id =$DATA['district_id'];
$upazila_id =$DATA['upazila_id'];
$union_id =$DATA['union_id'];
$shop_name =$DATA['shop_name'];
$customer_code = $CODE['prefix'] . $DATA['code'];
$QUERY1= " WHERE `division_id` = '".$division_id."' " ;
$QUERY2= " WHERE `district_id` = '".$district_id."' " ;
$QUERY3= " WHERE `upazilla_id` = '".$upazila_id."' " ;
$check_value =  $DATA['check_value'];
$in_service =  $DATA['in_service'];
$sales_person_id = $DATA['sales_person_id'];
}
    $content = '<div class="row">
    <div class="col-md-12">

        <div class="panel panel-default">
            <div class="panel-body">
            <form id="myform">
            
            <div class="alert alert-success alert-dismissible" id="success" style="display:none;">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
          </div>
          <input type="hidden" name="related_id" id="related_id" value="'.$related_id.'" >
            <table class="table table-hover table-condensed table-striped table-bordered">
                ';
                
                
$content .= '<tr>';
$content .= '<th>Customer Id </th>';
$content .= '<td>';
$content .= '<input type="text" readonly class="form-control text-danger" value="'.$customer_code.'" >';
$content .= '</td>';
$content .= '<th class="form-group required control-label">Mobile</th>';
$content .= '<td><input type="text"  class="form-control" value="'.$mobile.'" name="mobile" id="mobile"><span class="help-block" id="mess_box_mobile"></span></td>';      
$content .= '</tr>';
            

$content .= '<tr>';
$content .= '<th class="form-group required control-label">Shop Name </th>';
$content .= '<td><input type="text" class="form-control" value="'.$shop_name.'" required name="shop_name" id="shop_name"><span class="help-block" id="mess_box_shop_name"></span>
</td>';
$content .= '<th class="form-group required control-label">Customer Name</th>';
$content .= '<td> <input type="text" class="form-control" value="'.$customer_name.'" required name="customer_name" id="customer_name"><span class="help-block" id="mess_box_customer_name"></span></td>';      
$content .= '</tr>';


$content .= '<tr>';
$content .= '<th >Address</th>';
$content .= '<td><input type="text" class="form-control" value="'.$address.'" name="address" id="address"></td>';
$content .= '<th>Email</th>';
$content .= '<td> <input type="text" class="form-control" value="'.$email.'" name="email" id="email"></td>';      
$content .= '</tr>';

$content .= '<tr>';
$content .= '<th  class="form-group required control-label">Divisions</th>';
$content .= '<td><select id="division_id" name="division_id" required class="select form-control" class="form-control" data-live-search="true"
onchange="find_related_data(\'districts\',\'Districts\',\'division_id\',this.value);" name="dist" id="dist" data-rel="chosen">
<option value="">Select One</option>';
$qry = $conn_me->prepare("SELECT * FROM `divisions`  ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
 foreach($fetch_list AS $fetch) { 
   $content .=  '<option ';
   if($division_id == $fetch['id']){
       $content .=  'selected="selected"';
   }else{

   }

    $content .=  ' value="'.$fetch['id'].'">'.$fetch['name'].'</option>'; 
 } 
 $content .= '</select><span class="help-block" id="mess_box_division_id"></span></td>';
$content .= '<th  class="form-group required control-label">Districts</th>';
$content .= '<td id="Load_districts_value"><select id= "district_id" required class="select form-control" class="form-control" data-live-search="true"  data-rel="chosen">
<option value="">Select One</option>';
$qry = $conn_me->prepare("SELECT * FROM `districts` $QUERY1 ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
 foreach($fetch_list AS $fetch) { 
   $content .=  '<option ';
   if($district_id == $fetch['id']){
       $content .=  'selected="selected"';
   }else{

   }

    $content .=  ' value="'.$fetch['id'].'">'.$fetch['name'].'</option>'; 
 } 
$content .= '</select></td>';      
$content .= '</tr>';


$content .= '<tr>


<th id="Load_upazilas_level" class="form-group required control-label">Upazila</th>';

$content .= '<td id="Load_upazilas_value">';

$content .= '<select id= "upazilla_id" required class="select form-control" class="form-control" data-live-search="true"  data-rel="chosen">
<option value="">Select One</option>';
$qry = $conn_me->prepare("SELECT * FROM `upazilas` $QUERY2  ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
 foreach($fetch_list AS $fetch) { 
   $content .=  '<option ';
   if($upazila_id == $fetch['id']){
       $content .=  'selected="selected"';
   }else{

   }

    $content .=  ' value="'.$fetch['id'].'">'.$fetch['name'].'</option>'; 
 } 
$content .= '</select></td>';    
$content .='</td>	

<th id="Load_unions_level" class="form-group  control-label">Union</th>';

$content .= '<td id="Load_unions_value">';

$content .= '<select id= "union_id" required class="select form-control" class="form-control" data-live-search="true"  data-rel="chosen">
<option value="">Select One</option>';

$qry = $conn_me->prepare("SELECT * FROM `unions`  $QUERY3 ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
 foreach($fetch_list AS $fetch) { 
   $content .=  '<option ';
   if($union_id == $fetch['id']){
       $content .=  'selected="selected"';
   }else{

   }

    $content .=  ' value="'.$fetch['id'].'">'.$fetch['name'].'</option>'; 
 } 
$content .= '</select></td>';  
$content .='</td>


</tr>';

      
$content .= '<tr>
<th>In Service</th>
<td><label class="switch">
<input type="checkbox"   id="in_service" name="in_service" value="'.$check_value.'" '.$in_service.' />

<span></span>
</label></td>


</tr>';

$content .= '<tr>


<th>Sales Person</th>
<td>
<select name="sales_person" class="form-control select" data-live-search="true" id="sales_person">
                                            <option value="">Select One</option>';
                                          
                                            $qry = $conn_me->prepare("SELECT B.`name`,B.id FROM `setup_employee` B 
                                            WHERE  ( B.`designation` = '15' OR  B.`designation` = '12' OR   B.`designation` = '13'  OR  B.`designation` = '19' )  ");
                                            $qry->execute();
                                            $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
                                            foreach($fetch_list AS $fetch) { 

                                            
                                            $content .= '<option ';
                                            if($sales_person_id == $fetch['id'] ){ 
                                            $content .= 'selected="selected" '; }else{ }
                                            $content .= 'value="'.$fetch['id'].'" >'.$fetch['name'].'</option>';
                                          } 
                                            $content .= '</select>
</td>';
$content .= '<th  class="form-group required control-label">Credit Limit</th>';
$content .= '<td> <input type="text" class="form-control" value="'.$creadit_limit.'" name="creadit_limit" id="creadit_limit"><span class="help-block" id="mess_box_creadit_limit"></span></td>

</tr>';
         

        $content .='  <tr><td style="text-align:center;" colspan="4"><input type="button" name="save_customer" id="save_customer" class="btn btn-primary" value="Save Customer"></td> </tr>

        </table>
        </form>  

        </div>

        </div>
        </div>';

$content .= '<div class="row">
<div class="col-md-12">

    <div class="panel panel-default">
        <div class="panel-body" id="load_table">
       
        <table class="table datatable">
        <thead>
        <th>Sl</th>
        <th>Name</th>
        <th>Shop Name</th>
        <th>Contact Number</th>
        <th>Address</th>
                <th>Sales Person Ref.</th>

        <th>Created Date</th>
                <th>Update Date</th>

        <th>Action</th>
        </thead>
            <tbody>'; 
$sl =1;
$qry = $conn_me->prepare("SELECT A.*, 
       CASE 
           WHEN A.sales_person IS NULL THEN ''
           ELSE B.name  
       END AS sales_person_name
FROM setup_customer A
LEFT JOIN setup_employee B ON A.sales_person = B.id

");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ( $fetch_list  as  $value ) { 

 $date = !empty($value['date']) 
        ? date("d/m/Y", strtotime($value['date'])) 
        : ' ';
        
     
    $content .= '<tr>
    <th>'.$sl++.'</th>
    <th>'.$value['customer_name'].'</th>
    <th>'.$value['shop_name'].'</th>
    <th>'.$value['mobile'].'</th>
    <th>'.$value['address'].'</th>
        <th>'.$value['sales_person_name'].'</th>

    <th>'.$date.'</th>
        <th>'.$value['lastupdate'].'</th>';

    
                 
    $content .= '<td><a href="sales/Customer-Setup/'.$value['id'].'" ><i class="fa fa-edit danger"></i><a></td>';

  $content .= ' </tr>';


}
                
                  
$content .= '</tbody> 
      </table>
      </div>
            
    </div>

</div>
</div>';


print $content;
?>



