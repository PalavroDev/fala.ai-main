document.addEventListener("DOMContentLoaded", function () {
  // Verificar se já está logado
  const usuarioLogado = JSON.parse(localStorage.getItem("usuarioLogado"));
  if (usuarioLogado) {
    window.location.href = "index.html";
  }

  // Função de login
  document
    .getElementById("loginForm")
    ?.addEventListener("submit", function (e) {
      e.preventDefault();

      const login = document.getElementById("login").value;
      const senha = document.getElementById("senha").value;
      const usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];

      const usuario = usuarios.find(
        (u) => u.login === login && u.senha === senha
      );

      if (usuario) {
        localStorage.setItem("usuarioLogado", JSON.stringify(usuario));
        atualizarNavbar();
        window.location.href = "index.html";
      } else {
        alert("Login ou senha incorretos");
      }
    });
});

// Função para atualizar a navbar (a mesma do script.js)
function atualizarNavbar() {
  const userArea = document.getElementById("userArea");
  if (!userArea) return;

  const usuarioLogado = JSON.parse(localStorage.getItem("usuarioLogado"));

  if (usuarioLogado) {
    userArea.innerHTML = `
      <span style="margin-right: 10px;">${
        usuarioLogado.nome.split(" ")[0]
      }</span>
      <button onclick="logout()" class="logout-btn">Sair</button>
    `;
  } else {
    userArea.innerHTML = '<a href="login.php">Login/Cadastrar</a>';
  }
}
