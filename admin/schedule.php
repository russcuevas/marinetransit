<?php
include '../connection/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}

// Fetch ship data
$ship_query = "SELECT ship_id, ship_name FROM ships";
$ship_stmt = $conn->query($ship_query);
$ships = $ship_stmt->fetchAll(PDO::FETCH_ASSOC);
$ship_stmt->closeCursor();

// Fetch route data
$route_query = "SELECT r.route_id, p_from.port_name AS from_port, p_to.port_name AS to_port 
                FROM routes r
                JOIN ports p_from ON r.route_from = p_from.port_id
                JOIN ports p_to ON r.route_to = p_to.port_id";
$route_stmt = $conn->query($route_query);
$routes = $route_stmt->fetchAll(PDO::FETCH_ASSOC);
$route_stmt->closeCursor();

// Fetch accommodation data
$accom_query = "SELECT accomodation_id, accomodation_name, accomodation_type FROM accomodations";
$accom_stmt = $conn->query($accom_query);
$accommodations = $accom_stmt->fetchAll(PDO::FETCH_ASSOC);
$accom_stmt->closeCursor();

// Fetch schedule data
$schedule_query = "
SELECT s.schedule_id, s.schedule_time, s.schedule_date, sh.ship_id, sh.ship_name, r.route_id, p_from.port_name AS from_port, p_to.port_name AS to_port
FROM schedules s
JOIN ships sh ON s.ship_id = sh.ship_id
JOIN routes r ON s.route_id = r.route_id
JOIN ports p_from ON r.route_from = p_from.port_id
JOIN ports p_to ON r.route_to = p_to.port_id;
";
$schedule_stmt = $conn->query($schedule_query);
$schedules = $schedule_stmt->fetchAll(PDO::FETCH_ASSOC);
$schedule_stmt->closeCursor();

// add schedule
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $schedule_date = $_POST['schedule_date'];
    $schedule_time = $_POST['schedule_time'];
    $ship_id = $_POST['ship_id'];
    $route_id = $_POST['route_id'];
    $accommodations = $_POST['accommodation_id'];
    $net_fares = $_POST['net_fare'];
    $max_passengers = $_POST['max_passenger'];

    $stmt = $conn->prepare("INSERT INTO schedules (schedule_date, schedule_time, ship_id, route_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$schedule_date, $schedule_time, $ship_id, $route_id]);
    $schedule_id = $conn->lastInsertId();

    foreach ($accommodations as $index => $accommodation_id) {
        $max_passenger_value = !empty($max_passengers[$index]) ? $max_passengers[$index] : 0;

        $stmt_accom = $conn->prepare("INSERT INTO schedule_accom (schedule_id, accomodation_id, net_fare, max_passenger) VALUES (?, ?, ?, ?)");
        $stmt_accom->execute([$schedule_id, $accommodation_id, $net_fares[$index], $max_passenger_value]);
    }


    $_SESSION['success'] = 'Schedule added successfully!';
    header('location: schedule.php');
    exit;
}

if (isset($_GET['schedule_id'])) {
    $schedule_id = $_GET['schedule_id'];
    $accom_query = "
        SELECT sa.accomodation_id, a.accomodation_name, sa.net_fare, sa.max_passenger
        FROM schedule_accom sa
        JOIN accomodations a ON sa.accomodation_id = a.accomodation_id
        WHERE sa.schedule_id = ?";
    $accom_stmt = $conn->prepare($accom_query);
    $accom_stmt->execute([$schedule_id]);
    $accommodation_details = $accom_stmt->fetchAll(PDO::FETCH_ASSOC);
    $accom_stmt->closeCursor();
}


?>

<?php include 'header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">All Schedules</h6>
            <a class="btn btn-secondary" data-toggle="modal" data-target="#addSchedule"> Add New Schedule </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Schedule Date</th>

                            <th>Schedule Time</th>
                            <th>Ship</th>
                            <th>Route</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($schedules): ?>
                            <?php foreach ($schedules as $schedule): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($schedule['schedule_id']); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['schedule_date']); ?></td>

                                    <td><?php echo htmlspecialchars($schedule['schedule_time']); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['ship_name']); ?></td>
                                    <td><?php echo "From " . htmlspecialchars($schedule['from_port']) . " To " . htmlspecialchars($schedule['to_port']); ?></td>
                                    <td class="text-center">
                                        <a class="btn btn-primary edit" href="update_schedule.php?schedule_id=<?php echo $schedule['schedule_id']; ?>"><i class="fas fa-edit"></i> Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No schedules found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Schedule Modal -->
<div class="modal fade" id="addSchedule" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add New Schedule</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="AddScheduleForm" class="user" method="POST">
                    <div class="row">
                        <div class="col-xl-12 col-md-6 mb-4">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="schedule_date">Schedule Date</label>
                                    <input required class="form-control form-control-solid" type="date" id="schedule_date" name="schedule_date">
                                </div>

                                <div class="col-md-6">
                                    <label for="schedule_time">Schedule Time</label>
                                    <input required class="form-control form-control-solid" type="time" id="schedule_time" name="schedule_time">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="ship_id">Ship Name</label>
                                    <select class="form-control form-control-solid" id="ship_id" name="ship_id">
                                        <?php foreach ($ships as $ship): ?>
                                            <option value="<?php echo $ship['ship_id']; ?>"><?php echo $ship['ship_name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="route_id">Route</label>
                                    <select class="form-control form-control-solid" id="route_id" name="route_id">
                                        <?php foreach ($routes as $route): ?>
                                            <option value="<?php echo $route['route_id']; ?>"><?php echo "From " . $route['from_port'] . " To " . $route['to_port']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Accommodations Table -->
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <table class="table table-bordered border-hovered border-stripped" id="accom_list">
                                            <colgroup>
                                                <col width="60%">
                                                <col width="15%">
                                            </colgroup>
                                            <thead>
                                                <tr>
                                                    <th class="py-1 px-2 text-center">Accommodation</th>
                                                    <th class="py-1 px-2 text-center">Net Fare</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($accommodations as $accommodation): ?>
                                                    <tr>
                                                        <td class="py-1"><?php echo $accommodation['accomodation_name']; ?></td>
                                                        <td class="py-1 px-2 number text-right">
                                                            <input type="text" name="net_fare[]" value="0.00">
                                                        </td>
                                                        <input type="hidden" name="accommodation_id[]" value="<?php echo $accommodation['accomodation_id']; ?>">
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary" type="submit">Add Schedule</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="assets/admin/vendor/jquery/jquery.min.js"></script>

<?php include 'footer.php' ?>