<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Optional for CORS

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
include '../connection/database.php';

// Ensure no whitespace or errors before JSON response
ob_start();

if (!isset($_POST['ticket_code'])) {
    echo json_encode(['status' => 'failure', 'message' => 'Ticket code not provided']);
    exit;
}

$ticket_code = $_POST['ticket_code'];

try {
    // Update the ticket status to "Cancelled"
    $query = "UPDATE tickets SET ticket_status = 'Cancelled' WHERE ticket_code = :ticket_code";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':ticket_code', $ticket_code, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Fetch customer email
        $query_email = "SELECT contact_email, contact_person FROM tickets WHERE ticket_code = :ticket_code LIMIT 1";
        $stmt_email = $conn->prepare($query_email);
        $stmt_email->bindParam(':ticket_code', $ticket_code, PDO::PARAM_STR);
        $stmt_email->execute();
        $customer = $stmt_email->fetch(PDO::FETCH_ASSOC);

        if ($customer) {
            $contact_email = $customer['contact_email'];
            $contact_person = $customer['contact_person'];

            // Send email
            $email_sent = sendCancellationEmail($contact_email, $contact_person, $ticket_code);
            $response = [
                'status' => 'success',
                'message' => $email_sent ? 'Tickets cancelled, email sent' : 'Tickets cancelled, but email failed'
            ];
        } else {
            $response = ['status' => 'failure', 'message' => 'Tickets cancelled, but email not found'];
        }
    } else {
        $response = ['status' => 'failure', 'message' => 'No tickets updated'];
    }
} catch (PDOException $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

// Send JSON response
echo json_encode($response);
ob_end_flush();
exit;

// Function to send email
function sendCancellationEmail($email, $name, $ticket_code)
{
    $mail = new PHPMailer(true);
    try {
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
        $mail->Subject = 'Booking Cancellation - Ticket Code: ' . $ticket_code;
        $mail->Body = "
            <h3>Dear $name,</h3>
            <p>Your booking with ticket code <strong>$ticket_code</strong> has been cancelled.</p>
            <p>If you have any questions, please contact our support team.</p>
            <br>
            <p>Best regards,</p>
            <p><strong>Marine Transit Team</strong></p>
        ";

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}
