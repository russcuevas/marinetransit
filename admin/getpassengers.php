<?php
include '../connection/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the ticket ID from the POST data
    $ticket_id = $_POST['id'];

    try {
        // First, check if the ticket_id exists in the passenger_cargos table
        $cargo_check_query = "SELECT * FROM passenger_cargos WHERE ticket_id = :ticket_id";
        $cargo_check_stmt = $conn->prepare($cargo_check_query);
        $cargo_check_stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
        $cargo_check_stmt->execute();
        $cargo_exists = $cargo_check_stmt->fetch(PDO::FETCH_ASSOC);

        if ($cargo_exists) {
            // If cargo exists, get the cargo details and join with the tickets table to get ticket_code
            $cargo_query = "
                SELECT 
                    pc.passenger_cargo_id,
                    pc.ticket_id,
                    pc.cargo_id,
                    pc.passenger_cargo_brand,
                    pc.passenger_cargo_plate,
                    t.ticket_code  -- Joining with tickets table to get ticket_code
                FROM 
                    passenger_cargos pc
                JOIN 
                    tickets t ON pc.ticket_id = t.ticket_id  -- Join with the tickets table
                WHERE 
                    pc.ticket_id = :ticket_id
            ";
            $cargo_stmt = $conn->prepare($cargo_query);
            $cargo_stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
            $cargo_stmt->execute();
            $cargos = $cargo_stmt->fetchAll(PDO::FETCH_ASSOC);

            // Return cargo data along with ticket_code
            echo json_encode(['type' => 'cargo', 'data' => $cargos]); // Send 'cargo' type and data
        } else {
            // If no cargo exists, get passenger details
            $query = "
                SELECT 
                    p.passenger_id,
                    p.ticket_id,
                    p.passenger_fname,
                    p.passenger_mname,
                    p.passenger_lname,
                    p.passenger_bdate,
                    p.passenger_contact,
                    p.passenger_address,
                    p.passenger_type,
                    t.ticket_code,
                    t.ticket_date,
                    s.schedule_date,
                    s.schedule_time,
                    sh.ship_name
                FROM 
                    passengers p
                JOIN 
                    tickets t ON p.ticket_id = t.ticket_id
                JOIN 
                    schedules s ON t.schedule_id = s.schedule_id
                JOIN 
                    ships sh ON s.ship_id = sh.ship_id
                WHERE 
                    p.ticket_id = :ticket_id
            ";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
            $stmt->execute();
            $passengers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['type' => 'passenger', 'data' => $passengers]); // Send 'passenger' type and data
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
