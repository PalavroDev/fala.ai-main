<?php session_start();?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.1" />
    <link href="https://cdn.boxicons.com/fonts/brands/boxicons-brands.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/login.css" />
    <link rel="icon" href="imagens/favicon.ico.ico" type="image/x-icon" />
    <title>Login </title>
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
            <header>Login</header>
        </div>

        <form id="loginForm" action="php/processo_login.php" method="POST">
            <div class="input-box">
                <input
                    type="text"
                    class="input-field"
                    id="login"
                    name="login"
                    placeholder="Login"
                    autocomplete="off"
                    required
                    minlength="6"
                    maxlength="6"
                />
            </div>

            <div class="input-box">
                <input
                    type="password"
                    class="input-field"
                    id="password"
                    name="password"
                    placeholder="Senha"
                    autocomplete="off"
                    required
                    minlength="8"
                    maxlength="8"
                />
            </div>

            <div class="forgot">
                <section>
                    <input type="checkbox" id="check" name="remember" />
                    <label for="check">Lembrar-me</label>
                </section>
                <section>
                    <a href="#">Esqueceu a senha?</a>
                </section>
            </div>

            <div class="input-submit">
                <button type="submit" class="submit-btn">Entrar</button>
            </div>

            <div class="message-area" style="margin-top: 15px; text-align: center;">
                <?php
                // Exibe mensagem de erro se houver uma na sessão
                if (isset($_SESSION['error_message'])) {
                    echo '<p style="color: red;">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
                    unset($_SESSION['error_message']); // Limpa a mensagem após exibir
                }
                // Exibe mensagem de sucesso (ex: após o cadastro)
                if (isset($_SESSION['success_message'])) {
                    echo '<p style="color: green;">' . htmlspecialchars($_SESSION['success_message']) . '</p>';
                    unset($_SESSION['success_message']);
                }
                ?>
            </div>
        </form>

        <div class="sign-up-link">
            <p>Não possui uma conta? <a href="register.php">Cadastre-se</a></p>
        </div>
    </div>

    <script src="js/script.js"></script>
    <script src="js/auth.js"></script> 
</body>
</html>