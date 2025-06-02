<!--ADMIN SIDE FILE -->
<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $course_title = $_POST['course_title'];

    // Get course ID
    $stmt = $conn->prepare("SELECT id FROM courses WHERE title = ?");
    $stmt->bind_param("s", $course_title);
    $stmt->execute();
    $result = $stmt->get_result();
    $course = $result->fetch_assoc();
    $course_id = $course['id'];
    $stmt->close();

    // Get current banned status
    $stmt = $conn->prepare("SELECT banned FROM course_registrations WHERE student_id = ? AND course_id = ?");
    $stmt->bind_param("ii", $student_id, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $registration = $result->fetch_assoc();
    $current_banned = $registration['banned'];
    $stmt->close();

    // Toggle banned status
    $new_banned = $current_banned ? 0 : 1;

    $stmt = $conn->prepare("UPDATE course_registrations SET banned = ? WHERE student_id = ? AND course_id = ?");
    $stmt->bind_param("iii", $new_banned, $student_id, $course_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: Already_Register.php");
exit;
?>
