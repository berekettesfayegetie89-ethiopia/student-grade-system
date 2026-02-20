<?php include '../includes/header.php'; ?>

<h2 class="mb-4">Manage Students</h2>
<?php if (isset($_GET['msg'])): ?>
    <?php
    $msg = $_GET['msg'];
    ?>
    <?php if ($msg === 'updated'): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3">
            <strong>Success!</strong> Student information has been updated successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($msg === 'deleted'): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3">
            <strong>Success!</strong> Student has been deleted.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($msg === 'duplicate_id'): ?>
        <div class="alert alert-warning alert-dismissible fade show mt-3">
            <strong>Warning!</strong> This Student ID already exists. Please use a different ID.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($msg === 'error'): ?>
        <div class="alert alert-danger alert-dismissible fade show mt-3">
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
                <label for="search" class="form-label">Search Students</label>
                <input type="text" 
                       name="search" 
                       id="search" 
                       class="form-control form-control-lg" 
                       placeholder="Search by Student ID, Name, Username or Department..." 
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

<!-- Messages (optional - if you want to show messages later) -->
<?php if (isset($_GET['msg'])): ?>
    <?php
    $msg = $_GET['msg'];
    if ($msg === 'deleted'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong>Success!</strong> Student has been deleted.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Students Table -->
<div class="card shadow-sm">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Students</h5>
        <?php if (!empty($_GET['search'])): ?>
            <a href="manage_students.php" class="btn btn-sm btn-light">
                <i class="bi bi-x-circle"></i> Clear Search
            </a>
        <?php endif; ?>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>Student ID</th>
                        <th>Full Name</th>
                        <th>Department</th>
                        <th>Year</th>
                        <th>Username</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Base query
                $sql = "
                    SELECT s.*, d.dept_name, u.username 
                    FROM students s 
                    JOIN departments d ON s.dept_id = d.id 
                    LEFT JOIN users u ON s.user_id = u.id
                ";

                $search = isset($_GET['search']) ? trim($_GET['search']) : '';
                $params = [];
                $types = "";

                if ($search !== '') {
                    $like = "%$search%";
                    $sql .= " WHERE (
                        s.student_id LIKE ? OR
                        s.full_name LIKE ? OR
                        u.username LIKE ? OR
                        d.dept_name LIKE ?
                    )";
                    $params = [$like, $like, $like, $like];
                    $types = "ssss";
                }

                $sql .= " ORDER BY s.full_name";

                // Prepare and execute
                $stmt = $conn->prepare($sql);

                // Safety check
                if (!$stmt) {
                    echo "<div class='alert alert-danger'>Database error: " . htmlspecialchars($conn->error) . "</div>";
                    include '../includes/footer.php';
                    exit;
                }

                if ($search !== '' && $types) {
                    $stmt->bind_param($types, ...$params);
                }

                $stmt->execute();
                $result = $stmt->get_result();

                $has_results = $result->num_rows > 0;

                if ($has_results):
                    while ($row = $result->fetch_assoc()):
                ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['student_id']) ?></strong></td>
                            <td><?= htmlspecialchars($row['full_name']) ?></td>
                            <td><?= htmlspecialchars($row['dept_name']) ?></td>
                            <td><?= htmlspecialchars($row['year']) ?></td>
                            <td><?= htmlspecialchars($row['username'] ?: '—') ?></td>
                            <td>
    <div class="btn-group btn-group-sm">
        <a href="edit_student.php?id=<?= $row['id'] ?>" 
           class="btn btn-warning">
           Edit
        </a>
        <a href="delete_student.php?id=<?= $row['id'] ?>" 
           class="btn btn-danger"
           onclick="return confirm('Delete this student? This action cannot be undone.')">
           Delete
        </a>
    </div>
</td>
                        </tr>
                <?php 
                    endwhile;
                else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <?php if ($search !== ''): ?>
                                No students found matching "<?= htmlspecialchars($search) ?>".
                            <?php else: ?>
                                No students found in the system.
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