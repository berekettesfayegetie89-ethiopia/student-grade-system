<?php include '../includes/header.php'; ?>

<?php
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db_connect.php';

$dept_id = (int)($_GET['id'] ?? 0);

if ($dept_id <= 0) {
    header("Location: manage_departments.php?msg=error");
    exit();
}

// Fetch current department data
$stmt = $conn->prepare("SELECT dept_code, dept_name FROM departments WHERE id = ?");
$stmt->bind_param("i", $dept_id);
$stmt->execute();
$result = $stmt->get_result();
$department = $result->fetch_assoc();

if (!$department) {
    header("Location: manage_departments.php?msg=error");
    exit();
}
?>

<h2 class="mb-4">Edit Department</h2>

<div class="card shadow-sm">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">Editing Department ID: <?= $dept_id ?></h5>
    </div>
    
    <div class="card-body">
        <form action="process_update_department.php" method="POST">
            <input type="hidden" name="dept_id" value="<?= $dept_id ?>">

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Department Code</label>
                    <input type="text" 
                           name="dept_code" 
                           class="form-control" 
                           value="<?= htmlspecialchars($department['dept_code']) ?>" 
                           required 
                           maxlength="10">
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-bold">Department Name</label>
                    <input type="text" 
                           name="dept_name" 
                           class="form-control" 
                           value="<?= htmlspecialchars($department['dept_name']) ?>" 
                           required>
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                    
                    <a href="manage_departments.php" class="btn btn-secondary btn-lg px-5">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>