<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://plankton-app-2evxj.ondigitalocean.app");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Include Composer autoload
require __DIR__ . '/../../vendor/autoload.php';

use MongoDB\Client;
require_once __DIR__ . '/../db.php';


//Connect to MongoDB
$client = new Client($mongoUri);
$collection = $client->animalrescue->adoptions;

// Sanitize string input
function sanitizeString($input) {
    $input = trim($input);
    $input = strip_tags($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){

    // Initialize variables
    $shelter = $address = $animal = $breed = $visit_date = $visit_time = $name = $email = $phone = "";

    // Sanitize inputs
    $shelter = isset($_POST['shelter']) ? sanitizeString($_POST['shelter']) : '';
    $address = isset($_POST['address']) ? sanitizeString($_POST['address']) : '';
    $animal = isset($_POST['animalType']) ? sanitizeString($_POST['animalType']) : '';
    $breed = isset($_POST['breed']) ? sanitizeString($_POST['breed']) : '';
    $visit_date = isset($_POST['visitDate']) ? sanitizeString($_POST['visitDate']) : '';
    $visit_time = isset($_POST['visitTime']) ? sanitizeString($_POST['visitTime']) : '';
    $name = isset($_POST['applicantName']) ? sanitizeString($_POST['applicantName']) : '';
    $email = isset($_POST['email']) ? sanitizeString($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitizeString($_POST['phone']) : '';

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["message" => "Invalid email address!"]);
        exit;
    }

    try{

        // Insert sanitized data into MongoDB
        $result = $collection->insertOne([
            'shelter' => $shelter,
            'address' => $address,
            'animalType' => $animal,
            'breed' => $breed,
            'visitDate' => $visit_date,
            'visitTime' => $visit_time,
            'applicantName' => $name,
            'email' => $email,
            'phone' => $phone,
            'timestamp' => new MongoDB\BSON\UTCDateTime()
        ]);
    
        if ($result->getInsertedCount() > 0) {
            echo json_encode(["message" => "Data inserted successfully!"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to insert data."]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["message" => "Server error: " . $e->getMessage()]);
    }
}


?>