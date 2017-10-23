<?php

try {
    
    // create a new instance of PDO
    $pdo = new PDO('mysql:host=localhost:3306;dbname=webBooks', 'bookListerUser', 'myPassword');
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('SET NAMES "utf8"');
    
} catch (Exception $ex) {
    $closeSelect = false;
    $error = "Unable to connect to Database: " . $ex->getMessage();
    include 'error.html.php';
    exit();
}