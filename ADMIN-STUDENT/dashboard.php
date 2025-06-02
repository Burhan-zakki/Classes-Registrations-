<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || !isset($_SESSION['name']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: url('./img/12.png') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            min-height: 100vh;
        }

        .navbar {
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(12px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        }

        .navbar-brand {
            color: #ffffff;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .btn-outline-light {
            border-radius: 30px;
            padding: 6px 14px;
            font-size: 0.85rem;
        }

        .btn-danger, .btn-outline-light {
            transition: transform 0.2s ease;
        }

        .btn-danger:hover, .btn-outline-light:hover {
            transform: scale(1.05);
        }

        .tab-btn.active {
            font-weight: bold;
            text-decoration: underline;
        }

        .tab-content {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            backdrop-filter: blur(10px);
            color: white;
        }

        .container {
            margin-top: 30px;
        }

        a.btn {
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }

            .navbar .d-flex.gap-2 {
                flex-direction: column;
                gap: 0.5rem;
            }

            .navbar .d-flex:last-child {
                flex-direction: column;
                gap: 0.5rem;
                margin-top: 1rem;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->

<?php include 'navbar.php'; ?>


<!-- Main content -->
<div class="container">
    <?php if ($_SESSION['role'] !== 'Admin'): ?>
          <?php include 'Manage_Registered_courses.php'; ?>
    <?php else: ?>
        <?php include 'Manage_courses.php'; ?>
        
    <?php endif; ?>
</div>

<!-- Scripts -->
<script>
function showTab(tabId, clickedElement = null) {
    document.querySelectorAll('.tab-content').forEach(el => {
        el.style.display = 'none';
        el.classList.remove('active');
    });

    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));

    const activeContent = document.getElementById(tabId);
    if (activeContent) {
        activeContent.style.display = 'block';
        activeContent.classList.add('active');
    }

    if (clickedElement) {
        clickedElement.classList.add('active');
    }
}

window.onload = function() {
    const params = new URLSearchParams(window.location.search);
    const tab = params.get('tab');
    if (tab === 'registered' || tab === 'available') {
        showTab(tab);
    } else {
        showTab('available');
    }
};
</script>

<!-- SweetAlert -->
<?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        title: 'Deleted!',
        text: 'The course has been deleted successfully.',
        icon: 'success',
        confirmButtonText: 'OK'
    });
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.pathname);
    }
</script>
<?php endif; ?>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
