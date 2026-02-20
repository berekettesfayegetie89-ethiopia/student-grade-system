<?php include '../includes/header.php'; ?>

<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db_connect.php';

$student_id = (int)($_GET['id'] ?? 0);

if ($student_id <= 0) {
    header("Location: manage_students.php?msg=error");
    exit();
}

// Fetch student data
$sql = "
    SELECT s.*, u.username, d.id AS dept_id, d.dept_name 
    FROM students s 
    JOIN departments d ON s.dept_id = d.id 
    LEFT JOIN users u ON s.user_id = u.id 
    WHERE s.id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    header("Location: manage_students.php?msg=error");
    exit();
}
?>

<h2 class="mb-4">Edit Student</h2>

<div class="card shadow-sm">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">Editing Student: <?= htmlspecialchars($student['full_name']) ?> (ID: <?= htmlspecialchars($student['student_id']) ?>)</h5>
    </div>

    <div class="card-body">
        <form action="process_update_student.php" method="POST">
            <input type="hidden" name="student_id" value="<?= $student_id ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Student ID</label>
                    <input type="text" 
                           name="student_id_number" 
                           class="form-control" 
                           value="<?= htmlspecialchars($student['student_id']) ?>" 
                           required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Full Name</label>
                    <input type="text" 
                           name="full_name" 
                           class="form-control" 
                           value="<?= htmlspecialchars($student['full_name']) ?>" 
                           required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Username</label>
                    <input type="text" 
                           name="username" 
                           class="form-control" 
                           value="<?= htmlspecialchars($student['username'] ?? '') ?>" 
                           readonly>
                    <small class="text-muted">Username cannot be changed here.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Department</label>
                    <select name="dept_id" class="form-select" required>
                        <?php
                        $res = mysqli_query($conn, "SELECT id, dept_name FROM departments ORDER BY dept_name");
                        while ($d = mysqli_fetch_assoc($res)):
                            $selected = ($d['id'] == $student['dept_id']) ? 'selected' : '';
                        ?>
                            <option value="<?= $d['id'] ?>" <?= $selected ?>>
                                <?= htmlspecialchars($d['dept_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Academic Year</label>
                    <input type="number" 
                           name="year" 
                           class="form-control" 
                           value="<?= htmlspecialchars($student['year']) ?>" 
                           min="1" max="6" required>
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                    
                    <a href="manage_students.php" class="btn btn-secondary btn-lg px-5">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>