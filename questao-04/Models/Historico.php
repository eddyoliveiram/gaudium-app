<?php

class Historico {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getHistorico($offset, $limit) {
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

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countHistorico() {
        $countStmt = $this->pdo->query("SELECT COUNT(*) FROM corridas");
        return $countStmt->fetchColumn();
    }
}
