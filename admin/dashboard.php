<?php
/**
 * Dashboard da Loja - ADMIN
 */

require_once '../config/database.php';
require_once '../config/functions.php';

verificarAutenticacao();

$loja_id = $_SESSION['loja_id'];

try {
    // Buscar estatísticas
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM pedidos WHERE loja_id = ?");
    $stmt->execute([$loja_id]);
    $pedidos_total = $stmt->fetch()['total'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM pedidos WHERE loja_id = ? AND status = 'pendente'");
    $stmt->execute([$loja_id]);
    $pedidos_pendentes = $stmt->fetch()['total'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM produtos WHERE loja_id = ?");
    $stmt->execute([$loja_id]);
    $produtos_total = $stmt->fetch()['total'];
    
    $stmt = $conn->prepare("SELECT SUM(total) as valor FROM pedidos WHERE loja_id = ? AND status = 'pago'");
    $stmt->execute([$loja_id]);
    $faturamento = $stmt->fetch()['valor'] ?? 0;
    
    // Buscar últimos pedidos
    $stmt = $conn->prepare("
        SELECT p.*, COUNT(ip.id) as items_count
        FROM pedidos p
        LEFT JOIN itens_pedido ip ON p.id = ip.pedido_id
        WHERE p.loja_id = ?
        GROUP BY p.id
        ORDER BY p.criado_em DESC
        LIMIT 10
    ");
    $stmt->execute([$loja_id]);
    $pedidos = $stmt->fetchAll();
    
} catch (PDOException $e) {
    die("Erro ao buscar dados: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Comandas Digitais</title>
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
                <a href="dashboard.php" class="nav-item active">
                    <span class="icon">📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="produtos.php" class="nav-item">
                    <span class="icon">📦</span>
                    <span>Produtos</span>
                </a>
                <a href="pedidos.php" class="nav-item">
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
                    <h1>Dashboard</h1>
                    <div class="user-info">
                        <span><?php echo sanitizar($_SESSION['loja_nome']); ?></span>
                        <a href="logout.php" class="btn-logout">Logout</a>
                    </div>
                </div>
            </header>
            
            <!-- Statistics Cards -->
            <section class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #667eea;">📦</div>
                    <div class="stat-content">
                        <h3>Produtos</h3>
                        <p class="stat-number"><?php echo $produtos_total; ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #f093fb;">🛒</div>
                    <div class="stat-content">
                        <h3>Pedidos Total</h3>
                        <p class="stat-number"><?php echo $pedidos_total; ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #fa709a;">⏳</div>
                    <div class="stat-content">
                        <h3>Pendentes</h3>
                        <p class="stat-number"><?php echo $pedidos_pendentes; ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #30b0c0;">💰</div>
                    <div class="stat-content">
                        <h3>Faturamento</h3>
                        <p class="stat-number"><?php echo formatarMoeda($faturamento); ?></p>
                    </div>
                </div>
            </section>
            
            <!-- Recent Orders -->
            <section class="recent-orders">
                <div class="section-header">
                    <h2>Pedidos Recentes</h2>
                    <a href="pedidos.php" class="btn-small">Ver Todos</a>
                </div>
                
                <div class="table-responsive">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Itens</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Pagamento</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos as $pedido): ?>
                                <tr>
                                    <td>#<?php echo $pedido['id']; ?></td>
                                    <td><?php echo sanitizar($pedido['cliente_nome']); ?></td>
                                    <td><?php echo $pedido['items_count']; ?></td>
                                    <td><?php echo formatarMoeda($pedido['total']); ?></td>
                                    <td>
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
                                    </td>
                                    <td><?php echo ucfirst(str_replace('_', ' ', $pedido['metodo_pagamento'])); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($pedido['criado_em'])); ?></td>
                                    <td>
                                        <a href="ver_pedido.php?id=<?php echo $pedido['id']; ?>" class="btn-icon">👁</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if (empty($pedidos)): ?>
                    <div class="empty-state">
                        <p>Nenhum pedido ainda</p>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>
