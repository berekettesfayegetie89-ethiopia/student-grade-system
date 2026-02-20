<?php include '../includes/header.php'; ?>

<h2 class="mb-4">Assign Instructor to Course</h2>
<?php if (isset($_GET['msg'])): ?>
    <?php
    $msg = $_GET['msg'];
    ?>
    <?php if ($msg === 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3">
            <strong>Success!</strong> Instructor has been successfully assigned to the course.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($msg === 'already'): ?>
        <div class="alert alert-warning alert-dismissible fade show mt-3">
            <strong>Warning!</strong> This instructor is already assigned to this course for the selected year and semester.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($msg === 'error'): ?>
        <div class="alert alert-danger alert-dismissible fade show mt-3">
            <strong>Error!</strong> Could not assign the course. Please try again.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="process_assign.php" method="POST">
            <div class="row g-4">

                <!-- Instructor Selection (Searchable + triggers course filter) -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Select Instructor</label>
                    <select name="instructor_id" id="instructorSelect" class="form-select select2" required>
                        <option value="">-- Choose Instructor --</option>
                        <?php
                        $res = mysqli_query($conn, "
                            SELECT i.id, i.full_name, i.dept_id, d.dept_name 
                            FROM instructors i 
                            JOIN departments d ON i.dept_id = d.id 
                            ORDER BY i.full_name
                        ");
                        while ($r = mysqli_fetch_assoc($res)) {
                            echo "<option value='{$r['id']}' 
                                  data-dept='{$r['dept_id']}'>
                                  {$r['full_name']} ({$r['dept_name']})
                                </option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Course Selection (filtered by instructor's department) -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Select Course</label>
                    <select name="course_id" id="courseSelect" class="form-select select2" required>
                        <option value="">-- Select Course (filtered by instructor dept) --</option>
                        <?php
                        $res_course = mysqli_query($conn, "
                            SELECT c.id, c.course_code, c.course_name, d.dept_name, d.id AS dept_id, c.semester 
                            FROM courses c 
                            JOIN departments d ON c.dept_id = d.id 
                            ORDER BY c.course_code
                        ");
                        while ($c = mysqli_fetch_assoc($res_course)) {
                            echo "<option value='{$c['id']}' 
                                  data-dept='{$c['dept_id']}' 
                                  data-semester='{$c['semester']}'>
                                  {$c['course_code']} - {$c['course_name']} ({$c['dept_name']})
                                </option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Academic Year & Semester -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Academic Year</label>
                    <input type="text" name="year" value="2025-2026" class="form-control" required placeholder="e.g. 2025-2026">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Semester</label>
                    <select name="semester" class="form-select" required>
                        <option value="1">Semester 1</option>
                        <option value="2">Semester 2</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-link-45deg"></i> Assign Course
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<!-- Select2 CSS & JS (for searchable + nice dropdowns) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// Wait for document ready
$(document).ready(function() {

    // Initialize Select2 for better UX
    $('#instructorSelect').select2({
        placeholder: "Search instructor by name...",
        allowClear: true,
        width: '100%'
    });

    $('#courseSelect').select2({
        placeholder: "Select course (will be filtered by instructor department)",
        allowClear: true,
        width: '100%'
    });

    // When instructor changes → filter courses by department
    $('#instructorSelect').on('change', function() {
        const selectedDept = $(this).find(':selected').data('dept'); // instructor's department id

        // Reset course selection
        $('#courseSelect').val(null).trigger('change');

        $('#courseSelect option').each(function() {
            const courseDept = $(this).data('dept');

            if (!selectedDept || courseDept == selectedDept) {
                $(this).prop('disabled', false).show();
            } else {
                $(this).prop('disabled', true).hide();
            }
        });

        // Refresh Select2 to reflect changes
        $('#courseSelect').trigger('change');
    });
});
</script>

<?php include '../includes/footer.php'; ?>