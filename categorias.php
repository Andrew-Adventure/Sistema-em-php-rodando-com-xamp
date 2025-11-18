<?php
require_once 'conexao.php';

$id_editar = null;
$nome_editar = '';
$cor_editar = '';

// --- Lógica CRUD para Categorias ---

// 1. CREATE e UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $cor = trim($_POST['cor']);
    $id = $_POST['id'] ?? null;

    if (empty($nome)) {
        $_SESSION['mensagem'] = ['tipo' => 'erro', 'texto' => 'O nome da categoria é obrigatório!'];
    } else {
        try {
            if ($id) { // UPDATE
                $sql = "UPDATE categorias SET nome = :nome, cor = :cor WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id);
                $mensagem_sucesso = 'Categoria atualizada com sucesso!';
            } else { // CREATE
                $sql = "INSERT INTO categorias (nome, cor) VALUES (:nome, :cor)";
                $stmt = $pdo->prepare($sql);
                $mensagem_sucesso = 'Categoria cadastrada com sucesso!';
            }
            
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':cor', $cor);
            $stmt->execute();
            
            $_SESSION['mensagem'] = ['tipo' => 'sucesso', 'texto' => $mensagem_sucesso];
            header("Location: categorias.php");
            exit();

        } catch (PDOException $e) {
            $_SESSION['mensagem'] = ['tipo' => 'erro', 'texto' => 'Erro: ' . $e->getMessage()];
        }
    }
}

// 2. DELETE
if (isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        // Precisa garantir que não há tarefas vinculadas ou usar ON DELETE CASCADE no banco.
        // Aqui, faremos a exclusão direta (assumindo que o banco está configurado para permitir).
        $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $_SESSION['mensagem'] = ['tipo' => 'sucesso', 'texto' => 'Categoria excluída com sucesso!'];
        header("Location: categorias.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['mensagem'] = ['tipo' => 'erro', 'texto' => 'Erro ao excluir: Verifique se há tarefas usando esta categoria.'];
        header("Location: categorias.php");
        exit();
    }
}

// 3. Carregar dados para edição (READ específico)
if (isset($_GET['acao']) && $_GET['acao'] == 'editar' && isset($_GET['id'])) {
    $id_editar = $_GET['id'];
    $stmt = $pdo->prepare("SELECT id, nome, cor FROM categorias WHERE id = :id");
    $stmt->bindParam(':id', $id_editar);
    $stmt->execute();
    $cat_editar = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($cat_editar) {
        $nome_editar = $cat_editar['nome'];
        $cor_editar = $cat_editar['cor'];
    }
}

// 4. READ (Listagem de Categorias)
$stmt_listagem = $pdo->query("SELECT id, nome, cor FROM categorias ORDER BY nome");
$categorias_lista = $stmt_listagem->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Categorias - CRUD PHP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>🗃️ Gerenciar Categorias</h1>
        <nav>
            <a href="index.php" class="btn-secondary">Voltar para a Lista</a>
        </nav>
    </header>

    <main>
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

        <section class="formulario-cadastro">
            <h2><?= $id_editar ? 'Editar Categoria' : 'Nova Categoria' ?></h2>
            <form id="formCategoria" method="POST" action="categorias.php">
                <input type="hidden" name="id" value="<?= $id_editar ?>">
                
                <div class="form-group">
                    <label for="nome">Nome da Categoria *</label>
                    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($nome_editar) ?>" required>
                    <p class="erro-validacao" id="erroNome">O nome é obrigatório!</p>
                </div>
                
                <div class="form-group">
                    <label for="cor">Cor de Identificação (Hex)</label>
                    <input type="color" id="cor" name="cor" value="<?= htmlspecialchars($cor_editar ?: '#3498db') ?>">
                </div>

                <button type="submit" class="btn-form"><?= $id_editar ? 'Atualizar' : 'Cadastrar' ?></button>
                <?php if ($id_editar): ?>
                    <a href="categorias.php" class="btn-acao editar" style="background-color:#95a5a6; margin-left:10px;">Cancelar Edição</a>
                <?php endif; ?>
            </form>
        </section>

        <section class="lista-categorias" style="margin-top: 40px;">
            <h2>Lista de Categorias</h2>
            
            <?php if (count($categorias_lista) > 0): ?>
                <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden;">
                    <thead>
                        <tr style="background-color: #ecf0f1;">
                            <th style="padding: 12px; text-align: left;">ID</th>
                            <th style="padding: 12px; text-align: left;">Nome</th>
                            <th style="padding: 12px; text-align: left;">Cor</th>
                            <th style="padding: 12px; text-align: center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categorias_lista as $cat): ?>
                            <tr style="border-bottom: 1px solid #f4f7f6;">
                                <td style="padding: 12px;"><?= $cat['id'] ?></td>
                                <td style="padding: 12px;"><?= htmlspecialchars($cat['nome']) ?></td>
                                <td style="padding: 12px;">
                                    <span style="display: inline-block; width: 20px; height: 20px; border-radius: 50%; background-color: <?= htmlspecialchars($cat['cor']) ?>; border: 1px solid #ccc;"></span>
                                    <small><?= htmlspecialchars($cat['cor']) ?></small>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <a href="categorias.php?acao=editar&id=<?= $cat['id'] ?>" class="btn-acao editar">Editar</a>
                                    <a href="categorias.php?acao=excluir&id=<?= $cat['id'] ?>" class="btn-acao excluir" onclick="return confirm('Deseja realmente excluir esta categoria? Isso pode afetar tarefas vinculadas.')">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nenhuma categoria cadastrada.</p>
            <?php endif; ?>
        </section>
    </main>
    
    <footer>
        <p>&copy; 2025 Sistema de Tarefas | Desenvolvimento Web PROVA AV</p>
    </footer>

    <form id="formCategoria" method="POST" action="categorias.php" onsubmit="return validarFormulario(event)">
    </form>
    <script src="script.js"></script>
</body>
</html>