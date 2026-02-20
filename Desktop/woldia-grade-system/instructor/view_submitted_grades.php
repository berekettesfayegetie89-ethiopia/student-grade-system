<?php include '../includes/header.php'; ?>

<?php if ($current_role !== 'instructor'): header("Location: ../index.php"); exit(); endif; ?>

<?php
$course_id = (int)($_GET['course_id'] ?? 0);

if ($course_id <= 0) {
    header("Location: my_courses.php");
    exit();
}

// Check if this course is submitted and belongs to instructor
$sql_check = "SELECT c.course_code, c.course_name, ca.status
              FROM courses c
              JOIN course_assignments ca ON c.id = ca.course_id
              JOIN instructors i ON ca.instructor_id = i.id
              WHERE c.id = ? AND i.user_id = ? AND ca.status = 'submitted'";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("ii", $course_id, $_SESSION['user_id']);
$stmt->execute();
$course_check = $stmt->get_result()->fetch_assoc();

if (!$course_check) {
    echo "<div class='alert alert-danger'>You cannot view this course or grades are not submitted yet.</div>";
    include '../includes/footer.php';
    exit();
}
?>

<h2 class="mb-4">Final Submitted Grades - <?= htmlspecialchars($course_check['course_code']) ?> : <?= htmlspecialchars($course_check['course_name']) ?></h2>

<div class="alert alert-warning">
    <strong>Finalized!</strong> These grades have been submitted to the registrar office and can no longer be modified.
</div>

<table class="table table-bordered table-striped">
    <thead class="table-primary">
        <tr>
            <th>Student ID</th>
            <th>Full Name</th>
            <th>Score</th>
            <th>Letter Grade</th>
            <th>Grade Point</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $sql = "SELECT s.student_id, s.full_name, g.grade, g.letter_grade, g.grade_point
            FROM enrollments e
            JOIN students s ON e.student_id = s.id
            JOIN grades g ON e.id = g.enrollment_id
            WHERE e.course_id = ?";
    $stmt_grades = $conn->prepare($sql);
    $stmt_grades->bind_param("i", $course_id);
    $stmt_grades->execute();
    $result = $stmt_grades->get_result();

    if ($result->num_rows == 0) {
        echo "<tr><td colspan='5' class='text-center'>No grades recorded.</td></tr>";
    }

    while ($row = $result->fetch_assoc()):
    ?>
        <tr>
            <td><?= htmlspecialchars($row['student_id']) ?></td>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= $row['grade'] ?? '—' ?></td>
            <td><strong><?= $row['letter_grade'] ?? '—' ?></strong></td>
            <td><?= $row['grade_point'] ?? '—' ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<a href="my_courses.php" class="btn btn-secondary mt-3">Back to My Courses</a>

<?php include '../includes/footer.php'; ?>