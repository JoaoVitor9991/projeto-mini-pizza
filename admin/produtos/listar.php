<?php
require_once '../../includes/config.php';
verificarAdmin();

try {
    $pdo = conectarDB();
    
    // Buscar produtos
    $stmt = $pdo->query("
        SELECT p.*, c.nome as categoria_nome 
        FROM produtos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        ORDER BY p.nome
    ");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $erro = "Erro ao carregar produtos: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Produtos - <?php echo SITE_NAME; ?></title>
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
                <h1>Gerenciar Produtos</h1>
                <div class="header-actions">
                    <a href="adicionar.php" class="btn-primary">
                        <i class="fas fa-plus"></i> Novo Produto
                    </a>
                </div>
            </div>

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

            <!-- Campo de Busca -->
            <div class="admin-search">
                <input type="text" id="busca" placeholder="Buscar produtos..." class="search-input">
            </div>

            <!-- Tabela de Produtos -->
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagem</th>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th>Preço</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($produtos)): ?>
                            <?php foreach ($produtos as $produto): ?>
                                <tr>
                                    <td>#<?php echo $produto['id']; ?></td>
                                    <td>
                                        <?php if ($produto['imagem']): ?>
                                            <img src="../../assets/img/<?php echo $produto['imagem']; ?>" 
                                                 alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                                                 class="produto-imagem">
                                        <?php else: ?>
                                            <img src="../../assets/img/no-image.png" 
                                                 alt="Sem imagem"
                                                 class="produto-imagem">
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($produto['categoria_nome'] ?? 'Sem categoria'); ?></td>
                                    <td>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $produto['ativo'] ? 'status-ativo' : 'status-inativo'; ?>">
                                            <?php echo $produto['ativo'] ? 'Ativo' : 'Inativo'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="editar.php?id=<?php echo $produto['id']; ?>" class="btn-action" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" onclick="excluirProduto(<?php echo $produto['id']; ?>)" class="btn-action text-danger" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Nenhum produto cadastrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="../../assets/js/admin.js"></script>
    <script>
        function excluirProduto(id) {
            if (confirmarExclusao('Tem certeza que deseja excluir este produto?')) {
                window.location.href = `excluir.php?id=${id}`;
            }
        }
    </script>
</body>
</html> 