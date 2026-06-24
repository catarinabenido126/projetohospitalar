================================================================
MEDISYNC — Sistema de Gestão de Inventário Hospitalar
================================================================

Nome do projeto : MediSync
Nome do estudante: Catarina Benido
Número do estudante: 1241126
Unidade curricular: Sistemas de Informação e Bases de Dados Aplicados à Saúde (SIBDAS)
Curso: Licenciatura em Engenharia Biomédica (LEBIOM)
Ano letivo: 2025-2026

----------------------------------------------------------------
DESCRIÇÃO
----------------------------------------------------------------
O MediSync é uma aplicação web desenvolvida para apoiar a gestão
do inventário hospitalar de equipamentos médicos. Permite registar
e gerir equipamentos, fornecedores, localizações, componentes,
consumíveis, garantias, contratos e documentação técnica.

A aplicação é composta por duas áreas:
  - Área pública  — website institucional da empresa de software
  - Área privada  — sistema de gestão do inventário hospitalar

----------------------------------------------------------------
ESTRUTURA DE DIRETORIAS
----------------------------------------------------------------
medisync/
├── public/               Páginas públicas (sem autenticação)
│   └── index.php         Página pública principal
├── private/              Páginas privadas (requerem autenticação)
│   ├── index.php         Página inicial da área privada
│   ├── processa_login.php
│   └── views/
│       ├── dashboard/    Dashboard com indicadores
│       ├── equipamentos/ Lista, novo, editar, detalhes, apagar
│       ├── fornecedores/ Lista, novo, editar, detalhes, apagar
│       ├── localizacao/  Lista, novo, editar, apagar
│       ├── gestao/       Backoffice da área pública
│       └── historico/    Registo de eventos do sistema
├── login/
│   └── login.php         Formulário de autenticação
├── includes/             Ficheiros partilhados
│   ├── funcoes.php       Funções globais (sessão, histórico, AES)
│   ├── database.php      Ligação à base de dados via PDO
│   ├── config.php        Configurações (BD, chaves AES)
│   ├── header.php
│   ├── footer.php
│   ├── nav.php
│   └── sidebar.php
└── assets/               Recursos estáticos
    ├── css/
    │   └── 1241126.css   Estilos personalizados
    ├── js/
    ├── img/
    ├── bootstrap/
    └── fontawesome/

----------------------------------------------------------------
REQUISITOS
----------------------------------------------------------------
- PHP 8.0 ou superior
- MySQL 8.0 ou superior
- Servidor web Apache (XAMPP, MAMP ou equivalente)
- Browser moderno (Chrome, Firefox, Edge, Safari)

----------------------------------------------------------------
INSTALAÇÃO E EXECUÇÃO
----------------------------------------------------------------
1. Copiar a pasta medisync/ para o diretório do servidor web:
     Mac (MAMP)  : /Applications/MAMP/htdocs/sibdas/1241126/
     Windows (XAMPP): C:\xampp\htdocs\sibdas\1241126\

2. Importar a base de dados:
   - Abrir o HeidiSQL ou phpMyAdmin
   - Criar a base de dados db1241126 (se não existir)
   - Importar o ficheiro: database/medisync.sql

3. Verificar as configurações em includes/config.php:
     MYSQL_HOST     : vsgate-s1.dei.isep.ipp.pt
     MYSQL_PORT     : 10464
     MYSQL_DATABASE : db1241126
     MYSQL_USERNAME : 1241126

4. Iniciar o servidor web e aceder no browser:
     http://127.0.0.1/sibdas/1241126/medisync

----------------------------------------------------------------
CREDENCIAIS DE ACESSO
----------------------------------------------------------------
Perfil: Administrador
  Email   : admin@medisync.pt
  Password: Admin123
  Acesso  : Todas as funcionalidades (CRUD completo, arquivar,
             restaurar, gerir utilizadores e conteúdos públicos)

Perfil: Técnico
  Email   : tecnico@medisync.pt
  Password: Tecnico123
  Acesso  : Consulta e edição de equipamentos e fornecedores
             (sem permissão para arquivar ou restaurar)

Perfil: Profissional de Saúde
  Email   : saude@medisync.pt
  Password: Saude123
  Acesso  : Consulta de equipamentos, fornecedores e localizações
             (apenas leitura)

----------------------------------------------------------------
INSTRUÇÕES PARA TESTE
----------------------------------------------------------------
1. Aceder à área pública em:
     http://127.0.0.1/sibdas/1241126/medisync/public/index.php

2. Fazer login como Administrador em:
     http://127.0.0.1/sibdas/1241126/medisync/login/login.php

3. Funcionalidades a testar:
   a) Dashboard       — indicadores reais, modais ao clicar nos
                        cards, alertas de consumíveis em falta
   b) Equipamentos    — listar, criar (novo.php), editar, ver
                        detalhes com componentes e consumíveis
                        reais da BD, arquivar (soft delete)
   c) Fornecedores    — CRUD completo, exportação PDF/CSV/JSON
   d) Localizações    — CRUD completo, exportação PDF/CSV/JSON
   e) Gestão          — editar conteúdos da área pública,
                        ver mensagens de contacto recebidas
   f) Histórico       — registo de operações realizadas
   g) Logout          — terminar sessão em segurança

4. Testar controlo de acessos:
   - Fazer login com perfil Técnico e verificar que não aparece
     o botão de arquivar nos equipamentos
   - Fazer login com perfil Profissional de Saúde e verificar
     que só tem acesso a consulta

----------------------------------------------------------------
NOTAS ADICIONAIS
----------------------------------------------------------------
- A aplicação utiliza soft delete (ativo = 0) em vez de
  eliminação física dos registos
- Os emails dos utilizadores são armazenados encriptados na BD
  com AES_ENCRYPT e desencriptados com AES_DECRYPT no login
- As passwords são armazenadas com bcrypt (password_hash)
- Todas as páginas privadas verificam autenticação via sessão
- O histórico de operações é registado automaticamente nas
  principais acções (login, criação, edição, arquivo)
- Exportação de dados disponível em PDF (jsPDF), CSV e JSON
  nas listas de equipamentos, fornecedores e localizações

================================================================