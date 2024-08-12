<?php

class CategoriaController {
    private $categoriaModel;

    public function __construct($categoriaModel) {
        $this->categoriaModel = $categoriaModel;
    }

    public function getCategoriasPorCidade() {
        $cidade_id = $this->validateAndSanitize($_GET['cidade_id'], 'int');

        if (!$cidade_id) {
            Logger::logMessage('warning', 'ID da cidade inválido fornecido na requisição');
            die(json_encode(['error' => 'ID da cidade inválido fornecido']));
        }

        try {
            $categorias = $this->categoriaModel->getCategoriasByCidade($cidade_id);

            if (empty($categorias)) {
                Logger::logMessage('info', 'Nenhuma categoria encontrada para a cidade fornecida');
                echo json_encode(['message' => 'Nenhuma categoria encontrada para a cidade fornecida']);
                return;
            }

            Logger::logMessage('info', 'Categorias consultadas com sucesso');
            echo json_encode($categorias);
        } catch (Exception $e) {
            Logger::logMessage('error', 'Erro ao consultar categorias: ' . $e->getMessage());
            die(json_encode(['error' => $e->getMessage()]));
        }
    }

    private function validateAndSanitize($input, $type = 'string') {
        switch ($type) {
            case 'int':
                return filter_var($input, FILTER_VALIDATE_INT);
            case 'string':
                return filter_var(trim($input), FILTER_SANITIZE_STRING);
            default:
                return false;
        }
    }
}
