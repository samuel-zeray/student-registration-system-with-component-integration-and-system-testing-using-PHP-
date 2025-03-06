<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $selectedCourses = implode(',', $_POST['courses']);

    try {
        $checkQuery = "SELECT COUNT(*) FROM students WHERE email = :email";
        $stmt = $conn->prepare($checkQuery);
        $stmt->execute(['email' => $email]);
        $emailExists = $stmt->fetchColumn();

        if ($emailExists > 0) {
            echo "<script>
                alert('This email is already registered. Please use a different email.');
                window.location.href = 'index.html';
            </script>";
            exit;
        }

        $studentQuery = "INSERT INTO students (name, email, phone) VALUES (:name, :email, :phone)";
        $stmt = $conn->prepare($studentQuery);
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'phone' => $phone
        ]);

        $studentId = $conn->lastInsertId();

        $registrationQuery = "INSERT INTO registrations (student_id, course_ids) VALUES (:student_id, :course_ids)";
        $stmt = $conn->prepare($registrationQuery);
        $stmt->execute([
            'student_id' => $studentId,
            'course_ids' => $selectedCourses
        ]);

        $registrationId = $conn->lastInsertId();

        echo "<script>
            alert('Registration successful! Your Registration ID is: $registrationId');
            window.location.href = 'payment.html';
        </script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
