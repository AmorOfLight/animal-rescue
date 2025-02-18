<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require 'vendor/autoload.php'; // Include Composer autoload

use MongoDB\Client;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Stripe\Stripe;
use Stripe\PaymentIntent;

// MongoDB connection
$mongoUri = "mongodb+srv://mongodb+srv://doadmin:73F5a4nuJ8LY92d1@animalrescue-database-09b50270.mongo.ondigitalocean.com/admin?tls=true&authSource=admin&replicaSet=animalrescue-database";
$client = new Client($mongoUri);
$collection = $client->animal_shelter->donations;

// Stripe configuration
Stripe::setApiKey(getenv('STRIPE_SECRET_KEY')); // Use environment variable


// Get form data
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        // Create a PaymentIntent with Stripe
        $paymentIntent = PaymentIntent::create([
            'amount' => $data['amount'] * 100, // Convert to cents
            'currency' => 'zar',
            'payment_method' => $data['payment_method'],
            'confirmation_method' => 'manual',
            'confirm' => true,
            'description' => 'Donation from ' . $data['name'],
            'receipt_email' => $data['email'],
        ]);

    // Insert data into MongoDB
    $result = $collection->insertOne([
        'name' => $data['name'],
        'email' => $data['email'],
        'amount' => $data['amount'],
        'timestamp' => new MongoDB\BSON\UTCDateTime()
    ]);

    if ($result->getInsertedCount() === 1) {
        // Send email notification
        $mail = new PHPMailer(true);

        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = getenv('EMAIL_USERNAME'); // Use environment variable
            $mail->Password = getenv('EMAIL_PASSWORD'); // Use environment variable
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Sender and recipient
            $mail->setFrom('noreply@animalshelter.com', 'Animal Shelter');
            $mail->addAddress($data['email'], $data['name']); // Send to user
            $mail->addAddress('admin@animalshelter.com'); // Send to admin

             // Email content
             $mail->isHTML(true);
             $mail->Subject = 'Donation Received';
             $mail->Body = "
                 <h1>Thank you for your donation!</h1>
                 <p>We have received your donation of <strong>ZAR {$data['amount']}</strong>.</p>
                 <p>Your support helps us save more animals.</p>
                 <p>Best regards,<br>Animal Shelter Team</p>
             ";
             $mail->send();

        echo json_encode(['status' => 'success', 'message' => 'Donation submitted and email sent']);
        } catch (Exception $e){
           echo json_encode(['status' => 'error', 'message' => 'Email could not be sent: ' . $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to submit donation']);
    }
} catch (\Stripe\Exception\ApiErrorException $e){
    echo json_encode(['status' => 'error', 'message' => 'Payment failed: ' . $e->getMessage()]);
}
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>