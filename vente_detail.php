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
	    SELECT *  FROM paiements WHERE id_vente = :id_vente ORDER BY id_paiement DESC
	");
	$stmt_versement->execute(['id_vente' => $id_vente]);
	$ligne_versement = $stmt_versement->fetchAll(PDO::FETCH_ASSOC);

?>

<style>
	@media (max-width: 768px) {
  .container,
  .page-inner,
  .card-body,
  .tab-content,
  .table-responsive {
    padding-left: 12px !important;
    padding-right: 12px !important;
  }

  /* Pour éviter les éléments collés */
  .row {
    margin-left: 0 !important;
    margin-right: 0 !important;
  }

  .col-md-8,
  .col-md-4 {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }

  /* Les cartes doivent respirer un peu */
  .card {
    margin-left: 5px !important;
    margin-right: 5px !important;
    border-radius: 10px !important;
  }
}
@media (max-width: 768px) {
  /* STYLE CARDS POUR TABLE FACTURE */
  #pills-home-icon .table-responsive thead {
    display: none !important;
  }

  #pills-home-icon .table-responsive tbody tr {
    display: block;
    background: #fff;
    margin-bottom: 1rem;
    border-radius: 10px;
    padding: 0.8rem;
    border: 1px solid #eee;
  }

  #pills-home-icon .table-responsive tbody tr td {
    display: flex;
    justify-content: space-between;
    padding: 0.45rem 0 !important;
    font-size: 0.9rem;
  }

  #pills-home-icon .table-responsive tbody tr td:before {
    content: attr(data-label);
    font-weight: bold;
    color: #444;
  }
}

@media (max-width: 768px) {

  /* Désactiver le thead uniquement pour ce tableau */
  #pills-profile-icon .table-responsive thead {
    display: none !important;
  }

  /* Chaque ligne devient une carte */
  #pills-profile-icon .table-responsive tbody tr {
    display: block;
    background: #fff;
    margin-bottom: 1rem;
    border-radius: 10px;
    padding: 0.8rem;
    border: 1px solid #eee;
  }

  /* Les cellules se réorganisent */
  #pills-profile-icon .table-responsive tbody tr td {
    display: flex;
    justify-content: space-between;
    padding: 0.45rem 0 !important;
    font-size: 0.9rem;
  }

  /* Le label avant chaque valeur */
  #pills-profile-icon .table-responsive tbody tr td:before {
    content: attr(data-label);
    font-weight: bold;
    color: #444;
  }
}


</style>

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
        <button class="btn btn-danger btn-sm rounded-pill px-3"
				        data-bs-toggle="modal"
				        data-bs-target="#modalVersement"
				        data-id-vente="<?= $vente['id_vente'] ?>">
				  <i class="fa fa-plus"></i> Versement
				</button>

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
								<td data-label="Produit"><?= htmlspecialchars($ligne['produit']) ?></td>
								<td data-label="Quantité" class="text-center"><?= number_format($ligne['quantite'],0,',',' ') ?></td>
								<td data-label="PU" class="text-end"><?= number_format($ligne['prix'],0,',',' ') ?></td>
								<td data-label="Total" class="text-end"><?= number_format($ligne['total'],0,',',' ') ?></td>
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
				        <h3 class="fw-bold">Versement facture #<?= $id_vente ?></h3>
				        <h6 class="op-7">💡 List des versments pour cette facture</h6>
				      </div>
			        <div class="table-responsive">
			          <table class="table table-striped table-hover table-sm mb-0">
			            <thead class="table-danger">
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
								<td data-label="Date">
								<?= date('d/m/Y H:i', strtotime($versement['date_paiement'])) ?>
								</td>

								<td data-label="Responsable" class="text-center">
								<?= ($versement['responsable'] ?? '-') ?>
								</td>

								<td data-label="Montant" class="text-end">
								<?= number_format($versement['montant'],0,',',' ') ?>
								</td>

								<td data-label="Statut" class="text-end">
								<?= statutBadgePaiement($versement['statut']) ?>
								</td>
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

	<div class="modal fade" id="modalVersement" tabindex="-1" aria-labelledby="modalVersementLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered">
	    <div class="modal-content">

	      <div class="modal-header bg-danger text-white">
	        <h5 class="modal-title" id="modalVersementLabel">
	          💰 Nouveau Versement
	        </h5>
	        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
	      </div>

	      <form id="formVersement" action="" method="POST" class="needs-validation" novalidate autocomplete="off">
	        <div class="modal-body">
	          <input type="hidden" name="id_vente" id="id_vente">

	          <div class="mb-3">
						  <label for="montant" class="form-label fw-bold">Montant versé</label>
						  
						  <input type="text" class="form-control" id="montant" name="montant" placeholder="Entrer le montant ..." required value="<?= set_value('montant_operation') ?>" onkeyup="formatMoney(this)" pattern="([0-9]{1,3}\s?)*">

						  <div class="text-muted text-end">
						    Reste à payer : <span id="resteAffiche" class="fw-bold text-danger"><?= number_format($vente['montant_total'] - $vente['montant_regle'], 0, ',', ' ') ?></span> FCFA
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
	          <button type="submit" name="paiement_add" class="btn btn-danger">Enregistrer</button>
	        </div>
	      </form>

	    </div>
	  </div>
	</div>

  </div>
</div>