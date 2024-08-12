<?php

require_once '../config/Database.php';

class HistoricoController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getHistorico($page = 1, $limit = 5) {
        $offset = ($page - 1) * $limit;

        // Seleciona o histórico com JOIN para mostrar a cidade e categoria
        $stmt = $this->pdo->prepare("
            SELECT corridas.*, cidades.nome AS cidade_nome, categorias.nome AS categoria_nome
            FROM corridas
            JOIN cidades ON corridas.cidade_id = cidades.id
            JOIN categorias ON corridas.categoria_id = categorias.id
            ORDER BY corridas.data_hora_corrida DESC
            LIMIT :limit OFFSET :offset
        ");

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $historico = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Conta o total de registros
        $countStmt = $this->pdo->query("SELECT COUNT(*) FROM corridas");
        $totalRecords = $countStmt->fetchColumn();

        return [
            'historico' => $historico,
            'totalPages' => ceil($totalRecords / $limit),
            'currentPage' => $page
        ];
    }
}

// Acesso via requisicao AJAX
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$historicoController = new HistoricoController((new Database())->pdo);
$response = $historicoController->getHistorico($page);

echo json_encode($response);
