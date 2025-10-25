<?php 

  session_start();
  require_once 'managerDB.php';

  // IF ON EST EN MODE DETAILS
  $id_detail = 0;
  if(isset($_GET['id_vente'])){
      $id_detail = 1; 
      $id_vente = echapper($_GET['id_vente']);
  }

  $stmt = $pdo->query("
    SELECT v.id_vente, c.nom AS client, v.date_vente, v.montant_total, v.montant_regle, sp.libelle AS statut_paiement, sp.id_statut_paiement
    FROM ventes v
    JOIN clients c ON v.id_client = c.id_client
    JOIN statut_paiement sp ON v.id_statut_paiement = sp.id_statut_paiement
    ORDER BY id_vente DESC
  ");

?>

<?php include_once('./partials/header.php'); ?>

<style>
  /* Tableau compact */
    #basic-datatables.table thead th,
    #basic-datatables.table tbody td {
      padding: 0.25rem 2.5rem !important; /* réduit padding vertical et horizontal */
      font-size: 0.85rem !important;       /* réduit la police */
      vertical-align: middle !important;
    }


    /* Boutons compacts */
    #basic-datatables .btn-icon.btn-sm {
      padding: 0.25rem !important;
      width: 1.5rem;
      height: 1.5rem;
    }

    /* Optionnel : réduire l'espacement entre les lignes */
    #basic-datatables.table tbody tr {
      height: 2.8rem !important;
    }

    /* Optionnel : réduire l'espacement entre les lignes */
    #basic-datatables.table thead th {
      height: 2.0rem !important;
    }
</style>
<body>
  <div class="wrapper">
    <?php include_once('./partials/sidebar.php'); ?>
    <div class="main-panel">
      <?php include_once('./partials/topbar.php'); ?>

      <!-- PAGE PRINCIPALE -->
      <?php if($id_detail === 0): ?>

        <div class="container">
          <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
              <div>
                <h3 class="fw-bold mb-3">Liste des ventes</h3>
                <h6 class="op-7 mb-2">💡 Tableau de toutes les ventes, faites des recherches si nécessaire !</h6>
              </div>
              <div class="ms-md-auto py-2 py-md-0">
                <a href="vente_nouvelle.php" class="btn btn-primary btn-round btn-sm rounded-pill px-3">Nouvelle facture</a>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead class="table-dark small">
                          <tr>
                            <th class="py-1 px-2">Client</th>
                            <th class="text-center py-1 px-2">Date</th>
                            <th class="text-end py-1 px-2">M. total</th>
                            <th class="text-end py-1 px-2">M. réglé</th>
                            <th class="text-center py-1 px-2">Paiement</th>
                            <th class="text-center py-1 px-2">Actions</th>
                          </tr>
                        </thead>
                        <tbody class="small">
                          <?php while ($vente = $stmt->fetch(PDO::FETCH_ASSOC)) : 
                            $reste = max($vente['montant_total'] - $vente['montant_regle'], 0); ?>
                            <tr>
                              <td class="py-1 px-2">
                                <?= htmlspecialchars($vente['client']) ?>
                              </td>
                              <td class="text-center py-1 px-2">
                                <?= date('d/m/Y H:i', strtotime($vente['date_vente'])) ?>
                              </td>
                              <td class="text-end py-1 px-2">
                                <?= number_format($vente['montant_total'], 0, ',', ' ') ?>
                              </td>
                              <td class="text-end py-1 px-2">
                                <?= number_format($vente['id_statut_paiement'] != 1 ? $vente['montant_regle'] : $vente['montant_total'], 0, ',', ' ') ?>
                              </td>
                              <td class="text-center py-1 px-2">
                                <?= statutBadge($vente['id_statut_paiement']) ?>
                              </td>
                              <td class="text-center py-1 px-2">
                                <a href="vente_liste.php?id_vente=<?= $vente['id_vente'] ?>" class="btn btn-icon btn-sm btn-info me-1 p-1" title="Voir">
                                  <i class="fa fa-eye" style="font-size: 0.75rem;"></i>
                                </a>
                                <a href="vente_nouvelle.php?id_vente=<?= $vente['id_vente'] ?>" class="btn btn-icon btn-sm btn-primary me-1 p-1" title="Modifier">
                                  <i class="fa fa-pencil-alt" style="font-size: 0.75rem;"></i>
                                </a>
                                <a href="vente_delete.php?id=<?= $vente['id_vente'] ?>" class="btn btn-icon btn-sm btn-danger p-1" title="Supprimer" onclick="return confirm('Confirmer la suppression ?')">
                                  <i class="fa fa-trash" style="font-size: 0.75rem;"></i>
                                </a>
                              </td>
                            </tr>
                          <?php endwhile; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

      <!-- PAGE SECONDAITRE -->
      <?php else: ?>

          <?php include_once('./vente_detail.php') ?>

      <?php endif;  ?>

      <?php include_once('./partials/baspage.php'); ?>
    </div>

    <?php include_once('./partials/params.php'); ?>
    <?php include_once('./partials/footer.php'); ?>
  </body>
</html>

<script>
  document.getElementById("btnPrint").addEventListener("click", function() {
    // Sélectionner le contenu à imprimer
    const element = document.querySelector(".page-inner");

    // Options HTML2PDF
    const opt = {
      margin:       0.5,
      filename:     'facture_<?= $vente['id_vente'] ?>.pdf',
      image:        { type: 'jpeg', quality: 0.98 },
      html2canvas:  { scale: 2 },
      jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
    };

    // Générer le PDF
    html2pdf().set(opt).from(element).save();
  });
</script>

