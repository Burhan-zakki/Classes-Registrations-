<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Handle registration (only if user is a student)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register']) && strtolower($role) === 'student') {
    $course_id = intval($_POST['course_id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);  // New line

    // Check if already registered
    $check = $conn->prepare("SELECT * FROM course_registrations WHERE course_id = ? AND student_id = ?");
    $check->bind_param("ii", $course_id, $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO course_registrations (course_id, student_id, name, email, phone_number) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $course_id, $user_id, $name, $email, $phone_number); // Updated bind_param
        if ($stmt->execute()) {
            echo "<div class='alert alert-success text-center mt-3'>Successfully registered!</div>";
        } else {
            echo "<div class='alert alert-danger text-center mt-3'>Database Error: " . $stmt->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger text-center mt-3'>You are already registered for this course.</div>";
    }
}

// Fetch registered courses
$registered_courses = [];
$registered_stmt = $conn->prepare("
    SELECT c.*, cr.banned 
    FROM courses c
    INNER JOIN course_registrations cr ON c.id = cr.course_id
    WHERE cr.student_id = ?
");
$registered_stmt->bind_param("i", $user_id);
$registered_stmt->execute();
$registered_result = $registered_stmt->get_result();

while ($row = $registered_result->fetch_assoc()) {
    $registered_courses[] = $row;
}

// Fetch available courses
$available_courses = [];
$available_stmt = $conn->prepare("
    SELECT * FROM courses WHERE id NOT IN (
        SELECT course_id FROM course_registrations WHERE student_id = ?
    )
");
$available_stmt->bind_param("i", $user_id);
$available_stmt->execute();
$available_result = $available_stmt->get_result();
while ($row = $available_result->fetch_assoc()) {
    $available_courses[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Course Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="./CSS/courses.css" rel="stylesheet" />
</head>
<body>
   
    <?php include 'navbar.php'; ?>
    <div class="container mt-4">
        <h1 class="mb-4 text-center">Course Registration</h1>

        <div class="tabs text-center mb-4">
            <button class="btn btn-outline-primary tab-btn active" onclick="showTab('available', event)">Available Courses</button>
            <button class="btn btn-outline-primary tab-btn" onclick="showTab('registered', event)">Already Registered</button>
        </div>

        <div id="available" class="tab-content active">
            <h2>Available Courses</h2>
            <?php if (count($available_courses) > 0): ?>
                <?php foreach ($available_courses as $course): ?>
                    <div class="card course-box mb-3 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($course['description']); ?></p>
                            <p><strong>Schedule:</strong> <?php echo htmlspecialchars($course['schedule']); ?></p>
                            <p><strong>Seats:</strong> <?php echo htmlspecialchars($course['seats']); ?></p>
                            <?php if (strtolower($role) === 'student'): ?>
                                <form method="POST" class="register-form mt-3">
                                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                    <div class="mb-2">
                                        <input type="text" name="name" placeholder="Your Full Name" class="form-control" required>
                                    </div>
                                    <div class="mb-2">
                                        <input type="email" name="email" placeholder="Your Email Address" class="form-control" required>
                                    </div>
                                    <div class="mb-2">
                                        <input type="tel" name="phone_number" placeholder="Your Phone Number" class="form-control" required pattern="[0-9+\-\s]{7,15}" title="Enter a valid phone number">
                                    </div>
                                    <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No courses available for registration.</p>
            <?php endif; ?>
        </div>

        <div id="registered" class="tab-content" style="display:none;">
            <h2>Already Registered Courses</h2>
            <?php if (count($registered_courses) > 0): ?>
                <?php foreach ($registered_courses as $course): ?>
                    <div class="card course-box mb-3 <?= $course['banned'] ? 'border-danger' : 'border-success' ?> shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($course['title']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($course['description']) ?></p>
                            <p><strong>Schedule:</strong> <?= htmlspecialchars($course['schedule']) ?></p>
                            <p><strong>Seats:</strong> <?= htmlspecialchars($course['seats']) ?></p>

                            <?php if ($course['banned']): ?>
                                <p class="text-danger fw-bold">❌ You are banned from this course.</p>
                            <?php else: ?>
                                <p class="text-success fw-bold">✔ Registered</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You have not registered for any courses yet.</p>
            <?php endif; ?>
        </div>

        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <script>
    function showTab(tabId, event) {
        document.querySelectorAll('.tab-content').forEach(el => {
            el.style.display = (el.id === tabId) ? 'block' : 'none';
            el.classList.toggle('active', el.id === tabId);
        });
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        event.target.classList.add('active');
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
