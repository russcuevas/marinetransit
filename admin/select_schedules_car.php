<?php
include '../connection/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}

$query = "
    SELECT
        sa.schedule_accom_id,
        sa.net_fare,
        a.accomodation_name,
        a.accomodation_type,
        s.schedule_id,
        s.schedule_time,
        s.schedule_date,
        sh.ship_name, -- Include ship_name
        r_from.port_name AS route_from,
        r_to.port_name AS route_to
    FROM
        schedule_accom sa
    LEFT JOIN
        accomodations a ON sa.accomodation_id = a.accomodation_id
    LEFT JOIN
        schedules s ON sa.schedule_id = s.schedule_id
    LEFT JOIN
        ships sh ON s.ship_id = sh.ship_id -- Join with the ships table
    LEFT JOIN
        routes r ON s.route_id = r.route_id
    LEFT JOIN
        ports r_from ON r.route_from = r_from.port_id
    LEFT JOIN
        ports r_to ON r.route_to = r_to.port_id
    WHERE
        a.accomodation_type = 'cargo'
";



$stmt = $conn->prepare($query);
$stmt->execute();
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include 'header.php'; ?>

<?php if (!empty($schedules)): ?>
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Available Schedules</h6>
            </div>

            <div class="container">
                <div class="row" style="padding: 15px;">
                    <div class="col-sm-12" style="background-color:rgb(34, 92, 143); padding: 50px;">
                        <h3 style="color: white!important"><strong>Available Schedules:</strong></h3>
                        <table class="table table-bordered" style="color: white; background-color: black;" id="myTable">
                            <thead>
                                <tr>
                                    <th>Car</th>
                                    <th>Ship Name</th> <!-- Added this column -->

                                    <th>Schedule Date</th>
                                    <th>Schedule Time</th>
                                    <th>Routes</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $currentDate = new DateTime(); // Get the current date and time
                                foreach ($schedules as $schedule):
                                    // Create DateTime objects for schedule date and time
                                    $scheduleDate = new DateTime($schedule['schedule_date']);
                                    $scheduleTime = DateTime::createFromFormat('H:i:s', $schedule['schedule_time']);

                                    if (!$scheduleTime) continue; // Skip if time is invalid

                                    $scheduleDateTime = $scheduleDate->setTime($scheduleTime->format('H'), $scheduleTime->format('i'));

                                    // Compare current date and time with the schedule date and time
                                    if ($scheduleDateTime >= $currentDate): ?>
                                        <tr>
                                            <td><?php echo $schedule['accomodation_name']; ?></td>
                                            <td><?php echo $schedule['ship_name']; ?></td> <!-- Display ship name -->

                                            <td><?php echo $schedule['schedule_date']; ?></td>
                                            <td><?php echo $scheduleTime->format('h:i A'); ?></td>
                                            <td><?php echo $schedule['route_from']; ?> - <?php echo $schedule['route_to']; ?></td>
                                            <td><?php echo number_format($schedule['net_fare'], 2); ?></td>
                                            <td>
                                                <a href="add_new_tickets_car.php?schedule_accom_id=<?php echo $schedule['schedule_accom_id']; ?>" class="btn btn-info">Select</a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>





<?php include 'footer.php'; ?>
<script>
    $(document).ready(function() {
        $('#myTable').DataTable();
    });
</script>