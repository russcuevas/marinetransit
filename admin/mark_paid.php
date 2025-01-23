<?php
include '../connection/database.php';

if (isset($_POST['ticket_id'])) {
    $ticket_id = $_POST['ticket_id'];

    try {
        $query = "SELECT * FROM tickets WHERE ticket_id = :ticket_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
        $stmt->execute();

        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ticket) {
            $insertQuery = "INSERT INTO reports (
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
                contact_address
            ) VALUES (
                :ticket_code, 
                :ticket_price, 
                :ticket_type, 
                :ticket_status, 
                :schedule_id, 
                :user_id, 
                :ticket_vehicle, 
                :contact_person, 
                :contact_number, 
                :contact_email, 
                :contact_address
            )";

            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bindParam(':ticket_code', $ticket['ticket_code']);
            $insertStmt->bindParam(':ticket_price', $ticket['ticket_price']);
            $insertStmt->bindParam(':ticket_type', $ticket['ticket_type']);
            $insertStmt->bindParam(':ticket_status', $ticket['ticket_status']);
            $insertStmt->bindParam(':schedule_id', $ticket['schedule_id']);
            $insertStmt->bindParam(':user_id', $ticket['user_id']);
            $insertStmt->bindParam(':ticket_vehicle', $ticket['ticket_vehicle']);
            $insertStmt->bindParam(':contact_person', $ticket['contact_person']);
            $insertStmt->bindParam(':contact_number', $ticket['contact_number']);
            $insertStmt->bindParam(':contact_email', $ticket['contact_email']);
            $insertStmt->bindParam(':contact_address', $ticket['contact_address']);

            $insertStmt->execute();
            $updateQuery = "UPDATE tickets SET ticket_status = 'Paid' WHERE ticket_id = :ticket_id";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
            $updateStmt->execute();

            if ($insertStmt->rowCount() > 0 && $updateStmt->rowCount() > 0) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'failure', 'message' => 'Failed to update ticket or insert report']);
            }
        } else {
            echo json_encode(['status' => 'failure', 'message' => 'Ticket not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'failure', 'message' => 'Ticket ID not provided']);
}
