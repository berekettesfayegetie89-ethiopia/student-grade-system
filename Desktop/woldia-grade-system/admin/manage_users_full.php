<?php include '../includes/header.php'; ?>

<h2 class="mb-4">Manage System Users</h2>

<!-- Messages -->
<?php if (isset($_GET['msg'])): ?>
    <?php $msg = $_GET['msg']; ?>
    <?php if ($msg === 'updated'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong>Success!</strong> User updated successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($msg === 'deactivated'): ?>
        <div class="alert alert-warning alert-dismissible fade show">
            <strong>Info:</strong> User has been deactivated.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($msg === 'activated'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong>Success!</strong> User has been activated again.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($msg === 'reset_custom'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong>Success!</strong> Password has been successfully updated.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($msg === 'error'): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Error!</strong> Operation failed. Please try again.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Search Form -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-9">
                <label for="search" class="form-label">Search Users</label>
                <input type="text" 
                       name="search" 
                       id="search" 
                       class="form-control form-control-lg" 
                       placeholder="Search by name, username, ID or department..." 
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

<!-- Add New User Form -->
<div class="card mb-5 shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Add New User</h5>
    </div>
    <div class="card-body">
        <form action="process_add_user.php" method="POST">
            <div class="row g-3">
                <div class="col-md-4">
                    <label>Role</label>
                    <select name="role" class="form-select" required>
                        <option value="student">Student</option>
                        <option value="instructor">Instructor</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label>ID (Student/Staff)</label>
                    <input type="text" name="id_number" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label>Department</label>
                    <select name="dept_id" class="form-select" required>
                        <?php
                        $sql = "SELECT * FROM departments ORDER BY dept_name";
                        $result = mysqli_query($conn, $sql);
                        while ($dept = mysqli_fetch_assoc($result)) {
                            echo "<option value='{$dept['id']}'>{$dept['dept_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-success">Create User</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Users List -->
<div class="card shadow-sm">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All System Users</h5>
        <?php if (!empty($_GET['search'])): ?>
            <a href="manage_users_full.php" class="btn btn-sm btn-light">
                <i class="bi bi-x-circle"></i> Clear Search
            </a>
        <?php endif; ?>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Full Name</th>
                        <th>ID / Staff No</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Base query - we repeat CASE logic in WHERE to avoid alias problem
                $sql = "
                    SELECT u.id, u.username, u.role, u.is_active,
                           CASE 
                               WHEN u.role = 'student' THEN s.full_name
                               WHEN u.role = 'instructor' THEN i.full_name
                               ELSE 'Admin'
                           END AS full_name,
                           CASE 
                               WHEN u.role = 'student' THEN s.student_id
                               WHEN u.role = 'instructor' THEN i.staff_id
                               ELSE '-'
                           END AS user_id_number,
                           d.dept_name
                    FROM users u
                    LEFT JOIN students s ON u.id = s.user_id
                    LEFT JOIN instructors i ON u.id = i.user_id
                    LEFT JOIN departments d ON (s.dept_id = d.id OR i.dept_id = d.id)
                ";

                $search = isset($_GET['search']) ? trim($_GET['search']) : '';
                $params = [];
                $types = "";

                if ($search !== '') {
                    $like = "%$search%";
                    $sql .= " WHERE (
                        u.username LIKE ? OR
                        (u.role = 'student' AND s.full_name LIKE ?) OR
                        (u.role = 'instructor' AND i.full_name LIKE ?) OR
                        (u.role = 'student' AND s.student_id LIKE ?) OR
                        (u.role = 'instructor' AND i.staff_id LIKE ?) OR
                        d.dept_name LIKE ?
                    )";
                    $params = [$like, $like, $like, $like, $like, $like];
                    $types = "ssssss";
                }

                $sql .= " ORDER BY u.role, full_name";

                // Prepare statement
                $stmt = $conn->prepare($sql);
                
                // If prepare failed → show error (for debugging)
                if (!$stmt) {
                    echo "<div class='alert alert-danger'>Database error: " . htmlspecialchars($conn->error) . "</div>";
                    include '../includes/footer.php';
                    exit;
                }

                // Bind parameters if search exists
                if ($search !== '' && $types) {
                    $stmt->bind_param($types, ...$params);
                }

                $stmt->execute();
                $result = $stmt->get_result();

                $counter = 1;
                $has_results = $result->num_rows > 0;

                while ($row = $result->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= $counter++ ?></td>
                        <td><strong><?= htmlspecialchars($row['username']) ?></strong></td>
                        <td><?= ucfirst($row['role']) ?></td>
                        <td><?= htmlspecialchars($row['full_name'] ?: '—') ?></td>
                        <td><?= htmlspecialchars($row['user_id_number'] ?: '—') ?></td>
                        <td><?= htmlspecialchars($row['dept_name'] ?: '—') ?></td>
                        <td>
                            <?php if ($row['is_active']): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Deactivated</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-warning">Edit</a>
                                
                                <?php if ($row['is_active']): ?>
                                    <a href="process_user_status.php?id=<?= $row['id'] ?>&action=deactivate" 
                                       class="btn btn-outline-danger"
                                       onclick="return confirm('Deactivate this user? They will not be able to login.')">
                                       Deactivate
                                    </a>
                                <?php else: ?>
                                    <a href="process_user_status.php?id=<?= $row['id'] ?>&action=activate" 
                                       class="btn btn-outline-success">Activate</a>
                                <?php endif; ?>

                                <a href="reset_password.php?id=<?= $row['id'] ?>" 
                                   class="btn btn-outline-info">
                                   <i class="bi bi-key"></i> Reset PW
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>

                <?php if (!$has_results): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <?php if ($search !== ''): ?>
                                No users found matching "<?= htmlspecialchars($search) ?>".
                            <?php else: ?>
                                No users found in the system.
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>