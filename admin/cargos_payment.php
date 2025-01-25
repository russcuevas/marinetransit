<?php
include '../connection/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}

try {
    $query = "
        SELECT 
            t.ticket_id, 
            t.ticket_date, 
            t.ticket_code, 
            t.ticket_price,
            t.ticket_type, 
            t.ticket_status, 
            t.schedule_id, 
            t.user_id, 
            t.ticket_vehicle, 
            t.ticket_date_return, 
            t.schedule_id_return, 
            t.contact_person, 
            t.contact_number, 
            t.contact_email, 
            t.contact_address,
            s.schedule_date, 
            s.schedule_time, 
            rf.port_name AS route_from,
            rt.port_name AS route_to,
            sh.ship_name
        FROM 
            tickets t
        JOIN 
            schedules s ON t.schedule_id = s.schedule_id
        JOIN 
            routes r ON s.route_id = r.route_id
        JOIN 
            ports rf ON r.route_from = rf.port_id
        JOIN 
            ports rt ON r.route_to = rt.port_id
        JOIN 
            ships sh ON s.ship_id = sh.ship_id
        WHERE 
            t.ticket_code LIKE 'CARGO-%'  -- Only show tickets that start with 'CARGO-'
        GROUP BY 
            t.ticket_code;
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>
<?php include 'header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Cargo Payment</h6>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ticket No.</th>
                            <th>Schedule Date/Time</th>
                            <th>Ship</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Total Fare</th>
                            <th>Status</th>
                            <th style="width: 22%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($tickets)): ?>
                            <?php foreach ($tickets as $ticket): ?>
                                <tr>
                                    <td><?php echo $ticket['ticket_id']; ?></td>
                                    <td><?php echo $ticket['ticket_code']; ?></td>
                                    <td><?php echo $ticket['ticket_date'] . " / " . $ticket['schedule_time']; ?></td>
                                    <td><?php echo $ticket['ship_name']; ?></td>
                                    <td><?php echo $ticket['route_from']; ?></td>
                                    <td><?php echo $ticket['route_to']; ?></td>
                                    <td><?php echo $ticket['ticket_price']; ?></td>
                                    <td><?php echo $ticket['ticket_status']; ?></td>
                                    <td>
                                        <?php if ($ticket['ticket_status'] == 'Cancelled') { ?>
                                            <a href="#" class="btn btn-success view"
                                                data-ticket_code="<?php echo $ticket['ticket_code']; ?>"
                                                data-from="<?php echo $ticket['route_from']; ?>"
                                                data-to="<?php echo $ticket['route_to']; ?>">View</a>
                                            <a href="#" class="btn btn-danger delete-ticket" data-id="<?php echo $ticket['ticket_id']; ?>">Delete</a>
                                        <?php } else { ?>
                                            <a href="#" class="btn btn-success view"
                                                data-id="<?php echo $ticket['ticket_id']; ?>"
                                                data-from="<?php echo $ticket['route_from']; ?>"
                                                data-to="<?php echo $ticket['route_to']; ?>">View</a>
                                            <a href="#" class="btn btn-warning mark-paid" data-id="<?php echo $ticket['ticket_id']; ?>">Paid</a>
                                            <a href="#" class="btn btn-danger cancel-ticket" data-id="<?php echo $ticket['ticket_id']; ?>">Cancel</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9">No records found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

<!-- View Passenger Modal-->
<div class="modal fade" id="viewPassenger" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                        <!-- Ticket content will be rendered here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="assets/admin/vendor/jquery/jquery.min.js"></script>

<?php include 'footer.php' ?>

<!-- CRUD -->
<script>
    $(document).on('click', '.mark-paid', function() {
        var ticket_id = $(this).data('id');
        if (confirm('Are you sure this ticket is paid?')) {
            $.ajax({
                type: 'POST',
                url: 'mark_paid.php',
                data: {
                    ticket_id: ticket_id
                },
                success: function(response) {
                    var res = JSON.parse(response);

                    if (res.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Ticket status has been updated to Paid.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    } else if (res.status === 'failure') {
                        // Check if the message is about the ticket not being paid
                        if (res.message === 'Ticket is not paid') {
                            Swal.fire({
                                title: 'Failed!',
                                text: 'This ticket has not been paid yet.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                title: 'Failed!',
                                text: res.message || 'Failed to update ticket status. Please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
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
    $(document).on('click', '.cancel-ticket', function() {
        var ticket_id = $(this).data('id');
        if (confirm('Are you sure you want to cancel this ticket?')) {
            $.ajax({
                type: 'POST',
                url: 'cancel_ticket.php',
                data: {
                    ticket_id: ticket_id
                },
                success: function(response) {
                    var res = JSON.parse(response);

                    if (res.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Ticket has been cancelled.',
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
                            text: res.message || 'Failed to cancel the ticket. Please try again.',
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
    $(document).on('click', '.delete-ticket', function() {
        var ticket_id = $(this).data('id');
        if (confirm('Are you sure you want to delete this ticket?')) {
            $.ajax({
                type: 'POST',
                url: 'delete_ticket.php',
                data: {
                    ticket_id: ticket_id
                },
                success: function(response) {
                    var res = JSON.parse(response);

                    if (res.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Ticket has been deleted.',
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
                            text: res.message || 'Failed to delete the ticket. Please try again.',
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
    var base_url = "http://localhost/marinetransit/";

    $(document).on('click', '.view', function() {
        var id = $(this).data('id');
        var ticket_from = $(this).data('from');
        var ticket_to = $(this).data('to');

        console.log(id);
        console.log(ticket_from);
        console.log(ticket_to);

        $('#viewPassenger').modal('show');
        $.ajax({
            type: 'POST',
            url: 'http://localhost/marinetransit/admin/getcargo.php', // Change to the cargo-specific endpoint
            dataType: 'json',
            data: {
                id: id
            },
            success: function(response) {
                console.log(response);
                $('#printTicket').empty();

                if (response.type === 'cargo') {
                    var tr = $('<div class="py-3 px-0 my-3" style="border: 1px solid black; color: black">');
                    tr.append('<div style="display: flex; margin-bottom: 30px">' +
                        '<div class="col-xl-6 col-md-6">' +
                        '<img src="http://localhost/marinetransit/admin/assets/admin/img/ssf.png" style="height: 50px; width: auto;">' +
                        '</div>' +
                        '</div></div>');

                    tr.append('<h4 class="text-center"><b>CARGO DETAILS</b></h4>' +
                        '<h6 class="text-center">Super Shuttle Ferry</h6>' +
                        '<h6 class="text-center">38 Gorordo Avenue, Cebu City</h6>' +
                        '<h6 class="text-center">Tel: No. (32) 412-7688</h6>');

                    $.each(response.data, function(index, item) {
                        tr.append('<div class="d-flex justify-content-start" style="border-top: 1px dashed black">' +
                            '<div class="col-xl-4 col-md-4">' +
                            '<div class="h-100 py-2 bg-transparent">' +
                            '<h6 class="px-5 text-left">Route</h6>' +
                            '<h6 class="px-5 text-left">Cargo Type</h6>' +
                            '<h6 class="px-5 text-left">Vessel</h6>' +
                            '<h6 class="px-5 text-left">Departure</h6>' +
                            '<h6 class="mt-3 px-5 text-left">Ticket No.</h6>' +
                            '</div></div>' +
                            '<div class="col-xl-8 col-md-8">' +
                            '<h6 class="text-left mt-2">: ' + ticket_from + ' - ' + ticket_to + '</h6>' +
                            '<h6 class="text-left">: ' + item.accomodation_name + '</h6>' +
                            '<h6 class="text-left">: ' + item.ship_name + '</h6>' +
                            '<h6 class="text-left">: ' + item.ticket_date + '</h6>' +
                            '<h6 class="mt-3 text-left">: ' + item.ticket_code + '</h6>' +
                            '</div></div></div>');
                    });

                    $('#printTicket').append(tr);

                } else if (response.type === 'cargo') {
                    var tr = $('<div class="py-3 px-0 my-3" style="border: 1px solid black; color: black">');
                    tr.append('<div style="display: flex; margin-bottom: 30px">' +
                        '<div class="col-xl-6 col-md-6">' +
                        '<img src="http://localhost/marinetransit/admin/assets/admin/img/ssf.png" style="height: 50px; width: auto;">' +
                        '</div>' +
                        '</div></div>');

                    tr.append('<h4 class="text-center"><b>CARGO DETAILS</b></h4>' +
                        '<h6 class="text-center">Super Shuttle Ferry</h6>' +
                        '<h6 class="text-center">38 Gorordo Avenue, Cebu City</h6>' +
                        '<h6 class="text-center">Tel: No. (32) 412-7688</h6>');

                    $.each(response.data, function(index, item) {
                        tr.append('<div class="d-flex justify-content-start" style="border-top: 1px dashed black">' +
                            '<div class="col-xl-4 col-md-4">' +
                            '<div class="h-100 py-2 bg-transparent">' +
                            '<h6 class="px-5 text-left">Route</h6>' +
                            '<h6 class="px-5 text-left">Cargo Type</h6>' +
                            '<h6 class="px-5 text-left">Model/Brand</h6>' +
                            '<h6 class="px-5 text-left">Plate No.</h6>' +
                            '<h6 class="px-5 text-left">Vessel</h6>' +
                            '<h6 class="px-5 text-left">Departure</h6>' +
                            '<h6 class="mt-3 px-5 text-left">Ticket No.</h6>' +
                            '</div></div>' +
                            '<div class="col-xl-8 col-md-8">' +
                            '<h6 class="text-left">: ' + ticket_from + '-' + ticket_to + ' </h6>' +
                            '<h6 class="text-left">: ' + item.accomodation_name + '</h6>' +
                            '<h6 class="text-left">: ' + item.passenger_cargo_brand + '</h6>' +
                            '<h6 class="text-left">: ' + item.passenger_cargo_plate + '</h6>' +
                            '<h6 class="text-left">: ' + item.ship_name + '</h6>' +
                            '<h6 class="text-left">: ' + item.ticket_date + '</h6>' +
                            '<h6 class="mt-3 text-left">: ' + item.ticket_code + '</h6>' + '</div></div></div>')
                    });

                    $('#printTicket').append(tr);
                }
            }
        });
    });

    $(document).on('click', '#print', function() {
        var printContent = $('#printTicket').html();

        var iframe = document.createElement('iframe');
        iframe.style.position = 'absolute';
        iframe.style.width = '0px';
        iframe.style.height = '0px';
        iframe.style.border = 'none';

        document.body.appendChild(iframe);

        var iframeDoc = iframe.contentWindow.document;
        iframeDoc.open();
        iframeDoc.write('<html><head><title>Print</title>');

        iframeDoc.write('<style>');
        iframeDoc.write('@media print {');
        iframeDoc.write('body { font-family: Arial, sans-serif; font-size: 12pt; color: black; }');
        iframeDoc.write('.container { width: 100%; color: black; }');
        iframeDoc.write('.d-flex { display: flex; }');
        iframeDoc.write('.justify-content-start { justify-content: flex-start; }');
        iframeDoc.write('.justify-content-between { justify-content: space-between; }');
        iframeDoc.write('.col-xl-4, .col-md-4 { width: 40%; padding: 10px; }');
        iframeDoc.write('.col-xl-8, .col-md-8 { width: 60%; padding: 10px; }');
        iframeDoc.write('.py-3 { padding-top: 1rem; padding-bottom: 1rem; }');
        iframeDoc.write('.px-0 { padding-left: 0; padding-right: 0; }');
        iframeDoc.write('.text-center { text-align: center; }');
        iframeDoc.write('.text-left { text-align: left; }');
        iframeDoc.write('.h6 { font-size: 14px; }');
        iframeDoc.write('h6 { margin: 5px 0; }');
        iframeDoc.write('h4 { font-size: 16px; font-weight: bold; }');
        iframeDoc.write('.border-top { border-top: 1px dashed black; }');
        iframeDoc.write('</style>');
        iframeDoc.write('</head><body>');

        iframeDoc.write('<div class="container">' + printContent + '</div>');
        iframeDoc.write('</body></html>');
        iframeDoc.close();

        iframe.contentWindow.focus();
        iframe.contentWindow.print();

        document.body.removeChild(iframe);
    });
</script>