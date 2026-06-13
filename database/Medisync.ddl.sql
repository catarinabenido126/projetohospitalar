CREATE TABLE `utilizadores` (
  `id_utilizador` int PRIMARY KEY AUTO_INCREMENT,
  `nome_utilizador` varchar(50) UNIQUE NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `remember_token` varchar(100),
  `ultimo_acesso` datetime,
  `ativo` boolean NOT NULL DEFAULT true,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `password_resets` (
  `id_password_reset` int PRIMARY KEY AUTO_INCREMENT,
  `id_utilizador` int NOT NULL,
  `token` varchar(255) UNIQUE NOT NULL,
  `expira_em` datetime NOT NULL,
  `usado` boolean NOT NULL DEFAULT false,
  `created_at` datetime NOT NULL
);

CREATE TABLE `servicos` (
  `id_servico` int PRIMARY KEY AUTO_INCREMENT,
  `nome_servico` varchar(100) UNIQUE NOT NULL COMMENT 'Urgência, UCI, Bloco Operatório, Consultas, Laboratório, Radiologia, Reabilitação, Armazém',
  `ativo` boolean NOT NULL DEFAULT true,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `localizacoes` (
  `id_localizacao` int PRIMARY KEY AUTO_INCREMENT,
  `edificio` varchar(100) NOT NULL,
  `piso` varchar(50) NOT NULL,
  `sala` varchar(50) NOT NULL,
  `id_servico` int NOT NULL,
  `responsavel` varchar(100),
  `contacto` varchar(20),
  `email` varchar(100),
  `ativo` boolean NOT NULL DEFAULT true,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `categorias` (
  `id_categoria` int PRIMARY KEY AUTO_INCREMENT,
  `nome_categoria` varchar(80) UNIQUE NOT NULL COMMENT 'Monitorização, Suporte de Vida, Diagnóstico, Imagiologia, Laboratório, Terapia, Cirurgia, Reabilitação, Esterilização',
  `descricao` text,
  `ativo` boolean NOT NULL DEFAULT true,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `estados_equipamento` (
  `id_estado` int PRIMARY KEY AUTO_INCREMENT,
  `nome_estado` varchar(50) UNIQUE NOT NULL COMMENT 'Ativo, Em manutenção, Em calibração, Em quarentena, Inativo, Abatido',
  `ativo` boolean NOT NULL DEFAULT true,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `criticidades` (
  `id_criticidade` int PRIMARY KEY AUTO_INCREMENT,
  `nivel` varchar(50) UNIQUE NOT NULL COMMENT 'Baixa, Média, Alta, Suporte de vida',
  `descricao` text,
  `ativo` boolean NOT NULL DEFAULT true,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `tipos_entrada` (
  `id_tipo_entrada` int PRIMARY KEY AUTO_INCREMENT,
  `tipo` varchar(50) UNIQUE NOT NULL COMMENT 'Compra, Aluguer, Doação, Empréstimo',
  `ativo` boolean NOT NULL DEFAULT true,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `tipos_fornecedor` (
  `id_tipo_fornecedor` int PRIMARY KEY AUTO_INCREMENT,
  `tipo` varchar(80) UNIQUE NOT NULL COMMENT 'Fabricante, Distribuidor, Assistência técnica, Consumíveis ou acessórios',
  `ativo` boolean NOT NULL DEFAULT true,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `fornecedores` (
  `id_fornecedor` int PRIMARY KEY AUTO_INCREMENT,
  `nome_empresa` varchar(150) NOT NULL,
  `nif` varchar(9) UNIQUE NOT NULL,
  `id_tipo_fornecedor` int NOT NULL,
  `telefone` varchar(20),
  `email` varchar(100),
  `website` varchar(150),
  `pessoa_contacto` varchar(100),
  `telefone_contacto` varchar(20),
  `email_contacto` varchar(100),
  `morada` varchar(200),
  `codigo_postal` varchar(20),
  `cidade` varchar(100),
  `pais` varchar(100),
  `observacoes` text,
  `ativo` boolean NOT NULL DEFAULT true,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `equipamentos` (
  `id_equipamento` int PRIMARY KEY AUTO_INCREMENT,
  `codigo_interno` varchar(20) UNIQUE NOT NULL,
  `designacao` varchar(150) NOT NULL,
  `id_categoria` int NOT NULL,
  `marca` varchar(80),
  `modelo` varchar(80) NOT NULL,
  `fabricante` varchar(100) NOT NULL,
  `numero_serie` varchar(100) NOT NULL,
  `ano_fabrico` int,
  `id_estado` int NOT NULL,
  `id_criticidade` int NOT NULL,
  `id_localizacao` int NOT NULL,
  `observacoes` text,
  `ativo` boolean NOT NULL DEFAULT true,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `entradas_equipamento` (
  `id_entrada` int PRIMARY KEY AUTO_INCREMENT,
  `id_equipamento` int NOT NULL,
  `id_tipo_entrada` int NOT NULL,
  `data_entrada` date,
  `entidade_associada` varchar(150),
  `custo_aquisicao` decimal(10,2),
  `numero_fatura` varchar(80),
  `metodo_pagamento` varchar(80),
  `valor_mensal` decimal(10,2),
  `data_fim_aluguer` date,
  `condicoes_aluguer` text,
  `entidade_doadora` varchar(150),
  `valor_estimado` decimal(10,2),
  `condicoes_doacao` text,
  `entidade_proprietaria` varchar(150),
  `data_inicio_emprestimo` date,
  `data_prevista_devolucao` date,
  `condicoes_emprestimo` text,
  `ativo` boolean NOT NULL DEFAULT true,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `tipos_relacao_fornecedor` (
  `id_tipo_relacao` int PRIMARY KEY AUTO_INCREMENT,
  `tipo` varchar(80) UNIQUE NOT NULL COMMENT 'Fabricante, Fornecedor, Assistência técnica, Manutenção, Consumíveis',
  `ativo` boolean NOT NULL DEFAULT true,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `equipamento_fornecedor` (
  `id_equipamento` int NOT NULL,
  `id_fornecedor` int NOT NULL,
  `id_tipo_relacao` int NOT NULL,
  `ativo` boolean NOT NULL DEFAULT true,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_equipamento`, `id_fornecedor`, `id_tipo_relacao`)
);

CREATE TABLE `estados_componentes` (
  `id_estado_componente` int PRIMARY KEY AUTO_INCREMENT,
  `estado` varchar(50) UNIQUE NOT NULL COMMENT 'Funcional, Em manutenção, Avariado, Substituído, Abatido',
  `ativo` boolean NOT NULL DEFAULT true,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `componentes` (
  `id_componente` int PRIMARY KEY AUTO_INCREMENT,
  `id_equipamento` int NOT NULL,
  `codigo_componente` varchar(30) UNIQUE NOT NULL,
  `nome_componente` varchar(150) NOT NULL,
  `id_estado_componente` int NOT NULL,
  `notificacao` text,
  `ativo` boolean NOT NULL DEFAULT true,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `consumiveis` (
  `id_consumivel` int PRIMARY KEY AUTO_INCREMENT,
  `id_equipamento` int NOT NULL,
  `nome_consumivel` varchar(150) NOT NULL,
  `stock_atual` int NOT NULL,
  `stock_minimo` int NOT NULL,
  `ultima_atualizacao` date,
  `observacoes` text,
  `ativo` boolean NOT NULL DEFAULT true,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `tipos_documento` (
  `id_tipo_documento` int PRIMARY KEY AUTO_INCREMENT,
  `tipo` varchar(100) UNIQUE NOT NULL COMMENT 'Manual, Manual de utilizador, Manual de componente, Manual de manutenção, Ficha Técnica, Certificação, Relatório, Relatório de uso, Relatório de teste, Registo de substituição, Fatura, Comprovativo de pagamento, Contrato de aluguer, Termo de doação, Termo de empréstimo, Guia de transporte, Auto de receção, Planta, Instalação, Garantia, Contrato, Declaração, Outro',
  `ativo` boolean NOT NULL DEFAULT true,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `documentos` (
  `id_documento` int PRIMARY KEY AUTO_INCREMENT,
  `nome_documento` varchar(150) NOT NULL,
  `id_tipo_documento` int NOT NULL,
  `nome_ficheiro` varchar(200) NOT NULL,
  `caminho_ficheiro` varchar(255) NOT NULL,
  `data_documento` date,
  `data_validade` date,
  `tamanho_ficheiro` varchar(30),
  `formato_ficheiro` varchar(20),
  `id_equipamento` int,
  `id_entrada` int,
  `id_fornecedor` int,
  `id_localizacao` int,
  `id_componente` int,
  `id_garantia` int,
  `id_contrato` int,
  `ativo` boolean NOT NULL DEFAULT true,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `estados_garantia` (
  `id_estado_garantia` int PRIMARY KEY AUTO_INCREMENT,
  `estado` varchar(50) UNIQUE NOT NULL COMMENT 'Ativa, Expirada',
  `ativo` boolean NOT NULL DEFAULT true,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `garantias` (
  `id_garantia` int PRIMARY KEY AUTO_INCREMENT,
  `id_equipamento` int NOT NULL,
  `nome_garantia` varchar(150) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `id_estado_garantia` int NOT NULL,
  `observacoes` text,
  `ativo` boolean NOT NULL DEFAULT true,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `contratos` (
  `id_contrato` int PRIMARY KEY AUTO_INCREMENT,
  `id_equipamento` int NOT NULL,
  `id_fornecedor` int NOT NULL,
  `nome_contrato` varchar(150) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `valor_anual` decimal(10,2),
  `observacoes` text,
  `ativo` boolean NOT NULL DEFAULT true,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `mensagens_contacto` (
  `id_mensagem` int PRIMARY KEY AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `assunto` varchar(150),
  `mensagem` text NOT NULL,
  `data_envio` datetime NOT NULL,
  `ativo` boolean NOT NULL DEFAULT true,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `secoes_publicas` (
  `id_seccao` int PRIMARY KEY AUTO_INCREMENT,
  `nome_seccao` varchar(100) UNIQUE NOT NULL COMMENT 'Início, Sobre Nós, Serviços, Contacta-nos, Rodapé',
  `ativo` boolean NOT NULL DEFAULT true,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `conteudos_publicos` (
  `id_conteudo` int PRIMARY KEY AUTO_INCREMENT,
  `id_seccao` int NOT NULL,
  `campo` varchar(100) NOT NULL COMMENT 'titulo, texto, imagem, email, telefone, horario, localizacao',
  `valor` text NOT NULL,
  `ativo` boolean NOT NULL DEFAULT true,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `historico` (
  `id_historico` int PRIMARY KEY AUTO_INCREMENT,
  `id_utilizador` int NOT NULL,
  `modulo` varchar(80) NOT NULL COMMENT 'Equipamentos, Fornecedores, Localizações, Documentação, Garantias, Contratos, Gestão, Mensagens, Login',
  `acao` varchar(80) NOT NULL COMMENT 'Criação, Edição, Remoção, Alteração de password',
  `registo` varchar(150),
  `detalhes` text,
  `data_hora` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
);

CREATE TABLE `lembretes` (
  `id_lembrete` int PRIMARY KEY AUTO_INCREMENT,
  `id_utilizador` int NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `concluido` boolean NOT NULL DEFAULT false,
  `created_at` datetime NOT NULL
);

CREATE UNIQUE INDEX `localizacoes_index_0` ON `localizacoes` (`edificio`, `piso`, `sala`);

CREATE UNIQUE INDEX `equipamentos_index_1` ON `equipamentos` (`fabricante`, `modelo`, `numero_serie`);

ALTER TABLE `password_resets` ADD FOREIGN KEY (`id_utilizador`) REFERENCES `utilizadores` (`id_utilizador`);

ALTER TABLE `localizacoes` ADD FOREIGN KEY (`id_servico`) REFERENCES `servicos` (`id_servico`);

ALTER TABLE `equipamentos` ADD FOREIGN KEY (`id_localizacao`) REFERENCES `localizacoes` (`id_localizacao`);

ALTER TABLE `equipamentos` ADD FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`);

ALTER TABLE `equipamentos` ADD FOREIGN KEY (`id_estado`) REFERENCES `estados_equipamento` (`id_estado`);

ALTER TABLE `equipamentos` ADD FOREIGN KEY (`id_criticidade`) REFERENCES `criticidades` (`id_criticidade`);

ALTER TABLE `entradas_equipamento` ADD FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id_equipamento`);

ALTER TABLE `entradas_equipamento` ADD FOREIGN KEY (`id_tipo_entrada`) REFERENCES `tipos_entrada` (`id_tipo_entrada`);

ALTER TABLE `fornecedores` ADD FOREIGN KEY (`id_tipo_fornecedor`) REFERENCES `tipos_fornecedor` (`id_tipo_fornecedor`);

ALTER TABLE `equipamento_fornecedor` ADD FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id_equipamento`);

ALTER TABLE `equipamento_fornecedor` ADD FOREIGN KEY (`id_fornecedor`) REFERENCES `fornecedores` (`id_fornecedor`);

ALTER TABLE `equipamento_fornecedor` ADD FOREIGN KEY (`id_tipo_relacao`) REFERENCES `tipos_relacao_fornecedor` (`id_tipo_relacao`);

ALTER TABLE `componentes` ADD FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id_equipamento`);

ALTER TABLE `componentes` ADD FOREIGN KEY (`id_estado_componente`) REFERENCES `estados_componentes` (`id_estado_componente`);

ALTER TABLE `consumiveis` ADD FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id_equipamento`);

ALTER TABLE `documentos` ADD FOREIGN KEY (`id_tipo_documento`) REFERENCES `tipos_documento` (`id_tipo_documento`);

ALTER TABLE `documentos` ADD FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id_equipamento`);

ALTER TABLE `documentos` ADD FOREIGN KEY (`id_entrada`) REFERENCES `entradas_equipamento` (`id_entrada`);

ALTER TABLE `documentos` ADD FOREIGN KEY (`id_fornecedor`) REFERENCES `fornecedores` (`id_fornecedor`);

ALTER TABLE `documentos` ADD FOREIGN KEY (`id_localizacao`) REFERENCES `localizacoes` (`id_localizacao`);

ALTER TABLE `documentos` ADD FOREIGN KEY (`id_componente`) REFERENCES `componentes` (`id_componente`);

ALTER TABLE `documentos` ADD FOREIGN KEY (`id_garantia`) REFERENCES `garantias` (`id_garantia`);

ALTER TABLE `documentos` ADD FOREIGN KEY (`id_contrato`) REFERENCES `contratos` (`id_contrato`);

ALTER TABLE `garantias` ADD FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id_equipamento`);

ALTER TABLE `garantias` ADD FOREIGN KEY (`id_estado_garantia`) REFERENCES `estados_garantia` (`id_estado_garantia`);

ALTER TABLE `contratos` ADD FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id_equipamento`);

ALTER TABLE `contratos` ADD FOREIGN KEY (`id_fornecedor`) REFERENCES `fornecedores` (`id_fornecedor`);

ALTER TABLE `conteudos_publicos` ADD FOREIGN KEY (`id_seccao`) REFERENCES `secoes_publicas` (`id_seccao`);

ALTER TABLE `historico` ADD FOREIGN KEY (`id_utilizador`) REFERENCES `utilizadores` (`id_utilizador`);

ALTER TABLE `lembretes` ADD FOREIGN KEY (`id_utilizador`) REFERENCES `utilizadores` (`id_utilizador`);
