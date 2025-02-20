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

    //validation
    $name = $email = $number = "";

    if(isset($_POST['name'])) $name = fix_string($_POST['name']);
    if(isset($_POST['email'])) $email = fix_string($_POST['email']);
    if(isset($_POST['phone'])) $number = fix_string($_POST['phone']);

    $fail = validate_name($name);
    $fail .= validate_email($email);
    $fail .= validate_phone($number);

    if ($fail !== "") {
        echo json_encode(['status' => 'error', 'message' => $fail]);
        exit;
    }

    function validate_name($field){
        if (strlen($field) < 5) return "Name must be at least 5 characters<br>";
        else if (preg_match("/^[a-zA-Z\s]+$/", $field)) return "Only letters and spaces allowed in Name <br>";
        return "";
    }
    function validate_email($field){
        if (!((strpos($field, ".") >0) && (strpos($field, "@") >0))|| preg_match("/[^a-zA-Z0-9.@_-]/", $field))
        return  "Email address is invalid <br>";
     return "";
    }
    function validate_phone($field){
        if (strlen($field) <10) return "Number must be at least 10 digits <br>";
        else if (preg_match("/^[0-9]{10,15}$/", $field)) return "Phone number must be 10-15 digits <br>";
        return "";
    }

    function fix_string($string){
        return htmlentities($string, ENT_QUOTES, 'UTF-8');
    }
    //validation


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
        'animalType' => $_POST['animalType']??'',
        'breed' => $_POST['breed']??'',
        'name' =>$name, //$_POST['name'],
        'email' =>$email, //$_POST['email'],
        'phone' =>$number, //$_POST['phone'],
        'location' => $_POST['location']??'',
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