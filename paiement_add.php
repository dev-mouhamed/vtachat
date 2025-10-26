<?php
require_once 'managerDB.php';

header('Content-Type: application/json'); // pour bien retourner du JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $id_vente      = (int) $_POST['id_vente'];
  $montant       = floatval($_POST['montant']);
  $responsable   = trim($_POST['responsable']);
  $date_paiement = $_POST['date_paiement'] ?? date('Y-m-d H:i:s');

  if ($id_vente && $montant > 0) {
    try {
      // 🔹 Récupérer le total de la vente et le total déjà payé
      $venteStmt = $pdo->prepare("SELECT montant_total, montant_regle FROM ventes WHERE id_vente = ?");
      $venteStmt->execute([$id_vente]);
      $vente = $venteStmt->fetch(PDO::FETCH_ASSOC);

      if (!$vente) {
        echo json_encode(['status' => 'error', 'message' => "Vente introuvable."]);
        exit;
      }

      $montant_total = floatval($vente['montant_total']);
      $montant_regle = floatval($vente['montant_regle']);
      $reste_a_payer = max($montant_total - $montant_regle, 0);

      // 🔸 Vérifier que le montant versé ne dépasse pas le reste dû
      if ($montant > $reste_a_payer) {
        echo json_encode([
          'status' => 'error',
          'message' => "Le montant saisi ({$montant}) dépasse le reste à payer ({$reste_a_payer})."
        ]);
        exit;
      }

      // ✅ Insérer le paiement
      $stmt = $pdo->prepare("
        INSERT INTO paiements (id_vente, date_paiement, montant, statut, responsable)
        VALUES (:id_vente, :date_paiement, :montant, true, :responsable)
      ");
      $stmt->execute([
        ':id_vente'      => $id_vente,
        ':date_paiement' => $date_paiement,
        ':montant'       => $montant,
        ':responsable'   => $responsable
      ]);

      // 🔄 Calculer le nouveau total payé
      $sumStmt = $pdo->prepare("SELECT SUM(montant) AS total_paye FROM paiements WHERE id_vente = ?");
      $sumStmt->execute([$id_vente]);
      $total_paye = floatval($sumStmt->fetchColumn());

      // Déterminer le nouveau statut
      $nouveau_statut = 3; // crédit
      if ($total_paye >= $montant_total) {
        $nouveau_statut = 1; // payé
        $total_paye = $montant_total; // éviter dépassement
      } elseif ($total_paye > 0) {
        $nouveau_statut = 2; // partiel
      }

      // 🔁 Mise à jour de la table ventes
      $updateVente = $pdo->prepare("
        UPDATE ventes 
        SET montant_regle = :montant_regle, id_statut_paiement = :statut 
        WHERE id_vente = :id_vente
      ");
      $updateVente->execute([
        ':montant_regle' => $total_paye,
        ':statut'        => $nouveau_statut,
        ':id_vente'      => $id_vente
      ]);

      echo json_encode(['status' => 'success', 'message' => 'Versement ajouté avec succès !']);

    } catch (Exception $e) {
      echo json_encode(['status' => 'error', 'message' => "Erreur : " . $e->getMessage()]);
    }
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Données invalides.']);
  }

} else {
  echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
}
