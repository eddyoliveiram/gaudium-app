<?php
require_once '../config/Database.php';
require_once '../Models/Cidade.php';
$database = new Database();
$pdo = $database->pdo;

$cidadeModel = new Cidade($pdo);

$cidades = $cidadeModel->getAllCidades();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimativa de Tarifa</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <!-- Importando jQuery via CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
<div class="container">
    <div class="box" id="box-left">
        <h2>Calcular Estimativa</h2>
        <form id="calculo-form">
            <label for="cidade">Cidade:</label>
            <select id="cidade" name="cidade_id" required>
                <option value="">Selecione uma cidade</option>
                <?php foreach ($cidades as $cidade): ?>
                    <option value="<?= $cidade['id']; ?>"><?= $cidade['nome']; ?></option>
                <?php endforeach; ?>
            </select>

            <label for="categoria">Categoria:</label>
            <select id="categoria" name="categoria_id" required>
                <option value="">Selecione uma cidade primeiro</option>
            </select>

            <label for="endereco_origem">Endereço de Origem:</label>
            <input type="text" id="endereco_origem" name="endereco_origem" required>

            <label for="endereco_destino">Endereço de Destino:</label>
            <input type="text" id="endereco_destino" name="endereco_destino" required>

            <button type="submit">Calcular Tarifa</button>
        </form>

        <div id="resultado"></div>
    </div>

    <div class="box" id="box-right">
        <h2>Histórico de Cálculos</h2>
        <div id="historico">
            <p>Carregando histórico...</p>
        </div>
        <div id="paginacao"></div>
    </div>
</div>

<script src="../assets/js/script.js"></script>
</body>

</html>
