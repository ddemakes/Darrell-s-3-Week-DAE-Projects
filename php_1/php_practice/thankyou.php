<?php
session_start();

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["clinic_id"])) {
    header("Location: login.php");
    exit();
}

// DB connection
$host = "localhost";
$dbname = "LaughMD";
$username = "root";
$password = "root";
$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION["user_id"];
$clinic_id = $_SESSION["clinic_id"];

// Get the user's assigned survey
$survey_sql = "SELECT s.id AS survey_id, s.surveyName
               FROM Surveys s
               JOIN Clinic_Survey cs ON cs.surveyId = s.id
               WHERE cs.clinicId = $clinic_id
               LIMIT 1";
$survey_result = mysqli_query($conn, $survey_sql);
$survey = mysqli_fetch_assoc($survey_result);

$survey_id = $survey['survey_id'];
$survey_name = $survey['surveyName'];

// Count expected questions for this survey
$q_sql = "SELECT COUNT(*) AS total FROM Questions WHERE surveyId = $survey_id";
$q_result = mysqli_query($conn, $q_sql);
$q_data = mysqli_fetch_assoc($q_result);
$total_questions = $q_data['total'];

// Count user's actual answers
$a_sql = "SELECT COUNT(DISTINCT questionNumber) AS answered
          FROM SurveyAnswers
          WHERE surveyid = $survey_id AND userid = $user_id";
$a_result = mysqli_query($conn, $a_sql);
$a_data = mysqli_fetch_assoc($a_result);
$answered = $a_data['answered'];

$completed = ($answered == $total_questions);

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Thank You - LaughMD</title>
  <link rel="stylesheet" href="style.css" />
  <?php if ($completed): ?>
    <meta http-equiv="refresh" content="5;url=categories.php" />
  <?php endif; ?>
  <style>
    body {
      text-align: center;
      padding: 2rem;
      font-family: Arial, sans-serif;
    }
    img {
      margin-bottom: 1rem;
    }
    h2 {
      color: #0a6d19;
    }
  </style>
</head>
<body>
  <header>
    <img src="images/logo.png" alt="LaughMD Logo" width="200" />
  </header>
  <main>
    <?php if ($completed): ?>
      <h2>Thank you for completing the survey!</h2>
      <p>You will be redirected shortly...</p>
    <?php else: ?>
      <h2>Survey Incomplete</h2>
      <p>It looks like you didn’t answer all questions. Please return and complete the survey.</p>
      <a href="survey.php"><button>Return to Survey</button></a>
    <?php endif; ?>
  </main>
</body>
</html>
