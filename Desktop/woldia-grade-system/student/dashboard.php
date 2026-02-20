<?php include '../includes/header.php'; ?>

<?php 
// Force student role only
if ($current_role !== 'student') {
    header("Location: ../index.php");
    exit();
}

// Get student details from database
$sql = "SELECT s.id, s.student_id, s.full_name, d.dept_name 
        FROM students s 
        JOIN departments d ON s.dept_id = d.id 
        WHERE s.user_id = $current_user_id";
$result = mysqli_query($conn, $sql);
$student = mysqli_fetch_assoc($result);
?>

<div class="row">
    <div class="col-md-8">
        <h2>Welcome, <?php echo htmlspecialchars($student['full_name']); ?></h2>
        <p class="lead">
            Student ID: <strong><?php echo $student['student_id']; ?></strong><br>
            Department: <strong><?php echo $student['dept_name']; ?></strong>
        </p>
    </div>
    <div class="col-md-4 text-end">
        <a href="view_grades.php" class="btn btn-primary btn-lg">
            <i class="bi bi-table"></i> View My Grades
        </a>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5>Total Enrolled Courses</h5>
                <?php
                $sql = "SELECT COUNT(*) as count FROM enrollments e 
                        JOIN students s ON e.student_id = s.id 
                        WHERE s.user_id = $current_user_id";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);
                echo "<h3>" . $row['count'] . "</h3>";
                ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5>Completed Courses with Grades</h5>
                <?php
                $sql = "SELECT COUNT(*) as count FROM grades g 
                        JOIN enrollments e ON g.enrollment_id = e.id 
                        JOIN students s ON e.student_id = s.id 
                        WHERE s.user_id = $current_user_id";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);
                echo "<h3>" . $row['count'] . "</h3>";
                ?>
            </div>
        </div>
    </div>
</div>

<div class="mt-5">
    <div class="alert alert-info">
        <strong>Note:</strong> Click "View My Grades" to see detailed results, including GPA per semester.
    </div>
</div>

<?php include '../includes/footer.php'; ?>