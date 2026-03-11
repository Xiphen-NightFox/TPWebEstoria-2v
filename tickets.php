<?php
// On démarre la session
session_start();
// On inclut la connexion à la base de données
require_once __DIR__ . '/db.php';

// Si l'utilisateur n'est pas connecté, retour à l'accueil
if (!isset($_SESSION['client_id'])) {
    header("Location: index.php");
    exit;
}

$client_id = $_SESSION['client_id'];

// --- CRÉATION D'UN NOUVEAU TICKET ---
// Si le formulaire de la fenêtre modale a été envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // On récupère toutes les informations saisies
    $titre = $_POST['titre'];
    $projet_id = $_POST['projet_id'];
    $type = $_POST['type']; 
    $priorite = $_POST['priorite'];
    $temps_estime = $_POST['temps_estime'];
    $description = $_POST['description'];

    // On transforme le type de facturation (texte) en un chiffre (1 ou 0) pour la base de données
    $est_facturable = ($type === 'billable') ? 1 : 0;

    // On insère le nouveau ticket dans la base de données
    $stmt = $pdo->prepare("INSERT INTO tickets (projet_id, titre, description, priorite, est_facturable, temps_estime) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$projet_id, $titre, $description, $priorite, $est_facturable, $temps_estime]);
    
    // On recharge la page pour voir le ticket apparaître dans le tableau
    header("Location: tickets.php?success=1");
    exit;
}

// --- RÉCUPÉRATION DES DONNÉES ---
// On récupère la liste des projets de ce client (pour les afficher dans le menu déroulant du formulaire de création)
$stmtProjets = $pdo->prepare("SELECT id, titre FROM projets WHERE client_id = ?");
$stmtProjets->execute([$client_id]);
$projets = $stmtProjets->fetchAll(PDO::FETCH_ASSOC);

// On récupère tous les tickets liés aux projets de ce client, triés du plus récent au plus ancien
$sql = "SELECT t.*, p.titre AS nom_projet 
        FROM tickets t 
        JOIN projets p ON t.projet_id = p.id 
        WHERE p.client_id = ? 
        ORDER BY t.id DESC";
$stmtTickets = $pdo->prepare($sql);
$stmtTickets->execute([$client_id]);
$tickets = $stmtTickets->fetchAll(PDO::FETCH_ASSOC);

// --- FONCTIONS DE FORMATAGE CSS ---
// Cette fonction transforme la priorité (texte en français) en une classe CSS (en anglais)
function getClassPrio($priorite) {
    if ($priorite == 'Haute') return 'high';
    if ($priorite == 'Faible') return 'low';
    return 'Normal';
}

// Cette fonction transforme le statut de la base de données en une classe CSS et un texte lisible
function formatStatut($statut) {
    if ($statut == 'en_cours') return ['class' => 'in-progress', 'texte' => 'En cours'];
    if ($statut == 'termine') return ['class' => 'done', 'texte' => 'Terminé'];
    if ($statut == 'attente') return ['class' => 'to-validate', 'texte' => 'En attente'];
    return ['class' => 'new', 'texte' => 'Nouveau']; 
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Tickets - Estoria</title>
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
                    <li><a href="projets.php">Mes Projets</a></li>
                    <li><a href="tickets.php" class="active">Mes Tickets</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="index.php" class="logout-link">Déconnexion</a>
            </div>
        </aside>

        <!-- Contenu principal de la page -->
        <main class="content">
            <div class="page-header">
                <div>
                    <h1>Gestion des Tickets</h1>
                    <p class="subtitle">Suivi des demandes et maintenance</p>
                </div>
                
                <!-- On n'affiche le bouton "Nouveau Ticket" que si le client a au moins un projet -->
                <?php if(count($projets) > 0): ?>
                    <a href="#modal-new-ticket" class="btn-create">+ Nouveau Ticket</a>
                <?php else: ?>
                    <a href="projets.php" class="btn-create" style="background: grey;">Créez d'abord un projet</a>
                <?php endif; ?>
            </div>

            <!-- Barre de filtres (gérée en Javascript) -->
            <div class="filters-bar">
                <button class="btn-filter active" onclick="filterTickets('all')">Tous</button>
                <button class="btn-filter" onclick="filterTickets('included')">Inclus</button>
                <button class="btn-filter" onclick="filterTickets('billable')">Facturables</button>
            </div>

            <!-- Tableau listant tous les tickets -->
            <div class="table-wrapper">
                <table class="tickets-table">
                    <thead>
                        <tr>
                            <th>Sujet</th>
                            <th>Projet</th>
                            <th>Type</th>
                            <th>Priorité</th>
                            <th>Statut</th>
                            <th>Temps passé</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <!-- On boucle sur chaque ticket trouvé dans la base de données -->
                        <?php foreach($tickets as $ticket): 
                            // On prépare les couleurs via nos fonctions PHP
                            $statutF = formatStatut($ticket['statut']);
                            $prioClass = getClassPrio($ticket['priorite']);
                        ?>
                        <tr>
                            <td class="subject-col">
                                <!-- Lien pour accéder au détail de ce ticket -->
                                <a href="ticket-detail.php?id=<?php echo $ticket['id']; ?>">
                                    Ticket #<?php echo $ticket['id']; ?> : <?php echo $ticket['titre']; ?>
                                </a>
                            </td>
                            <td><?php echo $ticket['nom_projet']; ?></td>
                            <td>
                                <!-- Affichage du type de ticket (Facturable ou Inclus) -->
                                <?php if($ticket['est_facturable'] == 1): ?>
                                    <span class="badge-type billable">Facturable</span>
                                <?php else: ?>
                                    <span class="badge-type included">Inclus</span>
                                <?php endif; ?>
                            </td>
                                                                                    <!-- maj pour la premiere lettre-->
                            <td><span class="badge-prio <?php echo $prioClass; ?>"><?php echo ucfirst($ticket['priorite']); ?></span></td>
                            <td><span class="status-dot <?php echo $statutF['class']; ?>"><?php echo $statutF['texte']; ?></span></td>
                            <td><?php echo $ticket['temps_passe']; ?> h</td>
                            <td style="color: grey; font-size: 0.85rem;"><?php echo date('d/m/Y', strtotime($ticket['cree_le'])); ?></td>
                        </tr>
                        <?php endforeach; ?>

                        <!-- S'il n'y a aucun ticket à afficher -->
                        <?php if(empty($tickets)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 30px; color: grey;">Aucun ticket pour le moment.</td>
                        </tr>
                        <?php endif; ?>
                        
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Fenêtre modale de création d'un nouveau ticket -->
    <div id="modal-new-ticket" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Créer un ticket</h3>
                <a href="#" class="btn-close">×</a>
            </div>
            
            <!-- Le formulaire renvoie les infos sur cette même page avec la méthode POST -->
            <form class="modal-form" method="POST" action="tickets.php">
                <label>Titre du ticket *</label>
                <div class="input-group">
                    <input type="text" name="titre" class="Titre" placeholder="Titre du ticket..." required>
                    <span id="error-Titre" class="error-text titanic">Titre obligatoire</span>
                </div>
                
                <div class="form-row">
                    <div>
                        <label>Projet concerné *</label>
                        <?php if(empty($projets)): ?>
                            <p style="color: red; font-size: 0.9rem;">Erreur : Vous devez créer un projet d'abord.</p>
                        <?php else: ?>
                            <!-- On liste dynamiquement les projets du client -->
                            <select name="projet_id" required>
                                <?php foreach($projets as $proj): ?>
                                    <option value="<?php echo $proj['id']; ?>"><?php echo $proj['titre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label>Type *</label>
                        <select name="type" required>
                            <option value="included">Inclus au contrat</option>
                            <option value="billable">Facturable</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <label>Priorité</label>
                        <select name="priorite">
                            <option value="faible">Faible</option>
                            <option value="normale" selected>Normale</option>
                            <option value="haute">Haute</option>
                        </select>
                    </div>
                    <div>
                        <label>Temps estimé (h)</label>
                        <div class="input-group">
                            <input type="number" name="temps_estime" step="0.5" placeholder="0.5" class="TempsEstime" required>
                            <span id="error-Temps" class="error-text titanic">Temps obligatoire</span>
                        </div>
                    </div>
                </div>

                <label>Description détaillée *</label>
                <div class="input-group">
                    <textarea name="description" rows="5" class="DescriptionTicket" placeholder="Description..." required></textarea>
                    <span id="error-Description" class="error-text titanic">Description obligatoire</span>
                </div>

                <div class="modal-actions-right">
                    <button type="submit" class="btn-create-submit">Enregistrer</button>
                </div>
            </form>
            
        </div>
    </div>
    
    <!-- Notification animée de succès -->
    <div id="success" class="toast titanic">
        <div class="toast-icon">✅</div>
        <div class="toast-content">
            <span class="toast-title">Succès</span>
            <span class="toast-message">Ticket créé !</span>
        </div>
    </div>

<script src="script-tickets.js"></script>
</body>
</html>
