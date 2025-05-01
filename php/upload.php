<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'inventory';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $qty = (int)$_POST['quantity'];

    $uploadFolder = "uploads/";
    $serverPath = "../" . $uploadFolder;
    $fileName = basename($_FILES["image"]["name"]);
    $targetFilePath = $serverPath . $fileName;
    $imageURL = "http://localhost/dashboard/Inventory-System/" . $uploadFolder . $fileName;

    if (!file_exists($serverPath)) {
        mkdir($serverPath, 0755, true);
    }

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
        $stmt = $conn->prepare("INSERT INTO inventory (name, quantity, image) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $name, $qty, $imageURL);

        if ($stmt->execute()) {
            echo "Item added successfully.";
        } else {
            echo "Database insert failed: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Image upload failed.";
    }
}

$conn->close();
?>
