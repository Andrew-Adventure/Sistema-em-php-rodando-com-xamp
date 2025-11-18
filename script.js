/**
 * Funções JavaScript para o Sistema de Gerenciamento de Tarefas.
 * Inclui validação de formulários e exclusão via AJAX para interatividade.
 */

// --- Funções de Validação de Formulário (Requisito JS) ---

/**
 * Valida o formulário antes do envio (para cadastrar e editar).
 * Impede o envio se campos obrigatórios (título e categoria) estiverem vazios.
 * @param {Event} event - O evento de submissão do formulário.
 * @returns {boolean} True se válido, False se inválido (e impede o envio).
 */
function validarFormulario(event) {
    let isValid = true;
    
    // 1. Validação do Título
    const tituloInput = document.getElementById('titulo');
    const tituloErro = document.getElementById('erroTitulo');
    
    if (tituloInput && tituloInput.value.trim() === '') {
        if (tituloErro) tituloErro.style.display = 'block';
        isValid = false;
    } else {
        if (tituloErro) tituloErro.style.display = 'none';
    }
    
    // 2. Validação da Categoria
    const categoriaInput = document.getElementById('categoria_id');
    const categoriaErro = document.getElementById('erroCategoria');

    if (categoriaInput && categoriaInput.value === '') {
        if (categoriaErro) categoriaErro.style.display = 'block';
        isValid = false;
    } else {
        if (categoriaErro) categoriaErro.style.display = 'none';
    }

    if (!isValid) {
        event.preventDefault(); // Impede o envio do formulário
        alert('Por favor, preencha todos os campos obrigatórios (Título e Categoria).');
    }
    return isValid;
}


// --- Funções de Interatividade e AJAX (Requisito JS) ---

/**
 * Exibe uma mensagem dinâmica de feedback (sucesso/erro).
 * @param {string} tipo - 'sucesso' ou 'erro'.
 * @param {string} texto - A mensagem a ser exibida.
 */
function exibirMensagem(tipo, texto) {
    const main = document.querySelector('main');
    const alerta = document.createElement('div');
    alerta.className = tipo === 'sucesso' ? 'alerta-sucesso' : 'alerta-erro';
    alerta.innerHTML = texto;
    
    // Insere a mensagem logo após o cabeçalho principal
    const firstSection = main.querySelector('section');
    if (firstSection) {
        main.insertBefore(alerta, firstSection);
    } else {
        main.prepend(alerta); // Caso não haja seção
    }

    // Remove a mensagem após 4 segundos (Interatividade Visual)
    setTimeout(() => {
        alerta.remove();
    }, 4000);
}

/**
 * Exclui uma tarefa usando AJAX (Fetch API) sem recarregar a página.
 * @param {HTMLElement} elemento - O link/botão que disparou a ação.
 * @param {Event} event - O evento de clique.
 */
function excluirTarefa(elemento, event) {
    event.preventDefault(); 

    const id = elemento.getAttribute('data-id');
    
    // Confirmação (Interatividade Visual)
    if (!confirm("Tem certeza que deseja EXCLUIR a tarefa #" + id + "? Esta ação não pode ser desfeita.")) {
        return;
    }

    const formData = new FormData();
    formData.append('id', id);

    // Fetch API (AJAX) para enviar a requisição de exclusão
    fetch('excluir_ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            // Lança um erro com o status HTTP para o bloco catch
            return response.json().then(err => { throw new Error(err.mensagem || 'Erro HTTP ' + response.status); });
        }
        return response.json();
    })
    .then(data => {
        if (data.sucesso) {
            exibirMensagem('sucesso', data.mensagem);
            
            // Remove o item da lista (Interatividade Visual)
            const tarefaItem = elemento.closest('.tarefa-item');
            if (tarefaItem) {
                tarefaItem.style.opacity = 0;
                tarefaItem.style.transition = 'opacity 0.5s ease-out';
                setTimeout(() => tarefaItem.remove(), 500);
            }
        } else {
            exibirMensagem('erro', data.mensagem);
        }
    })
    .catch(error => {
        console.error('Erro na requisição AJAX:', error);
        exibirMensagem('erro', 'Ocorreu um erro na exclusão: ' + error.message);
    });
}