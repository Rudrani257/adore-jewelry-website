<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($action === 'login') {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            header("Location: index.php");
            exit;
        } else {
            // Failed login
            header("Location: index.php?error=login_failed");
            exit;
        }
    } elseif ($action === 'signup') {
        $name = $_POST['name'] ?? '';
        $hash = password_hash($password, PASSWORD_BCRYPT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hash]);
            
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $name;
            $_SESSION['user_role'] = 'user';
            
            header("Location: index.php");
            exit;
        } catch(PDOException $e) {
            header("Location: index.php?error=signup_failed");
            exit;
        }
    }
}

header("Location: index.php");
exit;
?>
