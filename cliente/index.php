<?php
/**
 * Página Principal - Cliente (Cardápio)
 */

require_once '../config/database.php';
require_once '../config/functions.php';

// Buscar todas as lojas
try {
    $stmt = $conn->prepare("SELECT * FROM lojas WHERE id = 1");
    $stmt->execute();
    $loja = $stmt->fetch();
    
    if (!$loja) {
        die('Loja não encontrada');
    }
    
    // Buscar produtos da loja, agrupados por categoria
    $stmt = $conn->prepare("
        SELECT * FROM produtos
        WHERE loja_id = ? AND ativo = 1
        ORDER BY categoria, nome
    ");
    $stmt->execute([$loja['id']]);
    $produtos = $stmt->fetchAll();
    
    // Agrupar por categoria
    $produtos_por_categoria = [];
    foreach ($produtos as $produto) {
        $categoria = $produto['categoria'] ?? 'Outros';
        if (!isset($produtos_por_categoria[$categoria])) {
            $produtos_por_categoria[$categoria] = [];
        }
        $produtos_por_categoria[$categoria][] = $produto;
    }
    
} catch (PDOException $e) {
    die("Erro ao buscar produtos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitizar($loja['nome']); ?> - Comandas Digitais</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="customer-header">
        <div class="header-container">
            <div class="header-logo">
                <h1><?php echo sanitizar($loja['nome']); ?></h1>
                <p>Seu pedido online</p>
            </div>
            <div class="cart-header">
                <button class="btn-cart" onclick="abrirCarrinho()">
                    🛒 Carrinho (<span id="cart-count">0</span>)
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="customer-main">
        <!-- Menu de Categorias -->
        <nav class="category-menu">
            <button class="category-btn active" onclick="mostrarCategoria('TODAS')">Todas</button>
            <?php foreach (array_keys($produtos_por_categoria) as $categoria): ?>
                <button class="category-btn" onclick="mostrarCategoria('<?php echo htmlspecialchars($categoria); ?>')">
                    <?php echo htmlspecialchars($categoria); ?>
                </button>
            <?php endforeach; ?>
        </nav>

        <!-- Produtos -->
        <section class="produtos-container">
            <?php foreach ($produtos_por_categoria as $categoria => $prods): ?>
                <div class="categoria-section" data-category="<?php echo htmlspecialchars($categoria); ?>">
                    <h2 class="categoria-titulo"><?php echo htmlspecialchars($categoria); ?></h2>
                    <div class="produtos-grid">
                        <?php foreach ($prods as $produto): ?>
                            <div class="produto-card-cliente">
                                <div class="produto-imagem">
                                    <?php if ($produto['imagem']): ?>
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($produto['imagem']); ?>" alt="<?php echo sanitizar($produto['nome']); ?>">
                                    <?php else: ?>
                                        <div class="placeholder-image">📷</div>
                                    <?php endif; ?>
                                </div>
                                <div class="produto-info-cliente">
                                    <h3><?php echo sanitizar($produto['nome']); ?></h3>
                                    <p><?php echo sanitizar($produto['descricao']); ?></p>
                                    <div class="produto-footer">
                                        <span class="preco"><?php echo formatarMoeda($produto['preco']); ?></span>
                                        <button class="btn-adicionar" onclick="adicionarAoCarrinho(<?php echo $produto['id']; ?>, '<?php echo sanitizar($produto['nome']); ?>', <?php echo $produto['preco']; ?>)">
                                            +
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
    </main>

    <!-- Carrinho Modal -->
    <div id="carrinho-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Carrinho de Compras</h2>
                <button class="close-btn" onclick="fecharCarrinho()">✕</button>
            </div>
            <div id="carrinho-items" class="carrinho-items">
                <p class="carrinho-vazio">Seu carrinho está vazio</p>
            </div>
            <div class="carrinho-footer">
                <div class="total-carrinho">
                    <strong>Total:</strong>
                    <span id="total-carrinho">R$ 0,00</span>
                </div>
                <button class="btn-primary" onclick="irParaCheckout()" id="btn-checkout" disabled>
                    Finalizar Pedido
                </button>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
    <script>
        // Dados da loja para uso no JavaScript
        const lojaId = <?php echo $loja['id']; ?>;
    </script>
</body>
</html>
