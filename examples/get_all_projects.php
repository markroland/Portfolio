<?php

// Connect to database
$pdo_connection = null;
try {
    $pdo_connection = new \PDO('mysql:host=localhost;dbname=portfolio;charset=utf8', 'portfolio_user', 'portfolio_pass');
    $pdo_connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $pdo_connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
} catch(\PDOException $e) {
    error_log('ERROR: ' . $e->getMessage() . "\n");
}

// Include class (if not using Composer's vendor/autoload.php)
require __DIR__ . '/../src/Portfolio.php';

// Create new object
$Portfolio = new MarkRoland\Portfolio($pdo_connection);

// List Webhooks
$projects = $Portfolio->get_all_projects();

// Display response
print_r($projects);
