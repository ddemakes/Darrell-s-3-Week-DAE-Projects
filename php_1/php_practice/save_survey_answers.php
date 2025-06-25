// ─────────────────────────────────────────────────────────────
// File: save_survey_answers.php - Save user answers
// ─────────────────────────────────────────────────────────────
<?php
require_once 'db.php';

function saveSurveyAnswer($surveyId, $questionNumber, $userAnswer, $userId) {
    global $conn;
    $sql = "INSERT INTO SurveyAnswers (surveyid, questionNumber, userAnswer, userid) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'iisi', $surveyId, $questionNumber, $userAnswer, $userId);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $success;
}
?>