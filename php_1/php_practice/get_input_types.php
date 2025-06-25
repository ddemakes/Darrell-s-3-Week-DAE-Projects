// ─────────────────────────────────────────────────────────────
// File: get_input_types.php - Retrieve input types (if needed)
// ─────────────────────────────────────────────────────────────
<?php
require_once 'db.php';

function getInputTypes() {
    global $conn;
    $types = [];
    $sql = "SELECT id, inputTypeName FROM SurveyAnswerTypes";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $types[] = $row;
    }
    return $types;
}
?>