<header class="header">
    <nav class="navbar">
        <div class="logo">
            <h1><?php echo SITE_NAME; ?></h1>
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Início</a></li>
            <li><a href="cardapio.php">Cardápio</a></li>
            <li><a href="sobre.php">Sobre</a></li>
            <li><a href="contato.php">Contato</a></li>
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <?php if ($_SESSION['usuario_tipo'] === 'admin'): ?>
                    <li><a href="admin/dashboard.php">Painel Admin</a></li>
                <?php endif; ?>
                <li><a href="meus-pedidos.php">Meus Pedidos</a></li>
                <li><a href="includes/logout.php">Sair</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header> 