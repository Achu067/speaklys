<?php
session_start();

// Error reporting (for development ONLY - remove in production):
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Database connection (REPLACE with your credentials):
    $db_host = "localhost"; // Your database host (e.g., localhost)
    $db_user = "username"; // Your MySQL username
    $db_pass = "password"; // Your MySQL password
    $db_name = "user"; // Your MySQL database name

    $db = new mysqli($db_host, $db_user, $db_pass, $db_name);

    if ($db->connect_error) {
        die("Database connection failed: " . $db->connect_error);
    }

    // Prepared statement to prevent SQL injection:
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?"); // Replace 'users' and 'username' if needed
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Password verification (using password_verify):
        if (password_verify($password, $row['password'])) { // Replace 'password' if needed
            $_SESSION['user'] = $username;
            $_SESSION['success'] = "Login successful!"; // Optional success message
            header("Location: ../speakly.php"); // Redirect to speakly.php (../ because it's in parent folder)
            exit(); // VERY important: Stop further execution
        } else {
            $_SESSION['error'] = "Incorrect password.";
        }
    } else {
        $_SESSION['error'] = "User not found.";
    }

    $stmt->close();
    $db->close();

    header("Location: ../index.html"); // Redirect back to index.html after error
    exit();
}
?>