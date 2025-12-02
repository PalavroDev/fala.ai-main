<?php
// Inclui o controle de acesso (apenas Master)
require_once 'php/auth_master.php'; 

// Lógica para carregar o SQL do arquivo
$schema_content = '';
$error_message = '';
$file_path = 'falaai_database_schema.sql'; 

if (file_exists($file_path)) {
    $schema_content = file_get_contents($file_path);
    if ($schema_content === false) {
        $error_message = "Erro ao ler o arquivo do esquema de banco de dados.";
    }
} else {
    $error_message = "Erro: Arquivo do esquema de banco de dados não encontrado em: " . $file_path;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.1">
    <title>Modelo do BD - Master</title>
    <link rel="stylesheet" href="css/main.css" /> 
    <link rel="icon" href="imagens/favicon.ico.ico" type="image/x-icon" />
    <style>
        .content { padding: 100px 20px 20px; max-width: 1000px; margin: 0 auto; color: white; }
        .content h2 { 
            text-align: center; 
            font-family: 'nulshock', sans-serif; 
            margin: 0 auto 30px auto;
            font-size: 3em; 
        }
        .sql-container { 
            background-color: #1e1e1e; 
            border: 1px solid #f0a92d; 
            padding: 20px; 
            border-radius: 5px;
            overflow-x: auto; 
            max-height: 80vh; 
            font-size: 0.9em;
            margin-bottom: 20px;
        }
        pre { margin: 0; white-space: pre-wrap; word-wrap: break-word; }
        code { color: #d4d4d4; }
        
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
                <li><a href="tela_bd.php">Modelo do BD</a></li> <?php endif; ?>

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
<div class="content">
    <h2>MODELO DO BANCO DE DADOS</h2>
    
    <?php if ($error_message): ?>
        <p style="color: red; text-align: center; margin-bottom: 20px;"><?php echo htmlspecialchars($error_message); ?></p>
    <?php else: ?>
        <div style="text-align: center; margin-bottom: 20px;">
            
        </div>
        <div class="sql-container">
            <h3>Código SQL:</h3>
            <pre><code><?php echo htmlspecialchars($schema_content); ?></code></pre>
        </div>
    <?php endif; ?>
</div>

<script src="js/script.js"></script>
</body>
</html>
