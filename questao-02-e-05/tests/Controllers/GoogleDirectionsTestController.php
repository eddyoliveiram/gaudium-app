<?php

require_once '../vendor/autoload.php';
require_once '../../api/Models/Tarifa.php';
require_once '../../api/Models/Corrida.php';
require_once '../../api/Services/Logger.php.php';

class GoogleDirectionsControllerTest extends TestCase
{
    private $pdo;
    private $googleDirectionsController;
    private $tarifaModelMock;
    private $corridaModelMock;
    private $redisMock;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->tarifaModelMock = $this->createMock(Tarifa::class);
        $this->corridaModelMock = $this->createMock(Corrida::class);
        $this->redisMock = $this->createMock(Predis\Client::class);

        $this->googleDirectionsController = $this->getMockBuilder(GoogleDirectionsController::class)
            ->setConstructorArgs([$this->pdo, $this->tarifaModelMock, $this->corridaModelMock, $this->redisMock])
            ->onlyMethods(['obterDistanciaEDuracao'])
            ->getMock();
    }

    public function testCalcularComGoogleDadosInvalidos()
    {
        $_POST['cidade_id'] = null;
        $_POST['categoria_id'] = null;
        $_POST['endereco_origem'] = '';
        $_POST['endereco_destino'] = '';

        $this->expectOutputString(json_encode(['error' => 'Dados inválidos fornecidos']));

        $this->googleDirectionsController->calcularComGoogle();
    }

    public function testCalcularComGoogleDadosCacheados()
    {
        $_POST['cidade_id'] = 1;
        $_POST['categoria_id'] = 1;
        $_POST['endereco_origem'] = 'Origem';
        $_POST['endereco_destino'] = 'Destino';

        $this->redisMock->method('get')
            ->willReturn(json_encode(['distance' => '10 km', 'duration' => '10 min']));

        $this->expectOutputString(json_encode(['distance' => '10 km', 'duration' => '10 min']));

        $this->googleDirectionsController->calcularComGoogle();
    }

    public function testCalcularComGoogleSemCache()
    {
        $_POST['cidade_id'] = 1;
        $_POST['categoria_id'] = 1;
        $_POST['endereco_origem'] = 'Origem';
        $_POST['endereco_destino'] = 'Destino';

        $this->redisMock->method('get')
            ->willReturn(null);

        $this->tarifaModelMock->method('getTarifa')
            ->willReturn(['bandeirada' => 5.00, 'valor_por_km' => 2.00, 'valor_por_hora' => 10.00]);

        $this->corridaModelMock->expects($this->once())
            ->method('registrarCorrida');

        // Simula o retorno do Google Directions API
        $this->googleDirectionsController->method('obterDistanciaEDuracao')
            ->willReturn([10, 10]);

        $this->expectOutputString(json_encode([
            'distance' => '10.00 km',
            'duration' => '10.00 min',
            'cidade_id' => 1,
            'categoria_id' => 1,
            'valor_calculado' => '45.00'
        ]));

        $this->googleDirectionsController->calcularComGoogle();
    }
}
