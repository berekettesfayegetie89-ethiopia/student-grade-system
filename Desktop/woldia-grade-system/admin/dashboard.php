<?php include '../includes/header.php'; ?>

<h2 class="mb-4">Admin Dashboard</h2>
<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5>Total Departments</h5>
                <?php
                $sql = "SELECT COUNT(*) as count FROM departments";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);
                echo "<h3>" . $row['count'] . "</h3>";
                ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5>Total Courses</h5>
                <?php
                $sql = "SELECT COUNT(*) as count FROM courses";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);
                echo "<h3>" . $row['count'] . "</h3>";
                ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info mb-3">
            <div class="card-body">
                <h5>Total Students</h5>
                <?php
                $sql = "SELECT COUNT(*) as count FROM students";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);
                echo "<h3>" . $row['count'] . "</h3>";
                ?>
            </div>
        </div>
    </div>
</div>

<p class="mt-4">Use the sidebar menu to manage the university system.</p>

<?php include '../includes/footer.php'; ?>