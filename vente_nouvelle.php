<?php include_once('./partials/header.php') ?>
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
                    <form action="vente_save.php" method="POST" id="formVente">
                      
                      <!-- En-tête -->
                      <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <div>
                          <h5 class="fw-bold text-primary mb-0">🧾 Nouvelle facture</h5>
                          <small class="text-muted">Enregistrez une vente rapidement</small>
                        </div>
                        <div>
                          <a href="vente_liste.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 me-2">↩ Liste</a>
                          <a href="index.php" class="btn btn-danger btn-sm rounded-pill px-3">✖ Annuler</a>
                        </div>
                      </div>

                      <!-- Informations principales -->
                      <div class="row g-2 mb-3">

                        <!-- CLIENT -->
                        <div class="col-md-4 position-relative">
                          <label for="client" class="form-label small fw-semibold mb-1">Client</label>
                          
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
                        <div class="col-md-4">
                          <label for="date_vente" class="form-label small fw-semibold mb-1">Date de la vente</label>
                          <input type="datetime-local" name="date_vente" id="date_vente" class="form-control form-control-sm" value="<?= date('Y-m-d\TH:i') ?>">
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
                        <div class="col-md-2"></div>
                        <div class="col-md-5">
                          <div class="border rounded-3 p-3 bg-light">
                            <div class="mb-2">
                              <label class="form-label small fw-bold mb-1">Montant réglé</label>
                              <input type="text" name="montant_regle" id="montant_regle" class="form-control form-control-sm text-end" value="">
                            </div>
                            <div class="mb-2 d-none">
                              <label class="form-label small fw-bold text-danger mb-1">Reste à payer</label>
                              <input type="text" id="reste_a_payer" class="form-control form-control-sm text-end fw-bold text-danger" readonly>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-5">
                          <div class="border rounded-3 p-3 bg-light">
                            <div class="mb-2">
                              <label class="form-label small fw-bold mb-1">Montant total</label>
                              <input type="text" name="montant_total" id="montant_total" class="form-control form-control-sm text-end fw-bold" readonly>
                            </div>
                          </div>
                            <!-- Boutons -->
                            <div class="text-end mt-3">
                              <button type="submit" class="btn btn-success btn-sm rounded-pill px-4">💾 Enregistrer</button>
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

<script>
$(document).ready(function () {
  const formatFR = new Intl.NumberFormat("fr-FR");
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
    const reste = Math.max(totalGeneral - regle, 0);
    $("#reste_a_payer").val(formatFR.format(reste.toFixed(0)));
  }

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
              $input.val(client.nom).prop("readonly", true);
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
                  $input.val(response.client.nom).prop("readonly", true);
                  selectedClientId = response.client.id_client;
                  $clear.show();
                  $list.empty().hide();
                } else {
                  alert_js(response.type, response.message);
                  // alert("Erreur : " + (response.message || "Impossible d’ajouter le client."));
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

