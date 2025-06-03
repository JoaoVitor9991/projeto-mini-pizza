<?php
require_once 'includes/config.php';

// Criar pasta de uploads se não existir
$upload_dir = 'assets/img/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

try {
    $pdo = conectarDB();
    
    // Atualizar imagem da Coca-Cola
    if (copy('assets/img/coca-cola.webp', 'assets/img/coca-cola-2l.jpg')) {
        $stmt = $pdo->prepare("
            UPDATE produtos 
            SET imagem = 'coca-cola-2l.jpg'
            WHERE nome LIKE '%Coca-Cola%'
        ");
        $stmt->execute();
        echo "Imagem da Coca-Cola atualizada com sucesso!<br>";
    }
    
    // Atualizar imagem do Guaraná
    if (copy('assets/img/guarana.jpeg', 'assets/img/guarana-2l.jpg')) {
        $stmt = $pdo->prepare("
            UPDATE produtos 
            SET imagem = 'guarana-2l.jpg'
            WHERE nome LIKE '%Guaraná%'
        ");
        $stmt->execute();
        echo "Imagem do Guaraná atualizada com sucesso!<br>";
    }
    
    echo "<br><a href='admin/produtos/listar.php'>Voltar para a lista de produtos</a>";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
} 