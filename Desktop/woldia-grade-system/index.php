<?php
// Start session on every page that needs login
session_start();

// If already logged in, send to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Woldia University - Student Grade Management System</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: Arial, sans-serif;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
            color: #0d6efd;
            font-size: 28px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="logo">Woldia University</div>
    <h4 class="text-center mb-4">Grade Management System</h4>

    <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?php
        switch ($_GET['error']) {
            case 'empty':
                echo "Please enter both username and password!";
                break;
            case 'invalid':
                echo "Invalid username or password!";
                break;
            case 'deactivated':
                echo "<strong>Account Deactivated!</strong> Your account has been deactivated by the administrator. Please contact support.";
                break;
            default:
                echo "An error occurred. Please try again.";
        }
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['logout'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        You have been successfully logged out.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

    <form action="process_login.php" method="POST">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>

    <p class="text-center mt-3 text-muted">
       
    </p>
</div>

</body>
</html>