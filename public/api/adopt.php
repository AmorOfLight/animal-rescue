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
       
        // Initialize variables
        $shelter = $address = $animal = $breed = $visit_date = $visit_time = $name = $email = $phone = "";
    
        // Sanitize and validate input
        if (isset($_POST['shelter'])) {
            $shelter = sanitizeString($_POST['shelter']);
        }
        if (isset($_POST['address'])) {
            $address = sanitizeString($_POST['address']);
        }
        if (isset($_POST['animalType'])) {
            $animal = sanitizeString($_POST['animalType']);
        }
        if (isset($_POST['breed'])) {
            $breed = sanitizeString($_POST['breed']);
        }
        if (isset($_POST['visitDate'])) {
            $visit_date = sanitizeString($_POST['visitDate']);
        }
        if (isset($_POST['visitTime'])) {
            $visit_time = sanitizeString($_POST['visitTime']);
        }
        if (isset($_POST['applicantName'])) {
            $name = sanitizeString($_POST['applicantName']);
        }
        if (isset($_POST['email'])) {
            $email = sanitizeInput($_POST['email']);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                die("Invalid email address!");
            }
        }
        if (isset($_POST['phone'])) {
            $phone = sanitizeInput($_POST['phone']);
            // Add phone number validation if needed
        }
    
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
            echo "Data inserted successfully!";
        } else {
            echo "Failed to insert data.";
        }
    }
    
    // Sanitize string input
    function sanitizeString($input) {
        $input = trim($input); // Remove extra spaces
        $input = strip_tags($input); // Remove HTML tags
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8'); // Escape special characters
        return $input;
    }
    
    // Sanitize and validate specific input types
    function sanitizeInput($input) {
        $input = trim($input);
        $input = strip_tags($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        return $input;
    }

}catch (Exception $e) {
    //echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>