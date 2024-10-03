<?php
// dashboard.php
require 'functions.php';
ensureLoggedIn();

$user_id = $_SESSION['user_id'];
$accounts = loadData('data/accounts.json');

// Récupérer les comptes de l'utilisateur
$user_accounts = array_filter($accounts, function($account) use ($user_id) {
    return $account['user_id'] == $user_id;
});
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tableau de Bord - Banque</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Bienvenue, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
        <a href="logout.php" class="btn">Déconnexion</a>
        <h3>Vos Comptes</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Numéro de Compte</th>
                    <th>Solde (€)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($user_accounts as $account): ?>
                    <tr>
                        <td><?= htmlspecialchars($account['id']) ?></td>
                        <td><?= htmlspecialchars($account['account_number']) ?></td>
                        <td><?= number_format($account['balance'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="transfer.php" class="btn">Effectuer un Transfert</a>
    </div>
</body>
</html>
