<?php
include 'db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch all courses
$sql = "SELECT * FROM courses ORDER BY id ASC";
$result = $conn->query($sql);
$courses = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Courses</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="./CSS/Manage_courses.css" />
</head>
<body>

  <div class="container py-4">
    <h1>Manage Courses</h1>
    <div id="submission-container" class="row row-cols-1 row-cols-md-3 g-4">
      <?php foreach ($courses as $course): 
          // Use image path from DB or fallback placeholder
          $image = !empty($course['image']) ? $course['image'] : 'https://via.placeholder.com/400x200?text=No+Image';
      ?>
      <div class="col">
        <div class="card h-100 shadow-sm">
          <img src="<?php echo htmlspecialchars($image); ?>" 
               class="card-img-top" 
               alt="<?php echo htmlspecialchars($course['title']); ?>"
               onerror="this.onerror=null;this.src='https://via.placeholder.com/400x200?text=No+Image';" />
          <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
            <p class="card-text"><?php echo htmlspecialchars($course['description']); ?></p>
            <p class="card-text"><strong>Price: </strong>$<?php echo number_format($course['price'], 2); ?></p>
            <a href="edit_course.php?id=<?php echo urlencode($course['id']); ?>" class="btn btn-outline-info">Manage</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
