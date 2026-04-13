<?php
/**
 * Gerenciar Produtos - ADMIN
 */

require_once '../config/database.php';
require_once '../config/functions.php';

verificarAutenticacao();

$loja_id = $_SESSION['loja_id'];
$mensagem = '';
$tipo_mensagem = '';

// Deletar produto
if (isset($_GET['deletar'])) {
    $produto_id = (int)$_GET['deletar'];
    
    if (verificarPermissaoProduto($conn, $produto_id)) {
        try {
            $stmt = $conn->prepare("DELETE FROM produtos WHERE id = ?");
            $stmt->execute([$produto_id]);
            $mensagem = 'Produto deletado com sucesso!';
            $tipo_mensagem = 'sucesso';
        } catch (PDOException $e) {
            $mensagem = 'Erro ao deletar produto';
            $tipo_mensagem = 'erro';
        }
    }
}

try {
    // Buscar todos os produtos da loja
    $stmt = $conn->prepare("
        SELECT * FROM produtos
        WHERE loja_id = ?
        ORDER BY criado_em DESC
    ");
    $stmt->execute([$loja_id]);
    $produtos = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao buscar produtos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - Comandas Digitais</title>
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
                <a href="produtos.php" class="nav-item active">
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
                    <h1>Produtos</h1>
                    <div class="header-actions">
                        <a href="adicionar_produto.php" class="btn-primary">+ Adicionar Produto</a>
                    </div>
                </div>
            </header>
            
            <?php if ($mensagem): ?>
                <div class="alert alert-<?php echo $tipo_mensagem; ?>">
                    <?php echo $mensagem; ?>
                </div>
            <?php endif; ?>
            
            <!-- Produtos Grid -->
            <section class="produtos-grid">
                <?php if (empty($produtos)): ?>
                    <div class="empty-state" style="grid-column: 1/-1;">
                        <p>Nenhum produto cadastrado ainda</p>
                        <a href="adicionar_produto.php" class="btn-primary">Adicionar Produto</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($produtos as $produto): ?>
                        <div class="produto-card">
                            <div class="produto-imagem">
                                <?php if ($produto['imagem']): ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($produto['imagem']); ?>" alt="<?php echo sanitizar($produto['nome']); ?>">
                                <?php else: ?>
                                    <div class="placeholder-image">📷</div>
                                <?php endif; ?>
                            </div>
                            <div class="produto-info">
                                <h3><?php echo sanitizar($produto['nome']); ?></h3>
                                <p class="produto-descricao"><?php echo sanitizar(substr($produto['descricao'], 0, 50)); ?>...</p>
                                <p class="produto-preco"><?php echo formatarMoeda($produto['preco']); ?></p>
                                <div class="produto-actions">
                                    <a href="editar_produto.php?id=<?php echo $produto['id']; ?>" class="btn-secondary">Editar</a>
                                    <a href="?deletar=<?php echo $produto['id']; ?>" class="btn-danger" onclick="return confirm('Tem certeza?')">Deletar</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        </main>
    </div>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>
