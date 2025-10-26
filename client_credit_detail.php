<?php

// Sécurité : vérifier la présence de l'id client
if (!isset($_GET['id_client'])) {
  header('Location: credits_clients.php');
  exit;
}

$id_client = intval($_GET['id_client']);

// 🔹 Informations sur le client
$stmt_client = $pdo->prepare("SELECT * FROM clients WHERE id_client = :id_client");
$stmt_client->execute(['id_client' => $id_client]);
$client = $stmt_client->fetch(PDO::FETCH_ASSOC);

if (!$client) {
  die("Client introuvable !");
}

// 🔹 Liste des factures impayées du client
$stmt_ventes = $pdo->prepare("
  SELECT v.*, sp.libelle AS statut_paiement
  FROM ventes v
  JOIN statut_paiement sp ON v.id_statut_paiement = sp.id_statut_paiement
  WHERE v.id_client = :id_client
  AND (v.montant_total - v.montant_regle) > 0
  ORDER BY v.date_vente DESC
");
$stmt_ventes->execute(['id_client' => $id_client]);
$ventes = $stmt_ventes->fetchAll(PDO::FETCH_ASSOC);

// 🔹 Totaux globaux
$total_achats = 0;
$total_regle = 0;
$total_reste = 0;

foreach ($ventes as $v) {
  $total_achats += $v['montant_total'];
  $total_regle += $v['montant_regle'];
  $total_reste += ($v['montant_total'] - $v['montant_regle']);
}
?>

<?php include_once('./partials/header.php'); ?>

<div class="container">
  <div class="page-inner">

    <!-- HEADER PAGE -->
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
      <div>
        <h3 class="fw-bold mb-1">Crédits du client : <?= htmlspecialchars($client['nom']) ?></h3>
        <h6 class="op-7 mb-2">
          💡 Liste des factures impayées et leurs versements
        </h6>
      </div>
      <div class="ms-md-auto py-2 py-md-0">
        <a href="client_credit.php" class="btn btn-info btn-sm rounded-pill px-3">
          <i class="fa fa-arrow-left"></i> Retour à la liste
        </a>
      </div>
    </div>

    <!-- DETAILS CLIENT -->
    <div class="row mb-3">
      <div class="col-md-4">
        <div class="card shadow-sm mb-3">
        	<div class="card-header bg-dark rounded-3 text-uppercase">
        		<h5 class="fw-bold text-primary mb-2">Informations client</h5>
        	</div>
          <div class="card-body p-2">
            <table class="table table-sm table-borderless mb-0">
              <tr>
                <th>Nom :</th>
                <td><?= htmlspecialchars($client['nom']) ?></td>
              </tr>
              <tr>
                <th>Téléphone :</th>
                <td><?= htmlspecialchars($client['telephone'] ?? '-') ?></td>
              </tr>
              <tr>
                <th>Adresse :</th>
                <td><?= htmlspecialchars($client['adresse'] ?? '-') ?></td>
              </tr>
            </table>
          </div>
        </div>

        <div class="card shadow-sm">
        	<div class="card-header bg-danger rounded-3 text-uppercase text-white">
        		<h5 class="fw-bold mb-2">Résumé des montants</h5>
        	</div>
          <div class="card-body p-2">
            <table class="table table-sm table-borderless mb-0">
              <tr>
                <th>Total achats :</th>
                <td class="text-end text-primary"><?= number_format($total_achats, 0, ',', ' ') ?> FCFA</td>
              </tr>
              <tr>
                <th>Total réglé :</th>
                <td class="text-end text-success"><?= number_format($total_regle, 0, ',', ' ') ?> FCFA</td>
              </tr>
              <tr>
                <th>Reste à payer :</th>
                <td class="text-end text-danger fw-bold"><?= number_format($total_reste, 0, ',', ' ') ?> FCFA</td>
              </tr>
            </table>
          </div>
        </div>
      </div>

      <!-- LISTE DES FACTURES -->
      <div class="col-md-8">
        <div class="card shadow-sm">
          <div class="card-body p-2">
            <h5 class="fw-bold mb-3">📄 Factures impayées</h5>
            <div class="table-responsive">
              <table class="table table-striped table-hover table-sm mb-0">
                <thead class="table-dark">
                  <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th class="text-end">Montant total</th>
                    <th class="text-end">Réglé</th>
                    <th class="text-end">Reste</th>
                    <th class="text-center">Statut</th>
                    <th class="text-center">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (count($ventes) > 0): ?>
                    <?php foreach ($ventes as $i => $vente): 
                      $reste = $vente['montant_total'] - $vente['montant_regle']; ?>
                      <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($vente['date_vente'])) ?></td>
                        <td class="text-end text-primary"><?= number_format($vente['montant_total'], 0, ',', ' ') ?></td>
                        <td class="text-end text-success"><?= number_format($vente['montant_regle'], 0, ',', ' ') ?></td>
                        <td class="text-end text-danger"><?= number_format($reste, 0, ',', ' ') ?></td>
                        <td class="text-center"><?= statutBadge($vente['id_statut_paiement']) ?></td>
                        <td class="text-center">
                          <a href="vente_liste.php?id_vente=<?= $vente['id_vente'] ?>" 
                             class="btn btn-icon btn-sm btn-info me-1" title="Détails facture">
                            <i class="fa fa-eye"></i>
                          </a>
                          <button class="btn btn-icon btn-sm btn-danger"
                                  data-bs-toggle="modal"
                                  data-bs-target="#modalVersement"
                                  data-id-vente="<?= $vente['id_vente'] ?>"
                                  data-reste="<?= $reste ?>">
                            <i class="fa fa-plus"></i>
                          </button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="7" class="text-center text-muted py-3">✅ Ce client n’a aucune facture impayée.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
                <?php if ($total_reste > 0): ?>
                <tfoot class="fw-bold table-light">
                  <tr>
                    <td colspan="2" class="text-end">Totaux :</td>
                    <td class="text-end text-primary"><?= number_format($total_achats, 0, ',', ' ') ?></td>
                    <td class="text-end text-success"><?= number_format($total_regle, 0, ',', ' ') ?></td>
                    <td class="text-end text-danger"><?= number_format($total_reste, 0, ',', ' ') ?></td>
                    <td colspan="2"></td>
                  </tr>
                </tfoot>
                <?php endif; ?>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL VERSEMENT -->
    <div class="modal fade" id="modalVersement" tabindex="-1" aria-labelledby="modalVersementLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="modalVersementLabel">💰 Nouveau Versement</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
          </div>

          <form id="formVersementCredit" action="" method="POST" class="needs-validation" novalidate autocomplete="off">
            <div class="modal-body">
              <input type="hidden" name="id_vente" id="id_vente">

              <div class="mb-3">
                <label for="montant" class="form-label fw-bold">Montant versé</label>
                <input type="text" class="form-control" id="montant" name="montant" placeholder="Entrer le montant ..." required onkeyup="formatMoney(this)" oninput="this.value = this.value.replace(/[^0-9\s]/g, '')">
                <div class="text-muted text-end">
                  Reste à payer : <span id="resteAffiche" class="fw-bold text-danger"></span> FCFA
                </div>
              </div>

              <div class="mb-3">
                <label for="responsable" class="form-label">Responsable</label>
                <input type="text" class="form-control" id="responsable" name="responsable" placeholder="Nom du caissier">
              </div>

              <div class="mb-3">
                <label for="date_paiement" class="form-label">Date du versement</label>
                <input type="text" class="form-control" id="date_paiement" name="date_paiement" value="<?= date('d-m-Y H:i:s') ?>" readonly>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
              <button type="submit" name="versementCredit" class="btn btn-danger">Enregistrer</button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </div>
</div>



