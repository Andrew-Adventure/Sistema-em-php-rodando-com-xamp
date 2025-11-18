# Sistema-em-php-rodando-com-xamp
Trabalho de Desenvolvimento web envolvendo as tecnologias de: html, css, JavaScript e php. Sendo o mesmo utilizado em banco de dados local em Xampp.


🚀 README.md: Sistema de Gerenciamento de Tarefas (CRUD PHP/MySQL)
Este é um projeto simples de Sistema de Gerenciamento de Tarefas (To-Do List) que demonstra o domínio das tecnologias fundamentais do desenvolvimento web: HTML5, CSS3, JavaScript, PHP e MySQL. Ele implementa as quatro operações básicas de CRUD (Create, Read, Update, Delete) em uma entidade principal (tarefas) e possui uma tabela auxiliar (categorias).

⚙️ Pré-requisitos
Para executar este projeto localmente, você precisará de um ambiente de servidor web que suporte PHP e MySQL.

Servidor Web: Apache (geralmente incluído em pacotes como XAMPP ou WAMP).

PHP: Versão 7.x ou superior.

MySQL: Para gerenciar o banco de dados.

Pacote Local: Recomendamos usar XAMPP, WAMP (Windows) ou MAMP (macOS) para configurar o ambiente rapidamente.

🛠️ Instalação Local e Configuração
Siga os passos abaixo para clonar o repositório e configurar o ambiente:

1. Clonar o Repositório
Abra seu terminal ou prompt de comando e execute:

Bash

git clone [URL_DO_SEU_REPOSITORIO]
cd sistema-de-tarefas
2. Configurar o Servidor Local
Mova a pasta sistema-de-tarefas para o diretório de projetos do seu servidor web local:

XAMPP: Mova para C:\xampp\htdocs\

WAMP: Mova para C:\wamp\www\

3. Configurar o Banco de Dados
A. Iniciar o Servidor
Inicie os módulos Apache e MySQL no painel de controle do seu XAMPP/WAMP.

B. Criar o Banco de Dados
Acesse o phpMyAdmin no seu navegador (geralmente em http://localhost/phpmyadmin).

Crie um novo banco de dados chamado todolist.

C. Criar as Tabelas
Execute os seguintes comandos SQL na aba SQL do banco de dados todolist:

SQL

-- Estrutura da Tabela Auxiliar: categorias
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cor VARCHAR(7) DEFAULT '#3498db'
);

-- Estrutura da Tabela Principal: tarefas
CREATE TABLE tarefas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pendente', 'Concluída') NOT NULL DEFAULT 'Pendente',
    categoria_id INT,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Insere algumas categorias iniciais (Opcional)
INSERT INTO categorias (nome, cor) VALUES 
('Trabalho', '#e74c3c'), 
('Pessoal', '#3498db'), 
('Estudos', '#2ecc71');
4. Ajustar a Conexão PHP
Verifique o arquivo conexao.php e garanta que as credenciais do MySQL correspondem às configurações padrão do seu ambiente local.

PHP

// Arquivo: conexao.php
<?php
// Geralmente, estas são as configurações padrão
$host = 'localhost'; 
$usuario = 'root';   
$senha = '';         // Deixe vazio se não houver senha
$banco = 'todolist'; 
// ...
?>
🏃 Como Usar
Após a instalação, acesse o projeto no seu navegador:

http://localhost/sistema-de-tarefas/
Funcionalidades Principais:
Listagem (index.php): Visualiza todas as tarefas, seus status e categorias.

Cadastrar (cadastrar.php): Cria novas tarefas (Operação Create).

Editar (editar.php): Modifica tarefas existentes e seu status (Operação Update).

Excluir: Ação direta na listagem (Operação Delete).

Gerenciar Categorias (categorias.php): Painel administrativo para criar, editar e excluir as categorias auxiliares.

📄 Estrutura de Arquivos
sistema-de-tarefas/
├── index.php             # Página principal: READ e DELETE de tarefas.
├── cadastrar.php         # Página de formulário: CREATE de tarefas.
├── editar.php            # Página de formulário: UPDATE de tarefas.
├── categorias.php        # Painel administrativo: CRUD da tabela auxiliar `categorias`.
├── conexao.php           # Arquivo central para a conexão com o MySQL.
└── style.css             # Estilização CSS3 (incluindo responsividade).
