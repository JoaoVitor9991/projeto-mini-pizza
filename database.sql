-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS mini_pizza CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mini_pizza;

-- Tabela de usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('cliente', 'admin') NOT NULL DEFAULT 'cliente',
    cpf VARCHAR(14) UNIQUE,
    telefone VARCHAR(15),
    data_cadastro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ativo BOOLEAN NOT NULL DEFAULT TRUE
);

-- Tabela de categorias
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE,
    descricao TEXT,
    ordem INT DEFAULT 0,
    ativo BOOLEAN NOT NULL DEFAULT TRUE
);

-- Tabela de produtos
CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    imagem VARCHAR(255),
    ordem INT DEFAULT 0,
    ativo BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- Tabela de pedidos
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    numero VARCHAR(20) NOT NULL,
    complemento VARCHAR(100),
    bairro VARCHAR(100) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    telefone VARCHAR(15) NOT NULL,
    observacoes TEXT,
    subtotal DECIMAL(10,2) NOT NULL,
    taxa_entrega DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pendente', 'confirmado', 'preparando', 'saiu_entrega', 'entregue', 'cancelado') NOT NULL DEFAULT 'pendente',
    data_pedido DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabela de itens do pedido
CREATE TABLE itens_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

-- Tabela de logs
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    acao VARCHAR(50) NOT NULL,
    descricao TEXT,
    data_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ip VARCHAR(45),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Inserir usuário admin padrão (senha: admin123)
INSERT INTO usuarios (nome, email, senha, tipo) VALUES 
('Administrador', 'admin@pizzariaamatsu.com', '$2y$10$8tDjcgyZ9WcyVg5Qb.AKVOdqvZe3QvPGJO3PB7HF8IWfNmtKjKhPi', 'admin');

-- Inserir algumas categorias
INSERT INTO categorias (nome, descricao) VALUES
('Pizzas Tradicionais', 'Pizzas com sabores clássicos e queridos por todos'),
('Pizzas Especiais', 'Pizzas com combinações únicas e ingredientes selecionados'),
('Pizzas Doces', 'Deliciosas pizzas com coberturas doces'),
('Bebidas', 'Refrigerantes, sucos e outras bebidas');

-- Inserir alguns produtos
INSERT INTO produtos (categoria_id, nome, descricao, preco, ordem) VALUES
(1, 'Pizza Margherita', 'Molho de tomate, mussarela, manjericão fresco e azeite', 35.90, 1),
(1, 'Pizza Calabresa', 'Molho de tomate, mussarela, calabresa fatiada e cebola', 38.90, 2),
(1, 'Pizza Portuguesa', 'Molho de tomate, mussarela, presunto, ovos, cebola e ervilha', 40.90, 3),
(2, 'Pizza 4 Queijos', 'Molho de tomate, mussarela, provolone, gorgonzola e parmesão', 45.90, 1),
(2, 'Pizza Frango com Catupiry', 'Molho de tomate, frango desfiado, catupiry e milho', 42.90, 2),
(3, 'Pizza de Chocolate', 'Chocolate ao leite derretido e granulado', 39.90, 1),
(3, 'Pizza de Banana', 'Banana, canela, leite condensado e chocolate', 39.90, 2),
(4, 'Coca-Cola 2L', 'Refrigerante Coca-Cola 2 litros', 12.90, 1),
(4, 'Guaraná Antarctica 2L', 'Refrigerante Guaraná Antarctica 2 litros', 10.90, 2),
(4, 'Suco de Laranja 500ml', 'Suco natural de laranja', 8.90, 3); 