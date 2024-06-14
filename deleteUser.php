<?php
include_once("db_connection.php");
$dbConnection = new mysqli($servername, $username, $password, $dbname);
session_start();
$response =array();
if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $email = $_POST['email'];

    $sql = "DELETE FROM Contact WHERE email='$email'";    
    //$userDlt = "DELETE FROM user WHERE email='$email'";    
    $profilesql = "select Profile from Contact WHERE email='$email'";    
    $result = mysqli_query($dbConnection, $profilesql);
    //  && $dbConnection->query($userDlt)
    if ($result && $dbConnection->query($sql) === TRUE) 
    {
        $row = mysqli_fetch_assoc($result);
        $profile = $row['Profile'];
        if (unlink($profile)) 
        {
            $response['status'] = 'success';
        } 
        else 
        {
            $response['status'] = 'fail';
        }
       
        
    } 
    else 
    {
        echo "Error: " . mysqli_error($dbConnection);
    }
}

echo json_encode($response);

?>