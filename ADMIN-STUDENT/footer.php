<style>
  .custom-footer {
    background: linear-gradient(to right, #2575fc, #6a11cb);
    color: #ffffff;
    padding: 40px 0 20px;
    font-size: 14px;
  }

  .custom-footer a {
    color: #ffd28d;
    text-decoration: none;
  }

  .custom-footer a:hover {
    text-decoration: underline;
    color: #ffffff;
  }

  .footer-title {
    font-weight: bold;
    margin-bottom: 10px;
  }

  .footer-bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    margin-top: 30px;
    padding-top: 10px;
    text-align: center;
    font-size: 13px;
    color: #dddddd;
  }
  
</style>
<link rel="stylesheet" href="./CSS/index.css">
<footer class="custom-footer">
  <div class="container">
    <div class="row">

      <div class="col-md-4 mb-4">
        <h5 class="footer-title">📘 Courses</h5>
        <p>Your trusted place to learn, grow, and upskill. Join us and unlock new opportunities every day.</p>
      </div>

      <div class="col-md-4 mb-4">
        <h5 class="footer-title">Quick Links</h5>
        <ul class="list-unstyled">
          <li><a href="index.php">Home</a></li>
          <li><a href="nouser_courses.php">Courses</a></li>
          <li><a href="About.php">About</a></li>
          <li><a href="contact.php">Contact</a></li>
        </ul>
      </div>

      <div class="col-md-4 mb-4">
        <h5 class="footer-title">Contact</h5>
        <p>Email: support@yourwebsite.com</p>
        <p>Phone: +92 317 5716505</p>
        <p>Location: Islamabad, Pakistan</p>
      </div>
    </div>

    <div class="footer-bottom">
      &copy; <?php echo date("Y"); ?> Courses Platform. All Rights Reserved.
    </div>
  </div>
</footer>
