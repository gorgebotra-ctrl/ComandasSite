# 🔧 DOCUMENTAÇÃO TÉCNICA - COMANDAS DIGITAIS

## Arquitetura

O sistema segue uma arquitetura simples mas eficaz:

```
┌─────────────────────────────────────────────────┐
│              CAMADA DE APRESENTAÇÃO             │
│   HTML/CSS/JS (Cliente) + HTML/CSS (Admin)      │
└────────────────┬────────────────────────────────┘
                 │
┌────────────────▼────────────────────────────────┐
│          CAMADA DE LÓGICA DE NEGÓCIO            │
│   PHP (Controllers/Actions)                     │
│   - Login                                       │
│   - CRUD de Produtos                            │
│   - Gerenciamento de Pedidos                    │
└────────────────┬────────────────────────────────┘
                 │
┌────────────────▼────────────────────────────────┐
│           CAMADA DE PERSISTÊNCIA                │
│   PDO + MySQL                                   │
│   - Prepared Statements (Segurança)             │
│   - Transações (Integridade)                    │
└─────────────────────────────────────────────────┘
```

## Stack Tecnológico

### Backend
- **PHP 7.2+** - Linguagem de servidor
- **PDO** - Database abstraction
- **MySQL 5.7+** - Database relacional
- **SHA2** - Password hashing

### Frontend
- **HTML5** - Estrutura
- **CSS3** - Estilos (Grid, Flexbox, Gradientes)
- **JavaScript Vanilla** - Dinâmico (sem frameworks)
- **LocalStorage** - Persistência do carrinho

### Deploy
- Apache (.htaccess)
- InfinityFree / Hostinger / Similar

---

## Fluxo de Datos

### Fluxo de Autenticação (Admin)

```
┌──────────┐
│  Login   │
│ Page     │
└────┬─────┘
     │ CPF + Senha (POST)
     ▼
┌──────────────────┐
│ login.php        │
│ Validação        │
│ Hash + Compare   │
└────┬─────────────┘
     │ ✓ Match
     ▼
┌──────────────────┐
│ Criar Sessão     │
│ $_SESSION        │
└────┬─────────────┘
     │ Sucesso
     ▼
┌──────────────────┐
│ Dashboard        │
└──────────────────┘
```

### Fluxo de Pedido (Cliente)

```
┌─────────────┐
│  Cardápio   │
│  (Produtos) │
└──────┬──────┘
       │ Adicionar (JS)
       ▼
┌─────────────┐
│ Carrinho    │
│ (localStorage)
└──────┬──────┘
       │ Finalizar Pedido
       ▼
┌─────────────┐
│  Checkout   │
│  Formulário │
└──────┬──────┘
       │ Submit (POST)
       ▼
┌──────────────────┐
│ /api/pedidos.php │
│ - Validar        │
│ - Inserir BD     │
│ - Transação      │
└──────┬───────────┘
       │ Sucesso
       ▼
┌──────────────┐
│ sucesso.php  │
│ Confirmação  │
└──────────────┘
```

---

## Segurança

### 1. Proteção contra SQL Injection

**Antes (PERIGOSO):**
```php
$query = "SELECT * FROM users WHERE cpf = '" . $_POST['cpf'] . "'";
```

**Depois (SEGURO):**
```php
$stmt = $conn->prepare("SELECT * FROM users WHERE cpf = ?");
$stmt->execute([$_POST['cpf']]);
```

### 2. Validação de Entrada

```php
// Sanitizar
$cpf = sanitizar($_POST['cpf']);

// Validar formato
if (!validarCPF($cpf)) {
    die("CPF inválido");
}

// Usar em query com prepared statement
$stmt = $conn->prepare("SELECT * FROM user WHERE cpf = ?");
$stmt->execute([$cpf]);
```

### 3. Proteção de Sessão

```php
session_start();

// Verificar autenticação
if (!isset($_SESSION['loja_id'])) {
    header('Location: login.php');
    exit();
}
```

### 4. Password Hashing

```php
// Registrar
$senha_hash = hash('sha2', $senha, 256);

// Verificar
if ($usuario['senha'] === hash('sha2', $_POST['senha'], 256)) {
    // Login OK
}
```

### 5. Proteção de Assets

`.htaccess` protege arquivos sensíveis:
```apache
<FilesMatch "(database\.php|\.sql)">
    Deny from all
</FilesMatch>
```

---

## Performance

### Otimizações Implementadas

1. **Índices de Banco de Dados**
```sql
CREATE INDEX idx_produtos_loja ON produtos(loja_id);
CREATE INDEX idx_pedidos_loja ON pedidos(loja_id);
```

2. **Caching CSS/JS**
```apache
<FilesMatch "\.(css|js)$">
    Header set Cache-Control "max-age=31536000"
</FilesMatch>
```

3. **Compressão GZIP**
```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css application/javascript
</IfModule>
```

4. **Lazy Loading de Imagens**
```html
<img src="..." loading="lazy">
```

5. **CSS Minificado**
- Usar ferramentas como CSSNano em produção

---

## Boas Práticas PHP

### 1. Use Prepared Statements SEMPRE

```php
✅ CORRETO
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);

❌ ERRADO
$result = $conn->query("SELECT * FROM users WHERE id = $id");
```

### 2. Trate Exceções

```php
try {
    $stmt = $conn->prepare("SELECT * FROM users");
    $stmt->execute();
} catch (PDOException $e) {
    error_log($e->getMessage());
    die("Erro: Banco de dados indisponível");
}
```

### 3. Validate Inputs

```php
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email inválido";
}
```

### 4. Use Object-Oriented quando possível

```php
class UserRepository {
    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }
    
    public function findById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
```

---

## API Endpoints

### Carrinho

```
POST /api/carrinho.php?action=adicionar
Body: {id, nome, preco, quantidade}

POST /api/carrinho.php?action=remover
Body: {id}

POST /api/carrinho.php?action=atualizar
Body: {id, quantidade}

GET /api/carrinho.php?action=obter

POST /api/carrinho.php?action=limpar
```

### Pedidos

```
POST /api/pedidos.php
Body: {
    cliente_nome,
    cliente_cpf,
    cliente_telefone,
    cliente_endereco,
    metodo_pagamento,
    observacoes,
    carrinho (JSON)
}
```

---

## Variáveis de Sessão

```php
$_SESSION['loja_id']      // ID da loja logada
$_SESSION['loja_nome']    // Nome da loja
$_SESSION['carrinho']     // Carrinho temporário (servidor)
```

---

## Estrutura CSS

O CSS está organizado em seções:

1. **Reset & Variáveis** - Padrões iniciais
2. **Botões** - Todos os tipos
3. **Alerts** - Mensagens
4. **Containers** - Layout principal
5. **Sidebar** - Navegação
6. **Forms** - Formulários
7. **Tables** - Tabelas
8. **Cliente** - Estilo do cardápio
9. **Checkiout** - Finalização
10. **Responsive** - Mobile-first

---

## JavaScript Functions

### Carrinho (Cliente)

```javascript
adicionarAoCarrinho(id, nome, preco, quantidade)
removerDoCarrinho(id)
atualizarQuantidade(id, quantidade)
abrirCarrinho()
fecharCarrinho()
irParaCheckout()
```

### Utilitários

```javascript
formatarMoeda(valor)
formatarCPF(cpf)
sanitizar(texto)
mostrarNotificacao(mensagem, tipo)
```

---

## Próximas Versões

### v2.0
- [ ] Dashboard com gráficos (Chart.js)
- [ ] Relatórios em PDF
- [ ] Integração com WhatsApp API
- [ ] Sistema de reviews/avaliações
- [ ] Multiple lojas (admin)

### v3.0
- [ ] Integração Mercado Pago
- [ ] Integração PagSeguro
- [ ] PWA (Offline support)
- [ ] Notificações Push
- [ ] Aplicativo Mobile (React Native)

---

## Troubleshooting Avançado

### Erro 500 Internal Server Error

1. Ativar exibição de erros:
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

2. Verificar logs:
```bash
// XAMPP
C:\xampp\apache\logs\error.log

// Linux
/var/log/apache2/error.log
```

### Carrinho não salva

1. Verificar localStorage:
```javascript
// No console do navegador (F12)
localStorage.getItem('comandas_carrinho')
```

2. Limpar cache:
- Ctrl + Shift + Delete no navegador

### Banco de dados lento

1. Verificar índices:
```sql
ANALYZE TABLE produtos;
OPTIMIZE TABLE produtos;
```

2. Escalar queries:
```sql
EXPLAIN SELECT * FROM pedidos WHERE loja_id = 1;
```

---

## Links Úteis

- [PHP Official Docs](https://www.php.net/)
- [PDO Prepared Statements](https://www.php.net/manual/en/pdo.prepared-statements.php)
- [MySQL Official](https://www.mysql.com/)
- [MDN Web Docs](https://developer.mozilla.org/)
- [OWASP Security](https://owasp.org/)

---

**Versão**: 1.0
**Última atualização**: 2026
**Status**: Pronto para Produção ✅
