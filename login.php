<?php 
session_start();
include_once("db_connection.php");
$dbConnection = new mysqli($servername, $username, $password, $dbname);
$response = array();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = $_POST['email'];
    $userpassword = $_POST['Pass'];
    $rememberMe = $_POST['rememberMe'];
    if($dbConnection->connect_error)
    {
        die("Connection failed: " .
        $dbConnection->connect_error);
    }

    $dataFromemail = "SELECT email,password from user where email=?";
    // $alreadyEmail = "SELECT COUNT(*) as count from Contact where email = '$email'";
    // $result = mysqli_query($dbConnection,$alreadyEmail);
    // $row = mysqli_fetch_assoc($result);

    $stmt = $dbConnection->prepare($dataFromemail);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0 ) 
    {
        $stmt->bind_result($emailResult, $passwordResult);
        $stmt->fetch();
        if (password_verify($userpassword, $passwordResult)) 
        {
            setCookies($rememberMe,$email);
            $response['status'] = 'success';
            $response['message'] = 'Logged in';
            // $session_id=bin2hex(random_bytes(16));
            // $_SESSION['session_id'] = $session_id;
            //$_SESSION['session_email'] = $email;
            //setcookie("UserEmail", $email, time() + 86400, "/");
            // $sql = "INSERT INTO session (userEmail, sessionID) VALUES ('$email', '$session_id')";
            // $dbConnection->query($sql);
            
            // if($row['count'] > 0)
            // {
            //     setCookies($rememberMe,$email);
            //     $response['status'] = 'success';
            //     $response['message'] = 'Logged in';
                
            // }
            // else
            // {
            //     setCookies($rememberMe,$email) ;
            //     $response['status'] = 'notExist';
            //     $response['message'] = 'Logged in';
            // }
        } 
        else 
        {
            $response['status'] = 'fail';
            $response['message'] = 'Password in correct';
           
        }
    } 
    else
    {
        $response['status'] = 'notFound';
        $response['message'] = 'User With this email not found, Kindly Sign up';
    }
    $stmt->close();
    $dbConnection->close();

    echo json_encode($response);
}

function setCookies($rememberMe,$email)
{
    if($rememberMe == 'true')
    {
        setcookie("UserEmail", $email, time() + 86400, "/");
    }
    else if($rememberMe == 'false')
    {
        setcookie("UserEmail", $email, time() + 3600, "/");
    }
            
}
?>