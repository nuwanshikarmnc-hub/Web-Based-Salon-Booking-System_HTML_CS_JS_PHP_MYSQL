<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'db_connect.php';

$sql = "SELECT id, name, description, duration, price, icon FROM services ORDER BY id";
$result = $conn->query($sql);

$services = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}

echo json_encode($services);
$conn->close();
?>