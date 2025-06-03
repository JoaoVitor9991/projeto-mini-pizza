<?php
require_once 'includes/config.php';

// Array com as imagens padrão para cada tipo de pizza
$imagens_padrao = [
    'margherita' => ['nome' => 'pizza-margherita.jpg', 'texto' => 'Pizza Margherita', 'cor' => '#FF6B6B'],
    'calabresa' => ['nome' => 'pizza-calabresa.jpg', 'texto' => 'Pizza Calabresa', 'cor' => '#E63946'],
    'portuguesa' => ['nome' => 'pizza-portuguesa.jpg', 'texto' => 'Pizza Portuguesa', 'cor' => '#F4A261'],
    '4-queijos' => ['nome' => 'pizza-4-queijos.jpg', 'texto' => 'Pizza 4 Queijos', 'cor' => '#FFD93D'],
    'frango-catupiry' => ['nome' => 'pizza-frango-catupiry.jpg', 'texto' => 'Pizza Frango com Catupiry', 'cor' => '#F9C74F'],
    'chocolate' => ['nome' => 'pizza-chocolate.jpg', 'texto' => 'Pizza de Chocolate', 'cor' => '#4A4E69'],
    'banana' => ['nome' => 'pizza-banana.jpg', 'texto' => 'Pizza de Banana', 'cor' => '#FFB703'],
    'suco-laranja' => ['nome' => 'suco-laranja.jpg', 'texto' => 'Suco de Laranja', 'cor' => '#FB8500']
];

// Criar pasta de imagens se não existir
$diretorio = 'assets/img';
if (!file_exists($diretorio)) {
    mkdir($diretorio, 0777, true);
}

// Função para criar uma imagem colorida com texto
function criarImagemProduto($texto, $cor, $arquivo_destino) {
    // Criar imagem 800x600
    $imagem = imagecreatetruecolor(800, 600);
    
    // Cores
    $cor_rgb = sscanf($cor, "#%02x%02x%02x");
    $cor_fundo = imagecolorallocate($imagem, $cor_rgb[0], $cor_rgb[1], $cor_rgb[2]);
    $cor_texto = imagecolorallocate($imagem, 255, 255, 255);
    
    // Preencher fundo
    imagefill($imagem, 0, 0, $cor_fundo);
    
    // Adicionar texto
    $fonte = 5; // Fonte padrão
    $texto = wordwrap($texto, 30, "\n");
    
    // Centralizar texto
    $x = (800 - (strlen($texto) * 10)) / 2;
    $y = 300;
    
    // Desenhar texto
    imagestring($imagem, $fonte, $x, $y, $texto, $cor_texto);
    
    // Salvar imagem
    imagejpeg($imagem, $arquivo_destino, 90);
    imagedestroy($imagem);
    
    return true;
}

try {
    $pdo = conectarDB();
    
    // Atualizar imagens das pizzas
    foreach ($imagens_padrao as $nome => $info) {
        // Criar imagem colorida
        if (criarImagemProduto($info['texto'], $info['cor'], $diretorio . '/' . $info['nome'])) {
            // Atualizar no banco de dados
            $stmt = $pdo->prepare("
                UPDATE produtos 
                SET imagem = ?
                WHERE LOWER(REPLACE(nome, ' ', '-')) LIKE ?
            ");
            
            $padrao_busca = '%' . $nome . '%';
            $stmt->execute([$info['nome'], $padrao_busca]);
            
            echo "Imagem criada e atualizada para: " . $info['texto'] . "<br>";
        }
    }
    
    echo "<br>Todas as imagens foram atualizadas com sucesso!<br>";
    echo "<a href='index.php'>Voltar para a página inicial</a>";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
} 