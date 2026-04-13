<?php
/**
 * API - Processar Pedido
 */

header('Content-Type: application/json');

require_once '../config/database.php';
require_once '../config/functions.php';

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderJSON(['erro' => 'Método inválido'], 405);
}

// Obter dados
$cliente_nome = sanitizar($_POST['cliente_nome'] ?? '');
$cliente_cpf = sanitizar($_POST['cliente_cpf'] ?? '');
$cliente_telefone = sanitizar($_POST['cliente_telefone'] ?? '');
$cliente_endereco = sanitizar($_POST['cliente_endereco'] ?? '');
$metodo_pagamento = sanitizar($_POST['metodo_pagamento'] ?? '');
$observacoes = sanitizar($_POST['observacoes'] ?? '');
$carrinho = json_decode($_POST['carrinho'] ?? '[]', true);

// Validar dados
$erros = [];

if (empty($cliente_nome)) {
    $erros[] = 'Nome do cliente é obrigatório';
}

if (empty($cliente_telefone)) {
    $erros[] = 'Telefone é obrigatório';
}

if (empty($cliente_endereco)) {
    $erros[] = 'Endereço é obrigatório';
}

if (empty($metodo_pagamento) || !in_array($metodo_pagamento, ['dinheiro', 'cartao', 'pix'])) {
    $erros[] = 'Método de pagamento inválido';
}

if (!validarCPF($cliente_cpf)) {
    $erros[] = 'CPF inválido';
}

if (empty($carrinho)) {
    $erros[] = 'Carrinho vazio';
}

if (!empty($erros)) {
    responderJSON(['erro' => implode(', ', $erros)], 400);
}

// Calcular total
$total = 0;
foreach ($carrinho as $item) {
    $total += $item['preco'] * $item['quantidade'];
}

// Remove caracteres especiais do CPF
$cliente_cpf_limpo = preg_replace('/[^0-9]/', '', $cliente_cpf);

try {
    // Iniciar transação
    $conn->beginTransaction();
    
    // Inserir pedido
    $stmt = $conn->prepare("
        INSERT INTO pedidos (
            loja_id, cliente_nome, cliente_cpf, cliente_telefone,
            cliente_endereco, metodo_pagamento, total, observacoes, status
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    // Determinar status inicial baseado no método de pagamento
    $status_inicial = 'pendente';
    if ($metodo_pagamento === 'dinheiro') {
        $status_inicial = 'pendente';
    }
    
    $stmt->execute([
        1, // loja_id (fixo para demonstração)
        $cliente_nome,
        $cliente_cpf_limpo,
        $cliente_telefone,
        $cliente_endereco,
        $metodo_pagamento,
        $total,
        $observacoes,
        $status_inicial
    ]);
    
    $pedido_id = $conn->lastInsertId();
    
    // Inserir itens do pedido
    foreach ($carrinho as $item) {
        $stmt = $conn->prepare("
            INSERT INTO itens_pedido (
                pedido_id, produto_id, quantidade, preco_unitario, subtotal
            )
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $subtotal = $item['preco'] * $item['quantidade'];
        
        $stmt->execute([
            $pedido_id,
            $item['id'],
            $item['quantidade'],
            $item['preco'],
            $subtotal
        ]);
    }
    
    // Commit da transação
    $conn->commit();
    
    // Limpar sessão do carrinho
    session_start();
    unset($_SESSION['carrinho']);
    
    // Redirecionar para sucesso
    header("Location: /cliente/sucesso.php?id=$pedido_id");
    exit();
    
} catch (PDOException $e) {
    // Rollback em caso de erro
    $conn->rollBack();
    responderJSON(['erro' => 'Erro ao processar pedido: ' . $e->getMessage()], 500);
}
?>
