<?php
include '../..//connection/database.php';
session_start();
$currentDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

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
        r.ticket_price -- Don't use SUM, just take the price for each unique ticket_code
    FROM 
        reports r
    LEFT JOIN 
        schedules s ON r.schedule_id = s.schedule_id
    LEFT JOIN 
        ships sh ON s.ship_id = sh.ship_id
    WHERE 1=1";

$get_report .= " AND DATE(r.report_date) = :currentDate";

$get_report .= " GROUP BY r.ticket_code";

$stmt_get_report = $conn->prepare($get_report);
$stmt_get_report->bindValue(':currentDate', $currentDate);

$stmt_get_report->execute();
$report = $stmt_get_report->fetchAll(PDO::FETCH_ASSOC);

$totalPrice = 0;
foreach ($report as $reports) {
    $totalPrice += $reports['ticket_price'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Report - Print</title>
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

        .header {
            margin-bottom: 20px;
        }

        .header h3,
        .header h4,
        .header h5 {
            margin: 0;
        }
    </style>
</head>

<body>
    <div id="printables" class="header">
        <h3>Daily Report</h3>
        <h4>Date: <?= $currentDate ?></h4>
        <h4>Total Sales: <?= number_format($totalPrice, 2); ?></h4>
        <br>
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