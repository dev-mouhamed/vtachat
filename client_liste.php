<?php 
  include_once('./managerDB.php'); // ton fichier de connexion MySQL
  $sql = "SELECT * FROM clients ORDER BY id_client DESC";
  $result = $pdo->query($sql);

  // IF ON EST EN MODE DETAILS
  $id_detail = 0;
  $nom = null;
  $telephone = null;
  $adresse = null;

  if(isset($_GET['id_edit'])){
      $id_detail = 1; 
      $id_edit = echapper($_GET['id_edit']);

      $sql_one = "SELECT * FROM clients WHERE id_client = $id_edit";
      $result_one = $pdo->query($sql_one);
      $data_one = $result_one->fetch();

      if ($data_one) {
        $nom = $data_one['nom'];
        $telephone = $data_one['telephone'];
        $adresse = $data_one['adresse'];
      }
  }

  if (isset($_POST['Enregistrer_client'])) {
    // Vérifie que le nom est bien renseigné
    if (not_empty_group(['nom'])) {

        // Récupération des données du formulaire
        $nom       = trim($_POST['nom']);
        $telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : null;
        $adresse   = isset($_POST['adresse']) ? trim($_POST['adresse']) : null;

        try {
            $pdo->beginTransaction();

            // 🔹 Insertion dans la table clients
            $stmt = $pdo->prepare("
                INSERT INTO clients (nom, telephone, adresse)
                VALUES (:nom, :telephone, :adresse)
            ");

            $stmt->execute([
                ':nom'       => $nom,
                ':telephone' => $telephone,
                ':adresse'   => $adresse
            ]);

            $pdo->commit();

            // 🔔 Message de succès
            alert_message('success', "Client ajouté avec succès !");
            header('Location:client_liste.php');
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            alert_message('danger', "Erreur lors de l’enregistrement du client. Veuillez réessayer !");
            // Si besoin pour debug : echo "Erreur : " . $e->getMessage();
        }

    } else {
        // Champs obligatoires manquants
        alert_message('warning', "Le nom du client est obligatoire !");
        header('Location:client_liste.php');
        exit;
      }
  }

  if (isset($_POST['Enregistrer_client_update'])) {

    // Vérifie que le nom est bien renseigné
    if (not_empty_group(['nom'])) {

        // Récupération des données du formulaire
        $id_client = $_GET['id_edit'];
        $nom       = trim($_POST['nom']);
        $telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : null;
        $adresse   = isset($_POST['adresse']) ? trim($_POST['adresse']) : null;

        try {
            $pdo->beginTransaction();

            // 🔹 Mise à jour du client
            $stmt = $pdo->prepare("
                UPDATE clients
                SET nom = :nom, telephone = :telephone, adresse = :adresse
                WHERE id_client = :id_client
            ");

            $stmt->execute([
                ':nom'        => $nom,
                ':telephone'  => $telephone,
                ':adresse'    => $adresse,
                ':id_client'  => $id_client
            ]);

            $pdo->commit();

            // 🔔 Message de succès
            alert_message('success', "Client mis à jour avec succès !");
            header('Location:client_liste.php');
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            alert_message('danger', "Erreur lors de la mise à jour du client. Veuillez réessayer !");
            // echo "Erreur : " . $e->getMessage();
        }

    } else {
        // Champs obligatoires manquants
        alert_message('warning', "Le nom du client est obligatoire !");
        header('Location:client_liste.php');
        exit;
    }
  }
?>

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
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
              <div>
                <h3 class="fw-bold mb-3">Liste des clients</h3>
                <h6 class="op-7 mb-2">💡 Tableau de tous les clients, avec la possibilité de consulter le détail de leurs opérations ! 💼</h6>
              </div>
              <div class="ms-md-auto py-2 py-md-0">
                <a href="vente_nouvelle.php" class="btn btn-primary btn-sm rounded-pill px-3">Nouvelle facture</a>
              </div>
            </div>
            <div class="row">
              <!-- Liste des clients -->
              <div class="col-md-8">
                <div class="card card-round shadow-sm">
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead class="table-dark small">
                          <tr>
                            <th class="py-1 px-2">#</th>
                            <th class="py-1 px-2">Nom</th>
                            <th class="py-1 px-2">Téléphone</th>
                            <th class="py-1 px-2">Adresse</th>
                            <th class="text-center py-1 px-2">Actions</th>
                          </tr>
                        </thead>
                        <tbody class="small">
                          <?php
                            $i = 1;
                            if ($result->rowCount() > 0):
                              while ($row = $result->fetch(PDO::FETCH_ASSOC)):
                          ?>
                            <tr>
                              <td class="py-1 px-2"><?= $i++ ?></td>
                              <td class="py-1 px-2"><?= htmlspecialchars($row['nom']) ?></td>
                              <td class="py-1 px-2"><?= htmlspecialchars($row['telephone']) ?></td>
                              <td class="py-1 px-2"><?= htmlspecialchars($row['adresse']) ?></td>
                              <td class="text-center py-1 px-2">
                                <a href="client_liste.php?id_edit=<?= $row['id_client'] ?>" class="btn btn-icon btn-sm btn-warning me-1 p-1" title="Modifier">
                                  <i class="fa fa-edit" style="font-size: 0.75rem;"></i>
                                </a>
                                <button class="btn btn-icon btn-sm btn-danger p-1" title="Supprimer" onclick="deleteClient(<?= $row['id_client'] ?>)">
                                  <i class="fa fa-trash" style="font-size: 0.75rem;"></i>
                                </button>
                              </td>
                            </tr>
                          <?php
                              endwhile;
                            else:
                          ?>
                            <tr>
                              <td colspan="5" class="text-center text-muted">Aucun client trouvé</td>
                            </tr>
                          <?php endif; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Formulaire d'ajout -->
              <div class="col-md-4">
                <div class="card card-round shadow-sm">
                  <div class="card-header">
                    <?php if(empty($id_edit)): ?>
                      <h4 class="card-title mb-0">Ajouter un client</h4>
                    <?php else: ?>
                      <h4 class="card-title mb-0 text-danger">Modifier un client</h4>
                    <?php endif; ?>
                  </div>
                  <div class="card-body">
                    <form action="" method="POST" id="formClient" class="needs-validation" novalidate autocomplete="off">
                      <div class="form-group">
                        <label for="nom" class="form-label">Nom du client</label>
                        <input type="text" name="nom" id="nom" class="form-control" required placeholder="Ex : Issa Mahamadou" value="<?= set_value('nom', $nom) ?>" />
                      </div>

                      <div class="form-group">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="text" name="telephone" id="telephone" class="form-control" placeholder="Ex : +227 90 12 34 56" value="<?= set_value('telephone', $telephone) ?>" />
                      </div>

                      <div class="form-group">
                        <label for="adresse" class="form-label">Adresse</label>
                        <input type="text" name="adresse" id="adresse" class="form-control" placeholder="Ex : Niamey, Plateau" value="<?= set_value('adresse', $adresse) ?>" />
                      </div>

                      <div class="text-end">
                        <a href="client_liste.php" class="btn btn-outline-danger btn-sm rounded-pill px-3">✖ Annuler</a>
                          <?php if(empty($id_edit)): ?>
                          <button type="submit" name="Enregistrer_client" class="btn btn-primary btn-sm rounded-pill px-3">
                            <i class="fa fa-save"></i> Enregistrer
                          </button>
                          <?php else: ?>

                          <button type="submit" name="Enregistrer_client_update" class="btn btn-primary btn-sm rounded-pill px-3">
                            <i class="fa fa-edit"></i> Modifier
                          </button>
                          <?php endif; ?>
                      </div>
                    </form>
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

<!-- SCRIPT FORMULAIRE CLIENT -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("#formClient");

    if (!form) return; // sécurité si la page n'a pas ce formulaire

    form.addEventListener("submit", (event) => {
      const nom = document.querySelector("#nom").value.trim();

      // Vérifie que le nom est rempli
      if (!nom) {
        event.preventDefault();
        event.stopPropagation();
        alert_js('warning', "Le nom du client est obligatoire !");
        document.querySelector("#nom").focus();
        return;
      }

      // Validation Bootstrap standard
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }

      form.classList.add("was-validated");
    });
  });
</script>

<script>
  function deleteClient(id_client) {
      if (!confirm("Voulez-vous vraiment supprimer ce client ?")) {
          return;
      }

      $.ajax({
          url: 'client_delete.php',      // ton fichier PHP de suppression
          type: 'POST',
          dataType: 'json',              // on attend un JSON en retour
          data: { id_client: id_client },// on envoie l'ID client
          success: function(data) {
              if (data.success) {
                  alert_js('success', data.message);
                  // Supprime la ligne du tableau sans recharger
                  const row = $("button[onclick='deleteClient(" + id_client + ")']").closest('tr');
                  row.remove();
              } else {
                  alert_js('danger', data.message);
              }
          },
          error: function(xhr, status, error) {
              console.error(error);
              alert_js('danger', "Erreur lors de la suppression !");
          }
      });
  }
</script>

