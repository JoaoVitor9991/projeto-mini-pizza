<?php
require_once 'includes/config.php';

try {
    $pdo = conectarDB();
    
    // Buscar produtos e suas imagens
    $stmt = $pdo->query("
        SELECT id, nome, imagem, categoria_id,
        (SELECT nome FROM categorias WHERE id = produtos.categoria_id) as categoria
        FROM produtos 
        ORDER BY categoria_id, nome
    ");
    
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Status das Imagens dos Produtos:</h2>";
    foreach ($produtos as $produto) {
        echo "ID: " . $produto['id'] . "<br>";
        echo "Nome: " . $produto['nome'] . "<br>";
        echo "Categoria: " . $produto['categoria'] . "<br>";
        echo "Caminho da Imagem: " . ($produto['imagem'] ? $produto['imagem'] : 'Sem imagem') . "<br>";
        
        // Verificar se o arquivo existe
        if ($produto['imagem']) {
            $caminho_completo = "assets/img/" . $produto['imagem'];
            echo "Arquivo existe? " . (file_exists($caminho_completo) ? 'Sim' : 'Não') . "<br>";
            echo "Caminho completo: " . $caminho_completo . "<br>";
        }
        
        echo "-------------------<br>";
    }
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
} 