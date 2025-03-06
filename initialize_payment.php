<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $registrationId = $_POST['registration_id'];
    $providedAmount = $_POST['amount'];

    try {
        // Fetch registration details and associated courses
        $query = "SELECT r.course_ids, s.email, s.name 
                  FROM registrations r
                  JOIN students s ON r.student_id = s.id 
                  WHERE r.id = :registration_id";
        $stmt = $conn->prepare($query);
        $stmt->execute(['registration_id' => $registrationId]);
        $registration = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($registration) {
            // Calculate total fee for selected courses
            $courseIds = explode(',', $registration['course_ids']);
            $placeholders = implode(',', array_fill(0, count($courseIds), '?'));
            $courseQuery = "SELECT SUM(course_fee) AS total_fee FROM courses WHERE id IN ($placeholders)";
            $courseStmt = $conn->prepare($courseQuery);
            $courseStmt->execute($courseIds);
            $courseData = $courseStmt->fetch(PDO::FETCH_ASSOC);

            $totalFee = $courseData['total_fee'];

            if ($providedAmount == $totalFee) {
                // Initialize payment via Chapa
                $chapaUrl = "https://api.chapa.co/v1/transaction/initialize";
                $chapaSecretKey = "CHASECK_TEST-w56dsQe5GgBY3NaAQVmJLc6rlqWg9YhN";

                $postData = [
                    'amount' => $totalFee,
                    'currency' => 'ETB',
                    'email' => $registration['email'],
                    'first_name' => $registration['name'],
                    'tx_ref' => 'tx-' . time(),
                    'callback_url' => 'http://localhost/student_registration/payment_callback.php',
                    //'return_url' => 'http://localhost/student_registration/payment_success.php',
                    'customization' => [
                        'title' => 'Reg Payment',
                        'description' => 'Payment for selected courses'
                    ]
                ];

                $ch = curl_init($chapaUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $chapaSecretKey,
                    'Content-Type: application/json'
                ]);

                $response = curl_exec($ch);
                $error = curl_error($ch);
                curl_close($ch);

                if ($error) {
                    echo "Error connecting to Chapa: $error";
                } else {
                    $responseData = json_decode($response, true);

                    if (isset($responseData['status']) && $responseData['status'] === 'success') {
                        $paymentUrl = $responseData['data']['checkout_url'];
                        header("Location: $paymentUrl");
                        exit;
                    } else {
                        echo "Failed to initialize payment: ";
                        if (is_array($responseData['message'])) {
                            echo implode(', ', $responseData['message']);
                        } else {
                            echo $responseData['message'];
                        }
                        // Debugging output for full response
                        echo '<pre>';
                        print_r($responseData);
                        echo '</pre>';
                    }
                }
            } else {
                echo "The provided payment amount does not match the total course fees.";
            }
        } else {
            echo "Registration not found!";
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
}
?>
