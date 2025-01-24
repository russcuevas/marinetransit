<?php
include '../..//connection/database.php';
session_start();

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
        r.contact_address,
        r.contact_email,
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
    WHERE 1=1";

// Apply date filter
if ($dateFrom && $dateTo) {
    $get_report .= " AND r.report_date BETWEEN :dateFrom AND :dateTo";
} else {
    // If no specific date range is selected, filter for the current week
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

// Calculate total ticket price
$totalPrice = 0;
foreach ($report as $reports) {
    $totalPrice += $reports['total_price']; // Sum up the total ticket price
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Report - Print</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        .print-btn {
            margin: 20px 0;
        }

        .total-price {
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div id="printables">
        <h3>Weekly Report</h3>
        <h4>Week <?= $currentWeekNumber ?> (<?= $startOfWeek ?> to <?= $endOfWeek ?>)</h4>
        <h5>Total Sales: <?= number_format($totalPrice, 2); ?> </h5>

        <table class="table table-bordered">
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
                        <td><?= htmlspecialchars($reports['contact_address']); ?></td>
                        <td><?= htmlspecialchars($reports['contact_email']); ?></td>
                        <td><?= htmlspecialchars($reports['ticket_status']); ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <script>
        window.print();
    </script>
</body>

</html>