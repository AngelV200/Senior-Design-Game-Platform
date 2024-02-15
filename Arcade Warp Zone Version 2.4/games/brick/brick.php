<?php
// Start Session
session_start();

// Check if the user is not authenticated
if (!isset($_SESSION['username'])) {
    header("Location: /login.html");
    exit();
}else{
    $currentUser = $_SESSION['username'];

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

    // Select userId from the table users where username == $currentuser
    $getUserIDQuery = "SELECT user_id FROM users WHERE username = '$currentUser'";
    $result = $conn->query($getUserIDQuery);

    // Check for errors in the query
    if (!$result) {
        echo "Error getting user ID: " . $conn->error;
        exit();
    }

    // Use the userID and fetch_assoc to get the result
    $userData = $result->fetch_assoc();
    $userID = $userData['user_id'];

    // Close the database connection
    $conn->close();
}

?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Brick Breaker</title>
    <link rel="icon" type="image/x-icon" href="/images/logo.ico">
    <link rel="stylesheet" type="text/css" href="../gamestyles.css">
    <!-- Must include Phaser javascript file to create games with Phaser! -->
    <script src="/games/phaser.js"></script>
</head>
<body>
<div class="header">
    <div style="font-family: pixelFont; position: absolute; left:-1000px; visibility:hidden;">.</div>
    <div class="logo">
        <a id="logo" href="#top"><img src="/images/logo.png" alt="logo"></a>
    </div>
    <div class="navbar">
        <a href="/dashboard.php">User Dashboard</a>
        <a href="/games.php">Games</a>
        <a href="/logout.php">Logout</a>
    </div>
</div>
<div id="background-container">
    <video autoplay muted loop playsinline>
        <source src="/videos/stars.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
</div>
<div id="game_canvas">
    <script>
        class BrickBreaker extends Phaser.Scene {
            constructor() {
                super({ key: 'BrickBreaker' });
            }

            preload() {
                this.load.image('paddle', '/games/brickbreaker/assets/images/paddle.png');
                this.load.image('ball', '/games/brickbreaker/assets/images/ball.png');
                this.load.image('brick', '/games/brickbreaker/assets/images/brick.png');
            }

            create() {
                // Create paddle
                this.paddle = this.physics.add.sprite(this.cameras.main.centerX, this.cameras.main.height - 50, 'paddle').setOrigin(0.5, 0.5);

                // Create ball
                this.ball = this.physics.add.sprite(this.cameras.main.centerX, this.paddle.y - 20, 'ball').setOrigin(0.5, 0.5);

                // Create bricks
                this.createBricks();

                // Set up physics
                this.physics.world.setBoundsCollision(true, true, true, false);
                this.ball.setCollideWorldBounds(true);
                this.ball.setBounce(1);

                // Allow paddle to collide with world bounds
                this.paddle.setCollideWorldBounds(true);
                this.paddle.setImmovable(true);

                // Handle user input
                this.cursorKeys = this.input.keyboard.createCursorKeys();

                // Ball and paddle collision
                this.physics.add.collider(this.ball, this.paddle, this.hitPaddle, null, this);

                // Ball and bricks collision
                this.physics.add.collider(this.ball, this.bricks, this.hitBrick, null, this);
            }

            update() {
                // Move paddle
                if (this.cursorKeys.left.isDown) {
                    this.paddle.setVelocityX(-300);
                } else if (this.cursorKeys.right.isDown) {
                    this.paddle.setVelocityX(300);
                } else {
                    this.paddle.setVelocityX(0);
                }

                // Check game over
                if (this.ball.y > this.cameras.main.height) {
                    this.gameOver();
                }
            }

            createBricks() {
                this.bricks = this.physics.add.group({
                    key: 'brick',
                    repeat: 4,
                    immovable: true,
                    setXY: { x: 80, y: 50, stepX: 100 }
                });
                this.bricks.children.iterate(brick => {
                    brick.setScale(2);
                });
            }

            hitPaddle(ball, paddle) {
                ball.setVelocityY(-300);
            }

            hitBrick(ball, brick) {
                brick.disableBody(true, true);
                if (this.bricks.countActive() === 0) {
                    this.gameOver();
                }
            }

            gameOver() {
                // Game over logic
            }

            restartGame() {
                // Reset game state
            }
        }

        // Config const used when creating phaser game
        const config = {
            type: Phaser.AUTO,
            width: window.innerWidth,
            height: 775,
            physics: {
                default: 'arcade',
                arcade: {
                    gravity: { y: 0 },
                    debug: false
                }
            },
            // Scenes in our Phaser Game
            scene: [BrickBreaker]
        };

        // Creating Phaser game after document is fully loaded
        window.addEventListener('load', () => new Phaser.Game(config));
    </script>
</div>
</body>
</html>