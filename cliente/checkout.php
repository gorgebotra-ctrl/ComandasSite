<?php
/**
 * Checkout - Cliente
 */

require_once '../config/database.php';
require_once '../config/functions.php';

header('Content-Type: text/html; charset=utf-8');

$carrinho = $_SESSION['carrinho'] ?? [];
$total = 0;
$itens_count = 0;

// Calcular total e itens
foreach ($carrinho as $item) {
    $total += $item['preco'] * $item['quantidade'];
    $itens_count += $item['quantidade'];
}

// Se não tem nada no carrinho, redirecionar
if (empty($carrinho)) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Pedido - Comandas Digitais</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <main class="checkout-container">
        <div class="checkout-wrapper">
            <!-- Lado Esquerdo - Formulário -->
            <div class="checkout-form-section">
                <div class="form-header">
                    <a href="index.php" class="btn-voltar">← Voltar</a>
                    <h1>Finalizar Pedido</h1>
                </div>

                <form id="checkout-form" method="POST" action="../api/pedidos.php" class="checkout-form">
                    <!-- Dados do Cliente -->
                    <div class="form-section">
                        <h3>Dados Pessoais</h3>
                        
                        <div class="form-group">
                            <label for="cliente_nome">Nome Completo *</label>
                            <input type="text" id="cliente_nome" name="cliente_nome" required>
                        </div>

                        <div class="form-group">
                            <label for="cliente_cpf">CPF *</label>
                            <input type="text" id="cliente_cpf" name="cliente_cpf" placeholder="000.000.000-00" required>
                        </div>

                        <div class="form-group">
                            <label for="cliente_telefone">Telefone *</label>
                            <input type="tel" id="cliente_telefone" name="cliente_telefone" placeholder="(11) 9999-9999" required>
                        </div>
                    </div>

                    <!-- Endereço -->
                    <div class="form-section">
                        <h3>Endereço de Entrega</h3>
                        
                        <div class="form-group">
                            <label for="cliente_endereco">Endereço Completo *</label>
                            <textarea id="cliente_endereco" name="cliente_endereco" rows="2" placeholder="Rua, número, complemento, bairro, cidade" required></textarea>
                        </div>
                    </div>

                    <!-- Método de Pagamento -->
                    <div class="form-section">
                        <h3>Método de Pagamento</h3>
                        
                        <div class="payment-options">
                            <label class="payment-option">
                                <input type="radio" name="metodo_pagamento" value="dinheiro" checked onchange="ao_mudar_pagamento()">
                                <span class="payment-label">💵 Dinheiro</span>
                            </label>

                            <label class="payment-option">
                                <input type="radio" name="metodo_pagamento" value="cartao" onchange="ao_mudar_pagamento()">
                                <span class="payment-label">💳 Cartão</span>
                            </label>

                            <label class="payment-option">
                                <input type="radio" name="metodo_pagamento" value="pix" onchange="ao_mudar_pagamento()">
                                <span class="payment-label">📱 PIX</span>
                            </label>
                        </div>

                        <!-- Seção PIX (inicialmente oculta) -->
                        <div id="pix-section" style="display: none;" class="pix-section">
                            <p class="info-pix">Escaneie o QR Code abaixo para efetuar o pagamento via PIX:</p>
                            <div id="pix-qrcode" class="pix-qrcode">
                                <img id="qrcode-img" src="" alt="QR Code PIX">
                            </div>
                            <label class="checkbox-pix">
                                <input type="checkbox" id="pix-confirmado" name="pix_confirmado">
                                <span>Confirmo que realizei o pagamento via PIX</span>
                            </label>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div class="form-section">
                        <h3>Observações (Opcional)</h3>
                        <textarea id="observacoes" name="observacoes" rows="3" placeholder="Deixe suas observações..."></textarea>
                    </div>

                    <button type="submit" class="btn-primary btn-grande" id="btn-enviar-pedido">
                        Confirmar Pedido
                    </button>
                </form>
            </div>

            <!-- Lado Direito - Resumo -->
            <aside class="checkout-resume-section">
                <div class="resume-box">
                    <h3>Resumo do Pedido</h3>

                    <div id="itens-resume" class="itens-resume">
                        <?php foreach ($carrinho as $id => $item): ?>
                            <div class="item-resume">
                                <span><?php echo sanitizar($item['nome']); ?> x <?php echo $item['quantidade']; ?></span>
                                <span><?php echo formatarMoeda($item['preco'] * $item['quantidade']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="resume-divider"></div>

                    <div class="resume-total">
                        <strong>Total do Pedido</strong>
                        <strong id="total-final"><?php echo formatarMoeda($total); ?></strong>
                    </div>

                    <input type="hidden" id="carrinhoData" name="carrinho" value='<?php echo json_encode($carrinho); ?>'>
                </div>
            </aside>
        </div>
    </main>

    <script src="../assets/js/script.js"></script>
    <script>
        const carrinhoData = <?php echo json_encode($carrinho); ?>;
        const totalPedido = <?php echo $total; ?>;

        function ao_mudar_pagamento() {
            const metodo = document.querySelector('input[name="metodo_pagamento"]:checked').value;
            const pixSection = document.getElementById('pix-section');
            const pixConfirmado = document.getElementById('pix-confirmado');

            if (metodo === 'pix') {
                pixSection.style.display = 'block';
                gerarQRCodePIX();
            } else {
                pixSection.style.display = 'none';
                pixConfirmado.checked = false;
            }
        }

        function gerarQRCodePIX() {
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=PIX-PAYMENT-${totalPedido.toFixed(2).replace('.', '')}`;
            document.getElementById('qrcode-img').src = qrUrl;
        }

        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const metodo = document.querySelector('input[name="metodo_pagamento"]:checked').value;
            const pixConfirmado = document.getElementById('pix-confirmado').checked;

            if (metodo === 'pix' && !pixConfirmado) {
                alert('Confirme o pagamento via PIX para continuar');
                return;
            }

            this.submit();
        });
    </script>
</body>
</html>
