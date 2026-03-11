<?php
// On démarre la session pour identifier le client connecté
session_start();
// Connexion à la base de données
require 'db.php';


// On s'assure que le client est bien connecté
if (!isset($_SESSION['client_id'])) {
    header("Location: tickets.php");
    exit;
}


$client_id = $_SESSION['client_id'];
$ticket_id = $_GET['id'];


// --- ACTION 1 : SUPPRESSION DU TICKET ---
// Si l'utilisateur a cliqué sur le bouton de suppression (qui ajoute ?action=delete dans l'URL)
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    
        // On supprime définitivement le ticket
        $delStmt = $pdo->prepare("DELETE FROM tickets WHERE id = ?");
        $delStmt->execute([$ticket_id]);
        
        // On redirige l'utilisateur vers la liste des tickets
        header("Location: tickets.php?success_delete=1");
        exit;
}


// --- ACTION 2 : AJOUT DE TEMPS SUR LE TICKET ---
// Si le formulaire d'ajout de temps a été envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add_time') {
    
    // On récupère le temps saisi par l'utilisateur
    $temps_ajoute = $_POST['temps_ajoute'];
    
    // On récupère le temps qui était déjà enregistré sur ce ticket dans la base de données
    $tStmt = $pdo->prepare("SELECT temps_passe FROM tickets WHERE id = ?");
    $tStmt->execute([$ticket_id]);
    $temps_actuel = $tStmt->fetchColumn();


    // On additionne l'ancien temps avec le nouveau temps
    $nouveau_temps = $temps_actuel + $temps_ajoute;
    
    // On met à jour la base de données avec ce nouveau total
    $updateStmt = $pdo->prepare("UPDATE tickets SET temps_passe = ? WHERE id = ?");
    $updateStmt->execute([$nouveau_temps, $ticket_id]);
    
    // On recharge la page pour voir le nouveau temps
    header("Location: ticket-detail.php?id=" . $ticket_id . "&success=time");
    exit;
}


// --- ACTION 3 : MODIFICATION DES INFOS DU TICKET ---
// Si le formulaire de modification a été envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit_ticket') {
    
    // On récupère toutes les nouvelles informations tapées par l'utilisateur
    $titre_edit = $_POST['titre'];
    $description_edit = $_POST['description'];
    $statut_edit = $_POST['statut'];
    $priorite_edit = $_POST['priorite'];
    
    // On met à jour le ticket dans la base de données
    $updateStmt = $pdo->prepare("UPDATE tickets SET titre = ?, description = ?, statut = ?, priorite = ? WHERE id = ?");
    $updateStmt->execute([$titre_edit, $description_edit, $statut_edit, $priorite_edit, $ticket_id]);
    
    // On recharge la page
    header("Location: ticket-detail.php?id=" . $ticket_id . "&success=edit");
    exit;
}


// --- AFFICHAGE DE LA PAGE : RÉCUPÉRATION DES DONNÉES ---
// On récupère toutes les informations du ticket, plus le nom du projet lié et sa deadline
$sql = "SELECT t.*, p.titre AS nom_projet, p.date_fin AS deadline 
        FROM tickets t 
        JOIN projets p ON t.projet_id = p.id 
        WHERE t.id = ? AND p.client_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$ticket_id, $client_id]);
// On stocke le résultat dans le tableau $ticket
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);


// Gestion dynamique des couleurs en CSS selon le statut et la priorité
$statutClasses = ['nouveau' => 'new', 'en_cours' => 'in-progress', 'attente' => 'to-validate', 'termine' => 'done'];
$statutClass = $statutClasses[$ticket['statut']];


$prioClasses = ['faible' => 'low', 'normale' => 'normal', 'haute' => 'high'];
$prioClass = $prioClasses[$ticket['priorite']];
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail Ticket #T-<?php echo htmlspecialchars($ticket['id']); ?> - Estoria</title>
    <link rel="stylesheet" href="styles-tickets.css">
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
                    <li><a href="dashboard.php">Accueil</a></li>
                    <li><a href="projets.php">Mes Projets</a></li>
                    <li><a href="tickets.php" class="active">Mes Tickets</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php" class="logout-link">Déconnexion</a>
            </div>
        </aside>


        <!-- Contenu principal -->
        <main class="content">
            
            <!-- En-tête de la page -->
            <div class="ticket-header-top">
                <div class="header-left">
                    <a href="tickets.php" class="back-link">← Retour</a>
                    <span class="separator-pipe">|</span>
                    <span class="ticket-id">#T-<?php echo htmlspecialchars($ticket['id']); ?></span>
                    <h1 class="ticket-title"><?php echo htmlspecialchars($ticket['titre']); ?></h1>
                </div>
                <div class="header-actions">
                    <!-- Bouton pour modifier le ticket (ouvre la modale en CSS) -->
                    <a href="#modal-edit-ticket" class="btn-action" style="text-decoration:none;">✏️ Modifier</a>
                    
                    <!-- Bouton pour supprimer le ticket (ajoute action=delete dans l'URL) -->
                    <a href="ticket-detail.php?id=<?php echo htmlspecialchars($ticket['id']); ?>&action=delete" 
                       class="btn-action" 
                       style="background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; text-decoration:none;"
                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce ticket ?');">
                       🗑️ Supprimer
                    </a>
                </div>
            </div>


            <!-- Grille d'informations -->
            <div class="ticket-grid-layout">
                
                <!-- Zone gauche : Description -->
                <div class="main-column">
                    <div class="content-card">
                        <div class="card-header">
                            <h3>📄 Description</h3>
                        </div>
                        <div class="card-body">
                            <!-- prendre en compte les retours à la lignes -->
                            <p><?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>
                        </div>
                    </div>
                </div>


                <!-- Zone droite : Sidebar d'infos clés -->
                <div class="side-column">
                    
                    <!-- Bloc 1 : Statut et Type -->
                    <div class="side-panel">
                        <div class="side-row">
                            <label>Statut</label>
                            <span class="status-dot <?php echo htmlspecialchars($statutClass); ?>" style="padding: 5px 10px; border-radius: 12px; font-size: 0.85rem;">
                                <!-- On met une majuscule et on enlève les tirets du bas -->
                                <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $ticket['statut']))); ?>
                            </span>
                        </div>
                        <div class="side-row">
                            <label>Priorité</label>
                            <span class="badge-prio <?php echo htmlspecialchars($prioClass); ?>">
                                <!-- On met une majuscule -->
                                <?php echo htmlspecialchars(ucfirst($ticket['priorite'])); ?>
                            </span>
                        </div>
                        <div class="side-row">
                            <label>Type</label>
                            <?php if($ticket['est_facturable'] == 1): ?>
                                <span class="badge-type billable">💰 Facturable</span>
                            <?php else: ?>
                                <span class="badge-type included">📦 Inclus</span>
                            <?php endif; ?>
                        </div>
                    </div>


                    <!-- Bloc 2 : Liens avec le projet -->
                    <div class="side-panel">
                        <h4 class="panel-title">Infos Générales</h4>
                        <div class="detail-list">
                            <div class="detail-item">
                                <span class="dt-label">Projet</span>
                                <span class="dt-val"><?php echo htmlspecialchars($ticket['nom_projet']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="dt-label">Créé le</span>
                                <span class="dt-val"><?php echo date('d/m/Y', strtotime($ticket['cree_le'])); ?></span>
                            </div>
                        </div>
                    </div>


                    <!-- Bloc 3 : Gestion du temps -->
                    <div class="side-panel">
                        <h4 class="panel-title">Suivi Temps</h4>
                        <div class="time-widget">
                            <div class="time-header">
                                <span><?php echo htmlspecialchars($ticket['temps_passe']); ?>h</span>
                                <span class="time-total"> / <?php echo htmlspecialchars($ticket['temps_estime']); ?>h</span>
                            </div>
                            <a href="#modal-log-time" class="btn-log-time" style="display: block; box-sizing: border-box; text-decoration:none;">+ Saisir temps</a>
                        </div>
                    </div>


                </div>
            </div>
        </main>
    </div>


    <!-- Fenêtre modale : Formulaire de saisie de temps -->
    <div id="modal-log-time" class="modal-overlay">
        <div class="modal-box" style="max-width: 400px;">
            <div class="modal-header">
                <h3>Saisir du temps</h3>
                <a href="#" class="btn-close">×</a>
            </div>
            
            <form id="form-time" class="modal-form" method="POST" action="ticket-detail.php?id=<?php echo htmlspecialchars($ticket_id); ?>">
                <input type="hidden" name="action" value="add_time">
                <label>Temps passé (en heures) *</label>
                <div class="input-group">
                    <input type="number" name="temps_ajoute" class="TempsAjoute" step="0.25" placeholder="Ex: 1.5" required>
                    <span id="error-TempsAjoute" class="error-text titanic">Veuillez entrer un temps valide.</span>
                </div>
                <div class="modal-actions-right">
                    <button type="submit" class="btn-create-submit">Ajouter</button>
                </div>
            </form>
            
        </div>
    </div>


    <!-- Fenêtre modale : Formulaire de modification du ticket -->
    <div id="modal-edit-ticket" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Modifier le ticket</h3>
                <a href="#" class="btn-close">×</a>
            </div>
            
            <form id="form-edit" class="modal-form" method="POST" action="ticket-detail.php?id=<?php echo htmlspecialchars($ticket_id); ?>">
                <input type="hidden" name="action" value="edit_ticket">
                
                <label>Titre du ticket *</label>
                <div class="input-group">
                    <input type="text" name="titre" class="TitreEdit" value="<?php echo htmlspecialchars($ticket['titre']); ?>" required>
                    <span id="error-TitreEdit" class="error-text titanic">Titre obligatoire</span>
                </div>
                
                <div class="form-row">
                    <div>
                        <label>Statut</label>
                        <select name="statut">
                            <option value="nouveau" <?php if($ticket['statut'] == 'nouveau') echo 'selected'; ?>>Nouveau</option>
                            <option value="en_cours" <?php if($ticket['statut'] == 'en_cours') echo 'selected'; ?>>En cours</option>
                            <option value="attente" <?php if($ticket['statut'] == 'attente') echo 'selected'; ?>>En attente</option>
                            <option value="termine" <?php if($ticket['statut'] == 'termine') echo 'selected'; ?>>Terminé</option>
                        </select>
                    </div>
                    <div>
                        <label>Priorité</label>
                        <select name="priorite">
                            <option value="faible" <?php if($ticket['priorite'] == 'faible') echo 'selected'; ?>>Faible</option>
                            <option value="normale" <?php if($ticket['priorite'] == 'normale') echo 'selected'; ?>>Normale</option>
                            <option value="haute"<?php if($ticket['priorite'] == 'haute') echo 'selected'; ?>>Haute</option>
                        </select>
                    </div>
                </div>
                
                <!-- Champ Description  -->
                <label>Description *</label>
                <div class="input-group">
                    <textarea name="description" class="DescEdit" rows="6" required><?php echo htmlspecialchars($ticket['description']); ?></textarea>
                    <span id="error-DescEdit" class="error-text titanic">Veuillez entrer une description.</span>
                </div>
                
                <!-- Bouton de validation -->
                <div class="modal-actions-right">
                    <button type="submit" class="btn-create-submit">Enregistrer les modifications</button>
                </div>
            </form>
            
        </div>
    </div>


<script src="script-tickets.js"></script> 
</body>
</html>
