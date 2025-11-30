<?php
// db.php
// Central MySQL connection using mysqli with error handling & UTF-8 charset
//JUMPSTARTER
//$sql_file = "/sql.txt";
const DB_HOST = '127.0.0.1';
const DB_USER = 'root';      // change if needed
const DB_PASS = '';          // change if needed
const DB_NAME = 'scheduler';
const DB_PORT = 3306;

$mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_errno) {
    http_response_code(500);
    die('Database connection failed: ' . $mysqli->connect_error);
}

//testing code
/*
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$result = mysqli_query($conn, "SELECT * FROM scheduler.Calendar_Events_TEMP;" );
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
        echo "id: " . $row["id"]. " - Name: " . $row["Name"]. " " . $row["Room"]. "<br>";
    }
} else {
    echo "0 results";
}
mysqli_close($conn);
*/
//end of testing code

// Set proper charset
if (!$mysqli->set_charset('utf8mb4')) {
    // Not fatal, but good to know
    // echo 'Error loading character set utf8mb4: ' . $mysqli->error;
}

function get_pdo(): PDO {
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $opts = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    return new PDO($dsn, DB_USER, DB_PASS, $opts);
}
//Jumpstarter - RUN PHP file
/*  $sql_content = file_get_contents($sql_file);
if ($conn->multi_query($sql_content)) {
    echo "SQL file executed successfully.";
} else {
    echo "Error executing SQL file: " . $conn->error;
}
 *
 *
 *
 */