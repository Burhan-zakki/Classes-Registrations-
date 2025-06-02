<!--ADMIN SIDE FILE -->

<?php
session_start();
include "db.php";

// Admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['reg_id']) || !filter_var($_GET['reg_id'], FILTER_VALIDATE_INT)) {
    echo "Invalid registration ID.";
    exit;
}

$reg_id = intval($_GET['reg_id']);
$error = "";
$success = "";

// Fetch registration info with student info
$sql = "SELECT cr.id as reg_id, u.name as student_name, u.email as student_email, u.id as student_id, c.id as course_id, c.title as course_title
        FROM course_registrations cr
        JOIN users u ON cr.student_id = u.id
        JOIN courses c ON cr.course_id = c.id
        WHERE cr.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $reg_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Registration not found.";
    exit;
}

$row = $result->fetch_assoc();

// Fetch all courses for dropdown
$courses = [];
$course_sql = "SELECT id, title FROM courses ORDER BY title ASC";
$course_result = $conn->query($course_sql);
if ($course_result) {
    while ($course_row = $course_result->fetch_assoc()) {
        $courses[] = $course_row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = trim($_POST['name']);
    $new_email = trim($_POST['email']);
    $new_course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;

    // Validate inputs
    if (empty($new_name) || empty($new_email)) {
        $error = "Name and email cannot be empty.";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $valid_course_ids = array_column($courses, 'id');
        if (!in_array($new_course_id, $valid_course_ids)) {
            $error = "Invalid course selected.";
        } else {
            // Start transaction to update both tables safely
            $conn->begin_transaction();

            try {
                // Update user info (name and email)
                $update_user_sql = "UPDATE users SET name = ?, email = ? WHERE id = ?";
                $update_user_stmt = $conn->prepare($update_user_sql);
                $update_user_stmt->bind_param("ssi", $new_name, $new_email, $row['student_id']);
                $update_user_stmt->execute();

                // Update course registration course_id
                $update_reg_sql = "UPDATE course_registrations SET course_id = ? WHERE id = ?";
                $update_reg_stmt = $conn->prepare($update_reg_sql);
                $update_reg_stmt->bind_param("ii", $new_course_id, $reg_id);
                $update_reg_stmt->execute();

                // Commit transaction
                $conn->commit();

                $success = "Registration and student info updated successfully.";

                // Refresh data for showing updated info
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();

            } catch (Exception $e) {
                $conn->rollback();
                $error = "Failed to update registration: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container my-5">
    <h2 class="mb-4 text-center">Update Student Registration</h2>

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            Student Info
        </div>
        <div class="card-body">

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Student Name</label>
                    <input type="text" id="name" name="name" class="form-control" required value="<?= htmlspecialchars($row['student_name']) ?>">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Student Email</label>
                    <input type="email" id="email" name="email" class="form-control" required value="<?= htmlspecialchars($row['student_email']) ?>">
                </div>

                <div class="mb-3">
                    <label for="course_id" class="form-label">Select Course</label>
                    <select name="course_id" id="course_id" class="form-select" required>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= $course['id'] ?>" <?= ($course['id'] == $row['course_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($course['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Update</button>
                <a href="Already_Register.php" class="btn btn-secondary ms-2">Back</a>
            </form>

        </div>
    </div>
</div>

</body>
</html>

