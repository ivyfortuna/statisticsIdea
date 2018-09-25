<?php

$servername = "localhost";
$username = "osticket1-owner";
$password = "osticket123$%";
$dbname = "osticket1";

// Create connection
$con = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($con->connect_error) {
    die('Could not connect: ' - mysqli_error($con));
}
mysqli_set_charset($con, 'utf8');

?>