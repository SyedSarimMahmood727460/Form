<?php
include_once("db_connection.php");
session_start();
$response = array();
$dbConnection = new mysqli($servername, $username, $password, $dbname);
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $first_name=$_POST['fname'];
    $last=$_POST['lname'];
    $address = $_POST['address'];
    $date = $_POST['date_of_birth'];
    $phone = $_POST['phone_number'];
    $age = $_POST['age'];
    $file = $_FILES["profile"]["name"];
    
    $imageType = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $unique_name = date('Ymd_His') . '_' . uniqid();
    $tempName = $_FILES["profile"]["tmp_name"];
    $newName = $unique_name . "." . $imageType;
    
    $target_dir = "./images/";
    $storage = $target_dir.$newName;


    if (move_uploaded_file($tempName, $storage)) 
    {
        $email = $_COOKIE['UserEmail'];
        
        $alreadyEmailQuery = "SELECT COUNT(*) as count FROM Contact WHERE email = ?";
        $stmt = $dbConnection->prepare($alreadyEmailQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    
        if ($row['count'] > 0) {
            $response['status'] = 'Found';
            $response['message'] = 'User with this email already exists';
        } 
        else 
        {
            $sql = "INSERT INTO Contact (first_name, last_name, email, address, date_of_birth, telephone, age, profile) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $dbConnection->prepare($sql);
            $stmt->bind_param("ssssssis", $first_name, $last, $email, $address, $date, $phone, $age, $storage);
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
