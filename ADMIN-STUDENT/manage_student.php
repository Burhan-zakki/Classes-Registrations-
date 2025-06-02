<?php
session_start();
include "db.php";

// Admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

// Get student ID and course title from URL
$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;
$course_title = isset($_GET['course']) ? urldecode($_GET['course']) : '';

// Fetch the registration info
$sql = "SELECT 
            u.id AS user_id,
            u.name AS student_name,
            u.email AS student_email,
            c.id AS course_id,
            c.title AS course_title,
            c.schedule AS course_schedule,
            cr.id AS registration_id
        FROM course_registrations cr
        JOIN users u ON cr.student_id = u.id
        JOIN courses c ON cr.course_id = c.id
        WHERE u.id = ? AND c.title = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $student_id, $course_title);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>Student or course not found.</p>";
    exit;
}

$row = $result->fetch_assoc();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reg_id = intval($_POST['registration_id']);
    $action = $_POST['action'] ?? '';

    if ($action === 'unregister') {
        $ban_sql = "UPDATE course_registrations SET banned = 1 WHERE id = ?";
        $ban_stmt = $conn->prepare($ban_sql);
        $ban_stmt->bind_param("i", $reg_id);
        if ($ban_stmt->execute()) {
            header("Location: Already_Register.php?msg=Student+banned+from+this+course");
            exit;
        } else {
            $error = "Failed to ban the student.";
        }
    } elseif ($action === 'delete') {
        $del_sql = "DELETE FROM course_registrations WHERE id = ?";
        $del_stmt = $conn->prepare($del_sql);
        $del_stmt->bind_param("i", $reg_id);
        if ($del_stmt->execute()) {
            header("Location: Already_Register.php?msg=Registration+deleted+successfully");
            exit;
        } else {
            $error = "Failed to delete the registration.";
        }
    } elseif ($action === 'update') {
        header("Location: update_registration.php?reg_id=" . $reg_id);
        exit;
    } elseif ($action === 'cancel') {
        header("Location: dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="./CSS/edit_course.css" />
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container my-5">
    <h2 class="mb-4 text-center">Manage Student Registration</h2>

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            Student Info
        </div>
        <div class="card-body">
            <p><strong>Name:</strong> <?= htmlspecialchars($row['student_name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($row['student_email']) ?></p>
            <hr />
            <p><strong>Course:</strong> <?= htmlspecialchars($row['course_title']) ?></p>
            <p><strong>Schedule:</strong> <?= htmlspecialchars($row['course_schedule']) ?></p>

            <?php if ($error): ?>
                <div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" id="actionForm">
                <input type="hidden" name="registration_id" value="<?= $row['registration_id'] ?>">
                <input type="hidden" name="action" id="actionInput" value="">

                <button type="button" class="btn btn-warning mt-3" onclick="confirmAction('unregister')">Unregister</button>
                <button type="button" class="btn btn-info mt-3 ms-2" onclick="confirmAction('update')">Update</button>
                <button type="button" class="btn btn-danger mt-3 ms-2" onclick="confirmAction('delete')">Delete</button>
                <button type="button" class="btn btn-secondary mt-3 ms-2" onclick="window.location.href='Already_register.php'">Cancel</button>

            </form>
        </div>
    </div>
</div>

<!-- Bootstrap Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="confirmModalLabel">Confirm Action</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="confirmModalBody">
        Are you sure you want to perform this action?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmYesBtn">Yes, proceed</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
let currentAction = '';

function confirmAction(action) {
    currentAction = action;
    let message = '';
    switch(action) {
        case 'unregister':
            message = 'Are you sure you want to unregister (ban) this student from the course?';
            break;
        case 'update':
            message = 'Do you want to go to the update registration page?';
            break;
        case 'delete':
            message = 'Are you sure you want to delete this registration? This action cannot be undone.';
            break;
        default:
            message = 'Are you sure you want to perform this action?';
    }
    document.getElementById('confirmModalBody').textContent = message;
    confirmModal.show();
}

document.getElementById('confirmYesBtn').addEventListener('click', () => {
    document.getElementById('actionInput').value = currentAction;
    document.getElementById('actionForm').submit();
});
</script>

</body>
</html>
