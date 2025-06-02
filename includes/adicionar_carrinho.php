<?php
require_once 'config.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'mensagem' => 'Você precisa estar logado para adicionar produtos ao carrinho.'
    ]);
    exit;
}

// Receber e decodificar os dados JSON
$dados = json_decode(file_get_contents('php://input'), true);

if (!isset($dados['produto_id']) || !is_numeric($dados['produto_id'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'mensagem' => 'Produto inválido.'
    ]);
    exit;
}

$produto_id = (int) $dados['produto_id'];

try {
    $pdo = conectarDB();
    
    // Verificar se o produto existe e está ativo
    $stmt = $pdo->prepare("SELECT id, nome, preco FROM produtos WHERE id = ? AND ativo = 1");
    $stmt->execute([$produto_id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$produto) {
        throw new Exception('Produto não encontrado ou indisponível.');
    }
    
    // Inicializar o carrinho se não existir
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }
    
    // Adicionar ou atualizar produto no carrinho
    if (isset($_SESSION['carrinho'][$produto_id])) {
        $_SESSION['carrinho'][$produto_id]['quantidade']++;
    } else {
        $_SESSION['carrinho'][$produto_id] = [
            'nome' => $produto['nome'],
            'preco' => $produto['preco'],
            'quantidade' => 1
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'mensagem' => 'Produto adicionado ao carrinho com sucesso!',
        'total_itens' => array_sum(array_column($_SESSION['carrinho'], 'quantidade'))
    ]);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'mensagem' => $e->getMessage()
    ]);
} 