// ─────────────────────────────────────────────────────────────
// File: record_user_survey.php - Log that a user took a survey
// ─────────────────────────────────────────────────────────────
<?php
require_once 'db.php';

function recordUserSurvey($userId, $surveyId) {
    global $conn;
    $date = date('Y-m-d');
    $time = date('H:i:s');
    $sql = "INSERT INTO UserSurvey (userid, surveyid, date, time) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'iiss', $userId, $surveyId, $date, $time);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $success;
}
?>