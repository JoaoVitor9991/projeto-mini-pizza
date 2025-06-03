<?php
require_once 'config.php';

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'mensagem' => 'Método não permitido']);
    exit;
}

// Obter dados da requisição
$json = file_get_contents('php://input');
$dados = json_decode($json, true);

if (!isset($dados['produto_id']) || !is_numeric($dados['produto_id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'mensagem' => 'ID do produto inválido']);
    exit;
}

$produto_id = (int) $dados['produto_id'];
$quantidade = isset($dados['quantidade']) ? (int) $dados['quantidade'] : 1;

if ($quantidade < 1) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'mensagem' => 'Quantidade inválida']);
    exit;
}

try {
    // Adicionar ao carrinho
    adicionarAoCarrinho($produto_id, $quantidade);
    
    // Retornar resposta de sucesso
    echo json_encode([
        'status' => 'success',
        'mensagem' => 'Produto adicionado ao carrinho',
        'total_itens' => contarItensCarrinho(),
        'total_valor' => calcularTotalCarrinho()
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'mensagem' => $e->getMessage()
    ]);
} 