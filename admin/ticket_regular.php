<?php
include '../connection/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}

$ticketQuery = "
    SELECT t.ticket_code, SUM(t.ticket_price) AS total_ticket_price, 
           t.ticket_status, t.ticket_date, t.contact_person, t.contact_number, 
           t.contact_email, t.contact_address,
           r1.route_from AS route_from_id, r2.route_to AS route_to_id,
           p1.port_name AS route_from, p2.port_name AS route_to,
           sh.ship_name,
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
    GROUP BY t.ticket_code, t.ticket_status, t.ticket_date, t.contact_person, 
             t.contact_number, t.contact_email, t.contact_address, 
             r1.route_from, r2.route_to, p1.port_name, p2.port_name, 
             sh.ship_name
";



$stmt = $conn->prepare($ticketQuery);
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<?php include 'header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">


    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Tickets</h6>
            <a class="btn btn-secondary" href="select_schedules.php"> Add New Ticket </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Schedule Date</th>
                            <th>Name</th>
                            <th>Ship</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Total Fare</th>
                            <th>Status</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $index => $ticket): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($ticket['ticket_date']) ?></td>
                                <td><?= htmlspecialchars($ticket['contact_person']) ?></td>
                                <td><?= htmlspecialchars($ticket['ship_name']) ?></td>
                                <td><?= htmlspecialchars($ticket['route_from']) ?></td>
                                <td><?= htmlspecialchars($ticket['route_to']) ?></td>
                                <td><?= htmlspecialchars($ticket['total_ticket_price']) ?></td>
                                <td><?= htmlspecialchars($ticket['ticket_status']) ?></td>
                                <td>
                                    <button class="btn btn-primary btn-sm view-passengers"
                                        data-ticket-code="<?= htmlspecialchars($ticket['ticket_code']) ?>">View Passengers</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>



</div>

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