<?php
include '../connection/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}

// Initialize date filter variables
$dateFrom = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : null;
$dateTo = isset($_GET['dateTo']) ? $_GET['dateTo'] : null;

// Calculate the current week number
$currentWeekNumber = date('W'); // Get current week number of the year

// Get the start and end date of the current week
$startOfWeek = date('Y-m-d', strtotime('last monday'));
$endOfWeek = date('Y-m-d', strtotime('next sunday'));

// Get the reports for the selected or current week
$get_report = "
    SELECT 
        r.ticket_code,
        MIN(r.report_id) AS report_id, 
        r.contact_person,
        rt1.port_name AS route_from,  -- Get route_from port name
        rt2.port_name AS route_to,    -- Get route_to port name
        r.ticket_status,
        s.schedule_time,
        sh.ship_name,
        SUM(r.ticket_price) AS total_price
    FROM 
        reports r
    LEFT JOIN 
        schedules s ON r.schedule_id = s.schedule_id
    LEFT JOIN 
        ships sh ON s.ship_id = sh.ship_id
    LEFT JOIN 
        routes r1 ON s.route_id = r1.route_id
    LEFT JOIN 
        routes r2 ON s.route_id = r2.route_id
    LEFT JOIN 
        ports rt1 ON r1.route_from = rt1.port_id
    LEFT JOIN 
        ports rt2 ON r2.route_to = rt2.port_id
    WHERE 1=1";

if ($dateFrom && $dateTo) {
    $get_report .= " AND r.report_date BETWEEN :dateFrom AND :dateTo";
} else {
    $get_report .= " AND WEEK(r.report_date) = WEEK(CURDATE())";
}

$get_report .= " GROUP BY r.ticket_code";

$stmt_get_report = $conn->prepare($get_report);
if ($dateFrom && $dateTo) {
    $stmt_get_report->bindValue(':dateFrom', $dateFrom);
    $stmt_get_report->bindValue(':dateTo', $dateTo);
}

$stmt_get_report->execute();
$report = $stmt_get_report->fetchAll(PDO::FETCH_ASSOC);


$totalPrice = 0;
foreach ($report as $reports) {
    $totalPrice += $reports['total_price'];
}

?>

<?php include 'header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Weekly Reports Card -->
    <div class="card shadow mb-4">
        <div class="p-3 d-flex justify-content-between">
            <div class="d-flex justify-content-start align-items-center">
                <form method="get" action="" class="d-flex align-items-center">
                    <div class="mr-3">
                        <label for="dateFrom" class="mr-2">From</label>
                        <input value="<?= htmlspecialchars($dateFrom); ?>" class="form-control mr-3" type="date" name="dateFrom" id="dateFrom">
                    </div>
                    <div class="mr-3">
                        <label for="dateTo" class="mr-2">To</label>
                        <input value="<?= htmlspecialchars($dateTo); ?>" class="form-control mr-3" type="date" name="dateTo" id="dateTo">
                    </div>
                    <button class="btn btn-primary" style="margin-top: 34px;" type="submit">Filter</button>
                </form>
            </div>
            <a href="print/weekly_reports.php?dateFrom=<?= $dateFrom ?>&dateTo=<?= $dateTo ?>" target="_blank" class="fa fa-print text-secondary" style="font-size: 35px!important; color: inherit; text-decoration: none; cursor: pointer;">
            </a>
        </div>

        <div class="card-body">
            <h4 style="color: black;">Week <?= $currentWeekNumber ?> (<?= $startOfWeek ?> to <?= $endOfWeek ?>)</h4>
            <h5 style="color: black;">Total: <?= number_format($totalPrice, 2); ?> </h5>
            <br>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Ticket No.</th>
                            <th>Name</th>
                            <th>Schedule Date/Time</th>
                            <th>Ship</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report as $reports): ?>
                            <tr>
                                <td><?= htmlspecialchars($reports['ticket_code']); ?></td>
                                <td><?= htmlspecialchars($reports['contact_person']); ?></td>
                                <td><?= htmlspecialchars($reports['schedule_time']); ?></td>
                                <td><?= htmlspecialchars($reports['ship_name']); ?></td>
                                <td><?= htmlspecialchars($reports['route_from']); ?></td> <!-- Display route_from -->
                                <td><?= htmlspecialchars($reports['route_to']); ?></td> <!-- Display route_to -->
                                <td><?= htmlspecialchars($reports['ticket_status']); ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

<script src="assets/admin/vendor/jquery/jquery.min.js"></script>

<?php include 'footer.php' ?>