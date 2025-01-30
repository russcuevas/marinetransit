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
            $codeQuery = "SELECT * FROM tickets WHERE ticket_code = :ticket_code";
            $codeStmt = $conn->prepare($codeQuery);
            $codeStmt->bindParam(':ticket_code', $ticket['ticket_code']);
            $codeStmt->execute();

            $tickets = $codeStmt->fetchAll(PDO::FETCH_ASSOC);

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
        'Completed',  
        :schedule_id, 
        :user_id, 
        :ticket_vehicle, 
        :contact_person, 
        :contact_number, 
        :contact_email, 
        :contact_address
    )";

            $insertStmt = $conn->prepare($insertQuery);

            foreach ($tickets as $t) {
                $insertStmt->bindParam(':ticket_code', $t['ticket_code']);
                $insertStmt->bindParam(':ticket_price', $t['ticket_price']);
                $insertStmt->bindParam(':ticket_type', $t['ticket_type']);
                $insertStmt->bindParam(':schedule_id', $t['schedule_id']);
                $insertStmt->bindParam(':user_id', $t['user_id']);
                $insertStmt->bindParam(':ticket_vehicle', $t['ticket_vehicle']);
                $insertStmt->bindParam(':contact_person', $t['contact_person']);
                $insertStmt->bindParam(':contact_number', $t['contact_number']);
                $insertStmt->bindParam(':contact_email', $t['contact_email']);
                $insertStmt->bindParam(':contact_address', $t['contact_address']);
                $insertStmt->execute();
            }

            $updateQuery = "UPDATE tickets SET ticket_status = 'Paid' WHERE ticket_code = :ticket_code";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bindParam(':ticket_code', $ticket['ticket_code']);
            $updateStmt->execute();

            if ($insertStmt->rowCount() > 0 && $updateStmt->rowCount() > 0) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'failure', 'message' => 'Failed to update tickets or insert reports']);
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
