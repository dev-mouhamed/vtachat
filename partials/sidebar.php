<?php
// Obtenir le nom du fichier actuel (ex : "client_credit.php")
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar" data-background-color="dark">
  <div class="sidebar-logo">
    <div class="logo-header" data-background-color="dark">
      <a href="index.php" class="logo">
        <img src="assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
      </a>
      <div class="nav-toggle">
        <button class="btn btn-toggle toggle-sidebar">
          <i class="gg-menu-right"></i>
        </button>
        <button class="btn btn-toggle sidenav-toggler">
          <i class="gg-menu-left"></i>
        </button>
      </div>
      <button class="topbar-toggler more">
        <i class="gg-more-vertical-alt"></i>
      </button>
    </div>
  </div>

  <div class="sidebar-wrapper scrollbar scrollbar-inner">
    <div class="sidebar-content">
      <ul class="nav nav-secondary">
        
        <!-- Accueil -->
        <li class="nav-item <?= $current_page == 'index.php' ? 'active' : '' ?>">
          <a href="index.php">
            <i class="fas fa-home"></i>
            <p>Accueil</p>
          </a>
        </li>

        <li class="nav-section">
          <span class="sidebar-mini-icon">
            <i class="fa fa-ellipsis-h"></i>
          </span>
          <h4 class="text-section">Opérations</h4>
        </li>

        <!-- Ventes -->
        <?php
          // Si la page actuelle est dans le module "Ventes"
          $vente_pages = ['vente_nouvelle.php', 'vente_liste.php'];
          $vente_active = in_array($current_page, $vente_pages) ? 'active' : '';
          $vente_show = in_array($current_page, $vente_pages) ? 'show' : '';
        ?>
        <li class="nav-item <?= $vente_active ?>">
          <a data-bs-toggle="collapse" href="#venteNav" aria-expanded="<?= $vente_show ? 'true' : 'false' ?>">
            <i class="fas fa-layer-group"></i>
            <p>Ventes</p>
            <span class="caret"></span>
          </a>
          <div class="collapse <?= $vente_show ?>" id="venteNav">
            <ul class="nav nav-collapse">
              <li class="<?= $current_page == 'vente_nouvelle.php' ? 'active' : '' ?>">
                <a href="vente_nouvelle.php">
                  <span class="sub-item">Nouvelle</span>
                </a>
              </li>
              <li class="<?= $current_page == 'vente_liste.php' ? 'active' : '' ?>">
                <a href="vente_liste.php">
                  <span class="sub-item">Liste des ventes</span>
                </a>
              </li>
            </ul>
          </div>
        </li>

        <!-- Clients -->
        <?php
          $client_pages = ['client_credit.php', 'client_liste.php'];
          $client_active = in_array($current_page, $client_pages) ? 'active' : '';
          $client_show = in_array($current_page, $client_pages) ? 'show' : '';
        ?>
        <li class="nav-item <?= $client_active ?>">
          <a data-bs-toggle="collapse" href="#clientNav" aria-expanded="<?= $client_show ? 'true' : 'false' ?>">
            <i class="fas fa-th-list"></i>
            <p>Clients</p>
            <span class="caret"></span>
          </a>
          <div class="collapse <?= $client_show ?>" id="clientNav">
            <ul class="nav nav-collapse">
              <li class="<?= $current_page == 'client_credit.php' ? 'active' : '' ?>">
                <a href="client_credit.php">
                  <span class="sub-item">Crédits clients</span>
                </a>
              </li>
              <li class="<?= $current_page == 'client_liste.php' ? 'active' : '' ?>">
                <a href="client_liste.php">
                  <span class="sub-item">Liste des clients</span>
                </a>
              </li>
            </ul>
          </div>
        </li>

      </ul>
    </div>
  </div>
</div>
