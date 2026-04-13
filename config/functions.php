<?php
/**
 * Functions & Utilities - Funções auxiliares do sistema
 */

session_start();

/**
 * Verificar se a loja está autenticada
 */
function verificarAutenticacao() {
    if (!isset($_SESSION['loja_id']) || !isset($_SESSION['loja_nome'])) {
        header('Location: /admin/login.php');
        exit();
    }
}

/**
 * Fazer logout
 */
function fazerLogout() {
    session_destroy();
    header('Location: /admin/login.php');
    exit();
}

/**
 * Sanitizar entrada
 */
function sanitizar($dado) {
    return htmlspecialchars(trim($dado), ENT_QUOTES, 'UTF-8');
}

/**
 * Validar CPF (formato básico)
 */
function validarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/is', '', $cpf);
    
    if (strlen($cpf) != 11) {
        return false;
    }
    
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
    
    return true;
}

/**
 * Formatar CPF para exibição
 */
function formatarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9);
}

/**
 * Formatar moeda em Real
 */
function formatarMoeda($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

/**
 * Gerar QR Code simulado (retorna URL para QR Code via API externa)
 */
function gerarQRCodePIX($text) {
    // Usando API externa gratuita para gerar QR Code
    $encoded = urlencode($text);
    return "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=$encoded";
}

/**
 * Enviar resposta JSON
 */
function responderJSON($dados, $statusCode = 200) {
    header('Content-Type: application/json');
    header("HTTP/1.1 $statusCode");
    echo json_encode($dados);
    exit();
}

/**
 * Verificar permissão de loja (se produto pertence à loja logada)
 */
function verificarPermissaoProduto($conn, $produto_id) {
    verificarAutenticacao();
    
    $stmt = $conn->prepare("SELECT id FROM produtos WHERE id = ? AND loja_id = ?");
    $stmt->execute([$produto_id, $_SESSION['loja_id']]);
    
    return $stmt->rowCount() > 0;
}

/**
 * Obter o ID da loja da sessão
 */
function getLojaId() {
    return $_SESSION['loja_id'] ?? null;
}
?>
