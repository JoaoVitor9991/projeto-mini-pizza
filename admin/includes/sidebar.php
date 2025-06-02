<aside class="admin-sidebar">
    <div class="sidebar-header">
        <h2><?php echo SITE_NAME; ?></h2>
        <button id="toggleSidebar" class="toggle-sidebar">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <li>
                <a href="<?php echo SITE_URL; ?>/admin/pedidos/listar.php" class="<?php echo strpos($_SERVER['PHP_SELF'], 'pedidos') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Pedidos</span>
                </a>
            </li>
            
            <li>
                <a href="<?php echo SITE_URL; ?>/admin/produtos/listar.php" class="<?php echo strpos($_SERVER['PHP_SELF'], 'produtos') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-pizza-slice"></i>
                    <span>Produtos</span>
                </a>
            </li>
            
            <li>
                <a href="<?php echo SITE_URL; ?>/admin/categorias/listar.php" class="<?php echo strpos($_SERVER['PHP_SELF'], 'categorias') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-tags"></i>
                    <span>Categorias</span>
                </a>
            </li>
            
            <li>
                <a href="<?php echo SITE_URL; ?>/admin/usuarios/listar.php" class="<?php echo strpos($_SERVER['PHP_SELF'], 'usuarios') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>Usuários</span>
                </a>
            </li>
            
            <li>
                <a href="<?php echo SITE_URL; ?>/admin/contatos/listar.php" class="<?php echo strpos($_SERVER['PHP_SELF'], 'contatos') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-envelope"></i>
                    <span>Mensagens</span>
                    <?php if (isset($mensagens_novas) && $mensagens_novas > 0): ?>
                        <span class="badge"><?php echo $mensagens_novas; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <li>
                <a href="<?php echo SITE_URL; ?>/admin/configuracoes.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'configuracoes.php' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i>
                    <span>Configurações</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <a href="<?php echo SITE_URL; ?>" target="_blank">
            <i class="fas fa-external-link-alt"></i>
            <span>Ver Site</span>
        </a>
    </div>
</aside> 