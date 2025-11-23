<?php 
session_start();
require_once "managerDB.php";  // connexion `$pdo`

// Vérifier si ID existe
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID vente invalide !");
}

$id_vente = intval($_GET['id']);

// 1️⃣ Vérifier si la vente existe
$stmt = $pdo->prepare("SELECT * FROM ventes WHERE id_vente = ?");
$stmt->execute([$id_vente]);
$vente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vente) {
    echo "<h1 style='color:red;text-align:center; font-size: 300%; margin-top: 30%'>❌ Cette vente n'existe pas !</h1>";
    echo "<div style='text-align:center;margin-top:20px; font-size: 300%'><a href='vente_liste.php' class='btn btn-primary'>Retour</a></div>";
    exit;
}

// 2️⃣ Vérifier si un paiement existe pour cette vente
$stmt = $pdo->prepare("SELECT COUNT(*) FROM paiements WHERE id_vente = ?");
$stmt->execute([$id_vente]);
$nb_paiements = $stmt->fetchColumn();

if ($nb_paiements > 0) {
    echo "<h1 style='color:red;text-align:center; font-size: 300%; margin-top: 30%'>❌ Suppression impossible</h1>";
    echo "<p style='text-align:center; font-size: 300%'>Cette vente possède déjà un paiement. Vous ne pouvez pas la supprimer.</p>";
    echo "<div style='text-align:center;margin-top:20px; font-size: 300%'><a href='vente_liste.php' class='btn btn-primary'>Retour</a></div>";
    exit;
}

// 3️⃣ Vérifier si la vente date de plus de 2 jours
$date_vente = strtotime($vente['date_vente']);
$maintenant = time();

$diff = ($maintenant - $date_vente) / 3600; // heures

if ($diff > 48) {
    echo "<h1 style='color:red;text-align:center; font-size: 300%'>❌ Suppression impossible</h1>";
    echo "<p style='text-align:center'>Cette vente a été créée il y a plus de 2 jours.<br>Vous ne pouvez plus la supprimer.</p>";
    echo "<div style='text-align:center;margin-top:20px; font-size: 300%'><a href='vente_liste.php' class='btn btn-primary'>Retour</a></div>";
    exit;
}

// 4️⃣ Si l'utilisateur confirme
if (isset($_POST['confirm_delete'])) {

    // Supprimer les lignes de ventes
    $pdo->prepare("DELETE FROM ligne_ventes WHERE id_vente = ?")->execute([$id_vente]);

    // Supprimer la vente
    $pdo->prepare("DELETE FROM ventes WHERE id_vente = ?")->execute([$id_vente]);

    echo "<h3 style='color:green;text-align:center; font-size: 300%'>✔ Vente supprimée avec succès</h3>";
    echo "<div style='text-align:center;margin-top:20px; font-size: 300%'><a href='vente_liste.php' class='btn btn-success'>Retour à la liste</a></div>";
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Suppression Vente</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white">
            <h4 class="mb-0">⚠ Confirmation de suppression</h4>
        </div>

        <div class="card-body">
            <p><strong>Vente #<?= $vente['id_vente'] ?></strong></p>
            <p>Client : <b><?= $vente['id_client'] ?></b></p>
            <p>Date vente : <b><?= date('d/m/Y H:i', strtotime($vente['date_vente'])) ?></b></p>
            <p class="text-danger fw-bold">Voulez-vous vraiment supprimer cette vente ?<br>
            Cette action est irréversible.</p>

            <form method="POST">
                <button class="btn btn-danger" name="confirm_delete">Oui, supprimer</button>
                <a href="vente_liste.php" class="btn btn-secondary">Annuler</a>
            </form>
        </div>
    </div>

</div>

</body>
</html>
