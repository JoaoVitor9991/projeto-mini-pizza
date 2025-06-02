<?php
require_once '../includes/config.php';
verificarAdmin();

// Buscar estatísticas
try {
    $pdo = conectarDB();
    
    // Total de pedidos pendentes
    $stmt = $pdo->query("SELECT COUNT(*) FROM pedidos WHERE status = 'pendente'");
    $pedidos_pendentes = $stmt->fetchColumn();
    
    // Total de pedidos hoje
    $stmt = $pdo->query("SELECT COUNT(*) FROM pedidos WHERE DATE(data_pedido) = CURDATE()");
    $pedidos_hoje = $stmt->fetchColumn();
    
    // Total de produtos
    $stmt = $pdo->query("SELECT COUNT(*) FROM produtos WHERE ativo = 1");
    $total_produtos = $stmt->fetchColumn();
    
    // Total de mensagens não lidas
    $stmt = $pdo->query("SELECT COUNT(*) FROM contatos WHERE status = 'novo'");
    $mensagens_novas = $stmt->fetchColumn();
    
} catch (PDOException $e) {
    $erro = "Erro ao carregar estatísticas: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- Menu Lateral -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Conteúdo Principal -->
        <main class="admin-content">
            <div class="admin-header">
                <h1>Dashboard</h1>
                <div class="user-info">
                    <span>Bem-vindo, <?php echo $_SESSION['usuario_nome']; ?></span>
                    <a href="../includes/logout.php" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a>
                </div>
            </div>

            <?php if (isset($erro)): ?>
                <div class="alert alert-error"><?php echo $erro; ?></div>
            <?php endif; ?>

            <!-- Cards de Estatísticas -->
            <div class="stats-grid">
                <div class="stats-card">
                    <i class="fas fa-clock"></i>
                    <div class="stats-info">
                        <h3>Pedidos Pendentes</h3>
                        <p><?php echo $pedidos_pendentes; ?></p>
                    </div>
                </div>
                
                <div class="stats-card">
                    <i class="fas fa-shopping-bag"></i>
                    <div class="stats-info">
                        <h3>Pedidos Hoje</h3>
                        <p><?php echo $pedidos_hoje; ?></p>
                    </div>
                </div>
                
                <div class="stats-card">
                    <i class="fas fa-pizza-slice"></i>
                    <div class="stats-info">
                        <h3>Total de Produtos</h3>
                        <p><?php echo $total_produtos; ?></p>
                    </div>
                </div>
                
                <div class="stats-card">
                    <i class="fas fa-envelope"></i>
                    <div class="stats-info">
                        <h3>Mensagens Novas</h3>
                        <p><?php echo $mensagens_novas; ?></p>
                    </div>
                </div>
            </div>

            <!-- Últimos Pedidos -->
            <section class="admin-section">
                <h2>Últimos Pedidos</h2>
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
                            <?php
                            try {
                                $stmt = $pdo->query("
                                    SELECT p.*, u.nome as cliente 
                                    FROM pedidos p 
                                    JOIN usuarios u ON p.usuario_id = u.id 
                                    ORDER BY p.data_pedido DESC 
                                    LIMIT 5
                                ");
                                while ($pedido = $stmt->fetch(PDO::FETCH_ASSOC)):
                            ?>
                                <tr>
                                    <td>#<?php echo $pedido['id']; ?></td>
                                    <td><?php echo htmlspecialchars($pedido['cliente']); ?></td>
                                    <td>R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $pedido['status']; ?>">
                                            <?php echo ucfirst($pedido['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></td>
                                    <td>
                                        <a href="pedidos/visualizar.php?id=<?php echo $pedido['id']; ?>" class="btn-action">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php 
                                endwhile;
                            } catch (PDOException $e) {
                                echo "<tr><td colspan='6'>Erro ao carregar pedidos.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <a href="pedidos/listar.php" class="btn-primary">Ver Todos os Pedidos</a>
            </section>
        </main>
    </div>

    <script src="../assets/js/admin.js"></script>
</body>
</html> 