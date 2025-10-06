<?php
session_start(); // Démarre la session PHP

// Identifiants valides pour l'administration (À MODIFIER ET À RENDRE PLUS SÛR EN PRODUCTION)
$VALID_USER = 'admin';
$VALID_PASS = 'admin'; 

// Récupération des données du formulaire POST
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Vérification des identifiants
if ($username === $VALID_USER && $password === $VALID_PASS) {
    // Connexion réussie
    $_SESSION['logged_in'] = true;
    $_SESSION['username'] = $VALID_USER; // Stocke l'utilisateur dans la session
    
    // Redirection vers la page d'administration
    header('Location: admin.php');
    exit;
} else {
    // Échec de la connexion
    
    // Redirection vers la page de connexion avec un message d'erreur
    header('Location: login.php?error=invalid');
    exit;
}
?>