<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = limparInput($_POST['nome']);
    $email = limparInput($_POST['email']);
    $mensagem = limparInput($_POST['mensagem']);
    
    // Validações
    $erros = [];
    
    if (strlen($nome) < 3) {
        $erros[] = "O nome deve ter pelo menos 3 caracteres.";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Email inválido.";
    }
    
    if (strlen($mensagem) < 10) {
        $erros[] = "A mensagem deve ter pelo menos 10 caracteres.";
    }
    
    if (empty($erros)) {
        try {
            $pdo = conectarDB();
            
            $stmt = $pdo->prepare("INSERT INTO contatos (nome, email, mensagem) VALUES (?, ?, ?)");
            $stmt->execute([$nome, $email, $mensagem]);
            
            // Enviar email de confirmação (implementar depois)
            
            $resposta = [
                'status' => 'success',
                'mensagem' => 'Mensagem enviada com sucesso! Em breve entraremos em contato.'
            ];
        } catch (PDOException $e) {
            $resposta = [
                'status' => 'error',
                'mensagem' => 'Erro ao enviar mensagem. Por favor, tente novamente.'
            ];
        }
    } else {
        $resposta = [
            'status' => 'error',
            'mensagem' => implode("\n", $erros)
        ];
    }
    
    // Retornar resposta em JSON
    header('Content-Type: application/json');
    echo json_encode($resposta);
    exit;
} else {
    // Acesso direto ao arquivo não permitido
    header("Location: " . SITE_URL . "/index.php");
    exit;
} 