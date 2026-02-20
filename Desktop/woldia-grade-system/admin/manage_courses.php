<?php include '../includes/header.php'; ?>

<h2 class="mb-4">Manage Courses</h2>

<!-- Search Form -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-9">
                <label for="search" class="form-label">Search Courses</label>
                <input type="text" 
                       name="search" 
                       id="search" 
                       class="form-control form-control-lg" 
                       placeholder="Search by course code, course name or department..." 
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

<!-- Messages (optional - can be used later for success/error) -->
<?php if (isset($_GET['msg'])): ?>
    <?php
    $msg = $_GET['msg'];
    if ($msg === 'added'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong>Success!</strong> Course has been added.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($msg === 'deleted'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong>Success!</strong> Course has been deleted.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($msg === 'error'): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Error!</strong> Operation failed. Please try again.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Add New Course Form -->
<div class="card mb-5 shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Add New Course</h5>
    </div>
    <div class="card-body">
        <form action="process_add_course.php" method="POST">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="course_code" class="form-control" placeholder="Course Code (e.g., ICT101)" required>
                </div>
                <div class="col-md-5">
                    <input type="text" name="course_name" class="form-control" placeholder="Course Name" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="credit_hours" class="form-control" value="3" min="1" max="6" required>
                </div>
                <div class="col-md-2">
                    <select name="dept_id" class="form-select" required>
                        <?php
                        $res = mysqli_query($conn, "SELECT * FROM departments ORDER BY dept_name");
                        while ($d = mysqli_fetch_assoc($res)) {
                            echo "<option value='{$d['id']}'>{$d['dept_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="semester" class="form-select" required>
                        <option value="1">Semester 1</option>
                        <option value="2">Semester 2</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Add Course</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Courses List -->
<div class="card shadow-sm">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Courses</h5>
        <?php if (!empty($_GET['search'])): ?>
            <a href="manage_courses.php" class="btn btn-sm btn-light">
                <i class="bi bi-x-circle"></i> Clear Search
            </a>
        <?php endif; ?>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Credit Hours</th>
                        <th>Semester</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Base query
                $sql = "
                    SELECT c.*, d.dept_name 
                    FROM courses c 
                    JOIN departments d ON c.dept_id = d.id
                ";

                $search = isset($_GET['search']) ? trim($_GET['search']) : '';
                $params = [];
                $types = "";

                if ($search !== '') {
                    $like = "%$search%";
                    $sql .= " WHERE (
                        c.course_code LIKE ? OR
                        c.course_name LIKE ? OR
                        d.dept_name LIKE ?
                    )";
                    $params = [$like, $like, $like];
                    $types = "sss";
                }

                $sql .= " ORDER BY c.course_code";

                // Prepare and execute safely
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
                            <td><strong><?= htmlspecialchars($row['course_code']) ?></strong></td>
                            <td><?= htmlspecialchars($row['course_name']) ?></td>
                            <td><?= htmlspecialchars($row['dept_name']) ?></td>
                            <td><?= htmlspecialchars($row['credit_hours']) ?></td>
                            <td><?= htmlspecialchars($row['semester']) ?></td>
                            <td>
                                <a href='delete_course.php?id=<?= $row['id'] ?>' 
                                   class='btn btn-danger btn-sm' 
                                   onclick='return confirm("Delete this course? This action cannot be undone.")'>
                                   Delete
                                </a>
                            </td>
                        </tr>
                <?php 
                    endwhile;
                else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <?php if ($search !== ''): ?>
                                No courses found matching "<?= htmlspecialchars($search) ?>".
                            <?php else: ?>
                                No courses found in the system.
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