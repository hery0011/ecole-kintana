<?php
// Définit l'en-tête pour que le navigateur sache qu'on renvoie du JSON
header('Content-Type: application/json');

// Vérifie la requête POST et les données de base
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'], $_POST['type'], $_POST['reaction'])) {
    echo json_encode(['success' => false, 'message' => 'Requête invalide ou données manquantes.']);
    exit;
}

$id = (int)$_POST['id'];
$type = $_POST['type']; 
$reaction = $_POST['reaction']; 

// Validation
if (!in_array($type, ['news', 'event']) || !in_array($reaction, ['like', 'love'])) {
    echo json_encode(['success' => false, 'message' => 'Type de contenu ou réaction invalide.']);
    exit;
}

$file_name = 'data/' . $type . '.json';

// Vérifie l'existence du fichier et les permissions de lecture
if (!file_exists($file_name) || !is_readable($file_name)) {
    // Ceci affichera l'alerte "Fichier de données introuvable."
    echo json_encode(['success' => false, 'message' => 'Fichier de données introuvable.']);
    exit;
}

// 1. Lecture des données
$data_json = file_get_contents($file_name);
$items = json_decode($data_json, true);

if (!is_array($items)) {
    echo json_encode(['success' => false, 'message' => 'Erreur de décodage du fichier JSON.']);
    exit;
}

$found = false;
$new_counts = ['likes' => 0, 'loves' => 0];

// 2. Recherche et mise à jour de l'élément
foreach ($items as $key => $item) { // Utiliser $key pour la manipulation par clé
    if ((int)$item['id'] === $id) {
        
        // Initialise et incrémente directement dans le tableau $items
        $items[$key]['likes'] = $item['likes'] ?? 0;
        $items[$key]['loves'] = $item['loves'] ?? 0;
        
        $items[$key][$reaction . 's']++;
        
        // Enregistre les nouveaux comptes pour les renvoyer
        $new_counts['likes'] = $items[$key]['likes'];
        $new_counts['loves'] = $items[$key]['loves'];
        
        $found = true;
        break;
    }
}

if (!$found) {
    echo json_encode(['success' => false, 'message' => 'ID non trouvé.']);
    exit;
}

// 3. Écriture des données mises à jour
if (!is_writable($file_name)) {
    echo json_encode(['success' => false, 'message' => 'Erreur : Le fichier n\'est pas accessible en écriture (permissions).']);
    exit;
}

$new_data_json = json_encode($items, JSON_PRETTY_PRINT);
if (file_put_contents($file_name, $new_data_json) === false) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'écriture du fichier.']);
    exit;
}

// 4. Succès: renvoie les nouveaux comptes au JavaScript
echo json_encode([
    'success' => true, 
    'message' => 'Réaction enregistrée avec succès.',
    'new_counts' => $new_counts
]);
?>