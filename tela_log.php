<?php
// 1. Controle de Acesso: Apenas Master pode entrar.
require_once 'php/auth_master.php'; 
require_once 'conexao.php'; 

// ----------------------------------------------------
// 2. Lógica de Busca e Filtro
// ----------------------------------------------------

$search_type = isset($_GET['type']) ? $_GET['type'] : 'all'; 
$search_value = isset($_GET['value']) ? trim($_GET['value']) : '';
$logs = [];
$error_log = '';

try {
    $sql = "SELECT id, created_at, user_name, user_cpf, user_login, ip_address, two_factor_used, login_successful 
            FROM access_logs";
    $params = [];
    $where_clauses = [];

    if (!empty($search_value)) {
        if ($search_type === 'name') {
            $where_clauses[] = "user_name LIKE ?";
            $params[] = '%' . $search_value . '%';
        } elseif ($search_type === 'cpf') {
            $cpf_limpo = preg_replace('/[^0-9]/', '', $search_value);
            $where_clauses[] = "user_cpf LIKE ?"; 
            $params[] = '%' . $cpf_limpo . '%';
        }
    }
    
    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }

    $sql .= " ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_log = "Erro ao carregar logs: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.1">
    <title>Logs de Autenticação - Master</title>
    <link rel="stylesheet" href="css/main.css" /> 
    <link rel="icon" href="imagens/favicon.ico.ico" type="image/x-icon" />
    <style>
        /* CORREÇÃO DO TÍTULO E LAYOUT DA TELA */
        .content { padding: 100px 20px 20px; max-width: 1400px; margin: 0 auto; color: white; }
        .content h2 { 
            text-align: center; 
            color: white; 
            font-size: 3em; 
            margin: 0 auto 20px auto; /* Centralização do Bloco do Título */
            font-family: 'nulshock', sans-serif; 
            white-space: pre-wrap; /* Permite que o texto se ajuste, mantendo a centralização */
        }
        
        /* CORREÇÃO PARA GARANTIR O FUNDO TOTAL */
        html { height: 100%; } 
        body { min-height: 100vh; background-attachment: fixed; } /* Fixa o fundo na tela */

        /* Estilos da Tabela e Filtro */
        .log-table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: #1f1e1e; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4); }
        .log-table th, .log-table td { padding: 10px; border: 1px solid #444; text-align: left; font-size: 0.9em; }
        .log-table th { background-color: #f0a92d; color: #333; }
        .log-table td { color: white; }
        .log-table tr:nth-child(even) { background-color: #222; }
        .filter-form { margin-bottom: 20px; display: flex; gap: 10px; align-items: center; justify-content: center; }
        .filter-form select, .filter-form input[type="text"] { padding: 8px; border-radius: 5px; border: 1px solid #444; background-color: #333; color: white; }
        .filter-form button { padding: 8px 15px; background-color: #f0a92d; border: none; border-radius: 5px; cursor: pointer; color: #333; font-weight: bold; }
        .status-success { color: #2ecc71; font-weight: bold; }
        .status-failed { color: #e74c3c; font-weight: bold; }
        .filter-form a { padding: 8px 15px; background-color: #555; border-radius: 5px; color: white; font-weight: bold; text-decoration: none; display: flex; align-items: center; justify-content: center; }
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
    
    <h2>LOGS DE AUTENTICAÇÃO</h2> 
    
    <form method="GET" action="tela_log.php" class="filter-form">
        <label for="type" style="color: #f0a92d;">Buscar por:</label>
        <select name="type" id="type">
            <option value="all" <?php echo $search_type === 'all' ? 'selected' : ''; ?>>Todos</option>
            <option value="name" <?php echo $search_type === 'name' ? 'selected' : ''; ?>>Nome do Usuário</option>
            <option value="cpf" <?php echo $search_type === 'cpf' ? 'selected' : ''; ?>>CPF</option>
        </select>
        
        <input type="text" name="value" placeholder="Valor da pesquisa..." value="<?php echo htmlspecialchars($search_value); ?>">
        <button type="submit">Filtrar</button>
        <a href="tela_log.php">Limpar Filtro</a>
    </form>

    <?php if ($error_log): ?>
        <p style="color: red; text-align: center; margin-bottom: 20px;"><?php echo htmlspecialchars($error_log); ?></p>
    <?php elseif (empty($logs)): ?>
        <p style="text-align: center;">Nenhum log encontrado com os critérios de busca.</p>
    <?php else: ?>
        <table class="log-table">
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Login</th>
                    <th>IP</th>
                    <th>2FA Usado</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?php echo date('d/m/Y H:i:s', strtotime($log['created_at'])); ?></td>
                    <td><?php echo htmlspecialchars($log['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($log['user_cpf']); ?></td>
                    <td><?php echo htmlspecialchars($log['user_login']); ?></td>
                    <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                    <td><?php echo $log['two_factor_used'] ? 'Sim' : 'Não'; ?></td>
                    <td>
                        <span class="<?php echo $log['login_successful'] ? 'status-success' : 'status-failed'; ?>">
                            <?php echo $log['login_successful'] ? 'Sucesso' : 'Falha'; ?>
                        </span>
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