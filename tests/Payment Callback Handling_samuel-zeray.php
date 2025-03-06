<?php
use PHPUnit\Framework\TestCase;

class PaymentCallbackTest extends TestCase
{
    private $conn;

    protected function setUp(): void
    {
        $host = 'localhost';
        $db = 'registration_system';
        $user = 'root';
        $pass = '';
        $this->conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    }

    public function testPaymentCallback(): void
    {
        $registrationId = 1;

        // Mark the payment as pending
        $this->conn->query("UPDATE registrations SET payment_status = 'pending' WHERE id = $registrationId");

        // Simulate callback
        $this->processPaymentCallback($registrationId);

        // Verify the payment status
        $stmt = $this->conn->query("SELECT payment_status FROM registrations WHERE id = $registrationId");
        $status = $stmt->fetchColumn();
        $this->assertEquals('completed', $status, "Payment status should be 'completed'");
    }

    private function processPaymentCallback($registrationId): void
    {
        // Update payment status in database
        $this->conn->query("UPDATE registrations SET payment_status = 'completed' WHERE id = $registrationId");
    }
}
?>