<?php
class Corrida {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function registrarCorrida($cidade_id, $categoria_id, $endereco_origem, $endereco_destino, $distancia, $duracao, $valor_calculado) {
        try {
            $stmt = $this->pdo->prepare('INSERT INTO corridas (cidade_id, categoria_id, endereco_origem, endereco_destino, distancia, duracao, tarifa_calculada, data_hora_corrida) 
                                   VALUES (:cidade_id, :categoria_id, :endereco_origem, :endereco_destino, :distancia, :duracao, :tarifa_calculada, NOW())');
            $stmt->execute([
                'cidade_id' => $cidade_id,
                'categoria_id' => $categoria_id,
                'endereco_origem' => $endereco_origem,
                'endereco_destino' => $endereco_destino,
                'distancia' => $distancia,
                'duracao' => $duracao,
                'tarifa_calculada' => $valor_calculado,
            ]);
        } catch (PDOException $e) {
            Logger::logMessage('error', 'Erro ao registrar a corrida: ' . $e->getMessage());
            throw new Exception('Erro ao registrar a corrida');
        }
    }
}
