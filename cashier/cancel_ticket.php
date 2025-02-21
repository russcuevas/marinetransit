<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Load PHPMailer (Ensure you installed PHPMailer via Composer)
include '../connection/database.php';

if (isset($_POST['ticket_id'])) {
    $ticket_id = $_POST['ticket_id'];

    try {
        // Fetch user email before updating the ticket status
        $query = "SELECT email FROM tickets WHERE ticket_id = :ticket_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
        $stmt->execute();
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ticket) {
            echo json_encode(['status' => 'failure', 'message' => 'Invalid Ticket ID']);
            exit;
        }

        $user_email = $ticket['email']; // Get the user's email from the database

        // Update ticket status to 'Cancelled'
        $updateQuery = "UPDATE tickets SET ticket_status = 'Cancelled' WHERE ticket_id = :ticket_id";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
        $updateStmt->execute();

        if ($updateStmt->rowCount() > 0) {
            // Send email notification
            $mail = new PHPMailer(true);

            try {
                // Enable verbose debugging (Remove in production)
                $mail->SMTPDebug = 2;
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'marinetransitbalingoanport@gmail.com';
                $mail->Password = 'ygxwoiybctgwiigk'; // Use App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Sender & Recipient
                $mail->setFrom('marinetransitbalingoanport@gmail.com', 'Marine Transit Booking');
                $mail->addAddress($user_email); // ✅ Fixed the variable name

                // Email Content
                $mail->isHTML(true);
                $mail->Subject = 'Ticket Cancellation Confirmation';
                $mail->Body = "
                    <h3>Your ticket has been cancelled</h3>
                    <p>Dear Customer,</p>
                    <p>Your booking with Ticket ID <strong>{$ticket_id}</strong> has been successfully cancelled.</p>
                    <p>If you did not request this cancellation, please contact support.</p>
                    <p>Thank you.</p>
                ";

                $mail->send();

                echo json_encode(['status' => 'success', 'message' => 'Ticket cancelled and email sent']);
            } catch (Exception $e) {
                echo json_encode(['status' => 'success', 'message' => 'Ticket cancelled but email not sent: ' . $mail->ErrorInfo]);
            }
        } else {
            echo json_encode(['status' => 'failure', 'message' => 'No rows updated']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'failure', 'message' => 'Ticket ID not provided']);
}
