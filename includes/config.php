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