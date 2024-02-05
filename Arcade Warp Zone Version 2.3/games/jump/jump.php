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
    <title>Puppy Jump</title>
    <link rel="icon" type="image/x-icon" href="/images/logo.ico">
    <link rel="stylesheet" type="text/css" href="../gamestyles.css">
    <!-- Must include Phaser javascript file to create games with Phaser! -->
    <script src="/games/phaser.js"></script>
</head>
<body>
<div class=header>
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
<div style="font-family: marioFont; position: absolute; left:-1000px; visibility:hidden;">.</div>
<div id="game_canvas">
    <script>
        // Define the Start Menu Phaser scene
        class StartMenu extends Phaser.Scene {
            constructor() {
                super({ key: 'StartMenu' });
            }

            preload() {
                this.load.image('background', '/games/jump/assets/images/beach.jpg');
                this.load.audio('waves', '/games/jump/assets/sounds/waves.wav');
            }

            create() {
                // Create the Start Menu UI elements
                this.game.canvas.style.position = 'absolute';
                this.game.canvas.style.top = '160px';
                this.add.image(0, 0, 'background').setOrigin(0, 0).setDisplaySize(config.width, config.height);
                this.add.text(450, 175, 'Puppy Jump', {fontFamily: 'marioFont', fontSize: '125px'}).setColor('#064EDE');

                this.menuSounds = this.sound.add('waves', { loop: true });

                this.menuSounds.play({ volume: 0.5 });

                // Create the How to Play Button
                const howToPlayButton = this.add.graphics();
                howToPlayButton.fillStyle(0x33CEFF);
                howToPlayButton.fillRoundedRect(900, 375, 300, 75, 35);
                howToPlayButton.setInteractive(new Phaser.Geom.Rectangle(900, 375, 300, 75), Phaser.Geom.Rectangle.Contains);
                const howToPlayButtonText = this.add.text(925, 395, 'How to Play', { fontFamily: 'marioFont', fontSize: '32px', color: '#2762D8' });

                // Add events for mouse hover on How to Play Button
                howToPlayButton.on('pointerover', function () {
                    howToPlayButton.clear();
                    howToPlayButton.fillStyle(0xF7AC32);
                    howToPlayButton.fillRoundedRect(900, 375, 300, 75, 35);
                    howToPlayButtonText.setColor('white'); // Set text color to white
                });

                howToPlayButton.on('pointerout', function () {
                    howToPlayButton.fillStyle(0x33CEFF);
                    howToPlayButton.fillRoundedRect(900, 375, 300, 75, 35);
                    howToPlayButtonText.setColor('#064EDE'); // Set text color to black
                });

                // When How to Play button is clicked call the howToPlay function
                howToPlayButton.on('pointerdown', this.howToPlay, this);

                const startButton = this.add.graphics();
                startButton.fillStyle(0x33CEFF);
                startButton.fillRoundedRect(550, 375, 250, 75, 35);
                startButton.setInteractive(new Phaser.Geom.Rectangle(550, 375, 250, 75, 35), Phaser.Geom.Rectangle.Contains);
                const startButtonText = this.add.text(615, 395, 'Start', { fontFamily: 'marioFont', fontSize: '32px', color: '#2762D8'}); // Add the text 'How to Play' to our button

                // Add events for mouse hover on Start Button
                startButton.on('pointerover', function () {
                    startButton.clear();
                    startButton.fillStyle(0xF7AC32);
                    startButton.fillRoundedRect(550, 375, 250, 75, 35);
                    startButtonText.setColor('white'); // Set text color to white
                });

                startButton.on('pointerout', function () {
                    startButton.fillStyle(0x33CEFF);
                    startButton.fillRoundedRect(550, 375, 250, 75, 35);
                    startButtonText.setColor('#064EDE'); // Set text color to black
                });

                // When Start button is clicked call the startGame function
                startButton.on('pointerdown', this.startGame, this);

            }

            // howToPlay function that will change to the HowToPlay Phaser Scene
            howToPlay() {
                this.menuSounds.stop();
                this.scene.start('HowToPlay');
            }

            // startGame() function that will change to the MainGame Phaser Scene
            startGame() {
                this.menuSounds.stop();
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
                this.game.canvas.style.position = 'absolute';
                this.game.canvas.style.top = '160px';
                this.add.image(0, 0, 'background').setOrigin(0, 0).setDisplaySize(config.width, config.height);

                // Title for How to Play Scene
                const instructionsTextTitle = this.add.text(650, 200, 'How to Play', {
                    fontFamily: 'marioFont',
                    fontSize: '64px',
                    color: 'white',
                    stroke: '#0055FF',
                    strokeThickness: 10
                });

                // Line 1 of game instructions
                const instructionLine1 = this.add.text(100, 300, "The rules of this game are quiet simple, just use the SPACE key to make the puppy jump and that's it!", {
                    fontFamily: 'marioFont',
                    fontSize: '24px',
                    color: 'white',
                    stroke: '#0055FF',
                    strokeThickness: 10
                });

                // Line 2 of game instructions
                const instructionLine2 = this.add.text(100, 350, 'For every jump the puppy does the user gets a point. How many jumps can you do before getting tired?', {
                    fontFamily: 'marioFont',
                    fontSize: '24px',
                    color: 'white',
                    stroke: '#0055FF',
                    strokeThickness: 10
                });

                const backToMenuButton = this.add.graphics();
                backToMenuButton.fillStyle(0x33CEFF);
                backToMenuButton.fillRoundedRect(700, 475, 375, 60, 35);
                backToMenuButton.setInteractive(new Phaser.Geom.Rectangle(700, 475, 375, 60, 35), Phaser.Geom.Rectangle.Contains);
                const backToMenuButtonText = this.add.text(785, 490, 'Main Menu', { fontFamily: 'marioFont', fontSize: '32px', color: '#2762D8'}); // Add the text 'How to Play' to our button

                // Add events for mouse hover on Start Button
                backToMenuButton.on('pointerover', function () {
                    backToMenuButton.clear();
                    backToMenuButton.fillStyle(0xF7AC32);
                    backToMenuButton.fillRoundedRect(700, 475, 375, 60, 35);
                    backToMenuButtonText.setColor('white'); // Set text color to white
                });

                backToMenuButton.on('pointerout', function () {
                    backToMenuButton.fillStyle(0x33CEFF);
                    backToMenuButton.fillRoundedRect(700, 475, 375, 60, 35);
                    backToMenuButtonText.setColor('#064EDE'); // Set text color to black
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
                this.playerScore = 0;
            }

            // Preload assets needed for the main game
            preload() {

                // Load your GIF as a sprite sheet
                this.load.spritesheet('dog', '/games/jump/assets/images/doge_dance.png', { frameWidth: 360, frameHeight: 360 });

                // Load sound files
                this.load.audio('jump', '/games/jump/assets/sounds/jump.mp3');
                this.load.audio('dogsout', '/games/jump/assets/sounds/dogsout.wav');
            }

            create() {
                this.game.canvas.style.position = 'absolute';
                this.game.canvas.style.top = '160px';
                this.add.image(0, 0, 'background').setOrigin(0, 0).setDisplaySize(config.width, config.height);

                this.gameSound = this.sound.add('dogsout', { loop: true });

                this.gameSound.play({ volume: 0.1 });

                // Add player character
                this.dog = this.physics.add.sprite(800, 475, 'dog');

                // Enable physics for the player character
                this.physics.add.existing(this.dog);

                // Set gravity for the game world
                this.physics.world.gravity.y = 700; // Adjust the value as needed

                // Set up animation from the sprite sheet
                this.anims.create({
                    key: 'playerAnim',  // Animation key
                    frames: this.anims.generateFrameNumbers('dog', { start: 0, end: 9 }),  // Adjust frame numbers based on your GIF
                    frameRate: 10,  // Frames per second
                    repeat: -1  // -1 means loop indefinitely
                });

                // Play the animation
                this.dog.anims.play('playerAnim');

                // Scales the player sprite to 50% of its original size
                this.dog.setScale(0.5);

                this.jumpSound = this.sound.add('jump');

                // Add score text
                this.scoreText = this.add.text(window.innerWidth/2 - 200, 150, 'Score: ' + this.playerScore, { fontFamily: 'marioFont', fontSize: '64px', color: '#064EDE' });

                // Enable cursor keys
                this.cursors = this.input.keyboard.createCursorKeys();

                // Create an invisible platform
                this.platform = this.physics.add.staticGroup();
                this.platform.create(800, 600, 'invisiblePlatform').setScale(5, 2).refreshBody(); // Adjust position and size
                this.platform.setAlpha(0); // Set the alpha (transparency) to 0 to make it invisible

                this.physics.add.collider(this.dog, this.platform);

                // Enable space key for jumping using an arrow function
                this.input.keyboard.on('keydown-SPACE', (event) => {
                    this.jump();
                });

                const backToMenuButton = this.add.graphics();
                backToMenuButton.fillStyle(0x33CEFF);
                backToMenuButton.fillRoundedRect(100, 600, 375, 60, 35);
                backToMenuButton.setInteractive(new Phaser.Geom.Rectangle(100, 600, 375, 60, 35), Phaser.Geom.Rectangle.Contains);
                const backToMenuButtonText = this.add.text(150, 615, 'Back to Main Menu', { fontFamily: 'marioFont', fontSize: '24px', color: '#2762D8'}); // Add the text 'How to Play' to our button

                // Add events for mouse hover on Start Button
                backToMenuButton.on('pointerover', function () {
                    backToMenuButton.clear();
                    backToMenuButton.fillStyle(0xF7AC32);
                    backToMenuButton.fillRoundedRect(100, 600, 375, 60, 35);
                    backToMenuButtonText.setColor('white'); // Set text color to white
                });

                backToMenuButton.on('pointerout', function () {
                    backToMenuButton.fillStyle(0x33CEFF);
                    backToMenuButton.fillRoundedRect(100, 600, 375, 60, 35);
                    backToMenuButtonText.setColor('#064EDE'); // Set text color to black
                });

                // Arrow function that changes to the Start Menu scene when Back to Menu button is clicked
                backToMenuButton.on('pointerdown', () => {
                    this.gameSound.stop();
                    this.scene.start('StartMenu');
                });
            }

            // Update the score on screen
            updateScoreText() {
                this.scoreText.setText('Score: ' + this.playerScore);
            }

            jump() {
                // Check if the player is on the ground or near the ground
                if (this.dog.body.touching.down) {
                    this.dog.setVelocityY(-400);  // Jump velocity
                    this.jumpSound.play({ volume: 0.2 }); // Lower jump sound effect
                    this.playerScore += 1; // Increase score with each jump
                    this.updateDatabase(); // Call function to update database
                    this.updateScoreText(); // Call function to update user score
                }
            }

            // Function to update database with user score
            updateDatabase() {
                // Use AJAX to make a POST request to the update_score.php script
                const ajaxRequest = new XMLHttpRequest();
                const url = '/games/update_score.php';
                const params = 'game_name=Puppy Jump&user_id=' + <?php echo $userID?> +'&score=' + this.playerScore;

                ajaxRequest.open('POST', url, true);
                ajaxRequest.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                ajaxRequest.onreadystatechange = function () {
                    if (ajaxRequest.readyState === 4 && ajaxRequest.status === 200) {
                        console.log(ajaxRequest.responseText);
                    }
                };

                // Send the request
                ajaxRequest.send(params);
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

