<?php
require_once('auth.php');
include_once("clean.php");

include_once('function_query.php'); 

if(!empty($_FILES["fileToUpload"])){
    
    $filename=$_FILES["fileToUpload"]["tmp_name"];    

     if($_FILES["fileToUpload"]["size"] > 0)
     {

    
        $file = fopen($filename, "r");
          while (($getData = fgetcsv($file, 10000, ",")) !== FALSE)
           {

            $employee_id = $getData[0];
            $date =  str_replace("/","-","$getData[1]");
            $attendance_date =  date("Y-m-d", strtotime($date));  
            $check_in = date('h:i:s a', strtotime($getData[2]));
            $check_out = date('h:i:s a', strtotime($getData[3]));
            $real_time = $attendance_date . ' ' . $getData[2];

            if($getData[2] != '' && $getData[0] > 0 ){

              $query = $conn_me->exec("INSERT INTO `check_in_check_out` 
              ( 
                  `id`, `employee_id`,`attendance_date`, `check_in`, `check_out`, `status`, `brunch_id`, `date`, `time`, `poster`, `lastupdate`
              ) 
              VALUES
              (
                  '0',
                  '".$employee_id."',
                  '".$attendance_date."',
                  '".$check_in."',
                  '".$check_out."',
                  'Pending',
                  '" . $_SESSION['USER_BRUNCH'] . "',
                  '" . date("Y-m-d") . "',
                  '" . date("h:i:s a") . "',
                  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
                  '".$real_time."'
              ) ");


              if(!isset($query))
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

    
  } else{
    print "Upload a CSV file";
  }  
 ?>