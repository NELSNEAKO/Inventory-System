<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$database = "inventory";
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) { die(json_encode(["error" => "DB error"])); }

// Assuming item name is stored in an `item` column
$sql = "SELECT item AS name, SUM(qty) AS total_qty, SUM(price * qty) AS total_revenue
        FROM sale
        GROUP BY item
        ORDER BY total_qty DESC
        LIMIT 5";

$result = $conn->query($sql);
$items = [];

while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode($items);
$conn->close();
?>
