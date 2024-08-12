<?php
class CalculoController {
    private $tarifaModel;
    private $corridaModel;

    public function __construct($tarifaModel, $corridaModel) {
        $this->tarifaModel = $tarifaModel;
        $this->corridaModel = $corridaModel;
    }

    public function validateAndSanitize($input, $type = 'string') {
        switch ($type) {
            case 'int':
                return filter_var($input, FILTER_VALIDATE_INT);
            case 'string':
                return filter_var(trim($input), FILTER_SANITIZE_STRING);
            default:
                return false;
        }
    }

    public function calcularTarifa() {
        $cidade_id = $this->validateAndSanitize($_POST['cidade_id'], 'int');
        $categoria_id = $this->validateAndSanitize($_POST['categoria_id'], 'int');
        $endereco_origem = $this->validateAndSanitize($_POST['endereco_origem'], 'string');
        $endereco_destino = $this->validateAndSanitize($_POST['endereco_destino'], 'string');

        if (!$cidade_id || !$categoria_id || !$endereco_origem || !$endereco_destino) {
            Logger::logMessage('warning', 'Dados inválidos fornecidos na requisição');
            die(json_encode(['error' => 'Dados inválidos fornecidos']));
        }

        $distancia = rand(0, 100);
        $duracao = rand(0, 60) / 60.0;

        $tarifa = $this->tarifaModel->getTarifa($cidade_id, $categoria_id);

        if (!$tarifa) {
            Logger::logMessage('warning', 'Nenhuma tarifa encontrada para a combinação de cidade e categoria');
            die(json_encode(['error' => 'Nenhuma tarifa encontrada para a combinação de cidade e categoria']));
        }

        $valor_calculado = $tarifa['bandeirada'] + ($tarifa['valor_por_hora'] * $duracao) + ($tarifa['valor_por_km'] * $distancia);

        try {
            $this->corridaModel->registrarCorrida($cidade_id, $categoria_id, $endereco_origem, $endereco_destino, $distancia, $duracao, $valor_calculado);
        } catch (Exception $e) {
            die(json_encode(['error' => $e->getMessage()]));
        }

        $response = [
            'distancia' => number_format($distancia, 2),
            'duracao' => number_format($duracao, 2),
            'parametros_utilizados' => [
                'bandeirada' => number_format($tarifa['bandeirada'], 2),
                'valor_por_km' => number_format($tarifa['valor_por_km'], 2),
                'valor_por_hora' => number_format($tarifa['valor_por_hora'], 2),
            ],
            'valor_calculado' => number_format($valor_calculado, 2)
        ];

        Logger::logMessage('info', 'Requisição processada com sucesso');
        echo json_encode($response);
    }
}
