<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["clinic_id"])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "root", "LaughMD");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = intval($_SESSION["user_id"]);
$clinic_id = intval($_SESSION["clinic_id"]);

// Survey check
$survey_sql = "
    SELECT s.id AS survey_id, s.numberofquestions
    FROM Surveys s
    JOIN Clinic_Survey cs ON cs.surveyId = s.id
    WHERE cs.clinicId = $clinic_id
    LIMIT 1
";
$survey_result = mysqli_query($conn, $survey_sql);
if (!$survey_result || mysqli_num_rows($survey_result) === 0) {
    echo "No survey assigned to your clinic.";
    mysqli_close($conn);
    exit();
}

$survey = mysqli_fetch_assoc($survey_result);
$survey_id = $survey['survey_id'];
$required_questions = $survey['numberofquestions'];

$answer_check_sql = "
    SELECT COUNT(*) AS answered
    FROM SurveyAnswers
    WHERE userid = $user_id AND surveyid = $survey_id
";
$answer_result = mysqli_query($conn, $answer_check_sql);
$answered = mysqli_fetch_assoc($answer_result)['answered'];

if ($answered < $required_questions) {
    echo "<p style='text-align:center; color:red; font-weight:bold;'>Please complete the full survey before accessing this content.</p>";
    mysqli_close($conn);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>LaughMD Playlist</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      background-color: #f9f9f9;
      padding: 2rem;
    }
    .video-grid {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 2rem;
      margin-top: 2rem;
    }
    .video-item {
      width: 300px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      overflow: hidden;
      text-decoration: none;
      color: #000;
    }
    .video-item img {
      width: 100%;
    }
    .video-title {
      padding: 1rem;
      font-size: 1rem;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <header>
    <img src="images/logo.png" alt="LaughMD Logo" width="250">
    <h2>Select a Video</h2>
  </header>

  <main>
    <div class="video-grid" id="video-list"></div>
  </main>

  <footer style="margin-top: 3rem;">
    <p>&copy; 2025 LaughMD</p>
  </footer>

  <script>
    const apiKey = "AIzaSyD-dTYeJsybZV88tAjJPNILyyLh470hMN8";
    const playlistId = "PL8CncUxRsuLCdrJho-_dbJkkQt_hahT8-";
    const maxResults = 10;

    async function fetchPlaylistVideos() {
      const apiUrl = `https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=${maxResults}&playlistId=${playlistId}&key=${apiKey}`;

      try {
        const response = await fetch(apiUrl);
        const data = await response.json();
        displayVideos(data.items);
      } catch (error) {
        console.error("Error fetching playlist:", error);
      }
    }

    function displayVideos(videos) {
      const container = document.getElementById("video-list");

      videos.forEach(video => {
        const videoId = video.snippet.resourceId.videoId;
        const title = video.snippet.title;
        const thumbnail = video.snippet.thumbnails.high.url;

        const anchor = document.createElement("a");
        anchor.href = `videos.php?vid=${videoId}`;
        anchor.className = "video-item";

        anchor.innerHTML = `
          <img src="${thumbnail}" alt="${title} Thumbnail">
          <div class="video-title">${title}</div>
        `;

        container.appendChild(anchor);
      });
    }

    fetchPlaylistVideos();
  </script>
</body>
</html>

<?php mysqli_close($conn); ?>
