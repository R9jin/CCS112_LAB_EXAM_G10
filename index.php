<?php
session_start();

// Connect to DB
$conn = new mysqli("db", "root", "rootpassword", "librarydb", 3306);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Initialize error
$error = "";

// Only process login if form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? $conn->real_escape_string($_POST['username']) : '';
    $password = isset($_POST['password']) ? $conn->real_escape_string($_POST['password']) : '';

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user'] = $user['username'];
        if ($user['role'] === 'admin') {
            header("Location: librarian.php");
        } else {
            header("Location: user.php");
        }
        exit;
    } else {
        $error = "Invalid login credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="library-container">
    <h1>WELCOME TO THE LIBRARY SYSTEM</h1>
    <br>
    <?php if ($error): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="" method="post">
        <label for="username">USER NAME:</label>
        <input type="text" name="username" required><br><br>

        <label for="password">PASSWORD:</label>
        <input type="password" name="password" required><br><br>

        <input type="submit" value="Sign-In">
    </form>
</div>

</body>
</html>
