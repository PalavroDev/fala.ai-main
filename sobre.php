<?php
session_start(); // CORREÇÃO PRINCIPAL: Inicia a sessão para acessar o usuário logado
?>
<!DOCTYPE html>
<html lang="pt-br">
 <meta charset="UTF-8" />
 <meta name="viewport" content="width=device-width, initial-scale=1.1" />
 <link
  href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
  rel="stylesheet"
 />
 <link rel="stylesheet" href="css/sobre.css" />
   <link rel="icon" href="imagens/favicon.ico.ico" type="image/x-icon" />
 <head>
  <title>Fala AI - Automação para seu negócio</title>
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
        <li><a href="assinaturas.php">Vendas/Assinaturas</a></li> 
                <li><a href="tela_modelo_bd.php">Modelo do BD</a></li>
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

  <br /><br /><br /><br /><br /><br />

   <h2>Quem Somos</h2>
   <p>
    Somos uma empresa especializada em soluções de automação para
    restaurantes, bares, lanchonetes e estabelecimentos do setor
    alimentício. Nosso objetivo é transformar a experiência dos nossos
    clientes por meio de tecnologias modernas e eficientes.
   </p>

   <h2>Nossa Missão</h2>
   <p>
    Proporcionar soluções tecnológicas que otimizem os processos
    operacionais, aumentem a produtividade e melhorem o atendimento ao
    cliente nos estabelecimentos alimentícios.
   </p>

   <h2>O Que Fazemos</h2>
   <p>
    Oferecemos sistemas de gestão de pedidos, controle de estoque, frente de
    caixa (PDV), cardápios digitais, aplicativos de autoatendimento e muito
    mais.
   </p>
   <p>
    Além disso, prestamos suporte técnico especializado, treinamentos e
    consultoria para ajudar nossos clientes a tirarem o máximo proveito das
    nossas soluções.
   </p>
  </section>

  <br /><br /><br />

    <footer class="site-footer">
   <div class="footer-columns">
    <div id="footer-col">
     <div class="footer-col">
      <img src="imagens/logo.svg" alt="footer-img" />
      <p>conheça mais nosso trabalho</p>
     </div>
     <div class="social-icons">
      <a href="https://www.instagram.com" target="_blank">
       <i class="bxl bx-instagram"></i>
      </a>
      <a href="https://www.facebook.com" target="_blank">
       <i class="bxl bx-facebook-circle"></i>
      </a>
      <a
       href="https://chat.whatsapp.com/G4rDsuICjOt00291r1Uru6"
       target="_blank"
      >
       <i class="bxl bx-instagram"></i>
      </a>
     </div>
    </div>

    <div class="footer-col">
     <h3>Links rápidos</h3>
     <ul>
      <li><a href="index.php">Home</a></li>       <li><a href="sobre.php">Sobre</a></li>       <li>
       <a href="https://chat.whatsapp.com/G4rDsuICjOt00291r1Uru6"
        >Contato</a
       >
      </li>
     </ul>
    </div>

    <div class="footer-col">
     <h3>Contato</h3>
     <p>(21) 96482-8109</p>
     <p class="email">✉ falaai@gmail.com</p>
    </div>
   </div>

   <hr />
   <p class="footer-copy">
    &copy; 2025 G&C INNOVATIONS. TODOS OS DIREITOS RESERVADOS.
   </p>
  </footer>

  <script src="js/script.js"></script>
  <script src="js/auth.js"></script>
    <script src="js/acessibilidade.js"></script>   <script>
   // Verificar estado de login ao carregar cada página
   document.addEventListener("DOMContentLoaded", function () {
    // Verificar se o script de atualização de navbar existe
    if (typeof atualizarNavbar === "function") {
     atualizarNavbar();
    }

    // Se estiver em página restrita e não logado, redirecionar
    // NOTA: Esta lógica em JS não é mais necessária se você usa PHP/Sessões
        // para controle de acesso, mas mantida para compatibilidade com o JS local.
    const usuarioLogado = JSON.parse(localStorage.getItem("usuarioLogado"));
    if (window.location.pathname.includes("restrito") && !usuarioLogado) {
     window.location.href = "login.php"; // Corrigido para .php
    }
   });
  </script>
 </body>
</html>