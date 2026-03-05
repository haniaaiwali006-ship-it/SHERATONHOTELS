<?php
$host = "localhost";
$dbname = "rsoa_rsoa278_16";
$username = "rsoa_rsoa278_16";
$password = "123456";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
