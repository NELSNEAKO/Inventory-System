<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$database = "inventory";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed."]));
}

// echo "{
//     \"status\": \"success\",
//     \"message\": \"Connected to the database.\"
// }";
?>