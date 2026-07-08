<?php

$con = mysqli_connect('localhost', 'osudlagb_product', 'rYOA}.*W^K3x', 'osudlagb_rc_center');
$con->set_charset('utf8mb4');

if(mysqli_connect_errno()){
    echo "MySql Connection Error<br>";
    die;
}

