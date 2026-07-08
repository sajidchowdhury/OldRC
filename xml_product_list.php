<?php

/** create XML file */ 
$conn_me = Database::getInstance();

$productArray = array();




$qry = $conn_me->prepare("SELECT A.*,B.`category` AS `CategoryName`  FROM `setup_product` A LEFT JOIN `setup_category` B ON (A.`category_id` = B.`id`)  where A.`in_service` = 'checked'  ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) { 

    array_push($productArray, $fetch);
}


if(count($productArray)){

    createXMLfile($productArray);

}

/* free result set */




function createXMLfile($productArray){
  

  $prefix = 'P';


   $filePath = 'xml_productList.xml';

   $dom     = new DOMDocument('1.0', 'utf-8'); 

   $root      = $dom->createElement('ROWDATA'); 

   for($i=0; $i<count($productArray); $i++){
     
     $productId        =  $productArray[$i]['id'];  

     $productName = htmlspecialchars( $prefix . $productArray[$i]['code'] . ' ' .$productArray[$i]['product_name'] );

     $productCategory    =  $productArray[$i]['CategoryName']; 
     $pcs_in_cartoon    =  $productArray[$i]['pcs_in_cartoon']; 


     $product = $dom->createElement('ROW');
     $product->setAttribute('id', $productId);
     $product->setAttribute('product_name', $productName);
     $product->setAttribute('product_category', $productCategory);
     $product->setAttribute('pcs_in_cartoon', $pcs_in_cartoon);

     $root->appendChild($product);

   }

   $dom->appendChild($root); 

   $dom->save($filePath); 

 } 