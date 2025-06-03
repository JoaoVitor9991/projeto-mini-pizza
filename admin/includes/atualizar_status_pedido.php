<?php
require_once '../../includes/config.php';
verificarAdmin();

// Receber dados do POST
$dados = json_decode(file_get_contents('php://input'), true);

if (!isset($dados['pedido_id']) || !isset($dados['status'])) {
    echo json_encode([
        'status' => 'error',
        'mensagem' => 'Dados inválidos'
    ]);
    exit;
}

$pedido_id = (int) $dados['pedido_id'];
$novo_status = $dados['status'];

// Validar status
$status_validos = ['pendente', 'confirmado', 'em_preparo', 'em_entrega', 'entregue', 'cancelado'];
if (!in_array($novo_status, $status_validos)) {
    echo json_encode([
        'status' => 'error',
        'mensagem' => 'Status inválido'
    ]);
    exit;
}

try {
    $pdo = conectarDB();
    $pdo->beginTransaction();
    
    // Buscar status atual
    $stmt = $pdo->prepare("SELECT status FROM pedidos WHERE id = ?");
    $stmt->execute([$pedido_id]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pedido) {
        throw new Exception('Pedido não encontrado');
    }
    
    $status_anterior = $pedido['status'];
    
    // Atualizar status
    $stmt = $pdo->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
    $stmt->execute([$novo_status, $pedido_id]);
    
    // Registrar log
    $stmt = $pdo->prepare("
        INSERT INTO logs_pedidos (
            pedido_id, status_anterior, status_novo, usuario_id
        ) VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $pedido_id,
        $status_anterior,
        $novo_status,
        $_SESSION['usuario_id']
    ]);
    
    $pdo->commit();
    
    echo json_encode([
        'status' => 'success',
        'mensagem' => 'Status atualizado com sucesso'
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'status' => 'error',
        'mensagem' => 'Erro ao atualizar status: ' . $e->getMessage()
    ]);
} 