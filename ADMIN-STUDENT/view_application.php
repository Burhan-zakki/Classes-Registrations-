<!---STUDENT SIDE VIEW -->

<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['registration_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$registration_id = intval($_GET['registration_id']);

// Fetch application details including course_id
$sql = "SELECT cr.*, c.id AS course_id, c.title AS course_name 
        FROM course_registrations cr
        JOIN courses c ON cr.course_id = c.id
        WHERE cr.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $registration_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Application not found.";
    exit;
}

$data = $result->fetch_assoc();
$course_id = $data['course_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title']) && isset($_FILES['image'])) {
    $title = trim($_POST['title']);
    $image = $_FILES['image'];

    if ($image['error'] === 0) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

        $ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];

        if (!in_array($ext, $allowed_ext)) {
            $error = "Invalid image type. Allowed: " . implode(", ", $allowed_ext);
        } else {
            $filename = uniqid('img_') . "." . $ext;
            $image_path = $upload_dir . $filename;

            if (move_uploaded_file($image['tmp_name'], $image_path)) {
                // Insert submission record
                $stmt = $conn->prepare("INSERT INTO course_submissions (user_id, course_id, registration_id, title, image_path, submitted_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->bind_param("iiiss", $user_id, $course_id, $registration_id, $title, $image_path);

                if ($stmt->execute()) {
                    $success = "Submission saved successfully!";
                } else {
                    $error = "Database error: " . $stmt->error;
                }
            } else {
                $error = "Failed to upload image.";
            }
        }
    } else {
        $error = "Image upload error.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Application Details</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="./CSS/view_application.css" />
  <style>
    .container-flex {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        margin-top: 50px;
    }
    .left-box, .right-box {
        flex: 1;
        min-width: 300px;
        max-width: 48%;
        border: 1px solid #ccc;
        border-radius: 10px;
        padding: 25px;
        background-color: #f8f9fa;
        margin-bottom: 30px;
    }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>
<div class="container">
  <div class="container-flex">
    <!-- Left Box: Application details -->
    <div class="left-box">
      <h4>Course: <?= htmlspecialchars($data['course_name']) ?></h4>
      <p><strong>Name:</strong> <?= htmlspecialchars($data['name']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($data['email']) ?></p>
      <p><strong>Status:</strong> 
        <?= $data['status'] === 'Proceeded' ? '<span class="text-success">Proceeded</span>' : htmlspecialchars($data['status']) ?>
      </p>
      <p><strong>Payment Status:</strong> 
        <?= $data['payment_status'] === 'Paid' ? '<span class="text-success">Paid</span>' : '<span class="text-danger">Unpaid</span>' ?>
      </p>
      
      
    </div>

    <!-- Right Box: Submission form -->
    <div class="right-box">
      <h4>IF YOU HAVE PAID THEN UPLOAD PAYMENT RECIEPT AND WRITE MESSGAE</h4>

      <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
      <?php elseif (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" novalidate>
        <div class="mb-3">
          <label for="title" class="form-label">Title</label>
          <input 
            type="text" 
            class="form-control" 
            id="title" 
            name="title" 
            required
            maxlength="255"
            value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>"
          >
        </div>

        <div class="mb-3">
          <label for="image" class="form-label">Upload Image</label>
          <input 
            class="form-control" 
            type="file" 
            id="image" 
            name="image" 
            accept="image/*" 
            required
          >
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
         <a href="dashboard.php" class="btn btn-primary">Back</a>
      </form>
    </div>
  </div>
</div>
</body>
</html>
