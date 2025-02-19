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

    // Check if username already exists (IMPORTANT):
    $check_stmt = $db->prepare("SELECT * FROM users WHERE username = ?"); // Replace 'users' and 'username' if needed
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $_SESSION['error'] = "Username already exists.";
        header("Location: ../index.html"); // Redirect back to index.html
        exit();
    }

    // Hash the password (CRUCIAL - never store plain text passwords):
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the user into the database:
    $insert_stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)"); // Replace 'users', 'username', and 'password' if needed
    $insert_stmt->bind_param("ss", $username, $hashed_password); // "ss" for two strings
    
    if ($insert_stmt->execute()) {
        $_SESSION['user'] = $username; // Store username in session
        $_SESSION['success'] = "Registration successful!";
        header("Location: ../speakly.php"); // Redirect to speakly.php
        exit();
    } else {
        $_SESSION['error'] = "Registration failed. Please try again.";
        header("Location: ../index.html"); // Redirect back to index.html
        exit();
    }

    $insert_stmt->close();
    $db->close();
}
?>