<?php 

if(isset($_POST['submit'])){

    $username = $_POST['user_name'];
    $password = $_POST['pwd'];


    
    include('connect_me.php');
    include('functions.inc.php');

    $conn = Database::getInstance();

    
    if(emptyInputLogin($username,$password) !== false){
        header("location: login/emptyinput");
        exit();
    }

    loginUser($conn,$username,$password);




}else{
    
    header("location: login/New");
    exit();

}