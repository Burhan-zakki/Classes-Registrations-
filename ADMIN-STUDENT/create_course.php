<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $schedule = trim($_POST['schedule']);
    $seats = intval($_POST['seats']);
    $price = floatval($_POST['price']);
    $created_by = $_SESSION['user_id'];
    $imagePath = null;

    // Validate inputs
    if (empty($title) || empty($description) || empty($schedule) || $seats <= 0 || $price <= 0) {
        $message = "<p class='alert alert-danger'>Please fill in all fields correctly. Seats and price must be positive values.</p>";
    } else {
        // Handle image upload if provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $targetDir = "uploads/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $imageName = basename($_FILES['image']['name']);
            $imageFileType = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($imageFileType, $allowedTypes)) {
                $newFileName = uniqid() . "_" . $imageName;
                $targetFile = $targetDir . $newFileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $imagePath = $targetFile;
                } else {
                    $message = "<p class='alert alert-danger'>Failed to upload image.</p>";
                }
            } else {
                $message = "<p class='alert alert-danger'>Only JPG, JPEG, PNG, and GIF files are allowed.</p>";
            }
        }

        // Insert into database
        if (empty($message)) {
            $stmt = $conn->prepare("INSERT INTO courses (title, description, schedule, seats, price, image, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssidsi", $title, $description, $schedule, $seats, $price, $imagePath, $created_by);

            if ($stmt->execute()) {
                $message = "<p class='alert alert-success'>Course added successfully!</p>";
            } else {
                $message = "<p class='alert alert-danger'>Error adding course: " . htmlspecialchars($conn->error) . "</p>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Course</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./CSS/create_course.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h2 class="mb-4">Create New Course</h2>

    <?php if (!empty($message)) echo $message; ?>

    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <div class="mb-3">
            <input type="text" name="title" class="form-control" placeholder="Course Title" required value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
        </div>
        <div class="mb-3">
            <textarea name="description" class="form-control" placeholder="Course Description" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
        </div>
        <div class="mb-3">
            <input type="text" name="schedule" class="form-control" placeholder="Schedule (e.g. Mon-Wed 10AM-12PM)" required value="<?php echo isset($_POST['schedule']) ? htmlspecialchars($_POST['schedule']) : ''; ?>">
        </div>
        <div class="mb-3">
            <input type="number" name="seats" class="form-control" placeholder="Total Seats" required min="1" value="<?php echo isset($_POST['seats']) ? (int)$_POST['seats'] : ''; ?>">
        </div>
        <div class="mb-3">
            <input type="number" step="0.01" name="price" class="form-control" placeholder="Course Price" required value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>">
        </div>
        <div class="mb-3">
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Add Course</button>
    </form>

    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>

</body>
</html>
