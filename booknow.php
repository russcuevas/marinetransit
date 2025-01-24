<?php
include 'connection/database.php';

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture form data
    $route_from = $_POST['route_from'];
    $route_to = $_POST['route_to'];
    $schedule_date = $_POST['schedule_date'];

    // Query to fetch schedules based on selected ports and date, including NULL schedules
    $query = "
        SELECT s.schedule_id, sh.ship_name, s.schedule_time, p_from.port_name AS route_from, p_to.port_name AS route_to
        FROM schedules s
        JOIN ships sh ON s.ship_id = sh.ship_id
        JOIN routes r ON s.route_id = r.route_id
        JOIN ports p_from ON r.route_from = p_from.port_id
        JOIN ports p_to ON r.route_to = p_to.port_id
        WHERE r.route_from = :route_from 
        AND r.route_to = :route_to 
        AND (s.schedule_date = :schedule_date OR s.schedule_date IS NULL)
    ";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':route_from', $route_from);
    $stmt->bindParam(':route_to', $route_to);
    $stmt->bindParam(':schedule_date', $schedule_date);
    $stmt->execute();
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $schedules = [];
}

// Query to fetch ports for the select options
$query_ports = "SELECT * FROM ports";
$stmt_ports = $conn->prepare($query_ports);
$stmt_ports->execute();
$ports = $stmt_ports->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'header.php' ?>

<!-- Top content -->
<div class="top-content" style="padding-bottom: 10px">

    <div class="container">

        <form id="AddSchedule" class="user" method="POST">
            <input type="hidden" name="ticket_type" id="ticket_type" value="passenger">
            <div style="background: linear-gradient(to right, #8c52ff, #00bf63); padding: 50px 10px; display: flex; flex-direction: column; align-content: space-between; text-align: start; gap: 40px;">

                <div class="col-lg-12" style="background: linear-gradient(to right, #8c52ff, #00bf63); padding: 10px; display: flex; flex-direction: row; align-items: start; text-align: start; justify-content: center;">
                    <div class="col-sm-4" style="display: flex; flex-direction: column; gap: 10px;">
                        <button class="btn btn-info" id="btn1" type="button"><i class="fa fa-user"></i> Passenger</button>
                        <button class="btn btn-info" id="btn2" type="button"><i class="fa fa-car"></i> Car</button>
                    </div>

                    <div class="col-sm-4" style="display: flex; flex-direction: column; gap: 10px;">
                        <div class="input-group">
                            <div class="input-group-addon">From</div>
                            <select class="form-control" name="route_from" id="route_from" required>
                                <?php foreach ($ports as $port): ?>
                                    <option value="<?php echo $port['port_id']; ?>"><?php echo $port['port_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="input-group">
                            <div class="input-group-addon">To &nbsp;&nbsp;&nbsp;&nbsp;</div>
                            <select class="form-control" name="route_to" id="route_to" required>
                                <?php foreach ($ports as $port): ?>
                                    <option value="<?php echo $port['port_id']; ?>"><?php echo $port['port_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-4" style="display: flex; flex-direction: column; gap: 10px;">
                        <div class="input-group">
                            <div class="input-group-addon">Depart</div>
                            <input class="form-control" required type="date" name="schedule_date" id="schedule_date" min="<?= date('Y-m-d') ?>">
                        </div>

                        <div id="section1">
                            <div class="input-group" style="margin-bottom: 10px">
                                <div class="input-group-addon">No. of Passenger</div>
                                <input class="form-control" type="number" name="passenger_no" id="passenger_no" min="1">
                            </div>
                            <button style="float: right;" class="btn btn-info" id="btn2" type="submit"><i class="fa fa-search"></i> Search Trips</button>
                        </div>
                    </div>

                </div>
            </div>
        </form>

        <!-- Display Schedules -->
        <?php if (!empty($schedules)): ?>
            <div class="row">
                <div class="col-sm-12" style="background-color: black;">
                    <h3 style="color: white!important"><strong><i>Available Schedules:</i></strong></h3>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Schedule ID</th>
                                <th>Ship</th>
                                <th>Time</th>
                                <th>Route From</th>
                                <th>Route To</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $schedule): ?>
                                <tr>
                                    <td><?php echo $schedule['schedule_id']; ?></td>
                                    <td><?php echo $schedule['ship_name']; ?></td>
                                    <td><?php echo $schedule['schedule_time']; ?></td>
                                    <td><?php echo $schedule['route_from']; ?></td>
                                    <td><?php echo $schedule['route_to']; ?></td>
                                    <td>
                                        <a href="selected_booking.php?schedule_id=<?php echo $schedule['schedule_id']; ?>" class="btn btn-info">Select</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php else: ?>
            <p style="color: white;">No schedules found for the selected route and date.</p>
        <?php endif; ?>
    </div>

</div>

<script src="assets/user/js/jquery-1.11.1.min.js"></script>



<?php include 'footer.php' ?>