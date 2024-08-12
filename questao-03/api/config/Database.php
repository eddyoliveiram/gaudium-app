<?php
class Database {
    private $host = 'localhost';
    private $dbname = 'gaudium_app';
    private $user = 'root';
    private $password = '';
    public $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->user, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            Logger::logMessage('error', 'Erro de conexão com o banco de dados: ' . $e->getMessage());
            die(json_encode(['error' => 'Erro de conexão com o banco de dados']));
        }
    }
}
