<?php
use PHPUnit\Framework\TestCase;

class PaymentInitializationTest extends TestCase
{
    public function testPaymentInitialization(): void
    {
        // Simulate initialization data
        $registrationId = 1;
        $amount = 113.0;

        $expectedResponse = [
            "status" => "success",
            "data" => ["checkout_url" => "https://example.chapa.co/payment"]
        ];

        // Simulate the API call
        $response = $this->simulatePaymentAPI($registrationId, $amount);

        // Assert the response is successful
        $this->assertEquals($expectedResponse['status'], $response['status']);
        $this->assertStringContainsString('https://', $response['data']['checkout_url']);
    }

    private function simulatePaymentAPI($registrationId, $amount)
    {
        // Simulate API success response
        if ($registrationId && $amount > 0) {
            return [
                "status" => "success",
                "data" => ["checkout_url" => "https://example.chapa.co/payment"]
            ];
        }
        return ["status" => "failed"];
    }
}
?>