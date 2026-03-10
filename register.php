<?php
// On inclut le fichier contenant la connexion PDO pour pouvoir parler à la base de données
require_once __DIR__ . '/db.php';

// Variable pour afficher un message d'erreur ou de succès plus tard
$message = ''; 

// On vérifie si l'utilisateur a envoyé le formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // On récupère les données tapées dans les champs du formulaire HTML
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $confirm_mdp = $_POST['confirm_mdp'];

    // On s'assure que les deux mots de passe tapés sont identiques
    if ($mot_de_passe === $confirm_mdp) {
        
        // On sécurise le mot de passe en le cryptant avant de l'envoyer dans la base
        $mdp_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        
        // On prépare la requête pour ajouter le nouveau client dans la base de données
        $stmt = $pdo->prepare("INSERT INTO clients (nom, email, mot_de_passe) VALUES (?, ?, ?)");
        
        // On exécute la requête en insérant nos variables à la place des points d'interrogation
        $stmt->execute([$nom, $email, $mdp_hash]);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Estoria</title>
    <!-- Chargement du design de la page -->
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>
    <div class="page-centrale">
        
        <!-- Partie gauche de l'écran avec le logo -->
        <div class="left-side">
            <h1 class="title1">Rejoignez Estoria</h1>
            <img src="logo.png" alt="logo" class="logo-hero">
        </div>

        <!-- Partie droite contenant le formulaire d'inscription -->
        <div class="right-side">
            <div id="connexion">
                
                <!-- Espace pour afficher le message PHP (vide par défaut) -->
                <?php echo $message; ?>

                <!-- Le formulaire utilise la méthode POST pour envoyer les données de manière invisible -->
                <form method="POST">
                    
                    <div class="input-group">
                        <input type="text" name="nom" class="Name" placeholder="Nom complet">
                        <span id="error-Name" class="error-text titanic">Nom complet invalide</span>
                    </div>

                    <div class="input-group">
                        <input type="email" name="email" class="Email" placeholder="Email">
                        <span id="error-Email" class="error-text titanic">Format email invalide</span>
                    </div>

                    <div class="input-group">
                        <input type="password" name="mot_de_passe" class="Password" placeholder="Mot de passe">
                        <span id="error-Password" class="error-text titanic">Mot de passe incorrect</span>
                    </div>

                    <div class="input-group">
                        <input type="password" name="confirm_mdp" class="VerifyPassword" placeholder="Confirmer le mot de passe">
                        <span id="error-VerifyPassword" class="error-text titanic">Les mots de passe ne correspondent pas</span>
                    </div>
                    
                    <!-- Bouton pour valider l'inscription -->
                    <button type="submit" id="btn-login">→</button>
                </form>
                
                <!-- Lien pour ceux qui sont déjà inscrits -->
                <div class="register-link">
                    Déjà un compte ? <a href="index.php">Connectez-vous</a>
                </div>
            </div>
        </div>

    </div>
</body>
<!-- Script pour les animations ou la validation visuelle du formulaire -->
<script src="script-register.js"></script>
</html>
