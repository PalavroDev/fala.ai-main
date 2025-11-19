<?php
// Certifica-se de que a sessão já está iniciada (deve ser chamada na página que o inclui)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário NÃO está logado
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Acesso negado. Faça login para continuar.";
    header("Location: login.php");
    exit();
}

// Verifica se o usuário NÃO tem o perfil 'master'
if ($_SESSION['user_profile'] !== 'master') {
    $_SESSION['error_message'] = "Acesso restrito. Sua conta não possui permissão de Master.";
    header("Location: index.php"); // Redireciona para a página inicial
    exit();
}

// Se chegou até aqui, o usuário é Master e o script continua
?>