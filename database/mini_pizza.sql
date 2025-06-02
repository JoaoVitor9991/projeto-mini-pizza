-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS mini_pizza;
USE mini_pizza;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('admin', 'cliente') NOT NULL DEFAULT 'cliente',
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de categorias
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    descricao TEXT
);

-- Tabela de produtos
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    imagem VARCHAR(255),
    ativo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- Tabela de pedidos
CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    status ENUM('pendente', 'confirmado', 'em_preparo', 'em_entrega', 'entregue', 'cancelado') DEFAULT 'pendente',
    valor_total DECIMAL(10,2) NOT NULL,
    data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    endereco_entrega TEXT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabela de itens do pedido
CREATE TABLE IF NOT EXISTS itens_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT,
    produto_id INT,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

-- Tabela de contatos
CREATE TABLE IF NOT EXISTS contatos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    mensagem TEXT NOT NULL,
    data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('novo', 'lido', 'respondido') DEFAULT 'novo'
);

-- Inserir usuário administrador padrão
-- Senha: admin123 (hash)
INSERT INTO usuarios (nome, email, senha, tipo) VALUES 
('Administrador', 'admin@minipizza.com', '$2y$10$8tqwm3m5Z1z3w4X5v6u7Y.oQ2Q3p4R5t6S7u8V9w0X1y2N3b4M5n6', 'admin');

-- Inserir algumas categorias básicas
INSERT INTO categorias (nome, descricao) VALUES 
('Pizzas Tradicionais', 'Pizzas com sabores clássicos e tradicionais'),
('Pizzas Especiais', 'Pizzas com ingredientes premium e combinações especiais'),
('Bebidas', 'Refrigerantes, sucos e bebidas em geral'); 