<?php
// Start a session to store login data (like who is logged in)
session_start();

// Initialize error message (in case login fails)
$error = "";

// Check if the request method is POST (form was submitted)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check credentials for normal user
    if ($username == "user" && $password == "123") {
        // Store session variable for logged-in user
        $_SESSION["user"] = $username;

        // Redirect to user dashboard
        header("Location: user.php");
        exit;
    }
    // Check credentials for librarian
    else if ($username == "librarian" && $password == "1234") {
        $_SESSION["user"] = $username;

        // Redirect to librarian dashboard
        header("Location: librarian.php");
        exit;
    }
    // If no match, login fails (you could add error handling here)
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Login</title>
    <!-- External stylesheet -->
    <link rel="stylesheet" href="staile.css">
</head>
<body>

    <!-- Main heading -->
    <h1>WELCOME TO THE LIBRARY SYSTEM</h1>

    <!-- Login form -->
    <form action="" method="post">
        <label for="username">USER NAME:</label>
        <input type="text" name="username" required><br><br>

        <label for="password">PASSWORD:</label>
        <input type="password" name="password" required>

        <!-- Submit button -->
        <input type="submit" value="Sign-In">
    </form>

</body>
</html>
