--------------------------------------------------------------
-- Banco de dados: tech_fit
-- Created by: Henrique e Diogo
--------------------------------------------------------------
DROP DATABASE IF EXISTS tech_fit;
CREATE DATABASE tech_fit CHARACTER SET = utf8mb4; -- Determina a linguagem a ser ultilizada no banco de dados
USE tech_fit; 

-- planos
DROP TABLE IF EXISTS PLANOS;
CREATE TABLE PLANOS( 
ID_PLANO INT AUTO_INCREMENT PRIMARY KEY,
NOME_PLANO VARCHAR(100) NOT NULL UNIQUE, -- UNIQUE É PARA DETERMINAR QUE O NOME DO PLANO É UNICO
VALOR DECIMAL(8,2) NOT NULL,
BENEFICIOS VARCHAR(1000) DEFAULT NULL,
DURACAO_MES SMALLINT UNSIGNED NOT NULL DEFAULT 1, -- SMALLINT SERVE PARA COLCOARMOS NÚMEROS INTEIROS PEQUENOS
CRIADO_EM TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP);

-- ALUNOS
DROP TABLE IF EXISTS ALUNOS;
CREATE TABLE ALUNOS (
  ID_ALUNO INT AUTO_INCREMENT PRIMARY KEY,
  NOME VARCHAR(150) NOT NULL,
  ENDERECO VARCHAR(500),
  NASCIMENTO DATE NOT NULL,
  TELEFONE VARCHAR(20),
  CPF VARCHAR(14) NOT NULL UNIQUE,
  SEXO ENUM('MASCULINO','FEMININO','OUTRO','NAO_DECLARAR') DEFAULT 'NAO_DECLARAR',
  EMAIL VARCHAR(255) NOT NULL UNIQUE,
  SENHA_HASH VARCHAR(255) NOT NULL,
  STATUS_ALUNO ENUM('ATIVO','INATIVO','SUSPENSO') NOT NULL DEFAULT 'ATIVO',
  ID_PLANO INT NOT NULL,
  CRIADO_EM TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_alunos_plano (ID_PLANO), -- INDEX SERVE PARA BUSCAR MAIS RÁPDIO
  CONSTRAINT fk_alunos_planos FOREIGN KEY (ID_PLANO) REFERENCES PLANOS(ID_PLANO));
  
-- FUNCIONÁRIOS
DROP TABLE IF EXISTS FUNCIONARIOS;
CREATE TABLE FUNCIONARIOS (
  ID_FUNCIONARIO INT AUTO_INCREMENT PRIMARY KEY,
  NOME_FUNCIONARIO VARCHAR(150) NOT NULL,
  LOGIN_REDE VARCHAR(255) NOT NULL,
  NASCIMENTO_FUNCIONARIO DATE NOT NULL,
  CPF_FUNCIONARIO VARCHAR(14) NOT NULL UNIQUE,
  TELEFONE_FUNCIONARIO VARCHAR(20),
  DATA_ADMISSAO DATE NOT NULL,
  SALARIO DECIMAL(10,2) NOT NULL CHECK (SALARIO >= 0),
  CARGO VARCHAR(80) NOT NULL,
  ENDERECO_FUNCIONARIO VARCHAR(500),
  EMAIL_FUNCIONARIO VARCHAR(100) NOT NULL UNIQUE UNIQUE,
  SENHA_FUNCIONARIO VARCHAR(255),
  TURNO ENUM('MANHA','TARDE','NOITE','ROTATIVO') DEFAULT 'ROTATIVO',
  ID_MODALIDADE INT DEFAULT NULL);
  
  
  
-- UNIDADES
DROP TABLE IF EXISTS UNIDADES;
CREATE TABLE UNIDADES (
  ID_UNIDADE INT AUTO_INCREMENT PRIMARY KEY,
  NOME_UNIDADE VARCHAR(120) NOT NULL,
  ENDERECO_UNIDADE VARCHAR(500) NOT NULL UNIQUE,
  TELEFONE_UNIDADE VARCHAR(20),
  ID_GERENTE INT DEFAULT NULL,
  CRIADO_EM TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP);
  

  
-- EXERCÍCIOS
DROP TABLE IF EXISTS MODALIDADES;
CREATE TABLE MODALIDADES (
  ID_MODALIDADE INT AUTO_INCREMENT PRIMARY KEY,
  NOME_MODALIDADE VARCHAR(120) NOT NULL,
  DESCRICAO_MODALIDADE VARCHAR(1000),
  CRIADO_EM TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_modalidades_nome (NOME_MODALIDADE)
);
	



-- AULAS
DROP TABLE IF EXISTS AULAS;
CREATE TABLE AULAS (
  ID_AULA INT AUTO_INCREMENT PRIMARY KEY,
  NOME_AULA VARCHAR(150),
  ID_MODALIDADE INT NOT NULL,
  ID_INSTRUTOR INT DEFAULT NULL,
  ID_UNIDADE INT DEFAULT NULL,
  DIA_SEMANA ENUM('DOM','SEG','TER','QUA','QUI','SEX','SAB') NOT NULL,
  HORARIO_INICIO TIME NOT NULL,
  DURACAO_MINUTOS SMALLINT UNSIGNED NOT NULL DEFAULT 60,
  VAGAS SMALLINT UNSIGNED NOT NULL DEFAULT 30,
  CRIADO_EM TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_aulas_modalidade (ID_MODALIDADE),
  INDEX idx_aulas_instrutor (ID_INSTRUTOR),
  INDEX idx_aulas_unidade (ID_UNIDADE),
  CONSTRAINT fk_aulas_modalidade FOREIGN KEY (ID_MODALIDADE) REFERENCES MODALIDADES(ID_MODALIDADE)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_aulas_instrutor FOREIGN KEY (ID_INSTRUTOR) REFERENCES FUNCIONARIOS(ID_FUNCIONARIO)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_aulas_unidade FOREIGN KEY (ID_UNIDADE) REFERENCES UNIDADES(ID_UNIDADE));
  
  -- PAGAMENTO
  DROP TABLE IF EXISTS PAGAMENTOS;
CREATE TABLE PAGAMENTOS (
  ID_PAGAMENTO INT AUTO_INCREMENT PRIMARY KEY,
  ID_ALUNO INT NOT NULL,
  TIPO_PAGAMENTO ENUM('DINHEIRO','CREDITO', 'DEBITO','PIX') NOT NULL,
  VALOR_PAGAMENTO DECIMAL(10,2) NOT NULL CHECK (VALOR_PAGAMENTO >= 0),
  DATA_PAGAMENTO DATE NOT NULL,
  CRIADO_EM TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_pagamentos_aluno (ID_ALUNO),
  CONSTRAINT fk_pagamentos_alunos FOREIGN KEY (ID_ALUNO) REFERENCES ALUNOS(ID_ALUNO)
    ON UPDATE CASCADE ON DELETE RESTRICT);
    
-- PERTENCE
DROP TABLE IF EXISTS PERTENCE;
CREATE TABLE PERTENCE (
  ID_UNIDADE INT NOT NULL,
  ID_ALUNO INT NOT NULL,
  DATA_INSCRICAO DATE NOT NULL DEFAULT (CURRENT_DATE),
  PRIMARY KEY (ID_UNIDADE, ID_ALUNO),
  INDEX idx_pertence_aluno (ID_ALUNO),
  CONSTRAINT fk_pertence_unidade FOREIGN KEY (ID_UNIDADE) REFERENCES UNIDADES(ID_UNIDADE),
  CONSTRAINT fk_pertence_aluno FOREIGN KEY (ID_ALUNO) REFERENCES ALUNOS(ID_ALUNO));
  
  -- PARTICIPAM
  DROP TABLE IF EXISTS PARTICIPAM;
CREATE TABLE PARTICIPAM (
  ID_AULA INT NOT NULL,
  ID_ALUNO INT NOT NULL,
  DATA_PARTICIPACAO DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (ID_AULA, ID_ALUNO),
  INDEX idx_participam_aluno (ID_ALUNO),
  CONSTRAINT fk_participam_aula FOREIGN KEY (ID_AULA) REFERENCES AULAS(ID_AULA),
  CONSTRAINT fk_participam_aluno FOREIGN KEY (ID_ALUNO) REFERENCES ALUNOS(ID_ALUNO));
  
  -- INSCRITOS
  DROP TABLE IF EXISTS INSCRITOS;
CREATE TABLE INSCRITOS (
  ID_MODALIDADE INT NOT NULL,
  ID_ALUNO INT NOT NULL,
  DATA_INSCRICAO DATE NOT NULL DEFAULT (CURRENT_DATE),
  PRIMARY KEY (ID_MODALIDADE, ID_ALUNO),
  INDEX idx_inscritos_aluno (ID_ALUNO),
  CONSTRAINT fk_inscritos_modalidade FOREIGN KEY (ID_MODALIDADE) REFERENCES MODALIDADES(ID_MODALIDADE),
  CONSTRAINT fk_inscritos_aluno FOREIGN KEY (ID_ALUNO) REFERENCES ALUNOS(ID_ALUNO));
  
-- TEM
DROP TABLE IF EXISTS  TEM;
CREATE TABLE TEM (
  ID_MODALIDADE INT NOT NULL,
  ID_FUNCIONARIO INT NOT NULL,
  DATA_ATUACAO DATE DEFAULT (CURRENT_DATE),
  PRIMARY KEY (ID_MODALIDADE, ID_FUNCIONARIO),
  INDEX idx_tem_funcionario (ID_FUNCIONARIO),
  CONSTRAINT fk_tem_modalidade FOREIGN KEY (ID_MODALIDADE) REFERENCES MODALIDADES(ID_MODALIDADE),
  CONSTRAINT fk_tem_funcionario FOREIGN KEY (ID_FUNCIONARIO) REFERENCES FUNCIONARIOS(ID_FUNCIONARIO));
  
  INSERT INTO PLANOS (NOME_PLANO, VALOR, BENEFICIOS, DURACAO_MES) VALUES
('Básico', 89.00, 'Acesso à academia, Área de musculação, 1 aula coletiva por semana', 1),
('Premium', 129.00, 'Todos os benefícios do plano Básico, Aulas ilimitadas, Área de cardio', 1),
('Elite', 180.00, 'Todos os benefícios do plano Premium, Personal trainer, Avaliação física', 1);

INSERT INTO UNIDADES (NOME_UNIDADE, ENDERECO_UNIDADE, TELEFONE_UNIDADE) VALUES
-- São Paulo - Capital
('Tech Fit Paulista', 'Avenida Paulista, 1000 - Cerqueira César, São Paulo - SP', '(11) 2222-3333'),
('Tech Fit Ibirapuera', 'Rua Vergueiro, 1500 - Vila Mariana, São Paulo - SP', '(11) 2222-4444'),
('Tech Fit Morumbi', 'Avenida Morumbi, 2500 - Morumbi, São Paulo - SP', '(11) 2222-5555'),
('Tech Fit Pinheiros', 'Rua dos Pinheiros, 800 - Pinheiros, São Paulo - SP', '(11) 2222-6666'),

-- Interior de São Paulo
('Tech Fit Limeira', 'Rua Dr. Trajano Barros de Camargo, 150 - Centro, Limeira - SP', '(19) 3304-7800'),
('Tech Fit Campinas', 'Avenida John Boyd Dunlop, 350 - Jardim Ipaussurama, Campinas - SP', '(19) 3304-7801'),
('Tech Fit São José dos Campos', 'Av. Dr. Nelson D''Ávila, 2200 - Vila das Acácias, São José dos Campos - SP', '(12) 3304-7802'),
('Tech Fit Ribeirão Preto', 'Rua Álvares Cabral, 1800 - Centro, Ribeirão Preto - SP', '(16) 3304-7803'),

-- Outros estados
('Tech Fit Rio de Janeiro', 'Avenida Atlântica, 2000 - Copacabana, Rio de Janeiro - RJ', '(21) 3304-7804'),
('Tech Fit Belo Horizonte', 'Avenida do Contorno, 8000 - Lourdes, Belo Horizonte - MG', '(31) 3304-7805'),
('Tech Fit Curitiba', 'Rua XV de Novembro, 1200 - Centro, Curitiba - PR', '(41) 3304-7806'),
('Tech Fit Porto Alegre', 'Avenida Ipiranga, 1500 - Praia de Belas, Porto Alegre - RS', '(51) 3304-7807'),
('Tech Fit Brasília', 'SHIS QI 15 Bloco E - Lago Sul, Brasília - DF', '(61) 3304-7808'),
('Tech Fit Salvador', 'Avenida Tancredo Neves, 1500 - Caminho das Árvores, Salvador - BA', '(71) 3304-7809'),
('Tech Fit Fortaleza', 'Av. Beira Mar, 3000 - Meireles, Fortaleza - CE', '(85) 3304-7810');

INSERT INTO ALUNOS (NOME, ENDERECO, NASCIMENTO, TELEFONE, CPF, SEXO, EMAIL, SENHA_HASH, STATUS_ALUNO, ID_PLANO) VALUES
('João Silva', 'Rua das Flores, 123 - Centro', '1990-05-15', '(11) 99999-1111', '123.456.789-01', 'MASCULINO', 'joao.silva@email.com', SHA2('senha123', 256), 'ATIVO', 1),
('Maria Santos', 'Av. Brasil, 456 - Jardins', '1985-08-22', '(11) 99999-2222', '234.567.890-12', 'FEMININO', 'maria.santos@email.com', SHA2('senha456', 256), 'ATIVO', 2),
('Carlos Oliveira', 'Rua Augusta, 789 - Consolação', '1995-02-10', '(11) 99999-3333', '345.678.901-23', 'MASCULINO', 'carlos.oliveira@email.com', SHA2('senha789', 256), 'ATIVO', 3),
('Ana Costa', 'Alameda Santos, 101 - Cerqueira César', '1988-11-30', '(11) 99999-4444', '456.789.012-34', 'FEMININO', 'ana.costa@email.com', SHA2('senha101', 256), 'SUSPENSO', 1),
('Pedro Almeida', 'Rua Oscar Freire, 202 - Pinheiros', '1992-07-18', '(11) 99999-5555', '567.890.123-45', 'MASCULINO', 'pedro.almeida@email.com', SHA2('senha202', 256), 'INATIVO', 2),
('Julia Ribeiro', 'Av. Paulista, 303 - Bela Vista', '1998-04-25', '(11) 99999-6666', '678.901.234-56', 'FEMININO', 'julia.ribeiro@email.com', SHA2('senha303', 256), 'ATIVO', 1),
('Marcos Souza', 'Rua Haddock Lobo, 404 - Jardins', '1983-09-12', '(11) 99999-7777', '789.012.345-67', 'MASCULINO', 'marcos.souza@email.com', SHA2('senha404', 256), 'ATIVO', 3),
('Fernanda Lima', 'Al. Jaú, 505 - Jardim Paulista', '1991-12-05', '(11) 99999-8888', '890.123.456-78', 'FEMININO', 'fernanda.lima@email.com', SHA2('senha505', 256), 'ATIVO', 2),
('Ricardo Martins', 'Rua Estados Unidos, 606 - Jardim América', '1987-06-20', '(11) 99999-9999', '901.234.567-89', 'MASCULINO', 'ricardo.martins@email.com', SHA2('senha606', 256), 'SUSPENSO', 1),
('Camila Ferreira', 'Av. Rebouças, 707 - Pinheiros', '1994-03-08', '(11) 99999-0000', '012.345.678-90', 'FEMININO', 'camila.ferreira@email.com', SHA2('senha707', 256), 'ATIVO', 2);

INSERT INTO FUNCIONARIOS (NOME_FUNCIONARIO, LOGIN_REDE, NASCIMENTO_FUNCIONARIO, CPF_FUNCIONARIO, TELEFONE_FUNCIONARIO, DATA_ADMISSAO, SALARIO, CARGO, ENDERECO_FUNCIONARIO, EMAIL_FUNCIONARIO, SENHA_FUNCIONARIO, TURNO, ID_MODALIDADE) VALUES
('Roberto Carlos', 'roberto.carlos', '1980-03-25', '111.222.333-44', '(11) 98888-1111', '2020-01-15', 4500.00, 'Gerente', 'Rua das Laranjeiras, 100 - Jardins', 'roberto.carlos@techfit.com', SHA2('func123', 256), 'MANHA', NULL),
('Patrícia Mendes', 'patricia.mendes', '1985-07-12', '222.333.444-55', '(11) 98888-2222', '2021-03-10', 3800.00, 'Instrutor', 'Av. Europa, 200 - Jardim Europa', 'patricia.mendes@techfit.com', SHA2('func456', 256), 'TARDE', 1),
('Lucas Oliveira', 'lucas.oliveira', '1990-11-30', '333.444.555-66', '(11) 98888-3333', '2022-05-20', 3500.00, 'Instrutor', 'Rua Bela Cintra, 300 - Consolação', 'lucas.oliveira@techfit.com', SHA2('func789', 256), 'NOITE', 2),
('Sandra Nunes', 'sandra.nunes', '1978-04-18', '444.555.666-77', '(11) 98888-4444', '2019-08-05', 4200.00, 'Recepcionista', 'Al. Campinas, 400 - Jardim Paulista', 'sandra.nunes@techfit.com', SHA2('func101', 256), 'ROTATIVO', NULL),
('Felipe Costa', 'felipe.costa', '1988-09-22', '555.666.777-88', '(11) 98888-5555', '2023-01-30', 3200.00, 'Instrutor', 'Rua Augusta, 500 - Cerqueira César', 'felipe.costa@techfit.com', SHA2('func202', 256), 'MANHA', 3),
('Amanda Silva', 'amanda.silva', '1992-12-15', '666.777.888-99', '(11) 98888-6666', '2022-07-15', 3000.00, 'Nutricionista', 'Av. Paulista, 600 - Bela Vista', 'amanda.silva@techfit.com', SHA2('func303', 256), 'TARDE', NULL),
('Rodrigo Santos', 'rodrigo.santos', '1983-06-08', '777.888.999-00', '(11) 98888-7777', '2020-11-25', 4000.00, 'Personal Trainer', 'Rua Haddock Lobo, 700 - Jardins', 'rodrigo.santos@techfit.com', SHA2('func404', 256), 'NOITE', 4),
('Tatiane Lima', 'tatiane.lima', '1987-02-28', '888.999.000-11', '(11) 98888-8888', '2021-09-10', 2800.00, 'Recepcionista', 'Al. Santos, 800 - Cerqueira César', 'tatiane.lima@techfit.com', SHA2('func505', 256), 'ROTATIVO', NULL),
('Marcelo Rocha', 'marcelo.rocha', '1975-10-05', '999.000.111-22', '(11) 98888-9999', '2018-04-18', 5000.00, 'Coordenador', 'Rua Oscar Freire, 900 - Pinheiros', 'marcelo.rocha@techfit.com', SHA2('func606', 256), 'MANHA', NULL),
('Bianca Alves', 'bianca.alves', '1995-08-14', '000.111.222-33', '(11) 98888-0000', '2023-03-01', 2900.00, 'Instrutor', 'Av. Rebouças, 1000 - Pinheiros', 'bianca.alves@techfit.com', SHA2('func707', 256), 'TARDE', 5);
  
INSERT INTO MODALIDADES (NOME_MODALIDADE, DESCRICAO_MODALIDADE) VALUES
('Musculação', 'Treinamento com pesos e equipamentos para fortalecimento muscular'),
('Spinning', 'Aula de ciclismo indoor com música motivacional'),
('Zumba', 'Aula de dança fitness com ritmos latinos'),
('Pilates', 'Método de exercícios para fortalecimento e alongamento'),
('CrossFit', 'Treinamento funcional de alta intensidade'),
('Yoga', 'Prática de posturas e respiração para equilíbrio físico e mental'),
('Natação', 'Aulas de natação para todos os níveis'),
('Boxe', 'Treinamento de boxe para condicionamento físico'),
('Funcional', 'Exercícios que simulam movimentos do dia a dia'),
('Alongamento', 'Aulas focadas em flexibilidade e relaxamento muscular');

-- Inserindo funcionários (instrutores)
INSERT INTO FUNCIONARIOS (NOME_FUNCIONARIO, LOGIN_REDE, NASCIMENTO_FUNCIONARIO, CPF_FUNCIONARIO, TELEFONE_FUNCIONARIO, DATA_ADMISSAO, SALARIO, CARGO, ENDERECO_FUNCIONARIO, EMAIL_FUNCIONARIO, SENHA_FUNCIONARIO, TURNO, ID_MODALIDADE) VALUES
-- Instrutores de Musculação
('Carlos Silva', 'carlos.silva', '1985-03-15', '111.222.333-01', '(11) 98888-1001', '2022-01-10', 3500.00, 'Instrutor', 'Rua das Flores, 100 - Centro', 'carlos.silva@techfit.com', SHA2('senha123', 256), 'MANHA', 1),
('Ana Paula Santos', 'ana.santos', '1990-07-22', '111.222.333-02', '(11) 98888-1002', '2022-03-15', 3200.00, 'Instrutor', 'Av. Brasil, 200 - Jardins', 'ana.santos@techfit.com', SHA2('senha123', 256), 'TARDE', 1),

-- Instrutor de Spinning
('Ricardo Oliveira', 'ricardo.oliveira', '1988-11-05', '111.222.333-03', '(11) 98888-1003', '2023-02-20', 3400.00, 'Instrutor', 'Rua Augusta, 300 - Consolação', 'ricardo.oliveira@techfit.com', SHA2('senha123', 256), 'MANHA', 2),

-- Instrutor de Zumba
('Fernanda Costa', 'fernanda.costa', '1992-04-18', '111.222.333-04', '(11) 98888-1004', '2022-08-12', 3000.00, 'Instrutor', 'Al. Santos, 400 - Jardim Paulista', 'fernanda.costa@techfit.com', SHA2('senha123', 256), 'TARDE', 3),

-- Instrutor de Pilates
('Marcos Lima', 'marcos.lima', '1983-09-30', '111.222.333-05', '(11) 98888-1005', '2021-11-25', 3800.00, 'Instrutor', 'Rua Oscar Freire, 500 - Pinheiros', 'marcos.lima@techfit.com', SHA2('senha123', 256), 'MANHA', 4),

-- Instrutor de CrossFit
('Bruno Almeida', 'bruno.almeida', '1986-12-08', '111.222.333-06', '(11) 98888-1006', '2023-01-15', 3600.00, 'Instrutor', 'Av. Paulista, 600 - Bela Vista', 'bruno.almeida@techfit.com', SHA2('senha123', 256), 'TARDE', 5),

-- Instrutor de Yoga
('Patricia Mendes', 'patricia.mendes', '1984-06-25', '111.222.333-07', '(11) 98888-1007', '2022-05-10', 3300.00, 'Instrutor', 'Rua Haddock Lobo, 700 - Jardins', 'patricia.mendes@techfit.com', SHA2('senha123', 256), 'MANHA', 6),

-- Instrutor de Natação
('Roberto Carlos', 'roberto.carlos', '1979-02-14', '111.222.333-08', '(11) 98888-1008', '2020-09-18', 4000.00, 'Instrutor', 'Al. Campinas, 800 - Jardim Paulista', 'roberto.carlos@techfit.com', SHA2('senha123', 256), 'TARDE', 7),

-- Instrutor de Boxe
('Lucas Souza', 'lucas.souza', '1991-08-03', '111.222.333-09', '(11) 98888-1009', '2022-10-30', 3200.00, 'Instrutor', 'Rua Bela Cintra, 900 - Consolação', 'lucas.souza@techfit.com', SHA2('senha123', 256), 'NOITE', 8),

-- Instrutor de Funcional
('Julia Ribeiro', 'julia.ribeiro', '1993-01-20', '111.222.333-10', '(11) 98888-1010', '2023-03-05', 3100.00, 'Instrutor', 'Av. Europa, 1000 - Jardim Europa', 'julia.ribeiro@techfit.com', SHA2('senha123', 256), 'TARDE', 9),

-- Instrutor de Alongamento
('Sandra Nunes', 'sandra.nunes', '1982-05-12', '111.222.333-11', '(11) 98888-1011', '2021-07-22', 2900.00, 'Instrutor', 'Rua Estados Unidos, 1100 - Jardim América', 'sandra.nunes@techfit.com', SHA2('senha123', 256), 'MANHA', 10);


-- ==============================================================
-- INSERIR AULAS COM NOMES PADRONIZADOS E 2 HORÁRIOS POR DIA
-- ==============================================================

-- SEGUNDA-FEIRA
INSERT INTO AULAS (NOME_AULA, ID_MODALIDADE, ID_INSTRUTOR, ID_UNIDADE, DIA_SEMANA, HORARIO_INICIO, DURACAO_MINUTOS, VAGAS) VALUES
-- Manhã
('Musculação', 1, 1, 1, 'SEG', '07:00:00', 60, 20),
('Spinning', 2, 3, 1, 'SEG', '08:00:00', 45, 15),
('Zumba', 3, 4, 1, 'SEG', '09:00:00', 50, 25),
('Pilates', 4, 5, 1, 'SEG', '10:00:00', 50, 10),
('CrossFit', 5, 6, 1, 'SEG', '11:00:00', 60, 12),
('Yoga', 6, 7, 1, 'SEG', '06:30:00', 60, 15),

-- Tarde/Noite
('Musculação', 1, 2, 1, 'SEG', '18:00:00', 60, 20),
('Spinning', 2, 3, 1, 'SEG', '19:00:00', 45, 15),
('Zumba', 3, 4, 1, 'SEG', '20:00:00', 50, 25),
('Pilates', 4, 5, 1, 'SEG', '17:00:00', 50, 10),
('CrossFit', 5, 6, 1, 'SEG', '19:30:00', 60, 12),
('Yoga', 6, 7, 1, 'SEG', '20:30:00', 60, 15);

-- TERÇA-FEIRA
INSERT INTO AULAS (NOME_AULA, ID_MODALIDADE, ID_INSTRUTOR, ID_UNIDADE, DIA_SEMANA, HORARIO_INICIO, DURACAO_MINUTOS, VAGAS) VALUES
-- Manhã
('Musculação', 1, 1, 1, 'TER', '07:00:00', 60, 20),
('Spinning', 2, 3, 1, 'TER', '08:00:00', 45, 15),
('Boxe', 8, 9, 1, 'TER', '09:00:00', 50, 12),
('Natação', 7, 8, 2, 'TER', '10:00:00', 45, 8),
('Treino Funcional', 9, 10, 1, 'TER', '11:00:00', 45, 15),

-- Tarde/Noite
('Musculação', 1, 2, 1, 'TER', '18:00:00', 60, 20),
('Spinning', 2, 3, 1, 'TER', '19:00:00', 45, 15),
('Boxe', 8, 9, 1, 'TER', '20:00:00', 50, 12),
('Natação', 7, 8, 2, 'TER', '19:00:00', 45, 8),
('Treino Funcional', 9, 10, 1, 'TER', '20:00:00', 45, 15);

-- QUARTA-FEIRA
INSERT INTO AULAS (NOME_AULA, ID_MODALIDADE, ID_INSTRUTOR, ID_UNIDADE, DIA_SEMANA, HORARIO_INICIO, DURACAO_MINUTOS, VAGAS) VALUES
-- Manhã
('Musculação', 1, 1, 1, 'QUA', '07:00:00', 60, 20),
('Zumba', 3, 4, 1, 'QUA', '08:00:00', 50, 25),
('CrossFit', 5, 6, 1, 'QUA', '09:00:00', 60, 12),
('Yoga', 6, 7, 1, 'QUA', '10:00:00', 60, 15),
('Alongamento', 10, 11, 1, 'QUA', '11:00:00', 30, 20),

-- Tarde/Noite
('Musculação', 1, 2, 1, 'QUA', '18:00:00', 60, 20),
('Zumba', 3, 4, 1, 'QUA', '19:00:00', 50, 25),
('CrossFit', 5, 6, 1, 'QUA', '20:00:00', 60, 12),
('Yoga', 6, 7, 1, 'QUA', '20:30:00', 60, 15),
('Alongamento', 10, 11, 1, 'QUA', '21:00:00', 30, 20);

-- QUINTA-FEIRA
INSERT INTO AULAS (NOME_AULA, ID_MODALIDADE, ID_INSTRUTOR, ID_UNIDADE, DIA_SEMANA, HORARIO_INICIO, DURACAO_MINUTOS, VAGAS) VALUES
-- Manhã
('Musculação', 1, 1, 1, 'QUI', '07:00:00', 60, 20),
('Pilates', 4, 5, 1, 'QUI', '08:00:00', 50, 10),
('Boxe', 8, 9, 1, 'QUI', '09:00:00', 50, 12),
('Treino Funcional', 9, 10, 1, 'QUI', '10:00:00', 45, 15),

-- Tarde/Noite
('Musculação', 1, 2, 1, 'QUI', '18:00:00', 60, 20),
('Pilates', 4, 5, 1, 'QUI', '19:00:00', 50, 10),
('Boxe', 8, 9, 1, 'QUI', '20:00:00', 50, 12),
('Treino Funcional', 9, 10, 1, 'QUI', '20:00:00', 45, 15);

-- SEXTA-FEIRA
INSERT INTO AULAS (NOME_AULA, ID_MODALIDADE, ID_INSTRUTOR, ID_UNIDADE, DIA_SEMANA, HORARIO_INICIO, DURACAO_MINUTOS, VAGAS) VALUES
-- Manhã
('Musculação', 1, 1, 1, 'SEX', '07:00:00', 60, 20),
('Spinning', 2, 3, 1, 'SEX', '08:00:00', 45, 15),
('Zumba', 3, 4, 1, 'SEX', '09:00:00', 50, 25),
('CrossFit', 5, 6, 1, 'SEX', '10:00:00', 60, 12),

-- Tarde/Noite
('Musculação', 1, 2, 1, 'SEX', '18:00:00', 60, 20),
('Spinning', 2, 3, 1, 'SEX', '19:00:00', 45, 15),
('Zumba', 3, 4, 1, 'SEX', '20:00:00', 50, 25),
('CrossFit', 5, 6, 1, 'SEX', '19:30:00', 60, 12);

-- SÁBADO
INSERT INTO AULAS (NOME_AULA, ID_MODALIDADE, ID_INSTRUTOR, ID_UNIDADE, DIA_SEMANA, HORARIO_INICIO, DURACAO_MINUTOS, VAGAS) VALUES
-- Manhã
('Musculação', 1, 1, 1, 'SAB', '08:00:00', 60, 20),
('Spinning', 2, 3, 1, 'SAB', '09:00:00', 60, 20),
('Zumba', 3, 4, 1, 'SAB', '10:00:00', 60, 30),
('Yoga', 6, 7, 1, 'SAB', '11:00:00', 60, 15),

-- Tarde (apenas algumas modalidades)
('Musculação', 1, 2, 1, 'SAB', '14:00:00', 60, 15),
('CrossFit', 5, 6, 1, 'SAB', '15:00:00', 75, 15);

-- DOMINGO
INSERT INTO AULAS (NOME_AULA, ID_MODALIDADE, ID_INSTRUTOR, ID_UNIDADE, DIA_SEMANA, HORARIO_INICIO, DURACAO_MINUTOS, VAGAS) VALUES
-- Manhã
('Musculação', 1, 1, 1, 'DOM', '09:00:00', 60, 15),
('Yoga', 6, 7, 1, 'DOM', '10:00:00', 75, 15),
('Spinning', 2, 3, 1, 'DOM', '11:00:00', 45, 15);

ALTER TABLE PAGAMENTOS MODIFY COLUMN TIPO_PAGAMENTO ENUM('DINHEIRO','CREDITO','DEBITO','PIX','TROCA_PLANO') NOT NULL;


