/***

Set Up mySql With Website:

1.) Create a database connection with root user using local host
    2) On root, make this table so just paste in on a query and run it
        -- Create the "Arcade Warp Zone" schema
        CREATE SCHEMA `arcade warp zone`;

        -- Use the "Arcade Warp Zone" schema
        USE `arcade warp zone`;

        -- Create a table for user information
        CREATE TABLE `users` (
            `email` VARCHAR(255) NOT NULL,
            `username` VARCHAR(255) PRIMARY KEY,
            `password` VARCHAR(255) NOT NULL,
            `first_name` VARCHAR(255) NOT NULL,
            `last_name` VARCHAR(255) NOT NULL
        );

    3) On root, create a user and grant it all priveledges:
    -- Create a user 'awz' identified by 'password'
    CREATE USER 'awzuser'@'localhost' IDENTIFIED BY 'awz12345+';

    -- Grant all privileges on the 'Arcade Warp Zone' schema to 'awz'
    GRANT ALL PRIVILEGES ON `arcade warp zone`.* TO 'awzuser'@'localhost';

    -- Reload the privileges
    FLUSH PRIVILEGES;
    
    4) Logout of root and login to awzuser using awz12345+

***/

const express = require('express'); // Imports the Express.js framework
const mysql = require('mysql'); // Imports the MySQL database library
const bodyParser = require('body-parser'); // Imports the body-parser middleware for parsing HTTP request bodies

const app = express(); // Creates an instance of the Express application
app.use(bodyParser.urlencoded({ extended: false })); // Configures the app to use the bodyParser middleware with URL-encoded data


const connection = mysql.createConnection({ // Connection object with these fields
    host: 'localhost',
    user: 'awzuser',
    password: 'awz12345+',
    database: 'arcade warp zone',
});

connection.connect((error) => { // Checks if the connection was successful
    if (error) {
        console.error('Error connecting to the database:', err);
        return;
    }
    console.log('Connected to the database');
});


function insertUser(request, response) { // Function to insert a new user that calls html code
    const user = { // Object with 
        email: request.body.email, // Access the email field text from the html code
        username: request.body.username,
        password: request.body.password,
        first_name: request.body.first_name,
        last_name: request.body.last_name,
    }

    connection.query('INSERT INTO users SET ?', user, (potentialError, results) => { // Checks if the insertion was correct
        if (potentialError) {
            console.error('Error inserting a new user:', potentialError);
            response.send('Error registering user'); // Sends this response to the html code
        } else {
            console.log('User inserted with ID:', results.insertId);
            alert("Successfully Registered")
        }
    });
}


function getUser(request, response) { // Function to retrieve user data
    const username = request.query.username;

    connection.query('SELECT * FROM users WHERE username = ?', username, (potentialError, rows) => { // Retrieve a user using the username
        if (potentialError) {
            console.error('Error retrieving user data:', potentialError);
            request.send('Error retrieving user data');
        } else { // It worked so
            if (rows.length > 0) { // If a row is returned
                response.send(rows[0]);
                alert("User Found")
            } else { 
                response.send('User not found');
                alert("User Does Not Exist")
            }
        }
    });
}

app.post('/register', insertUser);
app.get('/user', getUser);

app.listen(3000, () => { // Port for local host
    console.log('Server is running on port 3000');
});