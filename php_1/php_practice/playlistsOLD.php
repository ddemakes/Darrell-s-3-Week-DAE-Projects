<?php
session_start();
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["clinic_id"])) {
    header("Location: login.php");
    exit();
}

$host = "localhost";
$dbname = "LaughMD";
$username = "root";
$password = "root";
$conn = mysqli_connect($host, $username, $password, $dbname);
if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

$user_id = $_SESSION["user_id"];
$survey_check = "SELECT surveyId FROM UserSurvey WHERE userid = '$user_id' ORDER BY id DESC LIMIT 1";
$result = mysqli_query($conn, $survey_check);

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
// Example playlists (based on category)
$category = isset($_GET['category']) ? $_GET['category'] : 'Funny';
$playlists = [
    "Funny Animals" => ["Cats Compilation", "Dog Fails"],
    "Stand-Up Comedy" => ["Kevin Hart", "Ali Wong"],
    "Pranks" => ["Best Pranks 2024", "Office Pranks"],
    "Kids Say Funny Things" => ["Kid Interviews", "Little Comedians"]
];
?>
<!DOCTYPE html>
<html>
<head><title>Playlists - LaughMD</title></head>
<body>
    <h2 style="text-align:center;">Playlists in <?= htmlspecialchars($category) ?></h2>
    <ul style="text-align:center; list-style: none;">
        <?php foreach ($playlists[$category] ?? [] as $playlist): ?>
            <li><a href="videos.php?playlist=<?= urlencode($playlist) ?>"><?= htmlspecialchars($playlist) ?></a></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
