<?php
include '../connection/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the ticket ID from the POST data
    $ticket_id = $_POST['id'];

    try {
        // Query to get cargo details based on ticket_id
        $query = "SELECT * FROM passenger_cargos WHERE ticket_id = :ticket_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch the results
        $cargos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the result as JSON
        echo json_encode($cargos);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
