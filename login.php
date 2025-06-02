<?php
require_once 'includes/config.php';

// Se já estiver logado, redireciona
if (isset($_SESSION['usuario_id'])) {
    if ($_SESSION['usuario_tipo'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--light-gray);
        }

        .login-box {
            background-color: var(--white);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-box h2 {
            text-align: center;
            color: var(--secondary-color);
            margin-bottom: 2rem;
        }

        .login-box .form-group {
            margin-bottom: 1.5rem;
        }

        .login-box .btn-primary {
            width: 100%;
            padding: 0.8rem;
            font-size: 1.1rem;
            text-align: center;
        }

        .login-links {
            text-align: center;
            margin-top: 1rem;
        }

        .login-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
        }

        .login-links a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            text-align: center;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Login</h2>
            <?php echo exibirMensagem(); ?>
            <form action="includes/processar_login.php" method="POST">
                <div class="form-group">
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" required>
                </div>
                <button type="submit" class="btn-primary">Entrar</button>
            </form>
            <div class="login-links">
                <a href="recuperar_senha.php">Esqueceu a senha?</a> | 
                <a href="cadastro.php">Criar conta</a><br>
                <a href="index.php">Voltar para a página inicial</a>
            </div>
        </div>
    </div>

    <script>
        // Validação do formulário no lado do cliente
        document.querySelector('form').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const senha = document.getElementById('senha').value.trim();
            let isValid = true;
            let errorMessage = '';

            // Validação do email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                isValid = false;
                errorMessage += 'Email inválido.\n';
            }

            // Validação da senha
            if (senha.length < 6) {
                isValid = false;
                errorMessage += 'A senha deve ter pelo menos 6 caracteres.\n';
            }

            if (!isValid) {
                e.preventDefault();
                alert(errorMessage);
            }
        });
    </script>
</body>
</html> 