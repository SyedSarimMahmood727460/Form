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
    $address = $_POST['address'];
    $updatedEmail = $_POST['updatedEmail'];
    $loginUseremail = $_COOKIE['UserEmail'];
  

    if($updatedEmail != $email)
    {
        $alreadyEmail = "SELECT COUNT(*) as count from Contact where email = '$updatedEmail' and userEmail='$loginUseremail'";
        $result = mysqli_query($dbConnection,$alreadyEmail);
        $row = mysqli_fetch_assoc($result);

        if($row['count'] > 0)
        {
            $response['status'] = 'exist';
            $response['message'] = 'Contact with this email already exists';
        }
        else
        {
            $sql = "UPDATE Contact SET first_name='$firstName',last_name='$lastName',email ='$updatedEmail',address='$address',telephone = '$phone' WHERE email='$email' and userEmail='$loginUseremail'";
            if ($dbConnection->query($sql) === TRUE) 
            {
                $response['status'] = 'updated';
            } 
            else 
            {
                echo "Error updating record: " . $dbConnection->error;
            }
        
        }
    }
    else
    {
        $sql = "UPDATE Contact SET first_name='$firstName',last_name='$lastName',email ='$updatedEmail',address='$address',telephone = '$phone'  WHERE email='$email' and userEmail='$loginUseremail'";
        if ($dbConnection->query($sql) === TRUE) 
        {
            $response['status'] = 'updated';
        } 
        else 
        {
            echo "Error updating record: " . $dbConnection->error;
        }
    }
    
    
}
echo json_encode($response);


?>