<?php
$total_itens = isset($_SESSION['carrinho']) ? count($_SESSION['carrinho']) : 0;
?>
<div id="carrinho-flutuante" class="<?php echo $total_itens > 0 ? 'tem-itens' : ''; ?>">
    <a href="carrinho.php" class="carrinho-icone">
        <i class="fas fa-shopping-cart"></i>
        <?php if ($total_itens > 0): ?>
            <span class="carrinho-quantidade"><?php echo $total_itens; ?></span>
        <?php endif; ?>
    </a>
</div> 