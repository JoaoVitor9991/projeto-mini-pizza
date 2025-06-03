<?php
require_once 'includes/config.php';

try {
    $pdo = conectarDB();
    
    // Buscar categorias
    $stmt = $pdo->query("SELECT * FROM categorias ORDER BY nome");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar produtos ativos
    $stmt = $pdo->query("
        SELECT p.*, c.nome as categoria_nome 
        FROM produtos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.ativo = 1 
        ORDER BY c.nome, p.nome
    ");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Organizar produtos por categoria
    $produtos_por_categoria = [];
    foreach ($produtos as $produto) {
        $categoria_id = $produto['categoria_id'] ?? 0;
        $produtos_por_categoria[$categoria_id][] = $produto;
    }
    
} catch (PDOException $e) {
    $erro = "Erro ao carregar produtos: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Cardápio</title>
    <link rel="stylesheet" href="assets/css/styles.v2.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Cabeçalho -->
    <?php include 'includes/header.php'; ?>

    <!-- Conteúdo Principal -->
    <main class="container">
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

        <!-- Menu de Categorias -->
        <nav class="categorias-nav">
            <ul>
                <?php foreach ($categorias as $categoria): ?>
                    <li>
                        <a href="#categoria-<?php echo $categoria['id']; ?>">
                            <?php echo htmlspecialchars($categoria['nome']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <!-- Lista de Produtos -->
        <div class="produtos-grid">
            <?php foreach ($categorias as $categoria): ?>
                <?php if (isset($produtos_por_categoria[$categoria['id']])): ?>
                    <section id="categoria-<?php echo $categoria['id']; ?>" class="categoria-section">
                        <h2><?php echo htmlspecialchars($categoria['nome']); ?></h2>
                        <div class="produtos-lista">
                            <?php foreach ($produtos_por_categoria[$categoria['id']] as $produto): ?>
                                <div class="produto-card">
                                    <?php if ($produto['imagem']): ?>
                                        <img src="assets/img/<?php echo $produto['imagem']; ?>" 
                                             alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                                             class="produto-imagem">
                                    <?php else: ?>
                                        <img src="assets/img/no-image.png" 
                                             alt="Sem imagem"
                                             class="produto-imagem">
                                    <?php endif; ?>
                                    
                                    <div class="produto-info">
                                        <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>
                                        <p class="produto-descricao"><?php echo htmlspecialchars($produto['descricao']); ?></p>
                                        <p class="produto-preco">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                                        
                                        <button onclick="adicionarAoCarrinho(<?php echo $produto['id']; ?>)" class="btn-adicionar">
                                            <i class="fas fa-cart-plus"></i> Adicionar
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Rodapé -->
    <?php include 'includes/footer.php'; ?>

    <!-- Carrinho Flutuante -->
    <?php include 'includes/carrinho_flutuante.php'; ?>

    <script src="assets/js/script.js"></script>
</body>
</html> 