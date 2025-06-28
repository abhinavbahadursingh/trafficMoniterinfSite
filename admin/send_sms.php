<?php
require __DIR__ . '/vendor/autoload.php';

use Twilio\Rest\Client;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Twilio Credentials
$sid = "AC2801f30a915ab0b6320a065cea115fed";
$token = "defa35e91a4b90a2e6cdb5e45ee65b0f";
$twilio_number = "+13156271792";
$receiver_number = "+9192590800";

// Firebase URL
$firebase_url = "https://traffic-monitoring-f490d-default-rtdb.firebaseio.com/traffic_data/vehicle_Breakdown.json";

// Fetch latest breakdown entry
$response = file_get_contents($firebase_url);
$data = json_decode($response, true);

// Debugging
file_put_contents("firebase_log.txt", print_r($data, true));

if (!empty($data) && is_array($data)) {
    $latest_entry = end($data);
    $x = $latest_entry['x'] ?? 'Unknown';
    $y = $latest_entry['y'] ?? 'Unknown';

    $message_body = "🚨 Vehicle Accident Alert! Please check the portal. Coordinates: ($x, $y)";

    // Send SMS using Twilio
    try {
        $client = new Client($sid, $token);
        $client->messages->create(
            $receiver_number, 
            ['from' => $twilio_number, 'body' => $message_body]
        );
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "SMS failed: " . $e->getMessage()]);
        exit;
    }

    // Send Email using PHPMailer
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'demo49862020@gmail.com'; // your email
        $mail->Password = 'towMuj-qetrym-1femfu'; // your password or app password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('demo49862020@gmail.com', 'Traffic Monitoring System');
        $mail->addAddress('atyagibdn@gmail.com'); // recipient email

        // Content
        $mail->isHTML(true);
        $mail->Subject = '🚨 Vehicle Accident Alert!';
        $mail->Body    = $message_body;

        $mail->send();
        file_put_contents("email_log.txt", "[" . date("Y-m-d H:i:s") . "] Email sent successfully to recipient@example.com\n", FILE_APPEND);
        echo json_encode(["status" => "success", "message" => "SMS and Email sent successfully!"]);
    } catch (Exception $e) {
        file_put_contents("email_log.txt", "[" . date("Y-m-d H:i:s") . "] Email failed: " . $mail->ErrorInfo . "\n", FILE_APPEND);
        echo json_encode(["status" => "error", "message" => "Email failed: " . $mail->ErrorInfo]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "No breakdown data found"]);
}