<?php
include_once('./managerDB.php');

if (isset($_POST['id_client'])) {
    $id_client = (int) $_POST['id_client'];

    try {
        $stmt = $pdo->prepare("DELETE FROM clients WHERE id_client = :id_client");
        $stmt->execute([':id_client' => $id_client]);

        echo json_encode([
            'success' => true,
            'message' => "Client supprimé avec succès !"
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => "Erreur lors de la suppression."
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => "ID client invalide."
    ]);
}
?>
