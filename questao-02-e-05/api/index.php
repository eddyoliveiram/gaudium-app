<?php
require_once './config/Database.php';
require_once './Services/Logger.php';
require_once './Models/Tarifa.php';
require_once './Models/Corrida.php';
require_once './Controllers/CalculoController.php';

$database = new Database();
$pdo = $database->pdo;

$tarifaModel = new Tarifa($pdo);
$corridaModel = new Corrida($pdo);

$controller = new CalculoController($tarifaModel, $corridaModel);
$controller->calcularTarifa();
