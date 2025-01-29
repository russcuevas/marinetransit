<?php
include '../connection/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}

$query = "
    SELECT s.schedule_id, sh.ship_name, s.schedule_date, s.schedule_time, p_from.port_name AS route_from, p_to.port_name AS route_to
    FROM schedules s
    JOIN ships sh ON s.ship_id = sh.ship_id
    JOIN routes r ON s.route_id = r.route_id
    JOIN ports p_from ON r.route_from = p_from.port_id
    JOIN ports p_to ON r.route_to = p_to.port_id
";

$stmt = $conn->prepare($query);
$stmt->execute();
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>

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
                                <th>Ship</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Routes</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $currentDate = new DateTime();
                            ?>

                            <?php foreach ($schedules as $schedule): ?>
                                <?php
                                $scheduleDate = new DateTime($schedule['schedule_date']);
                                $scheduleTime = DateTime::createFromFormat('H:i:s', $schedule['schedule_time']);
                                $scheduleDateTime = $scheduleDate->setTime($scheduleTime->format('H'), $scheduleTime->format('i'));

                                if ($scheduleDateTime >= $currentDate): ?>
                                    <tr>
                                        <td><?php echo $schedule['ship_name']; ?></td>
                                        <td><?php echo $schedule['schedule_date']; ?></td>
                                        <td>
                                            <?php
                                            echo $scheduleTime ? $scheduleTime->format('h:i A') : 'Invalid Time';
                                            ?>
                                        </td>
                                        <td><?php echo $schedule['route_from']; ?> - <?php echo $schedule['route_to']; ?></td>
                                        <td>
                                            <a href="add_new_tickets.php?schedule_id=<?php echo $schedule['schedule_id']; ?>" class="btn btn-info">Select</a>
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




<?php include 'footer.php'; ?>
<script>
    $(document).ready(function() {
        $('#myTable').DataTable();
    });
</script>