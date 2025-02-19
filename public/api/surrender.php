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

// MongoDB connection details
$mongoUri = "mongodb+srv://doadmin:73F5a4nuJ8LY92d1@animalrescue-database-09b50270.mongo.ondigitalocean.com/admin?tls=true&authSource=admin&replicaSet=animalrescue-database";
$databaseName = "animalrescue"; // database name

try{
    // Connect to MongoDB
    $client = new Client($mongoUri);
    $collection = $client->animalrescue->surrenders;

    // Handle only POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get the raw POST data
    $data = json_decode(file_get_contents("php://input"), true);

    $uploadPath = ''; // Initialize the upload path

    if (isset($_FILES['upload']) && $_FILES['upload']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/'; // Directory to store uploaded files
        $uploadPath = $uploadDir . basename($_FILES['upload']['name']);

        if (!move_uploaded_file($_FILES['upload']['tmp_name'], $uploadPath)) {
            // Handle upload error
            echo json_encode(['error' => 'File upload failed']);
            exit;
        }
    }

    // Insert data into MongoDB
    $result = $collection->insertOne([
        'upload' => $uploadPath, // Store file path or URL
        'animalType' => $_POST['animalType'],
        'breed' => $_POST['breed'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'location' => $_POST['location'],
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
   /* $response = [
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ];*/
}

?>