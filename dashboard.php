<?php
// On démarre la session pour identifier l'utilisateur connecté
session_start();
// On se connecte à la base de données
require_once __DIR__ . '/db.php';

// Si l'utilisateur n'est pas connecté, on le renvoie à la page de connexion
if (!isset($_SESSION['client_id'])) {
    header("Location: index.php");
    exit;
}

$client_id = $_SESSION['client_id'];

// Ajout d'un collaborateur si le formulaire a été envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_collab') {
    
    // On récupère le nom et le rôle tapés dans le formulaire
    $nom_collab = $_POST['nom'];
    $role_collab = $_POST['role'];

    // On insère le nouveau collaborateur dans la base de données
    $stmt = $pdo->prepare("INSERT INTO collaborateurs (client_id, nom, role) VALUES (?, ?, ?)");
    $stmt->execute([$client_id, $nom_collab, $role_collab]);
    
    // On recharge la page pour voir le nouveau membre apparaître
    header("Location: dashboard.php?success=collab");
    exit;
}

// Récupération des informations du client connecté (nom, email)
$stmtClient = $pdo->prepare("SELECT nom, email FROM clients WHERE id = ?");
$stmtClient->execute([$client_id]);
$client_info = $stmtClient->fetch(PDO::FETCH_ASSOC);

// On stocke ses infos dans des variables et on génère ses 2 initiales
$nom_client = $client_info['nom'];
$email_client = $client_info['email'];
$initiales = strtoupper(substr($nom_client, 0, 2));

// On compte le nombre total de projets de ce client
$stmtProjets = $pdo->prepare("SELECT COUNT(*) FROM projets WHERE client_id = ?");
$stmtProjets->execute([$client_id]);
$nb_projets_actifs = $stmtProjets->fetchColumn();

// On compte le nombre de tickets qui ne sont pas encore terminés
$stmtTickets = $pdo->prepare("SELECT COUNT(*) FROM tickets t JOIN projets p ON t.projet_id = p.id WHERE p.client_id = ? AND t.statut != 'termine'");
$stmtTickets->execute([$client_id]);
$nb_tickets_ouverts = $stmtTickets->fetchColumn();

// On additionne tout le temps passé sur les tickets de ce client
$stmtHeures = $pdo->prepare("SELECT SUM(t.temps_passe) FROM tickets t JOIN projets p ON t.projet_id = p.id WHERE p.client_id = ?");
$stmtHeures->execute([$client_id]);
$total_heures = $stmtHeures->fetchColumn();

// On récupère les 2 derniers projets avec leurs statistiques (tickets ouverts et temps total)
$sqlProjetsRecents = "
    SELECT p.*, 
           (SELECT COUNT(*) FROM tickets t WHERE t.projet_id = p.id AND t.statut != 'termine') AS tickets_ouverts,
           (SELECT SUM(temps_passe) FROM tickets t WHERE t.projet_id = p.id) AS total_temps
    FROM projets p 
    WHERE p.client_id = ? 
    ORDER BY p.id DESC 
    LIMIT 2";

$stmtRecents = $pdo->prepare($sqlProjetsRecents);
$stmtRecents->execute([$client_id]);
$projets_recents = $stmtRecents->fetchAll(PDO::FETCH_ASSOC);

// On récupère la liste complète des collaborateurs (l'équipe du client)
$stmtCollabs = $pdo->prepare("SELECT * FROM collaborateurs WHERE client_id = ? ORDER BY id DESC");
$stmtCollabs->execute([$client_id]);
$collaborateurs = $stmtCollabs->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Dashboard - Estoria</title>
    <link rel="stylesheet" href="styles-dashboard.css">
</head>

<body>
    <div class="layout">
        
        <!-- Menu latéral -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="logo.png" alt="logo" class="logo">
                <span class="brand-name">Estoria</span>
            </div>
            
            <nav class="sidebar-nav">
                <ul class="sidebar-menu">
                    <li><a href="dashboard.php" class="active">Accueil</a></li>
                    <li><a href="projets.php">Mes Projets</a></li>
                    <li><a href="tickets.php">Mes Tickets</a></li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="settings.php" class="settings-link">⚙️ Paramètres</a>
                <a href="index.php" class="logout-link">Déconnexion</a>
            </div>
        </aside>

        <!-- Zone d'affichage principale -->
        <main class="content">
            <h1>Vue d'ensemble</h1>

            <!-- Section 1 : Chiffres clés (Stats) -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Projets Actifs</h3>
                    <p class="stat-number"><?php echo $nb_projets_actifs; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Tickets Ouverts</h3>
                    <p class="stat-number"><?php echo $nb_tickets_ouverts; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Heures ce mois</h3>
                    <p class="stat-number"><?php echo $total_heures; ?>h</p>
                </div>
            </div>

            <!-- Section 2 : Les projets récents -->
            <h2 class="section-title">Projets Récents</h2>
            <div class="dashboard-widgets">
                
                <!-- Boucle pour afficher les 2 derniers projets -->
                <?php foreach($projets_recents as $projet): 
                    $temps_projet = $projet['total_temps'];
                ?>
                <div class="widget project-widget">
                    <div class="widget-header">
                        <h3><?php echo htmlspecialchars($projet['titre']); ?></h3>
                        <span class="status in-progress"> <?php echo afficherStatut($projet['statut']);  ?> </span>
                    </div>
                    <p class="project-desc"><?php echo htmlspecialchars($projet['description']); ?></p>
                    <div class="project-details">
                        <div class="detail-item"><span>Tickets:</span> <strong><?php echo $projet['tickets_ouverts']; ?> ouverts</strong></div>
                        <div class="detail-item"><span>Deadline:</span> <strong><?php echo date('d/m/Y', strtotime($projet['date_fin'])); ?></strong></div>
                    </div>
                    <div class="progress-container" style="background: transparent; text-align:center; height:auto; border-radius:0;">
                        <span style="font-size: 1.1rem; color: #00ff88; font-weight:bold;"><?php echo $temps_projet; ?> heures investies</span>
                    </div>
                    <span class="progress-text">Temps total sur ce projet</span>
                </div>
                <?php endforeach; ?>

                <?php if(empty($projets_recents)): ?>
                    <p style="color: var(--muted);">Aucun projet récent trouvé.</p>
                <?php endif; ?>

            </div>

            <!-- Section 3 : L'équipe et le profil -->
            <div class="dashboard-widgets">
                
                <!-- Bloc de l'équipe (Collaborateurs) -->
                <div class="widget">
                    <div class="widget-header">
                        <h3>Collaborateurs</h3>
                    </div>
                    <div class="team-list">
                        
                        <!-- Boucle d'affichage de chaque membre de l'équipe -->
                        <?php foreach($collaborateurs as $collab): 
                            // On découpe le nom complet pour prendre la première lettre du prénom et la première du nom
                            $mots = explode(' ', trim($collab['nom']));
                            $initiales_collab = strtoupper(substr($mots[0], 0, 1));
                            if (isset($mots[1])) {
                                $initiales_collab .= strtoupper(substr($mots[1], 0, 1));
                            }
                            
                            // On attribue une couleur dynamique à l'avatar en fonction de la taille du nom
                            $couleurs = ['#3498db', '#e74c3c', '#2ecc71', '#f1c40f', '#9b59b6'];
                            $couleur_avatar = $couleurs[strlen($collab['nom']) % 5];
                        ?>
                        <div class="team-member">
                            <div class="avatar" style="background: <?php echo $couleur_avatar; ?>; color: white; display:flex; justify-content:center; align-items:center; width:40px; height:40px; border-radius:50%; font-weight:bold;">
                                <?php echo htmlspecialchars($initiales_collab); ?>
                            </div>
                            <div class="member-info">
                                <span class="name"><?php echo htmlspecialchars($collab['nom']); ?></span>
                                <span class="role"><?php echo htmlspecialchars($collab['role']); ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <?php if(empty($collaborateurs)): ?>
                            <p style="color: var(--muted); font-size: 0.9rem; font-style: italic;">Aucun membre dans l'équipe.</p>
                        <?php endif; ?>

                    </div>
                    <!-- Bouton pour ouvrir la modale d'ajout de membre -->
                    <a href="#modal-add-member" class="btn-add-collab" style="display:block; text-align:center; text-decoration:none; box-sizing:border-box;">
                        + Ajouter un membre
                    </a>
                </div>

                <!-- Bloc du profil de l'utilisateur connecté -->
                <div class="widget">
                    <div class="widget-header">
                        <h3>Mon Profil</h3>
                        <a href="settings.php" class="link-small">Modifier</a>
                    </div>
                    <div class="profile-summary">
                        <div class="profile-avatar-large"><?php echo htmlspecialchars($initiales); ?></div>
                        <div class="profile-details">
                            <span class="p-name"><?php echo htmlspecialchars($nom_client); ?></span>
                            <span class="p-role">Client</span>
                            <p class="p-bio">Bienvenue sur votre espace Estoria.</p>
                            <span class="p-email"><?php echo htmlspecialchars($email_client); ?></span>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Modale d'ajout de collaborateur (cachée par défaut) -->
    <div id="modal-add-member" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Ajouter un collaborateur</h3>
            </div>
            
            <form id="form-collab" class="modal-form" method="POST" action="dashboard.php">
                <input type="hidden" name="action" value="add_collab">
                
                <label>Nom du membre</label>
                <div class="input-group">
                    <input type="text" name="nom" class="Name" id="input-collab-name" placeholder="Ex: Jean Dupont">
                    <span id="error-Name" class="error-text titanic">Nom invalide</span>
                </div>
                
                <label>Rôle</label>
                <select name="role">
                    <option class="select" value="Développeur">Développeur</option>
                    <option class="select" value="Designer">Designer</option>
                    <option class="select" value="Chef de projet">Chef de projet</option>
                </select>
                
                <div class="modal-actions">
                    <!-- Le bouton annuler renvoie en haut de la page pour fermer la modale -->
                    <button type="button" class="btn-cancel" onclick="window.location.href='#'">Annuler</button>
                    <button type="submit" class="btn-confirm">Inviter</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Notification de succès en cas d'ajout de membre -->
    <div id="success" class="toast titanic">
        <div class="toast-icon">✅</div>
        <div class="toast-content">
            <span class="toast-title">Succès</span>
            <span class="toast-message">Collaborateur ajouté !</span>
        </div>
    </div>
    
    <script src="script-dashboard.js"></script>
</body>
</html>
