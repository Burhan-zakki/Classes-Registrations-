<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "Invalid request.";
    exit;
}

$registration_id = $_GET['id'];
$student_id = $_SESSION['user_id'];

$sql = "SELECT cr.*, c.title AS course_title
        FROM course_registrations cr
        JOIN courses c ON cr.course_id = c.id
        WHERE cr.id = ? AND cr.student_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $registration_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "No details found or access denied.";
    exit;
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>NOTIFICATION DETAIL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./CSS/S_Notifications.css" />
</head>
<body>
<?php include 'navbar.php'; ?>

<main class="container my-5">
    <h2>NOTIFICATION DETAIL</h2>
    <ul class="list-group">
        <li class="list-group-item"><strong>Course:</strong> <?= htmlspecialchars($row['course_title']) ?></li>
        <li class="list-group-item"><strong>Status:</strong> <?= $row['status'] ?></li>
        <li class="list-group-item"><strong>Payment Status:</strong> <?= $row['payment_status'] ?></li>
        <li class="list-group-item"><strong>Banned:</strong> <?= $row['banned'] ? 'Yes' : 'No' ?></li>
        <li class="list-group-item"><strong>Last Updated:</strong> <?= date('F j, Y, g:i a', strtotime($row['updated_at'])) ?></li>
    </ul>

    <a href="s_notifications.php" class="btn btn-secondary mt-4">Back to Notifications</a>
</main>
</body>
</html>
