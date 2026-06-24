SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================================
-- 1. ESTRUTURA DA BASE DE DADOS (DDL) - REMOÇÃO DE TABELAS EXISTENTES
-- ============================================================================
DROP TABLE IF EXISTS `documentos`;
DROP TABLE IF EXISTS `consumiveis`;
DROP TABLE IF EXISTS `componentes`;
DROP TABLE IF EXISTS `equipamento_fornecedor`;
DROP TABLE IF EXISTS `contratos`;
DROP TABLE IF EXISTS `garantias`;
DROP TABLE IF EXISTS `entradas_equipamento`;
DROP TABLE IF EXISTS `equipamentos`;
DROP TABLE IF EXISTS `fornecedores`;
DROP TABLE IF EXISTS `localizacoes`;
DROP TABLE IF EXISTS `conteudos_publicos`;
DROP TABLE IF EXISTS `secoes_publicas`;
DROP TABLE IF EXISTS `mensagens_contacto`;
DROP TABLE IF EXISTS `historico`;
DROP TABLE IF EXISTS `lembretes`;
DROP TABLE IF EXISTS `password_resets`;
DROP TABLE IF EXISTS `utilizadores`;
DROP TABLE IF EXISTS `servicos`;
DROP TABLE IF EXISTS `categorias`;
DROP TABLE IF EXISTS `estados_equipamento`;
DROP TABLE IF EXISTS `criticidades`;
DROP TABLE IF EXISTS `tipos_entrada`;
DROP TABLE IF EXISTS `tipos_fornecedor`;
DROP TABLE IF EXISTS `tipos_relacao_fornecedor`;
DROP TABLE IF EXISTS `estados_componentes`;
DROP TABLE IF EXISTS `tipos_documento`;
DROP TABLE IF EXISTS `estados_garantia`;

-- ============================================================================
-- 2. ESTRUTURA DA BASE DE DADOS (DDL) - CRIAÇÃO DE TABELAS
-- ============================================================================

CREATE TABLE `categorias` (
  `id_categoria` int NOT NULL AUTO_INCREMENT,
  `nome_categoria` varchar(80) NOT NULL,
  `descricao` text,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_categoria`),
  UNIQUE KEY `nome_categoria` (`nome_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `estados_componentes` (
  `id_estado_componente` int NOT NULL AUTO_INCREMENT,
  `estado` varchar(50) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_estado_componente`),
  UNIQUE KEY `estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `estados_equipamento` (
  `id_estado` int NOT NULL AUTO_INCREMENT,
  `nome_estado` varchar(50) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_estado`),
  UNIQUE KEY `nome_estado` (`nome_estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `estados_garantia` (
  `id_estado_garantia` int NOT NULL AUTO_INCREMENT,
  `estado` varchar(50) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_estado_garantia`),
  UNIQUE KEY `estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `criticidades` (
  `id_criticidade` int NOT NULL AUTO_INCREMENT,
  `nivel` varchar(50) NOT NULL,
  `descricao` text,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_criticidade`),
  UNIQUE KEY `nivel` (`nivel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tipos_entrada` (
  `id_tipo_entrada` int NOT NULL AUTO_INCREMENT,
  `tipo` varchar(50) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_tipo_entrada`),
  UNIQUE KEY `tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tipos_documento` (
  `id_tipo_documento` int NOT NULL AUTO_INCREMENT,
  `tipo` varchar(100) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_tipo_documento`),
  UNIQUE KEY `tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tipos_fornecedor` (
  `id_tipo_fornecedor` int NOT NULL AUTO_INCREMENT,
  `tipo` varchar(80) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_tipo_fornecedor`),
  UNIQUE KEY `tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tipos_relacao_fornecedor` (
  `id_tipo_relacao` int NOT NULL AUTO_INCREMENT,
  `tipo` varchar(80) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_tipo_relacao`),
  UNIQUE KEY `tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `servicos` (
  `id_servico` int NOT NULL AUTO_INCREMENT,
  `nome_servico` varchar(100) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_servico`),
  UNIQUE KEY `nome_servico` (`nome_servico`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `secoes_publicas` (
  `id_seccao` int NOT NULL AUTO_INCREMENT,
  `nome_seccao` varchar(100) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_seccao`),
  UNIQUE KEY `nome_seccao` (`nome_seccao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `mensagens_contacto` (
  `id_mensagem` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `assunto` varchar(150) DEFAULT NULL,
  `mensagem` text NOT NULL,
  `data_envio` datetime NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_mensagem`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `utilizadores` (
  `id_utilizador` int NOT NULL AUTO_INCREMENT,
  `email` varbinary(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `perfil` varchar(30) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `ultimo_acesso` datetime DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_utilizador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `lembretes` (
  `id_lembrete` int NOT NULL AUTO_INCREMENT,
  `id_utilizador` int NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `concluido` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id_lembrete`),
  KEY `id_utilizador` (`id_utilizador`),
  CONSTRAINT `lembretes_ibfk_1` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizadores` (`id_utilizador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `historico` (
  `id_historico` int NOT NULL AUTO_INCREMENT,
  `id_utilizador` int NOT NULL,
  `modulo` varchar(80) NOT NULL,
  `acao` varchar(80) NOT NULL,
  `registo` varchar(150) DEFAULT NULL,
  `detalhes` text,
  `data_hora` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_historico`),
  KEY `id_utilizador` (`id_utilizador`),
  CONSTRAINT `historico_ibfk_1` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizadores` (`id_utilizador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `password_resets` (
  `id_password_reset` int NOT NULL AUTO_INCREMENT,
  `id_utilizador` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `expira_em` datetime NOT NULL,
  `usado` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id_password_reset`),
  UNIQUE KEY `token` (`token`),
  KEY `id_utilizador` (`id_utilizador`),
  CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizadores` (`id_utilizador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `conteudos_publicos` (
  `id_conteudo` int NOT NULL AUTO_INCREMENT,
  `id_seccao` int NOT NULL,
  `campo` varchar(100) NOT NULL,
  `valor` text NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_conteudo`),
  KEY `id_seccao` (`id_seccao`),
  CONSTRAINT `conteudos_publicos_ibfk_1` FOREIGN KEY (`id_seccao`) REFERENCES `secoes_publicas` (`id_seccao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `localizacoes` (
  `id_localizacao` int NOT NULL AUTO_INCREMENT,
  `edificio` varchar(100) NOT NULL,
  `piso` varchar(50) NOT NULL,
  `sala` varchar(50) NOT NULL,
  `id_servico` int NOT NULL,
  `responsavel` varchar(100) DEFAULT NULL,
  `contacto` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_localizacao`),
  UNIQUE KEY `uq_localizacao` (`edificio`,`piso`,`sala`),
  KEY `id_servico` (`id_servico`),
  CONSTRAINT `localizacoes_ibfk_1` FOREIGN KEY (`id_servico`) REFERENCES `servicos` (`id_servico`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `fornecedores` (
  `id_fornecedor` int NOT NULL AUTO_INCREMENT,
  `nome_empresa` varchar(150) NOT NULL,
  `nif` varchar(9) NOT NULL,
  `id_tipo_fornecedor` int DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(150) DEFAULT NULL,
  `pessoa_contacto` varchar(100) DEFAULT NULL,
  `telefone_contacto` varchar(20) DEFAULT NULL,
  `email_contacto` varchar(100) DEFAULT NULL,
  `morada` varchar(200) DEFAULT NULL,
  `codigo_postal` varchar(20) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `pais` varchar(100) DEFAULT NULL,
  `observacoes` text,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_fornecedor`),
  UNIQUE KEY `nif` (`nif`),
  KEY `id_tipo_fornecedor` (`id_tipo_fornecedor`),
  CONSTRAINT `fornecedores_ibfk_1` FOREIGN KEY (`id_tipo_fornecedor`) REFERENCES `tipos_fornecedor` (`id_tipo_fornecedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `equipamentos` (
  `id_equipamento` int NOT NULL AUTO_INCREMENT,
  `codigo_interno` varchar(20) NOT NULL,
  `designacao` varchar(150) NOT NULL,
  `id_categoria` int NOT NULL,
  `marca` varchar(80) NOT NULL,
  `modelo` varchar(80) NOT NULL,
  `fornecedor_original` varchar(150) DEFAULT NULL,
  `numero_serie` varchar(100) NOT NULL,
  `ano_fabrico` int DEFAULT NULL,
  `id_estado` int NOT NULL,
  `id_criticidade` int NOT NULL,
  `id_localizacao` int NOT NULL,
  `observacoes` text,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_equipamento`),
  UNIQUE KEY `codigo_interno` (`codigo_interno`),
  UNIQUE KEY `numero_serie` (`numero_serie`),
  KEY `id_categoria` (`id_categoria`),
  KEY `id_estado` (`id_estado`),
  KEY `id_criticidade` (`id_criticidade`),
  KEY `id_localizacao` (`id_localizacao`),
  CONSTRAINT `equipamentos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`),
  CONSTRAINT `equipamentos_ibfk_2` FOREIGN KEY (`id_estado`) REFERENCES `estados_equipamento` (`id_estado`),
  CONSTRAINT `equipamentos_ibfk_3` FOREIGN KEY (`id_criticidade`) REFERENCES `criticidades` (`id_criticidade`),
  CONSTRAINT `equipamentos_ibfk_4` FOREIGN KEY (`id_localizacao`) REFERENCES `localizacoes` (`id_localizacao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `componentes` (
  `id_componente` int NOT NULL AUTO_INCREMENT,
  `id_equipamento` int NOT NULL,
  `codigo_componente` varchar(30) NOT NULL,
  `nome_componente` varchar(150) NOT NULL,
  `id_estado_componente` int NOT NULL,
  `notificacao` text,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_componente`),
  UNIQUE KEY `codigo_componente` (`codigo_componente`),
  KEY `id_equipamento` (`id_equipamento`),
  KEY `id_estado_componente` (`id_estado_componente`),
  CONSTRAINT `componentes_ibfk_1` FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id_equipamento`),
  CONSTRAINT `componentes_ibfk_2` FOREIGN KEY (`id_estado_componente`) REFERENCES `estados_componentes` (`id_estado_componente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `consumiveis` (
  `id_consumivel` int NOT NULL AUTO_INCREMENT,
  `id_equipamento` int NOT NULL,
  `nome_consumivel` varchar(150) NOT NULL,
  `stock_atual` int NOT NULL,
  `stock_minimo` int NOT NULL,
  `ultima_atualizacao` date DEFAULT NULL,
  `observacoes` text,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_consumivel`),
  KEY `id_equipamento` (`id_equipamento`),
  CONSTRAINT `consumiveis_ibfk_1` FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id_equipamento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `contratos` (
  `id_contrato` int NOT NULL AUTO_INCREMENT,
  `id_equipamento` int NOT NULL,
  `id_fornecedor` int NOT NULL,
  `nome_contrato` varchar(150) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `valor_anual` decimal(10,2) DEFAULT NULL,
  `observacoes` text,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_contrato`),
  KEY `id_equipamento` (`id_equipamento`),
  KEY `id_fornecedor` (`id_fornecedor`),
  CONSTRAINT `contratos_ibfk_1` FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id_equipamento`),
  CONSTRAINT `contratos_ibfk_2` FOREIGN KEY (`id_fornecedor`) REFERENCES `fornecedores` (`id_fornecedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `garantias` (
  `id_garantia` int NOT NULL AUTO_INCREMENT,
  `id_equipamento` int NOT NULL,
  `nome_garantia` varchar(150) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `id_estado_garantia` int NOT NULL,
  `observacoes` text,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_garantia`),
  KEY `id_equipamento` (`id_equipamento`),
  KEY `id_estado_garantia` (`id_estado_garantia`),
  CONSTRAINT `garantias_ibfk_1` FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id_equipamento`),
  CONSTRAINT `garantias_ibfk_2` FOREIGN KEY (`id_estado_garantia`) REFERENCES `estados_garantia` (`id_estado_garantia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `entradas_equipamento` (
  `id_entrada` int NOT NULL AUTO_INCREMENT,
  `id_equipamento` int NOT NULL,
  `id_tipo_entrada` int NOT NULL,
  `data_entrada` date DEFAULT NULL,
  `entidade_associada` varchar(150) DEFAULT NULL,
  `custo_aquisicao` decimal(10,2) DEFAULT NULL,
  `numero_fatura` varchar(80) DEFAULT NULL,
  `metodo_pagamento` varchar(80) DEFAULT NULL,
  `valor_mensal` decimal(10,2) DEFAULT NULL,
  `data_fim_aluguer` date DEFAULT NULL,
  `condicoes_aluguer` text,
  `entidade_doadora` varchar(150) DEFAULT NULL,
  `valor_estimado` decimal(10,2) DEFAULT NULL,
  `condicoes_doacao` text,
  `entidade_proprietaria` varchar(150) DEFAULT NULL,
  `data_inicio_emprestimo` date DEFAULT NULL,
  `data_prevista_devolucao` date DEFAULT NULL,
  `condicoes_emprestimo` text,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_entrada`),
  KEY `id_equipamento` (`id_equipamento`),
  KEY `id_tipo_entrada` (`id_tipo_entrada`),
  CONSTRAINT `entradas_equipamento_ibfk_1` FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id_equipamento`),
  CONSTRAINT `entradas_equipamento_ibfk_2` FOREIGN KEY (`id_tipo_entrada`) REFERENCES `tipos_entrada` (`id_tipo_entrada`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `equipamento_fornecedor` (
  `id_equipamento` int NOT NULL,
  `id_fornecedor` int NOT NULL,
  `id_tipo_relacao` int NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_equipamento`,`id_fornecedor`,`id_tipo_relacao`),
  KEY `id_fornecedor` (`id_fornecedor`),
  KEY `id_tipo_relacao` (`id_tipo_relacao`),
  CONSTRAINT `equipamento_fornecedor_ibfk_1` FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id_equipamento`),
  CONSTRAINT `equipamento_fornecedor_ibfk_2` FOREIGN KEY (`id_fornecedor`) REFERENCES `fornecedores` (`id_fornecedor`),
  CONSTRAINT `equipamento_fornecedor_ibfk_3` FOREIGN KEY (`id_tipo_relacao`) REFERENCES `tipos_relacao_fornecedor` (`id_tipo_relacao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `documentos` (
  `id_documento` int NOT NULL AUTO_INCREMENT,
  `nome_documento` varchar(150) NOT NULL,
  `id_tipo_documento` int NOT NULL,
  `nome_ficheiro` varchar(200) NOT NULL,
  `caminho_ficheiro` varchar(255) NOT NULL,
  `data_documento` date DEFAULT NULL,
  `data_validade` date DEFAULT NULL,
  `tamanho_ficheiro` varchar(30) DEFAULT NULL,
  `formato_ficheiro` varchar(20) DEFAULT NULL,
  `id_equipamento` int DEFAULT NULL,
  `id_entrada` int DEFAULT NULL,
  `id_fornecedor` int DEFAULT NULL,
  `id_localizacao` int DEFAULT NULL,
  `id_componente` int DEFAULT NULL,
  `id_garantia` int DEFAULT NULL,
  `id_contrato` int DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_documento`),
  KEY `id_tipo_documento` (`id_tipo_documento`),
  KEY `id_equipamento` (`id_equipamento`),
  KEY `id_entrada` (`id_entrada`),
  KEY `id_fornecedor` (`id_fornecedor`),
  KEY `id_localizacao` (`id_localizacao`),
  KEY `id_componente` (`id_componente`),
  KEY `id_garantia` (`id_garantia`),
  KEY `id_contrato` (`id_contrato`),
  CONSTRAINT `documentos_ibfk_1` FOREIGN KEY (`id_tipo_documento`) REFERENCES `tipos_documento` (`id_tipo_documento`),
  CONSTRAINT `documentos_ibfk_2` FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id_equipamento`),
  CONSTRAINT `documentos_ibfk_3` FOREIGN KEY (`id_entrada`) REFERENCES `entradas_equipamento` (`id_entrada`),
  CONSTRAINT `documentos_ibfk_4` FOREIGN KEY (`id_fornecedor`) REFERENCES `fornecedores` (`id_fornecedor`),
  CONSTRAINT `documentos_ibfk_5` FOREIGN KEY (`id_localizacao`) REFERENCES `localizacoes` (`id_localizacao`),
  CONSTRAINT `documentos_ibfk_6` FOREIGN KEY (`id_componente`) REFERENCES `componentes` (`id_componente`),
  CONSTRAINT `documentos_ibfk_7` FOREIGN KEY (`id_garantia`) REFERENCES `garantias` (`id_garantia`),
  CONSTRAINT `documentos_ibfk_8` FOREIGN KEY (`id_contrato`) REFERENCES `contratos` (`id_contrato`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Reativar temporariamente para os INSERTS correrem de forma segura
SET FOREIGN_KEY_CHECKS = 1;


-- ============================================================================
-- 3. CARGA DE DADOS (DML) - POPOULAR AS TABELAS
-- ============================================================================
INSERT INTO utilizadores VALUES
(1, AES_ENCRYPT('admin@medisync.pt', 'medisync_chave_teste_2026'), '$2y$10$GH.E37RRLIekHHjMVS71x.Tw46dm4RS4cjiFIVv7EqpPpmJXx5qoG', 'Administrador', NULL, NOW(), 1, NOW(), NOW()),
(2, AES_ENCRYPT('tecnico@medisync.pt', 'medisync_chave_teste_2026'), '$2y$10$jMHPPG4YGIUZCYXajMYTUOy5XH9eVKUvJN0meNDRHogUcV4nSCK1a', 'Tecnico', NULL, NOW(), 1, NOW(), NOW()),
(3, AES_ENCRYPT('saude@medisync.pt', 'medisync_chave_teste_2026'), '$2y$10$cDgtSZ56OnbcDgSuoFHuy.uKNSNqA/eWubOCmhlxuGktFy4EmP2zu', 'Profissional de Saúde', NULL, NOW(), 1, NOW(), NOW());

INSERT INTO servicos VALUES
(1,'Urgência',1,NOW(),NOW()),
(2,'UCI',1,NOW(),NOW()),
(3,'Bloco Operatório',1,NOW(),NOW()),
(4,'Consultas',1,NOW(),NOW()),
(5,'Laboratório',1,NOW(),NOW()),
(6,'Radiologia',1,NOW(),NOW()),
(7,'Reabilitação',1,NOW(),NOW()),
(8,'Armazém',1,NOW(),NOW());

INSERT INTO categorias VALUES
(1,'Monitorização','Equipamentos de monitorização de sinais vitais.',1,NOW(),NOW()),
(2,'Suporte de Vida','Equipamentos essenciais ao suporte de vida.',1,NOW(),NOW()),
(3,'Diagnóstico','Equipamentos usados em diagnóstico clínico.',1,NOW(),NOW()),
(4,'Imagiologia','Equipamentos de imagem médica.',1,NOW(),NOW()),
(5,'Laboratório','Equipamentos de análises laboratoriais.',1,NOW(),NOW()),
(6,'Terapia','Equipamentos usados em tratamentos.',1,NOW(),NOW()),
(7,'Cirurgia','Equipamentos utilizados em contexto cirúrgico.',1,NOW(),NOW()),
(8,'Reabilitação','Equipamentos de fisioterapia e recuperação.',1,NOW(),NOW()),
(9,'Esterilização','Equipamentos para esterilização hospitalar.',1,NOW(),NOW());

INSERT INTO estados_equipamento VALUES
(1,'Ativo',1,NOW(),NOW()),
(2,'Em manutenção',1,NOW(),NOW()),
(3,'Em calibração',1,NOW(),NOW()),
(4,'Em quarentena',1,NOW(),NOW()),
(5,'Inativo',1,NOW(),NOW()),
(6,'Abatido',1,NOW(),NOW());

INSERT INTO criticidades VALUES
(1,'Baixa','Equipamento não crítico.',1,NOW(),NOW()),
(2,'Média','Equipamento importante para o funcionamento do serviço.',1,NOW(),NOW()),
(3,'Alta','Equipamento essencial para diagnóstico ou tratamento.',1,NOW(),NOW()),
(4,'Suporte de vida','Equipamento diretamente ligado à manutenção da vida do paciente.',1,NOW(),NOW());

INSERT INTO tipos_entrada VALUES
(1,'Compra',1,NOW(),NOW()),
(2,'Aluguer',1,NOW(),NOW()),
(3,'Doação',1,NOW(),NOW()),
(4,'Empréstimo',1,NOW(),NOW());

INSERT INTO tipos_fornecedor VALUES
(1,'Fabricante',1,NOW(),NOW()),
(2,'Distribuidor',1,NOW(),NOW()),
(3,'Assistência técnica',1,NOW(),NOW()),
(4,'Consumíveis ou acessórios',1,NOW(),NOW());

INSERT INTO tipos_relacao_fornecedor VALUES
(1,'Fabricante',1,NOW(),NOW()),
(2,'Fornecedor',1,NOW(),NOW()),
(3,'Assistência técnica',1,NOW(),NOW()),
(4,'Manutenção',1,NOW(),NOW()),
(5,'Consumíveis',1,NOW(),NOW());

INSERT INTO estados_componentes VALUES
(1,'Funcional',1,NOW(),NOW()),
(2,'Em manutenção',1,NOW(),NOW()),
(3,'Avariado',1,NOW(),NOW()),
(4,'Substituído',1,NOW(),NOW()),
(5,'Abatido',1,NOW(),NOW());

INSERT INTO estados_garantia VALUES
(1,'Ativa',1,NOW(),NOW()),
(2,'Expirada',1,NOW(),NOW());

INSERT INTO tipos_documento VALUES
(1,'Manual',1,NOW(),NOW()),
(2,'Manual de utilizador',1,NOW(),NOW()),
(3,'Manual de componente',1,NOW(),NOW()),
(4,'Manual de manutenção',1,NOW(),NOW()),
(5,'Ficha Técnica',1,NOW(),NOW()),
(6,'Certificação',1,NOW(),NOW()),
(7,'Relatório',1,NOW(),NOW()),
(8,'Relatório de uso',1,NOW(),NOW()),
(9,'Relatório de teste',1,NOW(),NOW()),
(10,'Registo de substituição',1,NOW(),NOW()),
(11,'Fatura',1,NOW(),NOW()),
(12,'Comprovativo de pagamento',1,NOW(),NOW()),
(13,'Contrato de aluguer',1,NOW(),NOW()),
(14,'Termo de doação',1,NOW(),NOW()),
(15,'Termo de empréstimo',1,NOW(),NOW()),
(16,'Guia de transporte',1,NOW(),NOW()),
(17,'Auto de receção',1,NOW(),NOW()),
(18,'Planta',1,NOW(),NOW()),
(19,'Instalação',1,NOW(),NOW()),
(20,'Garantia',1,NOW(),NOW()),
(21,'Contrato',1,NOW(),NOW()),
(22,'Declaração',1,NOW(),NOW()),
(23,'Outro',1,NOW(),NOW());

INSERT INTO localizacoes VALUES
(1,'Edifício Principal','Piso 0','Urgência 01',1,'Enf. Carla Martins','210000001','urgencia@hospital.pt',1,NOW(),NOW()),
(2,'Edifício Principal','Piso 1','UCI 01',2,'Dr. Miguel Santos','210000002','uci@hospital.pt',1,NOW(),NOW()),
(3,'Edifício Cirúrgico','Piso 2','Bloco A',3,'Enf. Ana Ribeiro','210000003','bloco@hospital.pt',1,NOW(),NOW()),
(4,'Edifício Principal','Piso 0','Consultas 03',4,'Dr. João Costa','210000004','consultas@hospital.pt',1,NOW(),NOW()),
(5,'Edifício Técnico','Piso 1','Laboratório Central',5,'Dra. Sofia Neves','210000005','lab@hospital.pt',1,NOW(),NOW()),
(6,'Edifício Imagiologia','Piso 0','Sala RX',6,'Téc. Pedro Almeida','210000006','radiologia@hospital.pt',1,NOW(),NOW()),
(7,'Edifício Reabilitação','Piso 0','Ginásio Clínico',7,'Ft. Marta Lopes','210000007','reabilitacao@hospital.pt',1,NOW(),NOW()),
(8,'Edifício Técnico','Piso -1','Armazém Biomédico',8,'Carlos Ferreira','210000008','armazem@hospital.pt',1,NOW(),NOW());

INSERT INTO fornecedores VALUES
(1,'Medtronic Portugal','501000001',1,'211111111','geral@medtronic.pt','www.medtronic.pt','Rui Marques','911111111','rui.marques@medtronic.pt','Av. da Saúde 10','1000-001','Lisboa','Portugal','Fabricante de equipamentos médicos.',1,NOW(),NOW()),
(2,'Philips Healthcare','501000002',1,'222222222','geral@philips.pt','www.philips.pt','Inês Silva','922222222','ines.silva@philips.pt','Rua Clínica 20','4000-002','Porto','Portugal','Fabricante e assistência técnica.',1,NOW(),NOW()),
(3,'Siemens Healthineers','501000003',1,'233333333','geral@siemens-healthineers.pt','www.siemens-healthineers.pt','Paulo Sousa','933333333','paulo.sousa@siemens.pt','Rua da Imagiologia 30','3000-003','Coimbra','Portugal','Fornecedor de imagiologia.',1,NOW(),NOW()),
(4,'B. Braun Medical','501000004',2,'244444444','geral@bbraun.pt','www.bbraun.pt','Helena Costa','944444444','helena.costa@bbraun.pt','Rua Hospitalar 40','2700-004','Amadora','Portugal','Distribuidor de bombas e consumíveis.',1,NOW(),NOW()),
(5,'Getinge Portugal','501000005',3,'255555555','assistencia@getinge.pt','www.getinge.pt','Nuno Reis','955555555','nuno.reis@getinge.pt','Zona Industrial 5','4470-005','Maia','Portugal','Assistência técnica e manutenção.',1,NOW(),NOW()),
(6,'Fresenius Medical Care','501000006',1,'266666666','geral@fresenius.pt','www.fresenius.pt','Marta Gomes','966666666','marta.gomes@fresenius.pt','Av. Renal 60','1500-006','Lisboa','Portugal','Equipamentos de terapias renais.',1,NOW(),NOW()),
(7,'OrtoRehab Lda','501000007',2,'277777777','geral@ortorehab.pt','www.ortorehab.pt','Tiago Rocha','977777777','tiago.rocha@ortorehab.pt','Rua da Reabilitação 70','4700-007','Braga','Portugal','Equipamentos de reabilitação.',1,NOW(),NOW()),
(8,'BioConsumíveis SA','501000008',4,'288888888','vendas@bioconsumiveis.pt','www.bioconsumiveis.pt','Sara Dias','988888888','sara.dias@bioconsumiveis.pt','Rua dos Consumíveis 80','2900-008','Setúbal','Portugal','Fornecedor de acessórios e consumíveis.',1,NOW(),NOW());

INSERT INTO equipamentos VALUES
(1,'EQ-0001','Monitor multiparamétrico',1,'Philips','IntelliVue MX450','Philips','SN-MON-001',2021,1,3,2,'Monitorização contínua na UCI.',1,NOW(),NOW()),
(2,'EQ-0002','Ventilador pulmonar',2,'Medtronic','Puritan Bennett 980','Medtronic','SN-VENT-002',2020,1,4,2,'Equipamento de suporte ventilatório.',1,NOW(),NOW()),
(3,'EQ-0003','Desfibrilhador',2,'Philips','HeartStart XL+','Philips','SN-DESF-003',2022,1,4,1,'Disponível na urgência.',1,NOW(),NOW()),
(4,'EQ-0004','Eletrocardiógrafo',3,'GE Healthcare','MAC 2000','GE Healthcare','SN-ECG-004',2021,1,3,4,'Usado em consultas de cardiologia.',1,NOW(),NOW()),
(5,'EQ-0005','Ecógrafo portátil',4,'Siemens','ACUSON P500','Siemens','SN-ECO-005',2020,3,3,6,'A aguardar calibração anual.',1,NOW(),NOW()),
(6,'EQ-0006','Aparelho de RX digital',4,'Siemens','Multix Impact','Siemens','SN-RX-006',2019,1,3,6,'Sistema fixo de radiologia.',1,NOW(),NOW()),
(7,'EQ-0007','Bomba de infusão',6,'B. Braun','Infusomat Space','B. Braun','SN-INF-007',2022,1,3,2,'Bomba usada em terapêutica intravenosa.',1,NOW(),NOW()),
(8,'EQ-0008','Bomba seringa',6,'B. Braun','Perfusor Space','B. Braun','SN-SER-008',2022,1,3,2,'Utilizada na UCI.',1,NOW(),NOW()),
(9,'EQ-0009','Autoclave',9,'Getinge','HS 6610','Getinge','SN-AUTO-009',2018,2,3,3,'Em manutenção preventiva.',1,NOW(),NOW()),
(10,'EQ-0010','Centrífuga laboratorial',5,'Eppendorf','5804 R','Eppendorf','SN-CENT-010',2021,1,2,5,'Centrífuga refrigerada.',1,NOW(),NOW()),
(11,'EQ-0011','Analisador hematológico',5,'Sysmex','XN-550','Sysmex','SN-HEMA-011',2020,1,3,5,'Equipamento central do laboratório.',1,NOW(),NOW()),
(12,'EQ-0012','Microscópio ótico',5,'Olympus','CX43','Olympus','SN-MIC-012',2019,1,1,5,'Sem componentes registados.',1,NOW(),NOW()),
(13,'EQ-0013','Marquesa elétrica',8,'OrtoRehab','ME-300','OrtoRehab','SN-MAR-013',2023,1,1,7,'Usada em fisioterapia.',1,NOW(),NOW()),
(14,'EQ-0014','Laser terapêutico',6,'BTL','BTL-6000 Laser','BTL','SN-LAS-014',2021,1,2,7,'Tratamentos de reabilitação.',1,NOW(),NOW()),
(15,'EQ-0015','Monitor fetal',1,'Philips','Avalon FM30','Philips','SN-FET-015',2020,1,3,4,'Monitorização obstétrica.',1,NOW(),NOW()),
(16,'EQ-0016','Incubadora neonatal',2,'Dräger','Isolette 8000','Dräger','SN-INC-016',2018,1,4,2,'Equipamento crítico neonatal.',1,NOW(),NOW()),
(17,'EQ-0017','Aspirador cirúrgico',7,'Medela','Dominant Flex','Medela','SN-ASP-017',2021,1,3,3,'Usado no bloco operatório.',1,NOW(),NOW()),
(18,'EQ-0018','Mesa cirúrgica',7,'Maquet','Alphamaxx','Maquet','SN-MESA-018',2017,1,3,3,'Mesa principal do bloco A.',1,NOW(),NOW()),
(19,'EQ-0019','Lâmpada cirúrgica',7,'Getinge','Volista Access','Getinge','SN-LAMP-019',2019,1,3,3,'Iluminação cirúrgica.',1,NOW(),NOW()),
(20,'EQ-0020','Máquina de hemodiálise',6,'Fresenius','5008S','Fresenius','SN-HEMO-020',2020,1,4,2,'Terapia renal substitutiva.',1,NOW(),NOW()),
(21,'EQ-0021','Oxímetro de pulso',1,'Nonin','7500','Nonin','SN-OXI-021',2023,1,2,1,'Sem componentes registados.',1,NOW(),NOW()),
(22,'EQ-0022','Nebulizador clínico',6,'Omron','NE-C900','Omron','SN-NEB-022',2022,1,1,1,'Equipamento de baixa criticidade.',1,NOW(),NOW()),
(23,'EQ-0023','Termómetro infravermelho',3,'Braun','ThermoScan PRO 6000','Braun','SN-TERM-023',2023,1,1,1,'Sem componentes registados.',1,NOW(),NOW()),
(24,'EQ-0024','Balança médica',3,'Seca','704','Seca','SN-BAL-024',2022,1,1,4,'Usada em consultas.',1,NOW(),NOW()),
(25,'EQ-0025','TAC',4,'Siemens','SOMATOM go.Up','Siemens','SN-TAC-025',2019,1,4,6,'Equipamento de imagiologia avançada.',1,NOW(),NOW()),
(26,'EQ-0026','Ressonância magnética',4,'Philips','Ingenia 1.5T','Philips','SN-RM-026',2018,1,4,6,'Equipamento crítico de diagnóstico.',1,NOW(),NOW()),
(27,'EQ-0027','Frigorífico laboratorial',5,'Liebherr','LKUv 1610','Liebherr','SN-FRIO-027',2021,1,2,5,'Conservação de amostras.',1,NOW(),NOW()),
(28,'EQ-0028','Esterilizador plasma',9,'Steris','V-PRO maX','Steris','SN-EST-028',2020,1,3,3,'Esterilização de material sensível.',1,NOW(),NOW()),
(29,'EQ-0029','Cadeira de rodas clínica',8,'OrtoRehab','CR-Plus','OrtoRehab','SN-CR-029',2023,1,1,7,'Sem componentes registados.',1,NOW(),NOW()),
(30,'EQ-0030','Ventilador de transporte',2,'Dräger','Oxylog 3000 Plus','Dräger','SN-VTRANS-030',2021,1,4,1,'Usado em emergência e transporte.',1,NOW(),NOW());

INSERT INTO entradas_equipamento
(id_entrada,id_equipamento,id_tipo_entrada,data_entrada,entidade_associada,custo_aquisicao,numero_fatura,metodo_pagamento,valor_mensal,data_fim_aluguer,condicoes_aluguer,entidade_doadora,valor_estimado,condicoes_doacao,entidade_proprietaria,data_inicio_emprestimo,data_prevista_devolucao,condicoes_emprestimo,ativo,created_at,updated_at)
VALUES
(1,1,1,'2021-03-10','Philips Healthcare',8500,'FT-2021-001','Transferência',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(2,2,1,'2020-06-15','Medtronic Portugal',18500,'FT-2020-014','Transferência',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(3,3,1,'2022-02-20','Philips Healthcare',6200,'FT-2022-033','Transferência',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(4,4,1,'2021-05-12','Distribuidor Clínico',3400,'FT-2021-051','Cartão institucional',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(5,5,2,'2020-09-01','Siemens Healthineers',NULL,NULL,NULL,750,'2026-09-01','Aluguer com manutenção incluída.',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(6,6,1,'2019-11-25','Siemens Healthineers',92000,'FT-2019-087','Transferência',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(7,7,1,'2022-04-05','B. Braun Medical',2100,'FT-2022-091','Transferência',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(8,8,1,'2022-04-05','B. Braun Medical',1800,'FT-2022-092','Transferência',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(9,9,1,'2018-08-14','Getinge Portugal',48000,'FT-2018-120','Transferência',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(10,10,1,'2021-07-19','Fornecedor Laboratorial',7800,'FT-2021-130','Transferência',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(11,11,1,'2020-01-30','Fornecedor Laboratorial',24500,'FT-2020-009','Transferência',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(12,12,3,'2019-10-10',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Associação Amigos do Hospital',2200,'Doação sem encargos.',NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(13,13,1,'2023-01-18','OrtoRehab Lda',1400,'FT-2023-010','Transferência',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(14,14,1,'2021-09-09','OrtoRehab Lda',5200,'FT-2021-160','Transferência',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(15,15,1,'2020-03-22','Philips Healthcare',7100,'FT-2020-045','Transferência',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(16,16,1,'2018-12-01','Dräger Portugal',23000,'FT-2018-199','Transferência',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(17,17,1,'2021-02-17','Fornecedor Cirúrgico',2600,'FT-2021-028','Transferência',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(18,18,4,'2024-01-05',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Hospital Universitário Parceiro','2024-01-05','2027-01-05','Empréstimo institucional renovável.',1,NOW(),NOW()),
(19,19,1,'2019-05-02','Getinge Portugal',13500,'FT-2019-044','Transferência',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(20,20,1,'2020-07-07','Fresenius Medical Care',29500,'FT-2020-088','Transferência',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(21,21,1,'2023-02-14','BioConsumíveis SA',650,'FT-2023-031','Cartão institucional',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(22,22,1,'2022-06-21','BioConsumíveis SA',320,'FT-2022-117','Cartão institucional',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(23,23,1,'2023-03-04','BioConsumíveis SA',180,'FT-2023-044','Cartão institucional',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(24,24,1,'2022-01-12','OrtoRehab Lda',900,'FT-2022-011','Transferência',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(25,25,2,'2019-09-15','Siemens Healthineers',NULL,NULL,NULL,4200,'2027-09-15','Aluguer operacional com assistência.',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(26,26,2,'2018-04-10','Philips Healthcare',NULL,NULL,NULL,5100,'2028-04-10','Contrato de aluguer de longa duração.',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(27,27,1,'2021-11-11','Fornecedor Laboratorial',2600,'FT-2021-211','Transferência',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(28,28,1,'2020-10-20','Getinge Portugal',39000,'FT-2020-177','Transferência',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(29,29,3,'2023-05-09',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Liga de Amigos do Hospital',450,'Doação para apoio à mobilidade.',NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(30,30,1,'2021-08-03','Medtronic Portugal',12500,'FT-2021-144','Transferência',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW());

INSERT INTO equipamento_fornecedor VALUES
(1,2,1,1,NOW(),NOW()),(1,2,3,1,NOW(),NOW()),
(2,1,1,1,NOW(),NOW()),(2,1,4,1,NOW(),NOW()),
(3,2,1,1,NOW(),NOW()),
(5,3,1,1,NOW(),NOW()),(5,3,4,1,NOW(),NOW()),
(6,3,1,1,NOW(),NOW()),
(7,4,1,1,NOW(),NOW()),(7,4,5,1,NOW(),NOW()),
(8,4,1,1,NOW(),NOW()),
(9,5,3,1,NOW(),NOW()),(9,5,4,1,NOW(),NOW()),
(13,7,2,1,NOW(),NOW()),
(20,6,1,1,NOW(),NOW()),(20,6,5,1,NOW(),NOW()),
(25,3,1,1,NOW(),NOW()),(26,2,1,1,NOW(),NOW()),
(28,5,3,1,NOW(),NOW()),(30,1,1,1,NOW(),NOW());

INSERT INTO componentes VALUES
(1,1,'COMP-0001','Cabo ECG de 5 derivações',1,'Verificar desgaste mensalmente.',1,NOW(),NOW()),
(2,1,'COMP-0002','Sensor SpO2 reutilizável',1,'Funcional.',1,NOW(),NOW()),
(3,2,'COMP-0003','Circuito ventilatório',1,'Substituição periódica recomendada.',1,NOW(),NOW()),
(4,2,'COMP-0004','Filtro bacteriano',1,'Consumível associado ao ventilador.',1,NOW(),NOW()),
(5,3,'COMP-0005','Pás de desfibrilhação',1,'Testar semanalmente.',1,NOW(),NOW()),
(6,5,'COMP-0006','Sonda convexa',1,'Sonda principal.',1,NOW(),NOW()),
(7,5,'COMP-0007','Sonda linear',2,'Em verificação técnica.',1,NOW(),NOW()),
(8,6,'COMP-0008','Detector digital',1,'Componente essencial do RX.',1,NOW(),NOW()),
(9,7,'COMP-0009','Suporte de bomba',1,'Funcional.',1,NOW(),NOW()),
(10,8,'COMP-0010','Seringa compatível 50ml',1,'Acessório operacional.',1,NOW(),NOW()),
(11,9,'COMP-0011','Câmara de esterilização',2,'Em manutenção preventiva.',1,NOW(),NOW()),
(12,10,'COMP-0012','Rotor de centrifugação',1,'Funcional.',1,NOW(),NOW()),
(13,11,'COMP-0013','Módulo de reagentes',1,'Monitorizar consumos.',1,NOW(),NOW()),
(14,16,'COMP-0014','Sensor de temperatura neonatal',1,'Funcional.',1,NOW(),NOW()),
(15,17,'COMP-0015','Frasco coletor',1,'Esterilizar após utilização.',1,NOW(),NOW()),
(16,20,'COMP-0016','Filtro de dialisado',1,'Troca conforme protocolo.',1,NOW(),NOW()),
(17,25,'COMP-0017','Tubo de raios X',1,'Controlar número de exposições.',1,NOW(),NOW()),
(18,26,'COMP-0018','Bobina de cabeça',1,'Funcional.',1,NOW(),NOW()),
(19,28,'COMP-0019','Cartucho de peróxido',1,'Consumível controlado.',1,NOW(),NOW()),
(20,30,'COMP-0020','Bateria interna',1,'Testar autonomia mensalmente.',1,NOW(),NOW());

INSERT INTO consumiveis VALUES
(1,2,'Filtro bacteriano',35,10,'2026-06-01','Stock adequado.',1,NOW(),NOW()),
(2,7,'Sistemas de infusão',120,40,'2026-06-01','Uso frequente na UCI.',1,NOW(),NOW()),
(3,8,'Seringas 50 ml',80,25,'2026-06-01','Compatíveis com bomba seringa.',1,NOW(),NOW()),
(4,10,'Tubos de centrifugação',300,100,'2026-06-01','Material laboratorial.',1,NOW(),NOW()),
(5,11,'Reagente hematológico',18,8,'2026-06-01','Verificar validade.',1,NOW(),NOW()),
(6,20,'Linhas de hemodiálise',60,20,'2026-06-01','Stock crítico.',1,NOW(),NOW()),
(7,28,'Cartuchos de esterilização',25,10,'2026-06-01','Consumível essencial.',1,NOW(),NOW()),
(8,30,'Máscaras ventilação transporte',40,15,'2026-06-01','Para emergência.',1,NOW(),NOW());

INSERT INTO garantias VALUES
(1,1,'Garantia Monitor Philips','2021-03-10','2026-03-10',1,'Garantia ativa.',1,NOW(),NOW()),
(2,2,'Garantia Ventilador Medtronic','2020-06-15','2025-06-15',2,'Garantia expirada recentemente.',1,NOW(),NOW()),
(3,3,'Garantia Desfibrilhador Philips','2022-02-20','2027-02-20',1,'Garantia ativa.',1,NOW(),NOW()),
(4,5,'Garantia Ecógrafo Siemens','2020-09-01','2025-09-01',1,'Garantia ativa.',1,NOW(),NOW()),
(5,6,'Garantia RX Siemens','2019-11-25','2024-11-25',2,'Expirada.',1,NOW(),NOW()),
(6,7,'Garantia Bomba Infusão','2022-04-05','2027-04-05',1,'Garantia ativa.',1,NOW(),NOW()),
(7,9,'Garantia Autoclave Getinge','2018-08-14','2023-08-14',2,'Expirada.',1,NOW(),NOW()),
(8,11,'Garantia Analisador Hematológico','2020-01-30','2025-01-30',2,'Expirada.',1,NOW(),NOW()),
(9,16,'Garantia Incubadora Neonatal','2018-12-01','2024-12-01',2,'Expirada.',1,NOW(),NOW()),
(10,20,'Garantia Hemodiálise Fresenius','2020-07-07','2026-07-07',1,'Garantia ativa.',1,NOW(),NOW()),
(11,25,'Garantia TAC Siemens','2019-09-15','2027-09-15',1,'Associada ao aluguer.',1,NOW(),NOW()),
(12,26,'Garantia RM Philips','2018-04-10','2028-04-10',1,'Associada ao aluguer.',1,NOW(),NOW());

INSERT INTO contratos VALUES
(1,2,1,'Contrato manutenção ventilador','2024-01-01','2026-12-31',1800,'Manutenção preventiva e corretiva.',1,NOW(),NOW()),
(2,5,3,'Contrato manutenção ecógrafo','2024-01-01','2026-12-31',2200,'Inclui calibração anual.',1,NOW(),NOW()),
(3,6,3,'Contrato manutenção RX','2024-01-01','2026-12-31',7500,'Assistência especializada.',1,NOW(),NOW()),
(4,9,5,'Contrato manutenção autoclave','2024-01-01','2026-12-31',3100,'Inclui validações periódicas.',1,NOW(),NOW()),
(5,20,6,'Contrato hemodiálise','2024-01-01','2026-12-31',4200,'Suporte técnico e consumíveis específicos.',1,NOW(),NOW()),
(6,25,3,'Contrato aluguer TAC','2019-09-15','2027-09-15',50400,'Contrato operacional de longo prazo.',1,NOW(),NOW()),
(7,26,2,'Contrato aluguer RM','2018-04-10','2028-04-10',61200,'Inclui manutenção e suporte.',1,NOW(),NOW()),
(8,28,5,'Contrato esterilizador plasma','2024-01-01','2026-12-31',3900,'Manutenção preventiva.',1,NOW(),NOW());

INSERT INTO documentos
(id_documento,nome_documento,id_tipo_documento,nome_ficheiro,caminho_ficheiro,data_documento,data_validade,tamanho_ficheiro,formato_ficheiro,id_equipamento,id_entrada,id_fornecedor,id_localizacao,id_componente,id_garantia,id_contrato,ativo,created_at,updated_at)
VALUES
(1,'Manual Monitor Philips',2,'manual_monitor_philips.pdf','uploads/documentos/manual_monitor_philips.pdf','2021-03-10',NULL,'2.4 MB','PDF',1,NULL,2,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(2,'Fatura Monitor Philips',11,'fatura_monitor_philips.pdf','uploads/documentos/fatura_monitor_philips.pdf','2021-03-10',NULL,'850 KB','PDF',1,1,2,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(3,'Garantia Monitor Philips',20,'garantia_monitor_philips.pdf','uploads/documentos/garantia_monitor_philips.pdf','2021-03-10','2026-03-10','620 KB','PDF',1,NULL,2,NULL,NULL,1,NULL,1,NOW(),NOW()),
(4,'Manual Ventilador Medtronic',2,'manual_ventilador_medtronic.pdf','uploads/documentos/manual_ventilador_medtronic.pdf','2020-06-15',NULL,'3.1 MB','PDF',2,NULL,1,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(5,'Contrato Ventilador Medtronic',21,'contrato_ventilador_medtronic.pdf','uploads/documentos/contrato_ventilador_medtronic.pdf','2024-01-01','2026-12-31','1.2 MB','PDF',2,NULL,1,NULL,NULL,NULL,1,1,NOW(),NOW()),
(6,'Ficha Técnica Desfibrilhador',5,'ficha_desfibrilhador.pdf','uploads/documentos/ficha_desfibrilhador.pdf','2022-02-20',NULL,'900 KB','PDF',3,NULL,2,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(7,'Relatório Calibração Ecógrafo',7,'relatorio_calibracao_ecografo.pdf','uploads/documentos/relatorio_calibracao_ecografo.pdf','2026-01-15','2027-01-15','780 KB','PDF',5,NULL,3,NULL,NULL,NULL,2,1,NOW(),NOW()),
(8,'Contrato RX Siemens',21,'contrato_rx_siemens.pdf','uploads/documentos/contrato_rx_siemens.pdf','2024-01-01','2026-12-31','1.5 MB','PDF',6,NULL,3,NULL,NULL,NULL,3,1,NOW(),NOW()),
(9,'Manual Bomba Infusão',2,'manual_bomba_infusao.pdf','uploads/documentos/manual_bomba_infusao.pdf','2022-04-05',NULL,'1.1 MB','PDF',7,NULL,4,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(10,'Contrato Autoclave',21,'contrato_autoclave.pdf','uploads/documentos/contrato_autoclave.pdf','2024-01-01','2026-12-31','1.0 MB','PDF',9,NULL,5,NULL,NULL,NULL,4,1,NOW(),NOW()),
(11,'Relatório Manutenção Autoclave',7,'relatorio_autoclave.pdf','uploads/documentos/relatorio_autoclave.pdf','2026-02-10',NULL,'740 KB','PDF',9,NULL,5,NULL,NULL,NULL,4,1,NOW(),NOW()),
(12,'Fatura Centrífuga',11,'fatura_centrifuga.pdf','uploads/documentos/fatura_centrifuga.pdf','2021-07-19',NULL,'690 KB','PDF',10,10,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(13,'Manual Analisador Hematológico',2,'manual_analisador_hematologico.pdf','uploads/documentos/manual_analisador_hematologico.pdf','2020-01-30',NULL,'4.3 MB','PDF',11,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(14,'Termo Doação Microscópio',14,'termo_doacao_microscopio.pdf','uploads/documentos/termo_doacao_microscopio.pdf','2019-10-10',NULL,'500 KB','PDF',12,12,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(15,'Ficha Técnica Incubadora',5,'ficha_incubadora.pdf','uploads/documentos/ficha_incubadora.pdf','2018-12-01',NULL,'1.7 MB','PDF',16,NULL,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW()),
(16,'Contrato Hemodiálise',21,'contrato_hemodialise.pdf','uploads/documentos/contrato_hemodialise.pdf','2024-01-01','2026-12-31','1.4 MB','PDF',20,NULL,6,NULL,NULL,NULL,5,1,NOW(),NOW()),
(17,'Contrato TAC Siemens',13,'contrato_tac_siemens.pdf','uploads/documentos/contrato_tac_siemens.pdf','2019-09-15','2027-09-15','2.2 MB','PDF',25,25,3,NULL,NULL,NULL,6,1,NOW(),NOW()),
(18,'Contrato RM Philips',13,'contrato_rm_philips.pdf','uploads/documentos/contrato_rm_philips.pdf','2018-04-10','2028-04-10','2.8 MB','PDF',26,26,2,NULL,NULL,NULL,7,1,NOW(),NOW()),
(19,'Contrato Esterilizador Plasma',21,'contrato_esterilizador_plasma.pdf','uploads/documentos/contrato_esterilizador_plasma.pdf','2024-01-01','2026-12-31','1.2 MB','PDF',28,NULL,5,NULL,NULL,NULL,8,1,NOW(),NOW()),
(20,'Termo Doação Cadeira Rodas',14,'termo_doacao_cadeira_rodas.pdf','uploads/documentos/termo_doacao_cadeira_rodas.pdf','2023-05-09',NULL,'420 KB','PDF',29,29,NULL,NULL,NULL,NULL,NULL,1,NOW(),NOW());

INSERT INTO secoes_publicas VALUES
(1,'Início',1,NOW(),NOW()),
(2,'Sobre Nós',1,NOW(),NOW()),
(3,'Serviços',1,NOW(),NOW()),
(4,'Contacta-nos',1,NOW(),NOW()),
(5,'Rodapé',1,NOW(),NOW());

INSERT INTO conteudos_publicos VALUES
(1,1,'titulo','Gestão de Equipamentos Biomédicos',1,NOW(),NOW()),
(2,1,'texto','Sistema interno para controlo de equipamentos, garantias, contratos, documentos e fornecedores.',1,NOW(),NOW()),
(3,2,'titulo','Sobre o sistema',1,NOW(),NOW()),
(4,2,'texto','A plataforma permite centralizar informação técnica e administrativa dos equipamentos hospitalares.',1,NOW(),NOW()),
(5,4,'email','contacto@hospital.pt',1,NOW(),NOW()),
(6,4,'telefone','210000000',1,NOW(),NOW()),
(7,5,'texto','Projeto académico de gestão de equipamentos biomédicos.',1,NOW(),NOW());

INSERT INTO mensagens_contacto VALUES
(1,'Ana Martins','ana@email.pt','Pedido de informação','Gostaria de saber mais sobre o sistema de gestão.',NOW(),1,NOW(),NOW()),
(2,'Pedro Almeida','pedro@email.pt','Documentação','Envio de documentação técnica em falta.',NOW(),1,NOW(),NOW());

INSERT INTO historico VALUES
(1,1,'Equipamentos','Criação','EQ-0001','Registo inicial de equipamento.',NOW(),NOW(),NOW()),
(2,1,'Fornecedores','Criação','Philips Healthcare','Registo de fornecedor.',NOW(),NOW(),NOW()),
(3,2,'Documentação','Criação','Manual Monitor Philips','Documento associado ao equipamento.',NOW(),NOW(),NOW());

INSERT INTO lembretes VALUES
(1,1,'Verificar garantias que terminam este ano.',0,NOW()),
(2,1,'Confirmar manutenção preventiva do autoclave.',0,NOW());

SELECT 'Base de dados recriada e populada com sucesso!' AS resultado;