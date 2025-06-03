<?php
require_once '../../includes/config.php';
verificarAdmin();

try {
    $pdo = conectarDB();
    
    // Buscar categorias para o select
    $stmt = $pdo->query("SELECT id, nome FROM categorias ORDER BY nome");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $erro = "Erro ao carregar categorias: " . $e->getMessage();
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = limparInput($_POST['nome']);
    $descricao = limparInput($_POST['descricao']);
    $preco = str_replace(',', '.', limparInput($_POST['preco']));
    $categoria_id = isset($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : null;
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    
    // Validações
    $erros = [];
    
    if (empty($nome)) {
        $erros[] = "O nome é obrigatório.";
    }
    
    if (!is_numeric($preco) || $preco <= 0) {
        $erros[] = "Preço inválido.";
    }
    
    // Se não houver erros, processar a imagem e salvar
    if (empty($erros)) {
        try {
            $imagem = null;
            
            // Processar upload da imagem
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $arquivo = $_FILES['imagem'];
                $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
                
                // Validar extensão
                $permitidos = ['jpg', 'jpeg', 'png', 'gif'];
                if (!in_array($ext, $permitidos)) {
                    throw new Exception("Tipo de arquivo não permitido.");
                }
                
                // Gerar nome único
                $imagem = uniqid() . '.' . $ext;
                
                // Criar diretório se não existir
                $diretorio = '../../uploads/produtos';
                if (!file_exists($diretorio)) {
                    mkdir($diretorio, 0777, true);
                }
                
                // Mover arquivo
                if (!move_uploaded_file($arquivo['tmp_name'], $diretorio . '/' . $imagem)) {
                    throw new Exception("Erro ao fazer upload da imagem.");
                }
            }
            
            // Inserir produto
            $stmt = $pdo->prepare("
                INSERT INTO produtos (nome, descricao, preco, categoria_id, imagem, ativo) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $nome,
                $descricao,
                $preco,
                $categoria_id,
                $imagem,
                $ativo
            ]);
            
            redirecionarComMensagem('listar.php', 'Produto adicionado com sucesso!', 'success');
            
        } catch (Exception $e) {
            // Se houver erro, excluir a imagem se foi feito upload
            if ($imagem && file_exists('../../uploads/produtos/' . $imagem)) {
                unlink('../../uploads/produtos/' . $imagem);
            }
            $erro = "Erro ao adicionar produto: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Produto - <?php echo SITE_NAME; ?></title>
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
                <h1>Adicionar Produto</h1>
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
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nome">Nome:</label>
                        <input type="text" id="nome" name="nome" value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="descricao">Descrição:</label>
                        <textarea id="descricao" name="descricao" rows="4"><?php echo isset($_POST['descricao']) ? htmlspecialchars($_POST['descricao']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="preco">Preço:</label>
                        <input type="text" id="preco" name="preco" class="campo-moeda" value="<?php echo isset($_POST['preco']) ? htmlspecialchars($_POST['preco']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="categoria_id">Categoria:</label>
                        <select id="categoria_id" name="categoria_id">
                            <option value="">Selecione uma categoria</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?php echo $categoria['id']; ?>" <?php echo isset($_POST['categoria_id']) && $_POST['categoria_id'] == $categoria['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($categoria['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="imagem">Imagem:</label>
                        <input type="file" id="imagem" name="imagem" accept="image/*" onchange="previewImagem(this, 'preview-imagem')">
                        <img id="preview-imagem" src="#" alt="Preview" style="display: none; max-width: 200px; margin-top: 10px;">
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="ativo" <?php echo !isset($_POST['ativo']) || $_POST['ativo'] ? 'checked' : ''; ?>>
                            Produto ativo
                        </label>
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