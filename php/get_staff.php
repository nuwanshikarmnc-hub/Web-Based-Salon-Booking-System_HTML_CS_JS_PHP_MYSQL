<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once 'db_connect.php';

// Set charset to UTF-8 for the connection
$conn->set_charset("utf8");

$sql = "SELECT id, name, role, qualifications, experience, specialization, rating, review_count, image_url FROM staff ORDER BY id";
$result = $conn->query($sql);

$staff = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Convert special characters to UTF-8
        $name = utf8_encode($row['name']);
        $role = utf8_encode($row['role']);
        $qualifications = utf8_encode($row['qualifications']);
        $specialization = utf8_encode($row['specialization']);
        
        $staff[] = [
            'id' => $row['id'],
            'name' => $name,
            'role' => $role,
            'qualifications' => $qualifications,
            'experience' => (int)$row['experience'],
            'specializations' => array_map('utf8_encode', explode(', ', $specialization)),
            'rating' => (float)$row['rating'],
            'reviewCount' => (int)$row['review_count'],
            'imageFile' => $row['image_url'],
            'avatarInitials' => mb_substr($name, 0, 1) . mb_substr(explode(' ', $name)[1] ?? '', 0, 1)
        ];
    }
}

// Make sure JSON is valid
$json = json_encode($staff, JSON_UNESCAPED_UNICODE);
if ($json === false) {
    echo json_encode(['error' => 'JSON encoding failed']);
} else {
    echo $json;
}

$conn->close();
?>