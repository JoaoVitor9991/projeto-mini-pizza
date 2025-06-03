<?php
require_once 'includes/config.php';

$mensagem_enviada = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = limparInput($_POST['nome'] ?? '');
    $email = limparInput($_POST['email'] ?? '');
    $telefone = limparInput($_POST['telefone'] ?? '');
    $assunto = limparInput($_POST['assunto'] ?? '');
    $mensagem = limparInput($_POST['mensagem'] ?? '');
    
    // Aqui você pode adicionar o código para enviar o email
    // Por enquanto, apenas simulamos o envio
    $mensagem_enviada = true;
    $_SESSION['mensagem'] = [
        'texto' => 'Mensagem enviada com sucesso! Em breve entraremos em contato.',
        'tipo' => 'success'
    ];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Cabeçalho -->
    <?php include 'includes/header.php'; ?>

    <!-- Conteúdo Principal -->
    <main class="container">
        <div class="contato-container">
            <h1>Entre em Contato</h1>

            <?php if (isset($_SESSION['mensagem'])): ?>
                <div class="alert alert-<?php echo $_SESSION['mensagem']['tipo']; ?>">
                    <?php 
                    echo $_SESSION['mensagem']['texto'];
                    unset($_SESSION['mensagem']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="contato-grid">
                <!-- Informações de Contato -->
                <div class="contato-info">
                    <h2>Informações de Contato</h2>
                    
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h3>Endereço</h3>
                            <p>Rua da Pizza, 123 - Centro<br>São Paulo - SP</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h3>Telefone</h3>
                            <p>(11) 1234-5678</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h3>Email</h3>
                            <p>contato@pizzariaamatsu.com</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <h3>Horário de Funcionamento</h3>
                            <p>Segunda a Sexta: 18h às 23h<br>
                               Sábado e Domingo: 18h às 00h</p>
                        </div>
                    </div>

                    <div class="social-links">
                        <a href="#" title="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>

                <!-- Formulário de Contato -->
                <div class="contato-form">
                    <h2>Envie sua Mensagem</h2>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="nome">Nome:</label>
                            <input type="text" id="nome" name="nome" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="telefone">Telefone:</label>
                            <input type="tel" id="telefone" name="telefone">
                        </div>

                        <div class="form-group">
                            <label for="assunto">Assunto:</label>
                            <input type="text" id="assunto" name="assunto" required>
                        </div>

                        <div class="form-group">
                            <label for="mensagem">Mensagem:</label>
                            <textarea id="mensagem" name="mensagem" rows="5" required></textarea>
                        </div>

                        <button type="submit" class="btn-primary">Enviar Mensagem</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Rodapé -->
    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/script.js"></script>
</body>
</html> 