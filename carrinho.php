<?php
require_once 'includes/config.php';

// Processar ações do carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        $produto_id = isset($_POST['produto_id']) ? (int)$_POST['produto_id'] : 0;
        
        switch ($_POST['acao']) {
            case 'atualizar':
                $quantidade = isset($_POST['quantidade']) ? (int)$_POST['quantidade'] : 0;
                if ($quantidade > 0) {
                    foreach ($_SESSION['carrinho'] as &$item) {
                        if ($item['id'] == $produto_id) {
                            $item['quantidade'] = $quantidade;
                            break;
                        }
                    }
                }
                break;
                
            case 'remover':
                foreach ($_SESSION['carrinho'] as $key => $item) {
                    if ($item['id'] == $produto_id) {
                        unset($_SESSION['carrinho'][$key]);
                        break;
                    }
                }
                $_SESSION['carrinho'] = array_values($_SESSION['carrinho']); // Reindexar array
                break;
        }
        
        // Redirecionar para evitar reenvio do formulário
        header('Location: carrinho.php');
        exit;
    }
}

// Calcular totais
$subtotal = 0;
$total_itens = 0;

if (isset($_SESSION['carrinho'])) {
    foreach ($_SESSION['carrinho'] as $item) {
        $subtotal += $item['preco'] * $item['quantidade'];
        $total_itens += $item['quantidade'];
    }
}

// Taxa de entrega fixa (você pode modificar isso conforme sua necessidade)
$taxa_entrega = 5.00;
$total = $subtotal + $taxa_entrega;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Cabeçalho -->
    <?php include 'includes/header.php'; ?>

    <!-- Conteúdo Principal -->
    <main class="container">
        <div class="carrinho-container">
            <h1>Seu Carrinho</h1>
            
            <?php if (empty($_SESSION['carrinho'])): ?>
                <div class="carrinho-vazio">
                    <i class="fas fa-shopping-cart"></i>
                    <p>Seu carrinho está vazio</p>
                    <a href="index.php" class="btn-primary">Continuar Comprando</a>
                </div>
            <?php else: ?>
                <div class="carrinho-grid">
                    <!-- Lista de Produtos -->
                    <div class="carrinho-produtos">
                        <?php foreach ($_SESSION['carrinho'] as $item): ?>
                            <div class="carrinho-item">
                                <div class="item-info">
                                    <h3><?php echo htmlspecialchars($item['nome']); ?></h3>
                                    <p class="item-preco">R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></p>
                                </div>
                                
                                <div class="item-acoes">
                                    <form method="POST" class="form-quantidade">
                                        <input type="hidden" name="produto_id" value="<?php echo $item['id']; ?>">
                                        <input type="hidden" name="acao" value="atualizar">
                                        <div class="quantidade-controle">
                                            <button type="button" onclick="alterarQuantidade(this, -1)">-</button>
                                            <input type="number" name="quantidade" value="<?php echo $item['quantidade']; ?>" min="1" onchange="this.form.submit()">
                                            <button type="button" onclick="alterarQuantidade(this, 1)">+</button>
                                        </div>
                                    </form>
                                    
                                    <form method="POST" class="form-remover">
                                        <input type="hidden" name="produto_id" value="<?php echo $item['id']; ?>">
                                        <input type="hidden" name="acao" value="remover">
                                        <button type="submit" class="btn-remover">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    
                                    <p class="item-subtotal">
                                        R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Resumo do Pedido -->
                    <div class="carrinho-resumo">
                        <h2>Resumo do Pedido</h2>
                        <div class="resumo-item">
                            <span>Subtotal</span>
                            <span>R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></span>
                        </div>
                        <div class="resumo-item">
                            <span>Taxa de Entrega</span>
                            <span>R$ <?php echo number_format($taxa_entrega, 2, ',', '.'); ?></span>
                        </div>
                        <div class="resumo-item total">
                            <span>Total</span>
                            <span>R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                        </div>
                        
                        <a href="finalizar-pedido.php" class="btn-finalizar">
                            Finalizar Pedido
                        </a>
                        
                        <a href="index.php" class="btn-continuar">
                            Continuar Comprando
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Rodapé -->
    <?php include 'includes/footer.php'; ?>

    <script>
        function alterarQuantidade(botao, delta) {
            const input = botao.parentElement.querySelector('input[type="number"]');
            const novaQuantidade = parseInt(input.value) + delta;
            
            if (novaQuantidade >= 1) {
                input.value = novaQuantidade;
                input.form.submit();
            }
        }
    </script>
</body>
</html> 