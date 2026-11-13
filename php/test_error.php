<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing PHP<br>";

// Test database connection
require_once 'db_connect.php';
echo "Database connected<br>";

// Test POST data simulation
$test_data = [
    'services' => ['Hair Treatments'],
    'beautician' => 'Sara Elise',
    'appt_date' => '2026-06-20',
    'appt_time' => '09:00',
    'cust_name' => 'Test User',
    'cust_phone' => '0712345678',
    'cust_email' => 'test@email.com',
    'cust_notes' => 'Test notes'
];

echo "Test data prepared<br>";
echo "If you see this, PHP is working!";
?>