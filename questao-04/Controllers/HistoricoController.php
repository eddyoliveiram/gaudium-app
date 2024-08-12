<?php
require_once '../Models/Historico.php';
require_once '../config/Database.php';

class HistoricoController {
    private $historicoModel;

    public function __construct($pdo) {
        $this->historicoModel = new Historico($pdo);
    }

    public function getHistorico($page = 1, $limit = 5) {
        $offset = ($page - 1) * $limit;

        $historico = $this->historicoModel->getHistorico($offset, $limit);

        $totalRecords = $this->historicoModel->countHistorico();

        return [
            'historico' => $historico,
            'totalPages' => ceil($totalRecords / $limit),
            'currentPage' => $page
        ];
    }
}

// Acesso via requisição AJAX
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$historicoController = new HistoricoController((new Database())->pdo);
$response = $historicoController->getHistorico($page);

echo json_encode($response);
