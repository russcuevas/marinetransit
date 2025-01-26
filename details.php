<?php
include 'connection/database.php';

if (isset($_GET['ticket_code'])) {
    $ticket_code = $_GET['ticket_code'];
    $ticketQuery = "
        SELECT t.ticket_code, SUM(t.ticket_price) AS total_ticket_price, 
               t.ticket_status, t.schedule_id, t.contact_person, t.contact_number, 
               t.contact_email, t.contact_address,
               r1.route_from AS route_from_id, r2.route_to AS route_to_id,
               p1.port_name AS route_from, p2.port_name AS route_to
        FROM tickets t
        JOIN schedules s ON t.schedule_id = s.schedule_id
        JOIN routes r1 ON s.route_id = r1.route_id
        JOIN routes r2 ON s.route_id = r2.route_id
        JOIN ports p1 ON r1.route_from = p1.port_id
        JOIN ports p2 ON r2.route_to = p2.port_id
        WHERE t.ticket_code = :ticket_code
        GROUP BY t.ticket_code, t.ticket_status, t.schedule_id, t.contact_person, 
                 t.contact_number, t.contact_email, t.contact_address, 
                 r1.route_from, r2.route_to, p1.port_name, p2.port_name
    ";

    $stmt = $conn->prepare($ticketQuery);
    $stmt->bindParam(':ticket_code', $ticket_code);
    $stmt->execute();
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($ticket) {
        $passengerQuery = "
            SELECT p.passenger_fname, p.passenger_mname, p.passenger_lname, p.passenger_bdate, 
                   p.passenger_contact, p.passenger_address, p.passenger_type, p.passenger_gender
            FROM passengers p
            JOIN tickets t ON p.ticket_id = t.ticket_id
            WHERE t.ticket_code = :ticket_code
        ";

        $stmt = $conn->prepare($passengerQuery);
        $stmt->bindParam(':ticket_code', $ticket_code);
        $stmt->execute();
        $passengers = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket and Passenger Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h2 {
            color: #333;
        }

        .ticket-details,
        .passenger-details {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php else: ?>
            <h2>Ticket Details</h2>
            <img src="assets/user/img/ssr.jpeg" alt="">
            <div class="ticket-details">
                <table>
                    <tr>
                        <th>Route</th>
                        <td><?php echo $ticket['route_from']; ?> to <?php echo $ticket['route_to']; ?></td>
                    </tr>
                    <tr>
                        <th>Ticket Code</th>
                        <td><?php echo $ticket['ticket_code']; ?></td>
                    </tr>
                    <tr>
                        <th>Ticket Price</th>
                        <td><?php echo $ticket['total_ticket_price']; ?></td>
                    </tr>
                    <tr>
                        <th>Contact Person</th>
                        <td><?php echo $ticket['contact_person']; ?></td>
                    </tr>
                    <tr>
                        <th>Contact Number</th>
                        <td><?php echo $ticket['contact_number']; ?></td>
                    </tr>
                    <tr>
                        <th>Contact Email</th>
                        <td><?php echo $ticket['contact_email']; ?></td>
                    </tr>
                    <tr>
                        <th>Contact Address</th>
                        <td><?php echo $ticket['contact_address']; ?></td>
                    </tr>
                </table>
            </div>

            <h3>Passenger Details:</h3>
            <div class="passenger-details">
                <?php if ($passengers): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Birthdate</th>
                                <th>Contact</th>
                                <th>Address</th>
                                <th>Gender</th>
                                <th>Accommodation Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($passengers as $passenger): ?>
                                <tr>
                                    <td><?php echo $passenger['passenger_fname'] . " " . $passenger['passenger_mname'] . " " . $passenger['passenger_lname']; ?></td>
                                    <td><?php echo $passenger['passenger_bdate']; ?></td>
                                    <td><?php echo $passenger['passenger_contact']; ?></td>
                                    <td><?php echo $passenger['passenger_address']; ?></td>
                                    <td><?php echo $passenger['passenger_gender']; ?></td>
                                    <td><?php echo $passenger['passenger_type']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No passengers found for this ticket.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>