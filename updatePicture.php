<?php
include_once("db_connection.php");
$dbConnection = new mysqli($servername, $username, $password, $dbname);
session_start();
$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $storedImage = $_POST["storedImage"];
    $uploadedImage = $_FILES["profileImage"]["name"];
    
    $tempName = $_FILES["profileImage"]["tmp_name"];
 

    if (is_uploaded_file($tempName)) 
    {
        $imageType = strtolower(pathinfo($uploadedImage, PATHINFO_EXTENSION));
        $unique_name = date('Ymd_His') . '_' . uniqid();
        $newName = $unique_name . "." . $imageType;
        $target_dir = "./images/";
        $storage = $target_dir.$newName;
        

        $sql = "SELECT profile FROM Contact WHERE profile = '$storedImage'";
        $result = $dbConnection->query($sql);

        if ($result === false) 
        {
            die("Error executing query: " . $dbConnection->error);
        } 
        else 
        {

            if ($result->num_rows > 0) 
            {
                
                
                if (move_uploaded_file($tempName, $storage) && unlink($storedImage)) 
                {

                    $updateSql = "UPDATE Contact SET profile = '$storage' WHERE profile = '$storedImage'";
                    
                    if ($dbConnection->query($updateSql) === TRUE) 
                    {
                        $response['status'] = 'success';
                    } 
                    else 
                    {
                        $response['status'] = 'error';
                        $response['message'] = 'Error updating database: ' . $dbConnection->error;
                    }
                } 
                else 
                {
                    $response['status'] = 'error';
                    $response['message'] = 'Error moving uploaded file or deleting old file.';
                }
            } 
            else 
            {
                $response['status'] = 'error';
                $response['message'] = 'Stored image not found in database.';
            }
        }
    } 
    else 
    {
        $response['status'] = 'error';
        $response['message'] = 'File not uploaded correctly.';
    }

    echo json_encode($response);
}
?>
