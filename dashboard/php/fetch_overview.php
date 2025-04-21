<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$database = "inventory";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed."]));
}

function getRange($weekAgo = 0) {
    $start = new DateTime("last Sunday -$weekAgo week");
    $end = new DateTime("this Sunday -$weekAgo week");
    return [$start->format("Y-m-d"), $end->format("Y-m-d")];
}

function fetchData($conn, $range) {
    [$start, $end] = $range;
    $sql = "SELECT SUM(price * qty) as revenue, SUM(qty) as sold FROM sale WHERE date BETWEEN '$start' AND '$end'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $revenue = $row['revenue'] ?? 0;
    $sold = $row['sold'] ?? 0;
    $profit = $revenue * 0.2; // assuming 20% profit margin
    return [$revenue, $profit, $sold];
}

list($rWeek, $pWeek, $sWeek) = fetchData($conn, getRange(0));
list($rLast, $pLast, $sLast) = fetchData($conn, getRange(1));

$growth = ($rLast > 0) ? round((($rWeek - $rLast) / $rLast) * 100, 2) : 0;

echo json_encode([
    "revenue_week" => number_format($rWeek, 2),
    "revenue_last" => number_format($rLast, 2),
    "profit_week" => number_format($pWeek, 2),
    "profit_last" => number_format($pLast, 2),
    "sold_week" => $sWeek,
    "sold_last" => $sLast,
    "growth_percent" => $growth
]);
$conn->close();
?>
