<?php
// Connexion et récupération de la vente
require_once 'managerDB.php';

$id_vente = $_GET['id'] ?? null;
if (!$id_vente) exit("ID de vente manquant.");

// Récupérer la vente
$stmt = $pdo->prepare("
    SELECT v.id_vente, c.nom AS client, v.date_vente, v.montant_total, v.montant_regle, sp.libelle AS statut_paiement, sp.id_statut_paiement
    FROM ventes v
    JOIN clients c ON v.id_client = c.id_client
    JOIN statut_paiement sp ON v.id_statut_paiement = sp.id_statut_paiement
    WHERE v.id_vente = ?
");
$stmt->execute([$id_vente]);
$vente = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer les lignes de vente
$stmt2 = $pdo->prepare("SELECT * FROM ligne_ventes WHERE id_vente = ?");
$stmt2->execute([$id_vente]);
$ligne_ventes = $stmt2->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Facture #<?= $vente['id_vente'] ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<style>
body { font-family: 'Arial', sans-serif; }
.table td, .table th { padding: 0.3rem; }
.card { border: none; margin-bottom: 1rem; }
@media print {
  #generatePDF {
    display: none;
  }
}

</style>
</head>
<body>
<div class="container my-4" id="facture">
  <div class="d-flex justify-content-between mb-4">
    <h3>Facture #<?= $vente['id_vente'] ?></h3>
    <span><?= date('d/m/Y H:i', strtotime($vente['date_vente'])) ?></span>
  </div>

  <div class="row mb-3">
    <div class="col-md-6">
      <div class="card p-2">
        <table class="table table-sm table-borderless mb-0">
          <tbody>
            <tr><th>Client</th><td><?= htmlspecialchars($vente['client']) ?></td></tr>
            <tr><th>Statut</th><td><?= statutBadge($vente['id_statut_paiement']) ?></td></tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card p-2">
        <table class="table table-sm table-borderless mb-0">
          <tbody>
            <tr><th>Total</th><td><?= number_format($vente['montant_total'],0,',',' ') ?> FCFA</td></tr>
            <tr><th>Réglé</th><td><?= number_format($vente['montant_regle'],0,',',' ') ?> FCFA</td></tr>
            <tr><th>Reste</th>
              <td class="<?= $vente['montant_total'] - $vente['montant_regle'] > 0 ? 'text-danger' : 'text-success' ?>">
                <?= number_format(abs($vente['montant_total'] - $vente['montant_regle']),0,',',' ') ?> FCFA
                <?= $vente['montant_total'] - $vente['montant_regle'] > 0 ? '' : '(Monnaie client)' ?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body p-2">
      <table class="table table-striped table-hover table-sm mb-0">
        <thead class="table-primary">
          <tr>
            <th>Produit</th>
            <th class="text-center">Quantité</th>
            <th class="text-end">Prix Unitaire</th>
            <th class="text-end">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($ligne_ventes as $ligne): ?>
          <tr>
            <td><?= htmlspecialchars($ligne['produit']) ?></td>
            <td class="text-center"><?= number_format($ligne['quantite'],0,',',' ') ?></td>
            <td class="text-end"><?= number_format($ligne['prix'],0,',',' ') ?></td>
            <td class="text-end"><?= number_format($ligne['total'],0,',',' ') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
  <div class="text-center mt-3">
    <button id="generatePDF" class="btn btn-success">Télécharger PDF</button>
  </div>

<script>
document.getElementById("generatePDF").addEventListener("click", function() {
  const element = document.getElementById("facture");
  const opt = {
    margin: 0.5,
    filename: 'facture_<?= $vente['id_vente'] ?>.pdf',
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { scale: 2 },
    jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
  };
  html2pdf().set(opt).from(element).save();
});
</script>
</body>
</html>
