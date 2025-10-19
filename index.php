<?php include_once('./partials/header.php') ?>
  
  <style>
    .clickable-card {
      cursor: pointer;
      transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .clickable-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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
            <div
              class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4"
            >
              <div>
                <h3 class="fw-bold mb-3">Tableau de bord</h3>
                <h6 class="op-7 mb-2">Raccourcis pour effectuer des opérations rapidement !</h6>
              </div>
              <!-- <div class="ms-md-auto py-2 py-md-0">
                <a href="#" class="btn btn-primary btn-round">Add Customer</a>
              </div> -->
            </div>
            <div class="row">
              <!-- 🧾 Nouvelle vente -->
              <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round clickable-card" onclick="window.location.href='vente_nouvelle.php'">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                          <i class="fas fa-shopping-cart"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                          <h5 class="card-title">Facture</h5>
                          <p class="card-category">Nouvelle vente</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 👥 Clients -->
              <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round clickable-card" onclick="window.location.href='client_liste.php'">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-icon">
                        <div class="icon-big text-center icon-info bubble-shadow-small">
                          <i class="fas fa-user-check"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                          <h4 class="card-title">Clients</h4>
                          <p class="card-category">Voir les clients</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 🛍️ Liste des ventes -->
              <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round clickable-card" onclick="window.location.href='vente_liste.php'">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-icon">
                        <div class="icon-big text-center icon-success bubble-shadow-small">
                          <i class="fas fa-luggage-cart"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                          <h4 class="card-title">Ventes</h4>
                          <p class="card-category">Liste des ventes</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 💳 Crédits clients -->
              <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round clickable-card" onclick="window.location.href='client_credit.php'">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-icon">
                        <div class="icon-big text-center icon-danger bubble-shadow-small">
                          <i class="far fa-check-circle"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                          <h4 class="card-title">Crédits</h4>
                          <p class="card-category">Crédit des clients</p>
                        </div>
                      </div>
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
