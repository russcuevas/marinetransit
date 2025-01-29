<?php
require_once('../phpqrcode/qrlib.php');
include '../connection/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_GET['schedule_id']) && !empty($_GET['schedule_id'])) {
    $schedule_id = $_GET['schedule_id'];

    $query = "
    SELECT 
        s.schedule_id, 
        sh.ship_name, 
        s.schedule_date, 
        s.schedule_time, 
        p_from.port_name AS route_from, 
        p_to.port_name AS route_to
    FROM schedules s
    JOIN ships sh ON s.ship_id = sh.ship_id
    JOIN routes r ON s.route_id = r.route_id
    JOIN ports p_from ON r.route_from = p_from.port_id
    JOIN ports p_to ON r.route_to = p_to.port_id
    WHERE s.schedule_id = :schedule_id
";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':schedule_id', $schedule_id);
    $stmt->execute();
    $schedule = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$schedule) {
        header('Location: select_schedules.php');
        exit;
    }
} else {
    header('Location: select_schedules.php');
    exit;
}


$query = "
    SELECT sa.accomodation_id, a.accomodation_name, sa.net_fare
    FROM schedule_accom sa
    JOIN accomodations a ON sa.accomodation_id = a.accomodation_id
    WHERE sa.schedule_id = :schedule_id
    AND a.accomodation_type = 'passenger'
";
$stmt = $conn->prepare($query);
$stmt->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
$stmt->execute();
$accommodation_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['book'])) {
    $ticket_code = uniqid('PASSENGER-');
    $ticket_date = $_POST['ticket_date'];
    $ticket_status = 'Paid';
    $contact_person = $_POST['contact_person'];
    $contact_number = $_POST['contact_number'];
    $contact_email = $_POST['contact_email'];
    $contact_address = $_POST['contact_address'];

    $ticket_ids = [];

    if (isset($_POST['passenger_fname']) && is_array($_POST['passenger_fname']) && !empty($_POST['passenger_fname'])) {
        foreach ($_POST['passenger_fname'] as $index => $first_name) {
            $last_name = $_POST['passenger_lname'][$index];
            $middle_name = $_POST['passenger_mname'][$index];
            $birthdate = $_POST['passenger_bdate'][$index];
            $contact = $_POST['passenger_contact'][$index];
            $address = $_POST['passenger_address'][$index];
            $gender = $_POST['passenger_gender'][$index];
            $accommodation_type = $_POST['passenger_type'][$index];
            $accommodation_fare = $_POST['fare'][$index];

            $ticket_price = $accommodation_fare;
            $insertTicketQuery = "
                INSERT INTO tickets (ticket_date, ticket_code, ticket_price, ticket_type, ticket_status, schedule_id, contact_person, contact_number, contact_email, contact_address)
                VALUES (:ticket_date, :ticket_code, :ticket_price, :ticket_type, :ticket_status, :schedule_id, :contact_person, :contact_number, :contact_email, :contact_address)
            ";
            $stmt = $conn->prepare($insertTicketQuery);
            $stmt->bindParam(':ticket_date', $ticket_date);
            $stmt->bindParam(':ticket_code', $ticket_code);
            $stmt->bindParam(':ticket_price', $ticket_price);
            $stmt->bindParam(':ticket_type', $accommodation_type);
            $stmt->bindParam(':ticket_status', $ticket_status);
            $stmt->bindParam(':schedule_id', $schedule_id);
            $stmt->bindParam(':contact_person', $contact_person);
            $stmt->bindParam(':contact_number', $contact_number);
            $stmt->bindParam(':contact_email', $contact_email);
            $stmt->bindParam(':contact_address', $contact_address);
            $stmt->execute();

            $ticket_id = $conn->lastInsertId();
            $ticket_ids[] = $ticket_id;

            $insertPassengerQuery = "
                INSERT INTO passengers (ticket_id, passenger_fname, passenger_mname, passenger_lname, passenger_bdate, passenger_contact, passenger_address, passenger_type, passenger_gender)
                VALUES (:ticket_id, :first_name, :middle_name, :last_name, :birthdate, :contact, :address, :passenger_type, :gender)
            ";
            $stmt = $conn->prepare($insertPassengerQuery);
            $stmt->bindParam(':ticket_id', $ticket_id);
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':middle_name', $middle_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':birthdate', $birthdate);
            $stmt->bindParam(':contact', $contact);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':passenger_type', $accommodation_type);
            $stmt->bindParam(':gender', $gender);
            $stmt->execute();
        }

        foreach ($ticket_ids as $ticket_id) {
            $ticketQuery = "
        SELECT ticket_price, ticket_code FROM tickets WHERE ticket_id = :ticket_id
    ";
            $stmt = $conn->prepare($ticketQuery);
            $stmt->bindParam(':ticket_id', $ticket_id);
            $stmt->execute();
            $ticketData = $stmt->fetch(PDO::FETCH_ASSOC);
            $passengerQuery = "
        SELECT passenger_type FROM passengers WHERE ticket_id = :ticket_id
    ";
            $stmt = $conn->prepare($passengerQuery);
            $stmt->bindParam(':ticket_id', $ticket_id);
            $stmt->execute();
            $passenger_types = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($passenger_types as $accommodation_type) {
                $insertReportQuery = "
            INSERT INTO reports (
                ticket_code, 
                ticket_price, 
                ticket_type, 
                ticket_status, 
                schedule_id, 
                user_id, 
                ticket_vehicle, 
                contact_person, 
                contact_number, 
                contact_email, 
                contact_address,
                report_date
            ) VALUES (
                :ticket_code, 
                :ticket_price, 
                :ticket_type, 
                CASE WHEN :ticket_status = 'Paid' THEN 'Completed' ELSE :ticket_status END,  
                :schedule_id, 
                :user_id, 
                :ticket_vehicle, 
                :contact_person, 
                :contact_number, 
                :contact_email, 
                :contact_address,
                NOW()
            )
        ";

                // Prepare the insert statement for reports
                $stmt = $conn->prepare($insertReportQuery);
                $stmt->bindParam(':ticket_code', $ticketData['ticket_code']); // Use ticket code from the tickets table
                $stmt->bindParam(':ticket_price', $ticketData['ticket_price']); // Use the ticket price from the tickets table
                $stmt->bindParam(':ticket_type', $accommodation_type); // Use the specific passenger type for this record
                $stmt->bindParam(':ticket_status', $ticket_status); // 'Paid' will be replaced with 'Completed' in the query
                $stmt->bindParam(':schedule_id', $schedule_id);
                $stmt->bindParam(':user_id', $user_id); // Make sure user_id is available
                $stmt->bindParam(':ticket_vehicle', $ticket_vehicle); // Make sure ticket_vehicle is available
                $stmt->bindParam(':contact_person', $contact_person);
                $stmt->bindParam(':contact_number', $contact_number);
                $stmt->bindParam(':contact_email', $contact_email);
                $stmt->bindParam(':contact_address', $contact_address);
                $stmt->execute();
            }
        }


        $qr_image_directory = '../qr_codes/';
        $qr_image_filename = $ticket_code . '.png';
        $qr_image_path = $qr_image_directory . $qr_image_filename;

        if (!file_exists($qr_image_directory)) {
            mkdir($qr_image_directory, 0777, true);
        }

        if (!file_exists($qr_image_path)) {
            QRcode::png(
                "http://localhost/marinetransit/details.php?ticket_code=" . $ticket_code,
                $qr_image_path
            );

            $updateTicketQuery = "
                UPDATE tickets 
                SET qr_code = :qr_code 
                WHERE ticket_code = :ticket_code
            ";
            $stmt = $conn->prepare($updateTicketQuery);
            $stmt->bindParam(':qr_code', $qr_image_filename);
            $stmt->bindParam(':ticket_code', $ticket_code);
            $stmt->execute();
        }

        $_SESSION['success'] = 'Booking successfully!';
    } else {
        $_SESSION['error'] = 'Please add at least one passenger.';
    }
}

?>
<?php include 'header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Add tickets</h6>
        </div>

        <div class="container">
            <div class=" text-left">

                <h5 style="margin-top: 20px; font-size: 40px"><?php echo isset($schedule['route_from']) ? $schedule['route_from'] : 'N/A'; ?> -- <?php echo isset($schedule['route_to']) ? $schedule['route_to'] : 'N/A'; ?></h5>
                <p class="lead">
                    Please check properly the form before you submit
                </p>
            </div>

            <div class="row">
                <div class="col-md-4 order-md-2 mb-4">
                    <h4 class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Schedule Information</span>
                    </h4>
                    <ul class="list-group mb-3">
                        <li class="list-group-item d-flex justify-content-between lh-condensed">
                            <div>
                                <img src="../images/bg/ssr.jpeg" alt="" class="img-fluid">
                                <h6 class="my-0 mt-4" style="font-size: 19px;"><?php echo isset($schedule['route_from']) ? $schedule['route_from'] : 'N/A'; ?> -- <?php echo isset($schedule['route_to']) ? $schedule['route_to'] : 'N/A'; ?></h6>
                                <h6 class="my-0" style="font-size: 19px;"><?php echo isset($schedule['schedule_date']) ? $schedule['schedule_date'] : 'N/A'; ?> / <?php echo isset($schedule['schedule_time']) ? $schedule['schedule_time'] : 'N/A'; ?></h6>
                                <h6 class="my-0" style="font-size: 19px;"><?php echo isset($schedule['ship_name']) ? $schedule['ship_name'] : 'N/A'; ?></h6>
                            </div>
                        </li>

                    </ul>

                    <div class="card p-2">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Actions</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <form class="needs-validation" method="POST" action="" id="bookingForm">
                                    <input type="hidden" value="<?php echo isset($schedule['schedule_date']) ? $schedule['schedule_date'] : 'N/A'; ?>" name="ticket_date">

                                    <div id="passengerDataFields">

                                    </div>
                                    <?php foreach ($accommodation_details as $accommodation): ?>
                                        <tr>
                                            <td>
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#passengerModal"
                                                    onclick="setModalTitle('<?php echo $accommodation['accomodation_name']; ?>', '<?php echo $accommodation['accomodation_id']; ?>', '<?php echo $accommodation['net_fare']; ?>')">
                                                    Add passenger +
                                                </button>
                                            </td>
                                            <td><span class="badge badge-secondary"><?php echo $accommodation['accomodation_name']; ?></span></td>
                                            <td id="fare-<?php echo $accommodation['accomodation_id']; ?>"><span class="badge badge-danger"><?php echo number_format($accommodation['net_fare'], 2); ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-8 order-md-1">
                    <h4 class="mb-3">Contact Infromation</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="contact_person">Contact Person</label>
                            <input type="text" class="form-control form-control-sm" name="contact_person" required>
                            <div class="invalid-feedback">
                                Valid first name is required.
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contact_number">Mobile Number</label>
                            <input type="text" class="form-control form-control-sm" name="contact_number" required>
                            <div class="invalid-feedback">
                                Valid last name is required.
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="contact_email">Email Address</label>
                        <input type="email" class="form-control form-control-sm" name="contact_email" required>
                        <div class="invalid-feedback">
                            Please enter a valid email address for shipping updates.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="contact_email_confirm">Confirm Email Address</label>
                        <input type="email" class="form-control form-control-sm" name="contact_email_confirm">
                        <div class="invalid-feedback">
                            Please enter a valid email address for shipping updates.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="contact_address">Address</label>
                        <textarea rows="3" class="form-control form-control-sm" name="contact_address"
                            required></textarea>
                        <div style="text-align: right; color: red; font-size: 13px">
                            Ex: Purok, Barangay, Municipality
                        </div>
                    </div>


                    <hr class="mb-4">

                    <h4 class="mb-3">Passengers <span id="count-passenger">(0)</span></h4>

                    <div class="d-block my-3">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-bordered mt-5" id="passengerTable">
                                <thead>
                                    <tr>
                                        <th>FName</th>
                                        <th>MName</th>
                                        <th>LName</th>
                                        <th>Birthdate</th>
                                        <th>Contact</th>
                                        <th>Gender</th>
                                        <th>Type</th>
                                        <th>Address</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <p style="text-align: right; font-size: 30px; color: brown;" id="totalFare">Total: 0.00</p>
                    <button class="btn btn-primary btn-lg btn-block" type="submit" name="book">Add ticket</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="passengerModal" tabindex="-1" aria-labelledby="passengerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="passengerModalLabel">Passenger Details</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="passengerForm">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="passenger_fname">First Name</label>
                                    <input type="text" class="form-control form-control-sm" name="passenger_fname" required>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="passenger_mname">Middle Name</label>
                                    <input type="text" class="form-control form-control-sm" name="passenger_mname" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="passenger_lname">Last Name</label>
                                    <input type="text" class="form-control form-control-sm" name="passenger_lname" required>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="passenger_bdate">Birthdate</label>
                                    <input type="date" class="form-control form-control-sm" name="passenger_bdate" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="passenger_contact">Contact</label>
                                    <input type="text" class="form-control form-control-sm" name="passenger_contact">
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="passenger_gender">Gender</label>
                                    <select class="form-control form-control-sm" name="passenger_gender">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="passenger_type">Type</label>
                                    <input type="text" style="background-color: gray !important; color: white !important;" class="form-control form-control-sm" name="passenger_type" id="passenger_type" required readonly>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="passenger_address">Address</label>
                                    <textarea rows="3" class="form-control form-control-sm" name="passenger_address" required></textarea>
                                    <div style="text-align: right; color: red; font-size: 13px">
                                        Ex: Purok, Barangay, Municipality
                                    </div>
                                </div>
                            </div>
                            <button type="submit" style="float: right;" class="btn btn-primary mt-3">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script src="assets/admin/vendor/jquery/jquery.min.js"></script>
<script>
    let selectedAccommodationId = null;
    let accommodationFare = 0;

    function setModalTitle(accommodationName, accommodationId, fare) {
        document.getElementById('passengerModalLabel').innerText = 'Passenger for ' + accommodationName;
        document.getElementById('passenger_type').value = accommodationName;
        selectedAccommodationId = accommodationId;
        accommodationFare = fare;
    }

    $('#passengerForm').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const formData = form.serializeArray();
        let hiddenInputs = `
        <input type="text" name="passenger_fname[]" value="${formData[0].value}">
        <input type="text" name="passenger_mname[]" value="${formData[1].value}">
        <input type="text" name="passenger_lname[]" value="${formData[2].value}">
        <input type="text" name="passenger_bdate[]" value="${formData[3].value}">
        <input type="text" name="passenger_contact[]" value="${formData[4].value}">
        <input type="text" name="passenger_gender[]" value="${formData[5].value}">
        <input type="text" name="passenger_type[]" value="${$('#passenger_type').val()}">
        <input type="text" name="passenger_address[]" value="${formData[7].value}">
        <input type="hidden" name="fare[]" value="${accommodationFare}">
    `;

        $('#bookingForm').append(hiddenInputs);

        const row = `
        <tr>
            <td>${formData[0].value}</td>
            <td>${formData[1].value}</td>
            <td>${formData[2].value}</td>
            <td>${formData[3].value}</td>
            <td>${formData[4].value}</td>
            <td>${formData[5].value}</td>
            <td>${$('#passenger_type').val()}</td>
            <td>${formData[7].value}</td>
            <td>
                <button class="btn btn-danger remove-btn">Remove</button>
                <input type="hidden" class="fare-amount" value="${accommodationFare}">
            </td>
        </tr>`;

        $('#passengerTable tbody').append(row);

        const currentTotalFare = parseFloat($('#totalFare').text().replace('Total: ', '')) || 0;
        const newTotalFare = currentTotalFare + parseFloat(accommodationFare);
        $('#totalFare').text(`Total: ${newTotalFare.toFixed(2)}`);

        updatePassengerCount();
        $('#passengerModal').modal('hide');
    });

    $('#passengerTable').on('click', '.remove-btn', function() {
        const row = $(this).closest('tr');
        const fare = parseFloat(row.find('.fare-amount').val()) || 0;

        row.remove();

        const passengerIndex = row.index();
        $('#bookingForm input[name="passenger_fname[]"]').eq(passengerIndex).remove();
        $('#bookingForm input[name="passenger_mname[]"]').eq(passengerIndex).remove();
        $('#bookingForm input[name="passenger_lname[]"]').eq(passengerIndex).remove();
        $('#bookingForm input[name="passenger_bdate[]"]').eq(passengerIndex).remove();
        $('#bookingForm input[name="passenger_contact[]"]').eq(passengerIndex).remove();
        $('#bookingForm input[name="passenger_gender[]"]').eq(passengerIndex).remove();
        $('#bookingForm input[name="passenger_type[]"]').eq(passengerIndex).remove();
        $('#bookingForm input[name="passenger_address[]"]').eq(passengerIndex).remove();
        $('#bookingForm input[name="fare[]"]').eq(passengerIndex).remove();

        const currentTotalFare = parseFloat($('#totalFare').text().replace('Total: ', '')) || 0;
        const newTotalFare = currentTotalFare - fare;
        $('#totalFare').text(`Total: ${newTotalFare.toFixed(2)}`);
        updatePassengerCount();
    });

    function updatePassengerCount() {
        const rowCount = $('#passengerTable tbody tr').length;
        $('#count-passenger').text(rowCount);
    }
</script>
<?php include 'footer.php' ?>