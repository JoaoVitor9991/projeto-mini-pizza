<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

try {
    $pdo = conectarDB();
    
    // Nova senha: admin123
    $nova_senha = gerarHash('admin123');
    
    // Atualizar senha do admin
    $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE email = 'admin@pizzariaamatsu.com'");
    $stmt->execute([$nova_senha]);
    
    echo "Senha do administrador atualizada com sucesso!";
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
} 