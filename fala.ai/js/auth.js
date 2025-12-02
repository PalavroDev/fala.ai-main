// auth.js - Funções de autenticação reutilizáveis

// Função para atualizar a área de usuário na navbar
function atualizarHeader() {
  const usuarioLogado = JSON.parse(localStorage.getItem("usuarioLogado"));

  const userActions = document.querySelector(".user-actions");
  if (!userActions) return;

  if (usuarioLogado) {
    userActions.innerHTML = `
      <span>Bem-vindo, ${usuarioLogado.login}</span>
      <button onclick="logout()" type="button">Sair</button>
    `;
  } else {
    userActions.innerHTML = `<a href="login.html">Login</a>`;
  }
}

// Função para realizar logout
function logout() {
  localStorage.removeItem("usuarioLogado");
  location.reload();
}

// Função para buscar usuário por login ou CPF
function buscarUsuarioPorLogin(login) {
  const usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];
  return usuarios.find(u => u.login === login);
}

// Função para fazer login e salvar no localStorage
function fazerLogin(login, senha) {
  const usuario = buscarUsuarioPorLogin(login);

  if (usuario && usuario.senha === senha) {
    localStorage.setItem("usuarioLogado", JSON.stringify({
      nome: usuario.nome,
      login: usuario.login,
      cpf: usuario.cpf,
      email: usuario.email
    }));
    return true;
  }

  return false;
}