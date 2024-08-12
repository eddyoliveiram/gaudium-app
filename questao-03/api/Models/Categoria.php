<?php
class Categoria {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getCategoriasByCidade($cidade_id) {
        try {
            $stmt = $this->pdo->prepare('SELECT c.nome FROM categorias c 
                                         JOIN tarifas t ON c.id = t.categoria_id 
                                         WHERE t.cidade_id = :cidade_id 
                                         ORDER BY c.nome ASC');
            $stmt->execute(['cidade_id' => $cidade_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Logger::logMessage('error', 'Erro ao consultar categorias: ' . $e->getMessage());
            throw new Exception('Erro ao consultar categorias');
        }
    }
}
