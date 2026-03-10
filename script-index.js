console.log("je suis dans script-index.js");

function check_Email() {
    const email_input = document.querySelector('.Email'); 
    const email_error_msg = document.querySelector('#error-Email');

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (!emailRegex.test(email_input.value)) {
        email_error_msg.classList.remove('titanic'); 
        return 1; 
    } 
    else {
        email_error_msg.classList.add('titanic'); 
        return 0; 
    }
}

function check_Password() {
    const password_input = document.querySelector('.Password'); 
    const password_error_msg = document.querySelector('#error-Password');
    if (password_input.value.length < 6) {
        password_error_msg.classList.remove('titanic'); 
        return 1; 
    }
    else {
        password_error_msg.classList.add('titanic'); 
        return 0; 
    }
}

// ON SÉLECTIONNE LE FORMULAIRE, PAS LE BOUTON
// Les fonctions check_Email et check_Password ne changent pas.
// Remplace juste l'événement "submit" tout en bas :

const form = document.querySelector('form'); 

form.addEventListener("submit", function(event) {
    let nb_errors = 0;
    
    nb_errors += check_Email();
    nb_errors += check_Password();
    
    console.log("nb_errors : ", nb_errors);

    // S'il y a une erreur locale (JS), on bloque l'envoi vers PHP
    if (nb_errors > 0) {
        event.preventDefault(); 
    }
});

