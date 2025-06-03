<?php
require_once 'includes/config.php';

// Verificar se há itens no carrinho
if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    redirecionarComMensagem('carrinho.php', 'Seu carrinho está vazio.', 'error');
}

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['redirect_after_login'] = 'finalizar-pedido.php';
    redirecionarComMensagem('login.php', 'Faça login para continuar com o pedido.', 'warning');
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $endereco = limparInput($_POST['endereco']);
    $numero = limparInput($_POST['numero']);
    $complemento = limparInput($_POST['complemento']);
    $bairro = limparInput($_POST['bairro']);
    $cidade = limparInput($_POST['cidade']);
    $telefone = limparInput($_POST['telefone']);
    $observacoes = limparInput($_POST['observacoes']);
    
    // Validações
    $erros = [];
    
    if (empty($endereco)) $erros[] = "O endereço é obrigatório.";
    if (empty($numero)) $erros[] = "O número é obrigatório.";
    if (empty($bairro)) $erros[] = "O bairro é obrigatório.";
    if (empty($cidade)) $erros[] = "A cidade é obrigatória.";
    if (empty($telefone)) $erros[] = "O telefone é obrigatório.";
    
    // Se não houver erros, salvar pedido
    if (empty($erros)) {
        try {
            $pdo = conectarDB();
            $pdo->beginTransaction();
            
            // Calcular totais
            $subtotal = 0;
            $taxa_entrega = 5.00;
            
            foreach ($_SESSION['carrinho'] as $item) {
                $subtotal += $item['preco'] * $item['quantidade'];
            }
            
            $total = $subtotal + $taxa_entrega;
            
            // Inserir pedido
            $stmt = $pdo->prepare("
                INSERT INTO pedidos (
                    usuario_id, endereco, numero, complemento, bairro, 
                    cidade, telefone, observacoes, subtotal, taxa_entrega, 
                    valor_total, status, data_pedido
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendente', NOW()
                )
            ");
            
            $stmt->execute([
                $_SESSION['usuario_id'],
                $endereco,
                $numero,
                $complemento,
                $bairro,
                $cidade,
                $telefone,
                $observacoes,
                $subtotal,
                $taxa_entrega,
                $total
            ]);
            
            $pedido_id = $pdo->lastInsertId();
            
            // Inserir itens do pedido
            $stmt = $pdo->prepare("
                INSERT INTO itens_pedido (
                    pedido_id, produto_id, quantidade, preco_unitario
                ) VALUES (?, ?, ?, ?)
            ");
            
            foreach ($_SESSION['carrinho'] as $item) {
                $stmt->execute([
                    $pedido_id,
                    $item['id'],
                    $item['quantidade'],
                    $item['preco']
                ]);
            }
            
            $pdo->commit();
            
            // Limpar carrinho
            unset($_SESSION['carrinho']);
            
            redirecionarComMensagem(
                'meus-pedidos.php', 
                'Pedido realizado com sucesso! Em breve entraremos em contato.', 
                'success'
            );
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            $erro = "Erro ao finalizar pedido: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Pedido - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/styles.v2.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Cabeçalho -->
    <?php include 'includes/header.php'; ?>

    <!-- Conteúdo Principal -->
    <main class="container">
        <div class="finalizar-pedido">
            <h1>Finalizar Pedido</h1>
            
            <?php if (isset($erro)): ?>
                <div class="alert alert-error"><?php echo $erro; ?></div>
            <?php endif; ?>

            <?php if (!empty($erros)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($erros as $erro): ?>
                            <li><?php echo $erro; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="finalizar-grid">
                <!-- Formulário de Entrega -->
                <div class="form-entrega">
                    <h2>Endereço de Entrega</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label for="endereco">Endereço:</label>
                            <input type="text" id="endereco" name="endereco" value="<?php echo isset($_POST['endereco']) ? htmlspecialchars($_POST['endereco']) : ''; ?>" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="numero">Número:</label>
                                <input type="text" id="numero" name="numero" value="<?php echo isset($_POST['numero']) ? htmlspecialchars($_POST['numero']) : ''; ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="complemento">Complemento:</label>
                                <input type="text" id="complemento" name="complemento" value="<?php echo isset($_POST['complemento']) ? htmlspecialchars($_POST['complemento']) : ''; ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="bairro">Bairro:</label>
                                <input type="text" id="bairro" name="bairro" value="<?php echo isset($_POST['bairro']) ? htmlspecialchars($_POST['bairro']) : ''; ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="cidade">Cidade:</label>
                                <input type="text" id="cidade" name="cidade" value="<?php echo isset($_POST['cidade']) ? htmlspecialchars($_POST['cidade']) : ''; ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="telefone">Telefone:</label>
                            <input type="tel" id="telefone" name="telefone" value="<?php echo isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="observacoes">Observações:</label>
                            <textarea id="observacoes" name="observacoes" rows="4"><?php echo isset($_POST['observacoes']) ? htmlspecialchars($_POST['observacoes']) : ''; ?></textarea>
                        </div>

                        <button type="submit" class="btn-finalizar">
                            Confirmar Pedido
                        </button>
                    </form>
                </div>

                <!-- Resumo do Pedido -->
                <div class="resumo-pedido">
                    <h2>Resumo do Pedido</h2>
                    
                    <div class="itens-pedido">
                        <?php foreach ($_SESSION['carrinho'] as $item): ?>
                            <div class="item-pedido">
                                <div class="item-info">
                                    <h3><?php echo htmlspecialchars($item['nome']); ?></h3>
                                    <p class="item-quantidade">Quantidade: <?php echo $item['quantidade']; ?></p>
                                </div>
                                <p class="item-preco">
                                    R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php
                    $subtotal = 0;
                    foreach ($_SESSION['carrinho'] as $item) {
                        $subtotal += $item['preco'] * $item['quantidade'];
                    }
                    $taxa_entrega = 5.00;
                    $total = $subtotal + $taxa_entrega;
                    ?>
                    
                    <div class="resumo-valores">
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
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Rodapé -->
    <?php include 'includes/footer.php'; ?>

    <script>
        // Máscara para telefone
        const telefone = document.getElementById('telefone');
        telefone.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            
            if (value.length > 2) {
                value = '(' + value.slice(0, 2) + ') ' + value.slice(2);
            }
            if (value.length > 9) {
                value = value.slice(0, 9) + '-' + value.slice(9);
            }
            
            e.target.value = value;
        });
    </script>
</body>
</html> 