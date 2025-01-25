<?php
require_once('phpqrcode/qrlib.php');
include 'connection/database.php';

// Check if schedule_id is passed in the URL
if (isset($_GET['schedule_id'])) {
    $schedule_id = $_GET['schedule_id'];

    // Fetch schedule details
    $query = "
        SELECT s.schedule_id, sh.ship_name, s.schedule_time, p_from.port_name AS route_from, p_to.port_name AS route_to
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
} else {
    echo "No schedule selected.";
    exit;
}

// Fetch accommodation details for the selected schedule
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

// Handle the booking form submission
if (isset($_POST['book'])) {
    // Generate a unique ticket code and set ticket date and status
    $ticket_code = uniqid('PASSENGER-');
    $ticket_date = date('Y-m-d H:i:s');
    $ticket_status = 'Pending';
    $contact_person = $_POST['contact_person'];
    $contact_number = $_POST['contact_number'];
    $contact_email = $_POST['contact_email'];
    $contact_address = $_POST['contact_address'];

    $all_passenger_details = "";

    // Loop through all passengers and insert their details
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

        // Insert ticket query
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

        // Insert passenger details
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

        // Concatenate the passenger details for QR code
        $all_passenger_details .= "Passenger: " . $first_name . " " . $middle_name . " " . $last_name . "\n" .
            "Contact: " . $contact . "\n" .
            "Email: " . $contact_email . "\n" .
            "Fare: " . $accommodation_fare . "\n" .
            "Accommodation: " . $accommodation_type . "\n\n";
    }

    // Check if the QR code already exists for the ticket_code
    $qr_image_path = 'qr_codes/' . $ticket_code . '.png';

    if (!file_exists($qr_image_path)) {
        if (!file_exists('qr_codes/')) {
            mkdir('qr_codes/', 0777, true);
        }

        // Generate the QR code with all passenger details
        QRcode::png("http://localhost/marinetransit/details.php?ticket_code=" . $ticket_code, $qr_image_path);

        // Update the ticket with the QR code path
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

    echo "Booking successful! QR Code generated.";
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

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h1>Booking Details</h1>

                <?php if (isset($schedule)): ?>
                    <h3>Schedule Information</h3>
                    <p><strong>Schedule ID:</strong> <?php echo $schedule['schedule_id']; ?></p>
                    <p><strong>Ship Name:</strong> <?php echo isset($schedule['ship_name']) ? $schedule['ship_name'] : 'N/A'; ?></p>
                    <p><strong>Schedule Time:</strong> <?php echo isset($schedule['schedule_time']) ? $schedule['schedule_time'] : 'N/A'; ?></p>
                    <p><strong>Route From:</strong> <?php echo isset($schedule['route_from']) ? $schedule['route_from'] : 'N/A'; ?></p>
                    <p><strong>Route To:</strong> <?php echo isset($schedule['route_to']) ? $schedule['route_to'] : 'N/A'; ?></p>

                    <h3>Accommodation Details</h3>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Select Accommodation</th>
                                <th>Name</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <form action="" method="POST" id="bookingForm">
                                <div id="passengerDataFields">

                                </div>
                                <?php foreach ($accommodation_details as $accommodation): ?>
                                    <tr>
                                        <td>
                                            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#passengerModal"
                                                onclick="setModalTitle('<?php echo $accommodation['accomodation_name']; ?>', <?php echo $accommodation['accomodation_id']; ?>, <?php echo $accommodation['net_fare']; ?>)">
                                                Add passenger
                                            </button>
                                        </td>
                                        <td><?php echo $accommodation['accomodation_name']; ?></td>
                                        <td id="fare-<?php echo $accommodation['accomodation_id']; ?>"><?php echo number_format($accommodation['net_fare'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Schedule not found.</p>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <input type="text" value="<?php echo $schedule['schedule_id'] ?>">
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

                    <p id="totalFare">Total: 0.00</p>

                    <input type="submit" name="book" value="Book now">
                </fieldset>
                </form>

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
                                        <label for="passenger_type">Type</label>
                                        <input type="text" class="form-control form-control-sm" name="passenger_type" id="passenger_type" required>
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
            <input type="text" name="passenger_fname[]" value="${formData[0].value}">
            <input type="text" name="passenger_mname[]" value="${formData[1].value}">
            <input type="text" name="passenger_lname[]" value="${formData[2].value}">
            <input type="text" name="passenger_bdate[]" value="${formData[3].value}">
            <input type="text" name="passenger_contact[]" value="${formData[4].value}">
            <input type="text" name="passenger_gender[]" value="${formData[5].value}">
            <input type="text" name="passenger_type[]" value="${$('#passenger_type').val()}"> <!-- Correctly capturing the type -->
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
            <td>${$('#passenger_type').val()}
            <td>${formData[7].value}</td>
            <td><button class="btn btn-danger">Remove</button></td>
        </tr>`;

            $('#passengerTable tbody').append(row);

            const totalFare = parseFloat($('#totalFare').text().replace('Total: ', '')) + accommodationFare;
            $('#totalFare').text(`Total: ${totalFare.toFixed(2)}`);
            $('#passengerModal').modal('hide');
        });
    </script>
</body>

</html>