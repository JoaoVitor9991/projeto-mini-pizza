<?php
require_once '../../includes/config.php';
verificarAdmin();

// Filtros
$status = isset($_GET['status']) ? $_GET['status'] : '';
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';

try {
    $pdo = conectarDB();
    
    // Construir a query base
    $query = "
        SELECT p.*, u.nome as cliente 
        FROM pedidos p 
        JOIN usuarios u ON p.usuario_id = u.id 
        WHERE 1=1
    ";
    $params = [];
    
    // Adicionar filtros
    if ($status) {
        $query .= " AND p.status = ?";
        $params[] = $status;
    }
    
    if ($data_inicio) {
        $query .= " AND DATE(p.data_pedido) >= ?";
        $params[] = $data_inicio;
    }
    
    if ($data_fim) {
        $query .= " AND DATE(p.data_pedido) <= ?";
        $params[] = $data_fim;
    }
    
    $query .= " ORDER BY p.data_pedido DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $erro = "Erro ao carregar pedidos: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Pedidos - <?php echo SITE_NAME; ?></title>
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
                <h1>Gerenciar Pedidos</h1>
            </div>

            <?php if (isset($erro)): ?>
                <div class="alert alert-error"><?php echo $erro; ?></div>
            <?php endif; ?>

            <!-- Filtros -->
            <div class="admin-filters">
                <form method="GET" class="form-filters">
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select name="status" id="status">
                            <option value="">Todos</option>
                            <option value="pendente" <?php echo $status === 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                            <option value="confirmado" <?php echo $status === 'confirmado' ? 'selected' : ''; ?>>Confirmado</option>
                            <option value="em_preparo" <?php echo $status === 'em_preparo' ? 'selected' : ''; ?>>Em Preparo</option>
                            <option value="em_entrega" <?php echo $status === 'em_entrega' ? 'selected' : ''; ?>>Em Entrega</option>
                            <option value="entregue" <?php echo $status === 'entregue' ? 'selected' : ''; ?>>Entregue</option>
                            <option value="cancelado" <?php echo $status === 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="data_inicio">Data Início:</label>
                        <input type="date" name="data_inicio" id="data_inicio" value="<?php echo $data_inicio; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="data_fim">Data Fim:</label>
                        <input type="date" name="data_fim" id="data_fim" value="<?php echo $data_fim; ?>">
                    </div>
                    
                    <button type="submit" class="btn-primary">Filtrar</button>
                    <a href="listar.php" class="btn-secondary">Limpar</a>
                </form>
            </div>

            <!-- Campo de Busca -->
            <div class="admin-search">
                <input type="text" id="busca" placeholder="Buscar pedidos..." class="search-input">
            </div>

            <!-- Tabela de Pedidos -->
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pedidos)): ?>
                            <?php foreach ($pedidos as $pedido): ?>
                                <tr id="pedido-<?php echo $pedido['id']; ?>">
                                    <td>#<?php echo $pedido['id']; ?></td>
                                    <td><?php echo htmlspecialchars($pedido['cliente']); ?></td>
                                    <td>R$ <?php echo number_format(isset($pedido['valor_total']) ? $pedido['valor_total'] : 0, 2, ',', '.'); ?></td>
                                    <td>
                                        <select onchange="atualizarStatusPedido(<?php echo $pedido['id']; ?>, this.value)" class="status-select">
                                            <option value="pendente" <?php echo $pedido['status'] === 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                                            <option value="confirmado" <?php echo $pedido['status'] === 'confirmado' ? 'selected' : ''; ?>>Confirmado</option>
                                            <option value="em_preparo" <?php echo $pedido['status'] === 'em_preparo' ? 'selected' : ''; ?>>Em Preparo</option>
                                            <option value="em_entrega" <?php echo $pedido['status'] === 'em_entrega' ? 'selected' : ''; ?>>Em Entrega</option>
                                            <option value="entregue" <?php echo $pedido['status'] === 'entregue' ? 'selected' : ''; ?>>Entregue</option>
                                            <option value="cancelado" <?php echo $pedido['status'] === 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                        </select>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></td>
                                    <td>
                                        <a href="visualizar.php?id=<?php echo $pedido['id']; ?>" class="btn-action" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" onclick="imprimirPedido(<?php echo $pedido['id']; ?>)" class="btn-action" title="Imprimir">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Nenhum pedido encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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