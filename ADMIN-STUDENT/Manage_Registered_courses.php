<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Logged-in user's ID

// Fetch this user's registrations with course details including image and price
$sql = "SELECT cr.*, c.title AS course_name, c.description AS course_description, c.image AS course_image, c.price AS course_price
        FROM course_registrations cr
        JOIN courses c ON cr.course_id = c.id
        WHERE cr.student_id = ?
        ORDER BY cr.id ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$registrations = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $registrations[] = $row;
    }
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Your Registered Courses</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="./CSS/manage_courses.css" />

  <style>
    .course-image {
        width: 100%;
        height: 180px;
        background-size: cover;
        background-position: center;
        border-radius: 12px;
        margin-bottom: 15px;
    }

    .course-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #00e0ff;
        margin-bottom: 10px;
    }

    .course-description {
        color: #f1f1f1;
        font-size: 0.95rem;
        margin-bottom: 10px;
    }

    .course-price {
        font-weight: bold;
        font-size: 1rem;
        color: #0d6efd; /* bootstrap primary color */
        margin-bottom: 10px;
    }

    .load-more-wrapper {
        text-align: center;
        margin-top: 30px;
    }
  </style>
</head>
<body>
  <div class="container py-4">
    <h2 class="mb-4 text-center text-info">Your Registered Courses</h2>
    <div id="registrationsRow" class="row g-4">
      <?php foreach ($registrations as $index => $registration):
          // Use course image from DB or fallback
          $courseImage = !empty($registration['course_image']) ? $registration['course_image'] : 'https://via.placeholder.com/300?text=No+Image';
          $hidden_class = ($index >= 6) ? 'd-none' : '';
      ?>
      <div class="col-md-4 registration-card-wrapper <?php echo $hidden_class; ?>">
        <div class="course-card h-100 d-flex flex-column">
          <div class="course-image" style="background-image: url('<?php echo htmlspecialchars($courseImage); ?>');"></div>
          <div class="course-title"><?php echo htmlspecialchars($registration['course_name']); ?></div>

          <div class="course-description">
            <?php echo nl2br(htmlspecialchars($registration['course_description'])); ?>
          </div>

          <div class="course-price">
            Price: $<?php echo number_format($registration['course_price'], 2); ?>
          </div>

          <div class="course-description">
            <strong>Registrant:</strong> <?php echo htmlspecialchars($registration['name']); ?><br />
            <strong>Email:</strong> <?php echo htmlspecialchars($registration['email']); ?><br />
          </div>

          <div class="mt-auto">
            <a href="view_application.php?registration_id=<?php echo $registration['id']; ?>" class="btn btn-primary btn-sm">View Application</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <?php if (count($registrations) > 6): ?>
    <div class="load-more-wrapper">
      <button id="loadMoreBtn" class="btn btn-outline-primary">Load More</button>
    </div>
    <?php endif; ?>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const registrations = document.querySelectorAll('.registration-card-wrapper');
    const loadMoreBtn = document.getElementById('loadMoreBtn');

    loadMoreBtn?.addEventListener('click', () => {
      const hiddenItems = Array.from(registrations).filter(el => el.classList.contains('d-none'));
      const nextItems = hiddenItems.slice(0, 6);

      nextItems.forEach(el => el.classList.remove('d-none'));

      if (Array.from(registrations).filter(el => el.classList.contains('d-none')).length === 0) {
        loadMoreBtn.style.display = 'none';
      }
    });
  </script>
</body>
</html>
