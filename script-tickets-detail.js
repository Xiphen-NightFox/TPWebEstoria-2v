// --- VÉRIFICATIONS FORMULAIRE DE TEMPS ---
function CheckTemps() {
    const tempsInput = document.querySelector('.TempsAjoute');
    const errorTemps = document.getElementById('error-TempsAjoute');
    
    if (!tempsInput) return 0;
    
    const val = parseFloat(tempsInput.value.replace(',', '.'));
    
    if (isNaN(val) || val <= 0 || tempsInput.value.trim() === '') {
        if(errorTemps) errorTemps.classList.remove('titanic');
        return 1;
    } else {
        if(errorTemps) errorTemps.classList.add('titanic');
        return 0;
    }
}


// --- VÉRIFICATIONS FORMULAIRE DE MODIFICATION ---
function CheckTitreEdit() {
    const titreInput = document.querySelector('.TitreEdit');
    const errorTitre = document.getElementById('error-TitreEdit');
    
    if (!titreInput) return 0;
    
    if (titreInput.value.trim().length < 2) {
        if(errorTitre) errorTitre.classList.remove('titanic'); 
        return 1; 
    } else {
        if(errorTitre) errorTitre.classList.add('titanic'); 
        return 0; 
    }
}

function CheckDescriptionEdit() {
    const descriptionInput = document.querySelector('.DescriptionEdit');
    const errorDescription = document.getElementById('error-DescEdit');
    
    if (!descriptionInput) return 0;

    if (descriptionInput.value.trim().length < 5) {
        if(errorDescription) errorDescription.classList.remove('titanic');
        return 1;
    } else {
        if(errorDescription) errorDescription.classList.add('titanic');
        return 0;
    }
}


// --- GESTION DE LA SOUMISSION DES FORMULAIRES ---

// 1. Formulaire d'ajout de temps
const formTime = document.getElementById('form-time');
if (formTime) {
    formTime.addEventListener("submit", function(event) {
        let nb_errors = 0;
        nb_errors += CheckTemps();
        
        console.log("Erreurs Temps : ", nb_errors);

        if (nb_errors > 0) {
            event.preventDefault(); 
        }
    });
}

// 2. Formulaire de modification du ticket
const formEdit = document.getElementById('form-edit');
if (formEdit) {
    formEdit.addEventListener("submit", function(event) {
        let nb_errors = 0;
        nb_errors += CheckTitreEdit();
        nb_errors += CheckDescriptionEdit();
        
        console.log("Erreurs Modification : ", nb_errors);

        if (nb_errors > 0) {
            event.preventDefault(); 
        }
    });
}


// --- GESTION DU TOAST DE SUCCÈS ---

if (window.location.search.includes('success=')) {
    const toast = document.querySelector("#toast-success");
    const toastMsg = document.querySelector("#toast-msg");
    
    if (toast && toastMsg) {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Texte dynamique selon l'action
        if (urlParams.get('success') === 'time') {
            toastMsg.innerText = "Temps ajouté !";
        } else if (urlParams.get('success') === 'edit') {
            toastMsg.innerText = "Ticket modifié !";
        }

        toast.classList.remove('titanic');
        setTimeout(() => { toast.classList.add('titanic'); }, 2000);

        // Nettoyage de l'URL
        urlParams.delete('success');
        let newUrl = window.location.pathname;
        if(urlParams.toString()) newUrl += '?' + urlParams.toString();
        window.history.replaceState(null, '', newUrl);
    }
}
