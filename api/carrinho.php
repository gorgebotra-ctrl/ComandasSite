<?php
/**
 * API - Gerenciar Carrinho (GET/POST/DELETE)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');

session_start();

// Inicializar carrinho se não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Tratamento de preflight CORS
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    switch ($action) {
        case 'adicionar':
            if ($method !== 'POST') {
                throw new Exception('Método inválido');
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            $produto_id = (int)($data['id'] ?? 0);
            $nome = trim($data['nome'] ?? '');
            $preco = (float)($data['preco'] ?? 0);
            $quantidade = (int)($data['quantidade'] ?? 1);
            
            if (!$produto_id || !$nome || $preco <= 0 || $quantidade <= 0) {
                throw new Exception('Dados inválidos');
            }
            
            // Verificar se já existe no carrinho
            if (isset($_SESSION['carrinho'][$produto_id])) {
                $_SESSION['carrinho'][$produto_id]['quantidade'] += $quantidade;
            } else {
                $_SESSION['carrinho'][$produto_id] = [
                    'id' => $produto_id,
                    'nome' => $nome,
                    'preco' => $preco,
                    'quantidade' => $quantidade
                ];
            }
            
            echo json_encode([
                'status' => 'sucesso',
                'mensagem' => 'Produto adicionado ao carrinho',
                'carrinho' => $_SESSION['carrinho'],
                'total_itens' => array_sum(array_column($_SESSION['carrinho'], 'quantidade'))
            ]);
            break;

        case 'remover':
            if ($method !== 'POST') {
                throw new Exception('Método inválido');
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            $produto_id = (int)($data['id'] ?? 0);
            
            if (!$produto_id) {
                throw new Exception('ID do produto inválido');
            }
            
            if (isset($_SESSION['carrinho'][$produto_id])) {
                unset($_SESSION['carrinho'][$produto_id]);
            }
            
            echo json_encode([
                'status' => 'sucesso',
                'mensagem' => 'Produto removido do carrinho',
                'carrinho' => $_SESSION['carrinho'],
                'total_itens' => array_sum(array_column($_SESSION['carrinho'], 'quantidade'))
            ]);
            break;

        case 'atualizar':
            if ($method !== 'POST') {
                throw new Exception('Método inválido');
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            $produto_id = (int)($data['id'] ?? 0);
            $quantidade = (int)($data['quantidade'] ?? 0);
            
            if (!$produto_id) {
                throw new Exception('ID do produto inválido');
            }
            
            if ($quantidade <= 0) {
                if (isset($_SESSION['carrinho'][$produto_id])) {
                    unset($_SESSION['carrinho'][$produto_id]);
                }
            } elseif (isset($_SESSION['carrinho'][$produto_id])) {
                $_SESSION['carrinho'][$produto_id]['quantidade'] = $quantidade;
            }
            
            echo json_encode([
                'status' => 'sucesso',
                'mensagem' => 'Carrinho atualizado',
                'carrinho' => $_SESSION['carrinho'],
                'total_itens' => array_sum(array_column($_SESSION['carrinho'], 'quantidade'))
            ]);
            break;

        case 'obter':
            $total = 0;
            foreach ($_SESSION['carrinho'] as $item) {
                $total += $item['preco'] * $item['quantidade'];
            }
            
            echo json_encode([
                'carrinho' => $_SESSION['carrinho'],
                'total_itens' => array_sum(array_column($_SESSION['carrinho'], 'quantidade')),
                'total' => $total
            ]);
            break;

        case 'limpar':
            $_SESSION['carrinho'] = [];
            echo json_encode([
                'status' => 'sucesso',
                'mensagem' => 'Carrinho limpo',
                'carrinho' => []
            ]);
            break;

        default:
            throw new Exception('Ação inválida');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'erro',
        'mensagem' => $e->getMessage()
    ]);
}
?>
