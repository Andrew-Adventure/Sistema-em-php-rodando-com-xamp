<?php
require_once 'conexao.php';

// 1. Verifica se o ID foi fornecido (segurança)
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensagem'] = ['tipo' => 'erro', 'texto' => 'ID de tarefa inválido.'];
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Busca as categorias para o <select>
$stmt_categorias = $pdo->query("SELECT id, nome FROM categorias ORDER BY nome");
$categorias = $stmt_categorias->fetchAll(PDO::FETCH_ASSOC);

$tarefa = [];

// 2. Processamento do Formulário (UPDATE)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    $categoria_id = $_POST['categoria_id'];
    $status = $_POST['status']; 

    if (empty($titulo) || empty($categoria_id)) {
        $_SESSION['mensagem'] = ['tipo' => 'erro', 'texto' => 'O título e a categoria são obrigatórios!'];
    } else {
        try {
            $sql = "UPDATE tarefas SET titulo = :titulo, descricao = :descricao, 
                    categoria_id = :categoria_id, status = :status 
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':categoria_id', $categoria_id);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id);
            
            $stmt->execute();
            
            $_SESSION['mensagem'] = ['tipo' => 'sucesso', 'texto' => 'Tarefa atualizada com sucesso!'];
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['mensagem'] = ['tipo' => 'erro', 'texto' => 'Erro ao atualizar: ' . $e->getMessage()];
            // Recarrega os dados para manter o formulário preenchido em caso de erro
            // Fallthrough para a busca de dados abaixo
        }
    }
}

// 3. Busca os dados atuais da tarefa (mesmo após erro de POST ou primeira carga GET)
try {
    $stmt = $pdo->prepare("SELECT * FROM tarefas WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $tarefa = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$tarefa) {
        $_SESSION['mensagem'] = ['tipo' => 'erro', 'texto' => 'Tarefa não encontrada.'];
        header("Location: index.php");
        exit();
    }

} catch (PDOException $e) {
    $_SESSION['mensagem'] = ['tipo' => 'erro', 'texto' => 'Erro ao buscar dados: ' . $e->getMessage()];
    header("Location: index.php");
    exit();
}

// Se o POST falhou, usa os dados do POST para manter o preenchimento. Caso contrário, usa os dados do banco.
$titulo_form = $_POST['titulo'] ?? $tarefa['titulo'];
$descricao_form = $_POST['descricao'] ?? $tarefa['descricao'];
$categoria_id_form = $_POST['categoria_id'] ?? $tarefa['categoria_id'];
$status_form = $_POST['status'] ?? $tarefa['status'];

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarefa - CRUD PHP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>🔄 Editar Tarefa #<?= $id ?></h1>
        <nav>
            <a href="index.php" class="btn-primary">Voltar para a Lista</a>
        </nav>
    </header>

    <main>
        <section class="formulario-cadastro">
            <?php 
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
            
            <form id="formEdicao" method="POST" action="editar.php?id=<?= $id ?>">
                <div class="form-group">
                    <label for="titulo">Título da Tarefa *</label>
                    <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($titulo_form) ?>" required>
                    <p class="erro-validacao" id="erroTitulo">O título é obrigatório!</p>
                </div>
                
                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="5"><?= htmlspecialchars($descricao_form) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="categoria_id">Categoria *</label>
                    <select id="categoria_id" name="categoria_id" required>
                        <option value="">Selecione uma Categoria</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($categoria_id_form == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="erro-validacao" id="erroCategoria">A categoria é obrigatória!</p>
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="Pendente" <?= ($status_form == 'Pendente') ? 'selected' : '' ?>>Pendente</option>
                        <option value="Concluída" <?= ($status_form == 'Concluída') ? 'selected' : '' ?>>Concluída</option>
                    </select>
                </div>

                <button type="submit" class="btn-form">Atualizar Tarefa</button>
            </form>
        </section>
    </main>
    
    <footer>
        <p>&copy; 2025 Sistema de Tarefas | Desenvolvimento Web PROVA AV</p>
    </footer>

    <form id="formEdicao" method="POST" action="editar.php?id=<?= $id ?>" onsubmit="return validarFormulario(event)">
    </form>
    <script src="script.js"></script>
</body>
</html>