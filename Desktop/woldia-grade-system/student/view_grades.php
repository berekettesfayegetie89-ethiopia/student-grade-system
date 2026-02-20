<?php include '../includes/header.php'; ?>

<?php 
if ($current_role !== 'student') {
    header("Location: ../index.php");
    exit();
}

// Get student ID
$sql = "SELECT id FROM students WHERE user_id = $current_user_id";
$result = mysqli_query($conn, $sql);
$student_row = mysqli_fetch_assoc($result);
$student_id = $student_row['id'];
?>

<h2>My Grades</h2>

<?php
// Get all enrollments and grades grouped by academic year and semester
$sql = "SELECT ca.academic_year, ca.semester,
               c.course_code, c.course_name, c.credit_hours,
               g.grade, g.letter_grade, g.grade_point
        FROM enrollments e
        JOIN courses c ON e.course_id = c.id
        JOIN course_assignments ca ON c.id = ca.course_id AND e.academic_year = ca.academic_year AND e.semester = ca.semester
        LEFT JOIN grades g ON e.id = g.enrollment_id
        WHERE e.student_id = $student_id
        ORDER BY ca.academic_year DESC, ca.semester DESC, c.course_code";

$result = mysqli_query($conn, $sql);

$current_year = '';
$current_semester = '';

while ($row = mysqli_fetch_assoc($result)) {
    $year_sem = $row['academic_year'] . " - Semester " . $row['semester'];

    // New semester header
    if ($year_sem !== $current_year . " - Semester " . $current_semester) {
        // Close previous table if exists
        if ($current_year !== '') {
            // Calculate and show GPA
            echo "<tr class='table-success'><td colspan='4'><strong>Semester GPA</strong></td><td><strong>" . number_format($total_gp / $total_cr, 2) . "</strong></td></tr>";
            echo "</tbody></table></div>";
        }

        echo "<div class='mt-5'>
                <h4>$year_sem</h4>
                <table class='table table-bordered table-striped'>
                <thead class='table-primary'>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Credit Hours</th>
                        <th>Score</th>
                        <th>Letter Grade</th>
                        <th>Grade Point</th>
                    </tr>
                </thead>
                <tbody>";

        $current_year = $row['academic_year'];
        $current_semester = $row['semester'];
        $total_gp = 0;  // total grade points * credit
        $total_cr = 0;  // total credits
    }

    $score = $row['grade'] ?? '-';
    $letter = $row['letter_grade'] ?? '-';
    $gp = $row['grade_point'] ?? 0;
    $cr = $row['credit_hours'];

    echo "<tr>
            <td>{$row['course_code']}</td>
            <td>{$row['course_name']}</td>
            <td>$cr</td>
            <td>$score</td>
            <td><strong>$letter</strong></td>
            <td>$gp</td>
          </tr>";

    // Add to GPA calculation
    if ($gp > 0) {
        $total_gp += $gp * $cr;
        $total_cr += $cr;
    }
}

// Final semester GPA
if ($current_year !== '') {
    $gpa = $total_cr > 0 ? $total_gp / $total_cr : 0;
    echo "<tr class='table-success'>
            <td colspan='4'><strong>Semester GPA</strong></td>
            <td colspan='2'><strong>" . number_format($gpa, 2) . "</strong></td>
          </tr>";
    echo "</tbody></table></div>";
}

if (mysqli_num_rows(mysqli_query($conn, $sql)) == 0) {
    echo "<div class='alert alert-warning'>No enrolled courses or grades yet.</div>";
}
?>

<?php include '../includes/footer.php'; ?>