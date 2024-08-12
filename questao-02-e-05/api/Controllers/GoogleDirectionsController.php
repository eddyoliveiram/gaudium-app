<?php

require '../vendor/autoload.php'; // Carrega o autoload com Redis
require_once './Services/Logger.php';
require_once './Models/Tarifa.php';
require_once './Models/Corrida.php';

class GoogleDirectionsController {
    private $apiKey;
    private $pdo;
    private $redis;
    private $tarifaModel;
    private $corridaModel;

    public function __construct($pdo) {
        $this->apiKey = 'SUA_API_KEY_DO_GOOGLE';
        $this->pdo = $pdo;
        $this->redis = new Predis\Client();
        $this->tarifaModel = new Tarifa($pdo);
        $this->corridaModel = new Corrida($pdo);
    }

    public function calcularComGoogle() {
        $cidade_id = $this->validateAndSanitize($_POST['cidade_id'], 'int');
        $categoria_id = $this->validateAndSanitize($_POST['categoria_id'], 'int');
        $endereco_origem = $this->validateAndSanitize($_POST['endereco_origem'], 'string');
        $endereco_destino = $this->validateAndSanitize($_POST['endereco_destino'], 'string');

        if (!$this->dadosValidos($cidade_id, $categoria_id, $endereco_origem, $endereco_destino)) {
            return;
        }

        $cacheKey = $this->generateCacheKey($cidade_id, $categoria_id, $endereco_origem, $endereco_destino);
        $cachedData = $this->getFromCache($cacheKey);

        if ($cachedData) {
            echo $cachedData;
            return;
        }

        list($distance, $duration) = $this->obterDistanciaEDuracao($endereco_origem, $endereco_destino);

        $tarifa = $this->tarifaModel->getTarifa($cidade_id, $categoria_id);
        if (!$tarifa) {
            Logger::logMessage('error', 'Tarifa não encontrada para a cidade e categoria fornecidas.');
            echo json_encode(['error' => 'Tarifa não encontrada']);
            return;
        }

        $valor_calculado = $this->calcularTarifa($tarifa, $distance, $duration);

        $this->corridaModel->registrarCorrida($cidade_id, $categoria_id, $endereco_origem, $endereco_destino, $distance, $duration, $valor_calculado);

        $responseData = $this->formatarResposta($distance, $duration, $cidade_id, $categoria_id, $valor_calculado);
        $this->storeInCache($cacheKey, $responseData);

        echo $responseData;
    }

    private function dadosValidos($cidade_id, $categoria_id, $endereco_origem, $endereco_destino) {
        if (!$cidade_id || !$categoria_id || !$endereco_origem || !$endereco_destino) {
            Logger::logMessage('warning', 'Dados inválidos fornecidos na requisição');
            echo json_encode(['error' => 'Dados inválidos fornecidos']);
            return false;
        }
        return true;
    }

    private function generateCacheKey($cidade_id, $categoria_id, $endereco_origem, $endereco_destino) {
        return "route:$cidade_id:$categoria_id:" . md5($endereco_origem . $endereco_destino);
    }

    private function getFromCache($cacheKey) {
        $cachedData = $this->redis->get($cacheKey);
        if ($cachedData) {
            Logger::logMessage('info', 'Dados recuperados do cache Redis');
            return $cachedData;
        }
        return null;
    }

    private function storeInCache($cacheKey, $responseData) {
        $this->redis->setex($cacheKey, 15, $responseData);
        Logger::logMessage('info', "Dados armazenados no Redis com a chave: $cacheKey");
    }

    private function obterDistanciaEDuracao($endereco_origem, $endereco_destino) {
        $context = stream_context_create([
            'http' => [
                'timeout' => 5  // Timeout em segundos
            ]
        ]);

        $url = "https://maps.googleapis.com/maps/api/directions/json?origin=" . urlencode($endereco_origem) . "&destination=" . urlencode($endereco_destino) . "&key=" . $this->apiKey;

        try {
            $response = @file_get_contents($url, false, $context);
            if ($response === false) {
                throw new Exception('Erro ao acessar a API do Google.');
            }

            Logger::logMessage('info', 'Requisição à API do Google Directions bem-sucedida');

            $data = json_decode($response, true);
            if ($data['status'] === 'OK') {
                $distance = $data['routes'][0]['legs'][0]['distance']['value'] / 1000; // Distância em quilômetros
                $duration = $data['routes'][0]['legs'][0]['duration']['value'] / 60;   // Duração em minutos
                return [$distance, $duration];
            } else {
                throw new Exception('Erro ao acessar a API do Google: ' . $data['status']);
            }
        } catch (Exception $e) {
            Logger::logMessage('error', $e->getMessage());

            // Fallback para valores aleatórios
            $distance = rand(10, 80); // Distância aleatória em km
            $duration = rand(15, 75); // Duração aleatória em minutos

            Logger::logMessage('warning', 'Usando valores aleatórios para distância e duração devido à falha na API do Google');
            return [$distance, $duration];
        }
    }

    private function calcularTarifa($tarifa, $distance, $duration) {
        return $tarifa['bandeirada'] + ($tarifa['valor_por_hora'] * $duration) + ($tarifa['valor_por_km'] * $distance);
    }

    private function formatarResposta($distance, $duration, $cidade_id, $categoria_id, $valor_calculado) {
        return json_encode([
            'distance' => round($distance, 2) . ' km',
            'duration' => round($duration, 2) . ' min',
            'cidade_id' => $cidade_id,
            'categoria_id' => $categoria_id,
            'valor_calculado' => round($valor_calculado, 2)
        ]);
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
