<?php
include '../connection/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

if (isset($_GET['schedule_id'])) {
    $schedule_id = $_GET['schedule_id'];
    $schedule_query = "
        SELECT s.schedule_id, s.schedule_date, s.schedule_time, sh.ship_id, sh.ship_name, r.route_id, 
               p_from.port_name AS from_port, p_to.port_name AS to_port
        FROM schedules s
        JOIN ships sh ON s.ship_id = sh.ship_id
        JOIN routes r ON s.route_id = r.route_id
        JOIN ports p_from ON r.route_from = p_from.port_id
        JOIN ports p_to ON r.route_to = p_to.port_id
        WHERE s.schedule_id = :schedule_id
    ";
    $stmt = $conn->prepare($schedule_query);
    $stmt->execute(['schedule_id' => $schedule_id]);
    $schedule = $stmt->fetch(PDO::FETCH_ASSOC);

    $accom_query = "
        SELECT sa.accomodation_id, a.accomodation_name, sa.net_fare, sa.max_passenger
        FROM schedule_accom sa
        JOIN accomodations a ON sa.accomodation_id = a.accomodation_id
        WHERE sa.schedule_id = :schedule_id
    ";
    $accom_stmt = $conn->prepare($accom_query);
    $accom_stmt->execute(['schedule_id' => $schedule_id]);
    $accommodations = $accom_stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    header('location: schedule.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $schedule_id = $_POST['schedule_id'];
    $schedule_time = $_POST['schedule_time'];
    $schedule_date = $_POST['schedule_date']; // Get the schedule date
    $ship_id = $_POST['ship_id'];
    $route_id = $_POST['route_id'];
    $accommodation_ids = $_POST['accommodation_id'];
    $net_fares = $_POST['net_fare'];
    $max_passengers = $_POST['max_passenger'];

    // Update schedule with the schedule date
    $stmt = $conn->prepare("UPDATE schedules SET schedule_time = ?, schedule_date = ?, ship_id = ?, route_id = ? WHERE schedule_id = ?");
    $stmt->execute([$schedule_time, $schedule_date, $ship_id, $route_id, $schedule_id]);

    // Update accommodations
    foreach ($accommodation_ids as $index => $accommodation_id) {
        $net_fare_value = !empty($net_fares[$index]) ? $net_fares[$index] : 0.00;
        $max_passenger_value = !empty($max_passengers[$index]) ? $max_passengers[$index] : 0;

        $stmt_accom = $conn->prepare("UPDATE schedule_accom SET net_fare = ?, max_passenger = ? WHERE schedule_id = ? AND accomodation_id = ?");
        $stmt_accom->execute([$net_fare_value, $max_passenger_value, $schedule_id, $accommodation_id]);
    }

    $_SESSION['success'] = 'Schedule updated successfully!';
    header('location: schedule.php');
    exit;
}

?>

<?php include 'header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Update Schedule</h6>
        </div>

        <div class="card-body">
            <form id="UpdateScheduleForm" class="user" method="POST">
                <input type="hidden" name="schedule_id" value="<?php echo htmlspecialchars($schedule['schedule_id']); ?>">

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="schedule_date">Schedule Date</label>
                        <input required class="form-control form-control-solid" type="date" id="schedule_date" name="schedule_date" value="<?php echo $schedule['schedule_date'] ?>">
                    </div>
                </div>

                <!-- Schedule Time -->
                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="schedule_time">Schedule Time</label>
                        <input required class="form-control form-control-solid" type="time" id="schedule_time" name="schedule_time" value="<?php echo htmlspecialchars($schedule['schedule_time']); ?>">
                    </div>
                </div>

                <!-- Ship Name -->
                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="ship_id">Ship Name</label>
                        <select class="form-control form-control-solid" id="ship_id" name="ship_id">
                            <?php
                            // Fetch ships and populate the select box
                            $ship_query = "SELECT ship_id, ship_name FROM ships";
                            $ship_stmt = $conn->query($ship_query);
                            $ships = $ship_stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($ships as $ship) {
                                echo "<option value=\"{$ship['ship_id']}\"" . ($ship['ship_id'] == $schedule['ship_id'] ? ' selected' : '') . ">{$ship['ship_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Route -->
                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="route_id">Route</label>
                        <select class="form-control form-control-solid" id="route_id" name="route_id">
                            <?php
                            // Fetch routes and populate the select box
                            $route_query = "SELECT r.route_id, p_from.port_name AS from_port, p_to.port_name AS to_port 
                                            FROM routes r
                                            JOIN ports p_from ON r.route_from = p_from.port_id
                                            JOIN ports p_to ON r.route_to = p_to.port_id";
                            $route_stmt = $conn->query($route_query);
                            $routes = $route_stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($routes as $route) {
                                echo "<option value=\"{$route['route_id']}\"" . ($route['route_id'] == $schedule['route_id'] ? ' selected' : '') . ">From {$route['from_port']} To {$route['to_port']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Accommodations Table -->
                <div class="form-group row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <table class="table table-bordered" id="accom_list">
                                <thead>
                                    <tr>
                                        <th class="text-center">Accommodation</th>
                                        <th class="text-center">Net Fare</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($accommodations as $accommodation): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($accommodation['accomodation_name']); ?></td>
                                            <td>
                                                <input type="text" name="net_fare[]" value="<?php echo htmlspecialchars($accommodation['net_fare']); ?>">
                                            </td>
                                            <input type="hidden" name="accommodation_id[]" value="<?php echo $accommodation['accomodation_id']; ?>">
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button class="btn btn-primary" type="submit">Update Schedule</button>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php' ?>