<?php
// Démarre la session uniquement si elle n'existe pas déjà
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Vérifie si $_SESSION['alert'] existe
if (!empty($_SESSION['alert'])): ?>
    <script>
        $.notify({
            icon: 'icon-bell',
            title: 'Infos',
            message: "<?php echo addslashes($_SESSION['alert']['message']); ?>",
        },{
            type: "<?php echo $_SESSION['alert']['type']; ?>",
            placement: {
                from: "bottom",
                align: "right"
            },
            time: 1000,
        });
    </script>
<?php 
endif; 

// Nettoie l'alerte
if (isset($_SESSION['alert'])) {
    unset($_SESSION['alert']);
}
?>
