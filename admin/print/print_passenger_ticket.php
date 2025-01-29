<?php
include '../../connection/database.php';

if (!isset($_GET['ticket_code'])) {
    echo "Invalid Ticket Code";
    exit();
}

$ticket_code = $_GET['ticket_code'];

$ticketQuery = "
SELECT t.ticket_code, t.ticket_status, t.ticket_date, t.contact_person, t.contact_number, 
       t.contact_email, t.contact_address, t.qr_code,
       r1.route_from AS route_from_id, r2.route_to AS route_to_id,
       p1.port_name AS route_from, p2.port_name AS route_to,
       sh.ship_name, s.schedule_time, SUM(t.ticket_price) AS total_ticket_price,
       GROUP_CONCAT(p.passenger_fname, ' ', p.passenger_lname ORDER BY p.passenger_fname SEPARATOR ', ') AS passengers_names,
       GROUP_CONCAT(p.passenger_contact ORDER BY p.passenger_fname SEPARATOR ', ') AS passengers_contacts,
       GROUP_CONCAT(p.passenger_type ORDER BY p.passenger_fname SEPARATOR ', ') AS passengers_types,
       GROUP_CONCAT(p.passenger_gender ORDER BY p.passenger_fname SEPARATOR ', ') AS passengers_genders
FROM tickets t
JOIN schedules s ON t.schedule_id = s.schedule_id
JOIN ships sh ON s.ship_id = sh.ship_id
JOIN routes r1 ON s.route_id = r1.route_id
JOIN routes r2 ON s.route_id = r2.route_id
JOIN ports p1 ON r1.route_from = p1.port_id
JOIN ports p2 ON r2.route_to = p2.port_id
LEFT JOIN passengers p ON t.ticket_id = p.ticket_id
WHERE t.ticket_code = ?
GROUP BY t.ticket_code;
";

$stmt = $conn->prepare($ticketQuery);
$stmt->execute([$ticket_code]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    echo "Ticket not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Details - Print</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* Print-specific styles */
        @media print {
            body * {
                visibility: hidden;
            }

            #printableArea,
            #printableArea * {
                visibility: visible;
            }

            #printableArea {
                position: absolute;
                left: 0;
                top: 0;
                border: 2px solid black;
            }

            .btn {
                display: none;
                /* Hide buttons during printing */
            }
        }

        /* General styles for printable area */
        .container-fluid {
            font-family: Arial, sans-serif;
        }


        .card-header {
            font-size: 1.25rem;
            font-weight: bold;
        }

        .card-body {
            padding: 15px;
        }

        .py-3,
        .px-0,
        .my-3 {
            padding: 15px;
        }

        .col-xl-6,
        .col-md-6 {
            width: 50%;
        }

        .col-xl-4,
        .col-md-4 {
            width: 33.33%;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        p {
            margin: 5px 0;
        }

        .font-weight-bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div id="printableArea">
        <div class="container-fluid">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ticket Details</h6>
                </div>
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                        <img src="../assets/admin/img/ssf.png" alt="Logo" style="height: 50px; width: auto;">
                        <div style="text-align: right;">
                            <p>Departure: <?php echo $ticket['ticket_date'] . " / " . $ticket['schedule_time']; ?></p>
                        </div>
                    </div>

                    <div style="display: flex; margin-bottom: 20px;">
                        <div class="col-xl-4 col-md-4">
                            <p class="m-0" style="font-weight: bold">Route: <?php echo htmlspecialchars($ticket['route_from']); ?> -- <?php echo htmlspecialchars($ticket['route_to']); ?></p>
                            <p class="m-0" style="font-weight: bold">Vessel: <?php echo htmlspecialchars($ticket['ship_name']); ?></p>
                        </div>
                        <div class="col-xl-4 col-md-4">
                            <p class="m-0" style="font-weight: bold">Contact No.: <?php echo htmlspecialchars($ticket['contact_number']); ?></p>
                            <p class="m-0" style="font-weight: bold">Email Address: <?php echo htmlspecialchars($ticket['contact_email']); ?></p>
                        </div>
                        <div class="col-xl-4 col-md-4">
                            <p class="m-0" style="font-weight: bold">Do not fold this image</p>
                            <img src="../../qr_codes/<?php echo htmlspecialchars($ticket['qr_code']); ?>" alt="QR Code" style="height: 150px; width: auto;">
                        </div>
                    </div>

                    <div style="display: flex; margin-bottom: 20px;">
                        <div class="col-xl-4 col-md-4">
                            <p class="m-0" style="font-weight: bold">Accommodation: ECONOMY</p>
                        </div>
                        <div class="col-xl-4 col-md-4">
                            <p class="m-0" style="font-weight: bold">Ticket Price: <?php echo htmlspecialchars($ticket['total_ticket_price']); ?> - Php</p>
                        </div>
                        <div class="col-xl-4 col-md-4">
                            <p class="m-0" style="font-weight: bold">Ticket No.: <?php echo htmlspecialchars($ticket['ticket_code']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.print();
        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>

</html>