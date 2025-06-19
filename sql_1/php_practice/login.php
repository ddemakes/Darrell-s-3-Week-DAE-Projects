<?php
// login.php

// Database connection
$host = "localhost";
$dbname = "your_database_name";
$username = "your_db_username";
$password = "your_db_password";
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_id = trim($_POST["patient_id"]);
    $input_password = trim($_POST["password"]);
    
    // Validate inputs
    if (empty($patient_id) || empty($input_password)) {
        echo "Patient ID and Password are required.";
    } else {
        // Look up the patient in the database
        $stmt = $conn->prepare("SELECT password FROM patients WHERE patient_id = ?");
        $stmt->bind_param("s", $patient_id);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($hashed_password);
            $stmt->fetch();
            
            // Verify password
            if (password_verify($input_password, $hashed_password)) {
                echo "Login successful. Welcome, Patient ID: $patient_id!";
                // You can start a session here if needed
            } else {
                echo "Incorrect password.";
            }
        } else {
            echo "Patient ID not found.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

