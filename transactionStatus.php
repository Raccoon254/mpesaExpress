<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION["phone"])) {
    echo json_encode(["error" => "Phone number not found in session."]);
    exit;
}

$phone = $_SESSION["phone"];

$mysqli = new mysqli("localhost", "root", "pass", "db");
if ($mysqli->connect_errno) {
    echo json_encode(["error" => "Failed to connect to MySQL: " . $mysqli->connect_error]);
    exit;
}

$query = "SELECT * FROM orders WHERE Phone = '$phone' ORDER BY id DESC LIMIT 1";
$result = $mysqli->query($query);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo json_encode(["error" => "No matching transaction found."]);
}

$mysqli->close();
?>