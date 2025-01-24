<?php
include 'connection/database.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['schedule_id'])) {
    $schedule_id = $_POST['schedule_id'];

    try {
        $query = "
            SELECT sa.accomodation_id, a.accomodation_name, sa.net_fare
            FROM schedule_accom sa
            JOIN accomodations a ON sa.accomodation_id = a.accomodation_id
            WHERE sa.schedule_id = :schedule_id
            AND a.accomodation_type = 'passenger'
        ";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
        $stmt->execute();

        $accommodation_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($accommodation_details) {
            echo json_encode([
                'success' => true,
                'accommodation_details' => $accommodation_details
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No accommodations found for the selected schedule.']);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred while fetching accommodations: ' . $e->getMessage()
        ]);
    }
    die();
}
