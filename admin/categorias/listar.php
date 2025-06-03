<?php
require_once '../../includes/config.php';
verificarAdmin();

try {
    $pdo = conectarDB();
    
    // Buscar categorias
    $stmt = $pdo->query("
        SELECT c.*, COUNT(p.id) as total_produtos 
        FROM categorias c 
        LEFT JOIN produtos p ON c.id = p.categoria_id 
        GROUP BY c.id 
        ORDER BY c.nome
    ");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $erro = "Erro ao carregar categorias: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Categorias - <?php echo SITE_NAME; ?></title>
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
                <h1>Gerenciar Categorias</h1>
                <div class="header-actions">
                    <a href="adicionar.php" class="btn-primary">
                        <i class="fas fa-plus"></i> Nova Categoria
                    </a>
                </div>
            </div>

            <?php if (isset($erro)): ?>
                <div class="alert alert-error"><?php echo $erro; ?></div>
            <?php endif; ?>

            <?php if (isset($_SESSION['mensagem'])): ?>
                <div class="alert alert-<?php echo $_SESSION['mensagem']['tipo']; ?>">
                    <?php 
                    echo $_SESSION['mensagem']['texto'];
                    unset($_SESSION['mensagem']);
                    ?>
                </div>
            <?php endif; ?>

            <!-- Campo de Busca -->
            <div class="admin-search">
                <input type="text" id="busca" placeholder="Buscar categorias..." class="search-input">
            </div>

            <!-- Tabela de Categorias -->
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Total de Produtos</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($categorias)): ?>
                            <?php foreach ($categorias as $categoria): ?>
                                <tr>
                                    <td>#<?php echo $categoria['id']; ?></td>
                                    <td><?php echo htmlspecialchars($categoria['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($categoria['descricao']); ?></td>
                                    <td><?php echo $categoria['total_produtos']; ?></td>
                                    <td>
                                        <a href="editar.php?id=<?php echo $categoria['id']; ?>" class="btn-action" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($categoria['total_produtos'] == 0): ?>
                                            <a href="#" onclick="excluirCategoria(<?php echo $categoria['id']; ?>)" class="btn-action text-danger" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Nenhuma categoria cadastrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="../../assets/js/admin.js"></script>
    <script>
        function excluirCategoria(id) {
            if (confirmarExclusao('Tem certeza que deseja excluir esta categoria?')) {
                window.location.href = `excluir.php?id=${id}`;
            }
        }
    </script>
</body>
</html> 