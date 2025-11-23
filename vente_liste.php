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


  if (isset($_POST['paiement_add'])) {

    if (not_empty_group(['montant']) AND !empty($id_vente)) {

      // 🔹 Récupération et nettoyage des données
      $montant       = floatval(str_replace(' ', '', $_POST['montant']));
      $responsable   = trim($_POST['responsable']);
      $date_paiement = date('Y-m-d');

      // dd($montant,$responsable, $date_paiement);

      try {
        $pdo->beginTransaction();

        // 🔹 Récupérer la vente correspondante
        $venteStmt = $pdo->prepare("SELECT montant_total, montant_regle FROM ventes WHERE id_vente = ?");
        $venteStmt->execute([$id_vente]);
        $vente = $venteStmt->fetch(PDO::FETCH_ASSOC);

        if (!$vente) {
          alert_message('danger', "Vente introuvable !");
          header('location:vente_liste.php');
          exit;
        }

        $montant_total = floatval($vente['montant_total']);
        $montant_regle = floatval($vente['montant_regle']);
        $reste_a_payer = max($montant_total - $montant_regle, 0);

        // 🔸 Vérifier si le montant dépasse le reste dû
        if ($montant > $reste_a_payer) {
          alert_message('warning', "Le montant versé dépasse le reste à payer !");
          header('location:vente_liste.php?id_vente=' . $id_vente);
          exit;
        }

        // 🔹 Insertion du paiement
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

        // 🔹 Mise à jour du total réglé
        $sumStmt = $pdo->prepare("SELECT SUM(montant) AS total_paye FROM paiements WHERE id_vente = ?");
        $sumStmt->execute([$id_vente]);
        $total_paye = floatval($sumStmt->fetchColumn());

        // 🔹 Déterminer le nouveau statut de paiement
        if ($total_paye >= $montant_total) {
          $nouveau_statut = 1; // Payé
          $total_paye = $montant_total;
        } elseif ($total_paye > 0) {
          $nouveau_statut = 2; // Partiel
        } else {
          $nouveau_statut = 3; // Crédit
        }

        // 🔁 Mise à jour de la vente
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

        $pdo->commit();

        alert_message('success', "Versement ajouté avec succès !");
        header('location:vente_liste.php?id_vente=' . $id_vente);
        exit;

      } catch (Exception $e) {
        $pdo->rollBack();
        alert_message('danger', "Erreur : " . $e->getMessage());
        header('location:vente_liste.php');
        exit;
      }
    } else {
      alert_message('danger', "Veuillez remplir tous les champs obligatoires.");
      header('location:vente_liste.php');
      exit;
    }
  }



?>

<?php include_once('./partials/header.php'); ?>

<style>

  /* 📱 Amélioration générale mobile */
@media (max-width: 768px) {

  /* Évite que tout soit collé aux bords */
  .container, .page-inner {
    padding-left: 12px !important;
    padding-right: 12px !important;
  }

  /* Largeur des cartes */
  .card {
    border-radius: 12px;
    padding: 0 !important;
    margin-bottom: 1rem !important;
  }

  .card-body {
    padding: 12px !important;
  }

  /* Titres */
  h3.fw-bold {
    font-size: 1.25rem !important;
  }
  h6.op-7 {
    font-size: 0.9rem !important;
  }

  /* Bouton "Nouvelle facture" */
  .btn.btn-primary {
    width: 100% !important;
    font-size: 0.9rem;
    padding: 10px;
  }

  /* Table DataTables */
  #basic-datatables.table thead {
    display: none !important; /* Cache les têtes en mobile */
  }

  #basic-datatables.table tbody tr {
    display: block;
    background: white;
    margin-bottom: 1rem;
    padding: 12px;
    border-radius: 10px;
    box-shadow: 0 0 8px rgba(0,0,0,0.06);
  }

  /* Chaque cellule devient une ligne */
  #basic-datatables.table tbody td {
    display: flex;
    justify-content: space-between;
    font-size: 0.85rem !important;
    padding: 6px 0 !important;
    border-bottom: 1px dashed #ddd;
  }

  /* Dernière ligne sans bordure */
  #basic-datatables.table tbody td:last-child {
    border-bottom: none;
  }

  /* Ajout des labels (ex: "Client :", "Date :") */
  #basic-datatables.table tbody td:before {
    content: attr(data-label);
    font-weight: bold;
    color: #444;
  }

  /* Boutons actions plus grands pour les doigts */
  .btn-icon.btn-sm {
    width: 2rem !important;
    height: 2rem !important;
    padding: 0 !important;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .btn-icon i {
    font-size: 0.85rem !important;
  }
}


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

    /* 🔹 Ajustements généraux sur mobile */
@media (max-width: 768px) {

  /* Container avec marges réduites */
  .container, .page-inner {
    padding-left: 8px !important;
    padding-right: 8px !important;
  }

  /* Titre plus compact */
  h3.fw-bold {
    font-size: 1.2rem !important;
  }

  h6.op-7 {
    font-size: 0.85rem !important;
  }

  /* Le bouton "Nouvelle facture" prend toute la largeur */
  .btn.btn-primary {
    width: 100%;
    margin-top: 10px;
  }

  /* Table responsive (DataTables) */
  .table-responsive {
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
  }

  /* Colonnes plus compactes */
  #basic-datatables.table thead th,
  #basic-datatables.table tbody td {
    padding: 6px !important;
    font-size: 0.75rem !important;
    white-space: nowrap !important;
  }

  /* Boutons actions réduits */
  #basic-datatables .btn-icon.btn-sm {
    width: 1.8rem;
    height: 1.8rem;
    padding: 0 !important;
  }

  #basic-datatables i {
    font-size: 0.75rem !important;
  }

  /* Ligne plus petite */
  #basic-datatables.table tbody tr {
    height: auto !important;
  }
}
@media (max-width: 768px) {
  .btn-icon {
    margin: 0 2px !important;
  }
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
                            // Couleur du statut
                            $color = "";
                            switch ($vente['id_statut_paiement']) {
                                case 1:
                                    $color = "#28a745"; // Payé
                                    break;
                                case 2:
                                    $color = "#fd7e14"; // Partiel
                                    break;
                                case 3:
                                    $color = "#dc3545"; // Crédit
                                    break;
                                default:
                                    $color = "#6c757d";
                                    break;
                            }
                            $reste = max($vente['montant_total'] - $vente['montant_regle'], 0); ?>
                            <tr style="border-left: 5px solid <?= $color ?>;">
                              <td data-label="Client"><?= htmlspecialchars($vente['client'] ?? '') ?></td>
                              <td class="text-center" data-label="Date">
                                <?= date('d/m/Y H:i', strtotime($vente['date_vente'])) ?>
                              </td>

                              <td class="text-end" data-label="Total">
                                <?= number_format($vente['montant_total'], 0, ',', ' ') ?>
                              </td>

                              <td class="text-end" data-label="Réglé">
                                <?= number_format($vente['montant_regle'], 0, ',', ' ') ?>
                              </td>

                              <td class="text-center" data-label="Statut">
                                <?= statutBadge($vente['id_statut_paiement']) ?>
                              </td>

                              <td class="text-center" data-label="Actions">
                                <a href="vente_liste.php?id_vente=<?= $vente['id_vente'] ?>" class="btn btn-icon btn-sm btn-info me-1 p-1" title="Voir">
                                  <i class="fa fa-eye" style="font-size: 0.75rem;"></i>
                                </a>
                                <!-- <a href="vente_nouvelle.php?id_vente=<?= $vente['id_vente'] ?>" class="btn btn-icon btn-sm btn-primary me-1 p-1" title="Modifier">
                                  <i class="fa fa-pencil-alt" style="font-size: 0.75rem;"></i>
                                </a> -->
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
  document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("#formVersement");

    form.addEventListener("submit", (event) => {

      // Vérifie les autres validations Bootstrap
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }

      form.classList.add("was-validated");
    });
  });
</script>

<script>
  // 🔹 Formate le nombre avec espaces (ex: 15000 → 15 000)
  function formatNumberWithSpaces(value) {
    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
  }

  // 🔹 Convertit la saisie en nombre (supprime espaces)
  function parseNumber(value) {
    return parseFloat(value.replace(/\s+/g, '').replace(',', '.')) || 0;
  }

  // 🔹 Fonction exécutée à chaque frappe
  function formatMoney(input) {
    const resteElement = document.getElementById('resteAffiche');
    const reste = parseNumber(resteElement.textContent); // Récupère le reste réel
    let value = parseNumber(input.value);

    // Si le champ est vide, ne rien faire
    if (input.value.trim() === '') return;

    // Si dépasse le reste, on ramène à la valeur max
    if (value > reste) value = reste;

    // Reformater proprement avec séparateurs
    input.value = formatNumberWithSpaces(value);
  }
</script>

