<?php
require_once '../../includes/config.php';
verificarAdmin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID do pedido inválido.');
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
        die('Pedido não encontrado.');
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
    
} catch (PDOException $e) {
    die('Erro ao carregar pedido: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido #<?php echo $pedido_id; ?> - <?php echo SITE_NAME; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .print-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }
        
        .print-header h1 {
            margin: 0;
            font-size: 24px;
        }
        
        .print-header p {
            margin: 5px 0;
            font-size: 14px;
        }
        
        .info-section {
            margin-bottom: 30px;
        }
        
        .info-section h2 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        
        .info-section p {
            margin: 5px 0;
            font-size: 14px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        .total {
            font-weight: bold;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .status-pendente { background-color: #ffc107; }
        .status-confirmado { background-color: #17a2b8; color: white; }
        .status-em_preparo { background-color: #ff4d4d; color: white; }
        .status-em_entrega { background-color: #6f42c1; color: white; }
        .status-entregue { background-color: #28a745; color: white; }
        .status-cancelado { background-color: #dc3545; color: white; }
        
        .print-footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-header">
        <h1><?php echo SITE_NAME; ?></h1>
        <p>Pedido #<?php echo $pedido_id; ?></p>
        <p>Data: <?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></p>
    </div>

    <div class="info-section">
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

    <div class="info-section">
        <h2>Status do Pedido</h2>
        <span class="status-badge status-<?php echo $pedido['status']; ?>">
            <?php echo ucfirst(str_replace('_', ' ', $pedido['status'])); ?>
        </span>
    </div>

    <div class="info-section">
        <h2>Itens do Pedido</h2>
        <table>
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
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                    <td>R$ <?php echo number_format(isset($pedido['valor_total']) ? $pedido['valor_total'] : 0, 2, ',', '.'); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="print-footer">
        <p>Impresso em <?php echo date('d/m/Y H:i'); ?></p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()">Imprimir</button>
        <button onclick="window.close()">Fechar</button>
    </div>
</body>
</html> 