function check_title() {
    const title_input = document.querySelector('.Titre'); 
    const title_error_msg = document.querySelector('#error-Titre');
    if (title_input.value.length < 2) {
        title_error_msg.classList.remove('titanic'); // AFFICHE l'erreur
        return 1; 
    }
    else {
        title_error_msg.classList.add('titanic'); // CACHE l'erreur
        return 0; 
    }
}

function check_description() {
    const description_input = document.querySelector('.DescriptionProjet'); 
    const description_error_msg = document.querySelector('#error-Description');
    if (description_input.value.length < 5) {
        description_error_msg.classList.remove('titanic');
        return 1;
    }
    else {
        description_error_msg.classList.add('titanic');
        return 0;
    }
}

const form = document.querySelector('form'); 

form.addEventListener("submit", function(event) {
    let nb_errors = 0;
    
    // On vérifie les champs
    nb_errors += check_title();
    nb_errors += check_description();

    console.log("nb_errors : ", nb_errors);
    
    // S'il y a au moins une erreur, on bloque l'envoi !
    if (nb_errors > 0) {
        event.preventDefault(); 
    }
});

if (window.location.search.includes('success=1')) {
    const toast = document.querySelector("#success");
    toast.classList.remove('titanic');
    setTimeout(() => { toast.classList.add('titanic'); }, 2000);
}
