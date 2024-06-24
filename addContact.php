<?php
include_once("db_connection.php");
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
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
            mailSend($first_name,$last_name,$email,$phone,$address,$storage);
        }
    }
    
    else 
    {
        $response['status'] = 'fail';
        $response['message'] = 'Failed to store file';
    }
   
    echo json_encode($response);
}

function mailSend($first_name,$last_name,$email,$phone,$address,$storage)
{
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings                   //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'sarimmahmood78@gmail.com';                     //SMTP username
        $mail->Password   = 'voxvknjkdtfxoipm';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //ENCRYPTION_SMTPS-465 Enable implicit TLS encryption
        $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('sarimmahmood78@gmail.com', 'Admin');
        $mail->addAddress('syedsarimmahmood9@gmail.com', 'Username');     //Add a recipient

        //Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        $mail->addAttachment($storage, 'profilePicture');    //Optional name

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'New Contact Added';
        $mail->Body = '<h3>New Contact Details</h3>
        <p>First Name: ' . $first_name . '</p>
        <p>Last Name: ' . $last_name . '</p>
        <p>Email: ' . $email . '</p>
        <p>Phone Number: ' . $phone . '</p>
        <p>Address: ' . $address . '</p>';

        $mail->send();
    } 
    catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

    

?>
