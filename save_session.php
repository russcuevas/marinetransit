<?php
session_start();

// Initialize session array for passengers if not already done
if (!isset($_SESSION['passengers'])) {
    $_SESSION['passengers'] = [];
}

// Collect passenger data from the AJAX request
$passenger = [
    'id' => $_POST['passenger_id'] ?? null,
    'type' => $_POST['passenger_type'] ?? null,
    'first_name' => $_POST['passenger_fname'] ?? null,
    'middle_name' => $_POST['passenger_mname'] ?? null,
    'last_name' => $_POST['passenger_lname'] ?? null,
    'birthdate' => $_POST['passenger_bdate'] ?? null,
    'contact' => $_POST['passenger_contact'] ?? null,
    'gender' => $_POST['passenger_gender'] ?? null,
    'address' => $_POST['passenger_address'] ?? null,
];

// Append passenger data to the session
$_SESSION['passengers'][] = $passenger;

// Respond with success
echo json_encode(['success' => true]);
exit;
