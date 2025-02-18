<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Enable error reporting (for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

//require 'vendor/autoload.php'; // Include Composer autoload
require __DIR__ . '/../../vendor/autoload.php';


use MongoDB\Client;

// MongoDB connection
$mongoUri = "mongodb+srv://doadmin:73F5a4nuJ8LY92d1@animalrescue-database-09b50270.mongo.ondigitalocean.com/admin?tls=true&authSource=admin&replicaSet=animalrescue-database";
$client = new Client($mongoUri);
$collection = $client->animalrescue->surrenders;


// Get form data
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Insert data into MongoDB
    $result = $collection->insertOne([
        'upload' => $data['upload'], // Store file path or URL
        'animalType' => $data['animalType'],
        'breed' => $data['breed'],
        'email' => $data['email'],
        'phone' => $data['phone'],
        'location' => $data['location'],
        'timestamp' => new MongoDB\BSON\UTCDateTime()
    ]);

    if ($result->getInsertedCount() === 1) {
        echo json_encode(['status' => 'success', 'message' => 'Pet surrender submitted']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to submit surrender']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>