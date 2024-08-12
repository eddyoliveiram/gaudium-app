<?php

require_once './config/Database.php';
require_once './Services/Logger.php';
require_once './Models/Categoria.php';
require_once './Controllers/CategoriaController.php';

$database = new Database();
$pdo = $database->pdo;

$categoriaModel = new Categoria($pdo);

$categoriaController = new CategoriaController($categoriaModel);

$categoriaController->getCategoriasPorCidade();
