<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://plankton-app-2evxj.ondigitalocean.app");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Include Composer autoload
require __DIR__ . '/../../vendor/autoload.php';

use MongoDB\Client;

// MongoDB connection
$mongoUri = "mongodb+srv://doadmin:73F5a4nuJ8LY92d1@animalrescue-database-09b50270.mongo.ondigitalocean.com/admin?tls=true&authSource=admin&replicaSet=animalrescue-database";
$databaseName = "animalrescue"; // database name


try{
    //Connect to MongoDB
    $client = new Client($mongoUri);
    $collection = $client->animalrescue->adoptions;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get form data
    $data = json_decode(file_get_contents("php://input"), true);

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


    // Insert data into MongoDB
    $result = $collection->insertOne([
        'shelter' =>$data['shelter'],
        'address' =>$data['address'],
        'animalType' =>$data['animalType'],
        'breed' =>$data['breed'],
        'visitDate' =>$data['visitDate'],
        'visitTime' =>$data['visitTime'],
        'applicantName' =>$data['applicantName'],
        'email' =>$email['email'],
        'phone' =>$data['phone'],
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
}catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>