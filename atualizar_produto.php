<?php
require_once 'includes/config.php';

try {
    $pdo = conectarDB();
    
    // Atualizar o nome do produto
    $stmt = $pdo->prepare("
        UPDATE produtos 
        SET nome = 'Guaraná Antarctica 2L', 
            descricao = 'Refrigerante Guaraná Antarctica 2 litros'
        WHERE nome LIKE '%Guaran%Antarctica%'
    ");
    $stmt->execute();
    
    echo "Nome do produto atualizado com sucesso!<br>";
    echo "<br><a href='admin/produtos/listar.php'>Voltar para a lista de produtos</a>";
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
} 