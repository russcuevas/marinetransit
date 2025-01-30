<?php
include '../connection/database.php';

// SESSION
session_start();
if (isset($_SESSION['admin_id'])) {
    header('location:dashboard.php');
}

if (isset($_POST['login'])) {
    $user_name = $_POST['user_name'];
    $user_password = $_POST['user_password'];

    // Modify the query to check for user_type = 'admin'
    $select_admin = $conn->prepare("SELECT * FROM `users` WHERE user_name = ? AND user_password = ? AND user_type = 'admin'");
    $select_admin->execute([$user_name, $user_password]);

    if ($select_admin->rowCount() > 0) {
        $admin_id = $select_admin->fetch(PDO::FETCH_ASSOC);
        $_SESSION['admin_id'] = $admin_id['user_id'];
        header('location:dashboard.php');
    } else {
        $_SESSION['unsuccess'] = 'Incorrect username or password';
        header('location:admin_login.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | MARINETRANSIT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url('assets/admin/img/bg.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .login-container {
            max-width: 500px;
            width: 100%;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }


        .login-container h2 {
            margin-bottom: 20px;
            color: #002960;
        }

        .form-control {
            height: 45px;
            border-radius: 5px;
        }

        .btn-login {
            background: #0053a0;
            color: white;
            border: none;
            height: 45px;
            border-radius: 5px;
            width: 100%;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: #003b75;
        }

        .alert {
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <img src="assets/admin/img/system_dashboard.jpg" alt="Admin Avatar" class="mb-3" width="80">
        <h2>Admin Login</h2>

        <?php if (isset($_SESSION['unsuccess'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $_SESSION['unsuccess'];
                unset($_SESSION['unsuccess']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <input type="text" name="user_name" class="form-control" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="password" name="user_password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-login" name="login">LOGIN</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>