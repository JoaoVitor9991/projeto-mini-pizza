# Pizzaria do Amatsu - Sistema de Pedidos Online

Sistema web para gerenciamento de pedidos de uma pizzaria, desenvolvido em PHP com MySQL.

## Funcionalidades

### Área do Cliente
- Cardápio digital com fotos dos produtos
- Carrinho de compras intuitivo
- Sistema de pedidos online
- Acompanhamento de pedidos em tempo real
- Cadastro e login de usuários
- Histórico de pedidos

### Área Administrativa
- Gerenciamento de produtos
- Gerenciamento de categorias
- Controle de pedidos
- Relatórios de vendas
- Gestão de usuários

## Produtos Disponíveis

### Pizzas Salgadas
- Pizza Margherita
- Pizza Calabresa
- Pizza Portuguesa
- Pizza 4 Queijos
- Pizza Frango com Catupiry

### Pizzas Doces
- Pizza de Chocolate com Confete
- Pizza de Banana

### Bebidas
- Suco de Laranja
- Guaraná 2L
- Coca-Cola 2L

## Requisitos do Sistema

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor Apache (XAMPP recomendado)
- Extensões PHP necessárias:
  - PDO
  - PDO_MySQL
  - GD (para imagens)

## Instalação

1. Instale o XAMPP em seu computador
2. Clone este repositório na pasta htdocs do XAMPP:
```bash
cd C:/xampp/htdocs
git clone [URL_DO_REPOSITÓRIO]
```

3. Importe o banco de dados:
- Abra o phpMyAdmin (http://localhost/phpmyadmin)
- Crie um novo banco de dados chamado `mini_pizza`
- Importe o arquivo `database.sql`

4. Configure o acesso ao banco de dados:
- Abra o arquivo `includes/config.php`
- Verifique se as configurações de conexão estão corretas:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'mini_pizza');
define('DB_USER', 'root');
define('DB_PASS', '');
```

5. Acesse o sistema:
- Frontend: http://localhost/pizzaria-amatsu
- Área Administrativa: http://localhost/pizzaria-amatsu/admin

## Estrutura do Projeto

```
pizzaria-amatsu/
├── admin/              # Área administrativa
├── assets/
│   ├── css/           # Arquivos de estilo
│   ├── js/            # Scripts JavaScript
│   └── img/           # Imagens do sistema
├── includes/          # Arquivos de configuração e funções
└── database.sql      # Estrutura do banco de dados
```

## Customização

### Alterando Preços
Os preços dos produtos podem ser alterados através do painel administrativo:
1. Acesse a área administrativa
2. Vá para "Produtos"
3. Clique em "Editar" no produto desejado
4. Atualize o preço
5. Clique em "Salvar"

### Taxa de Entrega
A taxa de entrega padrão é R$ 5,00 e pode ser alterada no arquivo `includes/config.php`:
```php
define('TAXA_ENTREGA', 5.00);
```

## Suporte

Em caso de problemas ou dúvidas:
1. Verifique se todos os requisitos do sistema estão atendidos
2. Confira as configurações do banco de dados
3. Verifique as permissões das pastas
4. Entre em contato através do email: [SEU_EMAIL]

## Licença

Este projeto está sob a licença MIT. Consulte o arquivo LICENSE para mais detalhes.

