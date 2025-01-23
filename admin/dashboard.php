<?php
include '../connection/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}

// Count Passengers
$passenger_query = "SELECT COUNT(*) AS passenger_count FROM passengers";
$passenger_stmt = $conn->prepare($passenger_query);
$passenger_stmt->execute();
$passenger_result = $passenger_stmt->fetch();
$passenger_count = $passenger_result['passenger_count'];

// Count Vessels
$ship_query = "SELECT COUNT(*) AS ship_count FROM ships";
$ship_stmt = $conn->prepare($ship_query);
$ship_stmt->execute();
$ship_result = $ship_stmt->fetch();
$ship_count = $ship_result['ship_count'];

// Count Schedules
$schedule_query = "SELECT COUNT(*) AS schedule_count FROM schedules";
$schedule_stmt = $conn->prepare($schedule_query);
$schedule_stmt->execute();
$schedule_result = $schedule_stmt->fetch();
$schedule_count = $schedule_result['schedule_count'];

// Count Payments
$payment_query = "SELECT COUNT(*) AS payment_count FROM tickets";
$payment_stmt = $conn->prepare($payment_query);
$payment_stmt->execute();
$payment_result = $payment_stmt->fetch();
$payment_count = $payment_result['payment_count'];

// Count Routes
$route_query = "SELECT COUNT(*) AS route_count FROM routes";
$route_stmt = $conn->prepare($route_query);
$route_stmt->execute();
$route_result = $route_stmt->fetch();
$route_count = $route_result['route_count'];
?>
<?php include 'header.php' ?>
<style type="text/css">
    .bg-gradient-secondary {
        background: #d5d6d7 !important;
    }

    .card-body .text-white {
        color: #000 !important;
    }
</style>
<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="row">

        <div class="col-xl-12 col-md-12 mb-0">
            <p>Hi, System Administrator</p>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Passengers Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2 bg-gradient-secondary">
                <div class="card-body border-bottom-primary mx-2" id="passengerDiv">
                    <div class="row no-gutters align-items-center">
                        <div class="col-auto mr-5">
                            <i class="fas fa-users fa-3x text-white"></i>
                        </div>
                        <div class="col mr-2">
                            <div class="h5 mb-0 font-weight-bold text-white">Passengers</div>
                        </div>
                    </div>
                    <h3 class="m-0 text-right text-white font-weight-bold"><?php echo $passenger_count; ?></h3>
                </div>
            </div>
        </div>

        <!-- Vessels Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2 bg-gradient-secondary">
                <div class="card-body border-bottom-primary mx-2" id="shipDiv">
                    <div class="row no-gutters align-items-center">
                        <div class="col-auto mr-5">
                            <i class="fas fa-ship fa-3x text-white"></i>
                        </div>
                        <div class="col mr-2">
                            <div class="h5 mb-0 font-weight-bold text-white">Vessel</div>
                        </div>
                    </div>
                    <h3 class="m-0 text-right text-white font-weight-bold"><?php echo $ship_count; ?></h3>
                </div>
            </div>
        </div>

        <!-- Schedule Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2 bg-gradient-secondary">
                <div class="card-body border-bottom-primary mx-2" id="scheduleDiv">
                    <div class="row no-gutters align-items-center">
                        <div class="col-auto mr-5">
                            <i class="fas fa-calendar fa-3x text-white"></i>
                        </div>
                        <div class="col mr-2">
                            <div class="h5 mb-0 font-weight-bold text-white">Schedule</div>
                        </div>
                    </div>
                    <h3 class="m-0 text-right text-white font-weight-bold"><?php echo $schedule_count; ?></h3>
                </div>
            </div>
        </div>

        <!-- Payment Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2 bg-gradient-secondary">
                <div class="card-body border-bottom-primary mx-2" id="paymentDiv">
                    <div class="row no-gutters align-items-center">
                        <div class="col-auto mr-5">
                            <i class="fas fa-dollar-sign fa-3x text-white"></i>
                        </div>
                        <div class="col mr-2">
                            <div class="h5 mb-0 font-weight-bold text-white">Payment</div>
                        </div>
                    </div>
                    <h3 class="m-0 text-right text-white font-weight-bold"><?php echo $payment_count; ?></h3>
                </div>
            </div>
        </div>

        <!-- Route Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2 bg-gradient-secondary">
                <div class="card-body border-bottom-primary mx-2" id="routeDiv">
                    <div class="row no-gutters align-items-center">
                        <div class="col-auto mr-5">
                            <i class="fas fa-route fa-3x text-white"></i>
                        </div>
                        <div class="col mr-2">
                            <div class="h5 mb-0 font-weight-bold text-white">Route</div>
                        </div>
                    </div>
                    <h3 class="m-0 text-right text-white font-weight-bold"><?php echo $route_count; ?></h3>
                </div>
            </div>
        </div>

    </div>
    <!-- /.container-fluid -->

    <script src="assets/admin/vendor/jquery/jquery.min.js"></script>
    <?php include 'footer.php' ?>