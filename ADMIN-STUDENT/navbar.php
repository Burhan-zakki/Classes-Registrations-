<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid px-4 py-2 d-flex justify-content-between align-items-center">
        <!-- Welcome -->
        <span class="navbar-brand">
            <?php echo htmlspecialchars($_SESSION['role']) . ' - ' . htmlspecialchars($_SESSION['name']); ?>
        </span>

        <!-- Middle: Navigation Buttons -->
        <div class="d-flex flex-wrap gap-2">
            <?php if ($_SESSION['role'] === 'Admin'): ?>
                <a href="dashboard.php" class="btn btn-outline-light btn-sm">Home</a>
                <a href="create_course.php" class="btn btn-outline-light btn-sm">CREATE COURSE</a>
                <a href="All_courses.php" class="btn btn-outline-light btn-sm">ALL COURSES</a>
                <a href="Already_Register.php" class="btn btn-outline-light btn-sm">REGISTERED STUDENTS</a>
                <a href="Notifications.php" class="btn btn-outline-light btn-sm">NOTIFICATIONS</a>
            <?php else: ?>
                <a href="dashboard.php" class="btn btn-outline-light btn-sm">Home</a>
                <a href="courses.php?tab=available" class="btn btn-outline-light tab-btn" onclick="showTab('available', this)">Available Courses</a>
                <a href="courses.php?tab=registered" class="btn btn-outline-light tab-btn" onclick="showTab('registered', this)">Registered Courses</a>
                <a href="S_Notifications.php" class="btn btn-outline-light btn-sm">NOTIFICATIONS</a>
                <?php endif; ?>
        </div>

        <!-- Right: Logout & Back -->
        <div class="d-flex">
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            <a href="javascript:history.back()" class="btn btn-danger btn-sm ms-2">Back</a>
        </div>
    </div>
</nav>
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
