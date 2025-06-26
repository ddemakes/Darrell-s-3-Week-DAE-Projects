<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ─── Check if User is Logged In ─────────────────────────────
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["clinic_id"])) {
    header("Location: login.php");
    exit();
}

// ─── Database Connection ───────────────────────────────────
$host = "localhost";
$dbname = "LaughMD";
$username = "root";
$password = "root";

$conn = mysqli_connect($host, $username, $password, $dbname);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$user_id = intval($_SESSION["user_id"]);
$clinic_id = intval($_SESSION["clinic_id"]);

// ─── Fetch the Survey for this Clinic ──────────────────────
$survey_sql = "
    SELECT s.id AS survey_id, s.numberofquestions
    FROM Surveys s
    JOIN Clinic_Survey cs ON cs.surveyId = s.id
    WHERE cs.clinicId = $clinic_id
    LIMIT 1
";
$survey_result = mysqli_query($conn, $survey_sql);

if (!$survey_result || mysqli_num_rows($survey_result) === 0) {
    echo "No survey assigned to your clinic.";
    mysqli_close($conn);
    exit();
}

$survey = mysqli_fetch_assoc($survey_result);
$survey_id = intval($survey['survey_id']);
$required_questions = intval($survey['numberofquestions']);

// ─── Check if User Answered All Required Questions ─────────
$answer_check_sql = "
    SELECT COUNT(*) AS answered
    FROM SurveyAnswers
    WHERE userid = $user_id AND surveyid = $survey_id
";
$answer_result = mysqli_query($conn, $answer_check_sql);
$answer_data = mysqli_fetch_assoc($answer_result);
$answered = intval($answer_data['answered']);
// echo $answered . "<br>";
// echo $required_questions;
// exit();

if ($answered < $required_questions) {
    echo "<p style='text-align:center; color:red; font-weight:bold;'>Please complete the full survey before accessing this content.</p>";
    mysqli_close($conn);
    exit();
}

// ─── Define Static Categories (You can later pull from DB) ─
$categories = [
 
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Video Categories - LaughMD</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      padding: 2rem;
    }

    h2 {
  background-color:rgb(255, 255, 255);
  color: #0a6d19;
    }

    ul {
      list-style: none;
      padding: 0;
      margin: 2rem auto;
      max-width: 500px;
      color: #0a6d19;
    }

    li {
      background-color: #f0f0f0;
      margin: 0.5rem 0;
      padding: 1rem;
      border-radius: 6px;
      font-size: 1.2rem;
      color: #0a6d19;
    }
  </style>
</head>
<body>
  <header>
    <img src="images/logo.png" alt="LaughMD Logo" width="250" />
    <h2>Select a Playlist</h2>
  </header>

  <main>
    <ul>
        <li><a href="videos.php">Seinfeld</a></li>
        <li><a href="videos.php">Abbott Elementary</a></li>
        <li><a href="videos.php">Friends</a></li>
        <li><a href="videos.php">30 Rock</a></li>
        <li><a href="videos.php">The Office</a></li>
  
    </ul>
  </main>

  <footer>
    <p>&copy; 2025 LaughMD</p>
  </footer>
</body>
</html>

<?php mysqli_close($conn); ?>
