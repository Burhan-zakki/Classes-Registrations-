<?php
session_start();
include "db.php";

// Only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

// Fetch all registered students with course and login info
$sql = "SELECT 
            u.id AS user_id,
            u.email AS login_email,
            u.role AS user_role,
            cr.name AS reg_name,
            cr.email AS reg_email,
            c.title AS course_title,
            c.schedule AS course_schedule,
            cr.banned AS banned,
            cr.status AS reg_status,
            cr.payment_status  -- ✅ Add this line
        FROM 
            course_registrations cr
        JOIN 
            users u ON cr.student_id = u.id
        JOIN 
            courses c ON cr.course_id = c.id
        ORDER BY cr.id DESC";



$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registered Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./CSS/Already_Register.css">
</head>
<body>
   <?php include 'navbar.php'; ?>


    <main class="container my-5">
        <h2 class="mb-4 text-center" >All Registered Students</h2>

        <?php if ($result->num_rows > 0): ?>
            <div class="row row-cols-1 row-cols-md-2 g-4">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col">
                        <div class="card shadow-sm reg-card">
                            <div class="card-header login-info">
                                <span><strong>Login Email:</strong> <?= htmlspecialchars($row['login_email']) ?></span> |
                                <span><strong>Role:</strong> <?= htmlspecialchars($row['user_role']) ?></span>
                            </div>
                        <div class="card-body d-flex flex-column">
                        <p><strong>Registered Name:</strong> <?= htmlspecialchars($row['reg_name']) ?></p>
                        <p><strong>Registered Email:</strong> <?= htmlspecialchars($row['reg_email']) ?></p>
                        <p><strong>Course Title:</strong> <?= htmlspecialchars($row['course_title']) ?></p>
                        <p><strong>Schedule:</strong> <?= htmlspecialchars($row['course_schedule']) ?></p>
                        <p><strong>Status:</strong> 
                        <?= $row['banned'] ? '<span class="text-danger">Banned</span>' : '<span class="text-success">Active</span>' ?>
                        </p>

    <!-- Make button area stick to the bottom -->
    <div class="mt-auto d-flex justify-content-between align-items-center pt-3">
        <a href="manage_student.php?student_id=<?= $row['user_id'] ?>&course=<?= urlencode($row['course_title']) ?>" class="btn btn-outline-primary btn-sm">Manage</a>

        <form method="post" action="toggle_ban.php" class="d-inline">
            <input type="hidden" name="student_id" value="<?= $row['user_id'] ?>">
            <input type="hidden" name="course_title" value="<?= htmlspecialchars($row['course_title']) ?>">
            <button type="submit" class="btn btn-sm <?= $row['banned'] ? 'btn-success' : 'btn-warning' ?>">
                <?= $row['banned'] ? 'Unban Student' : 'Ban Student' ?>
            </button>
        </form>

                  <?php if ($row['reg_status'] === 'Proceeded'): ?>
                  <button class="btn btn-sm btn-success" disabled>Processed</button>
                   <?php if ($row['payment_status'] === 'Paid'): ?>
                   <span class="badge bg-primary ms-2">PAID</span>
                    <?php elseif ($row['payment_status'] === 'Unpaid'): ?>
                    <span class="badge bg-warning text-dark ms-2" >UNPAID</span>
                     <?php endif; ?>
                     <?php else: ?>
                     <form method="get" action="process_application.php" class="d-inline">
                      <input type="hidden" name="student_id" value="<?= $row['user_id'] ?>">
                       <input type="hidden" name="course_title" value="<?= htmlspecialchars($row['course_title']) ?>">
                        <button type="submit" class="btn btn-sm btn-dark">Process Application</button>
                        </form>
                         <?php endif; ?>
                        </div>
                        </div>

                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-center mt-5 fs-5">No students have registered for any courses yet.</p>
        <?php endif; ?>

        <div class="mt-4 text-center">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

// Fetch all registered students with course and login info
$sql = "SELECT 
            u.id AS user_id,
            u.email AS login_email,
            u.role AS user_role,
            cr.name AS reg_name,
            cr.email AS reg_email,
            c.title AS course_title,
            c.schedule AS course_schedule,
            cr.banned AS banned
        FROM 
            course_registrations cr
        JOIN 
            users u ON cr.student_id = u.id
        JOIN 
            courses c ON cr.course_id = c.id
        ORDER BY cr.id DESC";

try {
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception($conn->error);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>
<?php
// Only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

// Fetch all registered students with course and login info
$sql = "SELECT 
            u.id AS user_id,
            u.email AS login_email,
            u.role AS user_role,
            cr.name AS reg_name,
            cr.email AS reg_email,
            c.title AS course_title,
            c.schedule AS course_schedule,
            cr.banned AS banned
        FROM 
            course_registrations cr
        JOIN 
            users u ON cr.student_id = u.id
        JOIN 
            courses c ON cr.course_id = c.id
        ORDER BY cr.id DESC";

try {
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception($conn->error);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>
