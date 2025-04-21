<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$database = "inventory";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed."]));
}

$data = [];

// Last 7 days
$start = date("Y-m-d", strtotime("-6 days"));
$end = date("Y-m-d");

$sql = "SELECT DATE(date) as sale_day, HOUR(time) as sale_hour, COUNT(*) as count 
        FROM sale 
        WHERE date BETWEEN '$start' AND '$end'
        GROUP BY sale_day, sale_hour";

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $dayShort = date("D", strtotime($row['sale_day'])); // eg. Mon, Tue
    $hour = (int)$row['sale_hour'];
    $count = (int)$row['count'];

    if (!isset($data[$dayShort])) {
        $data[$dayShort] = array_fill(0, 24, 0);
    }
    $data[$dayShort][$hour] = $count;
}

echo json_encode($data);
$conn->close();
?>
