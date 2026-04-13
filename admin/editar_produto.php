<?php
/**
 * Editar Produto - ADMIN
 */

require_once '../config/database.php';
require_once '../config/functions.php';

verificarAutenticacao();

$loja_id = $_SESSION['loja_id'];
$produto_id = (int)($_GET['id'] ?? 0);
$erro = '';
$sucesso = '';

if (!verificarPermissaoProduto($conn, $produto_id)) {
    die('Acesso negado');
}

// Buscar produto
try {
    $stmt = $conn->prepare("SELECT * FROM produtos WHERE id = ? AND loja_id = ?");
    $stmt->execute([$produto_id, $loja_id]);
    $produto = $stmt->fetch();
    
    if (!$produto) {
        die('Produto não encontrado');
    }
} catch (PDOException $e) {
    die("Erro ao buscar produto: " . $e->getMessage());
}

// Processar edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitizar($_POST['nome'] ?? '');
    $descricao = sanitizar($_POST['descricao'] ?? '');
    $preco = floatval($_POST['preco'] ?? 0);
    $categoria = sanitizar($_POST['categoria'] ?? '');
    
    if (empty($nome) || empty($descricao) || $preco <= 0) {
        $erro = 'Preencha todos os campos obrigatórios';
    } else {
        try {
            // Processar imagem se enviada
            $imagem = $produto['imagem'];
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['imagem']['tmp_name'];
                $imagem = file_get_contents($tmp_name);
            }
            
            // Atualizar produto
            $stmt = $conn->prepare("
                UPDATE produtos
                SET nome = ?, descricao = ?, preco = ?, categoria = ?, imagem = ?
                WHERE id = ?
            ");
            $stmt->execute([$nome, $descricao, $preco, $categoria, $imagem, $produto_id]);
            
            $sucesso = 'Produto atualizado com sucesso!';
            
            // Atualizar dados do produto
            $stmt = $conn->prepare("SELECT * FROM produtos WHERE id = ? AND loja_id = ?");
            $stmt->execute([$produto_id, $loja_id]);
            $produto = $stmt->fetch();
            
        } catch (PDOException $e) {
            $erro = 'Erro ao atualizar produto: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto - Comandas Digitais</title>
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
                    <h1>Editar Produto</h1>
                </div>
            </header>
            
            <?php if ($erro): ?>
                <div class="alert alert-erro">
                    <?php echo $erro; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($sucesso): ?>
                <div class="alert alert-sucesso">
                    <?php echo $sucesso; ?>
                </div>
            <?php endif; ?>
            
            <!-- Formulário -->
            <div class="form-container">
                <form method="POST" enctype="multipart/form-data" class="form-produto">
                    <div class="form-group">
                        <label for="nome">Nome do Produto *</label>
                        <input type="text" id="nome" name="nome" value="<?php echo sanitizar($produto['nome']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="descricao">Descrição *</label>
                        <textarea id="descricao" name="descricao" rows="4" required><?php echo sanitizar($produto['descricao']); ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="preco">Preço (R$) *</label>
                            <input type="number" id="preco" name="preco" value="<?php echo $produto['preco']; ?>" step="0.01" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="categoria">Categoria</label>
                            <select id="categoria" name="categoria">
                                <option value="">Selecione uma categoria</option>
                                <option value="Lanches" <?php echo $produto['categoria'] === 'Lanches' ? 'selected' : ''; ?>>Lanches</option>
                                <option value="Pizzas" <?php echo $produto['categoria'] === 'Pizzas' ? 'selected' : ''; ?>>Pizzas</option>
                                <option value="Bebidas" <?php echo $produto['categoria'] === 'Bebidas' ? 'selected' : ''; ?>>Bebidas</option>
                                <option value="Acompanhamentos" <?php echo $produto['categoria'] === 'Acompanhamentos' ? 'selected' : ''; ?>>Acompanhamentos</option>
                                <option value="Sobremesas" <?php echo $produto['categoria'] === 'Sobremesas' ? 'selected' : ''; ?>>Sobremesas</option>
                                <option value="Outros" <?php echo $produto['categoria'] === 'Outros' ? 'selected' : ''; ?>>Outros</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="imagem">Imagem do Produto</label>
                        <?php if ($produto['imagem']): ?>
                            <div class="current-image">
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($produto['imagem']); ?>" alt="Imagem atual">
                                <small>Imagem atual</small>
                            </div>
                        <?php endif; ?>
                        <input type="file" id="imagem" name="imagem" accept="image/*">
                        <small>Deixe em branco para manter a imagem atual</small>
                    </div>
                    
                    <div class="form-actions">
                        <a href="produtos.php" class="btn-secondary">Voltar</a>
                        <button type="submit" class="btn-primary">Atualizar Produto</button>
                    </div>
                </form>
            </div>
            
        </main>
    </div>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>
