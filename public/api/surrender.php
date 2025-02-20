<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://plankton-app-2evxj.ondigitalocean.app");// Restrict to trusted domain
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

/* Enable error reporting (for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);*/

//require 'vendor/autoload.php'; // Include Composer autoload
require __DIR__ . '/../../vendor/autoload.php';


use MongoDB\Client;

// MongoDB connection details
$mongoUri = "mongodb+srv://doadmin:73F5a4nuJ8LY92d1@animalrescue-database-09b50270.mongo.ondigitalocean.com/admin?tls=true&authSource=admin&replicaSet=animalrescue-database";
$databaseName = "animalrescue"; // database name

try{
    // Connect to MongoDB
    $client = new Client($mongoUri);
    $collection = $client->animalrescue->surrenders;

    // Handle only POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES["petPhotos"])) {

    // Get the raw POST data
    $data = json_decode(file_get_contents("php://input"), true);


    $image = $_FILES["petPhotos"];
    $imageData = file_get_contents($image["tmp_name"]); // Read file content
    // Prepare data for MongoDB
    $document = [
        "filename" => $image["petPhotos"],
        "content_type" => $image["type"],
        "image_data" => new MongoDB\BSON\Binary($imageData, MongoDB\BSON\Binary::TYPE_GENERIC)
    ];



    // Sanitize input
    $upload = htmlspecialchars($data['petPhotos'],ENT_QUOTES, 'UTF-8');
    $type = htmlspecialchars($data['animalType'],ENT_QUOTES, 'UTF-8');
    $breed = htmlspecialchars($data['breed'], ENT_QUOTES, 'UTF-8');
    $name = htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($data['phone'], ENT_QUOTES, 'UTF-8'); 
    $location = htmlspecialchars($data['location'], ENT_QUOTES, 'UTF-8');

    // Validate input
    if (empty($upload) || empty($type) || empty($breed) || empty($name) || empty($email) || empty($phone) || empty($location)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        exit;
    }

    // Insert data into MongoDB
    $result = $collection->insertOne([
        'upload' => $upload, // Store file path or URL
        'animalType' =>$type, //$_POST['animalType'],
        'breed' =>$breed, //$_POST['breed'],
        'name' =>$name, //$_POST['name'],
        'email' =>$email, //$_POST['email'],
        'phone' =>$phone, //$_POST['phone'],
        'location' =>$location, //$_POST['location'],
        'timestamp' => new MongoDB\BSON\UTCDateTime()
    ]);

    if ($result->getInsertedCount() === 1) {
        echo json_encode(['status' => 'success', 'message' => 'Pet surrender submitted']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to submit surrender']);
    }
}else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
}catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}

?>