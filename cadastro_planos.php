<?php
// Acesso restrito ao Master
require_once 'php/auth_master.php';
require_once 'conexao.php';

// Lógica para carregar planos existentes (para edição)
$planos = [];
try {
    $stmt = $pdo->query("SELECT id, name, price, features FROM plans ORDER BY price ASC");
    $planos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Erro ao carregar planos: " . $e->getMessage();
}

// Lógica para exibir mensagens de sucesso/erro
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
    <title>Gestão de Planos - Master</title>
    <link rel="stylesheet" href="css/main.css" /> 
    <link rel="icon" href="imagens/favicon.ico.ico" type="image/x-icon" />
<style>
        /* CORREÇÃO GLOBAL: Garante que o fundo cubra 100% da tela, eliminando a faixa branca */
        html { height: 100%; } 
        body { min-height: 100vh; background-attachment: fixed; } 

        .content { 
            padding: 100px 20px 20px; 
            max-width: 900px; 
            margin: 0 auto; 
            color: white; 
        }
        /* CORREÇÃO DO TÍTULO PRINCIPAL (h2) */
        .content h2 { 
            text-align: center; 
            font-family: 'nulshock', sans-serif; 
            margin: 0 auto 30px auto; 
            font-size: 3em; 
            color: white;
            line-height: 1.2;
            width: 100%; 
        }
        
        /* Estilos do Formulário e da Tabela */
        .form-container, .plans-list { 
            background-color: #1f1e1e; 
            padding: 30px; 
            border-radius: 8px; 
            margin-bottom: 30px; 
        }
        .form-container input[type="text"], .form-container input[type="number"], .form-container textarea {
            width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #444; background-color: #333; color: white;
        }
        .form-container button { 
            padding: 10px 15px; 
            background-color: #f0a92d; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            color: #333; 
            font-weight: bold; 
        }
        .plans-list table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        .plans-list th, .plans-list td { 
            padding: 10px; 
            border: 1px solid #444; 
            text-align: left; 
        }
        .plans-list th { 
            background-color: #f0a92d; 
            color: #333; 
        }
        .edit-btn, .delete-btn { 
            padding: 5px 10px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
        }
        .edit-btn { 
            background-color: #2ecc71; 
            color: white; 
        }
        .delete-btn { 
            background-color: #e74c3c; 
            color: white; 
            margin-left: 5px; 
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
    <h2>Gestão de Planos de Automação</h2>
    
    <?php if ($success_message): ?>
        <p style="color: #2ecc71; text-align: center; margin-bottom: 20px; font-weight: bold;"><?php echo htmlspecialchars($success_message); ?></p>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <p style="color: red; text-align: center; margin-bottom: 20px; font-weight: bold;"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <div class="form-container">
        <h3>Cadastrar Novo Plano</h3>
        <form action="php/processo_planos.php" method="POST">
            <input type="hidden" name="action" value="create">
            <input type="text" name="name" placeholder="Nome do Plano (Ex: Smart)" required>
            <input type="number" name="price" placeholder="Preço Mensal (Ex: 199.90)" step="0.01" required>
            <textarea name="description" placeholder="Descrição Curta" required></textarea>
            <textarea name="features" placeholder="Recursos (Lista em formato JSON, Ex: [Cardápio Digital, PDV])" required></textarea>
            <button type="submit">Salvar Plano</button>
        </form>
    </div>

    <div class="plans-list">
        <h3>Planos Atuais</h3>
        <?php if (empty($planos)): ?>
            <p>Nenhum plano cadastrado.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Recursos (JSON)</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($planos as $plano): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($plano['name']); ?></td>
                            <td>R$ <?php echo number_format($plano['price'], 2, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($plano['features']); ?></td>
                            <td>
                                <button class="edit-btn">Editar</button>
                                <form method="POST" action="php/processo_planos.php" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="plan_id" value="<?php echo $plano['id']; ?>">
                                    <button type="submit" class="delete-btn" onclick="return confirm('Confirmar exclusão do plano <?php echo $plano['name']; ?>?');">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script src="js/script.js"></script>
</body>
</html>