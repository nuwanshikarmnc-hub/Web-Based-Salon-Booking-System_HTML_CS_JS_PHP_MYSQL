<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    require_once 'db_connect.php';
    
    $response = ['success' => false, 'message' => '', 'ref' => '', 'booking_details' => []];
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $response['message'] = 'Please use POST method.';
        echo json_encode($response);
        exit;
    }
    
    // Get data
    $services = isset($_POST['services']) ? implode(', ', $_POST['services']) : '';
    $beautician = isset($_POST['beautician']) ? trim($_POST['beautician']) : '';
    $appt_date = isset($_POST['appt_date']) ? $_POST['appt_date'] : '';
    $appt_time = isset($_POST['appt_time']) ? $_POST['appt_time'] : '';
    $cust_name = isset($_POST['cust_name']) ? trim($_POST['cust_name']) : '';
    $cust_phone = isset($_POST['cust_phone']) ? trim($_POST['cust_phone']) : '';
    $cust_email = isset($_POST['cust_email']) ? trim($_POST['cust_email']) : '';
    $cust_notes = isset($_POST['cust_notes']) ? trim($_POST['cust_notes']) : '';
    
    // Validate required fields
    if (empty($services) || empty($beautician) || empty($cust_name) || empty($cust_phone) || empty($appt_date) || empty($appt_time)) {
        $response['message'] = 'Please fill in all required fields.';
        echo json_encode($response);
        exit;
    }
    
    // Validate email (if provided, must be valid)
    if (!empty($cust_email) && !filter_var($cust_email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Please enter a valid email address (e.g., name@domain.com).';
        echo json_encode($response);
        exit;
    }
    
    // Check for conflicts
    $check_sql = "SELECT id FROM bookings WHERE staff_name = ? AND appointment_date = ? AND appointment_time = ? AND status != 'cancelled'";
    $check_stmt = $conn->prepare($check_sql);
    
    if (!$check_stmt) {
        $response['message'] = 'Database error: ' . $conn->error;
        echo json_encode($response);
        exit;
    }
    
    $check_stmt->bind_param("sss", $beautician, $appt_date, $appt_time);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $response['message'] = "❌ $beautician is already booked at " . date('h:i A', strtotime($appt_time)) . " on " . date('l, F j, Y', strtotime($appt_date)) . ". Please select a different time.";
        $response['conflict'] = true;
        $check_stmt->close();
        echo json_encode($response);
        exit;
    }
    $check_stmt->close();
    
    // Generate booking reference
    $booking_ref = 'GL-' . strtoupper(substr(uniqid(), -6));
    
    // Insert booking
    $insert_sql = "INSERT INTO bookings (booking_ref, service_names, staff_name, appointment_date, appointment_time, customer_name, customer_phone, customer_email, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'confirmed')";
    $insert_stmt = $conn->prepare($insert_sql);
    
    if (!$insert_stmt) {
        $response['message'] = 'Database error: ' . $conn->error;
        echo json_encode($response);
        exit;
    }
    
    $insert_stmt->bind_param("sssssssss", $booking_ref, $services, $beautician, $appt_date, $appt_time, $cust_name, $cust_phone, $cust_email, $cust_notes);
    
    if ($insert_stmt->execute()) {
        $response['success'] = true;
        $response['ref'] = $booking_ref;
        $response['message'] = 'Booking confirmed!';
        $response['booking_details'] = [
            'name' => $cust_name,
            'staff' => $beautician,
            'date' => date('l, F j, Y', strtotime($appt_date)),
            'time' => date('h:i A', strtotime($appt_time)),
            'services' => $services,
            'ref' => $booking_ref,
            'phone' => $cust_phone,
            'email' => $cust_email ?: 'Not provided'
        ];
    } else {
        $response['message'] = 'Database error: ' . $insert_stmt->error;
    }
    
    $insert_stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    $response['message'] = 'Server error: ' . $e->getMessage();
}

echo json_encode($response);
?>