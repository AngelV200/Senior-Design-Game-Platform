<?php
// Start Session
session_start();

if (isset($_SESSION['username'])) {
    $currentUser = $_SESSION['username'];
} else {
    header("Location: /login.html");
    exit();
}

// Database variables and information
$host = "arcadewarpzone.ccaow2uqh8ko.us-west-1.rds.amazonaws.com";
$db_username = "admin";
$db_password = "awz12345+";
$db = "awz";

// Establish a database connection
$conn = new mysqli($host, $db_username, $db_password, $db);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT profile_picture FROM users WHERE username = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $currentUser);
$stmt->execute();
$stmt->bind_result($result);
$stmt->fetch();
$stmt->close();

// Select userId from the table users where username == $currentuser
$getUserIDQuery = "SELECT user_id FROM users WHERE username = ?";
$stmtUserID = $conn->prepare($getUserIDQuery);
$stmtUserID->bind_param("s", $currentUser);  // Provide the data type specifier
$stmtUserID->execute();
$stmtUserID->bind_result($userID);

// Fetch the user ID
$stmtUserID->fetch();
$stmtUserID->close();

// Fetch user's game scores
$queryScores = "SELECT game_name, score FROM game_scores WHERE user_id = ?";
$stmtScores = $conn->prepare($queryScores);
$stmtScores->bind_param("i", $userID);  // Use "i" for integer type
$stmtScores->execute();
$stmtScores->bind_result($gameName, $score);

// Store scores in an array for later use
$userScores = [];
while ($stmtScores->fetch()) {
    $userScores[] = ['gameName' => $gameName, 'score' => $score];
}
$stmtScores->close();

?>

<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="UTF-8">
      <title>Arcade Warp Zone</title>
      <link rel="icon" type="image/x-icon" href="images/logo.ico">
      <link rel="stylesheet" type="text/css" href="css/styles.css">
  </head>
  <body>
      <div class=header>
          <div class="logo">
              <a id="logo" href="#top"><img src="images/logo.png" alt="logo"></a>
          </div>
          <div class="navbar">
              <a href="games.php">Games</a>
              <a href="logout.php">Logout</a>
          </div>
      </div>
      <div id="background-container">
          <video autoplay muted loop playsinline>
              <source src="/videos/stars.mp4" type="video/mp4">
              Your browser does not support the video tag.
          </video>
      </div>
      <div id="dashboard_container">
          <!-- Display the current profile picture -->
          <img src="<?php echo "images/profile_pictures/".$result.".png"?>" alt="<?php echo $result?>" id="currentProfilePicture"><br>

          <!-- Button to open the profile picture selection-->
          <button id="changePictureBtn">Change Profile Picture</button>

          <!-- Profile picture selection -->
          <div id="profilePictureSelection" class="selection_menu">
              <div class="menu_content">
                  <!-- Display a list of profile pictures from "profile_pictures" folder -->
                  <?php
                  $profilePictureFolder = "images/profile_pictures/";
                  $profilePictures = glob($profilePictureFolder . "*.png");

                  foreach ($profilePictures as $picture) {
                      echo '<img src="' . $picture . '" alt="profile_picture" class="profile_option">';
                  }
                  ?>
              </div>
          </div>
          <h1>Username: <?php echo $currentUser; ?></h1>
          <h1>Game Scores</h1>
          <!-- Display game scores with scrollable container -->
          <div id="gameScoresContainer">
              <?php
              foreach ($userScores as $userScore) {
                  echo "<h2>{$userScore['gameName']} : {$userScore['score']}</h2>";
              }
              ?>
          </div>
      </div>
  </body>
  <script>
      // JavaScript to handle the profile picture selection
      let menu = document.getElementById("profilePictureSelection");
      let btn = document.getElementById("changePictureBtn");

      btn.onclick = function () {
          menu.style.display = "block";
      };

      window.onclick = function (event) {
          if (event.target === menu) {
              menu.style.display = "none";
          }
      };

      // JavaScript to handle changing the current profile picture
      document.querySelectorAll(".profile_option").forEach(function (option) {
          option.addEventListener("click", function () {
              let imgElement = document.getElementById("currentProfilePicture");
              imgElement.src = this.src;
              menu.style.display = "none";

              // Extract only the filename without the extension
              let fileName = this.src.split('/').pop().split('.')[0];

              // Send a request to update the profile picture in the database
              fetch('update_profile_picture.php', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/x-www-form-urlencoded',
                  },
                  body: 'new_profile_picture=' + encodeURIComponent(fileName),
              })
                  .then(response => response.json())
                  .then(data => {
                      console.log(data);
                  })
                  .catch(error => {
                      console.error('Error:', error);
                  });
          });
      });
  </script>
</html>