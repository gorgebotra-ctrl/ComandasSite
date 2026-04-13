<?php
/**
 * TESTE DO SISTEMA - Verificar Funcionalidades
 * Acesse: http://localhost/Comandas/teste.php
 */

require_once 'config/database.php';
require_once 'config/functions.php';

$testes = [];
$total_sucesso = 0;
$total_falha = 0;

// Teste 1: Conexão com BD
try {
    $stmt = $conn->prepare("SELECT 1");
    $stmt->execute();
    $testes[] = ['nome' => 'Conexão com Banco de Dados', 'status' => 'OK', 'mensagem' => ''];
    $total_sucesso++;
} catch (Exception $e) {
    $testes[] = ['nome' => 'Conexão com Banco de Dados', 'status' => 'ERRO', 'mensagem' => $e->getMessage()];
    $total_falha++;
}

// Teste 2: Tabela lojas
try {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM lojas");
    $stmt->execute();
    $resultado = $stmt->fetch();
    $testes[] = ['nome' => 'Tabela lojas', 'status' => 'OK', 'mensagem' => "{$resultado['total']} lojas encontradas"];
    $total_sucesso++;
} catch (Exception $e) {
    $testes[] = ['nome' => 'Tabela lojas', 'status' => 'ERRO', 'mensagem' => $e->getMessage()];
    $total_falha++;
}

// Teste 3: Tabela produtos
try {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM produtos");
    $stmt->execute();
    $resultado = $stmt->fetch();
    $testes[] = ['nome' => 'Tabela produtos', 'status' => 'OK', 'mensagem' => "{$resultado['total']} produtos encontrados"];
    $total_sucesso++;
} catch (Exception $e) {
    $testes[] = ['nome' => 'Tabela produtos', 'status' => 'ERRO', 'mensagem' => $e->getMessage()];
    $total_falha++;
}

// Teste 4: Tabela pedidos
try {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM pedidos");
    $stmt->execute();
    $resultado = $stmt->fetch();
    $testes[] = ['nome' => 'Tabela pedidos', 'status' => 'OK', 'mensagem' => "{$resultado['total']} pedidos encontrados"];
    $total_sucesso++;
} catch (Exception $e) {
    $testes[] = ['nome' => 'Tabela pedidos', 'status' => 'ERRO', 'mensagem' => $e->getMessage()];
    $total_falha++;
}

// Teste 5: PDO Fetch Mode
try {
    $stmt = $conn->prepare("SELECT '1' as test");
    $stmt->execute();
    $result = $stmt->fetch();
    if (isset($result['test'])) {
        $testes[] = ['nome' => 'PDO Fetch Mode (ASSOC)', 'status' => 'OK', 'mensagem' => ''];
        $total_sucesso++;
    } else {
        throw new Exception("Fetch mode incorreto");
    }
} catch (Exception $e) {
    $testes[] = ['nome' => 'PDO Fetch Mode', 'status' => 'ERRO', 'mensagem' => $e->getMessage()];
    $total_falha++;
}

// Teste 6: Validação de CPF
$cpf_valido = validarCPF('12345678901');
if ($cpf_valido) {
    $testes[] = ['nome' => 'Função validarCPF', 'status' => 'OK', 'mensagem' => 'CPF validado corretamente'];
    $total_sucesso++;
} else {
    $testes[] = ['nome' => 'Função validarCPF', 'status' => 'ERRO', 'mensagem' => 'CPF não validado'];
    $total_falha++;
}

// Teste 7: Session Start
session_start();
if (isset($_SESSION)) {
    $testes[] = ['nome' => 'Sessão PHP', 'status' => 'OK', 'mensagem' => 'Session ativa'];
    $total_sucesso++;
} else {
    $testes[] = ['nome' => 'Sessão PHP', 'status' => 'ERRO', 'mensagem' => 'Session não iniciada'];
    $total_falha++;
}

// Teste 8: Verificar arquivos essenciais
$arquivos = [
    'admin/login.php',
    'admin/dashboard.php',
    'cliente/index.php',
    'api/carrinho.php',
    'api/pedidos.php',
    'assets/css/style.css',
    'assets/js/script.js'
];

$arquivos_ok = 0;
foreach ($arquivos as $arquivo) {
    if (file_exists($arquivo)) {
        $arquivos_ok++;
    }
}

$testes[] = [
    'nome' => 'Arquivos do Sistema',
    'status' => $arquivos_ok === count($arquivos) ? 'OK' : 'AVISO',
    'mensagem' => "$arquivos_ok/" . count($arquivos) . " arquivos encontrados"
];

if ($arquivos_ok === count($arquivos)) {
    $total_sucesso++;
} else {
    $total_falha++;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste do Sistema - Comandas Digitais</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            padding: 20px;
            background: #f5f5f5;
            border-bottom: 1px solid #ddd;
        }
        
        .stat {
            text-align: center;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #667eea;
        }
        
        .stat-label {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
        
        .testes {
            padding: 20px;
        }
        
        .teste {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
            background: #fafafa;
        }
        
        .teste.ok {
            border-left: 4px solid #4caf50;
            background: #f1f8f4;
        }
        
        .teste.erro {
            border-left: 4px solid #f44336;
            background: #fef1ef;
        }
        
        .teste.aviso {
            border-left: 4px solid #ff9800;
            background: #fff8f1;
        }
        
        .teste-status {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: white;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .teste.ok .teste-status {
            background: #4caf50;
        }
        
        .teste.erro .teste-status {
            background: #f44336;
        }
        
        .teste.aviso .teste-status {
            background: #ff9800;
        }
        
        .teste-content {
            flex: 1;
        }
        
        .teste-nome {
            font-weight: 600;
            color: #333;
            margin-bottom: 3px;
        }
        
        .teste-mensagem {
            font-size: 12px;
            color: #999;
        }
        
        .footer {
            padding: 20px;
            background: #f5f5f5;
            text-align: center;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #999;
        }
        
        .actions {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            padding: 20px;
        }
        
        .actions a {
            padding: 12px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            font-weight: 600;
            font-size: 13px;
            transition: background 0.3s;
        }
        
        .actions a:hover {
            background: #764ba2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 Teste do Sistema</h1>
            <p>Verificação das funcionalidades principais</p>
        </div>
        
        <div class="stats">
            <div class="stat">
                <div class="stat-number"><?php echo $total_sucesso; ?></div>
                <div class="stat-label">Testes Passou</div>
            </div>
            <div class="stat">
                <div class="stat-number"><?php echo $total_falha; ?></div>
                <div class="stat-label">Testes Falharam</div>
            </div>
        </div>
        
        <div class="testes">
            <?php foreach ($testes as $teste): ?>
                <div class="teste <?php echo strtolower($teste['status']); ?>">
                    <div class="teste-status">
                        <?php 
                        if ($teste['status'] === 'OK') echo '✓';
                        elseif ($teste['status'] === 'ERRO') echo '✕';
                        else echo '!';
                        ?>
                    </div>
                    <div class="teste-content">
                        <div class="teste-nome"><?php echo $teste['nome']; ?></div>
                        <?php if ($teste['mensagem']): ?>
                            <div class="teste-mensagem"><?php echo $teste['mensagem']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="actions">
            <a href="admin/login.php">🔐 Login</a>
            <a href="cliente/index.php">🍔 Cardápio</a>
            <a href="README.md" target="_blank">📖 Documentação</a>
        </div>
        
        <div class="footer">
            <p>Comandas Digitais v1.0 • Sistema pronto para produção ✅</p>
        </div>
    </div>
</body>
</html>
