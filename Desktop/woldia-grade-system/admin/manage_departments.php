<?php include '../includes/header.php'; ?>

<h2 class="mb-4">Manage Departments</h2>
<?php if (isset($_GET['msg'])): ?>
    <?php
    $msg = $_GET['msg'];
    ?>
    <?php if ($msg === 'deleted'): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3">
            <strong>Success!</strong> Department has been deleted successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($msg === 'cannot_delete'): ?>
        <div class="alert alert-warning alert-dismissible fade show mt-3">
            <strong>Warning!</strong> Cannot delete department – it has assigned courses. Remove courses first.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($msg === 'error'): ?>
        <div class="alert alert-danger alert-dismissible fade show mt-3">
            <strong>Error!</strong> Failed to delete department. Please try again.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Search Form -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-9">
                <label for="search" class="form-label">Search Departments</label>
                <input type="text" 
                       name="search" 
                       id="search" 
                       class="form-control form-control-lg" 
                       placeholder="Search by department code or name (e.g. CS, Computer Science)..." 
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

<!-- Optional Messages Area (for future use) -->
<?php if (isset($_GET['msg'])): ?>
    <?php
    $msg = $_GET['msg'];
    if ($msg === 'added'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong>Success!</strong> Department has been added.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($msg === 'updated'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong>Success!</strong> Department has been updated.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($msg === 'deleted'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong>Success!</strong> Department has been deleted.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($msg === 'error'): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Error!</strong> Operation failed. Please try again.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Add New Department Form -->
<div class="card mb-5 shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Add New Department</h5>
    </div>
    <div class="card-body">
        <form action="process_add_department.php" method="POST">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="dept_code" class="form-control" placeholder="Code (e.g., CS)" required>
                </div>
                <div class="col-md-6">
                    <input type="text" name="dept_name" class="form-control" placeholder="Department Name" required>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Add Department</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Departments List -->
<div class="card shadow-sm">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Departments</h5>
        <?php if (!empty($_GET['search'])): ?>
            <a href="manage_departments.php" class="btn btn-sm btn-light">
                <i class="bi bi-x-circle"></i> Clear Search
            </a>
        <?php endif; ?>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Base query
                $sql = "SELECT * FROM departments";

                $search = isset($_GET['search']) ? trim($_GET['search']) : '';
                $params = [];
                $types = "";

                if ($search !== '') {
                    $like = "%$search%";
                    $sql .= " WHERE (
                        dept_code LIKE ? OR
                        dept_name LIKE ?
                    )";
                    $params = [$like, $like];
                    $types = "ss";
                }

                $sql .= " ORDER BY dept_name";

                // Prepare statement
                $stmt = $conn->prepare($sql);

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
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><strong><?= htmlspecialchars($row['dept_code']) ?></strong></td>
                            <td><?= htmlspecialchars($row['dept_name']) ?></td>
                            <td>
                                <a href='edit_department.php?id=<?= $row['id'] ?>' 
                                   class='btn btn-sm btn-warning'>Edit</a>
                                <a href='delete_department.php?id=<?= $row['id'] ?>' 
                                   class='btn btn-sm btn-danger' 
                                   onclick='return confirm("Delete this department? Departments with courses may cause issues.")'>
                                   Delete
                                </a>
                            </td>
                        </tr>
                <?php 
                    endwhile;
                else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            <?php if ($search !== ''): ?>
                                No departments found matching "<?= htmlspecialchars($search) ?>".
                            <?php else: ?>
                                No departments found in the system.
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