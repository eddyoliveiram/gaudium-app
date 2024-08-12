CREATE DATABASE gaudium_app;

USE gaudium_app;

CREATE TABLE cidades (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE categorias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE tarifas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cidade_id INT UNSIGNED NOT NULL,
    categoria_id INT UNSIGNED NOT NULL,
    bandeirada DECIMAL(10, 2) NOT NULL,
    valor_por_km DECIMAL(10, 2) NOT NULL,
    valor_por_hora DECIMAL(10, 2) NOT NULL,
    UNIQUE (cidade_id, categoria_id),
    FOREIGN KEY (cidade_id) REFERENCES cidades(id),
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

CREATE TABLE corridas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cidade_id INT UNSIGNED NOT NULL,
    categoria_id INT UNSIGNED NOT NULL,
    endereco_origem VARCHAR(200) NOT NULL,
    endereco_destino VARCHAR(200) NOT NULL,
    distancia DECIMAL(10, 2) NOT NULL,
    duracao DECIMAL(10, 2) NOT NULL,
    tarifa_calculada DECIMAL(10, 2) NOT NULL,
    data_hora_corrida DATETIME NOT NULL,
    FOREIGN KEY (cidade_id) REFERENCES cidades(id),
    FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    INDEX (cidade_id),
    INDEX (categoria_id),
    INDEX (data_hora_corrida)
);
