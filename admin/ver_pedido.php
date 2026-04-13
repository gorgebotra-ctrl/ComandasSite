<?php
/**
 * Ver Detalhes do Pedido - ADMIN
 */

require_once '../config/database.php';
require_once '../config/functions.php';

verificarAutenticacao();

$loja_id = $_SESSION['loja_id'];
$pedido_id = (int)($_GET['id'] ?? 0);
$mensagem = '';
$tipo_mensagem = '';

try {
    // Buscar pedido
    $stmt = $conn->prepare("SELECT * FROM pedidos WHERE id = ? AND loja_id = ?");
    $stmt->execute([$pedido_id, $loja_id]);
    $pedido = $stmt->fetch();
    
    if (!$pedido) {
        die('Pedido não encontrado');
    }
    
    // Buscar itens do pedido
    $stmt = $conn->prepare("
        SELECT ip.*, p.nome as produto_nome
        FROM itens_pedido ip
        JOIN produtos p ON ip.produto_id = p.id
        WHERE ip.pedido_id = ?
    ");
    $stmt->execute([$pedido_id]);
    $itens = $stmt->fetchAll();
    
} catch (PDOException $e) {
    die("Erro ao buscar pedido: " . $e->getMessage());
}

// Processar mudança de status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['novo_status'])) {
    $novo_status = sanitizar($_POST['novo_status']);
    
    try {
        $stmt = $conn->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
        $stmt->execute([$novo_status, $pedido_id]);
        
        $mensagem = 'Status atualizado com sucesso!';
        $tipo_mensagem = 'sucesso';
        
        // Atualizar dados do pedido
        $stmt = $conn->prepare("SELECT * FROM pedidos WHERE id = ? AND loja_id = ?");
        $stmt->execute([$pedido_id, $loja_id]);
        $pedido = $stmt->fetch();
        
    } catch (PDOException $e) {
        $mensagem = 'Erro ao atualizar status';
        $tipo_mensagem = 'erro';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido #<?php echo $pedido['id']; ?> - Comandas Digitais</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container-admin">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <h2>🍔 Comandas</h2>
            </div>
            <nav class="nav-menu">
                <a href="dashboard.php" class="nav-item">
                    <span class="icon">📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="produtos.php" class="nav-item">
                    <span class="icon">📦</span>
                    <span>Produtos</span>
                </a>
                <a href="pedidos.php" class="nav-item active">
                    <span class="icon">🛒</span>
                    <span>Pedidos</span>
                </a>
                <a href="logout.php" class="nav-item logout">
                    <span class="icon">🚪</span>
                    <span>Sair</span>
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="top-header">
                <div class="header-content">
                    <h1>Pedido #<?php echo $pedido['id']; ?></h1>
                    <a href="pedidos.php" class="btn-secondary">← Voltar para Pedidos</a>
                </div>
            </header>
            
            <?php if ($mensagem): ?>
                <div class="alert alert-<?php echo $tipo_mensagem; ?>">
                    <?php echo $mensagem; ?>
                </div>
            <?php endif; ?>
            
            <!-- Informações do Pedido -->
            <div class="pedido-detalhes">
                <div class="info-section">
                    <h3>Informações do Cliente</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Nome</label>
                            <p><?php echo sanitizar($pedido['cliente_nome']); ?></p>
                        </div>
                        <div class="info-item">
                            <label>CPF</label>
                            <p><?php echo $pedido['cliente_cpf'] ? formatarCPF($pedido['cliente_cpf']) : 'Não informado'; ?></p>
                        </div>
                        <div class="info-item">
                            <label>Telefone</label>
                            <p><?php echo sanitizar($pedido['cliente_telefone']); ?></p>
                        </div>
                        <div class="info-item">
                            <label>Endereço</label>
                            <p><?php echo sanitizar($pedido['cliente_endereco']); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="info-section">
                    <h3>Itens do Pedido</h3>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Valor Unitário</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($itens as $item): ?>
                                <tr>
                                    <td><?php echo sanitizar($item['produto_nome']); ?></td>
                                    <td><?php echo $item['quantidade']; ?></td>
                                    <td><?php echo formatarMoeda($item['preco_unitario']); ?></td>
                                    <td><?php echo formatarMoeda($item['subtotal']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="info-section">
                    <h3>Resumo do Pedido</h3>
                    <div class="resumo-pedido">
                        <div class="resumo-linha">
                            <strong>Subtotal:</strong>
                            <span><?php echo formatarMoeda($pedido['total']); ?></span>
                        </div>
                        <div class="resumo-linha">
                            <strong>Método de Pagamento:</strong>
                            <span><?php echo ucfirst(str_replace('_', ' ', $pedido['metodo_pagamento'])); ?></span>
                        </div>
                        <div class="resumo-linha">
                            <strong>Status Atual:</strong>
                            <span class="badge badge-<?php echo $pedido['status']; ?>">
                                <?php 
                                $status_map = [
                                    'pendente' => 'Pendente',
                                    'pago' => 'Pago',
                                    'em_preparo' => 'Em Preparo',
                                    'entregue' => 'Entregue',
                                    'cancelado' => 'Cancelado'
                                ];
                                echo $status_map[$pedido['status']] ?? $pedido['status'];
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="info-section">
                    <h3>Atualizar Status</h3>
                    <form method="POST" class="form-status">
                        <div class="form-group">
                            <label for="novo_status">Novo Status</label>
                            <select id="novo_status" name="novo_status" required>
                                <option value="pendente" <?php echo $pedido['status'] === 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                                <option value="pago" <?php echo $pedido['status'] === 'pago' ? 'selected' : ''; ?>>Pago</option>
                                <option value="em_preparo" <?php echo $pedido['status'] === 'em_preparo' ? 'selected' : ''; ?>>Em Preparo</option>
                                <option value="entregue" <?php echo $pedido['status'] === 'entregue' ? 'selected' : ''; ?>>Entregue</option>
                                <option value="cancelado" <?php echo $pedido['status'] === 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary">Atualizar Status</button>
                    </form>
                </div>
                
                <?php if ($pedido['observacoes']): ?>
                    <div class="info-section">
                        <h3>Observações</h3>
                        <p><?php echo sanitizar($pedido['observacoes']); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>
