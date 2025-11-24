<?php
// 1. Controle de Acesso: Apenas Master pode entrar.
require_once 'php/auth_master.php'; 
require_once 'conexao.php'; // Inclui a conexão com o banco de dados

// ----------------------------------------------------
// 2. Lógica de Busca e Filtro
// ----------------------------------------------------

$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$usuarios = [];
$error_search = '';

try {
    // Consulta todos os usuários, Master e Comum, para que o Master possa gerenciar
    $sql = "SELECT id, name, cpf, email, login, is_active, profile FROM users WHERE 1=1";
    $params = [];

    if (!empty($search_query)) {
        // Pesquisa por substring (pedaço do nome do usuário)
        $sql .= " AND name LIKE ?";
        $params[] = '%' . $search_query . '%';
    }

    // Ordena por perfil (Master primeiro) e nome
    $sql .= " ORDER BY profile DESC, name ASC"; 
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_search = "Erro ao carregar usuários: " . $e->getMessage();
}

// Mensagens de sucesso/erro após ações de exclusão/alteração de perfil
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
unset($_SESSION['success_message']);

$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null;
unset($_SESSION['error_message']);

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
        /* CORREÇÃO DO CSS INLINE: Centralização, Fundo e Layout */
        .content { padding: 100px 20px 20px; max-width: 1200px; margin: 0 auto; color: white; }
        .content h2 { 
            text-align: center; 
            color: white; 
            font-size: 3em; 
            margin: 0 auto 30px auto; 
            font-family: 'nulshock', sans-serif; 
            white-space: pre-wrap;
        }
        
        /* CORREÇÃO PARA GARANTIR O FUNDO TOTAL */
        html { height: 100%; } 
        body { min-height: 100vh; background-attachment: fixed; } 
        
        /* Estilos da Tabela e Filtro */
        .search-form { margin-bottom: 20px; display: flex; gap: 10px; justify-content: center; align-items: center; }
        .search-form input[type="text"] { padding: 10px; width: 300px; border-radius: 5px; border: 1px solid #444; background-color: #333; color: white; }
        .search-form button { padding: 10px 15px; background-color: #f0a92d; border: none; border-radius: 5px; cursor: pointer; color: #333; font-weight: bold; }
        .user-table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: #1f1e1e; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4); }
        .user-table th, .user-table td { padding: 12px; border: 1px solid #444; text-align: left; }
        .user-table th { background-color: #f0a92d; color: #333; }
        .user-table td { color: white; }
        .user-table tr:nth-child(even) { background-color: #222; }
        .user-table tr:hover { background-color: #333; }
        .delete-btn { background-color: #e74c3c; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; margin-left: 10px; }
        .promote-link { color: #2ecc71; font-weight: bold; text-decoration: none; }
        .demote-link { color: #f0a92d; font-weight: bold; text-decoration: none; }
        .search-form a { padding: 10px 15px; background-color: #555; border-radius: 5px; color: white; font-weight: bold; text-decoration: none; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>
    
<div class="nav-container">
    <nav>
        <div class="logo">
            <img src="imagens/logo.svg" alt="logo" />
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="sobre.php">Sobre</a></li>
            
            <?php 
            // Links visíveis apenas para o perfil 'master'
            if (isset($_SESSION['user_profile']) && $_SESSION['user_profile'] === 'master'): ?>
                <li><a href="consulta_usuarios.php">Consultar Usuários</a></li>
                <li><a href="tela_log.php">Logs de Acesso</a></li>
                <li><a href="cadastro_planos.php">Gestão Planos</a></li> 
                <li><a href="assinaturas.php">Vendas/Assinaturas</a></li> <li><a href="tela_modelo_bd.php">Modelo do BD</a></li>
            <?php endif; ?>

            <?php
            // Link visível APENAS para usuários logados (Master ou Comum)
            if (isset($_SESSION['user_id'])): ?>
                <li><a href="alterar_senha.php">Alterar Senha</a></li> 
            <?php endif; ?>
            
            <li><a href="https://chat.whatsapp.com/G4rDsuICjOt00291r1Uru6">Contato</a></li>
            <li>
                <div class="user-actions">
                    <?php if (isset($_SESSION['user_id'])): ?> 
                        <span class="welcome-message">Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <a href="php/logout.php" class="logout-btn">Sair</a>
                    <?php else: ?>
                        <a href="login.php" class="btn-login">Login</a>
                        <a href="register.php" class="btn-register">Cadastre-se</a>
                    <?php endif; ?>
                </div>
            </li>
        </ul>
    </nav>
</div>
<div class="content">
    
    <h2>CONSULTA DE USUÁRIOS</h2>
    
    <?php if ($success_message): ?>
        <p style="color: #2ecc71; text-align: center; margin-bottom: 20px; font-weight: bold;"><?php echo htmlspecialchars($success_message); ?></p>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <p style="color: red; text-align: center; margin-bottom: 20px; font-weight: bold;"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>
    <?php if ($error_search): ?>
        <p style="color: red; text-align: center; margin-bottom: 20px;"><?php echo htmlspecialchars($error_search); ?></p>
    <?php endif; ?>

    <form method="GET" action="consulta_usuarios.php" class="search-form">
        <input type="text" name="search" placeholder="Pesquisar por nome do usuário..." value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit">Buscar</button>
        <a href="consulta_usuarios.php">Limpar Busca</a>
    </form>

    <?php if (empty($usuarios)): ?>
        <p style="text-align: center;">Nenhum usuário encontrado<?php echo !empty($search_query) ? ' para a busca "' . htmlspecialchars($search_query) . '".' : '.'; ?></p>
    <?php else: ?>
        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Login</th>
                    <th>CPF</th>
                    <th>Email</th>
                    <th>Perfil</th>
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
                    <td><?php echo ucfirst($user['profile']); ?></td>
                    <td><?php echo $user['is_active'] ? 'Ativo' : 'Inativo'; ?></td>
                    <td>
                        <?php 
                        $current_profile = $user['profile'];
                        $user_id = $user['id'];
                        ?>
                        
                        <?php if ($current_profile === 'master'): ?>
                            <?php if ($_SESSION['user_id'] !== $user_id): ?>
                                <a href="php/mudar_perfil.php?user_id=<?php echo $user_id; ?>&new_profile=comum" class="demote-link">Tornar Comum</a>
                            <?php else: ?>
                                <span style="color: #ccc;">(Seu Perfil)</span>
                            <?php endif; ?>

                        <?php else: ?>
                            <a href="php/mudar_perfil.php?user_id=<?php echo $user_id; ?>&new_profile=master" class="promote-link">Tornar Master</a>
                        <?php endif; ?>

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