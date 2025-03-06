<?php
use PHPUnit\Framework\TestCase;

class FeeCalculationTest extends TestCase
{
    public function testCalculateTotalFee(): void
    {
        // Simulate course fees
        $courseFees = [
            1 => 34.5,  // C++
            2 => 57.7,  // Java
            3 => 78.5   // AI
        ];

        // Selected courses
        $selectedCourses = [1, 3]; // C++ and AI
        $totalFee = array_sum(array_intersect_key($courseFees, array_flip($selectedCourses)));

        $this->assertEquals(113.0, $totalFee, "Total fee should be 113.0");
    }
}
?>