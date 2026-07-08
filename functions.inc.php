<?php 

function uidExists($conn,$username){

    $ck = $conn->prepare(" SELECT* FROM `admin` WHERE `username` = '$username' AND `hr_status` = 'Active'  ");
    $ck->execute();
    $fetch_ck = $ck->fetch(PDO::FETCH_ASSOC);
   if(!$ck){
    header("location: login/stmfailed");
    exit();
   }

    if($fetch_ck){
      return  $fetch_ck;

    }else{

       $result = false;
       return $result;
    }
    
  
}

function emptyInputLogin($username,$password){
  

if(empty($username) || empty($password) ){

    $result =true;
}else{
    $result = false;
}
return $result;
}


function loginUser($conn,$username,$password){

$uidExists = uidExists($conn,$username);


if($uidExists === false){
    header("location: login/wronglogin");
    exit();
}

$pwdHashed = $uidExists['password'];
$checkedPwd = password_verify($password,$pwdHashed);

if($checkedPwd === false){
    header("location: login/wronglogin");
    exit();

}else if($checkedPwd === true){

  
    session_start();
    $_SESSION['NEWERP_SESS_MEMBER_ID'] = $uidExists['id'] ;
    $_SESSION['USER_TYPE'] = $uidExists['user_type'] ;
    $_SESSION['USER_BRUNCH'] = $uidExists['brunch_id'] ;
    $_SESSION['USER_EMPLOYEE_ID'] = $uidExists['employee_id'] ;

   
$ck_user = $conn->prepare("SELECT *  FROM `webpush_member` WHERE `member_id` = '".$uidExists['id']."'  ");
$ck_user->execute();
if($ck_user->rowCount() > 0) {

    $up_qry = $conn->prepare("UPDATE `webpush_member` 
		SET 
			`flag` = '1', 
			`last_date` = '".date("Y-m-d")."', 
			`last_time` = '".date("h:i:s a")."',
			`ip` = '".$_SERVER['REMOTE_ADDR']."'
		WHERE `member_id` = '".$uidExists['id']."' ");
		$up_qry->execute();


}else{


   ## MEMBER STATUS
   $query = $conn->exec("INSERT INTO `webpush_member` 
   (
       `member_id`, 
       `flag`, 
       `last_date`, 
       `last_time`,
       `ip`
   )
   VALUES
   (
       '".$_SESSION['NEWERP_SESS_MEMBER_ID']."', 
       '1', 
       '".date("Y-m-d")."', 
       '".date("h:i:s a")."',
       '".$_SERVER['REMOTE_ADDR']."'
   )");

}
     

    header("location: home/Dashboard");
    exit();

}
    }
