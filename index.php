<?php
require_once 'includes/config.php';

// Buscar produtos do banco de dados
try {
    $pdo = conectarDB();
    $stmt = $pdo->query("SELECT * FROM produtos WHERE ativo = 1 ORDER BY nome");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $produtos = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Sua Pizza Favorita</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <h1><?php echo SITE_NAME; ?></h1>
            </div>
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#sobre">Sobre</a></li>
                <li><a href="#produtos">Produtos</a></li>
                <li><a href="#contato">Contato</a></li>
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <?php if ($_SESSION['usuario_tipo'] === 'admin'): ?>
                        <li><a href="admin/dashboard.php">Painel Admin</a></li>
                    <?php else: ?>
                        <li><a href="meus-pedidos.php">Meus Pedidos</a></li>
                    <?php endif; ?>
                    <li><a href="includes/logout.php" class="btn-login">Sair</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn-login">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <section id="home" class="hero">
            <div class="hero-content">
                <h2>Bem-vindo à <?php echo SITE_NAME; ?></h2>
                <p>As melhores pizzas da cidade, feitas com amor e ingredientes selecionados</p>
                <a href="#produtos" class="btn-primary">Ver Cardápio</a>
            </div>
        </section>

        <section id="sobre" class="sobre">
            <h2>Sobre Nós</h2>
            <div class="sobre-content">
                <p>Somos uma pizzaria comprometida com a qualidade e sabor. Nossa história começou com a paixão por fazer as melhores pizzas, usando ingredientes frescos e técnicas tradicionais.</p>
            </div>
        </section>

        <section id="produtos" class="produtos">
            <h2>Nossos Produtos</h2>
            <div class="produtos-grid">
                <?php if (!empty($produtos)): ?>
                    <?php foreach ($produtos as $produto): ?>
                        <div class="produto-card">
                            <img src="<?php echo htmlspecialchars($produto['imagem']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>">
                            <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>
                            <p><?php echo htmlspecialchars($produto['descricao']); ?></p>
                            <p class="preco">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                            <?php if (isset($_SESSION['usuario_id'])): ?>
                                <button onclick="adicionarAoCarrinho(<?php echo $produto['id']; ?>)" class="btn-primary">
                                    Adicionar ao Carrinho
                                </button>
                            <?php else: ?>
                                <a href="login.php" class="btn-primary">Faça login para comprar</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="mensagem-erro">Nenhum produto disponível no momento.</p>
                <?php endif; ?>
            </div>
        </section>

        <section id="contato" class="contato">
            <h2>Entre em Contato</h2>
            <div class="contato-container">
                <form id="formContato" class="form-contato">
                    <div class="form-group">
                        <label for="nome">Nome:</label>
                        <input type="text" id="nome" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="mensagem">Mensagem:</label>
                        <textarea id="mensagem" name="mensagem" required></textarea>
                    </div>
                    <button type="submit" class="btn-primary">Enviar</button>
                </form>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-info">
                <h3><?php echo SITE_NAME; ?></h3>
                <p>Endereço: Rua da Pizza, 123</p>
                <p>Telefone: (11) 1234-5678</p>
            </div>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script>
        // Função para adicionar ao carrinho
        function adicionarAoCarrinho(produtoId) {
            fetch('includes/adicionar_carrinho.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ produto_id: produtoId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Produto adicionado ao carrinho!');
                } else {
                    alert(data.mensagem);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao adicionar produto ao carrinho.');
            });
        }

        // Envio do formulário de contato
        document.getElementById('formContato').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('includes/processar_contato.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.mensagem);
                if (data.status === 'success') {
                    this.reset();
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao enviar mensagem. Por favor, tente novamente.');
            });
        });
    </script>
</body>
</html> 