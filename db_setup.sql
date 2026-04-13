-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS comandas_db;
USE comandas_db;

-- Tabela de Lojas (Vendedores)
CREATE TABLE IF NOT EXISTS lojas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    email VARCHAR(150),
    telefone VARCHAR(20),
    logo LONGBLOB,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de Produtos
CREATE TABLE IF NOT EXISTS produtos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    loja_id INT NOT NULL,
    nome VARCHAR(200) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10, 2) NOT NULL,
    imagem LONGBLOB,
    categoria VARCHAR(100),
    ativo BOOLEAN DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (loja_id) REFERENCES lojas(id) ON DELETE CASCADE
);

-- Tabela de Pedidos
CREATE TABLE IF NOT EXISTS pedidos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    loja_id INT NOT NULL,
    cliente_nome VARCHAR(200) NOT NULL,
    cliente_cpf VARCHAR(14),
    cliente_telefone VARCHAR(20) NOT NULL,
    cliente_endereco TEXT NOT NULL,
    status ENUM('pendente', 'pago', 'em_preparo', 'entregue', 'cancelado') DEFAULT 'pendente',
    metodo_pagamento ENUM('dinheiro', 'cartao', 'pix') NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    observacoes TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (loja_id) REFERENCES lojas(id) ON DELETE CASCADE
);

-- Tabela de Itens do Pedido
CREATE TABLE IF NOT EXISTS itens_pedido (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE RESTRICT
);

-- Índices para otimizar consultas
CREATE INDEX idx_produtos_loja ON produtos(loja_id);
CREATE INDEX idx_pedidos_loja ON pedidos(loja_id);
CREATE INDEX idx_pedidos_status ON pedidos(status);
CREATE INDEX idx_itens_pedido ON itens_pedido(pedido_id);

-- Inserir uma loja de exemplo para testes
INSERT INTO lojas (nome, cpf, senha, email, telefone) VALUES 
('Loja Exemplo', '12345678901', SHA2('senha123', 256), 'loja@exemplo.com', '1199999999');

-- Inserir produtos de exemplo
INSERT INTO produtos (loja_id, nome, descricao, preco, categoria) VALUES 
(1, 'Hambúrguer Clássico', 'Pão integral com carne 200g', 35.90, 'Lanches'),
(1, 'Pizza Margherita', 'Molho, queijo e manjericão', 45.00, 'Pizzas'),
(1, 'Refrigerante 2L', 'Refrigerante gelado', 8.00, 'Bebidas'),
(1, 'Batata Frita', 'Batata frita crocante 500g', 15.90, 'Acompanhamentos');
