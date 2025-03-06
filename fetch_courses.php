<?php
require 'config.php';

$query = "SELECT id, course_name, course_fee FROM courses";
$stmt = $conn->query($query);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($courses);
?>
