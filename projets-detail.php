<?php
// On démarre la session pour identifier l'utilisateur
session_start();
// On inclut la connexion à la base de données
require_once __DIR__ . '/db.php'; 

// Si aucun client n'est connecté, on le renvoie à la page de connexion
if (!isset($_SESSION['client_id'])) {
    header("Location: index.php");
    exit;
}

// L'ID du projet est passé dans l'URL (ex: projets-detail.php?id=3). On le récupère ici.
$projet_id = $_GET['id'];
$client_id = $_SESSION['client_id'];

// On vérifie si un formulaire de cette page a été envoyé (modification ou archivage)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Cas 1 : L'utilisateur a cliqué sur "Enregistrer les modifications"
    if (isset($_POST['action']) && $_POST['action'] === 'modifier') {
        
        // On récupère les nouvelles valeurs saisies dans le formulaire de modification
        $titre = $_POST['titre'];
        $description = $_POST['description'];
        $date_debut = $_POST['date_debut'];
        $date_fin = $_POST['date_fin'];
        $statut = $_POST['statut'];

        // On met à jour le projet correspondant dans la base de données
        $stmt = $pdo->prepare("UPDATE projets SET titre = ?, description = ?, date_debut = ?, date_fin = ?, statut = ? WHERE id = ? AND client_id = ?");
        $stmt->execute([$titre, $description, $date_debut, $date_fin, $statut, $projet_id, $client_id]);
        
        // On recharge la page pour voir les modifications
        header("Location: projets-detail.php?id=" . $projet_id . "&success=1");
        exit;
    }
    
    // Cas 2 : L'utilisateur a cliqué sur le bouton "Archiver"
    if (isset($_POST['action']) && $_POST['action'] === 'archiver') {
        
        // On supprime définitivement le projet de la base de données
        $stmt = $pdo->prepare("DELETE FROM projets WHERE id = ? AND client_id = ?");
        if ($stmt->execute([$projet_id, $client_id])) {
            // Une fois supprimé, on redirige l'utilisateur vers la liste de ses projets
            header("Location: projets.php"); 
            exit;
        }
    }
}

// Au chargement de la page, on récupère toutes les informations du projet actuel
$stmt = $pdo->prepare("SELECT * FROM projets WHERE id = ? AND client_id = ?");
$stmt->execute([$projet_id, $client_id]);
// On stocke toutes les infos du projet (titre, description, dates...) dans la variable $projet
$projet = $stmt->fetch(PDO::FETCH_ASSOC);

//temps passé sur le projet
$stmtHeures = $pdo->prepare("SELECT SUM(temps_passe) FROM tickets WHERE projet_id = ?");
$stmtHeures->execute([$projet_id]);
// On récupère la somme. S'il n'y a aucun ticket
$heures_totales = $stmtHeures->fetchColumn() ?: 0; 

// Petite fonction maison pour traduire le nom du statut ("en_cours" -> "En cours")
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
    <!-- On affiche dynamiquement le titre du projet dans l'onglet du navigateur -->
    <title>Détail <?php echo htmlspecialchars($projet['titre']); ?> - Estoria</title>
    <link rel="stylesheet" href="styles-tickets.css">
</head>

<body>
    <div class="layout">
        
        <!-- Menu de navigation latéral -->
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

        <!-- Zone d'affichage du projet -->
        <main class="content">
            
            <div class="ticket-header-top">
                <div class="header-left">
                    <a href="projets.php" class="back-link">Retour</a>
                    <span class="separator-pipe">|</span>
                    <!-- On affiche l'ID du projet -->
                    <span class="ticket-id">#P-<?php echo $projet['id']; ?></span>
                    <!-- On affiche le titre du projet -->
                    <h1 class="ticket-title"><?php echo htmlspecialchars($projet['titre']); ?></h1>
                </div>
                <div class="header-actions">
                    <!-- Bouton qui ouvre la fenêtre de modification -->
                    <a href="#modal-edit-project" class="btn-action" style="text-decoration: none;">✏️ Modifier</a>
                    
                    <!-- Bouton qui envoie la requête d'archivage (avec une confirmation JS) -->
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir archiver/supprimer ce projet ?');">
                        <input type="hidden" name="action" value="archiver">
                        <button type="submit" class="btn-action btn-close-ticket">Archiver</button>
                    </form>
                </div>
            </div>

            <div class="ticket-grid-layout">
                
                <!-- Zone gauche : Contenu principal (Description) -->
                <div class="main-column">
                    <div class="content-card">
                        <div class="card-header">
                            <h3>📄 Description du projet</h3>
                        </div>
                        <div class="card-body">
                            <!-- La fonction nl2br() permet de conserver les sauts de ligne tapés par l'utilisateur -->
                            <p><?php echo nl2br(htmlspecialchars($projet['description'])); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Zone droite : Informations clés -->
                <div class="side-column">
                    
                    <!-- Affichage du statut -->
                    <div class="side-panel">
                        <div class="side-row">
                            <label>Statut</label>
                            <!-- La couleur du badge change si le statut est "en_cours" -->
                            <span class="status-select <?php echo htmlspecialchars($projet['statut']); ?>">
                                <?php echo afficherStatut($projet['statut']); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Affichage des dates et du temps -->
                    <div class="side-panel">
                        <h4 class="panel-title">Planning & Temps</h4>
                        <div class="detail-list">
                            <div class="detail-item">
                                <span class="dt-label">Début</span>
                                <!-- On formate la date au format français (jour/mois/année) -->
                                <span class="dt-val"><?php echo date('d/m/Y', strtotime($projet['date_debut'])); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="dt-label">Fin prévue</span>
                                <span class="dt-val warning"><?php echo date('d/m/Y', strtotime($projet['date_fin'])); ?></span>
                            </div>
                            
                            <div class="detail-item" style="margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.1);">
                                <span class="dt-label">Temps total passé</span>
                                <span class="dt-val" style="color: #00ff88; font-weight: bold;"><?php echo $heures_totales; ?> h</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <!-- Fenêtre modale cachée : Formulaire de modification du projet -->
    <div id="modal-edit-project" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Modifier le projet</h3>
                <a href="#" class="btn-close">×</a>
            </div>
            
            <!-- Le formulaire renvoie les données sur cette même page avec la méthode POST -->
            <form class="modal-form" method="POST" action="">
                <!-- Ce champ caché indique à PHP qu'on veut faire une modification (voir ligne 21) -->
                <input type="hidden" name="action" value="modifier">
                
                <label>Nom du projet</label>
                <div class="input-group">
                    <!-- On pré-remplit le champ avec le titre actuel du projet -->
                    <input type="text" name="titre" class="TitreEdit" value="<?php echo htmlspecialchars($projet['titre']); ?>" required>
                    <span id="error-TitreEdit" class="error-text titanic">Le titre doit contenir au moins 2 caractères</span>
                </div>
                
                <label>Statut</label>
                <div class="input-group">
                    <!-- Le menu déroulant sélectionne automatiquement le statut actuel du projet -->
                    <select name="statut">
                        <option value="en_cours" <?php if($projet['statut'] == 'en_cours') echo 'selected'; ?>>En cours</option>
                        <option value="en_pause" <?php if($projet['statut'] == 'en_pause') echo 'selected'; ?>>En pause</option>
                        <option value="termine" <?php if($projet['statut'] == 'termine') echo 'selected'; ?>>Terminé</option>
                    </select>
                </div>

                <label>Description</label>
                <div class="input-group">
                    <!-- On pré-remplit la description -->
                    <textarea name="description" rows="4" class="DescriptionEdit" required><?php echo htmlspecialchars($projet['description']); ?></textarea>
                    <span id="error-DescEdit" class="error-text titanic">La description doit contenir au moins 5 caractères</span>
                </div>
                
                <div class="form-row">
                    <div>
                        <label>Date de début</label>
                        <input type="date" name="date_debut" value="<?php echo $projet['date_debut']; ?>" required>
                    </div>
                    <div>
                        <label>Date de fin</label>
                        <input type="date" name="date_fin" value="<?php echo $projet['date_fin']; ?>" required>
                    </div>
                </div>

                <div class="modal-actions-right">
                    <button type="submit" class="btn-create-submit">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Notification de succès en bas d'écran -->
    <div id="success" class="toast titanic">
        <div class="toast-icon">✅</div>
        <div class="toast-content">
            <span class="toast-title">Succès</span>
            <span class="toast-message">Projet mis à jour !</span>
        </div>
    </div>

    <script src="script-projets-detail.js"></script>
</body>
</html>
