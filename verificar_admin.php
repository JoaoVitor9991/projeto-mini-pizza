<?php
require_once 'includes/config.php';

try {
    $pdo = conectarDB();
    
    // Verificar se o admin existe
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = 'admin@minipizza.com'");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Nova senha: admin123
    $nova_senha = gerarHash('admin123');
    
    if ($admin) {
        // Atualizar senha do admin existente
        $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE email = 'admin@minipizza.com'");
        $stmt->execute([$nova_senha]);
        echo "Senha do administrador foi atualizada com sucesso!<br>";
    } else {
        // Criar novo usuário admin
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Administrador', 'admin@minipizza.com', $nova_senha, 'admin']);
        echo "Usuário administrador foi criado com sucesso!<br>";
    }
    
    echo "<br>Agora você pode fazer login com:<br>";
    echo "Email: admin@minipizza.com<br>";
    echo "Senha: admin123<br>";
    echo "<br><a href='login.php'>Ir para a página de login</a>";
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
} 