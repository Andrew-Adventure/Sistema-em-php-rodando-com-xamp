<?php
require_once 'conexao.php';

// Define o cabeçalho como JSON para a resposta AJAX
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método inválido ou ID ausente.']);
    exit();
}

$id = $_POST['id'];

try {
    // Tenta deletar a tarefa
    $stmt = $pdo->prepare("DELETE FROM tarefas WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Sucesso na exclusão
        echo json_encode(['sucesso' => true, 'mensagem' => 'Tarefa excluída com sucesso!']);
    } else {
        // Tarefa não encontrada
        http_response_code(404);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Tarefa não encontrada ou já excluída.']);
    }

} catch (PDOException $e) {
    // Erro no banco de dados
    http_response_code(500); // Internal Server Error
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao excluir no banco de dados: ' . $e->getMessage()]);
}
?>