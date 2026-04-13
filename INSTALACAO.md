# 📋 GUIA DE INSTALAÇÃO E CONFIGURAÇÃO

## ⚙️ Pré-requisitos

- PHP 7.2 ou superior
- MySQL 5.7 ou superior  
- Extensão PDO habilitada no PHP
- Um servidor web (Apache, Nginx)

## 🎯 Instalação Local (XAMPP)

### Passo 1: Preparar o Ambiente

```bash
# 1. Abrir o XAMPP Control Panel
cd C:\xampp
xampp-control.exe

# 2. Iniciar Apache e MySQL
# Clicar em "Start" para ambos
```

### Passo 2: Preparar os Arquivos

```bash
# 1. Extrair a pasta Comandas em:
C:\xampp\htdocs\Comandas

# Estrutura final:
C:\xampp\htdocs\
└── Comandas/
    ├── admin/
    ├── cliente/
    ├── api/
    ├── config/
    ├── assets/
    ├── index.php
    ├── db_setup.sql
    └── README.md
```

### Passo 3: Criar o Banco de Dados

**Opção A: Usando phpMyAdmin (Recomendado)**

1. Abrir `http://localhost/phpmyadmin` no navegador
2. Clicar em "Novo" → "Banco de dados"
3. Nome: `comandas_db`
4. Clique em "Criar"
5. Selionar o banco `comandas_db`
6. Ir em "Importar"
7. Selecionar arquivo `db_setup.sql`
8. Clicar em "Executar"

**Opção B: Usando CLI (Linha de comando)**

```bash
# Abrir CMD/PowerShell em C:\xampp\mysql\bin
cd C:\xampp\mysql\bin

# Conectar ao MySQL
mysql -u root

# Executar commands
CREATE DATABASE comandas_db;
USE comandas_db;
SOURCE C:\xampp\htdocs\Comandas\db_setup.sql;
EXIT;
```

### Passo 4: Verificar a Conexão

1. Abrir `http://localhost/Comandas/admin/login.php`
2. Se houver erro de conexão, verificar `config/database.php`

Se receber erro como "SQLSTATE[HY000]: General error", tente:
- Reiniciar Apache e MySQL
- Verificar se PDO está habilitado em php.ini
- Verificar permissões da pasta

### Passo 5: Acessar o Sistema

**Cliente (Cardápio):**
```
http://localhost/Comandas/
```

**Painel Admin:**
```
http://localhost/Comandas/admin/login.php
```

**Dados de Teste:**
- CPF: 12345678901
- Senha: senha123

---

## 🌐 Instalação em Hospedagem (InfinityFree)

### Passo 1: Preparar o FTP

1. Ir para painel InfinityFree
2. Copiar dados FTP:
   - Host: (algo como freepik.000webhostapp.com)
   - Usuário: (seu usuário FTP)
   - Senha: (sua senha FTP)
   - Porta: 21

### Passo 2: Upload dos Arquivos

**Usando FileZilla (Recomendado):**

1. Baixar e instalar FileZilla
2. Ir em "Arquivo" → "Gerenciador de Sites"
3. Criar novo site com dados FTP
4. Conectar
5. Navegação esquerda → Pasta do Comandas
6. Navegação direita → public_html
7. Fazer upload de todos os arquivos

**Ou usar o File Manager do InfinityFree:**

1. Painel InfinityFree → File Manager
2. Criar pasta: `Comandas`
3. Upload de arquivos (pode ser lento)

### Passo 3: Criar Banco de Dados

1. No painel InfinityFree, abrir phpMyAdmin
2. Criar novo banco de dados
3. Importar `db_setup.sql`

### Passo 4: Configurar Conexão

Editar `config/database.php` com dados da hospedagem:

```php
// Dados que você encontra no painel InfinityFree
$host = 'localhost';  // Geralmente localhost mesmo
$username = 'seu_usuario_db';  // Mostrado no painel
$password = 'sua_senha_db';     // Você define na criação
$database = 'seu_database_name';  // Nome do banco
```

### Passo 5: Testar

1. Acessar: `https://seudominio.000webhostapp.com/Comandas/`
2. Testar login com dados de teste
3. Verificar se banco de dados está funcionando

**Troubleshooting InfinityFree:**
- Se receber erro de conexão, verifique os dados do banco
- PDO deve estar habilitado (é padrão)
- Check: Error logs no painel InfinityFree

---

## 🔍 Verificação de Dados

### Verificar Banco de Dados

```sql
-- Ver todas as lojas
SELECT * FROM lojas;

-- Ver todos os produtos
SELECT * FROM produtos;

-- Ver todos os pedidos
SELECT * FROM pedidos;

-- Ver itens de um pedido específico
SELECT * FROM itens_pedido WHERE pedido_id = 1;
```

### Verificar Arquivo de Configuração

Editar `config/database.php` e verificar se está assim:

```php
<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'comandas_db';

try {
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
    die("Erro: " . $e->getMessage());
}
?>
```

---

## 📝 Teste as Funcionalidades

### Admin

1. **Login:**
   - Ir para: `/admin/login.php`
   - CPF: 12345678901
   - Senha: senha123

2. **Dashboard:**
   - Verificar estatísticas
   - Ver pedidos recentes

3. **Produtos:**
   - Clicar em "Produtos"
   - Ver 4 produtos de teste
   - Tentar adicionar novo produto
   - Tentar editar um produto
   - Tentar deletar um produto

4. **Pedidos:**
   - Clicar em "Pedidos"
   - Ver se há algum pedido
   - Clicar em um pedido para ver detalhes
   - Tentar atualizar status

### Cliente

1. **Cardápio:**
   - Ir para: `/cliente/index.php`
   - Ver produtos
   - Filtrar por categoria

2. **Carrinho:**
   - Adicionar produto
   - Ver carrinho
   - Remover produto
   - Atualizar quantidade

3. **Checkout:**
   - Clicar "Finalizar Pedido"
   - Preencher dados pessoais
   - Selecionar método de pagamento
   - Se PIX, ver QR Code
   - Enviar pedido

4. **Sucesso:**
   - Ver confirmação de pedido
   - Verificar se pedido foi criado no banco

---

## 🐛 Soluções de Problemas Comuns

### Erro: "SQLSTATE[HY000]: General error"

**Solução:**
- Reiniciar Apache e MySQL
- Verificar se `db_setup.sql` foi executado corretamente
- Verificar permissões do MySQL

### Erro: "Class PDO not found"

**Solução:**
- Abrir `C:\xampp\php\php.ini`
- Procurar por `;extension=pdo_mysql`
- Remover o `;` do início
- Remover `;extension=pdo` também
- Salvar e reiniciar Apache

### Erro: "Access denied for user"

**Solução:**
- Verificar usuário e senha do MySQL em `config/database.php`
- InfinityFree usa dados específicos (verificar painel)

### Carrinho não persiste

**Solução:**
- Verificar se navegador permite LocalStorage
- Abrir DevTools (F12) → Application → LocalStorage
- Verificar se há dado com chave `comandas_carrinho`

### Imagens de produtos não aparecem

**Solução:**
- Verificar se imagens foram salvas no banco (BLOB)
- Verificar permissões da pasta `/assets/imgs/`
- Tentar fazer upload de imagem novamente

---

## 📞 Suporte

Se ainda tiver dúvidas:

1. **Verificar logs do PHP:**
   - XAMPP: `C:\xampp\apache\logs\error.log`
   - InfinityFree: Painel → Logs

2. **Abrir DevTools do navegador (F12):**
   - Aba Console
   - Aba Network
   - Procurar por erros

3. **Testar conectividade:**
   - Criar arquivo teste.php com: `<?php phpinfo(); ?>`
   - Acessar: `http://localhost/Comandas/teste.php`

---

## ✅ Checklist Final

- [ ] Banco de dados criado
- [ ] Tabelas importadas
- [ ] `config/database.php` configurado
- [ ] Admin login funcionando
- [ ] Produto aparece no cardápio
- [ ] Carrinho funciona
- [ ] Pedido é criado no banco
- [ ] Status é atualizado no admin

🎉 **Sistema pronto para uso!**
