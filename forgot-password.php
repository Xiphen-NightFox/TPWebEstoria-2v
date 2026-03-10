<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récupération - Estoria</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="page-centrale">
        
        <!-- GAUCHE -->
        <div class="left-side">
            <h1 class="title1">Récupération</h1>
            <p style="opacity: 0.8; max-width: 300px; margin: 0 auto;">Entrez votre email pour recevoir un lien de réinitialisation.</p>
        </div>

        <!-- DROITE -->
        <div class="right-side">
            <div id="connexion">
                <form> <!-- Retour à l'accueil après envoi -->
                      <div class="input-group">
                            <input class="Password" type="password" placeholder="Mot de passe" >
                            <span id="error-Password" class="error-text titanic">Mot de passe incorrect</span>
                        </div>
                    
                    <button type="submit" id="btn-login" style="width: auto; padding: 0 20px; border-radius: 25px; font-size: 14px;">
                        Envoyer le lien
                    </button>
                </form>
                
                <div class="register-link">
                    <a href="index.html">Retour à la connexion</a>
                </div>
            </div>
        </div>

    </div>
</body>
<script src="script-Fpassword.js"></script>
</html>
