<?php
session_start();

include_once("db_connection.php");

$dbConnection = new mysqli($servername, $username, $password, $dbname);


if ($dbConnection->connect_error) {
    die("Connection failed: " . $dbConnection->connect_error);
}

$sql = "SELECT * FROM Contact";
$result = $dbConnection->query($sql);

if ($result === false) {
    die("Error executing query: " . $dbConnection->error);
}

$contacts = array();

while ($row = $result->fetch_assoc()) {
    $contacts[] = $row;
}

echo json_encode($contacts);
$dbConnection->close();
?>
