<?php include '../includes/header.php'; ?>

<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db_connect.php';

$instructor_id = (int)($_GET['id'] ?? 0);

if ($instructor_id <= 0) {
    header("Location: manage_instructors.php?msg=error");
    exit();
}

// Fetch instructor data
$sql = "
    SELECT i.*, u.username, d.id AS dept_id, d.dept_name 
    FROM instructors i 
    JOIN departments d ON i.dept_id = d.id 
    LEFT JOIN users u ON i.user_id = u.id 
    WHERE i.id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$result = $stmt->get_result();
$instructor = $result->fetch_assoc();

if (!$instructor) {
    header("Location: manage_instructors.php?msg=error");
    exit();
}
?>

<h2 class="mb-4">Edit Instructor</h2>

<div class="card shadow-sm">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">Editing Instructor: <?= htmlspecialchars($instructor['full_name']) ?></h5>
    </div>

    <div class="card-body">
        <form action="process_update_instructor.php" method="POST">
            <input type="hidden" name="instructor_id" value="<?= $instructor_id ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Staff ID</label>
                    <input type="text" 
                           name="staff_id" 
                           class="form-control" 
                           value="<?= htmlspecialchars($instructor['staff_id']) ?>" 
                           required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Full Name</label>
                    <input type="text" 
                           name="full_name" 
                           class="form-control" 
                           value="<?= htmlspecialchars($instructor['full_name']) ?>" 
                           required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Username</label>
                    <input type="text" 
                           name="username" 
                           class="form-control" 
                           value="<?= htmlspecialchars($instructor['username'] ?? '') ?>" 
                           readonly>
                    <small class="text-muted">Username cannot be changed here.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Department</label>
                    <select name="dept_id" class="form-select" required>
                        <?php
                        $res = mysqli_query($conn, "SELECT id, dept_name FROM departments ORDER BY dept_name");
                        while ($d = mysqli_fetch_assoc($res)):
                            $selected = ($d['id'] == $instructor['dept_id']) ? 'selected' : '';
                        ?>
                            <option value="<?= $d['id'] ?>" <?= $selected ?>>
                                <?= htmlspecialchars($d['dept_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                    
                    <a href="manage_instructors.php" class="btn btn-secondary btn-lg px-5">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>