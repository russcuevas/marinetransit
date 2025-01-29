<?php
include '../connection/database.php';
session_start();

if (!isset($_GET['ticket_code'])) {
    die('Ticket code not provided.');
}

$ticket_code = $_GET['ticket_code'];

$ticket_query = "
    SELECT t.ticket_code, t.ticket_date, t.qr_code, t.ticket_status, t.ticket_price, 
           t.contact_person, t.contact_number, t.contact_email, 
           sh.ship_name, c.cargo_name, a.accomodation_name,
           r1.route_from AS route_from_id, r2.route_to AS route_to_id,
           p1.port_name AS route_from, p2.port_name AS route_to,
           s.schedule_time  -- Added schedule_time
    FROM tickets t
    JOIN schedules s ON t.schedule_id = s.schedule_id
    JOIN ships sh ON s.ship_id = sh.ship_id
    JOIN passenger_cargos pc ON t.ticket_id = pc.ticket_id
    JOIN cargos c ON pc.accomodation_id = c.cargo_id
    LEFT JOIN accomodations a ON pc.accomodation_id = a.accomodation_id
    JOIN routes r1 ON s.route_id = r1.route_id
    JOIN routes r2 ON s.route_id = r2.route_id
    JOIN ports p1 ON r1.route_from = p1.port_id
    JOIN ports p2 ON r2.route_to = p2.port_id
    WHERE t.ticket_code = :ticket_code
";



$stmt = $conn->prepare($ticket_query);
$stmt->execute(['ticket_code' => $ticket_code]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

// If ticket not found
if (!$ticket) {
    die('Ticket not found.');
}
?>

<?php include 'header.php'; ?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Ticket Details</h6>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"></h6>
                <a href="print/print_cargo_ticket.php?ticket_code=<?php echo $ticket['ticket_code']; ?>" class="btn btn-primary" id="print" target="_blank"><i class="fas fa-print"></i> </a>
            </div>
            <div class="py-3 px-0 my-3" style="border: 1px solid black; color: black">
                <div style="display: flex; margin-bottom: 30px">
                    <div class="col-xl-6 col-md-6">
                        <img src="assets/admin/img/ssf.png" alt="QR Code" style="height: 50px; width: auto;">
                    </div>
                    <div class="col-xl-6 col-md-6">
                        <h5><b>
                            </b></h5>
                        <p style="text-align: right;">Departure: <?php echo $ticket['ticket_date'] . " / " . $ticket['schedule_time']; ?>
                        </p>
                    </div>
                </div>

                <div style="display: flex; margin-bottom: 10px">
                    <div class="col-xl-4 col-md-4">
                        <p class="m-0" style="font-weight: bold">Cargo Type:
                            <?php echo htmlspecialchars($ticket['cargo_name']); ?>
                        </p>
                        <p class="m-0" style="font-weight: bold">Route:
                            <?php echo htmlspecialchars($ticket['route_from']); ?> -- <?php echo htmlspecialchars($ticket['route_to']); ?>
                        </p>
                        <p class="m-0" style="font-weight: bold">Vessel: <?php echo htmlspecialchars($ticket['ship_name']); ?>
                        </p>
                    </div>
                    <div class="col-xl-4 col-md-4">
                        <p class="m-0" style="font-weight: bold">Contact No.:
                            <?php echo htmlspecialchars($ticket['contact_number']); ?>
                        </p>
                        <p class="m-0" style="font-weight: bold">Email Address:
                            <?php echo htmlspecialchars($ticket['contact_email']); ?>
                        </p>
                    </div>
                    <div class="col-xl-4 col-md-4">
                        <p class="m-0" style="font-weight: bold">Do not fold this image</p>
                        <img src="../qr_codes/<?php echo htmlspecialchars($ticket['qr_code']); ?>" alt="QR Code" style="height: 150px; width: auto;">
                    </div>
                </div>

                <div style="display: flex;">
                    <div class="col-xl-4 col-md-4">
                        <p class="m-0" style="font-weight: bold">Accommodation: ECONOMY</p>
                    </div>
                    <div class="col-xl-4 col-md-4">
                        <p class="m-0" style="font-weight: bold">Ticket Price: <?php echo htmlspecialchars($ticket['ticket_price']); ?> - Php
                        </p>
                    </div>
                    <div class="col-xl-4 col-md-4">
                        <p class="m-0" style="font-weight: bold">Ticket No.: <?php echo htmlspecialchars($ticket['ticket_code']); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

<?php include 'footer.php'; ?>