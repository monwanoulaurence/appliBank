<?php
// login.php
require 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validation des entrées
    if (empty($username) || empty($password)) {
        $error = "Tous les champs sont requis.";
    } else {
        $users = loadData('data/users.json');
        $found = false;
        foreach ($users as $user) {
            if ($user['username'] === $username && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: dashboard.php');
                exit();
            }
        }
        if (!$found) {
            $error = "Nom d'utilisateur ou mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Connexion - Banque</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Se Connecter</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <label>Nom d'utilisateur:</label>
            <input type="text" name="username" required>
            
            <label>Mot de passe:</label>
            <input type="password" name="password" required>
            
            <button type="submit">Se Connecter</button>
        </form>
        <p>Pas encore de compte? <a href="register.php">S'inscrire</a></p>
    </div>
</body>
</html>
