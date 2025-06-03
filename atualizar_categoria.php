<?php
require_once 'includes/config.php';

try {
    $pdo = conectarDB();
    
    // Atualizar a descrição da categoria
    $stmt = $pdo->prepare("
        UPDATE categorias 
        SET descricao = ? 
        WHERE nome = ?
    ");
    
    $stmt->execute([
        'Pizzas com combinações únicas e ingredientes selecionados',
        'Pizzas Especiais'
    ]);
    
    echo "Descrição da categoria atualizada com sucesso!";
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
} 