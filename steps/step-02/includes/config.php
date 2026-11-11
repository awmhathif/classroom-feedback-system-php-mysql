<?php
session_start();

// Settings you can change
define("APP_NAME", "Classroom Feedback");
define("TEACHER_CODE", "SCHOOL2026");   // New teachers must type this to sign up
define("MIN_RESPONSES", 4);             // Fewer answers than this and results stay hidden
define("MAX_QUESTIONS", 12);
define("MAX_DAYS", 60);                 // A round can stay open this many days at most

// The questions every new round starts with
define("READY_QUESTIONS", [
    ["The lessons were clear and easy to follow.", "rating"],
    ["The teacher explained difficult ideas well.", "rating"],
    ["The pace of the class was right for me.", "rating"],
    ["I felt comfortable asking questions.", "rating"],
    ["The class materials helped me learn.", "rating"],
    ["What worked well?", "comment"],
    ["What should change?", "comment"],
]);

// Connect to MySQL (XAMPP: user root, no password)
$conn = new mysqli("localhost", "root", "", "feedback_db");
$conn->set_charset("utf8mb4");

require __DIR__ . "/functions.php";
