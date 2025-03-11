
<?php

    $servername = "localhost"; // Change to your MySQL database host
    $mysql_username = "root"; // Change to your MySQL database username
    $mysql_password = ""; // Change to your MySQL database password
    $dbname = "database3"; // Change to your MySQL database name

    // Using MySQLi
    $mysql_conn = mysqli_connect($servername, $mysql_username, $mysql_password, $dbname);
    
    if (!$mysql_conn) {
        die("Both MS Access and MySQL connections failed.");
    }

    //echo "Connected to MySQL!";
    $conn = $mysql_conn; // Assign MySQL connection to $conn
    $db_type = "mysql"; // Set database type

?>

