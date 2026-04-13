/**
 * COMANDAS DIGITAIS - JAVASCRIPT PRINCIPAL
 */

// ===== FORMATAÇÃO E UTILITÁRIOS =====
function formatarMoeda(valor) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(valor);
}

function formatarCPF(cpf) {
    cpf = cpf.replace(/\D/g, '');
    if (cpf.length !== 11) return cpf;
    return cpf.substring(0, 3) + '.' + cpf.substring(3, 6) + '.' + 
           cpf.substring(6, 9) + '-' + cpf.substring(9);
}

function sanitizar(texto) {
    const div = document.createElement('div');
    div.textContent = texto;
    return div.innerHTML;
}

// ===== CARRINHO (Cliente) =====
let carrinho = {};
const carrinhoStorage = localStorage.getItem('comandas_carrinho');
if (carrinhoStorage) {
    carrinho = JSON.parse(carrinhoStorage);
}

function salvarCarrinho() {
    localStorage.setItem('comandas_carrinho', JSON.stringify(carrinho));
    atualizarUI();
}

function adicionarAoCarrinho(id, nome, preco, quantidade = 1) {
    if (id in carrinho) {
        carrinho[id].quantidade += quantidade;
    } else {
        carrinho[id] = {
            id: id,
            nome: nome,
            preco: parseFloat(preco),
            quantidade: quantidade
        };
    }
    salvarCarrinho();
    mostrarNotificacao(`${nome} adicionado ao carrinho!`);
}

function removerDoCarrinho(id) {
    delete carrinho[id];
    salvarCarrinho();
}

function atualizarQuantidade(id, quantidade) {
    quantidade = parseInt(quantidade);
    if (quantidade <= 0) {
        removerDoCarrinho(id);
    } else {
        if (id in carrinho) {
            carrinho[id].quantidade = quantidade;
            salvarCarrinho();
        }
    }
}

function atualizarUI() {
    const cartCount = document.getElementById('cart-count');
    const carrinhoItems = document.getElementById('carrinho-items');
    const totalCarrinho = document.getElementById('total-carrinho');
    const btnCheckout = document.getElementById('btn-checkout');
    
    if (!cartCount) return;

    // Contar total de itens
    let totalItens = 0;
    let totalValor = 0;

    for (let id in carrinho) {
        totalItens += carrinho[id].quantidade;
        totalValor += carrinho[id].preco * carrinho[id].quantidade;
    }

    if (cartCount) cartCount.textContent = totalItens;

    // Renderizar itens do carrinho
    if (carrinhoItems) {
        if (totalItens === 0) {
            carrinhoItems.innerHTML = '<p class="carrinho-vazio">Seu carrinho está vazio</p>';
            if (btnCheckout) btnCheckout.disabled = true;
        } else {
            let html = '';
            for (let id in carrinho) {
                const item = carrinho[id];
                const subtotal = item.preco * item.quantidade;
                html += `
                    <div class="carrinho-item">
                        <div class="item-details">
                            <div class="item-name">${sanitizar(item.nome)}</div>
                            <div class="item-quantity">
                                <button onclick="atualizarQuantidade(${id}, ${item.quantidade - 1})">-</button>
                                <input type="number" value="${item.quantidade}" 
                                       onchange="atualizarQuantidade(${id}, this.value)" min="1">
                                <button onclick="atualizarQuantidade(${id}, ${item.quantidade + 1})">+</button>
                            </div>
                        </div>
                        <div class="item-price">${formatarMoeda(subtotal)}</div>
                        <button class="btn-remover" onclick="removerDoCarrinho(${id})">✕</button>
                    </div>
                `;
            }
            carrinhoItems.innerHTML = html;
            if (btnCheckout) btnCheckout.disabled = false;
        }
    }

    if (totalCarrinho) totalCarrinho.textContent = formatarMoeda(totalValor);
}

function abrirCarrinho() {
    const modal = document.getElementById('carrinho-modal');
    if (modal) {
        modal.classList.add('active');
        atualizarUI();
    }
}

function fecharCarrinho() {
    const modal = document.getElementById('carrinho-modal');
    if (modal) {
        modal.classList.remove('active');
    }
}

function irParaCheckout() {
    const totalItens = Object.keys(carrinho).length;
    if (totalItens === 0) {
        alert('Seu carrinho está vazio!');
        return;
    }
    
    // Enviar carrinho via POST
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'checkout.php';
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'carrinho';
    input.value = JSON.stringify(carrinho);
    
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
}

// ===== FILTRO DE CATEGORIAS =====
function mostrarCategoria(categoria) {
    const sections = document.querySelectorAll('.categoria-section');
    const buttons = document.querySelectorAll('.category-btn');
    
    buttons.forEach(btn => btn.classList.remove('active'));
    sections.forEach(section => section.classList.remove('active'));
    
    if (categoria === 'TODAS') {
        sections.forEach(section => section.classList.add('active'));
        buttons[0].classList.add('active');
    } else {
        const section = document.querySelector(`[data-category="${categoria}"]`);
        if (section) {
            section.classList.add('active');
        }
        
        event.target.classList.add('active');
    }
}

// ===== NOTIFICAÇÕES =====
function mostrarNotificacao(mensagem, tipo = 'sucesso') {
    const nota = document.createElement('div');
    nota.className = `notificacao notificacao-${tipo}`;
    nota.textContent = mensagem;
    nota.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${tipo === 'erro' ? '#f44336' : '#4caf50'};
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        animation: slideIn 0.3s ease;
        z-index: 10000;
    `;
    
    document.body.appendChild(nota);
    
    setTimeout(() => {
        nota.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => nota.remove(), 300);
    }, 3000);
}

// ===== FECHAR MODAL AO CLICAR FORA =====
document.addEventListener('click', function(event) {
    const modal = document.getElementById('carrinho-modal');
    if (modal && event.target === modal) {
        fecharCarrinho();
    }
});

// ===== MÁSCARAS DE INPUT =====
document.addEventListener('DOMContentLoaded', function() {
    // Máscara de CPF
    const cpfInputs = document.querySelectorAll('input[name="cpf"], input[name="cliente_cpf"]');
    cpfInputs.forEach(input => {
        input.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            
            if (value.length <= 3) {
                this.value = value;
            } else if (value.length <= 6) {
                this.value = value.substring(0, 3) + '.' + value.substring(3);
            } else if (value.length <= 9) {
                this.value = value.substring(0, 3) + '.' + value.substring(3, 6) + '.' + value.substring(6);
            } else {
                this.value = value.substring(0, 3) + '.' + value.substring(3, 6) + '.' + 
                           value.substring(6, 9) + '-' + value.substring(9);
            }
        });
    });

    // Máscara de Telefone
    const phoneInputs = document.querySelectorAll('input[name="cliente_telefone"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            
            if (value.length <= 2) {
                this.value = value;
            } else if (value.length <= 7) {
                this.value = '(' + value.substring(0, 2) + ') ' + value.substring(2);
            } else {
                this.value = '(' + value.substring(0, 2) + ') ' + value.substring(2, 7) + '-' + value.substring(7);
            }
        });
    });

    // Inicializar carrinho
    atualizarUI();
    
    // Mostrar primeira categoria
    const firstBtn = document.querySelector('.category-btn');
    if (firstBtn) {
        firstBtn.click();
    }
});

// ===== ANIMAÇÕES CSS =====
const style = document.createElement('style');
style.textContent = `
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

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from {
            transform: translateY(100%);
        }
        to {
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);
