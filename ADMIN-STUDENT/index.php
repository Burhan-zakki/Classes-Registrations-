<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Online Learning Platform</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./CSS/index.css">
</head>
<body>

  <!-- Navbar -->
   <?php include 'nav2.php'; ?>

  <!-- Hero Section -->
  <section class="hero d-flex align-items-center">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6 text-white">
          <h1 class="display-4 fw-bold">Online learning<br>platform</h1>
          <p class="lead mt-3">
            Build skills with courses, certificates, and degrees online from world-class universities and companies
          </p>
          <a href="register.php" class="btn join-btn mt-4">Join For Free</a>
        </div>
        <div class="col-md-6 text-center">
          <img src="about2.png" alt="Illustration" class="img-fluid" style="max-height: 400px;">
        </div>
      </div>
    </div>
  </section>
    <?php include 'nouser_courses.php'; ?>
    <?php include 'About.php'; ?>`  
    <?php include 'contact.php'; ?>
    <?php include 'footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
