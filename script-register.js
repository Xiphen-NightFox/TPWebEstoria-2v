function check_Name() {
    const title_input = document.querySelector('.Name'); 
    const title_error_msg = document.querySelector('#error-Name');
    console.log("oui ");
    // Si (Pas d'espace OU longueur trop petite) -> ERREUR
    if (!title_input.value.includes(' ') || title_input.value.length < 3) {
        title_error_msg.classList.remove('titanic'); // AFFICHE l'erreur
        console.log("non ");
        return 1; 
    }
    else {
        console.log("ok ");
        title_error_msg.classList.add('titanic'); // CACHE l'erreur
        return 0; 
    }
}




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

function check_VerifyPassword() {
    const password_input = document.querySelector('.Password'); 
    const verify_password_input = document.querySelector('.VerifyPassword'); 
    const verify_password_error_msg = document.querySelector('#error-VerifyPassword');
    if (password_input.value != verify_password_input.value) {
        verify_password_error_msg.classList.remove('titanic'); 
        return 1; 
    }
    else {
        verify_password_error_msg.classList.add('titanic'); 
        return 0; 
    }
}

// ON SÉLECTIONNE LE FORMULAIRE, PAS LE BOUTON
// ON SÉLECTIONNE LE FORMULAIRE, PAS LE BOUTON
const form = document.querySelector('form'); 

form.addEventListener("submit", function(event) {
    let nb_errors = 0;
    
    // On lance toutes nos vérifications
    nb_errors += check_Email();
    nb_errors += check_Password();
    nb_errors += check_Name();
    nb_errors += check_VerifyPassword();
    
    console.log("nb_errors : ", nb_errors);

    // Si on a des erreurs, on BLOQUE l'envoi du formulaire vers PHP
    if (nb_errors > 0) {
        event.preventDefault(); 
    }
});

