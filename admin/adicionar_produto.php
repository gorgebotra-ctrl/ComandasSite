<?php
/**
 * Adicionar Produto - ADMIN
 */

require_once '../config/database.php';
require_once '../config/functions.php';

verificarAutenticacao();

$loja_id = $_SESSION['loja_id'];
$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitizar($_POST['nome'] ?? '');
    $descricao = sanitizar($_POST['descricao'] ?? '');
    $preco = floatval($_POST['preco'] ?? 0);
    $categoria = sanitizar($_POST['categoria'] ?? '');
    
    // Validar campos obrigatórios
    if (empty($nome) || empty($descricao) || $preco <= 0) {
        $erro = 'Preenchaa todos os campos obrigatórios';
    } else {
        try {
            // Processar imagem
            $imagem = null;
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['imagem']['tmp_name'];
                $imagem = file_get_contents($tmp_name);
            }
            
            // Inserir produto
            $stmt = $conn->prepare("
                INSERT INTO produtos (loja_id, nome, descricao, preco, categoria, imagem, ativo)
                VALUES (?, ?, ?, ?, ?, ?, 1)
            ");
            $stmt->execute([$loja_id, $nome, $descricao, $preco, $categoria, $imagem]);
            
            $sucesso = 'Produto adicionado com sucesso!';
            // Redirecionar após 2 segundos
            header('Refresh: 2; url=produtos.php');
            
        } catch (PDOException $e) {
            $erro = 'Erro ao adicionar produto: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Produto - Comandas Digitais</title>
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
                    <h1>Adicionar Produto</h1>
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
                        <input type="text" id="nome" name="nome" placeholder="Ex: Hambúrguer Clássico" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="descricao">Descrição *</label>
                        <textarea id="descricao" name="descricao" rows="4" placeholder="Descreva o produto" required></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="preco">Preço (R$) *</label>
                            <input type="number" id="preco" name="preco" placeholder="0.00" step="0.01" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="categoria">Categoria</label>
                            <select id="categoria" name="categoria">
                                <option value="">Selecione uma categoria</option>
                                <option value="Lanches">Lanches</option>
                                <option value="Pizzas">Pizzas</option>
                                <option value="Bebidas">Bebidas</option>
                                <option value="Acompanhamentos">Acompanhamentos</option>
                                <option value="Sobremesas">Sobremesas</option>
                                <option value="Outros">Outros</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="imagem">Imagem do Produto</label>
                        <input type="file" id="imagem" name="imagem" accept="image/*">
                        <small>Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB</small>
                    </div>
                    
                    <div class="form-actions">
                        <a href="produtos.php" class="btn-secondary">Cancelar</a>
                        <button type="submit" class="btn-primary">Adicionar Produto</button>
                    </div>
                </form>
            </div>
            
        </main>
    </div>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>
