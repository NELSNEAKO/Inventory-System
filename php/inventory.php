<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$database = "inventory";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the POST data
$name = $_POST['name'];
$quantity = $_POST['quantity'];
$image = $_POST['image'];

// Prepare and execute the insert query
$sql = "INSERT INTO inventory (name, quantity, image) VALUES ('$name', $quantity, '$image')";
if ($conn->query($sql) === TRUE) {
    echo "New item added successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
