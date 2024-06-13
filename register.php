<?php
include_once("db_connection.php");
session_start();
$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['useremail'];
    $userpassword = $_POST['Password'];

    $hashedPassword = password_hash($userpassword, PASSWORD_BCRYPT);

    $dbConnection = new mysqli($servername, $username, $password, $dbname);

    if ($dbConnection->connect_error) {
        $response['status'] = 'error';
        $response['message'] = 'Database connection failed';
    } else {
        $alreadyEmailQuery = "SELECT COUNT(*) as count FROM user WHERE email = ?";
        $stmt = $dbConnection->prepare($alreadyEmailQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row['count'] > 0) {
            $response['status'] = 'fail';
            $response['message'] = 'User with this email already exists';
        } else {
            $insertQuery = "INSERT INTO user (email, password) VALUES (?, ?)";
            $stmt = $dbConnection->prepare($insertQuery);
            $stmt->bind_param("ss", $email, $hashedPassword);
            if ($stmt->execute()) {
                // $_SESSION['session_email'] = $email;
                setcookie("UserEmail", $email, time() + 3600, "/");
                $response['status'] = 'success';
                $response['message'] = 'New user created successfully';
            } 
            else {
                $response['status'] = 'error';
                $response['message'] = 'Error creating user';
            }
            $stmt->close();
        }
        $dbConnection->close();
    }
}

echo json_encode($response);
?>
