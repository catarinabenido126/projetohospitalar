Table "categorias" {
  "id_categoria" int [pk, not null, increment]
  "nome_categoria" varchar(80) [not null, note: 'Monitorização, Suporte de Vida, Diagnóstico, Imagiologia, Laboratório, Terapia, Cirurgia, Reabilitação']
  "descricao" text
  "ativo" tinyint(1) [not null, default: '1']
  "created_at" datetime [not null]
  "updated_at" datetime [not null]

  Indexes {
    nome_categoria [unique]
  }
}

Table "componentes" {
  "id_componente" int [pk, not null, increment]
  "id_equipamento" int [not null]
  "codigo_componente" varchar(30) [not null]
  "nome_componente" varchar(150) [not null]
  "id_estado_componente" int [not null]
  "notificacao" text
  "ativo" tinyint(1) [not null, default: '1']
  "created_at" datetime [not null]
  "updated_at" datetime [not null]

  Indexes {
    codigo_componente [unique]
    id_equipamento
    id_estado_componente
  }
}

Table "consumiveis" {
  "id_consumivel" int [pk, not null, increment]
  "id_equipamento" int [not null]
  "nome_consumivel" varchar(150) [not null]
  "stock_atual" int [not null]
  "stock_minimo" int [not null]
  "ultima_atualizacao" date
  "observacoes" text
  "ativo" tinyint(1) [not null, default: '1']
  "created_at" datetime [not null]
  "updated_at" datetime [not null]

  Indexes {
    id_equipamento
  }
}

Table "conteudos_publicos" {
  "id_conteudo" int [pk, not null, increment]
  "id_seccao" int [not null]
  "campo" varchar(100) [not null, note: 'titulo, texto, imagem, email, telefone, horario, localizacao']
  "valor" text [not null]
  "ativo" tinyint(1) [not null, default: '1']
  "created_at" datetime [not null]
  "updated_at" datetime [not null]

  Indexes {
    id_seccao
  }
}

Table "contratos" {
  "id_contrato" int [pk, not null, increment]
  "id_equipamento" int [not null]
  "id_fornecedor" int [not null]
  "nome_contrato" varchar(150) [not null]
  "data_inicio" date [not null]
  "data_fim" date [not null]
  "valor_anual" decimal(10,2)
  "observacoes" text
  "ativo" tinyint(1) [not null, default: '1']
  "created_at" datetime [not null]
  "updated_at" datetime [not null]

  Indexes {
    id_equipamento
    id_fornecedor
  }
}

Table "criticidades" {
  "id_criticidade" int [pk, not null, increment]
  "nivel" varchar(50) [not null, note: 'Baixa, Média, Alta, Suporte de vida']
  "descricao" text
  "ativo" tinyint(1) [not null, default: '1']
  "created_at" datetime [not null]
  "updated_at" datetime [not null]

  Indexes {
    nivel [unique]
  }
}

Table "documentos" {
  "id_documento" int [pk, not null, increment]
  "nome_documento" varchar(150) [not null]
  "id_tipo_documento" int [not null]
  "nome_ficheiro" varchar(200) [not null]
  "caminho_ficheiro" varchar(255) [not null]
  "data_documento" date
  "data_validade" date
  "tamanho_ficheiro" varchar(30)
  "formato_ficheiro" varchar(20)
  "id_equipamento" int
  "id_entrada" int
  "id_fornecedor" int
  "id_localizacao" int
  "id_componente" int
  "id_garantia" int
  "id_contrato" int
  "ativo" tinyint(1) [not null, default: '1']
  "created_at" datetime [not null]
  "updated_at" datetime [not null]

  Indexes {
    id_tipo_documento
    id_equipamento
    id_entrada
    id_fornecedor
    id_localizacao
    id_componente
    id_garantia
    id_contrato
  }
}

Table "entradas_equipamento" {
  "id_entrada" int [pk, not null, increment]
  "id_equipamento" int [not null]
  "id_tipo_entrada" int [not null]
  "data_entrada" date
  "entidade_associada" varchar(150)
  "custo_aquisicao" decimal(10,2)
  "numero_fatura" varchar(80)
  "metodo_pagamento" varchar(80)
  "valor_mensal" decimal(10,2)
  "data_fim_aluguer" date
  "condicoes_aluguer" text
  "entidade_doadora" varchar(150)
  "valor_estimado" decimal(10,2)
  "condicoes_doacao" text
  "entidade_proprietaria" varchar(150)
  "data_inicio_emprestimo" date
  "data_prevista_devolucao" date
  "condicoes_emprestimo" text
  "ativo" tinyint(1) [not null, default: '1']
  "created_at" datetime [not null]
  "updated_at" datetime [not null]

  Indexes {
    id_equipamento
    id_tipo_entrada
  }
}

Table "equipamentos" {
  "id_equipamento" int [pk, not null, increment]
  "codigo_interno" varchar(20) [not null]
  "designacao" varchar(150) [not null]
  "id_categoria" int [not null]
  "marca" varchar(80) [not null]
  "modelo" varchar(80) [not null]
  "numero_serie" varchar(100) [not null]
  "ano_fabrico" int
  "id_estado" int [not null]
  "id_criticidade" int [not null]
  "id_localizacao" int [not null]
  "observacoes" text
  "ativo" tinyint(1) [not null, default: '1']
  "created_at" datetime [not null]
  "updated_at" datetime [not null]

  Indexes {
    codigo_interno [unique]
    numero_serie [unique]
    id_categoria
    id_estado
    id_criticidade
    id_localizacao
  }
}

Table "equipamento_fornecedor" {
  "id_equipamento" int [not null]
  "id_fornecedor" int [not null]
  "id_tipo_relacao" int [not null]
  "ativo" tinyint(1) [not null, default: '1']
  "created_at" datetime [not null]
  "updated_at" datetime [not null]

  Indexes {
    (id_equipamento, id_fornecedor, id_tipo_relacao) [pk]
    id_fornecedor
    id_tipo_relacao
  }
}

Table "estados_componentes" {
  "id_estado_componente" int [pk, not null, increment]
  "estado" varchar(50) [not null, note: 'Funcional, Em manutenção, Avariado, Substituído, Abatido']
  "ativo" tinyint(1) [not null, default: '1']
  "created_at" datetime [not null]
  "updated_at" datetime [not null]

  Indexes {
    estado [unique]
  }
}

Table "estados_equipamento" {
  "id_estado" int [pk, not null, increment]
  "nome_estado" varchar(50) [not null, note: 'Ativo, Em manutenção, Em calibração, Em quarentena, Inativo, Abatido']
  "ativo" tinyint(1) [not null, default: '1']
  "created_at" datetime [not null]
  "updated_at" datetime [not null]

  Indexes {
    nome_estado [unique]
  }
}

Table "estados_garantia" {
  "id_estado_garantia" int [pk, not null, increment]
  "estado" varchar(50) [not null, note: 'Ativa, Expirada']
  "ativo" tinyint(1) [not null, default: '1']
  "created_at" datetime [not null]
  "updated_at" datetime [not null]

  Indexes {
    estado [unique]
  }
}

Table "fornecedores" {
  "id_fornecedor" int [pk, not null, increment]
  "nome_empresa" varchar(150) [not null]
  "nif" varchar(9) [not null]
  "telefone" varchar(20)
  "email" varchar(100)
  "website" varchar(150)
  "pessoa_contacto" varchar(100)
  "telefone_contacto" varchar(20)
  "email_contacto" varchar(100)
  "morada" varchar(200)
  "codigo_postal" varchar(20)
  "cidade" varchar(100)
  "pais" varchar(100)
  "observacoes" text
  "ativo" tinyint(1) [not null, default: '1']
  "created_at" datetime [not null]
  "updated_at" datetime [not null]

  Indexes {
    nif [unique]
  }
}

Table "garantias" {
  "id_garantia" int [pk, not null, increment]
  "id_equipamento" int [not null]
  "nome_garantia" varchar(150) [not null]
  "data_inicio" date [not null]
  "data_fim" date [not null]
  "id_estado_garantia" int [not null]
  "observacoes" text
  "ativo" tinyint(1) [not null, default: '1']
  "created_at" datetime [not null]
  "updated_at" datetime [not null]

  Indexes {
    id_equipamento
    id_estado_garantia
  }
}

Table "historico" {
  "id_historico" int [pk, not null, increment]
  "id_utilizador" int [not null]
  "modulo" varchar(80) [not null, note: 'Equipamentos, Fornecedores, Localizações, Documentação, Garantias, Contratos, Gestão, Mensagens, Login']
  "acao" varchar(80) [not null, note: 'Criação, Edição, Remoção, Alteração de password']
  "registo" varchar(150)
  "detalhes" text
  "data_hora" datetime [not null]
  "created_at" datetime [not null]
  "updated_at" datetime [not null]

  Indexes {
    id_utilizador
  }
}

Table "lembretes" {
  "id_lembrete" int [pk, not null, increment]
  "id_utilizador" int [not null]
  "descricao" varchar(255) [not null]
  "concluido" tinyint(1) [not null, default: '0']
  "created_at" datetime [not null]

  Indexes {
    id_utilizador
  }
}

Table "localizacoes" {
  "id_localizacao" int [pk, not null, increment]
  "edificio" varchar(100) [not null]
  "piso" varchar(50) [not null]
  "sala" varchar(50) [not null]
  "id_servico" int [not null]
  "responsavel" varchar(100)
  "contacto" varchar(20)
  "email" varchar(100)
  "ativo" tinyint(1) [not null, default: '1']
  "created_at" datetime [not null]
  "updated_at" datetime [not null]

  Indexes {
    (edificio, piso, sala) [unique, name: "uq_localizacao"]
    id_servico
  }
}

Table "mensagens_contacto" {
  "id_mensagem" int [pk, not null, increment]
  "nome" varchar(100) [not null]
  "email" varchar(100) [not null]
  "assunto" varchar(150)
  "mensagem" text [not null]
  "data_envio" datetime [not null]
  "ativo" tinyint(1) [not null, default: '1']
  "created_at" datetime [not null]
  "updated_at" datetime [not null]
}

Table "password_resets" {
  "id_password_reset" int [pk, not null, increment]
  "id_utilizador" int [not null]
  "token" varchar(255) [not null]
  "expira_em" datetime [not null]
  "usado" tinyint(1) [not null, default: '0']
  "created_at" datetime [not null]

  Indexes {
    token [unique]
    id_utilizador
  }
}

Table "secoes_publicas" {
  "id_seccao" int [pk, not null, increment]
  "nome_seccao" varchar(100) [not null, note: 'Início, Sobre Nós, Serviços, Contacta-nos, Rodapé']
  "ativo" tinyint(1) [not null, default: '1']
  "created_at" datetime [not null]
  "updated_at" datetime [not null]

  Indexes {
    nome_seccao [unique]
  }
}

Table "servicos" {
  "id_servico" int [pk, not null, increment]
  "nome_servico" varchar(100) [not null, note: 'Urgência, UCI, Bloco Operatório, Consultas, Laboratório, Radiologia, Reabilitação, Armazém']
  "ativo" tinyint(1) [not null, default: '1']
  "created_at" datetime [not null]
  "updated_at" datetime [not null]

  Indexes {
    nome_servico [unique]
  }
}

Table "tipos_documento" {
  "id_tipo_documento" int [pk, not null, increment]
  "tipo" varchar(100) [not null, note: 'Manual, Manual de utilizador, Ficha Técnica, Certificação, Relatório, Relatório de teste, Fatura, Comprovativo de pagamento, Contrato de aluguer, Termo de doação, Termo de empréstimo, Planta, Instalação, Garantia, Contrato, Declaração, Outro']
  "ativo" tinyint(1) [not null, default: '1']
  "created_at" datetime [not null]
  "updated_at" datetime [not null]

  Indexes {
    tipo [unique]
  }
}

Table "tipos_entrada" {
  "id_tipo_entrada" int [pk, not null, increment]
  "tipo" varchar(50) [not null, note: 'Compra, Aluguer, Doação, Empréstimo']
  "ativo" tinyint(1) [not null, default: '1']
  "created_at" datetime [not null]
  "updated_at" datetime [not null]

  Indexes {
    tipo [unique]
  }
}

Table "tipos_fornecedor" {
  "id_tipo_fornecedor" int [pk, not null, increment]
  "tipo" varchar(80) [not null, note: 'Fabricante, Distribuidor, Assistência técnica, Consumíveis ou acessórios']
  "ativo" tinyint(1) [not null, default: '1']
  "created_at" datetime [not null]
  "updated_at" datetime [not null]

  Indexes {
    tipo [unique]
  }
}

Table "tipos_relacao_fornecedor" {
  "id_tipo_relacao" int [pk, not null, increment]
  "tipo" varchar(80) [not null, note: 'Fabricante, Fornecedor, Assistência técnica, Manutenção, Consumíveis']
  "ativo" tinyint(1) [not null, default: '1']
  "created_at" datetime [not null]
  "updated_at" datetime [not null]

  Indexes {
    tipo [unique]
  }
}

Table "utilizadores" {
  "id_utilizador" int [pk, not null, increment]
  "email" varbinary(255) [not null]
  "password_hash" varchar(255) [not null]
  "perfil" varchar(20) [not null]
  "remember_token" varchar(100)
  "ultimo_acesso" datetime
  "ativo" tinyint(1) [not null, default: '1']
  "created_at" datetime [not null]
  "updated_at" datetime [not null]
}

Ref "componentes_ibfk_1":"equipamentos"."id_equipamento" < "componentes"."id_equipamento"
Ref "componentes_ibfk_2":"estados_componentes"."id_estado_componente" < "componentes"."id_estado_componente"
Ref "consumiveis_ibfk_1":"equipamentos"."id_equipamento" < "consumiveis"."id_equipamento"
Ref "conteudos_publicos_ibfk_1":"secoes_publicas"."id_seccao" < "conteudos_publicos"."id_seccao"
Ref "contratos_ibfk_1":"equipamentos"."id_equipamento" < "contratos"."id_equipamento"
Ref "contratos_ibfk_2":"fornecedores"."id_fornecedor" < "contratos"."id_fornecedor"
Ref "documentos_ibfk_1":"tipos_documento"."id_tipo_documento" < "documentos"."id_tipo_documento"
Ref "documentos_ibfk_2":"equipamentos"."id_equipamento" < "documentos"."id_equipamento"
Ref "documentos_ibfk_3":"entradas_equipamento"."id_entrada" < "documentos"."id_entrada"
Ref "documentos_ibfk_4":"fornecedores"."id_fornecedor" < "documentos"."id_fornecedor"
Ref "documentos_ibfk_5":"localizacoes"."id_localizacao" < "documentos"."id_localizacao"
Ref "documentos_ibfk_6":"componentes"."id_componente" < "documentos"."id_componente"
Ref "documentos_ibfk_7":"garantias"."id_garantia" < "documentos"."id_garantia"
Ref "documentos_ibfk_8":"contratos"."id_contrato" < "documentos"."id_contrato"
Ref "entradas_equipamento_ibfk_1":"equipamentos"."id_equipamento" < "entradas_equipamento"."id_equipamento"
Ref "entradas_equipamento_ibfk_2":"tipos_entrada"."id_tipo_entrada" < "entradas_equipamento"."id_tipo_entrada"
Ref "equipamentos_ibfk_1":"categorias"."id_categoria" < "equipamentos"."id_categoria"
Ref "equipamentos_ibfk_2":"estados_equipamento"."id_estado" < "equipamentos"."id_estado"
Ref "equipamentos_ibfk_3":"criticidades"."id_criticidade" < "equipamentos"."id_criticidade"
Ref "equipamentos_ibfk_4":"localizacoes"."id_localizacao" < "equipamentos"."id_localizacao"
Ref "equipamento_fornecedor_ibfk_1":"equipamentos"."id_equipamento" < "equipamento_fornecedor"."id_equipamento"
Ref "equipamento_fornecedor_ibfk_2":"fornecedores"."id_fornecedor" < "equipamento_fornecedor"."id_fornecedor"
Ref "equipamento_fornecedor_ibfk_3":"tipos_relacao_fornecedor"."id_tipo_relacao" < "equipamento_fornecedor"."id_tipo_relacao"
Ref "garantias_ibfk_1":"equipamentos"."id_equipamento" < "garantias"."id_equipamento"
Ref "garantias_ibfk_2":"estados_garantia"."id_estado_garantia" < "garantias"."id_estado_garantia"
Ref "historico_ibfk_1":"utilizadores"."id_utilizador" < "historico"."id_utilizador"
Ref "lembretes_ibfk_1":"utilizadores"."id_utilizador" < "lembretes"."id_utilizador"
Ref "localizacoes_ibfk_1":"servicos"."id_servico" < "localizacoes"."id_servico"
Ref "password_resets_ibfk_1":"utilizadores"."id_utilizador" < "password_resets"."id_utilizador"