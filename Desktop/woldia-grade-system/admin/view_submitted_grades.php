<?php include '../includes/header.php'; ?>

<?php if ($current_role !== 'admin'): header("Location: ../index.php"); exit(); endif; ?>

<h2 class="mb-4">View Submitted Grades (Grouped by Department)</h2>

<div class="alert alert-info mb-4">
    <i class="bi bi-info-circle me-2"></i>
    This page shows all courses where instructors have submitted final grades to the registrar. 
    Grades are grouped by department for easier review. Use the search to find specific students or courses.
</div>

<!-- Search Form -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-9">
                <label for="search" class="form-label">Search Submitted Grades</label>
                <input type="text" 
                       name="search" 
                       id="search" 
                       class="form-control form-control-lg" 
                       placeholder="Search by Student Name, Course Code, Course Name, Department..." 
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Submitted Grades Grouped by Department -->
<?php
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_like = "%$search%";

// Query for departments with submitted courses
$sql_depts = "
    SELECT DISTINCT d.id AS dept_id, d.dept_name 
    FROM departments d
    JOIN courses c ON d.id = c.dept_id
    JOIN course_assignments ca ON c.id = ca.course_id
    WHERE ca.status = 'submitted'
    ORDER BY d.dept_name
";
$result_depts = mysqli_query($conn, $sql_depts);

if (mysqli_num_rows($result_depts) == 0) {
    echo "<div class='alert alert-warning'>No submitted grades available yet.</div>";
} else {
    while ($dept = mysqli_fetch_assoc($result_depts)):
        $dept_id = $dept['dept_id'];
?>
        <div class="card mb-5 shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Department: <?= htmlspecialchars($dept['dept_name']) ?></h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Score</th>
                                <th>Letter Grade</th>
                                <th>Grade Point</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sql_grades = "
                            SELECT c.course_code, c.course_name, 
                                   s.student_id, s.full_name, 
                                   g.grade, g.letter_grade, g.grade_point
                            FROM course_assignments ca
                            JOIN courses c ON ca.course_id = c.id
                            JOIN enrollments e ON ca.course_id = e.course_id
                            JOIN students s ON e.student_id = s.id
                            LEFT JOIN grades g ON e.id = g.enrollment_id
                            WHERE ca.status = 'submitted' AND c.dept_id = ?
                            AND (s.full_name LIKE ? OR s.student_id LIKE ? OR c.course_code LIKE ? OR c.course_name LIKE ?)
                            ORDER BY c.course_code, s.full_name
                        ";
                        $stmt = $conn->prepare($sql_grades);
                        $stmt->bind_param("issss", $dept_id, $search_like, $search_like, $search_like, $search_like);
                        $stmt->execute();
                        $result_grades = $stmt->get_result();

                        $current_course = '';
                        $has_grades = false;

                        while ($row = $result_grades->fetch_assoc()):
                            $has_grades = true;

                            // Group by course
                            if ($current_course !== $row['course_code']): 
                                if ($current_course !== '') echo "</tbody>"; // Close previous
                        ?>
                                <tr class="table-primary">
                                    <td colspan="7"><strong>Course: <?= htmlspecialchars($row['course_code']) ?> - <?= htmlspecialchars($row['course_name']) ?></strong></td>
                                </tr>
                        <?php
                                $current_course = $row['course_code'];
                            endif;
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($row['course_code']) ?></td>
                                <td><?= htmlspecialchars($row['course_name']) ?></td>
                                <td><?= htmlspecialchars($row['student_id']) ?></td>
                                <td><?= htmlspecialchars($row['full_name']) ?></td>
                                <td><?= $row['grade'] ?? '—' ?></td>
                                <td><strong><?= $row['letter_grade'] ?? '—' ?></strong></td>
                                <td><?= $row['grade_point'] ?? '—' ?></td>
                            </tr>
                        <?php endwhile; ?>

                        <?php if (!$has_grades): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No submitted grades in this department yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
<?php } ?>

<?php include '../includes/footer.php'; ?>