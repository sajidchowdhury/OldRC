<?php

/** create XML file */ 
$conn_me = Database::getInstance();



$customerArray = array();

$qry = $conn_me->prepare("SELECT A.*,B.`name` AS `division_name`,C.`name` AS `district_name`,D.`name` AS `upazila_name`,E.`name` AS `union_name`

FROM `setup_customer` A
LEFT JOIN `divisions` B ON (A.`division_id` = B.`id`)
LEFT JOIN `districts` C ON (A.`district_id` = C.`id`)
LEFT JOIN `upazilas` D ON (A.`upazila_id` = D.`id`)
LEFT JOIN `unions` E ON (A.`union_id` = E.`id`)
WHERE A.`in_service` = 'checked' ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch) { 

    array_push($customerArray, $fetch);
}


if(count($customerArray)){

    createXMLfileCustomer($customerArray);

}

/* free result set */


function xmlSafe($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_XML1, 'UTF-8');
}

function createXMLfileCustomer($customerArray){

    $filePath = 'xml_customerList.xml';

    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->formatOutput = true;

    $root = $dom->createElement('ROWDATA');

    foreach ($customerArray as $row) {

        $customer = $dom->createElement('ROW');

        $customer->setAttribute('id', xmlSafe($row['id']));
        $customer->setAttribute('code', xmlSafe($row['code']));
        $customer->setAttribute('shop_name', xmlSafe($row['shop_name']));
        $customer->setAttribute('customer_name', xmlSafe($row['customer_name']));
        $customer->setAttribute('creadit_limit', xmlSafe($row['creadit_limit']));
        $customer->setAttribute('customer_type', xmlSafe($row['customer_type']));
        $customer->setAttribute('address', xmlSafe($row['address']));
        $customer->setAttribute('mobile', xmlSafe($row['mobile']));
        $customer->setAttribute('division_name', xmlSafe($row['division_name']));
        $customer->setAttribute('district_name', xmlSafe($row['district_name']));
        $customer->setAttribute('upazila_name', xmlSafe($row['upazila_name']));
        $customer->setAttribute('union_name', xmlSafe($row['union_name']));

        $root->appendChild($customer);
    }

    $dom->appendChild($root);
    $dom->save($filePath);
}
