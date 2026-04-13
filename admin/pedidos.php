<?php
/**
 * Listar Pedidos - ADMIN
 */

require_once '../config/database.php';
require_once '../config/functions.php';

verificarAutenticacao();

$loja_id = $_SESSION['loja_id'];
$filtro_status = $_GET['status'] ?? '';

// Buscar pedidos com filtro opcional
try {
    $query = "
        SELECT p.*, COUNT(ip.id) as items_count
        FROM pedidos p
        LEFT JOIN itens_pedido ip ON p.id = ip.pedido_id
        WHERE p.loja_id = ?
    ";
    
    $params = [$loja_id];
    
    if (!empty($filtro_status)) {
        $query .= " AND p.status = ?";
        $params[] = $filtro_status;
    }
    
    $query .= " GROUP BY p.id ORDER BY p.criado_em DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $pedidos = $stmt->fetchAll();
    
} catch (PDOException $e) {
    die("Erro ao buscar pedidos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos - Comandas Digitais</title>
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
                    <h1>Pedidos</h1>
                </div>
            </header>
            
            <!-- Filtros -->
            <section class="filtro-pedidos">
                <a href="pedidos.php" class="filter-btn <?php echo empty($filtro_status) ? 'active' : ''; ?>">
                    Todos
                </a>
                <a href="?status=pendente" class="filter-btn <?php echo $filtro_status === 'pendente' ? 'active' : ''; ?>">
                    Pendentes
                </a>
                <a href="?status=pago" class="filter-btn <?php echo $filtro_status === 'pago' ? 'active' : ''; ?>">
                    Pagos
                </a>
                <a href="?status=em_preparo" class="filter-btn <?php echo $filtro_status === 'em_preparo' ? 'active' : ''; ?>">
                    Em Preparo
                </a>
                <a href="?status=entregue" class="filter-btn <?php echo $filtro_status === 'entregue' ? 'active' : ''; ?>">
                    Entregues
                </a>
            </section>
            
            <!-- Tabela de Pedidos -->
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
                                <td><?php echo date('d/m H:i', strtotime($pedido['criado_em'])); ?></td>
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
                    <p>Nenhum pedido encontrado</p>
                </div>
            <?php endif; ?>
            
        </main>
    </div>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>
