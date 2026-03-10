// --- FONCTION DE VÉRIFICATION ---
function check_Name() {
    const name_input = document.querySelector('.Name'); 
    const name_error_msg = document.querySelector('#error-Name');
    
    // Si pas de champ, on ne bloque rien
    if (!name_input) return 0;
    
    // Vérifie s'il fait moins de 3 caractères OU s'il n'y a pas d'espace (Nom Prénom)
    if (name_input.value.trim().length < 3 || !name_input.value.includes(' ')) {
        if(name_error_msg) name_error_msg.classList.remove('titanic');
        return 1;
    } else {
        if(name_error_msg) name_error_msg.classList.add('titanic');
        return 0;
    }
}

// --- SOUMISSION DU FORMULAIRE ---
const form = document.querySelector('form'); 

if (form) {
    form.addEventListener("submit", function(event) {
        let nb_errors = 0;
        nb_errors += check_Name();

        console.log("nb_errors : ", nb_errors);
        
        // On ne bloque l'envoi vers PHP QUE s'il y a des erreurs
        if (nb_errors > 0) {
            event.preventDefault(); 
        }
        // Si nb_errors == 0, on ne fait rien ! 
        // Le formulaire va s'envoyer au serveur PHP qui va l'enregistrer.
    });
}

// --- GESTION DU TOAST DE SUCCÈS (APRÈS L'ENREGISTREMENT PHP) ---
// Quand PHP aura fini, il rechargera la page avec ?success=collab dans l'URL
if (window.location.search.includes('success=collab')) {
    const toast = document.querySelector("#success");
    
    // La modale se fermera toute seule grâce au rechargement de la page (ou au :target du CSS)
    
    if (toast) {
        toast.classList.remove('titanic');
        setTimeout(() => { toast.classList.add('titanic'); }, 2500);
    }
    
    // On nettoie l'URL pour que le toast ne réapparaisse pas si on rafraîchit la page
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.delete('success');
    let newUrl = window.location.pathname;
    if(urlParams.toString()) newUrl += '?' + urlParams.toString();
    window.history.replaceState(null, '', newUrl);
}
