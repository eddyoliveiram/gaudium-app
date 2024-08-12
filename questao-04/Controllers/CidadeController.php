<?php
require_once '../Models/Cidade.php';
require_once '../config/Database.php';

class CidadeController {
    private $cidadeModel;

    public function __construct($pdo) {
        $this->cidadeModel = new Cidade($pdo);
    }

    public function getCidades() {
        $cidades = $this->cidadeModel->getAllCidades();
        echo json_encode($cidades);
    }
}

// Acesso via requisicao AJAX
if (isset($_GET['action']) && $_GET['action'] === 'getCidades') {
    require_once '../config/Database.php';
    $database = new Database();
    $controller = new CidadeController($database->pdo);
    $controller->getCidades();
}
