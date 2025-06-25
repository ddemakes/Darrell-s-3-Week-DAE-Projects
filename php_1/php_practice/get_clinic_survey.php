// ─────────────────────────────────────────────────────────────
// File: get_clinic_survey.php - Get assigned survey for a clinic
// ─────────────────────────────────────────────────────────────
<?php
require_once 'db.php';

function getSurveyIdByClinic($clinicId) {
    global $conn;
    $sql = "SELECT surveyId FROM ClinicSurveys WHERE clinicId = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $clinicId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $surveyId);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return $surveyId ?? null;
}
?>
