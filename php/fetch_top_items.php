<?php
include('connection.php');


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
