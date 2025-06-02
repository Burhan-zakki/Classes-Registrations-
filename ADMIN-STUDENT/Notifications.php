<?php
session_start();

// Enable error logging (for debugging only — disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// AJAX request handling
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    $sql = "SELECT 
              cs.id,
              cs.title,
              cs.image_path,
              cs.submitted_at,
              c.title AS course_name,
              u.name AS user_name
            FROM course_submissions cs
            JOIN courses c ON cs.course_id = c.id
            JOIN users u ON cs.user_id = u.id
            ORDER BY cs.submitted_at DESC";

    $result = $conn->query($sql);

    if (!$result) {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
        exit;
    }

    $submissions = [];
    while ($row = $result->fetch_assoc()) {
        $submissions[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($submissions);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Submission Notifications</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="./CSS/Notifications.css" />
  <style>
    body {
      background-color: #f4f6f8;
    }
    .card-img-top {
      object-fit: cover;
      height: 200px;
    }
    .container {
      margin-top: 40px;
      margin-bottom: 40px;
    }
    h1 {
      margin-bottom: 30px;
      text-align: center;
      font-weight: 700;
      color: #00f2fe;
    }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>

  <div class="container">
    <h1>Submission Notifications</h1>
    <div id="submission-container" class="row row-cols-1 row-cols-md-3 g-4">
      <!-- Submissions will be loaded here -->
    </div>
  </div>

  <!-- Modal for viewing submission details -->
  <div class="modal fade" id="submissionModal" tabindex="-1" aria-labelledby="submissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="submissionModalLabel">Submission Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <img id="modalImage" src="" class="img-fluid mb-3" alt="Submission Image">
          <h5 id="modalTitle"></h5>
          <p><strong>Course:</strong> <span id="modalCourse"></span></p>
          <p><strong>User:</strong> <span id="modalUser"></span></p>
          <p><small class="text-muted">Submitted at: <span id="modalTime"></span></small></p>
        </div>
      </div>
    </div>
  </div>

  <script>
    async function fetchSubmissions() {
      const container = document.getElementById('submission-container');
      try {
        const response = await fetch('Notifications.php?ajax=1');
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        container.innerHTML = '';

        if (data.length === 0) {
          container.innerHTML = '<div class="alert alert-info text-center w-100">No submissions found.</div>';
          return;
        }

        data.forEach(sub => {
          const card = document.createElement('div');
          card.className = 'col';
          card.innerHTML = `
            <div class="card h-100 shadow-sm">
              <img src="${sub.image_path}" 
                   class="card-img-top" 
                   alt="${sub.title}" 
                   onerror="this.onerror=null;this.src='https://via.placeholder.com/400x200?text=No+Image';" />
              <div class="card-body">
                <h5 class="card-title">${sub.title}</h5>
                <p class="card-text">
                  <strong>Course:</strong> ${sub.course_name}<br />
                  <strong>User:</strong> ${sub.user_name}
                </p>
                <p class="card-text">
                  <small class="text-muted">Submitted at: ${new Date(sub.submitted_at).toLocaleString()}</small>
                </p>
                <button class="btn btn-primary btn-sm" onclick='viewSubmission(${JSON.stringify(sub)})'>View</button>
              </div>
            </div>
          `;
          container.appendChild(card);
        });
      } catch (error) {
        console.error("Fetch error:", error);
        container.innerHTML = '<div class="alert alert-danger w-100 text-center">Error loading submissions.</div>';
      }
    }

    function viewSubmission(submission) {
      document.getElementById('modalImage').src = submission.image_path;
      document.getElementById('modalImage').alt = submission.title;
      document.getElementById('modalTitle').textContent = submission.title;
      document.getElementById('modalCourse').textContent = submission.course_name;
      document.getElementById('modalUser').textContent = submission.user_name;
      document.getElementById('modalTime').textContent = new Date(submission.submitted_at).toLocaleString();

      const modal = new bootstrap.Modal(document.getElementById('submissionModal'));
      modal.show();
    }

    fetchSubmissions(); // Load initially
    setInterval(fetchSubmissions, 5000); // Refresh every 5 seconds
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
