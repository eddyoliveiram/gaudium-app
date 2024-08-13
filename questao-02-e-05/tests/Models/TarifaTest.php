<?php
use PHPUnit\Framework\TestCase;
class TarifaTest extends TestCase
{
    private $databaseMock;
    private $pdoMock;
    private $tarifaModel;

    protected function setUp(): void
    {
        // Mock da classe Database
        $this->databaseMock = $this->createMock(Database::class);
        $this->pdoMock = $this->createMock(PDO::class);

        // Configura o mock para retornar o PDO mockado quando getConnection() for chamado
        $this->databaseMock->method('getConnection')
            ->willReturn($this->pdoMock);

        // Instância da classe Tarifa usando o mock de Database
        $this->tarifaModel = new Tarifa($this->databaseMock);
    }

    public function testGetTarifa()
    {
        // Mock de PDOStatement
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('fetch')
            ->willReturn(['bandeirada' => 5.00, 'valor_por_km' => 2.00, 'valor_por_hora' => 10.00]);

        // Configura o mock de PDO para retornar o mock de PDOStatement quando prepare() for chamado
        $this->pdoMock->method('prepare')
            ->willReturn($stmtMock);

        // Chama o método a ser testado
        $result = $this->tarifaModel->getTarifa(1, 1);

        // Verifica se o resultado é o esperado
        $this->assertEquals(['bandeirada' => 5.00, 'valor_por_km' => 2.00, 'valor_por_hora' => 10.00], $result);
    }
}