<?php
include 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get course ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_courses.php");
    exit;
}

$course_id = (int)$_GET['id'];
$message = "";
$deleted = false;

// Handle form submission for update or delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $price = floatval($_POST['price']);
        $seats = intval($_POST['seats']);
        
        // Default to current image path (if any)
        $image = '';

        // Get existing image from DB before updating (for fallback)
        $stmtImg = $conn->prepare("SELECT image FROM courses WHERE id = ?");
        $stmtImg->bind_param("i", $course_id);
        $stmtImg->execute();
        $resImg = $stmtImg->get_result();
        if ($resImg && $resImg->num_rows > 0) {
            $rowImg = $resImg->fetch_assoc();
            $image = $rowImg['image'];
        }
        $stmtImg->close();

        // Handle image upload if a file is selected
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $tmpName = $_FILES['image']['tmp_name'];
            $originalName = basename($_FILES['image']['name']);
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($extension, $allowedExt)) {
                $message = "Invalid image file type. Allowed types: jpg, jpeg, png, gif, webp.";
            } else {
                $newFileName = uniqid("course_") . '.' . $extension;
                $destination = $uploadDir . $newFileName;

                if (move_uploaded_file($tmpName, $destination)) {
                    $image = $destination;
                } else {
                    $message = "Image upload failed.";
                }
            }
        }

        // Only update if no image upload errors
        if (empty($message)) {
            $stmt = $conn->prepare("UPDATE courses SET title = ?, description = ?, price = ?, seats = ?, image = ? WHERE id = ?");
            $stmt->bind_param("ssdisi", $title, $description, $price, $seats, $image, $course_id);

            if ($stmt->execute()) {
                $message = "Course updated successfully.";
            } else {
                $message = "Error updating course: " . $stmt->error;
            }
            $stmt->close();
        }

    } elseif (isset($_POST['delete'])) {
        // First delete course_submissions related to course_registrations of this course
        $stmt0 = $conn->prepare("
            DELETE cs FROM course_submissions cs
            JOIN course_registrations cr ON cs.registration_id = cr.id
            WHERE cr.course_id = ?
        ");
        $stmt0->bind_param("i", $course_id);
        $stmt0->execute();
        $stmt0->close();

        // Then delete from course_registrations
        $stmt1 = $conn->prepare("DELETE FROM course_registrations WHERE course_id = ?");
        $stmt1->bind_param("i", $course_id);
        $stmt1->execute();
        $stmt1->close();

        // Then delete the course itself
        $stmt2 = $conn->prepare("DELETE FROM courses WHERE id = ?");
        $stmt2->bind_param("i", $course_id);

        if ($stmt2->execute()) {
            $deleted = true;
        } else {
            $message = "Error deleting course: " . $stmt2->error;
        }

        $stmt2->close();
        $conn->close();
    }
}

if (!$deleted) {
    $stmt3 = $conn->prepare("SELECT * FROM courses WHERE id = ? LIMIT 1");
    $stmt3->bind_param("i", $course_id);
    $stmt3->execute();
    $result = $stmt3->get_result();

    if ($result->num_rows === 0) {
        $stmt3->close();
        $conn->close();
        header("Location: manage_courses.php");
        exit;
    }

    $course = $result->fetch_assoc();
    $stmt3->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Course</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="./CSS/edit_course.css" />
</head>
<body>
  <?php include 'navbar.php'; ?>
  <div class="container py-4">

    <h2>Edit Course</h2>

    <?php if ($message): ?>
      <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (!$deleted): ?>
      <form method="post" enctype="multipart/form-data" id="editForm">
        <div class="mb-3">
          <label for="title" class="form-label">Course Title</label>
          <input type="text" class="form-control" id="title" name="title" required value="<?php echo htmlspecialchars($course['title']); ?>" />
        </div>
        <div class="mb-3">
          <label for="description" class="form-label">Course Description</label>
          <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($course['description']); ?></textarea>
        </div>
        <div class="mb-3">
          <label for="price" class="form-label">Price (in USD)</label>
          <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" required value="<?php echo htmlspecialchars($course['price']); ?>" />
        </div>
        <div class="mb-3">
          <label for="seats" class="form-label">Seats</label>
          <input type="number" min="1" class="form-control" id="seats" name="seats" required value="<?php echo htmlspecialchars($course['seats']); ?>" />
        </div>
        <div class="mb-3">
          <label for="image" class="form-label">Course Image (upload new to replace)</label>
          <input type="file" class="form-control" id="image" name="image" accept="image/*" />
        </div>
        <?php if (!empty($course['image'])): ?>
          <div class="mb-3">
            <label class="form-label">Current Image:</label><br>
            <img src="<?php echo htmlspecialchars($course['image']); ?>" alt="Course Image" width="200" style="border:1px solid #ccc; padding:5px;"/>
          </div>
        <?php endif; ?>

        <button type="submit" name="update" class="btn btn-success">Update Course</button>
        <button type="button" class="btn btn-danger" id="deleteBtn">Delete Course</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
      </form>
    <?php endif; ?>
  </div>

  <script>
    const deleteBtn = document.getElementById('deleteBtn');
    deleteBtn?.addEventListener('click', () => {
      Swal.fire({
        title: 'Are you sure?',
        text: 'This will permanently delete the course and all related data.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          const form = document.createElement('form');
          form.method = 'POST';

          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'delete';
          input.value = '1';

          form.appendChild(input);
          document.body.appendChild(form);
          form.submit();
        }
      });
    });

    // Redirect after delete
    <?php if ($deleted): ?>
      Swal.fire({
        title: 'Deleted!',
        text: 'Course has been deleted.',
        icon: 'success',
        confirmButtonText: 'OK',
        timer: 2000,
        showConfirmButton: false
      });
      setTimeout(() => {
        window.location.href = 'dashboard.php';
      }, 2000);
    <?php endif; ?>
  </script>
</body>
</html>
