<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Cabeçalho -->
    <?php include 'includes/header.php'; ?>

    <!-- Conteúdo Principal -->
    <main class="container">
        <div class="sobre-container">
            <h1>Sobre a <?php echo SITE_NAME; ?></h1>
            
            <section class="sobre-section">
                <h2>Nossa História</h2>
                <p>Desde 2010, a <?php echo SITE_NAME; ?> tem se dedicado a oferecer as melhores pizzas da região. Nossa história começou com uma pequena pizzaria familiar e hoje somos referência em qualidade e sabor.</p>
            </section>

            <section class="sobre-section">
                <h2>Nossa Missão</h2>
                <p>Proporcionar momentos de felicidade através de pizzas artesanais, preparadas com ingredientes selecionados e muito amor.</p>
            </section>

            <section class="sobre-section">
                <h2>Nossos Valores</h2>
                <ul class="valores-lista">
                    <li><i class="fas fa-check"></i> Qualidade em primeiro lugar</li>
                    <li><i class="fas fa-check"></i> Atendimento excepcional</li>
                    <li><i class="fas fa-check"></i> Higiene impecável</li>
                    <li><i class="fas fa-check"></i> Compromisso com o cliente</li>
                </ul>
            </section>

            <section class="sobre-section">
                <h2>Diferenciais</h2>
                <div class="diferenciais-grid">
                    <div class="diferencial-item">
                        <i class="fas fa-pizza-slice"></i>
                        <h3>Ingredientes Selecionados</h3>
                        <p>Utilizamos apenas ingredientes frescos e de alta qualidade.</p>
                    </div>
                    <div class="diferencial-item">
                        <i class="fas fa-truck"></i>
                        <h3>Entrega Rápida</h3>
                        <p>Sua pizza chega quentinha e no prazo combinado.</p>
                    </div>
                    <div class="diferencial-item">
                        <i class="fas fa-heart"></i>
                        <h3>Feito com Amor</h3>
                        <p>Cada pizza é preparada com dedicação especial.</p>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Rodapé -->
    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/script.js"></script>
</body>
</html> 