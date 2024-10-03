<?php
// register.php
require 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation des entrées
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "Tous les champs sont requis.";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        $users = loadData('data/users.json');
        // Vérifier si le nom d'utilisateur existe déjà
        foreach ($users as $user) {
            if ($user['username'] === $username) {
                $error = "Nom d'utilisateur déjà pris.";
                break;
            }
        }

        if (!isset($error)) {
            $newUser = [
                'id' => generateId($users),
                'username' => htmlspecialchars($username, ENT_QUOTES, 'UTF-8'),
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'created_at' => date('c')
            ];
            $users[] = $newUser;
            saveData('data/users.json', $users);

            // Créer un compte initial pour l'utilisateur
            $accounts = loadData('data/accounts.json');
            $newAccount = [
                'id' => generateId($accounts),
                'user_id' => $newUser['id'],
                'account_number' => generateAccountNumber(),
                'balance' => 0.00,
                'created_at' => date('c')
            ];
            $accounts[] = $newAccount;
            saveData('data/accounts.json', $accounts);

            $_SESSION['user_id'] = $newUser['id'];
            $_SESSION['username'] = $newUser['username'];
            header('Location: dashboard.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inscription - Banque</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Créer un Compte</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="register.php">
            <label>Nom d'utilisateur:</label>
            <input type="text" name="username" required>
            
            <label>Mot de passe:</label>
            <input type="password" name="password" required>
            
            <label>Confirmer le mot de passe:</label>
            <input type="password" name="confirm_password" required>
            
            <button type="submit">S'inscrire</button>
        </form>
        <p>Déjà inscrit? <a href="login.php">Se connecter</a></p>
    </div>
</body>
</html>
