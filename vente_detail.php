<?php 

	$data_vente = $pdo->query("
		SELECT v.id_vente, c.nom AS client, v.date_vente, v.montant_total, v.montant_regle, sp.libelle AS statut_paiement, sp.id_statut_paiement
	    FROM ventes v
	    JOIN clients c ON v.id_client = c.id_client
	    JOIN statut_paiement sp ON v.id_statut_paiement = sp.id_statut_paiement
	 	WHERE id_vente = $id_vente");

	$vente = $data_vente->fetchAll(PDO::FETCH_ASSOC)[0];

	// Exemple pour récupérer les lignes de vente
	$stmt_lignes = $pdo->prepare("
	    SELECT produit, quantite, prix, total 
	    FROM ligne_ventes 
	    WHERE id_vente = :id_vente
	");
	$stmt_lignes->execute(['id_vente' => $id_vente]);
	$ligne_ventes = $stmt_lignes->fetchAll(PDO::FETCH_ASSOC);

	// Exemple pour récupérer les lignes de vente
	$stmt_versement = $pdo->prepare("
	    SELECT *  FROM paiements WHERE id_vente = :id_vente
	");
	$stmt_versement->execute(['id_vente' => $id_vente]);
	$ligne_versement = $stmt_versement->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container">
  <div class="page-inner">
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    	<div>
    		<ul
        class="nav nav-pills nav-secondary nav-pills-no-bd nav-pills-icons justify-content-left"
        id="pills-tab-with-icon"
        role="tablist"
      >
        <li class="nav-item">
          <a
            class="nav-link active"
            id="pills-home-tab-icon"
            data-bs-toggle="pill"
            href="#pills-home-icon"
            role="tab"
            aria-controls="pills-home-icon"
            aria-selected="true"
          >
            <i class="fas fa-file-invoice"></i>
            Facture
          </a>
        </li>
        <li class="nav-item">
          <a
            class="nav-link"
            id="pills-profile-tab-icon"
            data-bs-toggle="pill"
            href="#pills-profile-icon"
            role="tab"
            aria-controls="pills-profile-icon"
            aria-selected="false"
          >
            <i class="fas fa-dollar-sign"></i>
            Versements
          </a>
        </li>
      </ul>
    	</div>
      <div class="ms-md-auto py-2 py-md-0">
        <a href="vente_pdf.php?id=<?= $vente['id_vente'] ?>" target="_blank" 
		   class="btn btn-success btn-sm rounded-pill px-3">
		  <i class="fa fa-print"></i> Imprimer / PDF
		</a>
        <a href="vente_nouvelle.php" class="btn btn-primary btn-sm rounded-pill px-3">
          <i class="fa fa-plus"></i> Nouvelle facture
        </a>
        <a href="vente_liste.php" class="btn btn-info btn-sm rounded-pill px-3">
          <i class="fa fa-list"></i> Liste des ventes
        </a>
      </div>
    </div>

	<div class="row mb-4">
		<!-- Bloc Produits -->
		<div class="col-md-8">
      <div
        class="tab-content mt-2 mb-3"
        id="pills-with-icon-tabContent"
      >
        <div
          class="tab-pane fade show active"
          id="pills-home-icon"
          role="tabpanel"
          aria-labelledby="pills-home-tab-icon"
        >
          <div class="card shadow-sm">
			      <div class="card-body p-2">
			      	<div class="p-3">
				        <h3 class="fw-bold">Facture #<?= $id_vente ?></h3>
				        <h6 class="op-7">💡 Détails de la facture et informations client</h6>
				      </div>
			        <div class="table-responsive">
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
        </div>
        <div
          class="tab-pane fade"
          id="pills-profile-icon"
          role="tabpanel"
          aria-labelledby="pills-profile-tab-icon"
        >
          <div class="card shadow-sm">
			      <div class="card-body p-2">
			      	<div class="p-3">
				        <h3 class="fw-bold">Facture #<?= $id_vente ?></h3>
				        <h6 class="op-7">💡 Détails de la facture et informations client</h6>
				      </div>
			        <div class="table-responsive">
			          <table class="table table-striped table-hover table-sm mb-0">
			            <thead class="table-primary">
			              <tr>
			                <th>Date</th>
			                <th class="text-center">Responsable</th>
			                <th class="text-end">Montant</th>
			                <th class="text-end">Statut</th>
			              </tr>
			            </thead>
			            <tbody>
			              <?php foreach($ligne_versement as $versement): ?>
			              <tr>
			                <td><?= date('d/m/Y H:i', strtotime($versement['date_paiement'])) ?></td>
			                <td class="text-center"><?= htmlspecialchars($versement['responsable']) ?></td>
			                <td class="text-end"><?= number_format($versement['montant'],0,',',' ') ?></td>
			                <td class="text-end"><?= statutBadgePaiement($versement['statut']) ?></td>
			              </tr>
			              <?php endforeach; ?>
			            </tbody>
			          </table>
			        </div>
			      </div>
			    </div>
        </div>
      </div>
		</div>
		<!-- Bloc Infos Client & Statut -->
		<div class="col-md-4">
		    <!-- Carte Client & Date -->
		    <div class="card shadow-sm mb-2">
		      <div class="card-body p-2">
		        <table class="table table-sm table-borderless mb-0">
		          <tbody>
		            <tr>
		              <th class="fw-bold" style="width: 100px;">Client</th>
		              <td><?= htmlspecialchars($vente['client']) ?></td>
		            </tr>
		            <tr>
		              <th class="fw-bold">Date</th>
		              <td><?= date('d/m/Y H:i', strtotime($vente['date_vente'])) ?></td>
		            </tr>
		          </tbody>
		        </table>
		      </div>
		    </div>

		    <!-- Carte Statut & Montants -->
		    <div class="card shadow-sm">
		      <div class="card-body p-2">
		        <table class="table table-sm table-borderless mb-0">
		          <tbody>
		            <tr>
		              <th class="fw-bold" style="width: 100px;">Statut</th>
		              <td><?= statutBadge($vente['id_statut_paiement']) ?></td>
		            </tr>
		            <tr>
		              <th class="fw-bold">Total</th>
		              <td><?= number_format($vente['montant_total'],0,',',' ') ?> FCFA</td>
		            </tr>
		            <tr>
		              <th class="fw-bold">Réglé</th>
		              <td><?= number_format($vente['montant_regle'],0,',',' ') ?> FCFA</td>
		            </tr>
		            <tr>
		              <th class="fw-bold">Reste</th>
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
	</div>


  </div>
</div>
