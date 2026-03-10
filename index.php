<?php
// On démarre la session pour pouvoir mémoriser qui se connecte
session_start();

// On inclut le fichier de connexion à la base de données
require_once __DIR__ . '/db.php';

// On prépare une variable vide pour afficher un message d'erreur si l'utilisateur se trompe
$erreur = ''; 

// On vérifie si l'utilisateur a cliqué sur le bouton de connexion (qui envoie le formulaire)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // On récupère l'email et le mot de passe tapés dans le formulaire HTML
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // On cherche dans la base de données s'il existe un client avec cet email précis
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE email = ?");
    $stmt->execute([$email]);
    // On récupère toutes les informations de ce client sous forme de tableau
    $client = $stmt->fetch(PDO::FETCH_ASSOC); 

    // On vérifie que le client a bien été trouvé ET que le mot de passe tapé correspond au mot de passe crypté de la base de données
    if ($client && password_verify($mot_de_passe, $client['mot_de_passe'])) {
        
        // La connexion est réussie ! On sauvegarde l'ID et le nom du client dans la session pour qu'il reste connecté sur les autres pages
        $_SESSION['client_id'] = $client['id'];
        $_SESSION['client_nom'] = $client['nom'];
        
        // On le redirige automatiquement vers son tableau de bord
        header("Location: dashboard.php");
        exit; 
    } else {
        // Si l'email n'existe pas ou que le mot de passe est faux, on prépare ce message d'erreur
        $erreur = "<div style='color: red; text-align: center; margin-bottom: 10px;'>Email ou mot de passe incorrect.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Estoria</title>
    <!-- Chargement de la feuille de style pour l'apparence de la page -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="page-centrale">
        
        <!-- Partie gauche de la page avec le titre et le logo -->
        <div class="left-side">
            <h1 class="title1">Bienvenue sur Estoria</h1>
            <img src="logo.png" alt="logo" class="logo-hero">
        </div>

        <!-- Partie droite contenant le formulaire de connexion -->
        <div class="right-side">
            <div id="connexion">
                
                <!-- Zone qui affiche le message d'erreur PHP s'il y en a un -->
                <?php echo $erreur; ?>

                <!-- Formulaire envoyé en méthode POST pour cacher les données (comme le mot de passe) dans l'URL -->
                <form method="POST">
                    
                    <!-- Champ pour l'email -->
                    <div class="input-group">
                        <input type="email" name="email" class="Email" placeholder="Email">
                        <span class="error-text titanic" id="error-Email">Email invalide</span>
                    </div>

                    <!-- Champ pour le mot de passe -->
                    <div class="input-group">
                        <input type="password" name="mot_de_passe" class="Password" placeholder="Mot de passe">
                        <span class="error-text titanic" id="error-Password">Mot de passe incorrect</span>
                    </div>

                    <!-- Bouton pour valider la connexion -->
                    <button type="submit" class="submits" id="btn-login">→</button>
                    
                </form>

                <!-- Lien pour réinitialiser le mot de passe en cas d'oubli -->
                <div class="forgot-link">
                    <a href="forgot-password.html">Mot de passe oublié ?</a>
                </div>

                <!-- Lien de redirection vers la page d'inscription pour les nouveaux utilisateurs -->
                <div class="register-link">
                    Première connexion ? <a href="register.php">Inscrivez-vous</a>
                </div>
            </div>
        </div>

    </div>
</body>
<!-- Script JavaScript pour les animations et la validation visuelle du formulaire -->
<script src="script-index.js"></script>
</html>
