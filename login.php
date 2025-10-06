<?php
session_start();
// Redirige l'utilisateur vers admin.php s'il est déjà connecté
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: admin.php');
    exit;
}

$error_message = '';
if (isset($_GET['error']) && $_GET['error'] === 'invalid') {
    $error_message = '<p class="error-message">Identifiants incorrects. Veuillez réessayer.</p>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin</title>
    <style>
        /* Définition des couleurs principales */
        :root {
            --primary-color: #004d40; /* Vert foncé de l'école */
            --secondary-color: #ffb300; /* Jaune/Orange */
            --light-bg: #e0f2f1; /* Fond très clair */
            --shadow-light: 0 4px 12px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        /* Styles de base */
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg); 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; /* Utiliser min-height pour la réactivité */
            margin: 0; 
            padding: 20px;
        }

        /* Conteneur de connexion */
        .login-container { 
            background: white; 
            padding: 40px; /* Augmenté pour un look plus aéré */
            border-radius: 12px; 
            box-shadow: var(--shadow-light); 
            width: 100%; 
            max-width: 400px; /* Légèrement plus large */
            transition: box-shadow 0.3s ease-in-out;
        }
        .login-container:hover {
            box-shadow: var(--shadow-hover);
        }

        /* Titre */
        h2 { 
            color: var(--primary-color); 
            text-align: center; 
            margin-bottom: 30px; 
            font-size: 1.8em;
            border-bottom: 2px solid var(--secondary-color);
            padding-bottom: 10px;
        }

        /* Groupes de formulaire */
        .form-group { 
            margin-bottom: 25px; 
        }
        .form-group label { 
            display: block; 
            font-weight: 600; /* Plus épais */
            margin-bottom: 8px; 
            color: #555; 
            font-size: 0.95em;
        }
        
        /* Champs de saisie */
        .form-group input[type="text"], 
        .form-group input[type="password"] { 
            width: 100%; 
            padding: 12px 15px; /* Plus de padding */
            border: 1px solid #ddd; 
            border-radius: 6px; /* Bords plus doux */
            box-sizing: border-box; 
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-group input:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(255, 179, 0, 0.2); /* Petit halo au focus */
            outline: none;
        }

        /* Bouton de soumission */
        .submit-button { 
            background-color: var(--primary-color); 
            color: white; 
            padding: 14px; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            width: 100%; 
            font-size: 1.15em; 
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.1s;
        }
        .submit-button:hover { 
            background-color: #00695c; /* Un peu plus foncé au survol */
        }
        .submit-button:active {
            transform: translateY(1px); /* Effet de clic */
        }

        /* Message d'erreur */
        .error-message {
            color: #d32f2f; /* Rouge plus doux */
            background-color: #ffebee; /* Fond rouge très clair */
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid #ef9a9a;
            font-weight: bold;
        }

        /* --- MEDIA QUERIES pour la Réactivité (Responsive) --- */
        @media (max-width: 600px) {
            body {
                padding: 10px;
                align-items: flex-start; /* Centre en haut sur les petits écrans */
            }
            .login-container {
                padding: 25px;
                margin-top: 5vh; /* Espace du haut de l'écran */
                border-radius: 0; /* Bords carrés pour un look plein écran sur mobile */
                box-shadow: none; /* Pas d'ombre sur mobile */
                max-width: 100%;
            }
            .login-container:hover {
                box-shadow: none;
            }
            h2 {
                font-size: 1.5em;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Connexion Administration</h2>
    <?php echo $error_message; ?>
    <form action="process_login.php" method="POST">
        <div class="form-group">
            <label for="username">Identifiant</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="submit-button">Se connecter</button>
    </form>
</div>

</body>
</html>