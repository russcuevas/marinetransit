<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
include '../connection/database.php';

if (isset($_POST['ticket_code'])) {
    $ticket_code = $_POST['ticket_code'];

    try {
        // Fetch ticket details
        $query = "SELECT t.ticket_code, t.ticket_price, t.ticket_type, t.ticket_status, 
                         t.schedule_id, t.user_id, t.ticket_vehicle, 
                         t.contact_person, t.contact_number, t.contact_email, t.contact_address,
                         s.schedule_time, p1.port_name AS route_from, p2.port_name AS route_to, sh.ship_name
                  FROM tickets t
                  JOIN schedules s ON t.schedule_id = s.schedule_id
                  JOIN ships sh ON s.ship_id = sh.ship_id
                  JOIN routes r ON s.route_id = r.route_id
                  JOIN ports p1 ON r.route_from = p1.port_id
                  JOIN ports p2 ON r.route_to = p2.port_id
                  WHERE t.ticket_code = :ticket_code";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':ticket_code', $ticket_code, PDO::PARAM_STR);
        $stmt->execute();
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ticket) {
            echo json_encode(['status' => 'failure', 'message' => 'Ticket not found']);
            exit();
        }

        // Insert into reports table
        $insertQuery = "INSERT INTO reports (
            ticket_code, ticket_price, ticket_type, ticket_status, 
            schedule_id, user_id, ticket_vehicle, contact_person, 
            contact_number, contact_email, contact_address
        ) VALUES (
            :ticket_code, :ticket_price, :ticket_type, 'Completed', 
            :schedule_id, :user_id, :ticket_vehicle, :contact_person, 
            :contact_number, :contact_email, :contact_address
        )";

        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bindParam(':ticket_code', $ticket['ticket_code']);
        $insertStmt->bindParam(':ticket_price', $ticket['ticket_price']);
        $insertStmt->bindParam(':ticket_type', $ticket['ticket_type']);
        $insertStmt->bindParam(':schedule_id', $ticket['schedule_id']);
        $insertStmt->bindParam(
            ':user_id',
            $ticket['user_id']
        );
        $insertStmt->bindParam(':ticket_vehicle', $ticket['ticket_vehicle']);
        $insertStmt->bindParam(':contact_person', $ticket['contact_person']);
        $insertStmt->bindParam(':contact_number', $ticket['contact_number']);
        $insertStmt->bindParam(':contact_email', $ticket['contact_email']);
        $insertStmt->bindParam(':contact_address', $ticket['contact_address']);
        $insertStmt->execute();

        // Fetch passenger details
        $passengerQuery = "SELECT passenger_fname, passenger_lname, passenger_contact, passenger_type, passenger_gender
                           FROM passengers WHERE ticket_id IN (SELECT ticket_id FROM tickets WHERE ticket_code = :ticket_code)";
        $passengerStmt = $conn->prepare($passengerQuery);
        $passengerStmt->bindParam(':ticket_code', $ticket_code, PDO::PARAM_STR);
        $passengerStmt->execute();
        $passengers = $passengerStmt->fetchAll(PDO::FETCH_ASSOC);

        // Update ticket status to 'Paid'
        $updateQuery = "UPDATE tickets SET ticket_status = 'Paid' WHERE ticket_code = :ticket_code";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':ticket_code', $ticket_code);
        $updateStmt->execute();

        if ($updateStmt->rowCount() > 0) {
            // Prepare email
            $recipientEmail = $ticket['contact_email'];
            $recipientName = $ticket['contact_person'];
            $subject = "Ticket Payment Confirmation";

            // Create Passenger List in Table Format
            $passengerDetails = "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; width: 100%;'>
                                    <tr>
                                        <th>Name</th>
                                        <th>Contact</th>
                                        <th>Type</th>
                                        <th>Gender</th>
                                    </tr>";
            foreach ($passengers as $passenger) {
                $passengerDetails .= "<tr>
                                        <td>{$passenger['passenger_fname']} {$passenger['passenger_lname']}</td>
                                        <td>{$passenger['passenger_contact']}</td>
                                        <td>{$passenger['passenger_type']}</td>
                                        <td>{$passenger['passenger_gender']}</td>
                                    </tr>";
            }
            $passengerDetails .= "</table>";

            // Construct Email Body
            $message = "
                <h2>Payment Confirmed</h2>
                <p>Dear $recipientName,</p>
                <p>Your ticket with code <strong>$ticket_code</strong> has been successfully marked as <strong>Paid</strong>.</p>
                <h3>Ticket Details</h3>
                <ul>
                    <li><strong>Ticket Code:</strong> {$ticket['ticket_code']}</li>
                    <li><strong>Departure Date & Time:</strong> {$ticket['schedule_time']}</li>
                    <li><strong>Route:</strong> {$ticket['route_from']} to {$ticket['route_to']}</li>
                    <li><strong>Vessel:</strong> {$ticket['ship_name']}</li>
                    <li><strong>Ticket Price:</strong> PHP {$ticket['ticket_price']}</li>
                    <li><strong>Status:</strong> Paid</li>
                </ul>
                <h3>Passenger Details</h3>
                $passengerDetails
                <p>Thank you for booking with us!</p>
            ";

            // Send email
            if (sendEmail($recipientEmail, $recipientName, $subject, $message)) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'failure', 'message' => 'Payment updated, but email not sent']);
            }
        } else {
            echo json_encode(['status' => 'failure', 'message' => 'Failed to update ticket status']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'failure', 'message' => 'Ticket code not provided']);
}

/**
 * Function to send email using PHPMailer
 */
function sendEmail($email, $name, $subject, $body)
{
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'marinetransitbalingoanport@gmail.com';
        $mail->Password = 'ygxwoiybctgwiigk'; // Use App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('marinetransitbalingoanport@gmail.com', 'Marine Transit Booking');
        $mail->addAddress($email, $name);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        return $mail->send();
    } catch (Exception $e) {
        error_log("Email Error: " . $mail->ErrorInfo);
        return false;
    }
}
