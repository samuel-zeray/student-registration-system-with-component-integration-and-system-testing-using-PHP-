<?php
use PHPUnit\Framework\TestCase;

class CourseRetrievalTest extends TestCase
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

    public function testFetchCourses(): void
    {
        // Insert sample courses
        $this->conn->query("DELETE FROM courses");
        $this->conn->query("INSERT INTO courses (course_name, course_fee) VALUES ('C++', 34.5), ('Java', 57.7)");

        // Fetch courses
        $stmt = $this->conn->query("SELECT course_name, course_fee FROM courses");
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(2, $courses, "Should return 2 courses");
        $this->assertEquals('C++', $courses[0]['course_name']);
        $this->assertEquals(34.5, $courses[0]['course_fee']);
    }
}
?>