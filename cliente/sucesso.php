<?php
/**
 * Sucesso do Pedido - Cliente
 */

require_once '../config/database.php';
require_once '../config/functions.php';

$pedido_id = $_GET['id'] ?? null;
$loja_id = 1; // Fixo para demonstração

if (!$pedido_id) {
    header('Location: index.php');
    exit();
}

try {
    $stmt = $conn->prepare("SELECT * FROM pedidos WHERE id = ? AND loja_id = ?");
    $stmt->execute([$pedido_id, $loja_id]);
    $pedido = $stmt->fetch();
    
    if (!$pedido) {
        header('Location: index.php');
        exit();
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
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Confirmado - Comandas Digitais</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .success-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }

        .success-box {
            background: white;
            border-radius: 10px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            padding: 60px 40px;
            text-align: center;
        }

        .success-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }

        .success-box h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .success-box p {
            color: #666;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .pedido-info {
            background: #f5f5f5;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
            text-align: left;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #333;
        }

        .info-value {
            color: #666;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        .action-buttons a {
            flex: 1;
            padding: 12px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.2s;
        }

        .action-buttons a:hover {
            transform: translateY(-2px);
        }

        .btn-nova-compra {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-home {
            background: #f0f0f0;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-box">
            <div class="success-icon">✅</div>
            <h1>Pedido Confirmado!</h1>
            <p>Seu pedido foi recebido com sucesso</p>

            <div class="pedido-info">
                <div class="info-row">
                    <span class="info-label">Número do Pedido:</span>
                    <span class="info-value">#<?php echo $pedido['id']; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Cliente:</span>
                    <span class="info-value"><?php echo sanitizar($pedido['cliente_nome']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Telefone:</span>
                    <span class="info-value"><?php echo sanitizar($pedido['cliente_telefone']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Método de Pagamento:</span>
                    <span class="info-value"><?php echo ucfirst(str_replace('_', ' ', $pedido['metodo_pagamento'])); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total:</span>
                    <span class="info-value" style="font-weight: 700; color: #667eea;">
                        <?php echo formatarMoeda($pedido['total']); ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Itens:</span>
                    <span class="info-value"><?php echo count($itens); ?> produto(s)</span>
                </div>
            </div>

            <p style="color: #999; font-size: 14px;">
                Você receberá atualizações sobre seu pedido via WhatsApp
            </p>

            <div class="action-buttons">
                <a href="index.php" class="btn-nova-compra">Fazer Outro Pedido</a>
                <a href="index.php" class="btn-home">Voltar Home</a>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>
