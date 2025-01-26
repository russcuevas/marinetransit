<?php
include '../connection/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = $_POST['id'];

    try {
        $ticket_code_query = "SELECT ticket_code FROM tickets WHERE ticket_id = :ticket_id";
        $ticket_code_stmt = $conn->prepare($ticket_code_query);
        $ticket_code_stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
        $ticket_code_stmt->execute();
        $ticket_code_data = $ticket_code_stmt->fetch(PDO::FETCH_ASSOC);
        $ticket_code = $ticket_code_data['ticket_code'];

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
    pc.accomodation_id,
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
    cargos c ON pc.accomodation_id = c.cargo_id  -- Join 'accomodation_id' with 'cargo_id'
LEFT JOIN 
    accomodations a ON pc.accomodation_id = a.accomodation_id  -- Join 'accomodation_id' with 'accomodations'
JOIN
    schedules s ON t.schedule_id = s.schedule_id
JOIN
    ships sh ON s.ship_id = sh.ship_id
WHERE 
    t.ticket_code = :ticket_code
";
            $cargo_stmt = $conn->prepare($cargo_query);
            $cargo_stmt->bindParam(':ticket_code', $ticket_code, PDO::PARAM_STR);
            $cargo_stmt->execute();
            $cargos = $cargo_stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['type' => 'cargo', 'data' => $cargos]);
        } else {
            echo json_encode(['type' => 'cargo', 'data' => []]);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
