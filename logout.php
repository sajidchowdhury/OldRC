<?php
session_start();
include("connect_me.php");
$conn_me = Database::getInstance();

## DATE TIME
$date = date("Y-m-d");
$time = date("h:i:s a");
	
## STATUS UPDATE
$update = $conn_me->prepare("UPDATE `webpush_member` 
		SET 
			`flag` = '0',
			`last_date` = '".date("Y-m-d")."',
			`last_time`='".date("h:i:s a")."' 
            
		WHERE `member_id` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' ");

$update->execute();	
	
//Unset the variables stored in session
session_destroy();
session_unset();



## REDIRECT
define('BASE_URL', 'https://erp.remotecenter.com.bd');
header("location: login/logout");
exit();
?>
