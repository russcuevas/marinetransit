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

    // Modify the query to check for user_type = 'cashier'
    $select_admin = $conn->prepare("SELECT * FROM `users` WHERE user_name = ? AND user_password = ? AND user_type = 'cashier'");
    $select_admin->execute([$user_name, $user_password]);

    if ($select_admin->rowCount() > 0) {
        $admin_id = $select_admin->fetch(PDO::FETCH_ASSOC);
        $_SESSION['admin_id'] = $admin_id['user_id'];
        header('location:dashboard.php');
    } else {
        $_SESSION['unsuccess'] = 'Incorrect username or password';
        header('location:cashier_login.php');
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>MARINETRANSIT</title>

    <!-- Custom fonts for this template-->
    <link href="assets/admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="assets/admin/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        #test {
            background-image: url('assets/admin/img/loginbg1.jpeg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 550px;
            width: auto;
        }

        body {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 550px;
            width: auto;
        }

        input {
            width: 300px !important;
            background-color: #d9d9d9;
            border: #000000 2px solid !important;
            height: 50px !important;
        }

        .btn-facebook {
            background-color: #182864 !important;
            width: 300px !important;
            height: 50px !important;
        }

        h1 {
            color: #002960 !important;
            font-family: 'Recursive', 'Times New Roman', Times, serif;
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div>
                    <div class="card-body p-3 my-3">
                        <!-- Nested Row within Card Body -->
                        <form class="user" method="POST" action="">
                            <div class="row justify-content-center" style="border: black 1px solid !important; background: white;">
                                <div class="col-lg-8 d-flex align-items-center justify-content-center">
                                    <div class="p-4">
                                        <div class="row col-lg-12 d-flex align-items-center  justify-content-center">
                                            <img class="img-profile mx-2 rounded-circle" src="assets/admin/img/avatar2.png" style="height: 50px; width: auto;">
                                            <h2>Cashier Login</h2>
                                        </div>
                                        <div class="form-group my-5">
                                            <label for="user_name">Username</label>
                                            <input type="text" class="form-control" id="user_name" name="user_name" placeholder="Username">
                                        </div>
                                        <div class="form-group my-5">
                                            <label for="user_password">Password</label>
                                            <input type="password" class="form-control" name="user_password" id="user_password" placeholder="Password">
                                        </div>
                                        <div class="form-group my-5 d-flex justify-content-end">
                                            <button type="submit" name="login" class="btn btn-info">LOGIN</button>
                                        </div>
                                        <?php if (isset($_SESSION['unsuccess'])): ?>
                                            <div class="alert alert-danger" role="alert">
                                                <?php echo $_SESSION['unsuccess'];
                                                unset($_SESSION['unsuccess']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="row col-lg-12 ">
                                        <div class="col-xl-12" id="test"></div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="assets/admin/vendor/jquery/jquery.min.js"></script>
    <script src="assets/admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="assets/admin/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="assets/admin/js/sb-admin-2.min.js"></script>

</body>

</html>