/**
 * MediSync — Sistema de Gestão de Inventário Hospitalar
 * Ficheiro de scripts globais — Catarina Benido, 1241126
 *
 * Nota: A maioria das funções JavaScript da aplicação encontra-se
 * integrada diretamente nas páginas PHP onde são utilizadas
 * (lista.php, novo.php, editar.php, detalhes.php, dashboard.php, etc.),
 * seguindo o padrão adotado nas aulas laboratoriais.
 * Este ficheiro centraliza as funções utilitárias de uso global.
 */

'use strict';

/* ============================================================
   1. TOAST — Notificações visuais de feedback ao utilizador
      Utilizado em todas as páginas de listagem após operações
      de criação, edição, arquivo e restauro de registos.
   ============================================================ */

/**
 * Mostra uma notificação Bootstrap Toast no canto do ecrã.
 * @param {string} mensagem - Texto a apresentar
 * @param {string} tipo     - 'success' (verde) ou 'danger' (vermelho)
 */
function mostrarToast(mensagem, tipo = 'success') {
    const el = document.getElementById('toastApp');
    if (!el) return;
    document.getElementById('toastMensagem').textContent = mensagem;
    el.className = 'toast align-items-center border-0 text-white '
        + (tipo === 'success' ? 'bg-success' : 'bg-danger');
    new bootstrap.Toast(el, { delay: 4000 }).show();
}

/* ============================================================
   2. URL PARAMS — Leitura de parâmetros de query string
      Após submissão de formulários, a página redireciona com
      ?criado=1, ?guardado=1, etc. para acionar o toast certo.
   ============================================================ */

/**
 * Lê parâmetros da URL e mostra o toast correspondente.
 * Remove os parâmetros do histórico após leitura para evitar
 * que o toast reapareça ao atualizar a página.
 */
function processarMensagensURL() {
    const p = new URLSearchParams(window.location.search);

    if (p.get('criado')     === '1') mostrarToast('Registo criado com sucesso.');
    if (p.get('guardado')   === '1') mostrarToast('Alterações guardadas com sucesso.');
    if (p.get('desativado') === '1') mostrarToast('Registo arquivado com sucesso.');
    if (p.get('restaurado') === '1') mostrarToast('Registo restaurado com sucesso.');
    if (p.get('erro')       === '1') mostrarToast('Ocorreu um erro. Tente novamente.', 'danger');

    // Limpa os parâmetros da barra de endereço sem recarregar a página
    if (p.toString()) {
        window.history.replaceState({}, document.title, window.location.pathname);
    }
}

/* ============================================================
   3. TOOLTIPS — Ativação global dos tooltips Bootstrap
      Aplicado a todos os elementos com data-bs-toggle="tooltip"
      presentes nas tabelas de listagem (botões de ação).
   ============================================================ */

/**
 * Inicializa todos os tooltips Bootstrap na página.
 */
function inicializarTooltips() {
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
        .forEach(el => new bootstrap.Tooltip(el));
}

/* ============================================================
   4. EXPORTAÇÃO CSV — Geração de ficheiro CSV a partir de
      uma tabela HTML. Utilizado nas listas de equipamentos,
      fornecedores e localizações.
   ============================================================ */

/**
 * Exporta os dados de uma tabela HTML para ficheiro CSV.
 * @param {string} tabelaId  - ID do elemento <table>
 * @param {string} nomeFicheiro - Nome do ficheiro a descarregar
 */
function exportarCSV(tabelaId, nomeFicheiro = 'exportacao.csv') {
    const tabela = document.getElementById(tabelaId);
    if (!tabela) return;

    const linhas = Array.from(tabela.querySelectorAll('tr'));
    const csv = linhas.map(linha => {
        const celulas = Array.from(linha.querySelectorAll('th, td'));
        // Ignora a última coluna se for de ações (botões)
        return celulas.slice(0, -1)
            .map(c => '"' + c.innerText.replace(/"/g, '""').trim() + '"')
            .join(',');
    }).join('\n');

    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = nomeFicheiro;
    a.click();
    URL.revokeObjectURL(url);
}

/* ============================================================
   5. EXPORTAÇÃO JSON — Serialização de dados para JSON.
      Complementa a exportação CSV nas páginas de listagem.
   ============================================================ */

/**
 * Exporta os dados de uma tabela HTML para ficheiro JSON.
 * @param {string} tabelaId     - ID do elemento <table>
 * @param {string} nomeFicheiro - Nome do ficheiro a descarregar
 */
function exportarJSON(tabelaId, nomeFicheiro = 'exportacao.json') {
    const tabela = document.getElementById(tabelaId);
    if (!tabela) return;

    const cabecalhos = Array.from(tabela.querySelectorAll('thead th'))
        .slice(0, -1)
        .map(th => th.innerText.trim());

    const linhas = Array.from(tabela.querySelectorAll('tbody tr'));
    const dados  = linhas.map(linha => {
        const celulas = Array.from(linha.querySelectorAll('td')).slice(0, -1);
        const obj = {};
        cabecalhos.forEach((h, i) => {
            obj[h] = celulas[i] ? celulas[i].innerText.trim() : '';
        });
        return obj;
    });

    const blob = new Blob([JSON.stringify(dados, null, 2)], { type: 'application/json' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = nomeFicheiro;
    a.click();
    URL.revokeObjectURL(url);
}

/* ============================================================
   6. INICIALIZAÇÃO — Executado quando o DOM está pronto
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {
    inicializarTooltips();
    processarMensagensURL();
});