<?php
// Configurações do Banco de Dados
$host = 'localhost'; // Geralmente 'localhost'
$usuario = 'root';   // Usuário padrão do XAMPP/WAMP
$senha = '';         // Senha padrão (deixe vazio se não definiu)
$banco = 'todolist'; // Nome do banco que você deve criar

try {
    // Cria uma instância do PDO (PHP Data Objects) para a conexão
    $pdo = new PDO("mysql:host=$host;dbname=$banco;charset=utf8", $usuario, $senha);
    
    // Define o modo de erro para lançar exceções em caso de falha
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Exemplo de como inicializar uma mensagem de sucesso/erro (opcional)
    // if (!isset($_SESSION)) { session_start(); }
    // $_SESSION['status_conexao'] = 'Conexão bem-sucedida!';

} catch (PDOException $e) {
    // Em caso de erro, exibe uma mensagem e encerra o script
    die("❌ Erro na conexão com o banco de dados: " . $e->getMessage());
}

// Inicia a sessão para usar mensagens de feedback (opcional, mas recomendado)
if (!isset($_SESSION)) {
    session_start();
}
?>