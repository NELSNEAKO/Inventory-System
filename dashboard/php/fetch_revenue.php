<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$database = "inventory";
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) { die(json_encode(["error" => "DB error"])); }

$labels = [];
$revenue = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date("Y-m-d", strtotime("-$i days"));
    $label = date("D", strtotime($date));
    $labels[] = $label;

    $sql = "SELECT SUM(price * qty) as total FROM sale WHERE date = '$date'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $revenue[] = $row['total'] ?? 0;
}

echo json_encode(["labels" => $labels, "revenue" => $revenue]);
$conn->close();
?>
