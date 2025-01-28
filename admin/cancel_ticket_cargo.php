<?php
include '../connection/database.php';

if (isset($_POST['ticket_code'])) {
    $ticket_code = $_POST['ticket_code'];  // Use ticket_code instead of ticket_id

    try {
        // Prepare and execute the query to update all tickets with the same ticket_code to 'Cancelled'
        $query = "UPDATE tickets SET ticket_status = 'Cancelled' WHERE ticket_code = :ticket_code";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':ticket_code', $ticket_code, PDO::PARAM_STR);
        $stmt->execute();

        // Check if the update was successful
        if ($stmt->rowCount() > 0) {
            // Return success response if tickets were updated
            echo json_encode(['status' => 'success']);
        } else {
            // If no rows were affected (maybe already cancelled or invalid ticket_code)
            echo json_encode(['status' => 'failure', 'message' => 'No tickets updated.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'failure', 'message' => 'Ticket code not provided']);
}
