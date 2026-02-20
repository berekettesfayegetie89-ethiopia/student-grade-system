<?php include '../includes/header.php'; ?>

<?php if ($current_role !== 'instructor'): ?>
    <?php header("Location: ../index.php"); exit(); ?>
<?php endif; ?>

<h2>Instructor Dashboard</h2>

<div class="row">
    <div class="col-md-6">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5>My Assigned Courses</h5>
                <?php
                $sql = "SELECT COUNT(*) as count FROM course_assignments ca 
                        JOIN instructors i ON ca.instructor_id = i.id 
                        WHERE i.user_id = $current_user_id";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);
                echo "<h3>" . $row['count'] . "</h3>";
                ?>
            </div>
        </div>
    </div>
</div>

<p>Use the sidebar to view your courses and enter grades.</p>

<?php include '../includes/footer.php'; ?>