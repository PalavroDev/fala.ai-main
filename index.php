<?php
// Inicia a sessão para poder verificar se o usuário está logado
session_start();
require_once 'conexao.php'; // Inclui a conexão para buscar os planos

// Lógica para buscar os planos ativos e ordenados
$plans = [];
try {
    // Seleciona planos ativos, ordenados pelo preço (simulando Básico, Smart, Ultra)
    // As colunas (name, price, features) devem existir na sua tabela 'plans'
    $stmt = $pdo->query("SELECT name, price, features FROM plans WHERE is_active = 1 ORDER BY price ASC");
    $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Em caso de erro na busca (ex: tabela 'plans' não existe ou erro de conexão), 
    // o array de planos ficará vazio e não causará falha na renderização da página.
    // O erro real pode ser registrado em log se necessário.
    // Por enquanto, apenas garante que a página não quebre.
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.1" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/main.css" />
    <link rel="icon" href="imagens/favicon.ico.ico" type="image/x-icon" />
    <title>Fala AI - Automação para seu negócio</title>
    
    <style>
        /* ======================================================= */
        /* CSS para a NOVA SEÇÃO: APRESENTAÇÃO DOS PLANOS */
        /* ======================================================= */
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            max-width: 1200px;
            margin: 40px auto;
            text-align: center;
        }
        .plan-card {
            background-color: #1f1e1e;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
            transition: transform 0.3s ease;
            height: 100%;
        }
        .plan-card:hover {
            transform: translateY(-5px);
            border-top: 3px solid #f0a92d;
        }
        .plan-card h3 {
            font-family: 'nulshock', sans-serif;
            color: #f0a92d;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        .plan-card .price {
            font-size: 2.2rem;
            font-family: 'nexab', sans-serif;
            color: white;
            margin-bottom: 20px;
        }
        .plan-card .price span {
            font-size: 1rem;
            font-weight: 300;
        }
        .plan-card ul {
            list-style: none;
            padding: 0;
            text-align: left;
            margin-bottom: 20px;
        }
        .plan-card ul li {
            font-family: 'nexa', sans-serif;
            font-size: 0.95rem;
            margin-bottom: 8px;
            color: #ccc;
            padding-left: 15px;
            position: relative;
        }
        .plan-card ul li::before {
            content: '✔';
            position: absolute;
            left: 0;
            color: #2ecc71;
            font-weight: bold;
        }
        .plan-card .btn-contratar {
            display: inline-block;
            padding: 10px 25px;
            background-color: #f0a92d;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .plan-card .btn-contratar:hover {
            background-color: #e69a00;
        }

        /* ======================================================= */
        /* CSS para a CORREÇÃO dos ÍCONES nos cards de BENEFÍCIOS */
        /* ======================================================= */
        .projeto-card img {
            display: none; /* Esconde a tag <img> antiga */
        }

        .projeto-card i.bx {
            font-size: 3em; /* Tamanho do ícone */
            color: white; /* Cor do ícone */
            margin-bottom: 15px; /* Espaçamento abaixo do ícone */
            padding: 15px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
            /* Usando cores diferentes por item */
            background-color: #f0a92d; 
        }
        
        /* Cores personalizadas para os ícones */
        .projeto-card:nth-child(1) i.bx { background-color: #6a1aed; } /* Gestão de Mesas */
        .projeto-card:nth-child(2) i.bx { background-color: #2196f3; } /* Cardápio Digital */
        .projeto-card:nth-child(3) i.bx { background-color: #ff5722; } /* Nota Fiscal */
        .projeto-card:nth-child(4) i.bx { background-color: #25d366; } /* Whatsapp */
        .projeto-card:nth-child(5) i.bx { background-color: #e91e63; } /* Financeiro */
        .projeto-card:nth-child(6) i.bx { background-color: #00bcd4; } /* PDV */

        .projeto-card .projeto-info p {
            font-family: 'nexa', sans-serif;
            color: #ccc;
        }
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
                <li><a href="assinaturas.php">Vendas/Assinaturas</a></li>
                <li><a href="tela_bd.php">Modelo do BD</a></li> <?php endif; ?>

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
<section class="painel">
      <div class="logo-principal">
        <img class="logo-principal" src="imagens/logo.svg" alt="logo" />
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
      <h2 style="margin-bottom: 40px;">Nossos Planos de Automação</h2>
      <div class="plans-grid">
          <?php 
          if (empty($plans)): 
              // Mensagem caso não encontre planos (pode ser problema de conexão ou dados)
              echo '<p style="color: #f0a92d; grid-column: 1 / -1;">Não foi possível carregar os planos. Tente novamente mais tarde.</p>';
          endif;
          
          foreach ($plans as $plan): ?>
          <div class="plan-card">
              <h3><?php echo htmlspecialchars($plan['name']); ?></h3>
              <div class="price">R$ <?php echo number_format($plan['price'], 2, ',', '.'); ?> <span>/ mês</span></div>
              
              <ul>
                <?php
                // Decodifica a string JSON de features em um array PHP
                $features = json_decode($plan['features'], true);
                if (is_array($features)):
                    foreach ($features as $feature):
                ?>
                        <li><?php echo htmlspecialchars($feature); ?></li>
                <?php
                    endforeach;
                endif;
                ?>
              </ul>
              
              <a href="https://chat.whatsapp.com/G4rDsuICjOt00291r1Uru6" class="btn-contratar">Contratar</a>
          </div>
          <?php endforeach; ?>
      </div>
    </section>

<section class="margem">
      <h2>Benefícios</h2>
      <div class="grid-projetos">
        
        <article class="projeto-card">
          <i class='bx bx-table'></i> <div class="projeto-info">
            <p>Gestão de Mesas, Comandas e Estoque</p>
          </div>
        </article>

        <article class="projeto-card">
          <i class='bx bx-food-menu'></i> <div class="projeto-info">
            <p>Cardápio e Comanda Digital</p>
          </div>
        </article>

        <article class="projeto-card">
          <i class='bx bx-receipt'></i> <div class="projeto-info">
            <p>Emissão de nota fiscal eletrônica</p>
          </div>
        </article>

        <article class="projeto-card">
          <i class='bx bxl-whatsapp'></i> <div class="projeto-info">
            <p>Automação para Whatsapp</p>
          </div>
        </article>

        <article class="projeto-card">
          <i class='bx bx-wallet'></i> <div class="projeto-info">
            <p>Painel de gestão financeira</p>
          </div>
        </article>

        <article class="projeto-card">
          <i class='bx bx-store-alt'></i> <div class="projeto-info">
            <p>Sistema de Ponto de Venda (PDV)</p>
          </div>
        </article>
      </div>
    </section>

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
  </body>
</html>