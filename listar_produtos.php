<?php
require_once 'includes/config.php';

try {
    $pdo = conectarDB();
    
    // Buscar produtos
    $stmt = $pdo->query("
        SELECT p.*, c.nome as categoria_nome 
        FROM produtos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        ORDER BY c.nome, p.nome
    ");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Lista de Produtos:</h2>";
    foreach ($produtos as $produto) {
        echo "ID: " . $produto['id'] . "<br>";
        echo "Nome: " . $produto['nome'] . "<br>";
        echo "Categoria: " . $produto['categoria_nome'] . "<br>";
        echo "Imagem: " . ($produto['imagem'] ? $produto['imagem'] : 'Sem imagem') . "<br>";
        echo "-------------------<br>";
    }
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
} 