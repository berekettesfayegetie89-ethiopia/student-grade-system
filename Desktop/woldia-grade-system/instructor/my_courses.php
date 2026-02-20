<?php include '../includes/header.php'; ?>

<?php if ($current_role !== 'instructor'): header("Location: ../index.php"); exit(); endif; ?>

<h2 class="mb-4">My Assigned Courses</h2>
<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'submitted'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong>Success!</strong> Grades have been successfully submitted to the registrar and are now locked.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($_GET['msg'] === 'error'): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Error!</strong> Could not submit grades. Please try again.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="alert alert-info mb-4">
    <i class="bi bi-info-circle me-2"></i>
    Once you submit grades to the registrar, they become final and cannot be changed.
</div>

<table class="table table-striped table-hover">
    <thead class="table-primary">
        <tr>
            <th>Course Code</th>
            <th>Course Name</th>
            <th>Department</th>
            <th>Semester</th>
            <th>Academic Year</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $sql = "SELECT c.course_code, c.course_name, d.dept_name, 
                   ca.semester, ca.academic_year, ca.status, c.id as course_id
            FROM course_assignments ca
            JOIN courses c ON ca.course_id = c.id
            JOIN instructors i ON ca.instructor_id = i.id
            JOIN departments d ON c.dept_id = d.id
            WHERE i.user_id = $current_user_id
            ORDER BY ca.academic_year DESC, ca.semester";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 0) {
        echo "<tr><td colspan='7' class='text-center text-muted py-4'>No courses assigned yet.</td></tr>";
    }

    while ($row = mysqli_fetch_assoc($result)):
        $is_submitted = ($row['status'] === 'submitted');
    ?>
        <tr>
            <td><strong><?= htmlspecialchars($row['course_code']) ?></strong></td>
            <td><?= htmlspecialchars($row['course_name']) ?></td>
            <td><?= htmlspecialchars($row['dept_name']) ?></td>
            <td><?= $row['semester'] ?></td>
            <td><?= htmlspecialchars($row['academic_year']) ?></td>
            <td>
                <?php if ($is_submitted): ?>
                    <span class="badge bg-success">Submitted to Registrar</span>
                <?php else: ?>
                    <span class="badge bg-warning">In Progress</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($is_submitted): ?>
                    <a href="view_submitted_grades.php?course_id=<?= $row['course_id'] ?>" 
                       class="btn btn-outline-info btn-sm">
                        <i class="bi bi-eye"></i> View Final Grades
                    </a>
                <?php else: ?>
                    <!-- Still editable -->
                    <a href="enter_grades.php?course_id=<?= $row['course_id'] ?>" 
                       class="btn btn-primary btn-sm me-2">
                        <i class="bi bi-pencil"></i> Enter/Edit Grades
                    </a>

                    <a href="process_submit_grades.php?course_id=<?= $row['course_id'] ?>" 
                       class="btn btn-success btn-sm"
                       onclick="return confirm('Are you sure? After submission, grades cannot be changed!')">
                        <i class="bi bi-check-circle"></i> Submit to Registrar
                    </a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>