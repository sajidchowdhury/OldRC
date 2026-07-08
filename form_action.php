<?php
include_once("auth.php");
include_once("clean.php");
include('function_query.php');
$conn_me = Database::getInstance();


if($_POST['action'] == 'save_ac_head' ){


	$account_head= clean($_POST['account_head']);
	$account_type= clean($_POST['account_type']);
	$description= clean($_POST['description']);
    $ladger_head= clean($_POST['ladger_head']);
	$parent_id= clean($_POST['parent_id']);

	$data1 = [
		'ledger_id' => $parent_id,
		'account_head' => $account_head,
		'account_type' => $account_type,
		'date' => date("Y-m-d"),
		'time' =>date("h:i:s a"),
		'poster' => $_SESSION['NEWERP_SESS_MEMBER_ID'],
		'lastupdate' => $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR']
	  ];

	  $data2 = [
		'name' => $ladger_head,
		'date' => date("Y-m-d"),
		'time' =>date("h:i:s a"),
		'poster' => $_SESSION['NEWERP_SESS_MEMBER_ID'],
		'lastupdate' => $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR']
	  ];




    if($parent_id == 'New_Parent'){

           
    $lc_fetch = $conn_me->prepare("SELECT * FROM `setup_ladger_head` WHERE `name` = '".$ladger_head."' ");
	$lc_fetch->execute();
	if ($lc_fetch->rowCount() > 0){

      $mess = 'Duplicate Ladger Head Found';


    }else{

		$query =   CRUD::insert_data('setup_ladger_head', $data2);
		$mess = $query['mess'] ;
		  
    }


    }else{

		if($_POST['related_id'] == 'new_id' ){

		$query =   CRUD::insert_data('setup_ac_head', $data1);
		$mess = $query['mess'] ;

		}else{

		$query =   CRUD::updateData('setup_ac_head',$_POST['related_id'],$data1);
		$mess = $query['mess'] ;

		}


		print $mess;

    }

	
	
}else if($_POST['action'] == 'edit_ac_head' ){


	$new_ledger_id= clean($_POST['new_ledger_id']);
	$new_account_head= clean($_POST['new_account_head']);
    $new_account_type = clean($_POST['new_account_type']);

	$data1 = [
		'ledger_id' => $new_ledger_id,
		'account_head' => $new_account_head,
		'account_type' => $new_account_type,
		'date' => date("Y-m-d"),
		'time' =>date("h:i:s a"),
		'poster' => $_SESSION['NEWERP_SESS_MEMBER_ID'],
		'lastupdate' => $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR']
	  ];

	  $query =   CRUD::updateData('setup_ac_head',$_POST['related_id'],$data1);
	  $mess = $query['mess'] ;
	  print $mess;
}else if($_POST['action'] == 'edit_ledger' ){


	$new_ladger_head= clean($_POST['new_ladger_head']);

	$data1 = [
		'name' => $new_ladger_head,
		'date' => date("Y-m-d"),
		'time' =>date("h:i:s a"),
		'poster' => $_SESSION['NEWERP_SESS_MEMBER_ID'],
		'lastupdate' => $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR']
	  ];


	  $query =   CRUD::updateData('setup_ladger_head',$_POST['related_id'],$data1);
		$mess = $query['mess'] ;

      print $mess;

}else if($_POST['action'] == 'transfer_transaction' ){


	$report_type= clean($_POST['report_type']);
	$tr_type= clean($_POST['tr_type']);
	$tr_from= clean($_POST['tr_from']);
	$tr_to= clean($_POST['tr_to']);

	$amount= clean($_POST['amount']);
	$note = clean($_POST['note']);
	$ledger_id = 32;
	$transaction_head_id = 88;
	$transection_by_to = clean($_POST['transection_by_to']);
	$transection_by_from= clean($_POST['transection_by_from']);
	$to_brunch_id = clean($_POST['to_brunch_id']);

	$transection_date = date("Y-m-d", strtotime($_POST['transection_date']));



	
	if($tr_from == 'Cash' ){
		$from_transection_by_id = 0;
	}else{
		$from_transection_by_id = $tr_from;

	}

	if( $tr_to == 'Cash'){
		$to_transection_by_id = 0;
	}else{
		$to_transection_by_id = $tr_to;

	}



	
	$TRANSECTING = SETUP::SETUP_CODE_INSERT_DATA('account_transection');

	$query = $conn_me->prepare("UPDATE `account_transection` 

	SET
	`transection_type` = 'EXPENSE',
	`ledger_id` = '".$ledger_id."',
	`transection_head_id` = '".$transaction_head_id."',
	`transection_to` = 'Account Head',
	`transection_to_id` = '0',
	`transection_by` = '".$transection_by_from."',
	`transection_by_id` = '".$from_transection_by_id."',
	`note` = '".$note."',
	`status` = 'Done',
	`transection_date` =  '" .  $transection_date . "',
	`out_amount` = 	'".$amount."',
	`data_inserted_from` = 	'MONEY-TRANSFER-FROM',
	`date` = '" . date("Y-m-d") . "',
	`time` =  '" . date("h:i:s a") . "',
	`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
	`brunch_id` =   '" . $_SESSION['USER_BRUNCH'] . "',
	`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'

	WHERE `id` = '".$TRANSECTING['last_id']."'");

	$query->execute();


     QUICK_BALANCE::QUICK_OPENING_BALANCE('out_amount',$transection_by_from,$from_transection_by_id,$amount,$transection_date,$_SESSION['USER_BRUNCH']);





	$TRANSECTING2 = SETUP::SETUP_CODE_INSERT_DATA('account_transection');

	$query = $conn_me->prepare("UPDATE `account_transection` 

	SET
	`transection_type` = 'INCOME',
	`ledger_id` = '".$ledger_id."',
	`transection_head_id` = '".$transaction_head_id."',
	`transection_to` = 'Account Head',
	`transection_to_id` = '0',
	`transection_by` = '".$transection_by_to."',
	`transection_by_id` = '".$to_transection_by_id."',
	`note` = '".$note."',
	`status` = 'Done',
	`transection_date` =  '" .  $transection_date . "',
	`in_amount` = 	'".$amount."',
	`data_inserted_from` = 	'MONEY-TRANSFER-TO',
	`date` = '" . date("Y-m-d") . "',
	`time` =  '" . date("h:i:s a") . "',
	`poster` =   '0',
	`brunch_id` =   '" . $to_brunch_id . "',
	`transection_id` = '".$TRANSECTING['last_id']."',
	`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'

	WHERE `id` = '".$TRANSECTING2['last_id']."'");

	$query->execute();




    QUICK_BALANCE::QUICK_OPENING_BALANCE('in_amount',$transection_by_to,$to_transection_by_id,$amount,$transection_date,$to_brunch_id);

	print "Transfer Success";

}else if($_POST['action'] == 'final_sales_invocie' ){

	$customer_id= clean($_POST['customer_id']);
	$discount= clean($_POST['discount']);
	$dispatch_from_which_brunch= clean($_POST['dispatch_from_which_brunch']);
	$transport_cost= clean($_POST['transport_cost']);
	$total_vat_cost= clean($_POST['total_vat_cost']);
	$invoice_payable= clean($_POST['invoice_payable']);
	$invice_or_quotation= clean($_POST['invice_or_quotation']);
	$sales_by= clean($_POST['sales_by']);
	$sales_person= clean($_POST['sales_person']);

	$invoice_date = date("Y-m-d", strtotime($_POST['invoice_date']));
	$related_id = clean($_POST['related_id']);
	$related_code = clean($_POST['related_code']);
    $brunch_id =  clean($_POST['brunch_id']);
    $narration =  clean($_POST['narration']);

	$draft_code_query = (!empty($_POST['draft_code'])) ? " draft_code = '".$_POST['draft_code']."' " : "  draft_code IS NULL  " ;
	$draft_code= (!empty($_POST['draft_code'])) ? $_POST['draft_code'] : NULL  ;


if($invice_or_quotation == 'Invoice'){

	if($related_id == 'New'){

		$prepaire_table = SETUP::GENERATE_INVOICE('sales_invoice',$invoice_date);
	    $code = $prepaire_table['related_code'];




	$query1 = $conn_me->prepare("UPDATE `sales_invoice` 
		
	SET
	`customer_id` = '".$customer_id."',
		`narration` = '".$narration."',
	`confirm_by_sales_manager` = 'Done',
	`sales_manager_id` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."',
	`sales_manager_confirm_date` ='".$invoice_date."',
	`dispatch_from_which_brunch` = '".$dispatch_from_which_brunch."',
	`brunch_id` =   '" . $brunch_id . "',
	`discount` ='".$discount."',
	`invoice_date` ='".$invoice_date."',
	`transport_cost` ='".$transport_cost."',
	`total_vat_cost` ='".$total_vat_cost."',
	`sales_by` ='".$sales_by."',
	`sales_person` ='".$sales_person."',
	`status` = 'Done'

	WHERE  `id` = '".$prepaire_table['last_id']."'  ");
	
	$query1->execute();


	$query2 = $conn_me->prepare("UPDATE `sales_invoice_item` 
		
		SET
		`sales_invoice_id` = '".$prepaire_table['last_id']."',
		`confirm_by_sales_manager` =  'Done',
		`sales_manager_id` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."',
		`sales_manager_confirm_date` =  '".$invoice_date."',
		`code` ='".$code."',
		`sales_by` ='".$sales_by."',
		`status` = 'Done'
		WHERE  `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `brunch_id` = '".$brunch_id."' AND `status` = 'Pending' AND  $draft_code_query  ");
		
		$query2->execute();
	
		
		
	$invoice_info = FIND::TOTAL_SALES_INVOICE_PRICE($prepaire_table['last_id']);
	$ac_note = "";
	QUICK_BALANCE::CUSTOMER_QUICK_DUE($customer_id,$invoice_info['price'],'invoice_amount',$invoice_date,$brunch_id);



	$data = [
		'invoice_id' => $prepaire_table['last_id'],
		'created_time' => date("d-m-Y h:i:s a"),
	  ];
	 CRUD::insert_data('invoice_timeline', $data);



		if($query1){
             $mess = "New Invoice Created";
		}else{
			$mess = "Somthing wrong";

		}



	}else{

		   $pendingItem = FIND::SALES_INVOICE_PENDING_ITEM_PRICE($related_id);
                 
				// Calculate the values for $quick_due variables
				$quick_due_1 = +$pendingItem['sub_total']; // check for new item added , in database status = 'Pending'
				$quick_due_2 = -$_POST['previous_vat'];  //  decrease invoice amount 
				$quick_due_3 = $total_vat_cost;  // increase invoice amount 
				$quick_due_4 =  $_POST['previous_discount']; // increase invoice amount 
				$quick_due_5 = -$discount; //  decrease invoice amount  


				$quick_due_total = $quick_due_1 + $quick_due_2 + $quick_due_3 + $quick_due_4 + $quick_due_5;
				QUICK_BALANCE::CUSTOMER_QUICK_DUE($customer_id, $quick_due_total, 'invoice_amount', $invoice_date,$brunch_id);

		
				$query1 = $conn_me->prepare("UPDATE `sales_invoice` 
							
						SET
						`customer_id` = '".$customer_id."',
						`narration` = '".$narration."',
						`confirm_by_sales_manager` = 'Done',
						`sales_manager_id` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."',
						`sales_manager_confirm_date` ='".$invoice_date."',
						`dispatch_from_which_brunch` = '".$dispatch_from_which_brunch."',
						`brunch_id` =   '" . $brunch_id . "',
						`discount` ='".$discount."',
						`invoice_date` ='".$invoice_date."',
						`transport_cost` ='".$transport_cost."',
						`total_vat_cost` ='".$total_vat_cost."',
						`sales_by` ='".$sales_by."',
						`sales_person` ='" . $sales_person . "',
						`status` = 'Done'
					
						WHERE  `id` = '".$related_id."'  ");
						
						$query1->execute();
			
						$query2 = $conn_me->prepare("UPDATE `sales_invoice_item` 
							
							SET
							`code` = '".$related_code."',
							`confirm_by_sales_manager` =  'Done',
							`sales_manager_id` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."',
							`sales_manager_confirm_date` =  '".$invoice_date."',
							`sales_by` ='".$sales_by."',
							`status` = 'Done'
							WHERE  `sales_invoice_id` = '".$related_id."'  ");
							
							$query2->execute();
			
		
			
						if($query1){
							$mess = "Invoice Update Success";
							
					   }else{
						   $mess = "Somthing wrong";
			   
					   }
			   


					   $code = '';

	}

	




}else if($_POST['invice_or_quotation'] == 'Quotation'){



	$prepaire_table = SETUP::SETUP_CODE_INSERT_DATA('quotation_invoice');
	$code = $prepaire_table['related_code'];
	$ac_note = "Quotation Date: $invoice_date Invoice No: $prepaire_table[invoice_no]";

	$query1 = $conn_me->prepare("UPDATE `quotation_invoice` 
		
	SET
	`customer_id` = '".$customer_id."',
	`invoice_date` ='".$invoice_date."',
	`sales_by` ='".$sales_by."',
	`sales_person` ='" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
	`brunch_id` ='" . $_SESSION['USER_BRUNCH'] . "',
	`status` = 'Done'

	WHERE  `id` = '".$prepaire_table['last_id']."'  ");
	
	$query1->execute();


	$query2 = $conn_me->prepare("UPDATE `quotation_invoice_item` 
		
		SET
		`quotation_invoice_id` = '".$prepaire_table['last_id']."',
		`code` ='".$prepaire_table['related_code']."',
		`status` = 'Done'
		WHERE  `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `brunch_id` = '".$_SESSION['USER_BRUNCH']."' AND `status` = 'Pending'  ");
		
		$query2->execute();
	


	if($query1){
	$mess = "New Quitation Created";
	}else{
	$mess = "Somthing wrong";

	}




	   
	}else if( $_POST['invice_or_quotation'] ==  'Preorder'){

	

			
	$prepaire_table = SETUP::SETUP_CODE_INSERT_DATA('preorder_invoice');
	$code = $prepaire_table['related_code'];
	$ac_note = "Preorder Date: $invoice_date Invoice No: $prepaire_table[invoice_no]";

	$query1 = $conn_me->prepare("UPDATE `preorder_invoice` 
		
	SET
	`customer_id` = '".$customer_id."',
	`invoice_date` ='".$invoice_date."',
	`sales_by` ='".$sales_by."',
	`sales_person` ='" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
	`brunch_id` =	'" . $_SESSION['USER_BRUNCH'] . "',
	`status` = 'Done'

	WHERE  `id` = '".$prepaire_table['last_id']."'  ");
	
	$query1->execute();


	$query2 = $conn_me->prepare("UPDATE `pre_order_invoice_item` 
		
		SET
		`preorder_invoice_id` = '".$prepaire_table['last_id']."',
		`code` ='".$prepaire_table['related_code']."',
		`status` = 'Done'
		WHERE  `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `brunch_id` = '".$_SESSION['USER_BRUNCH']."' AND `status` = 'Pending'  ");
		
		$query2->execute();
	


		if($query1){
$mess = "New Pre Invoice Created";
		}else{
			$mess = "Somthing wrong";

		}





	

}else{
	$mess = "Somthing wrong";
	$code = '';
}
	
      
		
		
		 print json_encode(array ('mess' => $mess , 'code' => $code ));


}else if($_POST['action'] == 'save_to_draft' ){


    $draft_code = time() . 'SAJID'  ;
    $customer_id= clean($_POST['customer_id']);
    
    $DraftCode = (!empty($customer_id)) ? $draft_code  . $customer_id : $draft_code . 0 ; 
    
    
    $query1 = $conn_me->prepare("UPDATE `sales_invoice_item` 
    
    SET
    `draft_code` = '".$DraftCode."'
    
    WHERE `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `brunch_id` = '".$_SESSION['USER_BRUNCH']."' AND `status` = 'Pending' AND draft_code IS NULL ");
    
    $query1->execute();
		


}else if($_POST['action'] == 'add_cart_sales_invocie' ){



	$invice_or_quotation= clean($_POST['invice_or_quotation']);
	$customer_id= clean($_POST['customer_id']);
	$product_id= clean($_POST['product_id']);
    $note= clean($_POST['note']);
	$quantity = clean($_POST['quantity']);
	$recommended_price= clean($_POST['recommended_price']);
	$draft_code_query = (!empty($_POST['draft_code'])) ? " draft_code = '".$_POST['draft_code']."' " : "  draft_code IS NULL  " ;
	$draft_code= (!empty($_POST['draft_code'])) ? $_POST['draft_code'] : NULL  ;



if($invice_or_quotation == 'Invoice'){

	if($_POST['related_id'] == 'New' ){  


		$query = $conn_me->prepare("SELECT *  FROM `sales_invoice_item`  where `product_id` = '".$product_id."' AND `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `brunch_id` = '".$_SESSION['USER_BRUNCH']."' AND `status` = 'Pending' AND $draft_code_query  ORDER BY `id` DESC");
        $query->execute(); 
        $count = $query->rowCount();

		if($count > 0 ){

			print "This product alredy in cart";
		}else{


$query = $conn_me->prepare("
    INSERT INTO `sales_invoice_item` 
    (
        `id`, `note`, `draft_code`, `product_id`, `sales_quantity`, 
        `recommended_price`, `sales_rate`, `sales_person`, `brunch_id`, 
        `poster`, `date`, `time`, `lastupdate`
    ) 
    VALUES
    (
        :id, :note, :draft_code, :product_id, :sales_quantity, 
        :recommended_price, :sales_rate, :sales_person, :brunch_id, 
        :poster, :date, :time, :lastupdate
    )
");

$query->execute([
    ':id' => 0,
    ':note' => $note,
    ':draft_code' => $draft_code, // Will automatically bind NULL if $draft_code is NULL
    ':product_id' => $product_id,
    ':sales_quantity' => $quantity,
    ':recommended_price' => $recommended_price,
    ':sales_rate' => $recommended_price,
    ':sales_person' => $_SESSION['NEWERP_SESS_MEMBER_ID'],
    ':brunch_id' => $_SESSION['USER_BRUNCH'],
    ':poster' => $_SESSION['NEWERP_SESS_MEMBER_ID'],
    ':date' => date("Y-m-d"),
    ':time' => date("h:i:s a"),
    ':lastupdate' => $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'],
]);





		print "Add Cart Success";

		}


		
		
		
	}else{

	
		$query = $conn_me->prepare("SELECT *  FROM `sales_invoice_item`  where `product_id` = '".$product_id."' AND `sales_invoice_id` = '".$_POST['related_id']."' ");
        $query->execute();
        $count = $query->rowCount();

		if($count > 0 ){

			print "This product alredy in cart";
		}else{

			$query = $conn_me->exec("INSERT INTO `sales_invoice_item` 
		( 
			`id`, `sales_invoice_id`, `note`, `product_id`, `sales_quantity`, `recommended_price`,`sales_rate`,`sales_person`,`brunch_id`, `poster`, `date`, `time`, `lastupdate`
		
		) 
		VALUES
		(
			'0',
			'".clean($_POST['related_id'])."',
		    '".$note."',
			'".$product_id."',
			'".$quantity."',
			'".$recommended_price."',
			'".$recommended_price."',
			'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
			'" . $_SESSION['USER_BRUNCH'] . "',
			'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
			'" . date("Y-m-d") . "',
			'" . date("h:i:s a") . "',
			'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'

		)  ");







		print "Add Cart Success";

		}

	}

}else if($invice_or_quotation == 'Quotation'){

	if($_POST['related_id'] == 'New' ){  


		$query = $conn_me->exec("INSERT INTO `quotation_invoice_item` 
		( 
			`id`, `note`, `product_id`, `quantity`, `recommended_price`,`sales_person`, `brunch_id`, `poster`, `date`, `time`, `lastupdate`

		) 
		VALUES
		(
			'0',
			'".$note."',
			'".$product_id."',
			'".$quantity."',
			'".$recommended_price."',
			'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
			'" . $_SESSION['USER_BRUNCH'] . "',
			'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
			'" . date("Y-m-d") . "',
			'" . date("h:i:s a") . "',
			'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'

		) ");
		
		print "Add Cart Success";


		
	}else{

		$query = $conn_me->prepare("UPDATE `quotation_invoice_item` 

		SET
	
		`product_id` = '".$product_id."',
		`quantity` = '".$quantity."',
		`recommended_price` = '".$recommended_price."',
		`note` = '".$note."',
		`date` = '" . date("Y-m-d") . "',
		`time` =  '" . date("h:i:s a") . "',
		`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
	
	
		WHERE `id` = '".$_POST['related_id']."'");
	
		$query->execute();

	print "Update Success";

	}


}else if($invice_or_quotation == 'Preorder'){

	if($_POST['related_id'] == 'New' ){  


		$query = $conn_me->exec("INSERT INTO `pre_order_invoice_item` 
		( 
			`id`, `note`, `product_id`, `quantity`, `recommended_price`, `sales_person`,`brunch_id`, `poster`, `date`, `time`, `lastupdate`
		
		) 
		VALUES
		(
			'0',
			'".$note."',
			'".$product_id."',
			'".$quantity."',
			'".$recommended_price."',
			'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
			'" . $_SESSION['USER_BRUNCH'] . "',
			'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
			'" . date("Y-m-d") . "',
			'" . date("h:i:s a") . "',
			'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'

		) ");



		print "Add Cart Success";

		
	}else{
		$query = $conn_me->prepare("UPDATE `pre_order_invoice_item` 

		SET
	
		`product_id` = '".$product_id."',
		`quantity` = '".$quantity."',
		`recommended_price` = '".$recommended_price."',
		`note` = '".$note."',
		`date` = '" . date("Y-m-d") . "',
		`time` =  '" . date("h:i:s a") . "',
		`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
	
	
		WHERE `id` = '".$_POST['related_id']."'");
	
		$query->execute();

	print "Update Success";

	}

}else{

}
	



}else if($_POST['action'] == 'save_expense_transection' ){

	$transection_type = 'EXPENSE';
	$transaction_type= clean($_POST['transaction_type']);

	$transection_head_id= clean($_POST['transection_head_id']);
	$amount= clean($_POST['receive_now']);
	$transection_by= clean($_POST['transection_by']);
	$transection_by_id= clean($_POST['transection_by_id']);
	$check_number= clean($_POST['check_number']);
	$check_date= clean($_POST['check_date']);
	$transection_to= clean($_POST['transection_to']);
	$transection_to_id= clean($_POST['transection_to_id']);
	$note= clean($_POST['note']);
	$data_inserted_from= clean($_POST['data_inserted_from']);
	$ledger_id= clean($_POST['ledger_id']);

	$transection_date = date("Y-m-d", strtotime($_POST['transection_date']));


	if($transaction_type == 'PAYMENT' ){
		$amount= $amount;
	}else{
		$amount= -$amount;
	}


	if($_POST['related_id'] == 'new_id' ){


		$TRANSECTIONE = SETUP::SETUP_CODE_INSERT_DATA('account_transection');

		$CODE = $TRANSECTIONE['related_code'];
		$id = $TRANSECTIONE['last_id'];

		$query = $conn_me->prepare("UPDATE `account_transection` 

		SET
		`transection_type` = '".$transection_type."',
		`ledger_id` = '".$ledger_id."',
        `transection_head_id` = '".$transection_head_id."',
        `transection_to` = '".$transection_to."',
        `transection_to_id` = '".$transection_to_id."',
		`transection_by` = '".$transection_by."',
		`transection_by_id` = '".$transection_by_id."',
        `check_number` = '".$check_number."',
		`check_date` = '".$check_date."',
        `note` = '".$note."',
		`status` = 'Done',
        `transection_date` =  '" . $transection_date . "',
		`out_amount` = 	'".$amount."',
		`data_inserted_from` = 	'".$data_inserted_from."',
		`date` = '" . date("Y-m-d") . "',
		`time` =  '" . date("h:i:s a") . "',
		`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		`brunch_id` =   '" . $_SESSION['USER_BRUNCH'] . "',
		`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


		WHERE `id` = '".$TRANSECTIONE['last_id']."'");

		$query->execute();


		if($transection_to == 'Supplier' ){
			QUICK_BALANCE::SUPPLIER_QUICK_DUE($transection_to_id,$amount,'payment_amount',$transection_date);
		}
		QUICK_BALANCE::QUICK_OPENING_BALANCE('out_amount',$transection_by,$transection_by_id,$amount,$transection_date,$_SESSION['USER_BRUNCH']);




		if ($query) {
			$mess =  'Insert Success' ;
		} 
		else {
			$mess =  'Insert Failed';
		}





	}else{

			$CODE = $_POST['code'];
			$id = $_POST['related_id'];

			$ck1 = $conn_me->prepare("SELECT *  FROM `account_transection` where `id` = '".$id."' ");
			$ck1->execute();
			$fe_ck1 = $ck1->fetch(PDO::FETCH_ASSOC);
	
			QUICK_BALANCE::QUICK_OPENING_BALANCE('out_amount',$fe_ck1['transection_by'],$fe_ck1['transection_by_id'],-$fe_ck1['out_amount'],$fe_ck1['transection_date'],$fe_ck1['brunch_id']);
	
			$query = $conn_me->prepare("UPDATE `account_transection` 

		SET
		`transection_type` = '".$transection_type."',
		`ledger_id` = '".$ledger_id."',
        `transection_head_id` = '".$transection_head_id."',
        `transection_to` = '".$transection_to."',
        `transection_to_id` = '".$transection_to_id."',
		`transection_by` = '".$transection_by."',
		`transection_by_id` = '".$transection_by_id."',
        `check_number` = '".$check_number."',
        `note` = '".$note."',
        `transection_date` =  '" .$transection_date . "',
		`out_amount` = 	'".$amount."',
		`date` = '" . date("Y-m-d") . "',
		`time` =  '" . date("h:i:s a") . "',
		`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


		WHERE `id` = '".$id."'");

		$query->execute();





		QUICK_BALANCE::QUICK_OPENING_BALANCE('out_amount',$transection_by,$transection_by_id,$amount,$transection_date,$_SESSION['USER_BRUNCH']);



		if ($query) {
			$mess =  'Update Success' ;
		} 
		else {
			$mess =  'Update Failed';
		}




	}




	print json_encode(array ('mess' => $mess ,'transection_id' => $id));


	

}else if($_POST['action'] == 'save_pending_expense_transection' ){


	$transection_head_id= clean($_POST['transection_head_id']);
	$amount= clean($_POST['receive_now']);
	$transection_by= clean($_POST['transection_by']);
	$transection_by_id= clean($_POST['transection_by_id']);
	$check_number=  (!empty($_POST['check_number'])) ? clean($_POST['check_number']) : 'NULL';
	$check_date= (!empty($_POST['check_date'])) ? clean($_POST['check_date']) : '0000-00-00'; 
	$transection_to=  clean($_POST['transection_to']);
	$transection_to_id= clean($_POST['transection_to_id']);
	$note= clean($_POST['note']);
	$data_inserted_from= clean($_POST['data_inserted_from']);
	$ledger_id= clean($_POST['ledger_id']);
	$tr_type= clean($_POST['tr_type']);
	$transection_date = date("Y-m-d", strtotime($_POST['transection_date']));
	$extra_field= (!empty($_POST['extra_field'])) ? clean($_POST['extra_field']) : 0; 




	if($tr_type == 'PAYMENT' ){
	$transection_type = 'EXPENSE';
	$in_amount = 0 ; 
	$out_amount = $amount; 
	}else if ($tr_type == 'RECEIVE'){
	$transection_type = 'INCOME';
	$in_amount =$amount ; 
	$out_amount = 0; 
	}else{
	$transection_type = 'INCOME';
	$in_amount = 0 ; 
	$out_amount = 0; 

	}



	if($_POST['related_id'] == 'new_id' ){


		$TRANSECTIONE = SETUP::SETUP_CODE_INSERT_DATA('account_posting_pending');

		$CODE = $TRANSECTIONE['related_code'];
		$id = $TRANSECTIONE['last_id'];

		$query = $conn_me->prepare("UPDATE `account_posting_pending` 

		SET
		`transection_type` = '".$transection_type."',
		`ledger_id` = '".$ledger_id."',
        `transection_head_id` = '".$transection_head_id."',
        `transection_to` = '".$transection_to."',
        `transection_to_id` = '".$transection_to_id."',
		`transection_by` = '".$transection_by."',
		`transection_by_id` = '".$transection_by_id."',
        `check_number` = '".$check_number."',
		`check_date` = '".$check_date."',
        `note` = '".$note."',
		`status` = 'Done',
        `transection_date` =  '" . $transection_date . "',
		`in_amount` = 	'".$in_amount."',
		`out_amount` = 	'".$out_amount."',
		`data_inserted_from` = 	'".$data_inserted_from."',
		`date` = '" . date("Y-m-d") . "',
		`time` =  '" . date("h:i:s a") . "',
		`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		`brunch_id` =   '" . $_SESSION['USER_BRUNCH'] . "',
		`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


		WHERE `id` = '".$id."'");

		$query->execute();


		if ($query) {
			$mess =  'Insert Success' ;
		} 
		else {
			$mess =  'Insert Failed';
		}





	}else{

			$CODE = $_POST['code'];
			$id = $_POST['related_id'];


			$query = $conn_me->prepare("UPDATE `account_posting_pending` 

		SET
		`transection_type` = '".$transection_type."',
		`ledger_id` = '".$ledger_id."',
        `transection_head_id` = '".$transection_head_id."',
        `transection_to` = '".$transection_to."',
        `transection_to_id` = '".$transection_to_id."',
		`transection_by` = '".$transection_by."',
		`transection_by_id` = '".$transection_by_id."',
        `check_number` = '".$check_number."',
        `note` = '".$note."',
        `transection_date` =  '" .$transection_date . "',
		`out_amount` = 	'".$amount."',
		`date` = '" . date("Y-m-d") . "',
		`time` =  '" . date("h:i:s a") . "',
		`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


		WHERE `id` = '".$id."'");

		$query->execute();



		if ($query) {
			$mess =  'Update Success' ;
		} 
		else {
			$mess =  'Update Failed';
		}




	}




	print json_encode(array ('mess' => $mess ,'transection_id' => $id));


	

}else if($_POST['action'] == 'save_pending_income_transection' ){

	

	$transection_head_id= clean($_POST['transection_head_id']);
	$amount= clean($_POST['receive_now']);
	$transection_by= clean($_POST['transection_by']);
	$transection_by_id= clean($_POST['transection_by_id']);
	
	$check_number=  (!empty($_POST['check_number'])) ? clean($_POST['check_number']) : 'NULL';
	
   $collect_by=  (!empty($_POST['collect_by'])) ? clean($_POST['collect_by']) : 'NULL';
	
	$check_date= (!empty($_POST['check_date'])) ? clean($_POST['check_date']) : '0000-00-00'; 
	$transection_to=  clean($_POST['transection_to']);
	$transection_to_id= clean($_POST['transection_to_id']);
	$note= clean($_POST['note']);
	$data_inserted_from= clean($_POST['data_inserted_from']);
	$ledger_id= clean($_POST['ledger_id']);
	$tr_type= clean($_POST['tr_type']);
	$transection_date = date("Y-m-d", strtotime($_POST['transection_date']));
	$extra_field= (!empty($_POST['extra_field'])) ? clean($_POST['extra_field']) : 0; 


if($tr_type == 'INCREASE-DUE'){

	$query2 = $conn_me->exec("INSERT INTO `balance_customer` 
	( 
		`id`, `customer_id`, `invoice_amount`, `brunch_id`,`date`,`note`,`actual_note`,`poster`
	) 
	VALUES
	(
		'0',
		'".$transection_to_id."',
		'".$amount."',
		'" . $_SESSION['USER_BRUNCH'] . "',
		'".$transection_date."',
		'LAST DUE',
		'".$note."',
		'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "'
	
	) ");


	if($query2){
		$mess = "Due Increased at date $_POST[transection_date]";
		$id = "NOID";
	}

}else if ($tr_type == 'DISCOUNT'){

	$query2 = $conn_me->exec("INSERT INTO `balance_customer` 
	( 
		`id`, `customer_id`,`return_amount`, `brunch_id`,`date`,`note`,`actual_note`,`poster`
	) 
	VALUES
	(
		'0',
		'".$transection_to_id."',
		'".$amount."',
		'" . $_SESSION['USER_BRUNCH'] . "',
		'".$transection_date."',
		'DISCOUNT',
		'".$note."',
		'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "'

	
	) ");


	if($query2){

		$mess = "Discount given successfully";
		$id = "NOID";
	
	}


}else{

	if($tr_type == 'PAYMENT' ){
	$transection_type = 'EXPENSE';
	$in_amount = 0 ; 
	$out_amount = $amount; 
	}else if ($tr_type == 'RECEIVE'){
	$transection_type = 'INCOME';
	$in_amount =$amount ; 
	$out_amount = 0; 
	}else{
	$transection_type = 'INCOME';
	$in_amount = 0 ; 
	$out_amount = 0; 

	}

	if($_POST['related_id'] == 'new_id' ){


		$TRANSECTIONE = SETUP::SETUP_CODE_INSERT_DATA('account_posting_pending');

		$CODE = $TRANSECTIONE['related_code'];
		$id = $TRANSECTIONE['last_id'];



		try {
    $query = $conn_me->prepare("
        UPDATE `account_posting_pending` 
        SET
            `transection_type` = '".$transection_type."',
            `ledger_id` = '".$ledger_id."',
            `transection_head_id` = '".$transection_head_id."',
            `transection_to` = '".$transection_to."',
            `transection_to_id` = '".$transection_to_id."',
            `transection_by` = '".$transection_by."',
            `transection_by_id` = '".$transection_by_id."',
            `check_number` = '".$check_number."',
            `collect_by` = '".$collect_by."',
            `check_date` = '".$check_date."',
            `note` = '".$note."',
            `status` = 'Done',
            `transection_date` = '".$transection_date."',
            `in_amount` = '".$in_amount."',
            `out_amount` = '".$out_amount."',
            `data_inserted_from` = '".$data_inserted_from."',
            `extra_field` = '".$extra_field."',
            `date` = '" . date("Y-m-d") . "',
            `time` = '" . date("h:i:s a") . "',
            `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."',
            `brunch_id` = '".$_SESSION['USER_BRUNCH']."',
            `lastupdate` = '".$_SESSION['NEWERP_SESS_MEMBER_ID'].' Date: '.date("d-M-Y").' IP '.$_SERVER['REMOTE_ADDR']."'
        WHERE `id` = '".$id."'
    ");

    $query->execute();

    if ($query->rowCount() > 0) {
        $mess = 'Update Success';
    } else {
        $mess = 'No rows updated. Check your WHERE condition.';
    }
} catch (PDOException $e) {
    // Capture and display the PDO error
    $mess = 'Error: ' . $e->getMessage() . ' ' .  $transection_by_id ;
}





	}else{

			$CODE = $_POST['code'];
			$id = $_POST['related_id'];


			$query = $conn_me->prepare("UPDATE `account_posting_pending` 

		SET
		`transection_type` = '".$transection_type."',
		`ledger_id` = '".$ledger_id."',
        `transection_head_id` = '".$transection_head_id."',
        `transection_to` = '".$transection_to."',
        `transection_to_id` = '".$transection_to_id."',
        `collect_by` = '".$collect_by."',
		`transection_by` = '".$transection_by."',
		`transection_by_id` = '".$transection_by_id."',
        `check_number` = '".$check_number."',
        `note` = '".$note."',
        `transection_date` =  '" . $transection_date . "',
		`in_amount` = 	'".$amount."',
		`date` = '" . date("Y-m-d") . "',
		`time` =  '" . date("h:i:s a") . "',
		`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


		WHERE `id` = '".$id."'");

		$query->execute();

		
		if ($query) {
			$mess =  'Update Success' ;
		} 
		else {
			$mess =  'Update Failed';
		}




	}




}


	print json_encode(array ('mess' => $mess ,'transection_id' => $id));


	
}else if($_POST['action'] == 'save_income_transection' ){

	$transection_type= 'INCOME';
	$transection_head_id= clean($_POST['transection_head_id']);
	$amount= clean($_POST['receive_now']);
	$transection_by= clean($_POST['transection_by']);
	$transection_by_id= clean($_POST['transection_by_id']);
	$check_number= clean($_POST['check_number']);
	$check_date= clean($_POST['check_date']);
	$transection_to=  clean($_POST['transection_to']);
	$transection_to_id= clean($_POST['transection_to_id']);
	$note= clean($_POST['note']);
	$data_inserted_from= clean($_POST['data_inserted_from']);
	$ledger_id= clean($_POST['ledger_id']);
	$transaction_type= clean($_POST['transaction_type']);
	$transection_date = date("Y-m-d", strtotime($_POST['transection_date']));


if($transaction_type == 'INCREASE-DUE'){

	$query2 = $conn_me->exec("INSERT INTO `balance_customer` 
	( 
		`id`, `customer_id`, `invoice_amount`, `brunch_id`,`date`,`note`
	) 
	VALUES
	(
		'0',
		'".$transection_to_id."',
		'".$amount."',
		'" . $_SESSION['USER_BRUNCH'] . "',
		'".$transection_date."',
		'LAST DUE'
	
	) ");


	if($query2){
		$mess = "Due Increased at date $_POST[transection_date]";
		$id = "NOID";
	}


}else{

	if($transaction_type == 'PAYMENT' ){
		$amount= -$amount;
		$field = 'return_amount';
        
	}else{

		$amount= $amount;
		$field = 'receive_amount';
	}

	if($_POST['related_id'] == 'new_id' ){


		$TRANSECTIONE = SETUP::SETUP_CODE_INSERT_DATA('account_transection');

		$CODE = $TRANSECTIONE['related_code'];
		$id = $TRANSECTIONE['last_id'];
        $brunch_id = $_SESSION['USER_BRUNCH'] ; 

		if($transection_to == 'Customer' ){
			
			QUICK_BALANCE::CUSTOMER_QUICK_DUE($transection_to_id,$_POST['receive_now'],$field,$transection_date,$_SESSION['USER_BRUNCH']);
		}
		

		$query = $conn_me->prepare("UPDATE `account_transection` 

		SET
		`transection_type` = '".$transection_type."',
		`ledger_id` = '".$ledger_id."',
        `transection_head_id` = '".$transection_head_id."',
        `transection_to` = '".$transection_to."',
        `transection_to_id` = '".$transection_to_id."',
		`transection_by` = '".$transection_by."',
		`transection_by_id` = '".$transection_by_id."',
        `check_number` = '".$check_number."',
		`check_date` = '".$check_date."',
        `note` = '".$note."',
		`status` = 'Done',
        `transection_date` =  '" . $transection_date . "',
		`in_amount` = 	'".$amount."',
		`data_inserted_from` = 	'".$data_inserted_from."',
		`date` = '" . date("Y-m-d") . "',
		`time` =  '" . date("h:i:s a") . "',
		`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		`brunch_id` =   '" . $brunch_id . "',
		`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


		WHERE `id` = '".$TRANSECTIONE['last_id']."'");

		$query->execute();



		QUICK_BALANCE::QUICK_OPENING_BALANCE('in_amount',$transection_by,$transection_by_id,$amount,$transection_date,$brunch_id);



		if ($query) {
			$mess =  'Insert Success' ;
		} 
		else {
			$mess =  'Insert Failed';
		}




	}else{

			$CODE = $_POST['code'];
			$id = $_POST['related_id'];


			$ck1 = $conn_me->prepare("SELECT *  FROM `account_transection` where `id` = '".$id."' ");
			$ck1->execute();
			$fe_ck1 = $ck1->fetch(PDO::FETCH_ASSOC);

			$brunch_id = $fe_ck1['brunch_id'] ; 


			QUICK_BALANCE::QUICK_OPENING_BALANCE('in_amount',$fe_ck1['transection_by'],$fe_ck1['transection_by_id'],-$fe_ck1['in_amount'],$fe_ck1['transection_date'],$brunch_id);


			if($transection_to == 'Customer' ){

				QUICK_BALANCE::CUSTOMER_QUICK_DUE($fe_ck1['transection_to_id'],-$fe_ck1['in_amount'],'receive_amount',$fe_ck1['transection_date'],$brunch_id);

				QUICK_BALANCE::CUSTOMER_QUICK_DUE($fe_ck1['transection_to_id'],$amount,'receive_amount',$fe_ck1['transection_date'],$brunch_id);
	
				}



			$query = $conn_me->prepare("UPDATE `account_transection` 

		SET
		`transection_type` = '".$transection_type."',
		`ledger_id` = '".$ledger_id."',
        `transection_head_id` = '".$transection_head_id."',
        `transection_to` = '".$transection_to."',
        `transection_to_id` = '".$transection_to_id."',
		`transection_by` = '".$transection_by."',
		`transection_by_id` = '".$transection_by_id."',
        `check_number` = '".$check_number."',
        `note` = '".$note."',
        `transection_date` =  '" . $transection_date . "',
		`in_amount` = 	'".$amount."',
		`date` = '" . date("Y-m-d") . "',
		`time` =  '" . date("h:i:s a") . "',
		`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


		WHERE `id` = '".$id."'");

		$query->execute();


		QUICK_BALANCE::QUICK_OPENING_BALANCE('in_amount',$transection_by,$transection_by_id,$amount,$transection_date,$brunch_id);



		
		if ($query) {
			$mess =  'Update Success' ;
		} 
		else {
			$mess =  'Update Failed';
		}




	}


      if($data_inserted_from == 'Invoice Wise Payment' ){
		
		$quick_due_4 =  $_POST['previous_discount']; // increase invoice amount 
		$quick_due_5 = - $_POST['discount']; //  decrease invoice amount  
		$quick_due_total = $quick_due_4 + $quick_due_5;
		QUICK_BALANCE::CUSTOMER_QUICK_DUE($transection_to_id, $quick_due_total, 'invoice_amount', $transection_date,$brunch_id);

		
		$query = $conn_me->prepare("UPDATE `sales_invoice` 

		SET
		`transection_id` = '".$id."', 
		`discount` = '".$_POST['discount']."'
		WHERE `id` = '".$_POST['extra_field']."'");

		$query->execute();
	  }



}


	
	  

	print json_encode(array ('mess' => $mess ,'transection_id' => $id));

}else if($_POST['action'] == 'save_mobile_bank' ){

	



	$mobile_bank_name= clean($_POST['mobile_bank_name']);
	$mobile_number= clean($_POST['mobile_number']);
	$description= clean($_POST['description']);
	$status= clean($_POST['status']);

  

	if($_POST['related_id'] == 'new_id' ){

	$query = $conn_me->exec("INSERT INTO `setup_mobile_banking` 
( 
	`id`, `mobile_bank_name`, `mobile_number`, `description`, `status`, `date`, `time`, `poster`, `lastupdate`

) 
VALUES
(
	'0',
	'".$mobile_bank_name."',
    '".$mobile_number."',
	'".$description."',
	'".$status."',
	'" . date("Y-m-d") . "',
    '" . date("h:i:s a") . "',
    '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
    '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
) ");



		if ($query) {
			echo 'Insert Success' ;
		} 
		else {
			echo 'Insert Failed';
		}



	}else{

		$query = $conn_me->prepare("UPDATE `setup_mobile_banking` 

		SET
		`mobile_bank_name` = '".$mobile_bank_name."',
        `mobile_number` = '".$mobile_number."',
        `description` = '".$description."',
		`status` = '".$status."',
		`date` = '" . date("Y-m-d") . "',
		`time` =  '" . date("h:i:s a") . "',
		`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


		WHERE `id` = '".$_POST['related_id']."'");

		$query->execute();

		if ($query) {
			echo 'Update Success' ;
		} 
		else {
			echo 'Update Failed';
		}



	}


}else if($_POST['action'] == 'save_bank' ){



	$bank_name= clean($_POST['bank_name']);
	$brunch_name= clean($_POST['brunch_name']);
	$account_number= clean($_POST['account_number']);
	$account_name= clean($_POST['account_name']);
	$description= clean($_POST['description']);
	$status= clean($_POST['status']);

  

	if($_POST['related_id'] == 'new_id' ){

	$query = $conn_me->exec("INSERT INTO `setup_bank` 
( 
	`id`, `bank_name`, `brunch_name`, `account_number`, `account_name`, `description`, `status`, `date`, `time`, `poster`, `lastupdate`

) 
VALUES
(
	'0',
	'".$bank_name."',
    '".$brunch_name."',
    '".$account_number."',
	'".$account_name."',
	'".$description."',
	'".$status."',
	'" . date("Y-m-d") . "',
    '" . date("h:i:s a") . "',
    '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
    '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
) ");



		if ($query) {
			echo 'Insert Success' ;
		} 
		else {
			echo 'Insert Failed';
		}



	}else{

		$query = $conn_me->prepare("UPDATE `setup_bank` 

		SET
		`bank_name` = '".$bank_name."',
        `brunch_name` = '".$brunch_name."',
        `account_number` = '".$account_number."',
        `account_name` = '".$account_name."',
        `description` = '".$description."',
		`status` = '".$status."',
		`date` = '" . date("Y-m-d") . "',
		`time` =  '" . date("h:i:s a") . "',
		`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


		WHERE `id` = '".$_POST['related_id']."'");

		$query->execute();

		if ($query) {
			echo 'Update Success' ;
		} 
		else {
			echo 'Update Failed';
		}



	}


} else if($_POST['action'] == 'save_timetable' ){

	$on_duty_time= clean($_POST['on_duty_time']);
	$off_duty_time= clean($_POST['off_duty_time']);
	$late_time= clean($_POST['late_time']);
	$leave_early= clean($_POST['leave_early']);




	$query = $conn_me->prepare("UPDATE `setup_timetable` 

	SET
	`on_duty_time` = '".$on_duty_time."',
	`off_duty_time` = '".$off_duty_time."',
	`late_time` = '".$late_time."',
	`leave_early` = '".$leave_early."',

	`date` = '" . date("Y-m-d") . "',
	`time` =  '" . date("h:i:s a") . "',
	`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
	`brunch` =    '" . $_SESSION['USER_BRUNCH'] . "',
	`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


	WHERE `id` = '".$_POST['related_id']."'");

	$query->execute();

	if ($query) {
		echo 'Update Success' ;
	} 
	else {
		echo 'Update Failed';
	}



} else if($_POST['action'] == 'save_department' ){

	$department_name= clean($_POST['department_name']);



	if($_POST['related_id'] == 'new_id' ){

	$query = $conn_me->exec("INSERT INTO `setup_department` 
( 
	`id` , `department` , `date`, `time`, `poster`, `lastupdate`

) 
VALUES
(
	'0',
	'".$department_name."',
	'" . date("Y-m-d") . "',
    '" . date("h:i:s a") . "',
    '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
    '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
) ");



		if ($query) {
			echo 'Insert Success' ;
		} 
		else {
			echo 'Insert Failed';
		}



	}else{

		$query = $conn_me->prepare("UPDATE `setup_department` 

		SET
		`department` = '".clean($department_name)."',
		`date` = '" . date("Y-m-d") . "',
		`time` =  '" . date("h:i:s a") . "',
		`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


		WHERE `id` = '".$_POST['related_id']."'");

		$query->execute();

		if ($query) {
			echo 'Update Success' ;
		} 
		else {
			echo 'Update Failed';
		}



	}



}else if ($_POST['action'] == 'update_personal_profile'){

	$birth_date = date("Y-m-d", strtotime($_POST['birth_date']));

	if(preg_match('^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*^', $_POST['user_password'])) {
	


		$query1 = $conn_me->prepare("UPDATE `admin` 
		
					SET
			`username` = '".$_POST['username']."',
			`password` = 	'".password_hash("$_POST[user_password]",PASSWORD_DEFAULT)."',
			`dypricpt_pass` = '".$_POST['user_password']."',
			`date` = '" . date("Y-m-d") . "',
			`time` =  '" . date("h:i:s a") . "',
			`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
			`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
			
			WHERE `id` = '".$_POST['admin_id']."'");
			
					$query1->execute();





		$query = $conn_me->prepare("UPDATE `setup_employee` 
		
	SET
`name` = '".$_POST['hr_name']."',
`fa_name` = '".$_POST['fa_name']."',
`mo_name` = '".$_POST['mo_name']."',
`birth_date` = '".$birth_date."',
`mob_no` = '".$_POST['mob_no']."',
`email` =  '".$_POST['email']."',
`date` = '" . date("Y-m-d") . "',
`time` =  '" . date("h:i:s a") . "',
`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'

	WHERE `id` = '".$_POST['employee_id']."'");

	$query->execute();

	if ($query) {
		echo 'Profile Save' ;
	} 
	else {
		echo 'Update Failed';
	}



	}else{

		print 'Must be a minimum of 8 characters
		Must contain at least 1 number
		Must contain at least one uppercase character
		Must contain at least one lowercase character';
	

	}
	


}else if ($_POST['action'] == 'save_company_profile'){

	$query = $conn_me->prepare("UPDATE `setup_company` 
		
	SET
`name` = '".$_POST['company_name']."',
`short_name` = '".$_POST['company_short_name']."',
`address` = '".$_POST['company_address']."',
`phone` = '".$_POST['company_phone']."',
`email` = '".$_POST['company_email']."',
`date` = '" . date("Y-m-d") . "',
`time` =  '" . date("h:i:s a") . "',
`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


	WHERE `id` = '".$_POST['related_id']."'");

	$query->execute();

	if ($query) {
		echo 'Profile Save' ;
	} 
	else {
		echo 'Update Failed';
	}

	


}else if ($_POST['action'] == 'final_demand'){


    $invoice_date = date("Y-m-d");
	$CODE = SETUP::SETUP_CODE('demand');
	

	$prepaire_table = SETUP::SETUP_CODE_INSERT_DATA('demand');
	$code = $prepaire_table['related_code'];
	$invoice_no = $prepaire_table['invoice_no'];



	$data = [

				'status' => 'Done',
				'demand_created_from' => $_SESSION['USER_BRUNCH'],
				'demand_created_to' => $_POST['brunch_id'],
				'notes' => $_POST['notes'],
				'code' => $code,
				'invoice_no' => $invoice_no,
				'invoice_date' => $invoice_date,
				'date' => date("Y-m-d"),
				'time' =>  date("h:i:s a"),
				'poster' =>   $_SESSION['NEWERP_SESS_MEMBER_ID'] ,
				'lastupdate' =>   $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] 
	];


	$query =   CRUD::updateData('demand',$prepaire_table['last_id'],$data);
	$mess = $query['mess'] ;



	$query = $conn_me->prepare("UPDATE `demand_item` SET `status` = 'Done',`demand_id` = '".$prepaire_table['last_id']."' WHERE `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `status` = 'Pending' 
	");
	$query->execute();


print $mess; 
die();


}else if ($_POST['action'] == 'add_cart_demand'){
    
    
    
// Check if the product is already in the cart
$ck_mob = $conn_me->prepare("SELECT `id` FROM `demand_item` WHERE `product_id` = :product_id AND poster = :poster AND status = 'Pending'");
$ck_mob->execute([
    'product_id' => $_POST['product_id'],
    'poster' => $_SESSION['NEWERP_SESS_MEMBER_ID']
]);

if ($ck_mob->rowCount() > 0) {
    echo 'Product already in cart';
    exit;
}

// Determine invoice ID and status
$invoice_id = ($_POST['invoice_id'] === 'New') ? NULL : $_POST['invoice_id'];
$status = ($_POST['invoice_id'] === 'New') ? 'Pending' : 'Done';

// Prepare data for insertion or update
$data = [
    'demand_id'  => $invoice_id,
    'status'     => $status,
    'product_id' => clean($_POST['product_id']),
    'quantity'   => clean($_POST['quantity']),
    'date'       => date("Y-m-d"),
    'time'       => date("h:i:s a"),
    'poster'     => $_SESSION['NEWERP_SESS_MEMBER_ID'],
    'lastupdate' => $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR']
];

// Insert or update data based on `related_id`
if ($_POST['related_id'] === 'New') {
    $query = CRUD::insert_data('demand_item', $data);
} else {
    $query = CRUD::updateData('demand_item', $_POST['related_id'], $data);
}

// Output success message
echo 'Added to cart successfully';
exit;


}else if ($_POST['action'] == 'create_user'){


	if(preg_match('^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*^', $_POST['user_password'])) {



		if($_POST['related_id'] == 'new_id' ){
	
			$ck_mob = $conn_me->prepare("SELECT `username` FROM `admin` WHERE `username` = '".$_POST['user_name']."' ");
			$ck_mob->execute();
	
			if ($ck_mob->rowCount() > 0) {
				print 'User Name Taken';
	
			}else{
	
				
			$query = $conn_me->exec("INSERT INTO `admin` 
		( 
			`id`, `employee_id`, `username`,`user_type`, `password`, `dypricpt_pass`, `brunch_id`,`date`, `time`,`poster`, `lastupdate`
		) 
		VALUES
		(
			'0',
			'".$_POST['employee_id']."',
			'".$_POST['user_name']."',
			'".$_POST['user_type']."',
			'".password_hash("$_POST[user_password]",PASSWORD_DEFAULT)."',
			'".$_POST['user_password']."',
			'".$_POST['brunch_id']."',
		'" . date("Y-m-d") . "',
		'" . date("h:i:s a") . "',
		'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
		
		) ");
		
		
		
				if ($query) {
					echo '1' ;
				} 
				else {
					echo 'Insert Failed';
				}
		
		


			}
			}else{
		
				$ck_mob = $conn_me->prepare("SELECT `username` FROM `admin` WHERE `username` = '".$_POST['user_name']."' AND `employee_id` <> '".$_POST['employee_id']."' ");
				$ck_mob->execute();
		
				if ($ck_mob->rowCount() > 0) {
					print 'User Name Taken';
		
				}else{

					$query = $conn_me->prepare("UPDATE `admin` 
		
					SET
			`username` = '".$_POST['user_name']."',
			`password` = 	'".password_hash("$_POST[user_password]",PASSWORD_DEFAULT)."',
			`user_type` = '".$_POST['user_type']."',
			`dypricpt_pass` = '".$_POST['user_password']."',
			`brunch_id` = '".$_POST['brunch_id']."',
			`date` = '" . date("Y-m-d") . "',
			`time` =  '" . date("h:i:s a") . "',
			`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
			`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
			
			
					WHERE `id` = '".$_POST['related_id']."'");
			
					$query->execute();
			
					if ($query) {
						echo '2' ;
					} 
					else {
						echo 'Update Failed';
					}
				}



		
		
			}
			
	
	} else {
	
		print 'Must be a minimum of 8 characters
		Must contain at least 1 number
		Must contain at least one uppercase character
		Must contain at least one lowercase character';
	}

}else if ($_POST['action'] == 'save_employee_data'){

	$birth_date = date("Y-m-d", strtotime($_POST['birth_date']));
	$join_d = date("Y-m-d", strtotime($_POST['join_d']));


		if($_POST['related_id'] == 'new_id' ){
	
			$query =  $conn_me->exec("INSERT INTO `setup_employee` 
			( 
				`id`, `code`, `name`, `gender`,`matrial_status`,`referrer`,`supervisor`,`nominee_information`,`bank_account`,`medical`,`joining_department`, `birth_date`,`po_office`,`present_department`, `present_section`, `joining_section`, `designation`, `joining_designation`, `fa_name`, `mo_name`, `mob_no`, `nationality`, `division_id`,`district_id`,`upazila_id`,`union_id`,`village`,`house`, `nid`, `religion`, `email`, `edu_qul`, `previous_company`, `joining_salary`, `present_salary`, `house_rent`, `da`,`ta`, `provident_fund`,`basic`, `over_time_bill`, `join_d`,`status`,  `date`, `time`, `poster`, `lastupdate`

			) 
			VALUES
			(
				'0',
				'".clean($_POST['show_code'])."',
				'".clean($_POST['name'])."',
				'".clean($_POST['gender'])."',
				'".clean($_POST['matrial_status'])."',
				'".clean($_POST['referrer'])."',
				'".clean($_POST['supervisor'])."',
				'".clean($_POST['nominee_information'])."',
				'".clean($_POST['bank_account'])."',
				'".clean($_POST['medical'])."',
				'".clean($_POST['joining_department'])."',
				'".clean($birth_date)."',				
				'".clean($_POST['po_office'])."',
				'".clean($_POST['present_department'])."',
				'".clean($_POST['present_section'])."',
				'".clean($_POST['joining_section'])."',
				'".clean($_POST['designation'])."',
				'".clean($_POST['joining_designation'])."',
				'".clean($_POST['fa_name'])."',
				'".clean($_POST['mo_name'])."',
				'".clean($_POST['mob_no'])."',
				'".clean($_POST['nationality'])."',
				'".clean($_POST['division_id'])."',
				'".clean($_POST['district_id'])."',
				'".clean($_POST['upazila_id'])."',
				'".clean($_POST['union_id'])."',
				'".clean($_POST['village'])."',
				'".clean($_POST['house'])."',
				'".clean($_POST['nid'])."',
				'".clean($_POST['religion'])."',
				'".clean($_POST['email'])."',
				'".clean($_POST['edu_qul'])."',
				'".clean($_POST['previous_company'])."',
				'".clean($_POST['joining_salary'])."',
				'".clean($_POST['present_salary'])."',
				'".clean($_POST['house_rent'])."',
				'".clean($_POST['da'])."',
				'".clean($_POST['ta'])."',
				'".clean($_POST['provident_fund'])."',
				'".clean($_POST['basic'])."',
				'".clean($_POST['over_time_bill'])."',
				'".clean($join_d)."',
				'Done',
				'".date("Y-m-d")."',
				'".date("h:i:s a")."',
				'".$_SESSION['NEWERP_SESS_MEMBER_ID']."',
                '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


			) ");
	
			if ($query) {
				echo 'Insert Success' ;
			} 
			else {
				echo 'Insert Failed';
			}
	
	
	
		}else{
	
	
			
## update into local db
$query = $conn_me->prepare("UPDATE `setup_employee`
SET 
`name` = '".clean($_POST['name'])."',
`gender` = '".clean($_POST['gender'])."',
`matrial_status` = '".clean($_POST['matrial_status'])."',
`referrer` = '".clean($_POST['referrer'])."',
`supervisor` = '".clean($_POST['supervisor'])."',
`nominee_information` = '".clean($_POST['nominee_information'])."',
`bank_account` = '".clean($_POST['bank_account'])."',
`provident_fund` = '".clean($_POST['provident_fund'])."',
			`joining_department`	= '".clean($_POST['joining_department'])."',
			`birth_date`	= '".clean($birth_date)."',		
			`medical`	= '".clean($_POST['medical'])."',						
			`po_office`	= '".clean($_POST['po_office'])."',
			`present_department`	= '".clean($_POST['present_department'])."',
			`present_section`	= '".clean($_POST['present_section'])."',
			`joining_section`	= '".clean($_POST['joining_section'])."',
			`designation`	= '".clean($_POST['designation'])."',
			`joining_designation`	= '".clean($_POST['joining_designation'])."',
			`fa_name`	= '".clean($_POST['fa_name'])."',
			`mo_name`	= '".clean($_POST['mo_name'])."',
			`mob_no`	= '".clean($_POST['mob_no'])."',
			`nationality`	= '".clean($_POST['nationality'])."',
			`division_id`	= '".clean($_POST['division_id'])."',
			`district_id`	= '".clean($_POST['district_id'])."',
			`upazila_id`	= '".clean($_POST['upazila_id'])."',
			`union_id`	= '".clean($_POST['union_id'])."',
			`village`	= '".clean($_POST['village'])."',
			`house`	= '".clean($_POST['house'])."',
			`nid`	= '".clean($_POST['nid'])."',
			`religion`	= '".clean($_POST['religion'])."',
			`email`	= '".clean($_POST['email'])."',
			`edu_qul`	= '".clean($_POST['edu_qul'])."',
			`previous_company`	= '".clean($_POST['previous_company'])."',
			`joining_salary`	= '".clean($_POST['joining_salary'])."',
			`present_salary`	= '".clean($_POST['present_salary'])."',
			`house_rent`	= '".clean($_POST['house_rent'])."',
			`da`	= '".clean($_POST['da'])."',
			`ta`	= '".clean($_POST['ta'])."',
			`basic`	= '".clean($_POST['basic'])."',
			`over_time_bill`	= '".clean($_POST['over_time_bill'])."',
			`join_d`	= '".clean($join_d)."',
			`date`	= '".date("Y-m-d")."',
			`time`	= '".date("h:i:s a")."',
			`poster`	= '".$_SESSION['NEWERP_SESS_MEMBER_ID']."',
               `lastupdate` = '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
	
WHERE `id` = '".$_POST['related_id']."' "); 
	
			$query->execute();
	
			if ($query) {
				echo 'Update Success' ;
			} 
			else {
				echo 'Update Failed';
			}
	
	
	
		}

}else if ($_POST['action'] == 'save_notification'){


	$notification= clean($_POST['notification']);
	$status= clean($_POST['status']);



	if($_POST['related_id'] == 'new_id' ){

	$query = $conn_me->exec("INSERT INTO `notice_bord` 
( 
	`id` , 
	`notice_text`,
	`status` ,`date`, `time`, `poster`, `lastupdate`

) 
VALUES
(
	'0',
	'".$notification."',
	'".$status."',
	'" . date("Y-m-d") . "',
    '" . date("h:i:s a") . "',
    '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
    '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


) ");



		if ($query) {
			echo 'Insert Success' ;
		} 
		else {
			echo 'Insert Failed';
		}



	}else{

		$query = $conn_me->prepare("UPDATE `notice_bord` 

		SET
		`notice_text` = '".clean($notification)."',
		`status` = '".clean($status)."',
		`date` = '" . date("Y-m-d") . "',
`time` =  '" . date("h:i:s a") . "',
`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


		WHERE `id` = '".$_POST['related_id']."'");

		$query->execute();

		if ($query) {
			echo 'Update Success' ;
		} 
		else {
			echo 'Update Failed';
		}



	}
}else if ($_POST['action'] == 'save_designation'){



	


	$designation_name= clean($_POST['designation_name']);



if($_POST['related_id'] == 'new_id' ){

$query = $conn_me->exec("INSERT INTO `setup_designation` 
( 
`id` , 
`designation` , `date`, `time`, `poster`, `lastupdate`

) 
VALUES
(
'0',
'".$designation_name."',
'" . date("Y-m-d") . "',
'" . date("h:i:s a") . "',
'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
) ");



    if ($query) {
        echo 'Insert Success' ;
    } 
    else {
        echo 'Insert Failed';
    }



}else{

    $query = $conn_me->prepare("UPDATE `setup_designation` 

    SET
    `designation` = '".clean($designation_name)."',
	`date` = '" . date("Y-m-d") . "',
	`time` =  '" . date("h:i:s a") . "',
	`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
	`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


    WHERE `id` = '".$_POST['related_id']."'");

    $query->execute();

    if ($query) {
        echo 'Update Success' ;
    } 
    else {
        echo 'Update Failed';
    }



}

}else if ($_POST['action'] == 'save_section'){

	


	$section_name= clean($_POST['section_name']);



	if($_POST['related_id'] == 'new_id' ){

	$query = $conn_me->exec("INSERT INTO `setup_section` 
( 
	`id` , 
	`section` , `date`, `time`, `poster`, `lastupdate`

) 
VALUES
(
	'0',
	'".$section_name."',
	'" . date("Y-m-d") . "',
    '" . date("h:i:s a") . "',
    '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
    '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'

) ");



		if ($query) {
			echo 'Insert Success' ;
		} 
		else {
			echo 'Insert Failed';
		}



	}else{

		$query = $conn_me->prepare("UPDATE `setup_section` 

		SET
		`section` = '".clean($section_name)."',
		`date` = '" . date("Y-m-d") . "',
		`time` =  '" . date("h:i:s a") . "',
		`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


		WHERE `id` = '".$_POST['related_id']."'");

		$query->execute();

		if ($query) {
			echo 'Update Success' ;
		} 
		else {
			echo 'Update Failed';
		}



	}


}else if ($_POST['action'] == 'save_raw_material'){




	$product_name= clean($_POST['product_name']);
	$category_id= clean($_POST['category_id']);
	$unit_id= clean($_POST['unit_id']);
	$pcs_in_cartoon= clean($_POST['pcs_in_cartoon']);
	$product_code= clean($_POST['product_code']);
	$supporting_product= clean($_POST['supporting_product']);
	$spray_product= clean($_POST['spray_product']);
	$mold_product= clean($_POST['mold_product']);
	$print_product= clean($_POST['print_product']);
	$weight= clean($_POST['weight']);
	$minimum_stock_qty= clean($_POST['minimum_stock_qty']);

	

	if($_POST['related_id'] == 'new_id' ){

	$query = $conn_me->exec("INSERT INTO `setup_raw_material` 
( 
	`id`, `code`, `material_name`,`supporting_product`,`mold_product`,`spray_product`,`print_product`,`weight`, `category_id`, `unit_id`, `pcs_in_cartoon`,`minimum_stock_qty`, `date`, `time`, `poster`, `lastupdate`
) 
VALUES
(
	'0',
	'".$product_code."',
	'".$product_name."',
	'".$supporting_product."',
	'".$mold_product."',
	'".$spray_product."',
	'".$print_product."',
	'".$weight."',
	'".$category_id."',
	'".$unit_id."',
	'".$pcs_in_cartoon."',
	'".$minimum_stock_qty."',
    '" . date("Y-m-d") . "',
    '" . date("h:i:s a") . "',
    '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
    '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'

) ");



		if ($query) {
			echo 'Insert Success' ;
		} 
		else {
			echo 'Insert Failed';
		}



	}else{

		$query = $conn_me->prepare("UPDATE `setup_raw_material` 

		SET
`material_name` = '".$product_name."',
`supporting_product` = '".$supporting_product."',
`mold_product` = '".$mold_product."',
`spray_product` = '".$spray_product."',
`print_product` = '".$print_product."',
`weight` = '".$weight."',
`category_id` = '".$category_id."',
`minimum_stock_qty` = '".$minimum_stock_qty."',
`unit_id` = '".$unit_id."', 
`pcs_in_cartoon` = '".$pcs_in_cartoon."',
`date` = '" . date("Y-m-d") . "',
`time` =  '" . date("h:i:s a") . "',
`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'



		WHERE `id` = '".$_POST['related_id']."'");

		$query->execute();

		if ($query) {
			echo 'Update Success' ;
		} 
		else {
			echo 'Update Failed';
		}



	}



}else if ($_POST['action'] == 'save_product'){


	$product_name= clean($_POST['product_name']);
	$category_id= clean($_POST['category_id']);
	$unit_id= clean($_POST['unit_id']);
	$pcs_in_cartoon= clean($_POST['pcs_in_cartoon']);
	$sales_rate= clean($_POST['sales_rate']);
	$wholesale_rate= clean($_POST['wholesale_rate']);
	$in_service= clean($_POST['in_service']);
	$product_code= clean($_POST['product_code']);

	$safety_stock= clean($_POST['safety_stock']);


	if($_POST['related_id'] == 'new_id' ){



	$query = $conn_me->exec("INSERT INTO `setup_product` 
( 
	`id`, `code`, `product_name`,`safty_stock`, `category_id`, `unit_id`, `pcs_in_cartoon`,`sales_rate`,`wholesale_rate`,`in_service`, `date`, `time`, `poster`, `lastupdate`,`status`
) 
VALUES
(
	'0',
	'".$product_code."',
	'".$product_name."',
	'".$safety_stock."',
	'".$category_id."',
	'".$unit_id."',
	'".$pcs_in_cartoon."',
	'".$sales_rate."',
	'".$wholesale_rate."',
	'".$in_service."',
    '" . date("Y-m-d") . "',
    '" . date("h:i:s a") . "',
    '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
    '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "',
	'Done'

) ");



		if ($query) {
			echo 'Insert Success' ;
		} 
		else {
			echo 'Insert Failed';
		}





	}else{

		$query = $conn_me->prepare("UPDATE `setup_product` 

		SET
`product_name` = '".$product_name."',
`category_id` = '".$category_id."',
`safty_stock` = '".$safety_stock."',
`unit_id` = '".$unit_id."',
`in_service` = '".$in_service."',
`pcs_in_cartoon` = '".$pcs_in_cartoon."',
`date` = '" . date("Y-m-d") . "',
`time` =  '" . date("h:i:s a") . "',
`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'



		WHERE `id` = '".$_POST['related_id']."'");

		$query->execute();

		if ($query) {
			echo 'Update Success' ;
		} 
		else {
			echo 'Update Failed';
		}



	}
    

	
}else if ($_POST['action'] == 'final_local_purches'){
	

	$qry1 = $conn_me->prepare("SELECT * FROM  `{$_POST['purches_type']}`  WHERE  `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `status` = 'Pending' AND  `supplier_id` IS NULL");
	$qry1->execute();

	if($qry1->rowCount() > 0 ){
	
		$mess = "Please add supplier and warehouse information ";	
		$relared_code = '';
		$transection_id = '';
		$invoice_date = '';
		
	}else{

		$invoice_date = date("Y-m-d", strtotime($_POST['supplier_bill_date']));
		$CODE = SETUP::SETUP_CODE($_POST['purches_type']);
		$relared_code = 	$CODE['code'];
		

		$query = $conn_me->prepare("UPDATE `{$_POST['purches_type']}` 
	
	SET
	`status` = 'Done',
	`invoice_no` = '".$CODE['invoice_no']."',
	`transport_cost` = '".$_POST['total_transport_cost']."',
	`vat_cost` = '".$_POST['total_vat_cost']."',
	`code` = '".$CODE['code']."',
	`invoice_date` = '" . date("Y-m-d") . "',
	`date` = '" . date("Y-m-d") . "',
	`time` =  '" . date("h:i:s a") . "',
	`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
	`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
	
	
	WHERE `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `status` = 'Pending' ");
	
	$query->execute();

if($query){
	$mess = 'Insert Success';
}else{
	$mess = 'Failed';
}


if ($_POST['invoice_total'] > 0 ) {

	$TRANSECTIONE = SETUP::SETUP_CODE_INSERT_DATA('account_transection');
	$transection_id = $TRANSECTIONE['last_id'];
	
	$query2 = $conn_me->prepare("SELECT *  FROM `{$_POST['purches_type']}`   where `code` = '".$CODE['code']."' GROUP BY `code` ");
	$query2->execute();
	$fetch_ck12 = $query2->fetch(PDO::FETCH_ASSOC);
	 
	if(!empty($fetch_ck12['note'])){ $note = $fetch_ck12['note'];}else{ $note = '';}
	if(!empty($fetch_ck12['supplier_id'])){ $supplier_id = $fetch_ck12['supplier_id'];}else{ $supplier_id = '';}
	if(!empty($fetch_ck12['invoice_no'])){ $invoice_no = $fetch_ck12['invoice_no'];}else{ $invoice_no = '';}


	$note =  "$note $invoice_no Inv. Price $_POST[invoice_total]";
    if($_POST['purches_type'] == 'raw_local_purchase' ){ $tra_head_id = 44; }else{ $tra_head_id = 55;}

	ACCOUNT::MAKE_TRANSECTION('EXPENSE','Supplier',$supplier_id,'3',$tra_head_id,$note,0.00,$TRANSECTIONE['last_id'],'Local Purchase');


} else {
$transection_id = '';

}


	}

	print json_encode(array ('mess' => $mess , 'relared_code' => $relared_code, 'transection_id' => $transection_id));



}else if ($_POST['action'] == 'final_raw_wTow'){

	

	$send_date = date("Y-m-d", strtotime($_POST['send_date']));
	$CODE = SETUP::SETUP_CODE('raw_warehouse_to_warehouse_transfer');

	
	
	$query = $conn_me->prepare("UPDATE `raw_warehouse_to_warehouse_transfer` 
	
	SET
	`status` = 'Done',
	`invoice_no` = '" .$CODE['invoice_no']."',
	`code` = '". $code."',
	`invoice_date` = '".clean($send_date)."',
	`date` = '" . date("Y-m-d") . "',
	`time` =  '" . date("h:i:s a") . "',
	`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
	`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
	
	
	WHERE `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `status` = 'Pending' ");
	
	$query->execute();
	



	if ($query) {
		echo 'Update Success' ;
	} 
	else {
		echo 'Update Failed';
	}

}else if ($_POST['action'] == 'final_wTow'){

	$send_date = date("Y-m-d", strtotime($_POST['send_date']));
	$CODE = SETUP::SETUP_CODE('fg_warehouse_to_warehouse_transfer');
    $related_code = $CODE['code'];
	$dispatcher_id= clean($_POST['dispatcher_id']);
	$related_invoice_id= clean($_POST['related_invoice_id']);

	
$qry1 = $conn_me->prepare("SELECT * FROM `fg_warehouse_to_warehouse_transfer` where  `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `status` = 'Pending' ");
$qry1->execute();
$fetch_list1 = $qry1->fetchAll(PDO::FETCH_ASSOC);
foreach ($fetch_list1 as $value1) {

QUICK_BALANCE::FG_QUICK_STOCK($value1["product_id"],$value1["FROM_warehouse_id"],$value1["quantity"],'stock_out',$send_date);
QUICK_BALANCE::FG_QUICK_STOCK($value1["product_id"],$value1["TO_warehouse_id"],$value1["quantity"],'stock_in',$send_date);

}

$query = $conn_me->prepare("UPDATE `fg_warehouse_to_warehouse_transfer` 
	
SET
`status` = 'Done',
`approve_data` = 'Done',
`invoice_no` = '" .$CODE['invoice_no']."',
`code` = '". $related_code."',
`dispatcher_id` = '".$dispatcher_id."',
`related_sales_invoice_id` = '".$related_invoice_id."',
`invoice_date` = '".$send_date."',
`approve_date` = '" . date("Y-m-d") . "',
`approve_by` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
`date` = '" . date("Y-m-d") . "',
`time` =  '" . date("h:i:s a") . "',
`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


WHERE `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `status` = 'Pending' ");

$query->execute();




	if ($query) {
		$mess =  'Update Success' ;
	} 
	else {
		$mess =  'Update Failed';
	}
	

	
print json_encode(array 
(
    'mess' => $mess,
    'code' => $related_code
));



}else if ($_POST['action'] == 'CALL_IT_A_DAY'){

$my_array = json_decode($_POST['all_data'], true);

for ($x = 0; $x < $_POST['count_total_invoice']; $x++) {
    $invoice_id = $my_array[$x]['element']['inid'];

    $info_status = FIND::SALES_INVOICE_STATUS($invoice_id);

   if($info_status['status'] == 'Warehouse Dispatch Done' ){
 $query = $conn_me->prepare("
        UPDATE `sales_invoice` 
        SET `call_it_a_day` = 'YES' 
        WHERE `id` = :invoice_id 
    ");
    
    $query->execute(['invoice_id' => $invoice_id]);

   }
   
}

echo "Day call Done";


}else if ($_POST['action'] == 'update_safty_stock'){


	if($_POST['SECTION'] == 'product_id' ){

		$query1 = $conn_me->prepare("UPDATE `setup_product` SET `safty_stock` = '".$_POST['safty_stock']."' WHERE  `id` =  '".$_POST['ID']."'  ");
		$query1->execute();

		


	}else if ($_POST['SECTION'] == 'category_id'){

		$query1 = $conn_me->prepare("UPDATE `setup_product` SET `safty_stock` = '".$_POST['safty_stock']."' WHERE  `category_id` =  '".$_POST['ID']."'  ");
		$query1->execute();



	}else{



	}
	
	print "Update Done";


}else if ($_POST['action'] == 'Save Demand Delivery Copy'){
    
    
    
    
   
	$my_array = json_decode($_POST['today_data'], true);

	$dispatcher_id = json_encode($_POST['dispatcher_id']);
    $demand_id = clean($_POST['demand_id']);


for ($x = 0; $x < $_POST['total_item']; $x++) {

	if($my_array[$x]['element2']['dispatch_from_warehouse'] != 'DONT' && $my_array[$x]['element2']['received_warehouse'] != 'DONT'  ){

		if($my_array[$x]['element2']['demand_qty'] != 'DONE' && $my_array[$x]['element2']['demand_qty'] > 0 ){



			// Get total demand qty
			$stmt = $conn_me->prepare("SELECT quantity FROM demand_item WHERE demand_id = :demand_id AND product_id = :product_id");
			$stmt->execute([':demand_id' => $demand_id, ':product_id' => $my_array[$x]['element2']['product_id']]);
			$total_demand_qty = (float)($stmt->fetchColumn() ?? 0);

			// Get already received qty
			$stmt2 = $conn_me->prepare("SELECT SUM(quantity) FROM demand_receive WHERE demand_id = :demand_id AND product_id = :product_id");
			$stmt2->execute([':demand_id' => $demand_id, ':product_id' => $my_array[$x]['element2']['product_id']]);
			$total_received_qty = (float)($stmt2->fetchColumn() ?? 0);

			$remaining_qty = $total_demand_qty - $total_received_qty;


		    if ($remaining_qty <= 0) {


             }else{

$query = $conn_me->exec("INSERT INTO `demand_receive` 
			( 
			  `id`,`dispatcher_id`, `product_id`,`demand_id`, `quantity`, `price`, `demand_from_brunch`, `demand_for_brunch`, `invoice_date`, `dispatch_from_warehouse`, `received_warehouse`, `time`, `poster`
			) 
			VALUES
			(
			  '0',
			  '".$dispatcher_id."',
			  '".$my_array[$x]['element2']['product_id']."',
			  '".clean($_POST['demand_id'])."',
			  '".$my_array[$x]['element2']['demand_qty']."',
			  '".$my_array[$x]['element2']['sales_rate']."',
			  '".clean($_POST['demand_from_brunch_id'])."',
			  '".clean($_POST['demand_to_brunch_id'])."',
			  '".date("Y-m-d")."',
			  '".$my_array[$x]['element2']['dispatch_from_warehouse']."',
			  '".$my_array[$x]['element2']['received_warehouse']."',
			  '".date("H:i:s a")."',
			  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "'
			  
			  
			  ) ");


			QUICK_BALANCE::FG_QUICK_STOCK($my_array[$x]['element2']['product_id'],$my_array[$x]['element2']['dispatch_from_warehouse'],$my_array[$x]['element2']['demand_qty'],'stock_out',date("Y-m-d"));
			QUICK_BALANCE::FG_QUICK_STOCK($my_array[$x]['element2']['product_id'],$my_array[$x]['element2']['received_warehouse'],$my_array[$x]['element2']['demand_qty'],'stock_in',date("Y-m-d"));


			$query1 = $conn_me->prepare("UPDATE `demand_item` SET `converat_to_invoice` = 'Done' WHERE `id` = '".$my_array[$x]['element2']['related_id']."'  ");
			$query1->execute(); 


			
             }

			
		  
		}
	}
  }



  print " Generated Successfully";


}else if ($_POST['action'] == 'quotation_action_convert_to_invoice'){

	
	$prepaire_table = SETUP::SETUP_CODE_INSERT_DATA('sales_invoice');
    $today = date('d-m-Y');
	$code = $prepaire_table['related_code'];
	$ac_note = "Invoice Date: $today Invoice No: $prepaire_table[invoice_no]";


	$my_array = json_decode($_POST['all_data'], true);

	$query = $conn_me->prepare("SELECT * FROM `quotation_invoice`  where `id` = '".$_POST['main_invoice_id']."'   ");
	$query->execute();
	$fetch_list = $query->fetch(PDO::FETCH_ASSOC);


	for ($x = 0; $x < $_POST['count_item']; $x++) {
	
		$query = $conn_me->exec("INSERT INTO `sales_invoice_item` 
		( 
			`id`, `sales_invoice_id`,`code`,`product_id`, `sales_quantity`, `recommended_price`,`sales_person`,`sales_by`,`brunch_id`, `poster`, `date`, `time`, `lastupdate`,`status`,`note`,`sales_rate`,`confirm_by_sales_manager`,`sales_manager_id`,`sales_manager_confirm_date`
		
		) 
		VALUES
		(
			'0',
			'".$prepaire_table['last_id']."',
			'".$prepaire_table['related_code']."',
			'".$my_array[$x]['element']['product_id']."',
			'".$my_array[$x]['element']['total_demand']."',
			'".$my_array[$x]['element']['recommended_price']."',
			'" . $fetch_list['sales_person'] . "',
			'" . $fetch_list['sales_by'] . "',
			'" . $fetch_list['brunch_id'] . "',
			'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
			'" . date("Y-m-d") . "',
			'" . date("h:i:s a") . "',
			'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "',
			'Done',
			'Pre-invoice to Invoice',
			'".$my_array[$x]['element']['recommended_price']."',
			'Done',
			'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
			'" . date("Y-m-d") . "'

		) ");


		$query1 = $conn_me->prepare("UPDATE `quotation_invoice_item` 
		
		SET
		`converat_to_invoice` = 'Done',
		`converat_to_invoice_date` = '".date('Y-m-d')."',
		`converat_to_invoice_by` = '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		`quantity` = '".$my_array[$x]['element']['total_demand']."',
		`recommended_price` = 		'".$my_array[$x]['element']['recommended_price']."'

	
		WHERE  `id` = '".$my_array[$x]['element']['quotation_id']."'  ");
		
		$query1->execute();

	
	}


	$query1 = $conn_me->prepare("UPDATE `sales_invoice` 
		
	SET
	`customer_id` = '".$fetch_list['customer_id']."',
	`discount` ='0.00',
	`invoice_date` = '".date('Y-m-d')."',
	`transport_cost` ='0.00',
	`total_vat_cost` ='0.00',
	`sales_by` ='".$fetch_list['sales_by']."',
	`sales_person` ='".$fetch_list['sales_person']."',
	`status` = 'Done',
	`converted_from` = 'quotation_invoice',
	`converted_invoice_id` = '".$_POST['main_invoice_id']."',
	`confirm_by_sales_manager` = 'Done',
	`sales_manager_id` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."',
	`sales_manager_confirm_date` = '".date('Y-m-d')."',
	`dispatch_from_which_brunch` = '" . $_POST['dispatch_from_which_brunch'] . "',
	`brunch_id` =   '" . $_SESSION['USER_BRUNCH'] . "'


	WHERE  `id` = '".$prepaire_table['last_id']."'  ");
	
	$query1->execute();


	$queryw = $conn_me->prepare("SELECT COUNT(id) as `PENDING` FROM `quotation_invoice_item`  where `quotation_invoice_id` = '".$_POST['main_invoice_id']."' and `converat_to_invoice` = 'Pending'   ");
	$queryw->execute();
	$fetch_listw = $queryw->fetch(PDO::FETCH_ASSOC);

	if($fetch_listw['PENDING'] > 0  ){

	}else{

	$query2 = $conn_me->prepare("UPDATE `quotation_invoice` 

	SET
	`converat_to_invoice` = 'Done',
	`converat_to_invoice_by` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."',
	`converat_to_invoice_date` = '".date('Y-m-d')."'

	WHERE  `id` = '".$_POST['main_invoice_id']."'  ");

	$query2->execute();
	}




	$invoice_info = FIND::TOTAL_SALES_INVOICE_PRICE($prepaire_table['last_id']);
	QUICK_BALANCE::CUSTOMER_QUICK_DUE($fetch_list['customer_id'],$invoice_info['price'],'invoice_amount',date('Y-m-d'),$_SESSION['USER_BRUNCH']);



	print "Invoice convert success";


}else if ($_POST['action'] == 'action_convert_to_invoice'){


	$prepaire_table = SETUP::SETUP_CODE_INSERT_DATA('sales_invoice');
    $today = date('d-m-Y');
	$code = $prepaire_table['related_code'];
	$ac_note = "Invoice Date: $today Invoice No: $prepaire_table[invoice_no]";


	$my_array = json_decode($_POST['all_data'], true);

	$query = $conn_me->prepare("SELECT * FROM `preorder_invoice`  where `id` = '".$_POST['main_invoice_id']."'   ");
	$query->execute();
	$fetch_list = $query->fetch(PDO::FETCH_ASSOC);


	for ($x = 0; $x < $_POST['count_item']; $x++) {
	
		$query = $conn_me->exec("INSERT INTO `sales_invoice_item` 
		( 
			`id`, `sales_invoice_id`,`code`,`product_id`, `sales_quantity`, `recommended_price`,`sales_person`,`sales_by`,`brunch_id`, `poster`, `date`, `time`, `lastupdate`,`status`,`note`,`sales_rate`,`confirm_by_sales_manager`,`sales_manager_id`,`sales_manager_confirm_date`

		
		) 
		VALUES
		(
			'0',
			'".$prepaire_table['last_id']."',
			'".$prepaire_table['related_code']."',
			'".$my_array[$x]['element']['product_id']."',
			'".$my_array[$x]['element']['total_demand']."',
			'".$my_array[$x]['element']['recommended_price']."',
			'" . $fetch_list['sales_person'] . "',
			'" . $fetch_list['sales_by'] . "',
			'" . $fetch_list['brunch_id'] . "',
			'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
			'" . date("Y-m-d") . "',
			'" . date("h:i:s a") . "',
			'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "',
			'Done',
			'Pre-invoice to Invoice',
			'".$my_array[$x]['element']['recommended_price']."',
			'Done',
			'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
			'" . date("Y-m-d") . "'


		) ");


		$query1 = $conn_me->prepare("UPDATE `pre_order_invoice_item` 
		
		SET
		`converat_to_invoice` = 'Done',
		`converat_to_invoice_date` = '".date('Y-m-d')."',
		`converat_to_invoice_by` = '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		`quantity` = '".$my_array[$x]['element']['total_demand']."',
		`recommended_price` = 		'".$my_array[$x]['element']['recommended_price']."'
	
		WHERE  `id` = '".$my_array[$x]['element']['pre_order_item_id']."'  ");
		
		$query1->execute();

	
	}


	$query1 = $conn_me->prepare("UPDATE `sales_invoice` 
		
	SET
	`customer_id` = '".$fetch_list['customer_id']."',
	`discount` ='0.00',
	`invoice_date` = '".date('Y-m-d')."',
	`transport_cost` ='0.00',
	`total_vat_cost` ='0.00',
	`sales_by` ='".$fetch_list['sales_by']."',
	`sales_person` ='".$fetch_list['sales_person']."',
	`status` = 'Done',
	`converted_from` = 'Preorder Invoice',
	`converted_invoice_id` = '".$_POST['main_invoice_id']."',
	`confirm_by_sales_manager` = 'Done',
	`sales_manager_id` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."',
	`sales_manager_confirm_date` = '".date('Y-m-d')."',
	`dispatch_from_which_brunch` = '" . $_POST['dispatch_from_which_brunch'] . "',
	`brunch_id` =   '" . $_SESSION['USER_BRUNCH'] . "'


	WHERE  `id` = '".$prepaire_table['last_id']."'  ");
	
	$query1->execute();



	$queryw = $conn_me->prepare("SELECT COUNT(id) as `PENDING` FROM `pre_order_invoice_item`  where `preorder_invoice_id` = '".$_POST['main_invoice_id']."' and `converat_to_invoice` = 'Pending'   ");
	$queryw->execute();
	$fetch_listw = $queryw->fetch(PDO::FETCH_ASSOC);

	if($fetch_listw['PENDING'] > 0  ){

	}else{

	$query2 = $conn_me->prepare("UPDATE `preorder_invoice` 

	SET
	`converat_to_invoice` = 'Done',
	`converat_to_invoice_by` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."',
	`converat_to_invoice_date` = '".date('Y-m-d')."'

	WHERE  `id` = '".$_POST['main_invoice_id']."'  ");

	$query2->execute();
	}



	$invoice_info = FIND::TOTAL_SALES_INVOICE_PRICE($prepaire_table['last_id']);
	QUICK_BALANCE::CUSTOMER_QUICK_DUE($fetch_list['customer_id'],$invoice_info['price'],'invoice_amount',date('Y-m-d'),$_SESSION['USER_BRUNCH']);


	print "Invoice convert success";



}else if ($_POST['action'] == 'action_go_print'){

	


	$my_array = json_decode($_POST['purches_me'], true);


	$ck_print_yes = '';
	$ck_print_no = '';
	$ck_recipe_yes = '';
	$ck_recipe_no = '';

	for ($x = 0; $x < $_POST['count_item']; $x++) {

		$info_product = SETUP::SETUP_RAW_MATERIAL($my_array[$x]['element']['material_id']); 

		if($info_product['print_product'] == 'Yes' ){

			$ck_print_yes .= 'Print Item,';
			$ck_print_no .= '';

			$query = $conn_me->prepare("SELECT *  FROM `receip_print`  where `print_material_id` = '".$my_array[$x]['element']['material_id']."' ORDER BY `id` DESC");
			$query->execute();
			$rowCount =$query->rowCount();
			if($rowCount > 0 ){
				$prepaire_table = SETUP::SETUP_CODE_INSERT_DATA('raw_print');


				
				$query2 = $conn_me->prepare("UPDATE `raw_request_recipe_wise_item` SET `action_status` = 'Sent For Printing' WHERE `demand_code` = '".$_POST['code']."' AND `material_id` = '".$my_array[$x]['element']['material_id']."' ");
				$query2->execute();


				$ck_recipe_yes .= 'Yes,';
				$ck_recipe_no .= '';

				$fetch_query = $query->fetchAll(PDO::FETCH_ASSOC);
			foreach($fetch_query AS $fetch){
		
		
				$demand_qty = $fetch['quantity']*$my_array[$x]['element']['total_demand'];
		
		
				$statement = $conn_me->prepare("INSERT INTO `raw_print_item` 
					(   `demand_code`, `material_id`, `raw_print_id`,`demand_quantity`, `date`, `time`, `poster`, `lastupdate`)
					VALUES (:demand_code, :material_id, :raw_print_id, :demand_quantity, :date, :time, :poster, :lastupdate  )
					");
					
					$statement->execute(
					  array(
						':demand_code'               =>  $prepaire_table['related_code'],
						':material_id'           =>  $fetch['raw_material_id'],
						':raw_print_id'           => $prepaire_table['last_id'],
						':demand_quantity'           =>  $demand_qty,
						':date'           =>  date("Y-m-d"),
						':time'           => date("H:i:s a"),
						':poster'           =>   $_SESSION['NEWERP_SESS_MEMBER_ID'],
						':lastupdate'           =>  $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] 
					
					
					  )
					);
		
		
				}

				$query2 = $conn_me->prepare("UPDATE `raw_print` 
			
				SET
				`material_id` = '". $my_array[$x]['element']['material_id']."',
				`batch_quantity` ='".$my_array[$x]['element']['total_demand']."',
				`invoice_date` ='" . date("Y-m-d") . "',
				`data_inseted_from_where` = 'From Manufacture Tab',
				`receipe_wise_demand_code` ='".$_POST['code']."',
				`date` = '" . date("Y-m-d") . "',
				`time` =  '" . date("h:i:s a") . "',
				`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
				`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
				
				
				WHERE `id` = '".$prepaire_table['last_id']."'  ");
				
				$query2->execute();
				 
		
			
			
			   
			}else{
				$ck_recipe_yes .= '';
				$ck_recipe_no .= 'No,';

			}
			
		
				

		}else{
	
			$ck_print_yes .= '';
			$ck_print_no .= 'Not a Print Item,';
			
		}

		
	}


	$aa1 = trim($ck_print_yes,',');
    $bb1 = explode(",",$aa1);
    $dd1 = count($bb1);

	$aa2 = trim($ck_print_no,',');
    $bb2 = explode(",",$aa2);
    $dd2 = count($bb2);

	$aa3 = trim($ck_recipe_yes,',');
    $bb3 = explode(",",$aa3);
    $dd3 = count($bb3);

	$aa4 = trim($ck_recipe_no,',');
    $bb4 = explode(",",$aa4);
    $dd4 = count($bb4);


	if($dd1 > 0 ){ $mss1 = $dd1 . " Item Send For Print " ;  } else{ $mss1 = ' ';}
	if($dd2 > 0 ){ $mss2 = $dd2 . " Item Not Printable " ;  } else{ $mss2 = ' '; }
	if($dd3 > 0 ){ $mss3 =  " " ;  } else{ $mss3 = $dd3 . ' Item Dont Have Recipe ';}
	if($dd4 > 0 ){ $mss4 =  $dd4 . ' Item Dont Have Recipe ';  } else{ $mss4 = " "; }

	$mess = $mss1.$mss2.$mss3.$mss4;
	
	print $mess;



}else if ($_POST['action'] == 'action_go_purches'){


	$my_array = json_decode($_POST['purches_me'], true);

	for ($x = 0; $x < $_POST['count_item']; $x++) {
	
		
	$query = $conn_me->exec("INSERT INTO `raw_local_purches` 
    ( 
        `id`, `receipe_wise_demand_code`,`product_id`, `quantity`, `date`, `time`, `poster`, `lastupdate`,`data_inseted_from_where`
    ) 
    VALUES
    (
    '0',
	'".$_POST['code']."',
    '".$my_array[$x]['element']['material_id']."',
    '".$my_array[$x]['element']['total_demand']."',
    '" . date("Y-m-d") . "',
        '" . date("h:i:s a") . "',
        '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
        '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "',
		'From Manufacture Tab'
    
    
    ) ");
         


	$query = $conn_me->prepare("UPDATE `raw_request_recipe_wise_item` SET `action_status` = 'Created P.O' WHERE `demand_code` = '".$_POST['code']."' AND `material_id` = '".$my_array[$x]['element']['material_id']."' ");
	$query->execute();
}

	
	
	print 'Action Success';

	

}else if ($_POST['action'] == 'action_go_requasation'){


	$my_array = json_decode($_POST['purches_me'], true);

	$data = array();
	for ($x = 0; $x < $_POST['count_item']; $x++) {

	

	$data = array(
		'send_requisition' => 'Done',
		'action_status' => 'Sent Requisition',
		'send_requisition_by' => $_SESSION['NEWERP_SESS_MEMBER_ID'],
		'send_requisition_date' => date("d-m-Y"),
		'material_id' =>  $my_array[$x]['element_reco']['material_id'],
		'demand_code' =>  $_POST['code']


	);


	$sql = "UPDATE raw_request_recipe_wise_item SET send_requisition=:send_requisition, action_status=:action_status, send_requisition_by=:send_requisition_by
	 , send_requisition_date:=send_requisition_date, material_id=:material_id, demand_code=:demand_code
	 WHERE material_id=:material_id AND demand_code=:demand_code" ;

	$statement = $conn_me->prepare($sql);
	
	$query2 = $conn_me->prepare("UPDATE `raw_request_recipe_wise` 
			
	SET
	`status` = 'Done',
	`warehouse_dispatch` = 'Pending'
	WHERE `code` = '".$_POST['code']."'  ");
	
	$query2->execute();
	 


	if($statement->execute($data)) {
		echo "1";
	  }



	}





}else if ($_POST['action'] == 'action_go_spray'){
	$my_array = json_decode($_POST['purches_me'], true);


	$ck_spray_yes = '';
	$ck_spray_no = '';
	$ck_recipe_yes = '';
	$ck_recipe_no = '';

	for ($x = 0; $x < $_POST['count_item']; $x++) {

		$info_product = SETUP::SETUP_RAW_MATERIAL($my_array[$x]['element']['material_id']); 

		if($info_product['spray_product'] == 'Yes' ){

			$ck_spray_yes .= 'Spray Item,';
			$ck_spray_no .= '';

			$query = $conn_me->prepare("SELECT *  FROM `receip_spray`  where `spray_material_id` = '".$my_array[$x]['element']['material_id']."' ORDER BY `id` DESC");
			$query->execute();
			$rowCount =$query->rowCount();
			if($rowCount > 0 ){
				$prepaire_table = SETUP::SETUP_CODE_INSERT_DATA('raw_spray');

				$query2 = $conn_me->prepare("UPDATE `raw_request_recipe_wise_item` SET `action_status` = 'Sent For Spray' WHERE `demand_code` = '".$_POST['code']."' AND `material_id` = '".$my_array[$x]['element']['material_id']."' ");
				$query2->execute();

				$ck_recipe_yes .= 'Yes,';
				$ck_recipe_no .= '';

				$fetch_query = $query->fetchAll(PDO::FETCH_ASSOC);
			foreach($fetch_query AS $fetch){
		
		
				$demand_qty = $fetch['quantity']*$my_array[$x]['element']['total_demand'];
		
		
				$statement = $conn_me->prepare("INSERT INTO `raw_spray_item` 
					(   `demand_code`, `material_id`, `raw_spray_id`,`demand_quantity`, `date`, `time`, `poster`, `lastupdate`)
					VALUES (:demand_code, :material_id, :raw_spray_id, :demand_quantity, :date, :time, :poster, :lastupdate  )
					");
					
					$statement->execute(
					  array(
						':demand_code'               =>  $prepaire_table['related_code'],
						':material_id'           =>  $fetch['raw_material_id'],
						':raw_spray_id'           => $prepaire_table['last_id'],
						':demand_quantity'           =>  $demand_qty,
						':date'           =>  date("Y-m-d"),
						':time'           => date("H:i:s a"),
						':poster'           =>   $_SESSION['NEWERP_SESS_MEMBER_ID'],
						':lastupdate'           =>  $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] 
					
					
					  )
					);
		
		
				}

				$query2 = $conn_me->prepare("UPDATE `raw_spray` 
			
				SET
				`material_id` = '". $my_array[$x]['element']['material_id']."',
				`batch_quantity` ='".$my_array[$x]['element']['total_demand']."',
				`invoice_date` ='" . date("Y-m-d") . "',
				`data_inseted_from_where` = 'From Manufacture Tab',
				`receipe_wise_demand_code` ='".$_POST['code']."',
				`date` = '" . date("Y-m-d") . "',
				`time` =  '" . date("h:i:s a") . "',
				`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
				`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
				
				
				WHERE `id` = '".$prepaire_table['last_id']."'  ");
				
				$query2->execute();
				 
		
			
			
			   
			}else{
				$ck_recipe_yes .= '';
				$ck_recipe_no .= 'No,';

			}
			
		
				

		}else{
	
			$ck_spray_yes .= '';
			$ck_spray_no .= 'Not a Print Item,';
			
		}

		
	}


	$aa1 = trim($ck_spray_yes,',');
    $bb1 = explode(",",$aa1);
    $dd1 = count($bb1);

	$aa2 = trim($ck_spray_no,',');
    $bb2 = explode(",",$aa2);
    $dd2 = count($bb2);

	$aa3 = trim($ck_recipe_yes,',');
    $bb3 = explode(",",$aa3);
    $dd3 = count($bb3);

	$aa4 = trim($ck_recipe_no,',');
    $bb4 = explode(",",$aa4);
    $dd4 = count($bb4);


	if($dd1 > 0 ){ $mss1 = $dd1 . " Item Send For Spray " ;  } else{ $mss1 = ' ';}
	if($dd2 > 0 ){ $mss2 = $dd2 . " Item Not Sprayable " ;  } else{ $mss2 = ' '; }
	if($dd3 > 0 ){ $mss3 =  " " ;  } else{ $mss3 = $dd3 . ' Item Dont Have Recipe ';}
	if($dd4 > 0 ){ $mss4 =  $dd4 . ' Item Dont Have Recipe ';  } else{ $mss4 = " "; }

	$mess = $mss1.$mss2.$mss3.$mss4;
	
	print $mess;

}else if ($_POST['action'] == 'action_go_mold'){

	


	$my_array = json_decode($_POST['purches_me'], true);


	$ck_mold_yes = '';
	$ck_mold_no = '';
	$ck_recipe_yes = '';
	$ck_recipe_no = '';

	for ($x = 0; $x < $_POST['count_item']; $x++) {

		$info_product = SETUP::SETUP_RAW_MATERIAL($my_array[$x]['element']['material_id']); 

		if($info_product['mold_product'] == 'Yes' ){

			$ck_mold_yes .= 'Mold Item,';
			$ck_mold_no .= '';

			$query = $conn_me->prepare("SELECT *  FROM `receip_supporting_goods`  where `supporting_id` = '".$my_array[$x]['element']['material_id']."' ORDER BY `id` DESC");
			$query->execute();
			$rowCount =$query->rowCount();
			if($rowCount > 0 ){


	


				$prepaire_table = SETUP::SETUP_CODE_INSERT_DATA('raw_molding');

				$ck_recipe_yes .= 'Yes,';
				$ck_recipe_no .= '';


				$query2 = $conn_me->prepare("UPDATE `raw_request_recipe_wise_item` SET `action_status` = 'Sent For Molding' WHERE `demand_code` = '".$_POST['code']."' AND `material_id` = '".$my_array[$x]['element']['material_id']."' ");
				$query2->execute();


				$fetch_query = $query->fetchAll(PDO::FETCH_ASSOC);
			foreach($fetch_query AS $fetch){
		
		
				$demand_qty = $fetch['quantity']*$my_array[$x]['element']['total_demand'];
		
		
				$statement = $conn_me->prepare("INSERT INTO `raw_molding_item` 
					(   `demand_code`, `material_id`, `raw_molding_id`,`demand_quantity`, `date`, `time`, `poster`, `lastupdate`)
					VALUES (:demand_code, :material_id, :raw_molding_id, :demand_quantity, :date, :time, :poster, :lastupdate  )
					");
					
					$statement->execute(
					  array(
						':demand_code'               =>  $prepaire_table['related_code'],
						':material_id'           =>  $fetch['raw_material_id'],
						':raw_molding_id'           => $prepaire_table['last_id'],
						':demand_quantity'           =>  $demand_qty,
						':date'           =>  date("Y-m-d"),
						':time'           => date("H:i:s a"),
						':poster'           =>   $_SESSION['NEWERP_SESS_MEMBER_ID'],
						':lastupdate'           =>  $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] 
					
					
					  )
					);
		
		
				}

				$query2 = $conn_me->prepare("UPDATE `raw_molding` 
			
				SET
				`supporting_id` = '". $my_array[$x]['element']['material_id']."',
				`batch_quantity` ='".$my_array[$x]['element']['total_demand']."',
				`receipe_wise_demand_code` ='".$_POST['code']."',
				`invoice_date` ='" . date("Y-m-d") . "',
				`data_inseted_from_where` = 'From Manufacture Tab',
				`date` = '" . date("Y-m-d") . "',
				`time` =  '" . date("h:i:s a") . "',
				`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
				`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
				
				
				WHERE `id` = '".$prepaire_table['last_id']."'  ");
				
				$query2->execute();
				 
		
			
			
			   
			}else{
				$ck_recipe_yes .= '';
				$ck_recipe_no .= 'No,';

			}
			
		
				

		}else{
	
			$ck_mold_yes .= '';
			$ck_mold_no .= 'Not a Mold Item,';
			
		}

		
	}


	$aa1 = trim($ck_mold_yes,',');
    $bb1 = explode(",",$aa1);
    $dd1 = count($bb1);

	$aa2 = trim($ck_mold_no,',');
    $bb2 = explode(",",$aa2);
    $dd2 = count($bb2);

	$aa3 = trim($ck_recipe_yes,',');
    $bb3 = explode(",",$aa3);
    $dd3 = count($bb3);

	$aa4 = trim($ck_recipe_no,',');
    $bb4 = explode(",",$aa4);
    $dd4 = count($bb4);


	if($dd1 > 0 ){ $mss1 = $dd1 . " Item Send For Mold " ;  } else{ $mss1 = ' ';}
	if($dd2 > 0 ){ $mss2 = $dd2 . " Item Not Moldable " ;  } else{ $mss2 = ' '; }
	if($dd3 > 0 ){ $mss3 =  " " ;  } else{ $mss3 = $dd3 . ' Item Dont Have Recipe ';}
	if($dd4 > 0 ){ $mss4 =  $dd4 . ' Item Dont Have Recipe ';  } else{ $mss4 = " "; }

	$mess = $mss1.$mss2.$mss3.$mss4;
	
	print $mess;



}else if ($_POST['action'] == 'add_local_purches'){



$supplier_id= clean($_POST['supplier_id']);
$material_id= clean($_POST['material_id']);
$purches_type= clean($_POST['purches_type']);
$supplier_bill_date = date("Y-m-d", strtotime($_POST['supplier_bill_date']));  
$supplier_bill_no= clean($_POST['supplier_bill_no']);
$quantity= clean($_POST['quantity']);
$purches_price= clean($_POST['purches_price']);
$note= clean($_POST['note']);






if($_POST['related_id'] == 'new_id' ){

	




	$query = $conn_me->exec("INSERT INTO `{$purches_type}` 
	( 
		`id`, `product_id`, `supplier_id`,`quantity`,`supplier_bill_date`,`supplier_bill_no`,`purches_price`, `note`,`date`, `time`, `poster`, `lastupdate`
	) 
	VALUES
	(
	'0',
	'".$material_id."',
		'".$supplier_id."',
	'".$quantity."',
	'".$supplier_bill_date."',
	'".$supplier_bill_no."',
	'".$purches_price."',
	'".$note."',
	'" . date("Y-m-d") . "',
		'" . date("h:i:s a") . "',
		'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
	
	
	) ");
	
	
	
		if ($query) {
			echo 'Insert Success';
		} 
		else {
			echo 'Insert Failed';
		}
	
	
	
	}else{
	
		$query = $conn_me->prepare("UPDATE `{$purches_type}` 
	
		SET
		`product_id` = '".$material_id."',
		`supplier_id` = '".$supplier_id."',
		`quantity` = '".$quantity."',
		`supplier_bill_date` = '".$supplier_bill_date."',
		`supplier_bill_no` = '".$supplier_bill_no."',
		`purches_price` = '".$purches_price."',
		`note` = '".$note."',
		`date` = '" . date("Y-m-d") . "',
		`time` =  '" . date("h:i:s a") . "',
		`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
	
	
		WHERE `id` = '".$_POST['related_id']."'");
	
		$query->execute();
	
		if ($query) {
			echo 'Update Success' ;
		} 
		else {
			echo 'Update Failed';
		}
	
	
	
	}


}else if ($_POST['action'] == 'add_cart_raw_wTow'){



$notes= clean($_POST['notes']);
$product_id= clean($_POST['product_id']);
$quantity= clean($_POST['quantity']);
$to_warehouse_id= clean($_POST['to_warehouse_id']);
$from_warehouse_id= clean($_POST['from_warehouse_id']);




if($_POST['related_id'] == 'new_id' ){

	


$query = $conn_me->exec("INSERT INTO `raw_warehouse_to_warehouse_transfer` 
( 
	`id`, `product_id`, `quantity`, `FROM_warehouse_id`, `TO_warehouse_id`, `notes`, `date`, `time`, `poster`, `lastupdate`
) 
VALUES
(
'0',
'".$product_id."',
'".$quantity."',
'".$from_warehouse_id."',
'".$to_warehouse_id."',
'".$notes."',
'" . date("Y-m-d") . "',
    '" . date("h:i:s a") . "',
    '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
    '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


) ");



    if ($query) {
        echo 'Insert Success';
    } 
    else {
        echo 'Insert Failed';
    }



}else{

    $query = $conn_me->prepare("UPDATE `raw_warehouse_to_warehouse_transfer` 

    SET
	`product_id` = '".$product_id."',
	`quantity` = '".$quantity."',
	`TO_warehouse_id` = '".$to_warehouse_id."',
    `FROM_warehouse_id` = '".$from_warehouse_id."',
    `notes` = '".$notes."',
	`date` = '" . date("Y-m-d") . "',
	`time` =  '" . date("h:i:s a") . "',
	`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
	`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


    WHERE `id` = '".$_POST['related_id']."'");

    $query->execute();

    if ($query) {
        echo 'Update Success' ;
    } 
    else {
        echo 'Update Failed';
    }



}


}else if ($_POST['action'] == 'add_cart_fg_wTow'){


$notes= clean($_POST['notes']);
$product_id= clean($_POST['product_id']);
$quantity= clean($_POST['quantity']);
$to_warehouse_id= clean($_POST['to_warehouse_id']);
$from_warehouse_id= clean($_POST['from_warehouse_id']);


$stock = STOCK::FG_ITEM_WISE_STOCK($from_warehouse_id,$product_id,'warehouse_wise');

if($stock['ITEM_STOCK'] < $quantity){

	echo "This warehouse have only $stock[ITEM_STOCK] pcs";

}else{

	$query = $conn_me->exec("INSERT INTO `fg_warehouse_to_warehouse_transfer` 
	( 
		`id`, `product_id`, `quantity`, `FROM_warehouse_id`, `TO_warehouse_id`, `notes`, `date`, `time`, `poster`, `lastupdate`
	) 
	VALUES
	(
	'0',
	'".$product_id."',
	'".$quantity."',
	'".$from_warehouse_id."',
	'".$to_warehouse_id."',
	'".$notes."',
	'" . date("Y-m-d") . "',
		'" . date("h:i:s a") . "',
		'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
	
	
	) ");
	
	
	
		if ($query) {
			echo 'Insert Success';
		} 
		else {
			echo 'Insert Failed';
		}
	
}



}else if ($_POST['action'] == 'done_receive_fg_local_purches'){

	
	$query = $conn_me->prepare("UPDATE `fg_local_purches` 
	
	SET
	`warehouse_receive` = 'Done' WHERE `code` = '".$_POST['code']."'  ");
	
	$query->execute();

	print 'Operation Success';



}else if ($_POST['action'] == 'done_receive_raw_local_purches'){


			
	$query = $conn_me->prepare("UPDATE `raw_local_purches` 
	
	SET
	`warehouse_receive` = 'Done' WHERE `code` = '".$_POST['code']."'  ");
	
	$query->execute();




	print 'Operation Success';

}else if ($_POST['action'] == 'update_return_fg_local_purches'){

	
	for($count=0; $count<$_POST["total_item"]; $count++)

	{

		if( trim($_POST["return_now"][$count]) > 0 && trim($_POST["warehouse_id"][$count]) != '' ){
			
			$statement = $conn_me->prepare("INSERT INTO `history_local_fg_purches` 
			(   `supplier_id`, `code`, `product_id`, `reject_quantity`, `warehouse_id`,`invoice_date`,`brunch_id`, `poster`, `date`, `lastupdate`)
			VALUES (:supplier_id, :code, :product_id, :reject_quantity, :warehouse_id, :invoice_date, :brunch_id , :poster, :date, :lastupdate )
			");
			
			$statement->execute(
			  array(
				':supplier_id'               =>  $_POST['supplier_id'][$count],
				':code'               =>   trim($_POST["code"]),
				':product_id'           =>  trim($_POST["product_id"][$count]),
				':reject_quantity'           =>  trim($_POST["return_now"][$count]),
				':warehouse_id'           =>  trim($_POST["warehouse_id"][$count]),
				':invoice_date'           =>  date("Y-m-d"),
				':brunch_id'           =>   $_SESSION['USER_BRUNCH'],
				':poster'           =>   $_SESSION['NEWERP_SESS_MEMBER_ID'],
				':date'           =>  date("Y-m-d"),
				':lastupdate'           =>  $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] 
			
			
			  )
			);
			
				QUICK_BALANCE::FG_QUICK_STOCK($_POST["product_id"][$count],$_POST["warehouse_id"][$count],$_POST["return_now"][$count],'stock_out',date("Y-m-d"));
		}




	
	}

	print "Report Updated";


}else if ($_POST['action'] == 'update_receive_fg_local_purches'){


	for($count=0; $count<$_POST["total_item"]; $count++)

	{

		if( trim($_POST["receive_now"][$count]) > 0 ){
			
			$statement = $conn_me->prepare("INSERT INTO `history_local_fg_purches` 
			(   `supplier_id`, `code`, `product_id`, `receive_quantity`,`warehouse_id`, `invoice_date`, `brunch_id`,`poster`, `date`, `lastupdate`)
			VALUES (:supplier_id, :code, :product_id, :receive_quantity, :warehouse_id, :invoice_date, :brunch_id, :poster, :date, :lastupdate )
			");
			
			$statement->execute(
			  array(
				':supplier_id'               =>  $_POST['supplier_id'][$count],
				':code'               =>   trim($_POST["code"]),
				':product_id'           =>  trim($_POST["product_id"][$count]),
				':receive_quantity'           =>  trim($_POST["receive_now"][$count]),
				':warehouse_id'           =>  trim($_POST["warehouse_id"][$count]),
				':invoice_date'           =>  date("Y-m-d"),
				':brunch_id'           =>   $_SESSION['USER_BRUNCH'],
				':poster'           =>   $_SESSION['NEWERP_SESS_MEMBER_ID'],
				':date'           =>  date("Y-m-d"),
				':lastupdate'           =>  $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] 
			
			
			  )
			);


			QUICK_BALANCE::FG_QUICK_STOCK($_POST["product_id"][$count],$_POST["warehouse_id"][$count],$_POST["receive_now"][$count],'stock_in',date("Y-m-d"));

		}

		

	
	}

	print "Report Updated";


}else if ($_POST['action'] == 'update_return_raw_local_purches'){

	


	for($count=0; $count<$_POST["total_item"]; $count++)

	{

		if( trim($_POST["reject_now"][$count]) > 0 ){
			
			$statement = $conn_me->prepare("INSERT INTO `history_local_raw_purches` 
			(   `supplier_id`, `code`, `product_id`, `warehouse_id`, `reject_quantity`, `invoice_date`, `poster`, `date`,`time`, `lastupdate`)
			VALUES (:supplier_id, :code, :product_id, :warehouse_id, :reject_quantity, :invoice_date, :poster, :date, :time. :lastupdate )
			");
			
			$statement->execute(
			  array(
				':supplier_id'               =>  $_POST['supplier_id'][$count],
				':code'               =>   trim($_POST["code"]),
				':product_id'           =>  trim($_POST["product_id"][$count]),
				':warehouse_id'           =>  trim($_POST["warehouse_id"][$count]),
				':reject_quantity'           =>  trim($_POST["reject_now"][$count]),
				':invoice_date'           =>  date("Y-m-d"),
				':poster'           =>   $_SESSION['NEWERP_SESS_MEMBER_ID'],
				':date'           =>  date("Y-m-d"),
				':time' =>  date("h:i:s a"),
				':lastupdate'           =>  $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] 
			
			
			  )
			);
		}

		

	
	}


	$query = $conn_me->prepare("UPDATE `raw_local_purches` 
	
	SET
	`warehouse_receive` = 'Pending'
	
	WHERE `code` = '".$_POST["code"]."'  ");
	
	$query->execute();



	print "Report Updated";



}else if ($_POST['action'] == 'update_receive_raw_local_purches'){


	for($count=0; $count<$_POST["total_item"]; $count++)

	{

		if( trim($_POST["receive_now"][$count]) > 0  ){
			
			$statement = $conn_me->prepare("INSERT INTO `history_local_raw_purches` 
			(   `supplier_id`, `code`, `product_id`, `receive_quantity`, `warehouse_id`, `invoice_date`, `poster`, `date`,`time` ,`lastupdate`)
			VALUES (:supplier_id, :code, :product_id, :receive_quantity, :warehouse_id, :invoice_date, :poster, :date, :time, :lastupdate )
			");
			
			$statement->execute(
			  array(
				':supplier_id'               =>  $_POST['supplier_id'][$count],
				':code'               =>   trim($_POST["code"]),
				':product_id'           =>  trim($_POST["product_id"][$count]),
				':receive_quantity'           =>  trim($_POST["receive_now"][$count]),
				':warehouse_id'           =>  trim($_POST["warehouse_id"][$count]),
				':invoice_date'           =>  date("Y-m-d"),
				':poster'           =>   $_SESSION['NEWERP_SESS_MEMBER_ID'],
				':date'           =>  date("Y-m-d"),
				':time'           => date("h:i:s a"),
				':lastupdate'           =>  $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] 
			
			
			  )
			);
		}

		

	
	}

	print "Report Updated";


}else if ($_POST['action'] == 'fitting_action'){




	for($count_loop=0; $count_loop<$_POST["total_loop"]; $count_loop++){

		
		if( trim($_POST['employee_id'][$count_loop]) == 'OLD' ){

		}else{
			$statement = $conn_me->prepare("INSERT INTO `history_batch_wise_fg_fitting` 
			( `raw_request_recipe_wise_code`, `product_id`, `emloyee_id`, `fitting_quantity`,`note`, `poster`, `date`, `lastupdate`)
			VALUES (:raw_request_recipe_wise_code, :product_id, :emloyee_id, :fitting_quantity, :note, :poster, :date, :lastupdate )
			");
			
			$statement->execute(
			  array(
				':raw_request_recipe_wise_code'               =>  $_POST['code'],
				':product_id'              =>  trim($_POST['product_id'][$count_loop]),
				':emloyee_id'          =>  trim($_POST['employee_id'][$count_loop]),
				':fitting_quantity'           =>  trim($_POST['done_qty'][$count_loop]),
				':note'           =>  trim($_POST['note'][$count_loop]),
				':poster'           =>   $_SESSION['NEWERP_SESS_MEMBER_ID'],
				':date'           =>  date("Y-m-d"),
				':lastupdate'           =>  $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] 
			
			
			  )
			);
		}

		

	}

	print 'Data Saved';




}else if ($_POST['action'] == 'receive_after_batch_done'){



	
	for($count=0; $count<$_POST["total_item"]; $count++)

	{

		if( trim($_POST["receive_now"][$count]) > 0 ){
			
			$statement = $conn_me->prepare("INSERT INTO `history_batch_wise_fg_receive` 
			(   `code`, `supplier_or_factory_id`,`send_to`, `product_id`, `receive_quantity`,`warehouse_id`, `invoice_date`,`brunch_id`, `poster`, `date`, `lastupdate`)
			VALUES (:code, :supplier_or_factory_id, :send_to, :product_id, :receive_quantity, :warehouse_id, :invoice_date,:brunch_id, :poster, :date, :lastupdate )
			");
			
			$statement->execute(
			  array(
				':code'               =>  $_POST['code'],
				':supplier_or_factory_id'               =>   trim($_POST["send_to_id"]),
				':send_to'               =>   trim($_POST["send_to"]),
				':product_id'           =>  trim($_POST["product_id"][$count]),
				':receive_quantity'           =>  trim($_POST["receive_now"][$count]),
				':warehouse_id'           =>  trim($_POST["warehouse_id"][$count]),
				':invoice_date'           =>  date("Y-m-d"),
				':brunch_id'           =>   $_SESSION['USER_BRUNCH'],
				':poster'           =>   $_SESSION['NEWERP_SESS_MEMBER_ID'],
				':date'           =>  date("Y-m-d"),
				':lastupdate'           =>  $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] 
			
			
			  )
			);

			QUICK_BALANCE::FG_QUICK_STOCK($_POST["product_id"][$count],$_POST["warehouse_id"][$count],$_POST["receive_now"][$count],'stock_in',date("Y-m-d"));

		}

		

	
	}

	print "Report Updated";

}else if ($_POST['action'] == 'receive_after_print'){

	
	for($count=0; $count<$_POST["total_item"]; $count++)

	{

		if( trim($_POST["receive_now"][$count]) > 0 ){
			
			$statement = $conn_me->prepare("INSERT INTO `history_receive_raw_after_print` 
			(   `code`, `supplier_or_factory_id`,`send_to`, `product_id`, `receive_quantity`,`warehouse_id`, `invoice_date`, `poster`, `date`, `time`,`lastupdate`)
			VALUES (:code, :supplier_or_factory_id, :send_to, :product_id, :receive_quantity, :warehouse_id, :invoice_date, :poster, :date, :time, :lastupdate )
			");
			
			$statement->execute(
			  array(
				':code'               =>  $_POST['code'],
				':supplier_or_factory_id'               =>   trim($_POST["supplier_or_factory_id"]),
				':send_to'               =>   trim($_POST["send_to"]),
				':product_id'           =>  trim($_POST["material_id"][$count]),
				':receive_quantity'           =>  trim($_POST["receive_now"][$count]),
				':warehouse_id'           =>  trim($_POST["warehouse_id"][$count]),
				':invoice_date'           =>  date("Y-m-d"),
				':poster'           =>   $_SESSION['NEWERP_SESS_MEMBER_ID'],
				':date'           =>  date("Y-m-d"),
				':time'           =>  date("H:i:s a"),
				':lastupdate'           =>  $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] 
			
			
			  )
			);
		}

		

	
	}

	print "Report Updated";

}else if ($_POST['action'] == 'receive_after_spray'){


	for($count=0; $count<$_POST["total_item"]; $count++)

	{

		if( trim($_POST["receive_now"][$count]) > 0 ){
			
			$statement = $conn_me->prepare("INSERT INTO `history_receive_raw_after_spray` 
			(   `code`, `supplier_or_factory_id`,`send_to`, `product_id`, `receive_quantity`,`warehouse_id`, `invoice_date`, `poster`, `date`, `lastupdate`)
			VALUES (:code, :supplier_or_factory_id, :send_to, :product_id, :receive_quantity, :warehouse_id, :invoice_date, :poster, :date, :lastupdate )
			");
			
			$statement->execute(
			  array(
				':code'               =>  $_POST['code'],
				':supplier_or_factory_id'               =>   trim($_POST["supplier_or_factory_id"]),
				':send_to'               =>   trim($_POST["send_to"]),
				':product_id'           =>  trim($_POST["material_id"][$count]),
				':receive_quantity'           =>  trim($_POST["receive_now"][$count]),
				':warehouse_id'           =>  trim($_POST["warehouse_id"][$count]),
				':invoice_date'           =>  date("Y-m-d"),
				':poster'           =>   $_SESSION['NEWERP_SESS_MEMBER_ID'],
				':date'           =>  date("Y-m-d"),
				':lastupdate'           =>  $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] 
			
			
			  )
			);
		}

		

	
	}

	print "Report Updated";


}else if ($_POST['action'] == 'HARD_DELETE'){


if($_POST['WHAT'] == 'INVOICE' ){



	$query = $conn_me->prepare("SELECT * FROM `sales_invoice`  where `id` = '".$_POST["ID"]."'   ");
	$query->execute();
	$fetch_list = $query->fetch(PDO::FETCH_ASSOC);
    
	$invoice_price = FIND::TOTAL_SALES_INVOICE_PRICE($fetch_list['id']);
	
	
	
QUICK_BALANCE::DELETEATEMP($_POST["ID"]);


	QUICK_BALANCE::CUSTOMER_QUICK_DUE($fetch_list['customer_id'],-$invoice_price['price'],'invoice_amount',$fetch_list['invoice_date'],$fetch_list['brunch_id']);


	if(!empty($fetch_list['transection_id'])){
		
		$query2 = $conn_me->prepare("SELECT * FROM `account_transection`  WHERE `id`  = '".$fetch_list['transection_id']."'  ");
		$query2->execute();
		$fe_ck1 = $query2->fetch(PDO::FETCH_ASSOC);


		QUICK_BALANCE::QUICK_OPENING_BALANCE('in_amount',$fe_ck1['transection_by'],$fe_ck1['transection_by_id'],-$fe_ck1['in_amount'],$fe_ck1['transection_date'],$fe_ck1['brunch_id']);



	}




	$query3 = $conn_me->prepare("SELECT * FROM `sales_invoice_item`  where `code` = '".$fetch_list["code"]."'   ");
	$query3->execute();
	$fetch_list3 = $query3->fetchAll(PDO::FETCH_ASSOC);
	foreach ($fetch_list3 as $fetch ) {

		if($fetch['pcs_receive'] > 0 ){
               $qty = $fetch['pcs_receive'];
		}else{
			$qty = $fetch['pcs_receive'];	
		}
		QUICK_BALANCE::FG_QUICK_STOCK($fetch['product_id'],$fetch['warehouse_id'],-$fetch['pcs_receive'],'stock_out',$fetch_list["invoice_date"]);
	}





	$query_x = $conn_me->prepare("DELETE FROM `sales_invoice` WHERE `id` = '".$fetch_list['id']."' "); 
	$query_x->execute();


	$query_y = $conn_me->prepare("DELETE FROM `sales_invoice_item` WHERE `sales_invoice_id` = '".$fetch_list['id']."' "); 
	$query_y->execute();


	$query_z = $conn_me->prepare("DELETE FROM `account_transection` WHERE `id` = '".$fetch_list['transection_id']."' "); 
	$query_z->execute();



print "Invoice Delete Success";
	
}



	

}else if ($_POST['action'] == 'final_damage_receiv_by_warehouse'){


	$query3 = $conn_me->prepare("SELECT `customer_id`,`id`,`brunch_id`  FROM `damage_invoice` where `code` = '".$_POST['code']."' AND  `warehouse_receive` = 'Pending' ");
    $query3->execute();
    $fetch_list3 = $query3->fetch(PDO::FETCH_ASSOC);


	$after_final = FIND::PRODUCT_DAMAGE_INVOICE_VALUE($fetch_list3['id']);


	QUICK_BALANCE::CUSTOMER_QUICK_DUE($fetch_list3['customer_id'],$after_final['price'],'return_amount',date('Y-m-d'),$fetch_list3['brunch_id']);


	$my_array = json_decode($_POST['today_data'], true);

	for ($x = 0; $x < $_POST['total_item']; $x++) {


		$query1 = $conn_me->prepare("UPDATE `damage_invoice_item` 
		
		SET
		`warehouse_id` = '".$my_array[$x]['element2']['warehouse_id']."',
		`warehouse_receive` = 'Done',
		`status` = 'Done',
		`warehouse_receive_by` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."',
		`warehouse_receive_date` = '".date('Y-m-d')."'


		WHERE `id` = '".$my_array[$x]['element2']['id']."'");
		
		$query1->execute();
		

		
		QUICK_BALANCE::FG_QUICK_STOCK($my_array[$x]['element2']['product_id'],$my_array[$x]['element2']['warehouse_id'],$my_array[$x]['element2']['damage_quantity'],'stock_in',date("Y-m-d"));


		}


		$query1 = $conn_me->prepare("UPDATE `damage_invoice` 
		
		SET


		`warehouse_receive` = 'Done',
		`warehouse_receive_by` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."',
		`warehouse_receive_date` = '".date('Y-m-d')."'


		WHERE `code` = '".$_POST['code']."'");
		
		$query1->execute();


	print "Receive Success";



}else if ($_POST['action'] == 'receive_sales_retuen_by_warehouse'){



	$my_array = json_decode($_POST['today_data'], true);

	for ($x = 0; $x < $_POST['total_item']; $x++) {
		
		if($my_array[$x]['element2']['warehouse_id'] == 'NOTSELECTED' ){


			$query_y = $conn_me->prepare("DELETE FROM `sales_return_invoice_item` WHERE `id` = '".$my_array[$x]['element2']['id']."' "); 
			$query_y->execute();

		}else{

			$query1 = $conn_me->prepare("UPDATE `sales_return_invoice_item` 
		
			SET
			`warehouse_id` = '".$my_array[$x]['element2']['warehouse_id']."',
			`warehouse_receive` = 'Done',
			`status` = 'Done',
			`warehouse_receive_by` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."',
			`warehouse_receive_date` = '".date('Y-m-d')."'
	
	
			WHERE `id` = '".$my_array[$x]['element2']['id']."'");
			
			$query1->execute();
			
	
			QUICK_BALANCE::FG_QUICK_STOCK($my_array[$x]['element2']['product_id'],$my_array[$x]['element2']['warehouse_id'],$my_array[$x]['element2']['return_quantity'],'stock_in',date("Y-m-d"));

		}

		
		}



		$query1 = $conn_me->prepare("UPDATE `sales_return_invoice` 
		
		SET

		`warehouse_receive` = 'Done',
		`warehouse_receive_by` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."',
		`warehouse_receive_date` = '".date('Y-m-d')."'
		WHERE `code` = '".$_POST['code']."'");
		$query1->execute();


		$query3 = $conn_me->prepare("SELECT `customer_id`,`id`,`brunch_id`,`warehouse_receive_date`  FROM `sales_return_invoice` where `code` = '".$_POST['code']."' AND  `warehouse_receive` = 'Done' ");
		$query3->execute();
		$fetch_list3 = $query3->fetch(PDO::FETCH_ASSOC);
	

	$after_final = FIND::PRODUCT_RETURN_INVOICE_VALUE($fetch_list3['id']);
	
	QUICK_BALANCE::CUSTOMER_QUICK_DUE($fetch_list3['customer_id'],$after_final['price'],'return_amount',$fetch_list3['warehouse_receive_date'],$fetch_list3['brunch_id']);

	print "Receive Success";


	

}else if ($_POST['action'] == 'return_damage_product_from_customer'){



	$my_array = json_decode($_POST['today_data'], true);

	$prepaire_table = SETUP::SETUP_CODE_INSERT_DATA('damage_invoice');

	for ($x = 0; $x < $_POST['total_item']; $x++) {


if($my_array[$x]['element2']['damage_qty'] > 0 ){

	$query = $conn_me->exec("INSERT INTO `damage_invoice_item` 
	( 
		`id`, `code`,`damage_invoice_id`, `note`, `product_id`, `damage_quantity`,`damage_carton`, `sales_rate`,`brunch_id`, `poster`, `date`, `time`, `lastupdate`
	
	) 
	VALUES
	(
		'0',
		'".$prepaire_table['related_code']."',
		'".$prepaire_table['last_id']."',
		'".$my_array[$x]['element2']['note']."',
		'".$my_array[$x]['element2']['product_id']."',
		'".$my_array[$x]['element2']['damage_qty']."',
		'".$my_array[$x]['element2']['damage_carton']."',
		'".$my_array[$x]['element2']['sales_rate']."',
		'" . $_SESSION['USER_BRUNCH'] . "',
		'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		'" . date("Y-m-d") . "',
		'" . date("h:i:s a") . "',
		'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


	) ");

	
}
	



	

	}
	

	$query1 = $conn_me->prepare("UPDATE `damage_invoice` 

	SET
	`customer_id` = '".$_POST['customer_id']."',
	`sale_invoice_id` ='".$_POST['main_invoice_id']."',
	`invoice_date` ='".date('Y-m-d')."',
	`status` = 'Done'

	WHERE  `id` = '".$prepaire_table['last_id']."'  ");

	$query1->execute();




print "Damage Return Success";

}else if ($_POST['action'] == 'return_product_from_customer'){

	$my_array = json_decode($_POST['today_data'], true);

	$prepaire_table = SETUP::SETUP_CODE_INSERT_DATA('sales_return_invoice');

	for ($x = 0; $x < $_POST['total_item']; $x++) {

		if($my_array[$x]['element2']['return_qty'] > 0 ){

			$query = $conn_me->exec("INSERT INTO `sales_return_invoice_item` 
	( 
		`id`, `code`,`return_invoice_id`, `note`, `product_id`, `return_quantity`,`return_carton`, `sales_rate`,`brunch_id`, `poster`, `date`, `time`, `lastupdate`
	
	) 
	VALUES
	(
		'0',
		'".$prepaire_table['related_code']."',
		'".$prepaire_table['last_id']."',
		'".clean($my_array[$x]['element2']['note'])."',
		'".$my_array[$x]['element2']['product_id']."',
		'".$my_array[$x]['element2']['return_qty']."',
		'".$my_array[$x]['element2']['return_carton']."',
		'".$my_array[$x]['element2']['sales_rate']."',
		'" . $_SESSION['USER_BRUNCH'] . "',
		'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		'" . date("Y-m-d") . "',
		'" . date("h:i:s a") . "',
		'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'

	) ");


		}
	



	}
	

	$query1 = $conn_me->prepare("UPDATE `sales_return_invoice` 

	SET
	`customer_id` = '".$_POST['customer_id']."',
	`sale_invoice_id` ='".$_POST['main_invoice_id']."',
	`brunch_id` ='".$_SESSION['USER_BRUNCH']."',
	`invoice_date` ='".date('Y-m-d')."',
	`status` = 'Done'

	WHERE  `id` = '".$prepaire_table['last_id']."'  ");

	$query1->execute();


print "Return Success";

}else if ($_POST['action'] == 'godowncopy_copy_by_warehouse_manager'){


	$not_in_stock = array();
	$my_array = json_decode($_POST['today_data'], true);

	for ($x = 0; $x < $_POST['total_item']; $x++) {

		$stock = STOCK::FG_ITEM_WISE_STOCK($my_array[$x]['element2']['warehouse_id'],$my_array[$x]['element2']['product_id'],'warehouse_wise');

        $stock_checker = $my_array[$x]['element2']['sales_quantity_stock_check'];

		if( $stock_checker > $stock['ITEM_STOCK'] ){		
			$not_in_stock[] = $x+1;
		}
		
}


if (count($not_in_stock) > 0) {
    // output the values of $x where the item is not in stock
    echo  implode(", ", $not_in_stock);


}else{


		for ($x = 0; $x < $_POST['total_item']; $x++) {
		$query1 = $conn_me->prepare("UPDATE `sales_invoice_item` 
		SET
		`warehouse_id` = '".$my_array[$x]['element2']['warehouse_id']."'
		WHERE `id` = '".$my_array[$x]['element2']['sales_id']."'");
		$query1->execute();

		}


		if(is_null($_POST['dispatcher_name'] )){

			$query1 = $conn_me->prepare("UPDATE `sales_invoice` 
			
			SET
			`generate_challan` = 'Pending',
			`dispatcher_id` = '".json_encode($_POST['dispatcher_id'])."'
			WHERE `id` = '".$_POST['main_invoice_id']."'");
		
			$query1->execute();
		
		}else{
		
			if(is_null($_POST['dispatcher_id'] )){
		
			}else{
		
				$query1 = $conn_me->prepare("UPDATE `sales_invoice` 
			
				SET
				`generate_challan` = 'Pending',
				`dispatcher_id` = '".json_encode($_POST['dispatcher_id'])."'
				WHERE `id` = '".$_POST['main_invoice_id']."'");
			
				$query1->execute();
			
				}
			
		}



	$data = [
		'godowncopy_time' => date("d-m-Y h:i:s a"),
	  ];
	CRUD::updateTimelineData($_POST['main_invoice_id'],$data);


		print "Success";
}


}else if ($_POST['action'] == 'WarehouseWiseProductStockInQuatation'){

	$item_stock = array();

	$ck1 = $conn_me->prepare("SELECT *  FROM `quotation_invoice_item` where `quotation_invoice_id` = '".$_POST['main_invoice_id']."' ");
	$ck1->execute();
	$fe_ck1 = $ck1->fetchAll(PDO::FETCH_ASSOC);
    $sl = 1;
	foreach ($fe_ck1 as  $value) {
		
		$stock = STOCK::FG_ITEM_WISE_STOCK($_POST['brunch_id'],$value['product_id'],'unique_brunch_wise');
		$item_stock[$sl]['sl'] = $sl;
		$item_stock[$sl]['stock'] = $stock['ITEM_STOCK'];

        $sl++;
	}


	print json_encode(array 
	(
		'item_stock' => $item_stock,

	));



}else if ($_POST['action'] == 'WarehouseWiseProductStockInPreinvoice'){

	$item_stock = array();

	$ck1 = $conn_me->prepare("SELECT *  FROM `pre_order_invoice_item` where `preorder_invoice_id` = '".$_POST['main_invoice_id']."' ");
	$ck1->execute();
	$fe_ck1 = $ck1->fetchAll(PDO::FETCH_ASSOC);
    $sl = 1;
	foreach ($fe_ck1 as  $value) {
		
		$stock = STOCK::FG_ITEM_WISE_STOCK($_POST['brunch_id'],$value['product_id'],'unique_brunch_wise');
		$item_stock[$sl]['sl'] = $sl;
		$item_stock[$sl]['stock'] = $stock['ITEM_STOCK'];

        $sl++;
	}


	print json_encode(array 
	(
		'item_stock' => $item_stock,

	));
}else if ($_POST['action'] == 'challan_copy_by_warehouse_manager'){



$not_in_stock = array();
$my_array = json_decode($_POST['today_data'], true);

$invoice_date = date("Y-m-d", strtotime($_POST['invoice_date']));



for ($x = 0; $x < $_POST['total_item']; $x++) {

		$stock = STOCK::FG_ITEM_WISE_STOCK($my_array[$x]['element2']['warehouse_id'],$my_array[$x]['element2']['product_id'],'warehouse_wise');


		$stock_checker = $my_array[$x]['element2']['sales_quantity'];
		
		if( $stock_checker > $stock['ITEM_STOCK'] ){		
			$not_in_stock[] = $x+1;
		}
		
}

if (count($not_in_stock) > 0) {
    // output the values of $x where the item is not in stock
    echo  implode(", ", $not_in_stock);


}else{

for ($x = 0; $x < $_POST['total_item']; $x++) {
		$query1 = $conn_me->prepare("UPDATE `sales_invoice_item` 
		SET
		`warehouse_id` = '".$my_array[$x]['element2']['warehouse_id']."',
		`pcs_receive` = '".$my_array[$x]['element2']['sales_quantity']."',
		`carton_receive` = '".$my_array[$x]['element2']['carton_receive']."',
		`warehouse_dispatch` = 'Done',
		`warehouse_dispatch_by` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."',
		`warehouse_dispatch_date` = '".$invoice_date."',
		`send_to_sales_person_for_approval` = 'Done',
		`send_to_sales_person_for_approval_by` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."',
		`send_to_sales_person_for_approval_date` ='".$invoice_date."',
		`sales_quantity` = '".$my_array[$x]['element2']['sales_quantity']."',
		`final_confirm_sales_person` = 'Done',
		`final_confirm_sales_person_id` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."',
		`final_confirm_sales_person_date` =  '".$invoice_date."'



		WHERE `id` = '".$my_array[$x]['element2']['sales_id']."'");
		$query1->execute();

		QUICK_BALANCE::FG_QUICK_STOCK($my_array[$x]['element2']['product_id'],$my_array[$x]['element2']['warehouse_id'],$my_array[$x]['element2']['sales_quantity'],'stock_out',$_POST["invoice_date"]);



}


$query1 = $conn_me->prepare("UPDATE `sales_invoice` 
	
SET
`transport_cost` = '".$_POST['total_transport_cost']."',
`generate_challan` = 'Done',
`dispatcher_id` = '".json_encode($_POST['dispatcher_id'])."',
`warehouse_dispatch` = 'Done',
`warehouse_dispatch_by` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."',
`warehouse_dispatch_date` = '".$invoice_date."',
`send_to_sales_person_for_approval` = 'Done',
`send_to_sales_person_for_approval_by` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."',
`send_to_sales_person_for_approval_date` = '".$invoice_date."',
`final_confirm_sales_person` = 'Done',
`final_confirm_sales_person_id` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."',
`final_confirm_sales_person_date` = '".$invoice_date."'

WHERE `id` = '".$_POST['main_invoice_id']."'");

$query1->execute();


QUICK_BALANCE::CUSTOMER_QUICK_DUE($_POST['customer_id'],$_POST['total_transport_cost'],'invoice_amount',$invoice_date,$_POST['brunch_id']);

print "Success";


}


$data = [
	'challancopy_time' => date("d-m-Y h:i:s a"),
  ];
CRUD::updateTimelineData($_POST['main_invoice_id'],$data);


}else if ($_POST['action'] == 'check_and_insert_warehouse_live_data'){


	$stock = STOCK::FG_ITEM_WISE_STOCK($_POST['warehouse_id'],$_POST['product_id'],'warehouse_wise');

     if( $stock['ITEM_STOCK'] < $_POST['demand_qty'] ){

          print "This warehouse have only $stock[ITEM_STOCK] Pcs";

	 }else{


		$query1 = $conn_me->prepare("UPDATE `sales_invoice_item` 

		SET
		`warehouse_id` = '".$_POST['warehouse_id']."'
	
		WHERE `id` = '".$_POST['sales_id']."'");

		$query1->execute();
		print "DONE";

	 }

}else if ($_POST['action'] == 'confirm_invoice_by_sales_manager'){

	$invoice_date = date("Y-m-d", strtotime($_POST['invoice_date']));

	$my_array = json_decode($_POST['today_data'], true);

	for ($x = 0; $x < $_POST['total_item']; $x++) {


	$query1 = $conn_me->prepare("UPDATE `sales_invoice_item` 

	SET
	`sales_rate` = '".$my_array[$x]['element2']['sales_price']."',
	`confirm_by_sales_manager` =  'Done',
	`sales_manager_id` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."',
	`sales_manager_confirm_date` =  '".$invoice_date."'

	WHERE `id` = '".$my_array[$x]['element2']['sales_id']."'");

	$query1->execute();

	}




$query2 = $conn_me->prepare("UPDATE `sales_invoice` 
	
SET
`confirm_by_sales_manager` = 'Done',
`sales_manager_id` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."',
`sales_manager_confirm_date` ='".$invoice_date."'

WHERE `code` = '".$_POST["code"]."'  ");

$query2->execute();






	$invoice_info = SETUP::SETUP_SALES_INVOICE($_POST["code"]);
	QUICK_BALANCE::CUSTOMER_QUICK_DUE($invoice_info['customer_id'],$invoice_info['total_invoice_price'],'invoice_amount',$invoice_info['invoice_date'],$invoice_info['brunch_id']);

	echo "updated successfully!";

	
}else if ($_POST['action'] == 'receive_after_molding'){



	for($count=0; $count<$_POST["total_item"]; $count++)

	{

		if( trim($_POST["receive_now"][$count]) > 0 ){
			
			$statement = $conn_me->prepare("INSERT INTO `history_receive_raw_after_mold` 
			(   `code`, `supplier_or_factory_id`,`send_to`, `product_id`, `receive_quantity`,`warehouse_id`, `invoice_date`, `poster`, `date`,`time`, `lastupdate`)
			VALUES (:code, :supplier_or_factory_id, :send_to, :product_id, :receive_quantity, :warehouse_id, :invoice_date, :poster, :date, :time, :lastupdate )
			");
			
			$statement->execute(
			  array(
				':code'               =>  $_POST['code'],
				':supplier_or_factory_id'               =>   trim($_POST["supplier_or_factory_id"]),
				':send_to'               =>   trim($_POST["send_to"]),
				':product_id'           =>  trim($_POST["supporting_id"][$count]),
				':receive_quantity'           =>  trim($_POST["receive_now"][$count]),
				':warehouse_id'           =>  trim($_POST["warehouse_id"][$count]),
				':invoice_date'           =>  date("Y-m-d"),
				':poster'           =>   $_SESSION['NEWERP_SESS_MEMBER_ID'],
				':date'           =>  date("Y-m-d"),
				':time'           =>  date("h:i:s a"),
				':lastupdate'           =>  $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] 
			
			
			  )
			);
		}

		

	
	}

	print "Report Updated";

}else if ($_POST['action'] == 'dispatch_receipe_wise_item_demand'){

	for($count=0; $count<$_POST["total_item"]; $count++)

	{

		if( trim($_POST["dispatch_now"][$count]) > 0 ){
			
			$statement = $conn_me->prepare("INSERT INTO `history_receipe_wise_item_dispatch` 
			(   `code`, `supplier_or_factory_id`,`send_to`, `product_id`, `dispatch_quantity`,`warehouse_id`, `invoice_date`, `poster`, `date`,`time`, `lastupdate`)
			VALUES (:code, :supplier_or_factory_id, :send_to, :product_id, :dispatch_quantity, :warehouse_id, :invoice_date, :poster, :date, :time, :lastupdate )
			");
			
			$statement->execute(
			  array(
				':code'               =>  $_POST['code'],
				':supplier_or_factory_id'               =>   trim($_POST["supplier_or_factory_id"]),
				':send_to'               =>   trim($_POST["send_to"]),
				':product_id'           =>  trim($_POST["product_id"][$count]),
				':dispatch_quantity'           =>  trim($_POST["dispatch_now"][$count]),
				':warehouse_id'           =>  trim($_POST["warehouse_id"][$count]),
				':invoice_date'           =>  date("Y-m-d"),
				':poster'           =>   $_SESSION['NEWERP_SESS_MEMBER_ID'],
				':date'           =>  date("Y-m-d"),
				':time'           =>  date("h:i:s a"),
				':lastupdate'           =>  $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] 
			
			
			  )
			);
		}

		

	
	}

	print "Report Updated";



}else if ($_POST['action'] == 'dispatch_raw_material_for_print'){



	
	for($count2=0; $count2<$_POST["total_print_item"]; $count2++)

	{

		$stock = STOCK::RAW_ITEM_WISE_STOCK($_POST["print_warehouse_id"][$count2],$_POST["print_product_id"][$count2],'warehouse_wise');

if( $_POST["dispatch_now_item"][$count2] > $stock['ITEM_STOCK']){

	$mess = $count2+1;
break;


}else{

	if( trim($_POST["dispatch_now_item"][$count2]) > 0 ){
			
		$mess = $count+1;


		$statement = $conn_me->prepare("INSERT INTO `history_print_item_dispatch` 
		(   `code`, `supplier_or_factory_id`,`send_to`, `product_id`, `dispatch_quantity`,`warehouse_id`, `invoice_date`, `poster`, `date`, `time`,`lastupdate`)
		VALUES (:code, :supplier_or_factory_id, :send_to, :product_id, :dispatch_quantity, :warehouse_id, :invoice_date, :poster, :date, :time, :lastupdate )
		");
		
		$statement->execute(
		  array(
			':code'               =>  $_POST['code'],
			':supplier_or_factory_id'               =>   trim($_POST["supplier_or_factory_id"]),
			':send_to'               =>   trim($_POST["send_to"]),
			':product_id'           =>  trim($_POST["print_product_id"][$count2]),
			':dispatch_quantity'           =>  trim($_POST["dispatch_now_item"][$count2]),
			':warehouse_id'           =>  trim($_POST["print_warehouse_id"][$count2]),
			':invoice_date'           =>  date("Y-m-d"),
			':poster'           =>   $_SESSION['NEWERP_SESS_MEMBER_ID'],
			':date'           =>  date("Y-m-d"),
			':time'           =>  date("H:i:s a"),

			':lastupdate'           =>  $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] 
		
		
		  )
		);
	}

	$mess = 0;
}

		}




	for($count=0; $count<$_POST["total_item"]; $count++)

	{

		$stock = STOCK::RAW_ITEM_WISE_STOCK($_POST["warehouse_id"][$count],$_POST["product_id"][$count],'warehouse_wise');


		if( $_POST["dispatch_now"][$count] > $stock['ITEM_STOCK']){

			$mess = $count+1;
		break;
		
		
		}else{

			if( trim($_POST["dispatch_now"][$count]) > 0 ){
			
				$statement = $conn_me->prepare("INSERT INTO `history_print_raw_item_dispatch` 
				(   `code`, `supplier_or_factory_id`,`send_to`, `product_id`, `dispatch_quantity`,`warehouse_id`, `invoice_date`, `poster`, `date`, `time`,`lastupdate`)
				VALUES (:code, :supplier_or_factory_id, :send_to, :product_id, :dispatch_quantity, :warehouse_id, :invoice_date, :poster, :date, :time, :lastupdate )
				");
				
				$statement->execute(
				  array(
					':code'               =>  $_POST['code'],
					':supplier_or_factory_id'               =>   trim($_POST["supplier_or_factory_id"]),
					':send_to'               =>   trim($_POST["send_to"]),
					':product_id'           =>  trim($_POST["product_id"][$count]),
					':dispatch_quantity'           =>  trim($_POST["dispatch_now"][$count]),
					':warehouse_id'           =>  trim($_POST["warehouse_id"][$count]),
					':invoice_date'           =>  date("Y-m-d"),
					':poster'           =>   $_SESSION['NEWERP_SESS_MEMBER_ID'],
					':date'           =>  date("Y-m-d"),
					':time'           =>  date("h:i:s a"),
					':lastupdate'           =>  $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] 
				
				
				  )
				);
			}
			$mess = 0;
		}
		

		

	
	}

	if($mess > 0 ) { print "$mess item not enough in selected warehouse";}else{ print  "Data Updated "; };




}else if ($_POST['action'] == 'dispatch_raw_material_for_spray'){


	
	for($count2=0; $count2<$_POST["total_spray_item"]; $count2++)

	{

		



		$stock = STOCK::RAW_ITEM_WISE_STOCK($_POST["spray_warehouse_id"][$count2],$_POST["spray_product_id"][$count2],'warehouse_wise');


		if($_POST["dispatch_now_item"][$count2] > $stock['ITEM_STOCK']){

			$countThis = $count2+1;
			break;
		}else{

			
		if( trim($_POST["dispatch_now_item"][$count2]) > 0 ){
			
			$statement = $conn_me->prepare("INSERT INTO `history_spray_item_dispatch` 
			(   `code`, `supplier_or_factory_id`,`send_to`, `product_id`, `dispatch_quantity`,`warehouse_id`, `invoice_date`, `poster`, `date`, `lastupdate`)
			VALUES (:code, :supplier_or_factory_id, :send_to, :product_id, :dispatch_quantity, :warehouse_id, :invoice_date, :poster, :date, :lastupdate )
			");
			
			$statement->execute(
			  array(
				':code'               =>  $_POST['code'],
				':supplier_or_factory_id'               =>   trim($_POST["supplier_or_factory_id"]),
				':send_to'               =>   trim($_POST["send_to"]),
				':product_id'           =>  trim($_POST["spray_product_id"][$count2]),
				':dispatch_quantity'           =>  trim($_POST["dispatch_now_item"][$count2]),
				':warehouse_id'           =>  trim($_POST["spray_warehouse_id"][$count2]),
				':invoice_date'           =>  date("Y-m-d"),
				':poster'           =>   $_SESSION['NEWERP_SESS_MEMBER_ID'],
				':date'           =>  date("Y-m-d"),
				':lastupdate'           =>  $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] 
			
			
			  )
			);
		}

		$countThis =  0;
		}

	}


	for($count=0; $count<$_POST["total_item"]; $count++)

	{

		$stock = STOCK::RAW_ITEM_WISE_STOCK($_POST["warehouse_id"][$count],$_POST["product_id"][$count],'warehouse_wise');

		if($_POST["dispatch_now"][$count] > $stock['ITEM_STOCK']){

			$countThis2 = $count+1;
			break;
		}else{

		if( trim($_POST["dispatch_now"][$count]) > 0 ){
			
			$statement = $conn_me->prepare("INSERT INTO `history_spray_raw_item_dispatch` 
			(   `code`, `supplier_or_factory_id`,`send_to`, `product_id`, `dispatch_quantity`,`warehouse_id`, `invoice_date`, `poster`, `date`, `lastupdate`)
			VALUES (:code, :supplier_or_factory_id, :send_to, :product_id, :dispatch_quantity, :warehouse_id, :invoice_date, :poster, :date, :lastupdate )
			");
			
			$statement->execute(
			  array(
				':code'               =>  $_POST['code'],
				':supplier_or_factory_id'               =>   trim($_POST["supplier_or_factory_id"]),
				':send_to'               =>   trim($_POST["send_to"]),
				':product_id'           =>  trim($_POST["product_id"][$count]),
				':dispatch_quantity'           =>  trim($_POST["dispatch_now"][$count]),
				':warehouse_id'           =>  trim($_POST["warehouse_id"][$count]),
				':invoice_date'           =>  date("Y-m-d"),
				':poster'           =>   $_SESSION['NEWERP_SESS_MEMBER_ID'],
				':date'           =>  date("Y-m-d"),
				':lastupdate'           =>  $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] 
			
			
			  )
			);
		}

		$countThis =  0;


		}
	}



	if($countThis > 0 ) { print "$countThis item not enough in selected warehouse";}else{ print  "Data Updated"; };





}else if ($_POST['action'] == 'dispatch_raw_material_for_molding'){



	for($count=0; $count<$_POST["total_item"]; $count++)

	{

		if( trim($_POST["dispatch_now"][$count]) > 0 ){

			
			$stock = STOCK::RAW_ITEM_WISE_STOCK($_POST["warehouse_id"][$count],$_POST["product_id"][$count],'warehouse_wise');


			if( $_POST["dispatch_now"][$count] > $stock['ITEM_STOCK'] ){

				$mess = $count+1;
			    break;

			}else{

				$statement = $conn_me->prepare("INSERT INTO `history_mold_raw_item_dispatch` 
				(   `code`, `supplier_or_factory_id`,`send_to`, `product_id`, `dispatch_quantity`,`warehouse_id`, `invoice_date`, `poster`, `date`,`time` `lastupdate`)
				VALUES (:code, :supplier_or_factory_id, :send_to, :product_id, :dispatch_quantity, :warehouse_id, :invoice_date, :poster, :date, :time,:lastupdate )
				");
				
				$statement->execute(
				  array(
					':code'               =>  $_POST['code'],
					':supplier_or_factory_id'               =>   trim($_POST["supplier_or_factory_id"]),
					':send_to'               =>   trim($_POST["send_to"]),
					':product_id'           =>  trim($_POST["product_id"][$count]),
					':dispatch_quantity'           =>  trim($_POST["dispatch_now"][$count]),
					':warehouse_id'           =>  trim($_POST["warehouse_id"][$count]),
					':invoice_date'           =>  date("Y-m-d"),
					':poster'           =>   $_SESSION['NEWERP_SESS_MEMBER_ID'],
					':date'           =>  date("Y-m-d"),
					':time'           => date("H:i:s a"),
					':lastupdate'           =>  $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] 
				
				
				  )
				);

				$mess = 0;
			}
			
		
		}

		

	
	}

	 if($mess > 0 ) { print "$mess item not enough in selected warehouse";}else{ print  "Data Updated"; };

	
}else if ($_POST['action'] == 'create_print_recipe_wise_demand'){





	$prepaire_table = SETUP::SETUP_CODE_INSERT_DATA('raw_print');


    $accepting_delivery_date = date("Y-m-d", strtotime($_POST['accepting_delivery_date']));  


	for($count=0; $count<$_POST["total_item"]; $count++)

	{

		$statement = $conn_me->prepare("INSERT INTO `raw_print_item` 
		(   `demand_code`, `material_id`, `raw_print_id`,`demand_quantity`, `date`, `time`, `poster`, `lastupdate`)
		VALUES (:demand_code, :material_id, :raw_print_id, :demand_quantity, :date, :time, :poster, :lastupdate  )
		");
		
		$statement->execute(
		  array(
			':demand_code'               =>  $prepaire_table['related_code'],
			':material_id'           =>  trim($_POST["material_id"][$count]),
			':raw_print_id'           =>  $prepaire_table['last_id'],
			':demand_quantity'           =>  trim($_POST["demand_qty"][$count]),
			':date'           =>  date("Y-m-d"),
			':time'           => date("H:i:s a"),
			':poster'           =>   $_SESSION['NEWERP_SESS_MEMBER_ID'],
			':lastupdate'           =>  $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] 
		
		
		  )
		);

	}


			
	$query = $conn_me->prepare("UPDATE `raw_print` 
	
	SET
	`supplier_or_factory_id` = '".$_POST['send_to_id']."',
	`send_to` = '".$_POST['send_to']."',
	`material_id` = '".$_POST['print_material_id']."',
	`accepting_delivery_date` ='".$accepting_delivery_date."',
	`batch_quantity` ='".$_POST['batch_quantity']."',
	`note` ='".$_POST['note']."',
	`invoice_date` ='" . date("Y-m-d") . "',
	`date` = '" . date("Y-m-d") . "',
	`time` =  '" . date("h:i:s a") . "',
	`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
	`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
	
	
	WHERE `id` = '".$prepaire_table['last_id']."'  ");
	
	$query->execute();


	print 'Demand Created';
	

}else if ($_POST['action'] == 'create_spray_recipe_wise_demand'){


	

	$prepaire_table = SETUP::SETUP_CODE_INSERT_DATA('raw_spray');


    $accepting_delivery_date = date("Y-m-d", strtotime($_POST['accepting_delivery_date']));  


	for($count=0; $count<$_POST["total_item"]; $count++)

	{

		$statement = $conn_me->prepare("INSERT INTO `raw_spray_item` 
		(   `demand_code`, `material_id`, `raw_spray_id`,`demand_quantity`, `date`, `time`, `poster`, `lastupdate`)
		VALUES (:demand_code, :material_id, :raw_spray_id, :demand_quantity, :date, :time, :poster, :lastupdate  )
		");
		
		$statement->execute(
		  array(
			':demand_code'               =>  $prepaire_table['related_code'],
			':material_id'           =>  trim($_POST["material_id"][$count]),
			':raw_spray_id'           =>  $prepaire_table['last_id'],
			':demand_quantity'           =>  trim($_POST["demand_qty"][$count]),
			':date'           =>  date("Y-m-d"),
			':time'           => date("H:i:s a"),
			':poster'           =>   $_SESSION['NEWERP_SESS_MEMBER_ID'],
			':lastupdate'           =>  $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] 
		
		
		  )
		);

	}


			
	$query = $conn_me->prepare("UPDATE `raw_spray` 
	
	SET
	`supplier_or_factory_id` = '".$_POST['send_to_id']."',
	`send_to` = '".$_POST['send_to']."',
	`material_id` = '".$_POST['spray_material_id']."',
	`accepting_delivery_date` ='".$accepting_delivery_date."',
	`batch_quantity` ='".$_POST['batch_quantity']."',
	`note` ='".$_POST['note']."',
	`invoice_date` ='" . date("Y-m-d") . "',
	`date` = '" . date("Y-m-d") . "',
	`time` =  '" . date("h:i:s a") . "',
	`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
	`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
	
	
	WHERE `id` = '".$prepaire_table['last_id']."'  ");
	
	$query->execute();


	print 'Demand Created';


}else if ($_POST['action'] == 'create_molding_recipe_wise_demand'){


	

	$prepaire_table = SETUP::SETUP_CODE_INSERT_DATA('raw_molding');


    $accepting_delivery_date = date("Y-m-d", strtotime($_POST['accepting_delivery_date']));  


	for($count=0; $count<$_POST["total_item"]; $count++)

	{

		$statement = $conn_me->prepare("INSERT INTO `raw_molding_item` 
		(   `demand_code`, `material_id`, `raw_molding_id`,`demand_quantity`, `date`, `time`, `poster`, `lastupdate`)
		VALUES (:demand_code, :material_id, :raw_molding_id, :demand_quantity, :date, :time, :poster, :lastupdate  )
		");
		
		$statement->execute(
		  array(
			':demand_code'               =>  $prepaire_table['related_code'],
			':material_id'           =>  trim($_POST["material_id"][$count]),
			':raw_molding_id'           =>  $prepaire_table['last_id'],
			':demand_quantity'           =>  trim($_POST["demand_qty"][$count]),
			':date'           =>  date("Y-m-d"),
			':time'           => date("H:i:s a"),
			':poster'           =>   $_SESSION['NEWERP_SESS_MEMBER_ID'],
			':lastupdate'           =>  $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] 
		
		
		  )
		);

	}


			
	$query = $conn_me->prepare("UPDATE `raw_molding` 
	
	SET
	`supplier_or_factory_id` = '".$_POST['send_to_id']."',
	`send_to` = '".$_POST['molding_type']."',
	`supporting_id` = '".$_POST['supporting_id']."',
	`accepting_delivery_date` ='".$accepting_delivery_date."',
	`batch_quantity` ='".$_POST['batch_quantity']."',
	`note` ='".$_POST['note']."',
	`invoice_date` ='" . date("Y-m-d") . "',
	`date` = '" . date("Y-m-d") . "',
	`time` =  '" . date("h:i:s a") . "',
	`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
	`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
	
	
	WHERE `id` = '".$prepaire_table['last_id']."'  ");
	
	$query->execute();


	print 'Demand Created';



}else if ($_POST['action'] == 'create_recipe_wise_demand'){



		$prepaire_table = SETUP::SETUP_CODE_INSERT_DATA('raw_request_recipe_wise');


		$accepting_delivery_date = date("Y-m-d", strtotime($_POST['accepting_delivery_date']));  
	
	
		for($count=0; $count<$_POST["total_item"]; $count++)
	
		{
	
			$statement = $conn_me->prepare("INSERT INTO `raw_request_recipe_wise_item` 
			(   `demand_code`, `material_id`, `raw_recipe_wise_request_id`,`demand_quantity`, `date`, `time`, `poster`, `lastupdate`)
			VALUES (:demand_code, :material_id, :raw_recipe_wise_request_id, :demand_quantity, :date, :time, :poster, :lastupdate  )
			");
			
			$statement->execute(
			  array(
				':demand_code'               =>  $prepaire_table['related_code'],
				':material_id'           =>  trim($_POST["material_id"][$count]),
				':raw_recipe_wise_request_id'           =>  $prepaire_table['last_id'],
				':demand_quantity'           =>  trim($_POST["demand_qty"][$count]),
				':date'           =>  date("Y-m-d"),
				':time'           => date("H:i:s a"),
				':poster'           =>   $_SESSION['NEWERP_SESS_MEMBER_ID'],
				':lastupdate'           =>  $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] 
			
			
			  )
			);
	
		}
	

				
		$query = $conn_me->prepare("UPDATE `raw_request_recipe_wise` 
		
		SET
		`product_id` = '".$_POST['product_id']."',
		`accepting_delivery_date` ='".$accepting_delivery_date."',
		`batch_quantity` ='".$_POST['batch_quantity']."',
		`send_to` ='".$_POST['send_to']."',
		`send_to_id` ='".$_POST['send_to_id']."',
		`note` ='".$_POST['note']."',
		`user_given_invoiceno` ='".$_POST['pi_no']."',
		`invoice_date` ='" . date("Y-m-d") . "',
		`date` = '" . date("Y-m-d") . "',
		`time` =  '" . date("h:i:s a") . "',
		`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
		
		
		WHERE `id` = '".$prepaire_table['last_id']."'  ");
		
		$query->execute();
	
	
		print 'Demand Created';



	
}else if ($_POST['action'] == 'final_print_recipe'){


	$query = $conn_me->prepare("UPDATE `receip_print` 
	
	SET
	`status` = 'Done'
	
	WHERE `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `status` = 'Pending' ");
	
	$query->execute();
	
	if ($query) {
		echo 'Update Success' ;
	} 
	else {
		echo 'Update Failed';
	}


}else if ($_POST['action'] == 'final_spray_recipe'){


	$query = $conn_me->prepare("UPDATE `receip_spray` 
	
	SET
	`status` = 'Done'
	
	WHERE `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `status` = 'Pending' ");
	
	$query->execute();
	
	if ($query) {
		echo 'Update Success' ;
	} 
	else {
		echo 'Update Failed';
	}


}else if ($_POST['action'] == 'final_supporting_recipe'){


	$query = $conn_me->prepare("UPDATE `receip_supporting_goods` 
	
	SET
	`status` = 'Done'
	
	WHERE `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `status` = 'Pending' ");
	
	$query->execute();
	
	if ($query) {
		echo 'Update Success' ;
	} 
	else {
		echo 'Update Failed';
	}

}else if ($_POST['action'] == 'final_fg_recipe'){


	
	
	$query = $conn_me->prepare("UPDATE `receip_fg` 
	
	SET
	`status` = 'Done'
	
	WHERE `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `status` = 'Pending' ");
	
	$query->execute();
	
	if ($query) {
		echo 'Update Success' ;
	} 
	else {
		echo 'Update Failed';
	}

	

}else if ($_POST['action'] == 'final_raw_writeoff'){



	$writeoff_date = date("Y-m-d", strtotime($_POST['writeoff_date']));
	$CODE = SETUP::SETUP_CODE('raw_opening_stock');

	
	
	$query = $conn_me->prepare("UPDATE `raw_opening_stock` 
	
	SET
	`status` = 'Done',
	`invoice_no` = '".$CODE['invoice_no']."',
	`code` = '".$CODE['related_code']."',
	`invoice_date` = '".clean($writeoff_date)."',
	`date` = '" . date("Y-m-d") . "',
	`time` =  '" . date("h:i:s a") . "',
	`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
	`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
	
	
	WHERE `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `status` = 'Pending' ");
	
	$query->execute();
	
	if ($query) {
		echo 'Update Success' ;
	} 
	else {
		echo 'Update Failed';
	}
	


	
}else if ($_POST['action'] == 'final_damage'){

	$invoice_date = date("Y-m-d", strtotime($_POST['invoice_date']));
	$CODE = SETUP::SETUP_CODE('fg_damage_store');
	$related_code = $CODE['code']; 
	$qry = $conn_me->prepare("SELECT `warehouse_id`,`product_id`,`quantity` FROM `fg_damage_store`  where  `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `status` = 'Pending' ");
	$qry->execute();
	$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
	foreach ($fetch_list as $fetch) {
		QUICK_BALANCE::FG_QUICK_STOCK($fetch['product_id'],$fetch['warehouse_id'],$fetch['quantity'],'stock_out',$invoice_date);
	}


	$query = $conn_me->prepare("UPDATE `fg_damage_store` 
	
	SET
	`status` = 'Done',
	`invoice_no` = '".$CODE['invoice_no']."',
	`code` = '".clean($CODE['code'])."', 
	`invoice_date` = '".clean($invoice_date)."',
	`dispatcher_id` = '".clean($_POST['dispatcher_id'])."',
	`date` = '" . date("Y-m-d") . "',
	`time` =  '" . date("h:i:s a") . "',
	`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
	`brunch_id` =   '" . $_SESSION['USER_BRUNCH'] . "',
	`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
	WHERE `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `status` = 'Pending' ");
	
	$query->execute();
	




	if ($query) {
		$mess = 'Update Success' ;
	} 
	else {
		$mess = 'Update Failed';
	}

	print json_encode(array 
(
    'mess' => $mess,
    'code' => $related_code
));



}else if ($_POST['action'] == 'final_writeoff'){

	$writeoff_date = date("Y-m-d", strtotime($_POST['writeoff_date']));
	$CODE = SETUP::SETUP_CODE('fg_opening_stock');

	$qry = $conn_me->prepare("SELECT `warehouse_id`,`product_id`,`quantity` FROM `fg_opening_stock`  where  `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `status` = 'Pending' ");
	$qry->execute();
	$fetch_list = $qry->fetchAll(PDO::FETCH_ASSOC);
	foreach ($fetch_list as $fetch) {
		QUICK_BALANCE::FG_QUICK_STOCK($fetch['product_id'],$fetch['warehouse_id'],$fetch['quantity'],'stock_in',$writeoff_date);
	}


	$query = $conn_me->prepare("UPDATE `fg_opening_stock` 
	
	SET
	`status` = 'Done',
	`invoice_no` = '".$CODE['invoice_no']."',
	`code` = '".clean($CODE['code'])."',
	`invoice_date` = '".clean($writeoff_date)."',
	`date` = '" . date("Y-m-d") . "',
	`time` =  '" . date("h:i:s a") . "',
	`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
	`brunch_id` =   '" . $_SESSION['USER_BRUNCH'] . "',
	`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
	WHERE `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `status` = 'Pending' ");
	
	$query->execute();
	




	if ($query) {
		echo 'Update Success' ;
	} 
	else {
		echo 'Update Failed';
	}

}else if ($_POST['action'] == 'add_cart_print_recipe'){

	
	$print_material_id= clean($_POST['print_material_id']);
	$quantity= clean($_POST['quantity']);
	$raw_material_id= clean($_POST['raw_material_id']);
	
	
	
	
	if($_POST['related_id'] == 'new_id' ){
	
				
	$qry = $conn_me->prepare("SELECT * FROM `receip_print` WHERE `print_material_id` = '".$print_material_id."' AND  `raw_material_id` = '".$raw_material_id."' ");
	$qry->execute();
	$count = $qry->rowCount();
	if($count > 0 ){
print "This material alredy in recipe";
	}else{
	
	
	$query = $conn_me->exec("INSERT INTO `receip_print` 
	( 
		`id`, `print_material_id`, `raw_material_id`,`quantity`, `date`, `time`, `poster`, `lastupdate`
	) 
	VALUES
	(
	'0',
	'".$print_material_id."',
		'".$raw_material_id."',
	'".$quantity."',
	'" . date("Y-m-d") . "',
		'" . date("h:i:s a") . "',
		'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
	
	
	) ");
	
	
	
		if ($query) {
			echo 'Insert Success';
		} 
		else {
			echo 'Insert Failed';
		}
	
	}
	
	}else{
	
		$query = $conn_me->prepare("UPDATE `receip_print` 
	
		SET
		`print_material_id` = '".$print_material_id."',
		`quantity` = '".$quantity."',
		`raw_material_id` = '".$raw_material_id."',
		`date` = '" . date("Y-m-d") . "',
		`time` =  '" . date("h:i:s a") . "',
		`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
	
	
		WHERE `id` = '".$_POST['related_id']."'");
	
		$query->execute();
	
		if ($query) {
			echo 'Update Success' ;
		} 
		else {
			echo 'Update Failed';
		}
	
	
	
	}


}else if ($_POST['action'] == 'add_cart_spray_recipe'){
	$spray_material_id= clean($_POST['spray_material_id']);
	$quantity= clean($_POST['quantity']);
	$raw_material_id= clean($_POST['raw_material_id']);
	
	
	
	
	if($_POST['related_id'] == 'new_id' ){
	
		
		$qry = $conn_me->prepare("SELECT * FROM `receip_spray` WHERE `spray_material_id` = '".$spray_material_id."' AND  `raw_material_id` = '".$raw_material_id."' ");
	$qry->execute();
	$count = $qry->rowCount();
	if($count > 0 ){
print "This material alredy in recipe";
	}else{
	
	$query = $conn_me->exec("INSERT INTO `receip_spray` 
	( 
		`id`, `spray_material_id`, `raw_material_id`,`quantity`, `date`, `time`, `poster`, `lastupdate`
	) 
	VALUES
	(
	'0',
	'".$spray_material_id."',
		'".$raw_material_id."',
	'".$quantity."',
	'" . date("Y-m-d") . "',
		'" . date("h:i:s a") . "',
		'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
	
	
	) ");
	
	
	
		if ($query) {
			echo 'Insert Success';
		} 
		else {
			echo 'Insert Failed';
		}
	
	
	}

	}else{
	
		$query = $conn_me->prepare("UPDATE `receip_spray` 
	
		SET
		`spray_material_id` = '".$spray_material_id."',
		`quantity` = '".$quantity."',
		`raw_material_id` = '".$raw_material_id."',
		`date` = '" . date("Y-m-d") . "',
		`time` =  '" . date("h:i:s a") . "',
		`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
	
	
		WHERE `id` = '".$_POST['related_id']."'");
	
		$query->execute();
	
		if ($query) {
			echo 'Update Success' ;
		} 
		else {
			echo 'Update Failed';
		}
	
	
	
	}



}else if ($_POST['action'] == 'add_cart_supporting_recipe'){



	$supporting_id= clean($_POST['supporting_id']);
	$quantity= clean($_POST['quantity']);
	$raw_material_id= clean($_POST['raw_material_id']);
	
	
	
	
	if($_POST['related_id'] == 'new_id' ){
	
		$qry = $conn_me->prepare("SELECT * FROM `receip_supporting_goods` WHERE `supporting_id` = '".$supporting_id."' AND  `raw_material_id` = '".$raw_material_id."' ");
		$qry->execute();
		$count = $qry->rowCount();
		if($count > 0 ){
	print "This material alredy in recipe";
		}else{
	
	
	$query = $conn_me->exec("INSERT INTO `receip_supporting_goods` 
	( 
		`id`, `supporting_id`, `raw_material_id`,`quantity`, `date`, `time`, `poster`, `lastupdate`
	) 
	VALUES
	(
	'0',
	'".$supporting_id."',
		'".$raw_material_id."',
	'".$quantity."',
	'" . date("Y-m-d") . "',
		'" . date("h:i:s a") . "',
		'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
	
	
	) ");
	
	
	
		if ($query) {
			echo 'Insert Success';
		} 
		else {
			echo 'Insert Failed';
		}
	
		}
	
	}else{
	
		$query = $conn_me->prepare("UPDATE `receip_supporting_goods` 
	
		SET
		`supporting_id` = '".$supporting_id."',
		`quantity` = '".$quantity."',
		`raw_material_id` = '".$raw_material_id."',
		`date` = '" . date("Y-m-d") . "',
		`time` =  '" . date("h:i:s a") . "',
		`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
	
	
		WHERE `id` = '".$_POST['related_id']."'");
	
		$query->execute();
	
		if ($query) {
			echo 'Update Success' ;
		} 
		else {
			echo 'Update Failed';
		}
	
	
	
	}


}else if ($_POST['action'] == 'add_cart_fg_recipe'){





	$product_id= clean($_POST['product_id']);
	$quantity= clean($_POST['quantity']);
	$raw_material_id= clean($_POST['raw_material_id']);
	
	
	
	
	if($_POST['related_id'] == 'new_id' ){
	
		
	$qry = $conn_me->prepare("SELECT * FROM `receip_fg` WHERE `product_id` = '".$product_id."' AND  `raw_material_id` = '".$raw_material_id."' ");
	$qry->execute();
	$count = $qry->rowCount();
	if($count > 0 ){
print "This material alredy in recipe";
	}else{

		$query = $conn_me->exec("INSERT INTO `receip_fg` 
	( 
		`id`, `product_id`, `raw_material_id`,`quantity`, `date`, `time`, `poster`, `lastupdate`
	) 
	VALUES
	(
	'0',
	'".$product_id."',
		'".$raw_material_id."',
	'".$quantity."',
	'" . date("Y-m-d") . "',
		'" . date("h:i:s a") . "',
		'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
	
	
	) ");
	
	if ($query) {
		echo 'Insert Success';
	} 
	else {
		echo 'Insert Failed';
	}


	}
	
	

	}else{
	
		$query = $conn_me->prepare("UPDATE `receip_fg` 
	
		SET
		`product_id` = '".$product_id."',
		`quantity` = '".$quantity."',
		`raw_material_id` = '".$raw_material_id."',
		`date` = '" . date("Y-m-d") . "',
		`time` =  '" . date("h:i:s a") . "',
		`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
	
	
		WHERE `id` = '".$_POST['related_id']."'");
	
		$query->execute();
	
		if ($query) {
			echo 'Update Success' ;
		} 
		else {
			echo 'Update Failed';
		}
	
	
	
	}
	
}else if ($_POST['action'] == 'add_cart_raw_writeoff'){

	

	
$notes= clean($_POST['notes']);

$product_id= clean($_POST['product_id']);
$quantity= clean($_POST['quantity']);
$warehouse_id= clean($_POST['warehouse_id']);




if($_POST['related_id'] == 'new_id' ){

	


$query = $conn_me->exec("INSERT INTO `raw_opening_stock` 
( 
	`id`, `product_id`, `quantity`,`warehouse_id`, `notes`, `date`, `time`, `poster`, `lastupdate`
) 
VALUES
(
'0',
'".$product_id."',
'".$quantity."',
'".$warehouse_id."',
'".$notes."',
'" . date("Y-m-d") . "',
    '" . date("h:i:s a") . "',
    '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
    '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


) ");



    if ($query) {
        echo 'Insert Success';
    } 
    else {
        echo 'Insert Failed';
    }



}else{

    $query = $conn_me->prepare("UPDATE `raw_opening_stock` 

    SET
	`product_id` = '".$product_id."',
	`quantity` = '".$quantity."',
	`warehouse_id` = '".$warehouse_id."',
    `notes` = '".$notes."',
	`date` = '" . date("Y-m-d") . "',
	`time` =  '" . date("h:i:s a") . "',
	`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
	`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


    WHERE `id` = '".$_POST['related_id']."'");

    $query->execute();

    if ($query) {
        echo 'Update Success' ;
    } 
    else {
        echo 'Update Failed';
    }



}


}else if ($_POST['action'] == 'add_cart_damage'){

	

	
$notes= clean($_POST['notes']);

$product_id= clean($_POST['product_id']);
$quantity= clean($_POST['quantity']);
$warehouse_id= clean($_POST['warehouse_id']);





$qu = $conn_me->prepare("SELECT *  FROM `fg_damage_store`  where `poster` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' AND `status` = 'Pending' AND `product_id` = '".$product_id."' ");
$qu->execute();
$count = $qu->rowCount();

if($count > 0 ){

	$mess =  "Alredy Added";

}else{
	$stock = STOCK::FG_ITEM_WISE_STOCK($warehouse_id,$product_id,'warehouse_wise');


	
if($stock['ITEM_STOCK'] < $quantity){

	$mess =  "This warehouse have only $stock[ITEM_STOCK] pcs";

}else{

	
$query = $conn_me->exec("INSERT INTO `fg_damage_store` 
( 
`id`, `product_id`, `quantity`,`warehouse_id`, `notes`, `date`, `time`, `poster`, `lastupdate`
) 
VALUES
(
'0',
'".$product_id."',
'".$quantity."',
'".$warehouse_id."',
'".$notes."',
'" . date("Y-m-d") . "',
'" . date("h:i:s a") . "',
'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


) ");



    if ($query) {
		$mess =  'Insert Success';
    } 
    else {
        $mess =  'Insert Failed';
    }




}



}


print $mess;


}else if ($_POST['action'] == 'add_cart_writeoff'){

	
$notes= clean($_POST['notes']);

$product_id= clean($_POST['product_id']);
$quantity= clean($_POST['quantity']);
$warehouse_id= clean($_POST['warehouse_id']);




if($_POST['related_id'] == 'new_id' ){

	


$query = $conn_me->exec("INSERT INTO `fg_opening_stock` 
( 
	`id`, `product_id`, `quantity`,`warehouse_id`, `notes`, `date`, `time`, `poster`, `lastupdate`
) 
VALUES
(
'0',
'".$product_id."',
'".$quantity."',
'".$warehouse_id."',
'".$notes."',
'" . date("Y-m-d") . "',
    '" . date("h:i:s a") . "',
    '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
    '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


) ");



    if ($query) {
        echo 'Insert Success';
    } 
    else {
        echo 'Insert Failed';
    }



}else{

    $query = $conn_me->prepare("UPDATE `fg_opening_stock` 

    SET
	`product_id` = '".$product_id."',
	`quantity` = '".$quantity."',
	`warehouse_id` = '".$warehouse_id."',
    `notes` = '".$notes."',
	`date` = '" . date("Y-m-d") . "',
	`time` =  '" . date("h:i:s a") . "',
	`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
	`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


    WHERE `id` = '".$_POST['related_id']."'");

    $query->execute();

    if ($query) {
        echo 'Update Success' ;
    } 
    else {
        echo 'Update Failed';
    }



}


}else if ($_POST['action'] == 'create_raw_category'){




	$category_name= clean($_POST['category_name']);



if($_POST['related_id'] == 'new_id' ){

$query = $conn_me->exec("INSERT INTO `setup_raw_material_category` 
( 
`id` , `category`, `date`, `time`, `poster`, `lastupdate`
) 
VALUES
(
'0',
'".$category_name."',
'" . date("Y-m-d") . "',
    '" . date("h:i:s a") . "',
    '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
    '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


) ");

$last_category_id = $conn_me->lastInsertId();


    if ($query) {
        echo 'Insert Success_SAJID_' . $last_category_id;
    } 
    else {
        echo 'Insert Failed';
    }



}else{

    $query = $conn_me->prepare("UPDATE `setup_raw_material_category` 

    SET
    `category` = '".clean($category_name)."',
	`date` = '" . date("Y-m-d") . "',
	`time` =  '" . date("h:i:s a") . "',
	`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
	`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


    WHERE `id` = '".$_POST['related_id']."'");

    $query->execute();

    if ($query) {
        echo 'Update Success' ;
    } 
    else {
        echo 'Update Failed';
    }



}




}else if ($_POST['action'] == 'save_category'){





	$category_name= clean($_POST['category_name']);



if($_POST['related_id'] == 'new_id' ){

$query = $conn_me->exec("INSERT INTO `setup_category` 
( 
`id` , `category`, `date`, `time`, `poster`, `lastupdate`
) 
VALUES
(
'0',
'".$category_name."',
'" . date("Y-m-d") . "',
    '" . date("h:i:s a") . "',
    '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
    '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


) ");

$last_category_id = $conn_me->lastInsertId();


    if ($query) {
        echo 'Insert Success_SAJID_' . $last_category_id;
    } 
    else {
        echo 'Insert Failed';
    }



}else{

    $query = $conn_me->prepare("UPDATE `setup_category` 

    SET
    `category` = '".clean($category_name)."',
	`date` = '" . date("Y-m-d") . "',
	`time` =  '" . date("h:i:s a") . "',
	`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
	`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


    WHERE `id` = '".$_POST['related_id']."'");

    $query->execute();

    if ($query) {
        echo 'Update Success' ;
    } 
    else {
        echo 'Update Failed';
    }



}




}else if ($_POST['action'] == 'save_unit'){

	$unit_name= clean($_POST['unit_name']);



    if($_POST['related_id'] == 'new_id' ){
    
    $query = $conn_me->exec("INSERT INTO `setup_unit` 
    ( 
    `id` , `unit`
    ) 
    VALUES
    (
    '0',
    '".$unit_name."'
    
    ) ");
    
    
    
        if ($query) {
            echo 'Insert Success' ;
        } 
        else {
            echo 'Insert Failed';
        }
    
    
    
    }else{
    
        $query = $conn_me->prepare("UPDATE `setup_unit` 
    
        SET
        `unit` = '".clean($unit_name)."'
    
    
        WHERE `id` = '".$_POST['related_id']."'");
    
        $query->execute();
    
        if ($query) {
            echo 'Update Success' ;
        } 
        else {
            echo 'Update Failed';
        }
    
    
    
    }



}else if ($_POST['action'] == 'save_transport_cost'){

	



	$district_id= clean($_POST['district_id']);
	$nogot_cost= clean($_POST['nogot_cost']);
	$vaki_cost= clean($_POST['vaki_cost']);



if($_POST['related_id'] == 'new_id' ){



$query = $conn_me->exec("INSERT INTO `tansport_cost` 
( 

	`id`, `district_id`, `nogot_cost`, `vaki_cost`

) 
VALUES
(
'0',
'".$district_id."',
'".$nogot_cost."',
'".$vaki_cost."'


) ");



    if ($query) {
        echo 'Insert Success' ;
    } 
    else {
        echo 'Insert Failed';
    }



}else{

    $query = $conn_me->prepare("UPDATE `tansport_cost` 

    SET

		`district_id` = '".$district_id."',
		`nogot_cost` = '".$nogot_cost."',
		`vaki_cost` = '".$vaki_cost."'



    WHERE `id` = '".$_POST['related_id']."'");

    $query->execute();

    if ($query) {
        echo 'Update Success' ;
    } 
    else {
        echo 'Update Failed';
    }



}






}else if ($_POST['action'] == 'save_machine_lcoation'){


	if (!file_exists($_POST['file_path'])) {   
		
	print "Please insert Data from machine first";

	}else{


		$query = $conn_me->prepare("UPDATE `setup_machine_location` 

			SET
			`file_path` = '".$_POST['file_path']."',
			`date` =  '" . date("Y-m-d") . "',
			`poster` = '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "'
			
			");
		
			$query->execute();



	print "Machine location set Done !";
	}

		

}else if ($_POST['action'] == 'get_data_from_machine'){

$info_file = SETUP::MACHINE_LOCATION();

if (!file_exists($info_file['file_path'])) {   
		
	print "Please insert Data from machine first";

}else{

	$xml = simplexml_load_file("$info_file[file_path]");

	foreach ($xml->ROWDATA -> ROW as  $value) {
	
		$today = date('Y-m-d');
		$info_employee = SETUP::SETUP_EMPLOYEEY_BY_CODE($value['BadgeNumber']);

		$qry = $conn_me->prepare("SELECT *  FROM `take_attandance`  where `employee_id` = '".$info_employee['id']."' AND `attandance_date` = '".$today."' ");
		$qry->execute();
		$fetch = $qry->fetch(PDO::FETCH_ASSOC);
		if ($qry->rowCount() > 0) { 

			$query = $conn_me->prepare("UPDATE `take_attandance` 
			


			SET
			`present` = 	IF('".$value['Absent']."' = 0,1,0),
			`late` =  '".$value['Late']."',
			`absent` = '".$value['Absent']."',
			`leave` =  '".$info_employee['in_leave']."',
			`note` =  'From Machine',
			`date` =  '" . date("Y-m-d") . "',
			`time` = '" . date("h:i:s a") . "',
			`poster` = '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
			`lastupdate` = '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
		
		
		
			WHERE `id` = '".$fetch['id']."'");
		
			$query->execute();

		}else{

			$query = $conn_me->exec("INSERT INTO `take_attandance` 
			( 
				`id`, `employee_id`,`department_id`,`note`, `present`, `late`, `absent`, `leave`, `attandance_date`, `year`, `date`, `time`, `poster`,`brunch_id`, `lastupdate` 
			) 
			VALUES
			(
			'0',
			'".$info_employee['id']."',
			'".$info_employee['department_id']."',
			'From Machine',
			IF('".$value['Absent']."' = 0,1,0),
			'".$value['Late']."',
			'".$value['Absent']."',
			'".$info_employee['in_leave']."',
			'" . date("Y-m-d") . "',
			'" . date("Y") . "',
			'" . date("Y-m-d") . "',
			'" . date("h:i:s a") . "',
			'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
			'" . $_SESSION['USER_BRUNCH'] . "',
			'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
			
			) ");
				 



			
		}

		   
		}
	

		print "Data Insert Success";

	}

	


}else if ($_POST['action'] == 'take_attandance_action'){

	$attendance_date = date("Y-m-d", strtotime($_POST['attendance_date']));  

	$year =  date("Y", strtotime($_POST['attendance_date']));
	
	$my_array = json_decode($_POST['today_attendance'], true);

	for ($x = 0; $x < $_POST['count_item']; $x++) {



		$qry = $conn_me->prepare("SELECT *  FROM `take_attandance`  where `employee_id` = '".$my_array[$x]['element2']['employee_id']."' AND `attandance_date` = '".$attendance_date."'  ");
		$qry->execute();
		$fetch = $qry->fetch(PDO::FETCH_ASSOC);

		if ($qry->rowCount() > 0) {


			$query = $conn_me->prepare("UPDATE `take_attandance` 

			SET

			`present` = '".$my_array[$x]['element2']['present']."',
			`late` =  '".$my_array[$x]['element2']['late']."',
			`absent` = '".$my_array[$x]['element2']['absent']."',
			`leave` =  '".$my_array[$x]['element2']['leave']."',
			`date` =  '" . date("Y-m-d") . "',
			`time` = '" . date("h:i:s a") . "',
			`poster` = '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
			`lastupdate` = '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
		
		
		
			WHERE `id` = '".$fetch['id']."'");
		
			$query->execute();

		}else{

	$query = $conn_me->exec("INSERT INTO `take_attandance` 
    ( 
        `id`, `employee_id`, `present`, `late`, `absent`, `leave`, `attandance_date`, `year`, `date`, `time`, `poster`, `brunch_id`,`lastupdate` 
    ) 
    VALUES
    (
    '0',
    '".$my_array[$x]['element2']['employee_id']."',
    '".$my_array[$x]['element2']['present']."',
    '".$my_array[$x]['element2']['late']."',
	'".$my_array[$x]['element2']['absent']."',
    '".$my_array[$x]['element2']['leave']."',
    '" . $attendance_date . "',
	'" . date("Y") . "',
	'" . date("Y-m-d") . "',
        '" . date("h:i:s a") . "',
        '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
		'" . $_SESSION['USER_BRUNCH'] . "',
        '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
    
    ) ");
         

	}
}

	
	
	print 'Attandance Done';

	

}else if ($_POST['action'] == 'save_advance'){

	
	$data = [
		'less_then_1_year' => $_POST['less_then_1_year'],
		'more_then_1_less_then_2' => $_POST['more_then_1_less_then_2'],
		'more_then_2_less_then_3' => $_POST['more_then_2_less_then_3'],
		'more_then_3_less_then_4' => $_POST['more_then_3_less_then_4'],
		'more_then_4_less_then_5' => $_POST['more_then_4_less_then_5'],
		'more_then_5' => $_POST['more_then_5'],
		'date' => date("Y-m-d"),
		'poster' => $_SESSION['NEWERP_SESS_MEMBER_ID'],
		'lastupdate' => $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR']
		];


		$query =   CRUD::updateData('setup_advance',1,$data);
		$mess = $query['mess'] ;

		print $mess;
}else if ($_POST['action'] == 'save_define_leave'){

  
	$leave_type_id = clean($_POST['leave_type_id']);
	$leave_name = clean($_POST['leave_name']);
	$leave_start = date("Y-m-d", strtotime($_POST['leave_start']));
	$leave_end = date("Y-m-d", strtotime($_POST['leave_end']));




	if($leave_type_id == '3' || $leave_type_id == '5' || $leave_type_id == '6' ){


		$date1 = date_create($leave_start);
		$date2 = date_create($leave_end);
		$days = date_diff($date1, $date2);
		$interval = $days->format('%R%a');


		if($leave_type_id == '3'){

           if($interval > 7 ){
			$mess =  "Festival Holiday can not more then 7 days";
			$exucite = 'NO';
			
		   }else{
			$mess = '';
			$exucite = 'YES';
		   }
		}else if ($leave_type_id == '5'){

			if($interval > 1 ){
				$mess =  "Casual Leave can not more then 1 days in a month";
				$exucite = 'NO';
				
			   }else{
				$mess = '';
				$exucite = 'YES';
			   }
		
	}else if ($leave_type_id == '6'){

		if($interval > 5 ){
			$mess =  "Annual Leave can not more then 5 days in row";
			$exucite = 'NO';
			
		   }else{
			$exucite = 'YES';
			$mess = '';
		   }

		}else{
			$mess = '';
			$exucite = 'NO';
		}

		if($exucite == 'YES'){
			for($count=0; $count<$_POST["num_item"]; $count++)

			{
			
				$data = [
					'employee_id' => $_POST['employee_id'][$count],
					'leave_type_id' => $leave_type_id,
					'leave_from_date' => $leave_start,
					'leave_to_date' => $leave_end,
					'year' => date("Y"),
					'date' => date("Y-m-d"),
					'time' =>date("h:i:s a"),
					'poster' => $_SESSION['NEWERP_SESS_MEMBER_ID'],
					'lastupdate' => $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR']
					];
				
				
					$query =   CRUD::insert_data('apply_leave', $data);
			
				
			}
			$mess = "Done";

		}
		


	}else{




		$data = [
			'description' => $leave_name,
			'holiday' => $leave_start,
			'holiday_year' => date("Y"),
			'date' => date("Y-m-d"),
			'time' =>date("h:i:s a"),
			'poster' => $_SESSION['NEWERP_SESS_MEMBER_ID'],
			'lastupdate' => $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR']
			];


			$query =   CRUD::insert_data('setup_holiday', $data);
			$mess = $query['mess'] ;



	}



	print $mess;


}else if ($_POST['action'] == 'save_area'){



	$area_name= clean($_POST['area_name']);



if($_POST['related_id'] == 'new_id' ){

$query = $conn_me->exec("INSERT INTO `districts` 
( 
`id` , `name`
) 
VALUES
(
'0',
'".$area_name."'

) ");



    if ($query) {
        echo 'Insert Success' ;
    } 
    else {
        echo 'Insert Failed';
    }



}else{

    $query = $conn_me->prepare("UPDATE `districts` 

    SET
    `name` = '".clean($area_name)."'


    WHERE `id` = '".$_POST['related_id']."'");

    $query->execute();

    if ($query) {
        echo 'Update Success' ;
    } 
    else {
        echo 'Update Failed';
    }



}

}else if ($_POST['action'] == 'save_customer'){






	$customer_name= clean($_POST['customer_name']);
	$address= clean($_POST['address']);
	$mobile= clean($_POST['mobile']);
	$email= clean($_POST['email']);
	$customer_type= clean($_POST['customer_type']);
	$division_id= clean($_POST['division_id']);
	$district_id= clean($_POST['district_id']);
	$upazila_id= clean($_POST['upazila_id']);
	$union_id= clean($_POST['union_id']);
	$creadit_limit = clean($_POST['creadit_limit']);
	$shop_name= clean($_POST['shop_name']);
	$in_service= clean($_POST['in_service']);
	$sales_person= clean($_POST['sales_person']);


	if( $creadit_limit > 500000 &&  $_SESSION['USER_TYPE'] != 'Admin'){
		print 'Only Admin can change creadit limit....';
	}else{
		
		if(preg_match('/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im', $mobile)) {





			if($_POST['related_id'] == 'new_id' ){

				$ck_mob = $conn_me->prepare("SELECT `mobile`,`customer_name` FROM `setup_customer` WHERE `mobile` = '".$mobile."' AND `in_service` = 'checked' ");
				$ck_mob->execute();

				if ($ck_mob->rowCount() > 0) {
					$fetch_ck = $ck_mob->fetch(PDO::FETCH_ASSOC);
                    print 'This mobile number Assain with Mr.' . $fetch_ck['customer_name'];

				}else{
					$prepaire_table = SETUP::SETUP_CODE_INSERT_DATA('setup_customer');


					$query = $conn_me->prepare("UPDATE `setup_customer` 
			
					SET
			`customer_name` = '".clean($customer_name)."',
			`in_service` = '".clean($in_service)."',
			`sales_person` = '".clean($sales_person)."',
			`customer_type` = '".clean($customer_type)."',
			`shop_name` = '".clean($shop_name)."',
			`address` = '".clean($address)."',
			`mobile` = '".clean($mobile)."',
		    `status` = 'Done',
			`creadit_limit` = '".clean($creadit_limit)."',
			`division_id` = '".clean($division_id)."',
			`district_id` = '".clean($district_id)."',
			`upazila_id` = '".clean($upazila_id)."',
			`union_id` = '".clean($union_id)."',
			`email` = '".clean($email)."',
			`date` = '" . date("Y-m-d") . "',
			`time` =  '" . date("h:i:s a") . "',
			`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "'
			 
			 WHERE `id` = '".$prepaire_table['last_id']."'");
			
					$query->execute();
					

	

			
					if ($query) {
						echo 'Insert Success' ;
					} 
					else {
						echo 'Insert Failed';
					}
			
			
				}
				}else{
			
					$query = $conn_me->prepare("UPDATE `setup_customer` 
			
					SET
			`customer_name` = '".clean($customer_name)."',
			`customer_type` = '".clean($customer_type)."',
			`sales_person` = '".clean($sales_person)."',
			`in_service` = '".clean($in_service)."',
			`shop_name` = '".clean($shop_name)."',
			`address` = '".clean($address)."',
			`mobile` = '".clean($mobile)."',
			`creadit_limit` = '".clean($creadit_limit)."',
			`division_id` = '".clean($division_id)."',
			`district_id` = '".clean($district_id)."',
			`upazila_id` = '".clean($upazila_id)."',
			`union_id` = '".clean($union_id)."',
			`email` = '".clean($email)."',
			`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
			`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
			
			
					WHERE `id` = '".$_POST['related_id']."'");
			
					$query->execute();
			
					if ($query) {
						echo 'Update Success' ;
					} 
					else {
						echo 'Update Failed';
					}
			
			
			
				}
				
		
		} else {




		print 'Phone number not valid';
		}
  

	}



		

	}else if ($_POST['action'] == 'save_factory'){


		




	$factory_name= clean($_POST['factory_name']);
	$address= clean($_POST['address']);
	$mobile= clean($_POST['mobile']);
	$owner_name= clean($_POST['owner_name']);
	$email= clean($_POST['email']);

		if(preg_match('/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im', $mobile)) {





			if($_POST['related_id'] == 'new_id' ){

				$ck_mob = $conn_me->prepare("SELECT `mobile`,`factory_name` FROM `setup_factory` WHERE `mobile` = '".$mobile."' ");
				$ck_mob->execute();

				if ($ck_mob->rowCount() > 0) {
					$fetch_ck = $ck_mob->fetch(PDO::FETCH_ASSOC);
                    print 'This mobile number Assain with Mr.' . $fetch_ck['factory_name'];

				}else{

				


				$query = $conn_me->exec("INSERT INTO `setup_factory` 
			( 
				`id`, `factory_name`, `address`, `mobile`, `owner_name`, `email`, `date`, `time`, `poster`, `lastupdate`
			) 
			VALUES
			(
				'0',
				'".$factory_name."',
				'".$address."',
				'".$mobile."',
				'".$owner_name."',
				'".$email."',
			'" . date("Y-m-d") . "',
			'" . date("h:i:s a") . "',
			'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
			'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
			
			) ");
			
			
			
					if ($query) {
						echo 'Insert Success' ;
					} 
					else {
						echo 'Insert Failed';
					}
			
			
				}
				}else{
			
					$query = $conn_me->prepare("UPDATE `setup_factory` 
			
					SET
			`factory_name` = '".clean($factory_name)."',
			`address` = '".clean($address)."',
			`mobile` = '".clean($mobile)."',
			`owner_name` = '".clean($owner_name)."',
			`email` = '".clean($email)."',
			`date` = '" . date("Y-m-d") . "',
			`time` =  '" . date("h:i:s a") . "',
			`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
			`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
			
			
					WHERE `id` = '".$_POST['related_id']."'");
			
					$query->execute();
			
					if ($query) {
						echo 'Update Success' ;
					} 
					else {
						echo 'Update Failed';
					}
			
			
			
				}
				
		
		} else {




		print 'Phone number not valid';
		}
  


}else if ($_POST['action'] == 'save_supplier'){




	$supplier_name= clean($_POST['supplier_name']);
	$address= clean($_POST['address']);
	$mobile= clean($_POST['mobile']);
	$owner_name= clean($_POST['owner_name']);
	$email= clean($_POST['email']);
	$description= clean($_POST['description']);

		if(preg_match('/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im', $mobile)) {





			if($_POST['related_id'] == 'new_id' ){

				$ck_mob = $conn_me->prepare("SELECT `mobile`,`supplier_name` FROM `setup_supplier` WHERE `mobile` = '".$mobile."' ");
				$ck_mob->execute();

				if ($ck_mob->rowCount() > 0) {
					$fetch_ck = $ck_mob->fetch(PDO::FETCH_ASSOC);
                    print 'This mobile number Assain with Mr.' . $fetch_ck['supplier_name'];

				}else{

				
					$CODE = SETUP::SETUP_CODE('setup_supplier');
					$relared_code = 	$CODE['code'];

				$query = $conn_me->exec("INSERT INTO `setup_supplier` 
			( 
				`id`,`code`, `supplier_name`, `description`,`address`, `mobile`, `owner_name`, `email`, `date`, `time`, `poster`, `lastupdate`,`status`
			) 
			VALUES
			(
				'0',
				'".$relared_code."',
				'".$supplier_name."',
				'".$description."',
				'".$address."',
				'".$mobile."',
				'".$owner_name."',
				'".$email."',
			'" . date("Y-m-d") . "',
			'" . date("h:i:s a") . "',
			'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
			'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "',
			'Done'
			
			) ");
			
			
			
					if ($query) {
						echo 'Insert Success' ;
					} 
					else {
						echo 'Insert Failed';
					}
			
			
				}
				}else{
			
					$query = $conn_me->prepare("UPDATE `setup_supplier` 
			
					SET
			`supplier_name` = '".clean($supplier_name)."',
			`address` = '".clean($address)."',
			`mobile` = '".clean($mobile)."',
			`description` = '".clean($description)."',
			`owner_name` = '".clean($owner_name)."',
			`email` = '".clean($email)."',
			`date` = '" . date("Y-m-d") . "',
			`time` =  '" . date("h:i:s a") . "',
			`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
			`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
			
			
					WHERE `id` = '".$_POST['related_id']."'");
			
					$query->execute();
			
					if ($query) {
						echo 'Update Success' ;
					} 
					else {
						echo 'Update Failed';
					}
			
			
			
				}
				
		
		} else {




		print 'Phone number not valid';
		}
  

	

	}else if ($_POST['action'] == 'save_brunch'){



		$brunch_name= clean($_POST['brunch_name']);
		$address1= clean($_POST['address1']);
		$address2= clean($_POST['address2']);
		$phone= clean($_POST['phone']);
		$related_warehouse= json_encode($_POST['related_warehouse']);

		
	
	
		if($_POST['related_id'] == 'new_id' ){
	
		$query = $conn_me->exec("INSERT INTO `setup_brunch` 
	( 
		`id`, `brunch`,`related_warehouse`, `address_line_one`,`address_line_two`, `phone`,`date`, `time`, `poster`, `lastupdate`
	) 
	VALUES
	(
		'0',
		'".$brunch_name."',
		'".$related_warehouse."',
		'".$address1."',
		'".$address2."',
		'".$phone."',
	'" . date("Y-m-d") . "',
	'" . date("h:i:s a") . "',
	'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
	'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
	
	) ");
	
	
	
			if ($query) {
				echo 'Insert Success' ;
			} 
			else {
				echo 'Insert Failed';
			}
	
	
	
		}else{
	
			$query = $conn_me->prepare("UPDATE `setup_brunch` 
	
			SET
	`brunch` = '".clean($brunch_name)."',
	`related_warehouse` = '".$related_warehouse."',
	`address_line_one` = '".clean($address1)."',
	`address_line_two` = '".clean($address2)."',
	`date` = '" . date("Y-m-d") . "',
	`time` =  '" . date("h:i:s a") . "',
	`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
	`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'
	
	
			WHERE `id` = '".$_POST['related_id']."'");
	
			$query->execute();
	
			if ($query) {
				echo 'Update Success' ;
			} 
			else {
				echo 'Update Failed';
			}
	
	
	
		}
		
}else if ($_POST['action'] == 'save_warehouse'){


	$warehouse_name= clean($_POST['warehouse_name']);
	$warehouse_address= clean($_POST['warehouse_address']);
	$warehouse_phone= clean($_POST['warehouse_phone']);
	$warehouse_height= clean($_POST['warehouse_height']);
	$warehouse_length= clean($_POST['warehouse_length']);
	$warehouse_width= clean($_POST['warehouse_width']);



	if($_POST['related_id'] == 'new_id' ){

	$query = $conn_me->exec("INSERT INTO `setup_warehouse` 
( 
	`id`, `name`, `address`, `phone`,`height`, `width`, `length`, `date`, `time`, `poster`, `lastupdate`
) 
VALUES
(
	'0',
	'".$warehouse_name."',
	'".$warehouse_address."',
	'".$warehouse_phone."',
	'".$warehouse_height."',
	'".$warehouse_length."',
	'".$warehouse_width."',
'" . date("Y-m-d") . "',
'" . date("h:i:s a") . "',
'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
'" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'

) ");



		if ($query) {
			echo 'Insert Success' ;
		} 
		else {
			echo 'Insert Failed';
		}



	}else{

		$query = $conn_me->prepare("UPDATE `setup_warehouse` 

		SET
`name` = '".clean($warehouse_name)."',
`address` = '".clean($warehouse_address)."',
`phone` = '".clean($warehouse_phone)."',
`height` = '".clean($warehouse_height)."',
`width` = '".clean($warehouse_length)."',
`length` = '".clean($warehouse_width)."',
`date` = '" . date("Y-m-d") . "',
`time` =  '" . date("h:i:s a") . "',
`poster` =   '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . "',
`lastupdate` =  '" . $_SESSION['NEWERP_SESS_MEMBER_ID'] . ' Date: ' . date("d-M-Y") . ' IP ' . $_SERVER['REMOTE_ADDR'] . "'


		WHERE `id` = '".$_POST['related_id']."'");

		$query->execute();

		if ($query) {
			echo 'Update Success' ;
		} 
		else {
			echo 'Update Failed';
		}



	}
    

}else{

    print '505 error';
}