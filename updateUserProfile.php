<?php
include_once("db_connection.php");
$dbConnection = new mysqli($servername, $username, $password, $dbname);
session_start();
$response =array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = $_POST['email'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $phone = $_POST['phone'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $Age = calAge($dob);

    $sql = "UPDATE Contact SET first_name='$firstName',last_name='$lastName',address='$address',date_of_birth = '$dob', telephone = '$phone' ,age='$Age' WHERE email='$email'";
    if ($dbConnection->query($sql) === TRUE) 
    {
        $response['status']='success';
    } 
    else 
    {
        echo "Error updating record: " . $dbConnection->error;
    }
    $response['status']='success';
    echo json_encode($response);
}

function calAge($dob) 
{
    $dobDate = new DateTime($dob);
    $currentDate = new DateTime();
    $age = $currentDate->format('Y') - $dobDate->format('Y');
    
    if ($currentDate->format('m') < $dobDate->format('m') || ($currentDate->format('m') == $dobDate->format('m') && $currentDate->format('d') < $dobDate->format('d'))) {
        $age--;
    }
    return $age;
}

?>