<?php session_start(); ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.1">
  <link rel="stylesheet" href="css/register.css">
  <link rel="icon" href="imagens/favicon.ico.ico" type="image/x-icon" />
  <title>Cadastro </title>
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
    <header>Cadastro</header>
  </div>
  
  <form id="cadastroForm" action="php/processo_register.php" method="POST">
    
    <div class="input-box">
      <input type="text" class="input-field" id="name" name="name" placeholder="Nome Completo*" required minlength="8" maxlength="60">
    </div>

    <div class="input-box">
      <input type="text" class="input-field" id="nasc" name="nasc" placeholder="Data de Nascimento*" required>
    </div>

    <div class="input-box">
      <select class="input-field" id="sexo" name="sexo" required>
        <option value="">Sexo*</option>
        <option value="Masculino">Masculino</option>
        <option value="Feminino">Feminino</option>
        <option value="Outro">Outro</option>
      </select>
    </div>

    <div class="input-box">
      <input type="text" class="input-field" id="login" name="login" placeholder="Login (6 letras)*" required minlength="6" maxlength="6" pattern="[A-Za-z]{6}">
    </div>

    <div class="input-box">
      <input type="text" class="input-field" id="cpf" name="cpf" placeholder="CPF*" required>
    </div>

    <div class="input-box">
      <input type="email" class="input-field" id="email" name="email" placeholder="Email*" required>
    </div>

    <div class="input-box">
      <input type="text" class="input-field" id="phone" name="phone" placeholder="Telefone Celular*" required>
    </div>

    <div class="input-box">
      <input type="password" class="input-field" id="password" name="password" placeholder="Senha (8 caracteres)*" required minlength="8" maxlength="8">
    </div>

    <div class="input-box">
      <input type="password" class="input-field" id="confirm_password" name="confirm_password" placeholder="Confirmar Senha*" required>
    </div>
    
    <div class="input-box">
        <input placeholder="CEP*" name="cep" type="text" id="cep" class="input-field" required>
    </div>
    <div class="input-box">
        <input placeholder="Rua*" name="street" type="text" id="rua" class="input-field" required>
    </div>
    <div class="input-box">
        <input placeholder="Número*" name="numero" type="text" id="numero" class="input-field" required>
    </div>
    <div class="input-box">
        <input placeholder="Complemento" name="complemento" type="text" id="complementoo" class="input-field">
    </div>
    <div class="input-box">
        <input placeholder="Bairro*" name="bairro" type="text" id="bairro" class="input-field" required>
    </div>
    <div class="input-box">
        <input placeholder="Cidade*" name="cidade" type="text" id="cidade" class="input-field" required>
    </div>
    <div class="input-box">
        <input placeholder="Estado*" name="estado" type="text" id="uf" maxlength="2" class="input-field" required>
    </div>

    <div class="input-submit">
      <button type="submit" class="submit-btn">Cadastrar-se</button>
      <button type="reset" class="reset-btn">Limpar</button>
    </div>

    <div id="erroCadastro" style="color: red; margin-top: 10px; text-align: center;"></div>
    
    <div class="message-area" style="color: red; margin-top: 15px; text-align: center;">
        <?php
            // Exibe a mensagem de erro se ela existir na sessão
            if (isset($_SESSION['error_message'])) {
                echo '<p>' . htmlspecialchars($_SESSION['error_message']) . '</p>';
                // Limpa a mensagem da sessão para que ela não seja exibida novamente
                unset($_SESSION['error_message']);
            }
        ?>
    </div>

    <div class="sign-up-link">
      <p>Já possui uma conta? <a href="login.php">Logar</a></p>
    </div>
  </form>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.js"></script>

<script>
// --- MÁSCARAS (JQuery Mask) ---
$(document).ready(function() {
  $('#phone').mask('(+55) 00 00000-0000');
  $('#cpf').mask('000.000.000-00');
  $('#cep').mask('00000-000');
  $('#nasc').mask('00/00/0000');
});

// --- API ViaCEP para preenchimento automático de endereço ---
$('#cep').on('blur', function() {
  const cep = $(this).val().replace(/\D/g, '');
  if (cep.length !== 8) return;
  
  fetch(`https://viacep.com.br/ws/${cep}/json/`)
    .then(response => response.json())
    .then(data => {
      if (!data.erro) {
        $('#rua').val(data.logradouro);
        $('#bairro').val(data.bairro);
        $('#cidade').val(data.localidade);
        $('#uf').val(data.uf);
        $('#numero').focus();
      } else {
        alert("CEP não encontrado. Preencha o endereço manualmente.");
      }
    })
    .catch(error => console.error('Erro ao buscar CEP:', error));
});

// --- VALIDAÇÕES DO FORMULÁRIO (JavaScript puro) ---
document.getElementById('cadastroForm').addEventListener('submit', function(event) {
  event.preventDefault(); 
  
  const form = event.target;
  const errorDiv = document.getElementById('erroCadastro');
  errorDiv.innerText = '';

  const nome = form.name.value;
  if (nome.length < 8 || !/^[A-Za-z\s]+$/.test(nome)) {
    errorDiv.innerText = 'Nome inválido! Deve ter entre 8 e 60 letras.';
    return;
  }

  const cpf = form.cpf.value;
  if (!validaCPF(cpf)) {
    errorDiv.innerText = 'CPF inválido! Verifique o dígito verificador.';
    return;
  }
  
  const login = form.login.value;
  if (!/^[A-Za-z]{6}$/.test(login)) {
      errorDiv.innerText = 'Login inválido! Deve ter exatamente 6 letras.';
      return;
  }

  const senha = form.password.value;
  if (senha.length < 8) {
    errorDiv.innerText = 'A senha deve ter 8 caracteres.';
    return;
  }
  
  const confirmaSenha = form.confirm_password.value;
  if (senha !== confirmaSenha) {
    errorDiv.innerText = 'As senhas não conferem!';
    return;
  }

  console.log("Validações do front-end passaram. Enviando para o back-end...");
  form.submit(); 
});

// --- Função de Validação de CPF ---
function validaCPF(cpf) {
    cpf = cpf.replace(/[^\d]+/g,'');
    if(cpf == '') return false;
    if (cpf.length != 11 || /^(\d)\1+$/.test(cpf)) return false;
    let add = 0;
    for (let i=0; i < 9; i ++) add += parseInt(cpf.charAt(i)) * (10 - i);
    let rev = 11 - (add % 11);
    if (rev == 10 || rev == 11) rev = 0;
    if (rev != parseInt(cpf.charAt(9))) return false;
    add = 0;
    for (let i = 0; i < 10; i ++) add += parseInt(cpf.charAt(i)) * (11 - i);
    rev = 11 - (add % 11);
    if (rev == 10 || rev == 11) rev = 0;
    if (rev != parseInt(cpf.charAt(10))) return false;
    return true;
}
</script>

</body>
</html>
