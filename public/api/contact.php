<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://plankton-app-2evxj.ondigitalocean.app");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

error_reporting(E_ALL);
ini_set('display_errors', 1);


// Include Composer autoload
require __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../db.php';

use MongoDB\Client;
//Connect to MongoDB
$client = new Client($mongoUri);
$collection = $client->animalrescue->contact;

function sanitizeString($input) {
    $input = trim($input);
    $input = strip_tags($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $email = $phone = $subject = $message = "";

    // Sanitize inputs
    $name = isset($_POST['name']) ? sanitizeString($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitizeString($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitizeString($_POST['phone']) : '';
    $subject = isset($_POST['subject']) ? sanitizeString($_POST['subject']) : '';
    $message = isset($_POST['message']) ? sanitizeString($_POST['message']) : '';

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["message" => "Invalid email address!"]);
        exit;
    }

    try {
        // Insert into MongoDB
        $result = $collection->insertOne([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message,
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