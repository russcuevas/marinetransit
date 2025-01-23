<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>MARINETRANSIT</title>

    <!-- CSS -->
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
    <link rel="stylesheet" href="assets/user/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/user/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/user/css/form-elements.css">
    <link rel="stylesheet" href="assets/user/css/style.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Favicon and touch icons -->
    <link rel="shortcut icon" href="assets/admin/img/logo.png">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/admin/img/logo.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/admin/img/logo.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/admin/img/logo.png">
    <link rel="apple-touch-icon-precomposed" href="assets/admin/img/logo.png">

    <style type="text/css">
        body {
            background-image: url('assets/user/img/backgrounds/bg2.jpg');
            background-size: cover;
            /* To cover the entire page */
            background-position: center;
            /* Center the image */
            background-repeat: no-repeat;
            /* Prevent image repetition */
            height: 90vh;
            /* Full viewport height */
            margin: 0;
            background-attachment: fixed;
        }

        ul li {
            margin-left: 20px;
            margin-right: 20px;
            padding-left: 10px;
            padding-right: 10px;
        }

        ul li.active {
            background: #bbddf5;
            border-radius: 5px;
            color: black !important;
        }

        ul li a {
            color: white;
        }

        ul li.active a {
            color: black !important;
        }
    </style>

</head>

<body id="page-top">
    <div class="col-xl-12 col-lg-12" style="background: black; height: 50px; display: flex; align-items: center; margin-bottom: 30px; position: fixed; z-index: 9999;">
        <img class="img-profile" src="assets/user/img/backgrounds/icon.jpg" style="height: 40px; width: auto; border-radius: 100%;">
        <h4 style="color: white; margin-left: 10px">MARINETRANSIT</h4>

        <!-- Navbar -->
        <nav class="navbar" style="background: black!important; margin-left: 30px;">
            <div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="navbar-collapse">
                    <ul class="nav navbar-nav navbar-left" style="display: flex; align-self: center!important; color: #000000!important;">
                        <li class="<?php echo ($page == "index") ? "active" : ''; ?>"><a href="index.php">HOME</a></li>
                        <li class="<?php echo ($page == "aboutus") ? "active" : ''; ?>"><a href="aboutus.php">ABOUT</a></li>
                        <li class="<?php echo ($page == "inspiration") ? "active" : ''; ?>"><a href="inspiration.php">INSPIRATION</a></li>
                        <li class="<?php echo ($page == "contactus") ? "active" : ''; ?>"><a href="contactus.php">CONTACT US</a></li>
                        <li class="<?php echo ($page == "policy") ? "active" : ''; ?>"><a href="policy.php">PRIVACY POLICY</a></li>
                        <li class="<?php echo ($page == "guidelines") ? "active" : ''; ?>"><a href="guidelines.php">FAQS</a></li>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>
    </div>