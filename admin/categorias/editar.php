<?php
require_once '../../includes/config.php';
verificarAdmin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirecionarComMensagem('listar.php', 'ID da categoria inválido.', 'error');
}

$categoria_id = (int) $_GET['id'];

try {
    $pdo = conectarDB();
    
    // Buscar categoria
    $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
    $stmt->execute([$categoria_id]);
    $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$categoria) {
        redirecionarComMensagem('listar.php', 'Categoria não encontrada.', 'error');
    }
    
} catch (PDOException $e) {
    $erro = "Erro ao carregar categoria: " . $e->getMessage();
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = limparInput($_POST['nome']);
    $descricao = limparInput($_POST['descricao']);
    
    // Validações
    $erros = [];
    
    if (empty($nome)) {
        $erros[] = "O nome é obrigatório.";
    }
    
    // Se não houver erros, salvar
    if (empty($erros)) {
        try {
            // Verificar se já existe outra categoria com o mesmo nome
            $stmt = $pdo->prepare("SELECT id FROM categorias WHERE nome = ? AND id != ?");
            $stmt->execute([$nome, $categoria_id]);
            
            if ($stmt->fetch()) {
                $erros[] = "Já existe uma categoria com este nome.";
            } else {
                // Atualizar categoria
                $stmt = $pdo->prepare("UPDATE categorias SET nome = ?, descricao = ? WHERE id = ?");
                $stmt->execute([$nome, $descricao, $categoria_id]);
                
                redirecionarComMensagem('listar.php', 'Categoria atualizada com sucesso!', 'success');
            }
            
        } catch (PDOException $e) {
            $erro = "Erro ao atualizar categoria: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Categoria - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- Menu Lateral -->
        <?php include '../includes/sidebar.php'; ?>

        <!-- Conteúdo Principal -->
        <main class="admin-content">
            <div class="admin-header">
                <h1>Editar Categoria</h1>
                <div class="header-actions">
                    <a href="listar.php" class="btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

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

            <div class="admin-form">
                <form method="POST">
                    <div class="form-group">
                        <label for="nome">Nome:</label>
                        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($categoria['nome']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="descricao">Descrição:</label>
                        <textarea id="descricao" name="descricao" rows="4"><?php echo htmlspecialchars($categoria['descricao']); ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Salvar
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="../../assets/js/admin.js"></script>
</body>
</html> 