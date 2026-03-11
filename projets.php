<?php
// On démarre la session pour pouvoir identifier l'utilisateur connecté
session_start();
// On inclut la connexion à la base de données
require_once __DIR__ . '/db.php';

// Si personne n'est connecté, on le renvoie à la page de connexion
if (!isset($_SESSION['client_id'])) {
    header("Location: index.php");
    exit;
}

// On mémorise l'ID du client connecté pour l'utiliser dans nos requêtes SQL
$client_id = $_SESSION['client_id']; 

// Création d'un nouveau projet quand le formulaire de la modale est envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // On récupère toutes les informations saisies dans le formulaire
    $titre = $_POST['nom'];
    $description = $_POST['description'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    // On prépare la requête pour insérer le nouveau projet lié à ce client précis
    $stmt = $pdo->prepare("INSERT INTO projets (client_id, titre, description, date_debut, date_fin) VALUES (?, ?, ?, ?, ?)");
    
    // On exécute l'insertion puis on recharge la page pour afficher le nouveau projet
    $stmt->execute([$client_id, $titre, $description, $date_debut, $date_fin]);
    header("Location: projets.php?success=1");
    exit();
}

// On récupère tous les projets du client connecté, en comptant aussi combien de tickets chaque projet possède
$sql = "SELECT p.*, COUNT(t.id) AS nb_tickets 
        FROM projets p 
        LEFT JOIN tickets t ON p.id = t.projet_id 
        WHERE p.client_id = ? 
        GROUP BY p.id 
        ORDER BY p.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$client_id]);
// On stocke tous les résultats dans un tableau PHP appelé $projets
$projets = $stmt->fetchAll(PDO::FETCH_ASSOC);

function afficherStatut($statut) {
    if ($statut == 'en_pause') return 'En pause';
    if ($statut == 'termine') return 'Terminé';
    return 'En cours';
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Projets - Estoria</title>
    <link rel="stylesheet" href="style-projets.css">
</head>

<body>
    <div class="layout">
        
        <!-- Menu latéral gauche pour la navigation entre les pages -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="logo.png" alt="logo" class="logo">
                <span class="brand-name">Estoria</span>
            </div>
            <nav class="sidebar-nav">
                <ul class="sidebar-menu">
                    <li><a href="dashboard.php">Accueil</a></li>
                    <li><a href="projets.php" class="active">Mes Projets</a></li>
                    <li><a href="tickets.php">Mes Tickets</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="index.php" class="logout-link">Déconnexion</a>
            </div>
        </aside>

        <!-- Zone principale de la page listant les projets -->
        <main class="content">
            <div class="page-header">
                <h1>Tous les Projets</h1>
                <!-- Bouton qui ouvre la fenêtre modale (grâce au lien d'ancre HTML) -->
                <a href="#modal-new-project" class="btn-create">+ Nouveau Projet</a>
            </div>

            <div class="projects-grid-full">
                
                <!-- On boucle sur chaque projet trouvé dans la base de données pour créer une carte HTML -->
                <?php foreach($projets as $projet): ?>
                    <div class="project-card-full in-progress">
                        
                        <!-- En-tête de la carte avec le statut et la date formatée au format français (jour/mois/année) -->
                        <div class="card-top">
                            <span class="status in-progress"> <?php echo afficherStatut($projet['statut']); ?> </span>
                            <span class="date-badge">Début : <?php echo date('d/m/Y', strtotime($projet['date_debut'])); ?></span>
                        </div>
                        
                        <!-- Affichage du titre du projet -->
                        <h3><?php echo htmlspecialchars($projet['titre']); ?></h3>
                        
                        <!-- Corps de la carte avec la description et le lien vers le détail du projet -->
                        <div class="card-top">
                            <p class="desc"><?php echo htmlspecialchars($projet['description']); ?></p>
                            <!-- On fait passer l'ID du projet dans l'URL pour la page suivante -->
                            <a href="projets-detail.php?id=<?php echo $projet['id']; ?>" class="btn-details">voir plus</a>
                        </div>
                        
                        <!-- Pied de la carte avec l'affichage dynamique du nombre de tickets (calculé par la requête SQL) -->
                        <div class="meta-info">
                            <span>🎫 <?php echo $projet['nb_tickets']; ?> Ticket<?php echo ($projet['nb_tickets'] > 1) ? 's' : ''; ?></span>
                        </div>

                    </div>
                <?php endforeach; ?>

            </div>
        </main>
    </div>

    <!-- Fenêtre modale qui apparaît en superposition quand on clique sur "Nouveau Projet" -->
    <div id="modal-new-project" class="modal-overlay">
        <div class="modal-box">
            
            <div class="modal-header">
                <h3>Créer un nouveau projet</h3>
                <a href="#" class="btn-close">×</a>
            </div>
            
            <!-- Le formulaire renvoie les données vers cette même page (projets.php) via POST -->
            <form class="modal-form" method="POST" action="projets.php">
                
                <label>Nom du projet</label>
                <div class="input-group">
                    <input type="text" name="nom" class="Titre" placeholder="Ex: Refonte Site Web" required>
                    <span id="error-Titre" class="error-text titanic">Titre obligatoire</span>
                </div>
                
                <label>Description</label>
                <div class="input-group">
                    <textarea name="description" rows="3" class="DescriptionProjet" placeholder="Objectifs du projet..." required style="resize: none;"></textarea>
                    <span id="error-Description" class="error-text titanic">Description obligatoire</span>
                </div>
                
                <div class="form-row">
                    <div>
                        <label>Date de début</label>
                        <input type="date" name="date_debut" required>
                    </div>
                    <div>
                        <label>Date de fin (Deadline)</label>
                        <input type="date" name="date_fin" required>
                    </div>
                </div>

                <div class="modal-actions-right">
                    <button type="submit" class="btn-create-submit">Créer le projet</button>
                </div>
            </form>
            
        </div>
    </div>

    <!-- Petit bandeau de notification (toast) animé par Javascript en cas de succès -->
    <div id="success" class="toast titanic">
        <div class="toast-icon">✅</div>
        <div class="toast-content">
            <span class="toast-title">Succès</span>
            <span class="toast-message">Projet créé !</span>
        </div>
    </div>

    <script src="script-project.js"></script>
</body>
</html>
