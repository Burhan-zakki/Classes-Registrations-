<?php
session_start();
include "db.php";

// Only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

// Check if student_id and course_title are provided via GET or POST
$student_id = $_GET['student_id'] ?? $_POST['student_id'] ?? null;
$course_title = $_GET['course_title'] ?? $_POST['course_title'] ?? null;

if (!$student_id || !$course_title) {
    echo "Invalid request.";
    exit;
}

// Handle form submission to update payment status and set status to proceeded
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_status = $_POST['payment_status'] ?? 'Unpaid';

    // Update course_registrations status and payment_status
    $stmt = $conn->prepare("UPDATE course_registrations cr
                            JOIN courses c ON cr.course_id = c.id
                            SET cr.status = 'Proceeded', cr.payment_status = ?
                            WHERE cr.student_id = ? AND c.title = ?");
    $stmt->bind_param("sis", $payment_status, $student_id, $course_title);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['message'] = "Application processed successfully.";
    } else {
        $_SESSION['message'] = "Failed to process application.";
    }
    $stmt->close();

    // Redirect back to this page (GET) to avoid resubmission
    header("Location: process_application.php?student_id=$student_id&course_title=" . urlencode($course_title));
    exit;
}

// Fetch registration details
$stmt = $conn->prepare("SELECT 
                            cr.name, cr.email, cr.status, cr.payment_status,
                            u.email AS login_email,
                            c.title, c.schedule
                        FROM 
                            course_registrations cr
                        JOIN users u ON cr.student_id = u.id
                        JOIN courses c ON cr.course_id = c.id
                        WHERE cr.student_id = ? AND c.title = ?");
$stmt->bind_param("is", $student_id, $course_title);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "No registration found.";
    exit;
}

$row = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Process Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./CSS/S_Notifications.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
<div class="container my-5">
    <h2 class="mb-4">Process Application</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info"><?= htmlspecialchars($_SESSION['message']); ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="row">
        <!-- Left side: Registration Details -->
        <div class="col-md-6 border-end">
            <h4>Registration Details</h4>
            <p><strong>Name:</strong> <?= htmlspecialchars($row['name']) ?></p>
            <p><strong>Registered Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
            <p><strong>Login Email:</strong> <?= htmlspecialchars($row['login_email']) ?></p>
            <p><strong>Course Title:</strong> <?= htmlspecialchars($row['title']) ?></p>
            <p><strong>Schedule:</strong> <?= htmlspecialchars($row['schedule']) ?></p>
            <p><strong>Current Status:</strong> <?= htmlspecialchars($row['status']) ?></p>
            <p><strong>Payment Status:</strong> <?= htmlspecialchars($row['payment_status'] ?? 'Unpaid') ?></p>
        </div>

        <!-- Right side: Payment status form -->
        <div class="col-md-6">
            <h4>Update Payment Status</h4>
            <form method="post" action="process_application.php">
                <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id) ?>">
                <input type="hidden" name="course_title" value="<?= htmlspecialchars($course_title) ?>">

                <div class="mb-3">
                    <label for="payment_status" class="form-label">Payment Status:</label>
                    <select name="payment_status" id="payment_status" class="form-select" required>
                        <option value="Paid" <?= ($row['payment_status'] === 'Paid') ? 'selected' : '' ?>>Paid</option>
                        <option value="Unpaid" <?= ($row['payment_status'] === 'Unpaid' || !$row['payment_status']) ? 'selected' : '' ?>>Unpaid</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Process</button>
            </form>
        </div>
    </div>

    <div class="mt-4">
        <a href="Already_Register.php" class="btn btn-secondary">Back to Registered Students</a>
    </div>
</div>
</body>
</html>
