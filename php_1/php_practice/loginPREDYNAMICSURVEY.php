<?php
session_start();

// Database connection
$host = "localhost";
$dbname = "LaughMD";
$username = "root";
$password = "root";

$conn = mysqli_connect($host, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_id = mysqli_real_escape_string($conn, $_POST["patient_id"]);
    $password_input = mysqli_real_escape_string($conn, $_POST["password"]);

    $sql = "SELECT id, hashedPassword, clinicID FROM User WHERE patientid = '$patient_id'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password_input, $user["hashedPassword"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["clinic_id"] = $user["clinicID"];

            // Get surveyID based on the clinicID
            $clinic_id = $user["clinicID"];
            $survey_sql = "SELECT surveyID FROM clinic_survey WHERE clinicID = '$clinic_id' LIMIT 1";
            $survey_result = mysqli_query($conn, $survey_sql);

            if ($survey_result && mysqli_num_rows($survey_result) === 1) {
                $survey = mysqli_fetch_assoc($survey_result);
                $survey_id = $survey["surveyID"];
                header("Location: survey_$survey_id.php");
                exit();
            } else {
                $message = "No survey assigned to this clinic.";
            }
        } else {
            $message = "Invalid password.";
        }
    } else {
        $message = "Invalid patient ID.";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>LaughMD Login</title>
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
          <h3>
            Please sign in and let us know how you are feeling today<br>
            We are here to help you with your mental health needs
          </h3>
        </div>

        <div class="login-container">
          <?php if (!empty($message)) : ?>
            <p style="color: red; text-align: center;"><strong><?php echo $message; ?></strong></p>
          <?php endif; ?>

          <form method="POST" action="login.php">
            <label for="patient_id">Patient ID</label>
            <input type="text" name="patient_id" required>

            <label for="password">Password</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
          </form>

          <p class="signup-link" style="text-align: center; margin-top: 1rem;">
            Don't have an account? <a href="signup.php">Sign up</a>
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
