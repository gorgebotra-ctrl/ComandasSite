# 🍔 Comandas Digitais - Sistema de Pedidos Online

Um sistema completo e profissional de pedidos online (comandas digitais) desenvolvido em PHP, MySQL, HTML, CSS e JavaScript. Totalmente compatível com hospedagem gratuita **InfinityFree**.

## 🌟 Características

### Sistema de Autenticação
- ✅ Login seguro para lojas com CPF e senha
- ✅ Proteção contra SQL Injection com prepared statements
- ✅ Sessões PHP seguras
- ✅ Hash de senhas com SHA2

### Painel Administrativo
- 📊 Dashboard com estatísticas em tempo real
- 👥 Gerenciamento completo de produtos (CRUD)
- 📦 Visualização de pedidos com filtros por status
- 💰 Acompanhamento de faturamento
- 🔄 Atualização de status de pedidos

### Área do Cliente
- 🛒 Carrinho de compras com JavaScript (LocalStorage)
- 🏷️ Produtos agrupados por categoria
- 📱 Design responsivo (Mobile e Desktop)
- 🎨 Interface moderna com gradientes

### Finalização de Pedido
- 📝 Formulário com dados do cliente
- 💳 Múltiplos métodos de pagamento (Dinheiro, Cartão, PIX)
- 🔐 Validação de dados com máscara
- 📱 QR Code PIX gerado automaticamente

### Banco de Dados
- 🗄️ 4 tabelas principais (lojas, produtos, pedidos, itens_pedido)
- 🔑 Relacionamentos com integridade referencial
- 📈 Índices para otimização de consultas

## 📁 Estrutura de Pastas

```
/Comandas
├── index.php                    # Redirecionamento
├── db_setup.sql                 # Script para criar banco de dados
├── config/
│   ├── database.php             # Conexão com PDO
│   └── functions.php            # Funções auxiliares e validações
├── admin/                       # Painel administrativo
│   ├── login.php                # Tela de login
│   ├── dashboard.php            # Dashboard principal
│   ├── produtos.php             # Listar produtos
│   ├── adicionar_produto.php    # Adicionar novo produto
│   ├── editar_produto.php       # Editar produto
│   ├── pedidos.php              # Listar pedidos
│   ├── ver_pedido.php           # Visualizar detalhes do pedido
│   └── logout.php               # Logout
├── cliente/                     # Interface pública
│   ├── index.php                # Cardápio (produtos)
│   ├── checkout.php             # Finalização do pedido
│   └── sucesso.php              # Confirmação de pedido
├── api/
│   ├── carrinho.php             # API de gerenciamento de carrinho
│   └── pedidos.php              # API de processamento de pedidos
├── assets/
│   ├── css/
│   │   └── style.css            # Estilos CSS responsivo
│   ├── js/
│   │   └── script.js            # JavaScript principal
│   └── imgs/                    # Pasta para imagens
└── README.md                    # Este arquivo
```

## 🚀 Como Instalar

### Local (XAMPP)

1. **Clonar ou baixar o projeto** na pasta `htdocs`:
```bash
cd C:\xampp\htdocs
# Copiar a pasta Comandas aqui
```

2. **Criar o banco de dados**:
   - Abrir phpMyAdmin: `http://localhost/phpmyadmin`
   - Criar novo banco de dados: `comandas_db`
   - Importar o arquivo `db_setup.sql`
   - Ou executar o SQL manualmente

3. **Configurar conexão** em `config/database.php`:
```php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'comandas_db';
```

4. **Acessar o sistema**:
   - Cliente: `http://localhost/Comandas/`
   - Painel Admin: `http://localhost/Comandas/admin/login.php`

### Hospedagem InfinityFree

1. **Fazer upload dos arquivos** via FTP/File Manager
2. **Criar banco de dados** no painel InfinityFree
3. **Editar `config/database.php`** com dados da hospedagem:
```php
$host = 'seu-host-infinityfree.com';
$username = 'seu-usuario-db';
$password = 'sua-senha-db';
$database = 'seu-database-name';
```
4. **Executar `db_setup.sql`** no phpMyAdmin da hospedagem
5. **Acessar** via seu domínio

## 📝 Dados de Teste

**Painel Admin:**
- CPF: `12345678901`
- Senha: `senha123`

**Dados já existentes:**
- Loja: "Loja Exemplo"
- 4 Produtos pré-cadastrados (Hambúrguer, Pizza, Refrigerante, Batata Frita)

## 🔒 Segurança

- ✅ **Prepared Statements**: Proteção contra SQL Injection
- ✅ **PDO (PHP Data Objects)**: Camada de abstração segura
- ✅ **Hash de Senhas**: Usando SHA2(256)
- ✅ **Validação de Dados**: CPF, E-mail, Campos obrigatórios
- ✅ **Sessões PHP**: Proteção de autenticação
- ✅ **CORS**: Controle de origem para APIs
- ✅ **Sanitização**: Escape de HTML/JS nos outputs

## 🎨 Design

- **Moderno**: Gradientes, sombras e animações
- **Responsivo**: Funciona em mobile, tablet e desktop
- **Acessível**: Contraste adequado de cores
- **Performance**: CSS e JS otimizados
- **Inspiração**: Design similar ao iFood

## 📊 Tabelas do Banco de Dados

### Tabela: lojas
```sql
id (INT) - ID da loja
nome (VARCHAR 150) - Nome da loja
cpf (VARCHAR 14) - CPF da loja
senha (VARCHAR 255) - Hash da senha
email (VARCHAR 150) - Email
telefone (VARCHAR 20) - Telefone
logo (LONGBLOB) - Logo da loja
criado_em - Data de criação
atualizado_em - Data de atualização
```

### Tabela: produtos
```sql
id (INT) - ID do produto
loja_id (INT) - Referência à loja
nome (VARCHAR 200) - Nome do produto
descricao (TEXT) - Descrição
preco (DECIMAL 10,2) - Preço
imagem (LONGBLOB) - Imagem do produto
categoria (VARCHAR 100) - Categoria
ativo (BOOLEAN) - Ativo/Inativo
criado_em - Data de criação
atualizado_em - Data de atualização
```

### Tabela: pedidos
```sql
id (INT) - ID do pedido
loja_id (INT) - Referência à loja
cliente_nome (VARCHAR 200) - Nome do cliente
cliente_cpf (VARCHAR 14) - CPF do cliente
cliente_telefone (VARCHAR 20) - Telefone
cliente_endereco (TEXT) - Endereço
status (ENUM) - pendente, pago, em_preparo, entregue, cancelado
metodo_pagamento (ENUM) - dinheiro, cartao, pix
total (DECIMAL 10,2) - Total do pedido
observacoes (TEXT) - Observações
criado_em - Data de criação
atualizado_em - Data de atualização
```

### Tabela: itens_pedido
```sql
id (INT) - ID do item
pedido_id (INT) - Referência ao pedido
produto_id (INT) - Referência ao produto
quantidade (INT) - Quantidade
preco_unitario (DECIMAL 10,2) - Preço unitário
subtotal (DECIMAL 10,2) - Subtotal (quantidade × preço)
```

## 🔧 Funcionalidades Implementadas

### Admin
- [x] Login com CPF e Senha
- [x] Dashboard com estatísticas
- [x] CRUD completo de Produtos
- [x] Upload de imagens (BLOB)
- [x] Lista de pedidos com filtros
- [x] Visualizar detalhes do pedido
- [x] Atualizar status do pedido
- [x] Logout

### Cliente
- [x] Visualizar cardápio
- [x] Filtrar por categoria
- [x] Adicionar ao carrinho
- [x] Remover do carrinho
- [x] Atualizar quantidade
- [x] Carrinho persistente (LocalStorage)
- [x] Checkout
- [x] Formulário de dados
- [x] Validação de CPF
- [x] Seleção de pagamento
- [x] QR Code PIX
- [x] Confirmação de pedido

### APIs
- [x] Carrinho (adicionar, remover, atualizar, obter)
- [x] Pedidos (processar, salvar no BD)

## 🖥️ Compatibilidade

- **PHP**: 7.2+
- **MySQL**: 5.7+
- **Navegadores**: Chrome, Firefox, Safari, Edge (últimas 2 versões)
- **Dispositivos**: Desktop, Tablet, Mobile
- **Hospedagem**: InfinityFree, Hostinger, Bluehost, HostGator, etc.

## 📱 Responsividade

- **Desktop**: Layout com sidebar (250px) + conteúdo
- **Tablet**: Sidebar reduzida ao ícone (70px)
- **Mobile**: Sidebar oculta ou drawer responsivo

## 🎓 Pontos de Aprendizado

Este projeto é ótimo para aprender:
- PHP orientado a procedimentos
- PDO e prepared statements
- Arquitetura MVC básica
- Design responsivo com CSS Grid e Flexbox
- JavaScript Vanilla (sem frameworks)
- LocalStorage para persistência de dados
- Autenticação e segurança web
- UX/UI design moderno

## 📄 Licença

Este projeto é de código aberto e pode ser usado livremente em projetos pessoais e comerciais.

## 👨‍💻 Suporte

Para dúvidas ou problemas:
1. Verifique a configuração do `database.php`
2. Verifique se o banco de dados foi criado corretamente
3. Verifique permissões de arquivos na hospedagem
4. Procure mensagens de erro no console do navegador (F12)

## 🚀 Próximas Melhorias Sugeridas

- [ ] API RESTful completa
- [ ] Dashboard com gráficos (Chart.js)
- [ ] Exportar relatórios em PDF/Excel
- [ ] Sistema de avaliação de pedidos
- [ ] Notificações via WhatsApp/Email
- [ ] Integração com gateway de pagamento real
- [ ] PWA (Progressive Web App)
- [ ] Autenticação com OAuth

---

**Desenvolvido com ❤️ para Digital Comandas** 🍔
