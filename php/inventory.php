<?php
// Database configuration
$host = '127.0.0.1'; // Database host
$username = 'root';   // Database username
$password = '';       // Database password
$database = 'inventory'; // Database name

// Create a connection to the database
$conn = new mysqli($host, $username, $password, $database);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch inventory items
$sql = "SELECT * FROM inventory";
$result = $conn->query($sql);

// Check if there are any results
if ($result->num_rows > 0) {
    // Create an array to store the items
    $items = [];
    
    // Fetch the data and add it to the items array
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    
    // Output the items as a JSON response
    echo json_encode($items);
} else {
    echo json_encode([]); // Return an empty array if no items are found
}

// Close the database connection
$conn->close();
?>
