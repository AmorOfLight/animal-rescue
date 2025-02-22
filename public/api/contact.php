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
    $collection = $client->animalrescue->contact;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get form data
    $data = json_decode(file_get_contents("php://input"), true);

    // Insert data into MongoDB
    $result = $collection->insertOne([
        'name' =>$_POST['name'],
        'email' =>$_POST['email'],
        'phone' =>$_POST['phone'],
        'subject' =>$_POST['subject'],
        'message' =>$_POST['message'],
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