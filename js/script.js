// Função para validar o formulário antes de salvar
function validarFormulario() {
  console.log("Iniciando validação do formulário..."); // Debug
  
  let valido = true;
  limparErros();

  // Obter valores dos campos
  const nome = document.getElementById("nome").value.trim();
  const nasc = document.getElementById("nasc").value.trim();
  const sexo = document.getElementById("sexo").value;
  const login = document.getElementById("login").value.trim();
  const cpf = document.getElementById("cpf").value.trim();
  const email = document.querySelector(".input-field[placeholder='Email*']").value.trim();
  const celular = document.getElementById("celular").value.trim();
  const senha = document.getElementById("senha").value;
  const confirmaSenha = document.getElementById("confirmaSenha").value;
  const cep = document.getElementById("cep").value.trim();
  
  console.log("Valores obtidos:", {nome, login, cpf}); // Debug

  // Validações dos campos
  if (nome.length < 15 || nome.length > 80) {
    mostrarErro("erro_nome", "Nome deve ter entre 15 e 80 caracteres");
    valido = false;
    console.log("Validação do nome falhou"); // Debug
  }

  if (!/^\d{2}\/\d{2}\/\d{4}$/.test(nasc)) {
    alert("Data de nascimento inválida. Use o formato dd/mm/aaaa.");
    valido = false;
  }

  if (sexo === "") {
    alert("Selecione o sexo");
    valido = false;
  }

  if (login.length !== 6) {
    mostrarErro("erro_login", "Login deve ter exatamente 6 caracteres");
    valido = false;
  }

  const cpfLimpo = cpf.replace(/\D/g, "");
  if (cpfLimpo.length !== 11 || !validarCPF(cpfLimpo)) {
    mostrarErro("erro_cpf", "CPF inválido");
    valido = false;
  }

  if (!validarEmail(email)) {
    alert("Email inválido");
    valido = false;
  }



  if (senha.length < 6 || senha.length > 8) {
    alert("Senha deve ter entre 6 e 8 caracteres");
    valido = false;
  }

  if (senha !== confirmaSenha) {
    alert("Confirmação de senha não confere");
    valido = false;
  }

  if (!valido) {
    console.log("Formulário inválido, não será enviado"); // Debug
    return false;
  }

  // Verificar se usuário já existe
  const usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];
  if (usuarios.some(u => u.login === login)) {
    alert("Login já cadastrado. Escolha outro login.");
    return false;
  }

  if (usuarios.some(u => u.cpf === cpfLimpo)) {
    alert("CPF já cadastrado.");
    return false;
  }

  // Criar objeto usuário
  const usuario = {
    nome,
    nasc,
    sexo,
    login,
    cpf: cpfLimpo,
    email,
    celular: celularLimpo,
    senha,
    cep,
    rua: document.getElementById("rua").value.trim(),
    bairro: document.getElementById("bairro").value.trim(),
    cidade: document.getElementById("cidade").value.trim(),
    uf: document.getElementById("uf").value.trim(),
    dataCadastro: new Date().toISOString()
  };

  console.log("Usuário a ser cadastrado:", usuario); // Debug

  // Salvar no localStorage
  try {
    usuarios.push(usuario);
    localStorage.setItem("usuarios", JSON.stringify(usuarios));
    console.log("Usuário salvo com sucesso no localStorage"); // Debug
    alert("Cadastro realizado com sucesso!");
    window.location.href = "login.html";
  } catch (e) {
    console.error("Erro ao salvar no localStorage:", e); // Debug
    alert("Erro ao cadastrar. Tente novamente.");
  }

  return false;
}

// Funções auxiliares de validação
function validarCPF(cpf) {
  if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) return false;
  
  let soma = 0;
  for (let i = 0; i < 9; i++) {
    soma += parseInt(cpf.charAt(i)) * (10 - i);
  }
  let resto = (soma * 10) % 11;
  if (resto === 10 || resto === 11) resto = 0;
  if (resto !== parseInt(cpf.charAt(9))) return false;

  soma = 0;
  for (let i = 0; i < 10; i++) {
    soma += parseInt(cpf.charAt(i)) * (11 - i);
  }
  resto = (soma * 10) % 11;
  if (resto === 10 || resto === 11) resto = 0;
  if (resto !== parseInt(cpf.charAt(10))) return false;

  return true;
}

function validarEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// Funções para exibir/limpar erros
function mostrarErro(idSpan, msg) {
  const span = document.getElementById(idSpan);
  if (span) {
    span.textContent = msg;
    span.style.color = "red";
    console.log(`Erro mostrado em ${idSpan}: ${msg}`); // Debug
  }
}

function limparErros() {
  const spans = document.querySelectorAll(".erro");
  spans.forEach(s => {
    s.textContent = "";
  });
}

// Função para limpar campos
function limparCampos() {
  document.getElementById("cadastroForm").reset();
  limparErros();
  console.log("Campos do formulário limpos"); // Debug
}

// ======================
// Funções para CEP (ViaCEP)
// ======================
function limpa_formulário_cep() {
  document.getElementById("rua").value = "";
  document.getElementById("bairro").value = "";
  document.getElementById("cidade").value = "";
  document.getElementById("uf").value = "";
}

function meu_callback(conteudo) {
  if (!("erro" in conteudo)) {
    document.getElementById("rua").value = conteudo.logradouro;
    document.getElementById("bairro").value = conteudo.bairro;
    document.getElementById("cidade").value = conteudo.localidade;
    document.getElementById("uf").value = conteudo.uf;
  } else {
    limpa_formulário_cep();
    alert("CEP não encontrado.");
  }
}

function pesquisacep(cep) {
  cep = cep.replace(/\D/g, '');

  if (cep !== "") {
    const validacep = /^[0-9]{8}$/;

    if (validacep.test(cep)) {
      document.getElementById('rua').value = "...";
      document.getElementById('bairro').value = "...";
      document.getElementById('cidade').value = "...";
      document.getElementById('uf').value = "...";

      const script = document.createElement('script');
      script.src = 'https://viacep.com.br/ws/' + cep + '/json/?callback=meu_callback';
      document.body.appendChild(script);
    } else {
      limpa_formulário_cep();
      alert("Formato de CEP inválido.");
    }
  } else {
    limpa_formulário_cep();
  }
}

// Adiciona event listener para o formulário
document.addEventListener("DOMContentLoaded", function() {
  console.log("DOM carregado, adicionando event listeners"); // Debug
  
  const form = document.getElementById("cadastroForm");
  if (form) {
    form.addEventListener("submit", function(e) {
      e.preventDefault();
      console.log("Formulário submetido"); // Debug
      validarFormulario();
    });
  } else {
    console.error("Formulário não encontrado!"); // Debug
  }

  // Teste do localStorage
  try {
    localStorage.setItem("teste_storage", "funcionando");
    const teste = localStorage.getItem("teste_storage");
    console.log("Teste localStorage:", teste); // Debug
    localStorage.removeItem("teste_storage");
  } catch (e) {
    console.error("Erro no localStorage:", e); // Debug
    alert("Seu navegador não suporta localStorage ou está bloqueado. Não é possível cadastrar.");
  }
});

// Função para atualizar a navbar com o estado de login
function atualizarNavbar() {
  const userArea = document.getElementById('userArea');
  if (!userArea) return;

  const usuarioLogado = JSON.parse(localStorage.getItem('usuarioLogado'));
  
  if (usuarioLogado) {
    userArea.innerHTML = `
      <span style="margin-right: 10px;">${usuarioLogado.nome.split(' ')[0]}</span>
      <button onclick="logout()" class="logout-btn">Sair</button>
    `;
  } else {
    userArea.innerHTML = '<a href="login.html">Login/Cadastrar</a>';
  }
}

// Função de logout
function logout() {
  localStorage.removeItem('usuarioLogado');
  atualizarNavbar();
  window.location.href = 'index.html';
}

// Modifique a função validarFormulario() para logar o usuário após cadastro
// Na parte final da função, onde tem o redirecionamento:
localStorage.setItem("usuarios", JSON.stringify(usuarios));

// Adicione esta linha para logar o usuário automaticamente após cadastro
localStorage.setItem("usuarioLogado", JSON.stringify(usuario));
console.log("Usuário cadastrado e logado com sucesso");

// Atualize a navbar antes de redirecionar
atualizarNavbar();
window.location.href = "index.html";

function limpa_formulário_cep() {
            //Limpa valores do formulário de cep.
            document.getElementById('rua').value=("");
            document.getElementById('bairro').value=("");
            document.getElementById('cidade').value=("");
            document.getElementById('uf').value=("");
            document.getElementById('ibge').value=("");
    }

    function meu_callback(conteudo) {
        if (!("erro" in conteudo)) {
            //Atualiza os campos com os valores.
            document.getElementById('rua').value=(conteudo.logradouro);
            document.getElementById('bairro').value=(conteudo.bairro);
            document.getElementById('cidade').value=(conteudo.localidade);
            document.getElementById('uf').value=(conteudo.uf);
            document.getElementById('ibge').value=(conteudo.ibge);
        } //end if.
        else {
            //CEP não Encontrado.
            limpa_formulário_cep();
            alert("CEP não encontrado.");
        }
    }
        
    function pesquisacep(valor) {

        //Nova variável "cep" somente com dígitos.
        var cep = valor.replace(/\D/g, '');

        //Verifica se campo cep possui valor informado.
        if (cep != "") {

            //Expressão regular para validar o CEP.
            var validacep = /^[0-9]{8}$/;

            //Valida o formato do CEP.
            if(validacep.test(cep)) {

                //Preenche os campos com "..." enquanto consulta webservice.
                document.getElementById('rua').value="...";
                document.getElementById('bairro').value="...";
                document.getElementById('cidade').value="...";
                document.getElementById('uf').value="...";
                document.getElementById('ibge').value="...";

                //Cria um elemento javascript.
                var script = document.createElement('script');

                //Sincroniza com o callback.
                script.src = 'https://viacep.com.br/ws/'+ cep + '/json/?callback=meu_callback';

                //Insere script no documento e carrega o conteúdo.
                document.body.appendChild(script);

            } //end if.
            else {
                //cep é inválido.
                limpa_formulário_cep();
                alert("Formato de CEP inválido.");
            }
        } //end if.
        else {
            //cep sem valor, limpa formulário.
            limpa_formulário_cep();
        }
    };
