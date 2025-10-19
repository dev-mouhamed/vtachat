<?php
header('Content-Type: application/json');
require_once 'managerDB.php'; // ton fichier de connexion à la BDD

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if(empty($search)) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id_client, nom FROM clients WHERE nom LIKE :search ORDER BY nom LIMIT 10");
    $stmt->execute(['search' => "%$search%"]);
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($clients);
} catch (Exception $e) {
    echo json_encode([]);
}
