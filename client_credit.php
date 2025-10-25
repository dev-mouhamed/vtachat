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
                <h3 class="fw-bold mb-3">Clients avec crédits</h3>
                <h6 class="op-7 mb-2">💡 Tableau de tous les clients qui on des crédits, vous pouvez effectuer des recherches si nécessaire !</h6>

              </div>
              <div class="ms-md-auto py-2 py-md-0">
                <a href="vente_nouvelle.php" class="btn btn-primary btn-round">Nouvelle facture</a>
              </div>
            </div>
            <div class="row">
              
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
