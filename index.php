<?php
require_once 'conexao.php';

// Busca todas as tarefas com o nome da categoria relacionada
$sql = "SELECT t.*, c.nome as categoria 
        FROM tarefas t 
        JOIN categorias c ON t.categoria_id = c.id 
        ORDER BY t.data_criacao DESC";
$stmt = $pdo->query($sql);
$tarefas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lógica para excluir a tarefa (DELETE)
if (isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM tarefas WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $_SESSION['mensagem'] = ['tipo' => 'sucesso', 'texto' => 'Tarefa excluída com sucesso!'];
        header("Location: index.php"); // Redireciona para evitar reenvio
        exit();
    } catch (PDOException $e) {
        $_SESSION['mensagem'] = ['tipo' => 'erro', 'texto' => 'Erro ao excluir: ' . $e->getMessage()];
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Tarefas - CRUD PHP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>📋 Gerenciador de Tarefas</h1>
        <nav>
            <a href="cadastrar.php" class="btn-primary">+ Nova Tarefa</a>
            <a href="categorias.php" class="btn-secondary">Gerenciar Categorias</a>
        </nav>
    </header>

    <main>
        <?php 
        // JavaScript/Interação Visual: Mensagens Dinâmicas
        if (isset($_SESSION['mensagem'])): 
            $msg = $_SESSION['mensagem'];
            $class = $msg['tipo'] == 'sucesso' ? 'alerta-sucesso' : 'alerta-erro';
        ?>
            <div class="<?= $class ?>">
                <?= htmlspecialchars($msg['texto']) ?>
            </div>
        <?php 
            unset($_SESSION['mensagem']); 
        endif; 
        ?>

        <section class="lista-tarefas">
            <h2>Tarefas Pendentes e Concluídas</h2>
            
            <?php if (count($tarefas) > 0): ?>
                <?php foreach ($tarefas as $tarefa): ?>
                    <article class="tarefa-item status-<?= strtolower($tarefa['status']) ?>">
                        <div class="tarefa-header">
                            <h3><?= htmlspecialchars($tarefa['titulo']) ?> 
                                <span class="tag-categoria"><?= htmlspecialchars($tarefa['categoria']) ?></span>
                            </h3>
                            <span class="status-badge"><?= htmlspecialchars($tarefa['status']) ?></span>
                        </div>
                        <p><?= nl2br(htmlspecialchars($tarefa['descricao'])) ?></p>
                        <small>Criado em: <?= date('d/m/Y H:i', strtotime($tarefa['data_criacao'])) ?></small>
                        <div class="tarefa-acoes">
                            <a href="editar.php?id=<?= $tarefa['id'] ?>" class="btn-acao editar">Editar</a>
                            <a href="#" class="btn-acao excluir" data-id="<?= $tarefa['id'] ?>" onclick="excluirTarefa(this, event)">Excluir</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="sem-tarefas">🎉 Nenhuma tarefa cadastrada. Crie uma agora!</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Sistema de Tarefas | Desenvolvimento Web PROVA AV</p>
    </footer>

    <script src="script.js"></script> 
</body>
</html>