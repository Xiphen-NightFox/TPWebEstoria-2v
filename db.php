<?php
// On utilise 127.0.0.1 au lieu de localhost, et on force le port 8889
$host = '127.0.0.1;port=8889'; 
$dbname = 'ticketing_app'; // Le nom de ta base
$user = 'root';
$pass = 'root'; // Laisse 'root' pour l'instant. Si ça recrée une erreur, mets $pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
