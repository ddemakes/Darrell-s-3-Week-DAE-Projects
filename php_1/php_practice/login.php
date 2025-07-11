<?php
session_start();

// Database connection
require_once 'db.php'; // assumes you have db.php from earlier

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $patient_id = trim($_POST["patient_id"]);
    $password_input = trim($_POST["password"]);

    // Get user by patientid
    $sql = "SELECT id, hashedPassword, clinicID FROM User WHERE patientid = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $patient_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password_input, $user["hashedPassword"])) {
            // Store user data in session
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["clinic_id"] = $user["clinicID"];

            // Fetch assigned survey for the user's clinic
            $survey_sql = "SELECT surveyID FROM clinic_survey WHERE clinicID = ? LIMIT 1";
            $survey_stmt = mysqli_prepare($conn, $survey_sql);
            mysqli_stmt_bind_param($survey_stmt, "i", $user["clinicID"]);
            mysqli_stmt_execute($survey_stmt);
            $survey_result = mysqli_stmt_get_result($survey_stmt);

            if ($survey_result && mysqli_num_rows($survey_result) === 1) {
                $survey = mysqli_fetch_assoc($survey_result);
                $_SESSION["survey_id"] = $survey["surveyID"]; // store survey ID for use in survey.php

                header("Location: survey.php");
                exit();
            } else {
                $message = "No survey assigned to your clinic.";
            }
        } else {
            $message = "Incorrect password.";
        }
    } else {
        $message = "Invalid patient ID.";
    }

    mysqli_stmt_close($stmt);
    if (isset($survey_stmt)) mysqli_stmt_close($survey_stmt);
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
            <input type="text" name="patient_id" required />

            <label for="password">Password</label>
            <input type="password" name="password" required />

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
