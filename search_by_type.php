<?php
include_once('function_query.php'); 
$conn_me = Database::getInstance();



if ($_POST['SEARCH_BY'] == 'All' ){
    $CONTENT = '<input type="hidden" value="All" id="report_wise_code">';
      
    print json_encode(array ('level' => '','value' => $CONTENT));


}else if ($_POST['SEARCH_BY'] == 'Invoice-Timeline'){

    $CONTENT = '<input type="hidden" value="InvoiceTimeline" id="report_wise_code">
    <input  id="InvoiceTimeline" name = "InvoiceTimeline" value="InvoiceTimeline" type="hidden">';


    print json_encode(array ('level' => '','value' => $CONTENT));


    
}else if ($_POST['SEARCH_BY'] == 'Raw-Category'){

        $CONTENT = '<select class="form-control select" id="category_id" name = "category_id"  data-live-search="true">
        <option value="">Select One</option>';
    
            $qry = $conn_me->prepare("SELECT * FROM `setup_raw_material_category` ORDER BY `id` ASC");
            $qry->execute();
            $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
            foreach ($fetch_list as $fetch) { 
    
                $CONTENT .= '<option  value="'.$fetch['id'].'">' .$fetch['category']. '</option>';
    
            }
            $CONTENT .= '</select>';
            print json_encode(array ('level' => $_POST['SEARCH_BY'],'value' => $CONTENT));
    


}else if ($_POST['SEARCH_BY'] == 'Raw-Material'){



$xml_raw_material_list = simplexml_load_file("xml_raw_material_list.xml");

$CONTENT = '<input type="hidden" value="product_id" id="report_wise_code">
<select class="form-control select" id="product_id" name = "product_id"  data-live-search="true">
<option value="">Select One</option>';

foreach ( $xml_raw_material_list->ROW as  $value ) { 


        $CONTENT .= '<option  value="'.$value['id'].'">'.$value['product_category'] .':::'. $value['product_name'].'</option>';

    }
    $CONTENT .= '</select>';



print json_encode(array ('level' => $_POST['SEARCH_BY'],'value' => $CONTENT));

        
        }else if ($_POST['SEARCH_BY'] == 'Finished-Goods'){



    $xml_product_list = simplexml_load_file("xml_productList.xml");
    $CONTENT = '<input type="hidden" value="product_id" id="report_wise_code">
    <select class="form-control select" id="product_id" name = "product_id"  data-live-search="true">
    <option value="">Select One</option>';

foreach ( $xml_product_list->ROW as  $value ) { 


            $CONTENT .= '<option  value="'.$value['id'].'">'.$value['product_category'] .':::'. $value['product_name'].'</option>';

        }
        $CONTENT .= '</select>';



    print json_encode(array ('level' => $_POST['SEARCH_BY'],'value' => $CONTENT));


}else if ($_POST['SEARCH_BY'] == 'Multipal-Ledger-Wise'){

    $CONTENT = '<input type="hidden" value="ledger_id" id="report_wise_code"><select class="form-control select" id="ledger_id" name = "ledger_id[]"  data-live-search=true class="selectpicker" multiple data-selected-text-format="count>1" data-all="false">
    ';
   
           $qry = $conn_me->prepare("SELECT *  FROM `setup_ladger_head` ORDER BY `id` ASC");
           $qry->execute();
           $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
           foreach ($fetch_list as $fetch) { 

            $CONTENT .= '<option  value="'.$fetch['id'].'"> '.$fetch['name'].'</option>';
   
           }
           $CONTENT .= '</select>';
           $CONTENT .= '<input type="button" class="btn btn-info"  onclick="selectAllOptions(\'ledger_id\')" value="Select All" >
           <input type="button"  class="btn btn-danger" onclick="unselectAllOptions(\'ledger_id\')" value="Unselect All" >';
           print json_encode(array ('level' => 'Ledger-List','value' => $CONTENT));
   



}else if ($_POST['SEARCH_BY'] == 'Multipal-Head-Wise'){

    $CONTENT = '<input type="hidden" value="head_id" id="report_wise_code"><select class="form-control select" id="head_id" name = "head_id[]"  data-live-search=true class="selectpicker" multiple data-selected-text-format="count>1" data-all="false">
    ';
   
           $qry = $conn_me->prepare("SELECT *  FROM `setup_ac_head` ORDER BY `id` ASC");
           $qry->execute();
           $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
           foreach ($fetch_list as $fetch) { 

            $CONTENT .= '<option  value="'.$fetch['id'].'"> '.$fetch['account_head'].'</option>';
   
           }
           $CONTENT .= '</select>';
           $CONTENT .= '<input type="button" class="btn btn-info"  onclick="selectAllOptions(\'head_id\')" value="Select All" >
           <input type="button"  class="btn btn-danger" onclick="unselectAllOptions(\'head_id\')" value="Unselect All" >';
           print json_encode(array ('level' => 'AC-List','value' => $CONTENT));
   




}else if ($_POST['SEARCH_BY'] == 'Receive-Account'){


    $CONTENT = '<input type="hidden" value="account_head_id" id="report_wise_code"><select class="form-control select" id="account_head_id" name = "account_head_id"  data-live-search="true">
    <option value="">Select One</option>';

        $qry = $conn_me->prepare("SELECT * FROM `setup_ac_head` where `account_type` = 'INCOME' ORDER BY `id` ASC");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) { 

            $CONTENT .= '<option  value="'.$fetch['id'].'">' .$fetch['account_head']. '</option>';

        }
        $CONTENT .= '</select>';
        print json_encode(array ('level' => $_POST['SEARCH_BY'],'value' => $CONTENT));



}else if ($_POST['SEARCH_BY'] == 'Payment-Account'){


    $CONTENT = '<input type="hidden" value="account_head_id" id="report_wise_code"><select class="form-control select" id="account_head_id" name = "account_head_id"  data-live-search="true">
    <option value="">Select One</option>';

        $qry = $conn_me->prepare("SELECT * FROM `setup_ac_head` where `account_type` = 'EXPENSE' ORDER BY `id` ASC");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) { 

            $CONTENT .= '<option  value="'.$fetch['id'].'">' .$fetch['account_head']. '</option>';

        }
        $CONTENT .= '</select>';
        print json_encode(array ('level' => $_POST['SEARCH_BY'],'value' => $CONTENT));



}else if ($_POST['SEARCH_BY'] == 'Warehouse-Wise'){

    $CONTENT = '<input type="hidden" value="warehouse_id" id="report_wise_code"><select class="form-control select" id="warehouse_id" name = "warehouse_id"  data-live-search="true">
    <option value="">Select One</option>';

        $qry = $conn_me->prepare("SELECT * FROM `setup_warehouse` where status = 1 ORDER BY `id` ASC");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) { 

            $CONTENT .= '<option  value="'.$fetch['id'].'">' .$fetch['name']. '</option>';

        }
        $CONTENT .= '</select>';
        print json_encode(array ('level' => $_POST['SEARCH_BY'],'value' => $CONTENT));


}else if ($_POST['SEARCH_BY'] == 'FG-Category'){

    $CONTENT = '<input type="hidden" value="category_id" id="report_wise_code"><select class="form-control select" id="category_id" name = "category_id"  data-live-search="true">
    <option value="">Select One</option>';

        $qry = $conn_me->prepare("SELECT * FROM `setup_category` ORDER BY `id` ASC");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) { 

            $CONTENT .= '<option  value="'.$fetch['id'].'">' .$fetch['category']. '</option>';

        }
        $CONTENT .= '</select>';
        print json_encode(array ('level' => $_POST['SEARCH_BY'],'value' => $CONTENT));

    }else if ($_POST['SEARCH_BY'] == 'Multiple-FG-Category'){

        $CONTENT = '<input type="hidden" value="category_id" id="report_wise_code"><select data-live-search="true" class="select selectpicker" multiple id="category_id" name = "category_id[]"  data-live-search="true">
        <option value="">Select One</option>';
    
            $qry = $conn_me->prepare("SELECT * FROM `setup_category` ORDER BY `id` ASC");
            $qry->execute();
            $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
            foreach ($fetch_list as $fetch) { 
    
                $CONTENT .= '<option  value="'.$fetch['id'].'">' .$fetch['category']. '</option>';
    
            }
            $CONTENT .= '</select>';
            print json_encode(array ('level' => $_POST['SEARCH_BY'],'value' => $CONTENT));



    }else if ($_POST['SEARCH_BY'] == 'LEAVE_TYPE_WISE_DATA'){

        $CONTENT = '';
        if($_POST['leave_type_id'] == '3'  ){

            $level = 'Employee List';
            $CONTENT .= '<select  id="employee_id" name="employee_id[]" data-live-search="true" class="select selectpicker" multiple="multiple" data-selected-text-format="count>2" data-all="false">';

$qry = $conn_me->prepare("SELECT * FROM `setup_employee`  ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) {

$CONTENT .= '<option value="'.$fetch['id'].'"><b style="font-color:blue">'.$fetch['name'].'</b></option>';

 }
 $CONTENT .= '</select>';

 $CONTENT2 = '<input type="button" class="btn btn-info" onclick="SelecteAll(\'employee_id\');" value="Select All">';

}else if( $_POST['leave_type_id'] == '5' || $_POST['leave_type_id'] == '6' ){

    $level = 'Employee List';
    $CONTENT .= '<select onchange="findLeaveLimit()" id="employee_id" name="employee_id" data-live-search="true" class="select selectpicker"  >
    <option value="">Select One</option>
    ';

$qry = $conn_me->prepare("SELECT * FROM `setup_employee`  ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) {

$CONTENT .= '<option value="'.$fetch['id'].'"><b style="font-color:blue">'.$fetch['name'].'</b></option>';

}
$CONTENT .= '</select>';

$CONTENT2 = '';



        }else{

            $level = 'Employee List';
            $CONTENT .= '<input type="text" class="form-control text-danger" value="FOR ALL EMPLOYEES" readonly id="employee_id"> ';
            $CONTENT2 = '';
        }



        print json_encode(array ('level' => $level, 'value' => $CONTENT,'value2' => $CONTENT2));

    }else if ($_POST['SEARCH_BY'] == 'Upazila-Wise'){

        $CONTENT = '<input type="hidden" value="upazila_id" id="report_wise_code"><select class="form-control select" id="upazila_id" name = "upazila_id"  data-live-search=true class="selectpicker" >
        <option value="">Select One</option>
        
        ';
        
               $qry = $conn_me->prepare("SELECT * FROM `upazilas` ORDER BY `id` ASC");
               $qry->execute();
               $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
               foreach ($fetch_list as $fetch) { 
        
                   $CONTENT .= '<option  value="'.$fetch['id'].'">' .$fetch['name']. '</option>';
        
               }
               $CONTENT .= '</select>';
            
        
               print json_encode(array ('level' => 'Upazila-Wise','value' => $CONTENT));
        



    }else if ($_POST['SEARCH_BY'] == 'District-Wise'){


        $CONTENT = '<input type="hidden" value="district_id" id="report_wise_code"><select class="form-control select" id="district_id" name = "district_id"  data-live-search=true class="selectpicker" >
        <option value="">Select One</option>
        
        ';
        
               $qry = $conn_me->prepare("SELECT * FROM `districts` ORDER BY `id` ASC");
               $qry->execute();
               $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
               foreach ($fetch_list as $fetch) { 
        
                   $CONTENT .= '<option  value="'.$fetch['id'].'">' .$fetch['name']. '</option>';
        
               }
               $CONTENT .= '</select>';
            
        
               print json_encode(array ('level' => 'District-Wise','value' => $CONTENT));
        

               

    }else if ($_POST['SEARCH_BY'] == 'Division-Wise'){


$CONTENT = '<input type="hidden" value="division_id" id="report_wise_code"><select class="form-control select" id="division_id" name = "division_id"  data-live-search=true class="selectpicker" >
<option value="">Select One</option>

';

       $qry = $conn_me->prepare("SELECT * FROM `divisions` ORDER BY `id` ASC");
       $qry->execute();
       $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
       foreach ($fetch_list as $fetch) { 

           $CONTENT .= '<option  value="'.$fetch['id'].'">' .$fetch['name']. '</option>';

       }
       $CONTENT .= '</select>';
    

       print json_encode(array ('level' => 'Division-Wise','value' => $CONTENT));



    }else if ($_POST['SEARCH_BY'] == 'Multiple-Division-Wise'){



        $CONTENT = '<input type="hidden" value="area_id" id="report_wise_code"><select class="form-control select" id="area_id" name = "area_id[]"  data-live-search=true class="selectpicker" multiple data-selected-text-format="count>1" data-all="false">
        ';
       
               $qry = $conn_me->prepare("SELECT * FROM `divisions` ORDER BY `id` ASC");
               $qry->execute();
               $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
               foreach ($fetch_list as $fetch) { 
       
                   $CONTENT .= '<option  value="'.$fetch['id'].'">' .$fetch['name']. '</option>';
       
               }
               $CONTENT .= '</select>';
               $CONTENT .= '<input type="button" class="btn btn-info"  onclick="selectAllOptions(\'area_id\')" value="Select All" >
               <input type="button"  class="btn btn-danger" onclick="unselectAllOptions(\'area_id\')" value="Unselect All" >';
               print json_encode(array ('level' => 'Division-Wise','value' => $CONTENT));
       



    }else if ($_POST['SEARCH_BY'] == 'Multiple-District-Wise'){


        $CONTENT = '<input type="hidden" value="area_id" id="report_wise_code"><select class="form-control select" id="area_id" name = "area_id[]"  data-live-search=true class="selectpicker" multiple data-selected-text-format="count>1" data-all="false">
        ';
       
               $qry = $conn_me->prepare("SELECT * FROM `districts` ORDER BY `id` ASC");
               $qry->execute();
               $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
               foreach ($fetch_list as $fetch) { 
       
                   $CONTENT .= '<option  value="'.$fetch['id'].'">' .$fetch['name']. '</option>';
       
               }
               $CONTENT .= '</select>';
               $CONTENT .= '<input type="button" class="btn btn-info"  onclick="selectAllOptions(\'area_id\')" value="Select All" >
               <input type="button"  class="btn btn-danger" onclick="unselectAllOptions(\'area_id\')" value="Unselect All" >';


               print json_encode(array ('level' => 'District-Wise','value' => $CONTENT));

    }else if ($_POST['SEARCH_BY'] == 'Multiple-Upazila-Wise'){

  $CONTENT = '<input type="hidden" value="area_id" id="report_wise_code"><select class="form-control select" id="area_id" name = "area_id[]"  data-live-search=true class="selectpicker" multiple data-selected-text-format="count>1" data-all="false">
        ';
       
               $qry = $conn_me->prepare("SELECT A.*,B.name as districtname FROM `upazilas` A JOIN districts B ON (A.district_id = B.id)  ORDER BY A.`id` ASC;");
               $qry->execute();
               $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
               foreach ($fetch_list as $fetch) { 
       
                   $CONTENT .= '<option  value="'.$fetch['id'].'">' .$fetch['name']. '  ( ' .$fetch['districtname']. ' )</option>';
       
               }
               $CONTENT .= '</select>';
               $CONTENT .= '<input type="button" class="btn btn-info"  onclick="selectAllOptions(\'area_id\')" value="Select All" >
               <input type="button"  class="btn btn-danger" onclick="unselectAllOptions(\'area_id\')" value="Unselect All" >';

               
               print json_encode(array ('level' => 'Upzilla-Wise','value' => $CONTENT));

               

    }else if ($_POST['SEARCH_BY'] == 'Area-Wise'){

$CONTENT = '';
$CONTENT .='<div class="row">';
$CONTENT .='<div class="col-md-3">';
$CONTENT .= '<input type="hidden" value="area_wise" id="report_wise_code">
Divisions: ';

$CONTENT .= '<select style="width:100%!imortant" onchange="find_area_data(\'districts\',\'Districts\',\'division_id\',this.value);" id="division_id" name="division_id[]" data-live-search=true class="selectpicker" multiple data-selected-text-format="count>1" data-all="false" >
<option value="">Select One</option>';
$qry = $conn_me->prepare("SELECT * FROM `divisions`  ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
 foreach($fetch_list AS $fetch) { 
   $CONTENT .=  '<option  value="'.$fetch['id'].'">'.$fetch['name'].'</option>'; 
 } 
 $CONTENT .= '</select>';
$CONTENT .='</div>';
$CONTENT .='<div class="col-md-3">Districts:<div id="Load_districts_value">';

$CONTENT .= '<select style="width:100%!imortant" onchange="find_area_data(\'upazilas\',\'Upazila\',\'district_id\',this.value);" id="district_id" name="district_id[]" data-live-search=true class="selectpicker" multiple data-selected-text-format="count>1" data-all="false" >
<option value="">Select One</option>';

$qry = $conn_me->prepare("SELECT * FROM `districts`  ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
 foreach($fetch_list AS $fetch) { 
   $CONTENT .=  '<option value="'.$fetch['id'].'">'.$fetch['name'].'</option>'; 
 } 
$CONTENT .= '</select>
</div></div>';
$CONTENT .='<div class="col-md-3">Upazila:<div id="Load_upazilas_value">';

$CONTENT .= '<select style="width:100%!imortant"  onchange="find_area_data(\'unions\',\'Union\',\'upazilla_id\',this.value);" id="upazilla_id" name="upazilla_id[]" data-live-search=true class="selectpicker" multiple data-selected-text-format="count>1" data-all="false" >
<option value="">Select One</option>';


$qry = $conn_me->prepare("SELECT * FROM `upazilas`  ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
 foreach($fetch_list AS $fetch) { 
   $CONTENT .=  '<option value="'.$fetch['id'].'">'.$fetch['name'].'</option>'; 
 } 
$CONTENT .= '</select></td>

</div>';
$CONTENT .='</div>';

$CONTENT .='<div class="col-md-3">Union:<div id="Load_unions_value">';
$CONTENT .= '<select style="width:100%!imortant" id= "union_id" name="union_id[]" data-live-search=true class="selectpicker" multiple data-selected-text-format="count>1" data-all="false">
<option value="">Select One</option>';
$qry = $conn_me->prepare("SELECT * FROM `unions`  ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
 foreach($fetch_list AS $fetch) { 
   $CONTENT .=  '<option value="'.$fetch['id'].'">'.$fetch['name'].'</option>'; 
 } 
$CONTENT .= '</select></td>

</div>';
$CONTENT .='</div>';



$CONTENT .='</div>';

print json_encode(array ('level' => 'Area Wise','value' => $CONTENT));

}else if ($_POST['SEARCH_BY'] == 'Multipal-Branch-Wise'){

    $CONTENT = '<input type="hidden" value="brunch_id" id="report_wise_code"><select class="form-control select" id="brunch_id" name = "brunch_id[]"  data-live-search=true class="selectpicker" multiple data-selected-text-format="count>1" data-all="false">
    ';

    $qry = $conn_me->prepare("SELECT *  FROM `setup_brunch`  where status = 'Active'  ORDER BY `id` ASC");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch) { 

        $CONTENT .= '<option  value="'.$fetch['id'].'"> '.$fetch['brunch'].' </option>';

    }
        $CONTENT .= '</select>';


        $CONTENT .= '<input type="button" class="btn btn-info"  onclick="selectAllOptions(\'brunch_id\')" value="Select All" >
        <input type="button"  class="btn btn-danger" onclick="unselectAllOptions(\'brunch_id\')" value="Unselect All" >';



    print json_encode(array ('level' => 'Branch-Wise','value' => $CONTENT));

}else if ($_POST['SEARCH_BY'] == 'Multipal-Product-Wise'){

    $CONTENT = '<input type="hidden" value="product_id" id="report_wise_code"><select class="form-control select" id="product_id" name = "product_id[]"  data-live-search=true class="selectpicker" multiple data-selected-text-format="count>1" data-all="false">
    ';
   
    $xml_product_list = simplexml_load_file("xml_productList.xml");

    foreach ( $xml_product_list->ROW as  $value ) { 
   
               $CONTENT .= '<option  value="'.$value['id'].'">'.$value['product_category'] .':::'. $value['product_name'].'</option>';
   
           }
           $CONTENT .= '</select>';
           $CONTENT .= '<input type="button" class="btn btn-info"  onclick="selectAllOptions(\'product_id\')" value="Select All" >
           <input type="button"  class="btn btn-danger" onclick="unselectAllOptions(\'product_id\')" value="Unselect All" >';
           print json_encode(array ('level' => 'Product-Wise','value' => $CONTENT));


}else if ($_POST['SEARCH_BY'] == 'Multipal-Category-Wise'){

    $CONTENT = '<input type="hidden" value="category_id" id="report_wise_code"><select class="form-control select" id="category_id" name = "category_id[]"  data-live-search=true class="selectpicker" multiple data-selected-text-format="count>1" data-all="false">
 ';

        $qry = $conn_me->prepare("SELECT * FROM `setup_category` ORDER BY `id` ASC");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) { 

            $CONTENT .= '<option  value="'.$fetch['id'].'">' .$fetch['category']. '</option>';

        }
        $CONTENT .= '</select>';
        $CONTENT .= '<input type="button" class="btn btn-info"  onclick="selectAllOptions(\'category_id\')" value="Select All" >
        <input type="button"  class="btn btn-danger" onclick="unselectAllOptions(\'category_id\')" value="Unselect All" >';
        print json_encode(array ('level' => 'Category-Wise','value' => $CONTENT));



}else if ($_POST['SEARCH_BY'] == 'Multipal-Warehouse-Wise'){

    $CONTENT = '<input type="hidden" value="warehouse_id" id="report_wise_code"><select class="form-control select" id="warehouse_id" name = "warehouse_id[]"  data-live-search=true class="selectpicker" multiple data-selected-text-format="count>1" data-all="false">
   ';

        $qry = $conn_me->prepare("SELECT * FROM `setup_warehouse` where status = 1 ORDER BY `id` ASC");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) { 

            $CONTENT .= '<option  value="'.$fetch['id'].'">' .$fetch['name']. '</option>';

        }
        $CONTENT .= '</select>';
        $CONTENT .= '</select>';
        $CONTENT .= '<input type="button" class="btn btn-info"  onclick="selectAllOptions(\'warehouse_id\')" value="Select All" >
        <input type="button"  class="btn btn-danger" onclick="unselectAllOptions(\'warehouse_id\')" value="Unselect All" >';
        print json_encode(array ('level' => 'Warehouse-Wise','value' => $CONTENT));

}else if ($_POST['SEARCH_BY'] == 'Multipal-Customer-Wise'){

    
    $xml_customer_list = simplexml_load_file("xml_customerList.xml");
    $CONTENT = '<input type="hidden" value="customer_id" id="report_wise_code">';
    $CONTENT .= '<select  class="form-control select"  id= "customer_id" name="customer_id[]" data-live-search=true class="selectpicker" multiple data-selected-text-format="count>1" data-all="false">';
foreach ( $xml_customer_list->ROW as  $value ) { 

    $CONTENT .= '<option value="' . $value['id'] . '"  data-content="<b style=\'color: red;\'>'.$value['mobile'].'</b> - <b style=\'color: black;\'>'.$value['shop_name'] .'</b> - <b style=\'color: darkorange;\'> ' . $value['upazila_name'] . ' </b> - <b style=\'color: orchid;\'> ' .  $value['district_name'] .'  </b> - <b style=\'color: turquoise;\'> ' . $value['division_name'] . '  </b>">
    </option>' ;


        }
        $CONTENT .= '</select>';



        $CONTENT .= '<input type="button" class="btn btn-info"  onclick="selectAllOptions(\'customer_id\')" value="Select All" >
        <input type="button"  class="btn btn-danger" onclick="unselectAllOptions(\'customer_id\')" value="Unselect All" >';

    print json_encode(array ('level' => 'Customer Wise','value' => $CONTENT));


}else if ($_POST['SEARCH_BY'] == 'Customer-Wise' || $_POST['SEARCH_BY'] == 'FOR-CUSTOMER'){

    $xml_customer_list = simplexml_load_file("xml_customerList.xml");

    $CONTENT = '<input type="hidden" value="customer_id" id="report_wise_code">
    <select class="form-control select" id="customer_id" name = "customer_id"  data-live-search="true">
    <option value="">Select One</option>';

foreach ( $xml_customer_list->ROW as  $value ) { 
           
    $CONTENT .= '<option value="' . $value['id'] . '"  data-content="<b style=\'color: red;\'>'.$value['mobile'].'</b> - <b style=\'color: black;\'>'.$value['shop_name'] .'</b> - <b style=\'color: darkorange;\'> ' . $value['upazila_name'] . ' </b> - <b style=\'color: orchid;\'> ' .  $value['district_name'] .'  </b> - <b style=\'color: turquoise;\'> ' . $value['division_name'] . '  </b>">
    </option>' ;



        }
        $CONTENT .= '</select>';


    print json_encode(array ('level' => $_POST['SEARCH_BY'],'value' => $CONTENT));

}else if ($_POST['SEARCH_BY'] == 'Multipal-Supplier-Wise'){

    $CONTENT = '<input type="hidden" value="supplier_id" id="report_wise_code"><select class="form-control select" id="supplier_id" name = "supplier_id[]"  data-live-search=true class="selectpicker" multiple data-selected-text-format="count>1" data-all="false">
    ';
   
           $qry = $conn_me->prepare("SELECT * FROM `setup_supplier`");
           $qry->execute();
           $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
           foreach ($fetch_list as $fetch) { 

            $CONTENT .= '<option  value="'.$fetch['id'].'">'.$fetch['mobile'] . '-' . $fetch['supplier_name'].'
            </option>';   
           }
           $CONTENT .= '</select>';
           $CONTENT .= '<input type="button" class="btn btn-info"  onclick="selectAllOptions(\'supplier_id\')" value="Select All" >
           <input type="button"  class="btn btn-danger" onclick="unselectAllOptions(\'supplier_id\')" value="Unselect All" >';
           print json_encode(array ('level' => 'Employee-List','value' => $CONTENT));
   



}else if ($_POST['SEARCH_BY'] == 'Supplier-Wise'){


    $CONTENT = ' <input type="hidden" value="supplier_id" id="report_wise_code"><select id="supplier_id" name="supplier_id"  class="form-control select" data-live-search="true">
    <option value="">Select One</option>';
    
    $qry = $conn_me->prepare("SELECT * FROM `setup_supplier`  ");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch) { 
    
        $CONTENT .= '<option  value="'.$fetch['id'].'">'.$fetch['mobile'] . '-' . $fetch['supplier_name'].'
    </option>';
    }
    
    $CONTENT .= '</select>';
    
    print json_encode(array ('level' => $_POST['SEARCH_BY'],'value' => $CONTENT));


}else if ($_POST['SEARCH_BY'] == 'Multipal-Sales-Person' || $_POST['SEARCH_BY'] == 'Multipal-Sales-By'){


    $CONTENT = '<input type="hidden" value="user_id" id="report_wise_code"><select class="form-control select" id="user_id" name = "user_id[]"  data-live-search=true class="selectpicker" multiple data-selected-text-format="count>1" data-all="false">
    ';
   
           $qry = $conn_me->prepare("SELECT `employee_id`,`id` FROM `admin` ORDER BY `id` ASC");
           $qry->execute();
           $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
           foreach ($fetch_list as $fetch) { 
            $info_employee = SETUP::SETUP_EMPLOYEEY($fetch['employee_id']);

            $CONTENT .= '<option  value="'.$fetch['id'].'"> '.$info_employee['employee_code'].' '.$info_employee['name'].'</option>';
   
           }
           $CONTENT .= '</select>';
           $CONTENT .= '<input type="button" class="btn btn-info"  onclick="selectAllOptions(\'user_id\')" value="Select All" >
           <input type="button"  class="btn btn-danger" onclick="unselectAllOptions(\'user_id\')" value="Unselect All" >';
           print json_encode(array ('level' => 'Employee-List','value' => $CONTENT));
   
           

}else if ($_POST['SEARCH_BY'] == 'By-Sales-Person' || $_POST['SEARCH_BY'] == 'By-Sales-By' || $_POST['SEARCH_BY'] == 'Employee-Wise' || $_POST['SEARCH_BY'] == 'Payment-Record'){

    $CONTENT = '<input type="hidden" value="user_id" id="report_wise_code"><select class="form-control select" id="user_id" name = "user_id"  data-live-search="true">
    <option value="">Select One</option>';

    $qry = $conn_me->prepare("SELECT `employee_id`,`id` FROM `admin` ORDER BY `id` ASC");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch) { 
        $info_employee = SETUP::SETUP_EMPLOYEEY($fetch['employee_id']);

        $CONTENT .= '<option  value="'.$fetch['id'].'"> '.$info_employee['employee_code'].' '.$info_employee['name'].'</option>';

    }
        $CONTENT .= '</select>';

    print json_encode(array ('level' => $_POST['SEARCH_BY'],'value' => $CONTENT));

}else if ($_POST['SEARCH_BY'] == 'Department-Wise'){

    $CONTENT = '<input type="hidden" value="department_id" id="report_wise_code"><select class="form-control select" id="department_id" name = "department_id"  data-live-search="true">
    <option value="">Select One</option>';

    $qry = $conn_me->prepare("SELECT *  FROM `setup_department` ORDER BY `id` ASC");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch) { 

        $CONTENT .= '<option  value="'.$fetch['id'].'"> '.$fetch['department'].' </option>';

    }
        $CONTENT .= '</select>';

    print json_encode(array ('level' => $_POST['SEARCH_BY'],'value' => $CONTENT));



}else if ($_POST['SEARCH_BY'] == 'Branch-Wise'){

    $CONTENT = '<input type="hidden" value="brunch_id" id="report_wise_code"><select class="form-control select" id="brunch_id" name = "brunch_id"  data-live-search="true">
    <option value="">Select One</option>';

    $qry = $conn_me->prepare("SELECT *  FROM `setup_brunch`  where status = 'Active'  ORDER BY `id` ASC");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch) { 

        $CONTENT .= '<option  value="'.$fetch['id'].'"> '.$fetch['brunch'].' </option>';

    }
        $CONTENT .= '</select>';

    print json_encode(array ('level' => $_POST['SEARCH_BY'],'value' => $CONTENT));





}else if ($_POST['SEARCH_BY'] == 'By-Sales-Invoice'){

    $CONTENT = '<input type="hidden" value="sales_invoice_id" id="report_wise_code">
    <input class="form-control" id="sales_invoice_id" name = "sales_invoice_id" value="" type="text">';
   

    print json_encode(array ('level' => $_POST['SEARCH_BY'],'value' => $CONTENT));


}else if ($_POST['SEARCH_BY'] == 'By-Purchase-Invoice'){


    $CONTENT = '<input type="hidden" value="purchase_invoice_id" id="report_wise_code">
    <input class="form-control" id="purchase_invoice_id" name = "purchase_invoice_id" value="" type="text">';
   

    print json_encode(array ('level' => $_POST['SEARCH_BY'],'value' => $CONTENT));


}else{
    $CONTENT = '<input type="hidden" value="" id="report_wise_code">';
   
    print json_encode(array ('level' => '','value' => ''));

    }