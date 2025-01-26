<?php
session_start();
require_once('phpqrcode/qrlib.php');
include 'connection/database.php';

if (isset($_GET['schedule_accom_id']) && !empty($_GET['schedule_accom_id'])) {
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

    if (!$selected_schedule) {
        header('Location: booking_car.php');
        exit;
    }
} else {
    header('Location: booking_car.php');
    exit;
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

    if (isset($_POST['passenger_fname']) && is_array($_POST['passenger_fname']) && !empty($_POST['passenger_fname'])) {

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
    } else {
        $_SESSION['error'] = 'Please add at least one passenger';
    }

    if (!empty($_POST['passenger_cargo_brand']) && !empty($_POST['passenger_cargo_plate'])) {
        foreach ($_POST['passenger_cargo_brand'] as $index => $cargo_brand) {
            if (!empty($_POST['accomodation_id'][$index])) {
                $accomodation_id = $_POST['accomodation_id'][$index];
                $cargo_plate = $_POST['passenger_cargo_plate'][$index];
                $insertCargoQuery = "
                INSERT INTO passenger_cargos (ticket_id, accomodation_id, passenger_cargo_brand, passenger_cargo_plate)
                VALUES (:ticket_id, :accomodation_id, :cargo_brand, :cargo_plate)
            ";
                $stmt = $conn->prepare($insertCargoQuery);
                $stmt->bindParam(':ticket_id', $ticket_id);
                $stmt->bindParam(':accomodation_id', $accomodation_id);
                $stmt->bindParam(':cargo_brand', $cargo_brand, PDO::PARAM_NULL);
                $stmt->bindParam(':cargo_plate', $cargo_plate, PDO::PARAM_NULL);
                $stmt->execute();
            }
        }
    } else {
        if (!empty($_POST['accomodation_id'])) {
            $insertCargoQuery = "
            INSERT INTO passenger_cargos (ticket_id, accomodation_id, passenger_cargo_brand, passenger_cargo_plate)
            VALUES (:ticket_id, :accomodation_id, NULL, NULL)
        ";
            $stmt = $conn->prepare($insertCargoQuery);
            $stmt->bindParam(':ticket_id', $ticket_id);
            $stmt->bindParam(':accomodation_id', $_POST['accomodation_id'][0]);
            $stmt->execute();
        }
    }
    $_SESSION['success'] = 'Booking successful!';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Marinetransit</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:200,300,400,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/aos.css">
    <link rel="stylesheet" href="css/ionicons.min.css">
    <link rel="stylesheet" href="css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="css/jquery.timepicker.css">
    <link rel="stylesheet" href="css/flaticon.css">
    <link rel="stylesheet" href="css/icomoon.css">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-dark "
        style="background-color: #000957 !important;" id="ftco-navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php"><img style="height: 50px; border-radius: 50px;"
                    src="images/bg/icon.jpg" alt="">
                Marine<span style="color: red;">transit</span></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav"
                aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="oi oi-menu"></span> Menu
            </button>

            <div class="collapse navbar-collapse" id="ftco-nav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
                    <li class="nav-item"><a href="aboutus.php" class="nav-link">About</a></li>
                    <li class="nav-item"><a href="inspiration.php" class="nav-link">Inspiration</a></li>
                    <li class="nav-item"><a href="contactus.php" class="nav-link">Contact Us</a></li>
                    <li class="nav-item"><a href="policy.php" class="nav-link">Privacy Policy</a></li>
                    <li class="nav-item"><a href="guidelines.php" class="nav-link">FAQ</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- END nav -->

    <section class="ftco-section contact-section bg-light">
        <div class="container">
            <div class=" text-left">
                <h2>Booking Details</h2>
                <h5><?php echo $selected_schedule['route_from']; ?> ---- <?php echo $selected_schedule['route_to']; ?></h5>
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
                                <img src="images/bg/ssr.jpeg" alt="" class="img-fluid">
                                <h6 class="my-0 mt-4" style="font-size: 19px;"><?php echo $selected_schedule['route_from']; ?> ---- <?php echo $selected_schedule['route_to']; ?></h6>
                                <h6 class="my-0" style="font-size: 19px;"><?php echo number_format($selected_schedule['net_fare'], 2); ?></h6>
                                <h6 class="my-0" style="font-size: 19px;"><?php echo $selected_schedule['accomodation_name']; ?></h6>
                                <h6 class="my-0" style="font-size: 19px;"><?php echo $selected_schedule['schedule_time']; ?></h6>

                            </div>
                        </li>

                    </ul>
                </div>
                <div class="col-md-8 order-md-1">
                    <form method="POST" action="" id="bookingForm">
                        <input type="hidden" value="<?php echo $selected_schedule['schedule_id']; ?>" name="schedule_id">
                        <input type="hidden" value="<?php echo $selected_schedule['net_fare']; ?>" name="ticket_price">
                        <input type="hidden" value="<?php echo $selected_schedule['accomodation_id']; ?>" name="accomodation_id[]">
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
                            <div class="invalid-feedback">
                                Please enter your shipping address.
                            </div>
                        </div>


                        <hr class="mb-4">

                        <h4 class="mb-3">Passengers <span id="count-passengers">0</span></h4>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#passengerModal">
                            Add passenger +
                        </button>

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
                        <!-- <p style="text-align: right; font-size: 30px; color: brown;" id="totalFare">Total: 0.00</p> -->
                        <button class="btn btn-primary btn-lg btn-block" type="submit" name="book">Booknow</button>
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
                                    <label for="passenger_type">Passenger Type:</label>
                                    <select name="passenger_type" class="form-control">
                                        <option value="adult">Adult ( 18 - 59 )</option>
                                        <option value="Child">Child ( 12 - 17 ) </option>
                                        <option value="Senior">Senior ( 60 - Older )</option>
                                        <option value="Student">Student</option>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="passenger_address">Address</label>
                                    <textarea rows="3" class="form-control form-control-sm" name="passenger_address" required></textarea>
                                </div>
                            </div>
                            <button type="submit" style="float: right;" class="btn btn-primary mt-3">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>






    <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px">
            <circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee" />
            <circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10"
                stroke="#F96D00" />
        </svg></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script src="js/jquery.min.js"></script>
    <script src="js/jquery-migrate-3.0.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.easing.1.3.js"></script>
    <script src="js/jquery.waypoints.min.js"></script>
    <script src="js/jquery.stellar.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/aos.js"></script>
    <script src="js/jquery.animateNumber.min.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <script src="js/scrollax.min.js"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
    <script src="js/google-map.js"></script>
    <script src="js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script>
        $(document).ready(function() {
            <?php if (isset($_SESSION['success'])): ?>
                Swal.fire({
                    title: 'Success!',
                    text: '<?php echo $_SESSION['success']; ?>',
                    icon: 'success',
                    confirmButtonText: 'OK'
                })
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                Swal.fire({
                    title: 'Error!',
                    text: '<?php echo $_SESSION['error']; ?>',
                    icon: 'error',
                    confirmButtonText: 'OK'
                })
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        });
    </script>
    <script>
        let selectedAccommodationId = null;
        let accommodationFare = 0;

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
            $('#count-passengers').text(totalPassengers);
        }

        $(document).on('click', '.remove-passenger', function() {
            $(this).closest('tr').remove();
            updateTotalPassengersCount();
        });
    </script>

</body>

</html>