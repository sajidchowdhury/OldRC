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

            $employe_code = $getData[0];
            $ProvidentFund	 = $getData[1];
            $Advance	 = $getData[2];
            $Loan	 = $getData[3];
            $Absent	 = $getData[4];
            $Late  = $getData[5];
            $Attendance = $getData[6];


              $query = $conn_me->exec("INSERT INTO `quick_pay_slip` 
              ( 
                  `id`, `employe_code`,`ProvidentFund`, `Advance`, `Loan`, `Absent`, `Late`, `Attendance`) 
              VALUES
              (
                  '0',
                  '".$employe_code."',
                  '".$ProvidentFund."',
                  '".$Advance."',
                  '".$Loan."',
                  '".$Absent."',
                  '".$Late."',
                  '".$Attendance."'
              ) ");


              if(!isset($query))
              {
                echo "Invalid File:Please Upload CSV File.";    
              }
              else {
                  echo "1";
              }
              
            
           
            
    

        
           }
      
           fclose($file);  
     }

    
  } else{
    print "Upload a CSV file";
  }  
 ?>