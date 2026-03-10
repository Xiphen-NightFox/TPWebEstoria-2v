<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - Estoria</title>
    <!-- On utilise styles-dashboard.css car c'est la base -->
    <link rel="stylesheet" href="styles-dashboard.css">
    <!-- + un petit CSS spécifique pour cette page si besoin, voir plus bas -->
</head>

<body>
    <div class="layout">
        
        <!-- SIDEBAR (Standard) -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="logo.png" alt="logo" class="logo">
                <span class="brand-name">Estoria</span>
            </div>
            
            <nav class="sidebar-nav">
                <ul class="sidebar-menu">
                    <li><a href="dashboard.php">Accueil</a></li>
                    <li><a href="projets.php">Mes Projets</a></li>
                    <li><a href="tickets.php">Mes Tickets</a></li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="settings.php" class="settings-link active">⚙️ Paramètres</a>
                <a href="index.php" class="logout-link">Déconnexion</a>
            </div>
        </aside>

        <!-- CONTENU PRINCIPAL -->
        <main class="content">
            <h1>Paramètres</h1>

            <div class="settings-container">
                
                <!-- SECTION 1 : MODIFIER MON PROFIL -->
                <div class="settings-section">
                    <div class="section-header">
                        <h2>Mon Profil</h2>
                        <p class="section-desc">Gérez vos informations personnelles visibles par l'équipe.</p>
                    </div>
                    
                    <form class="profile-form">
                        <!-- Avatar -->
                        <div class="avatar-upload-section">
                            <div class="current-avatar-large">Moi</div>
                            <div class="upload-controls">
                                <label for="avatar-input" class="btn-upload">Changer la photo</label>
                                <input type="file" id="avatar-input" hidden>
                                <span class="file-info">JPG, PNG max 2Mo</span>
                            </div>
                        </div>

                        <!-- Champs Texte -->
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nom complet</label>
                                <div class="input-group">
                                    <input type="text" class="Nom" value="Mon Nom" >
                                    <span id="error-Nom" class="error-text titanic">Nom complet invalide</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Poste / Rôle</label>
                                <div class="input-group">
                                    <input type="text" class="Role" value="Lead Developer">
                                    <span id="error-Role" class="error-text titanic">Rôle invalide</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <div class="input-group">
                                <input  value="mon.email@estoria.com" class="Email" >
                                <span id="error-Email" class="error-text titanic">Format email invalide</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Bio</label>
                            <textarea rows="3" placeholder="Quelques mots sur vous...">Passionné de code et de design.</textarea>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-save">Enregistrer les modifications</button>
                        </div>
                    </form>
                </div>

                <!-- SECTION 2 : GESTION DE L'ÉQUIPE -->
                <div class="settings-section">
                    <div class="section-header">
                        <h2>Gestion de l'équipe</h2>
                        <p class="section-desc">Gérez les accès et les membres de votre espace de travail.</p>
                    </div>

                    <div class="team-management-list">
                        
                        <!-- Membre 1 (Moi - Admin) -->
                        <div class="team-member-row">
                            <div class="member-info-col">
                                <div class="avatar-small">Moi</div>
                                <div>
                                    <div class="member-name">Mon Nom (Vous)</div>
                                    <div class="member-role">Admin</div>
                                </div>
                            </div>
                            <span class="status-badge admin">Propriétaire</span>
                        </div>

                        <!-- Membre 2 -->
                        <div class="team-member-row">
                            <div class="member-info-col">
                                <div class="avatar-small" style="background: #ff6b6b;">SL</div>
                                <div>
                                    <div class="member-name">Sarah L.</div>
                                    <div class="member-role">Lead Dev</div>
                                </div>
                            </div>
                            <button class="btn-remove-member" title="Retirer de l'équipe">Retirer 🗑️</button>
                        </div>

                        <!-- Membre 3 -->
                        <div class="team-member-row">
                            <div class="member-info-col">
                                <div class="avatar-small" style="background: #feca57;">AM</div>
                                <div>
                                    <div class="member-name">Alex M.</div>
                                    <div class="member-role">Chef de Projet</div>
                                </div>
                            </div>
                            <button class="btn-remove-member" title="Retirer de l'équipe">Retirer 🗑️</button>
                        </div>

                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- POPUP AJOUT MEMBRE (Caché par défaut) -->
    <div id="modal-add-member" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Ajouter un collaborateur</h3>
            </div>
            <form class="modal-form">
                <label>Nom</label>
                <input type="text" placeholder="Ex: Jean Dupont">
                <label>Rôle</label>
                <select>
                    <option>Développeur</option>
                    <option>Designer</option>
                </select>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="window.location.href='#'">Annuler</button>
                    <button type="submit" class="btn-confirm">Inviter</button>
                </div>
            </form>
        </div>
    </div>
<div id="success" class="toast titanic">
    <div class="toast-icon">✅</div>
    <div class="toast-content">
        <span class="toast-title">Succès</span>
        <span class="toast-message">Modifications enregistrées !</span>
    </div>
</div>
<script src="script.parametre.js"></script>
</body>
</html>
