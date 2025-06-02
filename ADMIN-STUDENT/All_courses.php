<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

// Fetch courses created by this admin
$created_by = $_SESSION['user_id'];
$courses = [];
$result = $conn->prepare("SELECT id, title, description, schedule, seats, price, image FROM courses WHERE created_by = ?");
$result->bind_param("i", $created_by);
$result->execute();
$res = $result->get_result();
if ($res) {
    $courses = $res->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>All Created Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="./CSS/All_courses.css" />
    <style>
        .card-title {
            color: #00f2fe;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .card-text {
            color: #e0e0e0;
            font-weight: 500;
        }
        .btn-edit {
            background-color: #00f2fe;
            border: none;
            color: #000;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .btn-edit:hover {
            background-color: #00c1d6;
            color: #fff;
        }
        .course-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 1px solid #ccc;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h1>All Created Courses</h1>
    
    <div id="course-container" class="row row-cols-1 row-cols-md-3 g-4">
        <?php if (empty($courses)): ?>
            <div class="alert alert-info w-100 text-center">No courses found.</div>
        <?php else: ?>
            <?php foreach ($courses as $course): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($course['image'])): ?>
                            <img src="<?php echo htmlspecialchars($course['image']); ?>" class="course-image" alt="Course Image">
                        <?php else: ?>
                            <img src="placeholder.jpg" class="course-image" alt="No Image">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                            <p class="card-text">
                                <strong>Description:</strong> <?php echo htmlspecialchars($course['description']); ?><br />
                                <strong>Schedule:</strong> <?php echo htmlspecialchars($course['schedule']); ?><br />
                                <strong>Seats:</strong> <?php echo htmlspecialchars($course['seats']); ?><br />
                                <strong>Price:</strong> $<?php echo number_format($course['price'], 2); ?>
                            </p>
                            <a href="edit_course.php?id=<?php echo $course['id']; ?>" class="btn btn-edit btn-sm">Edit</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Add New Course Card -->
            <div class="col">
                <div class="card h-100 d-flex justify-content-center align-items-center shadow-sm" style="min-height: 250px;">
                    <a href="create_course.php" class="btn btn-add-course rounded-circle d-flex justify-content-center align-items-center">
                        <span class="display-4 fw-bold">+</span>
                    </a>
                    <h5 class="add-course-heading mt-3 text-center" style=" color: #00f2fe;">ADD COURSE</h5>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
