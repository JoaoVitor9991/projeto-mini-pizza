<?php
require_once 'includes/config.php';

try {
    $pdo = conectarDB();
    
    // Criar tabela de contatos
    $sql_contatos = "CREATE TABLE IF NOT EXISTS contatos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        mensagem TEXT NOT NULL,
        data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('novo', 'lido', 'respondido') DEFAULT 'novo'
    )";
    $pdo->exec($sql_contatos);
    echo "Tabela 'contatos' criada/verificada com sucesso!<br>";

    // Criar tabela de categorias
    $sql_categorias = "CREATE TABLE IF NOT EXISTS categorias (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(50) NOT NULL,
        descricao TEXT
    )";
    $pdo->exec($sql_categorias);
    echo "Tabela 'categorias' criada/verificada com sucesso!<br>";

    // Criar tabela de produtos
    $sql_produtos = "CREATE TABLE IF NOT EXISTS produtos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        categoria_id INT,
        nome VARCHAR(100) NOT NULL,
        descricao TEXT,
        preco DECIMAL(10,2) NOT NULL,
        imagem VARCHAR(255),
        ativo BOOLEAN DEFAULT TRUE,
        FOREIGN KEY (categoria_id) REFERENCES categorias(id)
    )";
    $pdo->exec($sql_produtos);
    echo "Tabela 'produtos' criada/verificada com sucesso!<br>";

    // Criar tabela de pedidos
    $sql_pedidos = "CREATE TABLE IF NOT EXISTS pedidos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT,
        status ENUM('pendente', 'confirmado', 'em_preparo', 'em_entrega', 'entregue', 'cancelado') DEFAULT 'pendente',
        subtotal DECIMAL(10,2) NOT NULL,
        taxa_entrega DECIMAL(10,2) NOT NULL,
        valor_total DECIMAL(10,2) NOT NULL,
        data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        endereco VARCHAR(255) NOT NULL,
        numero VARCHAR(20) NOT NULL,
        complemento VARCHAR(100),
        bairro VARCHAR(100) NOT NULL,
        cidade VARCHAR(100) NOT NULL,
        telefone VARCHAR(20) NOT NULL,
        observacoes TEXT,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    )";
    $pdo->exec($sql_pedidos);
    echo "Tabela 'pedidos' criada/verificada com sucesso!<br>";

    // Criar tabela de itens do pedido
    $sql_itens_pedido = "CREATE TABLE IF NOT EXISTS itens_pedido (
        id INT AUTO_INCREMENT PRIMARY KEY,
        pedido_id INT,
        produto_id INT,
        quantidade INT NOT NULL,
        preco_unitario DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
        FOREIGN KEY (produto_id) REFERENCES produtos(id)
    )";
    $pdo->exec($sql_itens_pedido);
    echo "Tabela 'itens_pedido' criada/verificada com sucesso!<br>";

    // Criar tabela de logs de pedidos
    $sql_logs_pedidos = "CREATE TABLE IF NOT EXISTS logs_pedidos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        pedido_id INT,
        status_anterior VARCHAR(20) NOT NULL,
        status_novo VARCHAR(20) NOT NULL,
        usuario_id INT,
        data_alteracao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    )";
    $pdo->exec($sql_logs_pedidos);
    echo "Tabela 'logs_pedidos' criada/verificada com sucesso!<br>";

    echo "<br>Todas as tabelas foram criadas/verificadas com sucesso!<br>";
    echo "<br><a href='admin/dashboard.php'>Voltar para o Dashboard</a>";

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
} 