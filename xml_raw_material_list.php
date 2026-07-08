<?php

/** create XML file */ 

$productArray = array();

$conn_me = Database::getInstance();



$qry = $conn_me->prepare("SELECT A.*,B.`category` AS `CategoryName`  FROM `setup_raw_material` A JOIN `setup_raw_material_category` B ON (A.`category_id` = B.`id`)   ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);

/* free result set */


if ($qry->rowCount() == 0) {
  // Create a dummy entry
  $dummyEntry = array(
      'id' => 'no data',
      'product_name' => 'no data',
      'product_category' => 'no data'
  );

  array_push($productArray, $dummyEntry);
} else {
  // Populate productArray with fetched data
  foreach ($fetch_list as $fetch) { 
      array_push($productArray, $fetch);
  }
}

if (!empty($productArray)) {
  createXMLfile($productArray);
}



function createXMLfile($productArray){
  $prefix = 'Rw';
  $filePath = 'xml_raw_material_list.xml';
  $dom = new DOMDocument('1.0', 'utf-8');
  $dom->formatOutput = true; // Set formatOutput to true

  $root = $dom->createElement('ROWDATA');

  for ($i = 0; $i < count($productArray); $i++) {
      $productId = $productArray[$i]['id'];
      $productName = htmlspecialchars($prefix . $productArray[$i]['code'] . ' ' . $productArray[$i]['material_name']);
      $productCategory = $productArray[$i]['CategoryName'];

      $product = $dom->createElement('ROW');
      $product->setAttribute('id', $productId);
      $product->setAttribute('product_name', $productName);
      $product->setAttribute('product_category', $productCategory);

      $root->appendChild($product);
  }

  $dom->appendChild($root);
  $dom->save($filePath);
}
