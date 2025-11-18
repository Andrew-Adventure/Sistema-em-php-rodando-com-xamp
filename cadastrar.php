<?php
require_once 'conexao.php';

// Busca as categorias para o <select>
$stmt_categorias = $pdo->query("SELECT id, nome FROM categorias ORDER BY nome");
$categorias = $stmt_categorias->fetchAll(PDO::FETCH_ASSOC);

// Processamento do Formulário (CREATE)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    $categoria_id = $_POST['categoria_id'];
    $data_criacao = date('Y-m-d H:i:s');
    $status = 'Pendente'; 

    // Validação de servidor (além da validação JS)
    if (empty($titulo) || empty($categoria_id)) {
        $_SESSION['mensagem'] = ['tipo' => 'erro', 'texto' => 'O título e a categoria são obrigatórios!'];
        // Mantém os dados preenchidos no formulário (opcional)
    } else {
        try {
            // Separação da Lógica: PHP para processamento e persistência de dados
            $sql = "INSERT INTO tarefas (titulo, descricao, categoria_id, data_criacao, status) 
                    VALUES (:titulo, :descricao, :categoria_id, :data_criacao, :status)";
            $stmt = $pdo->prepare($sql);
            
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':categoria_id', $categoria_id);
            $stmt->bindParam(':data_criacao', $data_criacao);
            $stmt->bindParam(':status', $status);
            
            $stmt->execute();
            
            $_SESSION['mensagem'] = ['tipo' => 'sucesso', 'texto' => 'Tarefa cadastrada com sucesso!'];
            header("Location: index.php"); // Redireciona para a lista
            exit();

        } catch (PDOException $e) {
            $_SESSION['mensagem'] = ['tipo' => 'erro', 'texto' => 'Erro ao cadastrar: ' . $e->getMessage()];
        }
    }
}

// Valores padrão para o formulário (se o envio falhou)
$titulo = $_POST['titulo'] ?? '';
$descricao = $_POST['descricao'] ?? '';
$categoria_id = $_POST['categoria_id'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Tarefa - CRUD PHP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>✏️ Cadastrar Nova Tarefa</h1>
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

            <form id="formCadastro" method="POST" action="cadastrar.php">
                <div class="form-group">
                    <label for="titulo">Título da Tarefa *</label>
                    <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($titulo) ?>" required>
                    <p class="erro-validacao" id="erroTitulo">O título é obrigatório!</p>
                </div>
                
                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="5"><?= htmlspecialchars($descricao) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="categoria_id">Categoria *</label>
                    <select id="categoria_id" name="categoria_id" required>
                        <option value="">Selecione uma Categoria</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($categoria_id == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="erro-validacao" id="erroCategoria">A categoria é obrigatória!</p>
                </div>

                <button type="submit" class="btn-form">Cadastrar Tarefa</button>
            </form>
        </section>
    </main>
    
    <footer>
        <p>&copy; 2025 Sistema de Tarefas | Desenvolvimento Web PROVA AV</p>
    </footer>

    <form id="formCadastro" method="POST" action="cadastrar.php" onsubmit="return validarFormulario(event)">
    </form>
    <script src="script.js"></script>
</body>
</html>