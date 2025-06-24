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

            // Get SurveyID from clinic_survey table
            $clinic_id = $user["clinicID"];
            $survey_sql = "SELECT surveyID FROM clinic_survey WHERE clinicID = '$clinic_id' LIMIT 1";
            $survey_result = mysqli_query($conn, $survey_sql);

            if ($survey_result && mysqli_num_rows($survey_result) === 1) {
                $survey = mysqli_fetch_assoc($survey_result);
                $survey_id = $survey["surveyID"];
                header("Location: survey_$survey_id.php");
                exit();
            } else {
                echo "No survey assigned to this clinic.";
            }
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "Invalid patient ID.";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="POST" action="login.php">
    <label for="patient_id">Patient ID</label>
    <input type="text" name="patient_id" required><br><br>

    <label for="password">Password</label>
    <input type="password" name="password" required><br><br>
    <button type="submit">Login</button>

    </form>
</body>
</html>





