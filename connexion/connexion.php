<?php
// Informations de connexion
$host = 'localhost';     // Hôte (souvent 'localhost')
$dbname = 'vtachat_db'; // Nom de la base de données
$username = 'root';      // Nom d'utilisateur MySQL
$password = '';          // Mot de passe MySQL (souvent vide sous WAMP/XAMPP)

// Tentative de connexion
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Activer le mode exception pour les erreurs
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // (Optionnel) Pour que les résultats soient renvoyés sous forme de tableau associatif par défaut
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Décommenter cette ligne pour vérifier que la connexion marche
    // echo "✅ Connexion réussie à la base de données $dbname";
    
} catch (PDOException $e) {
    // En cas d’erreur, on affiche un message clair
    die("❌ Erreur de connexion : " . $e->getMessage());
}
?>
