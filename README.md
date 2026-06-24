# MEDISYNC — Sistema de Gestão de Inventário Hospitalar

* **Nome do projeto:** MediSync
* **Nome do estudante:** Catarina Benido
* **Número do estudante:** 1241126
* **Unidade curricular:** Sistemas de Informação e Bases de Dados Aplicados à Saúde (SIBDAS)
* **Curso:** Licenciatura em Engenharia Biomédica (LEBIOM)
* **Ano letivo:** 2025-2026

---

## Descrição
O MediSync é uma aplicação web desenvolvida para apoiar a gestão do inventário hospitalar de equipamentos médicos. Permite registar e gerir equipamentos, fornecedores, localizações, componentes, consumíveis, garantias, contratos e documentação técnica.

A aplicação é composta por duas áreas:
* **Área pública:** Website institucional da empresa de software
* **Área privada:** Sistema de gestão do inventário hospitalar

---

## Estrutura de Diretiorias
A estrutura do projeto deve estar organizada da seguinte forma dentro da diretoria raiz do servidor web local:

```text
sibdas/
└── 1241126/
    └── medisync/
        ├── commits.txt         # Registo obrigatório de commits Git
        ├── README.md           # Este ficheiro de instruções
        ├── public/             # Páginas públicas (sem autenticação)
        │   └── index.php       # Página pública principal
        ├── private/            # Páginas privadas (requerem autenticação)
        │   ├── index.php       # Página inicial da área privada
        │   ├── processa_login.php
        │   └── views/          # Módulos de gestão (dashboard, equipamentos, etc.)
        ├── login/
        │   └── login.php       # Formulário de autenticação
        ├── includes/           # Ligações à BD, funções globais e configurações
        └── assets/             # Recursos estáticos (CSS, JS, Imagens)