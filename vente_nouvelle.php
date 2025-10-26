<?php 
  
  session_start();
  require_once 'managerDB.php'; // connexion à ta BDD

  if (isset($_POST['Enregistrer_vente'])) {

    if(not_empty_group(['client_id', 'date_vente', 'produit', 'quantite', 'prix', 'montant_total'])){

      // Récupération des données du formulaire
      $id_client     = (int)$_POST['client_id'];
      $date_vente    = $_POST['date_vente'];
      $produits      = $_POST['produit'];
      $quantites     = $_POST['quantite'];
      $prix          = $_POST['prix'];
      $montant_total = floatval(montant_parse($_POST['montant_total'])); // enlever séparateurs
      $montant_regle = floatval(montant_parse($_POST['montant_regle'])); 

      // Détermination du statut de paiement
      if ($montant_regle >= $montant_total) {
          $id_statut_paiement = 1; // Payé
      } elseif ($montant_regle > 0) {
          $id_statut_paiement = 2; // Partiel
      } else {
          $id_statut_paiement = 3; // Crédit
      }

      try {
          $pdo->beginTransaction();

          // 🔹 Insertion dans ventes
          $stmt = $pdo->prepare("INSERT INTO ventes (id_client, id_statut_paiement, date_vente, montant_total, montant_regle) 
                                 VALUES (:id_client, :id_statut, :date_vente, :montant_total, :montant_regle)");
          $stmt->execute([
              ':id_client'     => $id_client,
              ':id_statut'     => $id_statut_paiement,
              ':date_vente'    => $date_vente,
              ':montant_total' => $montant_total,
              ':montant_regle' => $id_statut_paiement == 1 ? $montant_total : $montant_regle
          ]);

          $id_vente = $pdo->lastInsertId();

          // 🔹 Insertion des lignes de vente
          $stmtLigne = $pdo->prepare("INSERT INTO ligne_ventes (id_vente, produit, quantite, prix, total) 
                                      VALUES (:id_vente, :produit, :quantite, :prix, :total)");

          foreach ($produits as $k => $prod) {
              $qte       = floatval(montant_parse($quantites[$k]));
              $prixLigne = floatval(montant_parse($prix[$k]));
              $total     = $qte * $prixLigne;

              $stmtLigne->execute([
                  ':id_vente' => $id_vente,
                  ':produit' => $prod,
                  ':quantite' => $qte,
                  ':prix' => $prixLigne,
                  ':total' => $total
              ]);
          }

          // 🔹 Insertion dans paiements selon le statut
          if($id_statut_paiement == 1 || $id_statut_paiement == 2){
              $stmtPaiement = $pdo->prepare("
                  INSERT INTO paiements (id_vente, date_paiement, montant, statut, responsable)
                  VALUES (:id_vente, :date_paiement, :montant, :statut, :responsable)
              ");
              $stmtPaiement->execute([
                  ':id_vente'     => $id_vente,
                  ':date_paiement'=> date('Y-m-d H:i:s'),
                  ':montant'      => $id_statut_paiement == 1 ? $montant_total : $montant_regle,
                  ':statut'       => true,
                  ':responsable'  => 'Système vente'
              ]);
          }


          $pdo->commit();
          alert_message('success', 'Vente enregistrée avec succès !');
          header('location:vente_liste.php?id_vente='.$id_vente);
          die();

      } catch (Exception $e) {
          $pdo->rollBack();
          // echo "Erreur : " . $e->getMessage();
          alert_message('danger', "Echec de l'enregistrement, manque des valeurs obligatoires.");
      }

    }
    else
    {
      alert_message('danger', "Echec de l'enregistrement, manque des valeurs obligatoires.");
      header('location:vente_nouvelle.php');
      die();
    }
  }

?>


<?php include_once('./partials/header.php') ?>

  <style>
    #montant_total[readonly],
#reste_a_payer[readonly] {
  background-color: #fff !important; /* ou #e9ecef si tu veux gris clair */
  cursor: not-allowed; /* optionnel : curseur interdit */
  opacity: 1 !important;
}


  </style>  

  <body>
    <div class="wrapper">
      <!-- Sidebar -->
      <?php include_once('./partials/sidebar.php') ?>
      <!-- End Sidebar -->

      <div class="main-panel">
        <?php include_once('./partials/topbar.php') ?>

        <div class="container">
          <div class="page-inner">
            <div class="row">
              <div class="row justify-content-center">
                <div class="col-12 col-lg-12">
                  <div class="card border-0 shadow-sm p-3 rounded-3 bg-white">
                    <form action="" method="POST" id="formVente" class="needs-validation" novalidate autocomplete="off">
                      
                      <!-- En-tête -->
                      <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <div>
                          <h5 class="fw-bold text-primary mb-0">🧾 Nouvelle facture</h5>
                          <small class="text-muted">Enregistrez une vente rapidement</small>
                        </div>
                        <div>
                          <a href="vente_nouvelle.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 me-2">↩ Réinitialisé</a>
                          <a href="index.php" class="btn btn-outline-danger btn-sm rounded-pill px-3">✖ Annuler</a>
                          <button type="submit" name="Enregistrer_vente" class="btn btn-outline-success btn-sm rounded-pill px-3">💾 Enregistrer</button>
                        </div>
                      </div>

                      <!-- Informations principales -->
                      <div class="row g-2 mb-3">

                        <!-- CLIENT -->
                        <div class="col-md-4 position-relative">
                          <label for="client" class="form-label small fw-semibold mb-1">Client</label>
                          
                          <input type="hidden" id="client_id" name="client_id">

                          <div id="clientWrapper" class="position-relative">
                            <input type="text" id="client" name="client"
                                   class="form-control form-control-sm pe-5"
                                   placeholder="Saisir le client..."
                                   autocomplete="off" required>
                            
                            <!-- Bouton “close” (masqué au départ) -->
                            <button type="button" id="clearClient"
                                    class="btn btn-sm btn-light border position-absolute top-50 end-0 translate-middle-y me-1 px-2 py-0"
                                    style="display:none;">✖</button>
                          </div>

                          <div id="clientList"
                               class="list-group position-absolute shadow-sm mt-1"
                               style="z-index:1000; display:none; width:92%;"></div>
                        </div>

                        <!-- DATE FACTURE -->
                        <div class="col-md-4 position-relative">
                          <label for="date_vente" class="form-label small fw-semibold mb-1">Date de la vente</label>
                          <div class="input-group input-group-sm">
                            <input type="text"
                                   id="date_vente"
                                   name="date_vente"
                                   class="form-control form-control-sm shadow-sm"
                                   placeholder="Choisir la date..."
                                   required>
                            <span class="input-group-text bg-light"><i class="bi bi-calendar-event"></i></span>
                          </div>
                        </div>


                      </div>

                      <!-- Produits -->
                      <div class="border rounded-3 p-3 mb-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <h6 class="fw-bold text-secondary mb-0">🛍️ Produits vendus</h6>
                          <button type="button" id="addLigne" class="btn btn-outline-primary btn-sm rounded-pill px-2 py-1">+ Ajouter</button>
                        </div>

                        <div class="table-responsive">
                          <table class="table table-sm table-bordered align-middle mb-0">
                            <thead class="table-light text-center small">
                              <tr>
                                <th>Produit</th>
                                <th style="width:170px;">Qté</th>
                                <th style="width:170px;">Prix</th>
                                <th style="width:170px;">Total</th>
                                <th style="width:50px;">✖</th>
                              </tr>
                            </thead>
                            <tbody id="ligneContainer">
                              <tr>
                                <td>
                                  <textarea name="produit[]" placeholder="Saisir le produit ..." class="form-control form-control-sm shadow-sm" rows="1" required></textarea>
                                </td>
                                <td><input type="text" name="quantite[]" placeholder="Saisir Qté ..." class="form-control form-control-sm quantite" required></td>
                                <td><input type="text" name="prix[]" placeholder="Saisir Prix" class="form-control form-control-sm prix" required></td>
                                <td class="text-center fw-semibold total-text align-middle">0</td>
                                <td class="text-center"><button type="button" class="btn btn-sm btn-danger border removeRow py-0">✖</button></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>

                      <!-- Commentaire + Totaux -->
                      <div class="row g-3 align-items-start">
                        <div class="col-md-6"></div>
                        <div class="col-md-3">
                          <div id="blocPaiement" style="display:none;">
                            <div class="border rounded-3 p-3 bg-light">
                              <div class="mb-2">
                                <label class="form-label small fw-bold mb-1">Montant réglé</label>
                                <input type="text" name="montant_regle" id="montant_regle"
                                       class="form-control form-control-sm text-center">
                              </div>
                              <div class="mb-2">
                                <label for="reste_a_payer" class="form-label small fw-bold text-danger mb-1">
                                  Reste à payer
                                </label>
                                <input type="text" id="reste_a_payer"
                                       class="form-control form-control-sm text-center fw-bold text-danger" readonly>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="col-md-3">
                          <div class="border rounded-3 p-3 bg-light">
                            <div class="mb-4">
                              <label class="form-label small fw-bold mb-1">Montant total</label>
                              <input type="text" name="montant_total" id="montant_total" class="form-control form-control-sm text-end fw-bold" readonly>
                            </div>
                          </div>
                            <!-- Boutons -->
                            <div class="text-end mt-3">
                              <a href="index.php" class="btn btn-danger btn-sm rounded-pill px-3">✖ Annuler</a>
                              <button type="submit" name="Enregistrer_vente" class="btn btn-success btn-sm rounded-pill px-4">💾 Enregistrer</button>
                            </div>
                        </div>
                      </div>

                    </form>
                  </div>
                </div>
              </div>
            </div>
            </div>
          </div>
        </div>

        <?php include_once('./partials/baspage.php') ?>
      </div>

      <!-- Custom template | don't include it in your project! -->
        <?php include_once('./partials/params.php') ?>
      <!-- End Custom template -->
    </div>
    
    <?php include_once('./partials/footer.php') ?>
  </body>
</html>

<!-- SCRIPT PANIER -->
<script>
  $(document).ready(function () {
    const formatFR = new Intl.NumberFormat("fr-FR");

    $(document).on("input", ".prix, .quantite, #montant_regle", function () {
      let value = $(this).val().replace(/\D/g, "");
      $(this).val(formatFR.format(value));
      
      // Curseur à la fin
      const len = $(this).val().length;
      this.setSelectionRange(len, len);
    });


    const tbody = $("#ligneContainer");

    // 🔹 Fonction pour ajouter une ligne vide
    function addNouvelleLigne() {
      const ligne = `
        <tr>
          <td><textarea name="produit[]" class="form-control form-control-sm shadow-sm" rows="1" placeholder="Saisir le produit..." required></textarea></td>
          <td><input type="text" name="quantite[]" class="form-control form-control-sm shadow-sm quantite" placeholder="Qté" required></td>
          <td><input type="text" name="prix[]" class="form-control form-control-sm shadow-sm prix" placeholder="Prix" required></td>
          <td class="text-center fw-semibold total-text align-middle" data-raw="0">0</td>
          <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger border removeRow py-0">✖</button>
          </td>
        </tr>`;
      tbody.append(ligne);
    }

    // 🔹 Réinitialiser le tbody au chargement
    tbody.empty();
    addNouvelleLigne();

    // 🔹 Recalculer les totaux généraux
    function recalculerTotaux() {
      let totalGeneral = 0;

      $(".total-text").each(function () {
        totalGeneral += parseFloat($(this).data("raw")) || 0;
      });

      $("#montant_total").val(formatFR.format(totalGeneral.toFixed(0)));

      const regle = parseFloat($("#montant_regle").val().replace(/\s/g, "")) || 0;
      const $resteLabel = $("label[for='reste_a_payer']");
      const $resteInput = $("#reste_a_payer");

      let difference = totalGeneral - regle;

      // ✅ Si le montant réglé dépasse le total → "Monnaie client"
      if (difference < 0) {
        difference = Math.abs(difference);
        $resteLabel.text("Monnaie client").removeClass("text-danger").addClass("text-success");
        $resteInput
          .val(formatFR.format(difference.toFixed(0)))
          .removeClass("text-danger")
          .addClass("text-success");
      } 
      // ✅ Sinon → "Reste à payer"
      else {
        $resteLabel.text("Reste à payer").removeClass("text-success").addClass("text-danger");
        $resteInput
          .val(formatFR.format(difference.toFixed(0)))
          .removeClass("text-success")
          .addClass("text-danger");
      }

      // ✅ Afficher ou cacher le bloc paiement selon le total
      const $blocPaiement = $("#blocPaiement");
      if (totalGeneral > 0) {
        $blocPaiement.slideDown(200);
      } else {
        $("#montant_regle").val("");
        $("#reste_a_payer").val("");
        $blocPaiement.slideUp(200);
      }
    }


    // 🟢 Recalculer quand le montant réglé change
    $(document).on("input", "#montant_regle", recalculerTotaux);


    // 🔹 Calcul du total par ligne
    $(document).on("input", ".quantite, .prix", function () {
      const ligne = $(this).closest("tr");
      const qte = parseFloat(ligne.find(".quantite").val().replace(/\s/g, "")) || 0;
      const prix = parseFloat(ligne.find(".prix").val().replace(/\s/g, "")) || 0;
      const total = qte * prix;

      ligne.find(".total-text").data("raw", total).text(formatFR.format(total.toFixed(0)));
      recalculerTotaux();
    });

    // 🔹 Format automatique FR pendant la saisie
    $(document).on("input", ".prix, .quantite, #montant_regle", function () {
      const cursorPos = this.selectionStart;
      let value = $(this).val().replace(/\D/g, "");
      $(this).val(formatFR.format(value));
      this.setSelectionRange(cursorPos, cursorPos);
    });

    // 🔹 Ajouter une nouvelle ligne
    $("#addLigne").on("click", addNouvelleLigne);

    // 🔹 Supprimer une ligne
    $(document).on("click", ".removeRow", function () {
      $(this).closest("tr").remove();
      recalculerTotaux();
    });

    // 🔹 Initialisation des totaux
    recalculerTotaux();
  });
</script>

<!-- SCRIPT CLIENT -->
<script>
  $(document).ready(function() {
    const $input = $("#client");
    const $list = $("#clientList");
    const $wrapper = $input.closest(".position-relative");

    // Bouton ✖ (créé dynamiquement)
    const $clear = $('<button type="button" id="clearClient" class="btn btn-sm btn-danger border position-absolute top-50 end-0 translate-middle-y me-1 px-2 py-0" style="display:none;">✖</button>');
    $wrapper.append($clear);

    let selectedClientId = null;

    // Recherche AJAX
    $input.on("input", function() {
      const query = $(this).val().trim();
      selectedClientId = null;

      if(query.length < 2) {
        $list.empty().hide();
        return;
      }

      $.ajax({
        url: "client_search_ajax.php",
        method: "GET",
        data: { search: query },
        dataType: "json",
        success: function(data) {
          $list.empty();

          if(data.length > 0) {
            data.forEach(client => {
              const item = $(
                `<button type="button" class="list-group-item list-group-item-action small py-1">${client.nom}</button>`
              );
              item.on("click", function() {
                $input.val(client.nom).prop("readonly", true); // affichage du nom
                $("#client_id").val(client.id_client);        // soumission de l'id
                selectedClientId = client.id_client;
                $list.empty().hide();
                $clear.show();
              });

              $list.append(item);
            });
            $list.show();
          }  else {
            // ➕ Si le client n’existe pas → création AJAX directe
            const addItem = $(
              `<button type="button" class="list-group-item list-group-item-action text-success small py-1">➕ Ajouter "${query}"</button>`
            );
            addItem.on("click", function() {
              $.ajax({
                url: "client_add_ajax.php",
                method: "POST",
                data: { nom: query },
                dataType: "json",
                success: function(response) {
                  if (response.success) {
                    alert_js('success', response.message);

                    // Affiche le nom dans le champ visible
                    $input.val(response.client.nom).prop("readonly", true);

                    // Stocke l'ID dans le champ caché pour soumission
                    $("#client_id").val(response.client.id_client);
                    selectedClientId = response.client.id_client;

                    $clear.show();
                    $list.empty().hide();
                  } else {
                    alert_js(response.type, response.message);
                  }
                },
                error: function() {
                  alert_js('danger', "Erreur de communication avec le serveur.");
                }
              });
            });
            $list.append(addItem).show();
          }
        }
      });
    });

    // Bouton ✖ → réinitialiser
    $clear.on("click", function() {
      selectedClientId = null;
      $input.val("").prop("readonly", false).focus();
      $("#client_id").val(""); // réinitialiser l'id
      $(this).hide();
    });


    // Cacher la liste si on clique ailleurs
    $(document).on("click", function(e) {
      if(!$(e.target).closest("#client, #clientList").length) {
        $list.hide();
      }
    });
  });
</script>

<!-- SCRIPT FORMULAIRE -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("#formVente");

    form.addEventListener("submit", (event) => {
      const clientId = document.querySelector("#client_id").value;

      // Vérifie que le client a bien été sélectionné
      if (!clientId) {
        event.preventDefault();
        event.stopPropagation();
        alert_js('warning', "Veuillez sélectionner un client valide avant de continuer.");
        return;
      }

      // Vérifie les autres validations Bootstrap
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }

      form.classList.add("was-validated");
    });
  });

</script>