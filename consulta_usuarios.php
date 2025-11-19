<?php
// 1. Controle de Acesso: Apenas Master pode entrar.
require_once 'php/auth_master.php'; 
require_once 'php/conexao.php'; // Inclui a conexão com o banco

// ----------------------------------------------------
// 2. Lógica de Busca e Filtro
// ----------------------------------------------------

$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$usuarios = [];
$error_search = '';

try {
    // Monta a consulta SQL base para buscar usuários 'comum'
    $sql = "SELECT id, name, cpf, email, login, is_active FROM users WHERE profile = 'comum'";
    $params = [];

    // Adiciona a cláusula de pesquisa se um termo foi fornecido
    if (!empty($search_query)) {
        // Pesquisa por substring (pedaço do nome do usuário)
        $sql .= " AND name LIKE ?";
        $params[] = '%' . $search_query . '%';
    }

    // Ordena por nome
    $sql .= " ORDER BY name ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_search = "Erro ao carregar usuários: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.1">
    <title>Consulta de Usuários - Master</title>
    <link rel="stylesheet" href="css/main.css" /> 
    <link rel="icon" href="imagens/favicon.ico.ico" type="image/x-icon" />
    <style>
        /* Estilos básicos para a tabela, se não estiverem no main.css */
        .content { padding: 80px 20px 20px; max-width: 1200px; margin: 0 auto; }
        .user-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .user-table th, .user-table td { padding: 10px; border: 1px solid #444; text-align: left; }
        .user-table th { background-color: #f0a92d; color: #333; }
        .user-table tr:nth-child(even) { background-color: #222; }
        .search-form { margin-bottom: 20px; display: flex; gap: 10px; }
        .search-form input[type="text"] { padding: 10px; width: 300px; border-radius: 5px; border: 1px solid #444; }
        .search-form button { padding: 10px 15px; background-color: #f0a92d; border: none; border-radius: 5px; cursor: pointer; color: #333; font-weight: bold; }
        .delete-btn { background-color: #e74c3c; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
        .delete-btn:hover { background-color: #c0392b; }
    </style>
</head>
<body>
    
<?php include 'navbar_master.php'; ?> <div class="content">
    <h2>Consulta de Usuários Comuns</h2>
    
    <form method="GET" action="consulta_usuarios.php" class="search-form">
        <input type="text" name="search" placeholder="Pesquisar por nome do usuário..." value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit">Buscar</button>
        <a href="consulta_usuarios.php" class="search-form button" style="background-color: #555; text-decoration: none; color: white; display: flex; align-items: center;">Limpar Busca</a>
    </form>

    <?php if (!empty($_SESSION['message'])): ?>
        <p style="color: green; text-align: center; margin-bottom: 20px;"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
    <?php endif; ?>

    <?php if ($error_search): ?>
        <p style="color: red; text-align: center; margin-bottom: 20px;"><?php echo $error_search; ?></p>
    <?php elseif (empty($usuarios)): ?>
        <p style="text-align: center;">Nenhum usuário comum encontrado<?php echo !empty($search_query) ? ' para a busca "' . htmlspecialchars($search_query) . '".' : '.'; ?></p>
    <?php else: ?>
        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Login</th>
                    <th>CPF</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['login']); ?></td>
                    <td><?php echo htmlspecialchars($user['cpf']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo $user['is_active'] ? 'Ativo' : 'Inativo'; ?></td>
                    <td>
                        <form method="POST" action="php/processo_exclusao.php" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja EXCLUIR o usuário <?php echo htmlspecialchars($user['name']); ?>? Esta ação é irreversível.');">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                            <button type="submit" class="delete-btn">Excluir</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="js/script.js"></script>
</body>
</html>