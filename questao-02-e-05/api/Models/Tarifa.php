<?php
class Tarifa {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getTarifa($cidade_id, $categoria_id) {
        $stmt = $this->pdo->prepare('SELECT bandeirada, valor_por_km, valor_por_hora FROM tarifas WHERE cidade_id = :cidade_id AND categoria_id = :categoria_id');
        $stmt->execute(['cidade_id' => $cidade_id, 'categoria_id' => $categoria_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
