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

     // Initialize variables
     $name = $email = $phone = $subject = $message = "";
    
     // Sanitize and validate input
     if (isset($_POST['name'])) {
         $name = sanitizeString($_POST['name']);
     }
     if (isset($_POST['email'])) {
         $email = sanitizeString($_POST['email']);
         if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            die("Invalid email address!");
        }
     }
     if (isset($_POST['phone'])) {
         $phone = sanitizeString($_POST['phone']);
         // Add phone number validation if needed
     }
     if (isset($_POST['subject'])) {
         $subject = sanitizeString($_POST['subject']);
     }
     if (isset($_POST['message'])) {
         $message = sanitizeString($_POST['message']);
     }
 
     // Insert sanitized data into MongoDB
     $result = $collection->insertOne([
         'name' => $name,
         'email' => $email,
         'phone' => $phone,
         'subject' => $subject,
         'message' => $message,
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

/*if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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
        echo json_encode(['status' => 'success', 'message' => 'Contact details submitted']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to submit application']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}*/
}catch (Exception $e) {
   // echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>