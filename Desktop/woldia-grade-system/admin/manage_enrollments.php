<?php include '../includes/header.php'; ?>

<h2 class="mb-4">Manage Student Enrollments</h2>

<!-- Enrollment Form (unchanged) -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <h5>Enroll Student in a Course</h5>
        <form action="process_enrollment.php" method="POST">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Student</label>
                    <select name="student_id" class="form-control" required>
                        <option value="">Select Student</option>
                        <?php
                        $sql = "SELECT s.id, s.student_id, s.full_name FROM students s ORDER BY s.full_name";
                        $result = mysqli_query($conn, $sql);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='{$row['id']}'>{$row['student_id']} - {$row['full_name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Course</label>
                    <select name="course_id" class="form-control" required>
                        <option value="">Select Course</option>
                        <?php
                        $sql = "SELECT c.id, c.course_code, c.course_name, d.dept_name 
                                FROM courses c 
                                JOIN departments d ON c.dept_id = d.id 
                                ORDER BY c.course_code";
                        $result = mysqli_query($conn, $sql);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='{$row['id']}'>{$row['course_code']} - {$row['course_name']} ({$row['dept_name']})</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Academic Year</label>
                    <input type="text" name="year" value="2025-2026" class="form-control" placeholder="e.g. 2025-2026" required>
                </div>

                <div class="col-md-1">
                    <label class="form-label">Semester</label>
                    <input type="number" name="semester" value="1" min="1" max="2" class="form-control" required>
                </div>

                <div class="col-md-1">
                    <button type="submit" class="btn btn-success w-100">Enroll</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Search Form for Enrollments -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-9">
                <label for="search" class="form-label">Search Enrollments</label>
                <input type="text" 
                       name="search" 
                       id="search" 
                       class="form-control form-control-lg" 
                       placeholder="Search by Student ID, Name, Course Code, Course Name, Year or Semester..." 
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

<!-- Current Enrollments List -->
<div class="card shadow-sm">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Current Enrollments</h5>
        <?php if (!empty($_GET['search'])): ?>
            <a href="manage_enrollments.php" class="btn btn-sm btn-light">
                <i class="bi bi-x-circle"></i> Clear Search
            </a>
        <?php endif; ?>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Year</th>
                        <th>Semester</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Base query
                $sql = "
                    SELECT e.id, s.student_id, s.full_name, 
                           c.course_code, c.course_name, 
                           e.academic_year, e.semester
                    FROM enrollments e
                    JOIN students s ON e.student_id = s.id
                    JOIN courses c ON e.course_id = c.id
                ";

                $search = isset($_GET['search']) ? trim($_GET['search']) : '';
                $params = [];
                $types = "";

                if ($search !== '') {
                    $like = "%$search%";
                    $sql .= " WHERE (
                        s.student_id LIKE ? OR
                        s.full_name LIKE ? OR
                        c.course_code LIKE ? OR
                        c.course_name LIKE ? OR
                        e.academic_year LIKE ? OR
                        e.semester LIKE ?
                    )";
                    $params = [$like, $like, $like, $like, $like, $like];
                    $types = "ssssss";
                }

                $sql .= " ORDER BY e.academic_year DESC, e.semester, s.full_name";

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
                            <td><strong><?= htmlspecialchars($row['student_id']) ?></strong></td>
                            <td><?= htmlspecialchars($row['full_name']) ?></td>
                            <td><?= htmlspecialchars($row['course_code']) ?></td>
                            <td><?= htmlspecialchars($row['course_name']) ?></td>
                            <td><?= htmlspecialchars($row['academic_year']) ?></td>
                            <td><?= htmlspecialchars($row['semester']) ?></td>
                            <td>
                                <a href='delete_enrollment.php?id=<?= $row['id'] ?>' 
                                   class='btn btn-danger btn-sm' 
                                   onclick='return confirm("Remove this enrollment? This action cannot be undone.")'>
                                   Remove
                                </a>
                            </td>
                        </tr>
                <?php 
                    endwhile;
                else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <?php if ($search !== ''): ?>
                                No enrollments found matching "<?= htmlspecialchars($search) ?>".
                            <?php else: ?>
                                No enrollments found in the system.
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