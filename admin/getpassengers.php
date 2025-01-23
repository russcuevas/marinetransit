<?php
include '../connection/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = $_POST['id'];

    try {
        $cargo_check_query = "SELECT * FROM passenger_cargos WHERE ticket_id = :ticket_id";
        $cargo_check_stmt = $conn->prepare($cargo_check_query);
        $cargo_check_stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
        $cargo_check_stmt->execute();
        $cargo_exists = $cargo_check_stmt->fetch(PDO::FETCH_ASSOC);

        if ($cargo_exists) {
            $cargo_query = "
        SELECT 
            pc.passenger_cargo_id,
            pc.ticket_id,
            pc.cargo_id,
            pc.passenger_cargo_brand,
            pc.passenger_cargo_plate,
            t.ticket_code, 
            t.ticket_date,
            c.cargo_name, 
            a.accomodation_name,
            sh.ship_name 
        FROM 
            passenger_cargos pc
        JOIN 
            tickets t ON pc.ticket_id = t.ticket_id
        JOIN 
            cargos c ON pc.cargo_id = c.cargo_id
        LEFT JOIN 
            accomodations a ON c.cargo_name = a.accomodation_name
        JOIN
            schedules s ON t.schedule_id = s.schedule_id
        JOIN
            ships sh ON s.ship_id = sh.ship_id
        WHERE 
            pc.ticket_id = :ticket_id
    ";
            $cargo_stmt = $conn->prepare($cargo_query);
            $cargo_stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
            $cargo_stmt->execute();
            $cargos = $cargo_stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['type' => 'cargo', 'data' => $cargos]);
        } else {
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

            echo json_encode(['type' => 'passenger', 'data' => $passengers]);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
