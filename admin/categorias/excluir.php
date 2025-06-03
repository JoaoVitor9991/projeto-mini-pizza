<?php
require_once '../../includes/config.php';
verificarAdmin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirecionarComMensagem('listar.php', 'ID da categoria inválido.', 'error');
}

$categoria_id = (int) $_GET['id'];

try {
    $pdo = conectarDB();
    
    // Verificar se a categoria existe
    $stmt = $pdo->prepare("SELECT id FROM categorias WHERE id = ?");
    $stmt->execute([$categoria_id]);
    
    if (!$stmt->fetch()) {
        redirecionarComMensagem('listar.php', 'Categoria não encontrada.', 'error');
    }
    
    // Verificar se existem produtos vinculados
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM produtos WHERE categoria_id = ?");
    $stmt->execute([$categoria_id]);
    $tem_produtos = $stmt->fetchColumn() > 0;
    
    if ($tem_produtos) {
        redirecionarComMensagem('listar.php', 'Não é possível excluir a categoria pois existem produtos vinculados.', 'error');
    } else {
        // Excluir categoria
        $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
        $stmt->execute([$categoria_id]);
        
        redirecionarComMensagem('listar.php', 'Categoria excluída com sucesso!', 'success');
    }
    
} catch (PDOException $e) {
    redirecionarComMensagem('listar.php', 'Erro ao excluir categoria: ' . $e->getMessage(), 'error');
} 