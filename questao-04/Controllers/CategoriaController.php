<?php

require_once '../Models/Categoria.php';
require_once '../config/Database.php';

class CategoriaController {
    private $categoriaModel;

    public function __construct($pdo) {
        $this->categoriaModel = new Categoria($pdo);
    }

    public function getCategoriasPorCidade($cidade_id) {
        $categorias = $this->categoriaModel->getCategoriasByCidade($cidade_id);
        echo json_encode($categorias);
    }
}

// Acesso via requisicao AJAX
if (isset($_GET['action']) && $_GET['action'] === 'getCategoriasPorCidade' && isset($_GET['cidade_id'])) {
    $database = new Database();
    $controller = new CategoriaController($database->pdo);
    $controller->getCategoriasPorCidade($_GET['cidade_id']);
}
