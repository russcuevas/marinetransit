<?php
require_once('phpqrcode/qrlib.php');
include 'connection/database.php';

if (isset($_GET['schedule_accom_id'])) {
    $schedule_accom_id = $_GET['schedule_accom_id'];
    $query = "
    SELECT
        sa.schedule_accom_id,
        sa.net_fare,
        a.accomodation_id,
        a.accomodation_name,
        a.accomodation_type,
        s.schedule_id,
        s.schedule_time,
        r_from.port_name AS route_from,
        r_to.port_name AS route_to
    FROM
        schedule_accom sa
    LEFT JOIN
        accomodations a ON sa.accomodation_id = a.accomodation_id
    LEFT JOIN
        schedules s ON sa.schedule_id = s.schedule_id
    LEFT JOIN
        routes r ON s.route_id = r.route_id
    LEFT JOIN
        ports r_from ON r.route_from = r_from.port_id
    LEFT JOIN
        ports r_to ON r.route_to = r_to.port_id
    WHERE
        sa.schedule_accom_id = :schedule_accom_id
    ";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':schedule_accom_id', $schedule_accom_id, PDO::PARAM_INT);
    $stmt->execute();
    $selected_schedule = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (isset($_POST['book'])) {
    $ticket_code = uniqid('CARGO-');
    $ticket_date = date('Y-m-d H:i:s');
    $ticket_status = 'Pending';
    $contact_person = $_POST['contact_person'];
    $contact_number = $_POST['contact_number'];
    $contact_email = $_POST['contact_email'];
    $contact_address = $_POST['contact_address'];
    $schedule_id = $_POST['schedule_id'];

    $ticket_type = 'Regular';

    $insertTicketQuery = "
        INSERT INTO tickets (ticket_date, ticket_code, ticket_price, ticket_type, ticket_status, schedule_id, contact_person, contact_number, contact_email, contact_address)
        VALUES (:ticket_date, :ticket_code, :ticket_price, :ticket_type, :ticket_status, :schedule_id, :contact_person, :contact_number, :contact_email, :contact_address)
    ";

    $stmt = $conn->prepare($insertTicketQuery);
    $stmt->bindParam(':ticket_date', $ticket_date);
    $stmt->bindParam(':ticket_code', $ticket_code);
    $stmt->bindParam(':ticket_price', $_POST['ticket_price']);
    $stmt->bindParam(':ticket_type', $ticket_type);
    $stmt->bindParam(':ticket_status', $ticket_status);
    $stmt->bindParam(':schedule_id', $schedule_id);
    $stmt->bindParam(':contact_person', $contact_person);
    $stmt->bindParam(':contact_number', $contact_number);
    $stmt->bindParam(':contact_email', $contact_email);
    $stmt->bindParam(':contact_address', $contact_address);
    $stmt->execute();

    $ticket_id = $conn->lastInsertId();

    foreach ($_POST['passenger_fname'] as $index => $first_name) {
        $last_name = $_POST['passenger_lname'][$index];
        $middle_name = $_POST['passenger_mname'][$index];
        $birthdate = $_POST['passenger_bdate'][$index];
        $contact = $_POST['passenger_contact'][$index];
        $address = $_POST['passenger_address'][$index];
        $type = $_POST['passenger_type'][$index];
        $gender = $_POST['passenger_gender'][$index];

        $insertPassengerQuery = "
            INSERT INTO passengers (ticket_id, passenger_fname, passenger_mname, passenger_lname, passenger_bdate, passenger_contact, passenger_address, passenger_type, passenger_gender)
            VALUES (:ticket_id, :first_name, :middle_name, :last_name, :birthdate, :contact, :address, :type, :gender)
        ";

        $stmt = $conn->prepare($insertPassengerQuery);
        $stmt->bindParam(':ticket_id', $ticket_id);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':middle_name', $middle_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':birthdate', $birthdate);
        $stmt->bindParam(':contact', $contact);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':gender', $gender);
        $stmt->execute();

        $qr_image_path = 'qr_codes/' . $ticket_code . '.png';

        if (!file_exists($qr_image_path)) {
            if (!file_exists('qr_codes/')) {
                mkdir('qr_codes/', 0777, true);
            }
            $qr_content = "http://localhost/marinetransit/details.php?ticket_code=" . $ticket_code . "&passenger_id=" . $conn->lastInsertId();
            QRcode::png($qr_content, $qr_image_path);

            $updateTicketQuery = "
                UPDATE tickets 
                SET qr_code = :qr_code 
                WHERE ticket_code = :ticket_code
            ";
            $stmt = $conn->prepare($updateTicketQuery);
            $stmt->bindParam(':qr_code', $qr_image_path);
            $stmt->bindParam(':ticket_code', $ticket_code);
            $stmt->execute();
        }
    }

    echo "Booking successful! QR Codes generated and saved to the database.";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<div class="container">
    <div class="row" style="margin-top: 200px;">
        <div class="col-md-6">
            <h2>Selected Cargo Booking Details</h2>

            <?php if ($selected_schedule): ?>
                <table class="table table-bordered" style="background-color: black; color: white;">
                    <tr>
                        <th>Schedule ID</th>
                        <td><?php echo $selected_schedule['schedule_accom_id']; ?></td>
                    </tr>
                    <tr>
                        <th>Net Fare</th>
                        <td><?php echo number_format($selected_schedule['net_fare'], 2); ?></td>
                    </tr>
                    <tr>
                        <th>Car</th>
                        <td><?php echo $selected_schedule['accomodation_name']; ?></td>
                    </tr>
                    <tr>
                        <th>Schedule Time</th>
                        <td><?php echo $selected_schedule['schedule_time']; ?></td>
                    </tr>
                    <tr>
                        <th>Route From</th>
                        <td><?php echo $selected_schedule['route_from']; ?></td>
                    </tr>
                    <tr>
                        <th>Route To</th>
                        <td><?php echo $selected_schedule['route_to']; ?></td>
                    </tr>
                </table>
            <?php else: ?>
                <p>No details found for this booking.</p>
            <?php endif; ?>

            <table class="table table-bordered mt-5" id="passengerTable">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
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
            <p id="total-passengers">Total Passengers: </p>
        </div>

        <div class="col-md-6">
            <form method="POST" action="" id="bookingForm">
                <input type="hidden" value="<?php echo $selected_schedule['schedule_id']; ?>" name="schedule_id">
                <input type="hidden" value="<?php echo $selected_schedule['net_fare']; ?>" name="ticket_price">
                <input type="hidden" value="<?php echo $selected_schedule['accomodation_id']; ?>" name="accomodation_id[]">
                <fieldset>
                    <h4>Contact Information</h4>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_person">Contact Person</label>
                                    <input type="text" class="form-control form-control-sm" name="contact_person" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_number">Mobile Number</label>
                                    <input type="text" class="form-control form-control-sm" name="contact_number" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_email">Email Address</label>
                                    <input type="email" class="form-control form-control-sm" name="contact_email" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_email_confirm">Confirm Email Address</label>
                                    <input type="email" class="form-control form-control-sm" name="contact_email_confirm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label for="contact_address">Address</label>
                            <textarea rows="3" class="form-control form-control-sm" name="contact_address" required></textarea>
                        </div>
                    </div>
                    <input type="submit" name="book" value="Book now" class="btn btn-primary">
                </fieldset>
            </form>

            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#passengerModal">
                Add passenger
            </button>


            <div class="modal fade" id="passengerModal" tabindex="-1" aria-labelledby="passengerModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="passengerModalLabel">Passenger Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="passengerForm">

                                <div class="form-group">
                                    <label for="passenger_fname">First Name</label>
                                    <input type="text" class="form-control form-control-sm" name="passenger_fname" required>
                                </div>

                                <div class="form-group">
                                    <label for="passenger_mname">Middle Name</label>
                                    <input type="text" class="form-control form-control-sm" name="passenger_mname" required>
                                </div>
                                <div class="form-group">
                                    <label for="passenger_lname">Last Name</label>
                                    <input type="text" class="form-control form-control-sm" name="passenger_lname" required>
                                </div>
                                <div class="form-group">
                                    <label for="passenger_bdate">Birthdate</label>
                                    <input type="date" class="form-control form-control-sm" name="passenger_bdate" required>
                                </div>
                                <div class="form-group">
                                    <label for="passenger_contact">Contact</label>
                                    <input type="text" class="form-control form-control-sm" name="passenger_contact">
                                </div>
                                <div class="form-group">
                                    <label for="passenger_gender">Gender</label>
                                    <select class="form-control form-control-sm" name="passenger_gender">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="passenger_type">Passenger Type:</label>
                                    <select name="passenger_type" class="form-control">
                                        <option value="adult">Adult</option>
                                        <option value="Child">Child</option>
                                        <option value="Senior">Senior</option>
                                        <option value="Student">Student</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="passenger_address">Address</label>
                                    <textarea rows="3" class="form-control form-control-sm" name="passenger_address" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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
        <input type="hidden" name="passenger_fname[]" value="${formData[0].value}">
        <input type="hidden" name="passenger_mname[]" value="${formData[1].value}">
        <input type="hidden" name="passenger_lname[]" value="${formData[2].value}">
        <input type="hidden" name="passenger_bdate[]" value="${formData[3].value}">
        <input type="hidden" name="passenger_contact[]" value="${formData[4].value}">
        <input type="hidden" name="passenger_gender[]" value="${formData[5].value}">
        <input type="hidden" name="passenger_type[]" value="${formData[6].value}">
        <input type="hidden" name="passenger_address[]" value="${formData[7].value}">
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
            <td>${formData[6].value}</td>
            <td>${formData[7].value}</td>
            <td><button class="btn btn-danger remove-passenger">Remove</button></td>
        </tr>
    `;

        $('#passengerTable tbody').append(row);
        updateTotalPassengersCount();
        $('#passengerModal').modal('hide');
    });

    function updateTotalPassengersCount() {
        const totalPassengers = $('#passengerTable tbody tr').length;
        $('#total-passengers').text(`Total Passengers: ${totalPassengers}`);
    }

    $(document).on('click', '.remove-passenger', function() {
        $(this).closest('tr').remove();
        updateTotalPassengersCount();
    });
</script>