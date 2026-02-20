<nav class="nav flex-column pt-3">
    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
        <i class="bi bi-house-door"></i> Dashboard
    </a>
    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'my_courses.php' ? 'active' : ''; ?>" href="my_courses.php">
        <i class="bi bi-book-half"></i> My Courses
    </a>
</nav>