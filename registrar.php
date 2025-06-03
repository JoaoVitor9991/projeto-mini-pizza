<?php
require_once 'includes/config.php';

// Se já estiver logado, redireciona para a página inicial
if (isset($_SESSION['usuario_id'])) {
    redirecionarComMensagem('index.php', 'Você já está logado.', 'warning');
}

$erro = null;
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = limparInput($_POST['nome'] ?? '');
    $email = limparInput($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    $telefone = limparInput($_POST['telefone'] ?? '');
    $cpf = limparInput($_POST['cpf'] ?? '');
    
    // Validações
    $erros = [];
    
    if (empty($nome)) {
        $erros[] = "O nome é obrigatório.";
    }
    
    if (empty($email)) {
        $erros[] = "O email é obrigatório.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Email inválido.";
    }
    
    if (empty($senha)) {
        $erros[] = "A senha é obrigatória.";
    } elseif (strlen($senha) < 6) {
        $erros[] = "A senha deve ter no mínimo 6 caracteres.";
    }
    
    if ($senha !== $confirmar_senha) {
        $erros[] = "As senhas não conferem.";
    }
    
    if (empty($telefone)) {
        $erros[] = "O telefone é obrigatório.";
    }
    
    if (empty($cpf)) {
        $erros[] = "O CPF é obrigatório.";
    }
    
    // Se não houver erros, tenta cadastrar
    if (empty($erros)) {
        try {
            $pdo = conectarDB();
            
            // Verificar se email já existe
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $erros[] = "Este email já está cadastrado.";
            } else {
                // Verificar se CPF já existe
                $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE cpf = ?");
                $stmt->execute([$cpf]);
                
                if ($stmt->fetch()) {
                    $erros[] = "Este CPF já está cadastrado.";
                } else {
                    // Inserir novo usuário
                    $stmt = $pdo->prepare("
                        INSERT INTO usuarios (nome, email, senha, telefone, cpf, tipo)
                        VALUES (?, ?, ?, ?, ?, 'cliente')
                    ");
                    
                    $stmt->execute([
                        $nome,
                        $email,
                        password_hash($senha, PASSWORD_DEFAULT),
                        $telefone,
                        $cpf
                    ]);
                    
                    // Login automático após cadastro
                    $_SESSION['usuario_id'] = $pdo->lastInsertId();
                    $_SESSION['usuario_nome'] = $nome;
                    $_SESSION['usuario_tipo'] = 'cliente';
                    
                    // Redirecionar
                    redirecionarComMensagem(
                        'index.php',
                        'Cadastro realizado com sucesso! Bem-vindo(a) ' . $nome,
                        'success'
                    );
                }
            }
            
        } catch (PDOException $e) {
            $erro = "Erro ao realizar cadastro: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Cabeçalho -->
    <?php include 'includes/header.php'; ?>

    <!-- Conteúdo Principal -->
    <main class="container">
        <div class="auth-container">
            <div class="auth-box">
                <h1>Criar Conta</h1>
                
                <?php if (isset($erro)): ?>
                    <div class="alert alert-error"><?php echo $erro; ?></div>
                <?php endif; ?>

                <?php if (!empty($erros)): ?>
                    <div class="alert alert-error">
                        <ul>
                            <?php foreach ($erros as $erro): ?>
                                <li><?php echo $erro; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="nome">Nome Completo:</label>
                        <input type="text" id="nome" name="nome" value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="senha">Senha:</label>
                        <input type="password" id="senha" name="senha" required minlength="6">
                    </div>

                    <div class="form-group">
                        <label for="confirmar_senha">Confirmar Senha:</label>
                        <input type="password" id="confirmar_senha" name="confirmar_senha" required minlength="6">
                    </div>

                    <div class="form-group">
                        <label for="telefone">Telefone:</label>
                        <input type="tel" id="telefone" name="telefone" value="<?php echo isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="cpf">CPF:</label>
                        <input type="text" id="cpf" name="cpf" value="<?php echo isset($_POST['cpf']) ? htmlspecialchars($_POST['cpf']) : ''; ?>" required>
                    </div>

                    <button type="submit" class="btn-primary btn-block">Criar Conta</button>
                </form>

                <div class="auth-links">
                    <p>Já tem uma conta? <a href="login.php">Fazer Login</a></p>
                </div>
            </div>
        </div>
    </main>

    <!-- Rodapé -->
    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/script.js"></script>
    <script>
        // Máscara para CPF
        const cpf = document.getElementById('cpf');
        cpf.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            
            if (value.length > 3) {
                value = value.slice(0, 3) + '.' + value.slice(3);
            }
            if (value.length > 7) {
                value = value.slice(0, 7) + '.' + value.slice(7);
            }
            if (value.length > 11) {
                value = value.slice(0, 11) + '-' + value.slice(11);
            }
            
            e.target.value = value;
        });
    </script>
</body>
</html> 