<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    if (empty($first_name) || empty($email) || empty($message)) {
        $response['message'] = 'Please fill in name, email, and message.';
        echo json_encode($response);
        exit;
    }
    
    $stmt = $conn->prepare("INSERT INTO contact_messages (first_name, last_name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $first_name, $last_name, $email, $phone, $subject, $message);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Message sent successfully!';
    } else {
        $response['message'] = 'Database error: ' . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
echo json_encode($response);
?>