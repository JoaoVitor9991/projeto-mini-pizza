<?php
require_once 'config.php';

// Destruir todas as variáveis de sessão
$_SESSION = array();

// Destruir a sessão
session_destroy();

// Redirecionar para a página inicial
redirecionarComMensagem(SITE_URL . '/index.php', 'Você foi desconectado com sucesso!'); 