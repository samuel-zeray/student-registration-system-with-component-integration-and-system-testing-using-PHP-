<?php
use PHPUnit\Framework\TestCase;

class SystemTest_FullWorkflow extends TestCase
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

    public function testCompleteRegistrationAndPayment(): void
    {
        // Step 1: Register a Student
        $name = "John Doe";
        $email = "johndoe@example.com";
        $phone = "1234567890";

        $this->conn->query("DELETE FROM students WHERE email = '$email'");
        $stmt = $this->conn->prepare("INSERT INTO students (name, email, phone) VALUES (:name, :email, :phone)");
        $stmt->execute(['name' => $name, 'email' => $email, 'phone' => $phone]);
        $studentId = $this->conn->lastInsertId();
        $this->assertNotEmpty($studentId, "Student registration failed!");

        // Step 2: Select Courses
        $selectedCourses = [1, 3]; // C++ and AI
        $courseIds = implode(',', $selectedCourses);

        $stmt = $this->conn->prepare("INSERT INTO registrations (student_id, course_ids) VALUES (:student_id, :course_ids)");
        $stmt->execute(['student_id' => $studentId, 'course_ids' => $courseIds]);
        $registrationId = $this->conn->lastInsertId();
        $this->assertNotEmpty($registrationId, "Course selection failed!");

        // Step 3: Validate Fee Calculation
        $stmt = $this->conn->query("SELECT SUM(course_fee) AS total_fee FROM courses WHERE id IN ($courseIds)");
        $feeData = $stmt->fetch(PDO::FETCH_ASSOC);
        $expectedFee = 34.5 + 78.5;
        $this->assertEquals($expectedFee, $feeData['total_fee'], "Incorrect total fee calculation!");

        // Step 4: Process Payment
        $stmt = $this->conn->prepare("UPDATE registrations SET payment_status = 'completed' WHERE id = :registration_id");
        $stmt->execute(['registration_id' => $registrationId]);

        // Step 5: Verify Payment Success
        $stmt = $this->conn->prepare("SELECT payment_status FROM registrations WHERE id = :id");
        $stmt->execute(['id' => $registrationId]);
        $paymentStatus = $stmt->fetchColumn();

        $this->assertEquals('completed', $paymentStatus, "Payment process failed!");
    }
}
?>
