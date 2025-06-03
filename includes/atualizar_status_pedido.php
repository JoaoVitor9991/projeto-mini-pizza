<?php
require_once 'config.php';
verificarAdmin();

// Receber e decodificar os dados JSON
$dados = json_decode(file_get_contents('php://input'), true);

if (!isset($dados['pedido_id']) || !isset($dados['status'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'mensagem' => 'Dados inválidos.'
    ]);
    exit;
}

$pedido_id = (int) $dados['pedido_id'];
$status = $dados['status'];

// Validar status
$status_validos = ['pendente', 'confirmado', 'em_preparo', 'em_entrega', 'entregue', 'cancelado'];
if (!in_array($status, $status_validos)) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'mensagem' => 'Status inválido.'
    ]);
    exit;
}

try {
    $pdo = conectarDB();
    
    // Verificar se o pedido existe
    $stmt = $pdo->prepare("SELECT id FROM pedidos WHERE id = ?");
    $stmt->execute([$pedido_id]);
    
    if (!$stmt->fetch()) {
        throw new Exception('Pedido não encontrado.');
    }
    
    // Atualizar status
    $stmt = $pdo->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
    $stmt->execute([$status, $pedido_id]);
    
    // Registrar log da alteração (opcional)
    $stmt = $pdo->prepare("
        INSERT INTO logs_pedidos (pedido_id, status_anterior, status_novo, usuario_id) 
        VALUES (?, (SELECT status FROM pedidos WHERE id = ?), ?, ?)
    ");
    $stmt->execute([
        $pedido_id,
        $pedido_id,
        $status,
        $_SESSION['usuario_id']
    ]);
    
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'mensagem' => 'Status atualizado com sucesso!'
    ]);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'mensagem' => $e->getMessage()
    ]);
} 