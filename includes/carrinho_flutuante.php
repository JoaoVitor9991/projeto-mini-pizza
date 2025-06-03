<?php
$total_itens = 0;
if (isset($_SESSION['carrinho'])) {
    foreach ($_SESSION['carrinho'] as $item) {
        $total_itens += $item['quantidade'];
    }
}
?>
<div id="carrinho-flutuante" class="<?php echo $total_itens > 0 ? 'tem-itens' : ''; ?>">
    <a href="carrinho.php" class="carrinho-icone" title="Ver Carrinho">
        <i class="fas fa-shopping-cart"></i>
        <?php if ($total_itens > 0): ?>
            <span class="carrinho-quantidade"><?php echo $total_itens; ?></span>
        <?php endif; ?>
    </a>
</div> 