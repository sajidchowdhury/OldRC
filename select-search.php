<?php 
include('auth.php');
include('connect_me.php');


if(!isset($_POST['searchTerm'])){

$qry = $conn_me->prepare("SELECT * FROM `setup_product` where `in_service` = 'checked' LIMIT 10");
$qry->execute();

}else{

    $qry = $conn_me->prepare("SELECT * FROM `setup_product` where `in_service` = 'checked' and `product_name` = '".$_POST['searchTerm']."' ");
    $qry->execute();
}


$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) {  
$data[] =  array(
    'id' => $fetch['id'],
    'text' => $fetch['product_name'] 
);
}


echo json_encode($data);