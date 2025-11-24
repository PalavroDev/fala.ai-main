<?php
session_start();
require_once '../conexao.php';

// Controle de Acesso (Master)
if (!isset($_SESSION['user_id']) || $_SESSION['user_profile'] !== 'master') {
    $_SESSION['error_message'] = "Acesso negado para esta operação.";
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'create') {
        $name = trim($_POST['name']);
        $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
        $description = trim($_POST['description']);
        $features = trim($_POST['features']);
        
        if (empty($name) || $price === false || empty($features)) {
            $_SESSION['error_message'] = "Erro: Nome, Preço e Recursos são obrigatórios.";
            header("Location: ../cadastro_planos.php");
            exit();
        }

        try {
            $sql = "INSERT INTO plans (name, description, price, features, is_active) VALUES (?, ?, ?, ?, 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $description, $price, $features]);

            $_SESSION['success_message'] = "Plano '{$name}' cadastrado com sucesso!";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Erro ao cadastrar plano: " . $e->getMessage();
        }

    } elseif ($action === 'delete' && isset($_POST['plan_id'])) {
        $plan_id = filter_var($_POST['plan_id'], FILTER_VALIDATE_INT);
        
        if ($plan_id === false) {
             $_SESSION['error_message'] = "ID do plano inválido para exclusão.";
             header("Location: ../cadastro_planos.php");
             exit();
        }

        try {
            $sql = "DELETE FROM plans WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$plan_id]);

            $_SESSION['success_message'] = "Plano excluído com sucesso!";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Erro ao excluir plano: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_message'] = "Ação inválida.";
    }

    header("Location: ../cadastro_planos.php");
    exit();
} else {
    header("Location: ../cadastro_planos.php");
    exit();
}
?>