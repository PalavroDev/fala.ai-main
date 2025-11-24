<?php
// Inicia a sessão para acesso a variáveis
session_start();
// Acesso restrito ao Master (requer php/auth_master.php - arquivo que verifica a permissão)
require_once 'php/auth_master.php'; 
require_once 'conexao.php'; // Inclui a conexão com o banco de dados

// ----------------------------------------------------
// Lógica para carregar todas as Assinaturas/Vendas
// ----------------------------------------------------
$assinaturas = [];
$error_loading = '';

try {
    // Consulta JOIN para obter o nome do cliente, o nome do plano e o status da assinatura
    $sql = "SELECT s.id, u.name AS user_name, p.name AS plan_name, s.status, s.starts_at, s.ends_at, p.price AS current_price
            FROM subscriptions s
            JOIN users u ON s.user_id = u.id
            JOIN plans p ON s.plan_id = p.id
            ORDER BY s.starts_at DESC";
            
    $stmt = $pdo->query($sql);
    $assinaturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_loading = "Erro ao carregar o registro de vendas/assinaturas: " . $e->getMessage();
}

// Limpa mensagem de sucesso da sessão
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
unset($_SESSION['success_message']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.1">
    <title>Registro de Vendas/Assinaturas - Master</title>
    <link rel="stylesheet" href="css/main.css" /> 
    <link rel="icon" href="imagens/favicon.ico.ico" type="image/x-icon" />
    
    <style>
        /* Garante que o fundo cubra 100% da tela */
        html { height: 100%; } 
        body { min-height: 100vh; background-attachment: fixed; } 
        
        .content { 
            padding: 100px 20px 20px; 
            max-width: 1400px; 
            margin: 0 auto; 
            color: white; 
        }
        /* Título Centralizado */
        .content h2 { 
            text-align: center; 
            color: white; 
            font-family: 'nulshock', sans-serif; 
            margin: 0 auto 30px auto; 
            font-size: 3em; 
            line-height: 1.2; 
        }

        /* Estilos da Tabela de Vendas */
        .sales-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 20px auto; 
            background-color: #1f1e1e; 
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4); 
        }
        .sales-table th, .sales-table td { 
            padding: 12px; 
            border: 1px solid #444; 
            text-align: left; 
        }
        .sales-table th { 
            background-color: #f0a92d; 
            color: #333; 
        }
        .sales-table td { 
            color: white; 
        }
        .sales-table tr:nth-child(even) { 
            background-color: #222; 
        }
        .sales-table tr:hover {
            background-color: #333;
        }

        /* Classes para Status */
        .status-active { color: #2ecc71; font-weight: bold; }
        .status-cancelled { color: #e74c3c; font-weight: bold; }
        .status-expired { color: #f0a92d; font-weight: bold; }
        
        /* Estilos de Acessibilidade (Tabela Light Mode) */
        body.light-mode .sales-table {
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        body.light-mode .sales-table th {
            background-color: #f0a92d;
            color: white;
        }
        body.light-mode .sales-table td {
            color: #333;
        }
        body.light-mode .sales-table tr:nth-child(even) {
            background-color: #eeeeee;
        }
        body.light-mode .sales-table tr:hover {
            background-color: #dddddd;
        }
    </style>
</head>
<body>

<button id="toggleContraste" style="position: fixed; top: 100px; right: 20px; z-index: 1001; padding: 8px 12px; background-color: #f0a92d; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
    Modo Claro
</button>

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
    <h2>Registro de Vendas e Assinaturas</h2>
    
    <?php if ($success_message): ?>
        <p style="color: #2ecc71; text-align: center; margin-bottom: 20px; font-weight: bold;"><?php echo htmlspecialchars($success_message); ?></p>
    <?php endif; ?>
    <?php if ($error_loading): ?>
        <p style="color: red; text-align: center; margin-bottom: 20px; font-weight: bold;"><?php echo htmlspecialchars($error_loading); ?></p>
    <?php elseif (empty($assinaturas)): ?>
        <p style="text-align: center; color: #f0a92d;">Nenhuma assinatura/venda encontrada.</p>
    <?php else: ?>
        <table class="sales-table">
            <thead>
                <tr>
                    <th>ID Assinatura</th>
                    <th>Cliente</th>
                    <th>Plano Adquirido</th>
                    <th>Valor (Mensal)</th>
                    <th>Data Início</th>
                    <th>Data Fim Prevista</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assinaturas as $a): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($a['id']); ?></td>
                        <td><?php echo htmlspecialchars($a['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($a['plan_name']); ?></td>
                        <td>R$ <?php echo number_format($a['current_price'], 2, ',', '.'); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($a['starts_at'])); ?></td>
                        <td><?php echo $a['ends_at'] ? date('d/m/Y', strtotime($a['ends_at'])) : 'Vitalício/Indefinido'; ?></td>
                        <td>
                            <span class="status-<?php echo htmlspecialchars($a['status']); ?>">
                                <?php echo ucfirst($a['status']); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="js/script.js"></script>
<script src="js/acessibilidade.js"></script> 
</body>
</html>