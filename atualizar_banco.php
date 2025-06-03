<?php
require_once 'includes/config.php';

// Array com os nomes das imagens para cada produto
$imagens = [
    'Pizza Margherita' => 'pizza-margherita.jpg',
    'Pizza Calabresa' => 'pizza-calabresa.jpg',
    'Pizza Portuguesa' => 'pizza-portuguesa.jpg',
    'Pizza 4 Queijos' => 'pizza-4-queijos.jpg',
    'Pizza Frango com Catupiry' => 'pizza-frango-catupiry.jpg',
    'Pizza de Chocolate' => 'pizza-chocolate.jpg',
    'Pizza de Banana' => 'pizza-banana.jpg',
    'Suco de Laranja' => 'suco-laranja.jpg'
];

try {
    $pdo = conectarDB();
    
    // Atualizar cada produto
    foreach ($imagens as $nome => $imagem) {
        $stmt = $pdo->prepare("
            UPDATE produtos 
            SET imagem = ?
            WHERE nome LIKE ?
        ");
        
        $stmt->execute([$imagem, $nome]);
        echo "Atualizado: " . $nome . " -> " . $imagem . "<br>";
    }
    
    echo "<br>Banco de dados atualizado com sucesso!<br>";
    echo "<a href='index.php'>Voltar para a página inicial</a>";
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
} 