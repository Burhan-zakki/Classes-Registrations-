<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>About Us</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="./CSS/index.css">
  <style>
    .about-header {
     
      padding: 60px 20px;
      text-align: center;
    }
    .about-header h1 {
      font-size: 3rem;
    }
    .team-img {
      width: 100%;
      border-radius: 10px;
      object-fit: cover;
    }
  </style>
</head>
<body>
  <?php include 'nav2.php'; ?>
  <div class="about-header">
    <h1>About Us</h1>
    <p class="lead">Empowering students with high-quality and affordable online education.</p>
  </div>

  <div class="container py-5">
    <div class="row mb-5">
      <div class="col-md-6">
        <h2>Who We Are</h2>
        <p>
          We are a passionate team of educators, developers, and designers committed to building a platform that helps learners acquire skills and knowledge effectively. Our mission is to make quality education accessible to everyone, everywhere.
        </p>
      </div>
      <div class="col-md-6">
        <img src="./img/12.png" alt="Our Team" class="team-img shadow">
      </div>
    </div>

    <div class="row mb-5">
      <div class="col-md-6 order-md-2">
        <h2>Our Vision</h2>
        <p>
          To become a leading online learning platform that transforms lives through accessible, innovative, and skill-based courses. We believe in lifelong learning and strive to create a community of curious minds and future leaders.
        </p>
      </div>
      <div class="col-md-6 order-md-1">
        <img src="about2.png" alt="Our Vision" class="team-img shadow">
      </div>
    </div>

    <div class="text-center">
      <h2>Join Us on This Journey</h2>
      <p>
        Whether you're a student, teacher, or enthusiast, there's something here for you. Let's grow together!
      </p>
      <a href="courses.php" class="btn btn-primary btn-lg mt-3">Explore Courses</a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
