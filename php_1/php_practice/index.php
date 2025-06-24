<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>LaughMD - Welcome</title>
  <link rel="stylesheet" href="style.css" />
  
  <script>
    // Redirect to login.php after 2 seconds (10s wait + 3s fade simulation)
    setTimeout(() => {
      window.location.href = 'login.php';
    }, 2000);
  </script>
</head>
<body>
<header>
    <div style="text-align: center;">
      <img src="images/logo.png" alt="LaughMD Logo" width="250" />
    </div>
  </header>

    <h1>Login to Start Laughing!</h1>
    <button onclick="window.location.href='login.php'">Log In"</button>
  </div>

</body>
</html>
