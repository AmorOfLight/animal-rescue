<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://plankton-app-2evxj.ondigitalocean.app");// Restrict to trusted domain
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");


// Include Composer autoload
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

    if(isset($_FILES['photos[]'])){
        $picture = $_FILES['photos[]'];

        if(!empty($picture['tmp_name'])){
            // Read the image file and convert it to Base64
            $pictureData = base64_encode(file_get_contents($picture['tmp_name']));
        }
    }

    // Insert data into MongoDB
    $result = $collection->insertOne([
        'photos[]' => $pictureData,
        'animalType' =>$_POST['animalType'],
        'breed' =>$_POST['breed'],
        'name' =>$_POST['name'],
        'email' =>$_POST['email'],
        'phone' =>$_POST['phone'],
        'location' =>$_POST['location'],
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