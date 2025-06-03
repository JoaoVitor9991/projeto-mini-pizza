<?php
// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'mini_pizza');

// Configurações gerais
define('SITE_URL', 'http://localhost/projeto-mini-pizza');
define('SITE_NAME', 'Mini Pizza');

// Configurações de sessão
session_start();

// Configuração de fuso horário
date_default_timezone_set('America/Sao_Paulo');

// Configurações de erro (em desenvolvimento)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Função para conexão com o banco de dados
function conectarDB() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("Erro na conexão com o banco de dados: " . $e->getMessage());
    }
}

// Função para verificar se o usuário está logado
function verificarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: " . SITE_URL . "/login.php");
        exit;
    }
}

// Função para verificar se o usuário é administrador
function verificarAdmin() {
    if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
        header("Location: " . SITE_URL . "/index.php");
        exit;
    }
}

// Função para limpar e validar inputs
function limparInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Função para gerar hash seguro de senha
function gerarHash($senha) {
    return password_hash($senha, PASSWORD_DEFAULT);
}

// Função para verificar senha
function verificarSenha($senha, $hash) {
    return password_verify($senha, $hash);
}

// Função para redirecionar com mensagem
function redirecionarComMensagem($url, $mensagem, $tipo = 'success') {
    $_SESSION['mensagem'] = [
        'texto' => $mensagem,
        'tipo' => $tipo
    ];
    header("Location: " . $url);
    exit;
}

// Função para exibir mensagens
function exibirMensagem() {
    if (isset($_SESSION['mensagem'])) {
        $mensagem = $_SESSION['mensagem'];
        unset($_SESSION['mensagem']);
        return "<div class='alert alert-{$mensagem['tipo']}'>{$mensagem['texto']}</div>";
    }
    return '';
}

// Função para adicionar produto ao carrinho
function adicionarAoCarrinho($produto_id, $quantidade = 1) {
    try {
        $pdo = conectarDB();
        
        // Buscar informações do produto
        $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ? AND ativo = 1");
        $stmt->execute([$produto_id]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$produto) {
            throw new Exception('Produto não encontrado');
        }
        
        // Inicializar carrinho se não existir
        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }
        
        // Verificar se o produto já está no carrinho
        $produto_encontrado = false;
        foreach ($_SESSION['carrinho'] as &$item) {
            if ($item['id'] == $produto_id) {
                $item['quantidade'] += $quantidade;
                $produto_encontrado = true;
                break;
            }
        }
        
        // Se o produto não estiver no carrinho, adiciona
        if (!$produto_encontrado) {
            $_SESSION['carrinho'][] = [
                'id' => $produto['id'],
                'nome' => $produto['nome'],
                'preco' => $produto['preco'],
                'quantidade' => $quantidade,
                'imagem' => $produto['imagem']
            ];
        }
        
        return true;
        
    } catch (Exception $e) {
        throw new Exception('Erro ao adicionar produto ao carrinho: ' . $e->getMessage());
    }
}

// Função para contar itens no carrinho
function contarItensCarrinho() {
    if (!isset($_SESSION['carrinho'])) {
        return 0;
    }
    
    $total = 0;
    foreach ($_SESSION['carrinho'] as $item) {
        $total += $item['quantidade'];
    }
    return $total;
}

// Função para calcular total do carrinho
function calcularTotalCarrinho() {
    if (!isset($_SESSION['carrinho'])) {
        return 0;
    }
    
    $total = 0;
    foreach ($_SESSION['carrinho'] as $item) {
        $total += $item['preco'] * $item['quantidade'];
    }
    return $total;
} 