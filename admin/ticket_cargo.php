<?php
include '../connection/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}

$ticket_query = "
    SELECT t.ticket_code, t.ticket_date, t.qr_code, t.ticket_status, t.ticket_price, 
           t.contact_person,  -- Add contact_person to the select
           sh.ship_name, c.cargo_name, a.accomodation_name,
           r1.route_from AS route_from_id, r2.route_to AS route_to_id,
           p1.port_name AS route_from, p2.port_name AS route_to
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
";

$ticket_stmt = $conn->prepare($ticket_query);
$ticket_stmt->execute();
$tickets = $ticket_stmt->fetchAll(PDO::FETCH_ASSOC);

$cargo_query = "
    SELECT pc.passenger_cargo_id, pc.passenger_cargo_brand, pc.passenger_cargo_plate, 
           a.accomodation_name, sh.ship_name, t.ticket_date, t.ticket_code
    FROM passenger_cargos pc
    JOIN tickets t ON pc.ticket_id = t.ticket_id
    JOIN cargos c ON pc.accomodation_id = c.cargo_id
    LEFT JOIN accomodations a ON pc.accomodation_id = a.accomodation_id
    JOIN schedules s ON t.schedule_id = s.schedule_id
    JOIN ships sh ON s.ship_id = sh.ship_id
";

$cargo_stmt = $conn->prepare($cargo_query);
$cargo_stmt->execute();
$cargos = $cargo_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Cargo Tickets</h6>
            <a class="btn btn-secondary" href="select_schedules_car.php">Add New Ticket</a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>QR</th>
                            <th>Schedule Date/Time</th>
                            <th>Name</th>
                            <th>Cargo Type</th>
                            <th>Vessel</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $index => $ticket): ?>
                            <tr>
                                <td><img style="height: 70px;" src="../qr_codes/<?php echo htmlspecialchars($ticket['qr_code']); ?>" alt="QR Code"></td>
                                <td><?php echo htmlspecialchars($ticket['ticket_date']); ?></td>
                                <td><?php echo htmlspecialchars($ticket['contact_person']); ?></td>
                                <td>
                                    <?php
                                    foreach ($cargos as $cargo):
                                        if ($cargo['ticket_code'] === $ticket['ticket_code']):
                                    ?>
                                            <p><?php echo htmlspecialchars($cargo['accomodation_name']); ?></p>
                                    <?php endif;
                                    endforeach; ?>
                                </td>
                                <td><?php echo htmlspecialchars($ticket['ship_name']); ?></td>
                                <td><?php echo htmlspecialchars($ticket['route_from']); ?></td>
                                <td><?php echo htmlspecialchars($ticket['route_to']); ?></td>
                                <td><?php echo htmlspecialchars($ticket['ticket_status']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#viewPassenger" data-ticket-code="<?php echo htmlspecialchars($ticket['ticket_code']); ?>">
                                        View Passengers
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->




<!-- View Passenger Modal-->
<div class="modal fade" id="viewPassenger" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">List of Passenger</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">

                <form id="AddPassengerForm" class="user" method="POST">
                    <div class="row">
                        <!-- Left-->
                        <div class="col-xl-12 col-md-6">


                            <div class="form-group row">
                                <div class="col-md-12">

                                    <div class="d-flex justify-content-between">
                                        <h6 class="m-0 font-weight-bold text-primary"></h6>
                                        <a class="btn btn-primary" id="print"><i class="fas fa-print"></i> </a>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group row">

                                <div class="col-md-12">
                                    <div class="mb-3" id="printTicket">





                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
            </div>

            </form>
        </div>
    </div>
</div>
<script src="assets/admin/vendor/jquery/jquery.min.js"></script>
<?php include 'footer.php' ?>