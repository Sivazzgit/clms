<?php
$servername = getenv('MYSQL_HOST') ?: "localhost";
$username = getenv('MYSQL_USER') ?: "kancor";
$password = getenv('MYSQL_PASSWORD') ?: "kancor123";
$dbname = getenv('MYSQL_DATABASE') ?: "kancor";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  echo "Connected successfully Kancor";
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
?>