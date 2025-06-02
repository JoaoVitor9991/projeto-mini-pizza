// Produtos exemplo
const produtos = [
    {
        id: 1,
        nome: 'Pizza Margherita',
        descricao: 'Molho de tomate, mussarela, manjericão fresco',
        preco: 35.90,
        imagem: 'assets/img/margherita.jpg'
    },
    {
        id: 2,
        nome: 'Pizza Pepperoni',
        descricao: 'Molho de tomate, mussarela, pepperoni',
        preco: 39.90,
        imagem: 'assets/img/pepperoni.jpg'
    },
    {
        id: 3,
        nome: 'Pizza Portuguesa',
        descricao: 'Molho de tomate, mussarela, presunto, ovos, cebola, azeitonas',
        preco: 42.90,
        imagem: 'assets/img/portuguesa.jpg'
    }
];

// Carregar produtos na página
function carregarProdutos() {
    const produtosGrid = document.querySelector('.produtos-grid');
    if (!produtosGrid) return;

    produtos.forEach(produto => {
        const produtoElement = document.createElement('div');
        produtoElement.className = 'produto-card';
        produtoElement.innerHTML = `
            <img src="${produto.imagem}" alt="${produto.nome}">
            <h3>${produto.nome}</h3>
            <p>${produto.descricao}</p>
            <p class="preco">R$ ${produto.preco.toFixed(2)}</p>
            <button onclick="adicionarAoCarrinho(${produto.id})" class="btn-primary">Adicionar ao Carrinho</button>
        `;
        produtosGrid.appendChild(produtoElement);
    });
}

// Validação do formulário de contato
function validarFormularioContato() {
    const form = document.getElementById('formContato');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const nome = document.getElementById('nome').value.trim();
        const email = document.getElementById('email').value.trim();
        const mensagem = document.getElementById('mensagem').value.trim();
        let isValid = true;
        let errorMessage = '';

        // Validação do nome
        if (nome.length < 3) {
            isValid = false;
            errorMessage += 'Nome deve ter pelo menos 3 caracteres.\n';
        }

        // Validação do email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            isValid = false;
            errorMessage += 'Email inválido.\n';
        }

        // Validação da mensagem
        if (mensagem.length < 10) {
            isValid = false;
            errorMessage += 'Mensagem deve ter pelo menos 10 caracteres.\n';
        }

        if (isValid) {
            // Aqui você pode adicionar o código para enviar o formulário
            alert('Mensagem enviada com sucesso!');
            form.reset();
        } else {
            alert(errorMessage);
        }
    });
}

// Menu mobile
function initMobileMenu() {
    const navbar = document.querySelector('.navbar');
    const navLinks = document.querySelector('.nav-links');
    
    if (!navbar || !navLinks) return;

    const menuButton = document.createElement('button');
    menuButton.className = 'menu-toggle';
    menuButton.innerHTML = '<i class="fas fa-bars"></i>';
    
    navbar.insertBefore(menuButton, navLinks);

    menuButton.addEventListener('click', () => {
        navLinks.classList.toggle('active');
        menuButton.innerHTML = navLinks.classList.contains('active') 
            ? '<i class="fas fa-times"></i>' 
            : '<i class="fas fa-bars"></i>';
    });
}

// Smooth scroll para links internos
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
}

// Inicialização
document.addEventListener('DOMContentLoaded', () => {
    carregarProdutos();
    validarFormularioContato();
    initMobileMenu();
    initSmoothScroll();
}); 