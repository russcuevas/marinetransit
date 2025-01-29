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
           sh.ship_name, s.schedule_time,  -- Added schedule_time here
           GROUP_CONCAT(p.passenger_fname, ' ', p.passenger_lname ORDER BY p.passenger_fname SEPARATOR ', ') AS passengers_names,
           GROUP_CONCAT(p.passenger_contact ORDER BY p.passenger_fname SEPARATOR ', ') AS passengers_contacts,
           GROUP_CONCAT(p.passenger_type ORDER BY p.passenger_fname SEPARATOR ', ') AS passengers_types,
           GROUP_CONCAT(p.passenger_gender ORDER BY p.passenger_fname SEPARATOR ', ') AS passengers_genders,
           t.qr_code
    FROM tickets t
    JOIN schedules s ON t.schedule_id = s.schedule_id
    JOIN ships sh ON s.ship_id = sh.ship_id
    JOIN routes r1 ON s.route_id = r1.route_id
    JOIN routes r2 ON s.route_id = r2.route_id
    JOIN ports p1 ON r1.route_from = p1.port_id
    JOIN ports p2 ON r2.route_to = p2.port_id
    LEFT JOIN passengers p ON t.ticket_id = p.ticket_id
    WHERE t.ticket_code LIKE '%PASSENGER%'  -- Filtering for ticket codes that contain 'PASSENGER'
    GROUP BY t.ticket_code, t.ticket_status, t.ticket_date, t.contact_person, 
             t.contact_number, t.contact_email, t.contact_address, 
             r1.route_from, r2.route_to, p1.port_name, p2.port_name, 
             sh.ship_name, s.schedule_time, t.qr_code  -- Group by schedule_time
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
                            <th>QR</th>
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
                            <?php if ($ticket['ticket_status'] == 'Pending' || $ticket['ticket_status'] == 'Paid'): ?>
                                <tr>
                                    <td><img style="height: 70px;" src="../qr_codes/<?php echo htmlspecialchars($ticket['qr_code']); ?>" alt="QR Code"></td>
                                    <td><?php echo $ticket['ticket_date'] . " / " . $ticket['schedule_time']; ?></td>
                                    <td><?= htmlspecialchars($ticket['contact_person']) ?></td>
                                    <td><?= htmlspecialchars($ticket['ship_name']) ?></td>
                                    <td><?= htmlspecialchars($ticket['route_from']) ?></td>
                                    <td><?= htmlspecialchars($ticket['route_to']) ?></td>
                                    <td><?= htmlspecialchars($ticket['total_ticket_price']) ?></td>
                                    <td><?= htmlspecialchars($ticket['ticket_status']) ?></td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="view_passenger_ticket.php?ticket_code=<?php echo urlencode($ticket['ticket_code']); ?>" class="btn btn-primary mr-2">View</a>
                                            <?php if ($ticket['ticket_status'] != 'Paid'): ?>
                                                <a href="#" class="btn btn-warning mr-2 mark-paid-ticket-passengers" data-id="<?php echo $ticket['ticket_code']; ?>">Paid</a>
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


<script src="assets/admin/vendor/jquery/jquery.min.js"></script>

<?php include 'footer.php' ?>

<script>
    $(document).on('click', '.mark-paid-ticket-passengers', function() {
        var ticket_code = $(this).data('id');
        if (confirm('Are you sure this ticket is paid?')) {
            $.ajax({
                type: 'POST',
                url: 'mark_paid_ticket_passengers.php',
                data: {
                    ticket_code: ticket_code
                },
                success: function(response) {
                    var res = JSON.parse(response);

                    if (res.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Booking confirmation successfully paid!',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    } else if (res.status === 'failure') {
                        Swal.fire({
                            title: 'Failed!',
                            text: res.message || 'Failed to update ticket status or insert report.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    } else if (res.status === 'error') {
                        Swal.fire({
                            title: 'Error!',
                            text: res.message || 'An unexpected error occurred. Please try again later.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
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
</script>

<script>
    $(document).on('click', '.cancel-ticket-cargo', function() {
        var ticket_code = $(this).data('id');
        if (confirm('Are you sure you want to cancel all tickets with this code?')) {
            $.ajax({
                type: 'POST',
                url: 'cancel_ticket_cargo.php',
                data: {
                    ticket_code: ticket_code
                },
                success: function(response) {
                    var res = JSON.parse(response);

                    if (res.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Cancelled Booking Payment',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    } else if (res.status === 'failure') {
                        Swal.fire({
                            title: 'Failed!',
                            text: res.message || 'Failed to cancel tickets.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    } else if (res.status === 'error') {
                        Swal.fire({
                            title: 'Error!',
                            text: res.message || 'An unexpected error occurred. Please try again later.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
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
</script>