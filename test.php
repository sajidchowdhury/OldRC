<?php 
include('function_query.php');

?>
 <link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css"/>

 <?php

$content = '';

$date1 = '2012-01-01';
$date2 = '2030-01-01'; 


$content .= '<table class="table">';

$queryB = $conn_me->prepare("SELECT * FROM `setup_brunch` "  );
$queryB->execute();
$fetchAllB = $queryB->fetchAll(PDO::FETCH_ASSOC);
$content = '';
foreach($fetchAllB as $brunch_id ){

$content .= '<tr><th colspan="6">  BRUNCH ID ' . $brunch_id['id'] .'  </th></tr>';


$queryX = $conn_me->prepare("SELECT date FROM `balance_customer` where brunch_id = '".$brunch_id['id']."' and date between '".$date1."' and '".$date2."'    GROUP BY date  order by date asc"  );
$queryX->execute();
$fetchAllX = $queryX->fetchAll(PDO::FETCH_ASSOC);

foreach($fetchAllX as $fetchX ){

$content .= '<tr><th colspan="6">  DATE ' . $fetchX['date'] .'  </th></tr>';


$query1 = $conn_me->prepare("SELECT id FROM `sales_invoice` where invoice_date = '".$fetchX['date']."' and brunch_id = '".$brunch_id['id']."'  "  );
$query1->execute();
$fetch1 = $query1->fetchAll(PDO::FETCH_ASSOC);

foreach($fetch1 as $fetchX1 ){

$query2 = $conn_me->prepare("SELECT sum(invoice_amount) as INAMOUNT  FROM `balance_customer` where date = '".$fetchX1['date']."' and brunch_id = '".$brunch_id['id']."' group by date   "  );
$query2->execute();
$fetch2 = $query2->fetch(PDO::FETCH_ASSOC);

$receive_amount = $fetch2['INAMOUNT'];

$invoice_price = FIND::TOTAL_SALES_INVOICE_PRICE($fetchX1['id']);

$diffA = number_format((float)( $invoice_price['price']  - $receive_amount  ), 2, '.', '');

$colorA = $receive_amount == $invoice_price ? 'green' : 'red' ;


$content .= '<tr>

<th style="color:'.$colorA.'">'.$fetchX['date'].'</th>
<th style="color:'.$colorA.'">'.$invoice_price['price'].'</th>
<th style="color:'.$colorA.'">'.$receive_amount.'</th>

<th style="color:'.$colorA.'">'.$diffA.'</th>
</tr>';

}



}


}


print $content; 
/*
$content = '';

$date1 = '2012-01-01';
$date2 = '2030-01-01'; 


$content .= '<table class="table">';

$queryB = $conn_me->prepare("SELECT * FROM `setup_brunch` "  );
$queryB->execute();
$fetchAllB = $queryB->fetchAll(PDO::FETCH_ASSOC);

foreach($fetchAllB as $brunch_id ){


$content .= '<tr><th colspan="6">  BRUNCH ID ' . $brunch_id['id'] .'  </th></tr>';


$queryX = $conn_me->prepare("SELECT transection_date FROM `account_transection` where brunch_id = '".$brunch_id['id']."' and transection_date between '".$date1."' and '".$date2."' and transection_by = 'Cash'   GROUP BY transection_date  order by transection_date asc"  );
$queryX->execute();
$fetchAllX = $queryX->fetchAll(PDO::FETCH_ASSOC);

foreach($fetchAllX as $fetchX ){

$query1 = $conn_me->prepare("SELECT sum(in_amount) as INAMOUNT , sum(out_amount) as outamount FROM `balance_transection` where date = '".$fetchX['transection_date']."' and brunch_id = '".$brunch_id['id']."' and transection_by = 'Cash'  group by date "  );
$query1->execute();
$fetch1 = $query1->fetch(PDO::FETCH_ASSOC);



$balance_in_amount1 = !empty($fetch1['INAMOUNT']) ? $fetch1['INAMOUNT'] : 0 ; 
$balance_out_amount1 = !empty($fetch1['outamount']) ? $fetch1['outamount'] : 0 ; 



$query2 = $conn_me->prepare("SELECT sum(in_amount) as INAMOUNT , sum(out_amount) as outamount FROM `account_transection` where transection_date = '".$fetchX['transection_date']."' and brunch_id = '".$brunch_id['id']."' and transection_by = 'Cash' group by transection_date   "  );
$query2->execute();
$fetch2 = $query2->fetch(PDO::FETCH_ASSOC);


$tr_in_amount1 = !empty($fetch2['INAMOUNT']) ? $fetch2['INAMOUNT'] : 0 ; 
$tr_out_amount1 = !empty($fetch2['outamount']) ? $fetch2['outamount'] : 0 ; 

$colorA = $balance_in_amount1 == $tr_in_amount1 ? 'green' : 'red' ;
$colorB = $balance_out_amount1 == $tr_out_amount1 ? 'green' : 'red' ;


$diffA = number_format((float)( $tr_in_amount1  - $balance_in_amount1  ), 2, '.', '');
$diffB = number_format((float)( $tr_out_amount1  - $balance_out_amount1  ), 2, '.', '');




$content .= '<tr>

<th>'.$sl1++.'</th>
<th>'.$fetchX['transection_date'].'</th>
<th style="color:'.$colorA.'">'.$tr_in_amount1.'</th>
<th style="color:'.$colorA.'">'.$balance_in_amount1.'</th>

<th style="color:'.$colorA.'" >'.$diffA.'</th>

<th style="color:'.$colorB.'">'.$tr_out_amount1.'</th>
<th style="color:'.$colorB.'">'.$balance_out_amount1.'</th>

<th style="color:'.$colorB.'" >'.$diffB.'</th>


</tr>';



}

}

print $content ; 

/*
$content = '';

$date1 = '2012-01-01';
$date2 = '2030-01-01'; 


$content .= '<table class="table">';

$queryB = $conn_me->prepare("SELECT * FROM `setup_brunch` "  );
$queryB->execute();
$fetchAllB = $queryB->fetchAll(PDO::FETCH_ASSOC);

foreach($fetchAllB as $brunch_id ){


$content .= '<tr><th colspan="6">  BRUNCH ID ' . $brunch_id['id'] .'  </th></tr>';


$queryX = $conn_me->prepare("SELECT transection_date FROM `account_transection` where brunch_id = '".$brunch_id['id']."' and transection_date between '".$date1."' and '".$date2."' and transection_by = 'Cash'   GROUP BY transection_date  order by transection_date asc"  );
$queryX->execute();
$fetchAllX = $queryX->fetchAll(PDO::FETCH_ASSOC);

$content .= '<tr>

<th>Sl</th>
<th>Date</th>
<th >Opening Cash</th>
<th >TOday </th>

<th>Closing</th>

</tr>';

foreach($fetchAllX as $fetchX ){

    $opening_cash = TEST_BOOK::calculateOpeningBalance($fetchX['transection_date'], 'Cash',$brunch_id['id']);
    $today_cash = TEST_BOOK::calculateTodayTransection($fetchX['transection_date'], 'Cash',$brunch_id['id']);
    $closing_cash = $opening_cash + $today_cash ; 

  

$content .= '<tr>

<th>'.$sl1++.'</th>
<th>'.$fetchX['transection_date'].'</th>
<th >'.$opening_cash.'</th>
<th >'.$today_cash.'</th>

<th  >'.$closing_cash.'</th>

</tr>';



}

}

print $content ; 
/*
$content = '';

$date1 = '2012-01-01';
$date2 = '2030-01-01'; 


$content .= '<table class="table">';

$queryB = $conn_me->prepare("SELECT * FROM `setup_brunch` "  );
$queryB->execute();
$fetchAllB = $queryB->fetchAll(PDO::FETCH_ASSOC);

foreach($fetchAllB as $brunch_id ){


$content .= '<tr><th colspan="6">  BRUNCH ID ' . $brunch_id['id'] .'  </th></tr>';


$queryX = $conn_me->prepare("SELECT transection_date FROM `account_transection` where brunch_id = '".$brunch_id['id']."' and transection_date between '".$date1."' and '".$date2."' and transection_by = 'Cash'   GROUP BY transection_date  order by transection_date asc"  );
$queryX->execute();
$fetchAllX = $queryX->fetchAll(PDO::FETCH_ASSOC);

foreach($fetchAllX as $fetchX ){

$query1 = $conn_me->prepare("SELECT sum(in_amount) as INAMOUNT , sum(out_amount) as outamount FROM `balance_transection` where date = '".$fetchX['transection_date']."' and brunch_id = '".$brunch_id['id']."' and transection_by = 'Cash'  group by date "  );
$query1->execute();
$fetch1 = $query1->fetch(PDO::FETCH_ASSOC);



$balance_in_amount1 = !empty($fetch1['INAMOUNT']) ? $fetch1['INAMOUNT'] : 0 ; 
$balance_out_amount1 = !empty($fetch1['outamount']) ? $fetch1['outamount'] : 0 ; 



$query2 = $conn_me->prepare("SELECT sum(in_amount) as INAMOUNT , sum(out_amount) as outamount FROM `account_transection` where transection_date = '".$fetchX['transection_date']."' and brunch_id = '".$brunch_id['id']."' and transection_by = 'Cash' group by transection_date   "  );
$query2->execute();
$fetch2 = $query2->fetch(PDO::FETCH_ASSOC);


$tr_in_amount1 = !empty($fetch2['INAMOUNT']) ? $fetch2['INAMOUNT'] : 0 ; 
$tr_out_amount1 = !empty($fetch2['outamount']) ? $fetch2['outamount'] : 0 ; 

$colorA = $balance_in_amount1 == $tr_in_amount1 ? 'green' : 'red' ;
$colorB = $balance_out_amount1 == $tr_out_amount1 ? 'green' : 'red' ;


$diffA = number_format((float)( $tr_in_amount1  - $balance_in_amount1  ), 2, '.', '');
$diffB = number_format((float)( $tr_out_amount1  - $balance_out_amount1  ), 2, '.', '');




$content .= '<tr>

<th>'.$sl1++.'</th>
<th>'.$fetchX['transection_date'].'</th>
<th style="color:'.$colorA.'">'.$tr_in_amount1.'</th>
<th style="color:'.$colorA.'">'.$balance_in_amount1.'</th>

<th style="color:'.$colorA.'" >'.$diffA.'</th>

<th style="color:'.$colorB.'">'.$tr_out_amount1.'</th>
<th style="color:'.$colorB.'">'.$balance_out_amount1.'</th>

<th style="color:'.$colorB.'" >'.$diffB.'</th>


</tr>';



}

}

print $content ; 

/*

$content = '';

$date1 = '2012-01-01';
$date2 = '2030-01-01'; 


$content .= '<table class="table">';

$queryB = $conn_me->prepare("SELECT * FROM `setup_brunch` "  );
$queryB->execute();
$fetchAllB = $queryB->fetchAll(PDO::FETCH_ASSOC);

foreach($fetchAllB as $brunch_id ){

$content .= '<tr><th colspan="6">  BRUNCH ID ' . $brunch_id['id'] .'  </th></tr>';

$content .= '<tr>

<th>Sl</th>
<th>Date</th>
<th>IN AMOUNT (TRANSECTON) </th>
<th>IN AMOUNT (BALANCE) </th>

<th> DIFFREANCE (IN AMOUNT) </th>



</tr>';

$sl = 1; 
$queryX = $conn_me->prepare("SELECT transection_date,transection_to_id FROM `account_transection` where transection_date between '".$date1."' and '".$date2."' and transection_to = 'Customer' AND  brunch_id = '".$brunch_id['id']."'  GROUP BY transection_date  order by transection_date asc"  );
$queryX->execute();
$fetchAllX = $queryX->fetchAll(PDO::FETCH_ASSOC);


foreach($fetchAllX as $fetchX ){
$content .= '<tr><th colspan="6">  CUSTOMER ID ' . $fetchX['transection_to_id'] .'  </th></tr>';


$query1 = $conn_me->prepare("SELECT sum(receive_amount) as INAMOUNT  FROM `balance_customer` where date = '".$fetchX['transection_date']."' and brunch_id = '".$brunch_id['id']."'  and customer_id = '".$fetchX['transection_to_id']."'  group by date "  );
$query1->execute();
$fetch1 = $query1->fetch(PDO::FETCH_ASSOC);



$balance_in_amount1 = !empty($fetch1['INAMOUNT']) ? $fetch1['INAMOUNT'] : 0 ; 


$query2 = $conn_me->prepare("SELECT sum(in_amount) as INAMOUNT  FROM `account_transection` where transection_date = '".$fetchX['transection_date']."' and brunch_id = '".$brunch_id['id']."' and data_inserted_from = 'CUSTOMER-TRANSACTION'  and transection_to_id = '".$fetchX['transection_to_id']."'  group by transection_date   "  );
$query2->execute();
$fetch2 = $query2->fetch(PDO::FETCH_ASSOC);

$tr_in_amount1 = !empty($fetch2['INAMOUNT']) ? $fetch2['INAMOUNT'] : 0 ; 

$colorA = $balance_in_amount1 == $tr_in_amount1 ? 'green' : 'red' ;


$diffA = number_format((float)( $tr_in_amount1  - $balance_in_amount1  ), 2, '.', '');



$content .= '<tr>

<th>'.$sl++.'</th>
<th>'.$fetchX['transection_date'].'</th>
<th style="color:'.$colorA.'">'.$tr_in_amount1.'</th>
<th style="color:'.$colorA.'">'.$balance_in_amount1.'</th>

<th style="color:'.$colorA.'" >'.$diffA.'</th>




</tr>';

}



}

print $content; 
/*


/*
$content = '';

$date1 = '2012-01-01';
$date2 = '2030-01-01'; 


$content .= '<table class="table">';

$queryB = $conn_me->prepare("SELECT * FROM `setup_brunch` "  );
$queryB->execute();
$fetchAllB = $queryB->fetchAll(PDO::FETCH_ASSOC);

foreach($fetchAllB as $brunch_id ){



$query = $conn_me->prepare("SELECT * FROM `setup_bank` "  );
$query->execute();
$fetchAll = $query->fetchAll(PDO::FETCH_ASSOC);

foreach($fetchAll as $fetch ){

$content .= '<tr><th colspan="6"> BANK ID '.$fetch['id']. ' >>>> BRUNCH ID ' . $brunch_id['id'] .'  </th></tr>';
$content .= '<tr>

<th>Sl</th>
<th>Date</th>
<th>IN AMOUNT (TRANSECTON) </th>
<th>IN AMOUNT (BALANCE) </th>

<th> DIFFREANCE (IN AMOUNT) </th>


<th>OUT AMOUNT (TRANSECTON) </th>
<th>OUT AMOUNT (BALANCE) </th>

<th> DIFFREANCE (OUT AMOUNT) </th>

</tr>';


$queryX = $conn_me->prepare("SELECT transection_date FROM `account_transection` where brunch_id = '".$brunch_id['id']."' and transection_date between '".$date1."' and '".$date2."' and transection_by = 'Bank' and transection_by_id = '".$fetch['id']."'  GROUP BY transection_date  order by transection_date asc"  );
$queryX->execute();
$fetchAllX = $queryX->fetchAll(PDO::FETCH_ASSOC);

foreach($fetchAllX as $fetchX ){

$query1 = $conn_me->prepare("SELECT sum(in_amount) as INAMOUNT , sum(out_amount) as outamount FROM `balance_transection` where date = '".$fetchX['transection_date']."' and brunch_id = '".$brunch_id['id']."' and transection_by = 'Bank' and transection_by_id = '".$fetch['id']."' group by date "  );
$query1->execute();
$fetch1 = $query1->fetch(PDO::FETCH_ASSOC);



$balance_in_amount1 = !empty($fetch1['INAMOUNT']) ? $fetch1['INAMOUNT'] : 0 ; 
$balance_out_amount1 = !empty($fetch1['outamount']) ? $fetch1['outamount'] : 0 ; 



$query2 = $conn_me->prepare("SELECT sum(in_amount) as INAMOUNT , sum(out_amount) as outamount FROM `account_transection` where transection_date = '".$fetchX['transection_date']."' and brunch_id = '".$brunch_id['id']."' and transection_by = 'Bank' and transection_by_id = '".$fetch['id']."' group by transection_date   "  );
$query2->execute();
$fetch2 = $query2->fetch(PDO::FETCH_ASSOC);


$tr_in_amount1 = !empty($fetch2['INAMOUNT']) ? $fetch2['INAMOUNT'] : 0 ; 
$tr_out_amount1 = !empty($fetch2['outamount']) ? $fetch2['outamount'] : 0 ; 

$colorA = $balance_in_amount1 == $tr_in_amount1 ? 'green' : 'red' ;
$colorB = $balance_out_amount1 == $tr_out_amount1 ? 'green' : 'red' ;


$diffA = number_format((float)( $tr_in_amount1  - $balance_in_amount1  ), 2, '.', '');
$diffB = number_format((float)( $tr_out_amount1  - $balance_out_amount1  ), 2, '.', '');




$content .= '<tr>

<th>'.$sl1++.'</th>
<th>'.$fetchX['transection_date'].'</th>
<th style="color:'.$colorA.'">'.$tr_in_amount1.'</th>
<th style="color:'.$colorA.'">'.$balance_in_amount1.'</th>

<th style="color:'.$colorA.'" >'.$diffA.'</th>

<th style="color:'.$colorB.'">'.$tr_out_amount1.'</th>
<th style="color:'.$colorB.'">'.$balance_out_amount1.'</th>

<th style="color:'.$colorB.'" >'.$diffB.'</th>


</tr>';



}


}

}


foreach($fetchAllB as $brunch_id ){



$query = $conn_me->prepare("SELECT * FROM `setup_mobile_banking` "  );
$query->execute();
$fetchAll = $query->fetchAll(PDO::FETCH_ASSOC);

foreach($fetchAll as $fetch ){

$content .= '<tr><th colspan="6"> MOBILE TRANSECTON '.$fetch['id'].'  >>>> BRUNCH ID ' . $brunch_id['id'] .'   </th></tr>';
$content .= '<tr>

<th>Sl</th>
<th>Date</th>
<th>IN AMOUNT (TRANSECTON) </th>
<th>IN AMOUNT (BALANCE) </th>

<th> DIFFREANCE (IN AMOUNT) </th>


<th>OUT AMOUNT (TRANSECTON) </th>
<th>OUT AMOUNT (BALANCE) </th>

<th> DIFFREANCE (OUT AMOUNT) </th>

</tr>';


$queryX = $conn_me->prepare("SELECT transection_date FROM `account_transection` where brunch_id = '".$brunch_id['id']."' and transection_date between '".$date1."' and '".$date2."' and transection_by = 'Bank' and transection_by_id = '".$fetch['id']."'  GROUP BY transection_date  order by transection_date asc"  );
$queryX->execute();
$fetchAllX = $queryX->fetchAll(PDO::FETCH_ASSOC);

foreach($fetchAllX as $fetchX ){

$query1 = $conn_me->prepare("SELECT sum(in_amount) as INAMOUNT , sum(out_amount) as outamount FROM `balance_transection` where date = '".$fetchX['transection_date']."' and brunch_id = '".$brunch_id['id']."' and transection_by = 'Bank' and transection_by_id = '".$fetch['id']."' group by date "  );
$query1->execute();
$fetch1 = $query1->fetch(PDO::FETCH_ASSOC);



$balance_in_amount1 = !empty($fetch1['INAMOUNT']) ? $fetch1['INAMOUNT'] : 0 ; 
$balance_out_amount1 = !empty($fetch1['outamount']) ? $fetch1['outamount'] : 0 ; 



$query2 = $conn_me->prepare("SELECT sum(in_amount) as INAMOUNT , sum(out_amount) as outamount FROM `account_transection` where transection_date = '".$fetchX['transection_date']."' and brunch_id = '".$brunch_id['id']."' and transection_by = 'Bank' and transection_by_id = '".$fetch['id']."' group by transection_date   "  );
$query2->execute();
$fetch2 = $query2->fetch(PDO::FETCH_ASSOC);


$tr_in_amount1 = !empty($fetch2['INAMOUNT']) ? $fetch2['INAMOUNT'] : 0 ; 
$tr_out_amount1 = !empty($fetch2['outamount']) ? $fetch2['outamount'] : 0 ; 

$colorA = $balance_in_amount1 == $tr_in_amount1 ? 'green' : 'red' ;
$colorB = $balance_out_amount1 == $tr_out_amount1 ? 'green' : 'red' ;


$diffA = number_format((float)( $tr_in_amount1  - $balance_in_amount1  ), 2, '.', '');
$diffB = number_format((float)( $tr_out_amount1  - $balance_out_amount1  ), 2, '.', '');




$content .= '<tr>

<th>'.$sl1++.'</th>
<th>'.$fetchX['transection_date'].'</th>
<th style="color:'.$colorA.'">'.$tr_in_amount1.'</th>
<th style="color:'.$colorA.'">'.$balance_in_amount1.'</th>

<th style="color:'.$colorA.'" >'.$diffA.'</th>

<th style="color:'.$colorB.'">'.$tr_out_amount1.'</th>
<th style="color:'.$colorB.'">'.$balance_out_amount1.'</th>

<th style="color:'.$colorB.'" >'.$diffB.'</th>


</tr>';



}


}

}
print $content; 
 /*
$content = '';


$brunch_id = 1 ; 
$date1 = '2012-01-01';
$date2 = '2030-01-01'; 


$query = $conn_me->prepare("SELECT transection_date FROM `account_transection` where brunch_id = '".$brunch_id."' and transection_date between '".$date1."' and '".$date2."' GROUP BY transection_date  order by transection_date asc"  );
$query->execute();
$fetchAll = $query->fetchAll(PDO::FETCH_ASSOC);


$content .= '<table class="table">';


$content .= '<tr><th colspan="6"> CASH TRANSECTON </th></tr>';
$content .= '<tr>

<th>Sl</th>
<th>Date</th>
<th>IN AMOUNT (TRANSECTON) </th>
<th>IN AMOUNT (BALANCE) </th>

<th> DIFFREANCE (IN AMOUNT) </th>


<th>OUT AMOUNT (TRANSECTON) </th>
<th>OUT AMOUNT (BALANCE) </th>

<th> DIFFREANCE (OUT AMOUNT) </th>

</tr>';




foreach($fetchAll as $fetch ){

$query1 = $conn_me->prepare("SELECT sum(in_amount) as INAMOUNT , sum(out_amount) as outamount FROM `balance_transection` where date = '".$fetch['transection_date']."' and brunch_id = '".$brunch_id."' and transection_by = 'Cash' group by date "  );
$query1->execute();
$fetch1 = $query1->fetch(PDO::FETCH_ASSOC);


$balance_in_amount1 = !empty($fetch1['INAMOUNT']) ? $fetch1['INAMOUNT'] : 0 ; 
$balance_out_amount1 = !empty($fetch1['outamount']) ? $fetch1['outamount'] : 0 ; 


$query2 = $conn_me->prepare("SELECT sum(in_amount) as INAMOUNT , sum(out_amount) as outamount FROM `account_transection` where transection_date = '".$fetch['transection_date']."' and brunch_id = '".$brunch_id."' and transection_by = 'Cash' group by transection_date   "  );
$query2->execute();
$fetch2 = $query2->fetch(PDO::FETCH_ASSOC);


$tr_in_amount1 = !empty($fetch2['INAMOUNT']) ? $fetch2['INAMOUNT'] : 0 ; 
$tr_out_amount1 = !empty($fetch2['outamount']) ? $fetch2['outamount'] : 0 ; 

$colorA = $balance_in_amount1 == $tr_in_amount1 ? 'green' : 'red' ;
$colorB = $balance_out_amount1 == $tr_out_amount1 ? 'green' : 'red' ;


    $diffA = number_format((float)( $tr_in_amount1  - $balance_in_amount1  ), 2, '.', '');
    $diffB = number_format((float)( $tr_out_amount1  - $balance_out_amount1  ), 2, '.', '');




if($diffA > 0 ){

 $balance_in_amount =  number_format((float)( $balance_in_amount1 +  abs($diffA) ), 2, '.', '')   ; 

$query = $conn_me->prepare("UPDATE `balance_transection` SET `in_amount` = '".$balance_in_amount."'  WHERE `date` = '".$fetch['transection_date']."' and `brunch_id` = '".$brunch_id."'  AND  `transection_by` = 'Cash' ");
$query->execute();


}

if($diffA < 0 ){

 $balance_in_amount =  number_format((float)( $balance_in_amount1 -  abs($diffA) ), 2, '.', '')   ; 


 $query = $conn_me->prepare("UPDATE `balance_transection` SET `in_amount` = '".$balance_in_amount."'  WHERE `date` = '".$fetch['transection_date']."' and `brunch_id` = '".$brunch_id."'  AND  `transection_by` = 'Cash' ");
$query->execute();



}


if($diffB > 0 ){

  $balance_out_amount =  number_format((float)( $balance_out_amount1 +  abs($diffB) ), 2, '.', '')   ; 


$query = $conn_me->prepare("UPDATE `balance_transection` SET `out_amount` = '".$balance_out_amount."'  WHERE `date` = '".$fetch['transection_date']."' and `brunch_id` = '".$brunch_id."'  AND  `transection_by` = 'Cash' ");
$query->execute();


}

if($diffB < 0 ){

  $balance_out_amount =  number_format((float)( $balance_out_amount1 -  abs($diffB) ), 2, '.', '')   ; 

 $query = $conn_me->prepare("UPDATE `balance_transection` SET `out_amount` = '".$balance_out_amount."'  WHERE `date` = '".$fetch['transection_date']."' and `brunch_id` = '".$brunch_id."'  AND  `transection_by` = 'Cash' ");
$query->execute();



}




$content .= '<tr>

<th>'.$sl1++.'</th>
<th>'.$fetch['transection_date'].'</th>
<th style="color:'.$colorA.'">'.$tr_in_amount1.'</th>
<th style="color:'.$colorA.'">'.$balance_in_amount1.'</th>

<th style="color:'.$colorA.'" >'.$diffA.'</th>

<th style="color:'.$colorB.'">'.$tr_out_amount1.'</th>
<th style="color:'.$colorB.'">'.$balance_out_amount1.'</th>

<th style="color:'.$colorB.'" >'.$diffB.'</th>


</tr>';


}

print $content; 


/*
$sl = 1;
$qry = $conn_me->prepare("SELECT * FROM `account_transection`  ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $value) {


  if($value['transection_type'] == 'INCOME'){
    $field_name = 'in_amount';
    $amount = $value['in_amount'];
  }else{
    $field_name = 'out_amount';
    $amount = $value['out_amount'];
  }

  print $value['product_id']. '<br>' ;
  if($value['product_id'] <= 3347){
    QUICK_BALANCE::QUICK_OPENING_BALANCE($field_name,$value['transection_by'],$value['transection_by_id'],$amount,0,0,0,$value['transection_date'],$value['brunch_id']);

  }




}


/*



/*


$qry = $conn_me->prepare("SELECT * FROM `setup_product`    ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $value) {

$qry6 = $conn_me->prepare("SELECT * FROM `fg_opening_stock`  where `product_id` = '".$value['product_id']."' ");
$qry6->execute();
$fetch_list6 = $qry6->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list6 as $value6) {
QUICK_BALANCE::FG_QUICK_STOCK2($value6['product_id'],$value6['warehouse_id'],$value6['quantity'],0.00,0.00,0.00,'stock_in',$value6['invoice_date']);

}

  $qry6 = $conn_me->prepare("SELECT * FROM `fg_damage_store`  where  `product_id` = '".$value['product_id']."' ");
$qry6->execute();
$fetch_list6 = $qry6->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list6 as $value7) {
  QUICK_BALANCE::FG_QUICK_STOCK($value7["product_id"],$value7["warehouse_id"],$value7["quantity"],0.00,0.00,0.00,'stock_out',$value7["invoice_date"]);

}

// history_local_fg_purches

$qry1 = $conn_me->prepare("SELECT * FROM `history_local_fg_purches`  where `product_id` = '".$value['product_id']."' ");
$qry1->execute();
$fetch_list1 = $qry1->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list1 as $value1) {

QUICK_BALANCE::FG_QUICK_STOCK2($value1["product_id"],$value1["warehouse_id"],$value1["reject_quantity"],0.00,0.00,0.00,'stock_out',$value1["invoice_date"]);
QUICK_BALANCE::FG_QUICK_STOCK2($value1["product_id"],$value1["warehouse_id"],$value1["receive_quantity"],0.00,0.00,0.00,'stock_in',$value1["invoice_date"]);

}

// history_batch_wise_fg_receive

$qry2 = $conn_me->prepare("SELECT * FROM `history_batch_wise_fg_receive`  where `product_id` = '".$value['product_id']."' ");
$qry2->execute();
$fetch_list2 = $qry2->fetchAll(PDO::FETCH_ASSOC);
  foreach ($fetch_list2 as $value2) {

QUICK_BALANCE::FG_QUICK_STOCK2($value2["product_id"],$value2["warehouse_id"],$value2["receive_now"],0.00,0.00,0.00,'stock_in',$value2["invoice_date"]);

}

$qry3 = $conn_me->prepare("SELECT * FROM `sales_invoice_item`  where `product_id` = '".$value['product_id']."'   ");
$qry3->execute();
$fetch_list3 = $qry3->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list3 as $value3) {
QUICK_BALANCE::FG_QUICK_STOCK2($value3['product_id'],$value3['warehouse_id'],$value3['pcs_receive'],0.00,0.00,0.00,'stock_out',$value3["sales_manager_confirm_date"]);


}


$qry4 = $conn_me->prepare("SELECT * FROM `damage_invoice_item`  where `product_id` = '".$value['product_id']."'   ");
$qry4->execute();
$fetch_list4 = $qry4->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list4 as $value4) {
QUICK_BALANCE::FG_QUICK_STOCK2($value4['product_id'],$value4['warehouse_id'],$value4['damage_quantity'],0.00,0.00,0.00,'stock_in',$value4["warehouse_receive_date"]);

}


$qry5 = $conn_me->prepare("SELECT * FROM `sales_return_invoice_item`  where `product_id` = '".$value['product_id']."'   ");
$qry5->execute();
$fetch_list5 = $qry5->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list5 as $value5) {

QUICK_BALANCE::FG_QUICK_STOCK2($value5['product_id'],$value5['warehouse_id'],$value5['return_quantity'],0.00,0.00,0.00,'stock_in',$value5["warehouse_receive_date"]);

}




$qry7 = $conn_me->prepare("SELECT * FROM `fg_warehouse_to_warehouse_transfer`  where `product_id` = '".$value['product_id']."'  ");
$qry7->execute();
$fetch_list7 = $qry7->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list7 as $value7) {

QUICK_BALANCE::FG_QUICK_STOCK2($value7["product_id"],$value7["FROM_warehouse_id"],$value7["quantity"],0.00,0.00,0.00,'stock_out',$value7["invoice_date"]);
QUICK_BALANCE::FG_QUICK_STOCK2($value7["product_id"],$value7["TO_warehouse_id"],$value7["quantity"],0.00,0.00,0.00,'stock_in',$value7["invoice_date"]);

}




  }

?>