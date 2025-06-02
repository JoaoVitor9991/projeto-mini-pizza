<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = limparInput($_POST['email']);
    $senha = $_POST['senha'];
    
    try {
        $pdo = conectarDB();
        
        // Buscar usuário pelo email
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario && verificarSenha($senha, $usuario['senha'])) {
            // Login bem-sucedido
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_tipo'] = $usuario['tipo'];
            
            // Redirecionar com base no tipo de usuário
            if ($usuario['tipo'] === 'admin') {
                redirecionarComMensagem(SITE_URL . '/admin/dashboard.php', 'Bem-vindo ao painel administrativo!');
            } else {
                redirecionarComMensagem(SITE_URL . '/index.php', 'Login realizado com sucesso!');
            }
        } else {
            // Login falhou
            redirecionarComMensagem(SITE_URL . '/login.php', 'Email ou senha incorretos!', 'error');
        }
    } catch (PDOException $e) {
        redirecionarComMensagem(SITE_URL . '/login.php', 'Erro ao realizar login. Tente novamente.', 'error');
    }
} else {
    // Acesso direto ao arquivo não permitido
    header("Location: " . SITE_URL . "/login.php");
    exit;
} 