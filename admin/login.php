<?php
/**
 * Login da Loja - ADMIN
 */

session_start();

// Se já está autenticado, redireciona para dashboard
if (isset($_SESSION['loja_id'])) {
    header('Location: dashboard.php');
    exit();
}

require_once '../config/database.php';
require_once '../config/functions.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cpf = sanitizar($_POST['cpf'] ?? '');
    $senha = $_POST['senha'] ?? '';
    
    // Validar campos vazios
    if (empty($cpf) || empty($senha)) {
        $erro = 'CPF e senha são obrigatórios';
    } elseif (!validarCPF($cpf)) {
        $erro = 'CPF inválido';
    } else {
        // Remover caracteres especiais do CPF
        $cpf_limpo = preg_replace('/[^0-9]/', '', $cpf);
        
        try {
            // Buscar loja no banco de dados
            $stmt = $conn->prepare("SELECT id, nome, cpf, senha FROM lojas WHERE cpf = ?");
            $stmt->execute([$cpf_limpo]);
            
            if ($stmt->rowCount() > 0) {
                $loja = $stmt->fetch();
                
                // Verificar senha usando hash SHA2
                $senha_hash = hash('sha256', $senha);
                
                if ($loja['senha'] === $senha_hash) {
                    // Login bem-sucedido
                    $_SESSION['loja_id'] = $loja['id'];
                    $_SESSION['loja_nome'] = $loja['nome'];
                    
                    header('Location: dashboard.php');
                    exit();
                } else {
                    $erro = 'CPF ou senha incorretos';
                }
            } else {
                $erro = 'CPF ou senha incorretos';
            }
        } catch (PDOException $e) {
            $erro = 'Erro ao conectar com o banco de dados';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Comandas Digitais</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        
        .login-box {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            padding: 40px;
        }
        
        .login-box h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        .login-box p {
            text-align: center;
            color: #999;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .alert {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
        }
        
        .info-box {
            background: #f0f4ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 13px;
            color: #333;
        }
        
        .info-box strong {
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>🍔 Comandas Digitais</h1>
            <p>Painel da Loja</p>
            
            <?php if ($erro): ?>
                <div class="alert alert-error">
                    <?php echo $erro; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="cpf">CPF da Loja</label>
                    <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" maxlength="14" required>
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
                </div>
                
                <button type="submit" class="btn-login">Entrar</button>
            </form>
            
            <div class="info-box">
                <strong>📝 Dados de Teste:</strong><br>
                CPF: 12345678901<br>
                Senha: senha123
            </div>
        </div>
    </div>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>
