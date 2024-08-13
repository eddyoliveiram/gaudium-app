<?php
use PHPUnit\Framework\TestCase;
class CorridaTest extends TestCase
{
    private $databaseMock;
    private $pdoMock;
    private $corridaModel;

    protected function setUp(): void
    {
        // Mock da classe Database
        $this->databaseMock = $this->createMock(Database::class);
        $this->pdoMock = $this->createMock(PDO::class);

        // Configura o mock para retornar o PDO mockado quando getConnection() for chamado
        $this->databaseMock->method('getConnection')
            ->willReturn($this->pdoMock);

        // Instância da classe Corrida usando o mock de Database
        $this->corridaModel = new Corrida($this->databaseMock);
    }

    public function testRegistrarCorrida()
    {
        // Mock de PDOStatement
        $stmtMock = $this->createMock(PDOStatement::class);

        // Espera que o método execute seja chamado uma vez e retorne true
        $stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        // Configura o mock de PDO para retornar o mock de PDOStatement quando prepare() for chamado
        $this->pdoMock->method('prepare')
            ->willReturn($stmtMock);

        // Chama o método a ser testado
        $result = $this->corridaModel->registrarCorrida(1, 1, 'Origem', 'Destino', 10, 10, 45.00);

        // Verifica se o resultado é true, indicando que o registro foi bem-sucedido
        $this->assertTrue($result);
    }
}