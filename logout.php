<?php
session_start();    
$response = array(); // Initialize the response array

setcookie("UserEmail", "", time() - 3600, "/");
    session_unset();
    session_destroy();

    $response['status'] = 'success'; 
    $response['message'] = 'User logged out'; 

    echo json_encode($response);
?>
