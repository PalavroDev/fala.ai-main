<?php session_start();?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.1" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/main.css" />
    <link rel="icon" href="imagens/favicon.ico.ico" type="image/x-icon" />
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
    // NOVO: Link visível apenas para usuários com perfil 'master'
            if (isset($_SESSION['user_profile']) && $_SESSION['user_profile'] === 'master'): ?>
            <li><a href="consulta_usuarios.php">Consultar Usuários</a></li>
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


    <section class="painel">
      <div class="logo-principal">
        <img class="logo-principal" src="imagens/logo.svg" alt="" />
      </div>

      <section class="principal">
        <div class="texto-principal">
          <h1>Automação para seu restaurante!</h1>
          <ul>
            <li class="beneficio">
              <img src="imagens/arrow.svg" alt="arrow" class="arrow" />Cárdapio
              Digital
            </li>
            <li class="beneficio">
              <img
                src="imagens/arrow.svg"
                alt="arrow"
                class="arrow"
              />Aplicativo de Garçom
            </li>
            <li class="beneficio">
              <img src="imagens/arrow.svg" alt="arrow" class="arrow" />Gestão de
              Estoque
            </li>
            <li class="beneficio">
              <img src="imagens/arrow.svg" alt="arrow" class="arrow" />Gestão de
              Cozinha
            </li>
            <li class="beneficio">
              <img src="imagens/arrow.svg" alt="arrow" class="arrow" />Sistema
              PDV
            </li>
          </ul>
          <a class="consulta" href="https://wa.me/5521964828109"
            >Fale com um Consultor</a
          >
        </div>

        <div class="imagem-principal">
          <img
            class="imagem-direita"
            src="imagens/imagens-direita.png"
            alt="prints"
          />
        </div>
      </section>
    </section>

    <section class="margem">
      <h4>Nosso objetivo</h4>
      <div class="sobre">
        <div class="texto-sobre">
          <p class="robo">
            Somos uma empresa focada em transformar a experiência de atendimento
            e gestão no setor gastronômico, unindo tecnologia, eficiência e
            praticidade.
          </p>
          <p class="robo">
            Nosso objetivo é simplificar processos, reduzir filas, aumentar a
            produtividade e proporcionar uma jornada mais fluida tanto para o
            cliente quanto para a sua equipe.
          </p>
        </div>
        <div class="imagem-sobre">
          <img src="imagens/sobre.png" alt="sobreIMG" class="imagem-robo" />
        </div>
      </div>
    </section>

    <section class="margem">
      <h2>Benefícios</h2>
      <div class="grid-projetos">
        <article class="projeto-card">
          <img src="imagens/icon.png" alt="Projeto 3" />
          <div class="projeto-info">
            <p>Gestão de Mesas, Comandas e Estoque</p>
          </div>
        </article>

        <article class="projeto-card">
          <img src="imagens/icon2.png" alt="Projeto 3" />
          <div class="projeto-info">
            <p>Cardápio e Comanda Digital</p>
          </div>
        </article>

        <article class="projeto-card">
          <img src="imagens/icon3.png" alt="Projeto 3" />
          <div class="projeto-info">
            <p>Emissão de nota fiscal eletrônica</p>
          </div>
        </article>

        <article class="projeto-card">
          <img src="imagens/icon4.png" alt="Projeto 3" />
          <div class="projeto-info">
            <p>Automação para Whatsapp</p>
          </div>
        </article>

        <article class="projeto-card">
          <img src="imagens/icon5.png" alt="Projeto 3" />
          <div class="projeto-info">
            <p>Painel de gestão financeira</p>
          </div>
        </article>

        <article class="projeto-card">
          <img src="imagens/icon6.png" alt="Projeto 3" />
          <div class="projeto-info">
            <p>Sistema de Ponto de Venda (PDV)</p>
          </div>
        </article>
      </div>
    </section>

    <!-- Rodapé -->
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
            <li><a href="index.html">Home</a></li>
            <li><a href="sobre.html">Sobre</a></li>
            <li>
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
</script>
  </body>
</html>
