<?php
include '../includes/header.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../config/db_connect.php';

$user_id = (int)$_GET['id'] ?? 0;

if ($user_id <= 0) {
    header("Location: manage_users_full.php?msg=error");
    exit();
}

// Get user basic info (just for display)
$stmt = $conn->prepare("
    SELECT username, role 
    FROM users 
    WHERE id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header("Location: manage_users_full.php?msg=error");
    exit();
}
?>

<h2 class="mb-4">Reset Password</h2>

<div class="card shadow-sm">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">Reset Password for: <strong><?= htmlspecialchars($user['username']) ?></strong> (<?= ucfirst($user['role']) ?>)</h5>
    </div>
    
    <div class="card-body">
        <div class="alert alert-info">
            <i class="bi bi-info-circle-fill me-2"></i>
            Please enter a strong new password for this user. The user will need to use this password on next login.
        </div>

        <form action="process_reset_password.php" method="POST">
            <input type="hidden" name="user_id" value="<?= $user_id ?>">

            <div class="mb-4">
                <label class="form-label fw-bold">New Password</label>
                <div class="input-group input-group-lg">
                    <input type="password" 
                           name="new_password" 
                           id="new_password" 
                           class="form-control" 
                           placeholder="Enter new password" 
                           required 
                           minlength="8">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <small class="text-muted">Minimum 8 characters. Use letters, numbers, and symbols for better security.</small>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Confirm New Password</label>
                <input type="password" 
                       name="confirm_password" 
                       class="form-control form-control-lg" 
                       placeholder="Confirm new password" 
                       required>
            </div>

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    Reset Password
                </button>
                <a href="manage_users_full.php" class="btn btn-secondary btn-lg px-5">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Show/hide password toggle script -->
<script>
document.getElementById('togglePassword').addEventListener('click', function () {
    const passwordInput = document.getElementById('new_password');
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    
    this.querySelector('i').classList.toggle('bi-eye');
    this.querySelector('i').classList.toggle('bi-eye-slash');
});
</script>

<?php include '../includes/footer.php'; ?>