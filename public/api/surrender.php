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

// Connect to MongoDB
$client = new Client($mongoUri);
$collection = $client->animalrescue->surrenders;

// Sanitize string input
function sanitizeString($input) {
    $input = trim($input);
    $input = strip_tags($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}
  

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialize variables
    $type = $breed = $name = $email = $phone = $location = "";
    $imageData = [];

    // Validate required fields (example: email and photos)
    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["message" => "Valid email is required."]);
        exit;
    }

    // File upload handling
    if (empty($_FILES['photos'])) {
        http_response_code(400);
        echo json_encode(["message" => "At least one photo is required."]);
        exit;
    }

    $uploadedFiles = $_FILES['photos'];
    $maxFiles = 5;
    $maxFileSize = 5 * 1024 * 1024; // 5MB
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];

    // Validate file count
    if (count($uploadedFiles['name']) > $maxFiles) {
        http_response_code(400);
        echo json_encode(["message" => "Maximum of $maxFiles files allowed."]);
        exit;
    }

    // Process each file
    foreach ($uploadedFiles['tmp_name'] as $i => $tmpFilePath) {
        // Check for upload errors
        if ($uploadedFiles['error'][$i] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(["message" => "Error uploading file."]);
            exit;
        }

        // Validate file size
        if ($uploadedFiles['size'][$i] > $maxFileSize) {
            http_response_code(400);
            echo json_encode(["message" => "File exceeds 5MB limit."]);
            exit;
        }

        // Validate MIME type using finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedMime = finfo_file($finfo, $tmpFilePath);
        finfo_close($finfo);
        if (!in_array($detectedMime, $allowedMimeTypes)) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid file type."]);
            exit;
        }

        // Read file content
        $imageData[] = file_get_contents($tmpFilePath);
    }

    // Sanitize inputs (adjust sanitizeString as needed)
    $type = sanitizeString($_POST['animalType'] ?? '');
    $breed = sanitizeString($_POST['breed'] ?? '');
    $name = sanitizeString($_POST['name'] ?? '');
    $email = sanitizeString($_POST['email'] ?? '');
    $phone = sanitizeString($_POST['phone'] ?? '');
    $location = sanitizeString($_POST['location'] ?? '');

    try {
        // Insert into MongoDB
        $result = $collection->insertOne([
            'photos' => $imageData,
            'animalType' => $type,
            'breed' => $breed,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'location' => $location,
            'timestamp' => new MongoDB\BSON\UTCDateTime()
        ]);

        if ($result->getInsertedCount() > 0) {
            echo json_encode(["message" => "Data inserted successfully!"]);
        } else {
            throw new Exception("Insert failed.");
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["message" => "Server error: " . $e->getMessage()]);
    }
}

?>