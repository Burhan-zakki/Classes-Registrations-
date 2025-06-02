<style>
    /* Navbar background and padding */
.custom-navbar {
  background: linear-gradient(to right, #6a11cb, #2575fc);
  padding: 12px 0;
  z-index: 1030;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

/* White nav text and hover effect */
.custom-navbar .nav-link {
  color: #ffffff !important;
  font-weight: 500;
  margin-left: 15px;
  transition: 0.3s;
}

.custom-navbar .nav-link:hover {
  color: #ffd28d !important;
}

/* Button style */
.join-btn {
  background-color: #ff9d5c;
  color: white !important;
  font-weight: 600;
  border: none;
  border-radius: 8px;
  padding: 8px 16px;
  transition: background-color 0.3s ease;
  text-decoration: none;
}

.join-btn:hover {
  background-color: #ff8126;
  color: white;
}

/* Make space under fixed navbar */
body {
  padding-top: 80px;
}

</style>
<nav class="navbar navbar-expand-lg navbar-light custom-navbar fixed-top">
  <div class="container">
    <a class="navbar-brand text-white fw-bold" href="#">📘 Courses</a>
    <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link text-white" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="nouser_courses.php">Courses</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="About.php">About</a></li>
        
        <li class="nav-item"><a class="nav-link text-white" href="contact.php">Contact</a></li>
      </ul>
      <a class="btn join-btn ms-3" href="register.php">JOIN</a>
      <a class="btn join-btn ms-3" href="login.php">SIGN IN</a>
    </div>
  </div>
</nav>
