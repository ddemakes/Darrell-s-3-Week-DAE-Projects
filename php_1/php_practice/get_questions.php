// ─────────────────────────────────────────────────────────────
// File: get_questions.php - Get questions for a survey
// ─────────────────────────────────────────────────────────────
<?php
require_once 'db.php';

function getQuestionsBySurvey($surveyId) {
    global $conn;
    $questions = [];
    $sql = "SELECT id, questionText, questionOrder, inputTypeName, answerText FROM Questions WHERE surveyId = ? ORDER BY questionOrder ASC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $surveyId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $questions[] = $row;
    }
    mysqli_stmt_close($stmt);
    return $questions;
}
?>