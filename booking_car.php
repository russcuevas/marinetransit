<?php
include 'connection/database.php';

$query_ports = "SELECT * FROM ports";
$stmt_ports = $conn->prepare($query_ports);
$stmt_ports->execute();
$ports = $stmt_ports->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $route_from = $_POST['route_from'];
    $route_to = $_POST['route_to'];
    $accomodation_id = $_POST['accomodation_id'];

    $query = "
    SELECT
        sa.schedule_accom_id,
        sa.net_fare,
        a.accomodation_name,
        a.accomodation_type,
        s.schedule_id,
        s.schedule_time,
        r_from.port_name AS route_from,
        r_to.port_name AS route_to
    FROM
        schedule_accom sa
    LEFT JOIN
        accomodations a ON sa.accomodation_id = a.accomodation_id
    LEFT JOIN
        schedules s ON sa.schedule_id = s.schedule_id
    LEFT JOIN
        routes r ON s.route_id = r.route_id
    LEFT JOIN
        ports r_from ON r.route_from = r_from.port_id
    LEFT JOIN
        ports r_to ON r.route_to = r_to.port_id
    WHERE
        r_from.port_id = :route_from
        AND r_to.port_id = :route_to
        AND sa.accomodation_id = :accomodation_id
    ";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':route_from', $route_from, PDO::PARAM_INT);
    $stmt->bindParam(':route_to', $route_to, PDO::PARAM_INT);
    $stmt->bindParam(':accomodation_id', $accomodation_id, PDO::PARAM_INT);
    $stmt->execute();
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>
<?php include 'header.php' ?>

<!-- Top content -->
<div class="top-content" style="padding-bottom: 10px">

    <div class="container">

        <form id="AddSchedule" class="user" method="POST">
            <input type="hidden" name="ticket_type" id="ticket_type" value="cargo">
            <div style="background: linear-gradient(to right, #8c52ff, #00bf63); padding: 50px 10px; display: flex; flex-direction: column; align-content: space-between; text-align: start; gap: 40px;">

                <div class="col-lg-12" style="background: linear-gradient(to right, #8c52ff, #00bf63); padding: 10px; display: flex; flex-direction: row; align-items: start; text-align: start; justify-content: center;">
                    <div class="col-sm-4" style="display: flex; flex-direction: column; gap: 10px;">
                        <button class="btn btn-info" id="btn1" type="button" onclick="window.location.href='booknow.php'"><i class="fa fa-user"></i> Passenger</button>
                        <button class="btn btn-info" id="btn2" type="button" onclick="window.location.href='booking_car.php'">
                            <i class="fa fa-car"></i> Car
                        </button>
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
                    </div>

                </div>


                <div class="col-sm-12" id="section2" style="border: 1px solid black; border: 1px solid black; border-radius: 5px; padding: 10px;">

                    <h4 style="color: white;">Car Information</h4>

                    <div class="row" style="margin-bottom: 20px">

                        <div class="col-sm-4" style="display: flex; flex-direction: column; gap: 10px;">
                            <div class="input-group">
                                <div class="input-group-addon">Category</div>

                                <select class="form-control" id="accomodation_id" name="accomodation_id" required>
                                    <?php
                                    $query_accommodations = "SELECT * FROM accomodations WHERE accomodation_type = 'cargo'";
                                    $stmt_accommodations = $conn->prepare($query_accommodations);
                                    $stmt_accommodations->execute();
                                    $accommodations = $stmt_accommodations->fetchAll(PDO::FETCH_ASSOC);

                                    foreach ($accommodations as $accommodation):
                                    ?>
                                        <option value="<?php echo $accommodation['accomodation_id']; ?>">
                                            <?php echo $accommodation['accomodation_name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>


                            </div>
                        </div>


                        <div class="col-sm-4" style="display: flex; flex-direction: column; gap: 10px;">
                            <div class="input-group">
                                <div class="input-group-addon">No. of Passenger</div>
                                <input class="form-control" type="number" name="passenger_no_cargo" id="passenger_no_cargo" min="1">
                            </div>
                            <p class="m-0" style="color: white;">(Including Driver)</p>
                        </div>

                        <div class="col-sm-4" style="display: flex; flex-direction: column; gap: 10px;">

                            <button class="btn btn-info" id="btn2" type="submit"><i class="fa fa-search"></i> Cargo Trips</button>
                        </div>
                    </div>

                    <div class="row">

                    </div>

                </div>


        </form>


        <?php if (!empty($schedules)): ?>
            <table class="table table-bordered" style="background-color: black;">
                <thead>
                    <tr>
                        <th>Schedule ID</th>
                        <th>Net Fare</th>
                        <th>Car</th>
                        <th>Schedule Time</th>
                        <th>Route From</th>
                        <th>Route To</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $schedule): ?>
                        <tr>
                            <td><?php echo $schedule['schedule_accom_id']; ?></td>
                            <td><?php echo number_format($schedule['net_fare'], 2); ?></td>
                            <td><?php echo $schedule['accomodation_name']; ?></td>
                            <td><?php echo $schedule['schedule_time']; ?></td>
                            <td><?php echo $schedule['route_from']; ?></td>
                            <td><?php echo $schedule['route_to']; ?></td>
                            <td>
                                <a href="selected_booking_car.php?schedule_accom_id=<?php echo $schedule['schedule_accom_id']; ?>" class="btn btn-info">Select</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        <?php else: ?>
        <?php endif; ?>

    </div>

</div>

<script src="assets/user/js/jquery-1.11.1.min.js"></script>



<?php include 'footer.php' ?>