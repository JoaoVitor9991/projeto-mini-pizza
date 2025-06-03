<?php
require_once '../../includes/config.php';
verificarAdmin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirecionarComMensagem('listar.php', 'ID do pedido inválido.', 'error');
}

$pedido_id = (int) $_GET['id'];

try {
    $pdo = conectarDB();
    
    // Buscar informações do pedido
    $stmt = $pdo->prepare("
        SELECT p.*, u.nome as cliente, u.email as cliente_email
        FROM pedidos p 
        JOIN usuarios u ON p.usuario_id = u.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$pedido_id]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pedido) {
        redirecionarComMensagem('listar.php', 'Pedido não encontrado.', 'error');
    }
    
    // Buscar itens do pedido
    $stmt = $pdo->prepare("
        SELECT i.*, p.nome as produto_nome
        FROM itens_pedido i
        JOIN produtos p ON i.produto_id = p.id
        WHERE i.pedido_id = ?
    ");
    $stmt->execute([$pedido_id]);
    $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar histórico de status
    $stmt = $pdo->prepare("
        SELECT l.*, u.nome as usuario_nome
        FROM logs_pedidos l
        JOIN usuarios u ON l.usuario_id = u.id
        WHERE l.pedido_id = ?
        ORDER BY l.data_alteracao DESC
    ");
    $stmt->execute([$pedido_id]);
    $historico = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    redirecionarComMensagem('listar.php', 'Erro ao carregar pedido: ' . $e->getMessage(), 'error');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Pedido #<?php echo $pedido_id; ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- Menu Lateral -->
        <?php include '../includes/sidebar.php'; ?>

        <!-- Conteúdo Principal -->
        <main class="admin-content">
            <div class="admin-header">
                <h1>Pedido #<?php echo $pedido_id; ?></h1>
                <div class="header-actions">
                    <a href="listar.php" class="btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <button onclick="imprimirPedido(<?php echo $pedido_id; ?>)" class="btn-primary">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                </div>
            </div>

            <!-- Informações do Pedido -->
            <div class="pedido-info">
                <div class="card">
                    <h2>Informações do Cliente</h2>
                    <p><strong>Nome:</strong> <?php echo htmlspecialchars($pedido['cliente']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($pedido['cliente_email']); ?></p>
                    <p><strong>Endereço de Entrega:</strong><br>
                        <?php 
                        echo htmlspecialchars($pedido['endereco']) . ', ' . 
                             htmlspecialchars($pedido['numero']);
                        
                        if (!empty($pedido['complemento'])) {
                            echo ' - ' . htmlspecialchars($pedido['complemento']);
                        }
                        
                        echo '<br>' . 
                             htmlspecialchars($pedido['bairro']) . ', ' . 
                             htmlspecialchars($pedido['cidade']) . '<br>' .
                             'Tel: ' . htmlspecialchars($pedido['telefone']);
                        
                        if (!empty($pedido['observacoes'])) {
                            echo '<br><br><strong>Observações:</strong><br>' . 
                                 nl2br(htmlspecialchars($pedido['observacoes']));
                        }
                        ?>
                    </p>
                </div>

                <div class="card">
                    <h2>Status do Pedido</h2>
                    <div class="status-atual">
                        <span class="status-badge status-<?php echo $pedido['status']; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $pedido['status'])); ?>
                        </span>
                    </div>
                    <div class="status-update">
                        <select id="novoStatus" class="status-select">
                            <option value="pendente" <?php echo $pedido['status'] === 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                            <option value="confirmado" <?php echo $pedido['status'] === 'confirmado' ? 'selected' : ''; ?>>Confirmado</option>
                            <option value="em_preparo" <?php echo $pedido['status'] === 'em_preparo' ? 'selected' : ''; ?>>Em Preparo</option>
                            <option value="em_entrega" <?php echo $pedido['status'] === 'em_entrega' ? 'selected' : ''; ?>>Em Entrega</option>
                            <option value="entregue" <?php echo $pedido['status'] === 'entregue' ? 'selected' : ''; ?>>Entregue</option>
                            <option value="cancelado" <?php echo $pedido['status'] === 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                        </select>
                        <button onclick="atualizarStatusPedido(<?php echo $pedido_id; ?>, document.getElementById('novoStatus').value)" class="btn-primary">
                            Atualizar Status
                        </button>
                    </div>
                </div>
            </div>

            <!-- Itens do Pedido -->
            <div class="card">
                <h2>Itens do Pedido</h2>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Preço Unitário</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($itens as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['produto_nome']); ?></td>
                                    <td><?php echo $item['quantidade']; ?></td>
                                    <td>R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?></td>
                                    <td>R$ <?php echo number_format($item['quantidade'] * $item['preco_unitario'], 2, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="total">
                                <td colspan="3"><strong>Total</strong></td>
                                <td><strong>R$ <?php echo number_format(isset($pedido['valor_total']) ? $pedido['valor_total'] : 0, 2, ',', '.'); ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Histórico de Status -->
            <div class="card">
                <h2>Histórico de Status</h2>
                <div class="timeline">
                    <?php foreach ($historico as $log): ?>
                        <div class="timeline-item">
                            <div class="timeline-badge status-<?php echo $log['status_novo']; ?>">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="timeline-content">
                                <h3>
                                    <?php echo ucfirst(str_replace('_', ' ', $log['status_novo'])); ?>
                                </h3>
                                <p>
                                    Alterado por <?php echo htmlspecialchars($log['usuario_nome']); ?><br>
                                    em <?php echo date('d/m/Y H:i', strtotime($log['data_alteracao'])); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>

    <script src="../../assets/js/admin.js"></script>
    <script>
        function imprimirPedido(pedidoId) {
            window.open(`imprimir.php?id=${pedidoId}`, '_blank');
        }
    </script>
</body>
</html> 