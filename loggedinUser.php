<?php
session_start(); 
include_once("db_connection.php");
$dbConnection = new mysqli($servername, $username, $password, $dbname);
$response = array();
$email = $_COOKIE['UserEmail'];
$alreadyEmailQuery = "SELECT COUNT(*) as count FROM Contact WHERE email = ?";
$stmt = $dbConnection->prepare($alreadyEmailQuery);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
    
if ($row['count'] > 0) 
{
    $response['status'] = 'Found';
    $response['message'] = 'User with this email already exists';
    echo json_encode($response);
}
else
{
    $response['status'] = 'notFound';
    $response['message'] = 'Data to be add';
    echo json_encode($response);
}

?>