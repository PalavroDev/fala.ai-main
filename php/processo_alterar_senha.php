<?php
session_start();
require_once '../conexao.php';

// 1. Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Sua sessão expirou. Faça login novamente.";
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recebe e limpa os dados
    $user_id = $_SESSION['user_id'];
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_new_password = trim($_POST['confirm_new_password']);
    
    // 2. Validação da Nova Senha (8 caracteres)
    if (strlen($new_password) != 8) {
        $_SESSION['error_message'] = "A nova senha deve ter exatamente 8 caracteres.";
        header("Location: ../alterar_senha.php");
        exit();
    }
    if ($new_password !== $confirm_new_password) {
        $_SESSION['error_message'] = "A confirmação da nova senha não confere.";
        header("Location: ../alterar_senha.php");
        exit();
    }
    
    try {
        // 3. Buscar a senha atual criptografada do usuário
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // Este erro só deve ocorrer se o registro do usuário foi excluído
            $_SESSION['error_message'] = "Erro interno: Usuário não encontrado.";
            header("Location: ../alterar_senha.php");
            exit();
        }

        // 4. Verificar se a senha atual fornecida está correta (usando password_verify)
        if (!password_verify($current_password, $user['password'])) {
            $_SESSION['error_message'] = "Senha atual incorreta.";
            header("Location: ../alterar_senha.php");
            exit();
        }

        // 5. Criptografar e Atualizar a nova senha
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $sql_update = "UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$hashed_new_password, $user_id]);

        $_SESSION['success_message'] = "Senha alterada com sucesso! Você deve usar a nova senha no próximo login.";
        header("Location: ../alterar_senha.php");
        exit();

    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Erro no banco de dados durante a atualização: " . $e->getMessage();
        header("Location: ../alterar_senha.php");
        exit();
    }

} else {
    header("Location: ../alterar_senha.php");
    exit();
}
?>