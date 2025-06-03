<?php
require_once '../../includes/config.php';
verificarAdmin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirecionarComMensagem('listar.php', 'ID do produto inválido.', 'error');
}

$produto_id = (int) $_GET['id'];

try {
    $pdo = conectarDB();
    
    // Verificar se o produto existe e buscar imagem
    $stmt = $pdo->prepare("SELECT imagem FROM produtos WHERE id = ?");
    $stmt->execute([$produto_id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$produto) {
        redirecionarComMensagem('listar.php', 'Produto não encontrado.', 'error');
    }
    
    // Verificar se o produto está em algum pedido
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM itens_pedido WHERE produto_id = ?");
    $stmt->execute([$produto_id]);
    $tem_pedidos = $stmt->fetchColumn() > 0;
    
    if ($tem_pedidos) {
        // Se tiver pedidos, apenas inativar
        $stmt = $pdo->prepare("UPDATE produtos SET ativo = 0 WHERE id = ?");
        $stmt->execute([$produto_id]);
        redirecionarComMensagem('listar.php', 'Produto inativado pois possui pedidos vinculados.', 'warning');
    } else {
        // Se não tiver pedidos, excluir
        $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
        $stmt->execute([$produto_id]);
        
        // Excluir imagem se existir
        if ($produto['imagem']) {
            $caminho_imagem = '../../uploads/produtos/' . $produto['imagem'];
            if (file_exists($caminho_imagem)) {
                unlink($caminho_imagem);
            }
        }
        
        redirecionarComMensagem('listar.php', 'Produto excluído com sucesso!', 'success');
    }
    
} catch (PDOException $e) {
    redirecionarComMensagem('listar.php', 'Erro ao excluir produto: ' . $e->getMessage(), 'error');
} 