<?php include '../includes/header.php'; ?>

<?php if ($current_role !== 'instructor'): header("Location: ../index.php"); exit(); endif; ?>

<?php
$course_id = (int)$_GET['course_id'];

// Security: Check if this instructor teaches this course
$sql_check = "SELECT c.course_code, c.course_name FROM courses c
              JOIN course_assignments ca ON c.id = ca.course_id
              JOIN instructors i ON ca.instructor_id = i.id
              WHERE c.id = $course_id AND i.user_id = $current_user_id";
$result_check = mysqli_query($conn, $sql_check);
if (mysqli_num_rows($result_check) == 0) {
    echo "<div class='alert alert-danger'>You are not assigned to this course!</div>";
    exit();
}
$course = mysqli_fetch_assoc($result_check);
?>
<?php
// Success or error messages
if (isset($_GET['msg']) && $_GET['msg'] == 'saved') {
    echo "<div class='alert alert-success alert-dismissible fade show'>
            <strong>Success!</strong> Grade saved successfully.
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
          </div>";
}

if (isset($_GET['error']) && $_GET['error'] == 'invalid') {
    echo "<div class='alert alert-danger alert-dismissible fade show'>
            <strong>Error!</strong> Score must be between 0 and 100.
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
          </div>";
}
?>

<h2>Enter Grades - <?php echo $course['course_code'] . " : " . $course['course_name']; ?></h2>

<table class="table table-bordered">
    <thead class="table-light">
        <tr>
            <th>Student ID</th>
            <th>Full Name</th>
            <th>Score (0-100)</th>
            <th>Letter Grade</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Get enrolled students for this course (we use current year/semester - you can improve later)
        $sql = "SELECT s.student_id, s.full_name, e.id as enrollment_id, g.grade, g.letter_grade
                FROM enrollments e
                JOIN students s ON e.student_id = s.id
                LEFT JOIN grades g ON e.id = g.enrollment_id
                WHERE e.course_id = $course_id
                ORDER BY s.full_name";

        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $score = $row['grade'] ?? '';
            $letter = $row['letter_grade'] ?? '';
            echo "<tr>
                <td>{$row['student_id']}</td>
                <td>{$row['full_name']}</td>
                <td>
                    <form action='process_grade.php' method='POST' class='d-inline'>
                    <input type='hidden' name='enrollment_id' value='{$row['enrollment_id']}'>
                    <input type='hidden' name='course_id' value='$course_id'>
                    <input type='number' name='score' value='$score' min='0' max='100' step='0.01' class='form-control' style='width:100px; display:inline-block;'>
                </td>
                <td>
                    <input type='text' name='letter_grade' value='$letter' class='form-control' placeholder='e.g. A' style='width:80px; display:inline-block;'>
                </td>
                <td>
                    <button type='submit' class='btn btn-success btn-sm'>Save</button>
                    </form>
                </td>
            </tr>";
        }
        ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>