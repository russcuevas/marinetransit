<?php
include 'connection/database.php';

$error_message = '';

if (isset($_GET['ticket_code'])) {
    $ticket_code = $_GET['ticket_code'];
    $ticket_query = "
        SELECT t.ticket_code, t.ticket_date, t.ticket_status, t.ticket_price, 
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
        WHERE t.ticket_code = :ticket_code
    ";

    $ticket_stmt = $conn->prepare($ticket_query);
    $ticket_stmt->bindParam(':ticket_code', $ticket_code, PDO::PARAM_STR);
    $ticket_stmt->execute();
    $ticket = $ticket_stmt->fetch(PDO::FETCH_ASSOC);

    if ($ticket) {
        $cargo_query = "
            SELECT pc.passenger_cargo_id, pc.passenger_cargo_brand, pc.passenger_cargo_plate, 
                   a.accomodation_name, sh.ship_name, t.ticket_date
            FROM passenger_cargos pc
            JOIN tickets t ON pc.ticket_id = t.ticket_id
            JOIN cargos c ON pc.accomodation_id = c.cargo_id
            LEFT JOIN accomodations a ON pc.accomodation_id = a.accomodation_id
            JOIN schedules s ON t.schedule_id = s.schedule_id
            JOIN ships sh ON s.ship_id = sh.ship_id
            WHERE t.ticket_code = :ticket_code
        ";

        $cargo_stmt = $conn->prepare($cargo_query);
        $cargo_stmt->bindParam(':ticket_code', $ticket_code, PDO::PARAM_STR);
        $cargo_stmt->execute();
        $cargos = $cargo_stmt->fetchAll(PDO::FETCH_ASSOC);

        $passenger_query = "
        SELECT p.passenger_id, p.passenger_fname, p.passenger_mname, p.passenger_lname, 
            p.passenger_bdate, p.passenger_contact, p.passenger_address, p.passenger_type, p.passenger_gender
        FROM passengers p
        JOIN tickets t ON p.ticket_id = t.ticket_id
        WHERE t.ticket_code = :ticket_code
    ";

        $passenger_stmt = $conn->prepare($passenger_query);
        $passenger_stmt->bindParam(':ticket_code', $ticket_code, PDO::PARAM_STR);
        $passenger_stmt->execute();
        $passengers = $passenger_stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $error_message = "Ticket not found.";
    }
} else {
    $error_message = "Ticket code is missing.";
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
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-dark"
        style="background-color: #000957 !important;" id="ftco-navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php"><img style="height: 50px; border-radius: 50px;"
                    src="images/bg/icon.jpg" alt=""> Marine<span style="color: red;">transit</span></a>
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

    <div class="container mt-5">
        <h2 class="text-left mb-4"> <img src="images/bg/ssr.jpeg" alt="Ticket Image" class="img-fluid"><br>
            <?php echo $ticket['ticket_code']; ?>
        </h2>

        <?php if ($ticket) { ?>
            <!-- Ticket Information Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <h4>Ticket Information</h4>
                    <table class="table table-striped">
                        <tr>
                            <th>Ticket Code</th>
                            <td><?php echo htmlspecialchars($ticket['ticket_code']); ?></td>
                        </tr>
                        <tr>
                            <th>Ticket Date</th>
                            <td><?php echo htmlspecialchars($ticket['ticket_date']); ?></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td><?php echo htmlspecialchars($ticket['ticket_status']); ?></td>
                        </tr>
                        <tr>
                            <th>Ticket Price</th>
                            <td><?php echo htmlspecialchars($ticket['ticket_price']); ?></td>
                        </tr>
                        <tr>
                            <th>Ship Name</th>
                            <td><?php echo htmlspecialchars($ticket['ship_name']); ?></td>
                        </tr>
                        <tr>
                            <th>Route From</th>
                            <td><?php echo htmlspecialchars($ticket['route_from']); ?></td>
                        </tr>
                        <tr>
                            <th>Route To</th>
                            <td><?php echo htmlspecialchars($ticket['route_to']); ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Cargo Information Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <h4>Cargo Information</h4>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Vehicle</th>
                                <th>Ship Name</th>
                                <th>Ticket Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cargos as $cargo) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($cargo['accomodation_name']); ?></td>
                                    <td><?php echo htmlspecialchars($cargo['ship_name']); ?></td>
                                    <td><?php echo htmlspecialchars($cargo['ticket_date']); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Passenger Information Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <h4>Passenger Information</h4>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Last Name</th>
                                <th>Birth Date</th>
                                <th>Contact</th>
                                <th>Address</th>
                                <th>Type</th>
                                <th>Gender</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($passengers as $passenger) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($passenger['passenger_fname']); ?></td>
                                    <td><?php echo htmlspecialchars($passenger['passenger_mname']); ?></td>
                                    <td><?php echo htmlspecialchars($passenger['passenger_lname']); ?></td>
                                    <td><?php echo htmlspecialchars($passenger['passenger_bdate']); ?></td>
                                    <td><?php echo htmlspecialchars($passenger['passenger_contact']); ?></td>
                                    <td><?php echo htmlspecialchars($passenger['passenger_address']); ?></td>
                                    <td><?php echo htmlspecialchars($passenger['passenger_type']); ?></td>
                                    <td><?php echo htmlspecialchars($passenger['passenger_gender']); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php } else { ?>
            <p>No cargo or passenger details found for this ticket.</p>
        <?php } ?>

        <?php if ($error_message) { ?>
            <div class="alert alert-danger">
                <p><?php echo $error_message; ?></p>
            </div>
        <?php } ?>
    </div>


    <!-- loader -->
    <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px">
            <circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee" />
            <circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10"
                stroke="#F96D00" />
        </svg></div>


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

</body>

</html>