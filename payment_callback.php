<?php
require 'config.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Log Chapa's callback data
file_put_contents('chapa_callback.log', $input, FILE_APPEND);

if (isset($data['tx_ref']) && isset($data['status']) && $data['status'] === 'success') {
    $transactionRef = $data['tx_ref'];
    $registrationId = str_replace('tx-', '', $transactionRef);

    $updateQuery = "UPDATE registrations SET payment_status = 'completed' WHERE id = :registration_id";
    $stmt = $conn->prepare($updateQuery);
    $stmt->execute(['registration_id' => $registrationId]);

    echo "Payment successful for Registration ID: $registrationId";
} else {
    echo "Payment failed or invalid callback data.";
}
?>
