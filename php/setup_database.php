<?php
$servername = "localhost";
$username = "root";
$password = "";

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

echo "<h2 style='color:#8b5a2b; font-family:Arial;'>✨ GlowUp Ladies Database Setup ✨</h2>";

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS glowup_salon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($conn->query($sql) === TRUE) {
    echo "<p style='color:green;'>✓ Database 'glowup_salon' created successfully</p>";
} else {
    echo "<p style='color:red;'>✗ Error: " . $conn->error . "</p>";
}

$conn->select_db("glowup_salon");

// Staff table - FIXED typo!
$staff_table = "CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    role VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    qualifications TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    experience INT DEFAULT 0,
    specialization TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    rating DECIMAL(3,2) DEFAULT 5.0,
    review_count INT DEFAULT 0,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($staff_table) === TRUE) {
    echo "<p style='color:green;'>✓ Staff table created</p>";
} else {
    echo "<p style='color:red;'>✗ Error creating staff table: " . $conn->error . "</p>";
}

// Bookings table
$bookings_table = "CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_ref VARCHAR(20) UNIQUE NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    customer_email VARCHAR(100),
    service_names TEXT,
    staff_name VARCHAR(100),
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    notes TEXT,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($bookings_table) === TRUE) {
    echo "<p style='color:green;'>✓ Bookings table created</p>";
}

// Contact messages table
$messages_table = "CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100),
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(100),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($messages_table) === TRUE) {
    echo "<p style='color:green;'>✓ Contact messages table created</p>";
}

// Services table
$services_table = "CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    duration VARCHAR(50),
    price VARCHAR(50),
    icon VARCHAR(50),
    image_url VARCHAR(255)
)";
if ($conn->query($services_table) === TRUE) {
    echo "<p style='color:green;'>✓ Services table created</p>";
}

// Clear existing staff data
$conn->query("TRUNCATE TABLE staff");

// ============================================
// STAFF LIST - USING YOUR EXACT IMAGE FILENAMES
// ============================================
$sample_staff = [
    ["Menali Perera", "Senior Hair Artist", "Certified Master Stylist • L'Oréal Professionnel Graduate", 8, "Bridal Styling, Hair Coloring, Balayage", 4.9, 128, "menali.jpg"],
    ["Amelia Fernando", "Esthetician & Makeup Artist", "CIDESCO Diploma • Advanced Skincare Specialist", 6, "Anti-aging Facials, Bridal Makeup, Lash Lifting", 4.8, 95, "amelia.jpg"],
    ["Sara Elise", "Nail & Beauty Master", "CND Certified • Shellac Expert", 5, "Manicure, Pedicure, Gel Extensions", 4.9, 156, "Sara.jpg"],
    ["Vinaldi Perera", "Waxing & Body Specialist", "ITEC Level 4 • Sugaring Expert", 10, "Brazilian Waxing, Body Polishing, Sugaring", 4.7, 203, "Vinaldi.jpg"],
    ["A.J. Imana", "Bridal & Glam Specialist", "Vidal Sassoon Trained • Airbrush Makeup Pro", 9, "Updos, Airbrush Makeup, Event Styling", 5.0, 312, "Imana.jpg"],
    ["Nafla Rose", "Senior Laser & Skincare", "Medical Aesthetics Diploma • Laser Safety Certified", 7, "Laser Hair Removal, Chemical Peels, LED Therapy", 4.9, 87, "Nafta.jpg"]
];

$stmt = $conn->prepare("INSERT INTO staff (name, role, qualifications, experience, specialization, rating, review_count, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
foreach ($sample_staff as $staff) {
    $stmt->bind_param("sssisdds", $staff[0], $staff[1], $staff[2], $staff[3], $staff[4], $staff[5], $staff[6], $staff[7]);
    $stmt->execute();
}
$stmt->close();
echo "<p style='color:green;'>✓ New staff inserted (6 members)</p>";

// Clear existing services and re-insert
$conn->query("TRUNCATE TABLE services");

$services_data = [
    ["Hair Cuts", "From trims to stylish cuts for a fresh new look.", "30-45 mins", "LKR 1,000", "fa-cut", "haircut.jpg"],
    ["Hair Coloring", "Full color, highlights, ombre & more.", "60-120 mins", "LKR 3,000+", "fa-palette", "Haircolor.webp"],
    ["Styling & Blow Dry", "Perfect styling for any occasion.", "30-60 mins", "LKR 1,500", "fa-wind", "dry.jpg"],
    ["Facial", "Rejuvenate and refresh your glowing skin.", "45-60 mins", "LKR 2,500", "fa-smile", "makeup.jpeg"],
    ["Manicure", "Caring treatments for beautiful hands.", "30-45 mins", "LKR 1,200", "fa-hand-peace", "manicure.webp"],
    ["Pedicure", "Relaxing foot care and nail treatments.", "45-60 mins", "LKR 1,500", "fa-foot", "pedicure.jpg"],
    ["Waxing", "Smooth skin with professional care.", "15-60 mins", "LKR 800+", "fa-feather", "waxing.jpg"],
    ["Makeup", "Day, party and bridal makeup services.", "60-90 mins", "LKR 4,000+", "fa-magic", "makeup.avif"],
    ["Hair Treatments", "Nourish and repair for healthy hair.", "30-60 mins", "LKR 2,000+", "fa-leaf", "treat.jpg"]
];

$svc_stmt = $conn->prepare("INSERT INTO services (name, description, duration, price, icon, image_url) VALUES (?, ?, ?, ?, ?, ?)");
foreach ($services_data as $service) {
    $svc_stmt->bind_param("ssssss", $service[0], $service[1], $service[2], $service[3], $service[4], $service[5]);
    $svc_stmt->execute();
}
$svc_stmt->close();
echo "<p style='color:green;'>✓ Services inserted (9 services)</p>";

$conn->close();
echo "<hr><h3>✅ Setup Complete!</h3>";
echo "<ul>";
echo "<li><a href='../index.html'>🏠 Go to Homepage</a></li>";
echo "<li><a href='admin/dashboard.php'>📊 Go to Admin Dashboard</a> (admin / glowup123)</li>";
echo "</ul>";
?>