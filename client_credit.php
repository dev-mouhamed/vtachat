<?php
session_start();
require_once 'managerDB.php';

  // IF ON EST EN MODE DETAILS
  $id_detail = 0;
  if(isset($_GET['id_client'])){
      $id_detail = 1; 
      $id_client = echapper($_GET['id_client']);
  }

// 🔹 Récupérer uniquement les clients avec des impayés
$stmt = $pdo->query("
    SELECT 
        c.id_client,
        c.nom AS client,
        c.telephone,
        SUM(v.montant_total) AS total_achats,
        SUM(v.montant_regle) AS total_regle,
        (SUM(v.montant_total) - SUM(v.montant_regle)) AS reste_a_payer
    FROM ventes v
    JOIN clients c ON v.id_client = c.id_client
    GROUP BY c.id_client
    HAVING reste_a_payer > 0
    ORDER BY reste_a_payer DESC
");

  if (isset($_POST['versementCredit'])) {

    if (not_empty_group(['montant', 'id_vente'])) {

      // 🔹 Récupération et nettoyage des données
      $id_vente      = intval($_POST['id_vente']);
      $montant       = floatval(str_replace(' ', '', $_POST['montant']));
      $responsable   = trim($_POST['responsable']);
      $date_paiement = $_POST['date_paiement'] ?? date('Y-m-d H:i:s');

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
          header('location:client_credit.php?id_client=' . $id_client);
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
        header('location:client_credit.php?id_client=' . $id_client);
        exit;

      } catch (Exception $e) {
        $pdo->rollBack();
        alert_message('danger', "Erreur : " . $e->getMessage());
        header('location:client_credit.php');
        exit;
      }
    } else {
      alert_message('danger', "Veuillez remplir tous les champs obligatoires.");
      header('location:client_credit.php');
      exit;
    }
  }

?>

<?php include_once('./partials/header.php'); ?>

<style>

  /* 📱 Responsive Mobile */
  @media (max-width: 768px) {

    /* Container */
    .page-inner {
      padding: 0.5rem !important;
    }

    h3.fw-bold {
      font-size: 1.2rem !important;
    }

    h6.op-7 {
      font-size: 0.85rem !important;
    }

    /* Table */
    .table-responsive {
      overflow-x: auto;
    }

    #basic-datatables.table thead {
      display: none; /* ❌ Cache l’entête trop large pour téléphone */
    }

    #basic-datatables.table tr {
      display: block;
      margin-bottom: 1rem;
      border: 1px solid #eee;
      border-radius: 10px;
      padding: 0.7rem;
      background: #fff;
    }

    #basic-datatables.table td {
      display: flex;
      justify-content: space-between;
      font-size: 0.9rem;
      padding: 0.4rem 0 !important;
    }

    #basic-datatables.table td:before {
      content: attr(data-label);
      font-weight: bold;
      color: #555;
    }

    /* Boutons */
    .btn-icon.btn-sm {
      width: 2rem;
      height: 2rem;
    }
  }

  /* Tableau compact et lisible */
  #basic-datatables.table thead th,
  #basic-datatables.table tbody td {
    padding: 0.4rem 1rem !important;
    font-size: 0.9rem !important;
    vertical-align: middle !important;
  }

  #basic-datatables.table tbody tr:hover {
    background-color: #f9f9f9 !important;
  }

  /* Boutons */
  .btn-icon.btn-sm {
    padding: 0.25rem !important;
    width: 1.6rem;
    height: 1.6rem;
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
                <h3 class="fw-bold mb-3">Clients avec crédits</h3>
                <h6 class="op-7 mb-2">💡 Liste de tous les clients ayant un solde impayé.</h6>
              </div>
              <div class="ms-md-auto py-2 py-md-0">
                <a href="vente_nouvelle.php" class="btn btn-primary btn-round btn-sm rounded-pill px-3">
                  Nouvelle facture
                </a>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="card shadow-sm">
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead class="table-dark small">
                          <tr>
                            <th>Client</th>
                            <th class="text-center">Téléphone</th>
                            <th class="text-end">Total achats</th>
                            <th class="text-end">Total réglé</th>
                            <th class="text-end">Reste à payer</th>
                            <th class="text-center">Actions</th>
                          </tr>
                        </thead>
                        <tbody class="small">
                          <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr onclick="goToClient(<?= $row['id_client'] ?>)" style="cursor: pointer;">
                              <td data-label="Client"><?= htmlspecialchars($row['client']) ?></td>

                                <td data-label="Téléphone" class="text-center">
                                  <?= htmlspecialchars($row['telephone'] ?? '-') ?>
                                </td>

                                <td data-label="Total achats" class="text-end text-primary fw-bold">
                                  <?= number_format($row['total_achats'], 0, ',', ' ') ?> FCFA
                                </td>

                                <td data-label="Total réglé" class="text-end text-success fw-bold">
                                  <?= number_format($row['total_regle'], 0, ',', ' ') ?> FCFA
                                </td>

                                <td data-label="Reste à payer" class="text-end text-danger fw-bold">
                                  <?= number_format($row['reste_a_payer'], 0, ',', ' ') ?> FCFA
                                </td>

                                <td data-label="Actions" class="text-center">
                                  <a href="client_credit.php?id_client=<?= $row['id_client'] ?>" 
                                    class="btn btn-icon btn-sm btn-info">
                                    <i class="fa fa-eye"></i>
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

          <?php include_once('./client_credit_detail.php') ?>

      <?php endif;  ?>

      <?php include_once('./partials/baspage.php'); ?>
    </div>

    <?php include_once('./partials/params.php'); ?>
    <?php include_once('./partials/footer.php'); ?>
  </div>
</body>
</html>

<script>
  // Quand on ouvre le modal de versement
  const modalVersement = document.getElementById('modalVersement');
  modalVersement.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const idVente = button.getAttribute('data-id-vente');
    const reste = button.getAttribute('data-reste');

    document.getElementById('id_vente').value = idVente;
    document.getElementById('montant').value = null;
    document.getElementById('resteAffiche').textContent = new Intl.NumberFormat('fr-FR').format(reste);
  });
</script>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("#formVersementCredit");

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

<script>
  function goToClient(idClient) {
    window.location.href = "client_credit.php?id_client=" + idClient;
  }
</script>
