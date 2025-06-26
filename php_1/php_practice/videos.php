<?php
session_start();
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["clinic_id"])) {
    header("Location: login.php");
    exit();
}

$videoId = isset($_GET['vid']) ? htmlspecialchars($_GET['vid']) : null;
if (!$videoId) {
    echo "<p style='text-align:center; color:red;'>No video selected.</p>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Now Playing - LaughMD</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      background-color: #f9f9f9;
      padding: 2rem;
    }

    iframe {
      width: 100%;
      max-width: 800px;
      height: 450px;
      border: none;
      margin-top: 2rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .back-link {
      display: inline-block;
      margin-top: 2rem;
      font-weight: bold;
      color: #0a6d19;
      text-decoration: none;
    }

    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <header>
    <img src="images/logo.png" alt="LaughMD Logo" width="250">
    <h2>Now Playing</h2>
  </header>

  <main>
    <iframe src="https://www.youtube.com/embed/<?php echo $videoId; ?>?autoplay=1" allowfullscreen></iframe>
    <br>
    <a class="back-link" href="playlist.php">← Back to Playlist</a>
  </main>

  <footer style="margin-top: 3rem;">
    <p>&copy; 2025 LaughMD</p>
  </footer>
</body>
</html>
