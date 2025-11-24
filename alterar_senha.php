<?php
session_start();
// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Você precisa estar logado para alterar sua senha.";
    header("Location: login.php");
    exit();
}

// Limpa e armazena mensagens de feedback
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
    <title>Alterar Senha</title>
    <link rel="stylesheet" href="css/main.css" /> 
    <link rel="stylesheet" href="css/login.css" /> 
    <link rel="icon" href="imagens/favicon.ico.ico" type="image/x-icon" />
    <style>
        .login-box { 
            height: auto; 
            padding-top: 50px; 
            padding-bottom: 50px; 
            margin-top: 150px;
        }
        /* Garante que o fundo cubra 100% da tela */
        html { height: 100%; } 
        body { min-height: 100vh; background-attachment: fixed; }
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
            if (isset($_SESSION['user_profile']) && $_SESSION['user_profile'] === 'master'): ?>
                <li><a href="consulta_usuarios.php">Consultar Usuários</a></li>
                <li><a href="tela_log.php">Logs de Acesso</a></li>
                <li><a href="cadastro_planos.php">Gestão Planos</a></li>
                <li><a href="assinaturas.php">Vendas/Assinaturas</a></li>
                <li><a href="tela_bd.php">Modelo do BD</a></li>
            <?php endif; ?>

            <?php
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
<div class="login-box">
    <div class="login-header">
        <header>Alterar Senha</header>
    </div>
    
    <form id="alterarSenhaForm" action="php/processo_alterar_senha.php" method="POST">
        
        <div class="input-box">
            <input type="password" class="input-field" id="current_password" name="current_password" placeholder="Senha Atual (8 caracteres)*" required minlength="8" maxlength="8">
        </div>

        <div class="input-box">
            <input type="password" class="input-field" id="new_password" name="new_password" placeholder="Nova Senha (8 caracteres)*" required minlength="8" maxlength="8">
        </div>

        <div class="input-box">
            <input type="password" class="input-field" id="confirm_new_password" name="confirm_new_password" placeholder="Confirmar Nova Senha*" required minlength="8" maxlength="8">
        </div>
        
        <div class="input-submit">
            <button type="submit" class="submit-btn">Salvar Nova Senha</button>
        </div>

        <div class="message-area" style="margin-top: 15px; text-align: center;">
            <?php
                if ($error_message) {
                    echo '<p style="color: red;">' . htmlspecialchars($error_message) . '</p>';
                }
                if ($success_message) {
                    echo '<p style="color: green;">' . htmlspecialchars($success_message) . '</p>';
                }
            ?>
        </div>
    </form>
</div>

<script src="js/script.js"></script>
</body>
</html>