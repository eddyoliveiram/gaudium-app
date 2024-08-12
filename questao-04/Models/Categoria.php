<?php

class Categoria {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getCategoriasByCidade($cidade_id) {
        $stmt = $this->pdo->prepare("
            SELECT categorias.id, categorias.nome
            FROM categorias
            INNER JOIN tarifas ON categorias.id = tarifas.categoria_id
            WHERE tarifas.cidade_id = :cidade_id
            ORDER BY categorias.nome ASC
        ");
        $stmt->execute(['cidade_id' => $cidade_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
