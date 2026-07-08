<?php
require_once('auth.php');
include_once("clean.php");

include_once('function_query.php'); 

if(!empty($_FILES["fileToUpload"])){
    

  if($_SESSION['USER_TYPE'] == 'Admin' || $_SESSION['USER_TYPE'] == 'Editor'){

    $filename=$_FILES["fileToUpload"]["tmp_name"];    

     if($_FILES["fileToUpload"]["size"] > 0)
     {
        $file = fopen($filename, "r");
          while (($getData = fgetcsv($file, 10000, ",")) !== FALSE)
           {



            $prepaire_table = SETUP::SETUP_CODE_INSERT_DATA('raw_request_recipe_wise');
            $info_product = SETUP::SETUP_PRODUCT_BY_NAME($getData[1]);
            $accepting_delivery_date = date("Y-m-d", strtotime($getData[3]));  

            if($getData[5] == 'Factory' ){
              $info_send_to = SETUP::SETUP_FACTORY_BY_NAME($getData[6]);
              $send_to_id = $info_send_to['id'];

            }else if($getData[5] == 'Supplier'){
              $info_send_to = SETUP::SETUP_SUPPLIER_BY_NAME($getData[6]);
              $send_to_id = $info_send_to['id'];

            }else{
              $send_to_id = 'NOT_AVAILABE';
            }


            if($send_to_id == 'NOT_AVAILABE' ){
                    echo "The supplier or factory is not valid";
            }else{
              $query = $conn_me->prepare("SELECT *  FROM `receip_fg`  where `product_id` = '".$info_product['id']."' ORDER BY `id` DESC");
              $query->execute();
              $fetch_query = $query->fetchAll(PDO::FETCH_ASSOC);
              foreach($fetch_query AS $fetch){
  
                    $demand_qty = $fetch['quantity']*clean($getData[2]);
  
                  $statement = $conn_me->prepare("INSERT INTO `raw_request_recipe_wise_item` 
                  (   `demand_code`, `material_id`, `raw_recipe_wise_request_id`,`demand_quantity`, `date`, `time`, `poster`, `lastupdate`)
                  VALUES (:demand_code, :material_id, :raw_recipe_wise_request_id, :demand_quantity, :date, :time, :poster, :lastupdate  )
                  ");
                  
                  $statement->execute(
                    array(
                      ':demand_code'               =>  $prepaire_table['related_code'],
                      ':material_id'           =>  trim($fetch["raw_material_id"]),
                      ':raw_recipe_wise_request_id'           => $prepaire_table['last_id'],
                      ':demand_quantity'           =>  trim($demand_qty),
                      ':date'           =>  date("Y-m-d"),
                      ':time'           => date("H:i:s a"),
                      ':poster'           =>   $_SESSION['NEWERP_SESS_MEMBER_ID'],
                      ':lastupdate'           =>  $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] 
                  
                  
                    )
                  );
          
  
              }
  
                  
              $query2 = $conn_me->prepare("UPDATE `raw_request_recipe_wise` 
    
              SET
              `product_id` = '".$info_product['id']."',
              `accepting_delivery_date` ='".$accepting_delivery_date."',
              `batch_quantity` ='".clean($getData[2])."',
              `send_to` ='".clean($getData[5])."',
              `send_to_id` ='".clean($send_to_id)."',
              `user_given_invoiceno` ='".clean($getData[4])."',
              `invoice_date` ='" . date("Y-m-d") . "',
              `date` = '" . date("Y-m-d") . "',
              `time` =  '" . date("h:i:s a") . "',
              `poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
              `lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
              
              
              WHERE `id` = '".$prepaire_table['last_id']."'  ");
              
              $query2->execute();
              if(!isset($query2))
              {
                echo "Invalid File:Please Upload CSV File.";    
              }
              else {
                  echo "1";
              }
            }

            

        
            

            
        
           }
      
           fclose($file);  
     }


  }else{

 print "Only Admin or Editor Can Upload a file";
  }
    
  } else{
    print "Upload a CSV file";
  }  
 ?>