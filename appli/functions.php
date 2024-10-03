<?php
// functions.php

session_start();

/**
 * Charge les données depuis un fichier JSON.
 */
function loadData($file) {
    if (!file_exists($file)) {
        return [];
    }
    $json = file_get_contents($file);
    return json_decode($json, true) ?: [];
}

/**
 * Sauvegarde les données dans un fichier JSON de manière sécurisée.
 */
function saveData($file, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT);
    // Utiliser un verrou pour éviter les écritures concurrentes
    $fp = fopen($file, 'w');
    if (flock($fp, LOCK_EX)) {
        fwrite($fp, $json);
        fflush($fp);
        flock($fp, LOCK_UN);
    }
    fclose($fp);
}

/**
 * Génère un nouvel ID unique basé sur les données existantes.
 */
function generateId($data) {
    if (empty($data)) {
        return 1;
    }
    $ids = array_column($data, 'id');
    return max($ids) + 1;
}

/**
 * Génère un numéro de compte unique.
 */
function generateAccountNumber() {
    return 'AC' . rand(10000000, 99999999);
}

/**
 * Vérifie si l'utilisateur est connecté.
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Redirige vers la page de connexion si l'utilisateur n'est pas connecté.
 */
function ensureLoggedIn() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}
?>
