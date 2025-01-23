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
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="assets/admin/css/sb-admin-2.min.css" rel="stylesheet">
    <!-- Custom styles for this page -->
    <link href="assets/admin/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">


    <link rel="shortcut icon" href="assets/admin/img/icon.jpg">


    <style type="text/css">
        .navbar-nav span,
        .navbar-nav i {
            color: #000 !important;
            font-size: 15px !important;
        }

        .active {
            background: #1995ed;
        }

        .sidebar-dark .nav-item .nav-link[data-toggle=collapse]::after {
            color: #000;
        }
    </style>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background: #d5d6d7; color: black!important;">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-start" href="admin" style="background: #afdaf9">

                <img class="img-profile rounded-circle" src="assets/admin/img/logo.png" style="height: 50px; width: auto;">
                <div class="sidebar-brand-text mx-3" style="color: black!important">ShortName</div>
            </a>
            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item ">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-fw fa-ship"></i>
                    <span>Admin</span></a>
            </li>


            <!-- Divider -->
            <hr class="sidebar-divider">


            <li class="nav-item">
                <a class="nav-link active" href="dashboard.php">
                    <i class="fas fa-fw fa-home"></i>
                    <span>Home</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="schedule.php">
                    <i class="fas fa-fw fa-calendar"></i>
                    <span>Schedule</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="route.php">
                    <i class="fa fa-fw fa-route"></i>
                    <span>Route</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="ship.php">
                    <i class="fas fa-fw fa-ship"></i>
                    <span>Vessel</span></a>
            </li>


            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTickets"
                    aria-expanded="true" aria-controls="collapseTickets">
                    <i class="fa fa-file"></i>
                    <span>Tickets</span>
                </a>
                <div id="collapseTickets" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="ticket_free.php">Free</a>
                        <a class="collapse-item" href="ticket_regular.php">Regular</a>
                        <a class="collapse-item" href="ticket_children.php">Children</a>
                        <a class="collapse-item" href="ticket_student.php">Student</a>
                        <a class="collapse-item" href="ticket_senior.php">Senior</a>
                        <a class="collapse-item" href="ticket_cargo.php">Cargo</a>
                    </div>
                </div>
            </li>


            <li class="nav-item">
                <a class="nav-link" href="payment.php">
                    <i class="fa fa-fw fa-dollar-sign"></i>
                    <span>Payments</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="report.php">
                    <i class="fas fa-fw fa-file-alt"></i>
                    <span>Record</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Settings</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="setting.php">System Information</a>
                        <a class="collapse-item" href="setting_port.php">Port Locations</a>
                        <a class="collapse-item" href="setting_accom.php">Accomodation</a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Nav Item - Tables -->
            <li class="nav-item">
                <a class="nav-link" href="auth/logout.php">
                    <i class="fa fa-fw fa-sign-out-alt"></i>
                    <span>Logout</span></a>
            </li>

        </ul>
        <!-- End of Sidebar -->


        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light topbar mb-4 static-top shadow" style="background: #afdaf9">

                    <h3 class="text-white font-weight-bold">SystemName</h3>


                </nav>
                <!-- End of Topbar -->

                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-left p-2 mb-4" style="background: #afdaf9">
                    <img class="img-profile mx-2 rounded-circle" src="assets/admin/img/avatar2.png" style="height: 50px; width: auto;">
                    <div class="d-flex flex-column justify-content-center">
                        <h1 class="h4 mb-0 text-gray-800">Administrator Dashboard</h1>
                        <p>Melody Digdigan</p>
                    </div>
                </div>