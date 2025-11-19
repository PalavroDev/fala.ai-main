// auth.js - Sistema de autenticação completo usando localStorage

// Função para fazer login
function fazerLogin() {
  const email = document.getElementById('email')?.value.trim();
  const senha = document.getElementById('senha')?.value.trim();

  if (!email || !senha) {
    mostrarErro('Preencha todos os campos.');
    return false;
  }

  const userData = JSON.parse(localStorage.getItem(email));

  if (userData && userData.senha === senha) {
    localStorage.setItem('usuarioLogado', JSON.stringify({
      email: email,
      nome: userData.nome,
      tipoUsuario: userData.tipoUsuario
    }));
    atualizarHeader();
    window.location.href = 'index.html';
    return false;
  } else {
    mostrarErro('Email ou senha incorretos.');
    return false;
  }
}

// Função para exibir erro de login
function mostrarErro(msg) {
  const erroDiv = document.getElementById('erroGlobal');
  if (erroDiv) erroDiv.textContent = msg;
}

// Função para fazer logout
function fazerLogout() {
  localStorage.removeItem('usuarioLogado');
  atualizarHeader();
  window.location.href = 'index.html';
}

// Retorna dados do usuário logado
function getUsuarioLogado() {
  const usuario = localStorage.getItem('usuarioLogado');
  return usuario ? JSON.parse(usuario) : null;
}

// Atualiza o Header/Navegação
function atualizarHeader() {
  const usuarioLogado = getUsuarioLogado();
  const userActions = document.querySelector('.user-actions');

  if (!userActions) return;

  if (usuarioLogado) {
    const inicial = usuarioLogado.nome.charAt(0).toUpperCase();
    userActions.innerHTML = `
      <div class="user-logged">
        <span class="user-name">${usuarioLogado.nome.split(' ')[0]}</span>
        <button onclick="fazerLogout()" class="btn-logout">Sair</button>
      </div>
    `;
  } else {
    userActions.innerHTML = `
      <a href="login.php" class="btn-login">Entrar</a>
      <a href="register.php" class="btn-register">Cadastrar</a>
    `;
  }
}

// Verificar login em páginas protegidas
function verificarAutenticacao() {
  const usuarioLogado = getUsuarioLogado();

  // Exemplo: proteger uma futura página chamada servicos.html
  if (window.location.pathname.includes('servicos.html') && !usuarioLogado) {
    window.location.href = 'index.html';
    return false;
  }

  atualizarHeader();
  return !!usuarioLogado;
}

// Abrir perfil
function abrirPerfil() {
  alert('Página de perfil ainda não disponível.');
}

// Controle do comportamento do Header com scroll (se houver header fixo nas suas páginas)
document.addEventListener('DOMContentLoaded', function () {
  const header = document.querySelector('header');
  let lastScroll = 0;
  if (!header) return;

  const headerHeight = header.offsetHeight;

  window.addEventListener('scroll', function () {
    const currentScroll = window.pageYOffset;

    if (currentScroll <= 0) {
      header.classList.remove('hidden');
      return;
    }

    if (currentScroll > lastScroll && currentScroll > headerHeight) {
      header.classList.add('hidden');
    } else if (currentScroll < lastScroll) {
      header.classList.remove('hidden');
    }

    lastScroll = currentScroll;
  });

  verificarAutenticacao();
});
