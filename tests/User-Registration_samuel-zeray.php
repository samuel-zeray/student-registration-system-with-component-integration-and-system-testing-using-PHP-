<?php
use PHPUnit\Framework\TestCase;

class RegistrationTest extends TestCase
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

    public function testUniqueEmailValidation(): void
    {
        // Sample data
        $name = "John Doe";
        $email = "johndoe@example.com";
        $phone = "1234567890";

        // Insert a student to simulate an existing email
        $this->conn->query("DELETE FROM students WHERE email = '$email'");
        $stmt = $this->conn->prepare("INSERT INTO students (name, email, phone) VALUES (:name, :email, :phone)");
        $stmt->execute(['name' => $name, 'email' => $email, 'phone' => $phone]);

        // Attempt to insert the same email
        $stmt = $this->conn->prepare("INSERT INTO students (name, email, phone) VALUES (:name, :email, :phone)");
        $this->expectException(PDOException::class);
        $stmt->execute(['name' => $name, 'email' => $email, 'phone' => $phone]);
    }
}
?>