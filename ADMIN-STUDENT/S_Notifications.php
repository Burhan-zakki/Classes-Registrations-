<?php
session_start();
include "db.php";

// Only students can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['user_id'];

// Fetch notifications related to this student
$sql = "SELECT 
            cr.id,
            cr.status,
            cr.banned,
            cr.payment_status,
            cr.updated_at,
            c.title AS course_title
        FROM 
            course_registrations cr
        JOIN 
            courses c ON cr.course_id = c.id
        WHERE 
            cr.student_id = ?
        ORDER BY cr.updated_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./CSS/S_Notifications.css" />
    <style>
        .notification-card {
            border-left: 4px solid #007bff;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .text-muted {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<main class="container my-5">
    <h2 class="mb-4 text-center">Notifications</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="notification-card">
                <strong>Course:</strong> <?= htmlspecialchars($row['course_title']) ?><br>
                
                <?php if ($row['status'] === 'Proceeded'): ?>
                    ✅ Your application has been <strong>processed</strong> by the admin.<br>
                <?php elseif ($row['status'] === 'Pending'): ?>
                    ⏳ Your application is still <strong>pending</strong> review.<br>
                <?php endif; ?>

                <?php if ($row['banned']): ?>
                    🚫 You have been <strong>banned</strong> from this course.<br>
                <?php else: ?>
                    ✅ You are <strong>active</strong> in this course.<br>
                <?php endif; ?>

                <?php if ($row['payment_status'] === 'Paid'): ?>
                    💰 Your payment has been <strong>received</strong>.<br>
                <?php elseif ($row['payment_status'] === 'Unpaid'): ?>
                    💸 Your payment is <strong>pending</strong>. Please complete it.<br>
                <?php endif; ?>

                <div class="text-muted mt-2">Last Updated: <?= date('F j, Y, g:i a', strtotime($row['updated_at'])) ?></div>
                
                <div class="mt-2">
                    <a href="view_snotifications.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">View Details</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-center fs-5 mt-5">You have no notifications yet.</p>
    <?php endif; ?>

    <div class="mt-4 text-center">
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
