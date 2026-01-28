# Tech-Fit
Projeto em dupla de Diogo Scherrer e Henrique Delado
Link Trello método Scrum: https://trello.com/b/iQh8mxWr/scrum-software
# TechFit - Sistema de Gerenciamento de Academia

## 📋 Descrição
Sistema completo MVC CRUD para gerenciamento de academia com funcionalidades de:
- Gerenciamento de Usuários
- Gerenciamento de Membros
- Controle de Pagamentos
- Dashboard com estatísticas

## 🛠️ Requisitos
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Apache (recomendado)

## 📁 Estrutura do Projeto

```
TechFit/
├── app/
│   ├── models/
│   │   ├── User.php
│   │   ├── Membro.php
│   │   └── Pagamento.php
│   ├── controllers/
│   │   ├── UserController.php
│   │   ├── MembroController.php
│   │   └── PagamentoController.php
│   └── views/
│       ├── layout/
│       │   ├── header.php
│       │   └── footer.php
│       ├── dashboard.php
│       ├── usuarios/
│       ├── membros/
│       └── pagamentos/
├── config/
│   ├── Database.php
│   └── init.php
├── Downloads/
│   └── BDTechFit/
│       └── setup.sql
└── index.php
```

## 🚀 Instalação e Configuração

### 1. Criar Banco de Dados
Abra seu MySQL e execute o arquivo `Downloads/BDTechFit/setup.sql`:

```sql
-- No MySQL/phpMyAdmin
-- Executar o arquivo setup.sql completo
```

### 2. Configurar Conexão com BD
Edite o arquivo `config/Database.php`:

```php
private $host = 'localhost';      // Seu host (geralmente localhost)
private $db_name = 'techfit_db';  // Nome do banco
private $db_user = 'root';        // Usuário MySQL
private $db_pass = '';            // Senha MySQL
```

### 3. Copiar Arquivos
Copie toda a pasta `TechFit` para seu servidor web (htdocs se for XAMPP)

### 4. Acessar a Aplicação
Abra seu navegador e acesse:
```
http://localhost/TechFit/
```

## 📊 Funcionalidades

### Dashboard
- Visualização de estatísticas gerais
- Últimos usuários cadastrados
- Últimos pagamentos registrados
- Contadores de usuários, membros e pagamentos

### Usuários
- Listar usuários
- Criar novo usuário
- Editar usuário
- Deletar usuário
- Validação de email único

### Membros
- Listar membros
- Criar novo membro (vinculado a usuário)
- Editar membros
- Deletar membros
- Tipos de plano: Bronze, Prata, Ouro

### Pagamentos
- Listar pagamentos
- Registrar novo pagamento
- Editar pagamento
- Deletar pagamento
- Relatório financeiro
- Métodos: Cartão, Boleto, Dinheiro, PIX
- Status: Pago, Pendente, Cancelado

## 💾 Banco de Dados

### Tabelas Criadas

#### usuarios
```sql
- id (PK)
- nome
- email (UNIQUE)
- telefone
- endereco
- data_cadastro
- data_atualizacao
```

#### membros
```sql
- id (PK)
- usuario_id (FK)
- tipo_plano
- data_inicio
- data_fim
- status
- data_cadastro
```

#### pagamentos
```sql
- id (PK)
- membro_id (FK)
- valor
- data_pagamento
- metodo_pagamento
- status
- descricao
- data_cadastro
```

## 🔐 Segurança
- Preparação de queries contra SQL Injection (PDO Prepared Statements)
- Validação de entrada de dados
- Escape de HTML com htmlspecialchars()
- Sessões para controle de fluxo

## 🎨 Interface
- Bootstrap 5.1.3
- Responsivo e mobile-friendly
- FontAwesome 6.0 para ícones
- Design moderno e intuitivo

## 🔧 Manutenção

### Adicionar Novo Módulo
1. Criar classe em `app/models/`
2. Criar controlador em `app/controllers/`
3. Criar views em `app/views/novo_modulo/`
4. Adicionar case no switch do index.php

### Modificar Banco de Dados
Edite `Downloads/BDTechFit/setup.sql` e reimporte

## 📝 Exemplos de Uso

### Acessar Usuários
```
http://localhost/TechFit/?module=usuarios
```

### Acessar Membros
```
http://localhost/TechFit/?module=membros
```

### Acessar Pagamentos
```
http://localhost/TechFit/?module=pagamentos
```

### Ver Relatório de Pagamentos
```
http://localhost/TechFit/?module=pagamentos&action=relatorio
```

## 🐛 Resolução de Problemas

### Erro: "Erro de Conexão"
- Verifique se MySQL está rodando
- Verifique credenciais em `config/Database.php`
- Verifique se o banco de dados foi criado

### Erro: "Classe não encontrada"
- Verifique se o autoload está funcional em `config/init.php`
- Confirme se os arquivos estão na pasta correta

### Erro: "Table not found"
- Execute o arquivo `setup.sql` novamente
- Verifique o nome do banco de dados

## 📞 Suporte
Para dúvidas ou problemas, verifique os logs do servidor ou do navegador (F12)

## 📄 Licença
Este projeto é fornecido como está para uso educacional.

## 🎓 Recursos Adicionais
- [Documentação PHP](https://www.php.net/docs.php)
- [Bootstrap Docs](https://getbootstrap.com/docs)
- [MySQL Reference](https://dev.mysql.com/doc/)

---
**Desenvolvido com ❤️ usando PHP, MySQL e Bootstrap**
