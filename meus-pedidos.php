<?php
require_once 'includes/config.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    redirecionarComMensagem('login.php', 'Faça login para ver seus pedidos.', 'warning');
}

try {
    $pdo = conectarDB();
    
    // Buscar pedidos do usuário
    $stmt = $pdo->prepare("
        SELECT p.*, 
               COUNT(i.id) as total_itens,
               GROUP_CONCAT(CONCAT(i.quantidade, 'x ', pr.nome) SEPARATOR ', ') as itens,
               p.valor_total as valor_total,
               p.subtotal as subtotal,
               p.taxa_entrega as taxa_entrega
        FROM pedidos p
        JOIN itens_pedido i ON p.id = i.pedido_id
        JOIN produtos pr ON i.produto_id = pr.id
        WHERE p.usuario_id = ?
        GROUP BY p.id
        ORDER BY p.data_pedido DESC
    ");
    
    $stmt->execute([$_SESSION['usuario_id']]);
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
    <title>Meus Pedidos - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Cabeçalho -->
    <?php include 'includes/header.php'; ?>

    <!-- Conteúdo Principal -->
    <main class="container">
        <div class="meus-pedidos">
            <h1>Meus Pedidos</h1>
            
            <?php if (isset($erro)): ?>
                <div class="alert alert-error"><?php echo $erro; ?></div>
            <?php endif; ?>

            <?php if (isset($_SESSION['mensagem'])): ?>
                <div class="alert alert-<?php echo $_SESSION['mensagem']['tipo']; ?>">
                    <?php 
                    echo $_SESSION['mensagem']['texto'];
                    unset($_SESSION['mensagem']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (empty($pedidos)): ?>
                <div class="pedidos-vazio">
                    <i class="fas fa-box-open"></i>
                    <p>Você ainda não fez nenhum pedido</p>
                    <a href="index.php" class="btn-primary">Fazer Pedido</a>
                </div>
            <?php else: ?>
                <div class="pedidos-lista">
                    <?php foreach ($pedidos as $pedido): ?>
                        <div class="pedido-card">
                            <div class="pedido-header">
                                <div class="pedido-info">
                                    <h2>Pedido #<?php echo $pedido['id']; ?></h2>
                                    <p class="pedido-data">
                                        <?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?>
                                    </p>
                                </div>
                                <div class="pedido-status <?php echo $pedido['status']; ?>">
                                    <?php
                                    $status_texto = [
                                        'pendente' => 'Pendente',
                                        'confirmado' => 'Confirmado',
                                        'preparando' => 'Preparando',
                                        'saiu_entrega' => 'Saiu para Entrega',
                                        'entregue' => 'Entregue',
                                        'cancelado' => 'Cancelado'
                                    ];
                                    echo $status_texto[$pedido['status']] ?? $pedido['status'];
                                    ?>
                                </div>
                            </div>
                            
                            <div class="pedido-detalhes">
                                <div class="pedido-itens">
                                    <h3>Itens do Pedido</h3>
                                    <p><?php echo htmlspecialchars($pedido['itens']); ?></p>
                                </div>
                                
                                <div class="pedido-endereco">
                                    <h3>Endereço de Entrega</h3>
                                    <p>
                                        <?php
                                        echo htmlspecialchars($pedido['endereco']) . ', ' . 
                                             htmlspecialchars($pedido['numero']);
                                        
                                        if ($pedido['complemento']) {
                                            echo ' - ' . htmlspecialchars($pedido['complemento']);
                                        }
                                        
                                        echo '<br>' . 
                                             htmlspecialchars($pedido['bairro']) . ', ' . 
                                             htmlspecialchars($pedido['cidade']);
                                        ?>
                                    </p>
                                </div>
                                
                                <div class="pedido-valores">
                                    <div class="valor-item">
                                        <span>Subtotal:</span>
                                        <span>R$ <?php echo number_format($pedido['subtotal'], 2, ',', '.'); ?></span>
                                    </div>
                                    <div class="valor-item">
                                        <span>Taxa de Entrega:</span>
                                        <span>R$ <?php echo number_format($pedido['taxa_entrega'], 2, ',', '.'); ?></span>
                                    </div>
                                    <div class="valor-item total">
                                        <span>Total:</span>
                                        <span>R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($pedido['observacoes']): ?>
                                <div class="pedido-observacoes">
                                    <h3>Observações</h3>
                                    <p><?php echo nl2br(htmlspecialchars($pedido['observacoes'])); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Rodapé -->
    <?php include 'includes/footer.php'; ?>
</body>
</html> 