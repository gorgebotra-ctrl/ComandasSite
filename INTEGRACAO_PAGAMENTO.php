<?php
/**
 * Exemplo de Integração com Pagamentos (Mercado Pago)
 * 
 * IMPORTANTE: Este é um exemplo educacional.
 * Para usar em produção, configure corretamente as credenciais.
 */

namespace PagamentoIntegration;

class MercadoPagoIntegration {
    private $publicKey;
    private $accessToken;
    private $endpoint = 'https://api.mercadopago.com/v1';
    
    public function __construct($publicKey, $accessToken) {
        $this->publicKey = $publicKey;
        $this->accessToken = $accessToken;
    }
    
    /**
     * Criar preferência de pagamento (para redirect)
     */
    public function criarPreferencia($pedido_id, $total, $cliente_email, $itens) {
        $preference = [
            "external_reference" => "PEDIDO-$pedido_id",
            "payer" => [
                "email" => $cliente_email
            ],
            "items" => $itens,
            "back_urls" => [
                "success" => "https://seu-dominio.com/cliente/sucesso.php",
                "pending" => "https://seu-dominio.com/cliente/pendente.php",
                "failure" => "https://seu-dominio.com/cliente/erro.php"
            ],
            "auto_return" => "approved",
            "notification_url" => "https://seu-dominio.com/webhooks/mercado-pago.php"
        ];
        
        return $this->fazer_requisicao("/checkout/preferences", "POST", $preference);
    }
    
    /**
     * Fazer requisição à API
     */
    private function fazer_requisicao($endpoint, $metodo = "GET", $dados = null) {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $this->endpoint . $endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $this->accessToken,
            "Content-Type: application/json"
        ]);
        
        if ($dados) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        } else {
            throw new \Exception("Erro na API Mercado Pago: $response");
        }
    }
}

// ===== Exemplo de Uso =====

// $mp = new MercadoPagoIntegration(
//     'YOUR_PUBLIC_KEY',
//     'YOUR_ACCESS_TOKEN'
// );

// $itens = [
//     [
//         "title" => "Hambúrguer Clássico",
//         "quantity" => 2,
//         "unit_price" => 35.90
//     ],
//     [
//         "title" => "Refrigerante 2L",
//         "quantity" => 1,
//         "unit_price" => 8.00
//     ]
// ];

// try {
//     $preferencia = $mp->criarPreferencia(1, 79.80, "cliente@email.com", $itens);
//     $checkout_url = $preferencia['init_point'];
//     // Redirecionar para $checkout_url
// } catch (Exception $e) {
//     echo "Erro: " . $e->getMessage();
// }
?>

<!-- ===== EXEMPLO DE BOTÃO MERCADO PAGO ===== -->
<!--
<div class="payment-option">
    <form action="<?php echo $checkout_url; ?>" method="POST">
        <button type="submit" class="btn-primary">
            Pagar com Mercado Pago
        </button>
    </form>
</div>
-->

<?php
/**
 * Exemplo: Receber Webhook do Mercado Pago
 * Arquivo: webhooks/mercado-pago.php
 */

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $data = json_decode(file_get_contents('php://input'), true);
//     
//     if ($data['type'] === 'payment') {
//         $payment_id = $data['data']['id'];
//         
//         // Obter detalhes do pagamento
//         $response = $mp->fazer_requisicao("/payments/$payment_id", "GET");
//         
//         if ($response['status'] === 'approved') {
//             // Atualizar pedido como PAGO
//             $stmt = $conn->prepare("UPDATE pedidos SET status = 'pago' WHERE id = ?");
//             $stmt->execute([$pedido_id]);
//         }
//     }
// }
?>
