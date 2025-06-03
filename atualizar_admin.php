<?php
require_once 'includes/config.php';

try {
    $pdo = conectarDB();
    
    // Nova senha: admin123
    $nova_senha = gerarHash('admin123');
    
    // Atualizar a senha do admin
    $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE email = 'admin@minipizza.com'");
    $stmt->execute([$nova_senha]);
    
    echo "Senha do admin atualizada com sucesso!";
    
} catch (PDOException $e) {
    echo "Erro ao atualizar senha: " . $e->getMessage();
} 