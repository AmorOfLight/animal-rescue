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
$collection = $client->animalrescue->adoptions;

// Get form data
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize input
    $shelter = htmlspecialchars($data['shelter'],ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars($data['address'],ENT_QUOTES, 'UTF-8');
    $type = htmlspecialchars($data['animalType'], ENT_QUOTES, 'UTF-8');
    $breed = htmlspecialchars($data['breed'], ENT_QUOTES, 'UTF-8');
    $visit = htmlspecialchars($data['visitDate'], ENT_QUOTES, 'UTF-8');
    $time = htmlspecialchars($data['visitTime'], ENT_QUOTES, 'UTF-8'); 
    $name = htmlspecialchars($data['applicantName'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($data['phone'], ENT_QUOTES, 'UTF-8');

    // Validate input
    if (empty($shelter) || empty($address) || empty($type) || empty($breed) || empty($visit) || empty($time) || empty($name) || empty($email) || empty($phone)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        exit;
    }


    // Insert data into MongoDB
    $result = $collection->insertOne([
        'shelter' =>$shelter, //$data['shelter'],
        'address' =>$address, //$data['address'],
        'animalType' =>$type, //$data['animalType'],
        'breed' =>$breed, //$data['breed'],
        'visitDate' =>$visit, //$data['visitDate'],
        'visitTime' =>$time, //$data['visitTime'],
        'applicantName' =>$name, //$data['applicantName'],
        'email' =>$email, //$data['email'],
        'phone' =>$phone, //$data['phone'],
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