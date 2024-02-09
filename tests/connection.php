<?php
$servername = "db:3306";
$username = "wordpress";
$password = "wordpress";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check server status
if ($result = $conn->query("SHOW STATUS LIKE 'Uptime';")) {
    $row = $result->fetch_assoc();
    echo "Server Uptime: " . $row['Value'] . " seconds\n";
    $result->free();
} else {
    echo "Unable to check server status: " . $conn->error;
}

$conn->close();
?>