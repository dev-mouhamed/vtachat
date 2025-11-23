<?php
// Informations de connexion Railway
$host = getenv('MYSQLHOST') ?: 'mysql.railway.internal';
$port = getenv('MYSQLPORT') ?: '3306';
$dbname = getenv('MYSQLDATABASE') ?: 'railway';
$username = getenv('MYSQLUSER') ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: '';

// Tentative de connexion
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    
    // Activer le mode exception pour les erreurs
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Pour que les résultats soient renvoyés sous forme de tableau associatif par défaut
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Décommenter cette ligne pour vérifier que la connexion marche
    // echo "✅ Connexion réussie à la base de données $dbname";
    
} catch (PDOException $e) {
    // En cas d'erreur, on affiche un message clair
    die("❌ Erreur de connexion : " . $e->getMessage());
}
?>