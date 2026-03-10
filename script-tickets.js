// --- VÉRIFICATIONS ---
function CheckTitre() {
    const titreInput = document.querySelector('.Titre');
    const errorTitre = document.getElementById('error-Titre');
    if (titreInput.value.trim().length < 2) {
        errorTitre.classList.remove('titanic'); 
        return 1; 
    } else {
        errorTitre.classList.add('titanic'); 
        return 0; 
    }
}

function CheckDescription() {
    const descriptionInput = document.querySelector('.DescriptionTicket');
    const errorDescription = document.getElementById('error-Description');
    if (descriptionInput.value.trim().length < 5) {
        errorDescription.classList.remove('titanic');
        return 1;
    } else {
        errorDescription.classList.add('titanic');
        return 0;
    }
}

function CheckTemps() {
    const tempsInput = document.querySelector('.TempsEstime');
    const errorTemps = document.getElementById('error-Temps');
    if (tempsInput.value.trim().length === 0 || parseFloat(tempsInput.value) <= 0) {
        errorTemps.classList.remove('titanic');
        return 1;
    } else {
        errorTemps.classList.add('titanic');
        return 0;
    }
}

// --- GESTION DES FILTRES ---
function filterTickets(type) {
    const buttons = document.querySelectorAll('.btn-filter');
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    const rows = document.querySelectorAll('.tickets-table tbody tr');

    rows.forEach(row => {
        // Ignorer la ligne "Aucun ticket"
        if(row.children.length === 1) return;

        const isBillable = row.querySelector('.badge-type.billable');
        const isIncluded = row.querySelector('.badge-type.included');

        if (type === 'all') {
            row.style.display = ''; 
        } 
        else if (type === 'billable') {
            row.style.display = isBillable ? '' : 'none'; 
        } 
        else if (type === 'included') {
            row.style.display = isIncluded ? '' : 'none'; 
        }
    });
}

// --- GESTION DE LA SOUMISSION DU FORMULAIRE ---
const form = document.querySelector('.modal-form');
if (form) {
    form.addEventListener("submit", function(event) {
        let nb_errors = 0;
        nb_errors += CheckTitre();
        nb_errors += CheckDescription();
        nb_errors += CheckTemps();
        
        console.log("Nombre d'erreurs : ", nb_errors);

        // Si et SEULEMENT SI il y a des erreurs, on bloque l'envoi au serveur
        if (nb_errors > 0) {
            event.preventDefault(); 
        }
        // S'il n'y a pas d'erreur, on ne fait rien, le formulaire va s'envoyer 
        // vers tickets.php, qui l'enregistrera en BDD.
    });
}

// --- GESTION DU TOAST DE SUCCÈS ---
// Quand le PHP aura fini d'enregistrer, il rechargera la page avec "?success=1"
if (window.location.search.includes('success=1')) {
    const toast = document.querySelector("#success");
    if (toast) {
        toast.classList.remove('titanic');
        setTimeout(() => { toast.classList.add('titanic'); }, 2000);
    }
    
    // On nettoie l'URL pour ne pas laisser le ?success=1
    const urlParams = newSearchParams(window.location.search);
    urlParams.delete('success');
    let newUrl = window.location.pathname;
    if(urlParams.toString()) newUrl += '?' + urlParams.toString();
    window.history.replaceState(null, '', newUrl);
}
