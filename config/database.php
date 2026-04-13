<?php
/**
 * Configuração de Conexão com Banco de Dados
 * Compatível com InfinityFree
 */

// Configurações locais (XAMPP/Desenvolvimento)
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'comandas_db';

// Para InfinityFree, use:
// $host = 'seu-host-infinityfree.com';
// $username = 'seu-usuario-db';
// $password = 'sua-senha-db';
// $database = 'seu-database-name';

try {
    // Usar PDO para maior segurança
    $conn = new PDO(
        "mysql:host=$host;dbname=$database;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die("Erro de conexão com banco de dados: " . $e->getMessage());
}

// Função auxiliar para criar conexão
function getConnection() {
    global $conn;
    return $conn;
}
?>
