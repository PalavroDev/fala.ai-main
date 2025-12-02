<?php
session_start();
require_once 'conexao.php';

// Função para validar o CPF (lógica do dígito verificador)
function validaCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/is', '', $cpf);
    if (strlen($cpf) != 11) return false;
    if (preg_match('/(\d)\1{10}/', $cpf)) return false;
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;
}

// Verifica se o formulário foi enviado usando o método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. RECEBER E LIMPAR OS DADOS DO FORMULÁRIO (usando os 'name' do HTML)
    $name = trim($_POST['name']);
    $login = trim($_POST['login']);
    $cpf = trim($_POST['cpf']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST['phone']); 
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $cep = trim($_POST['cep']);
    $street = trim($_POST['street']);
    $number = trim($_POST['number']);
    $complement = trim($_POST['complement']);
    $district = trim($_POST['district']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    
    // 2. VALIDAÇÃO DOS DADOS NO SERVIDOR 
    
    // Nome (8 a 60 caracteres alfabéticos) 
    if (strlen($name) < 8 || strlen($name) > 60 || !preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $_SESSION['error_message'] = "Nome inválido! Deve ter entre 8 e 60 letras.";
        header("Location: ../register.php");
        exit();
    }
    
    // CPF (dígito verificador) 
    if (!validaCPF($cpf)) {
        $_SESSION['error_message'] = "CPF inválido! Verifique o dígito verificador.";
        header("Location: ../register.php");
        exit();
    }

    //  Login (exatamente 6 caracteres alfabéticos) 
    if (strlen($login) != 6 || !ctype_alpha($login)) {
        $_SESSION['error_message'] = "Login inválido! Deve ter exatamente 6 letras.";
        header("Location: ../register.php");
        exit();
    }
    
    //  Senha (8 caracteres) e Confirmação  
    if (strlen($password) != 8) {
        $_SESSION['error_message'] = "Senha inválida! Deve ter exatamente 8 caracteres.";
        header("Location: ../register.php");
        exit();
    }
    if ($password !== $confirm_password) {
        $_SESSION['error_message'] = "Erro: As senhas não conferem!";
        header("Location: ../register.php");
        exit();
    }

    // 3. VERIFICAR SE USUÁRIO JÁ EXISTE NO BANCO DE DADOS
    try {
        $sql_check = "SELECT id FROM users WHERE cpf = ? OR email = ? OR login = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$cpf, $email, $login]);
        if ($stmt_check->fetch()) {
            $_SESSION['error_message'] = "Erro: CPF, E-mail ou Login já cadastrado no sistema.";
            header("Location: ../register.php");
            exit();
        }
    } catch (PDOException $e) {
        die("Erro ao verificar usuário: " . $e->getMessage());
    }

    // 4. CRIPTOGRAFAR A SENHA (REQUISITO DE SEGURANÇA) 
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 5. INSERIR O USUÁRIO NO BANCO DE DADOS
    try {
        // A instrução SQL deve corresponder exatamente às colunas da sua tabela
        $sql_insert = "INSERT INTO users 
            (name, cpf, email, phone, cep, street, number, complement, district, city, state, login, password, profile) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'comum')";

        $stmt_insert = $pdo->prepare($sql_insert);
        
        // A ordem dos valores aqui deve ser a mesma da instrução SQL acima
        $stmt_insert->execute([
            $name,
            $cpf,
            $email,
            $phone,
            $cep,
            $street,
            $number,
            $complement,
            $district,
            $city,
            $state,
            $login,
            $hashed_password
        ]);

        // 6. REDIRECIONAR PARA A TELA DE LOGIN COM MENSAGEM DE SUCESSO 
        $_SESSION['success_message'] = "Cadastro realizado com sucesso! Faça seu login.";
        header("Location: ../login.php");
        exit();

    } catch (PDOException $e) {
        die("Erro ao cadastrar usuário no banco de dados: " . $e->getMessage());
    }

} else {
    header("Location: ../register.php");
    exit();
}
?>