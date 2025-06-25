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

if ($result && mysqli_num_rows($result) > 0) {
    $survey_id = mysqli_fetch_assoc($result)['surveyId'];
    $total_q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM Questions WHERE surveyId = '$survey_id'"))['total'];
    $total_a = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS answered FROM SurveyAnswers WHERE surveyid = '$survey_id' AND userid = '$user_id'"))['answered'];

    if ($total_q != $total_a) {
        echo "<h2 style='text-align:center;color:red;'>Please complete the full survey before accessing this content.</h2>";
        exit();
    }
} else {
    echo "<h2 style='text-align:center;color:red;'>No survey submission found.</h2>";
    exit();
}

// Display categories
$categories = ["Funny Animals", "Stand-Up Comedy", "Pranks", "Kids Say Funny Things"];
?>
<!DOCTYPE html>
<html>
<head><title>Categories - LaughMD</title></head>
<body>
    <h2 style="text-align:center;">Choose a Humor Category</h2>
    <ul style="text-align:center; list-style: none;">
        <?php foreach ($categories as $cat): ?>
            <li><a href="playlists.php?category=<?= urlencode($cat) ?>"><?= htmlspecialchars($cat) ?></a></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
