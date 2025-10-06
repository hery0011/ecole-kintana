<?php
session_start(); // Démarre la session sur cette page

// SÉCURITÉ : Vérifie si l'utilisateur n'est PAS connecté
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirige vers la page de connexion
    header('Location: login.php');
    exit; // Arrête l'exécution du script
}

// Fonction utilitaire pour lire les fichiers JSON
function load_data($file_name) {
    $path = 'data/' . $file_name . '.json';
    if (!file_exists($path)) {
        return [];
    }
    $data = file_get_contents($path);
    return json_decode($data, true) ?: [];
}

// Charger toutes les données pour l'affichage de la liste
$news = load_data('news');
$events = load_data('event');

// GESTION DES MESSAGES DE RETOUR
$message = '';
if (isset($_GET['message'])) {
    $msg = $_GET['message'];
    
    // Messages d'ajout
    $message = $msg == 'news_ok' ? '<p class="msg success">Actualité ajoutée avec succès !</p>' : $message;
    $message = $msg == 'event_ok' ? '<p class="msg success">Événement ajouté avec succès !</p>' : $message;
    
    // Messages de suppression
    $message = $msg == 'news_deleted' ? '<p class="msg info">Actualité supprimée avec succès.</p>' : $message;
    $message = $msg == 'event_deleted' ? '<p class="msg info">Événement supprimé avec succès.</p>' : $message;
    
    // Messages de modification
    $message = $msg == 'news_updated' ? '<p class="msg warning">Actualité modifiée avec succès.</p>' : $message;
    $message = $msg == 'event_updated' ? '<p class="msg warning">Événement modifié avec succès.</p>' : $message;
    
    // Message d'erreur
    $message = $msg == 'error' ? '<p class="msg error">Erreur : Opération impossible ou champs requis manquants.</p>' : $message;
}


// GESTION DU MODE MODIFICATION (ÉDITION)
$is_editing = false;
$edit_data = [];
$edit_type = '';

if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['type']) && isset($_GET['id'])) {
    $is_editing = true;
    $edit_type = $_GET['type'];
    $edit_id = (int)$_GET['id'];
    
    $data_source = ($edit_type === 'news') ? $news : $events;
    
    foreach ($data_source as $item) {
        if ((int)$item['id'] === $edit_id) {
            $edit_data = $item;
            break;
        }
    }
    
    // Rediriger si l'élément n'est pas trouvé
    if (empty($edit_data)) {
        header('Location: admin.php?message=error');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - École</title>
    <style>
        /* Styles de Base et Full Screen */
        :root {
            --primary-color: #004d40; /* Vert foncé de l'école */
            --secondary-color: #ffb300; /* Jaune/Orange */
            --light-bg: #f4f7f6;
            --sidebar-width: 250px;
        }
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            display: flex; /* Active le mode flex pour le layout sidebar-content */
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background-color: var(--light-bg);
        }

        /* --- Barre Latérale (Sidebar) --- */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--primary-color);
            color: white;
            padding: 20px 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            flex-shrink: 0; /* Empêche le rétrécissement */
            z-index: 100;
        }
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid #00695c;
            text-align: center;
        }
        .sidebar-header h2 {
            margin: 0;
            font-size: 1.4em;
        }
        .sidebar nav ul {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }
        .sidebar nav ul li a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 15px 20px;
            transition: background-color 0.3s;
        }
        .sidebar nav ul li a:hover {
            background-color: #00897b;
        }

        /* --- Contenu Principal --- */
        .main-content {
            flex-grow: 1; /* Prend l'espace restant */
            padding: 40px;
            overflow-y: auto;
        }
        .admin-container {
            max-width: 100%;
            margin: 0; 
            padding: 20px; 
            background: white; 
            border-radius: 8px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
        }

        /* --- Bouton de Déconnexion --- */
        .logout-section {
            padding: 20px;
            text-align: center;
        }
        .logout-btn {
            background-color: var(--secondary-color);
            color: var(--primary-color);
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
            display: block;
        }
        .logout-btn:hover {
            background-color: #ffa000;
        }

        /* --- Styles de Formulaire et Listes (Adaptation) --- */
        .admin-form h2 { color: var(--primary-color); border-bottom: 2px solid var(--secondary-color); padding-bottom: 10px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; color: #333; }
        .form-group input[type="text"], .form-group textarea, .form-group input[type="date"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .form-group textarea { resize: vertical; height: 100px; }
        .submit-button { background-color: var(--primary-color); color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 1.1em; transition: background-color 0.3s; }
        .submit-button:hover { background-color: #00897b; }
        .tabs { display: flex; margin-bottom: 20px; border-bottom: 2px solid #ddd; }
        .tab-button { flex-grow: 1; padding: 15px; text-align: center; cursor: pointer; background: #f4f4f4; border: 1px solid #ddd; border-bottom: none; color: #333; font-weight: bold; }
        .tab-button.active { background: white; border-top: 3px solid var(--secondary-color); margin-bottom: -2px; color: var(--primary-color); }
        .tab-content { padding-top: 20px; }
        .hidden { display: none; }
        
        .item-list { list-style: none; padding: 0; }
        .item-list li { 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            background: #f9f9f9;
            border-left: 5px solid var(--primary-color);
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .item-list li span { flex-grow: 1; margin-right: 15px; }
        .actions { 
            display: flex; /* Permet aux boutons de rester côte à côte */
            gap: 5px;
        }
        .actions .action-btn { 
            text-decoration: none; 
            padding: 8px 12px; 
            border-radius: 4px; 
            font-size: 0.9em;
            white-space: nowrap; 
        }
        .edit-btn { background-color: var(--secondary-color); color: var(--primary-color); }
        .edit-btn:hover { background-color: #ffa000; }
        .delete-btn { background-color: #dc3545; color: white; }
        .delete-btn:hover { background-color: #c82333; }
        
        .edit-mode-indicator { background-color: #fff3cd; color: #856404; padding: 15px; text-align: center; border-radius: 5px; margin-bottom: 20px; font-weight: bold; border: 1px solid #ffeeba; }
        
        /* Styles des messages */
        .msg { padding: 10px; margin-bottom: 15px; border-radius: 4px; font-weight: bold; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        /* Responsive AMÉLIORÉ pour les petits écrans (max 768px) */
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { 
                width: 100%; 
                height: auto; 
                padding: 10px 0;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            }
            .sidebar-header { border-bottom: none; padding: 0 20px 10px; }
            .sidebar nav ul { 
                display: flex; 
                flex-wrap: wrap; 
                justify-content: center; 
                margin: 0;
                border-top: 1px solid #00695c;
                border-bottom: 1px solid #00695c;
            }
            .sidebar nav ul li a { padding: 8px 10px; font-size: 0.9em; }
            .logout-section { padding: 10px 20px; }
            .main-content { padding: 20px; }
            
            /* Gestion des listes pour mobile (plus lisible et utilisable) */
            .item-list li { 
                flex-direction: column; 
                align-items: flex-start; 
                padding: 10px; 
            }
            .item-list li span { 
                margin-bottom: 10px; 
                margin-right: 0; 
                width: 100%; 
                text-align: left;
            }
            .actions { 
                width: 100%; 
                justify-content: flex-end; /* Aligner les boutons à droite */
                gap: 10px;
            }
            .actions .action-btn { 
                flex: 1; 
                text-align: center; 
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="top-section">
        <div class="sidebar-header">
            <h2>Panel Admin 🔒</h2>
            <p style="font-size: 0.8em; color: #b2dfdb;">Connecté : <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></p>
        </div>
        
        <nav>
            <ul>
                <li><a href="admin.php">Tableau de Bord</a></li>
                <li><a href="index.php" target="_blank">Voir le Site Public</a></li>
            </ul>
        </nav>
    </div>
    
    <div class="logout-section">
        <a href="logout.php" class="logout-btn">Se Déconnecter</a>
    </div>
</div>

<div class="main-content">
    
    <div class="admin-container">
        <h1>Gestion du Contenu Dynamique</h1>

        <?php echo $message; ?>

        <?php if ($is_editing): ?>
            <div class="edit-mode-indicator">
                MODE MODIFICATION : Vous modifiez une <?php echo $edit_type === 'news' ? 'Actualité' : 'Événement'; ?> existante.
                <a href="admin.php" style="color: #004d40; font-weight: normal;">(Annuler la modification et revenir à l'ajout)</a>
            </div>
            
            <div class="admin-form">
                <h2>Modifier l'élément (ID: <?php echo $edit_data['id']; ?>)</h2>
                <form action="process.php" method="POST">
                    <input type="hidden" name="type" value="<?php echo $edit_type; ?>">
                    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                    
                    <?php if ($edit_type === 'news'): ?>
                        <div class="form-group">
                            <label for="news_title">Titre *</label>
                            <input type="text" id="news_title" name="title" value="<?php echo htmlspecialchars($edit_data['title']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="news_date">Date de Publication *</label>
                            <input type="date" id="news_date" name="date" value="<?php echo htmlspecialchars($edit_data['date']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="news_content">Contenu (Max 500 caractères) *</label>
                            <textarea id="news_content" name="content" maxlength="500" required><?php echo htmlspecialchars($edit_data['content']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="news_link">Lien (optionnel)</label>
                            <input type="text" id="news_link" name="link" value="<?php echo htmlspecialchars($edit_data['link'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="news_urgent" name="urgent" <?php echo ($edit_data['urgent'] ?? false) ? 'checked' : ''; ?>>
                            <label for="news_urgent" style="display: inline; font-weight: normal;">Marquer comme URGENT</label>
                        </div>
                        <button type="submit" class="submit-button" style="background-color: #ffa000;">Enregistrer les Modifications</button>

                    <?php elseif ($edit_type === 'event'): ?>
                        <div class="form-group">
                            <label for="event_title">Titre *</label>
                            <input type="text" id="event_title" name="title" value="<?php echo htmlspecialchars($edit_data['title']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="event_date">Date de l'Événement *</label>
                            <input type="date" id="event_date" name="date" value="<?php echo htmlspecialchars($edit_data['date']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="event_time">Heure (optionnel, ex: 18h00)</label>
                            <input type="text" id="event_time" name="time" value="<?php echo htmlspecialchars($edit_data['time'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="event_location">Lieu (optionnel)</label>
                            <input type="text" id="event_location" name="location" value="<?php echo htmlspecialchars($edit_data['location'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="event_description">Description courte *</label>
                            <textarea id="event_description" name="description" required><?php echo htmlspecialchars($edit_data['description']); ?></textarea>
                        </div>
                        <button type="submit" class="submit-button" style="background-color: #ffa000;">Enregistrer les Modifications</button>
                        
                    <?php endif; ?>
                </form>
            </div>
            
        <?php else: ?>
            <div class="tabs">
                <div class="tab-button active" onclick="showTab('news')">Ajouter une Actualité</div>
                <div class="tab-button" onclick="showTab('events')">Ajouter un Événement</div>
            </div>

            <div id="news-tab" class="tab-content">
                <div class="admin-form">
                    <h2>Nouvelle Actualité</h2>
                    <form action="process.php" method="POST">
                        <input type="hidden" name="type" value="news">
                        <div class="form-group">
                            <label for="news_title">Titre *</label>
                            <input type="text" id="news_title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="news_date">Date de Publication *</label>
                            <input type="date" id="news_date" name="date" required>
                        </div>
                        <div class="form-group">
                            <label for="news_content">Contenu (Max 500 caractères) *</label>
                            <textarea id="news_content" name="content" maxlength="500" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="news_link">Lien (optionnel, ex: pour lire la suite)</label>
                            <input type="text" id="news_link" name="link">
                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="news_urgent" name="urgent">
                            <label for="news_urgent" style="display: inline; font-weight: normal;">Marquer comme URGENT</label>
                        </div>
                        <button type="submit" class="submit-button">Ajouter l'Actualité</button>
                    </form>
                </div>
            </div>

            <div id="events-tab" class="tab-content hidden">
                <div class="admin-form">
                    <h2>Nouvel Événement</h2>
                    <form action="process.php" method="POST">
                        <input type="hidden" name="type" value="event">
                        <div class="form-group">
                            <label for="event_title">Titre *</label>
                            <input type="text" id="event_title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="event_date">Date de l'Événement *</label>
                            <input type="date" id="event_date" name="date" required>
                        </div>
                        <div class="form-group">
                            <label for="event_time">Heure (optionnel, ex: 18h00)</label>
                            <input type="text" id="event_time" name="time">
                        </div>
                        <div class="form-group">
                            <label for="event_location">Lieu (optionnel)</label>
                            <input type="text" id="event_location" name="location">
                        </div>
                        <div class="form-group">
                            <label for="event_description">Description courte *</label>
                            <textarea id="event_description" name="description" required></textarea>
                        </div>
                        <button type="submit" class="submit-button">Ajouter l'Événement</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="content-list" id="content-list">
            
            <h2 style="margin-top: 40px;" id="list-news">Actualités Existantes (<?php echo count($news); ?>)</h2>
            <?php if (empty($news)): ?>
                <p>Aucune actualité n'a été ajoutée.</p>
            <?php else: ?>
                <ul class="item-list">
                <?php foreach ($news as $item): ?>
                    <li>
                        <span>[<?php echo htmlspecialchars($item['date']); ?>] - **<?php echo htmlspecialchars($item['title']); ?>**</span>
                        <div class="actions">
                            <a href="admin.php?type=news&action=edit&id=<?php echo $item['id']; ?>" class="action-btn edit-btn">Modifier</a>
                            <a href="process.php?type=news&action=delete&id=<?php echo $item['id']; ?>" 
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette actualité ?');" 
                               class="action-btn delete-btn">Supprimer</a>
                        </div>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            
            <h2 style="margin-top: 40px;" id="list-events">Événements Existants (<?php echo count($events); ?>)</h2>
            <?php if (empty($events)): ?>
                <p>Aucun événement n'a été ajouté.</p>
            <?php else: ?>
                <ul class="item-list">
                <?php foreach ($events as $item): ?>
                    <li>
                        <span>[<?php echo htmlspecialchars($item['date']); ?>] - **<?php echo htmlspecialchars($item['title']); ?>**</span>
                        <div class="actions">
                            <a href="admin.php?type=event&action=edit&id=<?php echo $item['id']; ?>" class="action-btn edit-btn">Modifier</a>
                            <a href="process.php?type=event&action=delete&id=<?php echo $item['id']; ?>" 
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?');" 
                               class="action-btn delete-btn">Supprimer</a>
                        </div>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

    </div>

</div>

<script>
    // Fonction pour gérer les onglets d'administration
    function showTab(tabName) {
        const tabs = ['news', 'events'];
        tabs.forEach(tab => {
            document.getElementById(tab + '-tab').classList.add('hidden');
            document.querySelector(`.tab-button[onclick*="${tab}"]`).classList.remove('active');
        });
        document.getElementById(tabName + '-tab').classList.remove('hidden');
        document.querySelector(`.tab-button[onclick*="${tabName}"]`).classList.add('active');
    }
    
    // Ajout du script pour gérer la navigation par ancre (clic sur le menu)
    document.addEventListener('DOMContentLoaded', function() {
        if (window.location.hash) {
            const hash = window.location.hash.substring(1); // Enlève le '#'
            if (hash === 'news-tab' || hash === 'events-tab') {
                showTab(hash.replace('-tab', ''));
            }
        }
    });
</script>

</body>
</html>