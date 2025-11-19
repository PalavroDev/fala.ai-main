<?php
// 1. Controle de Acesso
session_start();
require_once '../conexao.php';

// Verifica se o usuário é Master
if (!isset($_SESSION['user_id']) || $_SESSION['user_profile'] !== 'master') {
    $_SESSION['error_message'] = "Permissão negada.";
    header("Location: ../index.php");
    exit();
}

// Verifica se o ID foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
    $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);

    if ($user_id === false) {
        $_SESSION['message'] = "ID de usuário inválido.";
        header("Location: ../consulta_usuarios.php");
        exit();
    }
    
    try {
        // 2. EXECUTAR EXCLUSÃO
        // O perfil 'master' só pode excluir perfis 'comum'.
        $sql = "DELETE FROM users WHERE id = ? AND profile = 'comum'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = "Usuário excluído com sucesso.";
        } else {
            $_SESSION['message'] = "Erro: Usuário não encontrado ou você tentou excluir um perfil Master.";
        }
        
    } catch (PDOException $e) {
        $_SESSION['message'] = "Erro no banco de dados durante a exclusão: " . $e->getMessage();
    }

    // Redireciona de volta para a tela de consulta
    header("Location: ../consulta_usuarios.php");
    exit();

} else {
    $_SESSION['message'] = "Requisição inválida.";
    header("Location: ../consulta_usuarios.php");
    exit();
}
?>