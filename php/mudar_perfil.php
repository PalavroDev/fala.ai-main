<?php
session_start();
require_once '../conexao.php';
require_once 'auth_master.php'; 
// OBS: auth_master.php verifica se o usuário é Master e interrompe o script se não for.

// 1. Verifica Parâmetros
if (!isset($_GET['user_id']) || !isset($_GET['new_profile'])) {
    $_SESSION['error_message'] = "Erro: Parâmetros de usuário ou perfil ausentes.";
    header("Location: ../consulta_usuarios.php");
    exit();
}

$user_id = (int)$_GET['user_id'];
$new_profile = trim(strtolower($_GET['new_profile']));

// Validação simples
if ($new_profile !== 'master' && $new_profile !== 'comum') {
    $_SESSION['error_message'] = "Erro: Perfil inválido.";
    header("Location: ../consulta_usuarios.php");
    exit();
}

try {
    // 2. Prevenção: Impede que o Master altere o próprio perfil (para evitar bloqueio)
    if ($_SESSION['user_id'] === $user_id && $new_profile !== 'master') {
        $_SESSION['error_message'] = "Erro: O Master não pode rebaixar a si mesmo.";
        header("Location: ../consulta_usuarios.php");
        exit();
    }
    
    // 3. Atualiza o Perfil no Banco de Dados
    $sql = "UPDATE users SET profile = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$new_profile, $user_id]);

    // 4. Feedback e Redirecionamento
    if ($stmt->rowCount() > 0) {
        $_SESSION['success_message'] = "Perfil do usuário ID " . $user_id . " alterado para **" . ucfirst($new_profile) . "** com sucesso!";
    } else {
        $_SESSION['error_message'] = "Nenhuma alteração feita ou usuário não encontrado.";
    }
    
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Erro ao atualizar o perfil: " . $e->getMessage();
}

header("Location: ../consulta_usuarios.php");
exit();
?>