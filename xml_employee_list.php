<?php

/** create XML file */ 

$employeeArray = array();

$qry = $conn_me->prepare("SELECT A.*,B.`designation` AS `P_designation`  FROM `setup_employee` A JOIN `setup_designation` B ON (A.`designation` = B.`id`) ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) { 

    array_push($employeeArray, $fetch);
}


if(count($employeeArray)){

    createXMLfileEmployee($employeeArray);

}

/* free result set */




function createXMLfileEmployee($employeeArray){
  
   $filePath = 'xml_employee_list.xml';

   $dom     = new DOMDocument('1.0', 'utf-8'); 

   $root      = $dom->createElement('ROWDATA'); 

   for($i=0; $i<count($employeeArray); $i++){
     
     $employeeId        =  $employeeArray[$i]['id'];  

     $photo = htmlspecialchars($employeeArray[$i]['photo']);
     $code    =  $employeeArray[$i]['code']; 
     $name = htmlspecialchars($employeeArray[$i]['name']);
     $designation    =  $employeeArray[$i]['P_designation']; 
     $mob_no    =  $employeeArray[$i]['mob_no']; 
     $status    =  $employeeArray[$i]['status']; 


     $employee = $dom->createElement('ROW');
     $employee->setAttribute('id', $employeeId);
     $employee->setAttribute('photo', $photo);
     $employee->setAttribute('code', $code);
     $employee->setAttribute('name', $name);
     $employee->setAttribute('designation', $designation);
     $employee->setAttribute('mob_no', $mob_no);
     $employee->setAttribute('status', $status);

     $root->appendChild($employee);

   }

   $dom->appendChild($root); 

   $dom->save($filePath); 

 } 