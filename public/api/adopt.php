<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require 'vendor/autoload.php'; // Include Composer autoload

use MongoDB\Client;

// MongoDB connection
$mongoUri = "mongodb+srv://doadmin:73F5a4nuJ8LY92d1@animalrescue-database-09b50270.mongo.ondigitalocean.com/admin?tls=true&authSource=admin&replicaSet=animalrescue-database";
$client = new Client($mongoUri);
$collection = $client->animal_shelter->adoptions;

// Get form data
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Insert data into MongoDB
    $result = $collection->insertOne([
        'shelter' => $data['shelter'],
        'shelter_address' => $data['address'],
        'name' => $data['applicantName'],
        'email' => $data['email'],
        'phone' => $data['phone'],
        'timestamp' => new MongoDB\BSON\UTCDateTime()
    ]);

    if ($result->getInsertedCount() === 1) {
        echo json_encode(['status' => 'success', 'message' => 'Adoption application submitted']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to submit application']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>