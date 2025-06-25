<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION["user_id"], $_SESSION["clinic_id"])) {
    header("Location: login.php");
    exit();
}

// Database connection
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

// Get the survey assigned to the user's clinic
$survey_sql = "SELECT s.id, s.surveyName 
               FROM Surveys s
               JOIN ClinicSurveys cs ON s.id = cs.surveyId
               WHERE cs.clinicId = ?";
$stmt = mysqli_prepare($conn, $survey_sql);
mysqli_stmt_bind_param($stmt, "i", $clinic_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) === 0) {
    die("No survey found for your clinic.");
}

$survey = mysqli_fetch_assoc($result);
$survey_id = $survey['id'];
$survey_name = $survey['surveyName'];

// Fetch survey questions
$questions_sql = "
    SELECT q.id, q.questionText, q.questionOrder, q.inputTypeName, q.answerCount, q.answerText, sat.inputTypeName AS inputType
    FROM Questions q
    JOIN SurveyAnswerTypes sat ON q.inputTypeName = sat.id
    WHERE q.surveyId = ?
    ORDER BY q.questionOrder ASC
";

$stmt = mysqli_prepare($conn, $questions_sql);
mysqli_stmt_bind_param($stmt, "i", $survey_id);
mysqli_stmt_execute($stmt);
$questions_result = mysqli_stmt_get_result($stmt);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST as $question_id => $user_answer) {
        if (!is_numeric($question_id)) continue; // Skip non-question fields

        $answer_sql = "INSERT INTO SurveyAnswers (surveyid, questionNumber, userAnswer, userid)
                       VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $answer_sql);
        mysqli_stmt_bind_param($stmt, "iisi", $survey_id, $question_id, $user_answer, $user_id);
        mysqli_stmt_execute($stmt);
    }

    // Log the survey completion
    $date = date("Y-m-d");
    $time = date("H:i:s");
    $log_sql = "INSERT INTO UserSurvey (userid, surveyid, date, time) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $log_sql);
    mysqli_stmt_bind_param($stmt, "iiss", $user_id, $survey_id, $date, $time);
    mysqli_stmt_execute($stmt);

    echo "<script>alert('Thank you for completing the survey!'); window.location.href='thankyou.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Survey - <?= htmlspecialchars($survey_name) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header style="text-align:center;">
        <img src="images/logo.png" alt="LaughMD Logo" width="200" />
        <h2><?= htmlspecialchars($survey_name) ?> Survey</h2>
