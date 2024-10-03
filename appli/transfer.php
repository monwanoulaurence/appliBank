<?php
// transfer.php
require 'functions.php';
ensureLoggedIn();

$user_id = $_SESSION['user_id'];
$accounts = loadData('data/accounts.json');
$transactions = loadData('data/transactions.json');

// Récupérer les comptes de l'utilisateur
$user_accounts = array_filter($accounts, function($account) use ($user_id) {
    return $account['user_id'] == $user_id;
});

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $source_id = (int)$_POST['source'];
    $destination_account_number = trim($_POST['destination']);
    $amount = floatval($_POST['amount']);

    // Validation
    if ($amount <= 0) {
        $error = "Le montant doit être supérieur à 0.";
    } elseif ($source_id == 0 || empty($destination_account_number)) {
        $error = "Veuillez sélectionner un compte source et un compte destinataire.";
    } else {
        // Trouver le compte source
        $source_account = null;
        foreach ($user_accounts as $account) {
            if ($account['id'] == $source_id) {
                $source_account = &$account;
                break;
            }
        }

        if (!$source_account) {
            $error = "Compte source invalide.";
        } else {
            // Charger tous les comptes pour trouver le destinataire
            $all_accounts = loadData('data/accounts.json');
            $destination_account = null;
            foreach ($all_accounts as &$acc) {
                if ($acc['account_number'] === $destination_account_number) {
                    $destination_account = &$acc;
                    break;
                }
            }

            if (!$destination_account) {
                $error = "Compte destinataire non trouvé.";
            } elseif ($source_account['id'] == $destination_account['id']) {
                $error = "Vous ne pouvez pas transférer vers le même compte.";
            } elseif ($source_account['balance'] < $amount) {
                $error = "Solde insuffisant.";
            } else {
                // Effectuer le transfert
                // Mettre à jour les soldes
                foreach ($all_accounts as &$acc) {
                    if ($acc['id'] == $source_account['id']) {
                        $acc['balance'] -= $amount;
                    }
                    if ($acc['id'] == $destination_account['id']) {
                        $acc['balance'] += $amount;
                    }
                }

                // Sauvegarder les comptes mis à jour
                saveData('data/accounts.json', $all_accounts);

                // Enregistrer la transaction
                $newTransaction = [
                    'id' => generateId($transactions),
                    'from_account' => $source_account['account_number'],
                    'to_account' => $destination_account['account_number'],
                    'amount' => $amount,
                    'transaction_type' => 'transfer',
                    'created_at' => date('c')
                ];
                $transactions[] = $newTransaction;
                saveData('data/transactions.json', $transactions);

                $success = "Transfert réussi !";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transfert - Banque</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Effectuer un Transfert</h2>
        <a href="dashboard.php" class="btn">Retour au Tableau de Bord</a>
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php elseif (isset($success)): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form method="POST" action="transfer.php">
            <label>Compte Source:</label>
            <select name="source" required>
                <option value="">-- Sélectionnez un compte --</option>
                <?php foreach ($user_accounts as $account): ?>
                    <option value="<?= htmlspecialchars($account['id']) ?>">
                        <?= htmlspecialchars($account['account_number']) ?> (<?= number_format($account['balance'], 2) ?> €)
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Compte Destinataire (Numéro de Compte):</label>
            <input type="text" name="destination" required pattern="^AC\d{8}$" placeholder="Ex: AC12345678">

            <label>Montant (€):</label>
            <input type="number" name="amount" step="0.01" min="0.01" required>

            <button type="submit">Transférer</button>
        </form>
    </div>
</body>
</html>
