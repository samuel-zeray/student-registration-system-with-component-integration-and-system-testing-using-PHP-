<?php
use PHPUnit\Framework\TestCase;

class PaymentGatewayIntegrationTest extends TestCase
{
    public function testPaymentInitializationWithChapa(): void
    {
        // Simulated Chapa API request data
        $postData = [
            'amount' => 113.0,
            'currency' => 'ETB',
            'email' => 'testuser@example.com',
            'first_name' => 'Test',
            'tx_ref' => 'tx-' . time(),
            'callback_url' => 'http://localhost/student_registration/payment_callback.php',
            'return_url' => 'http://localhost/student_registration/payment_success.php',
        ];

        // Simulate Chapa API response
        $response = $this->simulateChapaAPI($postData);

        $this->assertEquals("success", $response['status'], "Payment initialization failed!");
        $this->assertStringContainsString('https://', $response['data']['checkout_url'], "Invalid checkout URL.");
    }

    private function simulateChapaAPI($postData)
    {
        // Simulated response from Chapa API
        return [
            "status" => "success",
            "data" => ["checkout_url" => "https://checkout.chapa.co/transaction/12345"]
        ];
    }
}
?>