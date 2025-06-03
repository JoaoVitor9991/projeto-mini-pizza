<?php
require_once 'includes/config.php';

try {
    $pdo = conectarDB();
    
    // Primeiro, excluir as tabelas na ordem correta (por causa das chaves estrangeiras)
    $pdo->exec("DROP TABLE IF EXISTS logs_pedidos");
    $pdo->exec("DROP TABLE IF EXISTS itens_pedido");
    $pdo->exec("DROP TABLE IF EXISTS pedidos");
    
    // Criar tabela de pedidos com a nova estrutura
    $sql_pedidos = "CREATE TABLE pedidos (
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
    echo "Tabela 'pedidos' recriada com sucesso!<br>";
    
    // Recriar tabela de itens do pedido
    $sql_itens_pedido = "CREATE TABLE itens_pedido (
        id INT AUTO_INCREMENT PRIMARY KEY,
        pedido_id INT,
        produto_id INT,
        quantidade INT NOT NULL,
        preco_unitario DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
        FOREIGN KEY (produto_id) REFERENCES produtos(id)
    )";
    $pdo->exec($sql_itens_pedido);
    echo "Tabela 'itens_pedido' recriada com sucesso!<br>";
    
    // Recriar tabela de logs
    $sql_logs_pedidos = "CREATE TABLE logs_pedidos (
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
    echo "Tabela 'logs_pedidos' recriada com sucesso!<br>";
    
    echo "<br>Todas as tabelas foram atualizadas com sucesso!<br>";
    echo "<br><a href='admin/dashboard.php'>Voltar para o Dashboard</a>";
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
} 