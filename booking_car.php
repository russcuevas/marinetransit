<?php
include 'connection/database.php';

$query_ports = "SELECT * FROM ports";
$stmt_ports = $conn->prepare($query_ports);
$stmt_ports->execute();
$ports = $stmt_ports->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $route_from = $_POST['route_from'];
    $route_to = $_POST['route_to'];
    $accomodation_id = $_POST['accomodation_id'];
    $schedule_date = $_POST['schedule_date'];

    $query = "
SELECT
    sa.schedule_accom_id,
    sa.net_fare,
    a.accomodation_name,
    a.accomodation_type,
    s.schedule_id,
    s.schedule_date,
    s.schedule_time,
    r_from.port_name AS route_from,
    r_to.port_name AS route_to,
    sh.ship_name  -- Include the ship_name here
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
LEFT JOIN
    ships sh ON s.ship_id = sh.ship_id  -- Join ships table to get ship_name
WHERE
    r_from.port_id = :route_from
    AND r_to.port_id = :route_to
    AND sa.accomodation_id = :accomodation_id
    AND s.schedule_date = :schedule_date
";


    $stmt = $conn->prepare($query);
    $stmt->bindParam(':route_from', $route_from, PDO::PARAM_INT);
    $stmt->bindParam(':route_to', $route_to, PDO::PARAM_INT);
    $stmt->bindParam(':accomodation_id', $accomodation_id, PDO::PARAM_INT);
    $stmt->bindParam(':schedule_date', $schedule_date, PDO::PARAM_STR);
    $stmt->execute();
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css" />
    <style>
        body {
            color: white;
        }
    </style>
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
    <div class="hero-wrap" style="background-image: url('images/bg/bg2.jpg'); min-height: 200vh; padding: 40px;">
        <div class="overlay"></div>
        <div class=" container">
            <form id="AddSchedule" class="user" method="POST">
                <input type="hidden" name="ticket_type" id="ticket_type" value="passenger">
                <div style="background-color: black;">

                    <div class="col-lg-12"
                        style="padding: 10px; display: flex; flex-direction: row; align-items: start; text-align: start; justify-content: center;">

                        <div class="col-sm-4" style="display: flex; padding: 50px; flex-direction: column; gap: 10px;">
                            <button class="btn btn-info" id="btn1" type="button"
                                onclick="window.location.href='booknow.php'"><i class=" fa fa-user"></i>
                                Passenger</button>
                            <button class="btn btn-info" id="btn2" type="button"
                                onclick="window.location.href='booking_car.php'">
                                <i class="fa fa-car"></i> Car
                            </button>
                        </div>

                        <div class="col-sm-4" style="display: flex; flex-direction: column; gap: 10px;">
                            <div class="input-group">
                                <div class="input-group">From</div>
                                <select class="form-control" name="route_from" id="route_from" required>
                                    <?php foreach ($ports as $port): ?>
                                        <option value="<?php echo $port['port_id']; ?>"><?php echo $port['port_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="input-group">
                                <div class="input-group">To &nbsp;&nbsp;&nbsp;&nbsp;</div>
                                <select class="form-control" name="route_to" id="route_to" required>
                                    <?php foreach ($ports as $port): ?>
                                        <option value="<?php echo $port['port_id']; ?>"><?php echo $port['port_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-4" style="display: flex; flex-direction: column; gap: 10px;">
                            <div class="input-group">
                                <div class="input-group">Depart</div>
                                <input class="form-control" required type="date" name="schedule_date" id="schedule_date" min="<?= date('Y-m-d') ?>">

                            </div>

                        </div>

                    </div>
                    <div class="col-sm-12" id="section2" style="border-radius: 5px; padding: 50px;">

                        <h4 style="color: white;">Car Information</h4>

                        <div class="row" style="margin-bottom: 20px">

                            <div class="col-sm-6" style="display: flex; flex-direction: column; gap: 10px;">
                                <div class="input-group">
                                    <div class="input-group">Category</div>

                                    <select class="form-control" id="accomodation_id" name="accomodation_id" required>
                                        <?php
                                        $query_accommodations = "SELECT * FROM accomodations WHERE accomodation_type = 'cargo'";
                                        $stmt_accommodations = $conn->prepare($query_accommodations);
                                        $stmt_accommodations->execute();
                                        $accommodations = $stmt_accommodations->fetchAll(PDO::FETCH_ASSOC);

                                        foreach ($accommodations as $accommodation):
                                        ?>
                                            <option value="<?php echo $accommodation['accomodation_id']; ?>">
                                                <?php echo $accommodation['accomodation_name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>


                                </div>
                            </div>


                            <div class="col-sm-6" style="display: flex; flex-direction: column; gap: 10px;">
                                <div class="input-group">
                                    <div class="input-group">No. of Passenger</div>
                                    <input class="form-control" type="number" name="passenger_no_cargo" id="passenger_no_cargo" min="1">

                                </div>
                                <p class="m-0" style="color: white;">(Including Driver)</p>
                            </div>

                            <div class="col-sm-12">
                                <button class="btn btn-info" id="btn2" type="submit"><i class="fa fa-search"></i> Cargo
                                    Trips
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($schedules)): ?>
                <div class="row" style="padding: 15px;">
                    <div class="col-sm-12" style="background-color:rgb(34, 92, 143); padding: 50px;">
                        <h3 style="color: white!important"><strong>Available Schedules:</strong></h3>
                        <table class="table table-bordered" style="color: white; background-color: black;" id="myTable">
                            <thead>
                                <tr>
                                    <th>Car</th>
                                    <th>Ship</th> <!-- Change from 'Car' to 'Ship' -->
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Routes</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $currentDate = new DateTime();
                                ?>
                                <?php foreach ($schedules as $schedule): ?>
                                    <?php
                                    $scheduleDate = new DateTime($schedule['schedule_date']);
                                    $scheduleTime = DateTime::createFromFormat('H:i:s', $schedule['schedule_time']);
                                    $scheduleDateTime = $scheduleDate->setTime($scheduleTime->format('H'), $scheduleTime->format('i'));

                                    if ($scheduleDateTime >= $currentDate): ?>
                                        <tr>
                                            <td><?php echo $schedule['accomodation_name']; ?></td>
                                            <td><?php echo $schedule['ship_name']; ?></td>
                                            <td><?php echo $schedule['schedule_date']; ?></td>
                                            <td>
                                                <?php
                                                echo $scheduleTime ? $scheduleTime->format('h:i A') : 'Invalid Time';
                                                ?>
                                            </td>
                                            <td><?php echo $schedule['route_from']; ?> - <?php echo $schedule['route_to']; ?></td>
                                            <td><?php echo number_format($schedule['net_fare'], 2); ?></td>
                                            <td>
                                                <a href="selected_booking_car.php?schedule_accom_id=<?php echo $schedule['schedule_accom_id']; ?>" class="btn btn-info">Select</a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div class="row" style="padding: 15px;">
                    <div class="col-sm-12" style="background-color:rgb(34, 92, 143); padding: 50px;">
                        <h3 style="color: white!important; text-align: left;"><strong>No Schedules Found</strong></h3>
                        <p style="color: white; text-align: left;">We couldn't find any trips matching your search. Please try again with different schedules.</p>
                    </div>
                </div>
            <?php endif; ?>



        </div>
    </div>
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
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script>
        let table = new DataTable('#myTable', {
            responsive: true
        });
    </script>

</body>

</html>