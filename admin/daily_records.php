<?php
date_default_timezone_set('Asia/Manila');
include '../connection/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}

$currentDate = date('Y-m-d');
$get_report = "
    SELECT 
        r.ticket_code,
        MIN(r.report_id) AS report_id, -- Choose the lowest report_id for uniqueness
        r.contact_person,
        r.contact_address,
        r.contact_email,
        r.ticket_status,
        s.schedule_time,
        sh.ship_name,
        r.ticket_price,
        rt1.port_name AS route_from, 
        rt2.port_name AS route_to
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
    WHERE 1=1
    AND DATE(r.report_date) = :currentDate
    GROUP BY r.ticket_code";

$stmt_get_report = $conn->prepare($get_report);
$stmt_get_report->bindValue(':currentDate', $currentDate);

$stmt_get_report->execute();
$report = $stmt_get_report->fetchAll(PDO::FETCH_ASSOC);

$totalPrice = 0;
foreach ($report as $reports) {
    $totalPrice += $reports['ticket_price'];
}
$get_total_price = "
    SELECT SUM(r.ticket_price) AS total_ticket_price
    FROM reports r
    WHERE 1=1 AND DATE(r.report_date) = :currentDate
";

$stmt_get_total_price = $conn->prepare($get_total_price);
$stmt_get_total_price->bindValue(':currentDate', $currentDate);

$stmt_get_total_price->execute();
$total_price_result = $stmt_get_total_price->fetch(PDO::FETCH_ASSOC);
$totalPrice = $total_price_result['total_ticket_price'];

?>

<?php include 'header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="p-3">
            <div class="d-flex justify-content-between">
                <h4 style="color: black;">Date Today: <?= $currentDate ?> <br>Total: <?= number_format($totalPrice, 2); ?></h4>
                <a href="print/daily_reports.php?date=<?= $currentDate ?>" target="_blank" class="fa fa-print text-secondary" style="font-size: 35px; color: inherit; text-decoration: none; cursor: pointer;"></a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Ticket No.</th>
                            <th>Name</th>
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