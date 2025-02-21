<?php
include '../connection/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:cashier_login.php');
}

$ticket_query = "
    SELECT t.ticket_code, t.ticket_date, t.qr_code, t.ticket_status, t.ticket_price, 
           t.contact_person,  -- Add contact_person to the select
           sh.ship_name, c.cargo_name, a.accomodation_name,
           r1.route_from AS route_from_id, r2.route_to AS route_to_id,
           p1.port_name AS route_from, p2.port_name AS route_to,
           s.schedule_time  -- Make sure to select schedule_time here
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
                            <?php if ($ticket['ticket_status'] == 'Pending' || $ticket['ticket_status'] == 'Paid'): ?>
                                <tr>
                                    <td><img style="height: 70px;" src="../qr_codes/<?php echo htmlspecialchars($ticket['qr_code']); ?>" alt="QR Code"></td>
                                    <td><?php echo $ticket['ticket_date'] . " / " . $ticket['schedule_time']; ?></td>
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
                                        <div class="d-flex">
                                            <a href="view_cargo_ticket.php?ticket_code=<?php echo urlencode($ticket['ticket_code']); ?>" class="btn btn-primary mr-2">View</a>
                                            <?php if ($ticket['ticket_status'] != 'Paid'): ?>

                                                <a href="#" class="btn btn-warning mr-2 mark-paid-ticket-cargo" data-id="<?php echo $ticket['ticket_code']; ?>">Paid</a>
                                                <a href="#" class="btn btn-danger cancel-ticket-cargo" data-id="<?php echo $ticket['ticket_code']; ?>">Cancel</a>
                                            <?php endif; ?>

                                        </div>
                                    </td>

                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->


<script src="assets/admin/vendor/jquery/jquery.min.js"></script>
<?php include 'footer.php' ?>

<script>
    $(document).on('click', '.mark-paid-ticket-cargo', function() {
        var ticket_code = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: 'Are you sure this ticket is paid?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, mark as paid',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we update the ticket status.',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading(); // Show loading spinner
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: 'mark_paid_ticket_cargo.php',
                    data: {
                        ticket_code: ticket_code
                    },
                    success: function(response) {
                        var res = JSON.parse(response);
                        Swal.close(); // Close the loading Swal

                        if (res.status === 'success') {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Booking confirmation successfully paid!',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Failed!',
                                text: res.message || 'Failed to update ticket status or insert report.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function() {
                        Swal.close(); // Close the loading Swal
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred. Please try again later.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    });
</script>


<script>
    $(document).on('click', '.cancel-ticket-cargo', function() {
        var ticket_code = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you really want to cancel this ticket?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, cancel it',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we cancel the ticket.',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading(); // Show loading spinner
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: 'cancel_ticket_cargo.php',
                    data: {
                        ticket_code: ticket_code
                    },
                    success: function(response) {
                        try {
                            console.log("Raw Response:", response); // Log response before parsing
                            var res = typeof response === "object" ? response : JSON.parse(response);
                            console.log("Parsed Response:", res); // Log parsed response

                            Swal.close(); // Close loading state

                            if (res.status === "success") {
                                Swal.fire({
                                    title: "Success!",
                                    text: res.message || "Ticket successfully cancelled.",
                                    icon: "success",
                                    confirmButtonText: "OK",
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: "Failed!",
                                    text: res.message || "Failed to cancel the ticket.",
                                    icon: "error",
                                    confirmButtonText: "OK",
                                });
                            }
                        } catch (error) {
                            console.error("JSON Parse Error:", error, response);
                            Swal.fire({
                                title: "Error!",
                                text: "Invalid server response. Please try again later.",
                                icon: "error",
                                confirmButtonText: "OK",
                            });
                        }
                    },
                    error: function() {
                        Swal.close();
                        Swal.fire({
                            title: "Error!",
                            text: "An error occurred. Please try again later.",
                            icon: "error",
                            confirmButtonText: "OK",
                        });
                    }
                });
            }
        });
    });
</script>