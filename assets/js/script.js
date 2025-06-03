// Função para adicionar produto ao carrinho
function adicionarAoCarrinho(produtoId, quantidade = 1) {
    // Adicionar efeito visual ao botão
    const botao = event.target.closest('.btn-adicionar');
    botao.classList.add('adicionando');
    
    fetch('includes/adicionar_carrinho.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ produto_id: produtoId, quantidade: quantidade })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Atualizar ícone do carrinho
            const carrinhoFlutuante = document.getElementById('carrinho-flutuante');
            const quantidadeSpan = carrinhoFlutuante.querySelector('.carrinho-quantidade');
            
            if (data.total_itens > 0) {
                carrinhoFlutuante.classList.add('tem-itens');
                if (quantidadeSpan) {
                    quantidadeSpan.textContent = data.total_itens;
                } else {
                    const span = document.createElement('span');
                    span.className = 'carrinho-quantidade';
                    span.textContent = data.total_itens;
                    carrinhoFlutuante.appendChild(span);
                }
            } else {
                carrinhoFlutuante.classList.remove('tem-itens');
                if (quantidadeSpan) {
                    quantidadeSpan.remove();
                }
            }
            
            // Criar e mostrar o mini-carrinho
            const produtoCard = botao.closest('.produto-card');
            const produtoNome = produtoCard.querySelector('h3').textContent;
            const produtoPreco = produtoCard.querySelector('.produto-preco').textContent;
            
            mostrarMiniCarrinho(produtoNome, produtoPreco);
            
            // Remover classe de animação após 1 segundo
            setTimeout(() => {
                botao.classList.remove('adicionando');
            }, 1000);
            
        } else {
            mostrarMensagem(data.mensagem || 'Erro ao adicionar produto ao carrinho', 'error');
            botao.classList.remove('adicionando');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarMensagem('Erro ao adicionar produto ao carrinho', 'error');
        botao.classList.remove('adicionando');
    });
}

// Função para mostrar o mini-carrinho
function mostrarMiniCarrinho(nome, preco) {
    // Remover mini-carrinho anterior se existir
    const miniCarrinhoAntigo = document.querySelector('.mini-carrinho');
    if (miniCarrinhoAntigo) {
        miniCarrinhoAntigo.remove();
    }
    
    // Criar o mini-carrinho
    const miniCarrinho = document.createElement('div');
    miniCarrinho.className = 'mini-carrinho';
    miniCarrinho.innerHTML = `
        <div class="mini-carrinho-conteudo">
            <i class="fas fa-check-circle"></i>
            <div class="mini-carrinho-texto">
                <p>Item adicionado ao carrinho!</p>
                <p class="mini-carrinho-item">${nome}</p>
                <p class="mini-carrinho-preco">${preco}</p>
            </div>
            <a href="carrinho.php" class="btn-ver-carrinho">Ver Carrinho</a>
        </div>
    `;
    
    // Adicionar ao corpo do documento
    document.body.appendChild(miniCarrinho);
    
    // Adicionar classe para animar a entrada
    setTimeout(() => {
        miniCarrinho.classList.add('mostrar');
    }, 100);
    
    // Remover após 5 segundos
    setTimeout(() => {
        miniCarrinho.classList.remove('mostrar');
        setTimeout(() => {
            miniCarrinho.remove();
        }, 300);
    }, 5000);
}

// Função para mostrar mensagens
function mostrarMensagem(texto, tipo) {
    const mensagem = document.createElement('div');
    mensagem.className = `mensagem-flutuante ${tipo}`;
    mensagem.textContent = texto;
    
    document.body.appendChild(mensagem);
    
    // Animar entrada
    setTimeout(() => {
        mensagem.classList.add('mostrar');
    }, 100);
    
    // Remover após 3 segundos
    setTimeout(() => {
        mensagem.classList.remove('mostrar');
        setTimeout(() => {
            mensagem.remove();
        }, 300);
    }, 3000);
}

// Função para filtrar produtos por categoria
function filtrarProdutos(categoriaId) {
    const sections = document.querySelectorAll('.categoria-section');
    
    if (categoriaId === 'todas') {
        sections.forEach(section => {
            section.style.display = 'block';
        });
    } else {
        sections.forEach(section => {
            if (section.dataset.categoriaId === categoriaId) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });
    }
    
    // Atualizar link ativo
    const links = document.querySelectorAll('.categorias-nav a');
    links.forEach(link => {
        if (link.dataset.categoriaId === categoriaId) {
            link.classList.add('ativo');
        } else {
            link.classList.remove('ativo');
        }
    });
}

// Função para pesquisar produtos
function pesquisarProdutos(termo) {
    const produtos = document.querySelectorAll('.produto-card');
    const termoBusca = termo.toLowerCase().trim();
    
    produtos.forEach(produto => {
        const nome = produto.querySelector('h3').textContent.toLowerCase();
        const descricao = produto.querySelector('.produto-descricao').textContent.toLowerCase();
        
        if (nome.includes(termoBusca) || descricao.includes(termoBusca)) {
            produto.style.display = 'block';
        } else {
            produto.style.display = 'none';
        }
    });
    
    // Mostrar/ocultar mensagem de nenhum resultado
    const sections = document.querySelectorAll('.categoria-section');
    sections.forEach(section => {
        const produtosVisiveis = section.querySelectorAll('.produto-card[style="display: block"]');
        const mensagemVazia = section.querySelector('.categoria-vazia');
        
        if (produtosVisiveis.length === 0) {
            section.style.display = 'none';
        } else {
            section.style.display = 'block';
        }
    });
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    // Pesquisa
    const campoPesquisa = document.getElementById('pesquisa');
    if (campoPesquisa) {
        let timeoutPesquisa;
        campoPesquisa.addEventListener('input', (e) => {
            clearTimeout(timeoutPesquisa);
            timeoutPesquisa = setTimeout(() => {
                pesquisarProdutos(e.target.value);
            }, 300);
        });
    }
    
    // Filtro de categorias
    const linksCategorias = document.querySelectorAll('.categorias-nav a');
    linksCategorias.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            filtrarProdutos(link.dataset.categoriaId);
        });
    });
    
    // Máscara para telefone
    const telefone = document.getElementById('telefone');
    if (telefone) {
        telefone.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            
            if (value.length > 2) {
                value = '(' + value.slice(0, 2) + ') ' + value.slice(2);
            }
            if (value.length > 9) {
                value = value.slice(0, 9) + '-' + value.slice(9);
            }
            
            e.target.value = value;
        });
    }
    
    // Preview de imagem
    const inputImagem = document.getElementById('imagem');
    const previewImagem = document.getElementById('preview-imagem');
    
    if (inputImagem && previewImagem) {
        inputImagem.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImagem.src = e.target.result;
                    previewImagem.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }
});

// Rolagem suave para as categorias
document.querySelectorAll('.categorias-nav a').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const id = this.getAttribute('href').substring(1);
        const elemento = document.getElementById(id);
        if (elemento) {
            const headerHeight = document.querySelector('.header').offsetHeight;
            const y = elemento.getBoundingClientRect().top + window.pageYOffset - headerHeight - 20;
            window.scrollTo({top: y, behavior: 'smooth'});
        }
    });
});

// Adicionar estilos CSS para mensagens flutuantes
const style = document.createElement('style');
style.textContent = `
    .mensagem-flutuante {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 2rem;
        border-radius: 4px;
        color: #fff;
        font-weight: 500;
        z-index: 1000;
        animation: slideIn 0.3s ease-out;
    }
    
    .mensagem-success {
        background-color: #28a745;
    }
    
    .mensagem-error {
        background-color: #dc3545;
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
`;
document.head.appendChild(style); 