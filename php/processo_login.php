<?php
session_start();
require_once '../conexao.php';

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. RECEBER DADOS DO FORMULÁRIO
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);

    // Validação básica se os campos foram preenchidos
    if (empty($login) || empty($password)) {
        $_SESSION['error_message'] = "Login e Senha são obrigatórios!";
        header("Location: ../login.php");
        exit();
    }

    try {
        // 2. BUSCAR O USUÁRIO PELO LOGIN NO BANCO DE DADOS
        $sql = "SELECT id, name, cpf, login, password, profile FROM users WHERE login = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $login_successful = false;
        // 3. VERIFICAR SE O USUÁRIO EXISTE E SE A SENHA ESTÁ CORRETA
        if ($user && password_verify($password, $user['password'])) {
            // Senha correta! Login bem-sucedido.
            $login_successful = true;

            // 4. ARMAZENAR DADOS DO USUÁRIO NA SESSÃO
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_login'] = $user['login']; 
            $_SESSION['user_profile'] = $user['profile'];

            // Redirecionar para a página principal após o log
            // Futuramente, você pode redirecionar para dashboards diferentes
            header("Location: ../index.php");
            // exit() é importante para parar a execução do script após o redirecionamento
        
        } else {
            // Usuário não encontrado ou senha incorreta
            $_SESSION['error_message'] = "Login ou Senha inválidos.";
            header("Location: ../login.php");
        }

        // 5. REGISTRAR O LOG DE ACESSO 
        // Isso é feito para tentativas bem-sucedidas ou falhas
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        
        // Se o usuário foi encontrado, usamos os dados dele, senão usamos dados genéricos
        $user_id = $user ? $user['id'] : 0; 
        $user_name = $user ? $user['name'] : 'N/A';
        $user_cpf = $user ? $user['cpf'] : 'N/A';
        $user_login_log = $login;

        $sql_log = "INSERT INTO access_logs (user_id, user_name, user_cpf, user_login, ip_address, user_agent, login_successful) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_log = $pdo->prepare($sql_log);
        $stmt_log->execute([
            $user_id,
            $user_name,
            $user_cpf,
            $user_login_log,
            $ip_address,
            $user_agent,
            (int)$login_successful 
        ]);

        exit(); 

    } catch (PDOException $e) {
        die("Erro durante a autenticação: " . $e->getMessage());
    }

} else {
    // Se não for POST, redireciona para a página de login
    header("Location: ../login.php");
    exit();
}
?>