<?php
use PHPUnit\Framework\TestCase;

class SystemTest_DatabaseIntegrity extends TestCase
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

    public function testPaymentCompletionUpdatesDatabase(): void
    {
        $registrationId = 1;

        // Verify the payment status in database
        $stmt = $this->conn->prepare("SELECT payment_status FROM registrations WHERE id = :id");
        $stmt->execute(['id' => $registrationId]);
        $paymentStatus = $stmt->fetchColumn();

        $this->assertEquals('completed', $paymentStatus, "Database update failed after payment!");
    }
}
?>
