<?php
include('../auth.php');
include('../connect_me.php');

header("Content-type: application/json");

$data = json_decode(file_get_contents('php://input'), true);

if(is_array($data) && isset($data['endpoint'])){

  

  $query = DB::conn()->prepare("UPDATE `webpush_member` 
	
  SET
  `endpoint` = '".$data['endpoint']."',
  `expirationTime` =  '".$data['expirationTime']."',
  `p256dh` =  '".$data['keys']['p256dh']."',
  `authKey` =  '".$data['keys']['auth']."'

  
  WHERE `member_id` = '".$_SESSION['NEWERP_SESS_MEMBER_ID']."' ");   

$query->execute();


  if($query){
    echo json_encode(['status'=>'ok', 'message'=>'Subscribed']);

  }else{
    echo json_encode(['status'=>'error', 'message'=>'Try Again']);

  }




}