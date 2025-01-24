<?php
include '../../connection/database.php';

$dateFrom = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : null;
$dateTo = isset($_GET['dateTo']) ? $_GET['dateTo'] : null;

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
        SUM(DISTINCT r.ticket_price) AS total_price -- Use SUM(DISTINCT) to sum unique ticket_price values
    FROM 
        reports r
    LEFT JOIN 
        schedules s ON r.schedule_id = s.schedule_id
    LEFT JOIN 
        ships sh ON s.ship_id = sh.ship_id
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

$totalPrice = 0;
foreach ($report as $reports) {
    $totalPrice += $reports['total_price'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Printable Report</title>
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
    <div class="total-price">
        <p><strong>Total Price: <?= number_format($totalPrice, 2); ?> </strong></p>
    </div>

    <table>
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

    <script>
        window.print();
    </script>

</body>

</html>