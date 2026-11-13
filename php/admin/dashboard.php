<?php
session_start();
// Simple password protection
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="Admin Access"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Access denied';
    exit;
} elseif ($_SERVER['PHP_AUTH_USER'] != 'admin' || $_SERVER['PHP_AUTH_PW'] != 'glowup123') {
    header('WWW-Authenticate: Basic realm="Admin Access"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Invalid credentials';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - GlowUp Ladies</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #fefaf5; color: #2c241a; }
        .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
        h1 { color: #8b5a2b; margin-bottom: 20px; font-family: 'Cormorant Garamond', serif; font-size: 2.5rem; }
        .nav-tabs { display: flex; gap: 15px; margin-bottom: 30px; flex-wrap: wrap; }
        .nav-btn { padding: 12px 28px; background: #fff8f2; border: 2px solid #ead8c7; border-radius: 40px; cursor: pointer; font-weight: 600; color: #6e3f1c; transition: all 0.2s; font-size: 0.9rem; }
        .nav-btn:hover, .nav-btn.active { background: #8b5a2b; color: white; border-color: #8b5a2b; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 20px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eeddc8; }
        .stat-card i { font-size: 2rem; color: #8b5a2b; margin-bottom: 10px; }
        .stat-card .number { font-size: 2rem; font-weight: 700; color: #6e3f1c; }
        .stat-card .label { color: #a08060; font-size: 0.85rem; margin-top: 5px; }
        .section { display: none; }
        .section.active { display: block; }
        table { width: 100%; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-collapse: collapse; }
        th, td { padding: 14px 16px; text-align: left; border-bottom: 1px solid #f0e3d8; }
        th { background: #8b5a2b; color: white; font-weight: 600; }
        tr:hover { background: #fef8f2; }
        .status-pending { background: #fff3cd; color: #856404; padding: 4px 12px; border-radius: 20px; display: inline-block; font-size: 0.75rem; font-weight: 600; }
        .status-confirmed { background: #d4edda; color: #155724; padding: 4px 12px; border-radius: 20px; display: inline-block; font-size: 0.75rem; font-weight: 600; }
        .status-cancelled { background: #f8d7da; color: #721c24; padding: 4px 12px; border-radius: 20px; display: inline-block; font-size: 0.75rem; font-weight: 600; }
        .status-completed { background: #d1ecf1; color: #0c5460; padding: 4px 12px; border-radius: 20px; display: inline-block; font-size: 0.75rem; font-weight: 600; }
        select, .update-btn { padding: 6px 12px; border-radius: 8px; border: 1px solid #ddd; background: white; cursor: pointer; }
        .update-btn { background: #8b5a2b; color: white; border: none; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; }
        .badge-unread { background: #dc3545; color: white; }
        .badge-read { background: #28a745; color: white; }
        .table-wrapper { overflow-x: auto; }
        @media (max-width: 768px) { th, td { padding: 10px; font-size: 0.8rem; } }
    </style>
</head>
<body>
<div class="container">
    <h1><i class="fas fa-spa"></i> GlowUp Ladies - Admin Dashboard</h1>
    
    <?php
    require_once '../db_connect.php';
    
    // Get statistics
    $total_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
    $pending_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status='pending'")->fetch_assoc()['count'];
    $total_messages = $conn->query("SELECT COUNT(*) as count FROM contact_messages")->fetch_assoc()['count'];
    $unread_messages = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE is_read=0")->fetch_assoc()['count'];
    $total_staff = $conn->query("SELECT COUNT(*) as count FROM staff")->fetch_assoc()['count'];
    ?>
    
    <div class="stats-grid">
        <div class="stat-card"><i class="fas fa-calendar-check"></i><div class="number"><?php echo $total_bookings; ?></div><div class="label">Total Bookings</div></div>
        <div class="stat-card"><i class="fas fa-clock"></i><div class="number"><?php echo $pending_bookings; ?></div><div class="label">Pending Bookings</div></div>
        <div class="stat-card"><i class="fas fa-envelope"></i><div class="number"><?php echo $total_messages; ?></div><div class="label">Messages</div></div>
        <div class="stat-card"><i class="fas fa-envelope-open-text"></i><div class="number"><?php echo $unread_messages; ?></div><div class="label">Unread</div></div>
        <div class="stat-card"><i class="fas fa-users"></i><div class="number"><?php echo $total_staff; ?></div><div class="label">Staff Members</div></div>
    </div>
    
    <div class="nav-tabs">
        <button class="nav-btn active" onclick="showSection('bookings')"><i class="fas fa-calendar-alt"></i> Bookings</button>
        <button class="nav-btn" onclick="showSection('messages')"><i class="fas fa-envelope"></i> Contact Messages</button>
        <button class="nav-btn" onclick="showSection('staff')"><i class="fas fa-users"></i> Staff Management</button>
        <button class="nav-btn" onclick="showSection('services')"><i class="fas fa-cut"></i> Services</button>
    </div>
    
    <!-- BOOKINGS SECTION -->
    <div id="bookings-section" class="section active">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Ref</th><th>Customer</th><th>Services</th><th>Staff</th><th>Date</th><th>Time</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php 
                    $bookings = $conn->query("SELECT * FROM bookings ORDER BY created_at DESC");
                    while($row = $bookings->fetch_assoc()): 
                    ?>
                    <tr>
                        <td><strong><?php echo $row['booking_ref']; ?></strong></td>
                        <td><?php echo htmlspecialchars($row['customer_name']); ?><br><small><?php echo $row['customer_phone']; ?></small></td>
                        <td><?php echo substr(htmlspecialchars($row['service_names']), 0, 40); ?>...</td>
                        <td><?php echo htmlspecialchars($row['staff_name']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($row['appointment_date'])); ?></td>
                        <td><?php echo date('h:i A', strtotime($row['appointment_time'])); ?></td>
                        <td><span class="status-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                        <td>
                            <form method="POST" action="update_status.php" style="display:flex; gap:5px;">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <select name="status">
                                    <option value="pending" <?php echo $row['status']=='pending'?'selected':''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo $row['status']=='confirmed'?'selected':''; ?>>Confirmed</option>
                                    <option value="cancelled" <?php echo $row['status']=='cancelled'?'selected':''; ?>>Cancelled</option>
                                    <option value="completed" <?php echo $row['status']=='completed'?'selected':''; ?>>Completed</option>
                                </select>
                                <button type="submit" class="update-btn">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- MESSAGES SECTION -->
    <div id="messages-section" class="section">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Date</th><th>Name</th><th>Email</th><th>Phone</th><th>Subject</th><th>Message</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php 
                    $messages = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
                    while($msg = $messages->fetch_assoc()): 
                    ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($msg['created_at'])); ?></td>
                        <td><?php echo htmlspecialchars($msg['first_name'] . ' ' . $msg['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($msg['email']); ?></td>
                        <td><?php echo htmlspecialchars($msg['phone']); ?></td>
                        <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                        <td><?php echo htmlspecialchars(substr($msg['message'], 0, 60)); ?>...</td>
                        <td><span class="badge <?php echo $msg['is_read'] ? 'badge-read' : 'badge-unread'; ?>"><?php echo $msg['is_read'] ? 'Read' : 'Unread'; ?></span></td>
                        <td>
                            <?php if(!$msg['is_read']): ?>
                            <form method="POST" action="update_status.php" style="display:inline;">
                                <input type="hidden" name="msg_id" value="<?php echo $msg['id']; ?>">
                                <button type="submit" name="mark_read" class="update-btn">Mark Read</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- STAFF SECTION -->
    <div id="staff-section" class="section">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Role</th><th>Experience</th><th>Specializations</th><th>Rating</th><th>Reviews</th></tr>
                </thead>
                <tbody>
                    <?php 
                    $staff_list = $conn->query("SELECT * FROM staff ORDER BY id");
                    while($staff_member = $staff_list->fetch_assoc()): 
                    ?>
                    <tr>
                        <td><?php echo $staff_member['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($staff_member['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($staff_member['role']); ?></td>
                        <td><?php echo $staff_member['experience']; ?> years</td>
                        <td><?php echo htmlspecialchars($staff_member['specialization']); ?></td>
                        <td><?php echo $staff_member['rating']; ?> ★</td>
                        <td><?php echo $staff_member['review_count']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <p style="margin-top:20px; color:#a08060;"><i class="fas fa-info-circle"></i> To add/edit staff, use phpMyAdmin or run SQL queries directly.</p>
    </div>
    
    <!-- SERVICES SECTION -->
    <div id="services-section" class="section">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>ID</th><th>Service Name</th><th>Description</th><th>Duration</th><th>Price</th></tr>
                </thead>
                <tbody>
                    <?php 
                    $services_list = $conn->query("SELECT * FROM services ORDER BY id");
                    while($service = $services_list->fetch_assoc()): 
                    ?>
                    <tr>
                        <td><?php echo $service['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($service['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($service['description']); ?></td>
                        <td><?php echo $service['duration']; ?></td>
                        <td><?php echo $service['price']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function showSection(section) {
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.getElementById(section + '-section').classList.add('active');
    document.querySelectorAll('.nav-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
}
</script>
</body>
</html>
<?php $conn->close(); ?>