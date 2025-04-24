<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $qty = $_POST['quantity'];

    $targetDir = "uploads/";
    $fileName = basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . $fileName;

    // Upload the file
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
        // Save to DB (assuming you have a column for image path)
        // Example: INSERT INTO items (name, qty, image_path) VALUES ('$name', $qty, '$targetFilePath');
        echo "Item added successfully.";
    } else {
        echo "Image upload failed.";
    }
}
?>
