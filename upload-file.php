<?php 
include('clean.php');
include('connect_me.php');
$conn_me = Database::getInstance();

if (isset($_FILES['file'])) {
    // Get the file details
    $file = $_FILES['file'];
    $size = $file['size'];
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $mimetype = $file['type'];
    $folder_path = "upload/employee_photo";
    $employee_id= clean($_POST['employee_id']);
    $photo = $employee_id .'_pic.' .$extension;

    
    $query = $conn_me->prepare("UPDATE `setup_employee`  SET `photo` = '".$photo."'  WHERE `id` = '".$employee_id."'  ");
    $query->execute();

    // Move the file to the desired folder
    move_uploaded_file($file['tmp_name'], "$folder_path/" . $photo);

    // Execute the INSERT statement
    if ($query->execute()) {
        // Print a success message if the file was successfully inserted
        echo ' uploaded successfully';
    } else {
        // Print an error message if the file insert failed
        echo 'Error uploading and moving file';
    }
}