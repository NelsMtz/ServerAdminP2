<?php
// Database connection for the CAT 2 project
$db_host = "localhost";
$db_user = "cat2user";
$db_pass = "StrongPass123!";
$db_name = "CAT2";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
