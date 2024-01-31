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

    // Assuming the user ID is a unique identifier, use fetch_assoc to get the result
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
        <title>Pong</title>
        <link rel="icon" type="image/x-icon" href="/images/logo.ico">
        <link rel="stylesheet" type="text/css" href="/css/styles.css">
        <!-- Must include Phaser javascript file to create games with Phaser! -->
        <script src="/games/phaser.js"></script>
    </head>
    <body>
        <div class=header>
            <div class="logo">
                <a id="logo" href="#top"><img src="/images/logo.png" alt="logo"></a>
            </div>
            <div class="navbar">
                <a href="/dashboard.php">Dashboard</a>
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
        <div style="font-family: pixelFont; position: absolute; left:-1000px; visibility:hidden;">.</div>
        <div id="game_canvas">
            <script>
                // Define the Start Menu Phaser scene
                class StartMenu extends Phaser.Scene {
                    constructor() {
                        super({ key: 'StartMenu' });
                    }

                    create() {
                        // Create the Start Menu UI elements
                        this.game.canvas.style.position = 'absolute'; // Game Canvas position on webpage
                        this.game.canvas.style.top = '160px'; // Brings Canvas 160px down from the top of webpage
                        this.cameras.main.setBackgroundColor('black') // Set Background to the color black
                        this.add.text(600, 150, 'Pong', {fontFamily: 'pixelFont', fontSize: '125px' }); // Add the text 'Pong' to our Main Menu scene

                        // Create the How to Play Button
                        const howToPlayButton = this.add.rectangle(850, 465, 450, 60, 0xffffff); // Create white rectangle that will act as a button for the How to Play Scene
                        howToPlayButton.setInteractive(); // Make the button interactive
                        const howToPlayButtonText = this.add.text(675, 450, 'How to Play', { fontFamily: 'pixelFont', fontSize: '32px', color: 'black'}); // Add the text 'How to Play' to our button

                        // Add events for mouse hover on How to Play Button
                        howToPlayButton.on('pointerover', function () {
                            howToPlayButton.setFillStyle(0x000000); // Set button fill color to black
                            howToPlayButtonText.setColor('white'); // Set text color to white
                        });

                        howToPlayButton.on('pointerout', function () {
                            howToPlayButton.setFillStyle(0xffffff); // Set button fill color to white
                            howToPlayButtonText.setColor('black'); // Set text color to black
                        });

                        // When How to Play button is clicked call the howToPlay function
                        howToPlayButton.on('pointerdown', this.howToPlay, this);

                        // Create the Start Game Button
                        const startButton = this.add.rectangle(825, 365, 200, 60, 0xffffff); // Create white rectangle that will act as a button for the Start Game Scene
                        startButton.setInteractive(); // Make the button interactive
                        const buttonText = this.add.text(750, 350, 'Start', { fontFamily: 'pixelFont', fontSize: '32px', color: 'black'}); // Add the text 'Start' to our button

                        // Add events for mouse hover on Start Button
                        startButton.on('pointerover', function () {
                            startButton.setFillStyle(0x000000); // Set button fill color to black
                            buttonText.setColor('white'); // Set text color to white
                        });

                        startButton.on('pointerout', function () {
                            startButton.setFillStyle(0xffffff); // Set button fill color to white
                            buttonText.setColor('black'); // Set text color to black
                        });

                        // When Start button is clicked call the startGame function
                        startButton.on('pointerdown', this.startGame, this);

                    }

                    // howToPlay function that will change to the HowToPlay Phaser Scene
                    howToPlay() {
                        this.scene.start('HowToPlay');
                    }

                    // startGame() function that will change to the MainGame Phaser Scene
                    startGame() {
                        this.scene.start('MainGame');
                    }
                }

                // Define the How to Play Phaser scene
                class HowToPlay extends Phaser.Scene {
                    constructor() {
                        super({ key: 'HowToPlay' });
                    }

                    create() {
                        // Display How to Play instructions
                        // Title for How to Play Scene
                        const instructionsTextTitle = this.add.text(600, 200, 'How to Play', {
                            fontFamily: 'pixelFont',
                            fontSize: '48px',
                            color: 'white'
                        });

                        // Line 1 of game instructions
                        const instructionLine1 = this.add.text(200, 300, 'Use the UP and DOWN arrow keys to control your paddle.', {
                            fontFamily: 'pixelFont',
                            fontSize: '24px',
                            color: 'white'
                        });

                        // Line 2 of game instructions
                        const instructionLine2 = this.add.text(200, 350, 'Score points by getting the ball past your opponent\'s paddle.', {
                            fontFamily: 'pixelFont',
                            fontSize: '24px',
                            color: 'white'
                        });

                        // Line 3 of game instructions
                        const instructionLine3 = this.add.text(200, 400, 'First player to reach eleven points wins the game!', {
                            fontFamily: 'pixelFont',
                            fontSize: '24px',
                            color: 'white'
                        });

                        // Create the Back to Menu button
                        const backToMenuButton = this.add.rectangle(850, 500, 375, 60, 0xffffff); // Create white rectangle that will act as a button for the Start Menu Scene
                        backToMenuButton.setInteractive(); // Make the button interactive
                        const backToMenuButtonText = this.add.text(700, 485, 'Main Menu', { fontFamily: 'pixelFont', fontSize: '32px', color: 'black' }); // Add the text 'Main Menu' to our button

                        // Add events for mouse hover on Back to Menu button
                        backToMenuButton.on('pointerover', function () {
                            backToMenuButton.setFillStyle(0x000000); // Set button fill color to black
                            backToMenuButtonText.setColor('white'); // Set text color to white
                        });

                        backToMenuButton.on('pointerout', function () {
                            backToMenuButton.setFillStyle(0xffffff); // Set button fill color to white
                            backToMenuButtonText.setColor('black'); // Set text color to black
                        });

                        // Arrow function that changes to the Start Menu scene when Back to Menu button is clicked
                        backToMenuButton.on('pointerdown', () => {
                            this.scene.start('StartMenu');
                        });
                    }
                }

                // // Define the Main Game Phaser scene (Game Logic can be found here!)
                class MainGame extends Phaser.Scene {
                    constructor() {
                        super({ key: 'MainGame' });
                        this.playerLeftScore = 0; // Left player Score
                        this.playerRightScore = 0; // Right player Score
                    }

                    // Preload assets needed for the main game
                    preload() {
                        this.load.image('paddle', '/games/pong/assets/images/paddle.png'); // Loads paddle PNG image
                        this.load.image('ball', '/games/pong/assets/images/ball.png'); // Loads ball PNG image

                        // Load sound files
                        this.load.audio('wallHit', '/games/pong/assets/sounds/wallHit.wav'); // Loads wall hit sound
                        this.load.audio('ballHit', '/games/pong/assets/sounds/ballHit.wav'); // Loads paddle ball hit sound
                    }

                    create() {
                        // Flag variable to ensure the database is only updated once
                        this.isDatabaseUpdateCalled = false;

                        // Set world bounds for the Pong Game
                        this.physics.world.setBounds(0, 0, window.innerWidth, 775);

                        // Create sound variables
                        this.wallHit = this.sound.add('wallHit');
                        this.ballHit = this.sound.add('ballHit');

                        // Add Score text to game screen
                        this.scoreTextLeft = this.add.text(window.innerWidth/2 - 150, 50, '0', { fontFamily: 'pixelFont', fontSize: '36px', color: 'white' });
                        this.scoreTextRight = this.add.text(window.innerWidth/2 + 150, 50, '0', { fontFamily: 'pixelFont', fontSize: '36px', color: 'white' });

                        // Paddle dimensions
                        const paddleWidth = 30;
                        const paddleHeight = 100;

                        // Ball dimensions
                        const ballSize = 20;

                        // Create paddles
                        this.paddleLeft = this.physics.add.sprite(50, this.cameras.main.centerY, 'paddle').setOrigin(0.5, 0.5).setDisplaySize(paddleWidth, paddleHeight); // Set Left paddle to default location
                        this.paddleLeft.body.pushable = false; // Make the Left paddle unpushable so the paddle can stay in place when hit by the ball
                        this.paddleRight = this.physics.add.sprite(this.cameras.main.width - 50, this.cameras.main.centerY, 'paddle').setOrigin(0.5, 0.5).setDisplaySize(paddleWidth, paddleHeight); // Set Right paddle to default location
                        this.paddleRight.body.pushable = false; // Make the Right paddle unpushable so the paddle can stay in place when hit by the ball

                        // Create ball
                        this.ball = this.physics.add.sprite(this.cameras.main.centerX, this.cameras.main.centerY, 'ball').setOrigin(0.5, 0.5).setDisplaySize(ballSize, ballSize); // Set ball to default location

                        // Set initial velocity for the ball
                        // Generate a random number either between -350 and -250 or between 250 and 350
                        const randomNumberX = Phaser.Math.Between(0, 1) === 0 ? Phaser.Math.Between(-350, -250) : Phaser.Math.Between(250, 350);
                        const randomNumberY = Phaser.Math.Between(0, 1) === 0 ? Phaser.Math.Between(-350, -250) : Phaser.Math.Between(250, 350);
                        // Use random number to set ball's somewhat random initial velocity
                        this.ball.setVelocity(randomNumberX, randomNumberY);

                        // Use phaser setBonce method to determine how elastic the collision between objects should be
                        this.ball.setBounce(1);

                        // Enable the ball's collision with the world bounds
                        this.ball.setCollideWorldBounds(true);

                        // Enable cursor keys for input
                        this.cursorkeys = this.input.keyboard.createCursorKeys();


                        // Handle collision between the ball and the left paddle by calling the handleBallPaddleCollision function
                        this.physics.add.collider(this.paddleLeft, this.ball, this.handleBallPaddleCollision, null, this);

                        // Handle collision between the ball and the right paddle by calling the handleBallPaddleCollision function
                        this.physics.add.collider(this.paddleRight, this.ball, this.handleBallPaddleCollision, null, this);

                    }

                    // Function that is called when paddle and ball collide
                    handleBallPaddleCollision(paddle, ball) {
                        // Speed multiple when paddle and ball collide
                        const speedMultiplier = 1.05;

                        // Calculate the angle between the ball and the center of the paddle
                        const angle = Phaser.Math.Angle.Between(paddle.x, paddle.y, ball.x, ball.y);

                        // Calculate the new velocity components based on the angle
                        const newVelocityX = Math.cos(angle) * ball.body.speed * speedMultiplier;
                        const newVelocityY = Math.sin(angle) * ball.body.speed * speedMultiplier;

                        // Set the new velocity for the ball
                        ball.setVelocity(newVelocityX, newVelocityY);

                        // Play the ball and paddle collision sound
                        this.ballHit.play();
                    }

                    // Update logic for the Main Game Phaser scene
                    update() {
                        // Player's paddle speed
                        const paddleSpeed = 5;

                        // Opponent's paddle speed
                        const opponentSpeed = 3.5;

                        // Code to move Player's paddle depending on keys pressed and ensures paddle stays within the world bounds
                        if (this.cursorkeys.up.isDown) {
                            this.paddleLeft.y = Phaser.Math.Clamp(this.paddleLeft.y - paddleSpeed, this.paddleLeft.displayHeight / 2, this.physics.world.bounds.height - this.paddleLeft.displayHeight / 2);
                        } else if (this.cursorkeys.down.isDown) {
                            this.paddleLeft.y = Phaser.Math.Clamp(this.paddleLeft.y + paddleSpeed, this.paddleLeft.displayHeight / 2, this.physics.world.bounds.height - this.paddleLeft.displayHeight / 2);
                        }

                        // Adjust opponent paddle's position based on the ball's position ensures paddle stays within the world bounds
                        if (this.ball.y < this.paddleRight.y) {
                            this.paddleRight.y = Phaser.Math.Clamp(this.paddleRight.y - opponentSpeed, this.paddleRight.displayHeight / 2, this.physics.world.bounds.height - this.paddleRight.displayHeight / 2);
                        } else if (this.ball.y > this.paddleRight.y) {
                            this.paddleRight.y = Phaser.Math.Clamp(this.paddleRight.y + opponentSpeed, this.paddleRight.displayHeight / 2, this.physics.world.bounds.height - this.paddleRight.displayHeight / 2);
                        }

                        // Ball crossed over the left paddle, update the score for the right player
                        if (this.ball.x < this.paddleLeft.x) {
                            this.playerRightScore++; // Add to current score
                            this.resetBall(); // Reset the ball to the default location using the resetBall function
                        } else if (this.ball.x > this.paddleRight.x + this.paddleRight.displayWidth) {
                            // Ball crossed over the right paddle, update the score for the left player
                            this.playerLeftScore++; // Add to current score
                            this.resetBall(); // Reset the ball to the default location using the resetBall function
                        }

                        // Check if the ball hits the top or bottom walls
                        if (this.ball.y <= this.ball.displayHeight / 2 || this.ball.y >= this.physics.world.bounds.height - this.ball.displayHeight / 2) {
                            // Play the ball wall hit sound
                            this.wallHit.play();
                        }

                        // Update Player score during game play
                        this.scoreTextLeft.setText(this.playerLeftScore);
                        this.scoreTextRight.setText(this.playerRightScore);

                        // Check for the winning condition
                        this.checkWinCondition();

                    }

                    // Function to reset the game ball to default location
                    resetBall() {
                        this.ball.setPosition(this.cameras.main.centerX, this.cameras.main.centerY).setOrigin(0.5, 0.5);
                        this.ball.setVelocity(0, 0); // Stop the ball initially

                        // Use Phaser's time event to delay the ball's movement
                        this.time.addEvent({
                            delay: 1000, // 1000 milliseconds (1 second) delay
                            callback: () => {
                                // Set initial velocity for the ball after the delay
                                if (this.playerLeftScore > this.playerRightScore){
                                    // Generate a random number either between -350 and -250 or between 250 and 350
                                    const randomNumber = Phaser.Math.Between(0, 1) === 0 ? Phaser.Math.Between(-350, -250) : Phaser.Math.Between(250, 350);
                                    this.ball.setVelocity(300, randomNumber);
                                }
                                if (this.playerLeftScore < this.playerRightScore){
                                    // Generate a random number either between -350 and -250 or between 250 and 350
                                    const randomNumber = Phaser.Math.Between(0, 1) === 0 ? Phaser.Math.Between(-350, -250) : Phaser.Math.Between(250, 350);
                                    this.ball.setVelocity(-300, randomNumber);
                                }
                                else {
                                    // Generate a random number either between -350 and -250 or between 250 and 350
                                    const randomNumber = Phaser.Math.Between(0, 1) === 0 ? Phaser.Math.Between(-350, -250) : Phaser.Math.Between(250, 350);
                                    this.ball.setVelocity(300, randomNumber);
                                }
                            },
                            callbackScope: this,
                            loop: false // Execute only once
                        });
                    }

                    // Function to check on the game winning condition
                    checkWinCondition() {
                        // Score needed to win the game
                        const winningScore = 11;

                        // If winning score is obtained stop the game and declare a winner!
                        if (this.playerLeftScore >= winningScore || this.playerRightScore >= winningScore) {

                            if (this.playerLeftScore >= winningScore){
                                // Update database with score
                                this.updateDatabase();
                            }
                            // Const to display winner text
                            const winnerText = this.add.text(this.cameras.main.centerX - 250, this.cameras.main.centerY - 150, '', {
                                fontFamily: 'pixelFont',
                                fontSize: '32px',
                                color: 'white'
                            });

                            // String variable for winner message
                            let winnerMessage = '';
                            if (this.playerLeftScore >= winningScore) {
                                winnerMessage = 'Left Player Wins!';
                            } else {
                                winnerMessage = 'Right Player Wins!';
                            }

                            // Use winner text to display winner message
                            winnerText.setText(winnerMessage);

                            // Stop the ball from moving when winner is declared
                            this.ball.setPosition(this.cameras.main.centerX, this.cameras.main.centerY - 25).setVelocity(0, 0);

                            // Create the Play Again button
                            const playAgainButton = this.add.rectangle(875, 365, 425, 60, 0xffffff); // Create white rectangle that will act as a button for the Main Game Scene
                            playAgainButton.setInteractive(); // Make the button interactive
                            const playAgainButtonText = this.add.text(700, 350, 'Play Again?', { fontFamily: 'pixelFont', fontSize: '32px', color: 'black'}); // Add the text 'Play Again?' to our button

                            // When Play Again button is clicked call the restartGame function
                            playAgainButton.on('pointerdown', this.restartGame, this);

                            // Create the Back to Menu button
                            const gameMenuButton = this.add.rectangle(875, 465, 425, 60, 0xffffff); // Create white rectangle that will act as a button for the Start Menu Scene
                            gameMenuButton.setInteractive(); // Make the button interactive
                            const gameMenuText = this.add.text(725, 450, 'Main Menu', { fontFamily: 'pixelFont', fontSize: '32px', color: 'black'}); // Add the text 'Main Menu' to our button

                            // When Play Again button is clicked call the goToMainMenu function
                            gameMenuButton.on('pointerdown', this.goToMainMenu, this);
                        }
                    }

                    // Function to update game scores using AJAX
                    updateDatabase() {
                        // Check if the function has already been called
                        if (this.isDatabaseUpdateCalled) {
                            console.log("updateGameScores function has already been called.");
                            return;
                        }

                        // Set the flag to true to indicate that the function has been called
                        this.isDatabaseUpdateCalled = true;

                        // Use AJAX to make a POST request to the update_score.php script
                        // Replace 'YOUR_PHP_SCRIPT_URL' with the actual URL of your PHP script
                        // You may need to adjust the URL based on your project structure
                        const ajaxRequest = new XMLHttpRequest();
                        const url = '/games/update_score.php'; // Update the URL
                        const params = 'game_name=Pong&user_id=' + <?php echo $userID?>; // Adjust parameters as needed

                        ajaxRequest.open('POST', url, true);
                        ajaxRequest.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                        ajaxRequest.onreadystatechange = function () {
                            if (ajaxRequest.readyState === 4 && ajaxRequest.status === 200) {
                                // Handle the response if needed
                                console.log(ajaxRequest.responseText);
                            }
                        };

                        // Send the request
                        ajaxRequest.send(params);
                    }

                    // Function to restart the game
                    restartGame() {
                        // Reset scores
                        this.playerLeftScore = 0;
                        this.playerRightScore = 0;

                        // Start the Main Game Phaser scene
                        this.scene.start('MainGame');
                    }

                    // Function to go back to the game menu
                    goToMainMenu() {
                        // Reset scores before going to main menu
                        this.playerLeftScore = 0;
                        this.playerRightScore = 0;

                        // Start the Start Menu Phaser scene
                        this.scene.start('StartMenu');
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
                            gravity: {y: 0},
                            debug: false,
                            setBounds: {
                                left: true,
                                right: true,
                                top: true,
                                bottom: true
                            }
                        }
                    },
                    // Scenes in our Phaser Game
                    scene: [StartMenu, MainGame, HowToPlay]
                };

                // Creating Phaser game with our config const
                const game = new Phaser.Game(config);

            </script>
        </div>
    </body>
</html>
