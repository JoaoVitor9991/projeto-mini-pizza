# Mini Pizza - Sistema de Pedidos

Sistema web para gerenciamento de pedidos de uma pizzaria, desenvolvido em PHP com MySQL.

## Funcionalidades

### Área Administrativa
- Gerenciamento de produtos (CRUD)
- Gerenciamento de categorias (CRUD)
- Gerenciamento de pedidos
- Upload de imagens
- Relatórios

### Área do Cliente
- Catálogo de produtos
- Carrinho de compras
- Finalização de pedidos
- Acompanhamento de pedidos

## Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx)
- Extensões PHP:
  - PDO
  - PDO_MySQL
  - GD (para manipulação de imagens)
  - FileInfo

## Instalação

1. Clone o repositório:
```bash
git clone https://github.com/seu-usuario/mini-pizza.git
cd mini-pizza
```

2. Crie o banco de dados e importe a estrutura:
```bash
mysql -u root -p < database.sql
```

3. Configure o acesso ao banco de dados:
   - Abra o arquivo `includes/config.php`
   - Altere as constantes `DB_HOST`, `DB_NAME`, `DB_USER` e `DB_PASS` com suas configurações

4. Configure as permissões:
```bash
chmod 777 uploads/
chmod 777 uploads/produtos/
```

5. Acesse o sistema:
   - Frontend: `http://localhost/mini-pizza`
   - Admin: `http://localhost/mini-pizza/admin`
   - Credenciais padrão:
     - Email: admin@minipizza.com
     - Senha: admin123

## Estrutura de Arquivos

```
mini-pizza/
├── admin/
│   ├── categorias/
│   │   ├── adicionar.php
│   │   ├── editar.php
│   │   ├── excluir.php
│   │   └── listar.php
│   ├── produtos/
│   │   ├── adicionar.php
│   │   ├── editar.php
│   │   ├── excluir.php
│   │   └── listar.php
│   └── index.php
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── style.css
│   ├── js/
│   │   └── script.js
│   └── img/
├── includes/
│   ├── config.php
│   ├── header.php
│   └── footer.php
├── uploads/
│   └── produtos/
├── index.php
├── carrinho.php
├── finalizar-pedido.php
├── meus-pedidos.php
├── database.sql
└── README.md
```

## Funcionalidades Principais

### Gerenciamento de Produtos
- Listagem com filtros e busca
- Adição com upload de imagens
- Edição de dados e imagens
- Exclusão com verificação de pedidos

### Gerenciamento de Categorias
- Listagem com contagem de produtos
- CRUD completo
- Validações de exclusão

### Pedidos
- Carrinho de compras
- Cálculo de subtotal e frete
- Endereço de entrega
- Status do pedido
- Histórico de pedidos

### Segurança
- Autenticação de usuários
- Controle de acesso
- Validação de formulários
- Proteção contra SQL Injection
- Upload seguro de arquivos

## Customização

### Cores
As cores do sistema podem ser customizadas através das variáveis CSS no arquivo `assets/css/style.css`:

```css
:root {
    --primary-color: #ff4d4d;
    --secondary-color: #333;
    --success-color: #28a745;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --info-color: #17a2b8;
}
```

### Taxa de Entrega
A taxa de entrega pode ser alterada no arquivo `includes/config.php`:

```php
define('TAXA_ENTREGA', 5.00);
```

## Contribuição

1. Faça um Fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## Licença

Este projeto está licenciado sob a licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

## Suporte

Em caso de dúvidas ou problemas, abra uma issue no GitHub ou entre em contato através do email: suporte@minipizza.com

