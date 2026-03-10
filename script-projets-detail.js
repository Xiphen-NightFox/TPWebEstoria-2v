// 1. VÉRIFICATION DU TITRE
function check_edit_title() {
    const title_input = document.querySelector('.TitreEdit'); 
    const title_error_msg = document.querySelector('#error-TitreEdit');
    
    // Si vide ou moins de 2 caractères
    if (title_input.value.trim().length < 2) {
        title_error_msg.classList.remove('titanic'); // Affiche l'erreur
        return 1; 
    } else {
        title_error_msg.classList.add('titanic'); // Cache l'erreur
        return 0; 
    }
}

// 2. VÉRIFICATION DE LA DESCRIPTION
function check_edit_description() {
    const desc_input = document.querySelector('.DescriptionEdit'); 
    const desc_error_msg = document.querySelector('#error-DescEdit');
    
    // Si vide ou moins de 5 caractères
    if (desc_input.value.trim().length < 5) {
        desc_error_msg.classList.remove('titanic'); // Affiche l'erreur
        return 1;
    } else {
        desc_error_msg.classList.add('titanic'); // Cache l'erreur
        return 0;
    }
}

// 3. GESTION DE LA SOUMISSION DU FORMULAIRE DE MODIFICATION
const formEdit = document.querySelector('.modal-form'); 

if (formEdit) {
    formEdit.addEventListener("submit", function(event) {
        let nb_errors = 0;
        
        // On lance les vérifications
        nb_errors += check_edit_title();
        nb_errors += check_edit_description();

        console.log("nb_errors à la modification : ", nb_errors);
        
        // S'il y a des erreurs, on BLOQUE l'envoi du formulaire au serveur
        if (nb_errors > 0) {
            event.preventDefault(); 
        }
        // Si nb_errors == 0, le formulaire s'envoie naturellement vers PHP
    });
}

// 4. GESTION DU TOAST DE SUCCÈS
// Vérifie si "?success=1" est dans l'URL
if (window.location.search.includes('success=1')) {
    const toast = document.querySelector("#success");
    if (toast) {
        // Affiche le toast
        toast.classList.remove('titanic');
        // Le cache après 2 secondes
        setTimeout(() => { toast.classList.add('titanic'); }, 2000);
    }
    
    // On nettoie l'URL (supprime le success=1 pour ne pas réafficher le toast si on actualise)
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.delete('success');
    
    // On garde juste ?id=X dans la barre d'adresse
    const newUrl = window.location.pathname + '?' + urlParams.toString();
    window.history.replaceState(null, '', newUrl);
}
