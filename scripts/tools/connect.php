<?php
/**
 * Created by PhpStorm.
 * User: Srinivas
 * Date: 3/15/2016
 * Time: 1:57 AM
 *
 * This is a database connection service script
 */
 // Database Variables (edit with your own server information)
 $server = 'localhost';
 $user = 'root';
 $pass = 'root';
 $db = 'clientopoly';
 // Connect to Database
$conn = new mysqli($server, $user, $pass, $db);
if (!$conn) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    exit;
}
?>