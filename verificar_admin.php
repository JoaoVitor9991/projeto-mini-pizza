<?php
require_once 'includes/config.php';

try {
    $pdo = conectarDB();
    
    // Verificar se o admin existe
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = 'admin@pizzariaamatsu.com'");
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        // Criar senha aleatória
        $nova_senha = bin2hex(random_bytes(8));
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        
        // Criar admin
        $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE email = 'admin@pizzariaamatsu.com'");
        $stmt->execute([$senha_hash]);
        
        if ($stmt->rowCount() === 0) {
            // Inserir novo admin
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
            $stmt->execute(['Administrador', 'admin@pizzariaamatsu.com', $senha_hash, 'admin']);
        }
        
        echo "Admin criado com sucesso!<br>";
        echo "Email: admin@pizzariaamatsu.com<br>";
        echo "Senha: " . $nova_senha;
    } else {
        echo "Admin já existe.";
    }
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
} 