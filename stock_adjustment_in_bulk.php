<?php
require_once('auth.php');
include_once("clean.php");

include_once('function_query.php'); 

if(!empty($_FILES["fileToUpload"])){
    

  if($_SESSION['USER_TYPE'] == 'Admin' ){

    $filename=$_FILES["fileToUpload"]["tmp_name"];    

     if($_FILES["fileToUpload"]["size"] > 0)
     {
        $file = fopen($filename, "r");
          while (($getData = fgetcsv($file, 10000, ",")) !== FALSE)
           {


    
            $query = $conn_me->prepare("SELECT `id` FROM `setup_product`  WHERE  CONCAT('P',code) = '".$getData[0]."' "); 
            $query->execute();
            $fetch_list = $query->fetch(PDO::FETCH_ASSOC);
            
            if(!empty($fetch_list['id'])){


                $adj_date = date("Y-m-d", strtotime($_POST['adjustment_date']));  
                $warehouse_id = $_POST['warehouse_id'];
                $product_id = $fetch_list['id'];

                if($getData[3] >= 0  ){
                $stock_in = abs($getData[3]); 
                $stock_out = 0 ;
                }else{
                $stock_in = 0; 
                $stock_out = abs($getData[3]); 
                }
     
  
                  $statement = $conn_me->prepare("INSERT INTO `balance_product` 
                  (   `product_id`, `warehouse_id`, `stock_in`, `stock_out`, `date`, `note`,`data_insert_date`,`time`,`poster`)
                  VALUES ( :product_id, :warehouse_id, :stock_in, :stock_out, :date, :note, :data_insert_date, :time, :poster  )
                  ");
                  
                  $statement->execute(
                    array(
                      ':product_id'               =>  $product_id,
                      ':warehouse_id'           =>  $warehouse_id,
                      ':stock_in'           => $stock_in,
                      ':stock_out'           =>  $stock_out,
                      ':date'           =>  $adj_date,
                      ':note'           => 'STOCK ADJUSTMENT',
                      ':data_insert_date'           =>  date("Y-m-d"),
                      ':time'           =>  date("h:i:s a"),
                      ':poster'           =>  $_SESSION['NEWERP_SESS_MEMBER_ID']
                  
                  
                    )
                  );
          
  


            }else{


            }

            

            
        
           }
      
           fclose($file);  
     }


  }else{

 print "Only Admin or Editor Can Upload a file";
  }
    
  } else{
    print "Upload an Excel file";
  }  
 ?>