<?php
require_once 'includes/config.php';

// Array com os nomes das imagens para cada produto
$imagens = [
    'Pizza Margherita' => 'pizza-marguerita.jpg',
    'Pizza Calabresa' => 'calabresa.webp',
    'Pizza Portuguesa' => 'pizza-portuguesa.jpg',
    'Pizza 4 Queijos' => 'Pizza-Quatro-Queijos.jpg',
    'Pizza Frango com Catupiry' => 'frango-com-catupiry.jpg',
    'Pizza de Chocolate' => 'Pizza-de-chocolate-com-confete.jpg',
    'Pizza de Banana' => 'pizza-doce-banana-receita.webp',
    'Suco de Laranja' => 'suco-laranja.jpeg',
    'Guaraná' => 'guarana-2l.jpg',
    'Coca-Cola' => 'coca-cola-2l.jpg'
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