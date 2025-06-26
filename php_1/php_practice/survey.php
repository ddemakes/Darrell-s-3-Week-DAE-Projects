<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["clinic_id"])) {
    header("Location: login.php");
    exit();
}

// ─── Database Connection ───────────────────────────────────────────────
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

// ─── Fetch Survey Info for User's Clinic ───────────────────────────────
$clinic_id_safe = intval($clinic_id);
$survey_query = "
    SELECT s.id, s.surveyName
    FROM Surveys s
    JOIN Clinic_Survey cs ON cs.surveyId = s.id
    WHERE cs.clinicId = $clinic_id_safe
    LIMIT 1
";

$survey_result = mysqli_query($conn, $survey_query);
if (!$survey_result || mysqli_num_rows($survey_result) === 0) {
    echo "No survey assigned to your clinic.";
    mysqli_close($conn);
    exit();
}

$survey = mysqli_fetch_assoc($survey_result);
$survey_id = $survey["id"];
$survey_name = $survey["surveyName"];

// ─── Handle Form Submission ────────────────────────────────────────────
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    foreach ($_POST as $question_id => $user_answer) {
        if (!is_numeric($question_id)) {
            continue;
        }

        $question_id_safe = intval($question_id);
        $user_answer_safe = mysqli_real_escape_string($conn, trim($user_answer));
        $survey_id_safe = intval($survey_id);
        $user_id_safe = intval($user_id);

        $insert_answer_sql = "
            INSERT INTO SurveyAnswers (surveyid, questionNumber, userAnswer, userid)
            VALUES ('$survey_id_safe', '$question_id_safe', '$user_answer_safe', '$user_id_safe')
        ";
        mysqli_query($conn, $insert_answer_sql);
    }

    // Record submission in UserSurvey
    $date = date("Y-m-d");
    $time = date("H:i:s");

    $log_sql = "
        INSERT INTO UserSurvey (userid, surveyid, date, time)
        VALUES ('$user_id_safe', '$survey_id', '$date', '$time')
    ";
    mysqli_query($conn, $log_sql);

    // header echo "<script>alert('Thank you for completing the survey!'); window.location.href='thankyou.php';</script>";
    // mysqli_close($conn);

    header("Location: thankyou.php");
    mysqli_close($conn);
    exit();
}

// ─── Fetch Survey Questions ───────────────────────────────────────────
$questions_sql = "
    SELECT q.id, q.questionText, q.questionOrder, q.inputTypeName, q.answerCount, q.answerText,
           sat.inputTypeName AS inputType
    FROM Questions q
    JOIN SurveyAnswerTypes sat ON q.inputTypeName = sat.id
    WHERE q.surveyId = $survey_id
    ORDER BY q.questionOrder ASC
";

$questions_result = mysqli_query($conn, $questions_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?php echo htmlspecialchars($survey_name); ?> - LaughMD Survey</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <header style="text-align: center;">
    <img src="images/logo.png" alt="LaughMD Logo" width="250" />
   <!-- <h2><?php echo htmlspecialchars($survey_name); ?> Survey</h2> -->
  </header>

  <main style="max-width: 700px; margin: auto;">
    <form method="POST" action="survey.php">
      <?php while ($q = mysqli_fetch_assoc($questions_result)) { ?>
        <div class="survey-question" style="margin-bottom: 2rem;">
          <label style="display: block; text-align: center;">
            <strong><?php echo htmlspecialchars($q['questionText']); ?></strong>
          </label>

          <?php
            $inputType = strtolower($q['inputType']);
            $name = $q['id'];
            $answers = array_map('trim', explode(',', $q['answerText']));

            if ($inputType === 'dropdown') {
                echo "<select name=\"$name\" required>";
                echo "<option value=\"\">-- Select --</option>";
                foreach ($answers as $option) {
                    $opt = htmlspecialchars($option);
                    echo "<option value=\"$opt\">$opt</option>";
                }
                echo "</select>";
            } elseif ($inputType === 'textfield') {
                echo "<input type=\"text\" name=\"$name\" required />";
            } elseif ($inputType === 'textarea') {
                echo "<textarea name=\"$name\" rows=\"4\" required></textarea>";
            } elseif ($inputType === 'radiobutton' || $inputType === 'multiplechoice') {
                foreach ($answers as $option) {
                    $opt = htmlspecialchars($option);
                    echo "<label><input type=\"radio\" name=\"$name\" value=\"$opt\" required> $opt</label><br />";
                }
            } elseif ($inputType === 'slider') {
                echo "<input type=\"range\" name=\"$name\" min=\"1\" max=\"5\" required />";
            } else {
                echo "<input type=\"text\" name=\"$name\" required />";
            }
          ?>
        </div>
      <?php } ?>
      <div style="text-align: center;">
        <button type="submit">Submit Survey</button>
      </div>
    </form>
  </main>

  <footer style="text-align: center; margin-top: 3rem;">
    <p>&copy; 2025 LaughMD</p>
  </footer>
</body>
</html>

<?php mysqli_close($conn); ?>
