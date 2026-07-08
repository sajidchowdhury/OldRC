<?php

include('function_query.php');



$conn_me = Database::getInstance();


$query = $conn_me->prepare("SELECT * FROM `account_transection`  where `id`= 7147  ");
$query->execute();
$fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch ) {

print $fetch['id'] . "<br>";

if($fetch['transection_type'] == 'INCOME' ){

  $field_name = "in_amount";
  $amount = $fetch['in_amount'];
}else{
  $field_name = "out_amount";
  $amount = $fetch['out_amount'];

}

QUICK_BALANCE::QUICK_OPENING_BALANCE($field_name,$fetch['transection_by'],$fetch['transection_by_id'],$amount,$fetch['transection_date'],$fetch['brunch_id']);




}