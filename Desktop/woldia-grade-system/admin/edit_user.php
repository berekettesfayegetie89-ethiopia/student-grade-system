<?php
include '../includes/header.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db_connect.php';

$user_id = (int)$_GET['id'];

if ($user_id <= 0) {
    header("Location: manage_users_full.php");
    exit();
}

// Get current user data
$stmt = $conn->prepare("
    SELECT u.username, u.role, u.is_active,
           COALESCE(s.full_name, i.full_name, 'Admin') AS full_name,
           COALESCE(s.student_id, i.staff_id, '-') AS id_number,
           COALESCE(s.dept_id, i.dept_id, 0) AS dept_id
    FROM users u
    LEFT JOIN students s ON u.id = s.user_id
    LEFT JOIN instructors i ON u.id = i.user_id
    WHERE u.id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header("Location: manage_users_full.php?msg=error");
    exit();
}
?>

<h2 class="mb-4">Edit User</h2>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="process_edit_user.php" method="POST">
            <input type="hidden" name="user_id" value="<?= $user_id ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">ID / Staff Number</label>
                    <input type="text" name="id_number" class="form-control" value="<?= htmlspecialchars($user['id_number']) ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Department</label>
                    <select name="dept_id" class="form-select" required>
                        <?php
                        $res = mysqli_query($conn, "SELECT id, dept_name FROM departments ORDER BY dept_name");
                        while ($d = mysqli_fetch_assoc($res)):
                            $selected = ($d['id'] == $user['dept_id']) ? 'selected' : '';
                        ?>
                            <option value="<?= $d['id'] ?>" <?= $selected ?>>
                                <?= htmlspecialchars($d['dept_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" <?= $user['is_active'] ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= !$user['is_active'] ? 'selected' : '' ?>>Deactivated</option>
                    </select>
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary px-5">Save Changes</button>
                    <a href="manage_users_full.php" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>