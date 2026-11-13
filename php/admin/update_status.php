<?php
require_once '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && isset($_POST['status'])) {
        $id = (int)$_POST['id'];
        $status = $_POST['status'];
        
        $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $stmt->close();
    }
    
    if (isset($_POST['mark_read']) && isset($_POST['msg_id'])) {
        $msg_id = (int)$_POST['msg_id'];
        $stmt = $conn->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
        $stmt->bind_param("i", $msg_id);
        $stmt->execute();
        $stmt->close();
    }
}

$conn->close();
header('Location: dashboard.php');
?>