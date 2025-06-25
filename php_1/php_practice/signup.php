<?php
// ─── DB Connection ─────────────────────────────────────────────────────────────
$host = 'localhost';
$dbname = 'LaughMD';
$username = 'root';
$password = 'root';
$conn = mysqli_connect($host, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// ─── Custom Functions ──────────────────────────────────────────────────────────
require_once 'functions.php';

// ─── Retrieve Clinics ──────────────────────────────────────────────────────────
$clinics = getClinics($conn);

// ─── Initialize Message ────────────────────────────────────────────────────────
$message = "";

// ─── Handle Form Submission ────────────────────────────────────────────────────
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_id = mysqli_real_escape_string($conn, trim($_POST["patient_id"] ?? ''));
    $password = mysqli_real_escape_string($conn, trim($_POST["password"] ?? ''));
    $confirm_password = mysqli_real_escape_string($conn, trim($_POST["confirm_password"] ?? ''));
    $clinic_id = mysqli_real_escape_string($conn, trim($_POST["clinic_id"] ?? ''));

    $signup_date = date("Y-m-d");
    $signup_time = date("H:i:s");

    if (empty($patient_id) || empty($password) || empty($confirm_password) || empty($clinic_id)) {
        $message = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        $check_sql = "SELECT id FROM User WHERE patientid = '$patient_id'";
        $result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($result) > 0) {
            $message = "Patient ID already exists. Choose another.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_sql = "INSERT INTO User (patientid, hashedPassword, clinicID, signup_date, signup_time) 
                           VALUES ('$patient_id', '$hashed_password', '$clinic_id', '$signup_date', '$signup_time')";
            if (mysqli_query($conn, $insert_sql)) {
                $message = "Signup successful! Redirecting to login page...";
                echo '<script>
                    setTimeout(function() {
                        window.location.href = "login.php";
                    }, 3000);
                </script>';
            } else {
                $message = "Error: " . mysqli_error($conn);
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
  <title>LaughMD - Signup</title>
  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <header>
    <div style="text-align: center;">
      <img src="images/logo.png" alt="LaughMD Logo" width="250" />
    </div>
  </header>

  <main>
    <section>
      <div class="login-page-wrapper">
        <div id="heading">
          <h2>Patient Signup</h2>
        </div>

        <?php if (!empty($message)) : ?>
          <p style="text-align: center; color: red;"><strong><?php echo $message; ?></strong></p>
        <?php endif; ?>

        <div class="login-container">
          <form method="POST" action="signup.php">
            <label for="patient_id">Patient ID</label>
            <input type="text" name="patient_id" required>

            <label for="password">Password</label>
            <input type="password" name="password" required>

            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" required>

            <label for="clinic_id">Select Clinic</label>
            <select name="clinic_id" required>
           
              <?= renderClinicOptions($clinics); ?>
            </select>

            <button type="submit">Submit</button>
          </form>

          <p class="login-link" style="text-align: center; margin-top: 1rem;">
            Already have an account? <a href="login.php">Log In</a>
          </p>
        </div>
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
