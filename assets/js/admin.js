// Toggle do menu lateral
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.querySelector('.admin-sidebar');
    const content = document.querySelector('.admin-content');
    
    if (toggleBtn && sidebar && content) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            content.style.marginLeft = sidebar.classList.contains('active') ? '0' : 'var(--sidebar-width)';
        });
    }
    
    // Fechar menu ao clicar fora em dispositivos móveis
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768 && 
            !sidebar.contains(e.target) && 
            !toggleBtn.contains(e.target) && 
            sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
            content.style.marginLeft = '0';
        }
    });
});

// Confirmação de exclusão
function confirmarExclusao(mensagem = 'Tem certeza que deseja excluir este item?') {
    return confirm(mensagem);
}

// Formatação de moeda
function formatarMoeda(valor) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(valor);
}

// Formatação de data
function formatarData(data) {
    return new Intl.DateTimeFormat('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(new Date(data));
}

// Preview de imagem
function previewImagem(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        const preview = document.getElementById(previewId);
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Máscara para campos de moeda
function mascaraMoeda(campo) {
    campo.addEventListener('input', function(e) {
        let valor = e.target.value.replace(/\D/g, '');
        valor = (valor/100).toFixed(2);
        e.target.value = valor;
    });
}

// Atualizar status do pedido
function atualizarStatusPedido(pedidoId, novoStatus) {
    fetch('../../admin/includes/atualizar_status_pedido.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            pedido_id: pedidoId,
            status: novoStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Atualizar a interface
            const statusBadge = document.querySelector(`#pedido-${pedidoId} .status-badge`);
            if (statusBadge) {
                statusBadge.className = `status-badge status-${novoStatus}`;
                statusBadge.textContent = novoStatus.charAt(0).toUpperCase() + novoStatus.slice(1).replace('_', ' ');
            }
            alert('Status atualizado com sucesso!');
            // Recarregar a página para atualizar todas as informações
            window.location.reload();
        } else {
            alert(data.mensagem);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao atualizar status do pedido.');
    });
}

// Busca dinâmica em tabelas
function buscarNaTabela() {
    const input = document.getElementById('busca');
    const tabela = document.querySelector('.admin-table');
    
    if (input && tabela) {
        input.addEventListener('keyup', function() {
            const termo = this.value.toLowerCase();
            const linhas = tabela.getElementsByTagName('tr');
            
            for (let i = 1; i < linhas.length; i++) {
                const linha = linhas[i];
                const texto = linha.textContent.toLowerCase();
                linha.style.display = texto.includes(termo) ? '' : 'none';
            }
        });
    }
}

// Ordenação de tabelas
function ordenarTabela(coluna, tipo) {
    const tabela = document.querySelector('.admin-table');
    const linhas = Array.from(tabela.getElementsByTagName('tr'));
    const [cabecalho, ...corpo] = linhas;
    
    corpo.sort((a, b) => {
        const valorA = a.children[coluna].textContent;
        const valorB = b.children[coluna].textContent;
        
        if (tipo === 'numero') {
            return parseFloat(valorA) - parseFloat(valorB);
        } else if (tipo === 'data') {
            return new Date(valorA) - new Date(valorB);
        } else {
            return valorA.localeCompare(valorB);
        }
    });
    
    corpo.forEach(linha => tabela.appendChild(linha));
}

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    // Iniciar busca dinâmica
    buscarNaTabela();
    
    // Aplicar máscara em campos de moeda
    const camposMoeda = document.querySelectorAll('.campo-moeda');
    camposMoeda.forEach(campo => mascaraMoeda(campo));
}); 