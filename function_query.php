<?php 
include('auth.php');
include('connect_me.php');

$conn_me = Database::getInstance();


CLASS QUICK_BALANCE {


/* Retrieve the old transaction: Retrieve the values of the old transaction, including the customer_id, in_amount, out_amount, and date fields.
Calculate the difference: Calculate the difference between the new in_amount and out_amount values and the old values.
Update the balance_customer table: Update the balance_customer table by adding the difference to the customer's balance.

UPDATE balance_customer SET 
balance = balance + (new_in_amount - old_in_amount) + (old_out_amount - new_out_amount) 
WHERE customer_id = <customer_id>;


stockIn Stockot 
10  20

*/


static function TODAY_BALANCE($transection_by,$transection_by_id){

    $conn_me = Database::getInstance();


        $qry = $conn_me->prepare("SELECT sum(in_amount) as `INCOME` , sum(out_amount) AS `EXPENSE` FROM `balance_transection` where  `transection_by_id` = '".$transection_by_id."'  AND  `transection_by` = '".$transection_by."' group by `transection_by_id` ");
        $qry->execute();
        $fe_ck1 = $qry->fetch(PDO::FETCH_ASSOC);


        if(!empty($fe_ck1['INCOME']) ){
            $INCOME = $fe_ck1['INCOME'];
        }else{
            $INCOME = 0.00;
        }

        if(!empty($fe_ck1['EXPENSE']) ){
            $EXPENSE = $fe_ck1['EXPENSE'];
        }else{
            $EXPENSE = 0.00;
        }


                $BALANCE =  $INCOME-$EXPENSE;


        
                return array(
                    'BALANCE' => $BALANCE

                    );


}


static function DELETEATEMP($id) {
    
$conn_me = Database::getInstance();

// Get invoice info
$query = $conn_me->prepare("SELECT * FROM `sales_invoice` WHERE `id` = ?");
$query->execute([$id]);
$fetch_list = $query->fetch(PDO::FETCH_ASSOC);

// Build log info
$invoice_no = $fetch_list['invoice_no'] ?? 'Unknown';
$details = "Tried to delete invoice no " . $invoice_no;
$date = date("Y-m-d");
$full_datetime = date("Y-m-d H:i:s");

// Get user info
$ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
$device_info = php_uname(); // Server-side device info

// Insert log
$stmt = $conn_me->prepare("INSERT INTO `delete_atemp` 
    (`date`, `details`, `user_agent`, `ip_address`, `device_info`, `full_datetime`) 
    VALUES (?, ?, ?, ?, ?, ?)");
    
$stmt->execute([
    $date,
    $details,
    $user_agent,
    $ip_address,
    $device_info,
    $full_datetime
]);

    
    
}

static function QUICK_OPENING_BALANCE($field_name,$transection_by,$transection_by_id,$in_amout,$date,$brunch_id) {
    $conn_me = Database::getInstance();

    $date = date("Y-m-d", strtotime($date));


        $qry = $conn_me->prepare("SELECT * FROM `balance_transection` where `brunch_id` = '".$brunch_id."' AND `transection_by` = '".$transection_by."' AND  `transection_by_id` = '".$transection_by_id."'  AND `date` = '".$date."' ");
        $qry->execute();
        $fetch = $qry->fetch(PDO::FETCH_ASSOC);
  

 
  
    if( $qry->rowCount() > 0 ){
    $query = $conn_me->prepare("UPDATE balance_transection SET 
    `{$field_name}` = `{$field_name}` + ('".$in_amout."') 
    WHERE `id` = '".$fetch['id']."' ");
    $query->execute();
}else{
    if($field_name == 'in_amount' ){ 
        $IN = $in_amout; 
        $OUT = '0.00';
    } else if( $field_name == 'out_amount' ){ 
        $IN =  '0.00';
        $OUT = $in_amout; 
     }else{  
        $IN = "0.00"; 
        $OUT = "0.00"; 
    }

    $query = $conn_me->exec("INSERT INTO `balance_transection` 
    ( 
        `id`, `transection_by_id`, `transection_by`, `in_amount`, `out_amount`, `brunch_id`, `date`    
    ) 
    VALUES
    (
        '0',
        '".$transection_by_id."',
        '".$transection_by."',
        '".$IN."',
        '".$OUT."',
        '".$brunch_id."',
        '".$date."'
    ) ");

}


    }




    static function CUSTOMER_QUICK_DUE($customer_id,$amout,$field_name,$date,$brunch_id) {
    
        $conn_me = Database::getInstance();

        $date = date("Y-m-d", strtotime($date));

        $qry = $conn_me->prepare("SELECT * FROM `balance_customer` where `customer_id` = '".$customer_id."' AND `date` = '".$date."' AND `brunch_id` = '".$brunch_id."'  AND `note` IS NULL ");
        $qry->execute();
        $fetch = $qry->fetch(PDO::FETCH_ASSOC);
    
    if( $qry->rowCount() > 0 ){

        $query = $conn_me->prepare("UPDATE balance_customer SET 
        `{$field_name}` = `{$field_name}` + ('".$amout."')
        WHERE id = '".$fetch['id']."'");
        $query->execute();
  
    }else{

        if($field_name == 'invoice_amount' ){ 
            $invoice_amount = "$amout"; 
            $receive_amount = '0.00';
            $return_amount = '0.00';
        } else if( $field_name == 'receive_amount' ){ 
            $invoice_amount = "0.00"; 
            $receive_amount = "$amout"; 
            $return_amount = '0.00';
        } else if( $field_name == 'return_amount' ){ 
            $invoice_amount = "0.00"; 
            $receive_amount = "0.00"; 
            $return_amount = "$amout"; 
         }else{  
            $invoice_amount = "0.00"; 
            $receive_amount = "0.00"; 
            $return_amount = "0.00"; 
        }
    

        $query2 = $conn_me->exec("INSERT INTO `balance_customer` 
        ( 
            `id`, `customer_id`, `invoice_amount`, `receive_amount`, `return_amount`, `date`,`brunch_id`
        ) 
        VALUES
        (
            '0',
            '".$customer_id."',
            '".$invoice_amount."',
            '".$receive_amount."',
            '".$return_amount."',
            '".$date."',
            '".$brunch_id."'
        
        ) ");

    }


    }


    static function CUSTOMER_QUICK_DUE_TWO($customer_id,$in_amout,$field_name,$date,$brunch_id) {
    
        $conn_me = Database::getInstance();

        $date = date("Y-m-d", strtotime($date));

        $qry = $conn_me->prepare("SELECT * FROM `balance_customer_two` where `customer_id` = '".$customer_id."' AND `date` = '".$date."' AND `brunch_id` = '".$brunch_id."' AND `note` IS NULL ");
        $qry->execute();
        $fetch = $qry->fetch(PDO::FETCH_ASSOC);
    
    if( $qry->rowCount() > 0 ){

        $query = $conn_me->prepare("UPDATE balance_customer_two SET 
        `{$field_name}` = `{$field_name}` + ('".$in_amout."')
        WHERE id = '".$fetch['id']."'");
        $query->execute();
  
    
    }else{

        if($field_name == 'invoice_amount' ){ 
            $invoice_amount = "$in_amout"; 
            $receive_amount = '0.00';
            $return_amount = '0.00';
        } else if( $field_name == 'receive_amount' ){ 
            $invoice_amount = "0.00"; 
            $receive_amount = "$in_amout"; 
            $return_amount = '0.00';
        } else if( $field_name == 'return_amount' ){ 
            $invoice_amount = "0.00"; 
            $receive_amount = "0.00"; 
            $return_amount = "$in_amout"; 
         }else{  
            $invoice_amount = "0.00"; 
            $receive_amount = "0.00"; 
            $return_amount = "0.00"; 
        }
    

        $query2 = $conn_me->exec("INSERT INTO `balance_customer_two` 
        ( 
            `id`, `customer_id`, `invoice_amount`, `receive_amount`, `return_amount`, `date`,`brunch_id`
        ) 
        VALUES
        (
            '0',
            '".$customer_id."',
            '".$invoice_amount."',
            '".$receive_amount."',
            '".$return_amount."',
            '".$date."',
            '".$brunch_id."'
        
        ) ");

    }


    }



    static function SUPPLIER_QUICK_DUE($supplier_id,$in_amout,$field_name,$date) {
    
        $conn_me = Database::getInstance();

        $date = date("Y-m-d", strtotime($date));

        $qry = $conn_me->prepare("SELECT * FROM `balance_supplier` where `supplier_id` = '".$supplier_id."' AND `date` = '".$date."' ");
        $qry->execute();
        $fetch = $qry->fetch(PDO::FETCH_ASSOC);
    
    if( $qry->rowCount() > 0 ){

        $query = $conn_me->prepare("UPDATE balance_supplier SET 
        `{$field_name}` = `{$field_name}` + ('".$in_amout."' )
        WHERE id = '".$fetch['id']."'");
        $query->execute();
  
    
    }else{

        if($field_name == 'invoice_amount' ){ 
            $invoice_amount = "$in_amout"; 
            $receive_amount = '0.00';
            $payment_amount = '0.00';
        } else if( $field_name == 'receive_amount' ){ 
            $invoice_amount = "0.00"; 
            $receive_amount = "$in_amout"; 
            $payment_amount = '0.00';
        } else if( $field_name == 'payment_amount' ){ 
            $invoice_amount = "0.00"; 
            $receive_amount = "0.00"; 
            $payment_amount = "$in_amout"; 
         }else{  
            $invoice_amount = "0.00"; 
            $receive_amount = "0.00"; 
            $payment_amount = "0.00"; 
        }
    

        $query2 = $conn_me->exec("INSERT INTO `balance_supplier` 
        ( 
            `id`, `supplier_id`, `invoice_amount`, `receive_amount`, `payment_amount`, `date`
        ) 
        VALUES
        (
            '0',
            '".$supplier_id."',
              '".$invoice_amount."',
              '".$receive_amount."',
              '".$payment_amount."',
              '".$date."'
        
        ) ");

    }


    }



    static function FG_QUICK_STOCK($product_id,$warehouse_id,$in_amout,$field_name,$date) {
        $conn_me = Database::getInstance();

        $date = date("Y-m-d", strtotime($date));

    $qry = $conn_me->prepare("SELECT * FROM `balance_product` where `product_id` = '".$product_id."' AND  `warehouse_id` = '".$warehouse_id."' AND `date` = '".$date."' AND note IS NULL  ");
    $qry->execute();
    $fetch = $qry->fetch(PDO::FETCH_ASSOC);

if( $qry->rowCount() > 0 ){
    $query = $conn_me->prepare("UPDATE balance_product SET 
    `{$field_name}` = `{$field_name}` + ('".$in_amout."' ) 
    WHERE `id` = '".$fetch['id']."' ");
    $query->execute();
}else{
    if($field_name == 'stock_in' ){ 
        $stock_in = "$in_amout"; 
        $stock_out = '0.00';
    } else if( $field_name == 'stock_out' ){ 
        $stock_in =  '0.00';
        $stock_out = "$in_amout"; 
     }else{  
        $stock_in = "0.00"; 
        $stock_out = "0.00"; 
    }

    $query = $conn_me->exec("INSERT INTO `balance_product` 
    ( 
        `id`, `product_id`, `warehouse_id`, `stock_in`, `stock_out`,`date`
    
    ) 
    VALUES
    (
        '0',
        '".$product_id."',
        '".$warehouse_id."',
        '".$stock_in."',
        '".$stock_out."',
        '".$date."'
    ) ");

}


    }


  



    static function FG_QUICK_STOCK2($product_id,$warehouse_id,$in_amout,$field_name,$date) {
        $conn_me = Database::getInstance();

        $date = date("Y-m-d", strtotime($date));

    $qry = $conn_me->prepare("SELECT * FROM `balance_product_two` where `product_id` = '".$product_id."' AND  `warehouse_id` = '".$warehouse_id."' AND `date` = '".$date."' AND note IS NULL  ");
    $qry->execute();
    $fetch = $qry->fetch(PDO::FETCH_ASSOC);

if( $qry->rowCount() > 0 ){
    $query = $conn_me->prepare("UPDATE balance_product_two SET 
    `{$field_name}` = `{$field_name}` + ('".$in_amout."' ) 
    WHERE `id` = '".$fetch['id']."' ");
    $query->execute();
}else{
    if($field_name == 'stock_in' ){ 
        $stock_in = "$in_amout"; 
        $stock_out = '0.00';
    } else if( $field_name == 'stock_out' ){ 
        $stock_in =  '0.00';
        $stock_out = "$in_amout"; 
     }else{  
        $stock_in = "0.00"; 
        $stock_out = "0.00"; 
    }

    $query = $conn_me->exec("INSERT INTO `balance_product_two` 
    ( 
        `id`, `product_id`, `warehouse_id`, `stock_in`, `stock_out`,`date`
    
    ) 
    VALUES
    (
        '0',
        '".$product_id."',
        '".$warehouse_id."',
        '".$stock_in."',
        '".$stock_out."',
        '".$date."'
    ) ");

}


    }


}





CLASS CRUD {

   

    static function copyDataFromSetupAtoSetupB($id,$transaction_type,$data_inserted_from)
    {
        $conn_me = Database::getInstance();
        include('clean.php');

    
       
        if($id == 'All' ){ $QUERY = " "; }else{ $QUERY = " A.`id` = '".$id."' AND "; }
    
    
        $successCount = 0; 
    
        $query1 = $conn_me->prepare("SELECT A.* FROM `account_posting_pending` A where   $QUERY A.`poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."'  AND A.`data_inserted_from` = '".$data_inserted_from."'  AND ( A.`posting_status` = 'Pending' OR A.`data_confirmed` = 'NO') "); 
        $query1->execute();
        $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
        foreach($fetch_list1 AS $fetch){ 
    
            $TRANSECTIONE = SETUP::SETUP_CODE_INSERT_DATA('account_transection');
            $id = $TRANSECTIONE['last_id'];
    

                if($fetch['transection_type'] == 'INCOME' ){
                    $amount= $fetch['in_amount'];
                    $field = 'receive_amount';
                    $main_field = 'in_amount';
                }else{
                     $amount= $fetch['out_amount'];
                     $field = 'return_amount';
                     $main_field = 'out_amount';
                }


           
            
    
            $query = $conn_me->prepare("UPDATE `account_transection` 
    
            SET

            `transection_type` = '".$fetch['transection_type']."',
            `ledger_id` = '".$fetch['ledger_id']."',
            `transection_head_id` = '".$fetch['transection_head_id']."',
            `transection_to` = '".$fetch['transection_to']."',
            `transection_to_id` = '".$fetch['transection_to_id']."',
            `transection_by` = '".$fetch['transection_by']."',
            `transection_by_id` = '".$fetch['transection_by_id']."',
            `check_number` = '".$fetch['check_number']."',
            `collect_by` = '".$fetch['collect_by']."',
            `check_date` = '".$fetch['check_date']."',
            `note` = '".clean($fetch['note'])."',
            `brunch_id` = '".$fetch['brunch_id']."',
            `status` = 'Done',
            `transection_date` =  '" . $fetch['transection_date'] . "',
            `in_amount` =   '".$fetch['in_amount']."',
            `out_amount` =  '".$fetch['out_amount']."',
            `data_inserted_from` =  '".$fetch['data_inserted_from']."',
            `date` = '" . date("Y-m-d") . "',
            `time` =  '" . date("h:i:s a") . "',
            `poster` =   '" . $fetch['poster'] . "',
            `lastupdate` =  '" . $fetch['lastupdate'] . "'
    
    
            WHERE `id` = '".$id."'");
    
            $query->execute();
            $successCount++;
    

            if($fetch['transection_to'] == 'Customer' ){

            QUICK_BALANCE::CUSTOMER_QUICK_DUE($fetch['transection_to_id'],$amount,$field,$fetch['transection_date'],$fetch['brunch_id']);

            } else if ($fetch['transection_to'] == 'Supplier' ){
            QUICK_BALANCE::SUPPLIER_QUICK_DUE($fetch['transection_to_id'],$amount,'payment_amount',$fetch['transection_date']);
            }else{

            }


           QUICK_BALANCE::QUICK_OPENING_BALANCE($main_field,$fetch['transection_by'],$fetch['transection_by_id'],$amount,$fetch['transection_date'],$fetch['brunch_id']);


              if($fetch['ledger_id'] == '33' ){ // UNIDENTIFIED CUSTOMER PAYMENT
                $data_confirmed = 'NO';
            }else{
                $data_confirmed = 'YES';
            }


            $query2 = $conn_me->prepare("UPDATE `account_posting_pending`  SET  `transection_id` =  '".$id."', `posting_status` = 'Done' ,  `data_confirmed` =  '".$data_confirmed."' WHERE `id` = '".$fetch['id']."'");
            $query2->execute();
    
    
        }
    
        

        return $successCount . " Data Receive Transferred "  ;
    
    
    }
    

    

    static function insert_data($table, $data) {

        $conn_me = Database::getInstance();

        // Build the column list and placeholders for the values
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
      
        // Prepare the insert statement
        $stmt = $conn_me->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");
      
        // Bind the values to the placeholders
        foreach ($data as $key => $value) {
          $stmt->bindValue(":$key", $value);
        }
      
        // Execute the insert statement
        if ($stmt->execute()) {
            $mess =  "Record created successfully.";
          } else {
            $mess = "Error: " . $stmt->errorInfo();
          }

          return array(
            'mess' => $mess

            );


      }
      

      static function updateData($tableName, $id, $columnValues) {

        $conn_me = Database::getInstance();

        // Prepare the UPDATE statement
        $columns = [];
        $placeholders = [];
        foreach ($columnValues as $column => $value) {
            $columns[] = $column . ' = :' . $column;
            $placeholders[':' . $column] = $value;
        }
        $stmt = $conn_me->prepare('UPDATE ' . $tableName . ' SET ' . implode(', ', $columns) . ' WHERE id = :id');
    
        // Bind the values to the placeholders
        $placeholders[':id'] = $id;

        if ($stmt->execute($placeholders)) {
            // Print a success message if the update was successful
            $mess =  'Update successful';
        } else {
            // Print an error message if the update failed
            $mess=  'Update failed';
        }

        return array(
            'mess' => $mess

            );
    }


    static function updateTimelineData($id, $columnValues) {

        $conn_me = Database::getInstance();

        // Prepare the UPDATE statement
        $columns = [];
        $placeholders = [];
        foreach ($columnValues as $column => $value) {
            $columns[] = $column . ' = :' . $column;
            $placeholders[':' . $column] = $value;
        }
        $stmt = $conn_me->prepare('UPDATE invoice_timeline SET ' . implode(', ', $columns) . ' WHERE invoice_id = :id');
    
        // Bind the values to the placeholders
        $placeholders[':id'] = $id;

        if ($stmt->execute($placeholders)) {
            // Print a success message if the update was successful
            $mess =  'Update successful';
        } else {
            // Print an error message if the update failed
            $mess=  'Update failed';
        }

        return array(
            'mess' => $mess

            );
    }

}


CLASS PERMISSION{



    static function logoutcheck(){
        $conn_me = Database::getInstance();

            $ck_user = $conn_me->prepare("SELECT *  FROM `webpush_member` WHERE `member_id` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."'  ");
            $ck_user->execute();
            if($ck_user->rowCount() > 0) {


            }else{

                header("location: login/stmfailed");
                exit();
                
            }


}

static function pageCreatePermission($USERTYPE,$FIELD_VALUE,$FIELD_ID,$FUNCTION) {
        if($USERTYPE =='Admin'){
    
            $save_update_buton = '<input type="button" '.$FUNCTION.'  name="'.$FIELD_ID.'" id="'.$FIELD_ID.'" value="'.$FIELD_VALUE.'" class ="btn btn-primary pull-right" >';


        }else if($USERTYPE =='Editor'){
            $save_update_buton = '<input type="button"  '.$FUNCTION.'  name="'.$FIELD_ID.'" id="'.$FIELD_ID.'" value="'.$FIELD_VALUE.'" class ="btn btn-primary pull-right" > ';

        }else if($USERTYPE =='Viewer'){
            $save_update_buton = '';
    
        } else { 
            $save_update_buton = '';
    }
    

    return array(
        'save_update_buton' => $save_update_buton


        );


    }



}


CLASS DYNAMIC_MESS {



    static  function INSERT_MESS($mess_for,$head_line,$message,$link){
        $conn_me = Database::getInstance();

        $prepaire_table = SETUP::SETUP_CODE_INSERT_DATA('mess_box');


        $query = $conn_me->prepare("SELECT *  FROM `setup_designation`  where `designation` = '".$mess_for."' ");              
        $query->execute();
        $fetch_list = $query->fetch(PDO::FETCH_ASSOC);


        $query1 = $conn_me->prepare("UPDATE `mess_box` 
        
        SET
        `designation_id` = '".$fetch_list['id']."',
        `brunch_id` = '" . $_SESSION['USER_BRUNCH'] . "',
        `message` =   '".$message."',
        `head_line` = '".$head_line."',
        `link` = '".$link."',
        `mess_posted_by` ='" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
        `status` = 'Done',
        `mess_posted_time` =  '" . date("h:i:s a") . "',
        `mess_posted_date` =   '" . date("Y-m-d") . "'
    
        WHERE  `id` = '".$prepaire_table['last_id']."'  ");
        
        $query1->execute();

return array(
    'mess_id' => $prepaire_table['last_id']


    );



}


}

CLASS ACCOUNT {

    static   function MAKE_TRANSECTION($transection_type,$transection_to,$transection_to_id,$ledger_id,$transection_head_id,$note,$AMOUNT,$TRANSECTION_ID,$DATA_INSERETED_FROM){
        $conn_me = Database::getInstance();

        if($transection_type == 'INCOME'){

            $in_amount = $AMOUNT;
            $out_amount = 0.00;

      
         

        }else if($transection_type == 'EXPENSE') {

            $in_amount = 0.00;
            $out_amount = $AMOUNT;

        

        }else {
            $in_amount = 0.00;
            $out_amount = 0.00;
        }






        $query = $conn_me->prepare("UPDATE `account_transection` 

        SET
        `transection_type` = '".$transection_type."',
        `transection_to` = '".$transection_to."',
        `transection_head_id` = '".$transection_head_id."',
        `ledger_id` = '".$ledger_id."',
        `transection_to_id` = '".$transection_to_id."',
        `note` = '".$note."',
        `transection_date` =  '" . date("Y-m-d") . "',
        `in_amount` =   '".$in_amount."',
        `status` = 'Done',
        `out_amount` =  '".$out_amount."',
        `date` = '" . date("Y-m-d") . "',
        `time` =  '" . date("h:i:s a") . "',
        `brunch_id` =   '" . $_SESSION['USER_BRUNCH'] . "',
        `poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
        `data_inserted_from` = '".$DATA_INSERETED_FROM."',
        `lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


        WHERE `id` = '".$TRANSECTION_ID."'");

        $query->execute();





}
}
CLASS STOCK {


    static  function ALL_ITEM_STCOK_BY_DATE($date,$type){
        
        
        


        $conn_me = Database::getInstance();
        
        
        $date = date("Y-m-d", strtotime($date));
        
        if($type == 'TODAY'){
        $query = "  bp.date <= '".$date."' ";
        }else{ 
            $query = "  bp.date < '".$date."' ";
        }


        $ck1 = $conn_me->prepare("SELECT
        SUM(stock_in_nowabpur) AS total_stock_in_nowabpur,
        SUM(stock_in_headoffice) AS total_stock_in_headoffice,
        SUM(stock_in_nowabpur * price) AS total_value_in_nowabpur,
        SUM(stock_in_headoffice * price) AS total_value_in_headoffice
    FROM (
        SELECT
            bp.product_id,
            sp.product_name,
            COALESCE((
                SELECT price 
                FROM history_change_product_price_vat hcppv 
                WHERE hcppv.product_id = bp.product_id AND hcppv.date <= '".$date."' 
                ORDER BY hcppv.date DESC 
                LIMIT 1
            ), 0) AS price,
            SUM(CASE WHEN bp.warehouse_id IN (14, 15) THEN (bp.stock_in - bp.stock_out) ELSE 0 END) AS stock_in_nowabpur,
            SUM(CASE WHEN bp.warehouse_id NOT IN (14, 15) THEN (bp.stock_in - bp.stock_out) ELSE 0 END) AS stock_in_headoffice
        FROM
            balance_product bp
        JOIN
            setup_product sp ON bp.product_id = sp.id
        WHERE
            $query 
        GROUP BY
            bp.product_id, sp.product_name
    ) AS subquery;
    
    
        ");
        $ck1->execute();
        $fe_ck1 = $ck1->fetch(PDO::FETCH_ASSOC);
        


       return $fe_ck1 ; 
        
    }
    
    
 

    static function PRODUCT_IN_HEADOFFICE_TO_NOWABPUR($date){


        $conn_me = Database::getInstance();
        
        
        $date = date("Y-m-d", strtotime($date));
        


        $ck1 = $conn_me->prepare("SELECT
        SUM(total_product_value) AS total_sum_product_value
    FROM (
        SELECT
            A.product_id,
            SUM((A.quantity) * sp.sales_rate) AS total_product_value
        FROM
            fg_warehouse_to_warehouse_transfer A
        JOIN
            setup_product sp ON A.product_id = sp.id
        WHERE
            A.invoice_date = '".$date."'
            AND A.FROM_warehouse_id NOT IN ('14', '15')
            AND A.TO_warehouse_id IN ('14', '15')
        GROUP BY
            A.product_id
    ) AS subquery
        ");
        $ck1->execute();
        $fe_ck1 = $ck1->fetch(PDO::FETCH_ASSOC);
        
        if(!empty($fe_ck1['total_sum_product_value']) ){
            $total_sum_product_value = $fe_ck1['total_sum_product_value'];
        }else{
            $total_sum_product_value = 0.00;
        }
        
        
        return $total_sum_product_value;
        
    }
    
    
 
    static function TOTAL_MEMO($date,$brunch_id){

        $conn_me = Database::getInstance();

        $date = date("Y-m-d", strtotime($date));


    $qry = $conn_me->prepare("SELECT count(id) as INVOICENO FROM `sales_invoice` where invoice_date = '".$date."' and brunch_id = '".$brunch_id."'    ");
    $qry->execute();
    $fetch_list = $qry->fetch(PDO::FETCH_ASSOC);
   
                return $fetch_list['INVOICENO'];

    }




    
    static function TOTAL_MEMO_VALUE($date,$brunch_id){

        $conn_me = Database::getInstance();

        $date = date("Y-m-d", strtotime($date));
        $total_invoice_price = 0 ;
    $ck5 = $conn_me->prepare("SELECT `id`  FROM `sales_invoice`  where  `invoice_date` = '".$date."'   and brunch_id = '".$brunch_id."' ");
    $ck5->execute();
    
    $fe_ck5 = $ck5->fetchAll(PDO::FETCH_ASSOC);
    foreach($fe_ck5 AS $fetch5){
    
        $invoice_price = FIND::TOTAL_SALES_INVOICE_PRICE($fetch5['id']);

            $total_invoice_price += $invoice_price['price'];
              
    }


    return $total_invoice_price;
}


static function TOTAL_MEMO_HALF_HEADOFFICE_HALF_NOWABUR($date,$brunch_id){

    $conn_me = Database::getInstance();
    $invoic_count = 0 ; 

    $date = date("Y-m-d", strtotime($date));
    $total_invoice_price = 0 ;
$ck5 = $conn_me->prepare("SELECT sales_invoice_id, GROUP_CONCAT(warehouse_id) as warehouses
FROM sales_invoice_item
WHERE `sales_manager_confirm_date` = '".$date."'   and brunch_id = '".$brunch_id."'
GROUP BY sales_invoice_id
HAVING (FIND_IN_SET('15', warehouses) > 0 OR FIND_IN_SET('14', warehouses) > 0)
AND (
    FIND_IN_SET('1', warehouses) > 0 OR
    FIND_IN_SET('2', warehouses) > 0 OR
    FIND_IN_SET('3', warehouses) > 0 OR
    FIND_IN_SET('4', warehouses) > 0 OR
    FIND_IN_SET('5', warehouses) > 0 OR
    FIND_IN_SET('6', warehouses) > 0 OR
    FIND_IN_SET('7', warehouses) > 0 OR
    FIND_IN_SET('8', warehouses) > 0 OR
    FIND_IN_SET('9', warehouses) > 0 OR
    FIND_IN_SET('10', warehouses) > 0 OR
    FIND_IN_SET('11', warehouses) > 0 OR
    FIND_IN_SET('12', warehouses) > 0 OR
    FIND_IN_SET('13', warehouses) > 0 OR
    FIND_IN_SET('16', warehouses) > 0
)  ");
$ck5->execute();

$fe_ck5 = $ck5->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck5 AS $fetch5){

    $invoice_price = FIND::TOTAL_SALES_INVOICE_PRICE($fetch5['sales_invoice_id']);

        $total_invoice_price += $invoice_price['price'];
        $invoic_count += 1;  
}

return array(
    'total_invoice_price' => $total_invoice_price,
    'invoic_count' => $invoic_count

    );



}



static function TOTAL_MEMO_HEADOFFICE_AND_NOWABUR_SEPERATLY($date,$brunch_id){

    $conn_me = Database::getInstance();

    $date = date("Y-m-d", strtotime($date));

    $grand_total = 0 ;

$ck5 = $conn_me->prepare("SELECT sales_invoice_id, GROUP_CONCAT(warehouse_id) as warehouses
FROM sales_invoice_item
WHERE `sales_manager_confirm_date` = '".$date."'   and brunch_id = '".$brunch_id."'
GROUP BY sales_invoice_id
HAVING (FIND_IN_SET('15', warehouses) > 0 OR FIND_IN_SET('14', warehouses) > 0)
AND (
    FIND_IN_SET('1', warehouses) > 0 OR
    FIND_IN_SET('2', warehouses) > 0 OR
    FIND_IN_SET('3', warehouses) > 0 OR
    FIND_IN_SET('4', warehouses) > 0 OR
    FIND_IN_SET('5', warehouses) > 0 OR
    FIND_IN_SET('6', warehouses) > 0 OR
    FIND_IN_SET('7', warehouses) > 0 OR
    FIND_IN_SET('8', warehouses) > 0 OR
    FIND_IN_SET('9', warehouses) > 0 OR
    FIND_IN_SET('10', warehouses) > 0 OR
    FIND_IN_SET('11', warehouses) > 0 OR
    FIND_IN_SET('12', warehouses) > 0 OR
    FIND_IN_SET('13', warehouses) > 0 OR
    FIND_IN_SET('16', warehouses) > 0
)  
");
$ck5->execute();

$fe_ck5 = $ck5->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck5 AS $fetch5){

        $total_invoice_price = 0 ;
        $query2 = $conn_me->prepare("SELECT *  FROM  `sales_invoice_item`  WHERE `sales_invoice_id` = '".$fetch5['sales_invoice_id']."' AND warehouse_id IN ('14','15') ");
        $query2->execute();
        $fetch_list = $query2->fetchAll(PDO::FETCH_ASSOC);

        foreach ($fetch_list as $fetch2) { 
        $total_invoice_price += number_format((float)( $fetch2['sales_rate']*$fetch2['sales_quantity']), 2, '.', '');
        }

        $grand_total += $total_invoice_price ; 

}

return $grand_total;


    
}

static function TOTAL_MEMO_HEADOFFICE_AND_HEADOFFICE_SEPERATLY($date,$brunch_id){

    $conn_me = Database::getInstance();

    $date = date("Y-m-d", strtotime($date));

    $grand_total = 0 ;

$ck5 = $conn_me->prepare("SELECT sales_invoice_id, GROUP_CONCAT(warehouse_id) as warehouses
FROM sales_invoice_item
WHERE `sales_manager_confirm_date` = '".$date."'   and brunch_id = '".$brunch_id."'
GROUP BY sales_invoice_id
HAVING (FIND_IN_SET('15', warehouses) > 0 OR FIND_IN_SET('14', warehouses) > 0)
AND (
    FIND_IN_SET('1', warehouses) > 0 OR
    FIND_IN_SET('2', warehouses) > 0 OR
    FIND_IN_SET('3', warehouses) > 0 OR
    FIND_IN_SET('4', warehouses) > 0 OR
    FIND_IN_SET('5', warehouses) > 0 OR
    FIND_IN_SET('6', warehouses) > 0 OR
    FIND_IN_SET('7', warehouses) > 0 OR
    FIND_IN_SET('8', warehouses) > 0 OR
    FIND_IN_SET('9', warehouses) > 0 OR
    FIND_IN_SET('10', warehouses) > 0 OR
    FIND_IN_SET('11', warehouses) > 0 OR
    FIND_IN_SET('12', warehouses) > 0 OR
    FIND_IN_SET('13', warehouses) > 0 OR
    FIND_IN_SET('16', warehouses) > 0
)  
");
$ck5->execute();

$fe_ck5 = $ck5->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck5 AS $fetch5){

        $total_invoice_price = 0 ;
        $query2 = $conn_me->prepare("SELECT *  FROM  `sales_invoice_item`  WHERE `sales_invoice_id` = '".$fetch5['sales_invoice_id']."' AND warehouse_id NOT IN ('14','15') ");
        $query2->execute();
        $fetch_list = $query2->fetchAll(PDO::FETCH_ASSOC);

        foreach ($fetch_list as $fetch2) { 
        $total_invoice_price += number_format((float)( $fetch2['sales_rate']*$fetch2['sales_quantity']), 2, '.', '');
        }

        $grand_total += $total_invoice_price ; 

}

return $grand_total;


    
}




static function BRUNCH_WISE_TOTAL_MEMO_VALUE($date,$brunch_id,$type){


    
    $conn_me = Database::getInstance();
    $invoic_count = 0 ; 
    $date = date("Y-m-d", strtotime($date));
    $total_invoice_price = 0 ;
$ck5 = $conn_me->prepare("SELECT `id`  FROM `sales_invoice`  where  `invoice_date` = '".$date."'   and brunch_id = '".$brunch_id."' ");
$ck5->execute();

$fe_ck5 = $ck5->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck5 AS $fetch5){
    
    $invoice_price = FIND::WAREHOUSE_WISE_TOTAL_SALES_INVOICE_PRICE($fetch5['id'],$type);

        if( $invoice_price['price'] > 0 ){
          $invoic_count += 1;  
        }else{
            $invoic_count += 0 ; 
        }
        $total_invoice_price += $invoice_price['price'];
          
}


return array(
    'total_invoice_price' => $total_invoice_price,
    'invoic_count' => $invoic_count

    );


}





static function FG_ITEM_WISE_STOCK($REALATEDID,$ID,$TYPE){

    $conn_me = Database::getInstance();

    if($TYPE == 'unique_brunch_wise'){


        $ITEM_STOCK = 0 ;
        $qry = $conn_me->prepare("SELECT * FROM `setup_brunch` where `id` = '" . $REALATEDID."'  ");
        $qry->execute();
        $fetch_list = $qry->fetch(PDO::FETCH_ASSOC);
        $a = $fetch_list['related_warehouse'];
        $b = str_replace("[","",$a);
        $c = str_replace("]","",$b);

    $ck1 = $conn_me->prepare("SELECT SUM(`stock_in`- `stock_out`) AS `stock`  FROM `balance_product`  WHERE `product_id` = '" . $ID . "' AND `warehouse_id` IN ($c) GROUP BY `warehouse_id` HAVING 
    `stock` >= 0; ");
    $ck1->execute();
    $fe_ck1 = $ck1->fetchAll(PDO::FETCH_ASSOC);
     foreach ($fe_ck1 as $fetch) { 


        $ITEM_STOCK += (!empty($fetch['stock'])  ) ?  $fetch['stock'] : 0 ;

     }

    }else{

        if($TYPE == 'warehouse_wise' ){
        
        $QUERYTAG = " WHERE `product_id` = '" . $ID . "' AND `warehouse_id` = '" . $REALATEDID."' GROUP BY `warehouse_id` ";
        
    }else if($TYPE == 'product_wise') {

        $QUERYTAG = " WHERE `product_id` = '".$ID."'   GROUP BY `product_id` ";
        

    }else if($TYPE == 'brunch_wise') {

    $qry = $conn_me->prepare("SELECT * FROM `setup_brunch` where `id` = '".$_SESSION['USER_BRUNCH']."'  ");
    $qry->execute();
    $fetch_list = $qry->fetch(PDO::FETCH_ASSOC);
    $a = $fetch_list['related_warehouse'];
    $b = str_replace("[","",$a);
    $c = str_replace("]","",$b);
    $QUERYTAG = " WHERE `product_id` = '" . $ID . "' AND `warehouse_id` IN ($c) GROUP BY `product_id` ";
    }else{
    $QUERYTAG = '';
    }



    $ck1 = $conn_me->prepare("SELECT SUM(`stock_in`- `stock_out`) AS `stock`  FROM `balance_product` $QUERYTAG ");
    $ck1->execute();
    $fe_ck1 = $ck1->fetch(PDO::FETCH_ASSOC);

        $ITEM_STOCK = (!empty($fe_ck1['stock'])  ) ?  $fe_ck1['stock'] : 0 ;

    }
    


    
  


    
            return array(
                'ITEM_STOCK' => $ITEM_STOCK

                );
    

            }





            static function HOW_MANY_DAYS_LEAVE_LEFT($EMPLOYEE_ID,$LEAVE_ID,$YEAR,$MONTH,$TYPE){


            $conn_me = Database::getInstance();


    if($TYPE == 'Type Wise Leave Monthly'){
            if($LEAVE_ID == '5' ){
            $can_take_leave = 1;
            }else if ($LEAVE_ID == '6'){
            $can_take_leave = 5;
            }else{
            $can_take_leave = 0;
            }

                $QUERYTTPE = " where `employee_id` = '".$EMPLOYEE_ID."' AND `leave_type_id` = '".$LEAVE_ID."' AND MONTH(leave_from_date)  = '".$MONTH."' OR MONTH(leave_to_date)  = '".$MONTH."'   ";
            }else if($TYPE == 'Type Wise Leave Yearly'){
                if($LEAVE_ID == '5' ){
                    $can_take_leave = 12;
                    }else if ($LEAVE_ID == '6'){
                    $can_take_leave = 10;
                    }else{
                    $can_take_leave = 0;
                    }
              $QUERYTTPE = " where `employee_id` = '".$EMPLOYEE_ID."' AND `leave_type_id` = '".$LEAVE_ID."' AND `year` = '".$YEAR."'   ";
            }else{
                $QUERYTTPE = "";
            }
        $interval = 0;
        $query = $conn_me->prepare("SELECT *   FROM `apply_leave` $QUERYTTPE ");
        $query->execute();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) {
        
            $date1 = date_create($fetch['leave_from_date']);
            $date2 = date_create($fetch['leave_to_date']);
            $date = date_diff($date1, $date2);
            $interval += $date->format('%R%a')+1;
        }
           
           $total_amount = $interval;



           $LEAVE_LEFT = $can_take_leave - $total_amount ;

            return array(
                'LEAVE_TAKEN' => $total_amount,
                'LEAVE_LEFT' => $LEAVE_LEFT
                );
        }




        static function RAW_ITEM_WISE_STOCK($REALATEDID,$ID,$TYPE){

        $product_info = SETUP::SETUP_RAW_MATERIAL($ID);


$opening_stock  = FIND::RAW_OPENING_STOCK_RECEIVE($REALATEDID,$ID,$TYPE);
$local_purches = FIND::RAW_LOCAL_PURCHES_RECEIVE_REJECT($REALATEDID,$ID,$TYPE);
$send_raw_item_for_molding = FIND::MOLDING_RAW_MATERIAL_DEMAND_DISPATCH($REALATEDID,$ID,$TYPE);
$receive_item_after_molding  = FIND::MOLDING_RAW_MATERIAL_BATCH_RECEIVE($REALATEDID,$ID,$TYPE);
$send_raw_item_for_print = FIND::PRINT_RAW_MATERIAL_DEMAND_DISPATCH($REALATEDID,$ID,$TYPE);
$send_print_item_for_print =FIND::PRINT_MATERIAL_DEMAND_DISPATCH($REALATEDID,$ID,$TYPE);
$receive_raw_item_after_print = FIND::PRINT_RAW_MATERIAL_BATCH_RECEIVE($REALATEDID,$ID,$TYPE);
$send_raw_item_for_spray = FIND::SPRAY_RAW_MATERIAL_DEMAND_DISPATCH($REALATEDID,$ID,$TYPE);
$send_spray_item_for_spray = FIND::SPRAY_MATERIAL_DEMAND_DISPATCH($REALATEDID,$ID,$TYPE);
$receive_raw_item_after_spray = FIND::SPRAY_RAW_MATERIAL_BATCH_RECEIVE($REALATEDID,$ID,$TYPE);
$recipe_wise_demand_receive_reject = FIND::RECEIPE_WISE_DEMAND_RECEIVE_REJECT($REALATEDID,$ID,$TYPE);

          


                


            
                $TOTAL_STOCK_IN =  
                $opening_stock['total_receive'] + 
                $local_purches['total_receive'] +
                $receive_item_after_molding['total_receive'] + 
                $receive_raw_item_after_print['actual_receive'] + 
                $receive_raw_item_after_spray['total_receive'] 
                ;

                $TOTAL_STOCK_OUT =  
                $local_purches['total_reject'] +
                $send_raw_item_for_molding['total_dispatch'] +  
                $receive_item_after_molding['total_reject'] +
                $receive_raw_item_after_print['total_reject'] +
                $send_raw_item_for_print['total_dispatch'] +
                $send_print_item_for_print['total_dispatch'] +
                $send_raw_item_for_spray['total_dispatch'] +
                $send_spray_item_for_spray['total_dispatch'] +
                $receive_raw_item_after_spray['total_reject'] +
                $recipe_wise_demand_receive_reject['total_dispatch'] 
                ;
        
                $ITEM_STOCK =  $TOTAL_STOCK_IN-$TOTAL_STOCK_OUT;
        

                if($ITEM_STOCK > 0){
            
                    $in_carton = $ITEM_STOCK/ ($product_info['pcs_in_cartoon'] ?? 0);
                    }else{
                    $in_carton = 0.00;
                    }
                
                return array(
                    'ITEM_STOCK' => $ITEM_STOCK,
                    'ITEM_STOCK_CARTOON' => number_format((float)$in_carton, 2, '.', '')

                    );
        
        
            }

        }




class log
{  

    private $id;
    protected $log_action;
    protected $username;
    protected $page;
    protected $ip;
    protected $log_name;
    private $user_id;

    public function __construct(string $log_action,  string $log_name)
    {
      
        if(!empty($_SESSION['NEWERP_SESS_MEMBER_ID'])){
            $id = $_SESSION['NEWERP_SESS_MEMBER_ID'];
        } else {
            $id = 0;
        }

        $log_date = date("Y-m-d") ;
        $log_time = date("h:i:s a"); 
        $ip =  $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR']; 

       $username = SETUP::ADMIN_SETUP($_SESSION['NEWERP_SESS_MEMBER_ID']);

        $this->log_action = $log_action;
        $this->username = $username['username'];
        $this->log_name = $log_name;
        $this->user_id = $id;
        $this->page =  basename($_SERVER['PHP_SELF']);
        $this->ip = $ip;
      

    }

    }
  
 
 




class SETUP 
{



public static function getRandomLightColor() {
    // Generate random RGB values, keeping them high for light colors
    $r = rand(128, 255); // Range: 128–255
    $g = rand(128, 255); // Range: 128–255
    $b = rand(128, 255); // Range: 128–255

    // Return the color in hexadecimal format
    return sprintf("#%02x%02x%02x", $r, $g, $b);
}



    public static function SETUP_LEDGER($ID)
    {
        $conn_me = Database::getInstance();

        $qry2 = $conn_me->prepare("SELECT * FROM `setup_ladger_head` where `id` = '".$ID."' ");
        $qry2->execute();
        $fetch = $qry2->fetch(PDO::FETCH_ASSOC);
    
        return array(

            'fetch' => $fetch
            );



    }


    public static function SETUP_ADVANCE()
    {
        $conn_me = Database::getInstance();

        $ck1 = $conn_me->prepare("SELECT * FROM `setup_advance` ");
        $ck1->execute();
        $fetch = $ck1->fetch(PDO::FETCH_ASSOC);
    
        return array(

            'fetch' => $fetch
            );



    }


    public static function SETUP_MESS_BOX($ID)
    {
        $conn_me = Database::getInstance();

        $ck1 = $conn_me->prepare("SELECT * FROM `mess_box` where `id` = '".$ID."'");
        $ck1->execute();
        $fetch = $ck1->fetch(PDO::FETCH_ASSOC);
    
        return array(

            'id' => $fetch['id'],
            'designation_id' => $fetch['designation_id'],
            'head_line' => $fetch['head_line'],
            'message' => $fetch['message'],
            'link' => $fetch['link']
            );



    }



    public static function SETUP_FG_DAMAGE_HISTORY($CODE)
    {
        $conn_me = Database::getInstance();

        $ck1 = $conn_me->prepare("SELECT *      FROM `damage_invoice` where `code` = '".$CODE."'");
        $ck1->execute();
        $fetch = $ck1->fetch(PDO::FETCH_ASSOC);
    
        return array(

            'id' => $fetch['id'],
            'code' => $fetch['code'],
            'invoice_no' => $fetch['invoice_no'],
            'invoice_date' => $fetch['invoice_date']
            );



    }


    
    public static function SETUP_FG_SALES_RETURN_HISTORY($ID)
    {
        $conn_me = Database::getInstance();

        $ck1 = $conn_me->prepare("SELECT *,date_format(invoice_date, '%d-%m-%Y') AS `invoice_date`  FROM `sales_return_invoice` where `id` = '".$ID."'");
        $ck1->execute();
        $fetch = $ck1->fetch(PDO::FETCH_ASSOC);
    
        return array(

            'id' => $fetch['id'],
            'code' => $fetch['code'],
            'invoice_no' => $fetch['invoice_no'],
            'invoice_date' => $fetch['invoice_date']
            );



    }


    public static function SETUP_FG_SALES_BY_ID($ID)
    {
        $conn_me = Database::getInstance();

        $ck1 = $conn_me->prepare("SELECT A.*,B.creadit_limit FROM `sales_invoice` A JOIN setup_customer B ON (A.customer_id = B.id) where A.`id` = '".$ID."'");
        $ck1->execute();
        $fetch = $ck1->fetch(PDO::FETCH_ASSOC);
    
        return array(

            'fetch' => $fetch
            );



    }



    public static function SETUP_FG_BATCH_WISE_HISTORY($CODE)
    {
        $conn_me = Database::getInstance();

        $ck1 = $conn_me->prepare("SELECT * , date_format(accepting_delivery_date, '%d-%m-%Y') AS `accepting_delivery_date`
        FROM `raw_request_recipe_wise` where `code` = '".$CODE."' GROUP BY `code` ");
        $ck1->execute();
        $fetch = $ck1->fetch(PDO::FETCH_ASSOC);

        return array(

            'id' => $fetch['id'],
            'code' => $fetch['code'],
            'invoice_no' => $fetch['invoice_no'],
            'time' => $fetch['time'],
            'accepting_delivery_date' => $fetch['accepting_delivery_date']
            );



    }



    public static function SETUP_FG_LOCAL_PURCHASE_HISTORY($CODE)
    {
        $conn_me = Database::getInstance();

        $ck1 = $conn_me->prepare("SELECT *,date_format(supplier_bill_date, '%d-%m-%Y') AS `supplier_bill_date`,date_format(date, '%d-%m-%Y') AS `invoice_date`  FROM `fg_local_purches` where `code` = '".$CODE."'");
        $ck1->execute();
        $fetch = $ck1->fetch(PDO::FETCH_ASSOC);

        $info_supplier = SETUP::SETUP_SUPPLIER($fetch['supplier_id']);
        $user_info = SETUP::ADMIN_SETUP($fetch['poster']);
        $invoice_info = FIND::TOTAL_PURCHASE_INVOICE_PRICE($CODE,'fg_local_purches'); 

        return array(

            'id' => $fetch['id'],
            'code' => $fetch['code'],
            'invoice_no' => $fetch['invoice_no'],
            'product_id' => $fetch['product_id'],
            'time' => $fetch['time'],
            'quantity' => $fetch['quantity'],
            'transport_cost' => $fetch['transport_cost'],
            'invoice_price' => $invoice_info['invoice_price'],
            'sub_total' => $invoice_info['sub_total'],
            'sub_total_without_other_cost' => $invoice_info['sub_total_without_other_cost'],
            'vat_cost' => $fetch['vat_cost'],
            'purches_price' => $fetch['purches_price'],
            'supplier_bill_date' => $fetch['supplier_bill_date'],
            'invoice_date' => $fetch['invoice_date'],
            'note' => $fetch['note'],
            'supplier_bill_no' =>  $fetch['supplier_bill_no'],
            'time' =>  $fetch['time'],
            'supplier_id' =>  $fetch['supplier_id'],
            'supplier_name' => $info_supplier['supplier_name'],
            'supplier_address' => $info_supplier['address'],
            'supplier_mobile' => $info_supplier['mobile'],
            'supplier_code' => $info_supplier['supplier_code'],
            'emp_name' => $user_info['hr_name']

            );



    }


    public static function SETUP_RAW_LOCAL_PURCHASE_HISTORY($CODE)
    {
        $conn_me = Database::getInstance();

        $ck1 = $conn_me->prepare("SELECT *,date_format(supplier_bill_date, '%d-%m-%Y') AS `supplier_bill_date`  FROM `fg_local_purches` where `code` = '".$CODE."'");
        $ck1->execute();
        $fetch = $ck1->fetch(PDO::FETCH_ASSOC);

        
        $info_supplier = SETUP::SETUP_SUPPLIER($fetch['supplier_id']);
        $user_info = SETUP::ADMIN_SETUP($fetch['poster']);
        $invoice_info = FIND::TOTAL_PURCHASE_INVOICE_PRICE($CODE,'raw_local_purches'); 


    
        return array(

            'id' => $fetch['id'],
            'code' => $fetch['code'],
            'invoice_no' => $fetch['invoice_no'],
            'product_id' => $fetch['product_id'],
            'time' => $fetch['time'],
            'quantity' => $fetch['quantity'],
            'transport_cost' => $fetch['transport_cost'],
            'invoice_price' => $invoice_info['invoice_price'],
            'sub_total' => $invoice_info['sub_total'],
            'sub_total_without_other_cost' => $invoice_info['sub_total_without_other_cost'],
            'vat_cost' => $fetch['vat_cost'],
            'purches_price' => $fetch['purches_price'],
            'supplier_bill_date' => $fetch['supplier_bill_date'],
            'invoice_date' => $fetch['invoice_date'],
            'note' => $fetch['note'],
            'supplier_bill_no' =>  $fetch['supplier_bill_no'],
            'time' =>  $fetch['time'],
            'supplier_id' =>  $fetch['supplier_id'],
            'supplier_name' => $info_supplier['supplier_name'],
            'supplier_address' => $info_supplier['address'],
            'supplier_mobile' => $info_supplier['mobile'],
            'supplier_code' => $info_supplier['supplier_code'],
            'emp_name' => $user_info['hr_name']
            );



    }


   
    public static function SETUP_QUOTATION($CODE)
    {
        $conn_me = Database::getInstance();

        $ck1 = $conn_me->prepare("SELECT *,date_format(invoice_date, '%d-%m-%Y') AS `invoice_date`  FROM `quotation_invoice` where `code` = '".$CODE."'");
        $ck1->execute();
        $fetch = $ck1->fetch(PDO::FETCH_ASSOC);

        
        $info_customer = SETUP::SETUP_CUSTOMER($fetch['customer_id']);
        $info_sales_person = SETUP::ADMIN_SETUP($fetch['sales_person']);
        $info_sales_by= SETUP::ADMIN_SETUP($fetch['sales_by']);
          


    
        return array(

            'id' => $fetch['id'],
            'code' => $fetch['code'],
            'invoice_no' => $fetch['invoice_no'],
            'customer_id' => $fetch['customer_id'],
            'customer_name' => $info_customer['customer_name'],
            'shop_name' => $info_customer['shop_name'],
            'mobile' => $info_customer['mobile'],
            'address' => $info_customer['address'],
            'invoice_date' =>  $fetch['invoice_date'],
            'sales_person' =>  $fetch['sales_person'],
            'sales_person_name' =>  "$info_sales_person[employee_code_with_prefix] $info_sales_person[hr_name]",
            'sales_by' =>  $fetch['sales_by'],
            'sales_by_name' =>  "$info_sales_by[employee_code_with_prefix] $info_sales_by[hr_name]"
            );



    }

    
    public static function SETUP_PRE_INVOICE($CODE)
    {
        $conn_me = Database::getInstance();

        $ck1 = $conn_me->prepare("SELECT *,date_format(invoice_date, '%d-%m-%Y') AS `invoice_date`  FROM `preorder_invoice` where `code` = '".$CODE."'");
        $ck1->execute();
        $fetch = $ck1->fetch(PDO::FETCH_ASSOC);

        
        $info_customer = SETUP::SETUP_CUSTOMER($fetch['customer_id']);
        $info_sales_person = SETUP::ADMIN_SETUP($fetch['sales_person']);
        $info_sales_by= SETUP::ADMIN_SETUP($fetch['sales_by']);
          


    
        return array(

            'id' => $fetch['id'],
            'code' => $fetch['code'],
            'invoice_no' => $fetch['invoice_no'],
            'customer_id' => $fetch['customer_id'],
            'customer_name' => $info_customer['customer_name'],
            'shop_name' => $info_customer['shop_name'],
            'mobile' => $info_customer['mobile'],
            'address' => $info_customer['address'],
            'invoice_date' =>  $fetch['invoice_date'],
            'sales_person' =>  $fetch['sales_person'],
            'sales_person_name' =>  "$info_sales_person[employee_code_with_prefix] $info_sales_person[hr_name]",
            'sales_by' =>  $fetch['sales_by'],
            'sales_by_name' =>  "$info_sales_by[employee_code_with_prefix] $info_sales_by[hr_name]"
            );



    }

    public static function DISPATCHER($ARRAY)
    {
        $conn_me = Database::getInstance();

        $json = json_decode($ARRAY);
        $count = count($json);
        $dispatcher_name = '';
        $sl = 1;
        for($i=0 ; $i<$count ;$i++){

        $details = SETUP::ADMIN_SETUP($json[$i]);
        $dispatcher_name .= $sl++ . '# ' . $details['employee_code_with_prefix'] . ' ' .$details['hr_name'] . ', ';
        }

        $dispatcher_name = trim($dispatcher_name,',');

        return array(

            'dispatcher_name' => $dispatcher_name,
            'count' => $count,

        );
    }

    public static function SETUP_SALES_INVOICE($CODE)
    {
        $conn_me = Database::getInstance();

        $ck1 = $conn_me->prepare("SELECT *,date_format(invoice_date, '%d-%m-%Y') AS `invoice_date`  FROM `sales_invoice` where `code` = '".$CODE."'");
        $ck1->execute();
        $fetch = $ck1->fetch(PDO::FETCH_ASSOC);

        $invoice_price = FIND::TOTAL_SALES_INVOICE_PRICE($fetch['id']);

        $info_brunch = SETUP::SETUP_BRUNCH($fetch['dispatch_from_which_brunch']);

        $info_customer = SETUP::SETUP_CUSTOMER($fetch['customer_id']);
        $info_sales_person = SETUP::ADMIN_SETUP($fetch['sales_person']);
        $info_sales_by= SETUP::ADMIN_SETUP($fetch['sales_by']);
        if(!empty($fetch['dispatcher_id'])){
            $info_dispatcher_list= SETUP::DISPATCHER($fetch['dispatcher_id']);
           $dispatcher_name =  $info_dispatcher_list['dispatcher_name'];
           $dispatcher_id =   $fetch['dispatcher_id']; 
           $dispatcher_count =  $info_dispatcher_list['count'];
        }else{
            $dispatcher_name = 'No Dispatcher Found';
            $dispatcher_id = ''; 
            $dispatcher_count = '';
        }

 

    
        return array(

            'id' => $fetch['id'],
            'code' => $fetch['code'],
            'invoice_no' => $fetch['invoice_no'],
            'narration' => (!empty($fetch['narration'])) ? $fetch['narration'] : '' ,
            'BrunchName' => $info_brunch['brunch'],
            'dispatcher_id' => $dispatcher_id,
            'dispatcher_count' => $dispatcher_count,
            'customer_id' => $fetch['customer_id'],
            'transection_id' => $fetch['transection_id'],
            'time' => $fetch['time'],
            'customer_name' => $info_customer['customer_name'],
            'district' => $info_customer['district'],
            'upazila' => $info_customer['upazila'],
            'dispatcher_name' => $dispatcher_name,
            'shop_name' => $info_customer['shop_name'],
            'mobile' => $info_customer['mobile'],
            'address' => $info_customer['address'],
            'sub_total' =>  $invoice_price['sub_total'],
            'total_invoice_price' =>  $invoice_price['price'],
            'sub_total_without_discout' =>  $invoice_price['sub_total_without_discout'],
            'total_pcs' => $invoice_price['total_pcs'],
            'total_ctn' => $invoice_price['total_ctn'],
            'discount' => $fetch['discount'],
            'transport_cost' => $fetch['transport_cost'],
            'total_vat_cost' => $fetch['total_vat_cost'],
            'invoice_date' =>  $fetch['invoice_date'],
            'sales_person' =>  $fetch['sales_person'],
            'generate_challan' =>  $fetch['generate_challan'],
            'sales_person_name' =>  "$info_sales_person[hr_name]",
            'sales_person_brunch_address1' =>  "$info_sales_person[brunch_address1]",
            'sales_person_brunch_address2' =>  "$info_sales_person[brunch_address2]",
            'sales_by' =>  $fetch['sales_by'],
            'brunch_id' =>  $fetch['brunch_id'],
            'sales_by_name' =>  "$info_sales_by[employee_code_with_prefix] $info_sales_by[hr_name]"
            );



    }



    public static function SETUP_QUATATION_INVOICE($id)
    {
        $conn_me = Database::getInstance();

        $ck1 = $conn_me->prepare("SELECT *,date_format(invoice_date, '%d-%m-%Y') AS `invoice_date`  FROM `quotation_invoice` where `id` = '".$id."'");
        $ck1->execute();
        $fetch = $ck1->fetch(PDO::FETCH_ASSOC);

        $invoice_price = FIND::TOTAL_QUATATION_INVOICE_PRICE($fetch['id']);

        $info_brunch = SETUP::SETUP_BRUNCH($fetch['brunch_id']);

        $info_customer = SETUP::SETUP_CUSTOMER($fetch['customer_id']);
        $info_sales_person = SETUP::ADMIN_SETUP($fetch['sales_person']);
        $info_sales_by= SETUP::ADMIN_SETUP($fetch['sales_by']);



    
        return array(

            'id' => $fetch['id'],
            'code' => $fetch['code'],
            'invoice_no' => $fetch['invoice_no'],
            'BrunchName' => $info_brunch['brunch'],
            'customer_id' => $fetch['customer_id'],
            'customer_name' => $info_customer['customer_name'],
            'district' => $info_customer['district'],
            'upazila' => $info_customer['upazila'],
            'shop_name' => $info_customer['shop_name'],
            'mobile' => $info_customer['mobile'],
            'address' => $info_customer['address'],
            'total_invoice_price' =>  $invoice_price['invoice_price'],
            'invoice_date' =>  $fetch['invoice_date'],
            'sales_person' =>  $fetch['sales_person'],
            'sales_person_name' =>  "$info_sales_person[employee_code_with_prefix] $info_sales_person[hr_name]",
            'sales_person_brunch_address1' =>  "$info_sales_person[brunch_address1]",
            'sales_person_brunch_address2' =>  "$info_sales_person[brunch_address2]",
            'sales_by' =>  $fetch['sales_by'],
            'sales_by_name' =>  "$info_sales_by[employee_code_with_prefix] $info_sales_by[hr_name]"
            );



    }
    


    public static function ACCOUNT_TRANSECTION($ID)
    {
        $conn_me = Database::getInstance();

        $ck1 = $conn_me->prepare("SELECT *,date_format(transection_date, '%d-%m-%Y') AS `transectiondate`,date_format(check_date, '%d-%m-%Y') AS `check_date`  FROM `account_transection` where `id` = '".$ID."'");
        $ck1->execute();
        $fetch_ck1 = $ck1->fetch(PDO::FETCH_ASSOC);

        $info_brunch = SETUP::SETUP_BRUNCH($fetch_ck1['brunch_id']);


            if(!empty($fetch_ck1['transection_head_id'])){
            $info2 = SETUP::ACCOUNT_HEAD_SETUP($fetch_ck1['transection_head_id']);
            $transection_head_name = $info2['account_head'];
            }else{
            $transection_head_name = 'Not Set Yet';
            }

        if(!empty($fetch_ck1['ledger_id'])){
        $info2 = SETUP::SETUP_LEDGER($fetch_ck1['ledger_id']);
        $ladger_name = $info2['fetch']['name'];
        }else{
        $ladger_name = 'Not Set Yet';
        }

           
        if($fetch_ck1['transection_by'] == 'Cash' ){


            $bank_name = "";
            $check_no = "";
            $mobile_banking_no = "";
            $check_date =  "";
            $title1 = "";
            $title2 = ""; 
            $title3="";
            $title4="";
            $style="";
            $detaails = '';

        }else if ($fetch_ck1['transection_by'] == 'Bank'){

            $info_bank = SETUP::BANK_SETUP( $fetch_ck1['transection_by_id']);
            $bank_name = "$info_bank[bank_name] $info_bank[account_number]";
            $check_no = "$fetch_ck1[check_number]";
            if($fetch_ck1['check_date'] == '00/00/0000' ){$check_date = "";}else{ $check_date = "$fetch_ck1[check_date]"; }
            
            $mobile_banking_no = "";
            $style="border: 1px solid black";
           if(!empty($check_no)){  
            $title1 = "Cheque Transaction";
            $title2 = "Bank Name $fetch_ck1[note]";
            $title3="Cheque No. $check_no";
            $title4="Cheque Date. $check_date";
            }else{  
            $title1 = "Bank Transaction" ;
            $title2 = "Bank Name $fetch_ck1[note]";
            $title3="Transaction No. $info_bank[account_number]";
            $title4="Transaction Date. $fetch_ck1[transectiondate]";
            }
        
        $detaails = $bank_name . " >> ";

        }else if($fetch_ck1['transection_by'] == 'Mobile-Banking'){

            $info_bank = SETUP::BANK_MOBILE_BANKING( $fetch_ck1['transection_by_id']);
            $bank_name = "";
            $check_no = "";
            $check_date = "";
            $mobile_banking_no = $info_bank['mobile_number'];
            $title1 = $info_bank['mobile_bank_name'];
            $title2 = "Sender Mobile  $fetch_ck1[note]";
            $title3="Transaction No. $mobile_banking_no";
            $title4="Transaction Date. $fetch_ck1[transectiondate]";
            $style="border: 1px solid black";
            $detaails = $mobile_banking_no . " >> ";
        }else{
            $bank_name = "";
            $check_no = "";
            $mobile_banking_no = "";
            $check_date = "";
            $title1 = "";
            $title2 = "";
            $title3="";
            $title4="";
            $style="";
            $detaails = '';
        }


        if($fetch_ck1['transection_type'] == 'INCOME' ){
            $Transectionnow = "$fetch_ck1[in_amount]";
        }else if($fetch_ck1['transection_type'] == 'EXPENSE' ){
            $Transectionnow = "$fetch_ck1[out_amount]";
        }else{
            $Transectionnow = 0.00;

        }

        if($fetch_ck1['transection_to'] == 'Supplier' ){
            
        $info1 = SETUP::SETUP_SUPPLIER($fetch_ck1['transection_to_id']);
        $name = "Supplier: $info1[supplier_name] ($info1[mobile]) ";
        $only_name="$info1[supplier_name]";
        $only_mobile="$info1[mobile]";
        $address="$info1[address]";
        $district="";
        $upazila="";
        $copy_text = "সাপ্লায়ার কপি";
         

        }else if ($fetch_ck1['transection_to'] == 'Customer'){

        $info1 = SETUP::SETUP_CUSTOMER($fetch_ck1['transection_to_id']);

        $name = "Customer: $info1[shop_name] ($info1[mobile]) ";
       
        $only_name="$info1[shop_name]";
        $only_mobile="$info1[mobile]";
        $address="$info1[address]";
        $district="$info1[district]";
        $upazila="$info1[upazila]";
        $copy_text = "কাস্টমার কপি";


        }else if ($fetch_ck1['transection_to'] == 'Employee'){

        $info1 = SETUP::SETUP_EMPLOYEEY($fetch_ck1['transection_to_id']);
        $name = "Employee: $info1[employee_prefix] $info1[code] $info1[name]";
        $only_name="$info1[name]";
        $only_mobile="$info1[mob_no]";
        $address="$info1[house] $info1[po_office] $info1[village]";
        $district="";
        $upazila="";
        $copy_text = "কর্মচারীর কপি";

 

        }else{
            
            $name = "$ladger_name >> $transection_head_name";
            $only_name="$transection_head_name";
            $only_mobile="";
            $address="";
            $district="";
            $upazila="";
            $copy_text = "কর্মচারীর কপি";

       
     

    
    }



        return array(
            'id' => $fetch_ck1['id'],
            'code' => $fetch_ck1['code'],
            'invoice_no' => $fetch_ck1['invoice_no'],
            'time' => $fetch_ck1['time'],
            'transection_type' => $fetch_ck1['transection_type'],
            'ledger_id' => $fetch_ck1['ledger_id'],
            'poster' => $fetch_ck1['poster'],
            'data_inserted_from' => $fetch_ck1['data_inserted_from'],
            'in_amount' => $fetch_ck1['in_amount'],
            'out_amount' => $fetch_ck1['out_amount'],
            'due' => 0.00,
            'brunch_id' => $fetch_ck1['brunch_id'],
            'detaails' => $detaails,
            'total_receive' =>0.00,
            'total_invoice_price' => 0.00,
            'ladger_name' => $ladger_name,
            'transection_head_name' => $transection_head_name,
            'transection_head_id' => $fetch_ck1['transection_head_id'],
            'transection_id' => $fetch_ck1['transection_id'],
            'transection_to' => $fetch_ck1['transection_to'],
            'transection_to_id' => $fetch_ck1['transection_to_id'],
            'transection_by' => $fetch_ck1['transection_by'],
            'transection_by_id' => $fetch_ck1['transection_by_id'],
            'check_number' => $fetch_ck1['check_number'],
            'check_date' => $fetch_ck1['check_date'],
            'note' => $fetch_ck1['note'],
            'copy_text' => $copy_text,
            'details_of_transection_to' => $name,
            'only_name' => $only_name,
            'only_mobile' => $only_mobile,
            'address' => $address,
            'upazila' => $upazila,
            'transection_now' => $Transectionnow,
            'district' => $district,
            'transection_date' => $fetch_ck1['transection_date'],
            'transectiondate' => $fetch_ck1['transectiondate'],
            'address_line_one' => $info_brunch['address_line_one'],
            'address_line_two' => $info_brunch['address_line_two'],
            'brunch_name' => $info_brunch['brunch'],
            'bank_name' => $bank_name,
            'check_no' => $check_no,
            'mobile_banking_no' => $mobile_banking_no,
            'check_date' => $check_date,
            'title1' =>  $title1,
            'title2' =>  $title2,
            'title3' =>  $title3,
            'title4' =>  $title4,
            'style' => $style
            );



    }



    public static function ACCOUNT_TRANSECTION_PENDING($ID)
    {
        $conn_me = Database::getInstance();
    
        $ck1 = $conn_me->prepare("SELECT *,date_format(transection_date, '%d-%m-%Y') AS `transectiondate`,date_format(check_date, '%d-%m-%Y') AS `check_date`  FROM `account_posting_pending` where `id` = '".$ID."'");
        $ck1->execute();
        $fetch_ck1 = $ck1->fetch(PDO::FETCH_ASSOC);
    
        $info_brunch = SETUP::SETUP_BRUNCH($fetch_ck1['brunch_id']);
    
    
            if(!empty($fetch_ck1['transection_head_id'])){
            $info2 = SETUP::ACCOUNT_HEAD_SETUP($fetch_ck1['transection_head_id']);
            $transection_head_name = $info2['account_head'];
            }else{
            $transection_head_name = 'Not Set Yet';
            }
    
            if(!empty($fetch_ck1['ledger_id'])){
            $info2 = SETUP::SETUP_LEDGER($fetch_ck1['ledger_id']);
            $ladger_name = $info2['fetch']['name'];
            }else{
            $ladger_name = 'Not Set Yet';
            }
    
           
        if($fetch_ck1['transection_by'] == 'Cash' ){
    
    
            $bank_name = "";
            $check_no = "";
            $mobile_banking_no = "";
            $check_date =  "";
            $title1 = "";
            $title2 = "Cash"; 
            $title3="";
            $title4="";
            $style="";
    
        }else if ($fetch_ck1['transection_by'] == 'Bank'){
    
            $info_bank = SETUP::BANK_SETUP( $fetch_ck1['transection_by_id']);
            $bank_name = "$info_bank[bank_name] $info_bank[account_number]";
            $check_no = "$fetch_ck1[check_number]";
            if($fetch_ck1['check_date'] == '00/00/0000' ){$check_date = "";}else{ $check_date = "$fetch_ck1[check_date]"; }
            
            $mobile_banking_no = "";
            $style="border: 1px solid black";
           if(!empty($check_no)){  
            $title1 = "Cheque Transaction";
            $title2 = "$bank_name";
            $title3="Cheque No. $check_no";
            $title4="Cheque Date. $check_date";
            }else{  
            $title1 = "Bank Transaction" ;
            $title2 = "$bank_name ";
            $title3="Transaction No. $info_bank[account_number]";
            $title4="Transaction Date. $fetch_ck1[transectiondate]";
            }
        
        }else if($fetch_ck1['transection_by'] == 'Mobile-Banking'){
    
            $info_bank = SETUP::BANK_MOBILE_BANKING( $fetch_ck1['transection_by_id']);
            $bank_name = "";
            $check_no = "";
            $check_date = "";
            $mobile_banking_no = $info_bank['mobile_number'];
            $title1 = $info_bank['mobile_bank_name'];
            $title2 = "$title1 $mobile_banking_no";
            $title3="Transaction No. $mobile_banking_no";
            $title4="Transaction Date. $fetch_ck1[transectiondate]";
            $style="border: 1px solid black";
        }else{
            $bank_name = "";
            $check_no = "";
            $mobile_banking_no = "";
            $check_date = "";
            $title1 = "";
            $title2 = "";
            $title3="";
            $title4="";
            $style="";
        }
    
    
        if($fetch_ck1['transection_type'] == 'INCOME' ){
            $Transectionnow = "$fetch_ck1[in_amount]";
        }else if($fetch_ck1['transection_type'] == 'EXPENSE' ){
            $Transectionnow = "$fetch_ck1[out_amount]";
        }else{
            $Transectionnow = 0.00;
    
        }
    
        if($fetch_ck1['transection_to'] == 'Supplier' ){
            
        $info1 = SETUP::SETUP_SUPPLIER($fetch_ck1['transection_to_id']);
        $name = "Supplier: $info1[supplier_name] ($info1[mobile]) ";
        $only_name="$info1[supplier_name]";
        $only_mobile="$info1[mobile]";
        $address="$info1[address]";
        $district="";
        $upazila="";
        $copy_text = "সাপ্লায়ার কপি";
         
    
        }else if ($fetch_ck1['transection_to'] == 'Customer'){
    
        $info1 = SETUP::SETUP_CUSTOMER($fetch_ck1['transection_to_id']);
    
        $name = "Customer: $info1[shop_name] ($info1[mobile]) ";
    
        $only_name="$info1[shop_name]";
        $only_mobile="$info1[mobile]";
        $address="$info1[address]";
        $district="$info1[district]";
        $upazila="$info1[upazila]";
        $copy_text = "কাস্টমার কপি";
    
    
        }else if ($fetch_ck1['transection_to'] == 'Employee'){
    
        $info1 = SETUP::SETUP_EMPLOYEEY($fetch_ck1['transection_to_id']);
        $name = "Employee: $info1[employee_prefix] $info1[code] $info1[name]";
        $only_name="$info1[name]";
        $only_mobile="$info1[mob_no]";
        $address="$info1[house] $info1[po_office] $info1[village]";
        $district="";
        $upazila="";
        $copy_text = "কর্মচারীর কপি";
    
    
    
        }else{
            
            $name = "$ladger_name >> $transection_head_name";
    
            $only_name="$transection_head_name";
            $only_mobile="";
            $address="";
            $district="";
            $upazila="";
        
            $copy_text = "কর্মচারীর কপি";
    
       
     
    
    
    }
    
    
    
        return array(
            'id' => $fetch_ck1['id'],
            'code' => $fetch_ck1['code'],
            'ledger_id' => $fetch_ck1['ledger_id'],
            'collect_by' => $fetch_ck1['collect_by'],
            'invoice_no' => $fetch_ck1['invoice_no'],
            'time' => $fetch_ck1['time'],
            'transection_type' => $fetch_ck1['transection_type'],
            'poster' => $fetch_ck1['poster'],
            'data_inserted_from' => $fetch_ck1['data_inserted_from'],
            'in_amount' => $fetch_ck1['in_amount'],
            'out_amount' => $fetch_ck1['out_amount'],
            'brunch_id' => $fetch_ck1['brunch_id'],
            'ladger_name' => $ladger_name,
            'transection_head_name' => $transection_head_name,
            'transection_head_id' => $fetch_ck1['transection_head_id'],
            'transection_to' => $fetch_ck1['transection_to'],
            'transection_id' => $fetch_ck1['transection_id'],
            'transection_to_id' => $fetch_ck1['transection_to_id'],
            'transection_by' => $fetch_ck1['transection_by'],
            'transection_by_id' => $fetch_ck1['transection_by_id'],
            'check_number' => $fetch_ck1['check_number'],
            'check_date' => $fetch_ck1['check_date'],
            'note' => $fetch_ck1['note'],
            'copy_text' => $copy_text,
            'details_of_transection_to' => $name,
            'only_name' => $only_name,
            'only_mobile' => $only_mobile,
            'address' => $address,
            'upazila' => $upazila,
            'transection_now' => $Transectionnow,
            'district' => $district,
            'transection_date' => $fetch_ck1['transection_date'],
            'transectiondate' => $fetch_ck1['transectiondate'],
            'address_line_one' => $info_brunch['address_line_one'],
            'address_line_two' => $info_brunch['address_line_two'],
            'brunch_name' => $info_brunch['brunch'],
            'bank_name' => $bank_name,
            'check_no' => $check_no,
            'mobile_banking_no' => $mobile_banking_no,
            'check_date' => $check_date,
            'title1' =>  $title1,
            'title2' =>  $title2,
            'title3' =>  $title3,
            'title4' =>  $title4,
            'style' => $style
            );
    
    
    
    }
    



    public static function ACCOUNT_HEAD_SETUP($ID)
    {
        $conn_me = Database::getInstance();

        $ck1 = $conn_me->prepare("SELECT *  FROM `setup_ac_head` where `id` = '".$ID."'");
        $ck1->execute();
        $fetch_ck1 = $ck1->fetch(PDO::FETCH_ASSOC);
    

        return array(
            'id' => $fetch_ck1['id'],
            'ledger_id' => $fetch_ck1['ledger_id'],
            'account_head' => $fetch_ck1['account_head'],
            'account_type' => $fetch_ck1['account_type'],
            'description' => $fetch_ck1['description'],
            'status' => $fetch_ck1['status']
    
            );
    
    
    
    }
    


    public static function BANK_MOBILE_BANKING($ID)
    {
        $conn_me = Database::getInstance();

        $ck1 = $conn_me->prepare("SELECT *  FROM `setup_mobile_banking` where `id` = '".$ID."'");
        $ck1->execute();
        $fetch_ck1 = $ck1->fetch(PDO::FETCH_ASSOC);


        return array(
            'id' => $fetch_ck1['id'],
            'mobile_bank_name' => $fetch_ck1['mobile_bank_name'],
            'mobile_number' => $fetch_ck1['mobile_number'],
            'description' => $fetch_ck1['description'],
            'status' => $fetch_ck1['status']

            );



    }



    public static function BANK_SETUP($ID)
    {
        $conn_me = Database::getInstance();

        $ck1 = $conn_me->prepare("SELECT *  FROM `setup_bank` where `id` = '".$ID."'");
        $ck1->execute();
        $fetch_ck1 = $ck1->fetch(PDO::FETCH_ASSOC);




        return array(
            'id' => $fetch_ck1['id'],
            'bank_name' => $fetch_ck1['bank_name'],
            'brunch_name' => $fetch_ck1['brunch_name'],
            'account_number' => $fetch_ck1['account_number'],
            'account_name' => $fetch_ck1['account_name'],
            'description' => $fetch_ck1['description'],
            'status' => $fetch_ck1['status']

            );



    }

    public static function RAW_PRINT($ID)
    {
        $conn_me = Database::getInstance();

        $ck1 = $conn_me->prepare("SELECT *  FROM `raw_print` where `id` = '".$ID."'");
        $ck1->execute();
        $fetch_ck1 = $ck1->fetch(PDO::FETCH_ASSOC);


        $username = SETUP::ADMIN_SETUP($fetch_ck1['poster']);



        return array(
            'id' => $fetch_ck1['id'],
            'invoice_no' => $fetch_ck1['invoice_no'],
            'supplier_or_factory_id' => $fetch_ck1['supplier_or_factory_id'],
            'material_id' => $fetch_ck1['material_id'],
            'send_to' => $fetch_ck1['send_to'],
            'batch_quantity' => $fetch_ck1['batch_quantity'],
            'invoice_date' => $fetch_ck1['invoice_date'],
            'accepting_delivery_date' => $fetch_ck1['accepting_delivery_date'],
            'poster' => $fetch_ck1['poster'],
            'poster_name' => $username['hr_name'],
            'time' => $fetch_ck1['time'],
            'date' => $fetch_ck1['date'],
            'status' => $fetch_ck1['status'],

            );



    }



    public static function RAW_SPRAY($ID)
    {
        $conn_me = Database::getInstance();

        $ck1 = $conn_me->prepare("SELECT *  FROM `raw_spray` where `id` = '".$ID."'");
        $ck1->execute();
        $fetch_ck1 = $ck1->fetch(PDO::FETCH_ASSOC);


        $username = SETUP::ADMIN_SETUP($fetch_ck1['poster']);



        return array(
            'id' => $fetch_ck1['id'],
            'invoice_no' => $fetch_ck1['invoice_no'],
            'supplier_or_factory_id' => $fetch_ck1['supplier_or_factory_id'],
            'material_id' => $fetch_ck1['material_id'],
            'send_to' => $fetch_ck1['send_to'],
            'batch_quantity' => $fetch_ck1['batch_quantity'],
            'invoice_date' => $fetch_ck1['invoice_date'],
            'accepting_delivery_date' => $fetch_ck1['accepting_delivery_date'],
            'poster' => $fetch_ck1['poster'],
            'poster_name' => $username['hr_name'],
            'time' => $fetch_ck1['time'],
            'date' => $fetch_ck1['date'],
            'status' => $fetch_ck1['status'],

            );



    }




    public static function RAW_MOLDING($ID)
    {
        $conn_me = Database::getInstance();

        $ck1 = $conn_me->prepare("SELECT *  FROM `raw_molding` where `id` = '".$ID."'");
        $ck1->execute();
        $fetch_ck1 = $ck1->fetch(PDO::FETCH_ASSOC);


        $username = SETUP::ADMIN_SETUP($fetch_ck1['poster']);



        return array(
            'id' => $fetch_ck1['id'],
            'invoice_no' => $fetch_ck1['invoice_no'],
            'supplier_or_factory_id' => $fetch_ck1['supplier_or_factory_id'],
            'supporting_id' => $fetch_ck1['supporting_id'],
            'send_to' => $fetch_ck1['send_to'],
            'batch_quantity' => $fetch_ck1['batch_quantity'],
            'invoice_date' => $fetch_ck1['invoice_date'],
            'accepting_delivery_date' => $fetch_ck1['accepting_delivery_date'],
            'poster' => $fetch_ck1['poster'],
            'poster_name' => $username['hr_name'],
            'time' => $fetch_ck1['time'],
            'date' => $fetch_ck1['date'],
            'status' => $fetch_ck1['status'],

            );



    }

    public static function RAW_REQUEST_RECEIPE_WISE_ITEM($ID)
    {
        $conn_me = Database::getInstance();

        $ck1 = $conn_me->prepare("SELECT *  FROM `raw_request_recipe_wise_item` where `id` = '".$ID."'");
        $ck1->execute();
        $fetch_ck1 = $ck1->fetch(PDO::FETCH_ASSOC);


        $username = SETUP::ADMIN_SETUP($fetch_ck1['poster']);



        return array(
            'id' => $fetch_ck1['id'],
            'demand_code' => $fetch_ck1['demand_code'],
            'material_id' => $fetch_ck1['material_id'],
            'demand_quantity' => $fetch_ck1['demand_quantity'],
            'actual_receive_qty' => $fetch_ck1['actual_receive_qty'],
            'lastupdate' => $fetch_ck1['lastupdate'],
            'poster' => $fetch_ck1['poster'],
            'poster_name' => $username['hr_name'],
            'time' => $fetch_ck1['time'],
            'date' => $fetch_ck1['date'],
            'status' => $fetch_ck1['status'],

            );



    }


    public static function RAW_REQUEST_RECEIPE_WISE($ID)
    {
        $conn_me = Database::getInstance();

        $ck1 = $conn_me->prepare("SELECT * , date_format(accepting_delivery_date, '%d-%m-%Y') AS `accepting_delivery_date`
        FROM `raw_request_recipe_wise` where `id` = '".$ID."'");
        $ck1->execute();
        $fetch_ck1 = $ck1->fetch(PDO::FETCH_ASSOC);


        $username = SETUP::ADMIN_SETUP($fetch_ck1['poster']);



        return array(
            'id' => $fetch_ck1['id'],
            'invoice_no' => $fetch_ck1['invoice_no'],
            'invoice_date' => $fetch_ck1['invoice_date'],
            'send_to' => $fetch_ck1['send_to'],
            'send_to_id' => $fetch_ck1['send_to_id'],
            'product_id' => $fetch_ck1['product_id'],
            'batch_quantity' => $fetch_ck1['batch_quantity'],
            'actual_output' => $fetch_ck1['actual_output'],
            'note' => $fetch_ck1['note'],
            'lastupdate' => $fetch_ck1['lastupdate'],
            'poster' => $fetch_ck1['poster'],
            'poster_name' => $username['hr_name'],
            'time' => $fetch_ck1['time'],
            'date' => $fetch_ck1['date'],
            'pi_no' => $fetch_ck1['user_given_invoiceno'],
            'accepting_delivery_date' => $fetch_ck1['accepting_delivery_date'],
            'status' => $fetch_ck1['status']
            );



    }




    public static function MENU_PERMISSION($MENU_ID,$EMLOYEE_ID)
    {
        $conn_me = Database::getInstance();

        $ck1 = $conn_me->prepare("SELECT *  FROM `menu_permission` WHERE `employee_id` = '".$EMLOYEE_ID."' AND `menu_id` = '".$MENU_ID."'");
        $ck1->execute();
        $fetch_ck1 = $ck1->fetch(PDO::FETCH_ASSOC);

        if ($ck1->rowCount() > 0) { 

            $view_check = $fetch_ck1['view_check'];
            $edit_check = $fetch_ck1['edit_check'];

        }else{

            $view_check = '';
            $edit_check = '';
        }


        return array(
            'view_check' => $view_check,
            'edit_check' => $edit_check,
    
            );



    }


    public static function TOP_BAR()
    {

        $conn_me = Database::getInstance();

        $info_employee = SETUP::ADMIN_SETUP($_SESSION['NEWERP_SESS_MEMBER_ID']);


        $notification_marque = '';
        $notic_qry = $conn_me->prepare("SELECT * FROM `notice_bord`  WHERE `status` = 'true' ORDER BY `id` ASC");
        $notic_qry->execute();
        $fetch_notification = $notic_qry->fetchAll(PDO::FETCH_ASSOC);
        if ($notic_qry->rowCount() > 0)
        {
            foreach($fetch_notification AS $fetch_N) {
        
        
                $notification_marque .=  '<i class="fa fa-asterisk"></i>
                '.  $fetch_N['notice_text'] . '
                ' ;
               
            }
        
        }
    

        $active_user_query = $conn_me->prepare("SELECT * FROM `webpush_member`  WHERE `flag` = 1 ORDER BY `member_id` ASC");
        $active_user_query->execute();
        $fetch_user = $active_user_query->fetchAll(PDO::FETCH_ASSOC);
        $count_user = $active_user_query->rowCount();


        $pendingTask = $conn_me->prepare("SELECT * FROM `mess_box`  WHERE `designation_id` = '".$info_employee['designation']."' AND `seen` = 0 ");
        $pendingTask->execute();
        $fetch_task = $pendingTask->fetchAll(PDO::FETCH_ASSOC);
        $count_task = $pendingTask->rowCount();


       
   
$content = '<ul class="x-navigation x-navigation-horizontal x-navigation-panel hidden-print">';

$content .= '<li class="xn-icon-button">
<a href="#" class="x-navigation-minimize"><span class="fa fa-dedent"></span></a>
</li>';

//$content .= '<li class="xn-search">
//<form role="form">
//    <input type="text" name="search" placeholder="Search..."/>
//</form>
//</li>  ';

$content .= '<li class="xn-icon-button pull-right">
<a href="#" class="mb-control" data-box="#mb-signout"><span class="fa fa-sign-out"></span></a>                        
</li> ';

$content .='<li class="xn-icon-button pull-right">
<a href="#"><span class="fa fa-comments"></span></a>
<div class="informer informer-danger">'.$count_user.'</div>
<div class="panel panel-primary animated zoomIn xn-drop-left xn-panel-dragging">
    <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-comments"></span> Active User</h3>                                
        <div class="pull-right">
            <span class="label label-danger">'.$count_user.'</span>
        </div>
    </div>
    <div class="panel-body list-group list-group-contacts scroll" style="height: 200px;">';
       

    $content .= '</div>     
    <div class="panel-footer text-center">
    </div>                            
</div>                        
</li>';


$content .= '<li class="xn-icon-button pull-right">
<a href="#"><span class="fa fa-tasks"></span></a>
<div class="informer informer-warning">'.$count_task.'</div>
<div class="panel panel-primary animated zoomIn xn-drop-left xn-panel-dragging">
    <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-tasks"></span> Pending Task</h3>                                
        <div class="pull-right">
            <span class="label label-warning">'.$count_task.'</span>
        </div>
    </div>
    <div class="panel-body list-group scroll" style="height: 200px;">     ';                           
    foreach($fetch_task AS $fetch_user_task) {

        $user_info = SETUP::ADMIN_SETUP($fetch_user_task['mess_posted_by']);

        $content .= '<a onclick="SeenMess(\''.$fetch_user_task['id'].'\',\''.$fetch_user_task['link'].'\')" class="list-group-item user">
        <div class="list-group-status status-online"></div>
        <img src="upload/employee_photo/'.$user_info['photo'].'" class="pull-left" alt="John Doe"/>
        <span class="contacts-title">'.$fetch_user_task['head_line'].' ::: '.$user_info['hr_name'].'</span>
        <p style="font-size:11px">'.$fetch_user_task['message'].'</p>
    </a>';
    
      
    }                     
    $content .'</div>     
    <div class="panel-footer text-center">
      
    </div>                            
</div>                        
</li>';



$content .= '<li class="xn-icon-button pull-right">
 <marquee style="color:white;padding-top:10px;" behavior="scroll" direction="left" Scrollamount=3
        onmousedown="this.stop();"
        onmouseup="this.start();">'.$notification_marque.'</marquee>                       
</li> ';


$content .= '</ul>';
        


    return array(
        'top_bar_content' => $content

        );



    }


   

    public static function SETUP_FG_RECIPE($ID)
    {
        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `receip_fg`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);



$product_info = SETUP::SETUP_PRODUCT($fetch['product_id']);
$raw_material_info = SETUP::SETUP_RAW_MATERIAL($fetch['raw_material_id']);

        return array(
            'id' => $fetch['id'],
            'product_id' => $fetch['product_id'],
            'product_name' => $product_info['product_code'] . ' ' . $product_info['product_name'],
            'product_unit' => $product_info['unit'],
            'raw_material_unit' => $raw_material_info['unit'],
            'raw_material_id' => $fetch['raw_material_id'],
            'raw_material_name' => $raw_material_info['product_name'],
            'quantity' => $fetch['quantity']

            );

    }


    public static function SETUP_SPRAY_RECIPE($ID)
    {
        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `receip_spray`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);



        $spray_material_info = SETUP::SETUP_RAW_MATERIAL($fetch['spray_material_id']);
        $raw_material_info = SETUP::SETUP_RAW_MATERIAL($fetch['raw_material_id']);

        return array(
            'id' => $fetch['id'],
            'spray_material_id' => $fetch['spray_material_id'],
            'product_name' => $spray_material_info['material_code'] . ' ' . $spray_material_info['product_name'],
            'product_unit' => $spray_material_info['unit'],
            'raw_material_unit' => $raw_material_info['unit'],
            'raw_material_id' => $fetch['raw_material_id'],
            'raw_material_name' => $raw_material_info['product_name'],
            'quantity' => $fetch['quantity']

            );

    }

    public static function SETUP_PRINT_RECIPE($ID)
    {
        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `receip_print`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);



        $print_material_info = SETUP::SETUP_RAW_MATERIAL($fetch['print_material_id']);
        $raw_material_info = SETUP::SETUP_RAW_MATERIAL($fetch['raw_material_id']);

        return array(
            'id' => $fetch['id'],
            'print_material_id' => $fetch['print_material_id'],
            'product_name' => $print_material_info['material_code'] . ' ' . $print_material_info['product_name'],
            'product_unit' => $print_material_info['unit'],
            'raw_material_unit' => $raw_material_info['unit'],
            'raw_material_id' => $fetch['raw_material_id'],
            'raw_material_name' => $raw_material_info['product_name'],
            'quantity' => $fetch['quantity']

            );

    }



    public static function SETUP_SUPPORTING_RECIPE($ID)
    {
        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `receip_supporting_goods`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);



        $supporting_info = SETUP::SETUP_RAW_MATERIAL($fetch['supporting_id']);
        $raw_material_info = SETUP::SETUP_RAW_MATERIAL($fetch['raw_material_id']);

        return array(
            'id' => $fetch['id'],
            'supporting_id' => $fetch['supporting_id'],
            'product_name' => $supporting_info['material_code'] . ' ' . $supporting_info['product_name'],
            'product_unit' => $supporting_info['unit'],
            'raw_material_unit' => $raw_material_info['unit'],
            'raw_material_id' => $fetch['raw_material_id'],
            'raw_material_name' => $raw_material_info['product_name'],
            'quantity' => $fetch['quantity']

            );

    }


    public static function INVOICE_TIMELINE($invoice_id)
    {
        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `invoice_timeline`  WHERE `invoice_id` = '".$invoice_id."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);
        return $fetch ;
    }

public static function findAllCustomer() {
        // Load XML file
        $xml_customer_list = simplexml_load_file("xml_customerList.xml");
    
        // Loop through each ROW element
        foreach ($xml_customer_list->ROW as $row) {
            // Check if the id attribute matches the given customerId
                // Return customer details as an associative array
                return [
                    'id' => (string)$row['id']
                ];
            
        }
        // Return null if no match is found
        return null;
    }



   public static function findCustomerDueById($customerId) {
        // Load XML file
    

$customerInfo  = SETUP::findAllCustomer();
$data = FIND::getAllCustomerDues('',$customerInfo, date("Y-m-d"));

        // Loop through each ROW element
        foreach ($data->ROW as $row) {
            // Check if the id attribute matches the given customerId
            if ((string)$row['id'] === (string)$customerId) {
                // Return customer details as an associative array
                return [
                    'code' => (string)$row['code'],
                    'shop_name' => (string)$row['shop_name'],
                    'customer_name' => (string)$row['customer_name'],
                    'address' => (string)$row['address'],
                    'mobile' => (string)$row['mobile'],
                    'division_name' => (string)$row['division_name'],
                    'district_name' => (string)$row['district_name'],
                    'upazila_name' => (string)$row['upazila_name'],
                    'union_name' => (string)$row['union_name']
                ];
            }
        }
        // Return null if no match is found
        return null;
    }

    public static function findCustomerById($customerId) {
        // Load XML file
        $xml_customer_list = simplexml_load_file("xml_customerList.xml");
    
        // Loop through each ROW element
        foreach ($xml_customer_list->ROW as $row) {
            // Check if the id attribute matches the given customerId
            if ((string)$row['id'] === (string)$customerId) {
                // Return customer details as an associative array
                return [
                    'code' => (string)$row['code'],
                    'shop_name' => (string)$row['shop_name'],
                    'customer_name' => (string)$row['customer_name'],
                    'address' => (string)$row['address'],
                    'mobile' => (string)$row['mobile'],
                    'division_name' => (string)$row['division_name'],
                    'district_name' => (string)$row['district_name'],
                    'upazila_name' => (string)$row['upazila_name'],
                    'union_name' => (string)$row['union_name'],
                    'creadit_limit' => (string)$row['creadit_limit']

                ];
            }
        }
        // Return null if no match is found
        return null;
    }

    
    public static function SETUP_CUSTOMER($ID)
    {
        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `setup_customer`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        $prefix = SETUP::SETUP_PREFIX('setup_customer');


        if($fetch['in_service'] == 'checked') {
        $in_service = 'checked';
        $check_value = 1;
        }else{ 
        $in_service = ''; 
        $check_value = 0;
        }



        if(!empty($fetch['division_id'])){
            $a = SETUP::SETUP_DIVISION($fetch['division_id']);
            $division = $a['division'];
        }else{
            $division = '';
        }


    
        if(!empty($fetch['district_id'])){
            $a = SETUP::SETUP_DISTRICT($fetch['district_id']);
            $district = $a['district'];

        }else{
            $district = '';
        }

        if(!empty($fetch['upazila_id'])){
            $a = SETUP::SETUP_UPAZILA($fetch['upazila_id']);
            $upazila = $a['upazila'];
        }else{
            $upazila = '';
        }

        if(!empty($fetch['union_id'])){
            $a = SETUP::SETUP_UNION($fetch['union_id']);
            $union = $a['union'];
        }else{
            $union = '';
        }


        
        return array(
            'id' => $fetch['id'],
            'customer_name' => $fetch['customer_name'],
            'customer_code' => $prefix['prefix'] . $fetch['code'],
            'shop_name' => $fetch['shop_name'],
            'division_id' => $fetch['division_id'],
            'division' => $division,
            'district_id' => $fetch['district_id'],
            'district' => $district,
            'upazila_id' => $fetch['upazila_id'],
            'in_service' => $in_service,
            'check_value' => $check_value,
            'code' => $fetch['code'],
            'upazila' => $upazila,
            'union_id' => $fetch['union_id'],
            'union' => $union,
            'address' => $fetch['address'],
            'mobile' => $fetch['mobile'],
            'customer_type' => $fetch['customer_type'],
            'sales_person_id' => $fetch['sales_person'],
            'creadit_limit' => $fetch['creadit_limit'],
            'email' => $fetch['email']


            );

    }





    public static function RAW_OPENINGSTOCK($ID)
    {
        $conn_me = Database::getInstance();

        $qry = $conn_me->prepare("SELECT * FROM `raw_opening_stock`  where `id` = '".$ID."' ");
        $qry->execute();
        $fetch_list = $qry->fetch(PDO::FETCH_ASSOC);

        return array(
            'id' => $fetch_list['id'],
            'code' => $fetch_list['code'],
            'invoice_no' => $fetch_list['invoice_no'],
            'product_id' => $fetch_list['product_id'],
            'quantity' => $fetch_list['quantity'],
            'warehouse_id' => $fetch_list['warehouse_id'],
            'invoice_date' => $fetch_list['invoice_date'],
            'notes' => $fetch_list['notes'],
            'status' => $fetch_list['status']



        );

    }




    public static function FG_OPENINGSTOCK($ID)
    {
        $conn_me = Database::getInstance();

        $qry = $conn_me->prepare("SELECT * FROM `fg_opening_stock`  where `id` = '".$ID."' ");
        $qry->execute();
        $fetch_list = $qry->fetch(PDO::FETCH_ASSOC);

        return array(
            'id' => $fetch_list['id'],
            'code' => $fetch_list['code'],
            'invoice_no' => $fetch_list['invoice_no'],
            'product_id' => $fetch_list['product_id'],
            'quantity' => $fetch_list['quantity'],
            'warehouse_id' => $fetch_list['warehouse_id'],
            'invoice_date' => $fetch_list['invoice_date'],
            'notes' => $fetch_list['notes'],
            'status' => $fetch_list['status']



        );

    }


    

    public static function FG_DAMAGE_STORE($ID)
    {
        $conn_me = Database::getInstance();

        $qry = $conn_me->prepare("SELECT * FROM `fg_damage_store`  where `id` = '".$ID."' ");
        $qry->execute();
        $fetch_list = $qry->fetch(PDO::FETCH_ASSOC);

        $info_product = SETUP::SETUP_PRODUCT($fetch_list['product_id']);
        $warehouse_info = SETUP::SETUP_WAREHOUSE($fetch_list['warehouse_id']);

           if( is_null($fetch_list['quantity']) || $fetch_list['quantity'] == 0 ){
            $in_carton = 0;
        }else{
            $in_carton = $fetch_list['quantity']/ ($info_product['pcs_in_cartoon'] ?? 0);

        }
        

        return array(
            'id' => $fetch_list['id'],
            'code' => $fetch_list['code'],
            'invoice_no' => $fetch_list['invoice_no'],
            'product_id' => $fetch_list['product_id'],
            'product_name' => $info_product['product_name'],
            'quantity' => $fetch_list['quantity'],
            'in_carton' => $in_carton,
            'warehouse_id' => $fetch_list['warehouse_id'],
            'warehouse_name' => $warehouse_info['name'],
            'invoice_date' => $fetch_list['invoice_date'],
            'dispatcher_id' => $fetch_list['dispatcher_id'],
            'notes' => $fetch_list['notes'],
            'status' => $fetch_list['status']



        );

    }



    
    public static function LOCAL_PURCHES($ID,$TABLE)
    {
        $conn_me = Database::getInstance();

        $qry = $conn_me->prepare("SELECT *,date_format(invoice_date, '%d-%m-%Y') AS `invoice_date` FROM `{$TABLE}`  where `id` = '".$ID."' ");
        $qry->execute();
        $fetch_list = $qry->fetch(PDO::FETCH_ASSOC);


        $info_supplier = SETUP::SETUP_SUPPLIER($fetch_list['supplier_id']);
        $user_info = SETUP::ADMIN_SETUP($fetch_list['poster']);
        $prefix = SETUP::SETUP_PREFIX($TABLE);

        
        return array(
            'id' => $fetch_list['id'],
            'code' => $prefix['prefix'] . $fetch_list['code'],
            'invoice_no' => $fetch_list['invoice_no'],
            'supplier_id' => $fetch_list['supplier_id'],
            'supplier_name' => $info_supplier['supplier_name'],
            'supplier_address' => $info_supplier['address'],
            'supplier_mobile' => $info_supplier['mobile'],
            'supplier_code' => $info_supplier['supplier_code'],
            'material_id' => $fetch_list['product_id'],
            'quantity' => $fetch_list['quantity'],
            'supplier_bill_no' => $fetch_list['supplier_bill_no'],
            'invoice_date' => $fetch_list['invoice_date'],
            'purches_price' => $fetch_list['purches_price'],
            'supplier_bill_date' => $fetch_list['supplier_bill_date'],
            'status' => $fetch_list['status'],
            'purchase_by' => $user_info['hr_name'],
            'note' => $fetch_list['note']




        );

    }



    
    public static function ADMIN_DATA_BY_EMPLOYEEID($ID)
    {
        $conn_me = Database::getInstance();

        $qry = $conn_me->prepare("SELECT * FROM `admin`  where `employee_id` = '".$ID."' ");
        $qry->execute();
        $fetch_list = $qry->fetch(PDO::FETCH_ASSOC);


        $hr_info = SETUP::SETUP_EMPLOYEEY($fetch_list['employee_id']);
        


        return array(
            'id' => $fetch_list['id'],
            'employee_id' => $fetch_list['employee_id'],
            'user_type' => $fetch_list['user_type'],
            'status' => $fetch_list['hr_status'],
            'employee_name' => $hr_info['name'],
            'username' => $fetch_list['username'],
            'brunch_id' => $fetch_list['brunch_id'],
            'dypricpt_pass' => $fetch_list['dypricpt_pass'],
            'designation' => $hr_info['designation_text'],
            'photo' => $hr_info['photo'],
            'hr_name' => $hr_info['name']
        );
    }



public static function ADMIN_SETUP($ID)
    {
        $conn_me = Database::getInstance();

        $qry = $conn_me->prepare("SELECT * FROM `admin`  where `id` = '".$ID."' ");
        $qry->execute();
        $fetch_list = $qry->fetch(PDO::FETCH_ASSOC);

        $hr_info = SETUP::SETUP_EMPLOYEEY($fetch_list['employee_id']);
        $hr_brunch = SETUP::SETUP_BRUNCH($fetch_list['brunch_id']);
        $employee_code_with_prefix = $hr_info['employee_prefix'] . $hr_info['code'];

        return array(
            'id' => $fetch_list['id'],
            'employee_id' => $fetch_list['employee_id'],
            'username' => $fetch_list['username'],
            'brunch_id' => $fetch_list['brunch_id'],
            'brunch_name' => $hr_brunch['brunch'],
            'brunch_address1' => $hr_brunch['address_line_one'],
            'brunch_address2' => $hr_brunch['address_line_two'],
            'dypricpt_pass' => $fetch_list['dypricpt_pass'],
            'employee_code_with_prefix' => $employee_code_with_prefix,
            'designation' => $hr_info['designation_text'],
            'designation_id' => $hr_info['designation'],
            'department_id' => $hr_info['present_department'],
            'photo' => $hr_info['photo'],
            'hr_name' => $hr_info['name'],
            'fa_name' => $hr_info['fa_name'],
            'mo_name' => $hr_info['mo_name'],
            'birth_date' => $hr_info['birth_date'],
            'mob_no' => $hr_info['mob_no'],
            'photo' => $hr_info['photo'],
            'email' => $hr_info['email']


        );

    }


  

    

    public static function SETUP_PREFIX($table_name)
    {
        $conn_me = Database::getInstance();

        $lc_fetch = $conn_me->prepare("SELECT * FROM `invoice_prefix`  where  `table_name` = '".$table_name."'  ");
        $lc_fetch->execute();
        $fetch_list = $lc_fetch->fetch(PDO::FETCH_ASSOC);

        return array(
            'prefix' => $fetch_list['prefix']

        );

    }


    public static function SETUP_CODE($table)
    {
        $conn_me = Database::getInstance();

        $lc_fetch = $conn_me->prepare("SELECT `code` FROM `{$table}`  where  `status` = 'Done'  ORDER BY `code` DESC ");
        $lc_fetch->execute();
        if ($lc_fetch->rowCount() > 0)
{
    $fetch_list = $lc_fetch->fetch(PDO::FETCH_ASSOC);
    $number =  $fetch_list['code'];
    $number++;
    $code =  str_pad($number, 4, "0", STR_PAD_LEFT);  //00002

    


}else{
    $code = '0001';
}
$REFIX = SETUP::SETUP_PREFIX($table);

$invoice_no = $REFIX['prefix']  . '-' .date("d-m-Y").'-' .$code;
$only_code = $REFIX['prefix'] . $code;


        return array(
            'code' => $code,
            'prefix' => $REFIX['prefix'],
            'invoice_no' => $invoice_no,
            'only_code' => $only_code


        );

    }


    public static function GENERATE_INVOICE($table,$date)
    {

      
        $conn_me = Database::getInstance();

        $lc_fetch = $conn_me->prepare("SELECT `code` FROM `{$table}`  where  `status` = 'Done'  ORDER BY `id` DESC ");
        $lc_fetch->execute();
        if ($lc_fetch->rowCount() > 0)
{
    $fetch_list = $lc_fetch->fetch(PDO::FETCH_ASSOC);
    $number =  $fetch_list['code'];
    $number++;
    $code =  str_pad($number, 4, "0", STR_PAD_LEFT);  //00002

    


}else{
    $code = '0001';
}

$REFIX = SETUP::SETUP_PREFIX($table);

$invoice_no = $REFIX['prefix']  . '-' .$date.'-' .$code;


    $query =$conn_me->exec("INSERT INTO `{$table}` 
    ( 
        `code`, `invoice_no`, `date`, `time`, `poster`, `lastupdate`
    ) 
    VALUES
    (
    
        '".$code."',
        '".$invoice_no."',
        '" . date("Y-m-d") . "',
        '" . date("h:i:s a") . "',
        '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
        '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
    
    
    ) ");
    

    $last_insert_id = $conn_me->lastInsertId();



        return array(
            'last_id' => $last_insert_id,
            'invoice_no' =>  $invoice_no,
            'related_code' => $code

        );

    }


    public static function SETUP_CODE_INSERT_DATA($table)
    {

        $conn_me = Database::getInstance();


        $pdo = $conn_me;

    $CODE = SETUP::SETUP_CODE($table);

    $query =$pdo->exec("INSERT INTO `{$table}` 
    ( 
        `code`, `invoice_no`, `date`, `time`, `poster`, `lastupdate`
    ) 
    VALUES
    (
    
    '".$CODE['code']."',
        '".$CODE['invoice_no']."',
    '" . date("Y-m-d") . "',
        '" . date("h:i:s a") . "',
        '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
        '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
    
    
    ) ");
    

    $last_insert_id = $pdo->lastInsertId();



        return array(
            'last_id' => $last_insert_id,
            'invoice_no' =>  $CODE['invoice_no'],
            'related_code' => $CODE['code']

        );

    }




     public static function SETUP_COMPANY($status)
    {
        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `setup_company`  WHERE `status` = '".$status."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);


        $header_content ='<div class="table-responsive"><table style="width:100%;" class="table datatable_simple">';

        $header_content .='<tr><th style="text-align:center"><img style="height:70px" src="upload/Company_Logo/'.$fetch['logo'].'"></th></tr>';
        $header_content .='<tr><td style="text-align:center"><b>'.$fetch['name'].'</b><br>Mobile '.$fetch['phone'].'<br>Email '.$fetch['address'].'<br>'.$fetch['address'].'</td></tr>';

        $header_content .='</table></div>';

        return array(
            'id' => $fetch['id'],
            'company_name' => $fetch['name'],
            'company_short_name' => $fetch['short_name'],
            'company_address' => $fetch['address'],
            'company_phone' => $fetch['phone'],
            'company_email' => $fetch['email'],
            'company_logo' => $fetch['logo'],
            'invoice_header' => $fetch['invoice_header'],
            'invoice_footer' => $fetch['invoice_footer'],
            'empty_invoice_header' => $fetch['empty_invoice_header'],
            'empty_invoice_footer' => $fetch['empty_invoice_footer'],
            'software_start_date' => $fetch['software_start_date'],
            'software_end_date' => $fetch['software_end_date'],
            'header_content' => $header_content,
            'lastupdate' => $fetch['lastupdate'],
            'time' => $fetch['time'],
            'status' => $fetch['status']
            );


    }


    public static function SETUP_EMPLOYEEY_BY_CODE($CODE)
    {
        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT *  FROM `setup_employee`  WHERE `code` = '".$CODE."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        $leave_info = FIND::LEAVE($fetch['id'],date('Y-m-d'));



        return array(
            'id' => $fetch['id'],
            'department_id' => $fetch['present_department'],
            'in_leave' => $leave_info['in_leave']
            );


    }


    public static function SETUP_EMPLOYEEY($id)
    {
        
        
        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT 
        A.*,
        B.designation AS designation_text, 
        C.department AS present_department_name, 
        date_format(A.join_d, '%d-%m-%Y') AS `join_d`,date_format(A.birth_date, '%d-%m-%Y') AS `birth_date` FROM `setup_employee` A  
        JOIN setup_designation B ON (A.designation = B.id )
        JOIN setup_department C ON (A.present_department = C.id )

        WHERE A.`id` = '".$id."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

       $photo  = (!empty($fetch['photo'])) ? $fetch['photo'] : 'no_image.jpg'; 
        return array(
            'id' => $fetch['id'],
            'code' => $fetch['code'],
            'employee_prefix' => 'E',
            'employee_code' => 'E' . $fetch['code'],
            'name' => $fetch['name'],
            'joining_department' => $fetch['joining_department'],
            'present_department' => $fetch['present_department'],
            'present_department_name' => $fetch['present_department_name'],
            'present_section' => $fetch['present_section'],
            'joining_section' => $fetch['joining_section'],
            'designation' => $fetch['designation'],
            'designation_text' => $fetch['designation_text'],
            'joining_designation' => $fetch['joining_designation'],
            'fa_name' => $fetch['fa_name'],
            'mo_name' => $fetch['mo_name'],
            'birth_date' => $fetch['birth_date'],
            'mob_no' => $fetch['mob_no'],
            'nationality' => $fetch['nationality'],
            'division_id' => $fetch['division_id'],
            'district_id' => $fetch['district_id'],
            'upazila_id' => $fetch['upazila_id'],
            'union_id' => $fetch['union_id'],
            'village' => $fetch['village'],
            'po_office' => $fetch['po_office'],
            'house' => $fetch['house'],
            'nid' => $fetch['nid'],
            'religion' => $fetch['religion'],
            'email' => $fetch['email'],
            'edu_qul' => $fetch['edu_qul'],
            'previous_company' => $fetch['previous_company'],
            'joining_salary' => $fetch['joining_salary'],
            'present_salary' => $fetch['present_salary'],
            'house_rent' => $fetch['house_rent'],
            'medical' => $fetch['medical'],
            'mob_bill' => $fetch['mob_bill'],
            'da' => $fetch['da'],
            'ta' => $fetch['ta'],
            'provident_fund' => $fetch['provident_fund'],
            'basic' => $fetch['basic'],
            'over_time_bill' => $fetch['over_time_bill'],
            'other_allowance' => $fetch['other_allowance'],
            'join_d' => $fetch['join_d'],
            'status' => $fetch['status'],
            'gender' => $fetch['gender'],
            'matrial_status' => $fetch['matrial_status'],
            'referrer' => $fetch['referrer'],
            'supervisor' => $fetch['supervisor'],
            'nominee_information' => $fetch['nominee_information'],
            'bank_account' => $fetch['bank_account'],
            'photo' => $photo
            );


    }

    public static function SETUP_BRUNCH($ID)
    {
        $conn_me = Database::getInstance();


        $query = $conn_me->prepare("SELECT * FROM `setup_brunch`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        return array(
            'id' => $fetch['id'],
            'brunch' => $fetch['brunch'],
            'address_line_one' => $fetch['address_line_one'],
            'address_line_two' => $fetch['address_line_two'],
            'phone' => $fetch['phone'],
            'image' => $fetch['image'],
            'rerlated_warehouse' => $fetch['related_warehouse'],

            );

    }




    public static function getWarehouseIds($brunchId) {

        $conn_me = Database::getInstance();

        $qry = $conn_me->prepare("SELECT related_warehouse FROM setup_brunch WHERE id = :brunchId");
        $qry->bindParam(':brunchId', $brunchId, PDO::PARAM_INT);
        $qry->execute();
        $result = $qry->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $relatedWarehouse = json_decode($result['related_warehouse'], true);
            if (is_array($relatedWarehouse)) {
                return implode(',', array_map('intval', $relatedWarehouse));
            }
        }


        
        return '';
    }



    public static function SETUP_DEPARTMENT($ID)
    {

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `setup_department`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        return array(
            'id' => $fetch['id'],
            'department' => $fetch['department']

            );

    }

    public static function SETUP_TIMETABLE()
    {

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT *,DATE_ADD(`on_duty_time`, INTERVAL `late_time` MINUTE) as `new_time` FROM `setup_timetable`;  ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);



        return array(
            'id' => $fetch['id'],
            'on_duty_time' => $fetch['on_duty_time'],
            'off_duty_time' => $fetch['off_duty_time'],
            'late_time' => $fetch['late_time'],
            'leave_early' => $fetch['leave_early'],
            'brunch' => $fetch['brunch'],
            'poster' => $fetch['poster'],
            'count_late_time' =>  $fetch['new_time']

            );

    }



    public static function SETUP_FACTORY($ID)
    {

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `setup_factory`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        return array(
            'id' => $fetch['id'],
            'factory_name' => $fetch['factory_name'],
            'address' => $fetch['address'],
            'mobile' => $fetch['mobile'],
            'owner_name' => $fetch['owner_name'],
            'email' => $fetch['email']

            );

    }

    public static function SETUP_FACTORY_BY_NAME($NAME)
    {
        $conn_me = Database::getInstance();


        $query = $conn_me->prepare("SELECT * FROM `setup_factory`  WHERE `factory_name` = '".$NAME."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        return array(
            'id' => $fetch['id'],
            'factory_name' => $fetch['factory_name'],
            'address' => $fetch['address'],
            'mobile' => $fetch['mobile'],
            'owner_name' => $fetch['owner_name'],
            'email' => $fetch['email']

            );

    }


    public static function SETUP_SUPPLIER_BY_NAME($NAME)
    {

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `setup_supplier`  WHERE `supplier_name` = '".$NAME."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        return array(
            'id' => $fetch['id'],
            'supplier_name' => $fetch['supplier_name'],
            'address' => $fetch['address'],
            'mobile' => $fetch['mobile'],
            'owner_name' => $fetch['owner_name'],
            'email' => $fetch['email']

            );

    }

    public static function SETUP_SUPPLIER($ID)
    {

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `setup_supplier`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        $prefix_info = SETUP::SETUP_PREFIX('setup_supplier');

        return array(
            'id' => $fetch['id'],
            'supplier_code' => $prefix_info['prefix'] . $fetch['code'],
            'supplier_name' => $fetch['supplier_name'],
            'description' => $fetch['description'],
            'address' => $fetch['address'],
            'mobile' => $fetch['mobile'],
            'owner_name' => $fetch['owner_name'],
            'supplier_due' => 0.00,
            'total_paid' => 0.00,
            'total_invoice_price' => 0.00,
            'total_return' => 0.00,
            'email' => $fetch['email']

            );

    }

    public static function SETUP_PRODUCT($ID)
    {

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT A.*,B.`unit`,C.`category`
        FROM `setup_product` A  
        LEFT JOIN `setup_unit` B ON (A.`unit_id` = B.`id`)
        LEFT JOIN `setup_category` C ON (A.`category_id` = C.`id`)

        WHERE A.`id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);
        
        if($fetch['in_service'] == 'checked') {
            $in_service = 'checked';
            $check_value = 1;
        }else{ 
            $in_service = ''; 
            $check_value = 0;
        }

         $vat =   $fetch['vat_percentage']/100;
         $discount =   $fetch['discount']/100;

        return array(
            'id' => $fetch['id'],
            'product_name' => $fetch['product_name'],
            'product_code' =>  "P$fetch[code]",
            'vat_percentage' => $fetch['vat_percentage'],
            'vat' => $vat,
            'discount_percentage' => $fetch['discount'],
            'discounnt' => $discount,
            'category_id' => $fetch['category_id'],
            'category' => $fetch['category'],
            'unit_id' => $fetch['unit_id'],
            'unit' => $fetch['unit'],
            'code' => $fetch['code'],
            'sales_rate' => $fetch['sales_rate'],
            'wholesale_rate' => $fetch['wholesale_rate'],
            'pcs_in_cartoon' => $fetch['pcs_in_cartoon'],
            'safty_stock' => $fetch['safty_stock'],
            'in_service' => $in_service,
            'check_value' => $check_value



            );


         


    }

    public static function SETUP_PRODUCT_BY_NAME($NAME)
    {

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `setup_product`  WHERE `product_name` = '".$NAME."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);
        
        if($fetch['in_service'] == 'true') {$in_service = 'checked';}else{ $in_service = ''; }


         $category_info = SETUP::SETUP_CATEGORY($fetch['category_id']);
         $unit_info = SETUP::SETUP_UNIT($fetch['unit_id']);

        return array(
            'id' => $fetch['id'],
            'product_name' => $fetch['product_name'],
            'category_id' => $fetch['category_id'],
            'category' => $category_info['category'],
            'unit_id' => $fetch['unit_id'],
            'unit' => $unit_info['unit'],
            'code' => $fetch['code'],
            'sales_rate' => $fetch['sales_rate'],
            'wholesale_rate' => $fetch['wholesale_rate'],
            'pcs_in_cartoon' => $fetch['pcs_in_cartoon'],
            'in_service' => $in_service


            );


         


    }

    
    public static function SETUP_RAW_MATERIAL($ID)
    {

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `setup_raw_material`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);
        


         $category_info = SETUP::SETUP_RAW_MATERIAL_CATEGORY($fetch['category_id']);
         $unit_info = SETUP::SETUP_UNIT($fetch['unit_id']);
         $prefix_info = SETUP::SETUP_PREFIX('setup_raw_material');

        return array(
            'id' => $fetch['id'],
            'material_code' => $prefix_info['prefix'] . $fetch['code'],
            'product_name' => $fetch['material_name'],
            'category_id' => $fetch['category_id'],
            'category' => $category_info['category'],
            'unit_id' => $fetch['unit_id'],
            'weight' => $fetch['weight'],
            'unit' => $unit_info['unit'],
            'supporting_product' => $fetch['supporting_product'],
            'mold_product' => $fetch['mold_product'],
            'spray_product' => $fetch['spray_product'],
            'print_product' => $fetch['print_product'],
            'minimum_stock_qty' => $fetch['minimum_stock_qty'],
            'code' => $fetch['code'],
            'pcs_in_cartoon' => $fetch['pcs_in_cartoon']


            );


         


    }



    public static function SETUP_NOTIFICATION($ID)
    {

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `notice_bord`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        if($fetch['status'] == 'true') {$check = 'checked';}else{ $check = ''; }
        return array(
            'id' => $fetch['id'],
            'notification' => $fetch['notice_text'],
            'check' => $check
            );

    }


    
    public static function SETUP_SECTION($ID)
    {

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `setup_section`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        return array(
            'id' => $fetch['id'],
            'section' => $fetch['section']

            );

    }

    public static function SETUP_DESIGNATION($ID)
    {

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `setup_designation`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        return array(
            'id' => $fetch['id'],
            'designation' => $fetch['designation']

            );

    }

    


    public static function getProductPriceOnTransferDate($invoice_date, $product_id) {

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT price FROM history_change_product_price_vat WHERE product_id = '".$product_id."' AND date <=  '".$invoice_date."' ORDER BY date DESC,id DESC LIMIT 1");
        $query->execute();
        $fetch_data = $query->fetch(PDO::FETCH_ASSOC);

         $price =  (!empty($fetch_data['price'])) ? $fetch_data['price'] : 0 ;

        

        return $price;

}


    public static function FG_warehouse_to_warehouse_transfer($ID)
    {
       
        $conn_me = Database::getInstance();


        $query = $conn_me->prepare("SELECT * FROM `fg_warehouse_to_warehouse_transfer`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        $info_product = SETUP::SETUP_PRODUCT($fetch['product_id']);
        $to_warehouse_info = SETUP::SETUP_WAREHOUSE($fetch['TO_warehouse_id']);
        $from_warehouse_info = SETUP::SETUP_WAREHOUSE($fetch['FROM_warehouse_id']);



        $in_carton = (is_null($fetch['quantity']) || $fetch['quantity'] == 0) ? 0.00 :  number_format((float)($fetch['quantity']/$info_product['pcs_in_cartoon']), 2, '.', '') ;


        return array(
                'id' => $fetch['id'],
                'product_id' => $fetch['product_id'],
                'quantity' => $fetch['quantity'],
                'in_carton' => $in_carton,
                'invoice_no' => $fetch['invoice_no'],
                'code' => $fetch['code'],
                'from_warehouse_id' => $fetch['FROM_warehouse_id'],
                'to_warehouse_id' => $fetch['TO_warehouse_id'],
                'invoice_date' => $fetch['invoice_date'],
                'notes' => $fetch['notes'],
                'poster' => $fetch['poster'],
                'status' => $fetch['status'],
                'approve_data' => $fetch['approve_data'],
                'approve_by' =>  $fetch['approve_by'],
                'approve_date' =>  $fetch['approve_date'],
                'product_name' => $info_product['product_name'],
                'product_category' => $info_product['category'],
                'product_price' => $info_product['sales_rate'],
                'from_warehouse_name' => $from_warehouse_info['name'],
                'to_warehouse_name' => $to_warehouse_info['name'],
                'dispatcher_id' => $fetch['dispatcher_id']



            );

    }


    
    public static function RAW_warehouse_to_warehouse_transfer($ID)
    {
       $conn_me = Database::getInstance();


        $query = $conn_me->prepare("SELECT * FROM `raw_warehouse_to_warehouse_transfer`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        $info_product = SETUP::SETUP_RAW_MATERIAL($fetch['product_id']);
        $to_warehouse_info = SETUP::SETUP_WAREHOUSE($fetch['TO_warehouse_id']);
        $from_warehouse_info = SETUP::SETUP_WAREHOUSE($fetch['FROM_warehouse_id']);


        return array(
                'id' => $fetch['id'],
                'product_id' => $fetch['product_id'],
                'quantity' => $fetch['quantity'],
                'invoice_no' => $fetch['invoice_no'],
                'from_warehouse_id' => $fetch['FROM_warehouse_id'],
                'to_warehouse_id' => $fetch['TO_warehouse_id'],
                'invoice_date' => $fetch['invoice_date'],
                'notes' => $fetch['notes'],
                'status' => $fetch['status'],
                'approve_data' => $fetch['approve_data'],
                'approve_by' =>  $fetch['approve_by'],
                'approve_date' =>  $fetch['approve_date'],
                'product_name' => $info_product['product_name'],
                'from_warehouse_name' => $from_warehouse_info['name'],
                'to_warehouse_name' => $to_warehouse_info['name']


            );

    }

    public static function SETUP_WAREHOUSE($ID)
    {

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `setup_warehouse`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

     //  $capacity =  number_format(($fetch['height']*$fetch['width']*$fetch['length']),2,".",".");
     $capacity = 0 ;

        return array(
            'id' => $fetch['id'],
            'name' => $fetch['name'],
            'address' => $fetch['address'],
            'phone' => $fetch['phone'],
            'height' => $fetch['height'],
            'width' => $fetch['width'],
            'length' => $fetch['length'],
            'capacity' => $capacity

            );

    }

    public static function MACHINE_LOCATION()
    {

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `setup_machine_location`  ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        return array(
            'file_path' => $fetch['file_path']

            );

    }



    public static function SETUP_UNIT($ID)
    {

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `setup_unit`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        return array(
            'id' => $fetch['id'],
            'unit' => $fetch['unit']

            );

    }

    public static function SETUP_DISTRICT($ID)
    {

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `districts`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        return array(
            'id' => $fetch['id'],
            'district' => $fetch['name']

            );

    }


    public static function SETUP_LEAVE_DEFINE($ID)
    {

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT *  FROM `setup_leave_define`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        $info_employee = SETUP::SETUP_EMPLOYEEY($fetch['employee_id']);

        return array(
            'id' => $fetch['id'],
            'employee_id' => $fetch['employee_id'],
            'leave_type_id' => $fetch['leave_type_id'],
            'number_of_days' => $fetch['number_of_days'],
            'employee_name' => $info_employee['name']
            );

    }




    public static function SETUP_HOLIDAY($ID)
    {

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * , date_format(holiday, '%m/%d/%Y') AS `holiday` FROM `setup_holiday`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        return array(
            'id' => $fetch['id'],
            'holiday' => $fetch['holiday'],
            'description' => $fetch['description']
            );

    }




    public static function SETUP_TRANSPORT_COST($ID)
    {

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `tansport_cost`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        $info_dis = SETUP::SETUP_DISTRICT($fetch['district_id']);
        return array(
            'id' => $fetch['id'],
            'district_id' => $fetch['district_id'],
            'district_name' => $info_dis['district'],
            'nogot_cost' => $fetch['nogot_cost'],
            'vaki_cost' => $fetch['vaki_cost']


            );

    }




    
    public static function SETUP_DIVISION($ID)
    {

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `divisions`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        return array(
            'id' => $fetch['id'],
            'division' => $fetch['name']

            );

    }

    public static function SETUP_UPAZILA($ID)
    {

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `upazilas`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        return array(
            'id' => $fetch['id'],
            'upazila' => $fetch['name']

            );

    }


    public static function SETUP_UNION($ID)
    {

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `unions`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        return array(
            'id' => $fetch['id'],
            'union' => $fetch['name']

            );

    }


    public static function SETUP_CATEGORY($ID)
    {

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `setup_category`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        return array(
            'id' => $fetch['id'],
            'category' => $fetch['category']

            );

    }
    
    
    public static function SETUP_RAW_MATERIAL_CATEGORY($ID)
    {
        $conn_me = Database::getInstance();


        $query = $conn_me->prepare("SELECT * FROM `setup_raw_material_category`  WHERE `id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        return array(
            'id' => $fetch['id'],
            'category' => $fetch['category']

            );

    }





}


class TEST_BOOK {






    static function calculateOpeningBalance($DATE, $BRUNCH)
    {
        $conn_me = Database::getInstance();
    
        if ($BRUNCH == 'All') {
            $BRUNCH_QUERY = "";
        } else {
            $BRUNCH_QUERY = " AND  `brunch_id` = :brunch ";
        }
    
        $query = $conn_me->prepare("SELECT SUM(`in_amount`) - SUM(`out_amount`)  AS `balance` 
                                     FROM `account_transection`  
                                     WHERE `transection_date` < :date 
                                     AND `transection_by` = 'Cash' and transection_by_id = 0  $BRUNCH_QUERY ");
        
        $query->bindParam(':date', $DATE);
    
        if ($BRUNCH != 'All') {
            $query->bindParam(':brunch', $BRUNCH);
        }
    
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);
    
        return $fetch['balance'];
    }
    
    
    static function calculateOpeningBalanceForBankAccounts($DATE){
    
    
        $conn_me = Database::getInstance();
        $content = '<table class="table">';
    $total_balance = 0 ; 
        $query = $conn_me->prepare("SELECT b.bank_name, b.account_number, bt.transection_by_id, SUM(bt.in_amount) - SUM(bt.out_amount) as opening_balance  
                                    FROM account_transection bt 
                                    JOIN setup_bank b ON (bt.transection_by_id = b.id )  
                                    WHERE bt.transection_date < :date AND bt.transection_by = 'Bank' AND bt.transection_by_id <> 0   
                                    GROUP BY transection_by_id");
        $query->bindParam(':date', $DATE);
        $query->execute();
    
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($fetch_list)) {
           
            foreach ($fetch_list as $fetch) {
    
                $total_balance += $fetch['opening_balance'] ; 
                $content .= '<tr>';
                $content .= '<td>' . $fetch['bank_name'] . ' ' . $fetch['account_number'] . '</td>';
                $content .= '<td>' . number_format((float)$fetch['opening_balance'], 2, '.', '') . '</td>';
                $content .= '</tr>';
            }
        } else {
            $content .= '<tr>';
            $content .= '<td colspan="2">No bank accounts found for the selected date.</td>';
            $content .= '</tr>';
        }
    
        $content .= '</table>';
    
         return array(
            'total_balance' => $total_balance,
            'content' => $content
        );
    }
    
    
    static function calculateOpeningBalanceForIndividualMobileAccounts($DATE){
    
     $conn_me = Database::getInstance();
    
    $total_balance = 0 ;
        $content = '<table class="table">';
    $query = $conn_me->prepare("SELECT b.mobile_bank_name,b.mobile_number,bt.transection_by_id, SUM(bt.in_amount) - SUM(bt.out_amount) as opening_balance  FROM account_transection bt JOIN setup_mobile_banking b ON (bt.transection_by_id = b.id )  WHERE bt.transection_date < '".$DATE."' AND bt.transection_by = 'Mobile-Banking' AND bt.transection_by_id <> 0  group by transection_by_id");
    $query->execute();
    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch) { 
     $total_balance += $fetch['opening_balance'] ; 
            $content .= '<tr>';
            $content .= '<td >'.$fetch['mobile_bank_name'].' '.$fetch['mobile_number'].'</td>';
            $content .= '<td>'.number_format((float)$fetch['opening_balance'], 2, '.', '').'</td>';
            $content .= '</tr>';
       
    }
    
        $content .= '</table>';
       return array(
            'total_balance' => $total_balance,
            'content' => $content
        );
    }
    
    
    
    
    static function calculateSectionWiseTransection($DATE, $transection_type, $data_inserted_from, $transection_by, $BRUNCH)
    {
        $conn_me = Database::getInstance();
        $field = $transection_type == 'INCOME' ? 'in_amount' : 'out_amount';
        $total_balance = 0;
    
          if($transection_by == 'Cash' ){
              $transection_by_id_query = " AND transection_by_id = 0 ";
          }else{
              $transection_by_id_query = " AND transection_by_id <> 0 ";
          }
    
        $content = '<table class="table">';
    
        $query = $conn_me->prepare("SELECT `{$field}` as `total`,`id` FROM `account_transection` WHERE  `transection_date` =  '".$DATE."'  AND `transection_type` = '".$transection_type."'  AND `transection_by` = '".$transection_by."' $transection_by_id_query AND   `data_inserted_from` = '".$data_inserted_from."' AND  `brunch_id` = '".$BRUNCH."'  ");
        $query->execute();
        $fetch = $query->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($fetch as $fetch2) {
            $total_balance += !empty($fetch2['total']) ? $fetch2['total'] : 0.00;
           
            $tr_info = SETUP::ACCOUNT_TRANSECTION($fetch2['id']);
    
           
    
            $content .= '<tr>';
             if($tr_info['transection_id'] > 0 ){
                $tr_info33 =  SETUP::ACCOUNT_TRANSECTION($tr_info['transection_id']); 
                $content.= '<td style="padding-left:45px;">Fund Received From '.$tr_info33['brunch_name'].'</td>';
          }else{
            $content .= '<td>'.$tr_info['detaails'].''.$tr_info['only_name'].'</td>';
          }
            $content .= '<td>'.number_format((float)$tr_info[$field], 2, '.', '').'</td>';
            $content .= '</tr>';
    
        }
    
        $content .= '</table>';
    
        return array(
            'total_balance' => $total_balance,
            'all_ids' => $content
        );
    }
    
    
    
    static function calculateTotalForDate($DATE,$BANK_ID){
    
     $conn_me = Database::getInstance();
    
    
    $content = '';
    $query = $conn_me->prepare("SELECT SUM(in_amount - out_amount) as Total  FROM account_transection WHERE transection_date <= '".$DATE."' AND transection_by = 'Bank' and transection_by_id = '".$BANK_ID."' ") ;
    $query->execute();
    $fetch_list = $query->fetch(PDO::FETCH_ASSOC);
    
    
    
        return $fetch_list['Total'];
    }
    
    
    
    static function calculateClosingBalanceForBankAccounts($DATE){
    
     $conn_me = Database::getInstance();
    
    $total_balance = 0 ;
    $content = '';
    $query = $conn_me->prepare("SELECT * from setup_bank  ");
    $query->execute();
    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch) { 
    
    
    $total = self::calculateTotalForDate($DATE,$fetch['id']);
    
    
            $content .= '<tr>';
            $content .= '<td style="padding-left:45px;">'.$fetch['bank_name'].' '.$fetch['account_number'].'</td>';
            $content .= '<td>'.number_format((float)$total, 2, '.', '').'</td>';
            $content .= '</tr>';
       $total_balance += $total; 
    }
    
      $content .= '<tr>';
            $content .= '<td style="padding-left:45px;color:red">Total (All Brunch)</td>';
            $content .= '<td style="color:red">'.number_format((float) $total_balance, 2, '.', '').'</td>';
            $content .= '</tr>';
        return array(
            'content' => $content
        );
    }
    
    
    
    
    
    static function calculateClosingBalanceForIndividualMobileAccounts($DATE){
    
    
     $conn_me = Database::getInstance();
    
    $content = '';
    
    
    $total_balance = 0 ; 
    
    
    
    
    
    
    
    $query = $conn_me->prepare("SELECT B.mobile_bank_name,B.mobile_number,A.transection_by_id, sum(A.in_amount) - sum(A.out_amount) as balance from account_transection A 
        JOIN setup_mobile_banking B on (A.transection_by_id = B.id)
        where A.transection_by = 'Mobile-Banking' and A.transection_date <= '".$DATE."' AND A.transection_by_id <> 0
    GROUP BY A.transection_by_id 
     ");
    $query->execute();
    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch) { 
            $total_balance += $fetch['balance'] ; 
    
            $content .= '<tr>';
            $content .= '<td style="padding-left:45px;">'.$fetch['mobile_bank_name'].' '.$fetch['mobile_number'].'</td>';
            $content .= '<td>'.number_format((float) $fetch['balance'], 2, '.', '').'</td>';
            $content .= '</tr>';
       
    }
    
            $content .= '<tr>';
            $content .= '<td style="padding-left:45px;color:red">Total (All Brunch)</td>';
            $content .= '<td>'.number_format((float) $total_balance, 2, '.', '').'</td>';
            $content .= '</tr>';
    
        $content .= '</table>';
       return array(
            'total_balance' => $total_balance,
            'content' => $content
        );
    }
    
    
    
    
    
    
    public static function DAY_BOOK_NOWABPUR_AND_PATUWATULI_BRUNCH($DATE,$BRUNCH_ID)
        {
            $conn_me = Database::getInstance();
          
          $info_brunch = SETUP::SETUP_BRUNCH($BRUNCH_ID);
        
          
            $content = '<div class="row">';
            $content .= '<div class="col-sm-6">';
            $content .= '<table class="table">';
        
            $content .= '<tr style="background-color: orange">';
            $content .= '<th>Description</th>';
            $content .= '<th>Amount</th>';
            $content .= '</tr>';
        
        
            $content .= '<tr>';
            $content .= '<th colspan="2">#  Opening Balance </th>';
            $content .= '</tr>';
        
            $opening_cash = self::calculateOpeningBalance($DATE,$BRUNCH_ID);
        
        
            $content .= '<tr>';
        
            $content .= '<th colspan="2"><div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-money"></span> Cash  :: '.$info_brunch['brunch'].'</h3>                                
            <ul class="panel-controls">
            <li>' . $opening_cash . ' </li>
            </ul>                                   
            </div>
           
            </div></th>';
            $content .= '</tr>';
        
    
        
    
          $content .= '<tr>';
          $content .= '<th colspan="2">#  Invoice Wise Payment </th>';
          $content .= '</tr>';
        
          $cash_sales_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'Invoice Wise Payment','Cash',$BRUNCH_ID);
        
        if($cash_sales_data['total_balance'] == 0 ){ 
          $total_cash_sale = 0 ;
        
        }else{
        
              $total_cash_sale =number_format((float)$cash_sales_data['total_balance'], 2, '.', '');
        
            $content .= '<tr>';
        
            $content .= '<th colspan="2"><div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-money"></span> Cash  </h3>                                
            <ul class="panel-controls">
            <li>' . $total_cash_sale . ' </li>
            <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
            </ul>                                   
            </div>
            <div class="panel-body">'.$cash_sales_data['all_ids'].'</div>
            </div></th>';
        $content .= '</tr>';
        }
        
        
        $bank_sales_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'Invoice Wise Payment','Bank',$BRUNCH_ID);
        
        if($bank_sales_data['total_balance'] == 0 ){ 
         
          $total_bank_sale = 0 ;
        }else{
        
              $total_bank_sale =number_format((float)$bank_sales_data['total_balance'], 2, '.', '');
        
            $content .= '<tr>';
        
            $content .= '<th colspan="2"><div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-money"></span> Bank </h3>                                
            <ul class="panel-controls">
            <li>' . $total_bank_sale . ' </li>
            <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
            </ul>                                   
            </div>
            <div class="panel-body">'.$bank_sales_data['all_ids'].'</div>
            </div></th>';
        $content .= '</tr>';
        }
        
        
        
        
        $mobile_sales_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'Invoice Wise Payment','Mobile-Banking',$BRUNCH_ID);
        
        if($mobile_sales_data['total_balance'] == 0 ){ 
         
          $total_mobile_sale = 0 ;
        }else{
        
              $total_mobile_sale =number_format((float)$mobile_sales_data['total_balance'], 2, '.', '');
        
            $content .= '<tr>';
        
            $content .= '<th colspan="2"><div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-money"></span> Mobile </h3>                                
            <ul class="panel-controls">
            <li>' . $total_mobile_sale . ' </li>
            <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
            </ul>                                   
            </div>
            <div class="panel-body">'.$mobile_sales_data['all_ids'].'</div>
            </div></th>';
        $content .= '</tr>';
        }
        
        $content .= '<tr>';
        $content .= '<th colspan="2"># Customer Transaction</th>';
        $content .= '</tr>';
        
        $cash_customer_tr_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'CUSTOMER-TRANSACTION', 'Cash', $BRUNCH_ID);
        
        if ($cash_customer_tr_data['total_balance'] == 0) {
            $total_cash_customer_tr = 0;
        } else {
            $total_cash_customer_tr = number_format((float)$cash_customer_tr_data['total_balance'], 2, '.', '');
            $content .= '<tr>';
            $content .= '<th colspan="2">';
            $content .= '<div class="panel panel-default">';
            $content .= '<div class="panel-heading">';
            $content .= '<h3 class="panel-title"><span class="fa fa-money"></span> Cash  </h3>';
            $content .= '<ul class="panel-controls">';
            $content .= '<li>' . $total_cash_customer_tr . ' </li>';
            $content .= '<li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>';
            $content .= '</ul>';
            $content .= '</div>';
            $content .= '<div class="panel-body">'.$cash_customer_tr_data['all_ids'].'</div>';
            $content .= '</div>';
            $content .= '</th>';
            $content .= '</tr>';
        }
        
        
        
        
        
        
        $bank_customer_tr_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'CUSTOMER-TRANSACTION','Bank',$BRUNCH_ID);
        
        if($bank_customer_tr_data['total_balance'] == 0 ){ 
         
          $total_bank_customer_tr = 0 ;
        }else{
        
              $total_bank_customer_tr =number_format((float)$bank_customer_tr_data['total_balance'], 2, '.', '');
        
            $content .= '<tr>';
        
            $content .= '<th colspan="2"><div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-money"></span> Bank </h3>                                
            <ul class="panel-controls">
            <li>' . $total_bank_customer_tr . ' </li>
            <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
            </ul>                                   
            </div>
            <div class="panel-body">'.$bank_customer_tr_data['all_ids'].'</div>
            </div></th>';
        $content .= '</tr>';
        }
        
        
        
        
        
        
        $mobile_customer_tr_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'CUSTOMER-TRANSACTION','Mobile-Banking',$BRUNCH_ID);
        
        if($mobile_customer_tr_data['total_balance'] == 0 ){ 
         
          $total_mobile_customer_tr = 0 ;
        }else{
        
              $total_mobile_customer_tr =number_format((float)$mobile_customer_tr_data['total_balance'], 2, '.', '');
        
            $content .= '<tr>';
        
            $content .= '<th colspan="2"><div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-money"></span> Mobile </h3>                                
            <ul class="panel-controls">
            <li>' . $total_mobile_customer_tr . ' </li>
            <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
            </ul>                                   
            </div>
            <div class="panel-body">'.$mobile_customer_tr_data['all_ids'].'</div>
            </div></th>';
        $content .= '</tr>';
        }
        
        
        
        
        $content .= '<tr>';
        $content .= '<th colspan="2"># Add Income </th>';
        $content .= '</tr>';
        
        $cash_add_income_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'ADD INCOME', 'Cash', $BRUNCH_ID);
        
        if ($cash_add_income_data['total_balance'] == 0) {
            $total_cash_add_income = 0;
        } else {
            $total_cash_add_income = number_format((float)$cash_add_income_data['total_balance'], 2, '.', '');
            $content .= '<tr>';
            $content .= '<th colspan="2">';
            $content .= '<div class="panel panel-default">';
            $content .= '<div class="panel-heading">';
            $content .= '<h3 class="panel-title"><span class="fa fa-money"></span> Cash  </h3>';
            $content .= '<ul class="panel-controls">';
            $content .= '<li>' . $total_cash_add_income . ' </li>';
            $content .= '<li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>';
            $content .= '</ul>';
            $content .= '</div>';
            $content .= '<div class="panel-body">'.$cash_add_income_data['all_ids'].'</div>';
            $content .= '</div>';
            $content .= '</th>';
            $content .= '</tr>';
        }
        
        
        
        
        
        $bank_add_income_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'ADD INCOME','Bank',$BRUNCH_ID);
        
        if($bank_add_income_data['total_balance'] == 0 ){ 
         
          $total_bank_add_income = 0 ;
        }else{
        
              $total_bank_add_income =number_format((float)$bank_add_income_data['total_balance'], 2, '.', '');
        
            $content .= '<tr>';
        
            $content .= '<th colspan="2"><div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-money"></span> Bank </h3>                                
            <ul class="panel-controls">
            <li>' . $total_bank_add_income . ' </li>
            <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
            </ul>                                   
            </div>
            <div class="panel-body">'.$bank_add_income_data['all_ids'].'</div>
            </div></th>';
        $content .= '</tr>';
        }
        
        
        $mobile_add_income_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'ADD INCOME','Mobile-Banking',$BRUNCH_ID);
        
        if($mobile_add_income_data['total_balance'] == 0 ){ 
         
          $total_mobile_add_income = 0 ;
        }else{
        
              $total_mobile_add_income =number_format((float)$mobile_add_income_data['total_balance'], 2, '.', '');
        
            $content .= '<tr>';
        
            $content .= '<th colspan="2"><div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-money"></span> Mobile </h3>                                
            <ul class="panel-controls">
            <li>' . $total_mobile_add_income . ' </li>
            <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
            </ul>                                   
            </div>
            <div class="panel-body">'.$mobile_add_income_data['all_ids'].'</div>
            </div></th>';
        $content .= '</tr>';
        }
        
        
        
        $content .= '<tr>';
        $content .= '<th colspan="2"># Initial Balance </th>';
        $content .= '</tr>';
        
        $cash_initial_balance_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'CLOSING-BALANCE', 'Cash', $BRUNCH_ID);
        
        if ($cash_initial_balance_data['total_balance'] == 0) {
            $total_cash_initial_balance = 0;
        } else {
            $total_cash_initial_balance = number_format((float)$cash_initial_balance_data['total_balance'], 2, '.', '');
            $content .= '<tr>';
            $content .= '<th colspan="2">';
            $content .= '<div class="panel panel-default">';
            $content .= '<div class="panel-heading">';
            $content .= '<h3 class="panel-title"><span class="fa fa-money"></span> Cash  </h3>';
            $content .= '<ul class="panel-controls">';
            $content .= '<li>' . $total_cash_initial_balance . ' </li>';
            $content .= '<li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>';
            $content .= '</ul>';
            $content .= '</div>';
            $content .= '<div class="panel-body">'.$cash_initial_balance_data['all_ids'].'</div>';
            $content .= '</div>';
            $content .= '</th>';
            $content .= '</tr>';
        }
        
        
        
        
        
        $bank_initial_balance_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'CLOSING-BALANCE','Bank',$BRUNCH_ID);
        
        if($bank_initial_balance_data['total_balance'] == 0 ){ 
         
          $total_bank_initial_balance = 0 ;
        }else{
        
              $total_bank_initial_balance =number_format((float)$bank_initial_balance_data['total_balance'], 2, '.', '');
        
            $content .= '<tr>';
        
            $content .= '<th colspan="2"><div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-money"></span> Bank </h3>                                
            <ul class="panel-controls">
            <li>' . $total_bank_initial_balance . ' </li>
            <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
            </ul>                                   
            </div>
            <div class="panel-body">'.$bank_initial_balance_data['all_ids'].'</div>
            </div></th>';
        $content .= '</tr>';
        }
        
        
        $mobile_initial_balance_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'CLOSING-BALANCE','Mobile-Banking',$BRUNCH_ID);
        
        if($mobile_initial_balance_data['total_balance'] == 0 ){ 
         
          $total_mobile_initial_balance = 0 ;
        }else{
        
              $total_mobile_initial_balance =number_format((float)$mobile_initial_balance_data['total_balance'], 2, '.', '');
        
            $content .= '<tr>';
        
            $content .= '<th colspan="2"><div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-money"></span> Mobile </h3>                                
            <ul class="panel-controls">
            <li>' . $total_mobile_initial_balance . ' </li>
            <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
            </ul>                                   
            </div>
            <div class="panel-body">'.$mobile_initial_balance_data['all_ids'].'</div>
            </div></th>';
        $content .= '</tr>';
        }
        
        
        
        
        
        $content .= '<tr>';
        $content .= '<th colspan="2"># Money Transfer </th>';
        $content .= '</tr>';
        
        $cash_money_tr_to_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'MONEY-TRANSFER-TO', 'Cash', $BRUNCH_ID);
        
        if ($cash_money_tr_to_data['total_balance'] == 0) {
            $total_cash_money_tr_to = 0;
        } else {
            $total_cash_money_tr_to = number_format((float)$cash_money_tr_to_data['total_balance'], 2, '.', '');
            $content .= '<tr>';
            $content .= '<th colspan="2">';
            $content .= '<div class="panel panel-default">';
            $content .= '<div class="panel-heading">';
            $content .= '<h3 class="panel-title"><span class="fa fa-money"></span> Cash  </h3>';
            $content .= '<ul class="panel-controls">';
            $content .= '<li>' . $total_cash_money_tr_to . ' </li>';
            $content .= '<li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>';
            $content .= '</ul>';
            $content .= '</div>';
            $content .= '<div class="panel-body">'.$cash_money_tr_to_data['all_ids'].'</div>';
            $content .= '</div>';
            $content .= '</th>';
            $content .= '</tr>';
        }
        
        
        
        
        
        $bank_money_tr_to_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'MONEY-TRANSFER-TO','Bank',$BRUNCH_ID);
        
        if($bank_money_tr_to_data['total_balance'] == 0 ){ 
         
          $total_bank_money_tr_to = 0 ;
        }else{
        
              $total_bank_money_tr_to =number_format((float)$bank_money_tr_to_data['total_balance'], 2, '.', '');
        
            $content .= '<tr>';
        
            $content .= '<th colspan="2"><div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-money"></span> Bank </h3>                                
            <ul class="panel-controls">
            <li>' . $total_bank_money_tr_to . ' </li>
            <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
            </ul>                                   
            </div>
            <div class="panel-body">'.$bank_money_tr_to_data['all_ids'].'</div>
            </div></th>';
        $content .= '</tr>';
        }
        
        
        
        $mobile_money_tr_to_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'MONEY-TRANSFER-TO','Mobile-Banking',$BRUNCH_ID);
        
        if($mobile_money_tr_to_data['total_balance'] == 0 ){ 
         
          $total_mobile_money_tr_to = 0 ;
        }else{
        
              $total_mobile_money_tr_to =number_format((float)$mobile_money_tr_to_data['total_balance'], 2, '.', '');
        
            $content .= '<tr>';
        
            $content .= '<th colspan="2"><div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-money"></span> Mobile </h3>                                
            <ul class="panel-controls">
            <li>' . $total_mobile_money_tr_to . ' </li>
            <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
            </ul>                                   
            </div>
            <div class="panel-body">'.$mobile_money_tr_to_data['all_ids'].'</div>
            </div></th>';
        $content .= '</tr>';
        }
        
        
        $content .= '<tr>';
        $content .= '<th colspan="2"># Supplier Transaction </th>';
        $content .= '</tr>';
        
        $cash_supplier_tr_receive_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'SUPPLIER-PAYMENT', 'Cash', $BRUNCH_ID);
        
        if ($cash_supplier_tr_receive_data['total_balance'] == 0) {
            $total_cash_supplier_tr_receive = 0;
        } else {
            $total_cash_supplier_tr_receive = number_format((float)$cash_supplier_tr_receive_data['total_balance'], 2, '.', '');
            $content .= '<tr>';
            $content .= '<th colspan="2">';
            $content .= '<div class="panel panel-default">';
            $content .= '<div class="panel-heading">';
            $content .= '<h3 class="panel-title"><span class="fa fa-money"></span> Cash  </h3>';
            $content .= '<ul class="panel-controls">';
            $content .= '<li>' . $total_cash_supplier_tr_receive . ' </li>';
            $content .= '<li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>';
            $content .= '</ul>';
            $content .= '</div>';
            $content .= '<div class="panel-body">'.$cash_supplier_tr_receive_data['all_ids'].'</div>';
            $content .= '</div>';
            $content .= '</th>';
            $content .= '</tr>';
        }
        
        
        
        
        
        $bank_supplier_tr_receive_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'SUPPLIER-PAYMENT','Bank',$BRUNCH_ID);
        
        if($bank_supplier_tr_receive_data['total_balance'] == 0 ){ 
         
          $total_bank_supplier_tr_receive = 0 ;
        }else{
        
              $total_bank_supplier_tr_receive =number_format((float)$bank_supplier_tr_receive_data['total_balance'], 2, '.', '');
        
            $content .= '<tr>';
        
            $content .= '<th colspan="2"><div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-money"></span> Bank </h3>                                
            <ul class="panel-controls">
            <li>' . $total_bank_supplier_tr_receive . ' </li>
            <li class="hidden-print" ><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
            </ul>                                   
            </div>
            <div class="panel-body">'.$bank_supplier_tr_receive_data['all_ids'].'</div>
            </div></th>';
        $content .= '</tr>';
        }
        
        
        $mobile_supplier_tr_receive_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'SUPPLIER-PAYMENT','Mobile-Banking',$BRUNCH_ID);
        
        if($mobile_supplier_tr_receive_data['total_balance'] == 0 ){ 
         
          $total_mobile_supplier_tr_receive = 0 ;
        }else{
        
              $total_mobile_supplier_tr_receive =number_format((float)$mobile_supplier_tr_receive_data['total_balance'], 2, '.', '');
        
            $content .= '<tr>';
        
            $content .= '<th colspan="2"><div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-money"></span> Mobile </h3>                                
            <ul class="panel-controls">
            <li>' . $total_mobile_supplier_tr_receive . ' </li>
            <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
            </ul>                                   
            </div>
            <div class="panel-body">'.$mobile_supplier_tr_receive_data['all_ids'].'</div>
            </div></th>';
        $content .= '</tr>';
        }
        
        
        
        
            $content .= '</table>';
            $content .= '</div>';
           
        
            $content .= '<div class="col-sm-6">';
            $content .= '<table class="table">';
        
            $content .= '<tr style="background-color: orange">';
            $content .= '<th>Description</th>';
            $content .= '<th>Amount</th>';
            $content .= '</tr>';
           
        $content .= '<tr>';
        $content .= '<th colspan="2"># Add Expense </th>';
        $content .= '</tr>';
        
        $cash_add_expense_data = self::calculateSectionWiseTransection($DATE, 'EXPENSE', 'ADD EXPENSE', 'Cash', $BRUNCH_ID);
        
        if ($cash_add_expense_data['total_balance'] == 0) {
            $total_cash_add_expense = 0;
        } else {
            $total_cash_add_expense = number_format((float)$cash_add_expense_data['total_balance'], 2, '.', '');
            $content .= '<tr>';
            $content .= '<th colspan="2">';
            $content .= '<div class="panel panel-default">';
            $content .= '<div class="panel-heading">';
            $content .= '<h3 class="panel-title"><span class="fa fa-money"></span> Cash  </h3>';
            $content .= '<ul class="panel-controls">';
            $content .= '<li>' . $total_cash_add_expense . ' </li>';
            $content .= '<li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>';
            $content .= '</ul>';
            $content .= '</div>';
            $content .= '<div class="panel-body">'.$cash_add_expense_data['all_ids'].'</div>';
            $content .= '</div>';
            $content .= '</th>';
            $content .= '</tr>';
        }
        
        
        
        
        
        $bank_add_expense_data = self::calculateSectionWiseTransection($DATE, 'EXPENSE', 'ADD EXPENSE','Bank',$BRUNCH_ID);
        
        if($bank_add_expense_data['total_balance'] == 0 ){ 
         
          $total_bank_add_expense = 0 ;
        }else{
        
              $total_bank_add_expense =number_format((float)$bank_add_expense_data['total_balance'], 2, '.', '');
        
            $content .= '<tr>';
        
            $content .= '<th colspan="2"><div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-money"></span> Bank </h3>                                
            <ul class="panel-controls">
            <li>' . $total_bank_add_expense . ' </li>
            <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
            </ul>                                   
            </div>
            <div class="panel-body">'.$bank_add_expense_data['all_ids'].'</div>
            </div></th>';
        $content .= '</tr>';
        }
        
        
        $mobile_add_expense_data = self::calculateSectionWiseTransection($DATE, 'EXPENSE', 'ADD EXPENSE','Mobile-Banking',$BRUNCH_ID);
        
        if($mobile_add_expense_data['total_balance'] == 0 ){ 
         
          $total_mobile_add_expense = 0 ;
        }else{
        
              $total_mobile_add_expense =number_format((float)$mobile_add_expense_data['total_balance'], 2, '.', '');
        
            $content .= '<tr>';
        
            $content .= '<th colspan="2"><div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-money"></span> Mobile </h3>                                
            <ul class="panel-controls">
            <li>' . $total_mobile_add_expense . ' </li>
            <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
            </ul>                                   
            </div>
            <div class="panel-body">'.$mobile_add_expense_data['all_ids'].'</div>
            </div></th>';
        $content .= '</tr>';
        }
        
        $content .= '<tr>';
        $content .= '<th colspan="2"># Customer Transaction </th>';
        $content .= '</tr>';
        
        $cash_customer_expense_tr_data = self::calculateSectionWiseTransection($DATE, 'EXPENSE', 'CUSTOMER-TRANSACTION', 'Cash', $BRUNCH_ID);
        
        if ($cash_customer_expense_tr_data['total_balance'] == 0) {
            $total_cash_customer_expense_tr = 0;
        } else {
            $total_cash_customer_expense_tr = number_format((float)$cash_customer_expense_tr_data['total_balance'], 2, '.', '');
            $content .= '<tr>';
            $content .= '<th colspan="2">';
            $content .= '<div class="panel panel-default">';
            $content .= '<div class="panel-heading">';
            $content .= '<h3 class="panel-title"><span class="fa fa-money"></span> Cash  </h3>';
            $content .= '<ul class="panel-controls">';
            $content .= '<li>' . $total_cash_customer_expense_tr . ' </li>';
            $content .= '<li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>';
            $content .= '</ul>';
            $content .= '</div>';
            $content .= '<div class="panel-body">'.$cash_customer_expense_tr_data['all_ids'].'</div>';
            $content .= '</div>';
            $content .= '</th>';
            $content .= '</tr>';
        }
        
        
        
        
        
        $bank_customer_expense_tr_data = self::calculateSectionWiseTransection($DATE, 'EXPENSE', 'CUSTOMER-TRANSACTION','Bank',$BRUNCH_ID);
        
        if($bank_customer_expense_tr_data['total_balance'] == 0 ){ 
         
          $total_bank_customer_expense_tr = 0 ;
        }else{
        
              $total_bank_customer_expense_tr =number_format((float)$bank_customer_expense_tr_data['total_balance'], 2, '.', '');
        
            $content .= '<tr>';
        
            $content .= '<th colspan="2"><div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-money"></span> Bank </h3>                                
            <ul class="panel-controls">
            <li>' . $total_bank_customer_expense_tr . ' </li>
            <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
            </ul>                                   
            </div>
            <div class="panel-body">'.$bank_customer_expense_tr_data['all_ids'].'</div>
            </div></th>';
        $content .= '</tr>';
        }
        
        
        $mobile_customer_expense_tr_data = self::calculateSectionWiseTransection($DATE, 'EXPENSE', 'CUSTOMER-TRANSACTION','Mobile-Banking',$BRUNCH_ID);
        
        if($mobile_customer_expense_tr_data['total_balance'] == 0 ){ 
         
          $total_mobile_customer_expense_tr = 0 ;
        }else{
        
              $total_mobile_customer_expense_tr =number_format((float)$mobile_customer_expense_tr_data['total_balance'], 2, '.', '');
        
            $content .= '<tr>';
        
            $content .= '<th colspan="2"><div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-money"></span> Mobile </h3>                                
            <ul class="panel-controls">
            <li>' . $total_mobile_customer_expense_tr . ' </li>
            <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
            </ul>                                   
            </div>
            <div class="panel-body">'.$mobile_customer_expense_tr_data['all_ids'].'</div>
            </div></th>';
        $content .= '</tr>';
        }
        
        $content .= '<tr>';
        $content .= '<th colspan="2"># Supplier Transaction </th>';
        $content .= '</tr>';
        
        $cash_supplier_expense_tr_data = self::calculateSectionWiseTransection($DATE, 'EXPENSE', 'SUPPLIER-PAYMENT', 'Cash', $BRUNCH_ID);
        
        if ($cash_supplier_expense_tr_data['total_balance'] == 0) {
            $total_cash_supplier_expense_tr = 0;
        } else {
            $total_cash_supplier_expense_tr = number_format((float)$cash_supplier_expense_tr_data['total_balance'], 2, '.', '');
            $content .= '<tr>';
            $content .= '<th colspan="2">';
            $content .= '<div class="panel panel-default">';
            $content .= '<div class="panel-heading">';
            $content .= '<h3 class="panel-title"><span class="fa fa-money"></span> Cash  </h3>';
            $content .= '<ul class="panel-controls">';
            $content .= '<li>' . $total_cash_supplier_expense_tr . ' </li>';
            $content .= '<li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>';
            $content .= '</ul>';
            $content .= '</div>';
            $content .= '<div class="panel-body">'.$cash_supplier_expense_tr_data['all_ids'].'</div>';
            $content .= '</div>';
            $content .= '</th>';
            $content .= '</tr>';
        }
        
        
        
        
        
        $bank_supplier_expense_tr_data = self::calculateSectionWiseTransection($DATE, 'EXPENSE', 'SUPPLIER-PAYMENT','Bank',$BRUNCH_ID);
        
        if($bank_supplier_expense_tr_data['total_balance'] == 0 ){ 
         
          $total_bank_supplier_expense_tr = 0 ;
        }else{
        
              $total_bank_supplier_expense_tr =number_format((float)$bank_supplier_expense_tr_data['total_balance'], 2, '.', '');
        
            $content .= '<tr>';
        
            $content .= '<th colspan="2"><div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-money"></span> Bank </h3>                                
            <ul class="panel-controls">
            <li>' . $total_bank_supplier_expense_tr . ' </li>
            <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
            </ul>                                   
            </div>
            <div class="panel-body">'.$bank_supplier_expense_tr_data['all_ids'].'</div>
            </div></th>';
        $content .= '</tr>';
        }
        
        
        $mobile_supplier_expense_tr_data = self::calculateSectionWiseTransection($DATE, 'EXPENSE', 'SUPPLIER-PAYMENT','Mobile-Banking',$BRUNCH_ID);
        
        if($mobile_supplier_expense_tr_data['total_balance'] == 0 ){ 
         
          $total_mobile_supplier_expense_tr = 0 ;
        }else{
        
              $total_mobile_supplier_expense_tr =number_format((float)$mobile_supplier_expense_tr_data['total_balance'], 2, '.', '');
        
            $content .= '<tr>';
        
            $content .= '<th colspan="2"><div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-money"></span> Mobile </h3>                                
            <ul class="panel-controls">
            <li>' . $total_mobile_supplier_expense_tr . ' </li>
            <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
            </ul>                                   
            </div>
            <div class="panel-body">'.$mobile_supplier_expense_tr_data['all_ids'].'</div>
            </div></th>';
        $content .= '</tr>';
        }
        
        
        $content .= '<tr>';
        $content .= '<th colspan="2"># Money Transfer </th>';
        $content .= '</tr>';
        
        $cash_money_tranfer_from_data = self::calculateSectionWiseTransection($DATE, 'EXPENSE', 'MONEY-TRANSFER-FROM', 'Cash', $BRUNCH_ID);
        
        if ($cash_money_tranfer_from_data['total_balance'] == 0) {
            $total_cash_money_tranfer_from = 0;
        } else {
            $total_cash_money_tranfer_from = number_format((float)$cash_money_tranfer_from_data['total_balance'], 2, '.', '');
            $content .= '<tr>';
            $content .= '<th colspan="2">';
            $content .= '<div class="panel panel-default">';
            $content .= '<div class="panel-heading">';
            $content .= '<h3 class="panel-title"><span class="fa fa-money"></span> Cash  </h3>';
            $content .= '<ul class="panel-controls">';
            $content .= '<li>' . $total_cash_money_tranfer_from . ' </li>';
            $content .= '<li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>';
            $content .= '</ul>';
            $content .= '</div>';
            $content .= '<div class="panel-body">'.$cash_money_tranfer_from_data['all_ids'].'</div>';
            $content .= '</div>';
            $content .= '</th>';
            $content .= '</tr>';
        }
        
        
        
        
        
        $bank_money_tranfer_from_data = self::calculateSectionWiseTransection($DATE, 'EXPENSE', 'MONEY-TRANSFER-FROM','Bank',$BRUNCH_ID);
        
        if($bank_money_tranfer_from_data['total_balance'] == 0 ){ 
         
          $total_bank_money_tranfer_from = 0 ;
        }else{
        
              $total_bank_money_tranfer_from =number_format((float)$bank_money_tranfer_from_data['total_balance'], 2, '.', '');
        
            $content .= '<tr>';
        
            $content .= '<th colspan="2"><div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-money"></span> Bank </h3>                                
            <ul class="panel-controls">
            <li>' . $total_bank_money_tranfer_from . ' </li>
            <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
            </ul>                                   
            </div>
            <div class="panel-body">'.$bank_money_tranfer_from_data['all_ids'].'</div>
            </div></th>';
        $content .= '</tr>';
        }
        
        
        $mobile_money_tranfer_from_data = self::calculateSectionWiseTransection($DATE, 'EXPENSE', 'MONEY-TRANSFER-FROM','Mobile-Banking',$BRUNCH_ID);
        
        if($mobile_money_tranfer_from_data['total_balance'] == 0 ){ 
         
          $total_mobile_money_tranfer_from = 0 ;
        }else{
        
              $total_mobile_money_tranfer_from =number_format((float)$mobile_money_tranfer_from_data['total_balance'], 2, '.', '');
        
            $content .= '<tr>';
        
            $content .= '<th colspan="2"><div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-money"></span> Mobile </h3>                                
            <ul class="panel-controls">
            <li>' . $total_mobile_money_tranfer_from . ' </li>
            <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
            </ul>                                   
            </div>
            <div class="panel-body">'.$mobile_money_tranfer_from_data['all_ids'].'</div>
            </div></th>';
        $content .= '</tr>';
        }
        
        
        
        
        
        
        
        
            $content .= '<tr>';
            $content .= '<th colspan="2">#  Closing Balance </th>';
            $content .= '</tr>';
        
         // Calculate opening balance for all income sources (e.g., Bank, Cash, Mobile)
        
        
        $closing_cash = ( $opening_cash + ( $total_cash_sale + $total_cash_customer_tr + $total_cash_add_income + $total_cash_initial_balance + $total_cash_money_tr_to + $total_cash_supplier_tr_receive ) - ( $total_cash_add_expense + $total_cash_customer_expense_tr + $total_cash_supplier_expense_tr + $total_cash_money_tranfer_from ) ) ; 
        
        
        
        $closing_bank = (  ( $total_bank_sale + $total_bank_customer_tr + $total_bank_add_income + $total_bank_initial_balance + $total_bank_money_tr_to + $total_bank_supplier_tr_receive ) - ( $total_bank_add_expense + $total_bank_customer_expense_tr + $total_bank_supplier_expense_tr + $total_bank_money_tranfer_from ) ) ; 
        
        
        
        
            $content .= '<tr>';
        
            $content .= '<th colspan="2"><div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title"><span class="fa fa-money"></span> Cash  :: '.$info_brunch['brunch'].'</h3>                                
            <ul class="panel-controls">
            <li>' . $closing_cash . ' </li>
            </ul>                                   
            </div>
           
            </div></th>';
            $content .= '</tr>';
        
            
        
        
        
           
                $content .= '</table>';
            $content .= '</div>';
        
        
        
        
        $TOTAL_INCOME = $opening_cash  + $total_cash_sale + $total_bank_sale + $total_mobile_sale + $total_cash_customer_tr + $total_bank_customer_tr +  $total_mobile_customer_tr +  $total_cash_add_income + $total_bank_add_income + $total_mobile_add_income + $total_cash_initial_balance + $total_bank_initial_balance + $total_mobile_initial_balance +  $total_cash_money_tr_to +  $total_bank_money_tr_to + $total_mobile_money_tr_to +  $total_cash_supplier_tr_receive +  $total_bank_supplier_tr_receive + $total_mobile_supplier_tr_receive ; 
        
        $TOTAL_EXPENSE = $total_cash_add_expense + $total_bank_add_expense + $total_mobile_add_expense + $total_cash_customer_expense_tr + $total_bank_customer_expense_tr  +  $total_mobile_customer_expense_tr  + $total_cash_supplier_expense_tr + $total_bank_supplier_expense_tr +  $total_mobile_supplier_expense_tr +  $total_cash_money_tranfer_from  + $total_bank_money_tranfer_from + $total_mobile_money_tranfer_from + $closing_cash + $closing_bank  ;  
        
             
            $diff =  number_format((float) ($TOTAL_INCOME - $TOTAL_EXPENSE), 2, '.', '')  ; 
        
        
           if( $diff >  0.00 ||  $diff <  0.00 ){
                $color = "red";
            }else{
                $color = "green";
            }
        
            // Calculate closing balance
            $content .= '<div class="row">';
            $content .= '<div class="col-sm-12">';
              $content .= '<table class="table">';
        $content .= '<tr style="background-color: '. $color.' ; color:white;">';
        $content .= '<th>Total In</th>';
        $content .= '<th style="text-align:right">' . number_format((float)$TOTAL_INCOME, 2, '.', '') . '</th>';
        $content .= '<th>Total Out</th>';
        $content .= '<th style="text-align:right">' . number_format((float)$TOTAL_EXPENSE, 2, '.', '') . '</th>';
        
        $content .= '</tr>';
        
            $content .= '</table>';
            $content .= '</div>';
              $content .= '</div>';
        
        
           return $content; 
        }
    
    
    
    
    
    public static function DAY_BOOK_HEAD_OFFICE($DATE,$BRUNCH_ID)
    {
        $conn_me = Database::getInstance();
      
      $info_brunch = SETUP::SETUP_BRUNCH($BRUNCH_ID);
    
      
        $content = '<div class="row">';
        $content .= '<div class="col-sm-6">';
        $content .= '<table class="table">';
    
        $content .= '<tr style="background-color: orange">';
        $content .= '<th>Description</th>';
        $content .= '<th>Amount</th>';
        $content .= '</tr>';
    
    
        $content .= '<tr>';
        $content .= '<th colspan="2">#  Opening Balance </th>';
        $content .= '</tr>';
    
        $opening_cash = self::calculateOpeningBalance($DATE,$BRUNCH_ID);
    
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-money"></span> Cash  :: '.$info_brunch['brunch'].'</h3>                                
        <ul class="panel-controls">
        <li>' . $opening_cash . ' </li>
        </ul>                                   
        </div>
       
        </div></th>';
        $content .= '</tr>';
    
        $opening_bank_accounts = self::calculateOpeningBalanceForBankAccounts($DATE);
    
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-money"></span> Bank :: All Brunch </h3>                                
        <ul class="panel-controls">
        <li>' . $opening_bank_accounts['total_balance'] . ' </li>
        <li class="hidden-print"><a class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">'.$opening_bank_accounts['content'].'</div>
        </div></th>';
        $content .= '</tr>';
    
    
    
        $opening_mobile_accounts = self::calculateOpeningBalanceForIndividualMobileAccounts($DATE);
    
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-mobile-phone"></span> Mobile :: All Brunch </h3>                                
        <ul class="panel-controls">
        <li>' . $opening_mobile_accounts['total_balance'] . ' </li>
        <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">'.$opening_mobile_accounts['content'].'</div>
        </div></th>';
        $content .= '</tr>';
    
    
    
    
      $content .= '<tr>';
      $content .= '<th colspan="2">#  Invoice Wise Payment </th>';
      $content .= '</tr>';
    
      $cash_sales_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'Invoice Wise Payment','Cash',$BRUNCH_ID);
    
    if($cash_sales_data['total_balance'] == 0 ){ 
      $total_cash_sale = 0 ;
    
    }else{
    
          $total_cash_sale =number_format((float)$cash_sales_data['total_balance'], 2, '.', '');
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-money"></span> Cash  </h3>                                
        <ul class="panel-controls">
        <li>' . $total_cash_sale . ' </li>
        <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">'.$cash_sales_data['all_ids'].'</div>
        </div></th>';
    $content .= '</tr>';
    }
    
    
    $bank_sales_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'Invoice Wise Payment','Bank',$BRUNCH_ID);
    
    if($bank_sales_data['total_balance'] == 0 ){ 
     
      $total_bank_sale = 0 ;
    }else{
    
          $total_bank_sale =number_format((float)$bank_sales_data['total_balance'], 2, '.', '');
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-money"></span> Bank </h3>                                
        <ul class="panel-controls">
        <li>' . $total_bank_sale . ' </li>
        <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">'.$bank_sales_data['all_ids'].'</div>
        </div></th>';
    $content .= '</tr>';
    }
    
    
    
    
    $mobile_sales_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'Invoice Wise Payment','Mobile-Banking',$BRUNCH_ID);
    
    if($mobile_sales_data['total_balance'] == 0 ){ 
     
      $total_mobile_sale = 0 ;
    }else{
    
          $total_mobile_sale =number_format((float)$mobile_sales_data['total_balance'], 2, '.', '');
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-money"></span> Mobile </h3>                                
        <ul class="panel-controls">
        <li>' . $total_mobile_sale . ' </li>
        <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">'.$mobile_sales_data['all_ids'].'</div>
        </div></th>';
    $content .= '</tr>';
    }
    
    $content .= '<tr>';
    $content .= '<th colspan="2"># Customer Transaction</th>';
    $content .= '</tr>';
    
    $cash_customer_tr_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'CUSTOMER-TRANSACTION', 'Cash', $BRUNCH_ID);
    
    if ($cash_customer_tr_data['total_balance'] == 0) {
        $total_cash_customer_tr = 0;
    } else {
        $total_cash_customer_tr = number_format((float)$cash_customer_tr_data['total_balance'], 2, '.', '');
        $content .= '<tr>';
        $content .= '<th colspan="2">';
        $content .= '<div class="panel panel-default">';
        $content .= '<div class="panel-heading">';
        $content .= '<h3 class="panel-title"><span class="fa fa-money"></span> Cash  </h3>';
        $content .= '<ul class="panel-controls">';
        $content .= '<li>' . $total_cash_customer_tr . ' </li>';
        $content .= '<li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>';
        $content .= '</ul>';
        $content .= '</div>';
        $content .= '<div class="panel-body">'.$cash_customer_tr_data['all_ids'].'</div>';
        $content .= '</div>';
        $content .= '</th>';
        $content .= '</tr>';
    }
    
    
    
    
    
    
    $bank_customer_tr_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'CUSTOMER-TRANSACTION','Bank',$BRUNCH_ID);
    
    if($bank_customer_tr_data['total_balance'] == 0 ){ 
     
      $total_bank_customer_tr = 0 ;
    }else{
    
          $total_bank_customer_tr =number_format((float)$bank_customer_tr_data['total_balance'], 2, '.', '');
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-money"></span> Bank </h3>                                
        <ul class="panel-controls">
        <li>' . $total_bank_customer_tr . ' </li>
        <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">'.$bank_customer_tr_data['all_ids'].'</div>
        </div></th>';
    $content .= '</tr>';
    }
    
    
    
    
    
    
    $mobile_customer_tr_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'CUSTOMER-TRANSACTION','Mobile-Banking',$BRUNCH_ID);
    
    if($mobile_customer_tr_data['total_balance'] == 0 ){ 
     
      $total_mobile_customer_tr = 0 ;
    }else{
    
          $total_mobile_customer_tr =number_format((float)$mobile_customer_tr_data['total_balance'], 2, '.', '');
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-money"></span> Mobile </h3>                                
        <ul class="panel-controls">
        <li>' . $total_mobile_customer_tr . ' </li>
        <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">'.$mobile_customer_tr_data['all_ids'].'</div>
        </div></th>';
    $content .= '</tr>';
    }
    
    
    
    
    $content .= '<tr>';
    $content .= '<th colspan="2"># Add Income </th>';
    $content .= '</tr>';
    
    $cash_add_income_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'ADD INCOME', 'Cash', $BRUNCH_ID);
    
    if ($cash_add_income_data['total_balance'] == 0) {
        $total_cash_add_income = 0;
    } else {
        $total_cash_add_income = number_format((float)$cash_add_income_data['total_balance'], 2, '.', '');
        $content .= '<tr>';
        $content .= '<th colspan="2">';
        $content .= '<div class="panel panel-default">';
        $content .= '<div class="panel-heading">';
        $content .= '<h3 class="panel-title"><span class="fa fa-money"></span> Cash  </h3>';
        $content .= '<ul class="panel-controls">';
        $content .= '<li>' . $total_cash_add_income . ' </li>';
        $content .= '<li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>';
        $content .= '</ul>';
        $content .= '</div>';
        $content .= '<div class="panel-body">'.$cash_add_income_data['all_ids'].'</div>';
        $content .= '</div>';
        $content .= '</th>';
        $content .= '</tr>';
    }
    
    
    
    
    
    $bank_add_income_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'ADD INCOME','Bank',$BRUNCH_ID);
    
    if($bank_add_income_data['total_balance'] == 0 ){ 
     
      $total_bank_add_income = 0 ;
    }else{
    
          $total_bank_add_income =number_format((float)$bank_add_income_data['total_balance'], 2, '.', '');
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-money"></span> Bank </h3>                                
        <ul class="panel-controls">
        <li>' . $total_bank_add_income . ' </li>
        <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">'.$bank_add_income_data['all_ids'].'</div>
        </div></th>';
    $content .= '</tr>';
    }
    
    
    $mobile_add_income_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'ADD INCOME','Mobile-Banking',$BRUNCH_ID);
    
    if($mobile_add_income_data['total_balance'] == 0 ){ 
     
      $total_mobile_add_income = 0 ;
    }else{
    
          $total_mobile_add_income =number_format((float)$mobile_add_income_data['total_balance'], 2, '.', '');
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-money"></span> Mobile </h3>                                
        <ul class="panel-controls">
        <li>' . $total_mobile_add_income . ' </li>
        <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">'.$mobile_add_income_data['all_ids'].'</div>
        </div></th>';
    $content .= '</tr>';
    }
    
    
    
    $content .= '<tr>';
    $content .= '<th colspan="2"># Initial Balance </th>';
    $content .= '</tr>';
    
    $cash_initial_balance_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'CLOSING-BALANCE', 'Cash', $BRUNCH_ID);
    
    if ($cash_initial_balance_data['total_balance'] == 0) {
        $total_cash_initial_balance = 0;
    } else {
        $total_cash_initial_balance = number_format((float)$cash_initial_balance_data['total_balance'], 2, '.', '');
        $content .= '<tr>';
        $content .= '<th colspan="2">';
        $content .= '<div class="panel panel-default">';
        $content .= '<div class="panel-heading">';
        $content .= '<h3 class="panel-title"><span class="fa fa-money"></span> Cash  </h3>';
        $content .= '<ul class="panel-controls">';
        $content .= '<li>' . $total_cash_initial_balance . ' </li>';
        $content .= '<li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>';
        $content .= '</ul>';
        $content .= '</div>';
        $content .= '<div class="panel-body">'.$cash_initial_balance_data['all_ids'].'</div>';
        $content .= '</div>';
        $content .= '</th>';
        $content .= '</tr>';
    }
    
    
    
    
    
    $bank_initial_balance_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'CLOSING-BALANCE','Bank',$BRUNCH_ID);
    
    if($bank_initial_balance_data['total_balance'] == 0 ){ 
     
      $total_bank_initial_balance = 0 ;
    }else{
    
          $total_bank_initial_balance =number_format((float)$bank_initial_balance_data['total_balance'], 2, '.', '');
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-money"></span> Bank </h3>                                
        <ul class="panel-controls">
        <li>' . $total_bank_initial_balance . ' </li>
        <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">'.$bank_initial_balance_data['all_ids'].'</div>
        </div></th>';
    $content .= '</tr>';
    }
    
    
    $mobile_initial_balance_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'CLOSING-BALANCE','Mobile-Banking',$BRUNCH_ID);
    
    if($mobile_initial_balance_data['total_balance'] == 0 ){ 
     
      $total_mobile_initial_balance = 0 ;
    }else{
    
          $total_mobile_initial_balance =number_format((float)$mobile_initial_balance_data['total_balance'], 2, '.', '');
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-money"></span> Mobile </h3>                                
        <ul class="panel-controls">
        <li>' . $total_mobile_initial_balance . ' </li>
        <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">'.$mobile_initial_balance_data['all_ids'].'</div>
        </div></th>';
    $content .= '</tr>';
    }
    
    
    
    
    
    $content .= '<tr>';
    $content .= '<th colspan="2"># Money Transfer </th>';
    $content .= '</tr>';
    
    $cash_money_tr_to_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'MONEY-TRANSFER-TO', 'Cash', $BRUNCH_ID);
    
    if ($cash_money_tr_to_data['total_balance'] == 0) {
        $total_cash_money_tr_to = 0;
    } else {
        $total_cash_money_tr_to = number_format((float)$cash_money_tr_to_data['total_balance'], 2, '.', '');
        $content .= '<tr>';
        $content .= '<th colspan="2">';
        $content .= '<div class="panel panel-default">';
        $content .= '<div class="panel-heading">';
        $content .= '<h3 class="panel-title"><span class="fa fa-money"></span> Cash  </h3>';
        $content .= '<ul class="panel-controls">';
        $content .= '<li>' . $total_cash_money_tr_to . ' </li>';
        $content .= '<li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>';
        $content .= '</ul>';
        $content .= '</div>';
        $content .= '<div class="panel-body">'.$cash_money_tr_to_data['all_ids'].'</div>';
        $content .= '</div>';
        $content .= '</th>';
        $content .= '</tr>';
    }
    
    
    
    
    
    $bank_money_tr_to_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'MONEY-TRANSFER-TO','Bank',$BRUNCH_ID);
    
    if($bank_money_tr_to_data['total_balance'] == 0 ){ 
     
      $total_bank_money_tr_to = 0 ;
    }else{
    
          $total_bank_money_tr_to =number_format((float)$bank_money_tr_to_data['total_balance'], 2, '.', '');
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-money"></span> Bank </h3>                                
        <ul class="panel-controls">
        <li>' . $total_bank_money_tr_to . ' </li>
        <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">'.$bank_money_tr_to_data['all_ids'].'</div>
        </div></th>';
    $content .= '</tr>';
    }
    
    
    
    $mobile_money_tr_to_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'MONEY-TRANSFER-TO','Mobile-Banking',$BRUNCH_ID);
    
    if($mobile_money_tr_to_data['total_balance'] == 0 ){ 
     
      $total_mobile_money_tr_to = 0 ;
    }else{
    
          $total_mobile_money_tr_to =number_format((float)$mobile_money_tr_to_data['total_balance'], 2, '.', '');
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-money"></span> Mobile </h3>                                
        <ul class="panel-controls">
        <li>' . $total_mobile_money_tr_to . ' </li>
        <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">'.$mobile_money_tr_to_data['all_ids'].'</div>
        </div></th>';
    $content .= '</tr>';
    }
    
    
    $content .= '<tr>';
    $content .= '<th colspan="2"># Supplier Transaction </th>';
    $content .= '</tr>';
    
    $cash_supplier_tr_receive_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'SUPPLIER-PAYMENT', 'Cash', $BRUNCH_ID);
    
    if ($cash_supplier_tr_receive_data['total_balance'] == 0) {
        $total_cash_supplier_tr_receive = 0;
    } else {
        $total_cash_supplier_tr_receive = number_format((float)$cash_supplier_tr_receive_data['total_balance'], 2, '.', '');
        $content .= '<tr>';
        $content .= '<th colspan="2">';
        $content .= '<div class="panel panel-default">';
        $content .= '<div class="panel-heading">';
        $content .= '<h3 class="panel-title"><span class="fa fa-money"></span> Cash  </h3>';
        $content .= '<ul class="panel-controls">';
        $content .= '<li>' . $total_cash_supplier_tr_receive . ' </li>';
        $content .= '<li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>';
        $content .= '</ul>';
        $content .= '</div>';
        $content .= '<div class="panel-body">'.$cash_supplier_tr_receive_data['all_ids'].'</div>';
        $content .= '</div>';
        $content .= '</th>';
        $content .= '</tr>';
    }
    
    
    
    
    
    $bank_supplier_tr_receive_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'SUPPLIER-PAYMENT','Bank',$BRUNCH_ID);
    
    if($bank_supplier_tr_receive_data['total_balance'] == 0 ){ 
     
      $total_bank_supplier_tr_receive = 0 ;
    }else{
    
          $total_bank_supplier_tr_receive =number_format((float)$bank_supplier_tr_receive_data['total_balance'], 2, '.', '');
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-money"></span> Bank </h3>                                
        <ul class="panel-controls">
        <li>' . $total_bank_supplier_tr_receive . ' </li>
        <li class="hidden-print" ><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">'.$bank_supplier_tr_receive_data['all_ids'].'</div>
        </div></th>';
    $content .= '</tr>';
    }
    
    
    $mobile_supplier_tr_receive_data = self::calculateSectionWiseTransection($DATE, 'INCOME', 'SUPPLIER-PAYMENT','Mobile-Banking',$BRUNCH_ID);
    
    if($mobile_supplier_tr_receive_data['total_balance'] == 0 ){ 
     
      $total_mobile_supplier_tr_receive = 0 ;
    }else{
    
          $total_mobile_supplier_tr_receive =number_format((float)$mobile_supplier_tr_receive_data['total_balance'], 2, '.', '');
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-money"></span> Mobile </h3>                                
        <ul class="panel-controls">
        <li>' . $total_mobile_supplier_tr_receive . ' </li>
        <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">'.$mobile_supplier_tr_receive_data['all_ids'].'</div>
        </div></th>';
    $content .= '</tr>';
    }
    
    
    
    
        $content .= '</table>';
        $content .= '</div>';
       
    
        $content .= '<div class="col-sm-6">';
        $content .= '<table class="table">';
    
        $content .= '<tr style="background-color: orange">';
        $content .= '<th>Description</th>';
        $content .= '<th>Amount</th>';
        $content .= '</tr>';
       
    $content .= '<tr>';
    $content .= '<th colspan="2"># Add Expense </th>';
    $content .= '</tr>';
    
    $cash_add_expense_data = self::calculateSectionWiseTransection($DATE, 'EXPENSE', 'ADD EXPENSE', 'Cash', $BRUNCH_ID);
    
    if ($cash_add_expense_data['total_balance'] == 0) {
        $total_cash_add_expense = 0;
    } else {
        $total_cash_add_expense = number_format((float)$cash_add_expense_data['total_balance'], 2, '.', '');
        $content .= '<tr>';
        $content .= '<th colspan="2">';
        $content .= '<div class="panel panel-default">';
        $content .= '<div class="panel-heading">';
        $content .= '<h3 class="panel-title"><span class="fa fa-money"></span> Cash  </h3>';
        $content .= '<ul class="panel-controls">';
        $content .= '<li>' . $total_cash_add_expense . ' </li>';
        $content .= '<li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>';
        $content .= '</ul>';
        $content .= '</div>';
        $content .= '<div class="panel-body">'.$cash_add_expense_data['all_ids'].'</div>';
        $content .= '</div>';
        $content .= '</th>';
        $content .= '</tr>';
    }
    
    
    
    
    
    $bank_add_expense_data = self::calculateSectionWiseTransection($DATE, 'EXPENSE', 'ADD EXPENSE','Bank',$BRUNCH_ID);
    
    if($bank_add_expense_data['total_balance'] == 0 ){ 
     
      $total_bank_add_expense = 0 ;
    }else{
    
          $total_bank_add_expense =number_format((float)$bank_add_expense_data['total_balance'], 2, '.', '');
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-money"></span> Bank </h3>                                
        <ul class="panel-controls">
        <li>' . $total_bank_add_expense . ' </li>
        <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">'.$bank_add_expense_data['all_ids'].'</div>
        </div></th>';
    $content .= '</tr>';
    }
    
    
    $mobile_add_expense_data = self::calculateSectionWiseTransection($DATE, 'EXPENSE', 'ADD EXPENSE','Mobile-Banking',$BRUNCH_ID);
    
    if($mobile_add_expense_data['total_balance'] == 0 ){ 
     
      $total_mobile_add_expense = 0 ;
    }else{
    
          $total_mobile_add_expense =number_format((float)$mobile_add_expense_data['total_balance'], 2, '.', '');
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-money"></span> Mobile </h3>                                
        <ul class="panel-controls">
        <li>' . $total_mobile_add_expense . ' </li>
        <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">'.$mobile_add_expense_data['all_ids'].'</div>
        </div></th>';
    $content .= '</tr>';
    }
    
    $content .= '<tr>';
    $content .= '<th colspan="2"># Customer Transaction </th>';
    $content .= '</tr>';
    
    $cash_customer_expense_tr_data = self::calculateSectionWiseTransection($DATE, 'EXPENSE', 'CUSTOMER-TRANSACTION', 'Cash', $BRUNCH_ID);
    
    if ($cash_customer_expense_tr_data['total_balance'] == 0) {
        $total_cash_customer_expense_tr = 0;
    } else {
        $total_cash_customer_expense_tr = number_format((float)$cash_customer_expense_tr_data['total_balance'], 2, '.', '');
        $content .= '<tr>';
        $content .= '<th colspan="2">';
        $content .= '<div class="panel panel-default">';
        $content .= '<div class="panel-heading">';
        $content .= '<h3 class="panel-title"><span class="fa fa-money"></span> Cash  </h3>';
        $content .= '<ul class="panel-controls">';
        $content .= '<li>' . $total_cash_customer_expense_tr . ' </li>';
        $content .= '<li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>';
        $content .= '</ul>';
        $content .= '</div>';
        $content .= '<div class="panel-body">'.$cash_customer_expense_tr_data['all_ids'].'</div>';
        $content .= '</div>';
        $content .= '</th>';
        $content .= '</tr>';
    }
    
    
    
    
    
    $bank_customer_expense_tr_data = self::calculateSectionWiseTransection($DATE, 'EXPENSE', 'CUSTOMER-TRANSACTION','Bank',$BRUNCH_ID);
    
    if($bank_customer_expense_tr_data['total_balance'] == 0 ){ 
     
      $total_bank_customer_expense_tr = 0 ;
    }else{
    
          $total_bank_customer_expense_tr =number_format((float)$bank_customer_expense_tr_data['total_balance'], 2, '.', '');
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-money"></span> Bank </h3>                                
        <ul class="panel-controls">
        <li>' . $total_bank_customer_expense_tr . ' </li>
        <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">'.$bank_customer_expense_tr_data['all_ids'].'</div>
        </div></th>';
    $content .= '</tr>';
    }
    
    
    $mobile_customer_expense_tr_data = self::calculateSectionWiseTransection($DATE, 'EXPENSE', 'CUSTOMER-TRANSACTION','Mobile-Banking',$BRUNCH_ID);
    
    if($mobile_customer_expense_tr_data['total_balance'] == 0 ){ 
     
      $total_mobile_customer_expense_tr = 0 ;
    }else{
    
          $total_mobile_customer_expense_tr =number_format((float)$mobile_customer_expense_tr_data['total_balance'], 2, '.', '');
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-money"></span> Mobile </h3>                                
        <ul class="panel-controls">
        <li>' . $total_mobile_customer_expense_tr . ' </li>
        <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">'.$mobile_customer_expense_tr_data['all_ids'].'</div>
        </div></th>';
    $content .= '</tr>';
    }
    
    $content .= '<tr>';
    $content .= '<th colspan="2"># Supplier Transaction </th>';
    $content .= '</tr>';
    
    $cash_supplier_expense_tr_data = self::calculateSectionWiseTransection($DATE, 'EXPENSE', 'SUPPLIER-PAYMENT', 'Cash', $BRUNCH_ID);
    
    if ($cash_supplier_expense_tr_data['total_balance'] == 0) {
        $total_cash_supplier_expense_tr = 0;
    } else {
        $total_cash_supplier_expense_tr = number_format((float)$cash_supplier_expense_tr_data['total_balance'], 2, '.', '');
        $content .= '<tr>';
        $content .= '<th colspan="2">';
        $content .= '<div class="panel panel-default">';
        $content .= '<div class="panel-heading">';
        $content .= '<h3 class="panel-title"><span class="fa fa-money"></span> Cash  </h3>';
        $content .= '<ul class="panel-controls">';
        $content .= '<li>' . $total_cash_supplier_expense_tr . ' </li>';
        $content .= '<li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>';
        $content .= '</ul>';
        $content .= '</div>';
        $content .= '<div class="panel-body">'.$cash_supplier_expense_tr_data['all_ids'].'</div>';
        $content .= '</div>';
        $content .= '</th>';
        $content .= '</tr>';
    }
    
    
    
    
    
    $bank_supplier_expense_tr_data = self::calculateSectionWiseTransection($DATE, 'EXPENSE', 'SUPPLIER-PAYMENT','Bank',$BRUNCH_ID);
    
    if($bank_supplier_expense_tr_data['total_balance'] == 0 ){ 
     
      $total_bank_supplier_expense_tr = 0 ;
    }else{
    
          $total_bank_supplier_expense_tr =number_format((float)$bank_supplier_expense_tr_data['total_balance'], 2, '.', '');
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-money"></span> Bank </h3>                                
        <ul class="panel-controls">
        <li>' . $total_bank_supplier_expense_tr . ' </li>
        <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">'.$bank_supplier_expense_tr_data['all_ids'].'</div>
        </div></th>';
    $content .= '</tr>';
    }
    
    
    $mobile_supplier_expense_tr_data = self::calculateSectionWiseTransection($DATE, 'EXPENSE', 'SUPPLIER-PAYMENT','Mobile-Banking',$BRUNCH_ID);
    
    if($mobile_supplier_expense_tr_data['total_balance'] == 0 ){ 
     
      $total_mobile_supplier_expense_tr = 0 ;
    }else{
    
          $total_mobile_supplier_expense_tr =number_format((float)$mobile_supplier_expense_tr_data['total_balance'], 2, '.', '');
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-money"></span> Mobile </h3>                                
        <ul class="panel-controls">
        <li>' . $total_mobile_supplier_expense_tr . ' </li>
        <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">'.$mobile_supplier_expense_tr_data['all_ids'].'</div>
        </div></th>';
    $content .= '</tr>';
    }
    
    
    $content .= '<tr>';
    $content .= '<th colspan="2"># Money Transfer </th>';
    $content .= '</tr>';
    
    $cash_money_tranfer_from_data = self::calculateSectionWiseTransection($DATE, 'EXPENSE', 'MONEY-TRANSFER-FROM', 'Cash', $BRUNCH_ID);
    
    if ($cash_money_tranfer_from_data['total_balance'] == 0) {
        $total_cash_money_tranfer_from = 0;
    } else {
        $total_cash_money_tranfer_from = number_format((float)$cash_money_tranfer_from_data['total_balance'], 2, '.', '');
        $content .= '<tr>';
        $content .= '<th colspan="2">';
        $content .= '<div class="panel panel-default">';
        $content .= '<div class="panel-heading">';
        $content .= '<h3 class="panel-title"><span class="fa fa-money"></span> Cash  </h3>';
        $content .= '<ul class="panel-controls">';
        $content .= '<li>' . $total_cash_money_tranfer_from . ' </li>';
        $content .= '<li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>';
        $content .= '</ul>';
        $content .= '</div>';
        $content .= '<div class="panel-body">'.$cash_money_tranfer_from_data['all_ids'].'</div>';
        $content .= '</div>';
        $content .= '</th>';
        $content .= '</tr>';
    }
    
    
    
    
    
    $bank_money_tranfer_from_data = self::calculateSectionWiseTransection($DATE, 'EXPENSE', 'MONEY-TRANSFER-FROM','Bank',$BRUNCH_ID);
    
    if($bank_money_tranfer_from_data['total_balance'] == 0 ){ 
     
      $total_bank_money_tranfer_from = 0 ;
    }else{
    
          $total_bank_money_tranfer_from =number_format((float)$bank_money_tranfer_from_data['total_balance'], 2, '.', '');
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-money"></span> Bank </h3>                                
        <ul class="panel-controls">
        <li>' . $total_bank_money_tranfer_from . ' </li>
        <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">'.$bank_money_tranfer_from_data['all_ids'].'</div>
        </div></th>';
    $content .= '</tr>';
    }
    
    
    $mobile_money_tranfer_from_data = self::calculateSectionWiseTransection($DATE, 'EXPENSE', 'MONEY-TRANSFER-FROM','Mobile-Banking',$BRUNCH_ID);
    
    if($mobile_money_tranfer_from_data['total_balance'] == 0 ){ 
     
      $total_mobile_money_tranfer_from = 0 ;
    }else{
    
          $total_mobile_money_tranfer_from =number_format((float)$mobile_money_tranfer_from_data['total_balance'], 2, '.', '');
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-money"></span> Mobile </h3>                                
        <ul class="panel-controls">
        <li>' . $total_mobile_money_tranfer_from . ' </li>
        <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">'.$mobile_money_tranfer_from_data['all_ids'].'</div>
        </div></th>';
    $content .= '</tr>';
    }
    
    
    
    
    
    
    
    
        $content .= '<tr>';
        $content .= '<th colspan="2">#  Closing Balance </th>';
        $content .= '</tr>';
    
     // Calculate opening balance for all income sources (e.g., Bank, Cash, Mobile)
    
    
    $closing_cash = ( $opening_cash + ( $total_cash_sale + $total_cash_customer_tr + $total_cash_add_income + $total_cash_initial_balance + $total_cash_money_tr_to + $total_cash_supplier_tr_receive ) - ( $total_cash_add_expense + $total_cash_customer_expense_tr + $total_cash_supplier_expense_tr + $total_cash_money_tranfer_from ) ) ; 
    
    
    
    $closing_bank = ( $opening_bank_accounts['total_balance']  + ( $total_bank_sale + $total_bank_customer_tr + $total_bank_add_income + $total_bank_initial_balance + $total_bank_money_tr_to + $total_bank_supplier_tr_receive ) - ( $total_bank_add_expense + $total_bank_customer_expense_tr + $total_bank_supplier_expense_tr + $total_bank_money_tranfer_from ) ) ; 
    
    
    
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-money"></span> Cash  :: '.$info_brunch['brunch'].'</h3>                                
        <ul class="panel-controls">
        <li>' . $closing_cash . ' </li>
        </ul>                                   
        </div>
       
        </div></th>';
        $content .= '</tr>';
    
        $closing_bank_accounts = self::calculateClosingBalanceForBankAccounts($DATE);
    
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-bank"></span> Bank :: '.$info_brunch['brunch'].'</h3>                                
        <ul class="panel-controls">
        <li>' . $closing_bank . ' </li>
        <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">
        <table class="table">';
    
        $content .= $closing_bank_accounts['content'];
    
        $content .= '</table>
        </div>
        </div></th>';
        $content .= '</tr>';
    
    
        $closing_mobile_accounts = self::calculateClosingBalanceForIndividualMobileAccounts($DATE);
    
    
        $content .= '<tr>';
    
        $content .= '<th colspan="2"><div class="panel panel-default">
        <div class="panel-heading">
        <h3 class="panel-title"><span class="fa fa-mobile-phone"></span> Mobile :: '.$info_brunch['brunch'].'</h3>                                
        <ul class="panel-controls">
        <li>' . $closing_mobile_accounts['total_balance'] . ' </li>
        <li class="hidden-print"><a  class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
        </ul>                                   
        </div>
        <div class="panel-body">
        <table class="table">';
    
        // Calculate opening balance for individual mobile accounts
        $content .= $closing_mobile_accounts['content'];
    
            $content .= '</table>';
        $content .= '</div>';
    
    
    
    
    $TOTAL_INCOME = $opening_cash + $opening_bank_accounts['total_balance'] + $opening_mobile_accounts['total_balance'] + $total_cash_sale + $total_bank_sale + $total_mobile_sale + $total_cash_customer_tr + $total_bank_customer_tr +  $total_mobile_customer_tr +  $total_cash_add_income + $total_bank_add_income + $total_mobile_add_income + $total_cash_initial_balance + $total_bank_initial_balance + $total_mobile_initial_balance +  $total_cash_money_tr_to +  $total_bank_money_tr_to + $total_mobile_money_tr_to +  $total_cash_supplier_tr_receive +  $total_bank_supplier_tr_receive + $total_mobile_supplier_tr_receive ; 
    
    $TOTAL_EXPENSE = $total_cash_add_expense + $total_bank_add_expense + $total_mobile_add_expense + $total_cash_customer_expense_tr + $total_bank_customer_expense_tr  +  $total_mobile_customer_expense_tr  + $total_cash_supplier_expense_tr + $total_bank_supplier_expense_tr +  $total_mobile_supplier_expense_tr +  $total_cash_money_tranfer_from  + $total_bank_money_tranfer_from + $total_mobile_money_tranfer_from + $closing_cash + $closing_bank + $closing_mobile_accounts['total_balance']  ;  
    
         
        $diff =  number_format((float) ($TOTAL_INCOME - $TOTAL_EXPENSE), 2, '.', '')  ; 
    
    
       if( $diff >  0.00 ||  $diff <  0.00 ){
            $color = "red";
        }else{
            $color = "green";
        }
    
        // Calculate closing balance
        $content .= '<div class="row">';
        $content .= '<div class="col-sm-12">';
          $content .= '<table class="table">';
    $content .= '<tr style="background-color: '. $color.' ; color:white;">';
    $content .= '<th>Total In</th>';
    $content .= '<th style="text-align:right">' . number_format((float)$TOTAL_INCOME, 2, '.', '') . '</th>';
    $content .= '<th>Total Out</th>';
    $content .= '<th style="text-align:right">' . number_format((float)$TOTAL_EXPENSE, 2, '.', '') . '</th>';
    
    $content .= '</tr>';
    
        $content .= '</table>';
        $content .= '</div>';
          $content .= '</div>';
    
    
       return $content; 
    }
}



class FIND {

    
        static  function TransactionHistory($clientId) {

        $conn_me = Database::getInstance();

        $info_customer = SETUP::SETUP_CUSTOMER($clientId);


$content = '';

$content .='<div class="table-responsive">
<table class="table table-hover table-condensed table-striped table-bordered" style="white-space:nowrap;">
    <tr><th colspan="2" style="text-align:center;color:red;">Showing Last 5 Transaction Details</th></tr>
    <tr><th>Customer Name: '.$info_customer['customer_name'].'</th>
    <th>Shop Name: '.$info_customer['shop_name'].'</th></tr>
    <tr><th>Address: '.$info_customer['address'].'</th>
    <th>Address: '.$info_customer['mobile'].'</th></tr>

    </table>';



$content .='<div class="table-responsive">
<table class="table table-hover table-condensed table-striped table-bordered datatable" id="myDatatable" style="white-space:nowrap;">
    <thead>
    <th>Sl</th>
        <th>Date</th>
                <th>Brunch</th>
                                <th>User</th>

        <th>In Amount</th>
        <th>Out Amount</th>
        <th>Note</th>
    </thead>
    <tbody>';
    

     


       $qry = $conn_me->prepare("SELECT E.name,B.brunch,A.note,A.in_amount,A.out_amount,A.transection_date FROM account_transection A JOIN setup_brunch B ON ( A.brunch_id = B.id) 
JOIN admin D ON ( A.poster = D.id)
JOIN setup_employee E ON ( D.employee_id = E.id)
        WHERE A.transection_to = 'Customer' and A.transection_to_id = '".$clientId."' order by A.transection_date DESC LIMIT 5
    ");

$qry->execute();

$fetch = $qry->fetchAll(PDO::FETCH_ASSOC) ; 
$sl=1 ; 
foreach  ( $fetch as $row  ) {

    $content .='<tr>';

    $content .= '<td>'.$sl++.'</td>';
    $content .= '<td>'.$row['transection_date'].'</td>';
        $content .= '<td>'.$row['brunch'].'</td>';
        $content .= '<td>'.$row['name'].'</td>';

    $content .= '<td>'.$row['in_amount'].'</td>';
    $content .= '<td>'.$row['out_amount'].'</td>';
    $content .= '<td>'.$row['note'].'</td>';


    $content .='</tr>';
      
}


$content .='</tbody>

</table>
</div>';



  
   
            return  $content;
    
    
        
    }


    

    static  function PENDING_TRANSACTION_POST($transection_type,$data_inserted_from) {

        $conn_me = Database::getInstance();


if($transection_type == 'BOTH' ){
$QUERY = "";
}else{
$QUERY = " `transection_type` = '".$transection_type."'  AND ";
}


$query1 = $conn_me->prepare("SELECT count(`id`) As `total` FROM `account_posting_pending` where `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND $QUERY  `data_inserted_from` = '".$data_inserted_from."'  AND ( `posting_status` = 'Pending' OR  `data_confirmed` = 'NO')  "); 
$query1->execute();
$fetch = $query1->fetch(PDO::FETCH_ASSOC);
if($query1->rowCount() > 0 ){
    $total = $fetch['total'];
}else{
    $total = 0;
   
}
      
   
            return  $total . " Posting Pending" ;
    
    
        
    }


    

    static function SALES_REVENUE($DATE_FROM,$DATE_TO,$BRUNCH){
        $conn_me = Database::getInstance();



        if($BRUNCH == 'All' ){
            $BRUNCH_QUERY = "";
         }else {
           $BRUNCH_QUERY = " AND  `brunch_id` = '".$BRUNCH."' ";
         }


        $query = $conn_me->prepare("SELECT sum(`in_amount`) - sum(`out_amount`) AS `balance`  FROM `account_transection`  WHERE ( `transection_date` BETWEEN '".$DATE_FROM."' AND '".$DATE_TO."' ) AND `transection_to`  = 'Customer' AND `transection_type` = 'INCOME'  $BRUNCH_QUERY GROUP BY `transection_to` ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        if(!empty( $fetch['balance'])){
            $balance = $fetch['balance'];
        }else{
            $balance = 0.00;
        }

      
        return array(
            'balance' => $balance
            );



    }


    static function OTHER_REVENUE($DATE_FROM,$DATE_TO,$BRUNCH){
        $conn_me = Database::getInstance();



        if($BRUNCH == 'All' ){
            $BRUNCH_QUERY = "";
         }else {
           $BRUNCH_QUERY = " AND  `brunch_id` = '".$BRUNCH."' ";
         }


        $query = $conn_me->prepare("SELECT sum(`in_amount`) - sum(`out_amount`) AS `balance`  FROM `account_transection`  WHERE ( `transection_date` BETWEEN '".$DATE_FROM."' AND '".$DATE_TO."' ) AND `transection_to`  <> 'Customer'  AND `transection_type` = 'INCOME'  $BRUNCH_QUERY GROUP BY `transection_to` ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        if(!empty( $fetch['balance'])){
            $balance = $fetch['balance'];
        }else{
            $balance = 0.00;
        }

      
        return array(
            'balance' => $balance
            );



    }





    static function COST_OF_GOODS($DATE_FROM,$DATE_TO,$BRUNCH){
        $conn_me = Database::getInstance();

        if($BRUNCH == 'All' ){
            $BRUNCH_QUERY = "";
         }else {
           $BRUNCH_QUERY = " AND  `brunch_id` = '".$BRUNCH."' ";
         }


         $total_purchase_price = 0 ;
        
        $query = $conn_me->prepare("SELECT id,invoice_date  FROM `sales_invoice`  WHERE ( `invoice_date` BETWEEN '".$DATE_FROM."' AND '".$DATE_TO."' )   $BRUNCH_QUERY  ");
        $query->execute();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($fetch_list as $fetch) { 
        
            $info_invoice = FIND::TOTAL_SALES_ITEM_PURCHASE_PRICE($fetch['id'],$fetch['invoice_date']);
            $total_purchase_price +=   $info_invoice['total_purchase_price'];
           
        
        }

      
        return array(
            'total_purchase_price' => $total_purchase_price
            
            );



    }

    

    static  function TRANSECTION_BY_RANGE_BALANCE($DATE_FROM,$DATE_TO,$SECTION,$TYPE,$BRUNCH){
        $conn_me = Database::getInstance();


        if($TYPE == 'All' ){
           $QURRY = "";
        }else{
            $QURRY = " AND `transection_by_id` = '".$TYPE."' ";

        }

        if($BRUNCH == 'All' ){
            $BRUNCH_QUERY = "";
         }else {
           $BRUNCH_QUERY = " AND  `brunch_id` = '".$BRUNCH."' ";
         }


        $query = $conn_me->prepare("SELECT sum(`in_amount`) - sum(`out_amount`) AS `balance`  FROM `account_transection`  WHERE ( `transection_date` BETWEEN '".$DATE_FROM."' AND '".$DATE_TO."' ) AND `transection_by`  = '".$SECTION."'   $QURRY   $BRUNCH_QUERY GROUP BY `transection_by` ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        if(!empty( $fetch['balance'])){
            $balance = $fetch['balance'];
        }else{
            $balance = 0.00;
        }

      
        return array(
            'balance' => $balance
            );



    }



    static  function TRANSECTION_BY_OPENING_BALANCE($DATE,$SECTION,$BRUNCH){
        $conn_me = Database::getInstance();
        $previous_day = date("Y-m-d", strtotime($DATE . " -1 day"));


        if($BRUNCH == 'All' ){
            $BRUNCH_QUERY = "";
         }else {
           $BRUNCH_QUERY = " AND  `brunch_id` = '".$BRUNCH."' ";
         }
    
 


        $query = $conn_me->prepare("SELECT sum(`in_amount`) - sum(`out_amount`) AS `balance`,group_concat(transection_by_id) as `all_ids` FROM `account_transection`  WHERE `transection_date` <= '".$previous_day."' AND `transection_by`  = '".$SECTION."'  $BRUNCH_QUERY GROUP BY `transection_by` ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        if(!empty( $fetch['balance'])){
            $balance = $fetch['balance'];
        }else{
            $balance = 0.00;
        }

        if(!empty( $fetch['all_ids'])){
            $all_ids = $fetch['all_ids'];
        }else{
            $all_ids = 'No Data';
        }





        return array(
            'balance' => $balance,
            'all_ids' => $all_ids

            );



    }

    static function numberTowords($num)
    { $ones = array(
        1 => "one",
        2 => "two",
        3 => "three",
        4 => "four",
        5 => "five",
        6 => "six",
        7 => "seven",
        8 => "eight",
        9 => "nine",
        10 => "ten",
        11 => "eleven",
        12 => "twelve",
        13 => "thirteen",
        14 => "fourteen",
        15 => "fifteen",
        16 => "sixteen",
        17 => "seventeen",
        18 => "eighteen",
        19 => "nineteen"
        );
        $tens = array(
        1 => "ten",
        2 => "twenty",
        3 => "thirty",
        4 => "forty",
        5 => "fifty",
        6 => "sixty",
        7 => "seventy",
        8 => "eighty",
        9 => "ninety"
        );
        $hundreds = array(
        "hundred",
        "thousand",
        "million",
        "billion",
        "trillion",
        "quadrillion"
        );
        $num = number_format($num,2,".",",");
        $num_arr = explode(".",$num);
        $wholenum = $num_arr[0];
        $decnum = $num_arr[1];
        $whole_arr = array_reverse(explode(",",$wholenum));
        krsort($whole_arr);
        $words = "";
        foreach($whole_arr as $key => $i) {
        if($i == 0) {
        continue;
        }
        if($i < 20) {
        $words .= $ones[intval($i)];
        } elseif($i < 100) {
        if(substr($i,0,1) == 0 && strlen($i) == 3) {
        $words .= $tens[substr($i,1,1)];
        if(substr($i,2,1) != 0) {
        $words .= " ".$ones[substr($i,2,1)];
        }
        } else {
        $words .= $tens[substr($i,0,1)];
        if(substr($i,1,1) != 0) {
        $words .= " ".$ones[substr($i,1,1)];
        }
        }
        } else {
        // $words .= $ones[substr($i,0,1)]." ".$hundreds[0].' and ';
        if(substr($i,1,1) != 0 || substr($i,2,1) != 0) {
        $words .= $ones[substr($i,0,1)]." ".$hundreds[0].' and ';
        } else {
        $words .= $ones[substr($i,0,1)]." ".$hundreds[0];
        }
        if(substr($i,1,2) < 20 && substr($i,1,1) != 0) {
        $words .= " ".$ones[(substr($i,1,2))];
        } else {
        if(substr($i,1,1) != 0) {
        $words .= " ".$tens[substr($i,1,1)];
        }
        if(substr($i,2,1) != 0) {
        $words .= " ".$ones[substr($i,2,1)];
        }
        }
        }
        if($key > 0) {
        $words .= " ".$hundreds[$key]." ";
        }
        }
        $words .= $unit??' TK ONLY';
        if($decnum > 0) {
        $words .= " and ";
        if($decnum < 20) {
        $words .= $ones[intval($decnum)];
        } elseif($decnum < 100) {
        $words .= $tens[substr($decnum,0,1)];
        if(substr($decnum,1,1) != 0) {
        $words .= " ".$ones[substr($decnum,1,1)];
        }
        }
        $words .= $subunit??' subunits';
        }
        return $words;
    } 


    
    static function ADVANCE_LIMIT($year) {
    

        $conn_me = Database::getInstance();

        $ck1 = $conn_me->prepare("SELECT * FROM `setup_advance` ");
        $ck1->execute();
        $fetch = $ck1->fetch(PDO::FETCH_ASSOC);

        if($year <= 1 ){
           return  $fetch['less_then_1_year'];
        }else if ($year >  1 && $year <= 2 ){
            return  $fetch['more_then_1_less_then_2'];
        }else if ($year >  2 && $year <= 3 ){
            return  $fetch['more_then_2_less_then_3'];
        }else if ($year >  3 && $year <= 4 ){
            return  $fetch['more_then_3_less_then_4'];
        }else if ($year >  4 && $year <= 5 ){
            return  $fetch['more_then_4_less_then_5'];
        }else if ($year >  5 ){
            return  $fetch['more_then_5'];

        }else{
            return 0.00;
        }
    
    }



    static function canTakeCasualLeave($leaveCount, $currentMonth) {
        // Maximum casual leave days per year
        $maxDaysPerYear = 12;
        
        // Calculate the number of months remaining in the year
        $remainingMonths = 12 - $currentMonth + 1; // +1 to include the current month
        
        // Calculate the maximum casual leave days for the remaining months
        $maxDaysRemaining = $remainingMonths;
        if ($leaveCount + $maxDaysRemaining > $maxDaysPerYear) {
            $maxDaysRemaining = $maxDaysPerYear - $leaveCount;
        }
        
        // Check if the employee can take a leave
        return $maxDaysRemaining >= 1;
    }


    


static function getCurrentMenuId($LINK) {

        $conn_me = Database::getInstance();

$query1 = $conn_me->prepare("SELECT A.* FROM `menu_list` A  
JOIN `menu_permission` B  on (A.`id` = B.`menu_id`)  
JOIN `admin` C  on (B.`employee_id` = C.`employee_id`)  
 where CONCAT('/',A.`menu_link`)  = '".$LINK."'  AND B.`view_check` = 'Checked' AND C.`id` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."'  ");
$query1->execute();
$fetch = $query1->fetch(PDO::FETCH_ASSOC);
if($query1->rowCount() > 0 ){
    $menu_id = $fetch['id'];
    $parents = $fetch['time'];
}else{
    $menu_id = 'NOPERMISSION';
    $parents = 'NOPERMISSION';
}
      
   
            return array(
                'menu_id' => $menu_id,
                'parents' => $parents
    
                );
    
    
        
    }


    static function SALES_INVOICE_STATUS($invoice_id) {

        $conn_me = Database::getInstance();

        $query1 = $conn_me->prepare("SELECT 
        CASE 
            WHEN `status` = 'Done' AND  `confirm_by_sales_manager` = 'Pending' THEN 'Waitting for Sales manager approval'
            WHEN `status` = 'Done' AND  `confirm_by_sales_manager` = 'Done' AND `warehouse_dispatch` = 'Pending' THEN 'Waitting for Warehouse Manager Product Delivery'
            WHEN `status` = 'Done' AND  `confirm_by_sales_manager` = 'Done' AND `send_to_sales_person_for_approval` = 'Done' AND `warehouse_dispatch` = 'Pending' THEN 'Waitting for sales person approval'
            WHEN `status` = 'Done' AND  `confirm_by_sales_manager` = 'Done' AND `warehouse_dispatch` = 'Done' THEN 'Warehouse Dispatch Done'
            ELSE 'N/A'
        END as status
        FROM sales_invoice where id='".$invoice_id."'");
        $query1->execute();
        $fetch = $query1->fetch(PDO::FETCH_ASSOC);
       


        return array(
            'status' => $fetch['status']

            );


    }



    static function LAST_PURCHASED($item_id) {

        $conn_me = Database::getInstance();

        $query1 = $conn_me->prepare("SELECT *,date_format(invoice_date, '%d-%m-%Y') AS `invoice_date` FROM `history_local_fg_purches` where product_id = '".$item_id."' order by invoice_date DESC LIMIT 1");
        $query1->execute();
        $fetch = $query1->fetch(PDO::FETCH_ASSOC);
       
        if(!empty($fetch['receive_quantity'])){ $qty = $fetch['receive_quantity'] ; }else{ $qty = 0.00;}
        if(!empty($fetch['invoice_date'])){ $invoice_date = $fetch['invoice_date'] ; }else{ $invoice_date = '';}


        return array(
            'receive_quantity' => $qty,
            'invoice_date' => $invoice_date

            );


    }


    static function LAST_SOLD($item_id) {

        $conn_me = Database::getInstance();

        $query1 = $conn_me->prepare("SELECT product_id, MAX(date) AS last_sold_date
        FROM sold_item 
        WHERE date BETWEEN <start_date> AND <end_date>
        GROUP BY product_id;
        ");
        $query1->execute();
        $fetch = $query1->fetch(PDO::FETCH_ASSOC);
       
        if(!empty($fetch['receive_quantity'])){ $qty = $fetch['receive_quantity'] ; }else{ $qty = 0.00;}
        if(!empty($fetch['invoice_date'])){ $invoice_date = $fetch['invoice_date'] ; }else{ $invoice_date = '';}


        return array(
            'receive_quantity' => $qty,
            'invoice_date' => $invoice_date

            );


    }





      
      

    static function THIS_FROM_THAT_HISTORY($TABLE,$filed1,$filed_data1){

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `{$TABLE}`  WHERE `{$filed1}` = '".$filed_data1."' GROUP BY `{$filed1}` ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        if(!empty($fetch['invoice_no'])){ $invoice_no = $fetch['invoice_no'];}else{ $invoice_no = '' ;}
        if(!empty($fetch['code'])){ $code = $fetch['code'];}else{ $code = '' ;}



        return array(

            'code' => $code,
            'invoice_no' => $invoice_no

            );




        return array(
            'id' => $fetch['id'],
            'category' => $fetch['category']

            );

    }

    static function CASH_VIEW($BRANCH){




        $conn_me = Database::getInstance();

        if($BRANCH == "All"){ $QUERY = "" ;}else{  $QUERY = " AND `brunch_id` = '".$BRANCH."' " ; }

        $query1 = $conn_me->prepare("SELECT sum(`in_amount`) - sum(`out_amount`) as `cash_balance` FROM `account_transection`  WHERE `transection_by` = 'Cash' $QUERY  group by `transection_by` ");
        $query1->execute();
        $fetch1 = $query1->fetch(PDO::FETCH_ASSOC);
        if(!empty($fetch1['cash_balance'])){ $cash_balance = $fetch1['cash_balance'];}else{ $cash_balance = 0.00 ;}

        $query2 = $conn_me->prepare("SELECT sum(`in_amount`) - sum(`out_amount`) as `bank_balance` FROM `account_transection`  WHERE `transection_by` = 'Bank' $QUERY group by `transection_by`  ");
        $query2->execute();
        $fetch2 = $query2->fetch(PDO::FETCH_ASSOC);
        if(!empty($fetch2['bank_balance'])){ $bank_balance = $fetch2['bank_balance'];}else{ $bank_balance = 0.00 ;}

        $query3 = $conn_me->prepare("SELECT sum(`in_amount`) - sum(`out_amount`) as `mobile_balance` FROM `account_transection`  WHERE `transection_by` = 'Mobile-Banking' $QUERY group by `transection_by`  ");
        $query3->execute();
        $fetch3 = $query3->fetch(PDO::FETCH_ASSOC);
        if(!empty($fetch3['mobile_balance'])){ $mobile_balance = $fetch3['mobile_balance'];}else{ $mobile_balance = 0.00 ;}

        $toal_balance = $cash_balance+$bank_balance+$mobile_balance;
        return array(
            'cash_balance' => $cash_balance,
            'bank_balance' => $bank_balance,
            'mobile_balance' => $mobile_balance,
            'total_balance' => $toal_balance

            );




    }

    static function DATE_WISE_CASH($FROM,$TO,$TYPE,$SECTION){

        $conn_me = Database::getInstance();

        $query1 = $conn_me->prepare("SELECT sum(`{$TYPE}`) as `total_amount` FROM `account_transection`  WHERE  `transection_type` = '{$SECTION}'  AND (`transection_date` BETWEEN '".$FROM."' AND '".$TO."')  group by `transection_type` ");
        $query1->execute();
        $fetch1 = $query1->fetch(PDO::FETCH_ASSOC);
        if(!empty($fetch1['total_amount'])){ $total_amount = $fetch1['total_amount'];}else{ $total_amount = 0.00 ;}

        return array(
            'total_amount' => $total_amount

            );


    }


    static  function RAW_CLOSING_MOVEMENT($ID,$FROM){

        $conn_me = Database::getInstance();

        $date_to = date("Y-m-d", strtotime($FROM));
        
        if($ID == '' ){
            $CONDITION = "  ";
        }else{
            $CONDITION = " `product_id`  =  '".$ID."' AND ";  
        }

        $total_in = 0;
        $total_out = 0;

$ck1 = $conn_me->prepare("SELECT   `quantity`  FROM `raw_opening_stock` where $CONDITION `status` = 'Done' AND ( `invoice_date` < '".$date_to."' )  ");
$ck1->execute();

$fe_ck1 = $ck1->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck1 AS $fetch1){
$total_in += $fetch1['quantity'];
}

        



$ck2 = $conn_me->prepare("SELECT `receive_quantity`  FROM `history_local_raw_purches`  where $CONDITION ( `invoice_date` < '".$date_to."' ) AND `reject_quantity` IS NULL  ");
$ck2->execute();
$fe_ck2 = $ck2->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck2 AS $fetch2){

$total_in += $fetch2['receive_quantity'];

}



$ck3 = $conn_me->prepare("SELECT  `reject_quantity`   FROM `history_local_raw_purches` where $CONDITION  ( `invoice_date` < '".$date_to."' ) AND `receive_quantity` IS NULL ");
$ck3->execute();
$fe_ck3 = $ck3->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck3 AS $fetch3){

    $total_out += $fetch3['reject_quantity'];

}
        

$ck4 = $conn_me->prepare("SELECT  `dispatch_quantity` FROM `history_mold_raw_item_dispatch`  where $CONDITION  ( `invoice_date` < '".$date_to."' )  ");
$ck4->execute();

$fe_ck4 = $ck4->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck4 AS $fetch4){

$total_out += $fetch4['dispatch_quantity'];

}


$ck5 = $conn_me->prepare("SELECT `receive_quantity` FROM `history_receive_raw_after_mold`  where $CONDITION ( `invoice_date` <  '".$date_to."' ) ");
$ck5->execute();

$fe_ck5 = $ck5->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck5 AS $fetch5){

$total_in += $fetch5['receive_quantity'];

}

    
$ck6 = $conn_me->prepare("SELECT `return_quantity` FROM `history_receive_raw_after_mold`  where $CONDITION ( `invoice_date` < '".$date_to."' ) AND `invoice_date` = 'Done' ");
$ck6->execute();

$fe_ck6 = $ck6->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck6 AS $fetch6){


$total_out += $fetch6['return_quantity'];


}

$ck7 = $conn_me->prepare("SELECT `dispatch_quantity` FROM `history_print_raw_item_dispatch`  where $CONDITION ( `invoice_date` < '".$date_to."' ) ");
$ck7->execute();

$fe_ck7 = $ck7->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck7 AS $fetch7){

$total_out += $fetch7['dispatch_quantity'];

}


$ck8 = $conn_me->prepare("SELECT `dispatch_quantity` FROM `history_print_item_dispatch`  where $CONDITION ( `invoice_date` < '".$date_to."' ) ");
$ck8->execute();

$fe_ck8 = $ck8->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck8 AS $fetch8){

$total_out += $fetch8['dispatch_quantity'];

}


$ck9 = $conn_me->prepare("SELECT `receive_quantity` FROM `history_receive_raw_after_print`  where $CONDITION ( `invoice_date` < '".$date_to."' ) ");
$ck9->execute();

$fe_ck9 = $ck9->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck9 AS $fetch9){

$total_in += $fetch9['receive_quantity'];

}

$ck99 = $conn_me->prepare("SELECT `return_quantity` FROM `history_receive_raw_after_print`  where $CONDITION ( `invoice_date` < '".$date_to."' ) ");
$ck99->execute();

$fe_ck99 = $ck99->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck99 AS $fetch99){

$total_out += $fetch99['return_quantity'];

}




$ck10 = $conn_me->prepare("SELECT `dispatch_quantity` FROM `history_spray_raw_item_dispatch`  where $CONDITION ( `invoice_date` < '".$date_to."' ) ");
$ck10->execute();

$fe_ck10 = $ck10->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck10 AS $fetch10){

$total_out += $fetch10['dispatch_quantity'];

}

$ck11 = $conn_me->prepare("SELECT `dispatch_quantity` FROM `history_spray_item_dispatch`  where $CONDITION ( `invoice_date` < '".$date_to."' ) ");
$ck11->execute();

$fe_ck11 = $ck11->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck11 AS $fetch11){

$total_out += $fetch11['dispatch_quantity'];

}


$ck12 = $conn_me->prepare("SELECT `receive_quantity` FROM `history_receive_raw_after_spray`  where $CONDITION ( `invoice_date` < '".$date_to."' ) ");
$ck12->execute();

$fe_ck12 = $ck12->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck12 AS $fetch12){

$total_in += $fetch12['receive_quantity'];

}


$ck122 = $conn_me->prepare("SELECT `return_quantity` FROM `history_receive_raw_after_spray`  where $CONDITION ( `invoice_date` < '".$date_to."' ) ");
$ck122->execute();

$fe_ck122 = $ck122->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck122 AS $fetch122){

$total_out += $fetch122['return_quantity'];

}


$ck13 = $conn_me->prepare("SELECT `dispatch_quantity` FROM `history_receipe_wise_item_dispatch`  where $CONDITION ( `invoice_date` < '".$date_to."' ) ");
$ck13->execute();

$fe_ck13 = $ck13->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck13 AS $fetch13){

$total_out += $fetch13['dispatch_quantity'];

}



$ck14 = $conn_me->prepare("SELECT `return_quantity` FROM `history_receipe_wise_item_dispatch`  where $CONDITION ( `invoice_date` < '".$date_to."' ) ");
$ck14->execute();

$fe_ck14 = $ck14->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck14 AS $fetch14){

        $total_out += $fetch14['return_quantity'];
      
}







    
$closing =  $total_in-$total_out;

$prev_date = date('Y-m-d', strtotime($FROM .' -1 day'));
$pdate = date("d-m-Y", strtotime($prev_date));

return array(
    'note' => "Closing Balance",
    'closing' =>  $closing,
    'date' =>  $prev_date

);




    }





    static  function FG_CLOSING_MOVEMENT($ID,$RELATED_ID,$DATE,$TYPE){

        $conn_me = Database::getInstance();

        $date = date("Y-m-d", strtotime($DATE));
        

        
    if($TYPE == 'Multipal-Warehouse-Wise' ){
    $selectedValues = implode(',', $RELATED_ID);      
      
    $CONDITION = "  `product_id` = '" . $ID . "' AND `warehouse_id`  IN ({$selectedValues})   ";

    }else if($TYPE == 'Multipal-Branch-Wise') {
    $selectedValues = implode(',', $RELATED_ID);        
    $qry = $conn_me->prepare("SELECT * FROM `setup_brunch` where `id`  IN ({$selectedValues})  ");
    $qry->execute();
    $fetch_list = $qry->fetch(PDO::FETCH_ASSOC);
    $a = $fetch_list['related_warehouse'];
    $b = str_replace("[","",$a);
    $c = str_replace("]","",$b);
    $CONDITION = "  `product_id` = '" . $ID . "' AND `warehouse_id` IN ($c)  ";
    }else{
    $CONDITION = " `product_id` = '" . $ID . "'  " ;
    }


        $total_in = 0;
        $total_out = 0;

        $ck1 = $conn_me->prepare("SELECT  SUM(`stock_in`) AS `stockin`,SUM(`stock_out`) AS `stockout`  FROM `balance_product` where $CONDITION  AND  ( `date` < '".$date."' ) group by `product_id` ");
        $ck1->execute();
        $fe_ck1 = $ck1->fetch(PDO::FETCH_ASSOC);

         if(!empty($fe_ck1['stockin'])){ $total_in = $fe_ck1['stockin'] ;}else{ $total_in = 0.00 ;}
         if(!empty($fe_ck1['stockout'])){ $total_out = $fe_ck1['stockout'] ;}else{ $total_out = 0.00 ;}

         $closing =  $total_in-$total_out;

        $prev_date = date('Y-m-d', strtotime($date .' -1 day'));
        $pdate = date("d-m-Y", strtotime($prev_date));

return array(
    'note' => "Closing Balance",
    'closing' =>  $closing,
    'date' =>  $prev_date

);

    
    }



    static function RAW_MOVEMENT($ID,$FROM,$TO){
        $conn_me = Database::getInstance();

        $movement_array = [];
        $count = 0;


        $date_from = date("Y-m-d", strtotime($FROM));
        $date_to = date("Y-m-d", strtotime($TO));


        if($ID == '' ){
        $CONDITION = "  ";
        }else{
        $CONDITION = " `product_id`  =  '".$ID."' AND ";  
        }
            
        $closing_balance = FIND::RAW_CLOSING_MOVEMENT($ID,$FROM);

    $count  = $count+1;
    $movement_array[$count]['serial'] = $count;
    $movement_array[$count]['date'] = $closing_balance['date'];
    $movement_array[$count]['time'] = '07:24:22 am';
    $movement_array[$count]['qty'] = $closing_balance['closing'];
    $movement_array[$count]['description'] = $closing_balance['note'];
    $movement_array[$count]['type'] = 'IN';


    $ck1 = $conn_me->prepare("SELECT  *   FROM `raw_opening_stock` where $CONDITION `status` = 'Done' AND ( `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' )  ");
$ck1->execute();

$fe_ck1 = $ck1->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck1 AS $fetch1){

    $count  = $count+1;
    $movement_array[$count]['serial'] = $count;
    $movement_array[$count]['date'] = $fetch1['invoice_date'];
    $movement_array[$count]['time'] = $fetch1['time'];
    $movement_array[$count]['qty'] = $fetch1['quantity'];
    $movement_array[$count]['description'] = "Opening Stock - $fetch1[invoice_no]";
    $movement_array[$count]['type'] = 'IN';


}

        



$ck2 = $conn_me->prepare("SELECT *  FROM `history_local_raw_purches`  where $CONDITION ( `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ) AND `reject_quantity` IS NULL  ");
$ck2->execute();
$fe_ck2 = $ck2->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck2 AS $fetch2){

    $info2= FIND::THIS_FROM_THAT_HISTORY('raw_local_purches','code',$fetch2['code']);

    $count  = $count+1;
    $movement_array[$count]['serial'] = $count;
    $movement_array[$count]['date'] = $fetch2['invoice_date'];
    $movement_array[$count]['time'] = $fetch2['time'];
    $movement_array[$count]['qty'] = $fetch2['receive_quantity'];
    $movement_array[$count]['description'] = "Local Purchase - $info2[invoice_no]";
    $movement_array[$count]['type'] = 'IN';


}



$ck3 = $conn_me->prepare("SELECT  *   FROM `history_local_raw_purches` where $CONDITION  ( `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ) AND `receive_quantity` IS NULL ");
$ck3->execute();
$fe_ck3 = $ck3->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck3 AS $fetch3){

    $info3= FIND::THIS_FROM_THAT_HISTORY('raw_local_purches','code',$fetch3['code']);

    $count  = $count+1;
    $movement_array[$count]['serial'] = $count;
    $movement_array[$count]['date'] = $fetch3['invoice_date'];
    $movement_array[$count]['time'] = $fetch3['time'];
    $movement_array[$count]['qty'] = $fetch3['reject_quantity'];
    $movement_array[$count]['description'] = "Local Purchase Return- $info2[invoice_no]";
    $movement_array[$count]['type'] = 'OUT';

}
        

$ck4 = $conn_me->prepare("SELECT  * FROM `history_mold_raw_item_dispatch`  where $CONDITION  ( `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' )  ");
$ck4->execute();

$fe_ck4 = $ck4->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck4 AS $fetch4){

    $info4= FIND::THIS_FROM_THAT_HISTORY('raw_molding','code',$fetch4['code']);


    $count  = $count+1;
    $movement_array[$count]['serial'] = $count;
    $movement_array[$count]['date'] = $fetch4['invoice_date'];
    $movement_array[$count]['time'] = $fetch4['time'];
    $movement_array[$count]['qty'] = $fetch4['dispatch_quantity'];
    $movement_array[$count]['description'] = "Send For Molding - $info4[invoice_no]";
    $movement_array[$count]['type'] = 'OUT';


}


$ck5 = $conn_me->prepare("SELECT *  FROM `history_receive_raw_after_mold`  where $CONDITION ( `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ) ");
$ck5->execute();

$fe_ck5 = $ck5->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck5 AS $fetch5){

    $info5= FIND::THIS_FROM_THAT_HISTORY('raw_molding','code',$fetch5['code']);


    $count  = $count+1;
    $movement_array[$count]['serial'] = $count;
    $movement_array[$count]['date'] = $fetch5['invoice_date'];
    $movement_array[$count]['time'] = $fetch5['time'];
    $movement_array[$count]['qty'] = $fetch5['receive_quantity'];
    $movement_array[$count]['description'] = "Receive After Molding - $info5[invoice_no]";
    $movement_array[$count]['type'] = 'IN';

}

    
$ck6 = $conn_me->prepare("SELECT * FROM `history_receive_raw_after_mold`  where $CONDITION ( `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ) AND `invoice_date` = 'Done' ");
$ck6->execute();

$fe_ck6 = $ck6->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck6 AS $fetch6){

    $info6= FIND::THIS_FROM_THAT_HISTORY('raw_molding','code',$fetch6['code']);

    $count  = $count+1;
    $movement_array[$count]['serial'] = $count;
    $movement_array[$count]['date'] = $fetch6['invoice_date'];
    $movement_array[$count]['time'] = $fetch6['time'];
    $movement_array[$count]['qty'] = $fetch6['return_quantity'];
    $movement_array[$count]['description'] = "Return After Molding - $info6[invoice_no]";
    $movement_array[$count]['type'] = 'OUT';


}

$ck7 = $conn_me->prepare("SELECT * FROM `history_print_raw_item_dispatch`  where $CONDITION ( `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ) ");
$ck7->execute();

$fe_ck7 = $ck7->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck7 AS $fetch7){

    $info7= FIND::THIS_FROM_THAT_HISTORY('raw_molding','code',$fetch7['code']);


    $count  = $count+1;
    $movement_array[$count]['serial'] = $count;
    $movement_array[$count]['date'] = $fetch7['invoice_date'];
    $movement_array[$count]['time'] = $fetch7['time'];
    $movement_array[$count]['qty'] = $fetch7['dispatch_quantity'];
    $movement_array[$count]['description'] = "Dispatch Raw Item For Printing - $info7[invoice_no]";
    $movement_array[$count]['type'] = 'OUT';

}


$ck8 = $conn_me->prepare("SELECT * FROM `history_print_item_dispatch`  where $CONDITION ( `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ) ");
$ck8->execute();

$fe_ck8 = $ck8->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck8 AS $fetch8){

    $info8= FIND::THIS_FROM_THAT_HISTORY('raw_print','code',$fetch8['code']);


    $count  = $count+1;
    $movement_array[$count]['serial'] = $count;
    $movement_array[$count]['date'] = $fetch8['invoice_date'];
    $movement_array[$count]['time'] = $fetch8['time'];
    $movement_array[$count]['qty'] = $fetch8['dispatch_quantity'];
    $movement_array[$count]['description'] = "Dispatch Item For Printing - $info8[invoice_no]";
    $movement_array[$count]['type'] = 'OUT';

}


$ck9 = $conn_me->prepare("SELECT *  FROM `history_receive_raw_after_print`  where $CONDITION ( `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ) ");
$ck9->execute();

$fe_ck9 = $ck9->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck9 AS $fetch9){

    $info9= FIND::THIS_FROM_THAT_HISTORY('raw_print','code',$fetch9['code']);



    $count  = $count+1;
    $movement_array[$count]['serial'] = $count;
    $movement_array[$count]['date'] = $fetch9['invoice_date'];
    $movement_array[$count]['time'] = $fetch9['time'];
    $movement_array[$count]['qty'] = $fetch9['receive_quantity'];
    $movement_array[$count]['description'] = "Receive After Printing - $info9[invoice_no]";
    $movement_array[$count]['type'] = 'IN';

}

$ck99 = $conn_me->prepare("SELECT *  FROM `history_receive_raw_after_print`  where $CONDITION ( `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ) ");
$ck99->execute();

$fe_ck99 = $ck99->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck99 AS $fetch99){

    $info99= FIND::THIS_FROM_THAT_HISTORY('raw_print','code',$fetch99['code']);


    $count  = $count+1;
    $movement_array[$count]['serial'] = $count;
    $movement_array[$count]['date'] = $fetch99['invoice_date'];
    $movement_array[$count]['time'] = $fetch99['time'];
    $movement_array[$count]['qty'] = $fetch99['return_quantity'];
    $movement_array[$count]['description'] = "Return After Printing - $info99[invoice_no]";
    $movement_array[$count]['type'] = 'OUT';

}




$ck10 = $conn_me->prepare("SELECT *  FROM `history_spray_raw_item_dispatch`  where $CONDITION ( `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ) ");
$ck10->execute();

$fe_ck10 = $ck10->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck10 AS $fetch10){

    $count  = $count+1;
    $movement_array[$count]['serial'] = $count;
    $movement_array[$count]['date'] = $fetch10['invoice_date'];
    $movement_array[$count]['time'] = $fetch10['time'];
    $movement_array[$count]['qty'] = $fetch10['dispatch_quantity'];
    $movement_array[$count]['description'] = "Distaptch Raw Item for Spray - $fetch10[invoice_no]";
    $movement_array[$count]['type'] = 'OUT';

}

$ck11 = $conn_me->prepare("SELECT *  FROM `history_spray_item_dispatch`  where $CONDITION ( `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ) ");
$ck11->execute();

$fe_ck11 = $ck11->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck11 AS $fetch11){

    $count  = $count+1;
    $movement_array[$count]['serial'] = $count;
    $movement_array[$count]['date'] = $fetch11['invoice_date'];
    $movement_array[$count]['time'] = $fetch11['time'];
    $movement_array[$count]['qty'] = $fetch11['dispatch_quantity'];
    $movement_array[$count]['description'] = "Distaptch Item for Spray - $fetch11[invoice_no]";
    $movement_array[$count]['type'] = 'OUT';

}


$ck12 = $conn_me->prepare("SELECT * FROM `history_receive_raw_after_spray`  where $CONDITION ( `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ) ");
$ck12->execute();

$fe_ck12 = $ck12->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck12 AS $fetch12){

    $count  = $count+1;
    $movement_array[$count]['serial'] = $count;
    $movement_array[$count]['date'] = $fetch12['invoice_date'];
    $movement_array[$count]['time'] = $fetch12['time'];
    $movement_array[$count]['qty'] = $fetch12['receive_quantity'];
    $movement_array[$count]['description'] = "Receive Item After Spray - $fetch12[invoice_no]";
    $movement_array[$count]['type'] = 'IN';

}


$ck122 = $conn_me->prepare("SELECT *  FROM `history_receive_raw_after_spray`  where $CONDITION ( `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ) ");
$ck122->execute();

$fe_ck122 = $ck122->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck122 AS $fetch122){

    $count  = $count+1;
    $movement_array[$count]['serial'] = $count;
    $movement_array[$count]['date'] = $fetch122['invoice_date'];
    $movement_array[$count]['time'] = $fetch122['time'];
    $movement_array[$count]['qty'] = $fetch122['return_quantity'];
    $movement_array[$count]['description'] = "Return Item After Spray - $fetch122[invoice_no]";
    $movement_array[$count]['type'] = 'OUT';

}


$ck13 = $conn_me->prepare("SELECT * FROM `history_receipe_wise_item_dispatch`  where $CONDITION ( `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ) AND `dispatch_quantity` > 0 ");
$ck13->execute();

$fe_ck13 = $ck13->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck13 AS $fetch13){

    $info13= FIND::THIS_FROM_THAT_HISTORY('raw_request_recipe_wise','code',$fetch13['code']);


    $count  = $count+1;
    $movement_array[$count]['serial'] = $count;
    $movement_array[$count]['date'] = $fetch13['invoice_date'];
    $movement_array[$count]['time'] = $fetch13['time'];
    $movement_array[$count]['qty'] = $fetch13['dispatch_quantity'];
    $movement_array[$count]['description'] = "Receipe Wise Item Dispatch - $info13[invoice_no]";
    $movement_array[$count]['type'] = 'OUT';

}



$ck14 = $conn_me->prepare("SELECT * FROM `history_receipe_wise_item_dispatch`  where $CONDITION ( `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ) AND `return_quantity` > 0 ");
$ck14->execute();

$fe_ck14 = $ck14->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck14 AS $fetch14){

    $info14= FIND::THIS_FROM_THAT_HISTORY('raw_request_recipe_wise','code',$fetch14['code']);


    $count  = $count+1;
    $movement_array[$count]['serial'] = $count;
    $movement_array[$count]['date'] = $fetch14['invoice_date'];
    $movement_array[$count]['time'] = $fetch14['time'];
    $movement_array[$count]['qty'] = $fetch14['return_quantity'];
    $movement_array[$count]['description'] = "Receipe Wise Item Retuen - $info14[invoice_no]";
    $movement_array[$count]['type'] = 'OUT';

      
}




$stm = $conn_me->prepare("DROP table IF EXISTS tempTable_raw_movement");
$stm->execute();

$stmt2 = $conn_me->prepare("CREATE TABLE tempTable_raw_movement (
    id int NOT NULL AUTO_INCREMENT,
    movment_date date  NULL,
    time varchar(255)  NULL,
    note varchar(255)  NULL,
    in_amount float(20,2)  NULL,
    out_amount float(20,2)  NULL,
    PRIMARY KEY (id)
);
");
$stmt2->execute();


foreach($movement_array as $item) {
   
    $date = date("Y-m-d", strtotime($item['date']));



    $query = $conn_me->exec("INSERT INTO `tempTable_raw_movement` 
    ( 
        `id`, `movment_date`,`time`,`note`,`in_amount`, `out_amount`
    
    ) 
    VALUES
    (
        '0',
        '".$date."',
      '".$item['time']."',
        '".$item['description']."',
         IF('".$item['type']."' = 'IN','".$item['qty']."',0.00),
         IF('".$item['type']."' = 'OUT','".$item['qty']."',0.00)

    ) ");

}
       



    }


    
    static function BRUNCH_LEDGER($ID,$FROM,$TO,$BRUNCH){


        if($BRUNCH == 3 ){
            $warehouses = " IN ('14','15') " ; 
        }else{
            $warehouses =  " NOT IN ('14','15') " ; 
        }


        $conn_me = Database::getInstance();
            
    
            $movement_array = [];
            $count = 0;
            $date_from = date("Y-m-d", strtotime($FROM));
            $date_to = date("Y-m-d", strtotime($TO));
            $prev_date = date('Y-m-d', strtotime($date_from .' -1 day'));
    
        
          //  $closing_balance = FIND::BRUNCHERDUE($ID,$BRUNCH,$prev_date);

            //$count  = $count+1;
            //$movement_array[$count]['serial'] = $count;
            //$movement_array[$count]['date'] = $FROM;
            //$movement_array[$count]['time'] = '07:24:22 am';
            //$movement_array[$count]['qty'] = $closing_balance['customer_due'];
            //$movement_array[$count]['description'] = 'Closing Balance';
            //$movement_array[$count]['type'] = 'IN_AMOUNT';
            //$movement_array[$count]['link'] = 'No LInk';
            
                

            
               
     $ck5 = $conn_me->prepare("SELECT si.invoice_date,si.time,si.invoice_no,si.code,
     SUM(sii.sales_rate * sii.sales_quantity + si.discount + si.transport_cost + si.total_vat_cost) AS total_invoice_price
 FROM 
     sales_invoice si
 JOIN 
     sales_invoice_item sii ON si.id = sii.sales_invoice_id
 WHERE 
     si.invoice_date BETWEEN '".$date_from."' AND '".$date_to."'
     AND si.brunch_id = '".$BRUNCH."'
     AND si.generate_challan = 'Done';
  ");
     $ck5->execute();
     
     $fe_ck5 = $ck5->fetchAll(PDO::FETCH_ASSOC);
     foreach($fe_ck5 AS $fetch5){
     

            $count  = $count+1;
             $movement_array[$count]['serial'] = $count;
             $movement_array[$count]['date'] = $fetch5['invoice_date'];
             $movement_array[$count]['time'] = $fetch5['time'];
             $movement_array[$count]['qty'] = $fetch5['total_invoice_price'];
             $movement_array[$count]['description'] = "Invoice >>  $fetch5[invoice_no] ";
             $movement_array[$count]['type'] = 'IN_AMOUNT';
             $movement_array[$count]['link'] = "invoice_copy.php?code=$fetch5[code]";
     
           
     }



     $ck9 = $conn_me->prepare("SELECT A.transection_date,A.time,A.in_amount,
     CONCAT(C.name , ' > ', B.account_head) AS TR_DETAILS, A.transection_by AS description


      FROM `account_transection` A 
      JOIN setup_ac_head B ON (A.transection_head_id = B.id)
      JOIN setup_ladger_head C ON (B.ledger_id = C.id)

       where

       ( A.`transection_date` BETWEEN '".$date_from."' AND '".$date_to."' ) 
       AND A.transection_by =  'Cash'
        AND A.`data_inserted_from` = 'ADD INCOME'  AND A.brunch_id = '".$BRUNCH."' "  );

     $ck9->execute();
     
     $fe_ck9 = $ck9->fetchAll(PDO::FETCH_ASSOC);
     foreach($fe_ck9 AS $fetch9){
             

            
             $count  = $count+1;
             $movement_array[$count]['serial'] = $count;
             $movement_array[$count]['date'] = $fetch9['transection_date'];
             $movement_array[$count]['time'] = $fetch9['time'];
             $movement_array[$count]['qty'] = $fetch9['in_amount'];
             $movement_array[$count]['description'] = "Income: " . $fetch9['TR_DETAILS'] . ' > ' .  $fetch9['description'];
             $movement_array[$count]['type'] = 'IN_AMOUNT';
             $movement_array[$count]['link'] = 'No LInk';
     }


     $ck9 = $conn_me->prepare("SELECT A.transection_date,A.time,A.in_amount,
     CONCAT(C.name , ' > ', B.account_head) AS TR_DETAILS, A.transection_by  AS description


      FROM `account_transection` A 
      JOIN setup_ac_head B ON (A.transection_head_id = B.id)
      JOIN setup_ladger_head C ON (B.ledger_id = C.id)

       where

       ( A.`transection_date` BETWEEN '".$date_from."' AND '".$date_to."' ) 
        AND A.transection_by =  'Cash'
        AND A.`data_inserted_from` = 'CLOSING-BALANCE'  AND A.brunch_id = '".$BRUNCH."' "  );

     $ck9->execute();
     
     $fe_ck9 = $ck9->fetchAll(PDO::FETCH_ASSOC);
     foreach($fe_ck9 AS $fetch9){
             

            
             $count  = $count+1;
             $movement_array[$count]['serial'] = $count;
             $movement_array[$count]['date'] = $fetch9['transection_date'];
             $movement_array[$count]['time'] = $fetch9['time'];
             $movement_array[$count]['qty'] = $fetch9['in_amount'];
             $movement_array[$count]['description'] = "Closing Entry: " . $fetch9['TR_DETAILS'] . ' > ' .  $fetch9['description'];
             $movement_array[$count]['type'] = 'IN_AMOUNT';
             $movement_array[$count]['link'] = 'No LInk';
     }

     $ck6 = $conn_me->prepare("SELECT si.warehouse_receive_date,si.time,si.invoice_no,si.code,
     SUM(sii.sales_rate * sii.return_quantity) AS total_invoice_price
 FROM 
 sales_return_invoice si
 JOIN 
 sales_return_invoice_item sii ON si.id = sii.return_invoice_id
 WHERE 
     si.warehouse_receive_date BETWEEN '".$date_from."' AND '".$date_to."'
     AND si.brunch_id = '".$BRUNCH."'
     AND si.warehouse_receive = 'Done'  ");
     $ck6->execute();
     
     $fe_ck6 = $ck6->fetchAll(PDO::FETCH_ASSOC);
     foreach($fe_ck6 AS $fetch6){
     
             $count  = $count+1;

             $movement_array[$count]['serial'] = $count;
             $movement_array[$count]['date'] = $fetch6['warehouse_receive_date'];
             $movement_array[$count]['time'] = $fetch6['time'];
             $movement_array[$count]['qty'] = $fetch6['total_invoice_price'];
             $movement_array[$count]['description'] = "Sales Return -  $fetch6[invoice_no]";
             $movement_array[$count]['type'] = 'OUT_AMOUNT';
             $movement_array[$count]['link'] = 'No LInk';
     }
     
     $ck9 = $conn_me->prepare("SELECT A.id,CONCAT(C.name , ' > ', B.account_head) AS TR_DETAILS, A.transection_date,A.time,A.out_amount,
     CONCAT('From: ' , E.brunch , ' To: ' , F.brunch) AS br,
     CASE 
     WHEN A.transection_by = 'Bank' THEN ( SELECT CONCAT('Bank: ' , bank_name, ' ' , account_number ) as description FROM setup_bank  where id = A.transection_by_id ) 
     WHEN A.transection_by = 'Mobile-Banking' THEN ( SELECT CONCAT('Mobile: ',mobile_bank_name, ' ' , mobile_number ) as description FROM setup_mobile_banking  where id = A.transection_by_id ) 
     ELSE 'Cash' 
     END AS from_description,

     CASE 
     WHEN D.transection_by = 'Bank' THEN ( SELECT CONCAT('Bank: ' ,bank_name, ' ' , account_number ) as description FROM setup_bank  where id = D.transection_by_id ) 
     WHEN D.transection_by = 'Mobile-Banking' THEN ( SELECT CONCAT('Mobile: ',mobile_bank_name, ' ' , mobile_number ) as description FROM setup_mobile_banking  where id = D.transection_by_id ) 
     ELSE 'Cash' 
     END AS to_description

     
     FROM `account_transection` A 

     JOIN setup_ac_head B ON (A.transection_head_id = B.id)
     JOIN setup_ladger_head C ON (B.ledger_id = C.id)
     JOIN account_transection D ON (A.id = D.transection_id)
     JOIN setup_brunch E ON (A.brunch_id = E.id)
     JOIN setup_brunch F ON (D.brunch_id = F.id)

     where
     ( A.`transection_date` BETWEEN '".$date_from."' AND '".$date_to."' ) AND
     A.`data_inserted_from` = 'MONEY-TRANSFER-FROM'  AND  A.transection_by =  'Cash'  AND A.brunch_id = '".$BRUNCH."' "  );
     $ck9->execute();
  
  $fe_ck9 = $ck9->fetchAll(PDO::FETCH_ASSOC);
  foreach($fe_ck9 AS $fetch9){
  
         
          $count  = $count+1;
          $movement_array[$count]['serial'] = $count;
          $movement_array[$count]['date'] = $fetch9['transection_date'];
          $movement_array[$count]['time'] = $fetch9['time'];
          $movement_array[$count]['qty'] = $fetch9['out_amount'];
          $movement_array[$count]['description'] = "Money Transfer: " . $fetch9['from_description'] . ' > ' .  $fetch9['to_description'] . ' (' .$fetch9['br']. ') ' ;
          $movement_array[$count]['type'] = 'OUT_AMOUNT';
          $movement_array[$count]['link'] = 'No LInk';
  }



  $ck9 = $conn_me->prepare("SELECT A.transection_date,A.time,A.out_amount,
  CONCAT(C.name , ' > ', B.account_head) AS TR_DETAILS, 
  
  CASE 
  WHEN A.transection_by = 'Bank' THEN ( SELECT CONCAT(bank_name, ' ' , account_number ) as description FROM setup_bank  where id = A.transection_by_id ) 
  WHEN A.transection_by = 'Mobile-Banking' THEN ( SELECT CONCAT(mobile_bank_name, ' ' , mobile_number ) as description FROM setup_mobile_banking  where id = A.transection_by_id ) 
  ELSE 'Cash' 
  END AS description


   FROM `account_transection` A 
   JOIN setup_ac_head B ON (A.transection_head_id = B.id)
   JOIN setup_ladger_head C ON (B.ledger_id = C.id)

    where

    ( A.`transection_date` BETWEEN '".$date_from."' AND '".$date_to."' ) 
     AND A.transection_by <>  'Cash' AND A.transection_type =  'EXPENSE'
     AND A.`data_inserted_from` = 'CUSTOMER-TRANSACTION'  AND A.brunch_id = '".$BRUNCH."' "  );

  $ck9->execute();
  
  $fe_ck9 = $ck9->fetchAll(PDO::FETCH_ASSOC);
  foreach($fe_ck9 AS $fetch9){
          

         
          $count  = $count+1;
          $movement_array[$count]['serial'] = $count;
          $movement_array[$count]['date'] = $fetch9['transection_date'];
          $movement_array[$count]['time'] = $fetch9['time'];
          $movement_array[$count]['qty'] = $fetch9['out_amount'];
          $movement_array[$count]['description'] = "Customer Transaction: " . $fetch9['TR_DETAILS'] . ' > ' .  $fetch9['description'];
          $movement_array[$count]['type'] = 'OUT_AMOUNT';
          $movement_array[$count]['link'] = 'No LInk';
  }
  
  $ck9 = $conn_me->prepare("SELECT A.transection_date,A.time,A.in_amount,
  CONCAT(C.name , ' > ', B.account_head) AS TR_DETAILS, 
  
  CASE 
  WHEN A.transection_by = 'Bank' THEN ( SELECT CONCAT(bank_name, ' ' , account_number ) as description FROM setup_bank  where id = A.transection_by_id ) 
  WHEN A.transection_by = 'Mobile-Banking' THEN ( SELECT CONCAT(mobile_bank_name, ' ' , mobile_number ) as description FROM setup_mobile_banking  where id = A.transection_by_id ) 
  ELSE 'Cash' 
  END AS description


   FROM `account_transection` A 
   JOIN setup_ac_head B ON (A.transection_head_id = B.id)
   JOIN setup_ladger_head C ON (B.ledger_id = C.id)

    where

    ( A.`transection_date` BETWEEN '".$date_from."' AND '".$date_to."' ) 
     AND A.transection_by <>  'Cash' AND A.transection_type =  'INCOME'
     AND A.`data_inserted_from` = 'CUSTOMER-TRANSACTION'  AND A.brunch_id = '".$BRUNCH."' "  );

  $ck9->execute();
  
  $fe_ck9 = $ck9->fetchAll(PDO::FETCH_ASSOC);
  foreach($fe_ck9 AS $fetch9){
          
          $count  = $count+1;
          $movement_array[$count]['serial'] = $count;
          $movement_array[$count]['date'] = $fetch9['transection_date'];
          $movement_array[$count]['time'] = $fetch9['time'];
          $movement_array[$count]['qty'] = $fetch9['in_amount'];
          $movement_array[$count]['description'] = "Customer Transaction: " . $fetch9['TR_DETAILS'] . ' > ' .  $fetch9['description'];
          $movement_array[$count]['type'] = 'OUT_AMOUNT';
          $movement_array[$count]['link'] = 'No LInk';
  }
  



  
  $ck9 = $conn_me->prepare("SELECT A.transection_date,A.time,A.in_amount,
  CONCAT(C.name , ' > ', B.account_head) AS TR_DETAILS, 
  
  CASE 
  WHEN A.transection_by = 'Bank' THEN ( SELECT CONCAT(bank_name, ' ' , account_number ) as description FROM setup_bank  where id = A.transection_by_id ) 
  WHEN A.transection_by = 'Mobile-Banking' THEN ( SELECT CONCAT(mobile_bank_name, ' ' , mobile_number ) as description FROM setup_mobile_banking  where id = A.transection_by_id ) 
  ELSE 'Cash' 
  END AS description


   FROM `account_transection` A 
   JOIN setup_ac_head B ON (A.transection_head_id = B.id)
   JOIN setup_ladger_head C ON (B.ledger_id = C.id)

    where

    ( A.`transection_date` BETWEEN '".$date_from."' AND '".$date_to."' ) 
     AND A.transection_by <>  'Cash'
     AND A.`data_inserted_from` = 'Invoice Wise Payment'  AND A.brunch_id = '".$BRUNCH."' "  );

  $ck9->execute();
  
  $fe_ck9 = $ck9->fetchAll(PDO::FETCH_ASSOC);
  foreach($fe_ck9 AS $fetch9){
          
          $count  = $count+1;
          $movement_array[$count]['serial'] = $count;
          $movement_array[$count]['date'] = $fetch9['transection_date'];
          $movement_array[$count]['time'] = $fetch9['time'];
          $movement_array[$count]['qty'] = $fetch9['in_amount'];
          $movement_array[$count]['description'] = "Invoice wise payment: " . $fetch9['TR_DETAILS'] . ' > ' .  $fetch9['description'];
          $movement_array[$count]['type'] = 'OUT_AMOUNT';
          $movement_array[$count]['link'] = 'No LInk';
  }
  


 $ck9 = $conn_me->prepare("SELECT A.transection_date,A.time,A.out_amount,
 CONCAT(C.name , ' > ', B.account_head) AS TR_DETAILS, 
 
 CASE 
 WHEN A.transection_by = 'Bank' THEN ( SELECT CONCAT(bank_name, ' ' , account_number ) as description FROM setup_bank  where id = A.transection_by_id ) 
 WHEN A.transection_by = 'Mobile-Banking' THEN ( SELECT CONCAT(mobile_bank_name, ' ' , mobile_number ) as description FROM setup_mobile_banking  where id = A.transection_by_id ) 
 ELSE 'Cash' 
 END AS description


  FROM `account_transection` A 
  JOIN setup_ac_head B ON (A.transection_head_id = B.id)
  JOIN setup_ladger_head C ON (B.ledger_id = C.id)

   where

   ( A.`transection_date` BETWEEN '".$date_from."' AND '".$date_to."' ) 

    AND A.`data_inserted_from` = 'ADD EXPENSE'  AND A.brunch_id = '".$BRUNCH."' "  );

    
 $ck9->execute();
 
 $fe_ck9 = $ck9->fetchAll(PDO::FETCH_ASSOC);
 foreach($fe_ck9 AS $fetch9){
         

        
         $count  = $count+1;
         $movement_array[$count]['serial'] = $count;
         $movement_array[$count]['date'] = $fetch9['transection_date'];
         $movement_array[$count]['time'] = $fetch9['time'];
         $movement_array[$count]['qty'] = $fetch9['out_amount'];
         $movement_array[$count]['description'] = "Expense: " . $fetch9['TR_DETAILS'] . ' > ' .  $fetch9['description'];
         $movement_array[$count]['type'] = 'OUT_AMOUNT';
         $movement_array[$count]['link'] = 'No LInk';
 }
 
            
            $stm = $conn_me->prepare("DROP table IF EXISTS tempTable_brtunch_ledger_movement");
            $stm->execute();
            
            $stmt2 = $conn_me->prepare("CREATE TABLE tempTable_brtunch_ledger_movement (
                id int NOT NULL AUTO_INCREMENT,
                movment_date date  NULL,
                time varchar(255)  NULL,
                note varchar(255)  NULL,
                IN_AMOUNT float(20,2)  NULL,
                OUT_AMOUNT float(20,2)  NULL,
                link TEXT  NULL,
            
                PRIMARY KEY (id)
            );
            ");
            $stmt2->execute();
            
            
            foreach($movement_array as $item) {
               
                $date = date("Y-m-d", strtotime($item['date']));
            
            
            
                $query = $conn_me->exec("INSERT INTO `tempTable_brtunch_ledger_movement` 
                ( 
                    `id`, `movment_date`,`time`,`note`,`IN_AMOUNT`, `OUT_AMOUNT`,`link`
                
                ) 
                VALUES
                (
                    '0',
                    '".$date."',
                  '".$item['time']."',
                    '".$item['description']."',
                     IF('".$item['type']."' = 'IN_AMOUNT','".$item['qty']."',0.00),
                     IF('".$item['type']."' = 'OUT_AMOUNT','".$item['qty']."',0.00),
                     '".$item['link']."'
            
            
                ) ");
            
            }
                   
            
            
             
                        
                          }


    
static  function CUSTOMER_LEDGER($ID,$FROM,$TO,$BRUNCH){


    $conn_me = Database::getInstance();
        
            
    if($BRUNCH == 'All' ){
        $BRUNCH_QUERY = "";
     }else {
       $BRUNCH_QUERY = " AND  `brunch_id` = '".$BRUNCH."' ";
     }
        
        $movement_array = [];
        $count = 0;
        $date_from = date("Y-m-d", strtotime($FROM));
        $date_to = date("Y-m-d", strtotime($TO));
        $prev_date = date('Y-m-d', strtotime($date_from .' -1 day'));

    
        $closing_balance = FIND::getAllCustomerDues('Brunch-Wise-Single-Customer-Wise',$ID,$prev_date,$BRUNCH);

        $count  = $count+1;
        $movement_array[$count]['serial'] = $count;
        $movement_array[$count]['date'] = $FROM;
        $movement_array[$count]['time'] = '07:24:22 am';
        $movement_array[$count]['qty'] = $closing_balance[0]['customer_due'];
        $movement_array[$count]['description'] = 'Closing Balance';
        $movement_array[$count]['tr_type'] = '';
        $movement_array[$count]['poster'] = '';
        $movement_array[$count]['type'] = 'IN_AMOUNT';
        $movement_array[$count]['link'] = 'No LInk';
        
        $ck4 = $conn_me->prepare("SELECT *  FROM `balance_customer`  where  `note` = 'LAST DUE' AND `customer_id` = '".$ID."' AND ( `date` BETWEEN '".$date_from."' AND '".$date_to."' ) $BRUNCH_QUERY ");
        $ck4->execute();
        
        $fe_ck4 = $ck4->fetchAll(PDO::FETCH_ASSOC);
        foreach($fe_ck4 AS $fetch4){
        
                $count  = $count+1;
                $movement_array[$count]['serial'] = $count;
                $movement_array[$count]['date'] = $fetch4['date'];
                $movement_array[$count]['time'] =  '07:24:22 am';
                $movement_array[$count]['qty'] = $fetch4['invoice_amount'];
                $movement_array[$count]['description'] = "Due Adjustment " . $fetch4['actual_note'] ;
                                        $movement_array[$count]['tr_type'] = '';

                        $movement_array[$count]['poster'] = '';
                                                $movement_array[$count]['poster'] = '';

                $movement_array[$count]['type'] = 'IN_AMOUNT';
                $movement_array[$count]['link'] = 'No LInk'; 
              
        }
        



        $ck44 = $conn_me->prepare("SELECT *  FROM `balance_customer`  where  `note` = 'DISCOUNT' AND `customer_id` = '".$ID."' AND ( `date` BETWEEN '".$date_from."' AND '".$date_to."' ) $BRUNCH_QUERY ");
        $ck44->execute();
        
        $fe_ck44 = $ck44->fetchAll(PDO::FETCH_ASSOC);
        foreach($fe_ck44 AS $fetch44){
        
                $count  = $count+1;
                $movement_array[$count]['serial'] = $count;
                $movement_array[$count]['date'] = $fetch44['date'];
                $movement_array[$count]['time'] =  '07:24:22 am';
                $movement_array[$count]['qty'] = $fetch44['return_amount'];
                $movement_array[$count]['description'] = "Discount " . $fetch44['actual_note'] ;
                                        $movement_array[$count]['tr_type'] = '';

                        $movement_array[$count]['poster'] = '';
                                                $movement_array[$count]['poster'] = '';

                $movement_array[$count]['type'] = 'OUT_AMOUNT';
                $movement_array[$count]['link'] = 'No LInk';
        
              
        }
        


        $ck5 = $conn_me->prepare("SELECT poster,`id`,`invoice_date`,`time`,`code`,`invoice_no` FROM `sales_invoice`  where ( `invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' )  AND `customer_id` = '".$ID."' and generate_challan = 'Done' $BRUNCH_QUERY ");
        $ck5->execute();
        
        $fe_ck5 = $ck5->fetchAll(PDO::FETCH_ASSOC);
        foreach($fe_ck5 AS $fetch5){
        
            $invoice_price = FIND::TOTAL_SALES_INVOICE_PRICE($fetch5['id']);
    
    
               $count  = $count+1;
                $movement_array[$count]['serial'] = $count;
                $movement_array[$count]['date'] = $fetch5['invoice_date'];
                $movement_array[$count]['time'] = $fetch5['time'];
                $movement_array[$count]['qty'] = $invoice_price['price'];
                $movement_array[$count]['description'] = " Invoice >>  $fetch5[invoice_no] ";
                        $movement_array[$count]['tr_type'] = '';
                    $movement_array[$count]['poster'] = $fetch5['poster'];

                $movement_array[$count]['type'] = 'IN_AMOUNT';
                $movement_array[$count]['link'] = "invoice_copy.php?code=$fetch5[code]";
        
              
        }
        
        $ck6 = $conn_me->prepare("SELECT *  FROM `sales_return_invoice`  where  ( `warehouse_receive_date` BETWEEN '".$date_from."' AND '".$date_to."' ) AND `warehouse_receive` = 'Done'  AND `customer_id` = '".$ID."'  $BRUNCH_QUERY ");
        $ck6->execute();
        
        $fe_ck6 = $ck6->fetchAll(PDO::FETCH_ASSOC);
        foreach($fe_ck6 AS $fetch6){
        
                $count  = $count+1;
                $info6 = FIND::PRODUCT_RETURN_INVOICE_VALUE($fetch6['id']);
                $movement_array[$count]['serial'] = $count;
                $movement_array[$count]['date'] = $fetch6['warehouse_receive_date'];
                $movement_array[$count]['time'] = $fetch6['time'];
                $movement_array[$count]['qty'] = $info6['price'];
                $movement_array[$count]['description'] = "Sales Return -  $fetch6[invoice_no]";
                                        $movement_array[$count]['tr_type'] = '';
                    $movement_array[$count]['poster'] = $info6['poster'];

                $movement_array[$count]['type'] = 'OUT_AMOUNT';
                $movement_array[$count]['link'] = 'No LInk';
        }
        
        
        
        
        
        
        
        $ck9 = $conn_me->prepare("SELECT 
    A.*, 
    CASE 
        WHEN A.transection_by = 'Bank' THEN COALESCE(CONCAT('By Bank :: ',B.bank_name, ' ', B.account_number), ' ')
        WHEN A.transection_by = 'Mobile-Banking' THEN COALESCE(CONCAT('By Mobile :: ',C.mobile_bank_name, ' ', C.mobile_number), ' ')
        WHEN A.transection_by = 'Cash' THEN 'Cash'
        ELSE ' '
    END AS transection_details
FROM 
    account_transection A
LEFT JOIN 
    setup_bank B ON A.transection_by = 'Bank' AND A.transection_by_id = B.id
LEFT JOIN 
    setup_mobile_banking C ON A.transection_by = 'Mobile-Banking' AND A.transection_by_id = C.id
WHERE 
    A.transection_to_id = '".$ID."'  
    AND A.transection_date BETWEEN '".$date_from."' AND '".$date_to."'  
    AND A.transection_to = 'Customer' 
    AND A.data_inserted_from = 'Invoice Wise Payment' 
    $BRUNCH_QUERY;
"  );
        $ck9->execute();
        
        $fe_ck9 = $ck9->fetchAll(PDO::FETCH_ASSOC);
        foreach($fe_ck9 AS $fetch9){
        
               
                $count  = $count+1;
                $movement_array[$count]['serial'] = $count;
                $movement_array[$count]['date'] = $fetch9['transection_date'];
                $movement_array[$count]['time'] = $fetch9['time'];
                $movement_array[$count]['qty'] = $fetch9['in_amount'];
                $movement_array[$count]['description'] = "Invoice Wise Payment";
                $movement_array[$count]['tr_type'] = '<b style="color:blue">' . $fetch9['transection_details'] . '</b>';
                    $movement_array[$count]['poster'] = $fetch9['poster'];

                $movement_array[$count]['type'] = 'OUT_AMOUNT';
                $movement_array[$count]['link'] = 'No LInk';
        }
        
        
        
    
        $ck10 = $conn_me->prepare("SELECT 
    A.*, 
    CASE 
        WHEN A.transection_by = 'Bank' THEN COALESCE(CONCAT('By Bank :: ',B.bank_name, ' ', B.account_number), ' ')
        WHEN A.transection_by = 'Mobile-Banking' THEN COALESCE(CONCAT('By Mobile :: ',C.mobile_bank_name, ' ', C.mobile_number), ' ')
        WHEN A.transection_by = 'Cash' THEN 'Cash'
        ELSE ' '
    END AS transection_details
FROM 
    account_transection A
LEFT JOIN 
    setup_bank B ON A.transection_by = 'Bank' AND A.transection_by_id = B.id
LEFT JOIN 
    setup_mobile_banking C ON A.transection_by = 'Mobile-Banking' AND A.transection_by_id = C.id
WHERE 
    A.transection_to_id = '".$ID."'  
    AND A.transection_date BETWEEN '".$date_from."' AND '".$date_to."'  
    AND A.transection_to = 'Customer' 
    AND A.data_inserted_from = 'CUSTOMER-TRANSACTION' 
    AND A.transection_type = 'INCOME' 
    $BRUNCH_QUERY;
 " );
        $ck10->execute();
        
        $fe_ck10 = $ck10->fetchAll(PDO::FETCH_ASSOC);
        foreach($fe_ck10 AS $fetch10){
        
               
                $count  = $count+1;
                $movement_array[$count]['serial'] = $count;
                $movement_array[$count]['date'] = $fetch10['transection_date'];
                $movement_array[$count]['time'] = $fetch10['time'];
                $movement_array[$count]['qty'] = $fetch10['in_amount'];
                $movement_array[$count]['description'] = "Customer Receive";
                                $movement_array[$count]['tr_type'] = '<b style="color:blue">' . $fetch10['transection_details'] . '</b>';
                    $movement_array[$count]['poster'] = $fetch10['poster'];

                $movement_array[$count]['type'] = 'OUT_AMOUNT';
                $movement_array[$count]['link'] = 'No LInk';
        }
        
    
        $ck11 = $conn_me->prepare("SELECT 
    A.*, 
    CASE 
        WHEN A.transection_by = 'Bank' THEN COALESCE(CONCAT('By Bank :: ', B.bank_name, ' ', B.account_number), ' ')
        WHEN A.transection_by = 'Mobile-Banking' THEN COALESCE(CONCAT('By Mobile :: ',C.mobile_bank_name, ' ', C.mobile_number), ' ')
        WHEN A.transection_by = 'Cash' THEN 'Cash'
        ELSE ' '
    END AS transection_details
FROM 
    account_transection A
LEFT JOIN 
    setup_bank B ON A.transection_by = 'Bank' AND A.transection_by_id = B.id
LEFT JOIN 
    setup_mobile_banking C ON A.transection_by = 'Mobile-Banking' AND A.transection_by_id = C.id
WHERE 
    A.transection_to_id = '".$ID."'  
    AND A.transection_date BETWEEN '".$date_from."' AND '".$date_to."'  
    AND A.transection_to = 'Customer' 
    AND A.data_inserted_from = 'CUSTOMER-TRANSACTION' 
    AND A.transection_type = 'EXPENSE' 
    $BRUNCH_QUERY;
" );
        $ck11->execute();
        
        $fe_ck11 = $ck11->fetchAll(PDO::FETCH_ASSOC);
        foreach($fe_ck11 AS $fetch11){
        
               
                $count  = $count+1;
                $movement_array[$count]['serial'] = $count;
                $movement_array[$count]['date'] = $fetch11['transection_date'];
                $movement_array[$count]['time'] = $fetch11['time'];
                $movement_array[$count]['qty'] = abs($fetch11['out_amount']);
                $movement_array[$count]['description'] = "Customer Return";
                       $movement_array[$count]['tr_type'] = '<b style="color:blue">' . $fetch11['transection_by'] . '</b>';
                                           $movement_array[$count]['poster'] = $fetch11['poster'];

                $movement_array[$count]['type'] = 'IN_AMOUNT';
                $movement_array[$count]['link'] = 'No LInk';
        }
        
        
        
        
        
        $stm = $conn_me->prepare("DROP table IF EXISTS tempTable_customer_ledger_movement");
        $stm->execute();
        
        $stmt2 = $conn_me->prepare("CREATE TABLE tempTable_customer_ledger_movement (
            id int NOT NULL AUTO_INCREMENT,
            movment_date date  NULL,
            time varchar(255)  NULL,
            note varchar(255)  NULL,
            tr_type varchar(255)  NULL,
            poster int NULL ,
            IN_AMOUNT float(20,2)  NULL,
            OUT_AMOUNT float(20,2)  NULL,
            link TEXT  NULL,
        
            PRIMARY KEY (id)
        );
        ");
        $stmt2->execute();
        
        


        foreach($movement_array as $item) {
           
            $date = date("Y-m-d", strtotime($item['date']));
        
            $str = trim($item['description']);
            if (function_exists('stripslashes')) {
            $str = stripslashes($str);
            }
            $description =  addslashes(strip_tags($str));

        
        
            $query = $conn_me->exec("INSERT INTO `tempTable_customer_ledger_movement` 
            ( 
                `id`, `movment_date`,`time`,`note`,`IN_AMOUNT`, `OUT_AMOUNT`,`tr_type`,`link`,poster
            
            ) 
            VALUES
            (
                '0',
                '".$date."',
                '".$item['time']."',
                '".$description."',
                 IF('".$item['type']."' = 'IN_AMOUNT','".$item['qty']."',0.00),
                 IF('".$item['type']."' = 'OUT_AMOUNT','".$item['qty']."',0.00),
                                  '".$item['tr_type']."',
                 '".$item['link']."',
                '".$item['poster']."'
        
        
            ) ");
        
        }
               
        
        
         
                    
                      }


                      static function SALES_RETURN_DUE($from,$to){

    $conn_me = Database::getInstance();
    
    $from = date("Y-m-d", strtotime($from));
    $to = date("Y-m-d", strtotime($to));

    
    
    $brunch1 = $conn_me->prepare("SELECT SUM(`invoice_amount`)  AS `invoice_amount`, SUM(`receive_amount`)  AS `receive_amount` ,SUM(`return_amount`)  AS `return_amount`FROM `balance_customer` where  ( `date` BETWEEN '".$from."' AND '".$to."' )  and brunch_id = 1   GROUP BY `brunch_id` ");
    $brunch1->execute();
    $fetch_brunch1 = $brunch1->fetch(PDO::FETCH_ASSOC);


     $brunch2 = $conn_me->prepare("SELECT SUM(`invoice_amount`)  AS `invoice_amount`, SUM(`receive_amount`)  AS `receive_amount` ,SUM(`return_amount`)  AS `return_amount`FROM `balance_customer` where  ( `date` BETWEEN '".$from."' AND '".$to."' )  and brunch_id = 2   GROUP BY `brunch_id` ");
    $brunch2->execute();
    $fetch_brunch2 = $brunch2->fetch(PDO::FETCH_ASSOC);

     $brunch3 = $conn_me->prepare("SELECT SUM(`invoice_amount`)  AS `invoice_amount`, SUM(`receive_amount`)  AS `receive_amount` ,SUM(`return_amount`)  AS `return_amount`FROM `balance_customer` where   ( `date` BETWEEN '".$from."' AND '".$to."' )  and brunch_id = 3   GROUP BY `brunch_id` ");
    $brunch3->execute();
    $fetch_brunch3 = $brunch3->fetch(PDO::FETCH_ASSOC);


    if(!empty($fetch_brunch1['invoice_amount'])){ $invoice_amount1 = $fetch_brunch1['invoice_amount'] ; }else{ $invoice_amount1 =  0.00; }
    if(!empty($fetch_brunch1['receive_amount'])){ $receive_amount1 = $fetch_brunch1['receive_amount'] ; }else{ $receive_amount1 =  0.00; }
    if(!empty($fetch_brunch1['return_amount'])){ $return_amount1 = $fetch_brunch1['return_amount'] ; }else{ $return_amount1 =  0.00; }

    if(!empty($fetch_brunch2['invoice_amount'])){ $invoice_amount2 = $fetch_brunch2['invoice_amount'] ; }else{ $invoice_amount2 =  0.00; }
    if(!empty($fetch_brunch2['receive_amount'])){ $receive_amount2 = $fetch_brunch2['receive_amount'] ; }else{ $receive_amount2 =  0.00; }
    if(!empty($fetch_brunch2['return_amount'])){ $return_amount2 = $fetch_brunch2['return_amount'] ; }else{ $return_amount2 =  0.00; }

    if(!empty($fetch_brunch3['invoice_amount'])){ $invoice_amount3 = $fetch_brunch3['invoice_amount'] ; }else{ $invoice_amount3 =  0.00; }
    if(!empty($fetch_brunch3['receive_amount'])){ $receive_amount3 = $fetch_brunch3['receive_amount'] ; }else{ $receive_amount3 =  0.00; }
    if(!empty($fetch_brunch3['return_amount'])){ $return_amount3 = $fetch_brunch3['return_amount'] ; }else{ $return_amount3 =  0.00; }




    $customer_due1 = number_format((float)( $invoice_amount1  - $receive_amount1 - $return_amount1 ), 2, '.', '');
    $customer_due2 = number_format((float)( $invoice_amount2  - $receive_amount2 - $return_amount2 ), 2, '.', '');
    $customer_due3 = number_format((float)( $invoice_amount3  - $receive_amount3 - $return_amount3 ), 2, '.', '');





    return array(
    'invoice_amount1' =>  $invoice_amount1,
    'invoice_amount2' =>  $invoice_amount2,
    'invoice_amount3' =>  $invoice_amount3,
    'receive_amount1' =>  $receive_amount1,
    'receive_amount2' =>  $receive_amount2,
    'receive_amount3' =>  $receive_amount3,
    'return_amount1' =>   $return_amount1,
    'return_amount2' =>   $return_amount2,
    'return_amount3' =>   $return_amount3,
    'customer_due1' =>   $customer_due1,
    'customer_due2' =>   $customer_due2,
    'customer_due3' =>   $customer_due3

    );
    
    
    }





    static function TOP_SOLD_PRODUCT($from,$to,$productNumber){

    $conn_me = Database::getInstance();
    
    $from = date("Y-m-d", strtotime($from));
    $to = date("Y-m-d", strtotime($to));
    
$content = '' ;

$content .='<div class="table-responsive">
<table class="table table-hover table-condensed table-striped table-bordered datatable" id="myDatatable" style="white-space:nowrap;">
    <thead>
    <th>Sl</th>
        <th>Product Name</th>
        <th>Category</th>
        <th>Unit</th>
        <th>Quantity</th>
        <th>Actions </th>
    </thead>
    <tbody>';
    

     


       $qry = $conn_me->prepare("SELECT
       SUM(A.sales_quantity) AS total_sales,A.product_id,
       C.product_name,
       I.category,
       F.unit
       FROM
       sales_invoice_item A JOIN
               sales_invoice B ON A.sales_invoice_id = B.id
             JOIN
               setup_product C ON A.product_id = C.id
             JOIN
               setup_unit F ON C.unit_id = F.id
              JOIN
               setup_category I ON C.category_id = I.id
       
             WHERE
               A.sales_manager_confirm_date BETWEEN :from_date AND :to_date 
               AND B.generate_challan = 'Done' 
               GROUP BY
               A.product_id
           ORDER BY
               total_sales DESC
           LIMIT $productNumber
    ");

$qry->bindParam(":from_date", $from);
$qry->bindParam(":to_date", $to);

$qry->execute();

$fetch = $qry->fetchAll(PDO::FETCH_ASSOC) ; 
$sl=1 ; 
foreach  ( $fetch as $row  ) {

    $content .='<tr>';

    $content .= '<td>'.$sl++.'</td>';
    $content .= '<td>'.$row['product_name'].'</td>';
    $content .= '<td>'.$row['category'].'</td>';
    $content .= '<td>'.$row['unit'].'</td>';
    $content .= '<td>'.$row['total_sales'].'</td>';
    $content .= '<td><a target="_BLINK" href="Product-Wise-Sales-Record.php?product_id='.$row['product_id'].'&date_from='.$from.'&date_to='.$to.'" class="btn btn-info btn-rounded btn-sm"><i class="fa fa-eye"></i></a></td>';

    $content .='</tr>';
      
}


$content .='</tbody>

</table>
</div>';


    return  $content ;  
    
    }


    static function LEAST_SOLD_PRODUCT($from,$to,$productNumber){

        $conn_me = Database::getInstance();
        
        $from = date("Y-m-d", strtotime($from));
        $to = date("Y-m-d", strtotime($to));
        
    $content = '' ;
    
    $content .='<div class="table-responsive">
    <table class="table table-hover table-condensed table-striped table-bordered datatable" id="myDatatabletwo" style="white-space:nowrap;">
        <thead>
        <th>Sl</th>
            <th>Product Name</th>
            <th>Category</th>
            <th>Unit</th>
            <th>Quantity</th>
            <th>Actions </th>
        </thead>
        <tbody>';
        
    
         
    
    
           $qry = $conn_me->prepare("SELECT
           SUM(A.sales_quantity) AS total_sales,A.product_id,
           C.product_name,
           I.category,
           F.unit
           FROM
           sales_invoice_item A JOIN
                   sales_invoice B ON A.sales_invoice_id = B.id
                 JOIN
                   setup_product C ON A.product_id = C.id
                 JOIN
                   setup_unit F ON C.unit_id = F.id
                  JOIN
                   setup_category I ON C.category_id = I.id
           
                 WHERE
                   A.sales_manager_confirm_date BETWEEN :from_date AND :to_date 
                   AND B.generate_challan = 'Done' 
                   GROUP BY
                   A.product_id
               ORDER BY
                   total_sales ASC
               LIMIT $productNumber
        ");
    
    $qry->bindParam(":from_date", $from);
    $qry->bindParam(":to_date", $to);
    
    $qry->execute();
    
    $fetch = $qry->fetchAll(PDO::FETCH_ASSOC) ; 
    $sl=1 ; 
    foreach  ( $fetch as $row  ) {
    
        $content .='<tr>';
    
        $content .= '<td>'.$sl++.'</td>';
        $content .= '<td>'.$row['product_name'].'</td>';
        $content .= '<td>'.$row['category'].'</td>';
        $content .= '<td>'.$row['unit'].'</td>';
        $content .= '<td>'.$row['total_sales'].'</td>';
        $content .= '<td><a target="_BLINK" href="Product-Wise-Sales-Record.php?product_id='.$row['product_id'].'&date_from='.$from.'&date_to='.$to.'" class="btn btn-info btn-rounded btn-sm"><i class="fa fa-eye"></i></a></td>';
    
        $content .='</tr>';
          
    }
    
    
    $content .='</tbody>
    
    </table>
    </div>';
    
    
        return  $content ;  
    
    }




    static  function FG_MOVEMENT($ID,$RELATED_ID,$FROM,$TO,$TYPE){


$conn_me = Database::getInstance();




$movement_array = [];
$count = 0;
$date_from = date("Y-m-d", strtotime($FROM));
$date_to = date("Y-m-d", strtotime($TO));

if($TYPE == 'Multipal-Warehouse-Wise' ){
    
    $selectedValues = implode(',', $RELATED_ID);        
    $CONDITION = "   A.`product_id` = '" . $ID . "' AND A.`warehouse_id`  IN ({$selectedValues})  AND ";
    $CONDITION2 = "  A.`product_id` = '" . $ID . "' AND A.`FROM_warehouse_id`  IN ({$selectedValues})  AND ";
    $CONDITION3 = "  A.`product_id` = '" . $ID . "' AND A.`TO_warehouse_id`  IN ({$selectedValues})  AND ";
    $CONDITION4 = "  A.`product_id` = '" . $ID . "' AND A.`dispatch_from_warehouse`  IN ({$selectedValues})  AND ";
    $CONDITION5 = "  A.`product_id` = '" . $ID . "' AND A.`received_warehouse`  IN ({$selectedValues})  AND ";


}else if($TYPE == 'Multipal-Branch-Wise') {

$selectedValues = implode(',', $RELATED_ID);        
$qry = $conn_me->prepare("SELECT * FROM `setup_brunch` where `id`  IN ({$selectedValues})  ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);

$warehouse_ids = [];

foreach ($fetch_list as $row) {
    $related = $row['related_warehouse']; // get JSON string
    $decoded = json_decode($related, true); // decode it
    if (is_array($decoded)) {
        $warehouse_ids = array_merge($warehouse_ids, $decoded);
    }
}

$warehouse_ids = array_unique($warehouse_ids);
$c = implode(',', array_map(function($id) {
    return '"' . intval($id) . '"';
}, $warehouse_ids));
$CONDITION = "   A.`product_id` = '" . $ID . "' AND A.`warehouse_id` IN ($c) AND ";
$CONDITION2 = "  A.`product_id` = '" . $ID . "' AND A.`FROM_warehouse_id` IN ($c) AND ";
$CONDITION3 = "  A.`product_id` = '" . $ID . "' AND A.`TO_warehouse_id` IN ($c) AND ";
$CONDITION4 = "  A.`product_id` = '" . $ID . "' AND A.`dispatch_from_warehouse` IN ($c) AND ";
$CONDITION5 = "  A.`product_id` = '" . $ID . "' AND A.`received_warehouse` IN ($c) AND ";


}else{
$CONDITION = "  A.`product_id` = '" . $ID . "' AND ";
$CONDITION2 = " A.`product_id` = '" . $ID . "' AND ";
$CONDITION3 = " A.`product_id` = '" . $ID . "' AND ";
$CONDITION4 = " A.`product_id` = '" . $ID . "' AND ";
$CONDITION5 = " A.`product_id` = '" . $ID . "' AND ";

}

$closing_balance = FIND::FG_CLOSING_MOVEMENT($ID,$RELATED_ID,$FROM,$TYPE);

$count  = $count+1;
$movement_array[$count]['serial'] = $count;
$movement_array[$count]['date'] = $closing_balance['date'];
$movement_array[$count]['time'] = '07:24:22 am';
$movement_array[$count]['poster'] = $_SESSION['NEWERP_SESS_MEMBER_ID'];
$movement_array[$count]['qty'] = $closing_balance['closing'];
$movement_array[$count]['description'] = $closing_balance['note'];
$movement_array[$count]['type'] = 'IN';
$movement_array[$count]['link'] = '';
$movement_array[$count]['section'] = '';
$movement_array[$count]['related_id'] = 0;

$ck1 = $conn_me->prepare("SELECT A.*  FROM `fg_opening_stock` A where $CONDITION A.`status` = 'Done' AND ( A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' )   ");
$ck1->execute();
$fe_ck1 = $ck1->fetchAll(PDO::FETCH_ASSOC);
    foreach($fe_ck1 AS $fetch1){
 
          $info_warehouse = SETUP::SETUP_WAREHOUSE($fetch1['warehouse_id']);
        $count  = $count+1;
        $movement_array[$count]['serial'] = $count;
        $movement_array[$count]['date'] = $fetch1['invoice_date'];
        $movement_array[$count]['time'] = $fetch1['time'];
        $movement_array[$count]['poster'] = $fetch1['poster'];
        $movement_array[$count]['qty'] = $fetch1['quantity'];
        $movement_array[$count]['description'] = "Opening Stock at >> $info_warehouse[name]";
        $movement_array[$count]['type'] = 'IN';
        $movement_array[$count]['link'] = "";
        $movement_array[$count]['section'] = "fg_opening_stock";
        $movement_array[$count]['related_id'] = $fetch1['id'];

   
   
}


$ck1 = $conn_me->prepare("SELECT A.*  FROM `balance_product` A where $CONDITION A.note = 'STOCK ADJUSTMENT' AND ( A.`date` BETWEEN '".$date_from."' AND '".$date_to."' ) ");
$ck1->execute();
$fe_ck1 = $ck1->fetchAll(PDO::FETCH_ASSOC);
    foreach($fe_ck1 AS $fetch1){
 

        if( $fetch1['stock_in'] > 0 || $fetch1['stock_out'] > 0 ){ 

        $info_warehouse = SETUP::SETUP_WAREHOUSE($fetch1['warehouse_id']);
        $count  = $count+1;
        $movement_array[$count]['serial'] = $count;
        $movement_array[$count]['date'] = $fetch1['date'];
        $movement_array[$count]['time'] = $fetch1['time'];
        $movement_array[$count]['poster'] = $fetch1['poster'];


        if( $fetch1['stock_in'] > 0 ){
            $movement_array[$count]['qty'] = $fetch1['stock_in'];
            $movement_array[$count]['description'] = "<span class=\"text-success fa fa-arrow-up\"></span> Stock adjustment on >> $info_warehouse[name] (Data Insert Date: " . $fetch1['data_insert_date'] .")";
            $movement_array[$count]['type'] = 'IN';
            $movement_array[$count]['section'] = "stock_in_balance_product";

        }

        if( $fetch1['stock_out'] > 0 ){
            $movement_array[$count]['qty'] = $fetch1['stock_out'];
            $movement_array[$count]['description'] = "<span class=\"text-danger fa fa-arrow-down\"></span> Stock adjustment on >> $info_warehouse[name] (Data Insert Date: " . $fetch1['data_insert_date'] .")";
            $movement_array[$count]['type'] = 'OUT';
            $movement_array[$count]['section'] = "stock_out_balance_product";

        }
      
   
        $movement_array[$count]['link'] = "No LInk";
        $movement_array[$count]['related_id'] = $fetch1['id'];

}

    }


$ck131 = $conn_me->prepare("SELECT A.*  FROM `fg_damage_store` A where $CONDITION A.`status` = 'Done' AND ( A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' )   ");
$ck131->execute();
    $fe_ck31 = $ck131->fetchAll(PDO::FETCH_ASSOC);
    foreach($fe_ck31 AS $fetch31){

          $info_warehouse = SETUP::SETUP_WAREHOUSE($fetch31['warehouse_id']);
        $count  = $count+1;
        $movement_array[$count]['serial'] = $count;
        $movement_array[$count]['date'] = $fetch31['invoice_date'];
        $movement_array[$count]['time'] = $fetch31['time'];
        $movement_array[$count]['poster'] = $fetch31['poster'];
        $movement_array[$count]['qty'] = $fetch31['quantity'];
        $movement_array[$count]['description'] = "Deleted From >> $info_warehouse[name]";
        $movement_array[$count]['type'] = 'OUT';
        $movement_array[$count]['link'] = "print.php?print=FG-WAREHOUSE-TO-WAREHOUSE-TRANSFER-RECEIPT&code=$fetch31[code]";
        $movement_array[$count]['section'] = "fg_damage_store";
        $movement_array[$count]['related_id'] = $fetch31['id'];
    
   
}






$ck2 = $conn_me->prepare("SELECT A.id,A.`code`,A.`receive_quantity`,A.`invoice_date`,A.`warehouse_id`,A.`poster`   FROM `history_local_fg_purches` A  where $CONDITION ( A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ) AND `reject_quantity` IS NULL  ");
$ck2->execute();
$fe_ck2 = $ck2->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck2 AS $fetch2){
    $count  = $count+1;

$info2 = SETUP::SETUP_FG_LOCAL_PURCHASE_HISTORY($fetch2['code']);
$info_warehouse = SETUP::SETUP_WAREHOUSE($fetch2['warehouse_id']);

$movement_array[$count]['serial'] = $count;
$movement_array[$count]['date'] = $fetch2['invoice_date'];
$movement_array[$count]['time'] = $info2['time'];
$movement_array[$count]['poster'] = $fetch2['poster'];
$movement_array[$count]['qty'] = $fetch2['receive_quantity'];
$movement_array[$count]['description'] = "Suppliler Receive >> $info2[supplier_name] >> $info_warehouse[name] >> P.Price $info2[purches_price]";
$movement_array[$count]['type'] = 'IN';
$movement_array[$count]['link'] = 'No Link';
$movement_array[$count]['section'] = "receive_quantity_history_local_fg_purches";
$movement_array[$count]['related_id'] = 0;

}


$ck3 = $conn_me->prepare("SELECT A.id,A.`code`,A.`reject_quantity`,A.`invoice_date`,A.`warehouse_id`,A.`poster`   FROM `history_local_fg_purches` A where $CONDITION  ( A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ) AND A.`receive_quantity` IS NULL ");
$ck3->execute();

$fe_ck3 = $ck3->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck3 AS $fetch3){

    $count  = $count+1;


        $info3 = SETUP::SETUP_FG_LOCAL_PURCHASE_HISTORY($fetch3['code']);
        $info_warehouse = SETUP::SETUP_WAREHOUSE($fetch3['warehouse_id']);

        $movement_array[$count]['serial'] = $count;
        $movement_array[$count]['date'] = $fetch3['invoice_date'];
        $movement_array[$count]['time'] = $info3['time'];
        $movement_array[$count]['poster'] = $fetch3['poster'];
        $movement_array[$count]['qty'] = $fetch3['reject_quantity'];
        $movement_array[$count]['description'] = "Suppliler Return >> $info3[supplier_name] >>  $info_warehouse[name]";
        $movement_array[$count]['type'] = 'OUT';
        $movement_array[$count]['link'] = 'No LInk';
        $movement_array[$count]['section'] = "reject_quantityhistory_local_fg_purches";
        $movement_array[$count]['related_id'] = 0;




}


$ck4 = $conn_me->prepare("SELECT A.id,A.`code`,A.`receive_quantity`, A.`invoice_date`,A.`poster`  FROM `history_batch_wise_fg_receive` A  where $CONDITION  ( A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ) AND A.`return_quantity` IS NULL ");
$ck4->execute();

$fe_ck4 = $ck4->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck4 AS $fetch4){

    $count  = $count+1;


        $info4 = SETUP::SETUP_FG_BATCH_WISE_HISTORY($fetch4['code']);

        $movement_array[$count]['serial'] = $count;
        $movement_array[$count]['date'] = $fetch4['invoice_date'];
        $movement_array[$count]['time'] = $info4['time'];
        $movement_array[$count]['poster'] = $fetch4['poster'];
        $movement_array[$count]['qty'] = $fetch4['receive_quantity'];
        $movement_array[$count]['description'] = "Batch Receive -  $info4[invoice_no]";
        $movement_array[$count]['type'] = 'IN';
        $movement_array[$count]['link'] = 'No LInk';
        $movement_array[$count]['section'] = "history_batch_wise_fg_receive";
        $movement_array[$count]['related_id'] = 0;


}





    $ck55 = $conn_me->prepare("SELECT A.id,A.`sales_rate`,A.`sales_quantity`,A.`note`,B.`code`,B.`invoice_date`,B.`time`,B.`sales_person`,C.`customer_name`,D.`brunch` 
    FROM `sales_invoice_item` A 
    JOIN `sales_invoice` B ON (A.`sales_invoice_id` = B.`id`)
    JOIN `setup_customer` C ON (B.`customer_id` = C.`id`)
    JOIN `setup_brunch` D ON (B.`dispatch_from_which_brunch` = D.`id`)

    where $CONDITION B.`generate_challan` = 'Done'  AND   ( B.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ) ");
    $ck55->execute();

    $fe_ck55 = $ck55->fetchAll(PDO::FETCH_ASSOC);
    foreach($fe_ck55 AS $fetch5){
        $count  = $count+1;
        $movement_array[$count]['serial'] = $count;
        $movement_array[$count]['date'] = $fetch5['invoice_date'];
        $movement_array[$count]['time'] = $fetch5['time'];
        $movement_array[$count]['poster'] = $fetch5['sales_person'];
        $movement_array[$count]['qty'] = $fetch5['sales_quantity'];
        $movement_array[$count]['description'] = "  <b class=\"text-danger\"> $fetch5[note]</b> Sales >> $fetch5[customer_name] >> $fetch5[brunch] >> S.Price $fetch5[sales_rate] ";
        $movement_array[$count]['type'] = 'OUT';
        $movement_array[$count]['link'] = "invoice_copy.php?code=$fetch5[code]";
        $movement_array[$count]['section'] = "sales_invoice_item";
        $movement_array[$count]['related_id'] = 0;


    }



$ck6 = $conn_me->prepare("SELECT A.id,A.`poster`,A.`time`,A.`warehouse_receive_date`,A.`return_invoice_id`, A.`return_quantity` FROM `sales_return_invoice_item` A where $CONDITION ( A.`warehouse_receive_date` BETWEEN '".$date_from."' AND '".$date_to."' ) AND A.`warehouse_receive` = 'Done' ");
$ck6->execute();

$fe_ck6 = $ck6->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck6 AS $fetch6){

    $count  = $count+1;


        $info6 = SETUP::SETUP_FG_SALES_RETURN_HISTORY($fetch6['return_invoice_id']);

        $movement_array[$count]['serial'] = $count;
        $movement_array[$count]['date'] = $fetch6['warehouse_receive_date'];
        $movement_array[$count]['time'] = $fetch6['time'];
        $movement_array[$count]['poster'] = $fetch6['poster'];
        $movement_array[$count]['qty'] = $fetch6['return_quantity'];
        $movement_array[$count]['description'] = "Sales Return -  $info6[invoice_no]";
        $movement_array[$count]['type'] = 'IN';
        $movement_array[$count]['link'] = 'No LInk';
        $movement_array[$count]['section'] = "sales_return_invoice_item";
        $movement_array[$count]['related_id'] = 0;
}




$ck7 = $conn_me->prepare("SELECT A.id,A.`time`,A.`code`, A.`damage_quantity`,A.`poster` FROM `damage_invoice_item` A where $CONDITION ( A.`warehouse_receive_date` BETWEEN '".$date_from."' AND '".$date_to."' ) ");
$ck7->execute();

$fe_ck7 = $ck7->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck7 AS $fetch7){

        $count  = $count+1;
        $info7 = SETUP::SETUP_FG_DAMAGE_HISTORY($fetch7['code']);
        $movement_array[$count]['serial'] = $count;
        $movement_array[$count]['date'] = $info7['invoice_date'];
        $movement_array[$count]['time'] = $fetch7['time'];
        $movement_array[$count]['poster'] = $fetch7['poster'];
        $movement_array[$count]['qty'] = $fetch7['damage_quantity'];
        $movement_array[$count]['description'] = "Damage -  $info7[invoice_no]";
        $movement_array[$count]['type'] = 'OUT';
        $movement_array[$count]['link'] = 'No LInk';
        $movement_array[$count]['section'] = "damage_invoice_item";
        $movement_array[$count]['related_id'] = $fetch7['id'];
}






$ck9 = $conn_me->prepare("SELECT A.* FROM `fg_warehouse_to_warehouse_transfer` A where $CONDITION3 ( A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ) ");
$ck9->execute();

$fe_ck9 = $ck9->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck9 AS $fetch9){

    $info_warehouse = SETUP::SETUP_WAREHOUSE($fetch9['FROM_warehouse_id']);
    $info_warehouse2 = SETUP::SETUP_WAREHOUSE($fetch9['TO_warehouse_id']);

    $dateTime = DateTime::createFromFormat('H:i:s a', $fetch9['time']);
    $dateTime->modify('+2 seconds');
    $time = $dateTime->format('H:i:s a');



        $count  = $count+1;
        $movement_array[$count]['serial'] = $count;
        $movement_array[$count]['date'] = $fetch9['invoice_date'];
        $movement_array[$count]['time'] = $time;
        $movement_array[$count]['poster'] = $fetch9['poster'];
        $movement_array[$count]['qty'] = $fetch9['quantity'];
        $movement_array[$count]['description'] = "Transfer To $info_warehouse2[name] >> From $info_warehouse[name]";
        $movement_array[$count]['type'] = 'IN';
        $movement_array[$count]['link'] = 'No LInk';
        $movement_array[$count]['section'] = "Transfer_To_fg_warehouse_to_warehouse_transfer";
        $movement_array[$count]['related_id'] = $fetch9['id'];
}


$ck8 = $conn_me->prepare("SELECT A.* FROM `fg_warehouse_to_warehouse_transfer` A  where $CONDITION2 ( A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ) ");
$ck8->execute();

$fe_ck8 = $ck8->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck8 AS $fetch8){

    $info_warehouse = SETUP::SETUP_WAREHOUSE($fetch8['FROM_warehouse_id']);
    $info_warehouse2 = SETUP::SETUP_WAREHOUSE($fetch8['TO_warehouse_id']);

        $count  = $count+1;
        $movement_array[$count]['serial'] = $count;
        $movement_array[$count]['date'] = $fetch8['invoice_date'];
        $movement_array[$count]['time'] = $fetch8['time'];
        $movement_array[$count]['poster'] = $fetch8['poster'];
        $movement_array[$count]['qty'] = $fetch8['quantity'];
        $movement_array[$count]['description'] = "Transfer From $info_warehouse[name] >> To $info_warehouse2[name]";
        $movement_array[$count]['type'] = 'OUT';
        $movement_array[$count]['link'] = 'No LInk';
        $movement_array[$count]['section'] = "Transfer_From_fg_warehouse_to_warehouse_transfer";
        $movement_array[$count]['related_id'] = 0;
}




$ck8 = $conn_me->prepare("SELECT A.* FROM `demand_receive` A  where $CONDITION4 ( A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ) ");
$ck8->execute();

$fe_ck8 = $ck8->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck8 AS $fetch8){

    $info_warehouse = SETUP::SETUP_WAREHOUSE($fetch8['dispatch_from_warehouse']);
    $info_warehouse2 = SETUP::SETUP_WAREHOUSE($fetch8['received_warehouse']);

        $count  = $count+1;
        $movement_array[$count]['serial'] = $count;
        $movement_array[$count]['date'] = $fetch8['invoice_date'];
        $movement_array[$count]['time'] = $fetch8['time'];
        $movement_array[$count]['poster'] = $fetch8['poster'];
        $movement_array[$count]['qty'] = $fetch8['quantity'];
        $movement_array[$count]['description'] = "DEMAND:: Transfer From $info_warehouse[name] >> To $info_warehouse2[name]";
        $movement_array[$count]['type'] = 'OUT';
        $movement_array[$count]['link'] = 'No LInk';
        $movement_array[$count]['section'] = "Transfer_From_demand_receive";
        $movement_array[$count]['related_id'] = 0;
}



$ck9 = $conn_me->prepare("SELECT A.* FROM `demand_receive` A where $CONDITION5 ( A.`invoice_date` BETWEEN '".$date_from."' AND '".$date_to."' ) ");
$ck9->execute();

$fe_ck9 = $ck9->fetchAll(PDO::FETCH_ASSOC);
foreach($fe_ck9 AS $fetch9){

    $info_warehouse = SETUP::SETUP_WAREHOUSE($fetch9['dispatch_from_warehouse']);
    $info_warehouse2 = SETUP::SETUP_WAREHOUSE($fetch9['received_warehouse']);

        $count  = $count+1;
        $movement_array[$count]['serial'] = $count;
        $movement_array[$count]['date'] = $fetch9['invoice_date'];
        $movement_array[$count]['time'] = $fetch9['time'];
        $movement_array[$count]['poster'] = $fetch9['poster'];
        $movement_array[$count]['qty'] = $fetch9['quantity'];
        $movement_array[$count]['description'] = "Transfer To $info_warehouse2[name] >> From $info_warehouse[name]";
        $movement_array[$count]['type'] = 'IN';
        $movement_array[$count]['link'] = 'No LInk';
        $movement_array[$count]['section'] = "Transfer_To_demand_receive";
        $movement_array[$count]['related_id'] = 0;
}






$stm = $conn_me->prepare("DROP table IF EXISTS tempTable_product_movement");
$stm->execute();

$stmt2 = $conn_me->prepare("CREATE TABLE tempTable_product_movement (
    id int NOT NULL AUTO_INCREMENT,
    movment_date date  NULL,
    time varchar(255)  NULL,
    poster int(11)  NULL,
    note varchar(255)  NULL,
    in_amount float(20,2)  NULL,
    out_amount float(20,2)  NULL,
    link TEXT  NULL,
    section TEXT  NULL,
    related_id int(11)  NULL,

    PRIMARY KEY (id)
);
");
$stmt2->execute();


foreach($movement_array as $item) {
   
    $date = date("Y-m-d", strtotime($item['date']));



    $query = $conn_me->exec("INSERT INTO `tempTable_product_movement` 
    ( 
        `id`, `movment_date`,`time`,`poster`,`note`,`in_amount`, `out_amount`,`link`,`section`,`related_id`
    
    ) 
    VALUES
    (
        '0',
        '".$date."',
        '".$item['time']."',
        '".$item['poster']."',
        '".$item['description']."',
        IF('".$item['type']."' = 'IN','".$item['qty']."',0.00),
        IF('".$item['type']."' = 'OUT','".$item['qty']."',0.00),
        '".$item['link']."',
        '".$item['section']."',
        '".$item['related_id']."'



    ) ");

}
       


 
            
              }


              static    function FG_PRICE_DURING_SALE($CODE,$PRODUCT_ID){

        
                $conn_me = Database::getInstance();

                $query2 = $conn_me->prepare("SELECT `purches_price`  FROM  `fg_local_purches`  WHERE `code` = '".$CODE."' AND `product_id` = '".$PRODUCT_ID."'   ");
                $query2->execute();
                $fetch_list = $query2->fetch(PDO::FETCH_ASSOC);
      
                              
                
                return array(
                'price' => $fetch_list['purches_price']
                );
                
                    
                      }


                      static      function RAW_PRICE_DURING_SALE($CODE,$PRODUCT_ID){

                        $conn_me = Database::getInstance();


                        $query2 = $conn_me->prepare("SELECT * FROM  `raw_local_purches`  WHERE `code` = '".$CODE."' AND `product_id` = '".$PRODUCT_ID."'   ");
                        $query2->execute();
                        $fetch_list = $query2->fetch(PDO::FETCH_ASSOC);

if(!empty($fetch_list['purches_price'])) { $purches_price = $fetch_list['purches_price'] ; }else{$purches_price = 0.00 ;}

if(!empty($fetch_list['invoice_no'])) { $invoice_no = $fetch_list['invoice_no'] ; }else{$invoice_no = '' ;}                          
                        
                        return array(
                        'price' => $purches_price,
                        'invoice_no' =>$invoice_no
                        );
                        
                            

                    }



                    


                    static   function INVOCIE_WISE_PREVIOUS_RETURN($INVOICEID,$PRODUCT_ID){

                        $conn_me = Database::getInstance();


                        $count_pcs = 0;
                        $count_carton = 0;

                        $query1 = $conn_me->prepare("SELECT *  FROM  `sales_return_invoice`  WHERE `sale_invoice_id` = '".$INVOICEID."'  ");
                        $query1->execute();
                        $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($fetch_list1 as $fetch1) { 

                            $query2 = $conn_me->prepare("SELECT SUM(`return_quantity`) AS `qty`,SUM(`return_carton`) as `carton` FROM  `sales_return_invoice_item`  WHERE `return_invoice_id` = '".$fetch1['id']."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id`  ");
                            $query2->execute();
                            $fetch_list2 = $query2->fetch(PDO::FETCH_ASSOC);
                            if( $query2->rowCount() > 0 ){
                                $count_pcs += $fetch_list2['qty'];
                                $count_carton += $fetch_list2['carton'];
                            }else{
                                $count_pcs += 0.00;
                                $count_carton += 0.0;
                            }
                            
                           


                        }
                       
                        
                                        
                        
                        return array(
                        'count_pcs' => $count_pcs,
                        'count_carton' => $count_carton
                        );

                    }


                    

                    
                    static   function PRODUCT_DAMAGE_INVOICE_VALUE($INVOICEID){

                        $conn_me = Database::getInstance();


                        $total_subtotal = 0;

                        $query1 = $conn_me->prepare("SELECT *  FROM  `damage_invoice`  WHERE `sale_invoice_id` = '".$INVOICEID."'  ");
                        $query1->execute();
                        $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($fetch_list1 as $fetch1) { 

                            $sub_total = 0;
                            $query2 = $conn_me->prepare("SELECT `sales_rate`,`damage_quantity`  FROM  `damage_invoice_item`  WHERE `damage_invoice_id` = '".$fetch1['id']."'  ");
                            $query2->execute();
                            $fetch_list = $query2->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($fetch_list as $fetch2) { 
                                $sub_total += number_format((float)( $fetch2['sales_rate']*$fetch2['damage_quantity']), 2, '.', '');
                            }

                            $total_subtotal += $sub_total;
                        }
                       
                        
                        $invoice_price = number_format((float)( ( $total_subtotal ) ), 2, '.', '');
    
    
                        return array(
                        'price' => $invoice_price
                        );
                        
                    
                        
                          }

                          static    function PRODUCT_RETURN_INVOICE_VALUE($INVOICEID){

        
                            $conn_me = Database::getInstance();

                            $total_subtotal = 0;
    
                                $query2 = $conn_me->prepare("SELECT `sales_rate`,`return_quantity`  FROM  `sales_return_invoice_item`  WHERE `return_invoice_id` = '".$INVOICEID."'  ");
                                $query2->execute();
                                $fetch_list = $query2->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($fetch_list as $fetch2) { 
                             
                                    $total_subtotal +=number_format((float)( $fetch2['sales_rate']*$fetch2['return_quantity']), 2, '.', '');
                                }
    
                              
                            
                           
                            
                            $invoice_price = number_format((float)( ( $total_subtotal ) ), 2, '.', '');
        
        
                            return array(
                            'price' => $invoice_price
                            );
                            
                        
                            
                              }

                              static     function INVOCIE_WISE_PREVIOUS_DAMAGE($INVOICEID,$PRODUCT_ID){

                                $conn_me = Database::getInstance();


                                $count_pcs = 0;
                                $count_carton = 0;
        
                                $query1 = $conn_me->prepare("SELECT *  FROM  `damage_invoice`  WHERE `sale_invoice_id` = '".$INVOICEID."'  ");
                                $query1->execute();
                                $fetch_list1 = $query1->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($fetch_list1 as $fetch1) { 
        
                                    $query2 = $conn_me->prepare("SELECT SUM(`damage_quantity`) AS `qty`,SUM(`damage_carton`) as `carton` FROM  `damage_invoice_item`  WHERE `damage_invoice_id` = '".$fetch1['id']."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id`  ");
                                    $query2->execute();
                                    $fetch_list2 = $query2->fetch(PDO::FETCH_ASSOC);
                                    $count_pcs += $fetch_list2['qty'];
                                    $count_carton += $fetch_list2['carton'];
        
        
                                }
                               
                                
                                                
                                
                                return array(
                                'count_pcs' => $count_pcs,
                                'count_carton' => $count_carton
                                );
                                
                                    
                                      }

                                      static function TOTAL_SALES_RETURN_INVOICE_PRICE($INVOICEID){

        $conn_me = Database::getInstance();


        $sub_total = 0;
        $query2 = $conn_me->prepare("SELECT *  FROM  `sales_return_invoice_item`  WHERE `return_invoice_id` = '".$INVOICEID."'  ");
        $query2->execute();
        $fetch_list = $query2->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch2) { 
            $sub_total += number_format((float)( $fetch2['sales_rate']*$fetch2['return_quantity']), 2, '.', '');
        }
        
        
        $invoice_price = number_format((float)( ( $sub_total ) ), 2, '.', '');
        
        
        return array(
        'price' => $invoice_price
        );
        
            
              }
        

              static  function TOTAL_PURCHASE_INVOICE_PRICE($CODE,$TABLE){

                $conn_me = Database::getInstance();


                $query = $conn_me->prepare("SELECT *  FROM  `{$TABLE}`  WHERE `code` = '".$CODE."' GROUP BY `code` ");
                $query->execute();
                $fetch = $query->fetch(PDO::FETCH_ASSOC);
                
                if(!empty($fetch['transport_cost'])){ $transport_cost = number_format((float)$fetch['transport_cost'], 2, '.', ''); }else{$transport_cost = 0.00;}
                
                if(!empty($fetch['vat_cost'])){ $vat_cost = number_format((float)$fetch['vat_cost'], 2, '.', ''); }else{$vat_cost = 0.00;}

                
                
                $sub_total = 0;
                $query2 = $conn_me->prepare("SELECT *  FROM  `{$TABLE}`  WHERE `code` = '".$CODE."'  ");
                $query2->execute();
                $fetch_list = $query2->fetchAll(PDO::FETCH_ASSOC);
                foreach ($fetch_list as $fetch2) { 
                    $sub_total += number_format((float)( $fetch2['purches_price']*$fetch2['quantity']), 2, '.', '');
                }
                
                
                $invoice_price = number_format((float)( ( $sub_total  + $transport_cost + $vat_cost ) ), 2, '.', '');
                $sub_total_without_other_cost = number_format((float)( ( $sub_total  ) ), 2, '.', '');
                
                
                return array(
                'invoice_price' => $invoice_price,
                'sub_total' => $sub_total,
                'sub_total_without_other_cost' => $sub_total_without_other_cost
                
                
                );
                
                    
                      }


                      static   function SALES_INVOICE_PENDING_ITEM_PRICE($INVOICEID){

                        $conn_me = Database::getInstance();
                
                
                $sub_total = 0;
                
                $query2 = $conn_me->prepare("SELECT `sales_rate`,`sales_quantity`  FROM  `sales_invoice_item`  WHERE `sales_invoice_id` = '".$INVOICEID."' AND `status` = 'Pending' ");
                $query2->execute();
                $fetch_list = $query2->fetchAll(PDO::FETCH_ASSOC);
                foreach ($fetch_list as $fetch2) { 
                    $sub_total += number_format((float)( $fetch2['sales_rate']*$fetch2['sales_quantity']), 2, '.', '');
                   
                }
                
                $sub_total = number_format((float)( $sub_total), 2, '.', '');
                
                return array(
                'sub_total' => $sub_total
                
                );
                
                    
                      }



                      static function TOTAL_SALES_INVOICE_PRICE($INVOICEID){

        $conn_me = Database::getInstance();


$query = $conn_me->prepare("SELECT *  FROM  `sales_invoice`  WHERE `id` = '".$INVOICEID."' ");
$query->execute();
$fetch = $query->fetch(PDO::FETCH_ASSOC);

if(!empty($fetch['discount'])){
    $discount = number_format((float)$fetch['discount'], 2, '.', '');
}else{
    $discount = 0.00;
}

if(!empty($fetch['transport_cost'])){
    $transport_cost = number_format((float)$fetch['transport_cost'], 2, '.', '');
}else{
    $transport_cost = 0.00;
}

if(!empty($fetch['total_vat_cost'])){
    $total_vat_cost = number_format((float)$fetch['total_vat_cost'], 2, '.', '');
}else{
    $total_vat_cost = 0.00;
}



$sub_total = 0;
$total_pcs = 0;
$total_ctn = 0;

$query2 = $conn_me->prepare("SELECT *  FROM  `sales_invoice_item`  WHERE `sales_invoice_id` = '".$INVOICEID."'  ");
$query2->execute();
$fetch_list = $query2->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch2) { 
    $sub_total += number_format((float)( $fetch2['sales_rate']*$fetch2['sales_quantity']), 2, '.', '');
    $total_pcs += $fetch2['sales_quantity'];
    $total_ctn += $fetch2['carton_receive'];
}

$sub_total = number_format((float)( $sub_total), 2, '.', '');
$sub_total_without_discout = number_format((float)( ( $sub_total  + $transport_cost + $total_vat_cost ) ), 2, '.', '');
$invoice_price = number_format((float)( $sub_total_without_discout - $discount ), 2, '.', '');


return array(
'price' => $invoice_price,
'sub_total' => $sub_total,
'total_pcs' => $total_pcs,
'total_ctn' => $total_ctn,
'sub_total_without_discout' => $sub_total_without_discout,
'transport_cost' => $transport_cost,
'discount' => $discount,
'total_vat_cost' => $total_vat_cost



);

    
      }


      static  function WAREHOUSE_WISE_TOTAL_SALES_INVOICE_PRICE($INVOICEID,$type){

        $conn_me = Database::getInstance();


    if($type == 'NOWAPBUR'){
    $QUERY   = " AND warehouse_id IN ('14,15') ";
    }else{
    $QUERY   = " AND warehouse_id NOT IN  ('14,15') ";
    }

$query = $conn_me->prepare("SELECT *  FROM  `sales_invoice`  WHERE `id` = '".$INVOICEID."' ");
$query->execute();
$fetch = $query->fetch(PDO::FETCH_ASSOC);

if(!empty($fetch['discount'])){
    $discount = number_format((float)$fetch['discount'], 2, '.', '');
}else{
    $discount = 0.00;
}

if(!empty($fetch['transport_cost'])){
    $transport_cost = number_format((float)$fetch['transport_cost'], 2, '.', '');
}else{
    $transport_cost = 0.00;
}

if(!empty($fetch['total_vat_cost'])){
    $total_vat_cost = number_format((float)$fetch['total_vat_cost'], 2, '.', '');
}else{
    $total_vat_cost = 0.00;
}



$sub_total = 0;
$total_pcs = 0;
$total_ctn = 0;

$query2 = $conn_me->prepare("SELECT *  FROM  `sales_invoice_item`  WHERE `sales_invoice_id` = '".$INVOICEID."' $QUERY ");
$query2->execute();
$fetch_list = $query2->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch2) { 
    $sub_total += number_format((float)( $fetch2['sales_rate']*$fetch2['sales_quantity']), 2, '.', '');
    $total_pcs += $fetch2['sales_quantity'];
    $total_ctn += $fetch2['carton_receive'];
}

$sub_total = number_format((float)( $sub_total), 2, '.', '');
$sub_total_without_discout = number_format((float)( ( $sub_total  + $transport_cost + $total_vat_cost ) ), 2, '.', '');
$invoice_price = number_format((float)( $sub_total_without_discout - $discount ), 2, '.', '');


return array(
'price' => $invoice_price,
'sub_total' => $sub_total,
'total_pcs' => $total_pcs,
'total_ctn' => $total_ctn,
'sub_total_without_discout' => $sub_total_without_discout,
'transport_cost' => $transport_cost,
'discount' => $discount,
'total_vat_cost' => $total_vat_cost



);

    
      }


      static  function TOTAL_SALES_ITEM_PURCHASE_PRICE($INVOICEID,$invoice_date){

        $conn_me = Database::getInstance();


$sub_total = 0;

$query2 = $conn_me->prepare("SELECT A.`sales_quantity`,A.product_id  FROM  `sales_invoice_item` A  where `sales_invoice_id` = '".$INVOICEID."' ");
$query2->execute();
$fetch_list = $query2->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch2) { 

    $product_price = SETUP::getProductPriceOnTransferDate($invoice_date,$fetch2['product_id']);
    $sub_total += number_format((float)( $product_price*$fetch2['sales_quantity']), 2, '.', '');
  
}

$sub_total = number_format((float)( $sub_total), 2, '.', '');


return array(
'total_purchase_price' => $sub_total

);

    
      }


      static  function TOTAL_QUATATION_INVOICE_PRICE($INVOICEID){

        $conn_me = Database::getInstance();



$sub_total = 0;
$total_pcs = 0;
$total_ctn = 0;

$query2 = $conn_me->prepare("SELECT *  FROM  `quotation_invoice_item`  WHERE `quotation_invoice_id` = '".$INVOICEID."'  ");
$query2->execute();
$fetch_list = $query2->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch2) { 
    $sub_total += number_format((float)( $fetch2['recommended_price']*$fetch2['quantity']), 2, '.', '');
    $total_pcs += $fetch2['quantity'];
}

$invoice_price = number_format((float)( $sub_total), 2, '.', '');


return array(
'invoice_price' => $invoice_price


);

    
      }


      static  function TOTAL_PREORDER_INVOICE_PRICE($INVOICEID){

        $conn_me = Database::getInstance();



$sub_total = 0;
$total_pcs = 0;
$total_ctn = 0;

$query2 = $conn_me->prepare("SELECT *  FROM  `pre_order_invoice_item`  WHERE `preorder_invoice_id` = '".$INVOICEID."'  ");
$query2->execute();
$fetch_list = $query2->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch2) { 
    $sub_total += number_format((float)( $fetch2['recommended_price']*$fetch2['quantity']), 2, '.', '');
    $total_pcs += $fetch2['quantity'];
}

$invoice_price = number_format((float)( $sub_total), 2, '.', '');


return array(
'invoice_price' => $invoice_price


);

    
      }



      static function COUNT_DONE_DATA($TABLE,$STATUS,$CODE){

        $conn_me = Database::getInstance();


        $query = $conn_me->prepare("SELECT *  FROM  `{$TABLE}`  WHERE `{$STATUS}` = 'Done' AND `code` = '".$CODE."' ");
        $query->execute();
        $count =  $query->rowCount();
       
   
    
        return array(
            'count' => $count

            );

    
      }


      static  function Attendance_Status_With_Check_In_Check_Out_Time($checkInTime, $lateTime, $holidays, $leave, $currentDate) {
        // If the employee has a check-in time
        if ($checkInTime) {
          // Check if the employee is late
          if (strtotime($checkInTime) > strtotime($lateTime)) {
            $mess = 'Lt';
          } else {
            $mess = 'P';
          }
        } else {
          // Check if it is a predefined holiday
          if (in_array($currentDate, $holidays)) {
            $mess = 'H';
          }
          // Check if it is Friday
          if (date('l', strtotime($currentDate)) == 'Friday') {
            $mess = 'F';
          }
          // Check if the employee is on leave
          if (in_array($currentDate, $leave)) {
            $mess = 'Lev';
          }
          // If none of the above conditions are met, return 'A' for absent
          $mess = 'A';
        }

        return array(
            'mess' => $mess

            );

      }
      

      static  function EMPLOYEE_WISE_HOLIDAY($EMPLOYEE_ID,$DATE){
        $conn_me = Database::getInstance();

    $query = $conn_me->prepare("SELECT * FROM `setup_holiday` WHERE  `holiday` = '".$DATE."' ");
    $query->execute();        
    if($query->rowCount() > 0 ){
    $mess=  $DATE;
    }else{
        $mess= '';
    }

    return array(
        'mess' => $mess

        );


    }

    static function EMPLOYEE_WISE_LEAVE($EMPLOYEE_ID,$DATE){
        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT * FROM `apply_leave` WHERE `employee_id` = '".$EMPLOYEE_ID."' AND (`leave_from_date` <= '".$DATE."' AND `leave_to_date` >= '".$DATE."')");
        $query->execute();        
        if($query->rowCount() > 0 ){
        $mess=  $DATE;
        }else{
            $mess= '';
        }
    
        return array(
            'mess' => $mess
    
            );
    
    
        }




        static function checkAttendanceStatus($employee_id, $attendance_date)
{
    
    $conn_me = Database::getInstance();

    // Prepare and execute the query
    $query = "SELECT `present`, `late`, `absent`, `leave` FROM take_attandance  WHERE employee_id = :employee_id AND attandance_date = :attandance_date";
    $stmt =  $conn_me->prepare($query);
    $stmt->bindParam(':employee_id', $employee_id);
    $stmt->bindParam(':attandance_date', $attendance_date);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        if ($result['present'] == 1) {
            return "P";
        } elseif ($result['late'] == 1) {
            return "Lt";
        } elseif ($result['absent'] == 1) {
            return "A";
        } elseif ($result['leave'] == 1) {
            return "L";
        }
    }

    return "No attendance record found for the given employee and date.";
}



      
static function TAKE_ATTANDANCE_FROM_MACHINE_DATA($EMPLOYEE_ID,$DATE){

        $conn_me = Database::getInstance();


    $query = $conn_me->prepare("SELECT * FROM `check_in_check_out` WHERE `employee_id` = '".$EMPLOYEE_ID."' AND  `attendance_date` = '".$DATE."'  ORDER BY lastupdate ASC LIMIT 1");
    $query->execute();
    $count=  $query->rowCount();
    $fetch = $query->fetch(PDO::FETCH_ASSOC);

    if( $count > 0 ){
        $checkInTime = "$DATE $fetch[check_in]";

    }else{
        $checkInTime = "";
    }
    
    $info_timeTable = SETUP::SETUP_TIMETABLE();
    $info_holidays = FIND::EMPLOYEE_WISE_HOLIDAY($EMPLOYEE_ID,$DATE);
    $info_leave = FIND::EMPLOYEE_WISE_LEAVE($EMPLOYEE_ID,$DATE);

    $lateTime = $DATE . ' ' . $info_timeTable['count_late_time'] . ' am' ;
    $holidays = array($info_holidays['mess']);
    $leave = array($info_leave['mess']);
    $currentDate = $DATE;

   $attendanceStatus = FIND::Attendance_Status_With_Check_In_Check_Out_Time($checkInTime, $lateTime, $holidays, $leave, $currentDate);

if($attendanceStatus['mess'] == 'P'){
    $leave = 0;
    $late = 0;
    $present = 1;
    $absent = 0;
}else if ($attendanceStatus['mess'] == 'Lt'){
    $leave = 0;
    $late = 1;
    $present = 0;
    $absent = 0;
}else if($attendanceStatus['mess'] == 'Lev'){
    $leave = 1;
    $late = 0;
    $present = 0;
    $absent = 0;
}else if($attendanceStatus['mess'] == 'A'){
    $leave = 0;
    $late = 0;
    $present = 0;
    $absent = 1;
}else{
    $leave = 0;
    $late = 0;
    $present = 0;
    $absent = 0;
}
        return array(
            'value' => $attendanceStatus['mess'],
            'leave' => $leave,
            'late' => $late,
            'present' => $present,
            'absent' => $absent
      
        
            );

    
      }





      static function LEAVE($EMPLOYEE_ID,$DATE){

        $conn_me = Database::getInstance();


        $query = $conn_me->prepare("SELECT * FROM apply_leave WHERE `employee_id` = '".$EMPLOYEE_ID."' AND (( `leave_from_date` <= '".$DATE."' AND `leave_to_date` >= '".$DATE."'  ) OR (`leave_from_date` >= '".$DATE."'))  ");
        $query->execute();
        $count =  $query->rowCount();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);
       
        if( $count > 0 ){
        $mess = 'disabled';
        $in_leave = 1;
        }else{
            $mess= '';
            $in_leave = 0;
        }           
          
          
        return array(
            'mess' => $mess,
            'in_leave' => $in_leave

            );

    
      }


      static function ADVANCE_TRANSECTION($DATE,$EMPLOYEEID){
    $conn_me = Database::getInstance();

    $date1 = "31-$DATE";
    $given_date = date("Y-m-d", strtotime($date1));

    $date2 = new DateTime($given_date);
    $date2->modify('first day of -1 month');
    $previous_month = $date2->format('Y-m-d');

    
    $query = $conn_me->prepare("SELECT  sum(`out_amount`) AS `total_out`  FROM `account_transection` where ( DATE_FORMAT(`transection_date`, '%m-%Y') = '".$DATE."'  ) AND `transection_to_id` = '".$EMPLOYEEID."' AND `transection_to` = 'Employee' AND `transection_head_id` = '33'  GROUP BY `transection_to_id`");
    $query->execute();
    $fetch = $query->fetch(PDO::FETCH_ASSOC);

    IF(!empty($fetch['total_out'])){ $this_month_advance = $fetch['total_out'] ; }else{ $this_month_advance = 0.00 ;}

       
    $query1 = $conn_me->prepare("SELECT  sum(`out_amount`) AS `total_out`  FROM `account_transection` where ( `transection_date` BETWEEN  '".$previous_month."' AND '".$given_date."' ) AND `transection_to_id` = '".$EMPLOYEEID."' AND `transection_to` = 'Employee' AND `transection_head_id` = '33'  GROUP BY `transection_to_id`");
    $query1->execute();
    $fetch1 = $query1->fetch(PDO::FETCH_ASSOC);

    $query2 = $conn_me->prepare("SELECT  sum(`out_amount`) AS `total_out`  FROM `account_transection` where ( `transection_date`  <  '".$previous_month."'  ) AND `transection_to_id` = '".$EMPLOYEEID."' AND `transection_to` = 'Employee' AND `transection_head_id` = '33'  GROUP BY `transection_to_id`");
    $query2->execute();
    $fetch2 = $query2->fetch(PDO::FETCH_ASSOC);

    IF(!empty($fetch1['total_out'])){ $last_month_advance = $fetch1['total_out'] ; }else{ $last_month_advance = 0.00 ;}
    IF(!empty($fetch2['total_out'])){ $previous_advance = $fetch2['total_out'] ; }else{ $previous_advance = 0.00 ;}

    $total_advance_paid = $last_month_advance+$previous_advance;
    return array(
        'last_month_advance' => $last_month_advance,
        'this_month_advance' => $this_month_advance,
        'previous_advance' => $previous_advance,
        'total_advance_paid' => $total_advance_paid
        );



}




static function TOTAL_ACTIVITY_GIVEN_MONTH($DATE,$TYPE,$EMPLOYEEID){
    $conn_me = Database::getInstance();



$explode = explode("-",$DATE);
$month = $explode[0];
$year = $explode[1];
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);


$vacation_days = []; 
$query = $conn_me->prepare("SELECT DAY(`holiday`) as holiday  FROM setup_holiday  WHERE MONTH(`holiday`) = '".$month."' AND `holiday_year` = '".$year."' ;
");
$query->execute();
$fetch_all = $query->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_all as $fetch) {
  $vacation_days[] = $fetch['holiday'];
}

$total = 0;
for ($i = 1; $i <= $daysInMonth; $i++) {

    $date = new DateTime("$year-$month-$i");
    $day_of_week = $date->format('N');
    $day_of_month = $date->format('j');

    $attandance_date = date("Y-m-d", strtotime("$year-$month-$day_of_month"));


    $query =$conn_me->prepare("SELECT  `{$TYPE}`   FROM `take_attandance` where `attandance_date` = '".$attandance_date."' AND `employee_id` = '".$EMPLOYEEID."' ");
    $query->execute();
    $fetch = $query->fetch(PDO::FETCH_ASSOC);
    
    if( $query->rowCount() > 0 ){
        $total += $fetch['absent'];
    }else{
      
        if ($day_of_week == 5) { // Friday
            $total += 0;
        }else{
            if (in_array($day_of_month, $vacation_days)) {
                $total += 0;
            }else{
                $total += 1;
    
            }
        }
       

    }
  
}



    return array(
        'total_days' => $total
        );


 }





  


 static function UPDATE_STATUS($FIELD1,$FIELD2,$FIELD3,$TABLE,$CODE){

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("UPDATE `{$TABLE}` 

        SET
        `{$FIELD1}` = '" . date("Y-m-d") . "',
        `{$FIELD2}` = 'Done',
        `{$FIELD3}` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "'


        WHERE `code` = '".$CODE."'");

        $query->execute();

    
    }



    static function WAREHOUSE_LIST($TYPE,$MATERIALID,$SELECTED_WAREHOUSE){

        $conn_me = Database::getInstance();
        $total_pcs = 0;
        $total_ctn = 0;
        $sl = 1;
        $report = '<option value="">Select One</option>';
        $content = '<div class="table-responsive"><table class="table salestable" style="margin-bottom: 0px !important;"><tr><th>Sl</th><th>Warehouse</th><th>Pcs</th><th>Carton</th></tr>';


        
        $qry = $conn_me->prepare("SELECT * FROM `setup_brunch`  where `id` = '". $_SESSION['USER_BRUNCH']."' ");
        $qry->execute();
        $fetch_list = $qry->fetch(PDO::FETCH_ASSOC);
        $jsonData = $fetch_list['related_warehouse'];
        $obj = json_decode($jsonData);
        foreach($obj as $key => $value) {
            $info_wh = SETUP::SETUP_WAREHOUSE($value);
        $stock = FIND::WAREHOUSE_WISE_STOCK($TYPE,$MATERIALID,$value);

        if($stock['warhouse_wise_stock'] > 0 ){

        $content .=  '<tr><td>'. $sl++ .'</td><td>'. $info_wh['name'].'</td><td>'. $stock['warhouse_wise_stock'].'</td><td>'. $stock['warhouse_wise_stock_in_carton'].'</td></tr>';

        $report .= '<option  ';
        if($SELECTED_WAREHOUSE == $value){
            $report .= ' selected="selected"';
        }else{

        }
        $report .= ' value="'.$value.'"> '.$info_wh['name'].'  ( STOCK ::: Pcs-' . $stock['warhouse_wise_stock'] .' Carton-'.$stock['warhouse_wise_stock_in_carton'].')</option>';
        
                $total_pcs += $stock['warhouse_wise_stock'];
                $total_ctn += $stock['warhouse_wise_stock_in_carton'];
        
        
        } else{
            
               $total_pcs += 0;
            $total_ctn +=0;
        }


        } 
        $content .='<tr><td colspan="2" style="text-align:right"><b>TOTAL</b></td><td><b> '.$total_pcs.' </b></td><td><b>'.$total_ctn.'</b></td></tr>';
        $content .='</table></div>';
    return array(
        'warehouse_list' => $report,
        'warehouse_content' => $content


        );

    }



    static  function PIPELINE_DETAILS($product_id,$BRUNCH_ID){

        $conn_me = Database::getInstance();

    $content=  '<table class="table" style="margin-bottom: 0px !important;"><tr><th>Sl</th><th>Invoice Date</th><th>Invoice No</th><th>Qty</th></tr>';
    
    
    $qry = $conn_me->prepare("SELECT * FROM `setup_brunch` where `id` = '".$BRUNCH_ID."'  ");
    $qry->execute();
    $fetch_list = $qry->fetch(PDO::FETCH_ASSOC);
    $a = $fetch_list['related_warehouse'];
    $b = str_replace("[","",$a);
    $c = str_replace("]","",$b);


    $sl = 1 ;
    $query = $conn_me->prepare("SELECT B.invoice_no,B.invoice_date,sum(A.sales_quantity) as total_item  FROM `sales_invoice_item` A JOIN sales_invoice B ON (A.sales_invoice_id = B.id) JOIN setup_product C ON (A.product_id = C.id) WHERE A.`product_id` = '" . $product_id . "' AND A.`warehouse_id` IN ($c)  AND A.`final_confirm_sales_person` = 'Pending' GROUP BY A.sales_invoice_id  ");
    $query->execute();
    $fetch_lists = $query->fetchAll(PDO::FETCH_ASSOC);
    
    
    
    foreach ($fetch_lists as $fetch) {

        $content .=  '<tr><td>'.$sl++.'</td><td>'.$fetch['invoice_date'].'</td><td>'.$fetch['invoice_no'].'</td><td>'.$fetch['total_item'].'</td></tr>';
  
    }

 
    $content .=  '</table>';

        return  $content ;

    }



    static  function CENTRAL_WAREHOUSE_LIST($TYPE,$MATERIALID,$SELECTED_WAREHOUSE){
        $conn_me = Database::getInstance();

        $total_pcs = 0;
        $total_ctn = 0;
        $sl = 1;
        $report = '<option value="">Select One</option>';
        $content = '<div class="table-responsive">
        <table class="table salestable" style="margin-bottom: 0px !important;"><tr><th>Sl</th><th>Warehouse</th><th>Pcs</th><th>Carton</th></tr>';
        $qry = $conn_me->prepare("SELECT * FROM `setup_warehouse`  where status = 1 ");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) {

        $stock = FIND::WAREHOUSE_WISE_STOCK($TYPE,$MATERIALID,$fetch['id']);

        // if($stock['warhouse_wise_stock'] > 0 ){

        $content .=  '<tr><td>'. $sl++ .'</td><td>'. $fetch['name'].'</td><td>'. $stock['warhouse_wise_stock'].'</td><td>'. $stock['warhouse_wise_stock_in_carton'].'</td></tr>';

        $report .= '<option  ';
        if($SELECTED_WAREHOUSE == $fetch['id']){
            $report .= ' selected="selected"';
        }else{

        }
        $report .= ' value="'.$fetch['id'].'"> '.$fetch['name'].'  ( STOCK ::: Pcs-' . $stock['warhouse_wise_stock'] .' Carton-'.$stock['warhouse_wise_stock_in_carton'].')</option>';
       // } 

        $total_pcs += $stock['warhouse_wise_stock'];
        $total_ctn += $stock['warhouse_wise_stock_in_carton'];

        } 
          $content .='<tr><td colspan="2" style="text-align:right"><b>TOTAL</b></td><td><b> '.$total_pcs.' </b></td><td><b>'.$total_ctn.'</b></td></tr>';

        $content .='</table></div>';
    return array(
        'warehouse_list' => $report,
        'warehouse_content' => $content


        );

    }


    static function SUPPLIER_OR_FACTORY($id,$send_to){

        $conn_me = Database::getInstance();


        if($send_to == 'Supplier' ){

            $info = SETUP::SETUP_SUPPLIER($id);
            $name = $info['supplier_name'];

        }else if($send_to == 'Factory'){
            $info = SETUP::SETUP_FACTORY($id);
            $name = $info['factory_name'];

        }else{
           
            $name = 'Wrong Data';

        }

        return array(
            'name' => $name

            );


    }
    



    static function TRANSECTION_SUMMERY_INCOME_EXPENSE($ID,$HEAD){
        $conn_me = Database::getInstance();


        $query2 = $conn_me->prepare("SELECT *    FROM `account_transection` where `transection_head_id` = '".$ID."'   ");
        $query2->execute();
        $fetch_list2 = $query2->fetchAll(PDO::FETCH_ASSOC);
        foreach($fetch_list2 AS $fetch2){

            $query = $conn_me->prepare("SELECT *    FROM `account_transection` where `id` = '".$fetch2['id']."'   ");
            $query->execute();
            $fetch_list1 = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($fetch_list1 AS $fetch){

                if(!empty($fetch['in_amount'])){
                    $income = $fetch['in_amount'];
         
                    }else{
                     $income = 0.00;
                    }
                    
                     
                    if(!empty($fetch['out_amount'])){
                    $expense = $fetch['out_amount'];
         
                    }else{
                     $expense = 0.00;
                    }

                    $query = $conn_me->exec("INSERT INTO `tem_table` 
                    ( 
                        `id`,`account_head`,`transection_date`,`time`,`note`,`brunch_id`,`transection_type`,`ac_transection_id`,`in_amount`,`out_amount`
                    
                    ) 
                    VALUES
                    (
                        '0',
                        '".$HEAD."',
                        '".$fetch['transection_date']."',
                        '".$fetch['time']."',
                        '".$fetch['note']."',
                        '".$fetch['brunch_id']."',
                        '".$fetch['transection_type']."',
                        '".$fetch['id']."',
                        '".$income."',
                        '".$expense."'
                    ) ");
                        

            }

        }
       
  

    }

    
    static  function getTotalInvoicePriceByBrunch($BRUNCHID,$DATE) {
        $conn_me = Database::getInstance();
    
    

        $BRUNCH_QUERY = " AND  si.`brunch_id` = '".$BRUNCHID."' ";
        
    
        $total_invoice = 0 ;
        $total_discount = 0;
        $total_trasport = 0;
        $total_vat = 0;
    
    
        // Calculate total invoice price directly in the SQL query
        $query = $conn_me->prepare("SELECT 
        si.id AS invoice_id,
        si.customer_id,
        SUM(sii.sales_quantity * sii.sales_rate) AS total_invoice_price,
        si.discount AS Discount,
        si.transport_cost AS Trasport,
        si.total_vat_cost AS VAT
    
    FROM 
        sales_invoice si
    JOIN 
        sales_invoice_item sii ON si.id = sii.sales_invoice_id
    WHERE 
       si.invoice_date <= '".$DATE."' AND si.generate_challan = 'Done' $BRUNCH_QUERY
    GROUP BY 
        invoice_id, customer_id");
        $query->execute();
            $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($fetch_list as $fetch) { 
    
    
            $total_invoice += number_format((float)( $fetch['total_invoice_price']), 2, '.', '');
            $total_discount += number_format((float)( $fetch['Discount']), 2, '.', '');
            $total_trasport += number_format((float)( $fetch['Trasport']), 2, '.', '');
            $total_vat += number_format((float)( $fetch['VAT']), 2, '.', '');
               
    
               }
    
    
    
    $sub_total = number_format((float)( ( $total_invoice  + $total_trasport + $total_vat ) ), 2, '.', '');
    $invoice_price = number_format((float)( $sub_total - $total_discount ), 2, '.', '');
    
        return $invoice_price;
    }






static function getTotalTransectionWithBrunch($BRUNCHID,$DATE) {
    $conn_me = Database::getInstance();



    $BRUNCH_QUERY = " AND  `brunch_id` = '".$BRUNCHID."' ";
    

    // Calculate total invoice price directly in the SQL query
    $query = $conn_me->prepare("SELECT  IFNULL(SUM(in_amount), 0) as INAMOUNT , IFNULL(SUM(out_amount), 0) AS OUTAMOUNT
        FROM account_transection
        WHERE transection_date <= '".$DATE."' $BRUNCH_QUERY GROUP BY transection_to_id ");
    $query->execute();

if ($query->rowCount() > 0) {
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $INAMOUNT = number_format((float)$result['INAMOUNT'], 2, '.', '');
        $OUTAMOUNT = number_format((float)$result['OUTAMOUNT'], 2, '.', '');

        } else {
        $INAMOUNT = 0.00;
        $OUTAMOUNT = 0.00;
        }

        return array(
            'INAMOUNT' => $INAMOUNT,
            'OUTAMOUNT' => $OUTAMOUNT
            );
}





static function BRUNCHERDUE($BRUNCHID, $DATE)
{
    $conn_me = Database::getInstance();


    $invoice_amount = FIND::getTotalInvoicePriceByBrunch( $BRUNCHID, $DATE);
    $transection_amount = FIND::getTotalTransectionWithBrunch( $BRUNCHID, $DATE);

    $customer_due = number_format((float)( 

        ( $invoice_amount +  $transection_amount['OUTAMOUNT'] ) 

        - 
        (  $transection_amount['INAMOUNT'] )   
    
    ), 2, '.', '');
    


    $total_tr = number_format((float)(  ( $transection_amount['INAMOUNT'] - $transection_amount['OUTAMOUNT'] )), 2, '.', '');
    return [
        
            'customer_due' => $customer_due

    ];
}




static function getAllCustomerDues($report_type,$relatedIds, $date_to,$brunch = '')
{
    $date_to = date("Y-m-d", strtotime($date_to));

        
    if($report_type == 'Multipal-Customer-Wise' ){

    $selectedValues = implode(',', $relatedIds);
    $QUERY = " WHERE A.`id` IN ({$selectedValues}) AND A.`in_service` = 'checked' ";
    $SALES_BRUNCH_QUERY = '';
    $ACCOUNTS_BRUNCH_QUERY = '';
    $RETURN_BRUNCH_QUERY = '';
    $BALANCE_BRUNCH_QUERY = '';

}else if($report_type == 'Brunch-Wise-All-Customer'){


  $selectedValues = implode(',', $relatedIds);          
  $QUERY = " WHERE  A.`in_service` = 'checked' ";     
    $SALES_BRUNCH_QUERY =  ($brunch == 'All' ) ? "  " : " AND  si.`brunch_id` = '".$brunch."' " ;
    $ACCOUNTS_BRUNCH_QUERY =  ($brunch == 'All' ) ? "  " : " AND `brunch_id` = '".$brunch."' " ;
    $RETURN_BRUNCH_QUERY =  ($brunch == 'All' ) ? "  " : " AND si.`brunch_id` = '".$brunch."' " ;
    $BALANCE_BRUNCH_QUERY =  ($brunch == 'All' ) ? " " : "  AND `brunch_id` = '".$brunch."'  " ;
  


}else if($report_type == 'Brunch-Wise-Multipal-Customer-Wise'){

    $selectedValues = implode(',', $relatedIds);
    $QUERY = " WHERE A.`id` IN ({$selectedValues}) AND A.`in_service` = 'checked' ";
    $SALES_BRUNCH_QUERY =  ($brunch == 'All' ) ? "  " : " AND  si.`brunch_id` = '".$brunch."' " ;
    $ACCOUNTS_BRUNCH_QUERY =  ($brunch == 'All' ) ? "  " : " AND `brunch_id` = '".$brunch."' " ;
    $RETURN_BRUNCH_QUERY =  ($brunch == 'All' ) ? "  " : " AND si.`brunch_id` = '".$brunch."' " ;
    $BALANCE_BRUNCH_QUERY =  ($brunch == 'All' ) ? " " : "  AND `brunch_id` = '".$brunch."'  " ;

}else if($report_type == 'Brunch-Wise-Single-Customer-Wise'){


    $selectedValues = $relatedIds;
    $QUERY = " WHERE A.`id` = '".$selectedValues."' ";
    $SALES_BRUNCH_QUERY =  ($brunch == 'All' ) ? "  " : " AND  si.`brunch_id` = '".$brunch."' " ;
    $ACCOUNTS_BRUNCH_QUERY =  ($brunch == 'All' ) ? "  " : " AND `brunch_id` = '".$brunch."' " ;
    $RETURN_BRUNCH_QUERY =  ($brunch == 'All' ) ? "  " : " AND si.`brunch_id` = '".$brunch."' " ;
    $BALANCE_BRUNCH_QUERY =  ($brunch == 'All' ) ? " " : "  AND `brunch_id` = '".$brunch."'  " ;

}else if($report_type == 'Single-Customer-Wise'){

    $selectedValues = $relatedIds;
    $QUERY = " WHERE A.`id` = '".$selectedValues."' ";
    $SALES_BRUNCH_QUERY = '';
    $ACCOUNTS_BRUNCH_QUERY = '';
    $RETURN_BRUNCH_QUERY = '';
    $BALANCE_BRUNCH_QUERY = '';

    }else if($report_type == 'Multipal-Division-Wise'){
            
    $selectedValues = implode(',', $relatedIds);
    $QUERY = " WHERE A.`division_id` IN ({$selectedValues}) AND A.`in_service` = 'checked' ";
    $SALES_BRUNCH_QUERY = ''; 
    $ACCOUNTS_BRUNCH_QUERY = '';
    $RETURN_BRUNCH_QUERY = '';
    $BALANCE_BRUNCH_QUERY = '';

    }else if($report_type == 'Multipal-District-Wise'){

    $selectedValues = implode(',', $relatedIds);
    $QUERY = " WHERE A.`district_id` IN ({$selectedValues}) AND A.`in_service` = 'checked' ";
    $SALES_BRUNCH_QUERY = '';
    $ACCOUNTS_BRUNCH_QUERY = '';
    $RETURN_BRUNCH_QUERY = '';
    $BALANCE_BRUNCH_QUERY = '';
        }else if($report_type == 'Multipal-Upazila-Wise'){

    $selectedValues = implode(',', $relatedIds);
    $QUERY = " WHERE A.`upazila_id` IN ({$selectedValues}) AND A.`in_service` = 'checked' ";
    $SALES_BRUNCH_QUERY = '';
    $ACCOUNTS_BRUNCH_QUERY = '';
    $RETURN_BRUNCH_QUERY = '';
    $BALANCE_BRUNCH_QUERY = '';
}else if($report_type == 'Multipal-Union-Wise'){

    $selectedValues = implode(',', $relatedIds);
    $QUERY = " WHERE A.`union_id` IN ({$selectedValues}) AND A.`in_service` = 'checked' ";
    $SALES_BRUNCH_QUERY = '';
    $ACCOUNTS_BRUNCH_QUERY = '';
    $RETURN_BRUNCH_QUERY = '';
    $BALANCE_BRUNCH_QUERY = '';

}else if($report_type == 'Branch-Wise'){

    $selectedValues = ' ';
    $QUERY = " WHERE  A.`in_service` = 'checked' ";     
    $SALES_BRUNCH_QUERY =  ($relatedIds == 'All' ) ? "  " : " AND  si.`brunch_id` = '".$relatedIds."' " ;
    $ACCOUNTS_BRUNCH_QUERY =  ($relatedIds == 'All' ) ? "  " : " AND `brunch_id` = '".$relatedIds."' " ;
    $RETURN_BRUNCH_QUERY =  ($relatedIds == 'All' ) ? "  " : " AND si.`brunch_id` = '".$relatedIds."' " ;
    $BALANCE_BRUNCH_QUERY =  ($relatedIds == 'All' ) ? " " : "  AND `brunch_id` = '".$relatedIds."'  " ;


}else if($report_type == 'Multipal-Union-Wise'){

    $selectedValues = implode(',', $relatedIds);
    $QUERY = " WHERE A.`union_id` IN ({$selectedValues}) AND A.`in_service` = 'checked' ";
    $SALES_BRUNCH_QUERY = '';
    $ACCOUNTS_BRUNCH_QUERY = '';
    $RETURN_BRUNCH_QUERY = '';
    $BALANCE_BRUNCH_QUERY = '';

}else{



  $selectedValues = implode(',', $relatedIds);          
  $QUERY = " WHERE  A.`in_service` = 'checked' ";     
  $SALES_BRUNCH_QUERY = '';
  $ACCOUNTS_BRUNCH_QUERY = '';
  $RETURN_BRUNCH_QUERY = '';
  $BALANCE_BRUNCH_QUERY = '';
        }
        
        
    $conn_me = Database::getInstance();

    // Fetch all required data in bulk
    $query = $conn_me->prepare("
    SELECT 
    A.id AS customer_id,
    SI.invoice_price,SI.brunch_id,
    TR.INAMOUNT,
    TR.tr_brunch,
    TR.OUTAMOUNT,
    PR.total_invoice_price AS product_return_amount,
    PR.return_brunch,
    ADJ.last_due_amount,
    ADJ.balance_brunch,
    ADJ.discount_amount
FROM 
    setup_customer A

LEFT JOIN (
    SELECT 
        sub.customer_id,sub.brunch_id,
        SUM(sub.total) AS invoice_price
    FROM (
        SELECT 
            si.customer_id,
            si.brunch_id,
            SUM(sii.sales_quantity * sii.sales_rate) - si.discount + si.transport_cost + si.total_vat_cost AS total
        FROM 
            sales_invoice si
        JOIN 
            sales_invoice_item sii ON si.id = sii.sales_invoice_id
        WHERE 
            si.generate_challan = 'Done'  AND  si.invoice_date <= :date   $SALES_BRUNCH_QUERY
        GROUP BY 
            si.customer_id, si.invoice_no
    ) sub
    GROUP BY 
        sub.customer_id
) SI ON A.id = SI.customer_id
LEFT JOIN (
    SELECT 
        transection_to_id AS customer_id,
        brunch_id as tr_brunch,
        SUM(in_amount) AS INAMOUNT,
        SUM(out_amount) AS OUTAMOUNT
    FROM 
        account_transection
    WHERE 
        transection_to = 'Customer' AND transection_date <= :date $ACCOUNTS_BRUNCH_QUERY
    GROUP BY 
        transection_to_id
) TR ON A.id = TR.customer_id
LEFT JOIN (
    SELECT 
        si.customer_id,
        si.brunch_id as return_brunch,
        SUM(sii.return_quantity * sii.sales_rate) AS total_invoice_price
    FROM 
        sales_return_invoice si
    JOIN 
        sales_return_invoice_item sii ON si.id = sii.return_invoice_id
    WHERE 
        si.warehouse_receive_date <= :date AND si.warehouse_receive = 'Done' $RETURN_BRUNCH_QUERY
    GROUP BY 
        si.customer_id
) PR ON A.id = PR.customer_id
LEFT JOIN (
    SELECT 
        customer_id,
        brunch_id as balance_brunch,
        SUM(CASE WHEN note = 'LAST DUE' THEN invoice_amount ELSE 0 END) AS last_due_amount,
        SUM(CASE WHEN note = 'DISCOUNT' THEN return_amount ELSE 0 END) AS discount_amount
    FROM 
        balance_customer
    WHERE 
        date <= :date $BALANCE_BRUNCH_QUERY
    GROUP BY 
        customer_id
) ADJ ON A.id = ADJ.customer_id

$QUERY
 
    ");

    // Bind parameters
    $query->bindParam(':date', $date_to, PDO::PARAM_STR);
    $query->execute();
    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);

    $results = [];
    foreach ($fetch_list as $fetch) {
        $customer_id = $fetch['customer_id'];
        $due = self::calculateCustomerDue($fetch);

        $results[] = array_merge($fetch, $due);
    }

    return $results;


}

static function getCustomerPreviousDue($customer_id, $invoice_datetime, $brunch_id = 'All')
{
    $conn = Database::getInstance();

    $BRUNCH_SALES = ($brunch_id == 'All') ? "" : " AND si.brunch_id = :brunch_id ";
    $BRUNCH_TR = ($brunch_id == 'All') ? "" : " AND at.brunch_id = :brunch_id ";
    $BRUNCH_RETURN = ($brunch_id == 'All') ? "" : " AND sri.brunch_id = :brunch_id ";
    $BRUNCH_BAL = ($brunch_id == 'All') ? "" : " AND bc.brunch_id = :brunch_id ";

    $sql = "
    SELECT 
        COALESCE(SALES.total_sales,0) 
        - COALESCE(TR.total_in,0) 
        + COALESCE(TR.total_out,0)
        - COALESCE(RET.total_return,0)
        + COALESCE(ADJ.total_adj,0) AS previous_due

    FROM setup_customer c

    LEFT JOIN (
        SELECT 
            si.customer_id,
            SUM((sii.sales_quantity * sii.sales_rate) - si.discount + si.transport_cost + si.total_vat_cost) AS total_sales
        FROM sales_invoice si
        JOIN sales_invoice_item sii ON si.id = sii.sales_invoice_id
        WHERE 
            si.customer_id = :customer_id
            AND si.generate_challan = 'Done'
            AND CONCAT(si.invoice_date,' ',si.time) < :invoice_datetime
            $BRUNCH_SALES
        GROUP BY si.customer_id
    ) SALES ON c.id = SALES.customer_id

    LEFT JOIN (
        SELECT 
            at.transection_to_id AS customer_id,
            SUM(at.in_amount) AS total_in,
            SUM(at.out_amount) AS total_out
        FROM account_transection at
        WHERE 
            at.transection_to = 'Customer'
            AND at.transection_to_id = :customer_id
            AND CONCAT(at.transection_date,' ',at.time) < :invoice_datetime
            $BRUNCH_TR
        GROUP BY at.transection_to_id
    ) TR ON c.id = TR.customer_id

    LEFT JOIN (
        SELECT 
            sri.customer_id,
            SUM(srii.return_quantity * srii.sales_rate) AS total_return
        FROM sales_return_invoice sri
        JOIN sales_return_invoice_item srii ON sri.id = srii.return_invoice_id
        WHERE 
            sri.customer_id = :customer_id
            AND sri.warehouse_receive = 'Done'
            AND CONCAT(sri.warehouse_receive_date,' ',sri.time) < :invoice_datetime
            $BRUNCH_RETURN
        GROUP BY sri.customer_id
    ) RET ON c.id = RET.customer_id

    LEFT JOIN (
        SELECT 
            bc.customer_id,
            SUM(bc.invoice_amount - bc.return_amount) AS total_adj
        FROM balance_customer bc
        WHERE 
            bc.customer_id = :customer_id
            AND CONCAT(bc.date,' 00:00:00') < :invoice_datetime
            $BRUNCH_BAL
        GROUP BY bc.customer_id
    ) ADJ ON c.id = ADJ.customer_id

    WHERE c.id = :customer_id
    ";

    $query = $conn->prepare($sql);
    $query->bindParam(':customer_id', $customer_id);
    $query->bindParam(':invoice_datetime', $invoice_datetime);

    if ($brunch_id != 'All') {
        $query->bindParam(':brunch_id', $brunch_id);
    }

    $query->execute();
    $row = $query->fetch(PDO::FETCH_ASSOC);

    return $row['previous_due'] ?? 0;
}

 static function calculateCustomerDue($data) {


    $invoice_amount = (float)$data['invoice_price'] ;
    $transection_amount_in = (float)$data['INAMOUNT'];
    $transection_amount_out = (float)$data['OUTAMOUNT'];
    $product_return_amount = (float)$data['product_return_amount'];
    $adjustement_amount_last_due = (float)$data['last_due_amount'];
    $adjustement_amount_discount = (float)$data['discount_amount'];

    $customer_due = number_format(
        ($invoice_amount + $adjustement_amount_last_due + $transection_amount_out) - 
        ($adjustement_amount_discount + $transection_amount_in + $product_return_amount), 
        2, '.', ''
    );

    $total_tr = number_format($transection_amount_in - $transection_amount_out, 2, '.', '');

    return [
        'customer_due' => $customer_due,
        'invoice_amount' => $invoice_amount,
        'transection_amount' => $total_tr,
        'product_return_amount' => $product_return_amount,
        'due_adjustement' => $adjustement_amount_last_due,
        'dis_adjustement' => $adjustement_amount_discount
    ];
}



static function getTotalInvoicePriceAllClient($BRUNCHID,$DATE) {
    $conn_me = Database::getInstance();

    $BRUNCH_QUERY = " AND  si.`brunch_id` = '".$BRUNCHID."' ";


    $total_invoice = 0 ;
    $total_discount = 0;
    $total_trasport = 0;
    $total_vat = 0;


    // Calculate total invoice price directly in the SQL query
    $query = $conn_me->prepare("SELECT 
    si.id AS invoice_id,
    SUM(sii.sales_quantity * sii.sales_rate) AS total_invoice_price,
    si.discount AS Discount,
    si.transport_cost AS Trasport,
    si.total_vat_cost AS VAT

FROM 
    sales_invoice si
JOIN 
    sales_invoice_item sii ON si.id = sii.sales_invoice_id
WHERE 
   si.invoice_date <= '".$DATE."' AND si.generate_challan = 'Done' $BRUNCH_QUERY
GROUP BY 
    invoice_id");
    $query->execute();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) { 


        $total_invoice += number_format((float)( $fetch['total_invoice_price']), 2, '.', '');
        $total_discount += number_format((float)( $fetch['Discount']), 2, '.', '');
        $total_trasport += number_format((float)( $fetch['Trasport']), 2, '.', '');
        $total_vat += number_format((float)( $fetch['VAT']), 2, '.', '');
           

           }



$sub_total = number_format((float)( ( $total_invoice  + $total_trasport + $total_vat ) ), 2, '.', '');
$invoice_price = number_format((float)( $sub_total - $total_discount ), 2, '.', '');

    return $invoice_price;
}


static function getTotalTransectionAllClient($BRUNCHID,$DATE) {
    $conn_me = Database::getInstance();


    $BRUNCH_QUERY = " AND  `brunch_id` = '".$BRUNCHID."' ";


    // Calculate total invoice price directly in the SQL query
    $query = $conn_me->prepare("SELECT  IFNULL(SUM(in_amount), 0) as INAMOUNT , IFNULL(SUM(out_amount), 0) AS OUTAMOUNT
        FROM account_transection
        WHERE transection_to = 'Customer' AND transection_date <= '".$DATE."' $BRUNCH_QUERY GROUP BY transection_to ");
    $query->execute();

if ($query->rowCount() > 0) {
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $INAMOUNT = number_format((float)$result['INAMOUNT'], 2, '.', '');
        $OUTAMOUNT = number_format((float)$result['OUTAMOUNT'], 2, '.', '');

        } else {
        $INAMOUNT = 0.00;
        $OUTAMOUNT = 0.00;
        }

        return array(
            'INAMOUNT' => $INAMOUNT,
            'OUTAMOUNT' => $OUTAMOUNT
            );
}


static function getTotalProductReturnAllClient($BRUNCHID,$DATE) {
    $conn_me = Database::getInstance();


    $BRUNCH_QUERY = " AND  si.`brunch_id` = '".$BRUNCHID."' ";




 $total_invoice = 0 ; 

    // Calculate total invoice price directly in the SQL query
    $query = $conn_me->prepare("SELECT 
    si.id AS invoice_id,
    SUM(sii.return_quantity * sii.sales_rate) AS total_invoice_price

FROM 
    sales_return_invoice si
JOIN 
    sales_return_invoice_item sii ON si.id = sii.return_invoice_id
WHERE 
 si.warehouse_receive_date <= '".$DATE."' AND si.warehouse_receive = 'Done' $BRUNCH_QUERY
GROUP BY 
    invoice_id");
    $query->execute();

 $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) { 
        $total_invoice += number_format((float)( $fetch['total_invoice_price']), 2, '.', '');
       
           }


$totalInvoicePrice = number_format((float)($total_invoice ), 2, '.', '');



    // Format the result

    return $totalInvoicePrice;
}


static function getAdjustmentAllClient($BRUNCHID, $DATE) {
    $conn_me = Database::getInstance();

    $BRUNCH_QUERY = " AND  `brunch_id` = '".$BRUNCHID."' ";


    // Calculate total invoice price directly in the SQL query
    $query = $conn_me->prepare("SELECT 
        SUM(CASE WHEN note = 'LAST DUE' THEN invoice_amount ELSE 0 END) AS last_due_amount,
        SUM(CASE WHEN note = 'DISCOUNT' THEN return_amount ELSE 0 END) AS discount_amount
        FROM 
        balance_customer
        WHERE  date <= '".$DATE."'  $BRUNCH_QUERY
        GROUP BY 
        brunch_id ");
    $query->execute();

    $fetch = $query->fetch(PDO::FETCH_ASSOC);

    if ($fetch === false) {
        return array(
            'last_due_amount' =>0.00,
            'discount_amount' => 0.00
        );
    }


    return array(
        'last_due_amount' => $fetch['last_due_amount'],
        'discount_amount' => $fetch['discount_amount']
    );
    
}








static  function CUSTOMERDUEALLCLIENT( $DATE, $BRUNCHID)
{
    $conn_me = Database::getInstance();


    $invoice_amount = self::getTotalInvoicePriceAllClient( $BRUNCHID, $DATE);
    $transection_amount = self::getTotalTransectionAllClient( $BRUNCHID, $DATE);
    $product_return_amount = self::getTotalProductReturnAllClient($BRUNCHID, $DATE);
    $adjustement_amount = self::getAdjustmentAllClient( $BRUNCHID, $DATE);

    $customer_due = number_format((float)( 

        ( $invoice_amount + $adjustement_amount['last_due_amount'] + $transection_amount['OUTAMOUNT'] ) 

        - 
        ( $adjustement_amount['discount_amount'] + $transection_amount['INAMOUNT'] + $product_return_amount)   
    
    ), 2, '.', '');
    


    $total_tr = number_format((float)(  ( $transection_amount['INAMOUNT'] - $transection_amount['OUTAMOUNT'] )), 2, '.', '');
    return [
        
            'customer_due' => $customer_due

    ];
}

static function SALESRECORDBYDATEANDBRUNCH( $DATE, $BRUNCHID)
{
    $conn_me = Database::getInstance();


    $total_paid = 0 ; 

    $total_invoice_price = 0 ;
    $qry = $conn_me->prepare("SELECT A.`id`,A.transection_id FROM `sales_invoice`  A  where  A.`brunch_id` = '".$BRUNCHID."' AND A.`invoice_date` = '".$DATE."'  AND A.`generate_challan` = 'Done' ORDER BY A.`id` ASC");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
   
        foreach($fetch_list AS $fetch) {

            $invoice_price = FIND::TOTAL_SALES_INVOICE_PRICE($fetch['id']);
            $total_invoice_price += $invoice_price['price'];

            if(!empty($fetch['transection_id'])){
            $info_payment = SETUP::ACCOUNT_TRANSECTION($fetch['transection_id']);
            $total_paid += $info_payment['in_amount'];
            }else{
            $total_paid += 0 ; 
            }
            
        }
   



    
    return [
        
            'total_paid' => $total_paid,
            'total_invoice_price' => $total_invoice_price

    ];
}




static function getTotalMoneyTransfer($BRUNCHID,$DATE) {
    $conn_me = Database::getInstance();

    // Calculate total invoice price directly in the SQL query
    $query = $conn_me->prepare("SELECT
    SUM(at_out.out_amount) AS total_out_amount
FROM
    account_transection AS at_out
JOIN
    account_transection AS at_in
ON
    at_out.id = at_in.transection_id
WHERE
    at_out.brunch_id = '".$BRUNCHID."'
    AND at_out.data_inserted_from = 'MONEY-TRANSFER-FROM'
    AND at_in.data_inserted_from = 'MONEY-TRANSFER-TO'
    AND at_in.brunch_id = '".$_SESSION['USER_BRUNCH']."' 
    AND at_out.transection_date <= '".$DATE."' 

 ");
    $query->execute();

if ($query->rowCount() > 0) {
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $amount = number_format((float)$result['total_out_amount'], 2, '.', '');

        } else {
        $amount = 0.00;
        }

        return $amount ;
}

static function getTotalDemand($BRUNCHID,$DATE) {
    $conn_me = Database::getInstance();


    $total_invoice = 0 ;

    // Calculate total invoice price directly in the SQL query
    $query = $conn_me->prepare("SELECT  SUM(si.quantity * si.price) AS total_price FROM  demand_receive si WHERE 
    si.demand_from_brunch = '".$BRUNCHID."' AND si.demand_for_brunch = '".$_SESSION['USER_BRUNCH']."'  AND si.send_date <= '".$DATE."' 
GROUP BY 
si.demand_from_brunch");

    $query->execute();
       
if ($query->rowCount() > 0) {
    $result = $query->fetch(PDO::FETCH_ASSOC);
    $amount = number_format((float)$result['total_price'], 2, '.', '');

    } else {
    $amount = 0.00;
    }

    return $amount ;
}





static function getTotalSales($BRUNCHID,$DATE) {
    $conn_me = Database::getInstance();



    $total_invoice = 0 ;
    $total_discount = 0;
    $total_trasport = 0;
    $total_vat = 0;


    // Calculate total invoice price directly in the SQL query
    $query = $conn_me->prepare("SELECT 
    si.id AS invoice_id,
    si.customer_id,
    SUM(sii.sales_quantity * sii.sales_rate) AS total_invoice_price,
    si.discount AS Discount,
    si.transport_cost AS Trasport,
    si.total_vat_cost AS VAT

FROM 
    sales_invoice si
JOIN 
    sales_invoice_item sii ON si.id = sii.sales_invoice_id
WHERE 
   si.brunch_id = :brunch_id AND si.invoice_date <= '".$DATE."' AND si.generate_challan = 'Done' 
GROUP BY 
    invoice_id, si.brunch_id");
    $query->bindParam(':brunch_id', $BRUNCHID, PDO::PARAM_INT);
    $query->execute();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch) { 


        $total_invoice += number_format((float)( $fetch['total_invoice_price']), 2, '.', '');
        $total_discount += number_format((float)( $fetch['Discount']), 2, '.', '');
        $total_trasport += number_format((float)( $fetch['Trasport']), 2, '.', '');
        $total_vat += number_format((float)( $fetch['VAT']), 2, '.', '');
           

           }



$sub_total = number_format((float)( ( $total_invoice  + $total_trasport + $total_vat ) ), 2, '.', '');
$invoice_price = number_format((float)( $sub_total - $total_discount ), 2, '.', '');

    return $invoice_price;
}



static function BRUNCHDUE($BRUNCHID, $DATE)
{
    $conn_me = Database::getInstance();


    $transaction = self::getTotalMoneyTransfer( $BRUNCHID, $DATE);
    $demand = self::getTotalDemand($BRUNCHID, $DATE);
    $sales = self::getTotalSales( $BRUNCHID, $DATE);

    $brunch_due = number_format((float)( 

        ( $demand + $sales ) - ( $transaction )   
    
    ), 2, '.', '');
    


    return [
        
            'brunch_due' => $brunch_due,
            'demand' => $demand,
            'sales' => $sales,
            'transaction' => $transaction

    ];
}



static  function CUSTOMER_DUE($CUSTOMER_ID)
{
    $conn_me = Database::getInstance();

    $query = $conn_me->prepare("SELECT SUM(`invoice_amount`) AS `invoice_amount`, SUM(`receive_amount`) AS `receive_amount`, SUM(`return_amount`) AS `return_amount`
        FROM `balance_customer`
        WHERE `customer_id` = :customer_id
        GROUP BY `customer_id`");

    $query->bindParam(':customer_id', $CUSTOMER_ID, PDO::PARAM_INT);

    $query->execute();
    $fetch_list = $query->fetch(PDO::FETCH_ASSOC);

    $invoice_amount = $fetch_list['invoice_amount'] ?? 0.00;
    $receive_amount = $fetch_list['receive_amount'] ?? 0.00;
    $return_amount = $fetch_list['return_amount'] ?? 0.00;

    $customer_due = number_format((float)($invoice_amount + $return_amount - $receive_amount), 2, '.', '');

    return [
        'total_receive' => 0.00,
        'total_invoice_price' => 0.00,
        'customer_due' =>0.00,
        'total_return' => 0.00
    ];
}

    

  
static function SUPPLIER_DUE($SUPPLIER_ID){

    $conn_me = Database::getInstance();

    $total_invoice_price = 0;
    $query = $conn_me->prepare("SELECT `code`  FROM `fg_local_purches` where `supplier_id` = '".$SUPPLIER_ID."'  ");
    $query->execute();
    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch) { 
    $invoice_info = FIND::TOTAL_PURCHASE_INVOICE_PRICE($fetch['code'],'fg_local_purches'); 
    $total_invoice_price += $invoice_info['invoice_price'];
    }

    $query = $conn_me->prepare("SELECT `code`  FROM `raw_local_purches` where `supplier_id` = '".$SUPPLIER_ID."'  ");
    $query->execute();
    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch) { 
    $invoice_info = FIND::TOTAL_PURCHASE_INVOICE_PRICE($fetch['code'],'raw_local_purches'); 
    $total_invoice_price += $invoice_info['invoice_price'];
    }




    $query2 = $conn_me->prepare("SELECT sum(`out_amount`) AS `OutAmount`  FROM `account_transection` where `transection_to_id` = '".$SUPPLIER_ID."' AND `transection_to` = 'Supplier' ");
    $query2->execute();
    $fetch_list2 = $query2->fetch(PDO::FETCH_ASSOC);
    $total_paied = $fetch_list2['OutAmount'];



    $total_return = 0;
    $query3 = $conn_me->prepare("SELECT `reject_quantity`,`code`,`product_id`  FROM `history_local_fg_purches` where `supplier_id` = '".$SUPPLIER_ID."'  ");
    $query3->execute();
    $fetch_list3 = $query3->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list3 as $fetch3) { 

        $retun_invoice_price = FIND:: FG_PRICE_DURING_SALE($fetch3['code'],$fetch3['product_id']);
        $total_return += $retun_invoice_price['price']*$fetch3['reject_quantity'];
    }


    $query3 = $conn_me->prepare("SELECT `reject_quantity`,`code`,`product_id`  FROM `history_local_raw_purches` where `supplier_id` = '".$SUPPLIER_ID."' ");
    $query3->execute();
    $fetch_list3 = $query3->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list3 as $fetch3) { 

        $retun_invoice_price = FIND::RAW_PRICE_DURING_SALE($fetch3['code'],$fetch3['product_id']);
        $total_return += $retun_invoice_price['price']*$fetch3['reject_quantity'];
    }



    $supplier_due = number_format((float)( $total_invoice_price  - ($total_paied+$total_return)), 2, '.', '');



    return array(
        'total_paid' => $total_paied,
        'total_invoice_price' => $total_invoice_price,
        'supplier_due' =>  $supplier_due,
        'total_return' =>  $total_return


        );

  }

  static function EMPLOYEE_DUE($EMPLOYEEID){


    $conn_me = Database::getInstance();

    $query2 = $conn_me->prepare("SELECT sum(`out_amount`) AS `OutAmount`  FROM `account_transection` where `transection_to_id` = '".$EMPLOYEEID."' AND `transection_to` = 'Employee' ");
    $query2->execute();
    $fetch_list2 = $query2->fetch(PDO::FETCH_ASSOC);
    $total_paied = $fetch_list2['OutAmount'];

    return array(
        
        'emp_due' =>  $total_paied


        );

  }
  static function FG_SALES_STOCK_OUT($REALTED_ID,$PRODUCT_ID,$TYPE){

    $conn_me = Database::getInstance();

     if($TYPE == 'product_wise'){

          $QUERYTTPE = " where `final_confirm_sales_person` = 'Done' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` ";

      }else if($TYPE == 'warehouse_wise'){

          $QUERYTTPE = " where `final_confirm_sales_person` = 'Done' AND `warehouse_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` ";

        }else if($TYPE == 'brunch_wise') {

        $qry = $conn_me->prepare("SELECT * FROM `setup_brunch` where `id` = '".$_SESSION['USER_BRUNCH']."'  ");
        $qry->execute();
        $fetch_list = $qry->fetch(PDO::FETCH_ASSOC);
        $a = $fetch_list['related_warehouse'];
        $b = str_replace("[","",$a);
        $c = str_replace("]","",$b);
        $QUERYTTPE = " WHERE `product_id` = '" . $PRODUCT_ID . "' AND `final_confirm_sales_person` = 'Done' AND `warehouse_id` IN ($c) GROUP BY `product_id` ";

      }else{
          $QUERYTTPE = '';
      }



    
    $query = $conn_me->prepare("SELECT SUM(`sales_quantity`) AS `total_sale_qty`,SUM(`sales_rate`) AS `total_price`   FROM `sales_invoice_item` $QUERYTTPE  ");
    $query->execute();
    $fetch = $query->fetch(PDO::FETCH_ASSOC);
   
       
       if(!empty($fetch['total_sale_qty'])){
       $total_sale_qty = $fetch['total_sale_qty'];

       }else{
        $total_sale_qty = 0.00;
       }
       
        
       if(!empty($fetch['total_price'])){
       $total_price = $fetch['total_price'];

       }else{
        $total_price = 0.00;
       }
       
 
    return array(
        'total_sale_qty' => $total_sale_qty,
        'total_price' => $total_price

        );

  }


  
  static function FG_SALES_DAMAGE($REALTED_ID,$PRODUCT_ID,$TYPE){

    $conn_me = Database::getInstance();

    if($TYPE == 'product_wise'){

        $QUERYTTPE = " where `warehouse_receive` = 'Done' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` ";

    }else if($TYPE == 'warehouse_wise'){

        $QUERYTTPE = " where `warehouse_receive` = 'Done' AND `warehouse_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` ";

    }else if($TYPE == 'brunch_wise') {

        $qry = $conn_me->prepare("SELECT * FROM `setup_brunch` where `id` = '".$_SESSION['USER_BRUNCH']."'  ");
        $qry->execute();
        $fetch_list = $qry->fetch(PDO::FETCH_ASSOC);
        $a = $fetch_list['related_warehouse'];
        $b = str_replace("[","",$a);
        $c = str_replace("]","",$b);
        $QUERYTTPE = " WHERE `product_id` = '" . $PRODUCT_ID . "'  AND `warehouse_id` IN ($c) GROUP BY `product_id` ";


    }else{
        $QUERYTTPE = '';
    }


   
   $query = $conn_me->prepare("SELECT SUM(`damage_quantity`) AS `total_damage`   FROM `damage_invoice_item` $QUERYTTPE  ");
   $query->execute();
   $fetch = $query->fetch(PDO::FETCH_ASSOC);
  
      
      if(!empty($fetch['total_damage'])){
      $total_damage = $fetch['total_damage'];

      }else{
       $total_damage = 0.00;
      }
      
    
      

   return array(
       'total_damage' => $total_damage

       );

 }


 static function FG_SALES_RETURN($REALTED_ID,$PRODUCT_ID,$TYPE){
    $conn_me = Database::getInstance();


    if($TYPE == 'product_wise'){

         $QUERYTTPE = " where `warehouse_receive` = 'Done' AND  `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` ";

     }else if($TYPE == 'warehouse_wise'){

         $QUERYTTPE = " where `warehouse_receive` = 'Done' AND   `warehouse_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` ";

        }else if($TYPE == 'brunch_wise') {

            $qry = $conn_me->prepare("SELECT * FROM `setup_brunch` where `id` = '".$_SESSION['USER_BRUNCH']."'  ");
            $qry->execute();
            $fetch_list = $qry->fetch(PDO::FETCH_ASSOC);
            $a = $fetch_list['related_warehouse'];
            $b = str_replace("[","",$a);
            $c = str_replace("]","",$b);
            $QUERYTTPE = " WHERE `product_id` = '" . $PRODUCT_ID . "'  AND `warehouse_id` IN ($c) GROUP BY `product_id` ";
     }else{
         $QUERYTTPE = '';
     }



   
   $query = $conn_me->prepare("SELECT SUM(`return_quantity`) AS `total_return_qty`   FROM `sales_return_invoice_item` $QUERYTTPE  ");
   $query->execute();
   $fetch = $query->fetch(PDO::FETCH_ASSOC);
  
      
      if(!empty($fetch['total_return_qty'])){
      $total_return_qty = $fetch['total_return_qty'];

      }else{
       $total_return_qty = 0.00;
      }
      
    
      

   return array(
       'total_return_qty' => $total_return_qty

       );

 }

 static function FG_BATCH_WISE_RECEIVE_REJECT($REALTED_ID,$PRODUCT_ID,$TYPE){

        $conn_me = Database::getInstance();

        if($TYPE == 'supplier_wise'){
            
            $QUERYTTPE = " where `supplier_or_factory_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."'  AND `send_to` = 'Supplier' GROUP BY `product_id`  ";
  
          }else if($TYPE == 'Factory_Wise'){
  
              $QUERYTTPE = "where `supplier_or_factory_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."'  AND `send_to` = 'Factory' GROUP BY `product_id`  ";
  
          }else if($TYPE == 'Invoice_Wise'){
  
              $QUERYTTPE = " where `code` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `code`  ";
  
          }else if($TYPE == 'Only_Invoice_Wise'){
  
              $QUERYTTPE = " where `code` = '".$REALTED_ID."' ";
  
          }else if($TYPE == 'product_wise'){
  
              $QUERYTTPE = " where  `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` ";
  
          }else if($TYPE == 'brunch_wise'){
  
$qry = $conn_me->prepare("SELECT * FROM `setup_brunch` where `id` = '".$_SESSION['USER_BRUNCH']."'  ");
$qry->execute();
$fetch_list = $qry->fetch(PDO::FETCH_ASSOC);
$a = $fetch_list['related_warehouse'];
$b = str_replace("[","",$a);
$c = str_replace("]","",$b);
$QUERYTTPE = " WHERE `product_id` = '" . $PRODUCT_ID . "' AND `warehouse_id` IN ($c) GROUP BY `product_id` ";
  
            }else if($TYPE == 'warehouse_wise'){
                $QUERYTTPE = '';

          }else{
              $QUERYTTPE = '';
          }
  
  

        
        $query = $conn_me->prepare("SELECT SUM(`receive_quantity`) AS `total_receive`,SUM(`return_quantity`) AS `total_reject`   FROM `history_batch_wise_fg_receive` $QUERYTTPE  ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);
       
           
           if(!empty($fetch['total_receive'])){
           $total_receive = $fetch['total_receive'];

           }else{
            $total_receive = 0.00;
           }
           
            
           if(!empty($fetch['total_reject'])){
           $total_reject = $fetch['total_reject'];

           }else{
            $total_reject = 0.00;
           }
           
           $actual_receive = $total_receive - $total_reject ;
     
        return array(
            'total_receive' => $total_receive,
            'total_reject' => $total_reject,
            'actual_receive' => $actual_receive
    
            );

      }


      static    function FG_BATCH_WISE_FITTING($REALTED_ID,$PRODUCT_ID,$TYPE){

        $conn_me = Database::getInstance();

         if($TYPE == 'Invoice_Wise'){
  
              $QUERYTTPE = " where `raw_request_recipe_wise_code` = $REALTED_ID AND `product_id` = $PRODUCT_ID GROUP BY `raw_request_recipe_wise_code`  ";
  
          }else if($TYPE == 'Only_Invoice_Wise'){
  
              $QUERYTTPE = " where `raw_request_recipe_wise_code` = $REALTED_ID ";
  
          }else if($TYPE == 'product_wise'){
  
              $QUERYTTPE = " where  `product_id` = $PRODUCT_ID GROUP BY `product_id` ";
  
          }else if($TYPE == 'emloyee_wise'){
  
              $QUERYTTPE = " where  `emloyee_id` = $REALTED_ID AND `product_id` = $PRODUCT_ID GROUP BY `product_id` ";
  
          }else{
              $QUERYTTPE = '';
          }
  



        
        $query = $conn_me->prepare("SELECT SUM(`fitting_quantity`) AS `total_fitting`   FROM `history_batch_wise_fg_fitting` $QUERYTTPE  ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);
       
           
           if(!empty($fetch['total_fitting'])){
           $total_fitting = $fetch['total_fitting'];

           }else{
            $total_fitting = 0.00;
           }
           
        
             
        return array(
            'total_fitting' => $total_fitting
    
            );

      }


      static   function FG_LOCAL_PURCHES_RECEIVE_REJECT($REALTED_ID,$PRODUCT_ID,$TYPE){
        $conn_me = Database::getInstance();


        if($TYPE == 'supplier_wise'){
            
            $QUERYTTPE = "where `supplier_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id`" ;
  
          }else if($TYPE == 'Invoice_Wise'){
  
              $QUERYTTPE = "where `code` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."'  GROUP BY `code`" ;
  
  
          }else if($TYPE == 'product_wise'){
  
              $QUERYTTPE = " where  `product_id` = '".$PRODUCT_ID."'  GROUP BY `product_id` ";

            }else if($TYPE == 'warehouse_wise'){

                $QUERYTTPE = " where  `warehouse_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."'  GROUP BY `product_id`  ";
  
            }else if($TYPE == 'brunch_wise') {

        $qry = $conn_me->prepare("SELECT * FROM `setup_brunch` where `id` = '".$_SESSION['USER_BRUNCH']."'  ");
        $qry->execute();
        $fetch_list = $qry->fetch(PDO::FETCH_ASSOC);
        $a = $fetch_list['related_warehouse'];
        $b = str_replace("[","",$a);
        $c = str_replace("]","",$b);

        $QUERYTTPE = " WHERE `product_id` = '" . $PRODUCT_ID . "'  AND `warehouse_id` IN ($c) GROUP BY `product_id` ";
        
          }else{
              $QUERYTTPE = '';
          }
  
  
  
          
          $query = $conn_me->prepare("SELECT SUM(`receive_quantity`) AS `total_receive`,SUM(`reject_quantity`) AS `total_reject`   FROM `history_local_fg_purches` $QUERYTTPE  ");
          $query->execute();
          $fetch = $query->fetch(PDO::FETCH_ASSOC);
         
             
          if(!empty($fetch['total_receive'])){
            $total_receive = $fetch['total_receive'];

            }else{
             $total_receive = 0.00;
            }
            
             
            if(!empty($fetch['total_reject'])){
            $total_reject = $fetch['total_reject'];
 
            }else{
             $total_reject = 0.00;
            }
            
            $actual_receive = $total_receive - $total_reject ;
       
          return array(
              'total_receive' => $total_receive,
              'total_reject' => $total_reject,
              'actual_receive' => $actual_receive
      
              );
      
      
      }


      

      static function RECEIPE_WISE_DEMAND_RECEIVE_REJECT($REALTED_ID,$PRODUCT_ID,$TYPE){

        $conn_me = Database::getInstance();

        if($TYPE == 'supplier_wise'){
            
            $QUERYTTPE = " where `supplier_or_factory_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."'  AND `send_to` = 'Supplier' GROUP BY `product_id`  ";
  
          }else if($TYPE == 'Factory_Wise'){
  
              $QUERYTTPE = "where `supplier_or_factory_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."'  AND `send_to` = 'Factory' GROUP BY `product_id`  ";
  
          }else if($TYPE == 'Invoice_Wise'){
  
              $QUERYTTPE = " where `code` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `code`  ";
  
          }else if($TYPE == 'Only_Invoice_Wise'){
  
              $QUERYTTPE = " where `code` = '".$REALTED_ID."' ";
  
          }else if($TYPE == 'product_wise'){
  
              $QUERYTTPE = " where  `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` ";
  
          }else if($TYPE == 'warehouse_wise'){
  
              $QUERYTTPE = " where  `warehouse_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` ";
  
          }else{
              $QUERYTTPE = '';
          }
  
  

        
        $query = $conn_me->prepare("SELECT SUM(`dispatch_quantity`) AS `total_dispatch_quantity`,SUM(`return_quantity`) AS `total_reject`   FROM `history_receipe_wise_item_dispatch` $QUERYTTPE  ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);
       
           
           if(!empty($fetch['total_dispatch_quantity'])){
           $total_dispatch = $fetch['total_dispatch_quantity'];

           }else{
            $total_dispatch = 0.00;
           }
           
            
           if(!empty($fetch['total_reject'])){
           $total_reject = $fetch['total_reject'];

           }else{
            $total_reject = 0.00;
           }
           
           $actual_qty = $total_dispatch - $total_reject ;
     
        return array(
            'total_dispatch' => $total_dispatch,
            'total_reject' => $total_reject,
            'actual_qty' => $actual_qty
    
            );
    
      }



      static  function RAW_LOCAL_PURCHES_RECEIVE_REJECT($REALTED_ID,$PRODUCT_ID,$TYPE){

        $conn_me = Database::getInstance();

        if($TYPE == 'supplier_wise'){
            
          $QUERYTTPE = "where `supplier_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` " ;

        }else if($TYPE == 'Invoice_Wise'){

            $QUERYTTPE = " where `code` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `code` " ;
        }else if($TYPE == 'Only_Invoice_Wise'){

            $QUERYTTPE = " where `code` = '".$REALTED_ID."' GROUP BY `code` ";

        }else if($TYPE == 'product_wise'){

            $QUERYTTPE = " where  `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` ";

        }else if($TYPE == 'warehouse_wise'){

            $QUERYTTPE = " where  `warehouse_id` = '".$REALTED_ID."' AND  `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` ";

        }else{
            $QUERYTTPE = '';
        }



        
        $query = $conn_me->prepare("SELECT SUM(`receive_quantity`) AS `total_receive`,SUM(`reject_quantity`) AS `total_reject`   FROM `history_local_raw_purches` $QUERYTTPE  ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);
       
           
           if(!empty($fetch['total_receive'])){
           $total_receive = $fetch['total_receive'];

           }else{
            $total_receive = 0.00;
           }
           
            
           if(!empty($fetch['total_reject'])){
           $total_reject = $fetch['total_reject'];

           }else{
            $total_reject = 0.00;
           }
           
           $actual_receive = $total_receive - $total_reject ;
     
        return array(
            'total_receive' => $total_receive,
            'total_reject' => $total_reject,
            'actual_receive' => $actual_receive
    
            );
    
    
      }


      

      static  function PRINT_RAW_MATERIAL_BATCH_RECEIVE($REALTED_ID,$PRODUCT_ID,$TYPE){
        $conn_me = Database::getInstance();


        if($TYPE == 'supplier_wise'){
            
            $QUERYTTPE = " where `supplier_or_factory_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."'  AND `send_to` = 'Supplier' GROUP BY `product_id`  ";
  
          }else if($TYPE == 'Factory_Wise'){
  
              $QUERYTTPE = "where `supplier_or_factory_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."'  AND `send_to` = 'Factory' GROUP BY `product_id`  ";
  
          }else if($TYPE == 'Invoice_Wise'){
  
              $QUERYTTPE = " where `code` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `code`  ";
  
          }else if($TYPE == 'Only_Invoice_Wise'){
  
              $QUERYTTPE = " where `code` = '".$REALTED_ID."' ";
  
          }else if($TYPE == 'product_wise'){
  
              $QUERYTTPE = " where  `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` ";
  
          }else if($TYPE == 'warehouse_wise'){
  
              $QUERYTTPE = " where  `warehouse_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` ";
  
          }else{
              $QUERYTTPE = '';
          }
  
  

        
        $query = $conn_me->prepare("SELECT SUM(`receive_quantity`) AS `total_receive`,SUM(`return_quantity`) AS `total_reject`   FROM `history_receive_raw_after_print` $QUERYTTPE  ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);
       
           
           if(!empty($fetch['total_receive'])){
           $total_receive = $fetch['total_receive'];

           }else{
            $total_receive = 0.00;
           }
           
            
           if(!empty($fetch['total_reject'])){
           $total_reject = $fetch['total_reject'];

           }else{
            $total_reject = 0.00;
           }
           
           $actual_receive = $total_receive - $total_reject ;
     
        return array(
            'total_receive' => $total_receive,
            'total_reject' => $total_reject,
            'actual_receive' => $actual_receive
    
            );

      }
    
      static  function SPRAY_RAW_MATERIAL_BATCH_RECEIVE($REALTED_ID,$PRODUCT_ID,$TYPE){

        $conn_me = Database::getInstance();

        if($TYPE == 'supplier_wise'){
            
            $QUERYTTPE = " where `supplier_or_factory_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."'  AND `send_to` = 'Supplier' GROUP BY `product_id`  ";
  
          }else if($TYPE == 'Factory_Wise'){
  
              $QUERYTTPE = "where `supplier_or_factory_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."'  AND `send_to` = 'Factory' GROUP BY `product_id`  ";
  
          }else if($TYPE == 'Invoice_Wise'){
  
              $QUERYTTPE = " where `code` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `code`  ";
  
          }else if($TYPE == 'Only_Invoice_Wise'){
  
              $QUERYTTPE = " where `code` = '".$REALTED_ID."' ";
  
          }else if($TYPE == 'product_wise'){
  
              $QUERYTTPE = " where  `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` ";
  
          }else if($TYPE == 'warehouse_wise'){
  
              $QUERYTTPE = " where  `warehouse_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` ";
  
          }else{
              $QUERYTTPE = '';
          }
  
  

        
        $query = $conn_me->prepare("SELECT SUM(`receive_quantity`) AS `total_receive`,SUM(`return_quantity`) AS `total_reject`   FROM `history_receive_raw_after_spray` $QUERYTTPE  ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);
       
           
           if(!empty($fetch['total_receive'])){
           $total_receive = $fetch['total_receive'];

           }else{
            $total_receive = 0.00;
           }
           
            
           if(!empty($fetch['total_reject'])){
           $total_reject = $fetch['total_reject'];

           }else{
            $total_reject = 0.00;
           }
           
           $actual_receive = $total_receive - $total_reject ;
     
        return array(
            'total_receive' => $total_receive,
            'total_reject' => $total_reject,
            'actual_receive' => $actual_receive
    
            );
    
    
      }




      static   function MOLDING_RAW_MATERIAL_BATCH_RECEIVE($REALTED_ID,$PRODUCT_ID,$TYPE){

        $conn_me = Database::getInstance();

        if($TYPE == 'supplier_wise'){
            
            $QUERYTTPE = " where `supplier_or_factory_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."'  AND `send_to` = 'Supplier' GROUP BY `product_id`  ";
  
          }else if($TYPE == 'Factory_Wise'){
  
              $QUERYTTPE = "where `supplier_or_factory_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."'  AND `send_to` = 'Factory' GROUP BY `product_id`  ";
  
          }else if($TYPE == 'Invoice_Wise'){
  
              $QUERYTTPE = " where `code` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `code`  ";
  
          }else if($TYPE == 'Only_Invoice_Wise'){
  
              $QUERYTTPE = " where `code` = '".$REALTED_ID."' ";
  
          }else if($TYPE == 'product_wise'){
  
              $QUERYTTPE = " where  `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` ";
  
          }else if($TYPE == 'warehouse_wise'){
  
              $QUERYTTPE = " where  `warehouse_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` ";
  
          }else{
              $QUERYTTPE = '';
          }
  
  

        
        $query = $conn_me->prepare("SELECT SUM(`receive_quantity`) AS `total_receive`,SUM(`return_quantity`) AS `total_reject`   FROM `history_receive_raw_after_mold` $QUERYTTPE  ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);
       
           
           if(!empty($fetch['total_receive'])){
           $total_receive = $fetch['total_receive'];

           }else{
            $total_receive = 0.00;
           }
           
            
           if(!empty($fetch['total_reject'])){
           $total_reject = $fetch['total_reject'];

           }else{
            $total_reject = 0.00;
           }
           
           $actual_receive = $total_receive - $total_reject ;
     
        return array(
            'total_receive' => $total_receive,
            'total_reject' => $total_reject,
            'actual_receive' => $actual_receive
    
            );
    
    
      }

     
      


      static function SPRAY_MATERIAL_DEMAND_DISPATCH($REALTED_ID,$PRODUCT_ID,$TYPE){

        $conn_me = Database::getInstance();

        if($TYPE == 'supplier_wise'){
            
          $QUERYTTPE = " where `supplier_or_factory_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."'  AND `send_to` = 'Supplier' GROUP BY `product_id` ";

        }else if($TYPE == 'Factory_Wise'){

            $QUERYTTPE = " where `supplier_or_factory_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."'  AND `send_to` = 'Factory' GROUP BY `product_id` ";

        }else if($TYPE == 'Invoice_Wise'){

            $QUERYTTPE = " where `code` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `code` " ;

        }else if($TYPE == 'Only_Invoice_Wise'){

            $QUERYTTPE = " where `code` = '".$REALTED_ID."' " ;

        }else if($TYPE == 'product_wise'){

            $QUERYTTPE = " where  `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id`" ;

        }else if($TYPE == 'warehouse_wise'){

            $QUERYTTPE = " where  `warehouse_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` " ;

        }else{
            $QUERYTTPE = '';
        }



        
        $query = $conn_me->prepare("SELECT SUM(`dispatch_quantity`) AS `total_dispatch`   FROM `history_spray_item_dispatch` $QUERYTTPE  ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);
       
           
           if(!empty($fetch['total_dispatch'])){
           $total_dispatch = $fetch['total_dispatch'];

           }else{
            $total_dispatch = 0.00;
           }
           
        
     
        return array(
            'total_dispatch' => $total_dispatch

            );
    
    
      }

      static   function PRINT_MATERIAL_DEMAND_DISPATCH($REALTED_ID,$PRODUCT_ID,$TYPE){
        $conn_me = Database::getInstance();


        if($TYPE == 'supplier_wise'){
            
          $QUERYTTPE =  " where `supplier_or_factory_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."'  AND `send_to` = 'Supplier' GROUP BY `product_id` " ;

        }else if($TYPE == 'Factory_Wise'){

            $QUERYTTPE = " where `supplier_or_factory_id` = '".$REALTED_ID."' AND `product_id` = '.$PRODUCT_ID.'  AND `send_to` = 'Factory' GROUP BY `product_id` " ;

        }else if($TYPE == 'Invoice_Wise'){

            $QUERYTTPE = " where `code` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `code` " ;

        }else if($TYPE == 'Only_Invoice_Wise'){

            $QUERYTTPE = " where `code` = '".$REALTED_ID."' ";

        }else if($TYPE == 'product_wise'){

            $QUERYTTPE = " where  `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` " ;

        }else if($TYPE == 'warehouse_wise'){

            $QUERYTTPE =  " where  `warehouse_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` " ;

        }else{
            $QUERYTTPE = '';
        }



        
        $query = $conn_me->prepare("SELECT SUM(`dispatch_quantity`) AS `total_dispatch`   FROM `history_print_item_dispatch` $QUERYTTPE  ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);
       
           
           if(!empty($fetch['total_dispatch'])){
           $total_dispatch = $fetch['total_dispatch'];

           }else{
            $total_dispatch = 0.00;
           }
           
        
     
        return array(
            'total_dispatch' => $total_dispatch

            );
    
    
      }

      static  function SPRAY_RAW_MATERIAL_DEMAND_DISPATCH($REALTED_ID,$PRODUCT_ID,$TYPE){
        $conn_me = Database::getInstance();


        if($TYPE == 'supplier_wise'){
            
          $QUERYTTPE = " where `supplier_or_factory_id` = '".$REALTED_ID."' AND `product_id` = '.$PRODUCT_ID.'  AND `send_to` = 'Supplier' GROUP BY `product_id` ";

        }else if($TYPE == 'Factory_Wise'){

            $QUERYTTPE = " where `supplier_or_factory_id` = '".$REALTED_ID."' AND `product_id` = '.$PRODUCT_ID.'  AND `send_to` = 'Factory' GROUP BY `product_id` " ;

        }else if($TYPE == 'Invoice_Wise'){

            $QUERYTTPE = " where `code` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `code` ";

        }else if($TYPE == 'Only_Invoice_Wise'){

            $QUERYTTPE = " where `code` = '".$REALTED_ID."' ";

        }else if($TYPE == 'product_wise'){

            $QUERYTTPE = " where  `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` " ;

        }else if($TYPE == 'warehouse_wise'){

            $QUERYTTPE =  " where  `warehouse_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` " ;

        }else{
            $QUERYTTPE = '';
        }



        
        $query = $conn_me->prepare("SELECT SUM(`dispatch_quantity`) AS `total_dispatch`   FROM `history_spray_raw_item_dispatch` $QUERYTTPE  ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);
       
           
           if(!empty($fetch['total_dispatch'])){
           $total_dispatch = $fetch['total_dispatch'];

           }else{
            $total_dispatch = 0.00;
           }
           
        
     
        return array(
            'total_dispatch' => $total_dispatch

            );
    
    
      }

      static  function PRINT_RAW_MATERIAL_DEMAND_DISPATCH($REALTED_ID,$PRODUCT_ID,$TYPE){
        $conn_me = Database::getInstance();


        if($TYPE == 'supplier_wise'){
            
          $QUERYTTPE =  " where `supplier_or_factory_id` = '".$REALTED_ID."' AND `product_id` = '.$PRODUCT_ID.'  AND `send_to` = \'Supplier\' GROUP BY `product_id` ";

        }else if($TYPE == 'Factory_Wise'){

            $QUERYTTPE =  " where `supplier_or_factory_id` = '".$REALTED_ID."' AND `product_id` = '.$PRODUCT_ID.'  AND `send_to` = 'Factory' GROUP BY `product_id` " ;

        }else if($TYPE == 'Invoice_Wise'){

            $QUERYTTPE =  " where `code` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `code` " ;

        }else if($TYPE == 'Only_Invoice_Wise'){

            $QUERYTTPE =  " where `code` = '".$REALTED_ID."' "  ;

        }else if($TYPE == 'product_wise'){

            $QUERYTTPE = " where  `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` " ;

        }else if($TYPE == 'warehouse_wise'){

            $QUERYTTPE =  " where  `warehouse_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` " ;

        }else{
            $QUERYTTPE = '';
        }



        
        $query = $conn_me->prepare("SELECT SUM(`dispatch_quantity`) AS `total_dispatch`   FROM `history_print_raw_item_dispatch` $QUERYTTPE  ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);
       
           
           if(!empty($fetch['total_dispatch'])){
           $total_dispatch = $fetch['total_dispatch'];

           }else{
            $total_dispatch = 0.00;
           }
           
        
     
        return array(
            'total_dispatch' => $total_dispatch

            );
    
    
      }
      
      static  function FG_OPENING_STOCK_RECEIVE($REALTED_ID,$ID,$TYPE){

        $conn_me = Database::getInstance();

        if($TYPE == 'warehouse_wise' ){

            $QUERYTAG = " WHERE `product_id` = '" . $ID . "' AND `status` = 'Done' AND `warehouse_id` = '" . $REALTED_ID."' GROUP BY `product_id` ";
        
          }else if($TYPE == 'product_wise') {
        
            $QUERYTAG = " WHERE `product_id` = '".$ID."' AND `status` = 'Done'  GROUP BY `product_id` ";
        
        }else if($TYPE == 'brunch_wise') {

        $qry = $conn_me->prepare("SELECT * FROM `setup_brunch` where `id` = '".$_SESSION['USER_BRUNCH']."'  ");
        $qry->execute();
        $fetch_list = $qry->fetch(PDO::FETCH_ASSOC);
        $a = $fetch_list['related_warehouse'];
        $b = str_replace("[","",$a);
        $c = str_replace("]","",$b);
        $QUERYTAG = " WHERE `product_id` = '" . $ID . "' AND `status` = 'Done' AND `warehouse_id` IN ($c) GROUP BY `product_id` ";
        
          }else{
            $QUERYTAG = '';
        
          }


        $ck1 = $conn_me->prepare("SELECT SUM(`quantity`) AS `total_qty`  FROM `fg_opening_stock` $QUERYTAG ");
        $ck1->execute();
        $fe_ck1 = $ck1->fetch(PDO::FETCH_ASSOC);
    
        if(!empty($fe_ck1['total_qty']) ){
            $opening_stock = $fe_ck1['total_qty'];
        }else{
            $opening_stock = 0.00;
        }


        return array(
            'total_receive' => $opening_stock
            );

 


      }


      
      static function WAREHOUSE_WISE_STOCK($TYPE,$MATERIALID,$WAREOUSEID){
    $conn_me = Database::getInstance();

   if($TYPE == 'RAW'){

    $product_info = SETUP::SETUP_RAW_MATERIAL($MATERIALID);

    $stock = STOCK::RAW_ITEM_WISE_STOCK($WAREOUSEID,$MATERIALID,'warehouse_wise');
    $treansfer_from_this_WH = FIND::RAW_WAREHOUSE_TRANSFER_IN_OUT($WAREOUSEID,$MATERIALID,'from_warehouse_wise');
    $treansfer_from_this_TO = FIND::RAW_WAREHOUSE_TRANSFER_IN_OUT($WAREOUSEID,$MATERIALID,'to_warehouse_wise');

    $real_warehousestock = ($stock['ITEM_STOCK']+$treansfer_from_this_TO['total_move']) -  $treansfer_from_this_WH['total_move']; 

        if($real_warehousestock > 0){
            
        $in_carton = $real_warehousestock/$product_info['pcs_in_cartoon'];
        }else{
        $in_carton = 0.00;
        }
   

   }else if($TYPE == 'FG'){
    
    $product_info = SETUP::SETUP_PRODUCT($MATERIALID);

    $stock = STOCK::FG_ITEM_WISE_STOCK($WAREOUSEID,$MATERIALID,'warehouse_wise');

   // $treansfer_from_this_WH = FIND::FG_WAREHOUSE_TRANSFER_IN_OUT($WAREOUSEID,$MATERIALID,'from_warehouse_wise');
    // $treansfer_from_this_TO = FIND::FG_WAREHOUSE_TRANSFER_IN_OUT($WAREOUSEID,$MATERIALID,'to_warehouse_wise');

   // $real_warehousestock = ($stock['ITEM_STOCK']+$treansfer_from_this_TO['total_move']) -  $treansfer_from_this_WH['total_move']; 

    $real_warehousestock = ($stock['ITEM_STOCK']);
    if($real_warehousestock > 0){
        $in_carton = $real_warehousestock/ ($product_info['pcs_in_cartoon'] ?? 0);
        }else{
        $in_carton = 0.00;
        }
 

   }else{
$real_warehousestock = 0 ;
$in_carton = 0.00;
   }
    


    return array(
        'warhouse_wise_stock' => $real_warehousestock,
        'warhouse_wise_stock_in_carton' => number_format((float)$in_carton, 2, '.', '')
        );

}



static function PIPE_LINE_WISE_STOCK($BRUNCH_ID,$TYPE,$MATERIALID){
    $conn_me = Database::getInstance();

   if($TYPE == 'RAW'){


    $stock = 0;
   }else if($TYPE == 'FG'){
    

    $qry = $conn_me->prepare("SELECT * FROM `setup_brunch` where `id` = '".$BRUNCH_ID."'  ");
    $qry->execute();
    $fetch_list = $qry->fetch(PDO::FETCH_ASSOC);
    $a = $fetch_list['related_warehouse'];
    $b = str_replace("[","",$a);
    $c = str_replace("]","",$b);
    $QUERYTAG = " WHERE `product_id` = '" . $MATERIALID . "' AND `warehouse_id` IN ($c)  AND `final_confirm_sales_person` = 'Pending' group by `product_id`  ";


    $query = $conn_me->prepare("SELECT SUM(`sales_quantity`) AS `total`   FROM `sales_invoice_item` $QUERYTAG ");
    $query->execute();
    $fetch = $query->fetch(PDO::FETCH_ASSOC);
   
           $stock = !empty($fetch['total']) ? $fetch['total'] : 0 ;

     

   }else{

    $stock = 0;

   }
    


    return array(
        'pipe_line_stock' => $stock

        );

}



static   function RAW_OPENING_STOCK_RECEIVE($REALATEDID,$ID,$TYPE){


        $conn_me = Database::getInstance();


        if($TYPE == 'warehouse_wise' ){

            $QUERYTAG = " WHERE `product_id` = '".$ID."'  AND `status` = 'Done' AND `warehouse_id` = '".$REALATEDID."' ";
        
          }else if($TYPE == 'product_wise') {
        
            $QUERYTAG = "WHERE `product_id` = '".$ID."' AND `status` = 'Done'  GROUP BY `product_id`";
        
        
        
          }else{
            $QUERYTAG = '';
        
          }


        $ck1 = $conn_me->prepare("SELECT SUM(`quantity`) AS `total_qty`  FROM `raw_opening_stock` $QUERYTAG ");
        $ck1->execute();
        $fe_ck1 = $ck1->fetch(PDO::FETCH_ASSOC);
    
        if(!empty($fe_ck1['total_qty']) ){
            $opening_stock = $fe_ck1['total_qty'];
        }else{
            $opening_stock = 0.00;
        }


        return array(
            'total_receive' => $opening_stock
            );




      }




      static  function RAW_WAREHOUSE_TRANSFER_IN_OUT($REALTED_ID,$PRODUCT_ID,$TYPE){

        $conn_me = Database::getInstance();

         if($TYPE == 'Invoice_Wise'){

            $QUERYTTPE = ' where `code` = '.$REALTED_ID.' AND `product_id` = '.$PRODUCT_ID.' GROUP BY `code`  ';

        }else if($TYPE == 'Only_Invoice_Wise'){

            $QUERYTTPE = ' where `code` = '.$REALTED_ID.'   ';

        }else if($TYPE == 'product_wise'){

            $QUERYTTPE = ' where  `product_id` = '.$PRODUCT_ID.' GROUP BY `product_id` ';

        }else if($TYPE == 'from_warehouse_wise'){

            $QUERYTTPE = ' where  `FROM_warehouse_id` = '.$REALTED_ID.' AND `product_id` = '.$PRODUCT_ID.' GROUP BY `product_id` ';

        }else if($TYPE == 'to_warehouse_wise'){

            $QUERYTTPE = ' where  `TO_warehouse_id` = '.$REALTED_ID.' AND `product_id` = '.$PRODUCT_ID.' GROUP BY `product_id` ';


        }else{
            $QUERYTTPE = '';
        }



        
        $query = $conn_me->prepare("SELECT SUM(`quantity`) AS `total_move`   FROM `raw_warehouse_to_warehouse_transfer` $QUERYTTPE  ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);
       
           
           if(!empty($fetch['total_move'])){
           $total_move = $fetch['total_move'];

           }else{
            $total_move = 0.00;
           }
           
        
     
        return array(
            'total_move' => $total_move

            );
    
    
      }


      

      static  function FG_WAREHOUSE_TRANSFER_IN_OUT($REALTED_ID,$PRODUCT_ID,$TYPE){

        $conn_me = Database::getInstance();

        if($TYPE == 'Invoice_Wise'){

           $QUERYTTPE = " where `code` = '".$REALTED_ID."' AND `product_id` = '.$PRODUCT_ID.' GROUP BY `code` ";

       }else if($TYPE == 'Only_Invoice_Wise'){

           $QUERYTTPE = "where `code` = '".$REALTED_ID."' ";

       }else if($TYPE == 'product_wise'){

           $QUERYTTPE = " where  `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` ";

       }else if($TYPE == 'from_warehouse_wise'){

           $QUERYTTPE = " where  `FROM_warehouse_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` ";

       }else if($TYPE == 'to_warehouse_wise'){

           $QUERYTTPE = " where  `TO_warehouse_id` = '".$REALTED_ID."' AND `product_id` = '.$PRODUCT_ID.' GROUP BY `product_id` ";


       }else{
           $QUERYTTPE = '';
       }



       
       $query = $conn_me->prepare("SELECT SUM(`quantity`) AS `total_move`   FROM `fg_warehouse_to_warehouse_transfer` $QUERYTTPE  ");
       $query->execute();
       $fetch = $query->fetch(PDO::FETCH_ASSOC);
      
          
          if(!empty($fetch['total_move'])){
          $total_move = $fetch['total_move'];

          }else{
           $total_move = 0.00;
          }
          
       
    
       return array(
           'total_move' => $total_move

           );
   
   
     }

     static function MOLDING_RAW_MATERIAL_DEMAND_DISPATCH($REALTED_ID,$PRODUCT_ID,$TYPE){
        $conn_me = Database::getInstance();


        if($TYPE == 'supplier_wise'){
            
          $QUERYTTPE =  " where `supplier_or_factory_id` = '".$REALTED_ID."' AND `product_id` = '.$PRODUCT_ID.'  AND `send_to` = 'Supplier' GROUP BY `product_id` ";

        }else if($TYPE == 'Factory_Wise'){

            $QUERYTTPE =  " where `supplier_or_factory_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."'  AND `send_to` = 'Factory' GROUP BY `product_id` " ;

        }else if($TYPE == 'Invoice_Wise'){

            $QUERYTTPE = " where `code` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `code` " ;

        }else if($TYPE == 'Only_Invoice_Wise'){

            $QUERYTTPE = "  where `code` = '".$REALTED_ID."' ";

        }else if($TYPE == 'product_wise'){

            $QUERYTTPE = " where  `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` " ;

        }else if($TYPE == 'warehouse_wise'){

            $QUERYTTPE =  " where  `warehouse_id` = '".$REALTED_ID."' AND `product_id` = '".$PRODUCT_ID."' GROUP BY `product_id` " ;

        }else{
            $QUERYTTPE = '';
        }



        
        $query = $conn_me->prepare("SELECT SUM(`dispatch_quantity`) AS `total_dispatch`   FROM `history_mold_raw_item_dispatch` $QUERYTTPE  ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);
       
           
           if(!empty($fetch['total_dispatch'])){
           $total_dispatch = $fetch['total_dispatch'];

           }else{
            $total_dispatch = 0.00;
           }
           
        
     
        return array(
            'total_dispatch' => $total_dispatch

            );
    
    
      }

      static function SUPPLIER_WISE_PENDING_SPRAY($ID){
        $conn_me = Database::getInstance();


        $query = $conn_me->prepare("SELECT SUM(`batch_quantity`) AS `pending`   FROM `raw_spray` where `supplier_id` = '".$ID."' GROUP BY `supplier_id` ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);
        if ($query->rowCount() > 0)
        {
           
           $pending =  $fetch['pending'];
           
            
        }else{
            $pending = '0.00';
        }

     
        return array(
            'pending' => $pending

            );


      }



      
      static function SUPPLIER_WISE_PENDING_PRINT($ID){
        $conn_me = Database::getInstance();


        $query = $conn_me->prepare("SELECT SUM(`batch_quantity`) AS `pending`   FROM `raw_print` where `supplier_id` = '".$ID."' GROUP BY `supplier_id` ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);
        if ($query->rowCount() > 0)
        {
           
           $pending =  $fetch['pending'];
           
            
        }else{
            $pending = '0.00';
        }

     
        return array(
            'pending' => $pending

            );


      }



      static function SUPPLIER_WISE_PENDING_MOLDING($ID){

        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT SUM(`batch_quantity`) AS `pending`   FROM `raw_molding` where `supplier_id` = '".$ID."' GROUP BY `supplier_id` ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);
        if ($query->rowCount() > 0)
        {
           
           $pending =  $fetch['pending'];
           
            
        }else{
            $pending = '0.00';
        }

     
        return array(
            'pending' => $pending

            );


      }



    

      static function SUPPORTING_MATERIAL_DEMAND_FROM_PRODUCTION($ID){

        $conn_me = Database::getInstance();


        $query = $conn_me->prepare("SELECT SUM(`demand_quantity` - `actual_receive_qty`) AS `demand`  FROM `raw_request_recipe_wise_item` where `material_id` = '".$ID."' GROUP BY `material_id`  ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        if ($query->rowCount() > 0)
        {
        $demand =  $fetch['demand'];
        }else{
        $demand = '0.00';
        }

        return array(
        'demand' => $demand

        );


        }


    }





class MANUFACTUR_PRODUCT {




    static function MATERIAL_COLLECTION($KEYWORD,$TYPE){
        $conn_me = Database::getInstance();

        $content1 = '';

        
 $sl=1;
 $sll=1;
$count1 = 0;
 $qry1 = $conn_me->prepare("SELECT * FROM `raw_request_recipe_wise_item`  where `status` = 'Done' AND  `send_requisition` =   'Pending' GROUP BY `demand_code`,`send_requisition` ORDER BY `date`,`time` DESC");
 $qry1->execute();
 $rowCount1 =$qry1->rowCount();
 $fetch_list1 = $qry1->fetchAll(PDO::FETCH_ASSOC);
foreach($fetch_list1 as $fetch) {

$info_invoice = SETUP::RAW_REQUEST_RECEIPE_WISE($fetch['raw_recipe_wise_request_id']);

if($TYPE == 'All Data'){
$show_data = 'Yes';
}else if($TYPE == 'search_colection'){

  if(preg_match("/{$_POST['WHAT']}/i", $info_invoice['invoice_no']) || preg_match("/{$_POST['WHAT']}/i", $info_invoice['pi_no'])) {
            $show_data = 'Yes';
    

    }else{
        $show_data = 'No';
    }

}else{
    $show_data = 'Yes';  
}

if($show_data == 'Yes'){
$count1 = $count1+1;
    
$content1 .= '<input type="text" style="display:none" id="dc_'.$count1.'" value="'.$fetch['demand_code'].'" > <div class="task-item task-danger" ><label class="col-md-3 control-label"><span class="label label-danger"> # '.$sl++.'</span></label>                                    

<div class="table-responsive">

<table class="table table-hover table-condensed table-striped table-bordered">
<tr>
<td colspan="2" style="text-align:center;">

<button type="button" class="btn btn-danger btn-rounded btn-sm" id="#exampleModalCenter" onclick="modal_wihout_refresh(\'load_data_in_modal\',\'dc_'.$count1.'\',\'Invoice Wise Batch Status\');">Batch No.  <b class="text-danger">'.$info_invoice['invoice_no'].' </b> Pi No.  <b class="text-danger">'.$info_invoice['pi_no'].' </b></button>


</td>
</tr>

<tr>
<td style="text-align:center">Item Name</td>
<td style="text-align:center">Batch QTY</td>
</tr>';
$qry2 = $conn_me->prepare("SELECT * FROM `raw_request_recipe_wise` WHERE `code` = '".$fetch['demand_code']."' ");
$qry2->execute();
$fetch_list2 = $qry2->fetchAll(PDO::FETCH_ASSOC);
foreach($fetch_list2 as $fetch2) {
    $info_item  = SETUP::SETUP_PRODUCT($fetch2['product_id']);
$content1 .='<tr>';
$content1 .='<td>'.$info_item['product_code'] . ' ' .$info_item['product_name'].'</td><td> '.$fetch2['batch_quantity'].' '.$info_item['unit'].' </td>';

$content1 .='</tr>';
}
$content1 .= '</table> </div>



<div class="task-footer">

<div class="pull-left"><input type="hidden"  id="table_id'.$info_invoice['id'].'" value="'.$info_invoice['id'].'"></div>
<div class="pull-right"><span class="fa fa-flask"></span> Recipe Wise Demand</div>                                    
</div>                                     
</div>';
}
  

}


        return array(
            'raw_material_collection' => $content1
        
            );

    }


    static  function REQUISITION($KEYWORD,$TYPE){

        $conn_me = Database::getInstance();

        $content2 = '';

        $sll=1;  
        $count2 = 0; 
$qry1 = $conn_me->prepare("SELECT * FROM `raw_request_recipe_wise_item`  where `status` = 'Done' AND `send_requisition` = 'Done' AND `warehouse_dispatch` = 'Pending' GROUP BY `demand_code`,`send_requisition` ORDER BY `date`,`time` DESC");
$qry1->execute();
$rowCount1 =$qry1->rowCount();
$fetch_list1 = $qry1->fetchAll(PDO::FETCH_ASSOC);
foreach($fetch_list1 as $fetch) {

$info_invoice = SETUP::RAW_REQUEST_RECEIPE_WISE($fetch['raw_recipe_wise_request_id']);

        if($TYPE == 'All Data'){
        $show_data2 = 'Yes';
        }else if($TYPE == 'search_requsation'){

        if(preg_match("/{$_POST['WHAT']}/i", $info_invoice['invoice_no']) || preg_match("/{$_POST['WHAT']}/i", $info_invoice['pi_no'])) {
                $show_data2 = 'Yes';


        }else{
            $show_data2 = 'No';
        }

        }else{
        $show_data2 = 'Yes';  
        }

        if( $show_data2 == 'Yes'){

            $count2 = $count2+1;
            
            $content2 .= ' <input type="text" style="display:none" id="rq_'.$count2.'" value="'.$fetch['demand_code'].'" ><div class="task-item task-warning" ><label class="col-md-3 control-label"><span class="label label-warning"> # '.$sll++.'</span></label>                                    

            <div class="table-responsive">

            <table class="table table-hover table-condensed table-striped table-bordered">

            <tr>
            <td colspan="2" style="text-align:center;">
            
            <button type="button" class="btn btn-info btn-rounded btn-sm" id="#exampleModalCenter" onclick="modal_wihout_refresh(\'load_data_in_modal\',\'rq_'.$count2.'\',\'Report Recipe Wise Requisition\');">Batch No.  <b class="text-warning">'.$info_invoice['invoice_no'].' </b> Pi No.  <b class="text-info">'.$info_invoice['pi_no'].' </b></button>

            

            </td>
            </tr>

            <tr>
            <td colspan="2" style="text-align:center;" class="text-warning">Item List</td>
            </tr>
            <tr>
            <td style="text-align:center">Item Name</td>
            <td style="text-align:center">Batch QTY</td>
            </tr>';
            $qry2 = $conn_me->prepare("SELECT * FROM `raw_request_recipe_wise` WHERE `code` = '".$fetch['demand_code']."' ");
            $qry2->execute();
            $fetch_list2 = $qry2->fetchAll(PDO::FETCH_ASSOC);
            foreach($fetch_list2 as $fetch2) {
                $info_item  = SETUP::SETUP_PRODUCT($fetch2['product_id']);
            $content2 .='<tr>';
            $content2 .='<td>'.$info_item['product_code'] . ' ' .$info_item['product_name'].'</td><td> '.$fetch2['batch_quantity'].' '.$info_item['unit'].' </td>';
            
            $content2 .='</tr>';
            }
            $content2 .= '</table> </div>
            
            
            
            <div class="task-footer">
            
            <div class="pull-left"><input type="hidden"  id="table_id'.$info_invoice['id'].'" value="'.$info_invoice['id'].'"></div>
            <div class="pull-right"><span class="fa fa-flask"></span> Recipe Wise Demand</div>                                    
            </div>                                     
            </div>';
        }
        
    }


        return array(
            'requisition' => $content2

            );
    }

    static function ASSEMBLING($KEYWORD,$TYPE){
        $conn_me = Database::getInstance();

        $content3 = '';

        $slll=1;
        $count3 = 0; 
        $qry1 = $conn_me->prepare("SELECT * FROM `raw_request_recipe_wise_item`  where `warehouse_dispatch` = 'Done' AND `send_for_fitting` = 'Pending' GROUP BY `demand_code`,`send_requisition` ORDER BY `date`,`time` DESC");
        $qry1->execute();
        $rowCount1 =$qry1->rowCount();
        $fetch_list1 = $qry1->fetchAll(PDO::FETCH_ASSOC);
        foreach($fetch_list1 as $fetch) {
        
        $info_invoice = SETUP::RAW_REQUEST_RECEIPE_WISE($fetch['raw_recipe_wise_request_id']);
        
                if($TYPE == 'All Data'){
                $show_data2 = 'Yes';
        
                }else if($TYPE == 'search_assembling'){
        
                if(preg_match("/{$_POST['WHAT']}/i", $info_invoice['invoice_no']) || preg_match("/{$_POST['WHAT']}/i", $info_invoice['pi_no'])) {
                        $show_data2 = 'Yes';
                }else{
                    $show_data2 = 'No';
                }
        
                }else{
                $show_data2 = 'Yes';  
                }
        
                if( $show_data2 == 'Yes'){

                    $count3 = $count3+1;

                    $content3 .= '<input type="text" style="display:none" id="as_'.$count3.'" value="'.$fetch['demand_code'].'" ><div class="task-item task-success" id=""><label class="col-md-3 control-label"><span class="label label-success"> # '.$slll++.'</span></label>                                    
        
                    
                    <table class="table table-hover table-condensed table-striped table-bordered">
        
                    <tr>
                    <td colspan="2" style="text-align:center;">

                    <button type="button" class="btn btn-success btn-rounded btn-sm" id="#exampleModalCenter" onclick="modal_wihout_refresh(\'load_data_in_modal\',\'as_'.$count3.'\',\'Batch Receive ::: From '. $info_invoice['send_to'].'\');">Batch No.  <b class="text-success">'.$info_invoice['invoice_no'].' </b> Pi No.  <b class="text-success">'.$info_invoice['pi_no'].' </b></button>


                    </td>
                    </tr>
        
                    <tr>
                    <td colspan="2" style="text-align:center;" class="text-success">Item List</td>
                    </tr>
                    <tr>
                    <td style="text-align:center">Item Name</td>
                    <td style="text-align:center">Batch QTY</td>
                    </tr>';
                    $qry2 = $conn_me->prepare("SELECT * FROM `raw_request_recipe_wise` WHERE `code` = '".$fetch['demand_code']."' ");
                    $qry2->execute();
                    $fetch_list2 = $qry2->fetchAll(PDO::FETCH_ASSOC);
                    foreach($fetch_list2 as $fetch2) {
                        $info_item  = SETUP::SETUP_PRODUCT($fetch2['product_id']);
                    $content3 .='<tr>';
                    $content3 .='<td>'.$info_item['product_code'] . ' ' .$info_item['product_name'].'</td><td> '.$fetch2['batch_quantity'].' '.$info_item['unit'].' </td>';
                    
                    $content3 .='</tr>';
                    }
                    $content3 .= '</table> 
                    
                    
                    
                    <div class="task-footer">
                    
                    <div class="pull-left"><input type="hidden"  id="table_id'.$info_invoice['id'].'" value="'.$info_invoice['id'].'"></div>
                    <div class="pull-right"><span class="fa fa-flask"></span> Recipe Wise Demand</div>                                    
                    </div>                                     
                    </div>';
                }
                
            }
        
        return array(

            'assembling' => $content3

            );
    }


}


class DEMAND {


    public static function SETUP_DEMAND_ITEM($ID)
    {
        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT A.*  
        FROM `demand_item` A  
        WHERE A.`id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);        
        return array(
            'fetch' => $fetch
            );

    }



         public static function SETUP_DEMAND($ID)
    {
        $conn_me = Database::getInstance();

        $query = $conn_me->prepare("SELECT A.*  
        FROM `demand` A  
        WHERE A.`id` = '".$ID."' ");
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);        
        return array(
            'fetch' => $fetch
            );

    }

    static  function TotalDelivery($demand_id,$product_id) {
        $conn_me = Database::getInstance();
    
        // Calculate total invoice price directly in the SQL query
        $query = $conn_me->prepare("SELECT  SUM(quantity) AS salesquantity,AVG(price) as productprice  FROM demand_receive WHERE demand_id = :demand_id AND product_id = :product_id  GROUP BY 
        product_id  ");
        $query->bindParam(':demand_id', $demand_id, PDO::PARAM_INT);
        $query->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $query->execute();
        $fetch = $query->fetch(PDO::FETCH_ASSOC);
    
        $total_item = !empty($fetch['salesquantity']) ? number_format((float)( $fetch['salesquantity']), 2, '.', '') : 0 ;
        $productprice = !empty($fetch['productprice']) ? number_format((float)( $fetch['productprice']), 2, '.', '') : 0 ;

        return array(
            'total_item' => $total_item,
            'productprice' => $productprice
            );

    }




     static  function PENDING_DEMAND($ID){
         
         
         
            $conn_me = Database::getInstance();

    
            $ck1 = $conn_me->prepare("SELECT A.* , DATE_FORMAT(A.`invoice_date`, '%d-%m-%Y') AS `invoice_date`,B.brunch
            FROM `demand` A  
            JOIN setup_brunch  B ON (A.demand_created_from = B.id)

            WHERE A.`id` = '".$ID."' ");
            $ck1->execute();
            $fe_ck1 = $ck1->fetch(PDO::FETCH_ASSOC);


            $report ='<table style="width:100%;" class="table datatable_simple">';

            $report .='<tr><th style="text-align:center">PENDING DEMAND</th></tr>';
            $report .='<tr><td style="text-align:center">Demand Created : '.$fe_ck1['invoice_date'].'</td></tr>';

            $report .='<tr><td style="text-align:center">Demand From : '.$fe_ck1['brunch'].'</td></tr>';

            $report .='<tr>
                <td   style="text-align:center;font-size:18px;color:red">';
                $report .= '<b class="form-group required control-label">Select Dispatcher</b>

                <select style="width:100%!imortant"  id="Seletdispatcher_id" name="Seletdispatcher_id[]" data-live-search=true class="selectpicker" multiple data-selected-text-format="count>3" data-all="false" >';

                $qry = $conn_me->prepare("SELECT A.`id`,B.`name` FROM `admin` A JOIN `setup_employee` B ON (`A`.`employee_id` = B.`id`)  WHERE A.hr_status = 'Active' AND B.`designation` = '20'; ");
                $qry->execute();
                $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);

                foreach($fetch_list AS $fetch2) { 
                $report .= '<option value="'.$fetch2['id'].'">'.$fetch2['name'].'</option>';
                }


                $report .= '</select>';
                $report .= ' </td>
            </tr>';

            $report .='</table>
            <input type="hidden" id="demand_id" value="'.$ID.'"> 
            <input type="hidden" id="demand_from_brunch_id" value="'.$fe_ck1['demand_created_from'].'">
            <input type="hidden" id="demand_to_brunch_id" value="'.$fe_ck1['demand_created_to'].'">';

        

$report .= '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">';
                    
                   

                    $report .= ' <tr>

                        <th> Sl</th>
                        <th>Product Name</th>
                        <th>Demand Qty</th>
                        <th>From Warehouse </th>
                        <th> Price</th>
                        <th>To Warehouse </th>

                    </tr>';

                    $sl=1;
                    $count = 0;
                    $query = $conn_me->prepare("SELECT A.*,B.product_name,B.sales_rate FROM `demand_item` A  JOIN setup_product B ON (A.product_id  = B.id) where A.`demand_id`  = '".$ID."'  ");
                    $query->execute();
                    $rowCount =$query->rowCount();
                    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($fetch_list as $fetch ) {
                       
                        
$report .= ' <input value="'.$fetch['product_id'].'"  type="hidden" name="product_id[]" id="product_id'.$count.'" data-srno="'.$count.'" class="form-control product_id"/>';
$report .= ' <input value="'.$fetch['quantity'].'"  type="hidden" name="demand_quantity[]" id="demand_quantity'.$count.'" data-srno="'.$count.'" class="form-control demand_quantity"/>';
$report .= ' <input value="'.$fetch['id'].'"  type="hidden" name="related_id[]" id="related_id'.$count.'" data-srno="'.$count.'" class="form-control related_id"/>';
        
$report .= ' <input value="'.$fetch['sales_rate'].'"  type="hidden" name="sales_rate[]" id="sales_rate'.$count.'" data-srno="'.$count.'" class="form-control sales_rate"/>';

                       

                        $report .= ' <tr  id="tr_no'. $count .'">';
                        $report .= '<td>'.$sl.'</td>';
                        $report .= '<td>'.$fetch['product_name'].'</td>';
                       
                        $report .= ' <td>'.number_format((float)$fetch['quantity'], 2, '.', '').'</td>';

                      

                        if($fetch['converat_to_invoice'] == 'Done' ){

                            $report .= ' <td><select id="dispatch_from_warehouse'.$count.'" name="dispatch_from_warehouse[]"   class="form-control select"><option data-stock="0" value="DONT">DONE</option></select>';
    
                            $report .= '<td></td>';
                            $report .= ' <td><select id="received_warehouse'.$count.'" name="received_warehouse[]"   class="form-control select"><option data-stock="0" value="DONT">DONE</option></select></td>';
            
                               
                   
                            

       

                        }else{

                            $report .= ' <td><select id="dispatch_from_warehouse'.$count.'" name="dispatch_from_warehouse[]"   class="form-control select" data-live-search="true">';


                        $report .= '<option value="DONT">Dont Send</option>';

                        $qry = $conn_me->prepare("SELECT * FROM `setup_brunch`  where `id` = '". $fe_ck1['demand_created_to']."' ");
                        $qry->execute();
                        $fetch_list = $qry->fetch(PDO::FETCH_ASSOC);
                        $jsonData = $fetch_list['related_warehouse'];
                        $obj = json_decode($jsonData);
                        foreach($obj as $key => $value) {
                                    $info_wh = SETUP::SETUP_WAREHOUSE($value);
                                    $stock = FIND::WAREHOUSE_WISE_STOCK('FG',$fetch['product_id'],$value);

                                    if($stock['warhouse_wise_stock'] > 0 ){
                                        $report .= '<option data-stock="' . $stock['warhouse_wise_stock'] . '" value="'.$value.'"> '.$info_wh['name'].'  ( STOCK ::: Pcs-' . $stock['warhouse_wise_stock'] .' Carton-'.$stock['warhouse_wise_stock_in_carton'].')</option>';
                                    }
                           
                        
                        }

                        $report .= '</select>
                        </td>';


                        $report .= '<td>'.$fetch['sales_rate'].'</td>';

                        $report .= ' <td><select id="received_warehouse'.$count.'" name="received_warehouse[]"   class="form-control select" data-live-search="true">';

                        $report .= '<option value="DONT">Dont Send</option>';
                        $qry = $conn_me->prepare("SELECT * FROM `setup_brunch`  where `id` = '". $fe_ck1['demand_created_from']."' ");
                        $qry->execute();
                        $fetch_list = $qry->fetch(PDO::FETCH_ASSOC);
                        $jsonData = $fetch_list['related_warehouse'];
                        $obj = json_decode($jsonData);
                        foreach($obj as $key => $value) {
                                    $info_wh = SETUP::SETUP_WAREHOUSE($value);
                            $report .= '<option value="'.$value.'"> '.$info_wh['name'].' </option>';
                        
                        }


                        }

                        


                        $report .= '</select>
                        </td>';


               
                    

                    
                        $report .= '</tr>';
                        $count = $count + 1;
                        $sl++;
                    
                    }

                    $report .='<tr>
                       
                    <input type="hidden" name="total_item" id="total_item" value="'.$rowCount.'"/>

                    <th style="text-align:center;color:red" colspan="6" id="action_bar">';
                    $report .='<input type="button" onclick="convert_customer_demand_to_invoice();" id="action_bar"   value="Save Delivery">';
        
                    $report .='</th></tr></table></div>';




             return $report ; 
             
        }

}



class WORKFLOW {


  




    static  function PRODUCTION($WORK,$TYPE,$CODE,$FROM,$TO){
            $conn_me = Database::getInstance();

            $company_info = SETUP::SETUP_COMPANY('Active');
          

            $header_content ='<table style="width:100%;" class="table datatable_simple">';

            $header_content .='<tr><th style="text-align:center"><img style="height:70px" src="upload/Company_Logo/'.$company_info['company_logo'].'"></th></tr>';

            $header_content .='</table>';

            $report = '';

            if($WORK == 'molding' ){

// চেক করতেসে ::  একটা Molding  ইনভইচে এ একের অদিক সাপ্লাইয়ের আছে কিনা .... যদি থাকে রিপোর্ট দেকাবেনা  .... যদি ১ তা সাপ্লাইর হয় তাহলে dispatch করতে পারবে  

$ck = $conn_me->prepare("SELECT * FROM `raw_molding`  where `code` = '".$CODE."' GROUP BY `send_to`,`supplier_or_factory_id`  ");
$ck->execute();
$ck_count =$ck->rowCount();
$fetch_ck_data = $ck->fetch(PDO::FETCH_ASSOC);
$info_supplier_or_factory = FIND::SUPPLIER_OR_FACTORY($fetch_ck_data['supplier_or_factory_id'],$fetch_ck_data['send_to']);

if($ck_count > 1 ){
    
    $report .= '<b style="color:red;">This invoice contain more then one Supplier or Factory......</b>';

}else{

    if($TYPE == 'Pending Warehouse Dispatch For Molding' ){

        $report .= '<table style="width:100%;" class="table datatable_simple">
        <tr><th colspan="8" style="text-align:center;font-size:18px">'.$TYPE.'</th></tr> 
        <tr><th colspan="4" style="text-align:center;font-size:12px">'.$fetch_ck_data['send_to'].' Name: '.$info_supplier_or_factory['name'].' <th colspan="4" style="text-align:center;font-size:12px">Invoice No'.$fetch_ck_data['invoice_no'].'</th></tr> 

         <input type="hidden" id="code"  id="code"  value="'.$CODE.'">
         <input type="hidden" id="supplier_or_factory_id"  id="supplier_or_factory_id"  value="'.$fetch_ck_data['supplier_or_factory_id'].'">

        



         <input type="hidden" id="send_to"  id="send_to"  value="'.$fetch_ck_data['send_to'].'">
            <tr>
                <th>Sl</th>
                <th>Material Name</th>
                <th>Demand Qty </th>
                <th>Total Dispatch</th>
                <th>Dispatch Left </th>
                <th>Stock </th>
                <th>Dispatch Now </th>
                <th>Warehouse</th>

            </tr>';

        $total_qyantity = 0;
        $total_total_receive  = 0;
        $sl =1;
        $count = 0;
        $query = $conn_me->prepare("SELECT sum(`demand_quantity`) AS `total_demand`,`material_id`,`demand_code` FROM `raw_molding_item`  where `demand_code` = '".$CODE."' GROUP BY `material_id`  ");
        $query->execute();
        $rowCount =$query->rowCount();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) {
        $count = $count + 1;
        $info_material = SETUP::SETUP_RAW_MATERIAL($fetch['material_id']);
        $dispatch_info = FIND::MOLDING_RAW_MATERIAL_DEMAND_DISPATCH($fetch['demand_code'],$fetch['material_id'],'Invoice_Wise');
        $warehouse_list = FIND::WAREHOUSE_LIST('RAW',$fetch['material_id'],'');


        $total_demand = $fetch['total_demand'];
        $dispatch_left = $total_demand - $dispatch_info['total_dispatch'];
        $stock = STOCK::RAW_ITEM_WISE_STOCK('',$fetch['material_id'],'product_wise');



        $report .= '<input value="'.$fetch['total_demand'].'"  type="hidden" name="actual_quantity[]" id="actual_quantity'.$count.'" data-srno="'.$count.'" class="form-control actual_quantity"/> ';
        $report .= '<input value="'.$fetch['material_id'].'"  type="hidden" name="product_id[]" id="product_id'.$count.'" data-srno="'.$count.'" class="form-control product_id"/> ';
        $report .= '<input value="'.$stock['ITEM_STOCK'].'"  type="hidden" name="item_stock[]" id="item_stock'.$count.'" data-srno="'.$count.'" class="form-control item_stock"/> ';
        $report .= '<input value="'.$dispatch_info['total_dispatch'].'"  type="hidden" name="total_dispatch[]" id="total_dispatch'.$count.'" data-srno="'.$count.'" class="form-control total_dispatch"/>';

            $report .= '<tr class="tr'. $count .'">';
            $report .= '<td>'.$sl++.'</td>';
            $report .= '<td>'.$info_material['product_name'].'</td>';
            $report .= '<td>'.$total_demand.'</td>';
            $report .= '<td>'.$dispatch_info['total_dispatch'].'</td>';
            $report .= '<td>'.$dispatch_left.'</td>';
            $report .= '<td>'.$stock['ITEM_STOCK'].'</td>';
            $report .= '<td>';
            $report .='<input type="number" name="dispatch_now[]" id="dispatch_now'.$count.'" data-srno="'.$count.'" class="form-control dispatch_now" value="0.00"/> ';
            $report .= '</td>';
            $report .= '<td>';
            $report .= '<select id="warehouse_id'.$count.'" name="warehouse_id[]" onchange="getEachWarehouseStock(\'RAW\',\'product_id\',\'warehouse_id\','.$count.')" class="form-control select" data-live-search="true">';
            $report .= $warehouse_list['warehouse_list'];
          $report .= '</select>';
            $report .= '</td>';

            $report .= '</tr>';

    $total_qyantity += $dispatch_info['total_dispatch']; 
    $total_total_receive  += $dispatch_left;

        }

        $report .='<tr>
                       
        <input type="hidden" name="total_item" id="total_item" value="'.$rowCount.'"/>
        <th style="text-align:center;color:red" colspan="4" id="action_bar">';
        $report .='<input type="button" onclick="dispatch_raw_material_for_molding();"   value="Dispatch Now">';



    $report .='
    <th style="text-align:center;color:green" colspan="4" id="action_bar">';
    if($total_qyantity == $total_total_receive){
        $report .='<input type="button" onclick="done_receive_raw_local_purches();"  value="Dispatch Done">';
     }
$report .='</th></tr>';

    $report .='</table>';

    }else if($TYPE == 'Mold Receive From Supplier' || $TYPE == 'Mold Receive From Factory'){


        $report .= '<table style="width:100%;" class="table datatable_simple">
        <tr><th colspan="6" style="text-align:center;font-size:18px">'.$TYPE.'</th></tr> 
        <tr><th colspan="3" style="text-align:center;font-size:12px">'.$fetch_ck_data['send_to'].' Name: '.$info_supplier_or_factory['name'].' <th colspan="3" style="text-align:center;font-size:12px">Invoice No'.$fetch_ck_data['invoice_no'].'</th></tr> 

         <input type="hidden" id="code"  id="code"  value="'.$CODE.'">
         <input type="hidden" id="supplier_or_factory_id"  id="supplier_or_factory_id"  value="'.$fetch_ck_data['supplier_or_factory_id'].'">
         <input type="hidden" id="send_to"  id="send_to"  value="'.$fetch_ck_data['send_to'].'">
            <tr>
                <th>Sl</th>
                <th>Material Name</th>
                <th> Batch QTY  </th>
                <th> Received QTY  </th>
                <th> Receive Now  </th>
                <th>Warehouse</th>
            </tr>';
            $total_qyantity = 0;
            $total_total_receive  = 0;
            $sl =1;
            $count = 0;
            $query = $conn_me->prepare("SELECT sum(`batch_quantity`) AS `total_batch_qty`,`supporting_id`,`code` FROM `raw_molding`  where `code` = '".$CODE."' GROUP BY `supporting_id`  ");
            $query->execute();
            $rowCount =$query->rowCount();
            $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($fetch_list as $fetch ) {
                $count = $count + 1;
                $info_material = SETUP::SETUP_RAW_MATERIAL($fetch['supporting_id']);
                $received_info = FIND::MOLDING_RAW_MATERIAL_BATCH_RECEIVE($fetch['code'],$fetch['supporting_id'],'Invoice_Wise');


            $report .= '<tr class="tr'. $count .'">';
            $report .= '<td>'.$sl++.'</td>';
            $report .= '<td>'.$info_material['product_name'].'</td>';
            $report .= '<td>'.$fetch['total_batch_qty'].'</td>';
            $report .= '<td>'.$received_info['actual_receive'].'</td>';
            $report .= '<td>';
            $report .='<input type="number" name="receive_now[]" id="receive_now'.$count.'" data-srno="'.$count.'" class="form-control receive_now" value="0.00"/> ';


            $report .='<input type="hidden" name="total_batch_qty[]" id="total_batch_qty'.$count.'" data-srno="'.$count.'" class="form-control total_batch_qty" value="'.$fetch['total_batch_qty'].'"/> ';
            $report .='<input type="hidden" name="actual_receive[]" id="actual_receive'.$count.'" data-srno="'.$count.'" class="form-control actual_receive" value="'.$received_info['actual_receive'].'"/> ';
            $report .='<input type="hidden" name="supporting_id[]" id="supporting_id'.$count.'" data-srno="'.$count.'" class="form-control supporting_id" value="'.$fetch['supporting_id'].'"/> ';

            $report .= '</td>';
            $report .= '<td>';
            $report .= '<select id="warehouse_id'.$count.'" name="warehouse_id[]"   class="form-control select" data-live-search="true">
            <option value="">Select One</option>';
            $qry = $conn_me->prepare("SELECT * FROM `setup_warehouse` where status = 1 ");
            $qry->execute();
            $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
            foreach ($fetch_list as $fetch_w) {
    
            $report .= '<option value="'.$fetch_w['id'].'">'.$fetch_w['name'].'</option>';
                } 
    
          $report .= '</select>';
            $report .= '</td>';

            $report .= '</tr>';
            }
            $report .='<tr>
                       
            <input type="hidden" name="total_item" id="total_item" value="'.$rowCount.'"/>
            <th style="text-align:center;color:red" colspan="6" id="action_bar">';
            $report .='<input type="button" onclick="receive_after_molding();"   value="Receive Now">';

            $report .='</th></tr>';

        }else if($TYPE == 'Report Send For Molding'){


        $ck = $conn_me->prepare("SELECT * FROM `raw_molding`  where `code` = '".$CODE."' GROUP BY `send_to`,`supplier_or_factory_id`  ");
        $ck->execute();
        $ck_count =$ck->rowCount();
        $fetch_ck_data = $ck->fetch(PDO::FETCH_ASSOC);
        $info_supplier_or_factory = FIND::SUPPLIER_OR_FACTORY($fetch_ck_data['supplier_or_factory_id'],$fetch_ck_data['send_to']);



             $report .= '<table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">

             
        <tr><th colspan="3" style="text-align:center;font-size:18px">'.$TYPE.'</th></tr> ';
        $report .= ' <tr><td colspan="3" style="text-align:center;font-size:12px;">Supplier : '.$info_supplier_or_factory['name'].'<p style="text-align:center;font-size:12px;">Batch No : '.$fetch_ck_data['invoice_no'].' || Note : '.$fetch_ck_data['note'].' ||  Date : '.$fetch_ck_data['date'].' </p></td></tr> ';


        $report .= '<tr><th colspan="3" style="text-align:center"> Item List </th></tr>';

        $report .='<tr>
                <th>Sl</th>
                <th>Item Name</th>
                <th> Batch QTY  </th>
               
            </tr>';
            $total_batch = 0;
            $sl =1;
            $raw_molding_ids = '';
            $query = $conn_me->prepare("SELECT * FROM `raw_molding`  where `code` = '".$CODE."'  ");
            $query->execute();
            $rowCount =$query->rowCount();

            if($rowCount > 0 ){

                $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
                foreach ($fetch_list as $fetch ) {
    
                 
                    $info_material = SETUP::SETUP_RAW_MATERIAL($fetch['supporting_id']);
                   
                    $raw_molding_ids = $fetch['id'] . ',';
    
                $report .= '<tr>';
                $report .= '<td>'.$sl++.'</td>';
                $report .= '<td>'.$info_material['product_name'].'</td>';
                $report .= '<td>'.$fetch['batch_quantity'].'</td>';
                $report .= '</tr>';
                $total_batch += $fetch['batch_quantity']; 
                }


                $report .= '<tr><th colspan="2">T O T A L</th><th>'.$total_batch.'</th></tr>';
                $report .= '<tr ><th colspan="3" style="text-align:center"> Material List </th></tr>';
    
                $report .='<tr>
                        <th>Sl</th>
                        <th>Material Name</th>
                        <th>  QTY  </th>
                       
                    </tr>';
                    $total_qyantity = 0;
                    $sl =1;
                    $query2 = $conn_me->prepare("SELECT SUM(`demand_quantity`) AS `total_demand` ,`material_id` FROM `raw_molding_item`   where `raw_molding_id` IN ('$raw_molding_ids') group by `material_id`  ");
                    $query2->execute();
                    $fetch_list2 = $query2->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($fetch_list2 as $fetch2 ) {
                        $info_material = SETUP::SETUP_RAW_MATERIAL($fetch2['material_id']);
                      
        
                    $report .= '<tr>';
                    $report .= '<td>'.$sl++.'</td>';
                    $report .= '<td>'.$info_material['product_name'].'</td>';
                    $report .= '<td>'.$fetch2['total_demand'].'</td>';
                    $report .= '</tr>';
                    $total_qyantity +=$fetch2['total_demand'];
    
                    }
                    $report .= '<tr><th colspan="2">T O T A L</th><th>'.$total_qyantity.'</th></tr>';

            }else{
                $report .= '<b class="text-danger">Deleted After Cart..</b>';
            }
           

  
        }else if($TYPE == 'Batch Wise Send For Molding'){

        $ck = $conn_me->prepare("SELECT *,GROUP_CONCAT(`invoice_no`) as `INVOICENO` ,GROUP_CONCAT(`note`) as `nots`  FROM `raw_molding`  where `receipe_wise_demand_code` = '".$CODE."' GROUP BY `send_to`,`supplier_or_factory_id`  ");
        $ck->execute();
        $ck_count =$ck->rowCount();
        $fetch_ck_data = $ck->fetch(PDO::FETCH_ASSOC);
        $info_supplier_or_factory = FIND::SUPPLIER_OR_FACTORY($fetch_ck_data['supplier_or_factory_id'],$fetch_ck_data['send_to']);



             $report .= '<table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">

             
        <tr><th colspan="3" style="text-align:center;font-size:18px">'.$TYPE.'</th></tr> ';

        $report .= ' <tr><td colspan="3" style="text-align:center;font-size:12px;">Supplier : '.$info_supplier_or_factory['name'].'<p style="text-align:center;font-size:12px;">Batch No : '.$fetch_ck_data['INVOICENO'].' || Note : '.$fetch_ck_data['nots'].' ||  Date : '.$fetch_ck_data['date'].' </p></td></tr> ';


        $report .= '<tr><th colspan="3" style="text-align:center"> Item List </th></tr>';

        $report .='<tr>
                <th>Sl</th>
                <th>Item Name</th>
                <th> Batch QTY  </th>
               
            </tr>';
            $total_batch = 0;
            $sl =1;
            $raw_molding_ids = '';
            $query = $conn_me->prepare("SELECT * FROM `raw_molding`  where `receipe_wise_demand_code` = '".$CODE."'  ");
            $query->execute();
            $rowCount =$query->rowCount();

            if($rowCount > 0 ){

                $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
                foreach ($fetch_list as $fetch ) {
    
                 
                    $info_material = SETUP::SETUP_RAW_MATERIAL($fetch['supporting_id']);
                   
                    $raw_molding_ids .= "'$fetch[id]',";

                $report .= '<tr>';
                $report .= '<td>'.$sl++.'</td>';
                $report .= '<td>'.$info_material['product_name'].'</td>';
                $report .= '<td>'.$fetch['batch_quantity'].'</td>';
                $report .= '</tr>';
                $total_batch += $fetch['batch_quantity']; 
                }


                $report .= '<tr><th colspan="2">T O T A L</th><th>'.$total_batch.'</th></tr>';
                $report .= '<tr ><th colspan="3" style="text-align:center"> Material List </th></tr>';
    
                $report .='<tr>
                        <th>Sl</th>
                        <th>Material Name</th>
                        <th>  QTY  </th>
                       
                    </tr>';
                    $total_qyantity = 0;
                    $sl =1;
                    $raw_molding_ids  = trim($raw_molding_ids,",");
                    $query2 = $conn_me->prepare("SELECT SUM(`demand_quantity`) AS `total_demand` ,`material_id` FROM `raw_molding_item`   where `raw_molding_id` IN ($raw_molding_ids) group by `material_id`  ");
                    $query2->execute();
                    $fetch_list2 = $query2->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($fetch_list2 as $fetch2 ) {
                        $info_material = SETUP::SETUP_RAW_MATERIAL($fetch2['material_id']);
                      
        
                    $report .= '<tr>';
                    $report .= '<td>'.$sl++.'</td>';
                    $report .= '<td>'.$info_material['product_name'].'</td>';
                    $report .= '<td>'.$fetch2['total_demand'].'</td>';
                    $report .= '</tr>';
                    $total_qyantity +=$fetch2['total_demand'];
    
                    }
                    $report .= '<tr><th colspan="2">T O T A L</th><th>'.$total_qyantity.'</th></tr>';

            }else{
                $report .= '<b class="text-danger">Deleted After Cart..</b>';
            }
           

           


     }else{

        $report .= '';

     }

     



}



}else if ($WORK == 'spray'){



// চেক করতেসে ::  একটা spray  ইনভইচে এ একের অদিক সাপ্লাইয়ের আছে কিনা .... যদি থাকে রিপোর্ট দেকাবেনা  .... যদি ১ তা সাপ্লাইর হয় তাহলে dispatch করতে পারবে  

$ck = $conn_me->prepare("SELECT * FROM `raw_spray`  where `code` = '".$CODE."' GROUP BY `send_to`,`supplier_or_factory_id`  ");
$ck->execute();
$ck_count =$ck->rowCount();
$fetch_ck_data = $ck->fetch(PDO::FETCH_ASSOC);
$info_supplier_or_factory = FIND::SUPPLIER_OR_FACTORY($fetch_ck_data['supplier_or_factory_id'],$fetch_ck_data['send_to']);

if($ck_count > 1 ){
    
    $report .= '<b style="color:red;">This invoice contain more then one Supplier or Factory......</b>';

}else{

    if($TYPE == 'Pending Warehouse Dispatch For Spray' ){

        $report .= '<table style="width:100%;" class="table datatable_simple">
        <tr><th colspan="8" style="text-align:center;font-size:18px">'.$TYPE.'</th></tr> 
        <tr><th colspan="4" style="text-align:center;font-size:12px">'.$fetch_ck_data['send_to'].' Name: '.$info_supplier_or_factory['name'].' <th colspan="4" style="text-align:center;font-size:12px">Invoice No'.$fetch_ck_data['invoice_no'].'</th></tr> 

         <input type="hidden" id="code"  id="code"  value="'.$CODE.'">
         <input type="hidden" id="supplier_or_factory_id"  id="supplier_or_factory_id"  value="'.$fetch_ck_data['supplier_or_factory_id'].'">
         <input type="hidden" id="send_to"  id="send_to"  value="'.$fetch_ck_data['send_to'].'">
            
         
         <tr><th colspan="8" style="text-align:center;font-size:18px">Item List</th></tr> 

         <tr>
         <th>Sl</th>
         <th>Item Name</th>
         <th>Batch Qty </th>
         <th>Total Dispatch</th>
         <th>Dispatch Left </th>
         <th>Stock </th>
         <th>Dispatch Now </th>
         <th>Warehouse</th>

     </tr>';

    
     $sl2 =1;
     $count2 = 0;
     $query2 = $conn_me->prepare("SELECT sum(`batch_quantity`) AS `total_demand`,`material_id`,`code` FROM `raw_spray`  where `code` = '".$CODE."' GROUP BY `material_id`  ");
     $query2->execute();
     $rowCount2 =$query2->rowCount();
     $fetch_list2 = $query2->fetchAll(PDO::FETCH_ASSOC);
     foreach ($fetch_list2 as $fetch2 ) {
     $count2 = $count2 + 1;
     $info_material = SETUP::SETUP_RAW_MATERIAL($fetch2['material_id']);
     $dispatch_info = FIND::SPRAY_MATERIAL_DEMAND_DISPATCH($fetch2['code'],$fetch2['material_id'],'Invoice_Wise');
     $total_demand = $fetch2['total_demand'];
     $dispatch_left = $total_demand - $dispatch_info['total_dispatch'];
     $stock = STOCK::RAW_ITEM_WISE_STOCK('',$fetch2['material_id'],'product_wise');
     $warehouse_list = FIND::WAREHOUSE_LIST('RAW',$fetch2['material_id'],'');


     $report .= '<input value="'.$fetch2['material_id'].'"  type="hidden" name="spray_product_id[]" id="spray_product_id'.$count2.'" data-srno="'.$count2.'" class="form-control spray_product_id"/> ';

     $report .= '<input value="'.$fetch2['total_demand'].'"  type="hidden" name="spray_actual_quantity[]" id="spray_actual_quantity'.$count2.'" data-srno="'.$count2.'" class="form-control spray_actual_quantity"/> ';

     $report .= '<input value="'.$dispatch_info['total_dispatch'].'"  type="hidden" name="spray_total_dispatch[]" id="spray_total_dispatch'.$count2.'" data-srno="'.$count2.'" class="form-control spray_total_dispatch"/>';

         $report .= '<tr class="tr'. $count2 .'">';
         $report .= '<td>'.$sl2++.'</td>';
         $report .= '<td>'.$info_material['product_name'].'</td>';
         $report .= '<td>'.$total_demand.'</td>';
         $report .= '<td>'.$dispatch_info['total_dispatch'].'</td>';
         $report .= '<td>'.$dispatch_left.'</td>';
         $report .= '<td>'.$stock['ITEM_STOCK'].'</td>';
         $report .= '<td>';
         $report .='<input type="number" name="dispatch_now_item[]" id="dispatch_now_item'.$count2.'" data-srno="'.$count2.'" class="form-control dispatch_now_item" value="0.00"/> ';
         $report .= '</td>';
         $report .= '<td>';
         $report .= '<select id="spray_warehouse_id'.$count2.'" name="spray_warehouse_id[]"   class="form-control select" data-live-search="true">';
        
         $report .= $warehouse_list['warehouse_list'];

       $report .= '</select>';
         $report .= '</td>';

         $report .= '</tr>';


     }

     $report .='<tr>';
     $report .='<input type="hidden" name="total_spray_item" id="total_spray_item" value="'.$rowCount2.'"/>';

         $report .= '<tr><th colspan="8" style="text-align:center;font-size:18px">Material List</th></tr> 

         <tr>
                <th>Sl</th>
                <th>Material Name</th>
                <th>Demand Qty </th>
                <th>Total Dispatch</th>
                <th>Dispatch Left </th>
                <th>Stock </th>
                <th>Dispatch Now </th>
                <th>Warehouse</th>

            </tr>';

        $total_qyantity = 0;
        $total_total_receive  = 0;
        $sl =1;
        $count = 0;
        $query = $conn_me->prepare("SELECT sum(`demand_quantity`) AS `total_demand`,`material_id`,`demand_code` FROM `raw_spray_item`  where `demand_code` = '".$CODE."' GROUP BY `material_id`  ");
        $query->execute();
        $rowCount =$query->rowCount();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) {
        $count = $count + 1;
        $info_material = SETUP::SETUP_RAW_MATERIAL($fetch['material_id']);
        $dispatch_info = FIND::SPRAY_RAW_MATERIAL_DEMAND_DISPATCH($fetch['demand_code'],$fetch['material_id'],'Invoice_Wise');
        $total_demand = $fetch['total_demand'];
        $dispatch_left = $total_demand - $dispatch_info['total_dispatch'];
        $stock = STOCK::RAW_ITEM_WISE_STOCK('',$fetch['material_id'],'product_wise');
        $warehouse_list = FIND::WAREHOUSE_LIST('RAW',$fetch['material_id'],'');


        $report .= '<input value="'.$fetch['total_demand'].'"  type="hidden" name="actual_quantity[]" id="actual_quantity'.$count.'" data-srno="'.$count.'" class="form-control actual_quantity"/> ';
        $report .= '<input value="'.$fetch['material_id'].'"  type="hidden" name="product_id[]" id="product_id'.$count.'" data-srno="'.$count.'" class="form-control product_id"/> ';
        $report .= '<input value="'.$stock['ITEM_STOCK'].'"  type="hidden" name="item_stock[]" id="item_stock'.$count.'" data-srno="'.$count.'" class="form-control item_stock"/> ';
        $report .= '<input value="'.$dispatch_info['total_dispatch'].'"  type="hidden" name="total_dispatch[]" id="total_dispatch'.$count.'" data-srno="'.$count.'" class="form-control total_dispatch"/>';

            $report .= '<tr class="tr'. $count .'">';
            $report .= '<td>'.$sl++.'</td>';
            $report .= '<td>'.$info_material['product_name'].'</td>';
            $report .= '<td>'.$total_demand.'</td>';
            $report .= '<td>'.$dispatch_info['total_dispatch'].'</td>';
            $report .= '<td>'.$dispatch_left.'</td>';
            $report .= '<td>'.$stock['ITEM_STOCK'].'</td>';
            $report .= '<td>';
            $report .='<input type="number" name="dispatch_now[]" id="dispatch_now'.$count.'" data-srno="'.$count.'" class="form-control dispatch_now" value="0.00"/> ';
            $report .= '</td>';
            $report .= '<td>';
            $report .= '<select id="warehouse_id'.$count.'" name="warehouse_id[]"   class="form-control select" data-live-search="true">';
           
            $report .= $warehouse_list['warehouse_list'];

          $report .= '</select>';
            $report .= '</td>';

            $report .= '</tr>';

    $total_qyantity += $dispatch_info['total_dispatch']; 
    $total_total_receive  += $dispatch_left;

        }

        $report .='<tr>
                       
        <input type="hidden" name="total_item" id="total_item" value="'.$rowCount.'"/>
        <th style="text-align:center;color:red" colspan="4" id="action_bar">';
        $report .='<input type="button" onclick="dispatch_raw_material_for_spray();"   value="Dispatch Now">';

$report .='</th></tr>';

    $report .='</table>';

    }else if($TYPE == 'Spray Receive From Supplier' || $TYPE == 'Spray Receive From Factory'){


        $report .= '<table style="width:100%;" class="table datatable_simple">
        <tr><th colspan="6" style="text-align:center;font-size:18px">'.$TYPE.'</th></tr> 
        <tr><th colspan="3" style="text-align:center;font-size:12px">'.$fetch_ck_data['send_to'].' Name: '.$info_supplier_or_factory['name'].' <th colspan="3" style="text-align:center;font-size:12px">Invoice No'.$fetch_ck_data['invoice_no'].'</th></tr> 

         <input type="hidden" id="code"  id="code"  value="'.$CODE.'">
         <input type="hidden" id="supplier_or_factory_id"  id="supplier_or_factory_id"  value="'.$fetch_ck_data['supplier_or_factory_id'].'">
         <input type="hidden" id="send_to"  id="send_to"  value="'.$fetch_ck_data['send_to'].'">
            <tr>
                <th>Sl</th>
                <th>Material Name</th>
                <th> Batch QTY  </th>
                <th> Received QTY  </th>
                <th> Receive Now  </th>
                <th>Warehouse</th>
            </tr>';
            $total_qyantity = 0;
            $total_total_receive  = 0;
            $sl =1;
            $count = 0;
            $query = $conn_me->prepare("SELECT sum(`batch_quantity`) AS `total_batch_qty`,`material_id`,`code` FROM `raw_spray`  where `code` = '".$CODE."' GROUP BY `material_id`  ");
            $query->execute();
            $rowCount =$query->rowCount();
            $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($fetch_list as $fetch ) {
                $count = $count + 1;
                $info_material = SETUP::SETUP_RAW_MATERIAL($fetch['material_id']);
                $received_info = FIND::SPRAY_RAW_MATERIAL_BATCH_RECEIVE($fetch['code'],$fetch['material_id'],'Invoice_Wise');


            $report .= '<tr class="tr'. $count .'">';
            $report .= '<td>'.$sl++.'</td>';
            $report .= '<td>'.$info_material['product_name'].'</td>';
            $report .= '<td>'.$fetch['total_batch_qty'].'</td>';
            $report .= '<td>'.$received_info['actual_receive'].'</td>';
            $report .= '<td>';
            $report .='<input type="number" name="receive_now[]" id="receive_now'.$count.'" data-srno="'.$count.'" class="form-control receive_now" value="0.00"/> ';


            $report .='<input type="hidden" name="total_batch_qty[]" id="total_batch_qty'.$count.'" data-srno="'.$count.'" class="form-control total_batch_qty" value="'.$fetch['total_batch_qty'].'"/> ';
            $report .='<input type="hidden" name="actual_receive[]" id="actual_receive'.$count.'" data-srno="'.$count.'" class="form-control actual_receive" value="'.$received_info['actual_receive'].'"/> ';
            $report .='<input type="hidden" name="material_id[]" id="material_id'.$count.'" data-srno="'.$count.'" class="form-control material_id" value="'.$fetch['material_id'].'"/> ';

            $report .= '</td>';
            $report .= '<td>';
            $report .= '<select id="warehouse_id'.$count.'" name="warehouse_id[]"   class="form-control select" data-live-search="true">
            <option value="">Select One</option>';
            $qry = $conn_me->prepare("SELECT * FROM `setup_warehouse`  where status = 1 ");
            $qry->execute();
            $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
            foreach ($fetch_list as $fetch_w) {
    
            $report .= '<option value="'.$fetch_w['id'].'">'.$fetch_w['name'].'</option>';
                } 
    
          $report .= '</select>';
            $report .= '</td>';

            $report .= '</tr>';
            }
            $report .='<tr>
                       
            <input type="hidden" name="total_item" id="total_item" value="'.$rowCount.'"/>
            <th style="text-align:center;color:red" colspan="6" id="action_bar">';
            $report .='<input type="button" onclick="receive_after_spray();"   value="Receive Now">';

            $report .='</th></tr>';

        }else if($TYPE == 'Report Send For Spray' ){

            $report .= '<table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">

            
       <tr><th colspan="3" style="text-align:center;font-size:18px">'.$TYPE.'</th></tr> ';

       $report .= ' <tr><td colspan="3" style="text-align:center;font-size:12px;">Supplier : '.$info_supplier_or_factory['name'].'<p style="text-align:center;font-size:12px;">Batch No : '.$fetch_ck_data['invoice_no'].' || Note : '.$fetch_ck_data['note'].' ||  Date : '.$fetch_ck_data['date'].' </p></td></tr> ';


       $report .= '<tr><th colspan="3" style="text-align:center"> Item List </th></tr>';

       $report .='<tr>
               <th>Sl</th>
               <th>Item Name</th>
               <th> Batch QTY  </th>
              
           </tr>';
           $total_batch = 0;
           $sl =1;
           $query = $conn_me->prepare("SELECT * FROM `raw_spray`  where `code` = '".$CODE."'  ");
           $query->execute();
           $rowCount =$query->rowCount();
           $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
           foreach ($fetch_list as $fetch ) {
               $info_material = SETUP::SETUP_RAW_MATERIAL($fetch['material_id']);
              


           $report .= '<tr>';
           $report .= '<td>'.$sl++.'</td>';
           $report .= '<td>'.$info_material['product_name'].'</td>';
           $report .= '<td>'.$fetch['batch_quantity'].'</td>';
           $report .= '</tr>';
           $total_batch += $fetch['batch_quantity']; 
           }

           $report .= '<tr><th colspan="2">T O T A L</th><th>'.$total_batch.'</th></tr>';
           $report .= '<tr ><th colspan="3" style="text-align:center"> Material List </th></tr>';

           $report .='<tr>
                   <th>Sl</th>
                   <th>Material Name</th>
                   <th>  QTY  </th>
                  
               </tr>';
               $total_qyantity = 0;
               $sl =1;
               $query2 = $conn_me->prepare("SELECT SUM(`demand_quantity`) AS `total_demand` ,`material_id` FROM `raw_spray_item`   where `demand_code` = '".$CODE."' group by `material_id`  ");
               $query2->execute();
               $fetch_list2 = $query2->fetchAll(PDO::FETCH_ASSOC);
               foreach ($fetch_list2 as $fetch2 ) {
                   $info_material = SETUP::SETUP_RAW_MATERIAL($fetch2['material_id']);
                 
   
               $report .= '<tr>';
               $report .= '<td>'.$sl++.'</td>';
               $report .= '<td>'.$info_material['product_name'].'</td>';
               $report .= '<td>'.$fetch2['total_demand'].'</td>';
               $report .= '</tr>';
               $total_qyantity +=$fetch2['total_demand'];

               }
               $report .= '<tr><th colspan="2">T O T A L</th><th>'.$total_qyantity.'</th></tr>';

    }else if($TYPE == 'Batch Wise Send For Spray' ){

$ck = $conn_me->prepare("SELECT * FROM `raw_spray`  where `receipe_wise_demand_code` = '".$CODE."' GROUP BY `send_to`,`supplier_or_factory_id`  ");
$ck->execute();
$ck_count =$ck->rowCount();
$fetch_ck_data = $ck->fetch(PDO::FETCH_ASSOC);
$info_supplier_or_factory = FIND::SUPPLIER_OR_FACTORY($fetch_ck_data['supplier_or_factory_id'],$fetch_ck_data['send_to']);


        $report .= '<table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">

            
        <tr><th colspan="3" style="text-align:center;font-size:18px">'.$TYPE.'</th></tr> ';
 
        $report .= ' <tr><td colspan="3" style="text-align:center;font-size:12px;">Supplier : '.$info_supplier_or_factory['name'].'<p style="text-align:center;font-size:12px;">Batch No : '.$fetch_ck_data['invoice_no'].' || Note : '.$fetch_ck_data['note'].' ||  Date : '.$fetch_ck_data['date'].' </p></td></tr> ';
 
 
        $report .= '<tr><th colspan="3" style="text-align:center"> Item List </th></tr>';
 
        $report .='<tr>
                <th>Sl</th>
                <th>Item Name</th>
                <th> Batch QTY  </th>
               
            </tr>';
            $total_batch = 0;
            $sl =1;
            $raw_spray_ids = '';
            $query = $conn_me->prepare("SELECT * FROM `raw_spray`  where `receipe_wise_demand_code` = '".$CODE."'  ");
            $query->execute();
            $rowCount =$query->rowCount();
            $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($fetch_list as $fetch ) {
                $info_material = SETUP::SETUP_RAW_MATERIAL($fetch['material_id']);
               
                $raw_spray_ids .= "'$fetch[id]',";
 
            $report .= '<tr>';
            $report .= '<td>'.$sl++.'</td>';
            $report .= '<td>'.$info_material['product_name'].'</td>';
            $report .= '<td>'.$fetch['batch_quantity'].'</td>';
            $report .= '</tr>';
            $total_batch += $fetch['batch_quantity']; 
            }
 
            $report .= '<tr><th colspan="2">T O T A L</th><th>'.$total_batch.'</th></tr>';
            $report .= '<tr ><th colspan="3" style="text-align:center"> Material List </th></tr>';
 
            $report .='<tr>
                    <th>Sl</th>
                    <th>Material Name</th>
                    <th>  QTY  </th>
                   
                </tr>';
                $total_qyantity = 0;
                $sl =1;
                $raw_spray_ids  = trim($raw_spray_ids,",");
                $query2 = $conn_me->prepare("SELECT  *  FROM `raw_spray_item`   where `raw_spray_id` IN ($raw_spray_ids)  ");
                $query2->execute();
                $fetch_list2 = $query2->fetchAll(PDO::FETCH_ASSOC);
                foreach ($fetch_list2 as $fetch2 ) {
                    $info_material = SETUP::SETUP_RAW_MATERIAL($fetch2['material_id']);
                  
    
                $report .= '<tr>';
                $report .= '<td>'.$sl++.'</td>';
                $report .= '<td>'.$info_material['product_name'].'</td>';
                $report .= '<td>'.$fetch2['demand_quantity'].'</td>';
                $report .= '</tr>';
                $total_qyantity +=$fetch2['demand_quantity'];
 
                }
                $report .= '<tr><th colspan="2">T O T A L</th><th>'.$total_qyantity.'</th></tr>';
 

        

     }else{

        $report .= '';

     }

     



}

}else if ($WORK == 'print'){





// চেক করতেসে ::  একটা print  ইনভইচে এ একের অদিক সাপ্লাইয়ের আছে কিনা .... যদি থাকে রিপোর্ট দেকাবেনা  .... যদি ১ তা সাপ্লাইর হয় তাহলে dispatch করতে পারবে  

$ck = $conn_me->prepare("SELECT * FROM `raw_print`  where `code` = '".$CODE."' GROUP BY `send_to`,`supplier_or_factory_id`  ");
$ck->execute();
$ck_count =$ck->rowCount();
$fetch_ck_data = $ck->fetch(PDO::FETCH_ASSOC);
$info_supplier_or_factory = FIND::SUPPLIER_OR_FACTORY($fetch_ck_data['supplier_or_factory_id'],$fetch_ck_data['send_to']);

if($ck_count > 1 ){
    
    $report .= '<b style="color:red;">This invoice contain more then one Supplier or Factory......</b>';

}else{

    if($TYPE == 'Pending Warehouse Dispatch For Print' ){

        $report .= '<table style="width:100%;" class="table datatable_simple">
        <tr><th colspan="8" style="text-align:center;font-size:18px">'.$TYPE.'</th></tr> 
        <tr><th colspan="4" style="text-align:center;font-size:12px">'.$fetch_ck_data['send_to'].' Name: '.$info_supplier_or_factory['name'].' <th colspan="4" style="text-align:center;font-size:12px">Invoice No'.$fetch_ck_data['invoice_no'].'</th></tr> 

         <input type="hidden" id="code"  id="code"  value="'.$CODE.'">
         <input type="hidden" id="supplier_or_factory_id"  id="supplier_or_factory_id"  value="'.$fetch_ck_data['supplier_or_factory_id'].'">
         <input type="hidden" id="send_to"  id="send_to"  value="'.$fetch_ck_data['send_to'].'">


         <tr><th colspan="8" style="text-align:center;font-size:18px">Item List</th></tr> 

         <tr>
         <th>Sl</th>
         <th>Item Name</th>
         <th>Batch Qty </th>
         <th>Total Dispatch</th>
         <th>Dispatch Left </th>
         <th>Stock </th>
         <th>Dispatch Now </th>
         <th>Warehouse</th>

     </tr>';

    
     $sl2 =1;
     $count2 = 0;
     $query2 = $conn_me->prepare("SELECT sum(`batch_quantity`) AS `total_demand`,`material_id`,`code` FROM `raw_print`  where `code` = '".$CODE."' GROUP BY `material_id`  ");
     $query2->execute();
     $rowCount2 =$query2->rowCount();
     $fetch_list2 = $query2->fetchAll(PDO::FETCH_ASSOC);
     foreach ($fetch_list2 as $fetch2 ) {
     $count2 = $count2 + 1;
     $info_material = SETUP::SETUP_RAW_MATERIAL($fetch2['material_id']);
     $dispatch_info = FIND::PRINT_MATERIAL_DEMAND_DISPATCH($fetch2['code'],$fetch2['material_id'],'Invoice_Wise');
     $total_demand = $fetch2['total_demand'];
     $dispatch_left = $total_demand - $dispatch_info['total_dispatch'];
     $stock = STOCK::RAW_ITEM_WISE_STOCK('',$fetch2['material_id'],'product_wise');
     $warehouse_list = FIND::WAREHOUSE_LIST('RAW',$fetch2['material_id'],'');




     $report .= '<input value="'.$fetch2['material_id'].'"  type="hidden" name="print_product_id[]" id="print_product_id'.$count2.'" data-srno="'.$count2.'" class="form-control print_product_id"/> ';

     $report .= '<input value="'.$fetch2['total_demand'].'"  type="hidden" name="print_actual_quantity[]" id="print_actual_quantity'.$count2.'" data-srno="'.$count2.'" class="form-control print_actual_quantity"/> ';

     $report .= '<input value="'.$dispatch_info['total_dispatch'].'"  type="hidden" name="print_total_dispatch[]" id="print_total_dispatch'.$count2.'" data-srno="'.$count2.'" class="form-control print_total_dispatch"/>';

         $report .= '<tr class="tr'. $count2 .'">';
         $report .= '<td>'.$sl2++.'</td>';
         $report .= '<td>'.$info_material['product_name'].'</td>';
         $report .= '<td>'.$total_demand.'</td>';
         $report .= '<td>'.$dispatch_info['total_dispatch'].'</td>';
         $report .= '<td>'.$dispatch_left.'</td>';
         $report .= '<td>'.$stock['ITEM_STOCK'].'</td>';
         $report .= '<td>';
         $report .='<input type="number" name="dispatch_now_item[]" id="dispatch_now_item'.$count2.'" data-srno="'.$count2.'" class="form-control dispatch_now_item" value="0.00"/> ';
         $report .= '</td>';
         $report .= '<td>';
         $report .= '<select onchange="getEachWarehouseStock(\'RAW\',\'product_id\',\'print_warehouse_id\','.$count2.')" id="print_warehouse_id'.$count2.'" name="print_warehouse_id[]"   class="form-control select" data-live-search="true">';
        
         $report .= $warehouse_list['warehouse_list'];

       $report .= '</select>';
         $report .= '</td>';

         $report .= '</tr>';


     }

     $report .='<tr>';
     $report .='<input type="hidden" name="total_print_item" id="total_print_item" value="'.$rowCount2.'"/>';


           $report .= '<tr><th colspan="8" style="text-align:center;font-size:18px">Material List</th></tr> 
           
           <tr>
                <th>Sl</th>
                <th>Material Name</th>
                <th>Demand Qty </th>
                <th>Total Dispatch</th>
                <th>Dispatch Left </th>
                <th>Stock </th>
                <th>Dispatch Now </th>
                <th>Warehouse</th>

            </tr>';

        $total_qyantity = 0;
        $total_total_receive  = 0;
        $sl =1;
        $count = 0;
        $query = $conn_me->prepare("SELECT sum(`demand_quantity`) AS `total_demand`,`material_id`,`demand_code` FROM `raw_print_item`  where `demand_code` = '".$CODE."' GROUP BY `material_id`  ");
        $query->execute();
        $rowCount =$query->rowCount();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) {
        $count = $count + 1;
        $info_material = SETUP::SETUP_RAW_MATERIAL($fetch['material_id']);
        $dispatch_info = FIND::PRINT_RAW_MATERIAL_DEMAND_DISPATCH($fetch['demand_code'],$fetch['material_id'],'Invoice_Wise');
        $total_demand = $fetch['total_demand'];
        $dispatch_left = $total_demand - $dispatch_info['total_dispatch'];
        $stock = STOCK::RAW_ITEM_WISE_STOCK('',$fetch['material_id'],'product_wise');

        $warehouse_list = FIND::WAREHOUSE_LIST('RAW',$fetch['material_id'],'');

        $report .= '<input value="'.$fetch['total_demand'].'"  type="hidden" name="actual_quantity[]" id="actual_quantity'.$count.'" data-srno="'.$count.'" class="form-control actual_quantity"/> ';
        $report .= '<input value="'.$fetch['material_id'].'"  type="hidden" name="product_id[]" id="product_id'.$count.'" data-srno="'.$count.'" class="form-control product_id"/> ';
        $report .= '<input value="'.$stock['ITEM_STOCK'].'"  type="hidden" name="item_stock[]" id="item_stock'.$count.'" data-srno="'.$count.'" class="form-control item_stock"/> ';
        $report .= '<input value="'.$dispatch_info['total_dispatch'].'"  type="hidden" name="total_dispatch[]" id="total_dispatch'.$count.'" data-srno="'.$count.'" class="form-control total_dispatch"/>';

            $report .= '<tr class="tr'. $count .'">';
            $report .= '<td>'.$sl++.'</td>';
            $report .= '<td>'.$info_material['product_name'].'</td>';
            $report .= '<td>'.$total_demand.'</td>';
            $report .= '<td>'.$dispatch_info['total_dispatch'].'</td>';
            $report .= '<td>'.$dispatch_left.'</td>';
            $report .= '<td>'.$stock['ITEM_STOCK'].'</td>';
            $report .= '<td>';
            $report .='<input type="number" name="dispatch_now[]" id="dispatch_now'.$count.'" data-srno="'.$count.'" class="form-control dispatch_now" value="0.00"/> ';
            $report .= '</td>';
            $report .= '<td>';
            $report .= '<select id="warehouse_id'.$count.'" name="warehouse_id[]"   class="form-control select" data-live-search="true">';
            $report .= $warehouse_list['warehouse_list'];

          $report .= '</select>';
            $report .= '</td>';

            $report .= '</tr>';

    $total_qyantity += $dispatch_info['total_dispatch']; 
    $total_total_receive  += $dispatch_left;

        }

        $report .='<tr>
                       
        <input type="hidden" name="total_item" id="total_item" value="'.$rowCount.'"/>
        <th style="text-align:center;color:red" colspan="4" id="action_bar">';
        $report .='<input type="button" onclick="dispatch_raw_material_for_print();"   value="Dispatch Now">';

$report .='</th></tr>';

    $report .='</table>';

    }else if($TYPE == 'Print Receive From Supplier' || $TYPE == 'Print Receive From Factory'){


        $report .= '<table style="width:100%;" class="table datatable_simple">
        <tr><th colspan="6" style="text-align:center;font-size:18px">'.$TYPE.'</th></tr> 
        <tr><th colspan="3" style="text-align:center;font-size:12px">'.$fetch_ck_data['send_to'].' Name: '.$info_supplier_or_factory['name'].' <th colspan="3" style="text-align:center;font-size:12px">Invoice No'.$fetch_ck_data['invoice_no'].'</th></tr> 

         <input type="hidden" id="code"  id="code"  value="'.$CODE.'">
         <input type="hidden" id="supplier_or_factory_id"  id="supplier_or_factory_id"  value="'.$fetch_ck_data['supplier_or_factory_id'].'">
         <input type="hidden" id="send_to"  id="send_to"  value="'.$fetch_ck_data['send_to'].'">
            <tr>
                <th>Sl</th>
                <th>Material Name</th>
                <th> Batch QTY  </th>
                <th> Received QTY  </th>
                <th> Receive Now  </th>
                <th>Warehouse</th>
            </tr>';
            $total_qyantity = 0;
            $total_total_receive  = 0;
            $sl =1;
            $count = 0;
            $query = $conn_me->prepare("SELECT sum(`batch_quantity`) AS `total_batch_qty`,`material_id`,`code` FROM `raw_print`  where `code` = '".$CODE."' GROUP BY `material_id`  ");
            $query->execute();
            $rowCount =$query->rowCount();
            $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($fetch_list as $fetch ) {
                $count = $count + 1;
                $info_material = SETUP::SETUP_RAW_MATERIAL($fetch['material_id']);
                $received_info = FIND::PRINT_RAW_MATERIAL_BATCH_RECEIVE($fetch['code'],$fetch['material_id'],'Invoice_Wise');


            $report .= '<tr class="tr'. $count .'">';
            $report .= '<td>'.$sl++.'</td>';
            $report .= '<td>'.$info_material['product_name'].'</td>';
            $report .= '<td>'.$fetch['total_batch_qty'].'</td>';
            $report .= '<td>'.$received_info['actual_receive'].'</td>';
            $report .= '<td>';
            $report .='<input type="number" name="receive_now[]" id="receive_now'.$count.'" data-srno="'.$count.'" class="form-control receive_now" value="0.00"/> ';


            $report .='<input type="hidden" name="total_batch_qty[]" id="total_batch_qty'.$count.'" data-srno="'.$count.'" class="form-control total_batch_qty" value="'.$fetch['total_batch_qty'].'"/> ';
            $report .='<input type="hidden" name="actual_receive[]" id="actual_receive'.$count.'" data-srno="'.$count.'" class="form-control actual_receive" value="'.$received_info['actual_receive'].'"/> ';
            $report .='<input type="hidden" name="material_id[]" id="material_id'.$count.'" data-srno="'.$count.'" class="form-control material_id" value="'.$fetch['material_id'].'"/> ';

            $report .= '</td>';
            $report .= '<td>';
            $report .= '<select id="warehouse_id'.$count.'" name="warehouse_id[]"   class="form-control select" data-live-search="true">
            <option value="">Select One</option>';
            $qry = $conn_me->prepare("SELECT * FROM `setup_warehouse`  where status = 1");
            $qry->execute();
            $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
            foreach ($fetch_list as $fetch_w) {
    
            $report .= '<option value="'.$fetch_w['id'].'">'.$fetch_w['name'].'</option>';
                } 
    
          $report .= '</select>';
            $report .= '</td>';

            $report .= '</tr>';
            }
            $report .='<tr>
                       
            <input type="hidden" name="total_item" id="total_item" value="'.$rowCount.'"/>
            <th style="text-align:center;color:red" colspan="6" id="action_bar">';
            $report .='<input type="button" onclick="receive_after_print();"   value="Receive Now">';

            $report .='</th></tr>';


        }else if($TYPE == 'Report Send For Print' ){

            $report .= '<table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">

            
       <tr><th colspan="3" style="text-align:center;font-size:18px">'.$TYPE.'</th></tr> ';

       $report .= ' <tr><td colspan="3" style="text-align:center;font-size:12px;">Supplier : '.$info_supplier_or_factory['name'].'<p style="text-align:center;font-size:12px;">Batch No : '.$fetch_ck_data['invoice_no'].' || Note : '.$fetch_ck_data['note'].' ||  Date : '.$fetch_ck_data['date'].' </p></td></tr> ';


       $report .= '<tr><th colspan="3" style="text-align:center"> Item List </th></tr>';

       $report .='<tr>
               <th>Sl</th>
               <th>Item Name</th>
               <th> Batch QTY  </th>
              
           </tr>';
           $total_batch = 0;
           $sl =1;
           $query = $conn_me->prepare("SELECT * FROM `raw_print`  where `code` = '".$CODE."'  ");
           $query->execute();
           $rowCount =$query->rowCount();
           $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
           foreach ($fetch_list as $fetch ) {
               $info_material = SETUP::SETUP_RAW_MATERIAL($fetch['material_id']);
              


           $report .= '<tr>';
           $report .= '<td>'.$sl++.'</td>';
           $report .= '<td>'.$info_material['product_name'].'</td>';
           $report .= '<td>'.$fetch['batch_quantity'].'</td>';
           $report .= '</tr>';
           $total_batch += $fetch['batch_quantity']; 
           }

           $report .= '<tr><th colspan="2">T O T A L</th><th>'.$total_batch.'</th></tr>';
           $report .= '<tr ><th colspan="3" style="text-align:center"> Material List </th></tr>';

           $report .='<tr>
                   <th>Sl</th>
                   <th>Material Name</th>
                   <th>  QTY  </th>
                  
               </tr>';
               $total_qyantity = 0;
               $sl =1;
               $query2 = $conn_me->prepare("SELECT SUM(`demand_quantity`) AS `total_demand` ,`material_id` FROM `raw_print_item`   where `demand_code` = '".$CODE."' group by `material_id`  ");
               $query2->execute();
               $fetch_list2 = $query2->fetchAll(PDO::FETCH_ASSOC);
               foreach ($fetch_list2 as $fetch2 ) {
                   $info_material = SETUP::SETUP_RAW_MATERIAL($fetch2['material_id']);
                 
   
               $report .= '<tr>';
               $report .= '<td>'.$sl++.'</td>';
               $report .= '<td>'.$info_material['product_name'].'</td>';
               $report .= '<td>'.$fetch2['total_demand'].'</td>';
               $report .= '</tr>';
               $total_qyantity +=$fetch2['total_demand'];

               }
               $report .= '<tr><th colspan="2">T O T A L</th><th>'.$total_qyantity.'</th></tr>';

            }else if($TYPE == 'Batch Wise Send For Print' ){

$ck = $conn_me->prepare("SELECT *,group_concat(`invoice_no`) AS `invoiceno`,group_concat(`note`) AS `nots` FROM `raw_print`  where `receipe_wise_demand_code` = '".$CODE."' GROUP BY `send_to`,`supplier_or_factory_id`  ");
$ck->execute();
$ck_count =$ck->rowCount();
$fetch_ck_data = $ck->fetch(PDO::FETCH_ASSOC);
$info_supplier_or_factory = FIND::SUPPLIER_OR_FACTORY($fetch_ck_data['supplier_or_factory_id'],$fetch_ck_data['send_to']);


  $report .= '<table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">

            
       <tr><th colspan="3" style="text-align:center;font-size:18px">'.$TYPE.'</th></tr> ';

       $report .= ' <tr><td colspan="3" style="text-align:center;font-size:12px;">Supplier : '.$info_supplier_or_factory['name'].'<p style="text-align:center;font-size:12px;">Batch No : '.$fetch_ck_data['invoiceno'].' || Note : '.$fetch_ck_data['nots'].' ||  Date : '.$fetch_ck_data['date'].' </p></td></tr> ';


       $report .= '<tr><th colspan="3" style="text-align:center"> Item List </th></tr>';

       $report .='<tr>
               <th>Sl</th>
               <th>Item Name</th>
               <th> Batch QTY  </th>
              
           </tr>';
           $total_batch = 0;
           $sl =1;
           $raw_print_ids = '';
           $query = $conn_me->prepare("SELECT * FROM `raw_print`  where `receipe_wise_demand_code` = '".$CODE."'  ");
           $query->execute();
           $rowCount =$query->rowCount();
           $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
           foreach ($fetch_list as $fetch ) {
               $info_material = SETUP::SETUP_RAW_MATERIAL($fetch['material_id']);
               $raw_print_ids .= "'$fetch[id]',";


           $report .= '<tr>';
           $report .= '<td>'.$sl++.'</td>';
           $report .= '<td>'.$info_material['product_name'].'</td>';
           $report .= '<td>'.$fetch['batch_quantity'].'</td>';
           $report .= '</tr>';
           $total_batch += $fetch['batch_quantity']; 
           }

           $report .= '<tr><th colspan="2">T O T A L</th><th>'.$total_batch.'</th></tr>';
           $report .= '<tr ><th colspan="3" style="text-align:center"> Material List </th></tr>';

           $report .='<tr>
                   <th>Sl</th>
                   <th>Material Name</th>
                   <th>  QTY  </th>
                  
               </tr>';
               $total_qyantity = 0;
               $sl =1;
               $raw_print_ids  = trim($raw_print_ids,",");
               $query2 = $conn_me->prepare("SELECT  *  FROM `raw_print_item`   where `raw_print_id` IN ($raw_print_ids)  ");
               $query2->execute();
               $fetch_list2 = $query2->fetchAll(PDO::FETCH_ASSOC);
               foreach ($fetch_list2 as $fetch2 ) {
                   $info_material = SETUP::SETUP_RAW_MATERIAL($fetch2['material_id']);
                 
   
               $report .= '<tr>';
               $report .= '<td>'.$sl++.'</td>';
               $report .= '<td>'.$info_material['product_name'].'</td>';
               $report .= '<td>'.$fetch2['demand_quantity'].'</td>';
               $report .= '</tr>';
               $total_qyantity +=$fetch2['demand_quantity'];

               }
               $report .= '<tr><th colspan="2">T O T A L</th><th>'.$total_qyantity.'</th></tr>';
               

     }else{

        $report .= '';

     }

     



}


}else if ($WORK == 'receipe_wise_demand'){

    $ck = $conn_me->prepare("SELECT * FROM `raw_request_recipe_wise`  where `code` = '".$CODE."' GROUP BY `send_to`,`send_to_id`  ");
    $ck->execute();
    $ck_count =$ck->rowCount();
    $fetch_ck_data = $ck->fetch(PDO::FETCH_ASSOC);
    $info_supplier_or_factory = FIND::SUPPLIER_OR_FACTORY($fetch_ck_data['send_to_id'],$fetch_ck_data['send_to']);
    
    if($ck_count > 1 ){
        
        $report .= '<b style="color:red;">This invoice contain more then one Supplier or Factory......</b>';
    
    }else{ 

        if($TYPE == 'Pending Receipe wise Demand' ){



            $report .= '<table style="width:100%;" class="table datatable_simple">
            <tr><th colspan="8" style="text-align:center;font-size:18px">'.$TYPE.'</th></tr> 
            <tr><th colspan="4" style="text-align:center;font-size:12px">'.$fetch_ck_data['send_to'].' Name: '.$info_supplier_or_factory['name'].' <th colspan="4" style="text-align:center;font-size:12px">Invoice No'.$fetch_ck_data['invoice_no'].'</th></tr> 
        
             <input type="hidden" id="code"  id="code"  value="'.$CODE.'">
             <input type="hidden" id="send_to_id"  id="send_to_id"  value="'.$fetch_ck_data['send_to_id'].'">
             <input type="hidden" id="send_to"  id="send_to"  value="'.$fetch_ck_data['send_to'].'">
                <tr>
                    <th>Sl</th>
                    <th>Material Name</th>
                    <th>Demand Qty </th>
                    <th>Total Dispatch</th>
                    <th>Dispatch Left </th>
                    <th>Stock </th>
                    <th>Dispatch Now </th>
                    <th>Warehouse</th>
        
                </tr>';
        
            $total_qyantity = 0;
            $total_total_receive  = 0;
            $sl =1;
            $count = 0;
            $query = $conn_me->prepare("SELECT sum(`demand_quantity`) AS `total_demand`,`material_id`,`demand_code` FROM `raw_request_recipe_wise_item`  where `demand_code` = '".$CODE."' AND `send_requisition` = 'Done'  GROUP BY `material_id`   ");
            $query->execute();
            $rowCount =$query->rowCount();
            $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($fetch_list as $fetch ) {
            $count = $count + 1;
            $info_material = SETUP::SETUP_RAW_MATERIAL($fetch['material_id']);
            $dispatch_info = FIND::RECEIPE_WISE_DEMAND_RECEIVE_REJECT($fetch['demand_code'],$fetch['material_id'],'Invoice_Wise');
            $total_demand = $fetch['total_demand'];
            $dispatch_left = $total_demand - $dispatch_info['actual_qty'];
            $stock = STOCK::RAW_ITEM_WISE_STOCK('',$fetch['material_id'],'product_wise');
            $warehouse_list = FIND::WAREHOUSE_LIST('RAW',$fetch['material_id'],'');

        
            $report .= '<input value="'.$fetch['total_demand'].'"  type="hidden" name="actual_quantity[]" id="actual_quantity'.$count.'" data-srno="'.$count.'" class="form-control actual_quantity"/> ';
            $report .= '<input value="'.$fetch['material_id'].'"  type="hidden" name="product_id[]" id="product_id'.$count.'" data-srno="'.$count.'" class="form-control product_id"/> ';
            $report .= '<input value="'.$stock['ITEM_STOCK'].'"  type="hidden" name="item_stock[]" id="item_stock'.$count.'" data-srno="'.$count.'" class="form-control item_stock"/> ';
            $report .= '<input value="'.$dispatch_info['actual_qty'].'"  type="hidden" name="total_dispatch[]" id="total_dispatch'.$count.'" data-srno="'.$count.'" class="form-control total_dispatch"/>';
        
                $report .= '<tr class="tr'. $count .'">';
                $report .= '<td>'.$sl++.'</td>';
                $report .= '<td>'.$info_material['product_name'].'</td>';
                $report .= '<td>'.$total_demand.'</td>';
                $report .= '<td>'.$dispatch_info['actual_qty'].'</td>';
                $report .= '<td>'.$dispatch_left.'</td>';
                $report .= '<td>'.$stock['ITEM_STOCK'].'</td>';
                $report .= '<td>';
                $report .='<input type="number" name="dispatch_now[]" id="dispatch_now'.$count.'" data-srno="'.$count.'" class="form-control dispatch_now" value="0.00"/> ';
                $report .= '</td>';
                $report .= '<td>';
                $report .= '<select id="warehouse_id'.$count.'" name="warehouse_id[]"   class="form-control select" data-live-search="true">';
                
                $report .= $warehouse_list['warehouse_list'];

              $report .= '</select>';
                $report .= '</td>';
        
                $report .= '</tr>';
        
        $total_qyantity += $dispatch_info['total_dispatch']; 
        $total_total_receive  += $dispatch_left;
        
            }
        
            $report .='<tr>
                           
            <input type="hidden" name="total_item" id="total_item" value="'.$rowCount.'"/>
            <th style="text-align:center;color:red" colspan="4" id="action_bar">';
            $report .='<input type="button" onclick="dispatch_receipe_wise_item_demand();"   value="Dispatch Now">';
        
        $report .='</th></tr>';
        
        $report .='</table>';

    }else if($TYPE == 'Batch Receive ::: From Supplier' || $TYPE == 'Batch Receive ::: From Factory'){


        $report .='        
    
        <div class="panel-body tab-content">
            <div class="tab-pane active" id="tab-first">';
               


    $report .= '<table style="width:100%;" class="table datatable_simple" >
    <tr><th colspan="5" style="text-align:center;font-size:18px">'.$TYPE.'</th></tr> 
    <tr><th colspan="5" style="text-align:center;font-size:12px">'.$fetch_ck_data['send_to'].' Name: '.$info_supplier_or_factory['name'].' Invoice No'.$fetch_ck_data['invoice_no'].'</th></tr> 


        <tr>
            <th>Sl</th>
            <th>Item Name</th>
            <th> Batch QTY  </th>
            <th> Received QTY  </th>
            <th> Left  </th>

            </tr>';
        $total_qyantity = 0;
        $total_total_receive  = 0;
        $sl =1;
        $count = 0;
        $query = $conn_me->prepare("SELECT sum(`batch_quantity`) AS `total_batch_qty`,`product_id`,`code` FROM `raw_request_recipe_wise`  where `code` = '".$CODE."' GROUP BY `product_id`  ");
        $query->execute();
        $rowCount =$query->rowCount();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) {
            $count = $count + 1;
            $info_material = SETUP::SETUP_PRODUCT($fetch['product_id']);
            $received_info = FIND::FG_BATCH_WISE_RECEIVE_REJECT($fetch['code'],$fetch['product_id'],'Invoice_Wise');
            $dispatch_left = $fetch['total_batch_qty'] - $received_info['actual_receive'];

        $report .= '<tr class="tr'. $count .'">';
        $report .= '<td>'.$sl++.'</td>';
        $report .= '<td>'.$info_material['product_code'].' '.$info_material['product_name'].'</td>';
        $report .= '<td>'.$fetch['total_batch_qty'].'</td>';
        $report .= '<td>'.$received_info['actual_receive'].'</td>';
        $report .= '<td>'.$dispatch_left.'</td>';
        $report .= '<td>';
 
        $report .= '</td>';

        $report .= '</tr>';
        }

          
        $report .='</table>';
        $report .='</div>';



        $report .='</div>';
        
        
    }else if($TYPE == 'Batch Fitting' ){

        

        

        $report .='  
        <div class="panel-body tab-content">';
    



            $info_batch_receive = FIND::FG_BATCH_WISE_RECEIVE_REJECT($CODE,'','Only_Invoice_Wise');


            if($info_batch_receive['total_receive'] > 0 ){

                $report .='<div class="table-responsive">';

            $report .= '<input type="hidden" id="code" value="'.$CODE.'" ><table  class="table table-hover table-condensed" style="white-space:nowrap;"  id="invoice-item-table">';

            $report .= '<tr>
                <th>Emp. Name</th>
                <th> Others </th>';
        $column_no = 0;
        $qry2 = $conn_me->prepare("SELECT * FROM `history_batch_wise_fg_receive` where `code` = '".$CODE."' GROUP BY  `product_id` ");
        $qry2->execute();
        $fetch_list2 = $qry2->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list2 as $fetch2) {
      
        $column_no = $column_no+1;

        $info_product = SETUP::SETUP_PRODUCT($fetch2['product_id']);
        $report .='<th>'.$info_product['product_name'].'<input type="text" style="display:none" value="'.$fetch2['product_id'].'" id="get_product_id'.$column_no.'" name="get_product_id" ></th>';

                    }
        $report .='<input type="text"  style="display:none" value="'.$column_no.'" id="column_no" name="" >';

         $report .=' </tr>';

         $report .= '<tr>
         <th colspan="2"><span class="help-block">Fitting Left</span> </th>';

         $column_no2 = 0;
         foreach ($fetch_list2 as $fetch2) {
            $column_no2 = $column_no2+1;
            $info_fitting_done = FIND::FG_BATCH_WISE_FITTING($CODE,$fetch2['product_id'],'Invoice_Wise');
            $info_fitting_receive = FIND::FG_BATCH_WISE_RECEIVE_REJECT($CODE,$fetch2['product_id'],'Invoice_Wise');
            $fitting_left = $info_fitting_receive['actual_receive'] - $info_fitting_done['total_fitting'] ;

            $report .='<th><span class="help-block">'. $fitting_left. ' ' .$info_product['unit'].'</span> <input type="text"   style="display:none" value="'.$fitting_left.'" id="get_ftting_left'.$column_no2.'" name="get_ftting_left" ></th>';

        }
        $report .=' </tr>';
        $count22 = 0;
        $real_row = 0;
        $qry22 = $conn_me->prepare("SELECT *,group_concat(`product_id`) AS `productList` FROM `history_batch_wise_fg_fitting` where `raw_request_recipe_wise_code` = '".$CODE."' group by `emloyee_id` ");
        $qry22->execute();
        $fetch_list22 = $qry22->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list22 as $fetch22) {
            $real_row++;
            $info_em = SETUP::SETUP_EMPLOYEEY($fetch22['emloyee_id']);
            $p_count =  $count22+1;
        $report .= '<tr  class="tr'.$p_count.'" id="row_id_'.$p_count.'">';
        $report .= '<td>'. $info_em['name'].'</td>';
        $report .= '<td>'. $fetch22['note'].'</td>';
        $aa= explode(",",$fetch22['productList']);
        $bb = count($aa);
        for ($x = 0; $x < $bb; $x++) {
          
            $placement=$x+1;

    $qryXX = $conn_me->prepare("SELECT *  FROM `history_batch_wise_fg_fitting` where `emloyee_id` = '".$fetch22['emloyee_id']."' AND  `product_id` = '".$aa[$x]."'  ");
    $qryXX->execute();
    $fetch_listX = $qryXX->fetch(PDO::FETCH_ASSOC);

    $report .= '<input type="hidden" name="employee_id[]" id="employee_id'.$real_row.'_'.$placement.'" data-srno="'.$real_row.'" value="OLD" class="form-control number_only employee_id'.$real_row.'_'.$placement.'"/>
    
    <input type="hidden" name="product_id[]" id="product_id'.$real_row.'_'.$placement.'" data-srno="'.$real_row.'" value="OLD" class="form-control number_only product_id'.$real_row.'_'.$placement.'"/>';


    $report .= '<td><input type="number" READONLY name="done_qty[]" id="done_qty'.$real_row.'_'.$placement.'" data-srno="'.$real_row.'" value="'. $fetch_listX['fitting_quantity'].'" class="form-control number_only done_qty text-danger '.$real_row.'_'.$placement.'"/></td>';
        
    }
        $report .=' </tr>';
        }

         $report .='<input type="number"  style="display:none" value="'.$real_row.'" id="row_count" name="" >';

         $report .='</table>';
         $report .='<input type="button" id="add_row" value="Add Row" onclick="addField();" class="btn btn-info pull-left">';
         $report .='</div>';
         $report .='<input type="button" id="final_track" value="Save Data" onclick="saveFittingData();" class="btn btn-danger pull-right">';


            }else{

                $report .='No Batch Received to fitting';


            }
            $report .='</div>';


            




        }else if($TYPE == 'Batch Receive:: From Supplier' || $TYPE == 'Batch Receive:: From Factory' ){

        

            $report .='        
           
            <div class="panel-body tab-content">';


            $report .= '<table style="width:100%;" class="table datatable_simple" >
            <tr><th colspan="7" style="text-align:center;font-size:18px">'.$TYPE.'</th></tr> 
            <tr><th colspan="4" style="text-align:center;font-size:12px">'.$fetch_ck_data['send_to'].' Name: '.$info_supplier_or_factory['name'].' <th colspan="3" style="text-align:center;font-size:12px">Invoice No'.$fetch_ck_data['invoice_no'].'</th></tr> 
    
             <input type="hidden" id="code"  id="code"  value="'.$CODE.'">
             <input type="hidden" id="send_to_id"  id="send_to_id"  value="'.$fetch_ck_data['send_to_id'].'">
             <input type="hidden" id="send_to"  id="send_to"  value="'.$fetch_ck_data['send_to'].'">
                <tr>
                    <th>Sl</th>
                    <th>Item Name</th>
                    <th> Batch QTY  </th>
                    <th> Received QTY  </th>
                    <th> Left  </th>
                    <th> Receive Now  </th>
                    <th>Warehouse</th>
                </tr>';
                $total_qyantity = 0;
                $total_total_receive  = 0;
                $sl =1;
                $count = 0;
                $query = $conn_me->prepare("SELECT sum(`batch_quantity`) AS `total_batch_qty`,`product_id`,`code` FROM `raw_request_recipe_wise`  where `code` = '".$CODE."' GROUP BY `product_id`  ");
                $query->execute();
                $rowCount =$query->rowCount();
                $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
                foreach ($fetch_list as $fetch ) {
                    $count = $count + 1;
                    $info_material = SETUP::SETUP_PRODUCT($fetch['product_id']);
                    $received_info = FIND::FG_BATCH_WISE_RECEIVE_REJECT($fetch['code'],$fetch['product_id'],'Invoice_Wise');
                    $dispatch_left = $fetch['total_batch_qty'] - $received_info['actual_receive'];
    
                $report .= '<tr class="tr'. $count .'">';
                $report .= '<td>'.$sl++.'</td>';
                $report .= '<td>'.$info_material['product_code'].' '.$info_material['product_name'].'</td>';
                $report .= '<td>'.$fetch['total_batch_qty'].'</td>';
                $report .= '<td>'.$received_info['actual_receive'].'</td>';
                $report .= '<td>'.$dispatch_left.'</td>';
                $report .= '<td>';
                $report .='<input type="number" name="receive_now[]" id="receive_now'.$count.'" data-srno="'.$count.'" class="form-control receive_now" value="0.00"/> ';
    
    
                $report .='<input type="hidden" name="total_batch_qty[]" id="total_batch_qty'.$count.'" data-srno="'.$count.'" class="form-control total_batch_qty" value="'.$fetch['total_batch_qty'].'"/> ';
                $report .='<input type="hidden" name="actual_receive[]" id="actual_receive'.$count.'" data-srno="'.$count.'" class="form-control actual_receive" value="'.$received_info['actual_receive'].'"/> ';
                $report .='<input type="hidden" name="product_id[]" id="product_id'.$count.'" data-srno="'.$count.'" class="form-control product_id" value="'.$fetch['product_id'].'"/> ';
    
                $report .= '</td>';
                $report .= '<td>';
                $report .= '<select id="warehouse_id'.$count.'" name="warehouse_id[]"   class="form-control select" data-live-search="true">
                <option value="">Select One</option>';
                $qry = $conn_me->prepare("SELECT * FROM `setup_warehouse` where status = 1 ");
                $qry->execute();
                $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
                foreach ($fetch_list as $fetch_w) {
        
                $report .= '<option value="'.$fetch_w['id'].'">'.$fetch_w['name'].'</option>';
                    } 
        
              $report .= '</select>';
                $report .= '</td>';
    
                $report .= '</tr>';
                }
                $report .='<tr>
                           
                <input type="hidden" name="total_item" id="total_item" value="'.$rowCount.'"/>
                <th style="text-align:center;color:red" colspan="6" id="action_bar">';
                $report .='<input type="button" onclick="receive_after_batch_done();"   value="Receive Now">';
    
                $report .='</th></tr>';
                  
                $report .='</table>';
                

                $report .='</div>';

              
      



        }else if($TYPE == 'Report Recipe Wise Requisition' ){



            $report .= '<table style="width:100%;" class="table datatable_simple">
            <tr><th colspan="8" style="text-align:center;font-size:18px">'.$TYPE.'</th></tr> 
            <tr><th colspan="4" style="text-align:center;font-size:12px">'.$fetch_ck_data['send_to'].' Name: '.$info_supplier_or_factory['name'].' <th colspan="4" style="text-align:center;font-size:12px">Invoice No'.$fetch_ck_data['invoice_no'].'</th></tr> 

                <tr>
                    <th>Sl</th>
                    <th>Material Name</th>
                    <th>Demand Qty </th>
                    <th>Total Dispatch</th>
                    <th>Dispatch Left </th>
                  
        
                </tr>';
        
            $total_qyantity = 0;
            $total_total_receive  = 0;
            $sl =1;
            $count = 0;
            $query = $conn_me->prepare("SELECT sum(`demand_quantity`) AS `total_demand`,`material_id`,`demand_code` FROM `raw_request_recipe_wise_item`  where `demand_code` = '".$CODE."' AND `send_requisition` = 'Done'  GROUP BY `material_id`   ");
            $query->execute();
            $rowCount =$query->rowCount();
            $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($fetch_list as $fetch ) {
            $count = $count + 1;
            $info_material = SETUP::SETUP_RAW_MATERIAL($fetch['material_id']);
            $dispatch_info = FIND::RECEIPE_WISE_DEMAND_RECEIVE_REJECT($fetch['demand_code'],$fetch['material_id'],'Invoice_Wise');
            $total_demand = $fetch['total_demand'];
            $dispatch_left = $total_demand - $dispatch_info['actual_qty'];
            $stock = STOCK::RAW_ITEM_WISE_STOCK('',$fetch['material_id'],'product_wise');

    
                $report .= '<tr>';
                $report .= '<td>'.$sl++.'</td>';
                $report .= '<td>'.$info_material['material_code'].' '.$info_material['product_name'].'</td>';
                $report .= '<td>'.$total_demand.'</td>';
                $report .= '<td>'.$dispatch_info['actual_qty'].'</td>';
                $report .= '<td>'.$dispatch_left.'</td>';
   
        
                $report .= '</tr>';
        
        $total_qyantity += $dispatch_info['total_dispatch']; 
        $total_total_receive  += $dispatch_left;
        
            }
        

        
        $report .='</table>';

        }else{

            $report .='';
    
        }


    }


    
    
}else{


    
}

                

            return array(

                'report' =>  $header_content . $report
        
        
                );


        }

        static  function PREINVOICE($WORK,$TYPE,$CODE,$FROM,$TO){

            $conn_me = Database::getInstance();


            $info_sales = SETUP::SETUP_PRE_INVOICE($CODE);
            $company_info = SETUP::SETUP_COMPANY('Active');

            

            $header_content ='<div class="table-responsive"><table style="width:100%;" class="table datatable_simple">';
 
            $header_content .='<tr><th style="text-align:center"><img style="height:70px" src="upload/Company_Logo/'.$company_info['company_logo'].'"></th></tr>';

            $header_content .='</table></div>';

            $report = '';

            $report .= '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
            <tr><th colspan="6" style="text-align:center;font-size:18px;color:red">'.$TYPE.'</th></tr> ';
            
            $report .= ' <tr><td colspan="6" style="text-align:center;font-size:12px;">Customer : '.$info_sales['shop_name'].'<p style="text-align:center;font-size:12px;">Invoice No : '.$info_sales['invoice_no'].' ||  Date : '.$info_sales['invoice_date'].' </p></td></tr> ';
    
            $report .= ' <tr><td colspan="6" style="text-align:center;font-size:12px;"><p style="text-align:center;font-size:12px;">Sales By : '.$info_sales['sales_by_name'].' ||  Sales Person : '.$info_sales['sales_person_name'].' </p></td></tr></table></div>';
            
            $report .= '<input type="hidden" id="code" value="'.$CODE.'">';
            $report .= '<input type="hidden" id="main_invoice_id" value="'.$info_sales['id'].'">';
            $report .= '<input type="hidden" id="customer_id" value="'.$info_sales['customer_id'].'">';


            if($WORK == 'preinvoice' ){

                 if($TYPE == 'Pending Pre Order Invoice'){

                    $get_employeeid = SETUP::ADMIN_SETUP($_SESSION['NEWERP_SESS_MEMBER_ID']);

                


                

                    $report .= '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">';
                    $report .= '<tr>
                    <td >My Branch</td>
                    <td colspan="2"><input type="text" READONLY value="'.$get_employeeid['brunch_name'].'"  class="form-control text-danger">
                    </td>
                    
                    <td >Warehouse Branch</td>
                    <td colspan="2">
                    
                    <select class="form-control select" id="dispatch_from_which_brunch" name = "dispatch_from_which_brunch" onchange="WarehouseWiseProductStockInPreinvoice()" >';

                    $qry = $conn_me->prepare("SELECT * FROM `setup_brunch`  where  status = 'Active'  ");
                    $qry->execute();
                    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($fetch_list as $fetch) {
                    $report .= ' <option  value="'.$fetch['id'].'">'.$fetch['brunch'].'</option>';
                } 
                $report .= ' </select>
                    </td>
                    
                    </tr>';
                    $report .= ' <tr>  <td colspan="6"></td></tr>';
                    $report .= ' <tr>  <td colspan="6"></td></tr>';


                    $report .= ' <tr>  <td colspan="6"> </td></tr>';

                    $report .= ' <tr>

                        <th> Sl</th>
                        <th>Product Name</th>
                        <th>Qty</th>
                        <th>Stock <b id="bName" class="text-danger">['.$get_employeeid['brunch_name'].']</b></th>
                        <th>Recommended Price</th>
                        <th>Action  ( Select All <input type="checkbox" id="select-all" onclick="toggleCheckboxes()"> ) </th>


                    </tr>';

                    $sl=1;
                    $count = 0;
                    $query = $conn_me->prepare("SELECT * FROM `pre_order_invoice_item`  where `converat_to_invoice` = 'Pending' AND  `code` = '".$CODE."'   ");
                    $query->execute();
                    $rowCount =$query->rowCount();

                    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($fetch_list as $fetch ) {
                        $count = $count + 1;

                        $CART_DATA = SETUP::SETUP_PRODUCT($fetch['product_id']);
                        $stock = STOCK::FG_ITEM_WISE_STOCK($get_employeeid['brunch_id'],$fetch['product_id'],'unique_brunch_wise');

                        $report .='<input value="'.$fetch['id'].'"  type="hidden" name="pre_order_item_id" id="pre_order_item_id'.$count.'" data-srno="'.$count.'" class="form-control pre_order_item_id"/>';    

                        $report .='<input value="'.$fetch['product_id'].'"  type="hidden" name="product_id" id="product_id'.$count.'" data-srno="'.$count.'" class="form-control product_id"/>'; 

                        $report .='<input value="'.number_format((float)$stock['ITEM_STOCK'], 2, '.', '').'"  type="hidden" name="total_stock" id="total_stock'.$count.'" data-srno="'.$count.'" class="form-control total_stock"/>'; 

                       



                    $report .= ' <tr  id="tr_no'. $count .'">';
                        $report .= '<td>'.$count.'</td>';
                        $report .= '<td>'.$CART_DATA['product_name'].'</td>';
                       
                        $report .= ' <td><input value="'.number_format((float)$fetch['quantity'], 2, '.', '').'"  type="number" name="total_demand" id="total_demand'.$count.'" data-srno="'.$count.'" class="form-control total_demand"/></td>';
                        $report .= ' <td>'.number_format((float)$stock['ITEM_STOCK'], 2, '.', '').'</td>';
                        $report .= '<td><input value="'.$fetch['recommended_price'].'"  type="number" name="recommended_price" id="recommended_price'.$count.'" data-srno="'.$count.'" class="form-control recommended_price"/></td>';
                        if($fetch['quantity'] > $stock['ITEM_STOCK']){
                            $report .='<td>Stock Not Enough</td>';
                        }else{
                            $report .='<td> <label class="check"><input name="checkvalues[]" type="checkbox" class="icheckbox" value="'.$count.'" /></label></td>';
                        }
    
                    
                   

                        $report .= '</tr>';
                    
                    }

                    $report .='<tr>
                       
                    <input type="hidden" name="total_item" id="total_item" value="'.$rowCount.'"/>

                    <th style="text-align:center;color:red" colspan="6" id="action_bar">';
                    $report .='<input type="button" onclick="convertPreOrderInvoice();" id="action_bar"   value="Convert To Invoice">';
        
                    $report .='</th></tr></table></div>';


                }else{
                    $report .='No Report';
                }


            }

            return array(

                'report' =>  $header_content . $report
        
        
                );



        }


 


        static   function QUOTATION($WORK,$TYPE,$CODE,$FROM,$TO){

            $conn_me = Database::getInstance();


            $info_sales = SETUP::SETUP_QUOTATION($CODE);
            $company_info = SETUP::SETUP_COMPANY('Active');

            

            $header_content ='<div class="table-responsive"><table style="width:100%;" class="table datatable_simple">';
 
            $header_content .='<tr><th style="text-align:center"><img style="height:70px" src="upload/Company_Logo/'.$company_info['company_logo'].'"></th></tr>';

            $header_content .='</table></div>';

            $report = '';

            $report .= '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
            <tr><th colspan="6" style="text-align:center;font-size:18px;color:red">'.$TYPE.'</th></tr> ';
            
            $report .= ' <tr><td colspan="6" style="text-align:center;font-size:12px;">Customer : '.$info_sales['shop_name'].'<p style="text-align:center;font-size:12px;">Invoice No : '.$info_sales['invoice_no'].' ||  Date : '.$info_sales['invoice_date'].' </p></td></tr> ';
    
            $report .= ' <tr><td colspan="6" style="text-align:center;font-size:12px;"><p style="text-align:center;font-size:12px;">Sales By : '.$info_sales['sales_by_name'].' ||  Sales Person : '.$info_sales['sales_person_name'].' </p></td></tr></table></div>';
            
            $report .= '<input type="hidden" id="code" value="'.$CODE.'">';
            $report .= '<input type="hidden" id="main_invoice_id" value="'.$info_sales['id'].'">';
            $report .= '<input type="hidden" id="customer_id" value="'.$info_sales['customer_id'].'">';


            if($WORK == 'quotation' ){

                 if($TYPE == 'Pending Quotation'){

                    $get_employeeid = SETUP::ADMIN_SETUP($_SESSION['NEWERP_SESS_MEMBER_ID']);


                    $report .= '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">';
                    $report .= '<tr>
                    <td >My Branch</td>
                    <td colspan="2"><input type="text" READONLY value="'.$get_employeeid['brunch_name'].'"  class="form-control text-danger">
                    </td>
                    
                    <td >Warehouse Branch</td>
                    <td colspan="2">
                    
                    <select class="form-control select" id="dispatch_from_which_brunch" name = "dispatch_from_which_brunch" onchange="WarehouseWiseProductStockInQuatation()" >';

                    $qry = $conn_me->prepare("SELECT * FROM `setup_brunch` where  status = 'Active' ");
                    $qry->execute();
                    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($fetch_list as $fetch) {
                    $report .= ' <option  value="'.$fetch['id'].'">'.$fetch['brunch'].'</option>';
                } 
                $report .= ' </select>
                    </td>
                    
                    </tr>';
                    $report .= ' <tr>  <td colspan="6"></td></tr>';
                    $report .= ' <tr>  <td colspan="6"></td></tr>';


                    $report .= ' <tr>  <td colspan="6"> </td></tr>';

                    $report .= ' <tr>

                    <th> Sl</th>
                    <th>Product Name</th>
                    <th>Qty</th>
                    <th>Stock <b id="bName" class="text-danger">['.$get_employeeid['brunch_name'].']</b></th>
                    <th>Recommended Price</th>
                    <th>Action  ( Select All <input type="checkbox" id="select-all" onclick="toggleCheckboxes()"> ) </th>


                </tr>';

                    $sl=1;
                    $count = 0;
                    $query = $conn_me->prepare("SELECT * FROM `quotation_invoice_item`  where `converat_to_invoice` = 'Pending' AND  `code` = '".$CODE."'   ");
                    $query->execute();
                    $rowCount =$query->rowCount();

                    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($fetch_list as $fetch ) {
                        $count = $count + 1;

                        $CART_DATA = SETUP::SETUP_PRODUCT($fetch['product_id']);
                        $stock = STOCK::FG_ITEM_WISE_STOCK('',$fetch['product_id'],'product_wise');

                        $report .='<input value="'.$fetch['id'].'"  type="hidden" name="quotation_id" id="quotation_id'.$count.'" data-srno="'.$count.'" class="form-control quotation_id"/>';    

                        $report .='<input value="'.$fetch['product_id'].'"  type="hidden" name="product_id" id="product_id'.$count.'" data-srno="'.$count.'" class="form-control product_id"/>'; 

                        $report .='<input value="'.number_format((float)$stock['ITEM_STOCK'], 2, '.', '').'"  type="hidden" name="total_stock" id="total_stock'.$count.'" data-srno="'.$count.'" class="form-control total_stock"/>'; 



                        $report .= ' <tr  id="tr_no'. $count .'">';
                        $report .= '<td>'.$count.'</td>';
                        $report .= '<td>'.$CART_DATA['product_name'].'</td>';
                       
                        $report .= ' <td><input value="'.number_format((float)$fetch['quantity'], 2, '.', '').'"  type="number" name="total_demand" id="total_demand'.$count.'" data-srno="'.$count.'" class="form-control total_demand"/></td>';

                        $report .= ' <td>'.number_format((float)$stock['ITEM_STOCK'], 2, '.', '').'</td>';

                        $report .= '<td><input value="'.$fetch['recommended_price'].'"  type="number" name="recommended_price" id="recommended_price'.$count.'" data-srno="'.$count.'" class="form-control recommended_price"/></td>';
                        
                        if($fetch['quantity'] > $stock['ITEM_STOCK']){
                            $report .='<td>Stock Not Enough</td>';
                        }else{
                            $report .='<td> <label class="check"><input name="checkvalues[]" type="checkbox" class="icheckbox" value="'.$count.'" /></label></td>';
                        }
    
                    
                   

                        $report .= '</tr>';
                    
                    }

                    $report .='<tr>
                       
                    <input type="hidden" name="total_item" id="total_item" value="'.$rowCount.'"/>

                    <th style="text-align:center;color:red" colspan="6" id="action_bar">';
                    $report .='<input type="button" onclick="convertQuotation();" id="action_bar"   value="Convert To Invoice">';
        
                    $report .='</th></tr></table></div>';


                }else{
                    $report .='No Report';
                }


            }

            return array(

                'report' =>  $header_content . $report
        
        
                );



        }



        static  function FGTRANSFER($TYPE,$CODE){


            $conn_me = Database::getInstance();


            $query = $conn_me->prepare("SELECT invoice_date,invoice_no,notes FROM `fg_warehouse_to_warehouse_transfer`  where `code` = '".$CODE."'  GROUP BY code ");
            $query->execute();
            $fetch_list = $query->fetch(PDO::FETCH_ASSOC);




            $report = '';

            $report .= '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">';

            $report .= '<tr><th colspan="6" style="text-align:center;font-size:18px;color:red">'.$TYPE.'</th></tr> ';

            $report .= '<tr><th colspan="6" style="text-align:center;font-size:18px;">Invoice Date: '.$fetch_list['invoice_date'].' | Invoice No: '.$fetch_list['invoice_no'].'</th></tr> ';

            $report .= '<tr><th colspan="6" style="text-align:center;font-size:18px;color:red">'.$fetch_list['notes'].'</th></tr> ';

            $report .= ' </table>';

   


            $report .= '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
            <thead>';

            $query2 = $conn_me->prepare("SELECT id FROM `fg_warehouse_to_warehouse_transfer`  where `code` = '".$CODE."'   ");
            $query2->execute();
            if($query2->rowCount() > 0 ){
                $report .= ' <tr> <th colspan="2">Product Name</th><th>Quantity (in pcs)</th><th>Quantity (in Carton)</th><th colspan="2">Transfer From</th><th colspan="2">Transfer to</th> </tr>';
                $fetch_list2 = $query2->fetchAll(PDO::FETCH_ASSOC);
                foreach ($fetch_list2 as $fetch2 ) {
                          
                    $info = SETUP::FG_warehouse_to_warehouse_transfer($fetch2['id']);
                    $report .= ' <tr> <td colspan="2">'.$info['product_name'].'</td><td>'.$info['quantity'].'</td><td>'.$info['in_carton'].'</td><td colspan="2">'.$info['from_warehouse_name'].'</td><td colspan="2">'.$info['to_warehouse_name'].'</td> </tr>';


                }


            }

       

            $report .= '</tbody>
            </table></div>';


$report .= '<div class="table-responsive" style="padding-top:80px;"><table style="width:100%;" class="tabletable-condensed "><thead>';

           
$report .= '<tr style="text-align:center">';

$report .= '<td>....................................................<br>Dispatcher Signature    </td>';

$report .= ' </tr>';
         


            $report .= '<thead></table></div>';



             return array(

                'report' =>   $report
        
        
                );


        }




        static  function SALES($WORK,$TYPE,$CODE,$FROM,$TO){

            
            $conn_me = Database::getInstance();

            $info_sales = SETUP::SETUP_SALES_INVOICE($CODE);
            $company_info = SETUP::SETUP_COMPANY('Active');

            $timeline = SETUP::INVOICE_TIMELINE($info_sales['id']);

            $datetime = $info_sales['invoice_date'] . ' ' . $info_sales['time'];
            $dateTime = DateTime::createFromFormat('d-m-Y h:i:s a', $datetime);

            $godowncopy_time = !empty($timeline['godowncopy_time']) 
            ? 'Godowncopy created ' . $timeline['godowncopy_time'] 
            : 'Godowncopy created ' . $dateTime->modify('+1 hour')->format('d-m-Y h:i:s a');

            $challancopy_time = !empty($timeline['challancopy_time']) 
            ? 'Challan created ' . $timeline['challancopy_time'] 
            : 'Challan created ' . $dateTime->modify('+2 hour')->format('d-m-Y h:i:s a');



            $transport_cost = (!empty($info_sales['transport_cost'])) ? $info_sales['transport_cost'] : 0 ;

            $header_content ='';

            $report = '';

            $report .= '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">';
            

            if($TYPE == 'Challan Copy' || $TYPE == 'Generate Godown Copy' ){
                       
                if(!empty($info_sales['dispatcher_id'])){
                    $report .= '<tr><th colspan="6" style="text-align:center;font-size:18px;color:red">'.$TYPE.'</th></tr> ';

                }else{
                    $report .= '<tr><th colspan="6" style="text-align:center;font-size:18px;color:red">Godown Copy</th></tr> ';
                }
                  
            }else{
                $report .= '<tr><th colspan="6" style="text-align:center;font-size:18px;color:red">'.$TYPE.'</th></tr> ';

            }
            
            $report .= ' <tr><td colspan="6" style="text-align:center;font-size:18px;"><b>Customer : '.$info_sales['shop_name'].' || <b style="background-color: #d4ff32;">Address : '.$info_sales['address'].' </b>
            <p style="text-align:center;font-size:18px;">Invoice No : '.$info_sales['invoice_no'].' ||  Date : '.$info_sales['invoice_date'].' </p></b></td></tr> ';
    
            $report .= ' <tr><td colspan="6" style="text-align:center;font-size:18px;"><p style="text-align:center;font-size:18px;"><b>Brunch : '.$info_sales['BrunchName'].' </b></p></td></tr>';

            if(!empty($info_sales['narration'])){
                $report .= ' <tr><td colspan="6" style="text-align:center;font-size:12px;"><p style="text-align:center;font-size:18px;"><b>Narration : '.$info_sales['narration'].' </b></p></td></tr>';
            }
       

            $report .= ' <tr><td colspan="6" style="text-align:center;font-size:18px;"><p style="text-align:center;font-size:18px;">Sales By : '.$info_sales['sales_by_name'].' ||  Sales Person : '.$info_sales['sales_person_name'].' </p></td></tr></table></div>';
            
            $report .= '<input type="hidden" id="code" value="'.$CODE.'">';
            $report .= '<input type="hidden" id="main_invoice_id" value="'.$info_sales['id'].'">';
            $report .= '<input type="hidden" id="customer_id" value="'.$info_sales['customer_id'].'">';
            $report .= '<input type="hidden" id="invoice_date" value="'.$info_sales['invoice_date'].'">';
            $report .= '<input type="hidden" id="brunch_id" value="'.$info_sales['brunch_id'].'">';
            $report .= '<input type="hidden" id="transport_cost" value="">';




            if($WORK == 'sales' ){


                if($TYPE == 'Challan Copy For Sales Person Approval' ){

             
                    $report .= '<div class="table-responsive"><table style="width:100%;" class="table table-bordered">
                    <thead>
                      <tr>
                        <th class="tg-c3ow" rowspan="2">SL</th>
                        <th class="tg-c3ow" rowspan="2">PRODUCT DETAILS</th>
                        <th class="tg-c3ow" colspan="2" style="text-align:center">QUANTITY</th>
                        <th  colspan="2" style="text-align:center">Actual Dispatch</th>
                      </tr>
                      <tr>
                        <th >PCS</th>
                        <th >CARTON</th>
                        <th >PCS</th>
                        <th >CARTON</th>
                      </tr>
                    </thead>
                    <tbody>';
                    
                    
                    
                                    $sl=1;
                                
                                    $query = $conn_me->prepare("SELECT * FROM `sales_invoice_item`  where `sales_invoice_id` = '".$info_sales['id']."'    ");
                                    $query->execute();
                                    $rowCount =$query->rowCount();
                    
                                    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($fetch_list as $fetch ) {
                                    
                                        $info_product = SETUP::SETUP_PRODUCT($fetch['product_id']);
                                
                                        $demand_qty_carton = $fetch['sales_quantity']/$info_product['pcs_in_cartoon'];
                    
                                        $dispatch_qty_carton = $fetch['sales_quantity']/$info_product['pcs_in_cartoon'];

                                        if($fetch['sales_quantity'] != $fetch['pcs_receive'] ){ $color=  "red";}else{$color= ""; }
                                    
                                        $report .= ' <tr style="color:'.$color.';">
                                        <td >'.$sl++.'</td>
                                        <td >'.$info_product['product_name'].'</td>
                                        <td >'.$fetch['sales_quantity'].'</td>
                                        <td>'.number_format((float)$demand_qty_carton, 2, '.', '').'</td>

                                        <td >'.$fetch['pcs_receive'].'</td>
                                        <td >'.$fetch['carton_receive'].'</td>
                    
                    
                                      </tr>';
                                    
                                    }
                    
                    
                    
                                    $report .= '</tbody>
                                    </table></div>';
                    
                    
                                    $report .='';
                    

                }else if($TYPE == 'Sales Invoice'){

                    $info_customer_due = FIND::CUSTOMER_DUE($info_sales['customer_id']);
                    $obj = new BanglaNumberToWord();


                }else if($TYPE == 'Delivery Challan'){


                
                    $report .= '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
                    <thead>
                      <tr>
                        <th class="tg-c3ow" rowspan="2">SL</th>
                        <th class="tg-c3ow" rowspan="2">PRODUCT DETAILS</th>
                        <th class="tg-c3ow" colspan="2" style="text-align:center">QUANTITY</th>
                      </tr>
                      <tr>
                        <th >CARTON</th>
                        <th >PCS</th>


                      </tr>
                    </thead>
                    <tbody>';
                    
                    
                    
                                    $sl=1;
                                    $total_pcs = 0;
                                    $total_ctn = 0;
                                    $actual_pcs = 0;
                                    $actual_ctn = 0;
                                    $query = $conn_me->prepare("SELECT * FROM `sales_invoice_item`  where `sales_invoice_id` = '".$info_sales['id']."'    ");
                                    $query->execute();
                                    $rowCount =$query->rowCount();
                    
                                    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($fetch_list as $fetch ) {
                                    
                                        $info_product = SETUP::SETUP_PRODUCT($fetch['product_id']);
                                  
  
                
  
$qty_in_carton = (float)($fetch['sales_quantity'] / $info_product['pcs_in_cartoon']);
$qty_in_carton = ($qty_in_carton < 1) ? 0 : $qty_in_carton;

                                    
                $sales_quantity =  round((float)($fetch['sales_quantity']));


                                        

                                        $report .= ' <tr>
                                        <td >'.$sl++.'</td>
                                        <td >'.$info_product['product_name'].'</td>
                                        <td>'.$qty_in_carton.'</td>
                                        
                                        <td >'.$sales_quantity.'</td>

                                      
                    
                                      </tr>';
                                    
                                    $total_pcs += $fetch['sales_quantity'];
                                    $total_ctn += $qty_in_carton;
                  


                                    }

                                    $report .= ' <tr> <td colspan="2">Total</td> <td>'.$total_ctn.'</td><td>'.$total_pcs.'</td></tr>';
                                 
                                
                                    $report .= ' <tr> <td colspan="4"><b>Transport Cost : '.$transport_cost.'</b></td> </tr>';
       

                    
                                    $report .= '</tbody>
                                    </table></div>';
    
            
                    $report .= '<div class="table-responsive" style="padding-top:100px;"><table style="width:100%;" class="tabletable-condensed "><thead>';
                    
                                   
       

                    $report .= '<tr style="text-align:right">';
                    $report .= '<td colspan="2" style="font-size:13px;" >'.$challancopy_time.'</td>';
                    $report .= ' </tr>';

        
                                    
                                    $report .= '<thead></table></div>';


                                    $report .='';


                }else if($TYPE == 'Godown Copy'){

             
                    $report .= '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
                    <thead>
                    <tr><td colspan="8" style="text-align:center">Dispatcher List : '.$info_sales['dispatcher_name'].'</td></tr>
                <tr>
    <th class="tg-c3ow" rowspan="2">SL</th>
    <th class="tg-c3ow" rowspan="2">PRODUCT DETAILS</th>
    <th class="tg-c3ow" colspan="2" style="text-align:center">QUANTITY</th>
    <th rowspan="2">Warehouse List</th>
    <th colspan="3" style="text-align:center">Actual Dispatch</th>
    <th rowspan="2">PRICE</th>
</tr>
<tr>
    <th>CARTON</th>
    <th>PCS</th>
    <th>CARTON</th>
    <th>PCS</th>
    <th></th>
</tr>
                    </thead>
                    <tbody>';
                    
                    
                    
                                    $sl=1;
                                    $total_pcs = 0;
                                    $total_ctn = 0;
                                    $actual_pcs = 0;
                                    $actual_ctn = 0;
                                    $query = $conn_me->prepare("SELECT * FROM `sales_invoice_item`  where `sales_invoice_id` = '".$info_sales['id']."'    ");
                                    $query->execute();
                                    $rowCount =$query->rowCount();
                    
                                    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($fetch_list as $fetch ) {
                                    
                                        $info_product = SETUP::SETUP_PRODUCT($fetch['product_id']);
                                        if(!empty($fetch['warehouse_id'])){
                                            $info_warehouse = SETUP::SETUP_WAREHOUSE($fetch['warehouse_id']);
                                            $warehouse_name = $info_warehouse['name'];
                                        }else{
                                            $warehouse_name = '';
                                        }
                                        if(!empty($fetch['dispatcher_id'])){
                                            $info_dispatcher = SETUP::ADMIN_SETUP($fetch['dispatcher_id']);
                                            $dispatcher_name = $info_dispatcher['hr_name'];
                                        }else{
                                            $dispatcher_name = 'No Dispatcher';
                                        }


  
                
  
$qty_in_carton = (float)($fetch['sales_quantity'] / $info_product['pcs_in_cartoon']);
$qty_in_carton = ($qty_in_carton < 1) ? 0 : $qty_in_carton;

                                    
                $sales_quantity =  round((float)($fetch['sales_quantity']));


                                        

                                       $report .= ' <tr>
<td>'.$sl++.'</td>
<td>'.$info_product['product_name'].'</td>
<td>'.$qty_in_carton.'</td>
<td>'.$sales_quantity.'</td>
<td>'.$warehouse_name.'</td>
<td></td>
<td></td>
<td></td>
<td>'.number_format($fetch['sales_rate'],2).'</td>
</tr>';

                    
                                  
                                    
                                    $total_pcs += $fetch['sales_quantity'];
                                    $total_ctn += $qty_in_carton;
                  


                                    }
                                    



                                    $report .= ' <tr> <td colspan="2">Total</td>
<td>'.$total_ctn.'</td>
<td>'.$total_pcs.'</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td></tr>';
                                    $report .= ' <tr> <td colspan="8"></td> </tr>';
                                    $report .= ' <tr> <td colspan="8"></td> </tr>';
                                    $report .= ' <tr> <td colspan="8 "></td> </tr>';
                                
                                
                                    $report .= ' <tr> <td colspan="2"><b>Transport Cost : '.$transport_cost.'</b></td> <td></td><td></td><td></td><td></td><td></td><td></td></tr>';
                                    $report .= ' <tr> <td colspan="8"></td> </tr>';
                                    $report .= ' <tr> <td colspan="8"></td> </tr>';
                                    $report .= ' <tr> <td colspan="8 "></td> </tr>';

                                    $query2 = $conn_me->prepare("SELECT id FROM `fg_warehouse_to_warehouse_transfer`  where `related_sales_invoice_id` = '".$info_sales['id']."'   ");
                                    $query2->execute();
                                    if($query2->rowCount() > 0 ){

                                        $report .= ' <tr> <td colspan="7" style="text-align:center"><b >Transfer Product</b></td> </tr>';
                                        $report .= ' <tr> <th colspan="2">Product Name</th><th>Quantity (in pcs)</th><th>Quantity (in Carton)</th><th colspan="2">Transfer From</th><th colspan="2">Transfer to</th> </tr>';
                                        $fetch_list2 = $query2->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($fetch_list2 as $fetch2 ) {
                                                  
                                            $info = SETUP::FG_warehouse_to_warehouse_transfer($fetch2['id']);

                                           
                                            $report .= '
                                            <tr>
                                                <td colspan="2">'.$info['product_name'].'</td><td>'.$info['quantity'].'</td>
                                                <td>'.$info['in_carton'].'</td><td colspan="2">'.$info['from_warehouse_name'].'</td>
                                                <td colspan="2">'.$info['to_warehouse_name'].'</td>
                                            </tr>
                                            ';


                                        }


                                    }

                               
                    
                                    $report .= '</tbody>
                                    </table></div>';
    
            
                    $report .= '<div class="table-responsive" style="padding-top:100px;"><table style="width:100%;" class="tabletable-condensed "><thead>';
                    
                                   
                    $report .= '<tr style="text-align:center">';

                    $report .= '<td>....................................................<br>Godown Manager Signature    </td>';

                    $report .= '<td>';

                    for ($i=1; $i <= $info_sales['dispatcher_count'] ; $i++) { 
                        $report .= '....................................... <br>'.$i.'. Dispatcher Signature<br><br><br>';
                    
                    }
                    $report .= '</td>';
                    $report .= ' </tr>';

                    $report .= '<tr style="text-align:right">';
                    $report .= '<td colspan="2" style="font-size:13px;" >'.$godowncopy_time.'</td>';
                    $report .= ' </tr>';

        
                                    
                                    $report .= '<thead></table></div>';


                                    $report .='';
                    
                                }else if($TYPE == 'FG-STORE-DAMAGE-RECEIPT'){

                                    $report = '';    
                                    
                                    $report .= '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">';
            
                           
                                        $report .= '<tr><th colspan="6" style="text-align:center;font-size:18px;color:red">'.$TYPE.'</th></tr> ';
                        
                        
                    
                    $report .= '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
                    <thead>';

                    $query2 = $conn_me->prepare("SELECT id FROM `fg_damage_store`  where `code` = '".$CODE."'   ");
                    $query2->execute();
                    if($query2->rowCount() > 0 ){
                        $report .= ' <tr> <th colspan="2">Product Name</th><th>Quantity (in pcs)</th><th>Quantity (in Carton)</th><th colspan="2">Deleted  From</th></tr>';
                        $fetch_list2 = $query2->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($fetch_list2 as $fetch2 ) {
                                  
                            $info = SETUP::FG_DAMAGE_STORE($fetch2['id']);
                            $report .= ' <tr> <td colspan="2">'.$info['product_name'].'</td><td>'.$info['quantity'].'</td><td>'.$info['in_carton'].'</td><td colspan="2">'.$info['warehouse_name'].'</td> </tr>';


                        }


                    }

               
    
                    $report .= '</tbody>
                    </table></div>';


    $report .= '<div class="table-responsive" style="padding-top:80px;"><table style="width:100%;" class="tabletable-condensed "><thead>';
    
                   
    $report .= '<tr style="text-align:center">';

    $report .= '<td>....................................................<br>Dispatcher Signature    </td>';

    $report .= ' </tr>';
                 


                    $report .= '<thead></table></div>';



        
                }else if($TYPE == 'Pending Sales Invoice'){

                    $report .= '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered"> <tr>

                        <th> Sl</th>
                        <th>Product Name</th>
                        <th>Qty</th>
                        <th>Recommended Price</th>
                        <th>Final Price</th>

                    </tr>';

                    $sl=1;
                    $count = 0;
                    $query = $conn_me->prepare("SELECT * FROM `sales_invoice_item`  where `sales_invoice_id` = '".$info_sales['id']."'    ");
                    $query->execute();
                    $rowCount =$query->rowCount();

                    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($fetch_list as $fetch ) {
                        $count = $count + 1;

                        $CART_DATA = SETUP::SETUP_PRODUCT($fetch['product_id']);
                    
                        if(!empty($fetch['sales_rate'])){
                            $sales_price = $fetch['sales_rate'];
                        }else{
                            $sales_price = $fetch['recommended_price'];
                        }

                        $report .= ' <input value="'.$fetch['id'].'"  type="hidden" name="sales_id[]" id="sales_id'.$count.'" data-srno="'.$count.'" class="form-control sales_id"/>';
                        $report .= '<tr class="tr'. $count .'">';
                        $report .= '<td>'.$sl++.'</td>';
                        $report .= '<td>'.$CART_DATA['product_name'].'</td>';
                        $report .= '<td>'.$fetch['sales_quantity'].'</td>';
                        $report .= '<td>'.$fetch['recommended_price'].'</td>';
                        $report .= '<td><input value="'.$sales_price.'"  type="number" name="sales_price[]" id="sales_price'.$count.'" data-srno="'.$count.'" class="form-control sales_price"/></td>';
                    
                   

                        $report .= '</tr>';
                    
                    }

                    $report .='<tr>
                       
                    <input type="hidden" name="total_item" id="total_item" value="'.$rowCount.'"/>

                    <th style="text-align:center;color:red" colspan="6" id="action_bar">';
                    $report .='<input type="button" onclick="confirmBySalesManager();" id="action_bar"   value="Confirm Now">';
        
                    $report .='</th></tr></table></div>';


                }else if( $TYPE == 'Sales Return'){

             
                    $report .= '<div class="table-responsive"><table style="width:100%;white-space:nowrap;" class="table table-hover table-condensed table-striped table-bordered">
<thead>
  <tr>
    <th class="tg-c3ow" rowspan="2">SL</th>
    <th class="tg-c3ow" rowspan="2">PRODUCT DETAILS</th>
    <th class="tg-c3ow" colspan="2" style="text-align:center">INVOICE QUANTITY</th>
    <th class="tg-c3ow" colspan="3" style="text-align:center">Price</th>
    <th class="tg-c3ow" colspan="2" style="text-align:center">PREVIOUS RETURN</th>
    <th class="tg-c3ow" colspan="2" style="text-align:center"> RETURN NOW</th>
    <th class="tg-c3ow"  rowspan="2">NOTE</th>

  </tr>
  <tr>
    <th >PCS</th>
    <th >CARTON</th>

    <th >Invoice</th>
    <th >Current</th>
    <th >Recommended</th>

    <th >PCS</th>
    <th >CARTON</th>
    <th >PCS</th>
    <th >CARTON</th>
  </tr>

</thead>
<tbody>';



                $sl=1;
                $count = 0;
                $query = $conn_me->prepare("SELECT * FROM `sales_invoice_item`  where `sales_invoice_id` = '".$info_sales['id']."'   ");
                $query->execute();
                $rowCount =$query->rowCount();

                $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
                foreach ($fetch_list as $fetch ) {
                    $count = $count + 1;
                  
                    $info_product = SETUP::SETUP_PRODUCT($fetch['product_id']);
                    $info_previous_return = FIND::INVOCIE_WISE_PREVIOUS_RETURN($fetch['sales_invoice_id'],$fetch['product_id']);
                    $info_previous_damage = FIND::INVOCIE_WISE_PREVIOUS_DAMAGE($fetch['sales_invoice_id'],$fetch['product_id']);


                $product_price = SETUP::getProductPriceOnTransferDate(date("Y-m-d"),$fetch['product_id']);
                $price =  (!empty($product_price)) ? $product_price : $info_product['sales_rate'] ;



                    $damage_qty =$info_previous_damage['count_pcs'];
                    $damage_carton = $info_previous_damage['count_carton'];

                    $previous_return_qty =$info_previous_return['count_pcs'];
                    $previous_return_carton = $info_previous_return['count_carton'];

        
                $report .= ' <input value="'.$fetch['product_id'].'"  type="hidden" name="product_id[]" id="product_id'.$count.'" data-srno="'.$count.'" class="form-control product_id"/>';

                $report .= ' <input value="'.$fetch['sales_quantity'].'"  type="hidden" name="sales_quantity[]" id="sales_quantity'.$count.'" data-srno="'.$count.'" class="form-control sales_quantity"/>';


                $report .= '<input   value="'.$damage_qty.'" type="hidden" name="damage_qty[]" id="damage_qty'.$count.'" data-srno="'.$count.'" class="form-control damage_qty"/>';

                $report .= '<input type="hidden"  value="'.$previous_return_qty.'" name="previous_return_qty[]" id="previous_return_qty'.$count.'" data-srno="'.$count.'" class="form-control previous_return_qty"/>';


                
                    $report .= ' <tr'. $count .'">
                    <td >'.$sl++.'</td>
                    <td >'.$info_product['product_name'].'</td>
                    <td >'.number_format((float)$fetch['sales_quantity'], 2, '.', '').'</td>
                    <td >'.number_format((float)$fetch['carton_receive'], 2, '.', '').'</td>';
                    
                    $report .= '<td >'.number_format((float)$fetch['sales_rate'], 2, '.', '').'</td>';

                    $report .= '<td >'.number_format((float)$price, 2, '.', '').'</td>';

                   $report .= '<td ><input type="number"  value="'.$price.'"  name="sales_rate[]" id="sales_rate'.$count.'" data-srno="'.$count.'" class="form-control sales_rate"/></td >';




                    $report .= '<td >'.number_format((float)$previous_return_qty, 2, '.', '').'</td>';
                    $report .= '<td >'.number_format((float)$previous_return_carton, 2, '.', '').'</td>';


         
                    $report .= '  <td><input value="0.00"  onkeyup="calculation_of_two_number(\'DIVISION\',\'return_qty'.$count.'\',\'carton'.$count.'\',\'receive_sales_carton'.$count.'\')"  type="number" name="return_qty[]" id="return_qty'.$count.'" data-srno="'.$count.'" class="form-control sales_quantity"/></td>';

                    $report .= '  <td><input value="0.00"  onkeyup="calculation_of_two_number(\'DIVISION\',\'return_qty'.$count.'\',\'carton'.$count.'\',\'receive_sales_carton'.$count.'\')"  type="number" name="return_carton[]" id="return_carton'.$count.'" data-srno="'.$count.'" class="form-control sales_quantity"/></td>';

                    $report .= ' <td><input value=""  type="text" name="note[]" id="note'.$count.'" data-srno="'.$count.'" class="form-control note"/></td>
                    <td style="display:none"> 
                   

                    </td>

                  </tr>';
                
                }

                $report .='<tr>
                       
                <input type="hidden" name="total_item" id="total_item" value="'.$rowCount.'"/>
                <th style="text-align:center;color:red" colspan="10" id="action_bar">';
                $report .='<input type="button" onclick="ReturnProduct();" id="action_bar"   value="Return Product">';
    
                $report .='</th></tr>';


                $report .= '</tbody>
                </table></div>';


                $report .='';

                }else if( $TYPE == 'Damage Return'){

                    

                   

             
                    $report .= '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
<thead>
  <tr>
  <th class="tg-c3ow" rowspan="2">SL</th>
  <th class="tg-c3ow" rowspan="2">PRODUCT DETAILS</th>
  <th class="tg-c3ow" colspan="2" style="text-align:center">INVOICE QUANTITY</th>
  <th class="tg-c3ow" colspan="2" style="text-align:center">RETURN QUANTITY</th>
  <th class="tg-c3ow" colspan="2" style="text-align:center">PREVIOUS DAMAGE</th>
  <th class="tg-c3ow" colspan="2" style="text-align:center"> DAMAGE NOW</th>
  <th class="tg-c3ow"  rowspan="2">NOTE</th>

  </tr>
  <tr>
  <th >PCS</th>
  <th >CARTON</th>
  <th >PCS</th>
  <th >CARTON</th>
  <th >PCS</th>
  <th >CARTON</th>
  <th >PCS</th>
  <th >CARTON</th>
  </tr>

</thead>
<tbody>';


$sl=1;
                $count = 0;
                $query = $conn_me->prepare("SELECT * FROM `sales_invoice_item`  where `sales_invoice_id` = '".$info_sales['id']."'   ");
                $query->execute();
                $rowCount =$query->rowCount();

                $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
                foreach ($fetch_list as $fetch ) {
                    $count = $count + 1;
                  
                    $info_product = SETUP::SETUP_PRODUCT($fetch['product_id']);
                    $info_previous_damage = FIND::INVOCIE_WISE_PREVIOUS_DAMAGE($fetch['id'],$fetch['product_id']);
                    $info_previous_return = FIND::INVOCIE_WISE_PREVIOUS_RETURN($fetch['id'],$fetch['product_id']);

                    $return_qty =$info_previous_return['count_pcs'];
                    $return_carton = $info_previous_return['count_carton'];

                    $previous_damage_qty =$info_previous_damage['count_pcs'];
                    $previous_damage_carton = $info_previous_damage['count_carton'];

                    
                $report .= ' <input value="'.$fetch['product_id'].'"  type="hidden" name="product_id[]" id="product_id'.$count.'" data-srno="'.$count.'" class="form-control product_id"/>';

                $report .= ' <input value="'.$fetch['sales_quantity'].'"  type="hidden" name="sales_quantity[]" id="sales_quantity'.$count.'" data-srno="'.$count.'" class="form-control sales_quantity"/>';

                $report .= '<input type="hidden"  value="'.$fetch['sales_rate'].'" type="number" name="sales_rate[]" id="sales_rate'.$count.'" data-srno="'.$count.'" class="form-control sales_rate"/>';

                $report .= '<input type="hidden"  value="'.$return_qty.'" type="number" name="return_qty[]" id="return_qty'.$count.'" data-srno="'.$count.'" class="form-control return_qty"/>';

                $report .= '<input type="hidden"  value="'.$previous_damage_qty.'" type="number" name="previous_damage_qty[]" id="previous_damage_qty'.$count.'" data-srno="'.$count.'" class="form-control previous_damage_qty"/>';



                
                    $report .= ' <tr'. $count .'">
                    <td >'.$sl++.'</td>
                    <td >'.$info_product['product_name'].'</td>
                    <td >'.number_format((float)$fetch['sales_quantity'], 2, '.', '').'</td>
                    <td >'.number_format((float)$fetch['carton_receive'], 2, '.', '').'</td>';
                  
                    $report .= '<td >'.number_format((float)$return_qty, 2, '.', '').'</td>';
                    $report .= '<td >'.number_format((float)$return_carton, 2, '.', '').'</td>';

                    $report .= '<td >'.number_format((float)$previous_damage_qty, 2, '.', '').'</td>';
                    $report .= '<td >'.number_format((float)$previous_damage_carton, 2, '.', '').'</td>';

         
                    $report .= '  <td><input value="0.00"   type="number" name="damage_qty[]" id="damage_qty'.$count.'" data-srno="'.$count.'" class="form-control damage_qty"/></td>';

                    $report .= '  <td><input value="0.00"    type="number" name="damage_carton[]" id="damage_carton'.$count.'" data-srno="'.$count.'" class="form-control damage_carton"/></td>';

                    $report .= ' <td><input value=""  type="text" name="note[]" id="note'.$count.'" data-srno="'.$count.'" class="form-control note"/></td>
                    <td style="display:none"> 
                   

                    </td>

                  </tr>';
                
                }

                $report .='<tr>
                       
                <input type="hidden" name="total_item" id="total_item" value="'.$rowCount.'"/>
                <th style="text-align:center;color:red" colspan="9" id="action_bar">';
                $report .='<input type="button" onclick="DamageProduct();" id="action_bar"   value="Damage Return">';
    
                $report .='</th></tr>';


                $report .= '</tbody>
                </table></div>';


                $report .='';


                   

                }else if($TYPE == 'Challan Copy' || $TYPE == 'Generate Godown Copy'){

                   
                    $countX = 2;  
                    
                    $report .= '<div class="table-responsive"><table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">

<tr><td colspan="4"> 
<!-- <a target="_blink" href="InventoryActivity/Finished-Goods-Warehouse-To-Warehouse/New/'.$info_sales['id'].'" class="btn btn-info btn-rounded btn-sm">TRANSFER <span class="fa fa-exchange"></span><a>--></td><td colspan="2"  style="text-align:center;font-size:18px;color:red">';
                    $report .= '<b class="form-group required control-label">Select Dispatcher</b>
                    
                    <select style="width:100%!imortant"  id="Seletdispatcher_id" name="Seletdispatcher_id[]" data-live-search=true class="selectpicker" multiple data-selected-text-format="count>3" data-all="false" >';
                    
    
                    
                    
                    $qry = $conn_me->prepare("SELECT A.`id`,B.`name` FROM `admin` A JOIN `setup_employee` B ON (`A`.`employee_id` = B.`id`)  WHERE A.hr_status = 'Active' AND B.`designation` = '20'; ");
                    $qry->execute();
                    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);

                        foreach($fetch_list AS $fetch2) { 
                           $report .= '<option ';
                           if(!empty($info_sales['dispatcher_id'])){
                            $a = json_decode($info_sales['dispatcher_id']);
                            for ($i = 0; $i < count($a); $i++) {
                                if(  $a[$i] == $fetch2['id'] ){ $report .= 'selected="selected"'; }else{ }
                              }
                           }
                 
                           $report .= ' value="'.$fetch2['id'].'">'.$fetch2['name'].'</option>';
                        }
                    
             
                    $report .= '</select>';
                    $report .= ' </td><td colspan="4"><input class="form-control text-danger" READONLY type="text" value=" '.$info_sales['dispatcher_name'].'" id="dispatcherNname"></td>
</tr>
        
  <tr>
    <th class="tg-c3ow" rowspan="2">SL</th>
    <th class="tg-c3ow" rowspan="2">PRODUCT DETAILS</th>
    <th class="tg-c3ow" colspan="2" style="text-align:center">QUANTITY</th>
    <th class="tg-c3ow" colspan="2" style="text-align:center">STOCK (with pipeline)</th>
    <th  rowspan="2" style="text-align:center" >WAREHOUSE</th>
    <th  colspan="2" style="text-align:center">Actual Dispatch</th>';
    

    $report .= '</tr>
  <tr>
    <th >PCS</th>
    <th >CARTON</th>
    <th >PCS</th>
    <th >CARTON</th>
    <th >PCS</th>
    <th >CARTON</th>';
        $report .= '<th colspan="2"></th>';

        $report .= '</tr>
';




                $sl=1;
                $count = 0;
                $query = $conn_me->prepare("SELECT * FROM `sales_invoice_item`  where `sales_invoice_id` = '".$info_sales['id']."'   ");
                $query->execute();
                $rowCount =$query->rowCount();

                $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
                foreach ($fetch_list as $fetch ) {
                    $count = $count + 1;
                    if(!empty($fetch['warehouse_id'])){
                        $selected_warehouse = $fetch['warehouse_id'];
                    }else{
                        $selected_warehouse = 'NOT';
                    }
                    $info_product = SETUP::SETUP_PRODUCT($fetch['product_id']);
            

$qty_in_carton = (float)($fetch['sales_quantity'] / $info_product['pcs_in_cartoon']);
$qty_in_carton = ($qty_in_carton < 1) ? 0 : $qty_in_carton;


                    $stock = STOCK::FG_ITEM_WISE_STOCK('',$fetch['product_id'],'product_wise');

                    $stock_qty_in_carton = $stock['ITEM_STOCK'] / ($info_product['pcs_in_cartoon'] ?? 0);


                    if(!empty($fetch['sales_quantity'])){ 
                        
                        $sales_quantity = $fetch['sales_quantity']; 
                        $sales_quantity_carton = $fetch['sales_quantity'] / ($info_product['pcs_in_cartoon'] ?? 0) ;
                       
                    }else{

                    $sales_quantity = '';
                    $sales_quantity_carton = '';
                
                }
                   
                if($info_sales['generate_challan'] == 'Pending' ){ 
                    $pcs_receive = $sales_quantity; 
                    $carton_receive = $sales_quantity_carton;  
                    
                   
                }else{
                    $pcs_receive = $fetch['pcs_receive']; 
                    $carton_receive = $fetch['carton_receive']; 
            
            }


                $report .= '<input type="hidden"  value="'.$info_product['pcs_in_cartoon'].'" type="number" name="carton[]" id="carton'.$count.'" data-srno="'.$count.'" class="form-control carton"/>';

                $report .= ' <input value="'.$fetch['product_id'].'"  type="hidden" name="product_id[]" id="product_id'.$count.'" data-srno="'.$count.'" class="form-control product_id"/>';

                $report .= ' <input value="'.$fetch['sales_quantity'].'"  type="hidden" name="sales_quantity[]" id="sales_quantity'.$count.'" data-srno="'.$count.'" class="form-control sales_quantity"/>';

                $report .= ' <input value="'.$fetch['sales_rate'].'"  type="hidden" name="sales_rate[]" id="sales_rate'.$count.'" data-srno="'.$count.'" class="form-control sales_rate"/>';


                

                $report .= ' <input value="'.$fetch['id'].'"  type="hidden" name="sales_id[]" id="sales_id'.$count.'" data-srno="'.$count.'" class="form-control sales_id"/>';

                $report .= ' <input value="'.$fetch['sales_quantity'].'"  type="hidden" name="demand_qty[]" id="demand_qty'.$count.'" data-srno="'.$count.'" class="form-control demand_qty"/>';

                
                    $report .= ' <tr '. $count .'" id="tr_no'. $count .'">
                    <td >'.$sl++.'</td>
                    <td >'.$info_product['product_name'].'</td>
                    <td >'.$fetch['sales_quantity'].'</td>
                    <td>'.number_format((float)$qty_in_carton, 2, '.', '').'</td>
                    <td>'.$stock['ITEM_STOCK'].'</td>
                    <td>'.number_format((float)$stock_qty_in_carton, 2, '.', '').'</td>
             ';

                    $report .= '<td>';
                    $report .='<select id="warehouse_id'.$count.'" name="warehouse_id[]" onchange="saveWarehouseLivedata(\''.$count.'\')"  class="form-control select" data-live-search="true">';
                    $a = FIND::WAREHOUSE_LIST('FG',$fetch['product_id'],$selected_warehouse);
                    $report .= $a['warehouse_list'];
                    $report .= '</select>';
                    $report .= ' </td>';
                    
                     
                    if($TYPE == 'Challan Copy' ){
                        $report .= '<td>'.$pcs_receive.'<input value="'.$pcs_receive.'"  type="hidden" name="sales_quantity[]" id="sales_quantity'.$count.'" data-srno="'.$count.'" class="form-control sales_quantity"/></td>

                        <td><input  value="' .number_format((float)$carton_receive, 2, '.', '').'"  type="number" name="carton_receive[]" id="carton_receive'.$count.'" data-srno="'.$count.'" class="form-control carton_receive text-danger"/></td>

                        <input value="0.00"  type="hidden" name="sales_quantity_stock_check[]" id="sales_quantity_stock_check'.$count.'" data-srno="'.$count.'" class="form-control sales_quantity_stock_check"/>';


                    }else{
                    

           
 $report .= '<td><input value="'.$pcs_receive.'"  type="hidden" name="sales_quantity_stock_check[]" id="sales_quantity_stock_check'.$count.'" data-srno="'.$count.'" class="form-control sales_quantity_stock_check"/><input value="0.00"  type="hidden" name="sales_quantity[]" id="sales_quantity'.$count.'" data-srno="'.$count.'" class="form-control sales_quantity"/></td><td><input  value="0.00"  type="hidden" name="carton_receive[]" id="carton_receive'.$count.'" data-srno="'.$count.'" class="form-control carton_receive text-danger"/></td>';
                    }


                    
                    
                  $report .= '</tr>';
                
                }



                if($TYPE == 'Challan Copy' ){

                    
                    $report .='<tr>';
                    $report .='<td></td><td></td><td></td><td></td>';
                    $report .='<th>Transport  Type</th>
                    <td><select class="select form-control" onchange="fatch_customer_info(\'CHLLAN_COPY\')" id="transport_cost_type">
                    <option value="">Select One</option>   
                    <option value="nogot_cost">Nogot</option>
                        <option value="nogot_cost">Baki </option>
                    </select></td>
                    <td></td><td></td><td></td>
                </tr>

            
                <tr>
                 <td></td><td></td><td></td><td></td>

                    <th>Transport  Cost <b id="estamated_cost"></b></th>
                    <td><input type="number"  name="total_transport_cost"   onkeyup="sale_calculator();"   id="total_transport_cost" value="'.$transport_cost.'" class="form-control"></td>
                    <td></td><td></td><td></td>
                ';

                    $report .='</tr>';
                }else{
                    $report .='<input type="hidden" name="transport_cost_type" id="transport_cost_type" value=""/>
                    <input type="hidden" name="total_transport_cost" id="total_transport_cost" value=""/>';

                }

                $report .='<tr>
                       
                <input type="hidden" name="total_item" id="total_item" value="'.$rowCount.'"/>
                <th style="text-align:center;color:red" colspan="9" id="action_bar">';

                if(!empty($info_sales['dispatcher_id'])){
                    $report .='<input type="button" onclick="ChallanCopySave();" class="btn btn-info"  id="action_bar"   value="Save Challan">';
                  

                }else{
                    $report .='<input type="button" onclick="GodowncopyCopySave();" id="action_bar"   value="Save Godown Copy">';
                }

             
    
                $report .='</th></tr>';


                $report .= '
                </table></div>';


                $report .='';





                }else{
                    $report .= '';
                }


            }

            return array(

                'report' =>  $header_content . $report
        
        
                );

                
        }

        static   function Quatation($WORK,$TYPE,$CODE,$FROM,$TO){
            $conn_me = Database::getInstance();

            $ck = $conn_me->prepare("SELECT * FROM `quotation_invoice`  where `code` = '".$CODE."'   ");
            $ck->execute();
            $ck_count =$ck->rowCount();
            $fetch_ck_data = $ck->fetch(PDO::FETCH_ASSOC);
            $info_customer = SETUP::SETUP_CUSTOMER($fetch_ck_data['customer_id']);
        
            $company_info = SETUP::SETUP_COMPANY('Active');
        
            
        
            $header_content ='<table style="width:100%;" class="table datatable_simple">';
        
            $header_content .='<tr><th style="text-align:center"><img style="height:70px" src="upload/Company_Logo/'.$company_info['company_logo'].'"></th></tr>';
        
            $header_content .='</table>';
        
            $report = '';
        
            $report .= '<table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
            <tr><th colspan="6" style="text-align:center;font-size:18px;color:red">'.$TYPE.'</th></tr> ';
            
            $report .= ' <tr><td colspan="6" style="text-align:center;font-size:12px;">Customer : '.$info_customer['shop_name'].'<p style="text-align:center;font-size:12px;">Invoice No : '.$fetch_ck_data['invoice_no'].' ||  Date : '.$fetch_ck_data['invoice_date'].' </p></td></tr> ';
        
        
            
        
        
            if($WORK == 'quatation' ){
        
        
                if($TYPE == 'Quatation' ){
        
        
                    $report .= ' <tr>
                    <th>Sl</th>
                    <th>Product</th>
                    <th>Pcs</th>
                    <th>Carton</th>
        
                </tr>';
        
                    $sl=1;
                    $total_pcs = 0;
                    $total_carton = 0;
                    $query = $conn_me->prepare("SELECT * FROM `quotation_invoice_item`  where `code` = '".$CODE."'   ");
                    $query->execute();
                    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($fetch_list as $fetch ) {
                        $info_product = SETUP::SETUP_PRODUCT($fetch['product_id']);
                        if($fetch['quantity_type'] == 'Pcs'){
        
                            $qty_in_pcs = $fetch['quantity'];
                            $qty_in_carton = $fetch['quantity']/$info_product['pcs_in_cartoon'];
        
                        }else if($fetch['quantity_type'] == 'Carton'){
        
                            $qty_in_pcs = $fetch['quantity']*$info_product['pcs_in_cartoon'];
                            $qty_in_carton = $fetch['quantity'];
                            
                        }else{
                            $qty_in_pcs = 0.00;
                            $qty_in_carton = 0.00;
                        }
        
                        
                        $report .= ' <tr>
                        <td>'.$sl++.'</td>
                        <td>'.$info_product['product_name'].'</td>
                        <td>'.number_format((float)$qty_in_pcs, 2, '.', '').'</td>
                        <td>'.number_format((float)$qty_in_carton, 2, '.', '').'</td>
        
                    </tr>';
                    $total_pcs += number_format((float)$qty_in_pcs, 2, '.', '');
                    $total_carton += number_format((float)$qty_in_carton, 2, '.', '');
        
                    }
                    $report .= '<tr><th></th><th></th><th> Total Pcs '.$total_pcs.'</th><th> Total Carton '.$total_carton.'</th></tr>';
        
        
                }else{
        
                }
        
        
            }
        
            return array(
        
                'report' =>  $header_content . $report
        
        
                );
        
                
        }
        


        static   function STOCK_ADJUSTMENT_REPORT($date_and_warehouseid){

            $explode= explode("@",$date_and_warehouseid);

            $conn_me = Database::getInstance();

            $company_info = SETUP::SETUP_COMPANY('Active');
        
            $warehouse_info = SETUP::SETUP_WAREHOUSE($explode[0]);

            $header_content ='<table style="width:100%;" class="table datatable_simple">';

            $header_content .='<tr><th style="text-align:center"><img style="height:70px" src="upload/Company_Logo/'.$company_info['company_logo'].'"></th></tr>';

            $header_content .='</table>';

            $report = ' <div class="row">
            <div class="col-md-12">
                
                                                
                  <div class="table-responsive">
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Product Code</th>
                                        <th>Product Name</th>
                                        <th>Stock In</th>
                                        <th>Stock Out</th>

                                    </tr>
                                </thead>
                                <tbody>';
                
                                $sl =  1;
                                $count_invoice = 0;
$query = $conn_me->prepare("SELECT A.*,B.code,B.product_name   FROM `balance_product` A JOIN setup_product B ON (A.product_id = B.id) where A.warehouse_id = '".$explode['0']."' AND A.date = '".$explode['1']."' AND A.note = 'STOCK ADJUSTMENT'  ");
$query->execute();
$fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list as $fetch ) { 

    $report .= ' <tr>   
                                        <td>'.$sl++.'</td>
                                        <td>'.$fetch['code'].'</td>
                                        <td>'.$fetch['product_name'].'</td>
                                        <td>'.$fetch['stock_in'].'</td>
                                        <td>'.$fetch['stock_out'].'</td>

                                    </tr>';
 } 
 $report .= '</tbody>
                            </table>  

                            </div>                            
                
             
                
            </div>
        </div>';      



            return array(
        
                'report' =>  $header_content . $report
        
        
                );
        
                
        }

        static   function INVENTORY($WORK,$TYPE,$CODE,$FROM,$TO){
            $conn_me = Database::getInstance();

            $company_info = SETUP::SETUP_COMPANY('Active');
        

            $header_content ='<table style="width:100%;" class="table datatable_simple">';

            $header_content .='<tr><th style="text-align:center"><img style="height:70px" src="upload/Company_Logo/'.$company_info['company_logo'].'"></th></tr>';

            $header_content .='</table>';

            $report = '<input type="hidden" id="code" value="'.$CODE.'" >';

if($WORK == 'raw_local_purches' ){


    if($TYPE == 'Report Raw Local Purchase' ){

        

        $report .= '<table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
        <tr><th colspan="6" style="text-align:center;font-size:18px;color:red">'.$TYPE.'</th></tr> ';
        $qu = $conn_me->prepare("SELECT * FROM `raw_local_purches`  where `code` = '".$CODE."'  GROUP BY `code` ");
        $qu->execute();
        $fetch_data = $qu->fetch(PDO::FETCH_ASSOC);
        $info_supplier = SETUP::SETUP_SUPPLIER($fetch_data['supplier_id']);
        $report .= ' <tr><td colspan="6" style="text-align:center;font-size:12px;">Supplier : '.$info_supplier['supplier_name'].'<p style="text-align:center;font-size:12px;">Batch No : '.$fetch_data['invoice_no'].' || Note : '.$fetch_data['note'].' ||  Date : '.$fetch_data['date'].' </p></td></tr> ';

      

        $report .= ' <tr>
                <th>Sl</th>
                <th>Product</th>
                <th>Category</th>
                <th>Purchase Rate</th>
                <th>Quantity</th>
                <th>Total Amount</th>

            </tr>';

        
        $sl =1;
        $total_qty = 0;
        $total_rate = 0;
        $query = $conn_me->prepare("SELECT * FROM `raw_local_purches`  where `code` = '".$CODE."'  ");
        $query->execute();
        $rowCount =$query->rowCount();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) {

        $info_material = SETUP::SETUP_RAW_MATERIAL($fetch['product_id']);
         $total_price = $fetch['purches_price']*$fetch['quantity'];

            $report .= '<tr >';
            $report .= '<td>'.$sl++.'</td>';
            $report .= '<td>'.$info_material['product_name'].'</td>';
            $report .= '<td>'.$info_material['category'].'</td>';
            $report .= '<td>'.$fetch['purches_price'].'</td>';
            $report .= '<td>'.$fetch['quantity'].'</td>';
            $report .= '<td>'.$total_price.'</td>';


            $report .= '</tr>';
            $total_qty += $fetch['quantity'];
            $total_rate += $total_price;

        }


        $report .= '<tr >';
        $report .= '<td colspan="4" style="text-align:center"> T O T A L </td>';
        $report .= '<td>'.$total_qty.'</td>';
        $report .= '<td>'.$total_rate.'</td>';
        $report .= '</tr>';
    $report .='</table>';

}else if($TYPE == 'Batch Wise Raw Local Purches' ){


    $report .= '<table style="width:100%;" class="table table-hover table-condensed table-striped table-bordered">
    <tr><th colspan="6" style="text-align:center;font-size:18px;color:red">'.$TYPE.'</th></tr> ';

    
    $qu = $conn_me->prepare("SELECT * FROM `raw_local_purches`  where `receipe_wise_demand_code` = '".$CODE."'  GROUP BY `receipe_wise_demand_code` ");
    $qu->execute();
    $fetch_data = $qu->fetch(PDO::FETCH_ASSOC);
    $info_supplier = SETUP::SETUP_SUPPLIER($fetch_data['supplier_id']);
    $report .= ' <tr><td colspan="6" style="text-align:center;font-size:12px;">Supplier : '.$info_supplier['supplier_name'].'<p style="text-align:center;font-size:12px;">Batch No : '.$fetch_data['invoice_no'].' || Note : '.$fetch_data['note'].' ||  Date : '.$fetch_data['date'].' </p></td></tr> ';

  

    $report .= ' <tr>
            <th>Sl</th>
            <th>Product</th>
            <th>Category</th>
            <th>Purchase Rate</th>
            <th>Quantity</th>
            <th>Total Amount</th>

        </tr>';

    
    $sl =1;
    $total_qty = 0;
    $total_rate = 0;
    $query = $conn_me->prepare("SELECT * FROM `raw_local_purches`  where `receipe_wise_demand_code` = '".$CODE."'  ");
    $query->execute();
    $rowCount =$query->rowCount();
    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch ) {

    $info_material = SETUP::SETUP_RAW_MATERIAL($fetch['product_id']);
     $total_price = $fetch['purches_price']*$fetch['quantity'];

        $report .= '<tr >';
        $report .= '<td>'.$sl++.'</td>';
        $report .= '<td>'.$info_material['product_name'].'</td>';
        $report .= '<td>'.$info_material['category'].'</td>';
        $report .= '<td>'.$fetch['purches_price'].'</td>';
        $report .= '<td>'.$fetch['quantity'].'</td>';
        $report .= '<td>'.$total_price.'</td>';


        $report .= '</tr>';
        $total_qty += $fetch['quantity'];
        $total_rate += $total_price;

    }


    $report .= '<tr >';
    $report .= '<td colspan="4" style="text-align:center"> T O T A L </td>';
    $report .= '<td>'.$total_qty.'</td>';
    $report .= '<td>'.$total_rate.'</td>';
    $report .= '</tr>';
$report .='</table>';


}else if($TYPE == 'Pending Damage Receive' ){

    

    $report .= '<table style="width:100%;" class="table datatable_simple">
    <tr><th colspan="8" style="text-align:center;font-size:18px">'.$TYPE.'</th></tr> 
     <input type="hidden" id="code"  id="code"  value="'.$CODE.'">
        <tr>
            <th>Sl</th>
            <th>Product Name</th>
            <th>Damage Qty </th>
            <th>Warehouse</th>

        </tr>';

    
    $total_receive_left  = 0;
    $sl =1;
    $count = 0;
    $query = $conn_me->prepare("SELECT * FROM `damage_invoice_item`  where `code` = '".$CODE."'   ");
    $query->execute();
    $rowCount =$query->rowCount();
    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch ) {
    $count = $count + 1;
    $info_product = SETUP::SETUP_PRODUCT($fetch['product_id']);

    $report .= '<input value="'.$fetch['id'].'"  type="hidden" name="id[]" id="id'.$count.'" data-srno="'.$count.'" class="form-control id"/> ';

    $report .= '<input value="'.$fetch['damage_quantity'].'"  type="hidden" name="damage_quantity[]" id="damage_quantity'.$count.'" data-srno="'.$count.'" class="form-control damage_quantity"/> ';

    $report .= '<input value="'.$fetch['product_id'].'"  type="hidden" name="product_id[]" id="product_id'.$count.'" data-srno="'.$count.'" class="form-control product_id"/> ';

        $report .= '<tr class="tr'. $count .'">';
        $report .= '<td>'.$sl++.'</td>';
        $report .= '<td>'.$info_product['product_name'].'</td>';
        $report .= '<td>'.$fetch['damage_quantity'].'</td>';

        $report .= '<td>';
        $report .= '<select id="warehouse_id'.$count.'" name="warehouse_id[]"   class="form-control select" data-live-search="true">
        <option value="">Select One</option>';
        $qry = $conn_me->prepare("SELECT * FROM `setup_warehouse`  where status = 1 ");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch_w) {

        $report .= '<option value="'.$fetch_w['id'].'">'.$fetch_w['name'].'</option>';
            } 

      $report .= '</select>';
        $report .= '</td>';

        $report .= '</tr>';


    }

    $report .='<tr>
                   
    <input type="hidden" name="total_item" id="total_item" value="'.$rowCount.'"/>
    <th style="text-align:center;color:red" colspan="8" id="action_bar">';
    $report .='<input type="button" class="btn btn-danger" onclick="final_damage_receiv_by_warehouse();"   value="Update Receive">';
 

$report .='</th></tr>';

$report .='</table>';



}else if($TYPE == 'Pending Receive Sales Return' ){

    $report .= '<table style="width:100%;" class="table datatable_simple">
    <tr><th colspan="8" style="text-align:center;font-size:18px">'.$TYPE.'</th></tr> 
     <input type="hidden" id="code"  id="code"  value="'.$CODE.'">
        <tr>
            <th>Sl</th>
            <th>Product Name</th>
            <th>Return Qty </th>
            <th>Warehouse</th>

        </tr>';

    
    $total_receive_left  = 0;
    $sl =1;
    $count = 0;
    $query = $conn_me->prepare("SELECT * FROM `sales_return_invoice_item`  where `code` = '".$CODE."'   ");
    $query->execute();
    $rowCount =$query->rowCount();
    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch ) {
    $count = $count + 1;
    $info_product = SETUP::SETUP_PRODUCT($fetch['product_id']);

    $report .= '<input value="'.$fetch['id'].'"  type="hidden" name="id[]" id="id'.$count.'" data-srno="'.$count.'" class="form-control id"/> ';


    $report .= '<input value="'.$fetch['return_quantity'].'"  type="hidden" name="return_quantity[]" id="return_quantity'.$count.'" data-srno="'.$count.'" class="form-control return_quantity"/> ';

    $report .= '<input value="'.$fetch['product_id'].'"  type="hidden" name="product_id[]" id="product_id'.$count.'" data-srno="'.$count.'" class="form-control product_id"/> ';


        $report .= '<tr class="tr'. $count .'">';
        $report .= '<td>'.$sl++.'</td>';
        $report .= '<td>'.$info_product['product_name'].'</td>';
        $report .= '<td>'.$fetch['return_quantity'].'</td>';

        $report .= '<td>';
        $report .= '<select id="warehouse_id'.$count.'" name="warehouse_id[]"   class="form-control select" data-live-search="true">
        <option value="NOTSELECTED">Select One</option>';
        $qry = $conn_me->prepare("SELECT * FROM `setup_warehouse`  where status = 1 ");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch_w) {

        $report .= '<option value="'.$fetch_w['id'].'">'.$fetch_w['name'].'</option>';
            } 

      $report .= '</select>';
        $report .= '</td>';

        $report .= '</tr>';


    }

    $report .='<tr>
                   
    <input type="hidden" name="total_item" id="total_item" value="'.$rowCount.'"/>
    <th style="text-align:center;color:red" colspan="8" id="action_bar">';
    $report .='<input type="button" class="btn btn-danger" onclick="receive_sales_retuen_by_warehouse();"   value="Update Receive">';
 

$report .='</th></tr>';

$report .='</table>';


    }else if($TYPE == 'Pending Receive Raw Local Purches' ){

        $report .= '<table style="width:100%;" class="table datatable_simple">
        <tr><th colspan="8" style="text-align:center;font-size:18px">'.$TYPE.'</th></tr> 
         <input type="hidden" id="code"  id="code"  value="'.$CODE.'">
            <tr>
                <th>Sl</th>
                <th>Material Name</th>
                <th>Order Qty </th>
                <th>Total Receive</th>
                <th>Rceive Left </th>
                <th>Receive Now </th>
                <th>Warehouse</th>

            </tr>';

        
        $total_receive_left  = 0;
        $sl =1;
        $count = 0;
        $query = $conn_me->prepare("SELECT * FROM `raw_local_purches`  where `code` = '".$CODE."'  ");
        $query->execute();
        $rowCount =$query->rowCount();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) {
        $count = $count + 1;
        $info_material = SETUP::SETUP_RAW_MATERIAL($fetch['product_id']);
        $receive_reject_info = FIND::RAW_LOCAL_PURCHES_RECEIVE_REJECT($fetch['code'],$fetch['product_id'],'Invoice_Wise');
        $total_receive = $receive_reject_info['actual_receive'];
        $receive_left = $fetch['quantity'] - $receive_reject_info['actual_receive'];

        $report .= '<input value="'.$fetch['quantity'].'"  type="hidden" name="actual_quantity[]" id="actual_quantity'.$count.'" data-srno="'.$count.'" class="form-control actual_quantity"/> ';
        $report .= '<input value="'.$fetch['product_id'].'"  type="hidden" name="product_id[]" id="product_id'.$count.'" data-srno="'.$count.'" class="form-control product_id"/> ';
        $report .= '<input value="'.$fetch['supplier_id'].'"  type="hidden" name="supplier_id[]" id="supplier_id'.$count.'" data-srno="'.$count.'" class="form-control supplier_id"/> ';
        $report .= '<input value="'.$total_receive.'"  type="hidden" name="actual_receive[]" id="actual_receive'.$count.'" data-srno="'.$count.'" class="form-control actual_receive"/> ';

            $report .= '<tr class="tr'. $count .'">';
            $report .= '<td>'.$sl++.'</td>';
            $report .= '<td>'.$info_material['product_name'].'</td>';
            $report .= '<td>'.$fetch['quantity'].'</td>';
            $report .= '<td>'.$total_receive.'</td>';
            $report .= '<td>'.$receive_left.'</td>';
            $report .= '<td>';
            if($receive_left > 0){
                $report .='<input type="number" name="receive_now[]" id="receive_now'.$count.'" data-srno="'.$count.'" class="form-control receive_now" value="0.00"/> ';
            }else{
                $report .='Receive Done<input type="hidden" name="receive_now[]" id="receive_now'.$count.'" data-srno="'.$count.'" class="form-control receive_now" value="0.00"/>';

            }
            
            $report .= '</td>';
            $report .= '<td>';
            $report .= '<select id="warehouse_id'.$count.'" name="warehouse_id[]"   class="form-control select" data-live-search="true">
            <option value="">Select One</option>';
            $qry = $conn_me->prepare("SELECT * FROM `setup_warehouse` where status = 1 ");
            $qry->execute();
            $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
            foreach ($fetch_list as $fetch_w) {
    
            $report .= '<option value="'.$fetch_w['id'].'">'.$fetch_w['name'].'</option>';
                } 
    
          $report .= '</select>';
            $report .= '</td>';

            $report .= '</tr>';

    $total_receive_left+= $receive_left; 

        }

        $report .='<tr>
                       
        <input type="hidden" name="total_item" id="total_item" value="'.$rowCount.'"/>
        <th style="text-align:center;color:red" colspan="8" id="action_bar">';
        if($total_receive_left > 0 ){
        $report .='<input type="button" class="btn btn-danger" onclick="update_receive_raw_local_purches();"   value="Update Receive">';
        }else{
            $report .='<input type="button" class="btn btn-success"  onclick="done_receive_raw_local_purches();"  value="Receive Done">';

        }

$report .='</th></tr>';

    $report .='</table>';


    }else if($TYPE == 'Return Raw Local Purches'){

 $report .= '<table style="width:100%;" class="table datatable_simple">
        <tr><th colspan="8" style="text-align:center;font-size:18px">'.$TYPE.'</th></tr> 
         <input type="hidden" id="code"  id="code"  value="'.$CODE.'">
            <tr>
                <th>Sl</th>
                <th>Material Name</th>
                <th>Order Qty </th>
                <th>Total Receive</th>
                <th>Return Now </th>
                <th>Warehouse</th>
            </tr>';

        $total_qyantity = 0;
        $total_total_receive  = 0;
        $sl =1;
        $count = 0;
        $query = $conn_me->prepare("SELECT * FROM `raw_local_purches`  where `code` = '".$CODE."'  ");
        $query->execute();
        $rowCount =$query->rowCount();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) {
        $count = $count + 1;
        $info_material = SETUP::SETUP_RAW_MATERIAL($fetch['product_id']);
        $receive_reject_info = FIND::RAW_LOCAL_PURCHES_RECEIVE_REJECT($fetch['code'],$fetch['product_id'],'Invoice_Wise');
        $total_receive = $receive_reject_info['total_receive'];

        $report .= '<input value="'.$fetch['quantity'].'"  type="hidden" name="actual_quantity[]" id="actual_quantity'.$count.'" data-srno="'.$count.'" class="form-control actual_quantity"/> ';
        $report .= '<input value="'.$fetch['product_id'].'"  type="hidden" name="product_id[]" id="product_id'.$count.'" data-srno="'.$count.'" class="form-control product_id"/> ';
        $report .= '<input value="'.$fetch['supplier_id'].'"  type="hidden" name="supplier_id[]" id="supplier_id'.$count.'" data-srno="'.$count.'" class="form-control supplier_id"/> ';

            $report .= '<tr class="tr'. $count .'">';
            $report .= '<td>'.$sl++.'</td>';
            $report .= '<td>'.$info_material['product_name'].'</td>';
            $report .= '<td>'.$fetch['quantity'].'</td>';
            $report .= '<td>'.$total_receive.'</td>';
            $report .= '<td>';
            $report .='<input type="number" name="reject_now[]" id="reject_now'.$count.'" data-srno="'.$count.'" class="form-control reject_now" value="0.00"/> ';
            $report .= '</td>';
            $report .= '<td>';
            $report .= '<select id="warehouse_id" name="warehouse_id[]"   class="form-control select" data-live-search="true">
            <option value="">Select One</option>';
            $qry = $conn_me->prepare("SELECT * FROM `setup_warehouse` where status = 1 ");
            $qry->execute();
            $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
            foreach ($fetch_list as $fetch_w) {
    
            $report .= '<option value="'.$fetch_w['id'].'">'.$fetch_w['name'].'</option>';
                } 
    
          $report .= '</select>';
            $report .= '</td>';

            $report .= '</tr>';

    $total_qyantity += $fetch['quantity']; 
    $total_total_receive  += $total_receive;

        }

        $report .='<tr>
                       
        <input type="hidden" name="total_item" id="total_item" value="'.$rowCount.'"/>
        <th style="text-align:center;color:red" colspan="4" id="action_bar">';
        $report .='<input type="button" onclick="update_return_raw_local_purches();"   value="Update Receive">';



    $report .='
    <th style="text-align:center;color:green" colspan="4" id="action_bar">';
    if($total_qyantity == $total_total_receive){
        $report .='<input type="button" onclick="done_receive_raw_local_purches();"  value="Receive Done">';
     }
$report .='</th></tr>';

    $report .='</table>';

    
    }else{

        $report .= '';
    }

}else if($WORK == 'fg_local_purches'){


    if($TYPE == 'Pending Receive FG Local Purchase' ){

    

        $report .= '<table style="width:100%;" class="table datatable_simple">
        <tr><th colspan="8" style="text-align:center;font-size:18px">'.$TYPE.'</th></tr> 
         <input type="hidden" id="code"  id="code"  value="'.$CODE.'">
            <tr>
                <th>Sl</th>
                <th>Item Name</th>
                <th>Order Qty </th>
                <th>Total Receive</th>
                <th>Rceive Left </th>
                <th>Receive Now </th>
                <th>Warehouse</th>

            </tr>';

        $total_qyantity = 0;
        $total_total_receive  = 0;
        $sl =1;
        $count = 0;
        $query = $conn_me->prepare("SELECT * FROM `fg_local_purches`  where `code` = '".$CODE."'  group by product_id ORDER BY `id` DESC ");
        $query->execute();
        $rowCount =$query->rowCount();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fetch_list as $fetch ) {
        $count = $count + 1;
        $info_material = SETUP::SETUP_PRODUCT($fetch['product_id']);
        $receive_reject_info = FIND::FG_LOCAL_PURCHES_RECEIVE_REJECT($fetch['code'],$fetch['product_id'],'Invoice_Wise');
        $total_receive = $receive_reject_info['total_receive'];
        $receive_left = $fetch['quantity'] - $receive_reject_info['actual_receive'];

        $report .= '<input value="'.$fetch['quantity'].'"  type="hidden" name="actual_quantity[]" id="actual_quantity'.$count.'" data-srno="'.$count.'" class="form-control actual_quantity"/> ';
        $report .= '<input value="'.$fetch['product_id'].'"  type="hidden" name="product_id[]" id="product_id'.$count.'" data-srno="'.$count.'" class="form-control product_id"/> ';
        $report .= '<input value="'.$fetch['supplier_id'].'"  type="hidden" name="supplier_id[]" id="supplier_id'.$count.'" data-srno="'.$count.'" class="form-control supplier_id"/> ';

            $report .= '<tr class="tr'. $count .'">';
            $report .= '<td>'.$sl++.'</td>';
            $report .= '<td>'.$info_material['product_name'].'</td>';
            $report .= '<td>'.$fetch['quantity'].'</td>';
            $report .= '<td>'.$total_receive.'</td>';
            $report .= '<td>'.$receive_left.'</td>';
            $report .= '<td>';
            $report .='<input type="number" name="receive_now[]" id="receive_now'.$count.'" data-srno="'.$count.'" class="form-control receive_now" value="0.00"/> ';
            $report .= '</td>';
            $report .= '<td>';
            $report .= '<select id="warehouse_id" name="warehouse_id[]"   class="form-control select" data-live-search="true">
            <option value="">Select One</option>';
            $qry = $conn_me->prepare("SELECT * FROM `setup_warehouse`  where status = 1");
            $qry->execute();
            $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
            foreach ($fetch_list as $fetch_w) {
    
            $report .= '<option value="'.$fetch_w['id'].'">'.$fetch_w['name'].'</option>';
                } 
    
          $report .= '</select>';
            $report .= '</td>';
   


            $report .= '</tr>';

    $total_qyantity += $fetch['quantity']; 
    $total_total_receive  += $total_receive;

        }

        $report .='<tr>
                       
        <input type="hidden" name="total_item" id="total_item" value="'.$rowCount.'"/>
        <th style="text-align:center;color:red" colspan="4" id="action_bar">';
        $report .='<input type="button" onclick="update_receive_fg_local_purches();"   value="Update Receive">';



    $report .='
    <th style="text-align:center;color:green" colspan="4" id="action_bar">';
    if($total_qyantity == $total_total_receive){
        $report .='<input type="button" onclick="done_receive_fg_local_purches();"  value="Receive Done">';
     }
$report .='</th></tr>';

    $report .='</table>';

    }else if($TYPE == 'Return FG Local Purches'){$report = '';

$report .= '
                  <table class="table table-hover table-condensed table-striped table-bordered" id="MSalary">
    <tr>
        <th colspan="8" style="text-align:center; font-size:18px;">' . htmlspecialchars($TYPE) . '</th>
    </tr>
    <input type="hidden" id="code" name="code" value="' . htmlspecialchars($CODE) . '">
    <thead>
        <tr>
            <th>Sl</th>
            <th>Item Name</th>
            <th>Order Qty</th>
            <th>Total Receive</th>
            <th>Total Return</th>
            <th>Return Now</th>
            <th>Warehouse</th>
        </tr>
    </thead>
    <tbody>
';

$total_quantity = 0;
$total_total_receive = 0;
$sl = 1;
$count = 0;

$query = $conn_me->prepare("SELECT * FROM `history_local_fg_purches` WHERE `code` = :code group by product_id");
$query->execute(['code' => $CODE]);
$rowCount = $query->rowCount();
$fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);

foreach ($fetch_list as $fetch) {
    $count++;
    $info_material = SETUP::SETUP_PRODUCT($fetch['product_id']);
    $receive_reject_info = FIND::FG_LOCAL_PURCHES_RECEIVE_REJECT($fetch['code'], $fetch['product_id'], 'Invoice_Wise');

    $total_receive = $receive_reject_info['actual_receive'];
    $total_reject = $receive_reject_info['total_reject'];
    $selected_warehouse = $fetch['warehouse_id'] ?? 'NOT';

    // Hidden inputs
    $report .= '
        <input type="hidden" name="actual_quantity[]" id="actual_quantity' . $count . '" value="' . $fetch['quantity'] . '">
        <input type="hidden" name="product_id[]" id="product_id' . $count . '" value="' . $fetch['product_id'] . '">
        <input type="hidden" name="supplier_id[]" id="supplier_id' . $count . '" value="' . $fetch['supplier_id'] . '">
        <input type="hidden" name="total_receive[]" id="total_receive' . $count . '" value="' . $total_receive . '">
    ';

    // Table row
    $report .= '
        <tr class="tr' . $count . '">
            <td>' . $sl++ . '</td>
            <td>' . htmlspecialchars($info_material['product_name']) . '</td>
            <td>' . $fetch['quantity'] . '</td>
            <td>' . $total_receive . '</td>
            <td>' . $total_reject . '</td>
            <td>
                <input type="number" name="return_now[]" id="return_now' . $count . '" class="form-control return_now" value="0.00" data-srno="' . $count . '">
            </td>
            <td>
                <select id="warehouse_id' . $count . '" name="warehouse_id[]" class="form-control select" data-live-search="true">
                    ' . FIND::WAREHOUSE_LIST('FG', $fetch['product_id'], $selected_warehouse)['warehouse_list'] . '
                </select>
            </td>
        </tr>
    ';

    $total_quantity += $fetch['quantity'];
    $total_total_receive += $total_receive;
}

// Footer
$report .= '
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7" style="text-align:center;">
                <input type="hidden" name="total_item" id="total_item" value="' . $rowCount . '">
                <input type="button" onclick="update_return_fg_local_purches();" value="Update Receive" class="btn btn-primary">
            </td>
        </tr>
    </tfoot>
</table>
';



}else if($TYPE == 'Report FG Local Purchase'){


    }else{

        $report .= '';
    }



}else{



}




        return array(

        'report' =>  $header_content . $report


        );

        }

}


class TEMPLET 
{

   

    public static function TEMPLET_CONTENT($PAGENAME,$RELATEDID)
    {

        $conn_me = Database::getInstance();


          $access = SETUP::ADMIN_SETUP($_SESSION['NEWERP_SESS_MEMBER_ID']);





if($PAGENAME == 'Dashboard' ){



$content = '';
}else if($PAGENAME == 'New'){

}else if($PAGENAME == 'NoPermission'){
    $content = 'Its not a menu.. pls click sub menu ';

}else if($PAGENAME == 'Welcome'){

    


    $com_profile = SETUP::SETUP_COMPANY('Active');
   

       $warning1 =  date('d-m-Y', strtotime('-1 week', strtotime($com_profile["software_end_date"])));

if(date('d-m-Y') < $warning1){

    $mess = "<marquee class='text-danger'>Your Subscription will end at ".$com_profile["software_end_date"]." , Please contact developer</marquee>" ;

}else{

    $mess = "" ;

}

    $content = ' <div class="error-container animated flash" >
    <div class="error-text">'.$com_profile['company_name'].'</div>
    <div class="error-subtext">'.$com_profile['company_address'].' <br>'.$com_profile['company_phone']. '<br>'.$com_profile['company_email'].'</div>            
</div>';



}else if($PAGENAME == 'Advance-Setup'){


    $info_advance = SETUP::SETUP_ADVANCE();


    $content = '<div class="col-md-12 form-horizontal"><div class="form-group" >
    <label class="col-md-3 col-xs-12 control-label">Job less than one year</label>
    <div class="col-md-4 col-xs-12">
       <input type="number" name="less_then_1_year" id="less_then_1_year"  class="form-control" value="'.$info_advance['fetch']['less_then_1_year'].'"></div>
    </div>

    <div class="form-group" >
    <label class="col-md-3 col-xs-12 control-label">Job more than one year but less than two years</label>
    <div class="col-md-4 col-xs-12">
       <input type="number" name="more_then_1_less_then_2" id="more_then_1_less_then_2"  class="form-control" value="'.$info_advance['fetch']['more_then_1_less_then_2'].'"></div>
    </div>


    <div class="form-group" >
    <label class="col-md-3 col-xs-12 control-label">Job more than two years Less than three years</label>
    <div class="col-md-4 col-xs-12">
       <input type="number" name="more_then_2_less_then_3" id="more_then_2_less_then_3"  class="form-control" value="'.$info_advance['fetch']['more_then_2_less_then_3'].'"></div>
    </div>

    <div class="form-group" >
    <label class="col-md-3 col-xs-12 control-label">Job more than three years Less than four years</label>
    <div class="col-md-4 col-xs-12">
       <input type="number" name="more_then_3_less_then_4" id="more_then_3_less_then_4"  class="form-control" value="'.$info_advance['fetch']['more_then_3_less_then_4'].'"></div>
    </div>
    <div class="form-group" >
    <label class="col-md-3 col-xs-12 control-label">Job more than four years Less than five years</label>
    <div class="col-md-4 col-xs-12">
       <input type="number" name="more_then_4_less_then_5" id="more_then_4_less_then_5"  class="form-control" value="'.$info_advance['fetch']['more_then_4_less_then_5'].'"></div>
    </div>
    <div class="form-group" >
    <label class="col-md-3 col-xs-12 control-label">Job more than five years    </label>
    <div class="col-md-4 col-xs-12">
       <input type="number" name="more_then_5" id="more_then_5"  class="form-control" value="'.$info_advance['fetch']['more_then_5'].'"></div>
    </div>
    
    <div class="form-group" >
    <label class="col-md-3 col-xs-12 control-label"></label>
    <div class="col-md-4 col-xs-12">
       <input type="button" name="save_data" id="save_data"  class="btn btn-info" value="Save Data" onclick="saveAdvanceSetup()"></div>
    </div></div>';

}else if($PAGENAME == 'User-Activity'){

  

    if($RELATEDID == 'New'){
    $show_activity_line = 'No';
    $photo = 'no_image.jpg';
    $name = 'Mr. Nobody';
    $code = '';
    $present_department = '';
    $mob_no = '';
    $email = '';
    $admin_id = '';

    }else{
        
        $info = SETUP::ADMIN_SETUP($RELATEDID);
        $show_activity_line = 'Yes';
        $admin_id = $info['id'];
        $photo = $info['photo'];
        $name = $info['hr_name'];
        $code = $info['username'];
        $present_department = $info['designation'];

    
    }

    $content  ='<div class="row">
    <div class="col-md-3">
        
        <div class="panel panel-default">
            <div class="panel-body profile" style="background: #941d63 center center no-repeat;">
                <div class="profile-image">
                    <img src="upload/employee_photo/'.$photo.'" alt="'.$name.'"/>
                </div>
                <div class="profile-data">
                    <div class="profile-data-name">'.$name.'</div>
                    <div class="profile-data-title" style="color: #FFF;"></div>
                </div>
                <div class="profile-controls">
                   
                </div>                                    
            </div>                                
            <div class="panel-body">                                    
                <div class="row">
                    <div class="col-md-12">
                    <input type="text" class="date form-control" id="date_from" name="date_from" value="'.date('d-m-Y').'">
                    </div><br>
                    <div class="col-md-12">
                    <input type="text" class="date form-control" id="date_to" name="date_to" value="'.date('d-m-Y').'">
                    </div><br>

                    <div class="col-md-12" style="padding-top:15px">
                    <input type="button" onclick = "get_report(\'activity_report\',\'date_from\',\'date_to\',\''.$RELATEDID.'\')" value="Search" class="btn btn-primary block" >
                    </div>
                </div>
            </div>

            
<div class="panel-group accordion">

<div class="panel panel-warning">';

            $qry = $conn_me->prepare("SELECT *,group_concat(`id`) as `em` FROM `admin`  group by `user_type`");
            $qry->execute();
            $fetch_qry = $qry->fetchAll(PDO::FETCH_ASSOC);
            
                foreach($fetch_qry AS $fetch_data) {


            
                $content .=' <div class="panel-heading">
                <h4 class="panel-title">
                <a href="#'.$fetch_data['id'].'">
                ' . $fetch_data['user_type']. ' 
                </a>
                </h4>
                </div>                                
                <div class="panel-body" id="'.$fetch_data['id'].'">';
                   
                $content .=' ';
                     
                    $aa = explode(",",$fetch_data['em']);
                    $bb = count($aa);

                    for($no=0 ; $no<$bb; $no++) {
                        
                        $hr_info = SETUP::ADMIN_SETUP($aa[$no]);

                $content .=' <div class="col-md-4 col-xs-4">
                        <a href="Activity/'.$aa[$no].'" class="friend">
                        <img src="upload/employee_photo/'.$hr_info['photo'].'"/>
                        <span>'.$hr_info['hr_name'].'</span>
                        </a>                                            
                    </div>';

                    }




                $content .=' </div>    ';   


                   
                }
                $content .='  
       
                </div>
                </div>
            
        </div>                            
        
    </div>';
 if( $show_activity_line == 'Yes') {
    

    $content .='<div class="col-md-9">

        <!-- START TIMELINE -->
        <div class="timeline">
            
            <!-- START TIMELINE ITEM -->
            <div id="Load_activity_report_div" style="overflow-y: scroll;height:600px;">';
                                                 
            
    
            
    

            
            $content .='</div>                        
            <!-- END TIMELINE ITEM -->
        </div>
        <!-- END TIMELINE -->                            
        
    </div>';
 }else{

    $content .='Select someone ';
 }
$content .= '</div>';




} else if($PAGENAME == 'Purchase-Entry'){

    $content = '';

    $get_code = SETUP::SETUP_CODE('purchase');
    $user_info = SETUP::ADMIN_SETUP($_SESSION['NEWERP_SESS_MEMBER_ID']);

    $related_id = 'new_id';
    $code = $get_code['code'];
    $purchase_no = date('d-m-Y').$code;
    $poster = $_SESSION['NEWERP_SESS_MEMBER_ID'];
    $purches_by = $user_info['hr_name'];
    
   
    $content .= ' <input type="hidden" name="related_id" id="related_id" value="'.$related_id.'" > ';
    $content .= ' <input type="hidden" name="invoice_no" id="invoice_no" value="'.$code.'" > ';


    $content .= '<div class="row">';
    $content .= '<h4 style="text-align:center;">Invoice No. <b style="color:red">'.$code.' </b></h4>';

    $content .= '<h4 style="text-align:center;">Sales By. <b style="color:red">'.$purches_by.' </b></h4>';
    $content .= '<div class="col-md-12">';

    $content .= '<div id="refresh_purches_cart">';
     include("purches_cart.php");
    $content .= '</div>';

    $content .= '</div>';
    $content .= '<div class="col-md-6 form-horizontal">';
    $content .= '<div class="form-group">
    
    <label class="col-md-3 col-xs-12 control-label">Invoice Date</label>
    <div class="col-md-4 col-xs-12"><input type="date" class="form-control" value="" id="purches_date" name="purches_date" >
    
       </div></div>';
    $content .= '<div class="form-group">
    
    <label class="col-md-3 col-xs-12 control-label">Supplier Name</label>
    <div class="col-md-4 col-xs-12">
            <select id="supplier_id" name="supplier_id"   class="form-control select" data-live-search="true">
                <option value="">Select One</option>';

                $qry = $conn_me->prepare("SELECT * FROM `setup_supplier`  ");
                $qry->execute();
                $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
                foreach ($fetch_list as $fetch) { 
           $content .= '<option value="'.$fetch['id'].'">'.$fetch['supplier_name'].'
            </option>';
         }
    
       $content .='</select>
        
    </div>';

    $content .=' <label class="switch">
    <input type="checkbox"  id="target1" onchange=HIDE_AND_SHOW(\'target_div_1\',\'target1\',\'supplier_id\'); name="target1" value="1"  />
    <span></span>
</label>';


    $content .= '</div>';

    $content .= '<div id="target_div_1" style="display:none;padding-bottom:15px;">
    <div class="form-group" >
    <label class="col-md-3 col-xs-12 control-label">Supplier</label>
    <div class="col-md-4 col-xs-12">
       <input type="text" name="new_supplier_name" id="new_supplier_name"  class="form-control" value=""></div>
    </div>

    <div class="form-group" >
    <label class="col-md-3 col-xs-12 control-label">Address</label>
    <div class="col-md-4 col-xs-12">
       <input type="text" name="new_supplier_address" id="new_supplier_address"  class="form-control" value=""></div>
    </div>


    <div class="form-group" >
    <label class="col-md-3 col-xs-12 control-label">Mobile</label>
    <div class="col-md-4 col-xs-12">
       <input type="text" name="new_supplier_mobile" id="new_supplier_mobile"  class="form-control" value=""></div>
    </div>

    <div class="form-group" >
    <label class="col-md-3 col-xs-12 control-label">Owner</label>
    <div class="col-md-4 col-xs-12">
       <input type="text" name="new_supplier_owner" id="new_supplier_owner"  class="form-control" value=""></div>
    </div>
    <div class="form-group" >
    <label class="col-md-3 col-xs-12 control-label">Email</label>
    <div class="col-md-4 col-xs-12">
       <input type="text" name="new_supplier_email" id="new_supplier_email"  class="form-control" value=""></div>
    </div>

</div>

';







    $content .= '</div>';// end of 1st  6 grid


    $content .= '<div class="col-md-6 form-horizontal">';


    $content .= '<div class="form-group">
    
    <label class="col-md-3 col-xs-12 control-label">Warehouse Name</label>
    <div class="col-md-4 col-xs-12">
            <select id="warehouse_id" name="warehouse_id"   class="form-control select" data-live-search="true">
                <option value="">Select One</option>';
    
                $qry = $conn_me->prepare("SELECT * FROM `setup_warehouse` where status = 1 ");
                $qry->execute();
                $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
                foreach ($fetch_list as $fetch) { 
           $content .= '<option value="'.$fetch['id'].'">'.$fetch['name'].'
            </option>';
         }
    
       $content .='</select>
        
    </div>';
    
    $content .=' <label class="switch">
    <input type="checkbox"  id="target2" onchange=HIDE_AND_SHOW(\'target_div_2\',\'target2\',\'warehouse_id\'); name="target2" value="1"  />
    <span></span>
    </label>';
    
    
    $content .= '</div>';
    
    $content .= '<div id="target_div_2" style="display:none;padding-bottom:15px;">
    <div class="form-group" >
    <label class="col-md-3 col-xs-12 control-label">Warehouse Name</label>
    <div class="col-md-4 col-xs-12">
       <input type="text" name="new_warehouse_name" id="new_warehouse_name"  class="form-control" value=""></div>
    </div>
    
    <div class="form-group" >
    <label class="col-md-3 col-xs-12 control-label">Address</label>
    <div class="col-md-4 col-xs-12">
       <input type="text" name="new_warehouse_address" id="new_warehouse_address"  class="form-control" value=""></div>
    </div>
    
    
    <div class="form-group" >
    <label class="col-md-3 col-xs-12 control-label">Mobile</label>
    <div class="col-md-4 col-xs-12">
       <input type="text" name="new_supplier_mobile" id="new_supplier_mobile"  class="form-control" value=""></div>
    </div>
    
    <div class="form-group" >
    <label class="col-md-3 col-xs-12 control-label">Height</label>
    <div class="col-md-4 col-xs-12">
       <input type="text" name="new_warehouse_height" id="new_warehouse_height"  class="form-control" value=""></div>
    </div>
    <div class="form-group" >
    <label class="col-md-3 col-xs-12 control-label">Width</label>
    <div class="col-md-4 col-xs-12">
       <input type="text" name="new_warehouse_width" id="new_warehouse_width"  class="form-control" value=""></div>
    </div>
    <div class="form-group" >
    <label class="col-md-3 col-xs-12 control-label">Length</label>
    <div class="col-md-4 col-xs-12">
       <input type="text" name="new_warehouse_length" id="new_warehouse_length"  class="form-control" value=""></div>
    </div>
    </div>
    
    ';
    
$content .= '<div class="form-group">
    
<label class="col-md-3 col-xs-12 control-label">Product Category</label>
<div class="col-md-4 col-xs-12">
        <select id="category_id" name="category_id"   class="form-control select" data-live-search="true">
            <option value="">Select One</option>';

            $qry = $conn_me->prepare("SELECT * FROM `setup_category`  ");
            $qry->execute();
            $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
            foreach ($fetch_list as $fetch) { 
       $content .= '<option value="'.$fetch['id'].'">'.$fetch['category'].'
        </option>';
     }

   $content .='</select>
    
</div>';

$content .=' <label class="switch">
<input type="checkbox"  id="target2" onchange=HIDE_AND_SHOW(\'target_div_2\',\'target2\',\'warehouse_id\'); name="target2" value="1"  />
<span></span>
</label>';


$content .= '</div>';

$content .= '<div id="target_div_2" style="display:none;padding-bottom:15px;">
<div class="form-group" >
<label class="col-md-3 col-xs-12 control-label">Warehouse Name</label>
<div class="col-md-4 col-xs-12">
   <input type="text" name="new_warehouse_name" id="new_warehouse_name"  class="form-control" value=""></div>
</div>

<div class="form-group" >
<label class="col-md-3 col-xs-12 control-label">Address</label>
<div class="col-md-4 col-xs-12">
   <input type="text" name="new_warehouse_address" id="new_warehouse_address"  class="form-control" value=""></div>
</div>


<div class="form-group" >
<label class="col-md-3 col-xs-12 control-label">Mobile</label>
<div class="col-md-4 col-xs-12">
   <input type="text" name="new_supplier_mobile" id="new_supplier_mobile"  class="form-control" value=""></div>
</div>

<div class="form-group" >
<label class="col-md-3 col-xs-12 control-label">Height</label>
<div class="col-md-4 col-xs-12">
   <input type="text" name="new_warehouse_height" id="new_warehouse_height"  class="form-control" value=""></div>
</div>
<div class="form-group" >
<label class="col-md-3 col-xs-12 control-label">Width</label>
<div class="col-md-4 col-xs-12">
   <input type="text" name="new_warehouse_width" id="new_warehouse_width"  class="form-control" value=""></div>
</div>
<div class="form-group" >
<label class="col-md-3 col-xs-12 control-label">Length</label>
<div class="col-md-4 col-xs-12">
   <input type="text" name="new_warehouse_length" id="new_warehouse_length"  class="form-control" value=""></div>
</div>
</div>

';


   


    $content .= '</div>'; // end of 2nd  6 grid


    $content .= '</div>'; // end of row


        
    }else if($PAGENAME == 'Prefix'){



        $content = '';

        $content .= '<div class="row">
        <div class="col-md-12">
        <div class="panel-heading">
<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(\' :: Prefix List\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel(\'Prefix-List\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div>                                    

</div>
            <div class="panel panel-default">
                <div class="panel-body" id="load_table">
               
                <table class="table datatable" id="MSalary">
                <thead>
                <th>Sl</th>
                <th>Section</th>
                <th>Prefix</th>
                <th>Action</th>
                </thead>
                    <tbody>'; 
        $sl =1;
        $qry = $conn_me->prepare("SELECT * FROM `invoice_prefix`  ORDER BY `id` ASC");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
     
            foreach($fetch_list AS $fetch) {
        
    
        
                $content .= '<tr id="tr_'.$fetch['id'].'">
                <td>'.$sl++.'</td>
                <td><b style="color:blue">'.$fetch['section_name'].'</b> </td>
                <td><input type="text" id="prefix'.$fetch['id'].'" value="'.$fetch['prefix'].'" class="form-control"> </td>
                <td><input type="button" onclick="change_prefix(\''.$fetch['id'].'\');" class="btn btn-danger" value="Change"></td>
               </tr>';
            }
        
                        
                          
        $content .= '</tbody> 
              </table>
              </div>
                    
            </div>
        
        </div>
        </div>';


    }else if($PAGENAME == 'Update-Price'){


        $content = '';

        $content .= '<div class="row">
        <div class="col-md-12">
        
        <div class="panel-heading">
<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(\' :: Price List\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel(\'Price-List\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div>                                    

</div>


            <div class="panel panel-default">
                <div class="panel-body" id="load_table">
               
                <table class="table datatable" id="MSalary">
                <thead>
                <th>Sl</th>
                <th>Product</th>
                <th>Price</th>
                <th>VAT % </th>
                <th>Discount</th>
                <th>Action</th>
                </thead>
                    <tbody>'; 
        $sl =1;
        $qry = $conn_me->prepare("SELECT * FROM `setup_product` where `in_service` = 'checked'  ORDER BY `id` ASC");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        if ($qry->rowCount() > 0)
        {
            foreach($fetch_list AS $fetch) {
        
                $category_info = SETUP::SETUP_CATEGORY($fetch['category_id']);
        
        
                $content .= '<tr id="tr_'.$fetch['id'].'">
                <td>'.$sl++.'</td>
                <td><b style="color:blue">'.$fetch['product_name'].'</b><br><b style="color:orange">Category: </b>'.$category_info['category'].' </td>
                <td><input type="number" id="product_price'.$fetch['id'].'" value="'.$fetch['sales_rate'].'" class="form-control"> </td>
                <td><input type="number" id="vat_percentage'.$fetch['id'].'" value="'.$fetch['vat_percentage'].'" class="form-control"> </td>
                <td><input type="number" id="discount'.$fetch['id'].'" value="'.$fetch['discount'].'" class="form-control"> </td>
                <td><input type="button" onclick="change_priceANDvat(\''.$fetch['id'].'\');" class="btn btn-danger" value="Update"></td>
               </tr>';
            }
        
        }
                        
                          
        $content .= '</tbody> 
              </table>
              </div>
                    
            </div>
        
        </div>
        </div>';


}else if($PAGENAME == 'Product-Setup'){

    include('xml_product_list.php');
    $xml_product_list = simplexml_load_file("xml_productList.xml");

    if($RELATEDID == 'New'){
        $product_code = SETUP::SETUP_CODE('setup_product');

        $product_name = '';
        $category_id = '';
        $unit_id = '';
        $pcs_in_cartoon = '';
        $code = $product_code['code'];
        $sales_rate = '';
        $wholesale_rate = '';
        $in_service = 'checked';
        $related_id = 'new_id';
        $safety_stock = '0.00';
        $check_value = 1;


        }else{
        
        $DATA  = SETUP::SETUP_PRODUCT($RELATEDID);
        
        $product_name = $DATA['product_name'];
        $related_id =  $DATA['id'];
        $category_id = $DATA['category_id'];
        $unit_id = $DATA['unit_id'];
        $code = $DATA['code'];
        $sales_rate = $DATA['sales_rate'];
        $wholesale_rate = $DATA['wholesale_rate'];
        $pcs_in_cartoon = $DATA['pcs_in_cartoon'];
        $in_service = $DATA['in_service'];
        $safety_stock = $DATA['safty_stock'];
        $check_value =  $DATA['check_value'];
        
        
        }
            $content = '<div class="row">
            <div class="col-md-12">
        
                <div class="panel panel-default">
                    <div class="panel-body">
                    <form id="myform">
                    
                    <div class="alert alert-success alert-dismissible" id="success" style="display:none;">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                  </div>
        
                    <table class="table table-hover table-condensed table-striped table-bordered">
                        <tbody> 
                        <input type="hidden" name="related_id" id="related_id" value="'.$related_id.'" > 
                        <tr>
                        <th>Product Code  </th>
                        <td><input type="text" READONLY  class="form-control" value="'.$code.'" name="product_code" id="product_code"></td>
                        </tr>

                        <tr>
                        <th>Product Name</th>
                        <td><input type="text" class="form-control" value="'.$product_name.'" name="product_name" id="product_name"></td>
                        </tr>

                        <tr>
                        <th>Category   </th>
                        <td>';

                        $content .= '<div class="form-group">
    
                        <div class="col-md-10 col-xs-12">
                                <select id="category_id" name="category_id"   class="form-control select" data-live-search="true">
                                <option value="">Select One</option>';
                                    $qry = $conn_me->prepare("SELECT * FROM `setup_category`  ORDER BY `id` ASC");
                                    $qry->execute();
                                    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
                                     foreach($fetch_list AS $fetch) { 
                                        $content .=  '<option ';
                                        if($category_id == $fetch['id']){ 
                                            $content .=' selected="selected"';
                                        }else { }
                                        $content .=' value="'.$fetch['id'].'">'.$fetch['category'].'</option>'; 
                                     } 
                                     $content .= '</select>
                    
                            
                        </div>';
                        
                        $content .='<label class="switch">
                        <input type="checkbox"  id="target3" onchange=HIDE_AND_SHOW(\'target_div_3\',\'target3\',\'category_id\'); name="target3" value="1"  />
                        <span></span>
                        Add</label>';
                        
                        
                        $content .= '</div>';
                        
                        $content .= '<div id="target_div_3" style="display:none;padding-bottom:15px;">
                        <div class="form-group" >
                        <label class="col-md-2 col-xs-12 control-label">Category Name</label>
                        <div class="col-md-8 col-xs-12">
                           <input type="text" name="new_category_name" id="new_category_name"  class="form-control" value=""></div>
                        </div>
                        </div>';
                        $content .= '</td>
                        </tr>
                        <tr>
                        <th>Unit  </th>
                        <td><div class="form-group">
    
                        <div class="col-md-10 col-xs-12">
                                <select id="unit_id" name="unit_id"   class="form-control select" data-live-search="true">
                                <option value="">Select One</option>';
                                    $qry = $conn_me->prepare("SELECT * FROM `setup_unit`  ORDER BY `id` ASC");
                                    $qry->execute();
                                    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
                                     foreach($fetch_list AS $fetch) { 
                                        $content .=  '<option ';
                                        if($unit_id == $fetch['id']){ 
                                            $content .=' selected="selected" ';
                                        }else { }
                                        $content .=  ' value="'.$fetch['id'].'">'.$fetch['unit'].'</option>'; 
                                     } 
                                     $content .= '</select>
                    
                            
                        </div></td>


                        </tr>


                        <tr>
                        <th>Pcs Per Carton </th>
                        <td><input type="text" class="form-control" value="'.$pcs_in_cartoon.'" name="pcs_in_cartoon" id="pcs_in_cartoon"></td>


                        </tr>

                        <tr>
                        <th>Sales Rate   </th>
                        <td><input type="text" class="form-control" value="'.$sales_rate.'" name="sales_rate" id="sales_rate"></td>


                        </tr>
                        <tr>
                        <th></th>
                        <td><input type="hidden"  class="form-control" value="0.00" name="wholesale_rate" id="wholesale_rate"></td>

 </tr>

 <tr>
                        <th>Safety Stock   </th>
                        <td><input type="number" class="form-control" value="'.$safety_stock.'" name="safety_stock" id="safety_stock"></td>

 </tr>

                      
                        <tr>
                        <th>In Service</th>
                        <td><label class="switch">
                        <input type="checkbox"   id="in_service" name="in_service" value="'.$check_value.'" '.$in_service.' />
                        
                        <span></span>
                    </label></td>
                        
                        
                    </tr>

                            <tr>
                                
                                <td style="text-align:center;" colspan=2><input type="button" name="save_product" id="save_product" class="btn btn-primary" value="Save Product"></td>
                            </tr>
                      </tbody> 
                  </table>
                </form>  
                        
                </div>
        
            </div>
        </div>';
        
        $content .= '<div class="row">
        <div class="col-md-12">
        
        <div class="panel-heading">
<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(\' :: Product List\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel(\'Product-List\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div>                                    

</div>


            <div class="panel panel-default">
                <div class="panel-body" id="load_table">
               
                <table class="table datatable" id="MSalary">
                <thead>
                <th>Sl</th>
                                <th>Code</th>

                <th>Product Name</th>
                <th>Category</th>
                <th>In Service</th>
                <th>Action</th>
                </thead>
                    <tbody>'; 
                    $sl=1;
                    $qry = $conn_me->prepare("SELECT A.*,B.`category` FROM `setup_product`  A JOIN `setup_category` B ON (A.`category_id` = B.`id`) ");
                    $qry->execute();
                    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($fetch_list as $value) { 

                        if($value['in_service'] == 'checked') {
                            $in_service = 'Active';
                        }else{ 
                            $in_service = 'Deactive'; 
                        }
                $content .= '<tr>
                <th>'.$sl++.'</th>
                                <th>P'.$value['code'].'</th>

                <th>'.$value['product_name'].'</th>
                <th>'.$value['category'].'</th>
                <th>'.$in_service.'</th>';

                if($_SESSION['USER_TYPE'] == 'Admin'){
                $content .= '<td><a href="Setup/Product-Setup/'.$value['id'].'" ><i class="fa fa-edit danger"></i><a></td>';
                }else{
                    $content .= '<td></td>';
                }
              $content .= ' </tr>';
            }
        
    
                        
                          
        $content .= '</tbody> 
              </table>
              </div>
                    
            </div>
        
        </div>
        </div>';

}else if($PAGENAME == 'User-Management'){
    
    if($RELATEDID == 'New'){
       
     $permission_management = PERMISSION::pageCreatePermission($_SESSION['USER_TYPE'],'Save Data','create_user','');

        $active_class1 = 'active';
        $active_class2 = '';
        $related_id = 'new_id';
        $employee_id = '';
        $user_type = '';

        $brunch_id = '';
        $user_password = '';
        $employee_id = '';
        $employee_name = '';
        $username = '';
        }else{
        $info = SETUP::ADMIN_DATA_BY_EMPLOYEEID($RELATEDID);
        $permission_management = PERMISSION::pageCreatePermission($_SESSION['USER_TYPE'],'Update Data','create_user','');

        $related_id = $info['id'];
        $employee_id =  $info['employee_id'];
        $user_type = $info['user_type'];
        $employee_name = $info['employee_name'];
        $username = $info['username'];
        $brunch_id =  $info['brunch_id'];
        $user_password =  $info['dypricpt_pass'];

        $active_class2 = 'active';
        $active_class1 = '';
        }

$content = '';

$content .=  


'<div class="row">
    <div class="col-md-12">
    
    
        <form class="form-horizontal">
                      <input type="hidden" id="related_id" name ="related_id" value="'.$related_id.'">    
            <div class="panel panel-default tabs">                            
                <ul class="nav nav-tabs" role="tablist">
                    <li class="'.$active_class1.'"><a href="#tab-first" role="tab" data-toggle="tab"> User Info</a></li>
                    <li  class="'.$active_class2.'"><a href="#tab-third" role="tab" data-toggle="tab">Per</a></li>
                    <li><a href="Setup/User-Management/New" >Create New </a></li>

                </ul>
                <div class="panel-body tab-content">
                    <div class="tab-pane '.$active_class1.'" id="tab-first">';
                       
                    if($employee_id == ''){
                        $content .= '<div class="form-group">
                            <label class="col-md-3 col-xs-12 control-label">Employee Name</label>
            <div class="col-md-6 col-xs-12"> 
            
                <select data-live-search="true" onchange="get_employee_details(this.value);" class="select form-control" required id="employee_id" name="employee_id" data-rel="chosen">
                <option value="">Select One</option>';
        $qry = $conn_me->prepare("SELECT * FROM `setup_employee`  ORDER BY `id` ASC");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
         foreach($fetch_list AS $fetch) { 
            $content .=  '<option ';
            if($employee_id == $fetch['id']){
                $content .=  'selected="selected"';
            }else{

            }
            $content .= ' value="'.$fetch['id'].'">'.$fetch['code'].' - '.$fetch['name'].'</option>'; 
         } 
         $content .= '</select> </div>
         </div> ';
            }else{

                $content .= '<div class="form-group">
                <label class="col-md-3 col-xs-12 control-label">Employee Name</label>
                <div class="col-md-6 col-xs-12">                                            
                <input type="hidden" id="employee_id" value="'.$employee_id.'"> <input style="color:green;" id="employee_name" readonly class="form-control" type="text"  value="'.$employee_name.'">
                </div>
                </div>

                <div class="form-group">
                <label class="col-md-3 col-xs-12 control-label">username</label>
                <div class="col-md-6 col-xs-12">                                            
                <input style="color:green;" id="username"  class="form-control" type="text"  value="'.$username.'">
                </div>
                </div>';

              
                
            }                                                                                                                                                       
                                     
            

            $content .= '

                        <div id="load_emailoyee_details" style="padding-bottom:15px;"></div>

                       
                        <div class="form-group">
                        <label class="col-md-3 col-xs-12 control-label">Password</label>
                        <div class="col-md-6 col-xs-12">                                            
                        <input style="color:green;" id="user_password" class="form-control" type="text"  value="'.$user_password.'">
                        </div>
                        </div>
                        

                        <div class="form-group">
                            <label class="col-md-3 col-xs-12 control-label">Select Branch</label>
            <div class="col-md-6 col-xs-12">                                                                                                                                                        
                                <select data-live-search="true" class="select form-control" required id="brunch_id" name="brunch_id" data-rel="chosen">
                                    <option value="">Select One</option>';
                            $qry = $conn_me->prepare("SELECT * FROM `setup_brunch` where  status = 'Active' ");
                            $qry->execute();
                            $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
                             foreach($fetch_list AS $fetch) { 
                                $content .=  '<option ';
                                if($brunch_id == $fetch['id']){
                                    $content .=  'selected="selected"';
                                }else{
                
                                }
                                $content .= ' value="'.$fetch['id'].'">'.$fetch['brunch'].'</option>'; 
                             } 
                             $content .= '</select>                                                  
                            </div>
                        </div>

                        <div class="form-group">
                        <label class="col-md-3 col-xs-12 control-label">User Type</label>
        <div class="col-md-6 col-xs-12">                                                                                                                                                        
                            <select data-live-search="true" class="select form-control" required id="user_type" name="user_type">
                                <option value="">Select One</option>';
                                
                              $content .='<option '; if($user_type == 'Admin'){ $content .=  'selected="selected"'; } $content .=  ' value="Admin">Admin</option>';
                              $content .='<option '; if($user_type == 'Editor'){ $content .=  'selected="selected"'; } $content .=  ' value="Editor">Editor</option>';
                              $content .='<option '; if($user_type == 'Viewer'){ $content .=  'selected="selected"'; } $content .=  ' value="Viewer">Viewer</option>';


                            
                         $content .= '</select>                                                  
                        </div>
                    </div>


                    </div>

                                                       
                    <div class="tab-pane '.$active_class2.'" id="tab-third">';
                        
                    $content .= '<div class="row">
        <div class="col-md-12">
        
            <div class="panel panel-default">
                <div class="panel-body" id="load_table">';
               
            
if($RELATEDID == 'New'){
    $content .= '<b style="color:red">Need To Save The User First</b>';

}else{


    $content .= '<input type="hidden" id="employee_id" value="'.$employee_id.'">';

    
    $content .= '<div class="row">

    <div class="col-md-12">';
    $sl=1;
    $qry = $conn_me->prepare("SELECT * FROM `menu_list` where `parent_id` = 0  ORDER BY `sort` ASC ");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
    foreach($fetch_list AS $fetch) {

$qry3 = $conn_me->prepare("SELECT * FROM `menu_permission` where `employee_id` = '".$employee_id."' AND  `menu_id` = '".$fetch['id']."' ");
$qry3->execute();
$fetch_list3 = $qry3->fetch(PDO::FETCH_ASSOC);
if($qry3->rowCount() > 0 ){   
$checked =  $fetch_list3['view_check'];
}else{
$checked = '';
}


        $content .= '<input type="hidden" id="main_menu_id'.$fetch['id'].'" value="'.$fetch['id'].'">';    

    $content .= '<div class="panel-group accordion">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class="panel-title" id="title'.$fetch['section'].'">
                        <a href="#'.$fetch['id'].'">
                           '.$fetch['menu'].' #'.$sl++.' 
                        </a>
                    </h4>
                    <label class="switch switch-small pull-right"><input '.$checked .' onchange="give_main_access(\''.$fetch['section'].'\');"  id="give_main_access'.$fetch['section'].'" type="checkbox"  /><span></span></label>
                </div>                                
                <div class="panel-body panel-body-close" id="'.$fetch['id'].'">';
                   
                $content .= '<ul class="list-group border-bottom">';
                $qry2 = $conn_me->prepare("SELECT * FROM `menu_list` where `section` = '".$fetch['section']."' AND `parent_id` <> 0  ORDER BY `sort`  ");
                $qry2->execute();
                $fetch_list2 = $qry2->fetchAll(PDO::FETCH_ASSOC);
                foreach($fetch_list2 AS $fetch2) {
             
                    
$content .= '<input type="hidden" id="menu_id'.$fetch2['id'].'" value="'.$fetch2['id'].'">';    


$qry33 = $conn_me->prepare("SELECT * FROM `menu_permission` where `employee_id` = '".$employee_id."' AND  `menu_id` = '".$fetch2['id']."' ");
$qry33->execute();
$fetch_list33 = $qry33->fetch(PDO::FETCH_ASSOC);
if($qry33->rowCount() > 0 ){   

$checked2 =  $fetch_list33['view_check'];
}else{
$checked2 = '';
}



$content .= '<li class="list-group-item" id="single_title'.$fetch2['id'].'"> '.$fetch2['menu'].' <label class="switch switch-small pull-right"><input '.$checked2 .' onchange="givesingleaccess(\''.$fetch2['id'].'\');"  id = "give_single_access'.$fetch2['id'].'" type="checkbox" /><span></span></label></li>';
                   
                  
                
            }
            $content .= '</ul>    '; 
                $content .= '</div>                                
            </div>
            
        
        </div>';
    
    }}
        
        $content .= '    </div>


</div>';



             $content .=' </div>
                    
            </div>
        
        </div>
        </div>';


                   $content .=' </div>
                    <div class="tab-pane" id="tab-fourth">
                    </div>
                </div>
                <div class="panel-footer">';  
                $content .= $permission_management['save_update_buton'];   
                $content .'</div>
            </div>                                
        
        </form>
        
    </div>
</div>              ';      

$content .= '<div class="row">
<div class="col-md-12">
<div class="panel-heading">
<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(\' :: User List\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel(\'User-List\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div>                                    

</div>
    <div class="panel panel-default">
        <div class="panel-body" id="load_table">
       
        <table class="table datatable" id="MSalary">
        <thead>
        <th>Sl</th>
        <th>Code</th>
        <th>Employee Name</th>
        <th>Employee Type</th>
        <th>Action</th>
        </thead>
            <tbody>'; 
$sl =1;
$qry = $conn_me->prepare("SELECT * FROM `admin`  ORDER BY `id` ASC ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
if ($qry->rowCount() > 0)
{
    foreach($fetch_list AS $fetch) {
        $get_employeeid = SETUP::ADMIN_SETUP($fetch['id']);

        $content .= '<tr>
        <th>'.$sl++.'</th>
        <th>'.$get_employeeid['employee_code_with_prefix'] .' </th>
        <th>'. $get_employeeid['hr_name'].'</th>
        <th>'. $fetch['user_type'].'</th>
        <th><a href="Setup/User-Management/'.$get_employeeid['employee_id'].'">Edit</a></th>
       </tr>';
    }

}
                
                  
$content .= '</tbody> 
      </table>
      </div>
            
    </div>

</div>
</div>';



    
}else if($PAGENAME == 'Block-Sales'){


    include('xml_product_list.php');
    
    $content = '';
    $content .= '<div class="row">
    <div class="col-md-12">
    
    <div class="panel-heading">
<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(\' :: Block-Sales\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel(\'Block-Sales\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div>                                    

</div>


        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           
            <table class="table datatable" id="MSalary">
            <thead>
            <th>Sl</th>
            <th>Code</th>
            <th>Product</th>
            <th>Status</th>
            <th>Action</th>
            </thead>
                <tbody>'; 
    $sl =1;
    $qry = $conn_me->prepare("SELECT * FROM `setup_product` where `in_service` = 'checked'  ORDER BY `id` ASC");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
    if ($qry->rowCount() > 0)
    {
        foreach($fetch_list AS $fetch) {
            $unit_info = SETUP::SETUP_UNIT($fetch['unit_id']);
            if($fetch['in_service'] == 'checked'){$status='Active';$checked='checked';}else{$status='Deactive';$checked='';}
             
            $content .= '<tr>
            <th>'.$sl++.'</th>
            <th>'.$fetch['product_name'].'</th>
            <th>Unit: '.$unit_info['unit'].'<br>Pcs in a cartoon: '.$fetch['pcs_in_cartoon'].' </th>
            <th>'.$status.'</th>
            <td><label class="switch switch-small"><input '.$fetch['in_service'].'  onchange="block_salles(\''.$fetch['id'].'\');" id="user_sales'.$fetch['id'].'" type="checkbox"  '.$checked.' /><span></span></label></td>
           </tr>';

            
        }
    
    }
                    
                      
    $content .= '</tbody> 
          </table>
          </div>
                
        </div>
    
    </div>
    </div>';



}else if($PAGENAME == 'Block-User'){

    if($RELATEDID == 'New'){
        $QUERY1 = "";
        $QUERY2 = "";
    }else{
        $RELATEDID = preg_quote($RELATEDID, '/');

        $QUERY2 = " `name` REGEXP '".$RELATEDID."' AND ";
        $QUERY1 = " where `name` REGEXP '".$RELATEDID."'  ";
    }
   

    $content = '<div class="row" id="change_load_content">';

    $qry = $conn_me->prepare("SELECT `present_department` FROM `setup_employee` $QUERY1  GROUP BY `present_department` order by `id`");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
    foreach($fetch_list AS $fetch) {

    $info_department = SETUP::SETUP_DEPARTMENT($fetch['present_department']);
    $content .= '<div class="col-md-4">';

    $content .= '<table class="table table-hover table-condensed table-striped table-bordered">
    ';
    $content .= '<tr ><th colspan="4" style="text-align:center;color:red">Department :  '. $info_department['department'].'</th></tr>';
    $content .= '<tr>
    <td>#</td>
    <td>Employee</td>
    <td>Status</td>
    <td>Action</td>
    </tr>';
    $sl = 0;
    $qry2 = $conn_me->prepare("SELECT A.`name`,A.`hr_status`,A.`id` FROM `setup_employee` A   where $QUERY2 A.`present_department` = '".$fetch['present_department']."'");
    $qry2->execute();
    $fetch_list2 = $qry2->fetchAll(PDO::FETCH_ASSOC);
    foreach($fetch_list2 AS $fetch2) {

        $count = $sl + 1;
        $sl++;
        
       if($fetch2['hr_status'] == 'Active') { $status = "online";$in_service = 'checked'; }else{ $status = "offline"; $in_service = '';}
        $content .= '
        <tr>
        <th>'.$count.'</th>
        <th>'.$fetch2['name'].'</th>
        <th><div class="list-group-status status-'.$status.'"></div></th>
        <td><label class="switch switch-small"><input onchange="block_user(\''.$fetch2['id'].'\');" type="checkbox"  '.$in_service.' id="user_block'.$fetch2['id'].'" /><span></span></label></td>

        </td>
        </tr>';
      
    }
       

    $content .= '</table>';


    $content .= '</div>';
}
    $content .= '</div>';




}else if($PAGENAME == 'Section-Setup'){

    

if($RELATEDID == 'New'){

    $section_name = '';
    $related_id = 'new_id';
    
    }else{
    
    $DATA  = SETUP::SETUP_SECTION($RELATEDID);
    
    $section_name = $DATA['section'];
    $related_id =  $DATA['id'];
    
    
    }
        $content = '<div class="row">
        <div class="col-md-12">
    
            <div class="panel panel-default">
                <div class="panel-body">
                <form id="myform">
                
                <div class="alert alert-success alert-dismissible" id="success" style="display:none;">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
              </div>
    
                <table class="table table-hover table-condensed table-striped table-bordered">
                    <tbody> <tr>
                            <th>Section Name <input type="hidden" name="related_id" id="related_id" value="'.$related_id.'" > </th>
                            <td><input type="text" class="form-control" value="'.$section_name.'" name="section_name" id="section_name"></td>
                            
                            
                        </tr>
                        
                        <tr>
                            
                            <td style="text-align:center;" colspan=2><input type="button" name="save_section" id="save_section" class="btn btn-primary" value="Save Section"></td>
                        </tr>
                  </tbody> 
              </table>
            </form>  
                    
            </div>
    
        </div>
    </div>';
    
    $content .= '<div class="row">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="panel-body" id="load_table">
           
            <table class="table table-hover table-condensed table-striped table-bordered">
            <thead>
            <th>Sl</th>
            <th>Section</th>
            <th>Action</th>
            </thead>
                <tbody>'; 
    $sl =1;
    $qry = $conn_me->prepare("SELECT * FROM `setup_section`  ORDER BY `id` ASC");
    $qry->execute();
    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
    if ($qry->rowCount() > 0)
    {
        foreach($fetch_list AS $fetch) {
    
    
    
            $content .= '<tr>
            <th>'.$sl++.'</th>
            <th>'.$fetch['section'].'</th>
            <td><a href="Setup/Section-Setup/'.$fetch['id'].'" ><i class="fa fa-edit danger"></i><a></td>
           </tr>';
        }
    
    }
                    
                      
    $content .= '</tbody> 
          </table>
          </div>
                
        </div>
    
    </div>
    </div>';
}else if($PAGENAME == 'Account-Head-List'){

    $content ='   
    <div class="row">
    <div class="col-md-12">
                                            
            <div class="panel panel-default tabs">       


<ul class="nav nav-tabs" role="tablist">
    <li class="active"><a href="#tab-first" role="tab" data-toggle="tab">Ledger Head</a></li>
    <li><a href="#tab-second" role="tab" data-toggle="tab">Income Head</a></li>
    <li><a href="#tab-third" role="tab" data-toggle="tab">Expense Head</a></li>

</ul>
<div class="panel-body tab-content">
    <div class="tab-pane active" id="tab-first">
       

<div class="row">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="table-responsive" id="load_table">
           
        <table class="table datatable table-hover table-condensed table-striped table-bordered">
            <thead>
            <th>Sl</th>
            <th>Ledger Head</th>
            <th>Action</th>
            </thead>
                <tbody>'; 
    $sl =1;
    $count1 = 0;
    $qry1 = $conn_me->prepare("SELECT * FROM `setup_ladger_head` where `special_id` = 'NO'   ORDER BY `id` ASC");
    $qry1->execute();
    $fetch_list1 = $qry1->fetchAll(PDO::FETCH_ASSOC);

        foreach($fetch_list1 AS $fetch1) {
            $count1 = $count1 + 1;
            $content .= ' <input value="'.$fetch1['id'].'"  type="text" style="display:none" name="tr_id" id="tr_id'.$count1.'"  class="form-control tr_id"/>';

    
            $content .= '<tr>
            <th>'.$sl++.'</th>
            <th>'.$fetch1['name'].'</th>
            <th> <button type="button" class="btn btn-danger btn-rounded btn-sm" id="#exampleModalCenter" onclick="modal_wihout_refresh(\'EDIT-LEDGER\',\'tr_id'.$count1.'\',\'Edit Ledger\');"><i class="fa fa-pencil"></i></button></th>';

             $content .= ' </tr>';
        }
    

                    
                      
    $content .= '</tbody> 
          </table>
          </div>
                
        </div>
    
    </div>
    </div>
    
    

    
    </div>
    <div class="tab-pane" id="tab-second">
    
      <div class="row">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="table-responsive" id="load_table">
           
        <table class="table datatable table-hover table-condensed table-striped table-bordered">
            <thead>
            <th>Sl</th>
            <th>Income Head</th>
            <th>Action</th>
            </thead>
                <tbody>'; 
    $sl =1;
    $count2 = 0;
    $qry2 = $conn_me->prepare("SELECT * FROM `setup_ac_head` where `account_type` = 'INCOME' AND `special_id` = 'NO'   ORDER BY `id` ASC");
    $qry2->execute();
    $fetch_list2 = $qry2->fetchAll(PDO::FETCH_ASSOC);
    
        foreach($fetch_list2 AS $fetch2) {
    
            $count2 = $count2 + 1;

            $content .= ' <input value="'.$fetch2['id'].'"  type="text" style="display:none" name="trr_id" id="trr_id'.$count2.'"  class="form-control trr_id"/>';

            $content .= '<tr>
            <th>'.$sl++.'</th>
            <th>'.$fetch2['account_head'].' </th>
            <th> <button type="button" class="btn btn-danger btn-rounded btn-sm" id="#exampleModalCenter" onclick="modal_wihout_refresh(\'EDIT-ACCOUNT-HEAD\',\'trr_id'.$count2.'\',\'Edit Account Head\');"><i class="fa fa-pencil"></i></button></th>

              </tr>';
        }
    
    
                    
                      
    $content .= '</tbody> 
          </table>
          </div>
                
        </div>
    
    </div>
    </div>
    
    

    </div>     
    
    <div class="tab-pane" id="tab-third">
    
    <div class="row">
    <div class="col-md-12">
    
        <div class="panel panel-default">
            <div class="table-responsive" id="load_table">
           
        <table class="table datatable table-hover table-condensed table-striped table-bordered">
            <thead>
            <th>Sl</th>
            <th>Income Head</th>
            <th>Action</th>
            </thead>
                <tbody>'; 
    $sl =1;
    $count3 = 0;
    $qry3 = $conn_me->prepare("SELECT * FROM `setup_ac_head` where `account_type` = 'EXPENSE' AND `special_id` = 'NO'   ORDER BY `id` ASC");
    $qry3->execute();
    $fetch_list3 = $qry3->fetchAll(PDO::FETCH_ASSOC);
    
        foreach($fetch_list3 AS $fetch3) {
    
            $count3 = $count3 + 1;

            $content .= ' <input value="'.$fetch3['id'].'"  type="text" style="display:none" name="trrr_id" id="trrr_id'.$count3.'"  class="form-control trrr_id"/>';

            $content .= '<tr>
            <th>'.$sl++.'</th>
            <th>'.$fetch3['account_head'].' </th>
            <th> <button type="button" class="btn btn-danger btn-rounded btn-sm" id="#exampleModalCenter" onclick="modal_wihout_refresh(\'EDIT-ACCOUNT-HEAD\',\'trrr_id'.$count3.'\',\'Edit Account Head\');"><i class="fa fa-pencil"></i></button></th>

              </tr>';
        }
    
    
                    
                      
    $content .= '</tbody> 
          </table>
          </div>
                
        </div>
    
    </div>
    </div>

    </div>    
    
</div>

</div></div>     
    
    
    
</div>

</div>';    


    

}else if($PAGENAME == 'Designation-Setup'){





    if($RELATEDID == 'New'){

        $designation_name = '';
        $related_id = 'new_id';
        
        }else{
        
        $DATA  = SETUP::SETUP_DESIGNATION($RELATEDID);
        
        $designation_name = $DATA['designation'];
        $related_id =  $DATA['id'];
        
        
        }
            $content = '<div class="row">
            <div class="col-md-12">
        
                <div class="panel panel-default">
                    <div class="panel-body">
                    <form id="myform">
                    
                    <div class="alert alert-success alert-dismissible" id="success" style="display:none;">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                  </div>
        
                    <table class="table table-hover table-condensed table-striped table-bordered">
                        <tbody> <tr>
                                <th>Designation Name <input type="hidden" name="related_id" id="related_id" value="'.$related_id.'" > </th>
                                <td><input type="text" class="form-control" value="'.$designation_name.'" name="designation_name" id="designation_name"></td>
                                
                                
                            </tr>
                            
                            <tr>
                                
                                <td style="text-align:center;" colspan=2><input type="button" name="save_designation" id="save_designation" class="btn btn-primary" value="Save Designation"></td>
                            </tr>
                      </tbody> 
                  </table>
                </form>  
                        
                </div>
        
            </div>
        </div>';
        
        $content .= '<div class="row">
        <div class="col-md-12">
        
            <div class="panel panel-default">
                <div class="panel-body" id="load_table">
               
                <table class="table table-hover table-condensed table-striped table-bordered">
                <thead>
                <th>Sl</th>
                <th>Designation</th>
                </thead>
                    <tbody>'; 
        $sl =1;
        $qry = $conn_me->prepare("SELECT * FROM `setup_designation`  ORDER BY `id` ASC");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        if ($qry->rowCount() > 0)
        {
            foreach($fetch_list AS $fetch) {
        
        
        
                $content .= '<tr>
                <th>'.$sl++.'</th>
                <th>'.$fetch['designation'].'</th>
               </tr>';
            }
        
        }
                        
                          
        $content .= '</tbody> 
              </table>
              </div>
                    
            </div>
        
        </div>
        </div>';
    



    }else if($PAGENAME == 'Category-Setup'){




        if($RELATEDID == 'New'){

            $category_name = '';
            $related_id = 'new_id';
            
            }else{
            
            $DATA  = SETUP::SETUP_CATEGORY($RELATEDID);
            
            $category_name = $DATA['category'];
            $related_id =  $DATA['id'];
            
            
            }
                $content = '<div class="row">
                <div class="col-md-12">
            
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <form id="myform">
                        
                        <div class="alert alert-success alert-dismissible" id="success" style="display:none;">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                      </div>
            
                        <table class="table table-hover table-condensed table-striped table-bordered">
                            <tbody> <tr>
                                    <th>Category  <input type="hidden" name="related_id" id="related_id" value="'.$related_id.'" > </th>
                                    <td><input type="text" class="form-control" value="'.$category_name.'" name="category_name" id="category_name"></td>
                                    
                                    
                                </tr>
                                
                                <tr>
                                    
                                    <td style="text-align:center;" colspan=2><input type="button" name="save_category" id="save_category" class="btn btn-primary" value="Save Category"></td>
                                </tr>
                          </tbody> 
                      </table>
                    </form>  
                            
                    </div>
            
                </div>
            </div>';
            
            $content .= '<div class="row">
            <div class="col-md-12">


            <div class="panel-heading">
<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(\' :: Category List\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel(\'Category-List\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div>                                    

</div>


            
                <div class="panel panel-default">
                    <div class="panel-body" id="load_table">
                   
                    <table class="table table-hover table-condensed table-striped table-bordered" id="MSalary">
                    <thead>
                    <th>Sl</th>
                    <th>Category</th>
                    <th>Action</th>
                    </thead>
                        <tbody>'; 
            $sl =1;
            $qry = $conn_me->prepare("SELECT * FROM `setup_category`  ORDER BY `id` ASC");
            $qry->execute();
            $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
            if ($qry->rowCount() > 0)
            {
                foreach($fetch_list AS $fetch) {
            
            
            
                    $content .= '<tr>
                    <th>'.$sl++.'</th>
                    <th>'.$fetch['category'].'</th>
                    <td><a href="Setup/Category-Setup/'.$fetch['id'].'" ><i class="fa fa-edit danger"></i><a></td>
                   </tr>';
                }
            
            }
                            
                              
            $content .= '</tbody> 
                  </table>
                  </div>
                        
                </div>
            
            </div>
            </div>';

  }else if($PAGENAME == 'Transport-Cost-Setup'){


    if($RELATEDID == 'New'){

        $district_name = '';
        $nogot_cost = '';
        $related_id = 'new_id';
        $vaki_cost = '';
        $district_id = '';
        
        }else{
        
        $DATA  = SETUP::SETUP_DISTRICT($RELATEDID);
        
        $district_id = $DATA['district_id'];
        $related_id =  $DATA['id'];
        $district_name = $DATA['district_name'];
        $nogot_cost = $DATA['nogot_cost'];
        $vaki_cost = $DATA['vaki_cost'];

        
        }
            $content = '<div class="row">
            <div class="col-md-12">
        
                <div class="panel panel-default">
                    <div class="panel-body">
                    <form id="myform">
                    
                    <div class="alert alert-success alert-dismissible" id="success" style="display:none;">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                  </div>
                  <input type="hidden" name="related_id" id="related_id" value="'.$related_id.'" > 
                    <table class="table table-hover table-condensed table-striped table-bordered">
                        <tbody> 
                        <tr>
                        <th>District</th>


                        <td>
                        <select id="district_id" name="district_id" required class="select form-control" class="form-control" data-live-search="true" data-rel="chosen">
                        <option value="">Select One</option>';
                        $qry = $conn_me->prepare("SELECT * FROM `districts`  ORDER BY `id` ASC");
                        $qry->execute();
                        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
                         foreach($fetch_list AS $fetch) { 
                           $content .=  '<option ';
                           if($district_id == $fetch['id']){
                               $content .=  'selected="selected"';
                           }else{
           
                           }
           
                            $content .=  ' value="'.$fetch['id'].'">'.$fetch['name'].'</option>'; 
                         } 
                         $content .= '</select>
                        </td>
                        
                        
                    </tr>

                        <tr>
                                <th>Nogod  Cost</th>


                                <td><input type="number" class="form-control" value="'.$nogot_cost.'" name="nogot_cost" id="nogot_cost"></td>
                                
                                
                            </tr>

                            <tr>
                                <th>Vaki  Cost</th>

                                <td><input type="number" class="form-control" value="'.$vaki_cost.'" name="vaki_cost" id="vaki_cost"></td>
                                
                                
                            </tr>


                            
                            <tr>
                                
                                <td style="text-align:center;" colspan=2><input type="button" name="save_transport_cost" id="save_transport_cost" class="btn btn-primary" value="Save Data"></td>
                            </tr>
                      </tbody> 
                  </table>
                </form>  
                        
                </div>
        
            </div>
        </div>';
        
        $content .= '<div class="row">
        <div class="col-md-12">
        <div class="panel-heading">
<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(\' :: Transport-Cost List\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel(\'Transport-Cost-List\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div>                                    

</div>
            <div class="panel panel-default">
                <div class="panel-body" id="load_table">
               
                <table class="table datatable" id="MSalary">
                <thead>
                <th>Sl</th>
                <th>District</th>
                <th>Nogot</th>
                <th>Baki</th>
                <th>Action</th>
                </thead>
                    <tbody>'; 
        $sl =1;
        $qry = $conn_me->prepare("SELECT  * FROM `tansport_cost`  ORDER BY `id` ASC");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        if ($qry->rowCount() > 0)
        {
            foreach($fetch_list AS $fetch) {

                $info_dis = SETUP::SETUP_DISTRICT($fetch['district_id']);

        
    
                $content .= '<tr>
                <th>'.$sl++.'</th>
                <th>'.$info_dis['district'].'</th>
                <th>'.$fetch['nogot_cost'].'</th>
                <th>'.$fetch['vaki_cost'].'</th>
                <td><a href="Setup/Transport-Cost-Setup/'.$fetch['id'].'" ><i class="fa fa-edit danger"></i><a></td>
               </tr>';
            }
        
        }
                        
                          
        $content .= '</tbody> 
              </table>
              </div>
                    
            </div>
        
        </div>
        </div>';


    }else if($PAGENAME == 'Employee-List'){

        
$content = '<div class="row">
<div class="col-md-12">
<div class="panel-heading">
<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(\' :: User List\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel(\'User-List\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div>                                    

</div>
    <div class="panel panel-default">
        <div class="panel-body" id="load_table">
       
        <table class="table datatable" id="MSalary">
        <thead>
        <th>Sl</th>
        <th>Code</th>
        <th>Employee Name</th>
        <th>Employee Type</th>
        <th>Action</th>
        </thead>
            <tbody>'; 
$sl =1;
$qry = $conn_me->prepare("SELECT * FROM `admin`  ORDER BY `id` ASC ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
if ($qry->rowCount() > 0)
{
    foreach($fetch_list AS $fetch) {
        $get_employeeid = SETUP::ADMIN_SETUP($fetch['id']);

        $content .= '<tr>
        <th>'.$sl++.'</th>
        <th>'.$get_employeeid['employee_code_with_prefix'] .' </th>
        <th>'. $get_employeeid['hr_name'].'</th>
        <th>'. $fetch['user_type'].'</th>
        <th><a href="Setup/User-Management/'.$get_employeeid['employee_id'].'">Edit</a></th>
       </tr>';
    }

}
                
                  
$content .= '</tbody> 
      </table>
      </div>
            
    </div>

</div>
</div>';




}else if($PAGENAME == 'Unit-Setup'){




    if($RELATEDID == 'New'){

        $unit_name = '';
        $related_id = 'new_id';
        
        }else{
        
        $DATA  = SETUP::SETUP_UNIT($RELATEDID);
        
        $unit_name = $DATA['unit'];
        $related_id =  $DATA['id'];
        
        
        }
            $content = '<div class="row">
            <div class="col-md-12">
        
                <div class="panel panel-default">
                    <div class="panel-body">
                    <form id="myform">
                    
                    <div class="alert alert-success alert-dismissible" id="success" style="display:none;">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                  </div>
        
                    <table class="table table-hover table-condensed table-striped table-bordered">
                        <tbody> <tr>
                                <th>Unit  <input type="hidden" name="related_id" id="related_id" value="'.$related_id.'" > </th>
                                <td><input type="text" class="form-control" value="'.$unit_name.'" name="unit_name" id="unit_name"></td>
                                
                                
                            </tr>
                            
                            <tr>
                                
                                <td style="text-align:center;" colspan=2><input type="button" name="save_unit" id="save_unit" class="btn btn-primary" value="Save Unit"></td>
                            </tr>
                      </tbody> 
                  </table>
                </form>  
                        
                </div>
        
            </div>
        </div>';
        
        $content .= '<div class="row">
        <div class="col-md-12">
        
            <div class="panel panel-default">
                <div class="panel-body" id="load_table">
               
                <table class="table datatable">
                <thead>
                <th>Sl</th>
                <th>Area</th>
                <th>Action</th>
                </thead>
                    <tbody>'; 
        $sl =1;
        $qry = $conn_me->prepare("SELECT * FROM `setup_unit`  ORDER BY `id` ASC");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        if ($qry->rowCount() > 0)
        {
            foreach($fetch_list AS $fetch) {
        
        
        
                $content .= '<tr>
                <th>'.$sl++.'</th>
                <th>'.$fetch['unit'].'</th>
                <td><a href="Setup/Unit-Setup/'.$fetch['id'].'" ><i class="fa fa-edit danger"></i><a></td>
               </tr>';
            }
        
        }
                        
                          
        $content .= '</tbody> 
              </table>
              </div>
                    
            </div>
        
        </div>
        </div>';



    }else if($PAGENAME == 'Area-Setup'){


    if($RELATEDID == 'New'){

        $area_name = '';
        $related_id = 'new_id';
        
        }else{
        
        $DATA  = SETUP::SETUP_DISTRICT($RELATEDID);
        
        $area_name = $DATA['district'];
        $related_id =  $DATA['id'];
        
        
        }

 

            $content = '<div class="row">
            <div class="col-md-12">
        
                <div class="panel panel-default">
                    <div class="panel-body">
                    <form id="myform">
                    
                    <div class="alert alert-success alert-dismissible" id="success" style="display:none;">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                  </div>
        
                    <table class="table table-hover table-condensed table-striped table-bordered">
                        <tbody> <tr>
                                <th>Area  <input type="hidden" name="related_id" id="related_id" value="'.$related_id.'" > </th>
                                <td><input type="text" class="form-control" value="'.$area_name.'" name="area_name" id="area_name"></td>
                                
                                
                            </tr>
                            
                            <tr>
                                
                                <td style="text-align:center;" colspan=2><input type="button" name="save_area" id="save_area" class="btn btn-primary" value="Save Area"></td>
                            </tr>
                      </tbody> 
                  </table>
                </form>  
                        
                </div>
        
            </div>
        </div>';
        
        $content .= '<div class="row">
        <div class="col-md-12">
        <div class="panel-heading">
<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(\' :: Area List\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel(\'Area-List\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div>                                    

</div>
            <div class="panel panel-default">
                <div class="panel-body" id="load_table">
               
                <table class="table datatable" id="MSalary">
                <thead>
                <th>Sl</th>
                <th>Area</th>
                <th>Action</th>
                </thead>
                    <tbody>'; 
        $sl =1;
        $qry = $conn_me->prepare("SELECT * FROM `districts`  ORDER BY `id` ASC");
        $qry->execute();
        $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
        if ($qry->rowCount() > 0)
        {
            foreach($fetch_list AS $fetch) {
        
        
        
                $content .= '<tr>
                <th>'.$sl++.'</th>
                <th>'.$fetch['name'].'</th>
                <td><a href="Setup/Area-Setup/'.$fetch['id'].'" ><i class="fa fa-edit danger"></i><a></td>
               </tr>';
            }
        
        }
                        
                          
        $content .= '</tbody> 
              </table>
              </div>
                    
            </div>
        
        </div>
        </div>';


}else if($PAGENAME == 'Department-Setup'){

if($RELATEDID == 'New'){

$department_name = '';
$related_id = 'new_id';

}else{

$DATA  = SETUP::SETUP_DEPARTMENT($RELATEDID);

$department_name = $DATA['department'];
$related_id =  $DATA['id'];


}
    $content = '<div class="row">
    <div class="col-md-12">

        <div class="panel panel-default">
            <div class="panel-body">
            <form id="myform">
            
            <div class="alert alert-success alert-dismissible" id="success" style="display:none;">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
          </div>

            <table class="table table-hover table-condensed table-striped table-bordered">
                <tbody> <tr>
                        <th>Department Name <input type="hidden" name="related_id" id="related_id" value="'.$related_id.'" > </th>
                        <td><input type="text" class="form-control" value="'.$department_name.'" name="department_name" id="department_name"></td>
                        
                        
                    </tr>
                    
                    <tr>
                        
                        <td style="text-align:center;" colspan=2><input type="button" name="save_department" id="save_department" class="btn btn-primary" value="Save Department"></td>
                    </tr>
              </tbody> 
          </table>
        </form>  
                
        </div>

    </div>
</div>';

$content .= '<div class="row">
<div class="col-md-12">

    <div class="panel panel-default">
        <div class="panel-body" id="load_table">
       
        <table class="table table-hover table-condensed table-striped table-bordered">
        <thead>
        <th>Sl</th>
        <th>Department</th>
        <th>Action</th>
        </thead>
            <tbody>'; 
$sl =1;
$qry = $conn_me->prepare("SELECT * FROM `setup_department`  ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
if ($qry->rowCount() > 0)
{
    foreach($fetch_list AS $fetch) {



        $content .= '<tr>
        <th>'.$sl++.'</th>
        <th>'.$fetch['department'].'</th>
        <td><a href="Setup/Department-Setup/'.$fetch['id'].'" ><i class="fa fa-edit danger"></i><a></td>
       </tr>';
    }

}
                
                  
$content .= '</tbody> 
      </table>
      </div>
            
    </div>

</div>
</div>';

}else if($PAGENAME == 'Brunch-Setup'){

    if($RELATEDID == 'New'){

        $brunch_name = '';
        $address1 = '';
        $address2 = '';
        $phone = '';
        $related_id = 'new_id';
        $rerlated_warehouse = '';
        
        }else{
        
        $DATA  = SETUP::SETUP_BRUNCH($RELATEDID);
        
        $brunch_name = $DATA['brunch'];
        $phone = $DATA['phone'];
        $related_id =  $DATA['id'];
        $address1 = $DATA['address_line_one'];
        $address2 = $DATA['address_line_two'];
        $rerlated_warehouse = $DATA['rerlated_warehouse'];

        }


    $content = ' <div class="row">
    <div class="col-md-12">

        <div class="panel panel-default">
            <div class="panel-body">
            <form action="" method="post" enctype="multipart/form-data">
            
        <input type="hidden" name="related_id" id="related_id" value="'.$related_id.'">

            <table class="table table-hover table-condensed table-striped table-bordered">
                <tbody> <tr>
                        <th>Branch Name</th>
                        <td><input type="text" class="form-control" value="'.$brunch_name.'" name="brunch_name" id="brunch_name"></td>
                        
                        
                    </tr>

                    <tr>
                    <th>Address Line 1</th>
                    <td><input type="text" class="form-control" value="'.$address1.'" name="address1" id="address1"></td>
                </tr>
                <tr>
                    <th>Address Line 2</th>
                    <td><input type="text" class="form-control" value="'.$address2.'" name="address2" id="address2"></td>
                </tr>


                <tr>
                <th>Phone</th>
                <td><input type="text" class="form-control" value="'.$phone.'" name="phone" id="phone"></td>
                
                
            </tr>
            <tr>



            <td>Realated Warehouse</td>
            <td>
            <select style="width:100%!imortant"  id="related_warehouse" name="related_warehouse[]" data-live-search=true class="selectpicker" multiple data-selected-text-format="count>3" data-all="false" >';
                    
                    $qry = $conn_me->prepare("SELECT *  FROM `setup_warehouse` where status = 1 ");
                    $qry->execute();
                    $fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
                    foreach($fetch_list AS $fetch2) { 
                        $content .= '<option ';
                        if(!empty($rerlated_warehouse)){
                         $a = json_decode($rerlated_warehouse);
                         for ($i = 0; $i < count($a); $i++) {
                             if(  $a[$i] == $fetch2['id'] ){ $content .= 'selected="selected"'; }else{ }
                           }
                        }
              
                        $content .= ' value="'.$fetch2['id'].'">'.$fetch2['name'].'</option>';
                     }
                    
             
                    $content .= '</select></td>
                    </tr>
                    
                    <tr>
                        
                        <td style="text-align:center;" colspan=2><input type="button" name="save_brunch" id="save_brunch" class="btn btn-primary" value="Save Branch"></td>
                    </tr>
              </tbody> 
          </table>
        </form>  
                
        </div>

    </div>
</div>';

$content .= '<div class="row">
<div class="col-md-12">
<div class="panel-heading">
<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(\' :: Brunch List\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel(\'Brunch-List\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div>                                    

</div>
    <div class="panel panel-default">
        <div class="panel-body" id="load_table">
       
        <table class="table datatable" id="MSalary">
        <thead>
        <th>Sl</th>
        <th>Branch Name</th>
        <th>Action</th>
        </thead>
            <tbody>'; 
$sl =1;
$qry = $conn_me->prepare("SELECT * FROM `setup_brunch` ");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
if ($qry->rowCount() > 0)
{
    foreach($fetch_list AS $fetch) {



        $content .= '<tr>
        <th>'.$sl++.'</th>
        <th>'.$fetch['brunch'].'</th>
        <td><a href="Setup/Brunch-Setup/'.$fetch['id'].'" ><i class="fa fa-edit danger"></i><a></td>
       </tr>';
    }

}
                
                  
$content .= '</tbody> 
      </table>
      </div>
            
    </div>

</div>
</div>';


}else if($PAGENAME == 'Warehouse-Setup'){

    if($RELATEDID == 'New'){

        $related_id = 'new_id';
        $warehouse_name = '';
        $warehouse_address = '';
        $warehouse_phone = '';
        $warehouse_height = '0.00';
        $warehouse_width = '0.00';
        $warehouse_length = '0.00';

        }else{

      
        
        $DATA  = SETUP::SETUP_WAREHOUSE($RELATEDID);
        
        $related_id =  $DATA['id'];
        $warehouse_name = $DATA['name'];
        $warehouse_address = $DATA['address'];
        $warehouse_phone = $DATA['phone'];
        $warehouse_height = $DATA['height'];
        $warehouse_width = $DATA['width'];
        $warehouse_length = $DATA['length'];
        
        }


    $content = ' <div class="row">
    <div class="col-md-12">

        <div class="panel panel-default">
            <div class="panel-body">
            <form action="" method="post" enctype="multipart/form-data">
            
        
<input type="hidden" name="related_id" id="related_id" value="'.$related_id.'">
            <table class="table table-hover table-condensed table-striped table-bordered">
                <tbody> <tr>
                        <th>Warehouse Name</th>
                        <td><input type="text" class="form-control" value="'.$warehouse_name.'" name="warehouse_name" id="warehouse_name"></td>
                        
                        
                    </tr>

                    <tr>
                    <th>Address</th>
                    <td><input type="text" class="form-control" value="'.$warehouse_address.'" name="warehouse_address" id="warehouse_address"></td>
                    </tr>
                    <tr>
                    <th>Phone</th>
                    <td><input type="text" class="form-control" value="'.$warehouse_phone.'" name="warehouse_phone" id="warehouse_phone"></td>
                    </tr>
                    <tr>
                    <th>Height</th>
                    <td><input type="text" class="form-control" value="'.$warehouse_height.'" name="warehouse_height" id="warehouse_height"></td>
                    </tr>

                    <tr>
                    <th>Width</th>
                    <td><input type="text" class="form-control" value="'.$warehouse_width.'" name="warehouse_width" id="warehouse_width"></td>
                    </tr>

                    <tr>
                    <th>Length</th>
                    <td><input type="text" class="form-control" value="'.$warehouse_length.'" name="warehouse_length" id="warehouse_length"></td>
                    </tr>

                    
                    <tr>
                        
                        <td style="text-align:center;" colspan=2><input type="submit" name="submit" class="btn btn-primary" id="save_warehouse"  value="Save Warehouse"></td>
                    </tr>
              </tbody> 
          </table>
        </form>  
                
        </div>

    </div>
</div>';

$content .= '<div class="row">
<div class="col-md-12">
<div class="panel-heading">
<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(\' :: Warehouse List\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel(\'Warehouse-List\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div>                                    

</div>
    <div class="panel panel-default">
        <div class="panel-body" id="load_table">
       
        <table class="table table-hover table-condensed table-striped table-bordered" id="MSalary">
        <thead>
        <th>Sl</th>
        <th>Name</th>
        <th>Capacity</th>
        <th>Action</th>
        </thead>
            <tbody>'; 
$sl =1;
$qry = $conn_me->prepare("SELECT * FROM `setup_warehouse`  ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
if ($qry->rowCount() > 0)
{
    foreach($fetch_list AS $fetch) {



        $content .= '<tr>
        <td>'.$sl++.'</td>
        <td>'.$fetch['name'].'<br><b>Address:</b> '.$fetch['address'].'<br><b>Phone:</b> '.$fetch['phone'].'</td>
        <td>'.$fetch['height']*$fetch['width']*$fetch['length'].'('.$fetch['height'].'x'.$fetch['width'].'x'.$fetch['length'].')</td>
        <td><a href="Setup/Warehouse-Setup/'.$fetch['id'].'" ><i class="fa fa-edit danger"></i><a></td>
       </tr>';
    }

}
                
                  
$content .= '</tbody> 
      </table>
      </div>
            
    </div>

</div>
</div>';



}else if($PAGENAME == 'Employee-Personal-Profile'){



    $DATA  = SETUP::ADMIN_SETUP($_GET['related_id']);
    $employee_id = $DATA['employee_id'];
    $admin_id = $_GET['related_id'];
    $hr_name = $DATA['hr_name'];
    $fa_name = $DATA['fa_name'];
    $username = $DATA['username'];
    $user_password = $DATA['dypricpt_pass'];
    $mo_name = $DATA['mo_name'];

    $birth_date = $DATA['birth_date'];
    $mob_no = $DATA['mob_no'];
    $photo = $DATA['photo'];
$email =  $DATA['email'];
    if(!empty($photo)){
       $ck_logo = '<a target="_blink" href="upload/employee_photo/'.$photo.'" > Click here to see the logo </a>';
    }else{
        $ck_logo = 'No Logo';
    }


    
        $content = '<div class="row">
        <div class="col-md-12">
    
        <input type="hidden" id="old_pass" value="'.$user_password.'">
            <div class="panel panel-default">
                <div class="panel-body">
                <form id="myform">
                
                <div class="alert alert-success alert-dismissible" id="success" style="display:none;">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
              </div>
    
                <table class="table table-hover table-condensed table-striped table-bordered">
                    <tbody> 
                    <tr>
                            <th>Name <input type="hidden" name="employee_id" id="employee_id" value="'.$employee_id.'" > <input type="hidden" name="admin_id" id="admin_id" value="'.$admin_id.'" ></th>
                            <td><input type="text" class="form-control" value="'.$hr_name.'" name="hr_name" id="hr_name"></td>
                    </tr>

                    <tr>
                    <th>Father Name </th>
                    <td><input type="text" class="form-control" value="'.$fa_name.'" name="fa_name" id="fa_name"></td>
            </tr>

            
            <tr>
            <th>Mother Name </th>
            <td><input type="text" class="form-control" value="'.$mo_name.'" name="mo_name" id="mo_name"></td>
    </tr>

    
    <tr>
    <th>Birth Date </th>
    <td><input type="text" class="date form-control" value="'.$birth_date.'" name="birth_date" id="birth_date"></td>
</tr>


<tr>
<th>Mobile</th>
<td><input type="text" class="form-control" value="'.$mob_no.'" name="mob_no" id="mob_no"></td>
</tr>


<tr>
<th>Profile Pic </th>
<td><input type="file" onchange="uploadPic()" class="fileinput btn-primary" accept=".jpg,.jpeg,.png" name="fileInput" id="fileInput"  title="Browse" >
<span class="help-block"  id="load_msg_company_logo">'.$ck_logo.'</span>
</td>
</tr>

<tr>

<td colspan="2">

<div class="progress mt-2" >
<div class="progress-bar progress-bar-striped active" id="progressBar" role="progressbar" style="width: 0%"><b id="crate"></b></div>
</div>
<b id="textUpload"><b>
</td>
</tr>

<tr>
<th>Email </th>
<td><input type="text" class="form-control" value="'.$email.'" name="email" id="email"></td>
</tr>

<tr>
<th>User Name </th>
<td><input type="text" class="form-control" value="'.$username.'" name="username" id="username"></td>
</tr>

<tr>
<th>Password </th>
<td><input type="text" class="form-control" value="'.$user_password.'" name="user_password" id="user_password"></td>
</tr>



        <tr>
            
            <td style="text-align:center;" colspan=2><input type="button" name="update_personal_profile" id="update_personal_profile" class="btn btn-primary" value="Save Profile"></td>
        </tr>
                  </tbody> 
              </table>
            </form>  
                    
            </div>
    
        </div>
    </div>';




}else if($PAGENAME == 'Company-Profile'){


    $DATA  = SETUP::SETUP_COMPANY('Active');
    $related_id = $DATA['id'];
    $company_name = $DATA['company_name'];
    $company_short_name = $DATA['company_short_name'];
    $company_address = $DATA['company_address'];
    $company_phone = $DATA['company_phone'];
    $company_email = $DATA['company_email'];
    $company_logo = $DATA['company_logo'];
    $invoice_header = $DATA['invoice_header'];
    $invoice_footer = $DATA['invoice_footer'];
    $empty_invoice_header = $DATA['empty_invoice_header'];
    $empty_invoice_footer = $DATA['empty_invoice_footer'];
    $software_start_date = $DATA['software_start_date'];
    $lastupdate = $DATA['lastupdate'];
    $time = $DATA['time'];
    $status = $DATA['status'];

    if(!empty($company_logo)){
       $ck_logo = '<a target="_blink" href="upload/Company_Logo/'.$company_logo.'" > Click here to see the logo </a>';
    }else{
        $ck_logo = 'No Logo';
    }

    if(!empty($invoice_header)){
    $ck_invoice_header = '<a target="_blink" href="upload/Invoice_Header/'.$invoice_header.'" > Click here to see the Header </a>';
    }else{
    $ck_invoice_header = 'No Invoice Header';
    }

    if(!empty($invoice_footer)){
    $ck_invoice_footer = '<a target="_blink" href="upload/Invoice_Footer/'.$invoice_footer.'" > Click here to see the Header </a>';
    }else{
    $ck_invoice_footer = 'No Invoice Footer';
    }
    

    
        $content = '<div class="row">
        <div class="col-md-12">
    
            <div class="panel panel-default">
                <div class="panel-body">
                <form id="myform">
                
                <div class="alert alert-success alert-dismissible" id="success" style="display:none;">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
              </div>
    
                <table class="table table-hover table-condensed table-striped table-bordered">
                    <tbody> 
                    <tr>
                            <th>Company Name <input type="hidden" name="related_id" id="related_id" value="'.$related_id.'" > </th>
                            <td><input type="text" class="form-control" value="'.$company_name.'" name="company_name" id="company_name"></td>
                    </tr>

                    <tr>
                    <th>Short Name </th>
                    <td><input type="text" class="form-control" value="'.$company_short_name.'" name="company_short_name" id="company_short_name"></td>
            </tr>

            <tr>
<th>Logo </th>
<td><input type="file" class="fileinput btn-primary" accept=".xls,.xlsx,.jpg,.pdf,.jpeg,.docx,.doc" name="company_logo" id="company_logo"  title="Browse & Upload" >
<span class="help-block"  id="load_msg_company_logo">'.$ck_logo.'</span>
</td>
</tr>


                    <tr>
                    <th>Address </th>
                    <td><input type="text" class="form-control" value="'.$company_address.'" name="company_address" id="company_address"></td>
            </tr>


                    <tr>
                    <th>Phone </th>
                    <td><input type="text" class="form-control" value="'.$company_phone.'" name="company_phone" id="company_phone"></td>
            </tr>


            <tr>
            <th>Email </th>
            <td><input type="text" class="form-control" value="'.$company_email.'" name="company_email" id="company_email"></td>
    </tr>


<tr>
<th>Invoice Header </th>
<td><input type="file" class="fileinput btn-primary" accept=".jpg,.jpeg,.png" name="invoice_header" id="invoice_header"  title="Browse & Upload" >
<span class="help-block"  id="load_msg_invoice_header">'.$ck_invoice_header.'</span>
</td>
</tr>

<tr>
<th>Invoice Footer </th>
<td><input type="file" class="fileinput btn-primary" accept=".jpg,.jpeg,.png" name="invoice_footer" id="invoice_footer"  title="Browse & Upload" >
<span class="help-block"  id="load_msg_invoice_footer">'.$ck_invoice_footer.'</span>
</td>
</tr>


        <tr>
            
            <td style="text-align:center;" colspan=2><input type="button" name="save_profile" id="save_profile" class="btn btn-primary" value="Save Profile"></td>
        </tr>
                  </tbody> 
              </table>
            </form>  
                    
            </div>
    
        </div>
    </div>';


}else if($PAGENAME == 'Notification-Setup'){

    if($RELATEDID == 'New'){

        $notification = '';
        $check = 'checked';
        $related_id = 'new_id';
        
        }else{
        
        $DATA  = SETUP::SETUP_NOTIFICATION($RELATEDID);
        
        $notification = $DATA['notification'];
        $check = $DATA['check'];
        $related_id =  $DATA['id'];
        
        
        }

    
    $content = ' <div class="row">
    <div class="col-md-12">

        <div class="panel panel-default">
            <div class="panel-body">
            <form action="" method="post" enctype="multipart/form-data">
            
            <input type="hidden" name="related_id" id="related_id" value="'.$related_id.'" >

            <table class="table table-hover table-condensed table-striped table-bordered">
                <tbody> <tr>
                        <th>Notification</th>
                        <td><input type="text" class="form-control" value="'.$notification.'" name="notification" id="notification"></td>
                        
                        
                    </tr>

                    <tr>
                    <th>Status</th>
                    <td><label class="switch">
                    <input type="checkbox"   id="status" name="status" value="1" '.$check.' />
                    
                    <span></span>
                </label></td>
                    
                    
                </tr>

                    
                    <tr>
                        
                        <td style="text-align:center;" colspan=2><input type="button" name="save_notification" id="save_notification" class="btn btn-primary" value="Save Notification"></td>
                    </tr>
              </tbody> 
          </table>
        </form>  
                
        </div>

    </div>
</div>';
   

$content .= '<div class="row">
<div class="col-md-12">

<div class="panel-heading">
<div class="btn-group pull-right">
    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
    <ul class="dropdown-menu">
        <li><a  onclick="printButtn(\' :: Notification List\',\'MSalary\')" ><img src="img/icons/json.png" width="24"/>Print</a></li>
        <li><a onclick="exportToExcel(\'Notification-List\',\'MSalary\')"; ><img src="img/icons/xls.png" width="24"/> XLS</a></li>
    </ul>
</div>                                    

</div>


    <div class="panel panel-default">
        <div class="panel-body" id="load_table">
       
        <table class="table table-hover table-condensed table-striped table-bordered" id="MSalary">
        <thead>
        <th>Sl</th>
        <th>Notification</th>
        <th>Action</th>
        </thead>
            <tbody>'; 
$sl =1;
$qry = $conn_me->prepare("SELECT * FROM `notice_bord`  ORDER BY `id` ASC");
$qry->execute();
$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
if ($qry->rowCount() > 0)
{
    foreach($fetch_list AS $fetch) {



        $content .= '<tr>
        <th>'.$sl++.'</th>
        <th>'.$fetch['notice_text'].'</th>
        <td><a href="Notification/'.$fetch['id'].'" ><i class="fa fa-edit danger"></i><a> </td>
       </tr>';
    }

}
                
                  
$content .= '</tbody> 
      </table>
      </div>
            
    </div>

</div>
</div>';


}else if($PAGENAME == 'Welcome'){


    $com_profile = SETUP::SETUP_COMPANY('Active');
   

       $warning1 =  date('d-m-Y', strtotime('-1 week', strtotime($com_profile["software_end_date"])));

if(date('d-m-Y') < $warning1){

    $mess = "<marquee class='text-danger'>Your Subscription will end at ".$com_profile["software_end_date"]." , Please contact developer</marquee>" ;

}else{

    $mess = "" ;

}

    $content = ' <div class="error-container animated flash" >
    <div class="error-text">'.$com_profile['company_name'].'</div>
    <div class="error-subtext">'.$com_profile['company_address'].' <br>'.$com_profile['company_phone']. '<br>'.$com_profile['company_email'].'</div>            
</div>';


}else{

   
   
    $content = ' <div class="error-container animated flash" >
    <div class="error-code">You Lost !! </div>
    <div class="error-text">Need Help</div>
    <div class="error-subtext">call 911</div>            
</div>';

}
$mess = '';
        return array(
            'content' => $mess . $content

            );

    }


}




class LOAD {


    public function CREATE_XML(){
        $conn_me = Database::getInstance();
        $xml = new DomDocument("1.0");
        $ALLDATABOX = $xml->createElement("listData");
        $xml->appendChild($ALLDATABOX);
   
        $search = '';
        $query = $conn_me->prepare("SELECT `id` FROM `setup_product`  WHERE  `in_service` = 'checked'   ORDER BY `id` ASC "); 
        $query->execute();
        $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach($fetch_list AS $fetch){
   
             
             $productinfo = SETUP::SETUP_PRODUCT($fetch['id']);
   
   
             
   
   
        $dataTag=$xml->createElement("dataTag");
        $ALLDATABOX->appendChild($dataTag);
   
         
        $product_id =$xml->createElement("product_id",$productinfo['id']);
        $dataTag->appendChild($product_id);


        $code =$xml->createElement("code",$productinfo['code']);
        $dataTag->appendChild($code);
  

        $product_name =$xml->createElement("product_name",$productinfo['product_name']);
        $dataTag->appendChild($product_name);
   
        $category =$xml->createElement("category",$productinfo['category']);
        $dataTag->appendChild($category);
   
        $unit =$xml->createElement("unit",$productinfo['unit']);
        $dataTag->appendChild($unit);
        
        $pcs_in_cartoon =$xml->createElement("pcs_in_cartoon",$productinfo['pcs_in_cartoon']);
        $dataTag->appendChild($pcs_in_cartoon);
        
        $wholesale_rate =$xml->createElement("wholesale_rate",$productinfo['wholesale_rate']);
        $dataTag->appendChild($wholesale_rate);
   
        $sales_rate =$xml->createElement("sales_rate",$productinfo['sales_rate']);
        $dataTag->appendChild($sales_rate);
   

      

   
     
            
   
         
        }
   
        $xml->save("XMLFILE/links.xml");


    }
}




class REPORT 
{
    
  
    public static function BATCH_STATUS($WORK,$REPORT_TYPE,$CODE)
    {
        $conn_me = Database::getInstance();
        $company_info = SETUP::SETUP_COMPANY('Active');
 
        $content = '';

if($WORK == 'Batch Status' ){

        $content .='<div class="panel panel-default tabs">';
        
        $content .='<table style="width:100%;" class="table datatable_simple">';

        $content .='<tr><th style="text-align:center"><img style="height:70px" src="upload/Company_Logo/'.$company_info['company_logo'].'"></th></tr>';
        
        $content .='</table>';
    
        if($REPORT_TYPE == 'Invoice Wise Batch Status' ){

            $content .='        
                                        <ul class="nav nav-tabs" role="tablist">
                                            <li class="active"><a href="#tab-first" role="tab" data-toggle="tab">Item List</a></li>
                                            <li><a href="#tab-second" role="tab" data-toggle="tab">Material</a></li>
    
                                        </ul>
                                        <div class="panel-body tab-content">
                                            <div class="tab-pane active" id="tab-first">
                                               
                                            <table class="table table-hover table-condensed">
                                            <tr>
                                                    <th>Sl</th>
                                                    <th>Item</th>
                                                    <th>Batch QTY</th>
    
                                                </tr>';
    
    
    
    $total_qty = 0;
    $usergiven_pi = '';

    $aa1 = '1';
    $query = $conn_me->prepare("SELECT *  FROM `raw_request_recipe_wise`  WHERE `code` = '".$CODE."'  ORDER BY `id` DESC");
    $query->execute();
    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch) {
        
    
    
        $product_info = SETUP::SETUP_PRODUCT($fetch['product_id']);
        $usergiven_pi .= $fetch['user_given_invoiceno'] . ',';

        
        $content .=' <tr>
                    <td>'.$aa1++ .'</td>
                    <td>'.$product_info['product_code'].' '.$product_info['product_name'].'</td>
                    <td>'.$fetch['batch_quantity'].'</td>

                </tr>';
    
    
                $total_qty += $fetch['batch_quantity'];
    } 
    $aa = trim($usergiven_pi,',');
    $bb = explode(",",$aa);
    $cc = array_unique($bb);
    $dd = count($cc);

    $content .=' <input type="hidden" id="usergivenpino"   value="';
    for ($x = 0; $x < $dd; $x++) {
    
        $content .= $cc[$x];
        } 
        $content .= '">';                                       
 
    $content .=' </table>
                                            <span class="fa-left">Total Item <b style="color:red;font-size:18px;">'.$total_qty.'</b> Unit</span> 
    
                                            </div>
                                            <div class="tab-pane" id="tab-second">
                                            <table class="table table-hover table-condensed table-striped table-bordered">
         
    <thead>
    <tr>
    <th  style="text-align:center;vertical-align:top" colspan="7">BATCH DEMAND</th>
    <th style="text-align:center;vertical-align:top" colspan="2">PROCEESS</th>
    <th style="text-align:center;vertical-align:middle" rowspan="2">Action</th>
    <th style="text-align:center;vertical-align:middle" rowspan="2">Last Action</th>
    
    </tr>
    </thead>
    <tbody>
    <tr>
    <th >Sl</th>
    <th >Material Name</th>
    <th >Demad</th>
    <th >Receive</th>
    <th colspan="3">Stock</th>
    <th >Printing</th>
    <th >Spray</th>
    </tr>';
    $aa2 = '1';
    $count = 0;
    $total_materials = 0;
    $query = $conn_me->prepare("SELECT `action_status`,`send_requisition`,SUM(`demand_quantity`) AS `total_demand` ,`material_id` FROM `raw_request_recipe_wise_item` WHERE `demand_code` =  '".$CODE."' GROUP BY `material_id` ORDER BY `id` DESC");
    $query->execute();
    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch) {
        $count = $count + 1;
        $product_info = SETUP::SETUP_RAW_MATERIAL($fetch['material_id']);
    
        $receive_item_after_molding  = FIND::MOLDING_RAW_MATERIAL_BATCH_RECEIVE('',$fetch['material_id'],'product_wise');
        $receive_item_after_spray  = FIND::SPRAY_RAW_MATERIAL_BATCH_RECEIVE($CODE,$fetch['material_id'],'Invoice_Wise');
        $receive_item_after_print  = FIND::PRINT_RAW_MATERIAL_BATCH_RECEIVE($CODE,$fetch['material_id'],'Invoice_Wise');
     
        $recipe_wise_demand_receive_reject = FIND::RECEIPE_WISE_DEMAND_RECEIVE_REJECT($CODE,$fetch['material_id'],'Invoice_Wise');
    
    
        $stock = STOCK::RAW_ITEM_WISE_STOCK('',$fetch['material_id'],'product_wise');
        
        $total_demand = number_format((float)$fetch['total_demand'], 2, '.', '');
        $actual_qty = number_format((float)$recipe_wise_demand_receive_reject['actual_qty'], 2, '.', '');
        $item_stock = number_format((float)$stock['ITEM_STOCK'], 2, '.', '');

        
        $content .='<input value="'.$fetch['material_id'].'" style="display:none" type="text" name="material_id" id="material_id'.$count.'" data-srno="'.$count.'" class="form-control material_id"/>';
       
        $content .='<input value="total_demand'.$count.'" style="display:none" type="text" name="total_demand[]" id="total_demand'.$count.'" data-srno="'.$count.'" class="form-control total_demand"/>';

        $content .='<input value="'.$item_stock.'"  type="text" style="display:none" name="total_stock[]" id="total_stock'.$count.'" data-srno="'.$count.'" class="form-control total_stock"/>';

        $content .='<input value="'.$fetch['action_status'].'"  type="text" style="display:none" name="action_status[]" id="action_status'.$count.'" data-srno="'.$count.'" class="form-control action_status"/>';

        $content .='<input value="'.$CODE.'"  type="hidden" name="code" id="code" class="form-control"/>';
    
            $content .='<tr>
            <td>'. $aa2++.'</td>
            <td>'.$product_info['material_code'].' '.$product_info['product_name'].'</td>
            <td>'.$total_demand.'</td>
            <td>'.$actual_qty.'</td>';
    
            
          
            $content .='<td colspan="3">'.$item_stock.'</td>';
    
            if($product_info['supporting_product'] == 'Yes' ){
    
                $content .=' <td>'.$receive_item_after_print['total_receive'].'</td><td>'.$receive_item_after_spray['total_receive'].'</td>
               ';
            }else{
                $content .='
                <td></td>
                <td></td>';
            }

            $content .='<td> <label class="check"><input name="checkvalues[]" type="checkbox" class="icheckbox" value="'.$count.'" /></label></td>';
            
            if($fetch['action_status'] == 'Created P.O'){
                $content .='<td><a target="_BLINK" href="print.php?print=Batch Wise Raw Local Purches&code='.$CODE.'"> '.$fetch['action_status'].'</a></td>';
            }else if($fetch['action_status'] == 'Sent For Spray'){
                $content .='<td><a target="_BLINK" href="print.php?print=Batch Wise Send For Spray&code='.$CODE.'"> '.$fetch['action_status'].'</a></td>';
            }else if($fetch['action_status'] == 'Sent For Printing'){
                $content .='<td><a target="_BLINK" href="print.php?print=Batch Wise Send For Print&code='.$CODE.'"> '.$fetch['action_status'].'</a></td>';
            }else if($fetch['action_status'] == 'Sent For Molding'){
                $content .='<td><a target="_BLINK" href="print.php?print=Batch Wise Send For Molding&code='.$CODE.'"> '.$fetch['action_status'].'</a></td>';
            }else if($fetch['action_status'] == 'Sent Requisition'){
                $content .='<td><a target="_BLINK" href="print.php?print=Report Recipe Wise Requisition&code='.$CODE.'"> '.$fetch['action_status'].'</a></td>';

            }else{
                $content .='<td> '.$fetch['action_status'].'</td>';
            }
          
            $content .='</tr>';
    
    
    $total_materials += $fetch['total_demand'];
    } 
    
    $content .='<tr>'; 
    $content .='<td colspan="11"  style="text-align: center;">';
    $content .='<input type="button" class="btn-info"  onclick="go_requasation();" value="Send to Assembly">';
    $content .= '</td>';
    $content .='</tr>';  
    $content .='<tr>';
    $content .='<td colspan="11" style="text-align: center;">';
    $content .='<input type="button" class="btn-danger"  onclick="go_mold();" value="Mold"> ';
    $content .='<input type="button" class="btn-info"  onclick="go_print();" value="Print"> ';
    $content .='<input type="button" class="btn-warning"  onclick="go_sprey();" value="Spray">';
    $content .= '</td>';
    $content .='</tr>';                                    
    $content .='<tr>';
    $content .='<td colspan="11"  style="text-align: center;">';

     
    $content .='<input type="button" class="btn-info"  onclick="go_purches();" value="Purches">';
    $content .= '</td>';
    $content .='</tr>'; 
                                      
    
    $content .='<tbody></table>
    
                                            <span class="fa-left">Total Material <b style="color:red;font-size:18px;">'.$total_materials.'</b> Unit</span> 
                                            </div>     
                                            
                                            
                                            
                                        </div>
                                 
                                    </div>';    
    
    }else if($REPORT_TYPE == 'Pending Requisition' ){
    
 
        $content .='        
                                 
                                    <div class="panel-body tab-content">
                                                                            <table class="table table-hover table-condensed table-striped table-bordered">
     
    <thead>
    <tr>
    <th  style="text-align:center;vertical-align:top" colspan="4">BATCH DEMAND</th>
    <th style="text-align:center;vertical-align:top" colspan="3">STOCK</th>
    <th style="text-align:center;vertical-align:top" colspan="2">PROCEESS</th>
    <th style="text-align:center;vertical-align:middle" rowspan="2">Requisition Staus</th>
    
    </tr>
    </thead>
    <tbody>
    <tr>
    <th >Sl</th>
    <th >Material Name</th>
    <th >Demad</th>
    <th >Receive</th>
    <th >Molding</th>
    <th >Without Mold</th>
    <th >Stock</th>
    <th >Printing</th>
    <th >Spray</th>
    </tr>';
    $aa2 = '1';
    $count = 0;
    $total_materials = 0;
    $query = $conn_me->prepare("SELECT `send_requisition`,SUM(`demand_quantity`) AS `total_demand` ,`material_id` FROM `raw_request_recipe_wise_item` WHERE `demand_code` =  '".$CODE."' GROUP BY `material_id` ORDER BY `id` DESC");
    $query->execute();
    $fetch_list = $query->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fetch_list as $fetch) {
    $count = $count + 1;
    $product_info = SETUP::SETUP_RAW_MATERIAL($fetch['material_id']);
    
    $receive_item_after_molding  = FIND::MOLDING_RAW_MATERIAL_BATCH_RECEIVE('',$fetch['material_id'],'product_wise');
    $receive_item_after_spray  = FIND::SPRAY_RAW_MATERIAL_BATCH_RECEIVE('',$fetch['material_id'],'product_wise');
    $receive_item_after_print  = FIND::PRINT_RAW_MATERIAL_BATCH_RECEIVE('',$fetch['material_id'],'product_wise');
    
    $recipe_wise_demand_receive_reject = FIND::RECEIPE_WISE_DEMAND_RECEIVE_REJECT($CODE,$fetch['material_id'],'Invoice_Wise');
    
    
    $stock = STOCK::RAW_ITEM_WISE_STOCK('',$fetch['material_id'],'product_wise');
    if($stock['ITEM_STOCK'] > 0 ){
        $withut_molding =  $stock['ITEM_STOCK'] - $receive_item_after_molding['actual_receive'];
    }else{
        $withut_molding = 0.00;
    }
    
    $content .='<input value="'.$fetch['material_id'].'"  type="hidden" name="material_id" id="material_id'.$count.'" data-srno="'.$count.'" class="form-control material_id"/>';
    
    $content .='<input value="'.$fetch['total_demand'].'"  type="hidden" name="total_demand[]" id="total_demand'.$count.'" data-srno="'.$count.'" class="form-control total_demand"/>';
    $content .='<input value="'.$CODE.'"  type="hidden" name="code" id="code" class="form-control"/>';
    
        $content .='<tr>
        <td>'. $aa2++.'</td>
        <td>'.$product_info['product_code'].' '.$product_info['product_name'].'</td>

        <td>'.$fetch['total_demand'].'</td>
        <td>'.$recipe_wise_demand_receive_reject['actual_qty'].'</td>';
    
        if($product_info['supporting_product'] == 'Yes' ){
            
            $content .='
            <td>'.$receive_item_after_molding['total_receive'].'</td>
            <td>'.$withut_molding.'</td>';
        }else{
    
            $content .='<td></td><td></td>';
        }
        
        $content .='<td>'.$stock['ITEM_STOCK'].'</td>';
    
        if($product_info['supporting_product'] == 'Yes' ){
    
            $content .=' <td>'.$receive_item_after_print['total_receive'].'</td><td>'.$receive_item_after_spray['total_receive'].'</td>
           ';
        }else{
            $content .='
            <td></td>
            <td></td>';
        }
       
        $content .='
                    <td>'.$fetch['send_requisition'].'</td>
        </tr>';
    
    
    $total_materials += $fetch['total_demand'];
    } 
    
 
                                  
    
    $content .='<tbody></table>
    
                                        <span class="fa-left">Total Material <b style="color:red;font-size:18px;">'.$total_materials.'</b> Unit</span> 
                                        </div>     
                                        
                                    
                                   ';  
    
    }else {
    
        $content .=' ';
    
    }
    
    }else{

        $content .=' Nothiong to show';
    }

    
        
        return array(
            'print_report' => $content

            );


    }




    public static function INVOICE_WISE_FG_OPENINGSTOCK_REPORT($REPORT_TYPE,$CODE,$FROM,$TO)
    {

        $conn_me = Database::getInstance();

        
        $company_info = SETUP::SETUP_COMPANY('Active');

        $content = '';

        $content .='<table style="width:100%;" class="table datatable_simple">';

        $content .='<tr><th style="text-align:center"><img style="height:70px" src="upload/Company_Logo/'.$company_info['company_logo'].'"></th></tr>';
        
        $content .='</table>';

        $content .= '<table style="width:100%;" class="table datatable_simple">
        <tr><th colspan="6" style="text-align:center">'.$REPORT_TYPE.'</th></tr>        
        <tr>
            <th style="text-align:center;">Sl</th>
            <th style="text-align:center;">Invoice No</th>
            <th style="text-align:center;">Product</th>
            <th style="text-align:center;">Quantity</th>
            <th style="text-align:center;">Poster</th>
            <th style="text-align:center;">Approve By</th>

        </tr>';

           $sl=1;
        if($REPORT_TYPE == 'Invoice Wise FG OPENING STOCK' ){

        $report_qry = $conn_me->prepare("SELECT * FROM `fg_opening_stock`  WHERE `approve_data` = 'Pending' AND `code` = '".$CODE."'  ORDER BY `date` ASC");
       
        }else if($REPORT_TYPE == 'Date Wise FG Writeoff'){
            $report_qry = $conn_me->prepare("SELECT * FROM `fg_opening_stock`  WHERE `approve_data` = 'Pending' AND `code` = '".$CODE."'  AND `` BETWEEN '".$FROM."' AND '".$TO."' ORDER BY `date` ASC");

        }else{

            $report_qry = $conn_me->prepare("SELECT * FROM `fg_opening_stock`  WHERE `approve_data` = 'Pending' AND `code` = '".$CODE."'  ORDER BY `date` ASC");
       
        }

        $report_qry->execute();
        $fetch_report = $report_qry->fetchAll(PDO::FETCH_ASSOC);
        if ($report_qry->rowCount() > 0)
             {
                        foreach($fetch_report AS $fetch_R) { 

                            $product_info = SETUP::SETUP_PRODUCT($fetch_R['product_id']);
                            $poster_info = SETUP::ADMIN_SETUP($fetch_R['poster']);

                            if(!empty($fetch_R['approve_by'] )){
                                $approve_by_info = SETUP::ADMIN_SETUP($fetch_R['approve_by']);

                                $approve_by = $approve_by_info['hr_name']; }else{ $approve_by = 'Not Approved Yet';}


                            $content .='<tr>
                            <td style="text-align:center">'.$sl++.'</td>
                            <td style="text-align:center">'.$fetch_R['invoice_no'].'</td>
                            <td style="text-align:center">'.$product_info['product_name'].'</td>
                            <td style="text-align:center">'.$fetch_R['quantity'].'</td>
                            <td style="text-align:center" >'.$poster_info['hr_name'].'</td>
                            <td style="text-align:center">'.$approve_by.'</td>

                            </tr>';
            
            
                         }
            
        } else {
            $content .='<tr>
                <th style="text-align:center;color:red" colspan="2">No Record Found</th></tr>';
            
            }

        $content .= '</table>';




        return array(
            'print_report' => $content

            );



        }



   




    }

