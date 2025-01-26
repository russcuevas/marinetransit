<?php
include '../..//connection/database.php';
session_start();
$currentDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

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

// Calculate the total ticket price for the current date
$totalPrice = 0;
foreach ($report as $reports) {
    $totalPrice += $reports['ticket_price'];
}

// Now get the sum of all ticket prices for the entire database
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
                        <td><?= htmlspecialchars($reports['route_from']); ?></td> <!-- Display route_from -->
                        <td><?= htmlspecialchars($reports['route_to']); ?></td> <!-- Display route_to -->
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