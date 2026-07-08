<?php
include_once("auth.php");
include_once("clean.php");
include('connect_me.php');


if(!empty($_FILES["fileToUpload"])){

	

$target_dir = "upload/".$_POST['file_section'].'/';

$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

$uploadOk = 1;

$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));



// Check if image file is a actual image or fake image

if(isset($_POST["submit"])) {

    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);

    if($check !== false) {

        echo "File is an image - " . $check["mime"] . ".\n";

        $uploadOk = 1;

    } else {

        echo "File is not an image.\n";

        $uploadOk = 0;

    }

}



// Check if file already exists

if (file_exists($target_file)) {

    echo "Sorry, file already exists.\n";

    $uploadOk = 0;

}



// Check file size

if ($_FILES["fileToUpload"]["size"] > 200000200000200000) {

    echo "Sorry, your file is too large. Must be upload between 1T.\n";

    $uploadOk = 0;

}



// Allow certain file formats

if( $imageFileType != "jpg") {

    echo "Enter jpg png jpeg file   .\n";

    $uploadOk = 0;

}



// Check if $uploadOk is set to 0 by an error

if ($uploadOk == 0) {

    echo "Sorry, your file was not uploaded.\n";

// if everything is ok, try to upload file

} else {

## create new image name



if($_POST['file_section'] == 'Company_Logo' ){

    $temp = explode(".", $_FILES["fileToUpload"]["name"]);
    $newfilename = ($_POST['file_section']). '(' .(time()). ').'. end($temp);

    $check_image = $conn_me->prepare("SELECT `logo` FROM `setup_company` ");
    $check_image->execute();
    $fetch_check = $check_image->fetch(PDO::FETCH_ASSOC);



    $update_qry = $conn_me->prepare("UPDATE `setup_company` SET `logo` = '".$newfilename."'  ");
    $update_qry->execute();

    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],$target_dir.$newfilename)) {
    echo "Uplaod Success";
    } else {
    echo "Sorry, there was an error uploading your file.\n";
    }
       
}else if($_POST['file_section'] == 'Invoice_Header' ){


    $temp = explode(".", $_FILES["fileToUpload"]["name"]);
    $newfilename = ($_POST['file_section']). '(' .(time()). ').'. end($temp);

    $check_image = $conn_me->prepare("SELECT `invoice_header` FROM `setup_company` ");
    $check_image->execute();
    $fetch_check = $check_image->fetch(PDO::FETCH_ASSOC);



    $update_qry = $conn_me->prepare("UPDATE `setup_company` SET `invoice_header` = '".$newfilename."'  ");
    $update_qry->execute();

    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],$target_dir.$newfilename)) {
    echo "Uplaod Success";
    } else {
    echo "Sorry, there was an error uploading your file.\n";
    }

}else if($_POST['file_section'] == 'Invoice_Footer' ){




}else{


}




}
## end
}else{

	echo "Sorry, you do not select any file to upload.\n";

}	




?>