<?php
// Optional: Process form submission (if you're using PHP to send/store messages)
$successMsg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name    = htmlspecialchars($_POST['name']);
    $email   = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    // For demonstration: just show a message
    $successMsg = "Thank you, $name! Your message has been received.";

    // You can also:
    // - Send email using mail()
    // - Store in database
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Contact Us</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="./CSS/index.css">
  <style>
    .contact-header {
      
      padding: 60px 20px;
      text-align: center;
    }
    .form-control:focus {
      box-shadow: none;
      border-color: #007bff;
    }
  </style>
</head>
<body>
  <?php include 'nav2.php'; ?>
  <div class="contact-header">
    <h1>Contact Us</h1>
    <p class="lead">We’d love to hear from you. Feel free to reach out anytime.</p>
  </div>

  <div class="container py-5">
    <?php if (!empty($successMsg)): ?>
      <div class="alert alert-success"><?php echo $successMsg; ?></div>
    <?php endif; ?>

    <div class="row">
      <div class="col-md-6 mb-4">
        <h4>Our Office</h4>
        <p><strong>Email:</strong> support@VISIONITCORE-SOLUTIONS.com</p>
        <p><strong>Phone:</strong> +92 3175716505</p>
        <p><strong>Location:</strong> Islamabad, Pakistan</p>
        <p>Feel free to send us a message using the form. We’ll get back to you as soon as possible.</p>
      </div>

      <div class="col-md-6">
        <h4>Send a Message</h4>
        <form method="post" action="">
          <div class="mb-3">
            <label for="name" class="form-label">Your Name</label>
            <input type="text" required class="form-control" name="name" id="name">
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Your Email</label>
            <input type="email" required class="form-control" name="email" id="email">
          </div>

          <div class="mb-3">
            <label for="subject" class="form-label">Subject</label>
            <input type="text" required class="form-control" name="subject" id="subject">
          </div>

          <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea required class="form-control" name="message" id="message" rows="5"></textarea>
          </div>

          <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
