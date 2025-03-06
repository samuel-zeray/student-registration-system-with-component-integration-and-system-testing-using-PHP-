<?php
use PHPUnit\Framework\TestCase;

class StudentRegistrationIntegrationTest extends TestCase
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

    public function testStudentRegistrationAndPayment(): void
    {
        // Step 1: Register a new student
        $name = "Alice Johnson";
        $email = "alice@example.com";
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
        $expectedFee = 34.5 + 78.5; // C++ + AI
        $this->assertEquals($expectedFee, $feeData['total_fee'], "Fee calculation incorrect!");

        // Step 4: Simulate Payment Processing
        $paymentStatus = "completed";
        $stmt = $this->conn->prepare("UPDATE registrations SET payment_status = :payment_status WHERE id = :registration_id");
        $stmt->execute(['payment_status' => $paymentStatus, 'registration_id' => $registrationId]);

        // Verify payment status
        $stmt = $this->conn->prepare("SELECT payment_status FROM registrations WHERE id = :id");
        $stmt->execute(['id' => $registrationId]);
        $result = $stmt->fetchColumn();

        $this->assertEquals('completed', $result, "Payment process failed!");
    }
}
?>