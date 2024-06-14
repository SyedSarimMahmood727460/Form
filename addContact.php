<?php
include_once("db_connection.php");
session_start();
$response = array();
$dbConnection = new mysqli($servername, $username, $password, $dbname);
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['contactemail'];
    $first_name=$_POST['fname'];
    $last_name=$_POST['lname'];
    $address = $_POST['address'];
    $phone = $_POST['phone_number'];
    $file = $_FILES["profile"]["name"];
    $loginUseremail = $_COOKIE['UserEmail'];
    
    $imageType = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $unique_name = date('Ymd_His') . '_' . uniqid();
    $tempName = $_FILES["profile"]["tmp_name"];
    $newName = $unique_name . "." . $imageType;
    
    $target_dir = "./images/";
    $storage = $target_dir.$newName;


    if (move_uploaded_file($tempName, $storage)) 
    {
        $alreadyEmailQuery = "SELECT COUNT(*) as count FROM Contact WHERE email = ? and userEmail=?";
        $stmt = $dbConnection->prepare($alreadyEmailQuery);
        $stmt->bind_param("ss", $email,$loginUseremail);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    
        if ($row['count'] > 0) {
            $response['status'] = 'Found';
            $response['message'] = 'Contact with this email already exists';
        } 
        else 
        {
            $sql = "INSERT INTO Contact (first_name, last_name, email, address, telephone, profile,userEmail) VALUES (?,?,?,?,?,?,?)";
            $stmt = $dbConnection->prepare($sql);
            $stmt->bind_param("sssssss", $first_name, $last_name, $email, $address, $phone, $storage,$loginUseremail);
            $stmt->execute();
            $response['status'] = 'success';
            $response['message'] = 'Information Saved';
        }
    }
    
    else 
    {
        $response['status'] = 'fail';
        $response['message'] = 'Failed to store file';
    }
   
    echo json_encode($response);
}

    

?>
