<?php
include '../connection/database.php';

if (isset($_POST['ticket_id'])) {
    $ticket_id = $_POST['ticket_id'];

    try {
        // Prepare and execute the query to update ticket status
        $query = "UPDATE tickets SET ticket_status = 'Cancelled' WHERE ticket_id = :ticket_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
        $stmt->execute();

        // Check if the update was successful
        if ($stmt->rowCount() > 0) {
            // Return success response
            echo json_encode(['status' => 'success']);
        } else {
            // If no rows were affected (maybe already cancelled or invalid ticket_id)
            echo json_encode(['status' => 'failure', 'message' => 'No rows updated']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'failure', 'message' => 'Ticket ID not provided']);
}
