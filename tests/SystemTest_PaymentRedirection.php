<?php
use PHPUnit\Framework\TestCase;

class SystemTest_PaymentRedirection extends TestCase
{
    public function testPaymentPageRedirection(): void
    {
        $registrationId = 1; // Assume valid registration ID
        $redirectUrl = "http://localhost/student_registration/payment.html?registration_id=$registrationId";

        // Simulate redirection to payment page
        $headers = get_headers($redirectUrl, 1);
        $this->assertStringContainsString("200", $headers[0], "Redirection to payment page failed!");
    }
}
?>
