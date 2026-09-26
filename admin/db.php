<?php
function connect()
{
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'tourism_management'; // Change this to your actual database name

    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");

    return $conn;
}