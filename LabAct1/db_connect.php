
<?php
$host = "db";       
$user = "root";   
$pass = "rootpassword";      
$db   = "task_db";

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
?>
