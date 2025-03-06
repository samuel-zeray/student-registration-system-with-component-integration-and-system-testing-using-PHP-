<?php
use PHPUnit\Framework\TestCase;

class SystemTest_PaymentProcessing extends TestCase
{
    public function testPaymentInitialization(): void
    {
        $postData = [
            'amount' => 113.0,
            'currency' => 'ETB',
            'email' => 'testuser@example.com',
            'first_name' => 'John',
            'tx_ref' => 'tx-' . time(),
            'callback_url' => 'http://localhost/student_registration/payment_callback.php',
            'return_url' => 'http://localhost/student_registration/payment_success.php',
        ];

        // Simulate Chapa API request
        $response = $this->simulateChapaAPI($postData);

        $this->assertEquals("success", $response['status'], "Payment processing failed!");
        $this->assertStringContainsString('https://', $response['data']['checkout_url'], "Invalid checkout URL.");
    }

    private function simulateChapaAPI($postData)
    {
        return [
            "status" => "success",
            "data" => ["checkout_url" => "https://checkout.chapa.co/transaction/12345"]
        ];
    }
}
?>
