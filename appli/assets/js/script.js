// assets/js/script.js

// Fonction pour valider le formulaire d'inscription
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.querySelector('form[action="register.php"]');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert("Les mots de passe ne correspondent pas.");
            }
        });
    }

    // Fonction pour valider le formulaire de transfert
    const transferForm = document.querySelector('form[action="transfer.php"]');
    if (transferForm) {
        transferForm.addEventListener('submit', function(e) {
            const amount = parseFloat(document.querySelector('input[name="amount"]').value);
            if (isNaN(amount) || amount <= 0) {
                e.preventDefault();
                alert("Veuillez entrer un montant valide supérieur à 0.");
            }
        });
    }
});
