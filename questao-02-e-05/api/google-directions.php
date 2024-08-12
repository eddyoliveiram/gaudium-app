<?php

require_once './config/Database.php';
require_once './Controllers/GoogleDirectionsController.php';

$database = new Database();
$pdo = $database->pdo;

$requestUri = $_SERVER['REQUEST_URI'];

if (strpos($requestUri, '/api/calculoGoogle') !== false) {
    $controller = new GoogleDirectionsController($pdo);
    $controller->calcularComGoogle();
} else {

    echo json_encode(['error' => 'Endpoint não encontrado']);
}
