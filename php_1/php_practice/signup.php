<?php
// Database connection (procedural style)
$host = "localhost";
$dbname = "LaughMD";
$username = "root";
$password = "root";

$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get clinics from the database to populate dropdown, alphabetized by clinicname
$clinics = [];
$clinic_query = "SELECT id, clinicname FROM Clinics ORDER BY clinicname ASC";
$clinic_result = mysqli_query($conn, $clinic_query);
if ($clinic_result && mysqli_num_rows($clinic_result) > 0) {
    while ($row = mysqli_fetch_assoc($clinic_result)) {
        $clinics[] = $row;
    }
}

// Message to display after submission
$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_id = isset($_POST["patient_id"]) ? mysqli_real_escape_string($conn, trim($_POST["patient_id"])) : "";
    $password = isset($_POST["password"]) ? mysqli_real_escape_string($conn, trim($_POST["password"])) : "";
    $confirm_password = isset($_POST["confirm_password"]) ? mysqli_real_escape_string($conn, trim($_POST["confirm_password"])) : "";
    $clinic_id = isset($_POST["clinic_id"]) ? mysqli_real_escape_string($conn, trim($_POST["clinic_id"])) : "";

    // Capture current date and time
    $signup_date = date("Y-m-d");
    $signup_time = date("H:i:s");

    if (empty($patient_id) || empty($password) || empty($confirm_password) || empty($clinic_id)) {
        $message = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        // Check if patient ID already exists
        $check_sql = "SELECT id FROM User WHERE patientid = '$patient_id'";
        $result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($result) > 0) {
            $message = "Patient ID already exists. Choose another.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert into User table with signup date and time
            $insert_sql = "INSERT INTO User (patientid, hashedPassword, clinicID, signup_date, signup_time) 
                           VALUES ('$patient_id', '$hashed_password', '$clinic_id', '$signup_date', '$signup_time')";
            
            if (mysqli_query($conn, $insert_sql)) {
                $message = "Signup successful! Redirecting to login page...";
                echo '<script>
                    setTimeout(function() {
                        window.location.href = "login.php";
                    }, 3000);
                </script>';
            }
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>LaughMD</title>
  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <header>
    <div style="text-align: center;">
      <img src="images/logo.png" alt="LaughMD Logo" width="250" />
    </div>
  </header>

  <nav>
    <!-- Navigation can be added here -->
  </nav>

  <main>
    <section>
      <div style="text-align: center;" id="heading">
        <h2>Patient Signup</h2>

        <!-- Display PHP message -->
        <?php if (!empty($message)) : ?>
          <p style="color: red;"><strong><?php echo $message; ?></strong></p>
        <?php endif; ?>

        <form method="POST" action="signup.php">
          <label for="patient_id">Patient ID</label>
          <input type="text" name="patient_id" required><br><br>

          <label for="password">Password</label>
          <input type="password" name="password" required><br><br>

          <label for="confirm_password">Confirm Password</label>
          <input type="password" name="confirm_password" required><br><br>

          <label for="clinic_id">Select Clinic</label>
          <select name="clinic_id" required>
            <option value="">-- Choose a Clinic --</option>
            <?php foreach ($clinics as $clinic): ?>
              <option value="<?php echo $clinic['id']; ?>"><?php echo htmlspecialchars($clinic['clinicname']); ?></option>
            <?php endforeach; ?>
          </select><br><br>

          <button type="submit">Submit</button>
        </form>
      </div>
    </section>
  </main>

  <footer>
    <div style="text-align: center;">
      <p>&copy; 2025 LaughMD</p>
    </div>
  </footer>
</body>
</html>
