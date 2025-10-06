<?php
// Fichier de Traitement PHP Pur (process.php)

// Fonction utilitaire pour lire et écrire les données
function handle_data($type, $action, $item_id = null, $data = null) {
    $file_path = 'data/' . $type . '.json';

    // 1. Lire les données existantes
    $current_data = file_get_contents($file_path);
    $items = json_decode($current_data, true) ?: [];
    $found = false;

    // --- Action de SUPPRESSION (GET) ---
    if ($action === 'delete') {
        foreach ($items as $key => $item) {
            // L'ID est stocké comme un nombre (timestamp), donc on le caste
            if ((int)$item['id'] === (int)$item_id) { 
                unset($items[$key]);
                $found = true;
                break;
            }
        }
        $items = array_values($items); // Réindexer le tableau
        $redirect_message = $type . '_deleted';

    // --- Action d'AJOUT (POST) ---
    } elseif ($action === 'add') {
        // Pour l'ajout, on insère au début
        array_unshift($items, $data);
        $redirect_message = $type . '_ok';

    // --- Action de MODIFICATION (POST) ---
    } elseif ($action === 'update') {
        foreach ($items as $key => $item) {
            if ((int)$item['id'] === (int)$item_id) { 
                // Remplacer l'ancien élément par le nouvel élément mis à jour
                $items[$key] = $data;
                $found = true;
                break;
            }
        }
        $redirect_message = $type . '_updated';
    } 

    // 2. Écrire le tableau mis à jour dans le fichier
    file_put_contents($file_path, json_encode($items, JSON_PRETTY_PRINT));
    
    return $redirect_message;
}


// =========================================================================
// GESTION DES REQUÊTES
// =========================================================================

// --- GESTION DE LA SUPPRESSION (Requête GET) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete') {
    $type = $_GET['type'] ?? null;
    $id = $_GET['id'] ?? null;

    if ($type && $id && ($type === 'news' || $type === 'event')) {
        $message = handle_data($type, 'delete', $id);
        header('Location: admin.php?message=' . $message);
        exit;
    }
    header('Location: admin.php?message=error');
    exit;
}


// --- GESTION DE L'AJOUT ou MODIFICATION (Requête POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['type'])) {
    
    $type = $_POST['type'];
    $item_id = $_POST['id'] ?? null; // ID est présent seulement en modification
    $action = $item_id ? 'update' : 'add';

    $new_item = [];
    $required_fields = [];
    $redirect_message = 'error';

    if ($type === 'news') {
        $required_fields = ['title', 'date', 'content'];
        
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                header('Location: admin.php?message=' . $redirect_message);
                exit;
            }
        }
        
        $new_item = [
            'id' => $item_id ?: time(), // Utilise l'ancien ID ou en crée un nouveau
            'title' => htmlspecialchars($_POST['title']),
            'date' => htmlspecialchars($_POST['date']),
            'content' => htmlspecialchars($_POST['content']),
            'link' => isset($_POST['link']) ? htmlspecialchars($_POST['link']) : '',
            'urgent' => isset($_POST['urgent']) ? true : false,
        ];

    } elseif ($type === 'event') {
        $required_fields = ['title', 'date', 'description'];
        
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                header('Location: admin.php?message=' . $redirect_message);
                exit;
            }
        }

        $new_item = [
            'id' => $item_id ?: time(),
            'title' => htmlspecialchars($_POST['title']),
            'date' => htmlspecialchars($_POST['date']),
            'time' => isset($_POST['time']) ? htmlspecialchars($_POST['time']) : '',
            'location' => isset($_POST['location']) ? htmlspecialchars($_POST['location']) : '',
            'description' => htmlspecialchars($_POST['description']),
        ];

    } else {
        header('Location: admin.php?message=error');
        exit;
    }

    // Appel de la fonction de gestion
    $message = handle_data($type, $action, $item_id, $new_item);
    header('Location: admin.php?message=' . $message);
    exit;
}

// Si aucune action valide n'est reçue
header('Location: admin.php');
exit;
?>