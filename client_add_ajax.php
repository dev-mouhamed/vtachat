<?php
header('Content-Type: application/json');
require_once 'managerDB.php'; // connexion à ta BDD

if (!isset($_POST['nom']) || trim($_POST['nom']) === '') {
    echo json_encode(['success' => false, 'type' => 'danger', 'message' => 'Nom manquant']);
    exit;
}

$nom = trim($_POST['nom']);

// Vérifier si déjà existant
$stmt = $pdo->prepare("SELECT id_client FROM clients WHERE nom = ?");
$stmt->execute([$nom]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'type' => 'danger', 'message' => 'Ce client existe déjà.']);
    exit;
}

// Insertion
$stmt = $pdo->prepare("INSERT INTO clients (nom) VALUES (?)");
if ($stmt->execute([$nom])) {
    $id = $pdo->lastInsertId();
    echo json_encode(['success' => true, 'type' => 'success', 'message' => 'Client crée avec sucès.', 'client' => ['id_client' => $id, 'nom' => $nom]]);
} else {
    echo json_encode(['success' => false, 'type' => 'danger', 'message' => 'Erreur lors de la création.']);
}
?>
