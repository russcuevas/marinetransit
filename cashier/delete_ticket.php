<?php
include '../connection/database.php';

if (isset($_POST['ticket_id'])) {
    $ticket_id = $_POST['ticket_id'];

    try {
        $query = "SELECT ticket_code FROM tickets WHERE ticket_id = :ticket_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
        $stmt->execute();

        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ticket) {
            $ticket_code = $ticket['ticket_code'];

            $deleteQuery = "DELETE FROM tickets WHERE ticket_code = :ticket_code";
            $deleteStmt = $conn->prepare($deleteQuery);
            $deleteStmt->bindParam(':ticket_code', $ticket_code, PDO::PARAM_STR);
            $deleteStmt->execute();

            if ($deleteStmt->rowCount() > 0) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'failure', 'message' => 'Failed to delete the tickets']);
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
