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

// Build the query with date filter
$get_report = "
    SELECT 
        r.ticket_code,
        MIN(r.report_id) AS report_id, -- Choose the lowest report_id for uniqueness
        r.contact_person,
        p1.port_name AS route_from,    -- Get port_name for route_from
        p2.port_name AS route_to,      -- Get port_name for route_to
        r.ticket_status,
        s.schedule_time,
        sh.ship_name 
    FROM 
        reports r
    LEFT JOIN 
        schedules s ON r.schedule_id = s.schedule_id
    LEFT JOIN 
        ships sh ON s.ship_id = sh.ship_id
    LEFT JOIN
        routes ro ON s.route_id = ro.route_id  -- Join routes to get route_id
    LEFT JOIN
        ports p1 ON ro.route_from = p1.port_id  -- Join ports to get route_from (port_name)
    LEFT JOIN
        ports p2 ON ro.route_to = p2.port_id    -- Join ports to get route_to (port_name)
    WHERE 1=1";

if ($dateFrom) {
    $get_report .= " AND r.report_date >= :dateFrom";
}
if ($dateTo) {
    $get_report .= " AND r.report_date <= :dateTo";
}

$get_report .= " GROUP BY r.ticket_code";
$stmt_get_report = $conn->prepare($get_report);
if ($dateFrom) {
    $stmt_get_report->bindValue(':dateFrom', $dateFrom);
}
if ($dateTo) {
    $stmt_get_report->bindValue(':dateTo', $dateTo);
}
$stmt_get_report->execute();
$report = $stmt_get_report->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include 'header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">


    <!-- DataTales Example -->
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
            <a href="print/all_records.php?dateFrom=<?= $dateFrom ?>&dateTo=<?= $dateTo ?>" target="_blank" class="fa fa-print text-secondary" style="font-size: 35px!important; color: inherit; text-decoration: none; cursor: pointer;"></a>
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
                                <td><?= htmlspecialchars($reports['route_from']); ?></td> <!-- Display port_name for route_from -->
                                <td><?= htmlspecialchars($reports['route_to']); ?></td> <!-- Display port_name for route_to -->
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