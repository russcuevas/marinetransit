<?php

$query = "SELECT * FROM `systeminfo` WHERE `systeminfo_id` = 1";
$stmt = $conn->prepare($query);
$stmt->execute();
$system_info = $stmt->fetch();

$systeminfo_name = $system_info['systeminfo_name'];
$systeminfo_shortname = $system_info['systeminfo_shortname'];
$systeminfo_icon = $system_info['systeminfo_icon'] ? $system_info['systeminfo_icon'] : 'default-logo.png';

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
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="assets/admin/css/sb-admin-2.min.css" rel="stylesheet">
    <!-- Custom styles for this page -->
    <link href="assets/admin/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">


    <link rel="shortcut icon" href="assets/admin/img/icon.jpg">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">



    <style type="text/css">
        .navbar-nav .active span,
        .navbar-nav .active i {
            color: white !important;
        }

        .navbar-nav span,
        .navbar-nav i {
            color: white !important;
            font-size: 15px !important;
        }

        .active {
            background: #000957;
            color: black !important;
        }

        .sidebar-dark .nav-item .nav-link[data-toggle=collapse]::after {
            color: white;
        }
    </style>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background:rgb(0, 0, 0); color: white !important;">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-start" href="admin" style="background: #000957 ">
                <img class="img-profile" src="assets/system_image/<?php echo $systeminfo_icon; ?>" style="height: 70px; width: auto;">
                <div class="sidebar-brand-text mx-3" style="color: white !important"><?php echo $systeminfo_shortname; ?></div>
            </a>
            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item" id="">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-fw fa-ship"></i>
                    <span>Admin</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <li class="nav-item" id="nav-dashboard">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-fw fa-home"></i>
                    <span>Home</span>
                </a>
            </li>

            <li class="nav-item" id="nav-cashier">
                <a class="nav-link" href="cashier.php">
                    <i class="fas fa-fw fa-credit-card"></i>
                    <span>Cashier</span>
                </a>
            </li>

            <li class="nav-item" id="nav-rating">
                <a class="nav-link" href="ratings.php">
                    <i class="fas fa-fw fa-star"></i>
                    <span>Ratings</span>
                </a>
            </li>

            <li class="nav-item" id="nav-schedule">
                <a class="nav-link" href="schedule.php">
                    <i class="fas fa-fw fa-calendar"></i>
                    <span>Schedule</span>
                </a>
            </li>

            <li class="nav-item" id="nav-route">
                <a class="nav-link" href="route.php">
                    <i class="fa fa-fw fa-route"></i>
                    <span>Route</span>
                </a>
            </li>

            <li class="nav-item" id="nav-ship">
                <a class="nav-link" href="ship.php">
                    <i class="fas fa-fw fa-ship"></i>
                    <span>Vessel</span>
                </a>
            </li>

            <!-- Tickets Collapse -->
            <li class="nav-item" id="nav-tickets">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTickets" aria-expanded="false" aria-controls="collapseTickets">
                    <i class="fa fa-fw fa-file"></i>
                    <span>Tickets</span>
                </a>
                <div id="collapseTickets" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="ticket_regular.php" id="ticket-regular">Passengers Ticket</a>
                        <a class="collapse-item" href="ticket_cargo.php" id="ticket-cargo">Cargo Ticket</a>
                    </div>
                </div>
            </li>

            <li class="nav-item" id="nav-payment">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePayments" aria-expanded="false" aria-controls="collapsePayments">
                    <i class="fa fa-fw fa-dollar-sign"></i>
                    <span>Payments</span>
                </a>
                <div id="collapsePayments" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="payment.php" id="passenger-payment">Passenger Payment</a>
                        <a class="collapse-item" href="cargos_payment.php" id="cargos-payment">Cargos Payment</a>
                    </div>
                </div>
            </li>

            <li class="nav-item" id="nav-reports">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsereports" aria-expanded="false" aria-controls="collapsereports">
                    <i class="fas fa-fw fa-file-alt"></i>
                    <span>Reports</span>
                </a>
                <div id="collapsereports" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="report.php" id="all-records">All records</a>
                        <a class="collapse-item" href="weekly_records.php" id="weekly-records">Weekly records</a>
                        <a class="collapse-item" href="daily_records.php" id="daily-records">Daily</a>
                    </div>
                </div>
            </li>



            <li class="nav-item" id="nav-settings">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="false" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Settings</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="setting.php" id="setting-system">System Information</a>
                        <a class="collapse-item" href="setting_port.php" id="setting-port">Port Locations</a>
                        <a class="collapse-item" href="setting_accom.php" id="setting-accom">Accommodation</a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Nav Item - Logout -->
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fa fa-fw fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>

        </ul>


        <!-- End of Sidebar -->


        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light topbar mb-4 static-top shadow" style="background: #000957 ">

                    <h3 class="text-white font-weight-bold"><?php echo $systeminfo_name; ?></h3>


                </nav>
                <!-- End of Topbar -->

                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-left p-2 mb-4" style="background: #000957 ">
                    <img class="img-profile mx-2 rounded-circle" src="assets/admin/img/system_dashboard.jpg" style="height: 50px; width: auto;">
                    <div class="d-flex flex-column justify-content-center">
                        <h1 class="h4 mb-0" style="color: white !important;">Administrator Dashboard</h1>
                        <p style="color: white !important;">Melbin Querequere</p>
                    </div>
                </div>


                <script>
                    // Get the current URL
                    var currentURL = window.location.pathname;

                    // Function to set the active class for a menu item
                    function setActiveLink(id) {
                        var link = document.getElementById(id);
                        if (link) {
                            link.classList.add('active');
                        }
                    }

                    // Function to show the collapse based on the URL
                    function showCollapse(id) {
                        var collapse = document.getElementById(id);
                        if (collapse) {
                            collapse.classList.add('show');
                        }
                    }

                    // Check the current URL and activate the appropriate menu
                    if (currentURL.includes('dashboard.php')) {
                        setActiveLink('nav-dashboard');
                    }

                    if (currentURL.includes('ratings.php')) {
                        setActiveLink('nav-rating');
                    }

                    if (currentURL.includes('cashier.php')) {
                        setActiveLink('nav-cashier');
                    }

                    if (currentURL.includes('schedule.php')) {
                        setActiveLink('nav-schedule');
                    }
                    if (currentURL.includes('route.php')) {
                        setActiveLink('nav-route');
                    }
                    if (currentURL.includes('ship.php')) {
                        setActiveLink('nav-ship');
                    }

                    // Check the "Reports" section and activate related items
                    if (currentURL.includes('report.php')) {
                        setActiveLink('nav-reports');
                        showCollapse('collapsereports'); // Show collapse for Reports
                        setActiveLink('all-records'); // Activate "All records" link
                    }

                    if (currentURL.includes('weekly_records.php')) {
                        setActiveLink('nav-reports');
                        showCollapse('collapsereports');
                        setActiveLink('weekly-records'); // Activate "Weekly records" link
                    }

                    if (currentURL.includes('daily_records.php')) {
                        setActiveLink('nav-reports');
                        showCollapse('collapsereports');
                        setActiveLink('daily-records'); // Activate "Daily records" link
                    }

                    // For the Tickets section, check which subpage is active
                    if (currentURL.includes('ticket_free.php')) {
                        setActiveLink('nav-tickets');
                        setActiveLink('ticket-free');
                        showCollapse('collapseTickets');
                    }
                    if (currentURL.includes('ticket_regular.php')) {
                        setActiveLink('nav-tickets');
                        setActiveLink('ticket-regular');
                        showCollapse('collapseTickets');
                    }
                    if (currentURL.includes('ticket_children.php')) {
                        setActiveLink('nav-tickets');
                        setActiveLink('ticket-children');
                        showCollapse('collapseTickets');
                    }
                    if (currentURL.includes('ticket_student.php')) {
                        setActiveLink('nav-tickets');
                        setActiveLink('ticket-student');
                        showCollapse('collapseTickets');
                    }
                    if (currentURL.includes('ticket_senior.php')) {
                        setActiveLink('nav-tickets');
                        setActiveLink('ticket-senior');
                        showCollapse('collapseTickets');
                    }
                    if (currentURL.includes('ticket_cargo.php')) {
                        setActiveLink('nav-tickets');
                        setActiveLink('ticket-cargo');
                        showCollapse('collapseTickets');
                    }

                    // For the Settings section, check which subpage is active
                    if (currentURL.includes('setting.php')) {
                        setActiveLink('nav-settings');
                        setActiveLink('setting-system');
                        showCollapse('collapseUtilities');
                    }
                    if (currentURL.includes('setting_port.php')) {
                        setActiveLink('nav-settings');
                        setActiveLink('setting-port');
                        showCollapse('collapseUtilities');
                    }
                    if (currentURL.includes('setting_accom.php')) {
                        setActiveLink('nav-settings');
                        setActiveLink('setting-accom');
                        showCollapse('collapseUtilities');
                    }

                    // Check for Payments section and activate related items
                    if (currentURL.includes('payment.php')) {
                        setActiveLink('nav-payment');
                        showCollapse('collapsePayments'); // Show collapse for Payments
                        setActiveLink('passenger-payment'); // Activate "Passenger Payment" link
                        // Deactivate the other link
                        var cargosLink = document.getElementById('cargos-payment');
                        if (cargosLink) cargosLink.classList.remove('active');
                    }

                    if (currentURL.includes('cargos_payment.php')) {
                        setActiveLink('nav-payment');
                        showCollapse('collapsePayments');
                        setActiveLink('cargos-payment'); // Activate "Cargos Payment" link
                        // Deactivate the other link
                        var passengerLink = document.getElementById('passenger-payment');
                        if (passengerLink) passengerLink.classList.remove('active');
                    }
                </script>