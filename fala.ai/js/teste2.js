// teste2.js

function mostrarPopupErro(mensagem) {
  const msgEl = document.getElementById("mensagemErro");
  const popup = document.getElementById("popupErro");
  if (msgEl) msgEl.textContent = mensagem;
  if (popup) popup.style.display = "block";
}

document.getElementById("fecharPopup")?.addEventListener("click", function () {
  document.getElementById("popupErro").style.display = "none";
});

window.addEventListener("click", function (event) {
  const modal = document.getElementById("popupErro");
  if (modal && event.target === modal) modal.style.display = "none";
});

function validarIdade(dataNascimento) {
  const regex = /^(\d{2})\/(\d{2})\/(\d{4})$/;
  if (!regex.test(dataNascimento)) {
    return { valido: false, mensagem: "Formato inválido. Use DD/MM/AAAA." };
  }

  const [dia, mes, ano] = dataNascimento.split('/').map(Number);
  const dataUsuario = new Date(ano, mes - 1, dia);

  if (
    dataUsuario.getDate() !== dia ||
    dataUsuario.getMonth() !== mes - 1 ||
    dataUsuario.getFullYear() !== ano
  ) {
    return { valido: false, mensagem: "Data inválida." };
  }

  const hoje = new Date();
  if (dataUsuario > hoje) {
    return { valido: false, mensagem: "Data futura não é permitida." };
  }

  let idade = hoje.getFullYear() - ano;
  const mesAtual = hoje.getMonth();
  const diaAtual = hoje.getDate();

  if (mesAtual < mes - 1 || (mesAtual === mes - 1 && diaAtual < dia)) {
    idade--;
  }

  const idadeMinima = 16;
  if (idade < idadeMinima) {
    return { valido: false, mensagem: `Você deve ter pelo menos ${idadeMinima} anos.` };
  }

  return { valido: true, mensagem: "" };
}

function validarCPF(cpf) {
  cpf = cpf.replace(/\D/g, "");
  if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) return false;

  let soma = 0;
  for (let i = 0; i < 9; i++) {
    soma += parseInt(cpf.charAt(i)) * (10 - i);
  }
  let resto = (soma * 10) % 11;
  resto = resto === 10 || resto === 11 ? 0 : resto;
  if (resto !== parseInt(cpf.charAt(9))) return false;

  soma = 0;
  for (let i = 0; i < 10; i++) {
    soma += parseInt(cpf.charAt(i)) * (11 - i);
  }
  resto = (soma * 10) % 11;
  resto = resto === 10 || resto === 11 ? 0 : resto;
  if (resto !== parseInt(cpf.charAt(10))) return false;

  return true;
}

// Função nova: validarLogin()
function validarLogin(login, senha) {
  if (!login || login.trim().length < 6) {
    mostrarPopupErro("O login deve ter 6 caracteres.");
    return false;
  }

  if (!senha || senha.trim().length < 8 ) {
    mostrarPopupErro("A senha deve ter 8 caracteres.");
    return false;
  }

  return true;
}

// Validação do formulário de cadastro
document.addEventListener("DOMContentLoaded", function () {
  const nameInput = document.querySelector('#nome');
  const loginInput = document.querySelector('#login');
  const cpfInput = document.querySelector('#cpf');
  const celularInput = document.querySelector('#celular');
  const emailInput = document.querySelector('#email');
  const senhaInput = document.querySelector('#senha');
  const senhaconfInput = document.querySelector('#confirmaSenha');
  const submitBtn = document.querySelector('.submit-btn');

  submitBtn?.addEventListener('click', function (e) {
    e.preventDefault();

    const nameValue = nameInput.value.trim();
    const loginValue = loginInput.value.trim();
    const cpfValue = cpfInput.value.trim();
    const celularValue = celularInput.value.trim();
    const emailValue = emailInput.value.trim();
    const senhaValue = senhaInput.value.trim();
    const senhaconfValue = senhaconfInput.value.trim();

    if (nameValue.length < 15 || nameValue.length > 80 || !nameValue) {
      mostrarPopupErro("Nome deve ter entre 15 e 80 caracteres.");
      return;
    }

    if (loginValue.length !== 6) {
      mostrarPopupErro("Login deve ter exatamente 6 caracteres.");
      return;
    }

    const cpfLimpo = cpfValue.replace(/\D/g, "");
    if (cpfLimpo.length !== 11 || !validarCPF(cpfLimpo)) {
      mostrarPopupErro("CPF inválido.");
      return;
    }

    if (celularValue.length !== 19) {
      mostrarPopupErro("Telefone celular inválido.");
      return;
    }

    if (senhaValue !== senhaconfValue) {
      mostrarPopupErro("As senhas não coincidem.");
      return;
    }

    const nascValue = document.querySelector('#nasc')?.value.trim();
    const resultado = validarIdade(nascValue);
    if (!resultado.valido) {
      mostrarPopupErro(resultado.mensagem);
      return;
    }

    const usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];
    const usuarioExistente = usuarios.find(u => u.login === loginValue || u.cpf === cpfLimpo);
    if (usuarioExistente) {
      mostrarPopupErro("Já existe um usuário com este login ou CPF.");
      return;
    }

    const novoUsuario = {
      nome: nameValue,
      login: loginValue,
      cpf: cpfLimpo,
      telefone: celularValue,
      email: emailValue,
      senha: senhaValue
    };

    usuarios.push(novoUsuario);
    localStorage.setItem("usuarios", JSON.stringify(usuarios));

    alert("Cadastro realizado com sucesso!");
    window.location.href = "login.html";
  });
});





  function limpa_formulário_cep() {
            //Limpa valores do formulário de cep.
            document.getElementById('rua').value=("");
            document.getElementById('bairro').value=("");
            document.getElementById('cidade').value=("");
            document.getElementById('uf').value=("");
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

// Limpa campos de endereço em caso de erro
function limparCamposCEP() {
  document.getElementById("rua").value = "";
  document.getElementById("bairro").value = "";
  document.getElementById("cidade").value = "";
  document.getElementById("uf").value = "";
}
function buscarCEPManualmente() {
  const inputCep = document.getElementById("cep");
  let cep = inputCep.value.replace(/\D/g, "");
  if (cep.length === 8) {
    // Chama a mesma lógica do fetch
    executarBuscaCEP(cep);
  } else {
    mostrarPopupErro("Digite um CEP válido.");
  }
}

function executarBuscaCEP(cep) {
  fetch(`https://viacep.com.br/ws/${cep}/json/`) 
    .then(response => response.json())
    .then(data => {
      if (data.erro) {
        limparCamposCEP();
        mostrarPopupErro("CEP não encontrado.");
      } else {
        document.getElementById("rua").value = data.logradouro || "";
        document.getElementById("bairro").value = data.bairro || "";
        document.getElementById("cidade").value = data.localidade || "";
        document.getElementById("uf").value = data.uf || "";
      }
    })
    .catch(() => {
      limparCamposCEP();
      mostrarPopupErro("Erro ao consultar o CEP.");
    });
}

function limparCampos() {
  document.getElementById("cadastroForm").reset();
  limparErros();
  console.log("Campos do formulário limpos"); // Debug
}